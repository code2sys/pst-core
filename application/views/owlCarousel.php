<?php

$CI->load->helper("mustache_helper");

$template = mustache_tmpl_open("owlCarousel.html");

mustache_tmpl_set($template, "slideSpeed", defined("HOME_SCREEN_SLIDER_SPEED") ? HOME_SCREEN_SLIDER_SPEED : 500);
mustache_tmpl_set($template, "paginationSpeed", defined("HOME_SCREEN_PAGINATION_SPEED") ? HOME_SCREEN_PAGINATION_SPEED : 500);
mustache_tmpl_set($template, "autoPlay", defined("HOME_SCREEN_AUTO_PLAY_TIMEOUT") ? HOME_SCREEN_AUTO_PLAY_TIMEOUT : 5000);
mustache_tmpl_set($template, "autoPlayTimeout", defined("HOME_SCREEN_AUTO_PLAY_TIMEOUT") ? HOME_SCREEN_AUTO_PLAY_TIMEOUT : 1000);

print mustache_tmpl_parse($template);