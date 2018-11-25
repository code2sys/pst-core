<?php


$CI =& get_instance();
$CI->load->helper("mustache_helper");
$category_selector_v = mustache_tmpl_open("showcase/category_selector_v.html");

mustache_tmpl_set($category_selector_v, "breadcrumbs", $CI->load->view("showcase/breadcrumbs", array(
    "title" => $title,
    "full_url" => $full_url
), true));


if (isset($widgetBlock) && $widgetBlock != "") {
    mustache_tmpl_set($category_selector_v, "widgetBlock", $widgetBlock);
}

print mustache_tmpl_parse($category_selector_v);