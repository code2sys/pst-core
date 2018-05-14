<?php

$CI =& get_instance();
$CI->load->helper("mustache_helper");

if (!defined("ENABLE_OEMPARTS_BUTTON")) {
    define("ENABLE_OEMPARTS_BUTTON", false);
}

global $active_primary_navigation;

if (!isset($active_primary_navigation)) {
    $active_primary_navigation = jonathan_prepareGlobalPrimaryNavigation();
}

//if (!isset($partsfinder_link)) {
//    $CI->load->model("admin_m");
//    $store_name = $CI->admin_m->getAdminShippingProfile();
//    $partsfinder_link = $store_name["partsfinder_link"];
//}


$template = mustache_tmpl_open("mobile_navigation_fragment.html");
mustache_tmpl_set($template, "s_baseURL", $s_baseURL);
//if ($partsfinder_link != "") {
//    mustache_tmpl_set($template, "partsfinder_link", $partsfinder_link);
//}

// We have to dump out some more navigation items here in a list...
if (!isset($SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS) || !$SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS) {
    mustache_tmpl_set($template, "active_primary_navigation", $active_primary_navigation);
}


if (isset($GLOBAL_MOBILE_NAV_FRAG_STRING) && $GLOBAL_MOBILE_NAV_FRAG_STRING) {
    $GLOBAL_MOBILE_NAV_FRAG =  mustache_tmpl_parse($template);
} else {
    echo mustache_tmpl_parse($template);
}
