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
$extra_navigation = array();
// You have to explicitly turn NEW ON.
if (defined("MOTORCYCLE_SHOP_NEW") && MOTORCYCLE_SHOP_NEW) {
    $extra_navigation[] = array(
        "url" => "/Motorcycle_List?fltr=new&search_keywords=",
        "mobile_label" => "New Units"
    );
}

// This was how it was done in motorcycles.php. You have to explicitly turn used OFF.
if (!defined("MOTORCYCLE_SHOP_USED") || MOTORCYCLE_SHOP_USED) {
    $extra_navigation[] = array(
        "url" => "/Motorcycle_List?fltr=new&search_keywords=",
        "mobile_label" => "Pre-Owned Units"
    );
}

// OK, we only include the service if one of two things: You included the other two, OR you defined a service page URL constant.
if (count($extra_navigation) > 0 || defined('MOTORCYCLE_SERVICE_LINK_URL')) {
    $extra_navigation[] = array(
        "url" => defined('MOTORCYCLE_SERVICE_LINK_URL') ? MOTORCYCLE_SERVICE_LINK_URL : "/pages/index/servicerequest",
        "mobile_label" => "Service",
    );
}

mustache_tmpl_set($mobile_navigation_fragment_template, "extra_navigation", $extra_navigation);


$mobile_navigation_fragment_template = mustache_tmpl_open("mobile_navigation_fragment.html");
mustache_tmpl_set($mobile_navigation_fragment_template, "s_baseURL", $s_baseURL);
//if ($partsfinder_link != "") {
//    mustache_tmpl_set($mobile_navigation_fragment_template, "partsfinder_link", $partsfinder_link);
//}

// We have to dump out some more navigation items here in a list...
if (!isset($SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS) || !$SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS) {
    mustache_tmpl_set($mobile_navigation_fragment_template, "active_primary_navigation", $active_primary_navigation);
}


if (isset($GLOBAL_MOBILE_NAV_FRAG_STRING) && $GLOBAL_MOBILE_NAV_FRAG_STRING) {
    $GLOBAL_MOBILE_NAV_FRAG =  mustache_tmpl_parse($mobile_navigation_fragment_template);
} else {
    echo mustache_tmpl_parse($mobile_navigation_fragment_template);
}
