<?php
$CI =& get_instance();
$CI->load->helper("mustache_helper");

$sub_details_template = mustache_tmpl_open("benz_views/sub_product-details.html");

if (isset($override_breadcrumbs) && $override_breadcrumbs) {
    mustache_tmpl_set($sub_details_template, "override_breadcrumbs", true);
    mustache_tmpl_set($sub_details_template, "breadcrumbs", $breadcrumbs);
} else {
    mustache_tmpl_set($sub_details_template, "breadcrumb_url", base_url(strtolower($motorcycle['type']) . '/' . $motorcycle['url_title'] . '/' . $motorcycle['sku']));
    mustache_tmpl_set($sub_details_template, "breadcrumb_title", $motorcycle['title']);
}

mustache_tmpl_set($sub_details_template, "base_url", base_url());
mustache_tmpl_set($sub_details_template, "referUrl", $referUrl);



$menu_section_template = mustache_tmpl_open("benz_views/product-details/menu-section.html");
mustache_tmpl_set($menu_section_template, "motorcycle_id", $motorcycle['id']);
mustache_tmpl_set($menu_section_template, "base_url", jsite_url("/"));
mustache_tmpl_set($menu_section_template, "WORDING_SCHEDULE_TEST_DRIVE", defined('WORDING_SCHEDULE_TEST_DRIVE') ? WORDING_SCHEDULE_TEST_DRIVE : "SCHEDULE TEST DRIVE");
mustache_tmpl_set($menu_section_template, "GET_FINANCING_WORDING", defined('GET_FINANCING_WORDING') ? GET_FINANCING_WORDING : 'GET FINANCING');
mustache_tmpl_set($menu_section_template, "HAS_PAYMENT_CALCULATOR", $motorcycle["payment_option"]["active"] == 1 ? true : false);
mustache_tmpl_set($sub_details_template, "menu_section_template", mustache_tmpl_parse($menu_section_template));


// JLB 12-19-17
// If we have > 1 image, and we have a CRS thumbnail image in the mix, we don't show that.
if (!isset($filter_motorcycle_images) || $filter_motorcycle_images) {
    if (count($motorcycle['images']) > 1) {
        $clean_images = array();

        foreach ($motorcycle['images'] as $img) {
            if (!($img['crs_thumbnail'] > 0)) {
                $clean_images[] = $img;
            }
        }
        $motorcycle['images'] = $clean_images;
    }

    // You have to fix them...
    foreach ($motorcycle["images"] as &$img) {
        $img["url"] = $img["image_name"];
        if ($img["external"] == 0) {
            $img["url"] = $media_url. $img["url"];
        }
    }

}
mustache_tmpl_set($sub_details_template, "image_loop", $motorcycle['images']);
mustache_tmpl_set($sub_details_template, "motorcycle_title", $motorcycle["title"]);

$info_block_template = mustache_tmpl_open("benz_views/product-details/info_block.html");

// For some of these, we just set them...
foreach (array(
             "year", "make", "model", "type", "category", "engine_type", "transmission", "vin_number", "color", "mileage", "engine_hours"
         ) as $key) {

    if (array_key_exists($key, $motorcycle) && !is_null($motorcycle[$key]) && $motorcycle[$key] != "") {
        if (!in_array($key, array("color", "mileage", "engine_hours")) || ($key == "color" && $motorcycle[$key] != 'N/A') || ($key == "mileage" && $motorcycle["mileage"] > 0)|| ($key == "engine_hours" && $motorcycle["engine_hours"] > 0)) {
            mustache_tmpl_set($info_block_template, $key, $motorcycle[$key]);
        }
    }
}

mustache_tmpl_set($info_block_template, "hide_stock_information", isset($hide_stock_information) && $hide_stock_information);

if (!isset($hide_stock_information) || !$hide_stock_information) {
    mustache_tmpl_set($info_block_template, "condition" . $motorcycle['condition'], true);
    mustache_tmpl_set($info_block_template, "stock_status", $motorcycle["stock_status"]);
    // but we also have to do the in stock
    mustache_tmpl_set($info_block_template, "stock_status_in_stock", $motorcycle["stock_status"] == "In Stock");
    mustache_tmpl_set($info_block_template, "stock_status_big_flag", (($motorcycle['stock_status'] == 'In Stock' && $stock_status_mode >= 2) || ($motorcycle['stock_status'] != 'In Stock' && ($stock_status_mode == 1 || $stock_status_mode == 3))));

    mustache_tmpl_set($info_block_template, "clean_complex_SKU", clean_complex_sku($motorcycle));

    if ($motorcycle["location_description"] != "") {
        mustache_tmpl_set($info_block_template, "location_description", $motorcycle["location_description"]);

    } else {
        mustache_tmpl_set($info_block_template, "location_description", $store_name['city'] . ', ' . $store_name['state']);
    }

}

mustache_tmpl_set($sub_details_template, "info_block_template", mustache_tmpl_parse($info_block_template));

