<?php

$CI =& get_instance();

$CI =& get_instance();
$CI->load->helper("mustache_helper");

$major_unit_payment_calculator_modal_template = mustache_tmpl_open("modals/major_unit_payment_calculator_modal.html");

$show_template = true;

// Do we have the motorcycle variables?
if (isset($motorcycle) && array_key_exists("id", $motorcycle) && $motorcycle["id"] > 0) {
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle", true);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_id", $motorcycle["id"]);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_title", $motorcycle["title"]);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_color", $motorcycle["color"]);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_sku", $motorcycle["sku"]);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_display_sku", clean_complex_sku($motorcycle));
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_vin_number", $motorcycle["vin_number"]);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_color", $motorcycle["color"]);

    $sale_price = $motorcycle["sale_price"];
    $retail_price = $motorcycle["retail_price"];
    $saving = $retail_price - $sale_price;
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_retail_price", $retail_price > 0.1 ? number_format($retail_price, 2) : false);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_sale_price", $sale_price > 0.1 ? number_format($sale_price, 2) : false);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_saving_price", $saving > 0 ? number_format($saving, 2) : false);

    if (isset($motorcycle_image) && $motorcycle_image != "") {
        mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_image", $motorcycle_image);
    } else {
        mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle_image", false);
    }

} else {
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "motorcycle", false);

    // JLB: I don't want this thing shown more than once.
    global $majorUnitGenericModal;

    if (!isset($majorUnitGenericModal)) {
        $majorUnitGenericModal = false;
    }
    $show_template = !$majorUnitGenericModal;
    $majorUnitGenericModal = true;
}

if (isset($payment_option)) {

    mustache_tmpl_set($major_unit_payment_calculator_modal_template, 'display_base_payment', true);
    $price = $sale_price_zero ? $motorcycle['retail_price'] : $motorcycle['sale_price'];
    $moneydown = $payment_option["base_down_payment"];
    $interest = $payment_option["data"]["interest_rate"];
    $months = $payment_option["data"]["term"];
    $principal = $price - $moneydown;
    $month_interest = ($interest / (12 * 100));
    $monthly_payment = $principal * ($month_interest / (1 - pow((1 + $month_interest), -$months) ));
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "fine_print", $payment_option["data"]["fine_print"]);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "payment_text", $payment_option["base_payment_text"]);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "down_payment", $moneydown);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "interest_rate", $interest);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "term", $months);
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "monthly_payment", number_format($monthly_payment, 2));

    if (!empty($payment_option['data']['down_payment_options'])) {
        mustache_tmpl_set($major_unit_payment_calculator_modal_template, "has_down_payment_options", true);
        foreach($payment_option['data']['down_payment_options'] as $option) {
            mustache_tmpl_iterate($major_unit_payment_calculator_modal_template, "down_payment_options");
            mustache_tmpl_set($major_unit_payment_calculator_modal_template, "down_payment_options", array(
                'title' => '$ '.number_format($option, 2),
                'value' => $option,
            ));
        }
    }
    if (!empty($payment_option['data']['terms'])) {
        mustache_tmpl_set($major_unit_payment_calculator_modal_template, "has_terms", true);
        $index = 0;
        foreach($payment_option['data']['terms'] as $option) {
            mustache_tmpl_iterate($major_unit_payment_calculator_modal_template, "terms");
            mustache_tmpl_set($major_unit_payment_calculator_modal_template, "terms", array(
                'index' => $index,
                'term' => $option['term'],
                'interest_rate' => $option['interest_rate'],
            ));
            $index ++;
        }
    }
    if (!empty($payment_option['data']['warranty_options'])) {

        mustache_tmpl_set($major_unit_payment_calculator_modal_template, "has_warranty_options", true);
        $index = 0;
        foreach($payment_option['data']['warranty_options'] as $option) {
            mustache_tmpl_iterate($major_unit_payment_calculator_modal_template, "warranty_options");
            mustache_tmpl_set($major_unit_payment_calculator_modal_template, "warranty_options", array(
                'index' => $index,
                'title' => $option['title'],
                'description' => $option['description'],
                'price' => number_format($option['price'], 2)
            ));
            $index ++;
        }
    }

    if (!empty($payment_option['data']['accessory_options'])) {

        mustache_tmpl_set($major_unit_payment_calculator_modal_template, "has_accessory_options", true);
        $index = 0;
        foreach($payment_option['data']['accessory_options'] as $option) {
            mustache_tmpl_iterate($major_unit_payment_calculator_modal_template, "accessory_options");
            mustache_tmpl_set($major_unit_payment_calculator_modal_template, "accessory_options", array(
                'index' => $index,
                'title' => $option['title'],
                'description' => $option['description'],
                'image' => $option['image'],
                'price' => number_format($option['price'], 2)
            ));
            $index ++;
        }
    }
}

if ($show_template) {
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "form_open_string", form_open('welcome/productEnquiry', array('class' => 'form_payment_calculator')));
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "form_close_string", form_close());
    mustache_tmpl_set($major_unit_payment_calculator_modal_template, "form_action_url", base_url("welcome/productEnquiry"));
    echo mustache_tmpl_parse($major_unit_payment_calculator_modal_template);
}

echo $CI->load->view("modals/major_unit_detail_modal_global_include", array(), true);
