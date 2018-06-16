<?php

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$store_hours_template = mustache_tmpl_open("info/store_hours.html");
jtemplate_add_store_hours($store_hours_template, $store_name);
echo mustache_tmpl_parse($store_hours_template);