if (isset($override_pricing_widget) && $override_pricing_widget) {
    mustache_tmpl_set($sub_details_template, "pricing_widget", $pricing_widget);
} else {
    mustache_tmpl_set($sub_details_template, "pricing_widget", $CI->load->view("benz_views/pricing_widget", array(
        "motorcycle" => $motorcycle,
        "payment_option" => $motorcycle["payment_option"]
    ), true));
}

if (!isset($hide_recently_viewed) || !$hide_recently_viewed) {
    $specwidth = "col-md-9 col-sm-8 col-xs-12";

    mustache_tmpl_set($sub_details_template, "recently_viewed", $CI->load->view("benz_views/recently_viewed", array(
        "master_class" => "col-md-3 col-xs-12 fltrbar pull-right pdig oder col-sm-4",
        "subclass" => "col-xs-12",
        "innersubclass" => "",
        "recentlyMotorcycle" => $recentlyMotorcycle,
        "no_fify" => true
    ), true));
} else {
    $specwidth = "col-md-12 col-sm-12 col-xs-12";
}

mustache_tmpl_set($sub_details_template, "specwidth", $specwidth);


$show_info = !empty($mainVideo) || (trim($motorcycle['description']) != "");
mustache_tmpl_set($sub_details_template, "show_info", $show_info);
$show_spec = (count($motorcycle['specs']) > 0);
mustache_tmpl_set($sub_details_template, "show_spec", $show_spec);

if (!empty($mainVideo)) {
    mustache_tmpl_set($sub_details_template, "embedded_videos", $CI->load->view("master/embedded_videos", array(
        "class_name" => "main-vdo",
        "mainVideo" => $mainVideo['video_url'],
        "mainTitle" => $mainVideo['title'],
        "video" => $motorcycle['videos'],
        "rltdvdo_class" => "rltv-vdo",
        "autoplay" => false
    ), true));
}

mustache_tmpl_set($sub_details_template, "description", $motorcycle["description"]);

// Specs are a loop...
if (count($motorcycle['specs']) > 0) {
    $feature_name = "";
    $attributes = array();
    $k = 0;
    foreach ($motorcycle["specs"] as $s) {
        if ($feature_name != $s["spec_group"]) {
            if ($feature_name != "") {
                mustache_tmpl_iterate($sub_details_template, "specs_loop");
                mustache_tmpl_set($sub_details_template, "specs_loop", array(
                    "feature_name" => $feature_name,
                    "attributes" => $attributes
                ));
            }
            $feature_name = $s["spec_group"];
            $attributes = array();
            $k = 0;
        }
        $s["k"] = $k;
        $attributes[] = $s;
        $k = 1 - $k;
    }

    if (count($attributes) > 0) {
        mustache_tmpl_iterate($sub_details_template, "specs_loop");
        mustache_tmpl_set($sub_details_template, "specs_loop", array(
            "feature_name" => $feature_name,
            "attributes" => $attributes
        ));
    }
}

mustache_tmpl_set($sub_details_template, "tab_switcher", $show_info && $show_spec);
$image_url = ($image_url == "" || is_null($image_url) || $image_url == $media_url) ? "/assets/image_unavailable.png" : $image_url;

if (!preg_match("/^http(s)?/", $image_url)) {
    $image_url = base_url("/media/" . $image_url);
}

mustache_tmpl_set($sub_details_template, "image_url", $image_url);
mustache_tmpl_set($sub_details_template, "url_image_url", urlencode($image_url));
mustache_tmpl_set($sub_details_template, "MAJOR_UNIT_PAUSE_TIME", defined('MAJOR_UNIT_PAUSE_TIME') ? MAJOR_UNIT_PAUSE_TIME : 2000);


mustache_tmpl_set($sub_details_template, "showvideo_function", $CI->load->view("showvideo_function", array(), false));



if (!isset($in_showroom)) {
    $in_showroom = false;
}

mustache_tmpl_set($sub_details_template, "major_unit_detail_modal", $this->view('modals/major_unit_detail_modal.php', array(
    'in_showroom' => $in_showroom,
    'motorcycle'       => $motorcycle,
    'motorcycle_image' => $image_url,
), true));
mustache_tmpl_set($sub_details_template, "trade_in_value_modal", $this->view('modals/trade_in_value_modal.php', array('in_showroom' => $in_showroom, 'motorcycle' => $motorcycle), true));
if ($motorcycle["payment_option"]["active"] == 1) {
    mustache_tmpl_set($sub_details_template, "major_unit_payment_calculator_modal", $this->view('modals/major_unit_payment_calculator_modal.php', array(
        'in_showroom' => $in_showroom, 
        'motorcycle' => $motorcycle,
        "payment_option" => $motorcycle["payment_option"]
    ), true));
}
mustache_tmpl_set($sub_details_template, "customer_exit_modal", $this->view('modals/customer_exit_modal.php', array('in_showroom' => $in_showroom), true));

mustache_tmpl_set($sub_details_template, "motorcycle_id", $motorcycle["id"]);

echo mustache_tmpl_parse($sub_details_template);
