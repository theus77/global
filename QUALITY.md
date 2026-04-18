# Quality

## 1. Checklist

- ```yes```: Align with project setup?
  - Align all files as good as possible with the defined structure in [skeleton-dev-tools](https://git.cloud.extranetdc.be/webcontent/skeleton-dev-tools)
- ```yes```: No sleeping branches?
- ```yes```: EMS upgrade notes checked and applied https://ems-project.github.io/#/upgrade

### 1.1 - Checklist web

- ```yes```: Not using `_get_file_path` in ems_asset_path [example on demo](https://github.com/ems-project/elasticms/blob/5.x/demo/skeleton/template/redirects/media_files.json.twig)
- ```yes```: Seo routes bing and google created?
- ```yes```: Skeleton live using dedicated managed alias "ma_live_web"?
- ```yes```: Not using public CDN like google fonts?
- ```yes```: Using correct CDN domain: cdn.socialsecurity.be or cdn.gcloud.belgium.be ?
- ```yes```: Using ```twig {{ asset('', 'emsch') }}``` everywhere?
- ```yes```: Verify 404 is working?
  - check the route _emsch_error to test your dev with a url without locale in it /not-found
- ```yes```: /favicon.ico is redirected?
  - See [skeleton dev-tools for implementation](https://git.cloud.extranetdc.be/webcontent/skeleton-dev-tools/-/merge_requests/8).
- ```yes```: Is the robots.txt using an environment variable for allow indexing
  - See skeleton dev-tools for implementation.
- ```yes```: In webpack.config remove ```json watchOptions: { poll: true }```
- ```yes```: Add 2 routes to block autodiscovery.xml request (POST and GET). See [INAMI](https://git.cloud.extranetdc.be/webcontent/webinami/-/blob/master/skeleton/routes.yaml?ref_type=heads)
- ```yes```: In all template twig remove ```{% apply spaceless %}``` and if necessary use ```{%- -%}``` or ```{{- -}}```

### 1.2 - Checklist Admin

- ```yes```: Environments used in managed alias "ma_live_web" are not updating referrers?
- ```yes```: The channels are configured ?
- ```yes```: Forms migrated to admin with form content type ?
- ```yes```: Publish role is not defined for content types that have default as default environment?
- ```yes```: Delete role defined for all content types? (Aligned on the Publish role)
- ```yes```: Avoid type/search-id for data links replace by QuerySearch
  - For data links that use a legacy search id
- ```yes```: Query searches use "query": "%query%" and not "query": "*"
- ```yes```: Ensure that the page content type (or the content type that is hosting the a11y report page) has a no-index checkbox.
  -  If the website is exposed to internet. See the demo. It can be a single checkbox that applies to all locales.
- ```yes```: For each Style-Set: remove "Save dir" 
- ```yes```: Since 5.23.x - Make sure the /bundles route is not matching a defined route, emsch_path for example.
- ```yes```: In computed fields, post_processing,... remove ```{% apply spaceless %}``` and if necessary use ```{%- -%}``` or ```{{- -}}```

## 2. Deployment

- ```yes```: ES_CLUSTER_CONFIG variable used for EMS_ELASTICSEARCH_HOSTS ?
- ```yes```: Redis enabled?
  - Except eHealth, no redis available 
- ```yes```: Metrics enabled?
  - Make sure the /metrics route is not matching a defined route, emsch_path for example.
- ```yes```: Varnish enabled?
- ```yes```: Jobs enabled and trigger from web false?
  - JOBS_ENABLED=true and EMSCO_TRIGGER_JOB_FROM_WEB=false
- ```yes```: No more dev mode active?

## 3. Legacy

- ```yes```: Template environment removed?
- ```yes```: JsonFieldType replaced by CodeFieldType?
- ```yes```: Removed ENVIRONMENT_ALIAS_BASE_PATH?
- ```yes```: Removed ENVIRONMENT_ALIAS?
- ```yes```: Removed EMSCO_LOG_BY_PASS?
