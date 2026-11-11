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
docker run --rm -it \
  -v "$PWD/es-dump:/dump" \
  --add-host=host.docker.internal:host-gateway \
  taskrabbit/elasticsearch-dump \
  --input=http://host.docker.internal:9222/global_live \
  --output=/dump/globale_live_data.json \
  --type=data
````

How to migrate Aperture documents to the new eMS

Open a shell in the wcmtech-cli on the branch 

```shell
pbc ems:admin:login https://globalview-admin.theus.be
pbc globalview:migrate:places ../global/es-dump/aperture_v1_data.json
pbc globalview:migrate:keywords ../global/es-dump/aperture_v1_data.json
pbc globalview:migrate:versions ../global/es-dump/aperture_v1_data.json --s3-credentials='$OLD_GV_S3_CRED' --s3-bucket='global-previews'
```

## Install AWS server

```shell
sudo dnf install -y docker
sudo systemctl enable --now docker
sudo docker run --rm hello-world
sudo install -d -m 0755 /usr/local/lib/docker/cli-plugins
sudo curl -fSL \
  https://github.com/docker/compose/releases/download/v5.6.0/docker-compose-linux-aarch64 \
  -o /usr/local/lib/docker/cli-plugins/docker-compose
sudo chmod 0755 /usr/local/lib/docker/cli-plugins/docker-compose
sudo usermod -aG docker "ec2-user"
```