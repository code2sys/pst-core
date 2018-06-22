<?php

$CI =& get_instance();

$CI =& get_instance();
$CI->load->helper("mustache_helper");

$trade_in_value_modal = mustache_tmpl_open("modals/trade_in_value_modal.html");

mustache_tmpl_set($trade_in_value_modal, "motorcycle_id", $motorcycle["id"]);
mustache_tmpl_set($trade_in_value_modal, "motorcycle_title", $motorcycle["title"]);

mustache_tmpl_set($trade_in_value_modal, "form_open_string", form_open('welcome/productEnquiry', array('class' => 'form_standard')));
mustache_tmpl_set($trade_in_value_modal, "form_close_string", form_close());


if (!defined('DISABLE_TEST_DRIVE') || !DISABLE_TEST_DRIVE) {
    mustache_tmpl_set($trade_in_value_modal, "ENABLE_TEST_DRIVE", true);

    if (defined('WORDING_WANT_TO_SCHEDULE_A_TEST_DRIVE')) {
        mustache_tmpl_set($trade_in_value_modal, "WORDING_PLACEHOLDER_DATE_OF_RIDE", WORDING_PLACEHOLDER_DATE_OF_RIDE);
    } else {
        mustache_tmpl_set($trade_in_value_modal, "WORDING_PLACEHOLDER_DATE_OF_RIDE", false);
    }

} else {
    mustache_tmpl_set($trade_in_value_modal, "ENABLE_TEST_DRIVE", false);
}

echo mustache_tmpl_parse($trade_in_value_modal);
