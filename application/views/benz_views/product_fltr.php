<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 9/3/18
 * Time: 11:43 AM
 */

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("benz_views/product_fltr.html");

mustache_tmpl_set($template, "MOTORCYCLE_SHOP_NEW", MOTORCYCLE_SHOP_NEW);
mustache_tmpl_set($template, "fltr_special_active", $_GET['fltr'] == 'special' );
mustache_tmpl_set($template, "fltr_new_active", $_GET['fltr'] == 'new' );
mustache_tmpl_set($template, "fltr_preowned_active", $_GET['fltr'] == 'pre-owned' );
mustache_tmpl_set($template, "major_units_featured_only", array_key_exists("major_units_featured_only", $_SESSION) && $_SESSION["major_units_featured_only"] > 0);

mustache_tmpl_set($template, "major_unit_search_keywords", htmlentities(array_key_exists("major_unit_search_keywords", $_SESSION) ? $_SESSION["major_unit_search_keywords"] : ""));

$ctgrs = explode('$', $_GET['categories']);
$ctgrs = array_filter($ctgrs);
foreach ($categories as $category) {
    $key = array_search($category['id'], $ctgrs);
    mustache_tmpl_iterate($template, "categories");
    mustache_tmpl_set($template, "categories", array(
        "category_id" => $category['id'],
        "checked" => $ctgrs[$key] == $category['id'],
        "category_name" => $category['name']
    ));
}

$brnds = explode('$', $_GET['brands']);
$brnds = array_filter($brnds);

foreach ($brands as $k => $brand) {
    $key = array_search($brand['make'], $brnds);
    mustache_tmpl_iterate($template, "brands");
    mustache_tmpl_set($template, "brands", array(
        "brand_make" => $brand['make'],
        "k" => $k,
        "checked" => $brnds[$key] == $brand['make']
    ));
}


$vhcls = explode('$', $_GET['vehicles']);
$vhcls = array_filter($vhcls);

foreach ($vehicles as $vehicle) {
    $key = array_search($vehicle['id'], $vhcls);
    mustache_tmpl_iterate($template, "vehicles");
    mustache_tmpl_set($template, "vehicles", array(
        "vehicle_id" => $vehicle['id'],
        "vehicle_name" => $vehicle['name'],
        "checked" => $vhcls[$key] == $vehicle['id']
    ));
}

$yr = explode('$', $_GET['years']);
$yr = array_filter($yr);

foreach ($years as $k => $year) {
    $key = array_search($year['year'], $yr);
    mustache_tmpl_iterate($template, "years");
    mustache_tmpl_set($template, "years", array(
        "k" => $k,
        "year" => $year['year'],
        "checked" => $yr[$key] == $year['year']
    ));
}

// there's a specifically-styled one


print mustache_tmpl_parse($template);
