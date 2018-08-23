<?php

/*
 * Incoming:
 * $shipping_addresses
 * $value
 * $shipping
 * $states
 *
 *
 */

$CI =& get_instance();

$CI->load->helper("mustache_helper");
$shipping_details = mustache_tmpl_open("checkout/shipping_details.html");
mustache_tmpl_set($shipping_details, "auto_pop_bill_to_ship", form_checkbox('auto_pop_bill_to_ship', 1, 0, 'onclick="populateShipping();"'));
mustache_tmpl_set($shipping_details, "shipping_address_dropdown", form_dropdown('shipping_address_change', $shipping_addresses, 0, 'id="shipping_address_selector" onChange="changeShippingAddress()"' ));
mustache_tmpl_set($shipping_details, "company_name_input", form_input(array('name' => 'company[]',
    'value' => @$value['company'][1] ? @$value['company'][1] : @$shipping['company'],
    'id' => 'shipping_company',
    'placeholder' => 'Company',
    'class' => 'text large')));
mustache_tmpl_set($shipping_details, "first_name_input", form_input(array( 'name' => 'first_name[]',
    'value' => @$value['first_name'][1] ? @$value['first_name'][1] : @$shipping['first_name'],
    'id' => 'shipping_first_name',
    'placeholder' => 'Enter First Name',
    'class' => 'text large')));
mustache_tmpl_set($shipping_details, "last_name_input", form_input(array('name' => 'last_name[]',
    'value' => @$value['last_name'][1] ? @$value['last_name'][1] : @$shipping['last_name'],
    'id' => 'shipping_last_name',
    'class' => 'text large',
    'placeholder' => 'Enter Last Name')));
mustache_tmpl_set($shipping_details, "email_address_input", form_input(array('name' => 'email[]',
    'value' => @$value['email'][1] ? @$value['email'][1] : @$shipping['email'],
    'id' => 'shipping_email',
    'placeholder' => 'Enter Email Address',
    'class' => 'text large')));
mustache_tmpl_set($shipping_details, "phone_input", form_input(array('name' => 'phone[]',
    'value' => @$value['phone'][1] ? @$value['phone'][1] : @$shipping['phone'],
    'id' => 'shipping_phone',
    'placeholder' => 'Enter Phone Number',
    'class' => 'text large')));
mustache_tmpl_set($shipping_details, "address_1_input", form_input(array('name' => 'street_address[]',
    'value' =>  @$shipping['street_address'],
    'id' => 'shipping_street_address',
    'class' => 'text large',
    'placeholder' => 'Enter Address')));
mustache_tmpl_set($shipping_details, "address_2_input", form_input(array('name' => 'address_2[]',
    'value' => @$shipping['address_2'],
    'id' => 'shipping_address_2',
    'class' => 'text large',
    'placeholder' => 'Apt. Bld. Etc')));
mustache_tmpl_set($shipping_details, "city_input", form_input(array('name' => 'city[]',
    'value' =>  @$shipping['city'],
    'id' => 'shipping_city',
    'placeholder' => 'Enter City',
    'class' => 'text large')));
mustache_tmpl_set($shipping_details, "state_input", form_dropdown('state[]', $states, @$shipping['state'], 'id="shipping_state"'));
mustache_tmpl_set($shipping_details, "zip_input", form_input(array('name' => 'zip[]',
    'value' => @$shipping['zip'],
    'id' => 'shipping_zip',
    'class' => 'text large',
    'placeholder' => 'Enter Zipcode')));
mustache_tmpl_set($shipping_details, "country_input", form_dropdown('country[]',
    $countries,
    (@$value['country'][1] ? @$value['country'][1] : @$shipping['country']),
    'id="shipping_country" onChange="newChangeCountry(\'shipping\');"'));


print mustache_tmpl_parse($shipping_details);
