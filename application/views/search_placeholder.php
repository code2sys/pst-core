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
$search_placeholder_template = mustache_tmpl_open("search_placeholder.html");
mustache_tmpl_set($search_placeholder_template, "HIDE_SEARCH", $hide_search);
mustache_tmpl_set($search_placeholder_template, "base_url", base_url());
mustache_tmpl_set($search_placeholder_template, "SEARCH_PLACEHOLDER_WORDING", SEARCH_PLACEHOLDER_WORDING);


if (array_key_exists("sm_show_upper_link", $SMSettings) && $SMSettings["sm_show_upper_link"] == 1) {
    mustache_tmpl_set($search_placeholder_template, "search_holder", getSocialLinkButtons);
} else {
    mustache_tmpl_set($search_placeholder_template, "search_holder", false);
}

if (array_key_exists("fltr", $_REQUEST)) {
    mustache_tmpl_set($search_placeholder_template, "fltr", $_REQUEST["fltr"]);
}

echo mustache_tmpl_parse($search_placeholder_template);