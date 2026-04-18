# EMS - Standard

The default elasticMS standard project. This project will never be deployed in
production, it will only be used as base for new project.

## Prerequisites

* Local copy
  of [dev tools](https://git.cloud.extranetdc.be/webcontent/skeleton-dev-tools)
* **EMS_CLUSTER_USER** : required environment variable
* **EMS_CLUSTER_PASS** : required environment variable
* **TOOLS_DIR** : environment variable default
  ```~/dev/tools/skeleton-dev-tools/```
* **DOCKER_USER** : environment variable default ```$UID```
* **NPM_AUTH_KEY
  ** : [Define npm auth key](https://confluence.smals.be/display/WEBAGENCY/Skeleton+development#npm-auth-key)
* **EMS_USER**: optional ems username for login

### Urls

* [http://localhost:8888/dashboard/](http://localhost:8888/dashboard/)
* [http://mailhog.localhost/](http://mailhog.localhost/)

#### EMS Standard ACC

- https://acc-preview.standard.localhost
- https://acc-live.standard.localhost
- https://standard-admin.acc.ext.ssbcloud.be
- https://standard-live.acc.ext.ssbcloud.be
- https://console.apps.ssb-02.paas.cloud.ssbdc.be/k8s/ns/webagency-bu-smals-services/deployments/ems-standard-admin
- https://console.apps.ssb-02.paas.cloud.ssbdc.be/k8s/ns/webagency-bu-smals-services/deployments/ems-standard-website

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

```bash
oc login https://api.ssb-02.paas.cloud.ssbdc.be -u username # ACC

make oc-pause/admin # pause rollouts admin
make oc-resume/admin # resume rollouts admin
make oc-set-env/admin KEY=APP_ENV VAL=redis
make oc-update-config-maps # will update admin and web config map
make oc-restart/admin # restart admin

make oc-release/website VERSION=6.9.7 TAG=6.9.7-20260126-r1
make oc-release/admin VERSION=6.9.7 TAG=6.9.7-20260126-r1
```
