<?php

/*
        JLB 04-06-17
        There was inconsistent usage so	these things stopped working. I	attempt	to standardize the variables for the meta tags and title.
 */
$page_title = "";
if (isset($title) && $title != "") {
        $page_title = $title;
} else if (isset($pageRec) && is_array($pageRec) && array_key_exists("title", $pageRec) && $pageRec["title"] != "") {
        $page_title = $pageRec["title"];
}

$meta_description = "";
if (isset($descr) && $descr != "") {
        $meta_description = $descr;
} else if (isset($pageRec) && is_array($pageRec) && array_key_exists("descr", $pageRec) && $pageRec["descr"] != "") {
        $meta_description = $pageRec["descr"];
} else if (isset($pageRec) && is_array($pageRec) && array_key_exists("metatags", $pageRec) && $pageRec["metatags"] != "") {
        $meta_description = $pageRec["metatags"];
}

$meta_keywords = "";
if (isset($keywords) &&	$keywords != "") {
        $meta_keywords = $keywords;
} else if (isset($pageRec) & is_array($pageRec)	&& array_key_exists("keywords",	$pageRec) && $pageRec["keywords"] != "") {
        $meta_keywords = $pageRec["keywords"];
}

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$master_v_front_template = mustache_tmpl_open("master/master_v_front.html");
$new_assets_url = jsite_url("/qatesting/benz_assets/");
mustache_tmpl_set($master_v_front_template, "store_block_top_header", jget_store_block("top_header"));
mustache_tmpl_set($master_v_front_template, "page_title", $page_title);
mustache_tmpl_set($master_v_front_template, "top_header", $CI->load->view("master/top_header", array(
    "store_name" => $store_name,
    "meta_description" => $meta_description,
    "meta_keywords" => $meta_keywords
), true));
if (isset($metatag) && $metatag != "") {
    mustache_tmpl_set($master_v_front_template, "metatag", $metatag);
}
mustache_tmpl_set($master_v_front_template, "new_assets_url", $new_assets_url);
mustache_tmpl_set($master_v_front_template, "ENVIRONMENT", ENVIRONMENT);
mustache_tmpl_set($master_v_front_template, "base_url", base_url());
mustache_tmpl_set($master_v_front_template, "s_baseURL", $s_baseURL);
mustache_tmpl_set($master_v_front_template, "shopping_cart_count", (array_key_exists("cart", $_SESSION) && array_key_exists("qty", $_SESSION["cart"]) && $_SESSION['cart']['qty'] > 0) ? $_SESSION['cart']['qty'] : 0);
mustache_tmpl_set($master_v_front_template, "basebranding_url", jsite_url("/basebranding.css"));
mustache_tmpl_set($master_v_front_template, "custom_url", jsite_url("/custom.css"));
mustache_tmpl_set($master_v_front_template, "store_block_bottom_header", jget_store_block("bottom_header"));
mustache_tmpl_set($master_v_front_template, "store_block_top_body", jget_store_block("top_body"));
mustache_tmpl_set($master_v_front_template, "mainheader", $CI->load->view("master/widgets/mainheader", array(
    "store_name" => $store_name,
    "s_baseURL" => $s_baseURL,
    "invoking_page" => "master_v_front"
), true));
$motorcycle_action_buttons = mustache_tmpl_open("store_header_marquee.html");
mustache_tmpl_set($master_v_front_template, "store_header_marquee", mustache_tmpl_parse($motorcycle_action_buttons));
$motorcycle_action_buttons = mustache_tmpl_open("store_header_banner.html");
mustache_tmpl_set($master_v_front_template, "store_header_banner", mustache_tmpl_parse($motorcycle_action_buttons));
if (isset($bannerImages) && count($bannerImages) > 0) {
    mustache_tmpl_set($master_v_front_template, "homepage_main_slider", $this->load->view("master/widgets/homepage_main_slider", array(
        "bannerImages" => $bannerImages
    ), true));
}
if (isset($featured) && is_array($featured) && count($featured) > 0) {
    mustache_tmpl_set($master_v_front_template, "motorcycles_widget", $CI->load->view("master/widgets/motorcycles", array(
        "featured" => $featured
    ), true));
}
if (isset($topVideo) && is_array($topVideo) && count($topVideo) > 0) {
    mustache_tmpl_set($master_v_front_template, "top_video_widget", $CI->load->view("master/widgets/homepage_top_videos", array(
        "topVideo" => $topVideo
    ), true));
}
if (isset($featuredCategories) && is_array($featuredCategories) && count($featuredCategories) > 0) {
    mustache_tmpl_set($master_v_front_template, "featured_categories_widget", $CI->load->view("master/widgets/homepage_featured_categories", array(
        "featuredCategories" => $featuredCategories
    ), true));
}
if (isset($featuredBrands) && count($featuredBrands) > 0) {
    mustache_tmpl_set($master_v_front_template, "top_brands_widget", $CI->load->view("master/widgets/homepage_top_brands_widget", array(
        "featuredBrands" => $featuredBrands
    ), true));
}
if (isset($notice) && $notice != "") {
    mustache_tmpl_set($master_v_front_template, "notice_widget", $CI->load->view("master/widgets/homepage_notice_widget", array(
        "notice" => $notice
    ), true));
}
if (isset($footer)) {
    mustache_tmpl_set($master_v_front_template, "footer", $footer);
}
if (isset($header)) {
    mustache_tmpl_set($master_v_front_template, "header", $header);
}
mustache_tmpl_set($master_v_front_template, "WEBSITE_NAME", WEBSITE_NAME);

mustache_tmpl_set($master_v_front_template, "flexiselect",  $CI->load->view("master/widgets/flexiselect", array(), true));

if (isset($script)) {
    mustache_tmpl_set($master_v_front_template, "script", $script);
}

mustache_tmpl_set($master_v_front_template, "tracking", $CI->load->view("master/tracking", array(
    "store_name" => $store_name	,
    "product" => @$product,
    "ga_ecommerce" => true,
    "show_ga_conversion" => false

), true));
mustache_tmpl_set($master_v_front_template, "ride_selection_js", $CI->load->view("widgets/ride_selection_js", array(
    "product" => isset($product) ? $product : null,

), true));
mustache_tmpl_set($master_v_front_template, "showvideo_function", $CI->load->view("showvideo_function", array(), true));
mustache_tmpl_set($master_v_front_template, "custom_js_url", jsite_url('/custom.js'));
mustache_tmpl_set($master_v_front_template, "bottom_footer", $CI->load->view("master/bottom_footer", array(
    "store_name" => $store_name
), true));
mustache_tmpl_set($master_v_front_template, "bottom_body", jget_store_block("bottom_body"));
mustache_tmpl_set($master_v_front_template, "customer_exit_modal", $this->load->view('modals/customer_exit_modal.php', array(), true));


mustache_tmpl_set($master_v_front_template, "trade_in_value_modal_generic", $CI->load->view("modals/trade_in_value_modal", array(), true));

// these are just here, you know.
for ($i = 1; $i <= 3; $i++) {
    $name = "master_v_front_extra$i";
    $$name = mustache_tmpl_open("master/$name");
    mustache_tmpl_set($master_v_front_template, $name, mustache_tmpl_parse($$name));
}

echo mustache_tmpl_parse($master_v_front_template);

