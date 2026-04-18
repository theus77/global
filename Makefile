include .env
export $(grep -v '^#' .env | xargs)

TOOLS_DIR ?= ~/dev/tools/skeleton-dev-tools/
DOCKER_USER ?= $UID
PWD = $(shell pwd)

DOCKER = docker
DOCKER_COMP = docker compose

NPM_CMD = "npm config set registry=https://repo.gcloud.belgium.be/artifactory/api/npm/smals-npm-central/; npm config set //repo.gcloud.belgium.be/artifactory/api/npm/smals-npm-central/:_authToken=${NPM_AUTH_KEY}; npm $*"
RUN_NPM = docker run --rm -it -u ${DOCKER_USER} -p 5174:5174 --dns 10.4.65.16 -v ${PWD}:/opt/src -e NODE_OPTIONS="--openssl-legacy-provider" -e NO_PROXY=repo.gcloud.belgium.be -e HTTPS_PROXY=http://proxy.smals-mvm.be:8080 --workdir /opt/src gcloud-docker-internet.repo.gcloud.belgium.be/elasticms/base-php-cli-dev sh -c ${NPM_CMD}

.PHONY: help
.DEFAULT_GOAL := help

help: ## help
	@echo ${PROJECT_NAME}
	@echo "---------------------------"
	@echo VERSION: ${SKELETON_VERSION}
	@echo INSTANCE: ${INSTANCE_ID}
	@echo TOOLS: ${TOOLS_DIR}
	@echo Traefik: http://localhost:8888
	@echo Mailhog: http://mailhog.localhost
	@echo "---------------------------"
	@echo ""
	@echo "Usage: make [target]"
	@echo "Targets:"
	@grep -E '(^\S*:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— DOCKER ———————————————————————————————————————————————————————————————————————————————————————————————————————————
up/%: ## up/(acc|prd)
	@$(MAKE) -s tools-up
	@$(DOCKER_COMP) up skeleton-$* -d
restart/%: ## restart/(acc|prd)
	@$(DOCKER_COMP) up skeleton-$* -d --force-recreate
logs/%: ## logs/(acc|prd)
	@$(DOCKER_COMP) logs -f skeleton-$*
bash/%: ## bash/(acc|prd)
	@$(DOCKER_COMP) exec skeleton-$* bash
status: ## docker status
	@$(DOCKER_COMP) ps
down: ## docker down
	@$(DOCKER_COMP) down
stop/%: ## stop/(acc|prd)
	@$(DOCKER_COMP) stop skeleton-$*

## —— ELASTICMS ————————————————————————————————————————————————————————————————————————————————————————————————————————
login/%: ## login/(acc|prd)
	@$(MAKE) -s $*/"ems:admin:login$(if $(EMS_USER), --username=${EMS_USER})"
emsch-status/%: ## emsch-status/(acc|prd)
	@$(MAKE) -s $*/"emsch:local:status -v"
emsch-pull/%: ## emsch-pull/(acc|prd)
	@$(MAKE) -s $*/"emsch:local:pull"
emsch-push/%: ## emsch-push/(acc|prd)
	@$(MAKE) -s $*/"emsch:local:push"
emsch-assets/%: ## emsch-assets/(acc|prd)
	@$(MAKE) -s $*/"emsch:local:upload-assets --filename=/app/src/elasticms/local/${INSTANCE_ID}preview/ems_standard_template/asset_hash.twig --as-style-set-assets"
backup-documents/%: ## backup-documents/(acc|prd)
	@$(MAKE) -s $*/"ems:admin:backup --export --documents"
backup-configs/%: ## backup-configs/(acc|prd)
	@$(MAKE) -s $*/"ems:admin:backup --export --configs"
restore-configs/%: ## restore-configs/(acc|prd)
	@$(MAKE) -s $*/"ems:admin:restore --force --configs"
restore-documents/%: ## restore-documents/(acc|prd)
	@$(MAKE) -s $*/"ems:admin:restore --force --documents"
lint/%: ## lint/(acc|prd)
	-@$(MAKE) -s $*/"lint:twig --show-deprecations /app/src/elasticms/local/${INSTANCE_ID}preview/ems_standard_template --no-debug"
	-@$(MAKE) -s $*/"lint:yaml /app/src/elasticms/local/${INSTANCE_ID}preview"

