<?php

$CI =& get_instance();
$CI->load->helper("mustache_helper");

if (!defined("ENABLE_OEMPARTS_BUTTON")) {
    define("ENABLE_OEMPARTS_BUTTON", false);
}

if (!isset($partsfinder_link)) {
    $CI->load->model("admin_m");
    $store_name = $CI->admin_m->getAdminShippingProfile();
    $partsfinder_link = $store_name["partsfinder_link"];
}


$template = mustache_tmpl_open("mobile_navigation_fragment.html");
mustache_tmpl_set($template, "s_baseURL", $s_baseURL);
if ($partsfinder_link != "") {
    mustache_tmpl_set($template, "partsfinder_link", $partsfinder_link);
}
echo mustache_tmpl_parse($template);
