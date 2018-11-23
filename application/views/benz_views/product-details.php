<?php
$mainVideo = $motorcycle['videos'][0];
unset($motorcycle['videos'][0]);
//include('header.php');
	$new_assets_url = jsite_url( "/qatesting/newassets/" );
	$media_url = jsite_url(  "/media/" );
	// echo "<pre>";
	// print_r($media_url.$motorcycle['images'][0]['image_name']);
	// echo "</pre>";

$CI =& get_instance();
$stock_status_mode = $CI->_getStockStatusMode();

echo $CI->load->view("benz_views/sub_product-details", array(
    "override_breadcrumbs" => false,
    "referUrl" => $referUrl,
    "motorcycle" => $motorcycle,
    "filter_motorcycle_images" => true,
    "new_assets_url" => $new_assets_url,
    "media_url" => $media_url,
    "mainVideo" => $mainVideo,
    "override_pricing_widget" => false,
    "hide_stock_information" => false,
    "hide_recently_viewed" => false,
    "image_url" => count($motorcycle["images"]) > 0 ? $motorcycle["images"][0] : ""
), true);