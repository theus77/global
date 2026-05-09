include .env
#export $(grep -v '^#' .env | xargs)

TOOLS_DIR ?= ../elasticms/
DOCKER_USER ?= UID
PWD = $(shell pwd)

DOCKER = docker
DOCKER_COMP = docker compose

NPM_CMD="npm $*"
RUN_NPM = docker run --rm -it -u ${DOCKER_USER} -p 5174:5174 -v ${PWD}:/opt/src --workdir /opt/src docker.io/smalswebtech/base-php:8.5-cli-dev sh -c ${NPM_CMD}

.PHONY: help
.DEFAULT_GOAL := help

help: ## help
	@echo ${PROJECT_NAME}
	@echo "---------------------------"
	@echo VERSION: ${SKELETON_VERSION}
	@echo INSTANCE: ${INSTANCE_ID}
	@echo TOOLS: ${TOOLS_DIR}
	@echo Reverse proxy: http://localhost:8888
	@echo Mailserver: http://mailserver.localhost/
	@echo "---------------------------"
	@echo ""
	@echo "Usage: make [target]"
	@echo "Targets:"
	@grep -E '(^\S*:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— DOCKER ———————————————————————————————————————————————————————————————————————————————————————————————————————————
up/%: ## up/(acc|prd)
	@$(MAKE) -s tools-up
	@$(DOCKER_COMP) up skeleton-$* -d
	@$(DOCKER_COMP) up sandbox cli -d
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
emsch-force-push/%: ## emsch-force-push/(acc|prd)
	@$(MAKE) -s $*/"emsch:local:push --force"
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
ide-generate: ## Generate IDE helpers in the fake Symfony project
	cd ide-elasticms && php tools/generate-phpstorm-skeleton-controller.php && php tools/generate-phpstorm-twig-extension.php
tools-up: ## Start Traefik and MailHog
	cd $(TOOLS_DIR) && $(DOCKER_COMP) --project-directory=docker --profile=ems up -d
tools-down: ## Stop Traefik and MailHog
	cd $(TOOLS_DIR) && $(DOCKER_COMP) --project-directory=docker --profile=ems down ---profile=ems -remove-orphans
tools-create-network: ## Create tools network
	@$(DOCKER) network rm skeleton -f
	@$(DOCKER) network create skeleton
cli: ## Start a CLI bash
	@$(DOCKER_COMP) exec cli bash
cli/%: ## cli/"command"
	@$(DOCKER_COMP) exec cli elasticms $*
fake-ide: ## Generate and update the fake ide-elasticms project
	@$(MAKE) -s cli/"emscli:dev:fake /workspace/ide-elasticms --force"
	cd ide-elasticms && composer update
sandbox: ## Start a sandbox bash
	@$(DOCKER_COMP) exec sandbox bash

## —— EXTRA ————————————————————————————————————————————————————————————————————————————————————————————————————————————
find-crlf: ## Find files with CRLF line endings
	@find . \( -path "./node_modules" -o -path "./dist" \) -prune -o \( ! -path '*/.*' \) -type f -exec file "{}" ";" | grep CRLF
convert-crlf-lf: ## Convert CRLF to LF
	@find . \( -path "./node_modules" -o -path "./dist" \) -prune -o \( ! -path '*/.*' \) -type f -exec dos2unix "{}" \;
