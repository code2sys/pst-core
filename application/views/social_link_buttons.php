<?php
if (!isset($SMSettings)) {
    $CI =& get_instance();
    $CI->load->model("admin_m");
    $SMSettings = $CI->admin_m->getSMSettings();
}

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("social_link_buttons.html");

foreach (array() as $link) {
    if (array_key_exists($link, $SMSettings)) {
        mustache_tmpl_set($template, $link, $SMSettings[$link]);
    }
}

echo mustache_tmpl_parse($template);

