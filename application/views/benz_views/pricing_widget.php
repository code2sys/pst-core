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
        // JLB 12-24-18
        // Only show the retail price AND the sale price if the sale price < retail_price
        if (!$retail_price_zero && (floatVal($motorcycle["sale_price"]) < floatVal($motorcycle["retail_price"]))) {
            mustache_tmpl_set($pricing_widget_template, "SHOW_RETAIL_PRICE", true);
        }
    } else {
        // just retail
        mustache_tmpl_set($pricing_widget_template, "SHOW_RETAIL_PRICE", true);
    }
    
    // display down payment
    if ($payment_option["active"] == 1 && $payment_option["display_base_payment"] == 1)
    {
        $price = $sale_price_zero ? $motorcycle["retail_price"] : $motorcycle["sale_price"];
        $moneydown = $payment_option["base_down_payment"];
        $interest = $payment_option["data"]["interest_rate"];
        $months = $payment_option["data"]["term"];
        $principal = $price - $moneydown;
        $month_interest = ($interest / (12 * 100));
        $monthly_payment = $principal * ($month_interest / (1 - pow((1 + $month_interest), -$months) ));
        mustache_tmpl_set($pricing_widget_template, "PAYMENT_TEXT", $payment_option["base_payment_text"]);
        mustache_tmpl_set($pricing_widget_template, "MONTLY_PAYMENT", number_format($monthly_payment, 2));
        mustache_tmpl_set($pricing_widget_template, "INTEREST_RATE", number_format($interest, 2));
        mustache_tmpl_set($pricing_widget_template, "MONTHS", $months);
        mustache_tmpl_set($pricing_widget_template, "DOWN_PAYMENT", $moneydown);
        mustache_tmpl_set($pricing_widget_template, "MOTORCYCLE_ID", $motorcycle['id']);
    }

    if ($motorcycle["destination_charge"]) {
        mustache_tmpl_set($pricing_widget_template, "SHOW_DEST_CHARGE", true);
    }

    // embed them.
    mustache_tmpl_set($pricing_widget_template, "retail_price", $motorcycle["retail_price"]);
    mustache_tmpl_set($pricing_widget_template, "sale_price", $motorcycle["sale_price"]);
}


echo mustache_tmpl_parse($pricing_widget_template);
