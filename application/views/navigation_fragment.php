<?php
$CI =& get_instance();

$CI->load->helper("mustache_helper");

if (!isset($partsfinder_link)) {
    $CI->load->model("admin_m");
    $store_name = $CI->admin_m->getAdminShippingProfile();
    $partsfinder_link = $store_name["partsfinder_link"];
}

if (!defined("ENABLE_OEMPARTS_BUTTON") || !ENABLE_OEMPARTS_BUTTON) {
    define("ENABLE_OEMPARTS_BUTTON", false);
    $number_across = "six";
} else {
    $number_across = trim($partsfinder_link) == "" ? "six" : "seven";
}

$template = mustache_tmpl_open("navigation_fragment.html");
mustache_tmpl_set($template, "number_across", $number_across);
if ($partsfinder_link != "") {
    mustache_tmpl_set($template, "partsfinder_link", $partsfinder_link);
}
echo mustache_tmpl_parse($template);
