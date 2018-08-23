<?php
/*
 * Incoming:
 * billing_addresses
 * value
 * billing
 * states
 *
 */

$CI =& get_instance();

$CI->load->helper("mustache_helper");
$billing_details = mustache_tmpl_open("checkout/billing_details.html");
mustache_tmpl_set($billing_details, "billing_address_dropdown", form_dropdown('billing_address_change', $billing_addresses, 0, 'id="billing_address_selector" onChange="changeBillingAddress();"'));
mustache_tmpl_set($billing_details, "g", array_key_exists('g', $_GET) ? $_GET['g'] : "");
mustache_tmpl_set($billing_details, "company_name_input", form_input(array('name' => 'company[]',
    'value' =>  @$value['company'][0] ? @$value['company'][0] : @$billing['company'],
    'placeholder' => 'Enter Company Name',
    'id' => 'billing_company',
    'class' => 'text large')));
mustache_tmpl_set($billing_details, "first_name_input", form_input(array('name' => 'first_name[]',
    'value' => @$value['first_name'][0] ?@$value['first_name'][0] : @$billing['first_name'],
    'id' => 'billing_first_name',
    'placeholder' => 'Enter First Name',
    'class' => 'text large')));
mustache_tmpl_set($billing_details, "last_name_input", form_input(array('name' => 'last_name[]',
    'value' => @$value['last_name'][0] ? @$value['last_name'][0] : @$billing['last_name'],
    'id' => 'billing_last_name',
    'class' => 'text large',
    'placeholder' => 'Enter Last Name')));
mustache_tmpl_set($billing_details, "email_address_input", form_input(array('name' => 'email[]',
    'value' => @$value['email'][0] ? @$value['email'][0] : @$billing['email'],
    'id' => 'billing_email',
    'placeholder' => 'Enter Email Address',
    'class' => 'text large')));
mustache_tmpl_set($billing_details, "phone_input", form_input(array('name' => 'phone[]',
    'value' => @$value['phone'][0] ? @$value['phone'][0] : @$billing['phone'],
    'id' => 'billing_phone',
    'placeholder' => 'Enter Phone Number',
    'class' => 'text large')));
mustache_tmpl_set($billing_details, "address_1_input", form_input(array('name' => 'street_address[]',
    'value' => @$billing['street_address'],
    'id' => 'billing_street_address',
    'class' => 'text large',
    'placeholder' => 'Enter Address')));
mustache_tmpl_set($billing_details, "address_2_input", form_input(array('name' => 'address_2[]',
    'value' => @$billing['address_2'],
    'id' => 'billing_address_2',
    'class' => 'text large',
    'placeholder' => 'Apt. Bld. Etc')));
mustache_tmpl_set($billing_details, "city_input", form_input(array('name' => 'city[]',
    'value' => @$billing['city'],
    'id' => 'billing_city',
    'placeholder' => 'Enter City',
    'class' => 'text large')));
mustache_tmpl_set($billing_details, "state_input", form_dropdown('state[]', $states, @$billing['state'], 'id="billing_state"'));
mustache_tmpl_set($billing_details, "zip_input", form_input(array('name' => 'zip[]',
    'value' => @$billing['zip'],
    'id' => 'billing_zip',
    'class' => 'text large',
    'placeholder' => 'Zipcode')));
mustache_tmpl_set($billing_details, "country_input", form_dropdown('country[]',
    $countries,
    (@$value['country'][1] ? @$value['country'][1] : @$billing['country']),
    'id="billing_country" onChange="newChangeCountry(\'billing\');"'));


print mustache_tmpl_parse($billing_details);
