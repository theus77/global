# EMS - Standard

## Prerequisites

## Make

The project contains a Makefile for all commands

```bash
make up/acc
make login/acc
make emsch-pull/acc
make emsch-push/acc
make backup-configs/acc
make acc/"list"
make stop/acc

make npm-install
make npm-watch
make npm-prod
```

Create symlink for assets
```
ln -s /Users/theus/Workspace/global/dist /Users/theus/Workspace/ems7/elasticms-admin/public/bundles/globalview
```

## Start codex in a container 

```bash
docker run -it \
-u ${DOCKER_USER:-1000} \
-v "$PWD":/workspace \
--tmpfs /home/default:rw,exec,size=500m,mode=1777 \
-v /etc/ssl/certs/ca-certificates.crt:/etc/ssl/certs/ca-certificates.crt \
-v "$HOME/.codex_global":/home/app/.codex \
-w /workspace \
docker.io/smalswebtech/base-php:8.5-cli-dev \
bash
```

```bash
npm config set strict-ssl=false
npm i --save-dev @openai/codex
npx codex
```