<?php

$CI =& get_instance();

$CI =& get_instance();
$CI->load->helper("mustache_helper");

$major_unit_detail_modal_template = mustache_tmpl_open("modals/major_unit_detail_modal.html");

// Do we have the motorcycle variables?
if (isset($motorcycle) && array_key_exists("id", $motorcycle) && $motorcycle["id"] > 0) {
    mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle", true);
    mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle_id", $motorcycle["id"]);
    mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle_title", $motorcycle["title"]);
    mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle_color", $motorcycle["color"]);
    mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle_sku", $motorcycle["sku"]);
    mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle_vin_number", $motorcycle["vin_number"]);

    if (isset($motorcycle_image) && $motorcycle_image != "") {
        mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle_image", $motorcycle_image);
    } else {
        mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle_image", false);
    }

} else {
    mustache_tmpl_set($major_unit_detail_modal_template, "motorcycle", false);
}

mustache_tmpl_set($major_unit_detail_modal_template, "form_open_string", form_open('welcome/productEnquiry', array('class' => 'form_standard')));
mustache_tmpl_set($major_unit_detail_modal_template, "form_close_string", form_close());

echo mustache_tmpl_parse($major_unit_detail_modal_template);

echo $CI->load->view("models/major_unit_detail_modal_global_include", array(), true);
