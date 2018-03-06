<?php
//require_once( echo site_url()  . 'lib/Braintree.php');
require(__DIR__ . "/../../braintree_clienttoken.php");
?>
<div class="content_wrap">
    <div class="content">
        <?php if($this->session->flashdata('error')) { ?>
            <div class="validation_error" id="login_validation_error">
                <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
                <h1>Error</h1>
                <div class="clear"></div>
                <p>
                    <?php echo $this->session->flashdata('error'); ?>
                </p>
            </div>
        <?php } ?>
        <h1 class="wdth-25" style="width:20%;"><?php echo $order['order_id']; ?> : <?php echo $order['status']; ?></h1>
        <?php if ($order['created_by'] && $order['status'] == 'Pending') { ?>
            <div class="wdth-75" style="width:57%;">
                <span>Customer Look Up:</span>
                <input type="text" name="customer_look" value="" id="customer_lookup" style="width: 500px;"/>
                <input type="button" name="Go" value="Go" onclick="populateCustomer();"/>
            </div>
        <?php } ?>
        <div class="wdth-25" style="width:21%;padding-top:14px;float:right;">
            Source <img src="<?php echo $assets; ?>/images/<?php echo ($order['source']=="eBay"?"ebay_logo.png":"admin_logo.png"); ?>" style="vertical-align:middle" height="30px" border="0">
            <br>
            Customer IP Address <b> <?php echo $order['customer_ip']; ?> </b>
        </div>
        <?php
        echo form_open('', array('class' => 'form_standard', 'id' => 'order_info'));
        echo form_hidden('order_id', $order['order_id']);
        ?>
        <input type="hidden" name="ebay_id" value="<?php echo $order['ebay_order_id']; ?>" />
        <div class="tabular_data">
            <table width="100%" cellpadding="8">
                <tr>
                    <td>Bill To Address
                        (<span class="billing_display">
                                    <a href="javascript:void(0);" onclick="$('.billing_display').hide(); $('.billing_edit').show();">Edit</a>
                                </span><span class="billing_edit hide"><a href="javascript:void(0);" onclick="$('.billing_display').show(); $('.billing_edit').hide(); updateBilling();">Temp Save</a></span>)
                        (<span class="billing_display">
                                    <a href="<?php echo site_url('admin/customer_detail/'.$order['user_id']);?>" >Customer Profile</a>
                                </span>)
                    </td>
                    <td>
                        Ship To Address (<span class="shipping_display"><a href="javascript:void(0);" onclick="$('.shipping_display').hide(); $('.shipping_edit').show();">Edit</a></span><span class="shipping_edit hide"><a href="javascript:void(0);" onclick="$('.shipping_display').show(); $('.shipping_edit').hide(); updateShipping();">Temp Save</a></span>)<br>
                        <input type="checkbox" value="billing" name="sameAsBilling" id="sameAsBilling">Same as billing
                    </td>
                    <td>Tracking and Shipping</td>
                    <td>Payment Info <!-- (<span class="payment_display"><a href="javascript:void(0);" onclick="$('.payment_display').hide(); $('.payment_edit').show();">Edit</a></span><span class="payment_edit hide"><a href="javascript:void(0);" onclick="$('.payment_display').show(); $('.payment_edit').hide(); updatePayment();">Temp Save</a></span>) --></td>
                </tr>
                <tr>
                    <td>
                        <div class="hidden_table billing_edit hide">
                            <table width="100%" cellpadding="8">
                                <tr>
                                    <td><b>Company Name:</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'company[]',
                                            'value' => @$order['company'],
                                            'placeholder' => 'Enter Company Name',
                                            'id' => 'billing_company',
                                            'class' => 'text large'));
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>First Name:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'first_name[]',
                                            'value' => @$order['first_name'],
                                            'id' => 'billing_first_name',
                                            'placeholder' => 'Enter First Name',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>Last Name:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'last_name[]',
                                            'value' => @$order['last_name'],
                                            'id' => 'billing_last_name',
                                            'class' => 'text large',
                                            'placeholder' => 'Enter Last Name'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>Email Address:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'email[]',
                                            'value' => @$order['email'],
                                            'id' => 'billing_email',
                                            'placeholder' => 'Enter Email Address',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>Phone:</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'phone[]',
                                            'value' => @$order['phone'],
                                            'id' => 'billing_phone',
                                            'placeholder' => 'Enter Phone Number',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td id="billing_street_address_label"><b>Address Line 1:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'street_address[]',
                                            'value' => @$order['street_address'],
                                            'id' => 'billing_street_address',
                                            'class' => 'text large',
                                            'placeholder' => 'Enter Address'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td id="billing_address_2_label"><b>Address Line 2:</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'address_2[]',
                                            'value' => @$order['address_2'],
                                            'id' => 'billing_address_2',
                                            'class' => 'text large',
                                            'placeholder' => 'Apt. Bld. Etc'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td id="billing_city_label"><b>City:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'city[]',
                                            'value' => @$order['city'],
                                            'id' => 'billing_city',
                                            'placeholder' => 'Enter City',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td id="billing_state_label"><b>State:*</b></td>
                                    <td><?php echo form_dropdown('state[]', $states, @$order['state'], 'id="billing_state"'); ?></td>
                                </tr>
                                <tr>
                                    <td id="billing_zip_label"><b>Zip:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'zip[]',
                                            'value' => @$order['zip'],
                                            'id' => 'billing_zip',
                                            'class' => 'text large',
                                            'placeholder' => 'Zipcode'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>Country:*</b></td>
                                    <td><?php
                                        echo form_dropdown('country[]', $countries, @$billing['country'], 'id="billing_country" onChange="newChangeCountry(\'billing\');"');
                                        ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="billing_display">
                            <?php if (!empty($order['user_type'])) { ?>
                                <h3><?php echo strtoupper($order['user_type']) . " Order"; ?></h3>
                            <?php } ?>
                            <div id="billing_company_display"><?php echo @$order['company']; ?></div>
                            <span id="billing_first_name_display"><?php echo @$order['first_name']; ?></span>
                            <span id="billing_last_name_display"><?php echo @$order['last_name']; ?></span>
                            <div id="billing_email_display"><?php echo @$order['email']; ?></div>
                            <div id="billing_phone_display"><?php echo @$order['phone']; ?></div>
                            <div id="billing_street_address_display"><?php echo @$order['street_address']; ?></div>
                            <div id="billing_address_2_display"><?php echo @$order['address_2']; ?></div>
                            <span id="billing_city_display"><?php echo @$order['city']; ?></span>
                            <span id="billing_state_display"><?php echo @$states[$order['state']]; ?></span>
                            <span id="billing_zip_display"><?php echo @$order['zip']; ?></span>
                            <div id="billing_country_display"><?php echo @$order['country']; ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="hidden_table shipping_edit hide">
                            <table width="100%" cellpadding="8">
                                <tr>
                                    <td><b>Company Name:</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'company[]',
                                            'value' => @$order['shipping_company'],
                                            'placeholder' => 'Enter Company Name',
                                            'id' => 'shipping_company',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>First Name:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'first_name[]',
                                            'value' => @$order['shipping_first_name'],
                                            'id' => 'shipping_first_name',
                                            'placeholder' => 'Enter First Name',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>Last Name:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'last_name[]',
                                            'value' => @$order['shipping_last_name'],
                                            'id' => 'shipping_last_name',
                                            'class' => 'text large',
                                            'placeholder' => 'Enter Last Name'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>Email Address:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'email[]',
                                            'value' => @$order['shipping_email'],
                                            'id' => 'shipping_email',
                                            'placeholder' => 'Enter Email Address',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>Phone:</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'phone[]',
                                            'value' => @$order['shipping_phone'],
                                            'id' => 'shipping_phone',
                                            'placeholder' => 'Enter Phone Number',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td id="shipping_street_address_label"><b>Address Line 1:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'street_address[]',
                                            'value' => @$order['shipping_street_address'],
                                            'id' => 'shipping_street_address',
                                            'class' => 'text large',
                                            'placeholder' => 'Enter Address'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td id="shipping_address_2_label"><b>Address Line 2:</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'address_2[]',
                                            'value' => @$order['shipping_address_2'],
                                            'id' => 'shipping_address_2',
                                            'class' => 'text large',
                                            'placeholder' => 'Apt. Bld. Etc'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td id="shipping_city_label"><b>City:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'city[]',
                                            'value' => @$order['shipping_city'],
                                            'id' => 'shipping_city',
                                            'placeholder' => 'Enter City',
                                            'class' => 'text large'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td id="shipping_state_label"><b>State:*</b></td>
                                    <td><?php echo form_dropdown('state[]', $states, @$order['shipping_state'], 'id="shipping_state"'); ?></td>
                                </tr>
                                <tr>
                                    <td id="shipping_zip_label"><b>Zip:*</b></td>
                                    <td><?php
                                        echo form_input(array('name' => 'zip[]',
                                            'value' => @$order['shipping_zip'],
                                            'id' => 'shipping_zip',
                                            'class' => 'text large',
                                            'placeholder' => 'Zipcode'));
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><b>Country:*</b></td>
                                    <td><?php
                                        echo form_dropdown('country[]', $countries, (@$order['shipping_country']), 'id="shipping_country" onChange="newChangeCountry(\'shipping\');"');
                                        ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="shipping_display">
                            <?php if (!empty($order['user_type'])) { ?>
                                <h3><?php echo strtoupper($order['user_type']) . " Order"; ?></h3>
                            <?php } ?>
                            <div id="shipping_company_display"><?php echo @$order['shipping_company']; ?></div>
                            <span id="shipping_first_name_display"><?php echo @$order['shipping_first_name']; ?></span>
                            <span id="shipping_last_name_display"><?php echo @$order['shipping_last_name']; ?></span>
                            <div id="shipping_email_display"><?php echo @$order['shipping_email']; ?></div>
                            <div id="shipping_phone_display"><?php echo @$order['shipping_phone']; ?></div>
                            <div id="shipping_street_address_display"><?php echo @$order['shipping_street_address']; ?></div>
                            <div id="shipping_address_2_display"><?php echo @$order['shipping_address_2']; ?></div>
                            <span id="shipping_city_display"><?php echo @$order['shipping_city']; ?></span>
                            <span id="shipping_state_display"><?php echo @$states[$order['shipping_state']]; ?></span>
                            <span id="shipping_zip_display"><?php echo @$order['shipping_zip']; ?></span>
                            <div id="shipping_country_display"><?php echo @$order['shipping_country']; ?></div>
                        </div>

                    </td>
                    <td>
                    <!-- SUCCESS MESSAGE -->
                    <div class="success hide">
                        <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
                        <h1>Success</h1>
                        <div class="clear"></div>
                        <p>
                            Your email has been sent.
                        </p>
                    </div>
                    <!-- END SUCCESS MESSAGE -->
                    <!-- VALIDATION ERROR -->
                    <div class="validation_error hide">
                        <img src="<?php echo $assets; ?>/images/error.png">
                        <h1>Error</h1>
                        <div class="clear"></div>
                        <div id="tracking_validation_error"></div>
                    </div>
                    <!-- END VALIDATION ERROR -->

                    <table width="100%" cellpadding="2" style="margin-bottom:10px;">
                        <?php if(@$postalOptDD): foreach($postalOptDD as $code => $arr):
                            $set = FALSE; ?>
                            <?php if(@$order['shipping_type'] == $code):
                            $set = TRUE;
                        elseif(!isset($order['shipping_type']) && ($code == 'GND')):
                            $set = TRUE;
                        endif; ?>
                            <tr style="border:none;">
                                <td style="border:none;"><?php echo form_radio('shippingValue', $code, $set, 'onchange="changeShippingOrder('.$arr['value'].', jQuery(this));"'); ?></td>
                                <td style="border:none;"><b><?php echo $arr['label']; ?></b></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        <tr style="border:none;">
                            <?php
                            $set = FALSE;
                            if(@$order['shipping_type'] == '') {
                                $set = TRUE;
                            } ?>
                            <td style="border:none;"><?php echo form_radio('shippingValue', '', $set, 'onchange="changeShippingOrder(0.00, jQuery(this));"'); ?></td>
                            <td style="border:none;"><b><?php echo 'No Shipping Charge'; ?></b></td>
                        </tr>
                    </table>

                    Enter Tracking Number: <br />
                    <?php
                    echo form_input(array('name' => 'ship_tracking_code',
                        'value' => '',
                        'id' => 'ship_tracking_code',
                        'class' => 'text medium',
                        'placeholder' => 'Tracking Number'));
                    ?><br />
                    FedEx: <?php echo form_radio('carrier', 'FedEx'); ?>
                    UPS: <?php echo form_radio('carrier', 'UPS'); ?>
                    USPS: <?php echo form_radio('carrier', 'USPS'); ?>
                    OnTrac: <?php echo form_radio('carrier', 'OnTrac'); ?><br /><br />
                    <a href="javascript:void(0);" onclick="sendTrackingEmail();" id="button">
                        <?php if($order['source']=="eBay") { ?>Send Tracking to eBay<?php } else { ?>Send Tracking Conf Email<?php } ?></a>

                    <div id="past_ship_tracking_codes" style="display: none; clear: both;">
                            <strong>Past Tracking Codes:</strong>

                            <ul>

                            </ul>
                        </div>

                    </td>
                    <td>
                        <div class="payment_edit <?php echo ($order['created_by'] == '1') ? '' : 'hide1'; ?>">
                            <div class="fld1" style="height:25px;">
                                <label for="card-number" style="width:30%;float:left;">Card Number *</label>
                                <div id="card-number" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
                            </div>

                            <div class="fld1" style="height:25px;">
                                <label for="cvv" style="width:30%;float:left;">CVV *</label>
                                <div id="cvv" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
                            </div>

                            <div class="fld1" style="height:25px;">
                                <label for="expiration-date" style="width:30%;float:left;">Expiration Date *</label>
                                <div id="expiration-date" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
                            </div>

                            <div class="fld1" style="height:25px;">
                                <label for="expiration-date" style="width:30%;float:left;">Amount *</label>
                                <div style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;">
                                    <input type="text" value="<?php echo $order['sales_price']+$order['shipping']+$order['tax'];?>" name="amount" style="width: 100%;background: none;border: none;"/>
                                </div>
                            </div>
                            <div class="fld1" style="height:25px;">
                                <label for="expiration-date" style="width:30%;float:left;">Refund Amount *</label>
                                <div style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;">
                                    <input type="text" value="0" name="refund_amount" style="width: 100%;background: none;border: none;"/>
                                </div>
                            </div>
                        </div>
                        <div style="width:75%;float:left;margin-top:5px;" class="<?php echo ($order['created_by'] == 1) ? '' : 'hide1'; ?>">
                            <table width="100%" style="border:none;">
                                <tr>
                                    <td colspan="3" style="border:none;">
                                        <div style="border:none;background:grey;text-shadow:none;width:400px;">
                                            Transaction History &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*Select Transaction to Refund
                                        </div>
                                    </td>
                                </tr>
                                <?php foreach($order['transaction'] as $transaction) { ?>
                                    <tr>
                                        <td style="border:none;">Date: <?php echo date('m/d/Y', $transaction['order_date']);?>,</td>
                                        <td style="border:none;">
                                            <?php if($transaction['sales_price'] > 0) { ?>
                                                <input type="radio" value="<?php echo $transaction['braintree_transaction_id'];?>" name="transaction_id" style="position:absolute;margin-left: -19px;margin-top1px;">
                                            <?php } ?>
                                            ID: <span style="color:green;"><?php echo $transaction['braintree_transaction_id'];?>,</span>
                                        </td>
                                        <td style="border:none;">Authorized/Captured: <span style="color:<?php echo ($transaction['sales_price']) > 0 ? 'green' : 'red';?>;"><?php echo '$'.$transaction['sales_price'];?></span></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                        <div style="width:20%;margin-left:5px;float:left;height:42px;margin-top:5px;" class="<?php echo ($order['created_by'] == 1) ? '' : 'hide1'; ?>">
                            <input type="submit" name="submit" id="button" value="Process" class="processOrderButton"/>
                            <input type="button" name="refund" value="Process Refund" class="processRefundButton" onclick="refundProcess();"/>
                        </div>
                        <div class="payment_display <?php echo ($order['created_by'] == 1 && $order['status'] == 'Pending') ? 'hide' : ''; ?>">
                            <!--<b>Card	holders Name:</b><span id="cc_first_name_display"> <?php echo @$order['ccfname']; ?></span>
                            <span id="cc_last_name_display"><?php echo @$order['cclname']; ?></span><br />
                            <b>Credit Card Exp. Date:</b> <span id="exp_date_mn_display"><?php echo @$order['ccexpmo']; ?></span> /
                            <span id="exp_date_yr_display"><?php echo @$order['ccexpyr']; ?></span><br />

                            <?php if (@$_SESSION['userRecord']['cc_permission']) { ?>
                                                                    <b>Credit Card Number:</b><span id="cc_first_name_display"> <?php echo @$order['ccnmbr']; ?></span><br />
                            <?php } ?>
                            <b>CVV Code:</b> <span id="exp_date_mn_display"><?php echo @$order['cvc']; ?></span>-->
                        </div>
                        <br />
                        <div class="clear"></div>
                        <br />
                        <?php if($order['created_by'] != '1' && FALSE) { ?>
                            <?php
                            if (@$order['order_date'] > 0): // Payment Verfied
                                if ($order['process_date'] > 0): // Order Payment recevied
                                    ?>
                                    This payment was <b> Processed and Complete. </b>
                                <?php else: // Payment Verfied but no Payment Receved   ?>
                                    This payment was <b> Authorized / Captured. </b>
                                <?php endif; ?>
                            <?php else: // Order not verified  ?>
                                This payment has not yet been <b> Verified </b>
                            <?php endif; ?>
                            <br />
                            <br />
                            <div class="clear"></div>
                            <br />
                            Customer IP Address <b> <?php echo $order['customer_ip']; ?> </b>
                            <br />
                        <?php } ?>


                        <!-- <div style="float:left; margin-right:5px;">Cash amount: <input name="cash_amt" class="text mini"></div> -->
                        <?php //endif;   ?>
                    </td>
                </tr>
            </table>
            <div style="float:left;padding-top: 6px;">
                Coupon Code :
                <input type="text" name="coupon" value="" style="width: 100px;" id="couponCode"/>
                <input type="button" name="Go" value="Add" onclick="checkCoupon();"/>
                <?php //echo form_dropdown('distributor_shipping', array('Select Shipping', 'dealer' => 'Distributor to Dealer', 'customer' => 'Distributor to Customer'));    ?>
            </div>
            <div style="float:right">
                Select Actions <?php
                echo form_dropdown('actions', array('--Check then Select--',
                    'Update Calculations',
                    'Back Order',
                    'Shipped',
                    'Ready For Pick Up',
                    'Returned',
                    'Refunded',
                    'Delete'), '', 'id="productActions"');
                ?>
            </div>
            <div class="clear"></div>
            <!--
            <?php print_r($order); ?>
            -->
            <div class="tabular_data">
                <table width="100%" cellpadding="8" id="product_table">
                    <tr>
                        <td>Qty</td>
                        <td>SKU</td>
                        <td>Item Name</td>
                        <td>Dealer Inv.</td>
                        <td>Distributor</td>
                        <td>Backorder</td>
                        <td>Actions</td>
                        <td>Status</td>
                        <td>Price</td>
                    </tr>
                    <?php
                    $subtotal = 0.00;
                    $product_cost = array('0.00');
                    if (@$order['products']):
                        ?>
                        <?php
                        foreach ($order['products'] as $key => $product):
                            $qtyComplete = FALSE;
                            $qtyLft = $product['qty'];
                            $abcd1 = 0; // This ingenously named variable is the # of parts to take from dealer inventory.
                            ?>
                            <?php $partnumber = (@$product['distributorRecs'][0]['part_number']) ? $product['distributorRecs'][0]['part_number'] : $product['partnumber']; ?>
                            <tr>
                                <td><?php
                                    echo $product['qty'];
                                    echo form_hidden('qty[' . $product['partnumber'] . ']', $product['qty']);
                                    ?>
                                </td>
                                <td><?php
                                    $dist = (array) json_decode($product['distributor']);
                                    $partnumber = (@$product['distributorRecs'][0]['part_number']) ? $product['distributorRecs'][0]['part_number'] : $dist['part_number'];
                                    $partnumber = (@$partnumber) ? $partnumber : $product['partnumber'];
                                    echo $partnumber;
                                    echo form_hidden('product_sku[]', $product['partnumber']);
                                    ?></td>
                                <td><?php echo $product['name'] . '<br /> ' . $product['question'] . ' ::  ' . $product['answer']; ?>
                                    <?php
                                    if (@$product['fitment']) {
                                        echo '<br>Fitment :: ' . $product['fitment'];
                                    }
                                    ?>
                                </td>
                                <td style="vertical-align:top;">
                                    <div class="hidden_table">
                                        <table style="border:none;">
                                            <tr>
                                                <td style="width:60px">
                                                    Inv.
                                                </td>
                                                <td style="width:60px">
                                                    Qty.
                                                </td>
                                                <td style="width:60px">
                                                    Cost Ea.
                                                </td>
                                            </tr>
                                            <?php
                                            $lowestPrice = 0;
                                            $totalQty = 0;
                                            if (@$product['dealerRecs']):

                                                foreach ($product['dealerRecs'] as $distRec):
                                                    if ($distRec['quantity_available'] >= $product['qty']) {
                                                        //$product_cost[$product['partnumber']] = $product['qty']*$distRec['cost'];
                                                        $qtyLft = 0;
                                                        $abcd1 = $product['qty'];
                                                    } else {
                                                        $qtyLft = $qtyLft - $distRec['quantity_available'];
                                                        // JLB 06-11-17
                                                        // They used to assign $qtyLfty and $abcd1 to the same thing, which I believe is wrong.
                                                        $abcd1 = $distRec['quantity_available'];
                                                    }
                                                    if ($product['dealer_qty'] > 0) {
                                                        $abcd1 = $product['dealer_qty'];
                                                    }
                                                    if($product['status'] != 'Refunded') {
                                                        $product_cost[$product['partnumber']] = $abcd1*$distRec['cost'];
                                                    }
                                                    //$qtyLft = $qtyLft
                                                    if (($lowestPrice == 0) && ($distRec['quantity_available'] >= $product['qty'])):
                                                        $lowestPrice = $distRec['cost'];
                                                    elseif (($lowestPrice < $distRec['cost']) && ($distRec['quantity_available'] >= $product['qty'])):
                                                        $lowestPrice = $distRec['cost'];
                                                    endif;
                                                    //if ($distRec['distributor_id'] == array_search('Dealer Inventory', $distributors)):
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $distRec['quantity_available']; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if ($product['distributor'])
                                                                $savedDistributorInfo = json_decode($product['distributor'], TRUE);
                                                            if (@$savedDistributorInfo['id'] == $distRec['distributor_id']):
                                                                $totalQty = $savedDistributorInfo['qty'];
                                                            elseif ($distRec['quantity_available'] >= $product['qty']):
                                                                $totalQty = $product['qty'];
                                                                $qtyComplete = TRUE;
                                                            else:
                                                                $totalQty = 0;
                                                            endif;
                                                            echo form_hidden('dealer_id[' . $product['partnumber'] . ']', $distRec['distributor_id']);
                                                            echo form_hidden('dealer_partnumber[' . $product['partnumber'] . ']', $distRec['part_number']);
                                                            echo form_input(array('name' => 'dealer_qty[' . $distRec['partvariation_id'] . ']',
                                                                'value' => @$abcd1,
                                                                'class' => 'text mini dlr_qty',
                                                                'placeholder' => 'Qty'));
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $distRec['cost']; ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    //endif;
                                                endforeach;
                                            endif;
                                            if (!@$product['dealerRecs']):
                                                ?>
                                                <tr>
                                                    <td>
                                                        N/A
                                                    </td>
                                                    <td>
                                                        N/A
                                                    </td>
                                                    <td>
                                                        N/A
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </table>
                                    </div>

                                </td>
                                <td style="vertical-align:top;">
                                    <?php if (is_array(@$product['distributorRecs'])): foreach ($product['distributorRecs'] as $distRec): ?>
                                        <?php $dist = (array) json_decode($product['distributor']);?>

                                        <div class="hidden_table">
                                            <table style="border:none;">
                                                <tr>
                                                    <td style="width:60px">
                                                        Name
                                                    </td>
                                                    <td style="width:60px">
                                                        Inv.
                                                    </td>
                                                    <td style="width:60px">
                                                        Qty.
                                                    </td>
                                                    <td style="width:60px">
                                                        Cost Ea.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <?php echo @$dist['distributor_name'] ? $dist['distributor_name'] : $distributors[$distRec['distributor_id']]; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $distRec['quantity_available']; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($order['status'] == 'Processing' || $product['distributor_qty'] > 0) {
                                                            $qtyLft = $product['distributor_qty'];
                                                        }
                                                        if ($product['distributor'])
                                                            $savedDistributorInfo = json_decode($product['distributor'], TRUE);
                                                        if (@$savedDistributorInfo['id'] == $distRec['distributor_id']):
                                                            $totalQty = $savedDistributorInfo['qty'];
                                                        elseif (($lowestPrice > 0) && ($lowestPrice == $distRec['cost']) && (@$qtyComplete != TRUE)):
                                                            if ($distRec['quantity_available'] >= $product['qty']):
                                                                $totalQty = $product['qty'];
                                                                $qtyComplete = TRUE;
                                                            else:
                                                                $totalQty = 0;
                                                            endif;
                                                        else:
                                                            $totalQty = 0;
                                                        endif;
                                                        if($product['status'] != 'Refunded') {
                                                            $cst = @$dist['dis_cost'] ? $dist['dis_cost'] : $distRec['cost'];
                                                            $product_cost[$product['partnumber']] = $product_cost[$product['partnumber']]+($qtyLft*($cst));
                                                        }
                                                        echo form_hidden('distributor_id[' . $product['partnumber'] . ']', $distRec['distributor_id']);
                                                        echo form_hidden('distributor_partnumber[' . $product['partnumber'] . ']', $distRec['part_number']);
                                                        echo form_input(array('name' => 'distributor_qty[' . $product['partnumber'] . ']',
                                                            'value' => @$qtyLft,
                                                            'class' => 'text year dist_qty',
                                                            'placeholder' => 'Qty'));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo @$dist['dis_cost'] ? $dist['dis_cost'] : $distRec['cost']; ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <?php
                                    endforeach;
                                    elseif(@$product['distributor']):
                                        $dist = (array) json_decode($product['distributor']);
                                        if($product['status'] != 'Refunded') {
                                            $cst = @$dist['dis_cost'] ? $dist['dis_cost'] : $distRec['cost'];
                                            $product_cost[$product['partnumber']] = $product_cost[$product['partnumber']]+($dist['qty']*($cst));
                                        }
                                        ?>

                                        <div class="hidden_table">
                                            <table style="border:none;">
                                                <tr>
                                                    <td style="width:60px">
                                                        Name
                                                    </td>
                                                    <td style="width:60px">
                                                        Inv.
                                                    </td>
                                                    <td style="width:60px">
                                                        Qty.
                                                    </td>
                                                    <td style="width:60px">
                                                        Cost Ea.
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <?php echo $dist['distributor_name']; ?>
                                                    </td>
                                                    <td>N/A</td>
                                                    <td><?php echo $dist['qty']; ?></td>
                                                    <td>
                                                        <?php echo $dist['dis_cost']; ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <?php
                                    endif;
                                    ?>
                                </td>
                                <td><?php echo $product['stock_code']; ?></td>
                                <td><?php if ($partnumber != "COUPON") {
                                        echo form_checkbox('action', $product['partnumber']);
                                    } ?></td>
                                <td><?php echo (@$product['status']) ? $product['status'] : @$product['stock_code']; ?></td>
                                <td><?php
                                    echo $product['sale'];
                                    echo form_hidden('sale[' . $product['partnumber'] . ']', $product['sale']);
                                    if($product['status'] != 'Refunded') {
                                        $subtotal += $product['sale'];
                                    }
                                    ?></td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </table>
            </div>
            <div class="tabular_data">
                <table width="100%" cellpadding="8">
                    <tr>
                        <td rowspan="2">
                            <div style="float:left; margin-right:8px;">
                                SKU<br />
                                <input name="search_sku" class="text medium" id="search_sku">
                            </div>
                            <div style="float:left; margin-right:8px;">
                                Qty.<br />
                                <input name="search_qty" class="text mini" id="search_qty">
                            </div>
                            <div style="float:left; margin-top:11px">
                                <a href="javascript:void(0);" onclick="addProductNew(); return false;" id="button">Add To Order</a>
                            </div>
                            <div style="float:left; margin:11px 0 0 20px">
                                <a href="javascript:void(0);" onclick="searchProducts(); return false" id="button">Find Product In Store</a>
                            </div>
                            <div id="search_target" style="clear: both"></div>
                        </td>
                        <td>Subtotal:</td>
                        <td>$<?php
                            echo number_format($subtotal, 2);
                            //echo number_format($order['sales_price'], 2);
                            $grandTotal = $order['sales_price'];
                            ?></td>
                    </tr>
                    <tr>
                        <td>Tax:</td>
                        <td>$<?php
                            echo @$order['tax'] ? $order['tax'] : '0.00';
                            $grandTotal += @$order['tax'];
                            ?></td>
                    </tr>
                    <tr>
                        <td rowspan="6">
                            Notes:<br />
                            <textarea style="width:90%;" name="special_instr"><?php echo @$order['special_instr']; ?></textarea>
                        </td>
                        <td>S/H:</td>
                        <td>$<?php
                            echo @$order['shipping'] ? $order['shipping'] : '0.00';
                            $grandTotal += @$order['shipping'];
                            ?></td>
                    </tr>
                    <tr>
                        <td>Grand Total:</td>
                        <td>$<?php echo number_format($grandTotal, 2); ?></td>
                    </tr>
                    <tr>
                        <?php
                        $total_paid = 0.00;
                        foreach($order['transaction'] as $transaction) {
                            $total_paid += jonathan_extract_float_value($transaction['sales_price']);
                        } ?>
                        <td>Paid:</td>
                        <td>$<?php echo $total_paid;?></td>
                    </tr>
                    <?php $productCost = ($order['product_cost'] > 0) ? $order['product_cost'] : array_sum($product_cost);?>
                    <?php $shippingCost = ($order['shipping_cost'] > 0) ? $order['shipping_cost'] : 12.5;?>
                    <tr>
                        <td>Product Cost:</td>
                        <td style="width: 250px;">
                            $<input type="text" value="<?php echo ($order['product_cost'] > 0) ? $order['product_cost'] : array_sum($product_cost);?>" name="product_cost" style="width: 100px;">
                            <span>
                                    <input type="checkbox" name="product_cost_lock" value="1" style="top: 3px; position: relative;" <?php echo ($order['product_cost'] > 0) ? 'checked' : '';?>/> Lock Product Cost
                                </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Shipping Cost:</td>
                        <td style="width: 250px;">
                            $<input type="text" value="<?php echo ($order['shipping_cost'] > 0) ? $order['shipping_cost'] : '12.5';?>" name="shipping_cost" style="width: 100px;">
                            <span>
                                    <input type="checkbox" name="shipping_cost_lock" value="1" style="top: 3px; position: relative;" <?php echo ($order['shipping_cost'] > 0) ? 'checked' : '';?>/> Lock Shipping Cost
                                </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Profit:</td>
                        <td>$<?php echo $total_paid-($productCost+$shippingCost);?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="float:right;">
            <a href="javascript:void(0);" onclick="$.when(saveForLater()).done(refreshPage);" id="button">Save for Later</a>
            <!-- <a href="javascript:void(0);" onclick="addToBatch();" id="button">Add to Batch Order</a> -->
            <a href="javascript:void(0);" onclick="$.when(processOrder()).done(refreshPage);" id="button">Update To Processing</a>
            <a href="javascript:void(0);" onclick="sendToPST();" id="button">Send To Distributors</a>
        </div>

        </form>
    </div>
</div>

<script>

    function updateBilling()
    {
        $("#billing_company_display").html($('#billing_company').val());
        $("#billing_first_name_display").html($('#billing_first_name').val());
        $("#billing_last_name_display").html($('#billing_last_name').val());
        $("#billing_email_display").html($('#billing_email').val());
        $("#billing_phone_display").html($('#billing_phone').val());
        $("#billing_street_address_display").html($('#billing_street_address').val());
        $("#billing_address_2_display").html($('#billing_address_2').val());
        $("#billing_city_display").html($('#billing_city').val());
        $("#billing_state_display").html($('#billing_state').val());
        $("#billing_zip_display").html($('#billing_zip').val());
        $("#billing_country_display").html($('#billing_country').val());
        saveForLater();
    }

    function updateShipping()
    {
        $("#shipping_company_display").html($('#shipping_company').val());
        $("#shipping_first_name_display").html($('#shipping_first_name').val());
        $("#shipping_last_name_display").html($('#shipping_last_name').val());
        $("#shipping_email_display").html($('#shipping_email').val());
        $("#shipping_phone_display").html($('#shipping_phone').val());
        $("#shipping_street_address_display").html($('#shipping_street_address').val());
        $("#shipping_address_2_display").html($('#shipping_address_2').val());
        $("#shipping_city_display").html($('#shipping_city').val());
        $("#shipping_state_display").html($('#shipping_state').val());
        $("#shipping_zip_display").html($('#shipping_zip').val());
        $("#shipping_country_display").html($('#shipping_country').val());
        $.when(saveForLater()).done(refreshPage);
    }

    function updatePayment()
    {
        $("#cc_first_name_display").html($('#cc_first_name').val());
        $("#cc_last_name_display").html($('#cc_last_name').val());
        $("#exp_date_mn_display").html($('#exp_date_mn').val());
        $("#exp_date_yr_display").html($('#exp_date_yr').val());
    }

    function newChangeCountry(addressType)
    {
        country = $('#' + addressType + '_country').val();
        currentValue = $('#' + addressType + '_state').val();
        $('#' + addressType + '_state').empty();
        if (country == 'USA')
        {
            addressDD = $.post(base_url + 'checkout/load_states/1',
                {},
                function (returnData)
                {
                    var dataArr = jQuery.parseJSON(returnData);
                    var html = '';
                    $.each(dataArr, function (i, value)
                    {
                        if (currentValue == i)
                            html += '<option selected="selected" value="' + i + '">' + value + '</option>';
                        else
                            html += '<option value="' + i + '">' + value + '</option>';
                    })
                    $('#' + addressType + '_state').append(html);

                });
        }

        if (country == 'Canada')
        {
            addressDD = $.post(base_url + 'checkout/load_provinces/1',
                {},
                function (returnData)
                {
                    var dataArr = jQuery.parseJSON(returnData);
                    var html = '';
                    $.each(dataArr, function (i, value)
                    {
                        html += '<option value="' + i + '">' + value + '</option>';
                    })
                    $('#' + addressType + '_state').append(html);

                });
        }

        $.post(base_url + 'checkout/new_change_country',
            {
                'country': country
            },
            function (returnData)
            {
                var dataArr = jQuery.parseJSON(returnData);
                $('#' + addressType + '_street_address_label').html(dataArr.street_address);
                $('#' + addressType + '_address_2_label').html(dataArr.address_2);
                $('#' + addressType + '_city_label').html(dataArr.city);
                $('#' + addressType + '_state_label').html(dataArr.state);
                $('#' + addressType + '_zip_label').html(dataArr.zip);

            });

    }

    function addProduct()
    {
        qty = $('#search_qty').val();
        sku = $('#search_sku').val();

        if (qty == '')
            alert('Please enter a Qty');
        if (sku == '')
            alert('Please enter a SKU');
        if ((qty != '') && (sku != ''))
        {
            saveForLater();
            orderId = $('input[name="order_id"]').attr('value');
            //window.location.replace(base_url + 'admin/order_edit/' + orderId + '/' + sku + '/' + qty);
        }
    }

    function addProductNew()
    {
        qty = $('#search_qty').val();
        sku = $('#search_sku').val();

        if (qty == '') {
            alert('Please enter a Qty');
        } else if (sku == '') {
            alert('Please enter a SKU');
        } else
        {
            // JLB 01-10-18
            // This just stuck it on there, but now we have to query it....
            $.ajax({
                type: "POST",
                url : "/admin/ajax_query_part",
                data: {
                    "partnumber" : sku
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.data.success) {
                        if (response.data.store_inventory_match) {
                            //saveForLater();
                            orderId = $('input[name="order_id"]').attr('value');
                            window.location.replace(base_url + 'admin/order_edit/' + orderId + '/' + encodeURIComponent(sku) + '/' + qty);
                        } else if (response.data.lightspeed_match) {
                            // we have something else to talk about...
                            $("#search_target").html("");

                            for (var i= 0; i < response.data.lightspeed.length; i++) {
                                // We have to add a link for each one to really add them...
                                var m = response.data.lightspeed[i];
                                $("#search_target").append("<p><strong>" + m.description + "</strong> (Lightspeed Part Feed #" + m.part_number + ") - " + m.available + " available at $" + m.cost + " cost <a class='add' data-lightspeedpart-id='" + m.lightspeedpart_id + "' data-qty='" + qty + "' href='/admin/add_lightspeed_part/<?php echo $order_id; ?>/" + m.lightspeedpart_id + "/" + qty + "'>+ Add</a>");
                            }

                            // Now, lump them out there...
                        }

                    } else {
                        // do something with this error.
                        alert("Sorry, that part is not found.");
                    }

                }
            });

        }
    }

    function addToBatch()
    {
        $.when(saveForLater()).done(function() { return updateStatus('Batch Order'); });
    }

    function processOrder()
    {
        $.when(saveForLater()).done(function() { return updateStatus('Processing'); } );

    }

    function sendToPST() {
        var lngth = $('.dist_qty').length;
        var dlrQty = $('.dlr_qty').length;
        //dlr_qty
        var ttlDlr = 0;
        $('.dist_qty').each(function(){
            if( parseInt($(this).val()) == 0 ) {
                ttlDlr++;
            }
        });
        var sendPst = "<?php echo $order['sendPst']; ?>";
        var ordr_status = "<?php echo $order['status']; ?>";
        if(sendPst == '1' || ordr_status == 'Processing') {
            alert('This order has already been Submitted and cannot be submitted twice');
        } else if( (lngth == ttlDlr && dlrQty > 0) || (ttlDlr == 0 && dlrQty > 0) ) {
            alert('Only items being shipped from distributor stock can be sent to PST.');
        } else {
            $.when(saveForLater()).done(function() { return updateStatusPST('Processing'); });
            //alert();
        }
    }

    function saveForLater()
    {
        var data = $('#order_info').serialize();
        return $.post(base_url + 'ajax/order_save/',
            data,
            function (orderId)
            {
                $('input[name="order_id"]').attr('value', orderId);
                //refreshPage();
                return orderId;
            });

    }

    function refundProcess()
    {
        var refundAmount = parseInt(jQuery('input[name=refund_amount]').val());
        var transaction = jQuery('input[name=transaction_id]:checked').val();
        if( refundAmount <= 0 ) {
            alert('Please Enter amount to refund.');
            return;
        }
        if (typeof transaction === "undefined") {
            alert('Select any transaction to refund.');
            return;
        }
        var orderId = $('input[name="order_id"]').attr('value');
        var data = $('#order_info').serialize();
        $.post(base_url + 'admin/order_edit/'+orderId,
            data,
            function (status)
            {
                window.location.replace(base_url + 'admin/order_edit/' + orderId);
            });

    }


    function updateStatusPST(status)
    {
        orderId = $('input[name="order_id"]').attr('value');
        $.post(base_url + 'ajax/changeOrderStatus',
            {
                'orderId': orderId,
                'status': status
            },
            function (orderId)
            {
                $.post(base_url + 'ajax/changeDealerQuantity',
                    $('#order_info').serialize(),
                    function (orderId)
                    {
                        $.post(base_url + 'ajax/sendOrderToPST',
                            $('#order_info').serialize(),
                            function (orderId)
                            {
                                location.reload();
                            });
                    });
            });
    }

    function updateStatus(status)
    {
        orderId = $('input[name="order_id"]').attr('value');
        $.post(base_url + 'ajax/changeOrderStatus',
            {
                'orderId': orderId,
                'status': status
            },
            function (orderId)
            {
                $.post(base_url + 'ajax/changeDealerQuantity',
                    $('#order_info').serialize(),
                    function (orderId)
                    {
                        location.reload();
                    });
            });
    }

    function updateProductStatus(status, allCks)
    {
        orderId = $('input[name="order_id"]').attr('value');
        $.post(base_url + 'ajax/changeProductOrderStatus',
            {
                'products': allCks,
                'status': status,
                'orderId': orderId,
            },
            function (orderId)
            {
                location.reload();
            });
    }

    var currentCodes = <?php $codes = json_decode($order['ship_tracking_code']); echo isset($codes) ? json_encode($codes) : "[]"; ?>;

    $(document).on("click", ".remove_tracking_code", function(e) {
        if (e) {
            e.preventDefault();
        }
        var target = $(e.target);
        if (target.prop('nodeName') == 'SPAN') {
            target = $(target).parent();
            target = $(target);
        }
        removeTrackingCode(target.attr("data-index"));
    });

    function processTrackingCodes(codes) {
        if (codes && codes.length > 0) {
            var $ul = $("#past_ship_tracking_codes ul");
            $ul.html("");
            for (var i in codes) {
                var carrier = codes[i][0];
                var number = codes[i][1];
                $ul.append("<li>" + carrier + ": " + number + " <a href='#' class='remove_tracking_code' data-index='" + i + "' ><span style=\"color:red; vertical-align: super;\">x</span></a></li>");
            }
            $("#past_ship_tracking_codes").show();
        } else {
            // hide it...
            $("#past_ship_tracking_codes").hide();
        }

    }

    $(document).ready(function() {
        processTrackingCodes(currentCodes);
    });

    function sendTrackingEmail()
    {
        <?php if($order['source']=="eBay") { ?>
//		alert($('input[name=ebay_id]').attr('value'));

        $.post(
            // URL
            base_url + 'ajax/email_tracking_ebay/',
            // Data
            {
                'ship_tracking_code': $('#ship_tracking_code').val(),
                // JLB 08-24-17 - If you don't send up our internal ID, it's hard to save to our DB.
                'id': $('input[name="order_id"]').attr('value'),
                'ebay_id': $('input[name=ebay_id]').attr('value'),
                'carrier': $('input[name=carrier]:checked').val()
            },
            // Success Handler
            function (response)
            {
                if (response.success)
                {
                    $('.success').show();
                    $('.success').fadeOut(3000);
                    currentCodes = response.ship_tracking_code;
                    processTrackingCodes(currentCodes);
                } else
                {
                    $('#tracking_validation_error').html(response.error_message);
                    $('.validation_error').show();
                    $('.validation_error').fadeOut(4000);
                }
            },
            // Data type
            "json"
        );
        <?php } else { ?>
        $.post(base_url + 'ajax/email_tracking/',
            {
                'ship_tracking_code': $('#ship_tracking_code').val(),
                'id': $('input[name="order_id"]').attr('value'),
                'carrier': $('input[name=carrier]:checked').val()
            },
            function (response)
            {
                if (response == 'success')
                {
                    $('.success').show();
                    $('.success').fadeOut(3000);
                } else
                {
                    $('#tracking_validation_error').html(response);
                    $('.validation_error').show();
                    $('.validation_error').fadeOut(4000);
                }
            });
        <?php } ?>
    }

    function removeTrackingCode(key)
    {
        $.post(base_url + 'ajax/remove_tracking/',
            {
                'key': key,
                'id': $('input[name="order_id"]').attr('value')
            },
            function (response)
            {
                if (response.success) {
                    currentCodes = response.currentCodes;
                    processTrackingCodes(currentCodes);
                } else {
                    // Some error..just reload it...
                    location.reload();
                }
            },
            "json");
    }

    $(document).ready(function () {
        $('#product_search').keyup(function (e) {
            if (e.keyCode == 13) {
                searchProducts();
                return false;
            }
        });

        $('#productActions').change(function () {
            action = $(this).val();
            var allCks = [];
            $("input:checked").each(function () {
                allCks.push($(this).val());
            });
            console.log(allCks);
            switch (action)
            {
                case '1':
                    //alert('Update Calculations');
                    saveForLater();
                    break;
                case '2':
                    //alert('Back Order');
                    updateProductStatus('Back Order', allCks);
                    break;
                case '3':
                    //alert('Shipped');
                    updateProductStatus('Shipped', allCks);
                    break;
                case '4':
                    //alert('Ready for Pickup');
                    updateProductStatus('Ready for Pickup', allCks);
                    break;
                case '5':
                    //alert('Returned');
                    updateProductStatus('Returned', allCks);
                    break;
                case '6':
                    //alert('Refunded');
                    updateProductStatus('Refunded', allCks);
                    break;
                case '7':
                    //alert('Refunded');
                    if(confirm('Are you sure you want to delete this item?')) {
                        updateProductStatus('Delete', allCks);
                    }
                    break;
            }
        });
    });

    function searchProducts()
    {
        saveForLater();
        alert('You will be redirected to the main page of the site. Find your product through the provided search and filter tools and navigate to the detail page.  The add to cart button will bring you back to this screen and add the product to the order. Do not log out or navigate away from this site for this functionality to work.');
        orderId = $('input[name="order_id"]').attr('value');
        $.post(base_url + 'ajax/order_product_search',
            {'orderId': orderId},
            function () {
                window.location.replace(base_url);
            });
    }

    $(function() {

        $("#customer_lookup").autocomplete({
            source: base_url + 'ajax/getCustomer',
            minLength: 2,
            select: function(event, ui) {
            },

            html: true, // optional (jquery.ui.autocomplete.html.js required)

            // optional (if other layers overlap autocomplete list)
            open: function(event, ui) {
                $(".ui-autocomplete").css("z-index", 1000);
            }
        });
    });

    function populateCustomer() {
        var customer = jQuery('#customer_lookup').val();
        orderId = $('input[name="order_id"]').attr('value');
        $.post(base_url + 'ajax/getCustomerPopulated',
            {'customer': customer, 'orderId':orderId},
            function (result) {
                location.reload();
                //jQuery('.tblData').html(result);
                //alert(result);
                //window.location.replace(base_url);
            });
    }

    function checkCoupon() {
        var couponCode = jQuery('#couponCode').val();
        orderId = $('input[name="order_id"]').attr('value');
        $.post(base_url + 'ajax/checkCouponCode',
            {'couponCode': couponCode, 'orderId': orderId},
            function (result) {
                //alert(result);
                location.reload();
                //jQuery('.tblData').html(result);
                //alert(result);
                //window.location.replace(base_url);
            });
    }

    function changeShippingOrder(price, elem) {
        var shiping = elem.val();
        orderId = $('input[name="order_id"]').attr('value');
        $.post(base_url + 'ajax/applyShippingToOrder',
            {'shiping': shiping, 'orderId': orderId, 'price': price},
            function (result) {
                //alert(result);
                location.reload();
                //jQuery('.tblData').html(result);
                //alert(result);
                //window.location.replace(base_url);
            });
    }

    jQuery('#sameAsBilling').change(function() {
        if(jQuery(this).prop('checked') == true) {
            jQuery('#shipping_company').val(jQuery('#billing_company').val());
            jQuery('#shipping_first_name').val(jQuery('#billing_first_name').val());
            jQuery('#shipping_last_name').val(jQuery('#billing_last_name').val());
            jQuery('#shipping_email').val(jQuery('#billing_email').val());
            jQuery('#shipping_phone').val(jQuery('#billing_phone').val());
            jQuery('#shipping_street_address').val(jQuery('#billing_street_address').val());
            jQuery('#shipping_address_2').val(jQuery('#billing_address_2').val());
            jQuery('#shipping_city').val(jQuery('#billing_city').val());
            jQuery('#shipping_state').val(jQuery('#billing_state').val());
            jQuery('#shipping_zip').val(jQuery('#billing_zip').val());
            jQuery('#shipping_country').val(jQuery('#billing_country').val());

            $("#shipping_company_display").html($('#billing_company_display').html());
            $("#shipping_first_name_display").html($('#billing_first_name_display').html());
            $("#shipping_last_name_display").html($('#billing_last_name_display').html());
            $("#shipping_email_display").html($('#billing_email_display').html());
            $("#shipping_phone_display").html($('#billing_phone_display').html());
            $("#shipping_street_address_display").html($('#billing_street_address_display').html());
            $("#shipping_address_2_display").html($('#billing_address_2_display').html());
            $("#shipping_city_display").html($('#billing_city_display').html());
            $("#shipping_state_display").html($('#billing_state_display').html());
            $("#shipping_zip_display").html($('#billing_zip_display').html());
            $("#shipping_country_display").html($('#billing_country_display').html());
        } else {
            jQuery('#shipping_company').val('');
            jQuery('#shipping_first_name').val('');
            jQuery('#shipping_last_name').val('');
            jQuery('#shipping_email').val('');
            jQuery('#shipping_phone').val('');
            jQuery('#shipping_street_address').val('');
            jQuery('#shipping_address_2').val('');
            jQuery('#shipping_city').val('');
            jQuery('#shipping_state').val('');
            jQuery('#shipping_zip').val('');
            jQuery('#shipping_country').val('');

            $("#shipping_company_display").html('');
            $("#shipping_first_name_display").html('');
            $("#shipping_last_name_display").html('');
            $("#shipping_email_display").html('');
            $("#shipping_phone_display").html('');
            $("#shipping_street_address_display").html('');
            $("#shipping_address_2_display").html('');
            $("#shipping_city_display").html('');
            $("#shipping_state_display").html('');
            $("#shipping_zip_display").html('');
            $("#shipping_country_display").html('');
        }
        $.when(saveForLater()).done(refreshPage);
    });
    function refreshPage() {
        var orderId = $('input[name="order_id"]').attr('value');
        window.location.replace(base_url + 'admin/order_edit/' + orderId);
    }
