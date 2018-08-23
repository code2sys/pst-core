<?php
/*
 * Incoming:
 * billing_addresses
 * value
 * billing
 * states
 *
 */
?>
<div class="cart_wrap_left">
    <h3 style="float:left;margin:5px 0 0;">
        <i class="fa fa-list"></i> 2. Billing Details
    </h3>
    <div class="clear"></div>
    <?php echo form_dropdown('billing_address_change', $billing_addresses, 0, 'id="billing_address_selector" onChange="changeBillingAddress();"'); ?>
    <br>
    <p>Field marked with a * are required</p>
    <div class="hidden_table">
        <input name="guest" type="hidden" value="<?php echo @$_GET['g'];?>" />
        <table width="100%" cellpadding="6">
            <tr>
                <td><b>Company Name:</b></td>
                <td><?php echo form_input(array('name' => 'company[]',
                        'value' =>  @$value['company'][0] ? @$value['company'][0] : @$billing['company'],
                        'placeholder' => 'Enter Company Name',
                        'id' => 'billing_company',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td><b>First Name:*</b></td>
                <td><?php echo form_input(array('name' => 'first_name[]',
                        'value' => @$value['first_name'][0] ?@$value['first_name'][0] : @$billing['first_name'],
                        'id' => 'billing_first_name',
                        'placeholder' => 'Enter First Name',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td><b>Last Name:*</b></td>
                <td><?php echo form_input(array('name' => 'last_name[]',
                        'value' => @$value['last_name'][0] ? @$value['last_name'][0] : @$billing['last_name'],
                        'id' => 'billing_last_name',
                        'class' => 'text large',
                        'placeholder' => 'Enter Last Name')); ?></td>
            </tr>
            <tr>
                <td><b>Email Address:*</b></td>
                <td><?php echo form_input(array('name' => 'email[]',
                        'value' => @$value['email'][0] ? @$value['email'][0] : @$billing['email'],
                        'id' => 'billing_email',
                        'placeholder' => 'Enter Email Address',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td><b>Phone:*</b></td>
                <td><?php echo form_input(array('name' => 'phone[]',
                        'value' => @$value['phone'][0] ? @$value['phone'][0] : @$billing['phone'],
                        'id' => 'billing_phone',
                        'placeholder' => 'Enter Phone Number',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td id="billing_street_address_label"><b>Address Line 1:*</b></td>
                <td><?php echo form_input(array('name' => 'street_address[]',
                        'value' => @$billing['street_address'],
                        'id' => 'billing_street_address',
                        'class' => 'text large',
                        'placeholder' => 'Enter Address')); ?></td>
            </tr>
            <tr>
                <td id="billing_address_2_label"><b>Address Line 2:</b></td>
                <td><?php echo form_input(array('name' => 'address_2[]',
                        'value' => @$billing['address_2'],
                        'id' => 'billing_address_2',
                        'class' => 'text large',
                        'placeholder' => 'Apt. Bld. Etc')); ?></td>
            </tr>
            <tr>
                <td id="billing_city_label"><b>City:*</b></td>
                <td><?php echo form_input(array('name' => 'city[]',
                        'value' => @$billing['city'],
                        'id' => 'billing_city',
                        'placeholder' => 'Enter City',
                        'class' => 'text large')); ?></td>
            </tr>
            <tr>
                <td id="billing_state_label"><b>State:*</b></td>
                <td><?php echo form_dropdown('state[]', $states, @$billing['state'], 'id="billing_state"'); ?></td>
            </tr>
            <tr>
                <td id="billing_zip_label"><b>Zip:*</b></td>
                <td><?php echo form_input(array('name' => 'zip[]',
                        'value' => @$billing['zip'],
                        'id' => 'billing_zip',
                        'class' => 'text large',
                        'placeholder' => 'Zipcode')); ?></td>
            </tr>
            <tr>
                <td><b>Country:*</b></td>
                <td><?php echo form_dropdown('country[]',
                        $countries,
                        (@$value['country'][1] ? @$value['country'][1] : @$billing['country']),
                        'id="billing_country" onChange="newChangeCountry(\'billing\');"'); ?></td>
            </tr>
        </table>
    </div>
</div>