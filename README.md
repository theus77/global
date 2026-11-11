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
The same commands but from the GlobalView server :


````shell
sudo docker run --rm -it \
  -v "$PWD/es-dump:/dump" \
  --network host \
  taskrabbit/elasticsearch-dump \
  --input=http://localhost:9200/aperture_v1 \
  --output=/dump/aperture_v1_mapping.json \
  --type=mapping
sudo docker run --rm -it \
  -v "$PWD/es-dump:/dump" \
  --network host \
  taskrabbit/elasticsearch-dump \
  --input=http://localhost:9200/aperture_v1 \
  --output=/dump/aperture_v1_data.json \
  --type=data
sudo docker run --rm -it \
  -v "$PWD/es-dump:/dump" \
  --network host \
  taskrabbit/elasticsearch-dump \
  --input=http://localhost:9200/global_live \
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

The same commands but from the GlobalView server :

```shell
elasticms ems:admin:login https://globalview-admin.theus.be
elasticms globalview:migrate:places /app/global/es-dump/aperture_v1_data.json
elasticms globalview:migrate:keywords /app/global/es-dump/aperture_v1_data.json
elasticms globalview:migrate:versions /app/global/es-dump/aperture_v1_data.json --s3-credentials='$OLD_GV_S3_CRED' --s3-bucket='global-previews'
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

sudo mkfs.ext4 -L cms-cache /dev/sdb
sudo mkdir -p /srv/cms-cache
sudo mount /dev/sdb /srv/cms-cache
findmnt --mountpoint /srv/cms-cache
df -h /srv/cms-cache
sudo sysctl -w vm.max_map_count=1048576
echo 'vm.max_map_count=1048576' | sudo tee /etc/sysctl.d/99-elasticsearch.conf 
sudo sysctl --system
```

La commande `sudo blkid /dev/sdb` devrait afficher quelque chose comme `/dev/sdb: LABEL="cms-cache" UUID="37ae2742-fd57-46de-9bc9-bb83280079f8" BLOCK_SIZE="4096" TYPE="ext4"`

Il faut ajouter cette ligne à `/etc/fstab` (`sudo vi /etc/fstab`)

Initialise le cluster elasticsearch:

```shell
docker compose exec -T elasticsearch sh <<'SH' 
curl --fail-with-body -sS -u "elastic:$ELASTIC_PASSWORD" -X PUT "http://localhost:9200/_index_template/dev-single-node" -H "Content-Type: application/json" -d '{"index_patterns":["*"],"priority":1,"template":{"settings":{"number_of_replicas":0}}}' 
SH
```