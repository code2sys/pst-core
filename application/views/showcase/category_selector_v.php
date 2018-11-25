<?php

$display_makes = false;
$display_machine_types = false;
$display_models = false;
$display_trims = false;
$showcasemake_id = null;
$showcasemodel_id = null;
$showcasemachinetype_id = null;
$showcasemakes = null;
$showcasemodels = null;
$showcasetrims = null;
$showcasemachinetypes = null;

global $PSTAPI;
initializePSTAPI();

$title = $pageRec["title"];
$full_url = "";

figureShowcaseFlags($pageRec, $display_makes, $display_machine_types, $display_models, $display_trims, $showcasemake_id, $showcasemodel_id, $showcasemachinetype_id, $full_url, $showcasemakes, $showcasemodels, $showcasetrims, $showcasemachinetypes);

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