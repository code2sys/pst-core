<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 9/3/18
 * Time: 12:07 PM
 */

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("benz_views/product_bikeControlRow.html");

$bikeControlShow = $_SESSION["bikeControlShow"];
$bikeControlSort = $_SESSION["bikeControlSort"];

foreach (array(5, 10, 25, 50) as $show) {
    mustache_tmpl_iterate($template, "show_loop");
    mustache_tmpl_set($template, "show_loop", array(
        "number" => $show,
        "selected" => $bikeControlShow == $show
    ));
}
// This is just in case you want to rebuild it otherwise..
mustache_tmpl_set($template, "bikeControlShow$bikeControlShow", true);

foreach (array(
    0 => "Relevance",
    1 => "Price (High to Low)",
    2 => "Price (Low to High)",
    3 => "Year (New to Old)",
    4 => "Year (Old to New)"

        ) as $value => $label) {

    mustache_tmpl_iterate($template, "control_loop");
    mustache_tmpl_set($template, "control_loop", array(
        "value" => $value,
        "label" => $label,
        "selected" => $bikeControlSort == $value
    ));
}
// This is just in case you want to rebuild it otherwise..
mustache_tmpl_set($template, "bileControlSort$bikeControlSort", true);

mustache_tmpl_set($template, "major_units_featured_only", array_key_exists("major_units_featured_only", $_SESSION) && $_SESSION["major_units_featured_only"] > 0);

print mustache_tmpl_parse($template);