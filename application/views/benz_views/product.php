<?php

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("benz_views/product.html");


$new_assets_url = jsite_url("/qatesting/newassets/");
$media_url = jsite_url("/media/");

// JLB 12-26-17
// We have to retrofit this because the Benz guys never, ever thought to be consistent in naming.
if (MOTORCYCLE_SHOP_NEW) {
    if (!array_key_exists("fltr", $_GET)) {
        if (array_key_exists("condition", $filter) && $filter["condition"] == 2) {
            $_GET["fltr"] = "special";
        } else if (array_key_exists("condition", $filter) && $filter["condition"] == 1) {
            $_GET["fltr"] = "new";
        } else {
            $_GET["fltr"] = "pre-owned";
        }
    }
} else {
    if (array_key_exists("condition", $filter) && $filter["condition"] == 2) {
        $_GET["fltr"] = "special";
    } else {
        $_GET["fltr"] = "pre-owned";
    }
}


if (!array_key_exists("brands", $_GET) && array_key_exists("brands", $filter) && is_array($filter["brands"])) {
    $_GET["brands"] = implode("$", $filter["brands"]);
}

if (!array_key_exists("years", $_GET) && array_key_exists("years", $filter) && is_array($filter["years"])) {
    $_GET["years"] = implode("$", $filter["years"]);
}

if (!array_key_exists("categories", $_GET) && array_key_exists("categories", $filter) && is_array($filter["categories"])) {
    $_GET["categories"] = implode("$", $filter["categories"]);
}

if (!array_key_exists("vehicles", $_GET) && array_key_exists("vehicles", $filter) && is_array($filter["vehicles"])) {
    $_GET["vehicles"] = implode("$", $filter["vehicles"]);
}


$CI =& get_instance();
$stock_status_mode = $CI->_getStockStatusMode();

// First, we need to indicate the filter..
if (array_key_exists("fltr", $_REQUEST) && $_REQUEST["fltr"] == "pre-owned") {
    mustache_tmpl_set($template, "fltr_preowned", 1);
    mustache_tmpl_set($template, "preowned_new_flag", 1);
} else if (array_key_exists("fltr", $_REQUEST) && $_REQUEST["fltr"] == "special") {
    mustache_tmpl_set($template, "fltr_special", 1);
    mustache_tmpl_set($template, "preowned_new_flag", 2);
} else {
    mustache_tmpl_set($template, "fltr_new", 1);
    mustache_tmpl_set($template, "preowned_new_flag", 0);
}

// These float along the bottom.
if (count($recentlyMotorcycle) > 0) {
    mustache_tmpl_set($template, "recently_viewed", $this->load->view("benz_views/recently_viewed", array(
        "master_class" => "fltrbar search-two my-wdt",
        "subclass" => "",
        "innersubclass" => "",
        "recentlyMotorcycle" => $recentlyMotorcycle
    ), true));

    mustache_tmpl_set($template, "desktop_recently_viewed", $this->load->view("benz_views/recently_viewed", array(
        "master_class" => "",
        "subclass" => "search-one flat fit-none",
        "innersubclass" => "search-one fit-none",
        "recentlyMotorcycle" => $recentlyMotorcycle,
    ), true));
}

// This is the left filter bar
mustache_tmpl_set($template, "product_fltr", $this->load->view("benz_views/product_fltr", array(
    "recentlyMotorcycle" => $recentlyMotorcycle,
    "categories" => $categories,
    "vehicles" => $vehicles,
    "years" => $years,
    "brands" => $brands
), true));

// This is the control row... It figures it out itself from session.
mustache_tmpl_set($template, "product_bikeControlRow", $this->load->view("benz_views/product_bikeControlRow", array(
), true));

// This is the  pagination across the bottom
mustache_tmpl_set($template, "product_pagination", $this->load->view("benz_views/product_pagination", array(
    "pages" => $pages
), true));


// These are in the motorcycle loop...
mustache_tmpl_set($template, "product_motorcycleLoop", $this->load->view("benz_views/product_motorcycleLoop", array(
    "motorcycles" => $motorcycles,
    "stock_status_mode" => $stock_status_mode,
    "new_assets_url" => $new_assets_url,
    "media_url" => $media_url
), true));






print mustache_tmpl_parse($template);

$CI->load->view('modals/major_unit_generic_modal.php');
$CI->load->view('modals/customer_exit_modal.php');
