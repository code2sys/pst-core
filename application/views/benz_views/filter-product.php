<?php
$new_assets_url = jsite_url("/qatesting/benz_assets/");
$media_url = jsite_url("/media/");


$CI =& get_instance();
$stock_status_mode = $CI->_getStockStatusMode();

if (isset($motorcycles) && is_array($motorcycles) && count($motorcycles) > 0) {
    // These are in the motorcycle loop...
    echo $CI->load->view("benz_views/product_motorcycleLoop", array(
        "motorcycles" => $motorcycles,
        "stock_status_mode" => $stock_status_mode,
        "new_assets_url" => $new_assets_url,
        "media_url" => $media_url
    ), true);

} else {
    // filter-product-nomatches.html
    $CI->load->helper("mustache_helper");
    $template = mustache_tmpl_open("benz_views/filter-product-nomatches.html");
    echo mustache_tmpl_parse($template);
}

$this->load->view("benz_views/product_pagination", array(
    "pages" => $pages,
    "cpage" => $page
), true);
