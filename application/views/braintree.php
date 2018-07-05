<?php
$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("braintree.html");
$new_assets_url = jsite_url("/qatesting/newassets/");
$new_assets_url1 = jsite_url("/qatesting/benz_assets/");

mustache_tmpl_set($template, "new_assets_url", $new_assets_url);
mustache_tmpl_set($template, "merchant_id", $store_name['merchant_id']);

echo mustache_tmpl_parse($template);
