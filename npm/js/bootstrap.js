'use strict';
const $ = require('jquery');
window.$ = $;
window.jQuery = $;

import '../css/bootstrap.scss';
require('bootstrap');

import adminMenu from '@elasticms/admin-menu';
import back2top from "./back2top";
import ajaxSearch from "./ajax-search";
import toc from "./toc";
import externalLink from "./external-link";

adminMenu();
back2top();
ajaxSearch();
toc();
externalLink();

console.log('Demo website loaded!');