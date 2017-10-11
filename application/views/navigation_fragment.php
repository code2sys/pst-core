<?php
$CI =& get_instance();

$CI->load->helper("mustache_helper");

global $active_primary_navigation;

if (!isset($active_primary_navigation)) {
    $active_primary_navigation = jonathan_prepareGlobalPrimaryNavigation();
}
//
//if (!isset($partsfinder_link)) {
//    $CI->load->model("admin_m");
//    $store_name = $CI->admin_m->getAdminShippingProfile();
//    $partsfinder_link = $store_name["partsfinder_link"];
//}

$number_across = count($active_primary_navigation);

//if (!defined("ENABLE_OEMPARTS_BUTTON") || !ENABLE_OEMPARTS_BUTTON) {
//    define("ENABLE_OEMPARTS_BUTTON", false);
//} else if (trim($partsfinder_link) == "") {
//    $number_across++;
//}

$template = mustache_tmpl_open("navigation_fragment.html");
mustache_tmpl_set($template, "number_across", $number_across);
//if ($partsfinder_link != "") {
//    mustache_tmpl_set($template, "partsfinder_link", $partsfinder_link);
//}

// We have to dump out some more navigation items here in a list...
mustache_tmpl_set($template, "active_primary_navigation", $active_primary_navigation);

echo mustache_tmpl_parse($template);