acc/%: ## acc/"command"
	@$(DOCKER_COMP) exec skeleton-acc preview $*
prd/%: ## prd/"command"
	@$(DOCKER_COMP) exec skeleton-prd preview $*

## —— NPM ——————————————————————————————————————————————————————————————————————————————————————————————————————————————
npm/%: ## npm/"command"
	@$(RUN_NPM)
npm-install: ## npm install
	@$(MAKE) npm/"install"
npm-prod: ## npm run prod
	@$(MAKE) npm/"run prod"
npm-watch: ## npm run watch
	@$(MAKE) npm/"run watch"
npm-dev: ## npm run dev
	@$(MAKE) npm/"run dev"

## —— TOOLS ————————————————————————————————————————————————————————————————————————————————————————————————————————————
tools-up: ## Start Traefik and MailHog
	cd $(TOOLS_DIR) && $(DOCKER_COMP) up -d
tools-down: ## Stop Traefik and MailHog
	cd $(TOOLS_DIR) && $(DOCKER_COMP) down --remove-orphans
tools-create-network: ## Create tools network
	@$(DOCKER) network rm skeleton -f
	@$(DOCKER) network create skeleton

## —— OC ———————————————————————————————————————————————————————————————————————————————————————————————————————————————
check_oc = @if [ "$*" != "admin" -a "$*" != "website" ]; then echo "Exception: Only admin or website are allowed."; exit 1; fi

oc-pause/%: ## oc-pause/(admin|website)
	$(check_oc)
	@oc -n ${OC_PROJECT} rollout pause deployment/${OC_DEPLOY}-$*;
oc-resume/%: ## oc-resume/(admin|website)
	$(check_oc)
	@oc -n ${OC_PROJECT} rollout resume deployment/${OC_DEPLOY}-$*
oc-restart/%: ## oc-restart/(admin|website)
	$(check_oc)
	@oc -n ${OC_PROJECT} rollout restart deployment/${OC_DEPLOY}-$*
oc-set-env/%: ## oc-env/(admin|website) KEY=APP_ENV VAL=redis
	$(check_oc)
	@oc -n ${OC_PROJECT} set env deployment/${OC_DEPLOY}-$* --containers=elasticms ${KEY}='${VAL}'
oc-update-config-maps: ## update config maps
	@oc -n ${OC_PROJECT} set data cm/${OC_DEPLOY}-admin-config --from-file=elasticms=./configs/openshift/${OC_DEPLOY}.env
	@oc -n ${OC_PROJECT} set data cm/${OC_DEPLOY}-website-config --from-file=zz_live=./configs/skeleton/zz_live.env
oc-release/%: ## oc-release/(admin|website) VERSION=5.15.1 TAG=5.15.1-8634473045-r1
	$(check_oc)
	-@export image=$(if $(filter admin,$*),smalswebtech/elasticms-admin:${TAG},$(if $(filter website,$*),smalswebtech/elasticms-skeleton:${TAG},)); \
	oc -n ${OC_PROJECT} label --overwrite=true deployment/${OC_DEPLOY}-$* app.kubernetes.io/version=${VERSION}; \
	oc -n ${OC_PROJECT} annotate --overwrite=true deployment/${OC_DEPLOY}-$* app.openshift.io/vcs-ref=${TAG}; \
	oc -n ${OC_PROJECT} patch deployment/${OC_DEPLOY}-$* -p "{\"spec\":{\"template\":{\"metadata\":{\"labels\":{\"app.kubernetes.io/version\":\"${VERSION}\"}}}}}"; \
	oc -n ${OC_PROJECT} set image deployment/${OC_DEPLOY}-$* elasticms=gcloud-docker-release.repo.gcloud.belgium.be/$$image;

## —— EXTRA ————————————————————————————————————————————————————————————————————————————————————————————————————————————
find-crlf: ## Find files with CRLF line endings
	@find . \( -path "./node_modules" -o -path "./dist" \) -prune -o \( ! -path '*/.*' \) -type f -exec file "{}" ";" | grep CRLF
convert-crlf-lf: ## Convert CRLF to LF
	@find . \( -path "./node_modules" -o -path "./dist" \) -prune -o \( ! -path '*/.*' \) -type f -exec dos2unix "{}" \;
