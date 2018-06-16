<?php
$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("master/widgets/mainheader.html");

// the following builds up the address
mustache_tmpl_set($template, "street_address", $store_name['street_address']);
mustache_tmpl_set($template, "address_2", $store_name['address_2']);
mustache_tmpl_set($template, "city", $store_name['city']);
mustache_tmpl_set($template, "state", $store_name['state']);
mustache_tmpl_set($template, "zip", $store_name['zip']);
mustache_tmpl_set($template, "phone", $store_name['phone']);
mustache_tmpl_set($template, "email", $store_name['email']);

mustache_tmpl_set($template, "s_baseURL", $s_baseURL);


if (isset($invoking_page)) {
    if ($invoking_page == "master_v_front" || $invoking_page == "master_v_bikefront") {
        if (FALSE !== ($string = joverride_viewpiece("master-master_v_front-1"))) {
            mustache_tmpl_set($template, "master-master_v_front-1", $string);
        }
    }

    if ($invoking_page == "master_v_bikefront") {
        mustache_tmpl_set($template, "display_shopping_navigation", false);
    } else if ($invoking_page == "benz_views_header") {
        if (!defined('SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS')) {
            define('SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS', true);
        }
        mustache_tmpl_set($template, "display_shopping_navigation", !SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS);
    } else {
        mustache_tmpl_set($template, "display_shopping_navigation", true);
    }

} else {
    mustache_tmpl_set($template, "display_shopping_navigation", true);
}

mustache_tmpl_set($template, "shopping_count", (array_key_exists("cart", $_SESSION) && array_key_exists("qty", $_SESSION['cart']) && $_SESSION['cart']['qty'] > 0)  ? $_SESSION['cart']['qty'] : 0);


$GLOBAL_MOBILE_NAV_FRAG_STRING = true;
require(__DIR__ . "/../../mobile_navigation_fragment.php");
mustache_tmpl_set($template, "mobile_navigation_menu", $GLOBAL_MOBILE_NAV_FRAG);
$GLOBAL_MOBILE_NAV_FRAG_STRING = false;

mustache_tmpl_set($template, "CLEAN_PHONE_NUMBER", CLEAN_PHONE_NUMBER);

mustache_tmpl_set($template, "search_placeholder", $CI->load->view("search_placeholder", array(), true));

$GLOBAL_NAV_FRAG_STRING = true;
require(__DIR__ . "/../../navigation_fragment.php");
mustache_tmpl_set($template, "navigation_fragment", $GLOBAL_NAV_FRAG);
$GLOBAL_NAV_FRAG_STRING = false;

if (array_key_exists("userRecord", $_SESSION) && $_SESSION["userRecord"]) {
    mustache_tmpl_set($template, "userRecord_show", true);
    mustache_tmpl_set($template, "userRecord_firstname", $_SESSION['userRecord']['first_name']);

    mustache_tmpl_set($template, "userRecord_admin", $_SESSION['userRecord']['admin'] || $_SESSION['userRecord']['user_type'] == 'employee');
} else {
    mustache_tmpl_set($template, "userRecord_show", false);
}

if (!isset($SMSettings)) {
    $CI->load->model("admin_m");
    $SMSettings = $CI->admin_m->getSMSettings();
}
if (array_key_exists("sm_show_upper_link", $SMSettings)) {
    mustache_tmpl_set($template, "social_link_buttons", $CI->load->view("social_link_buttons", array("SMSettings" => $SMSettings), true));
} else {
    mustache_tmpl_set($template, "social_link_buttons", false);
}


echo mustache_tmpl_parse($template);