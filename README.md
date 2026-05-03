# EMS - Standard

## Prerequisites

## Make

The project contains a Makefile for all commands.

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

make sandbox
make fake-ide
```


## Start codex in a sandbox container

```bash
make sandbox
npm i -g @openai/codex && codex
```

How to backup old 2.4.0 indexes:

````shell
docker run --rm -it \
  -v "$PWD/es-dump:/dump" \
  --add-host=host.docker.internal:host-gateway \
  taskrabbit/elasticsearch-dump \
  --input=http://host.docker.internal:9222/aperture_v1 \
  --output=/dump/aperture_v1_mapping.json \
  --type=mapping
docker run --rm -it \
  -v "$PWD/es-dump:/dump" \
  --add-host=host.docker.internal:host-gateway \
  taskrabbit/elasticsearch-dump \
  --input=http://host.docker.internal:9222/aperture_v1 \
  --output=/dump/aperture_v1_data.json \
  --type=data
````