<?php

$retail_price_zero = $motorcycle["retail_price"] === "" || is_null($motorcycle["retail_price"]) || ($motorcycle["retail_price"] == "0.00") || ($motorcycle["retail_price"] == 0) || (floatVal($motorcycle["retail_price"]) < 0.01);

$sale_price_zero =$motorcycle["retail_price"] === "" || is_null($motorcycle["sale_price"]) || ($motorcycle["sale_price"] == "0.00") || ($motorcycle["sale_price"] == 0) || (floatVal($motorcycle["sale_price"]) < 0.01);

$CI =& get_instance();
$CI->load->helper("mustache_helper");

$pricing_widget_template = mustache_tmpl_open("benz_views/pricing_widget.html");

if( $motorcycle['call_on_price'] == '1' ||  ($retail_price_zero && $sale_price_zero) ) {
    mustache_tmpl_set($pricing_widget_template, "CALL_FOR_PRICE", true);
} else {
    if (!$sale_price_zero && $motorcycle["sale_price"] != $motorcycle["retail_price"]) {
        mustache_tmpl_set($pricing_widget_template, "SHOW_SALE_PRICE", true);
        if (!$retail_price_zero) {
            mustache_tmpl_set($pricing_widget_template, "SHOW_RETAIL_PRICE", true);
        }
    } else {
        // just retail
        mustache_tmpl_set($pricing_widget_template, "SHOW_RETAIL_PRICE", true);
    }

    if ($motorcycle["destination_charge"]) {
        mustache_tmpl_set($pricing_widget_template, "SHOW_DEST_CHARGE", true);
    }

    // embed them.
    mustache_tmpl_set($pricing_widget_template, "retail_price", $motorcycle["retail_price"]);
    mustache_tmpl_set($pricing_widget_template, "sale_price", $motorcycle["sale_price"]);
}


echo mustache_tmpl_parse($pricing_widget_template);
