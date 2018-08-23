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
?>
<div class="cart_wrap_right">
    <h3 style="float:left;margin:5px 0 0;">
        <i class="fa fa-home"></i> 3. Shipping Details
    </h3>
    <p style="float:right;"><b>Same As Billing</b><?php echo form_checkbox('auto_pop_bill_to_ship', 1, 0, 'onclick="populateShipping();"'); ?></p>
    <div class="clear"></div>
    <?php echo form_dropdown('shipping_address_change', $shipping_addresses, 0, 'id="shipping_address_selector" onChange="changeShippingAddress()"' ); ?>
    <br>
    <p>Field marked with a * are required</p>
    <div class="hidden_table">
        <table width="100%" cellpadding="6">
            <tr>
                <td><b>Company Name:</b></td>
                <td><?php echo form_input(array('name' => 'company[]',
                        'value' => @$value['company'][1] ? @$value['company'][1] : @$shipping['company'],
                        'id' => 'shipping_company',
                        'placeholder' => 'Company',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td><b>First Name:*</b></td>
                <td><?php echo form_input(array( 'name' => 'first_name[]',
                        'value' => @$value['first_name'][1] ? @$value['first_name'][1] : @$shipping['first_name'],
                        'id' => 'shipping_first_name',
                        'placeholder' => 'Enter First Name',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td><b>Last Name:*</b></td>
                <td><?php echo form_input(array('name' => 'last_name[]',
                        'value' => @$value['last_name'][1] ? @$value['last_name'][1] : @$shipping['last_name'],
                        'id' => 'shipping_last_name',
                        'class' => 'text large',
                        'placeholder' => 'Enter Last Name')); ?></td>
            </tr>
            <tr>
                <td><b>Email Address:*</b></td>
                <td><?php echo form_input(array('name' => 'email[]',
                        'value' => @$value['email'][1] ? @$value['email'][1] : @$shipping['email'],
                        'id' => 'shipping_email',
                        'placeholder' => 'Enter Email Address',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td><b>Phone:*</b></td>
                <td><?php echo form_input(array('name' => 'phone[]',
                        'value' => @$value['phone'][1] ? @$value['phone'][1] : @$shipping['phone'],
                        'id' => 'shipping_phone',
                        'placeholder' => 'Enter Phone Number',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td id="shipping_street_address_label"><b>Address Line 1:*</b></td>
                <td><?php echo form_input(array('name' => 'street_address[]',
                        'value' =>  @$shipping['street_address'],
                        'id' => 'shipping_street_address',
                        'class' => 'text large',
                        'placeholder' => 'Enter Address')); ?></td>
            </tr>
            <tr>
                <td id="shipping_address_2_label"><b>Address Line 2:</b></td>
                <td><?php echo form_input(array('name' => 'address_2[]',
                        'value' => @$shipping['address_2'],
                        'id' => 'shipping_address_2',
                        'class' => 'text large',
                        'placeholder' => 'Apt. Bld. Etc')); ?></td>
            </tr>
            <tr>
                <td id="shipping_city_label"><b>City:*</b></td>
                <td><?php echo form_input(array('name' => 'city[]',
                        'value' =>  @$shipping['city'],
                        'id' => 'shipping_city',
                        'placeholder' => 'Enter City',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td id="shipping_state_label"><b>State:*</b></td>
                <td><?php echo form_dropdown('state[]', $states, @$shipping['state'], 'id="shipping_state"'); ?></td>
            </tr>
            <tr>
                <td id="shipping_zip_label"><b>Zip:*</b></td>
                <td><?php echo form_input(array('name' => 'zip[]',
                        'value' => @$shipping['zip'],
                        'id' => 'shipping_zip',
                        'class' => 'text large',
                        'placeholder' => 'Enter Zipcode')); ?></td>
            </tr>
            <tr>
                <td><b>Country:*</b></td>
                <td><?php echo form_dropdown('country[]',
                        $countries,
                        (@$value['country'][1] ? @$value['country'][1] : @$shipping['country']),
                        'id="shipping_country" onChange="newChangeCountry(\'shipping\');"'); ?></td>
            </tr>
        </table>
    </div>
</div>