<?php
if (!defined('SEARCH_PLACEHOLDER_WORDING')) {
    define('SEARCH_PLACEHOLDER_WORDING', 'Search Parts and Apparel');
}

$CI =& get_instance();

if (!isset($SMSettings)) {
    $CI->load->model("admin_m");
    $SMSettings = $CI->admin_m->getSMSettings();
}

if (!isset($hide_search)) {
    $hide_search = false;
}

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("search_placeholder.html");
mustache_tmpl_set($template, "HIDE_SEARCH", $hide_search);
mustache_tmpl_set($template, "base_url", base_url());
mustache_tmpl_set($template, "SEARCH_PLACEHOLDER_WORDING", SEARCH_PLACEHOLDER_WORDING);


if (array_key_exists("sm_show_upper_link", $SMSettings) && $SMSettings["sm_show_upper_link"] == 1) {
    mustache_tmpl_set($template, "search_holder", $CI->load->view("social_link_buttons", array("SMSettings" => $SMSettings), true));
} else {
    mustache_tmpl_set($template, "search_holder", false);
}

echo mustache_tmpl_parse($template);