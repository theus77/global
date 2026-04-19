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