</script>
<!--<script src="https://code.jquery.com/jquery-2.1.1.js"></script>-->
<script src="https://js.braintreegateway.com/web/3.7.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.7.0/js/hosted-fields.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.7.0/js/data-collector.min.js"></script>
<script>
    var form = document.querySelector('#order_info');
    var submit = document.querySelector('.processOrderButton');

    braintree.client.create({
        authorization: '<?php echo $clientToken;?>'
    }, function (clientErr, clientInstance) {
        if (clientErr) {
            console.error(clientErr);
            return;
        }

        // This example shows Hosted Fields, but you can also use this
        // client instance to create additional components here, such as
        braintree.hostedFields.create({
            client: clientInstance,
            styles: {
                'input': {
                    'font-size': '14px'
                },
                'input.invalid': {
                    'color': 'red'
                },
                'input.valid': {
                    'color': 'green'
                }
            },
            fields: {
                number: {
                    selector: '#card-number',
                    placeholder: '4111 1111 1111 1111'
                },
                cvv: {
                    selector: '#cvv',
                    placeholder: '123'
                },
                expirationDate: {
                    selector: '#expiration-date',
                    placeholder: '10/2019'
                }
            }
        }, function (hostedFieldsErr, hostedFieldsInstance) {
            if (hostedFieldsErr) {
                console.error(hostedFieldsErr);
                return;
            }

            submit.removeAttribute('disabled');

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
                    if (tokenizeErr) {
                        console.error(tokenizeErr);
                        //alert('All fields are required.');
                        $('.fld').css('border', '1px solid red');
                        return;
                    }

                    // If this was a real integration, this is where you would
                    // send the nonce to your server.
                    var addNonce = "<input type='hidden' id='payment_method_nonce' name='payment_method_nonce' value='"+ payload.nonce +"'>";
                    $("#order_info").append(addNonce);
                    console.log('Got a nonce: ' + payload.nonce);
                    HTMLFormElement.prototype.submit.call(form);
                });
            }, false);
        });
        // PayPal or Data Collector.
        braintree.dataCollector.create({
            client: clientInstance,
            kount: true
        }, function (err, dataCollectorInstance) {
            if (err) {
                //alert(err);
                return;
            } else {
                //alert(dataCollectorInstance.deviceData)
            }
            // At this point, you should access the dataCollectorInstance.deviceData value and provide it
            // to your server, e.g. by injecting it into your form as a hidden input.
            var addNonce = "<input type='hidden' id='device_data' name='device_data' value='"+ dataCollectorInstance.deviceData +"'>";
            $("#order_info").append(addNonce);
            var deviceData = dataCollectorInstance.deviceData;
        });
    });
</script>