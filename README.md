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