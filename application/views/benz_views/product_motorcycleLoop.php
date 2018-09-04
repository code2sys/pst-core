<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 9/3/18
 * Time: 12:27 PM
 */

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("benz_views/product_motorcycleLoop.html");

foreach ($motorcycles as $motorcycle) {

    // What is the default...
    $motorcycle_image = $motorcycle['image_name'];
    if ($motorcycle['external'] == 0) {
        $motorcycle_image = $media_url . $motorcycle_image;
    }

    if ($motorcycle_image == "" || is_null($motorcycle_image) || $motorcycle_image == $media_url) {
        $motorcycle_image = "/assets/image_unavailable.png";
    }

    mustache_tmpl_iterate($template, "motorcycles");


    $motorcycle_action_buttons = mustache_tmpl_open("motorcycle_action_buttons.html");
    mustache_tmpl_set($motorcycle_action_buttons, "motorcycle_id", $motorcycle['id']);
    mustache_tmpl_set($motorcycle_action_buttons, "new_assets_url", $new_assets_url);
    if (!defined('GET_FINANCING_WORDING')) {
        define('GET_FINANCING_WORDING', 'GET FINANCING');
    }
    mustache_tmpl_set($motorcycle_action_buttons, "get_financing_wording", GET_FINANCING_WORDING);
    mustache_tmpl_set($motorcycle_action_buttons, "view_url", base_url(strtolower($motorcycle['type']) . '/' . $motorcycle['url_title'] . '/' . $motorcycle['sku']));

    mustache_tmpl_set($template, "motorcycles", array(
        "url" => base_url(strtolower($motorcycle['type']) . '/' . $motorcycle['url_title'] . '/' . $motorcycle['sku']),
        "motorcycle_type" => $motorcycle["type"],
        "motorcycle_url_title" => $motorcycle["url_title"],
        "motorcycle_sku" => clean_complex_sku($motorcycle),
        "motorcycle_image" => $motorcycle_image,
        "motorcycle_title" => $motorcycle["title"],
        "pricing_widget" => $CI->load->view("benz_views/pricing_widget", array(
            "motorcycle" => $motorcycle
        ), true),

        "major_unit_detail_modal" => $CI->load->view('modals/major_unit_detail_modal.php', array(
            'motorcycle'       => $motorcycle,
            'motorcycle_image' => $motorcycle_image,
        ), true),
        "trade_in_value_modal" => $CI->load->view('modals/trade_in_value_modal.php', array('motorcycle' => $motorcycle), true),
        "motorcycle_action_buttons" => mustache_tmpl_parse($motorcycle_action_buttons),
        "location_description" => $motorcycle["location_description"] != "" ? $motorcycle["location_description"] : false,
        "show_stock_status" => (($motorcycle['stock_status'] == 'In Stock' && $stock_status_mode >= 2 ) || ($motorcycle['stock_status'] != 'In Stock' && ($stock_status_mode == 1  || $stock_status_mode == 3))),
        "stock_status" => $motorcycle['stock_status'],
        "stock_status_code" => $motorcycle['stock_status'] == 'In Stock' ? 'green' : 'red',
        "engine_type" => $motorcycle['engine_type'] != '' ? $motorcycle['engine_type'] : false,
        "sku" => $motorcycle['sku'] != '' ? $motorcycle['sku'] : false,
        "engine_hours" => $motorcycle['engine_hours'] > 0 ? $motorcycle['engine_hours'] : false,
        "mileage" => $motorcycle['mileage'] > 0 ? $motorcycle['mileage'] : false,
        "color" => $motorcycle['color'] != "N/A" && $motorcycle['color'] != '' ? $motorcycle['color'] : false,
        "condition" => $motorcycle['condition'] == '1' ? 'New' : 'Pre-Owned'

    ));
}

print mustache_tmpl_parse($template);
