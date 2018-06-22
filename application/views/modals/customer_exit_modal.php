<?php

$CI =& get_instance();

$CI =& get_instance();
$CI->load->helper("mustache_helper");

$customer_exit_modal = mustache_tmpl_open("modals/customer_exit_modal.html");


// JLB: I don't want this thing shown more than once.
global $customerExitModal;

$show_template = false;
if (!isset($customerExitModal)) {
    $customerExitModal = false;
}
$show_template = !$customerExitModal;
$customerExitModal = true;

if ($show_template) {
    mustache_tmpl_set($customer_exit_modal, "form_open_string", form_open('welcome/productEnquiry', array('class' => 'form_standard')));
    mustache_tmpl_set($customer_exit_modal, "form_close_string", form_close());
    echo mustache_tmpl_parse($customer_exit_modal);
    echo $CI->load->view("modals/major_unit_detail_modal_global_include", array(), true);
}
