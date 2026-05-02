# AGENTS.md

Guidance for coding agents working in this migration repository.

## Project Overview

This repository migrates an existing Symfony website from `archive/` to an ElasticMS skeleton project. The source project uses Bootstrap 3 conventions, while the migrated skeleton targets Bootstrap 5.

Key areas:

- `archive/`: the original dedicated Symfony application. Use it as the behavioral and visual reference when porting features.
- `skeleton/`: the ElasticMS skeleton configuration and Twig templates used by the migrated website.
- `skeleton/routes.yaml`: the source of truth for this project's custom routes.
- `skeleton/translations/`: one YAML translation file per supported language: French, Dutch, German, and English.
- `skeleton/ems_standard_template/`: website Twig templates. These map to the Twig namespace `@EMSCH/ems_standard_template/`.
- `skeleton/ems_standard_template_ems/`: ElasticMS admin/customizer Twig templates for views and actions. These map to `@EMSCH/ems_standard_template_ems/`.
- `src/`: npm/Vite source project.
- `dist/`: Vite build output.
- `ide-elasticms/`: fake Symfony project used only for IDE autocompletion and generated helper classes.
- `public/`: public assets used by the site.

Do not treat this as a normal Symfony application migration target. The runtime target is the ElasticMS skeleton.

## ElasticMS And IDE Helpers

The fake Symfony project under `ide-elasticms/` exists to help IDEs understand ElasticMS-specific Twig functions, filters, routes, and controllers.

Important generated files:

- `ide-elasticms/src/PhpStorm/GeneratedElasticmsTwigAttributeExtension.php`: generated Twig function/filter metadata for ElasticMS.
- `ide-elasticms/src/PhpStorm/GeneratedSkeletonController.php`: generated controller and route metadata for ElasticMS bundles and this skeleton project.

ElasticMS bundle routes are extracted from `ide-elasticms/vendor/elasticms`. Project routes are defined in `skeleton/routes.yaml`, not in normal Symfony controller files.

Regenerate IDE helpers when route or Twig helper metadata changes:

```bash
make ide-generate
make fake-ide
```

## Twig Template Rules

For public website templates in `skeleton/ems_standard_template/`:

- Use the namespace `@EMSCH/ems_standard_template/`.
- ElasticMS Twig extensions starting with `ems_`, `emss_`, `emsf_`, and `emsch_` are available.
- Keep templates aligned with the migrated behavior from `archive/`, but prefer ElasticMS skeleton patterns where they already exist.

For admin/customizer templates in `skeleton/ems_standard_template_ems/`:

- Use the namespace `@EMSCH/ems_standard_template_ems/`.
- ElasticMS Twig extensions starting with `ems_` and `emsco_` are available.
- These templates define ElasticMS admin views and actions, not public website pages.

When porting Twig from `archive/`, check for Symfony-specific assumptions such as controller-generated URLs, normal Symfony forms, service-injected globals, or legacy asset paths. Adapt those assumptions to ElasticMS skeleton routes, ElasticMS Twig helpers, and skeleton assets.

## Translations

Supported languages are `fr`, `nl`, `de`, and `en`.

Translations live in `skeleton/translations/` as YAML files. When adding or changing a user-visible string, update all supported languages unless the existing pattern clearly uses fallback text.

Keep translation keys stable and consistent across languages. Prefer reusing existing keys over adding near-duplicates.

## Frontend And Assets

The frontend is an npm/Vite project:

- Source: `src/`
- Build output: `dist/`
- Config: `vite.config.js`
- Package manifest: `package.json`

Useful commands:

```bash
make npm-install
make npm-dev
make npm-watch
make npm-prod
```

Equivalent direct npm scripts are:

```bash
npm run dev
npm run watch
npm run prod
```

Only update generated build output in `dist/` when the task requires built assets. Avoid unrelated dependency or lockfile churn.

When porting frontend markup or styles from `archive/`, translate Bootstrap 3 classes, grid patterns, components, and JavaScript data attributes to their Bootstrap 5 equivalents instead of copying them verbatim.

## Common ElasticMS Commands

The Makefile wraps Docker and ElasticMS commands. Common targets:

```bash
make up/acc
make login/acc
make emsch-status/acc
make emsch-pull/acc
make emsch-push/acc
make emsch-assets/acc
make lint/acc
make stop/acc
```

Use `prd` variants only when explicitly requested:

```bash
make emsch-status/prd
make emsch-pull/prd
make emsch-push/prd
```

The generic command wrappers are:

```bash
make acc/"command"
make prd/"command"
make cli/"command"
```

Be careful with push, force-push, restore, and production commands. Do not run them unless the user explicitly asks for that operation.

## Validation

Run the narrowest useful checks for the change.

For Twig/YAML skeleton work:

```bash
make lint/acc
```

For frontend changes:

```bash
make npm-prod
```

For quick local searches and inspections, prefer `rg` and read nearby templates before editing.

If Docker, ElasticMS services, or npm dependencies are unavailable, report the exact command attempted and the failure. Do not invent validation results.

## Migration Workflow

When migrating a feature from `archive/`:

1. Find the original route, controller, template, translations, and assets in `archive/`.
2. Identify the matching ElasticMS skeleton route in `skeleton/routes.yaml` or add one there if needed.
3. Port Twig into the correct skeleton template namespace.
4. Replace Symfony-specific runtime assumptions with ElasticMS Twig helpers and skeleton configuration.
5. Move or reference assets through the Vite/public asset structure already used by this repository.
6. Update all translation YAML files for visible text.
7. Run focused validation.

Keep changes scoped to the migrated behavior. Do not refactor unrelated templates, routes, or assets while porting a specific page.

## Working Rules

- Preserve user changes and local environment files. Do not remove or revert files unless explicitly asked.
- Do not edit `node_modules/`.
- Treat `ide-elasticms/` as generated IDE support unless the task is specifically about IDE metadata or generator behavior.
- Prefer existing skeleton conventions over introducing new abstractions.
- Use structured YAML/Twig edits instead of broad string rewrites.
- Keep route names, translation keys, and template paths consistent with existing project naming.
- Avoid touching production commands or remote ElasticMS state without explicit instruction.

## Sandbox Note

In some Codex environments, sandboxed shell execution can fail before commands run with an error about `bwrap` and unprivileged user namespaces. If that happens, rerun required read-only inspections or validations with approval instead of guessing from memory.
