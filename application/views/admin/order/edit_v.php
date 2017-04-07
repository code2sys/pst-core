<div class="content_wrap">
    <div class="content">
        <h1><?php echo $order['order_id']; ?> : <?php echo $order['status']; ?></h1>
        <?php
        echo form_open('', array('class' => 'form_standard', 'id' => 'order_info'));
        echo form_hidden('order_id', $order['order_id']);
        ?>
        <div class="tabular_data">
            <table width="100%" cellpadding="8">
                <tr>
                    <td>Bill To Address (<span class="billing_display"><a href="javascript:void(0);" onclick="$('.billing_display').hide(); $('.billing_edit').show();">Edit</a></span><span class="billing_edit hide"><a href="javascript:void(0);" onclick="$('.billing_display').show(); $('.billing_edit').hide(); updateBilling();">Temp Save</a></span>)</td>
                    <td>Ship To Address (<span class="shipping_display"><a href="javascript:void(0);" onclick="$('.shipping_display').hide(); $('.shipping_edit').show();">Edit</a></span><span class="shipping_edit hide"><a href="javascript:void(0);" onclick="$('.shipping_display').show(); $('.shipping_edit').hide(); updateShipping();">Temp Save</a></span>)</td>
                    <td>Tracking Info (Email Customer)</td>
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
                        <a href="javascript:void(0);" onclick="sendTrackingEmail();" id="button">Send Email</a>
                        <?php
                        if ($order['ship_tracking_code']): $codes = json_decode($order['ship_tracking_code']);
                            foreach ($codes as $key => $code):
                                ?>
                                <?php echo $code[0]; ?> : <?php echo $code[1]; ?>&nbsp;
                                <a href="javascript:void(0);" onclick="removeTrackingCode('<?php echo $key; ?>')"><span style="color:red; vertical-align: super;">x</span></a><br />
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </td>
                    <td>
                        <div class="payment_edit hide">
                            <table width="100%" cellpadding="6">
                                <tr>
                                    <td><b>First Name On Card:*</b></td>
                                    <td><input id="cc_first_name" name="cc_first_name" class="text large" value="<?php echo @$order['ccfname']; ?>" /></td>
                                </tr>
                                <tr>
                                    <td><b>Last Name On Card:*</b></td>
                                    <td><input id="cc_last_name" name="cc_last_name" class="text large" value="<?php echo @$order['cclname']; ?>" /></td>
                                </tr>
                                <tr>
                                    <td><b>Card No:*</b></td>
                                    <td><input id="cc" name="cc" class="text large" value="<?php echo $order['ccnumber']; ?>"/></td>
                                </tr>
                                <tr>
                                    <td><b>Exp. Month:*</b></td>
                                    <td><?php echo form_dropdown('exp_date_mn', $months, @$order['ccexpmo']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>Exp. Year:*</b></td>
                                    <td><?php echo form_dropdown('exp_date_yr', $years, @$order['ccexpyr']); ?></td>
                                </tr>
                                <tr>
                                    <td><b>CSC:*</b></td>
                                    <td><input id="cvc" name="cvc" class="text mini" value="<?php echo @$payment['cvc']; ?>" /></td>
                                </tr>
                            </table>
                        </div>	
                        <div class="payment_display">
                            <!--<b>Card	holders Name:</b><span id="cc_first_name_display"> <?php echo @$order['ccfname']; ?></span>
                            <span id="cc_last_name_display"><?php echo @$order['cclname']; ?></span><br />
                            <b>Credit Card Exp. Date:</b> <span id="exp_date_mn_display"><?php echo @$order['ccexpmo']; ?></span> / 
                            <span id="exp_date_yr_display"><?php echo @$order['ccexpyr']; ?></span><br />
							
							<?php if( @$_SESSION['userRecord']['cc_permission'] ) { ?>
								<b>Credit Card Number:</b><span id="cc_first_name_display"> <?php echo @$order['ccnmbr']; ?></span><br />
							<?php } ?>
                            <b>CVV Code:</b> <span id="exp_date_mn_display"><?php echo @$order['cvc']; ?></span>-->
                        </div>
                        <br />
                        <div class="clear"></div>
                        <br />
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
                            Customer IP Address <b> <?php echo $order['customer_ip'];?> </b>
                        <br />


<!-- <div style="float:left; margin-right:5px;">Cash amount: <input name="cash_amt" class="text mini"></div> -->
                        <?php //endif;   ?>
                    </td>
                </tr>
            </table>
            <div style="float:left">
                <?php // echo form_dropdown('distributor_shipping', array('Select Shipping', 'dealer' => 'Distributor to Dealer', 'customer' => 'Distributor to Customer'));    ?>
            </div>
            <div style="float:right">
                Select Actions <?php
                echo form_dropdown('actions', array('--Check then Select--',
                    'Update Calculations',
                    'Back Order',
                    'Shipped',
                    'Ready For Pick Up',
                    'Returned',
                    'Refunded'), '', 'id="productActions"');
                ?>
            </div>
            <div class="clear"></div>
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
                    if (@$order['products']):
                        ?>
                        <?php
                        foreach ($order['products'] as $key => $product):
                            $qtyComplete = FALSE;
							$qtyLft = $product['qty'];
							$abcd1 = 0;
                            ?>
                            <?php $partnumber = (@$product['distributorRecs'][0]['part_number']) ? $product['distributorRecs'][0]['part_number'] : $product['partnumber']; ?>
                            <tr>
                                <td><?php
                                    echo $product['qty'];
                                    echo form_hidden('qty[' . $product['partnumber'] . ']', $product['qty']);
                                    ?>
                                </td>
                                <td><?php
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
                                                    Cost
                                                </td>
                                            </tr>
                                            <?php
                                            $lowestPrice = 0;
                                            $totalQty = 0;
                                            if (@$product['dealerRecs']):

                                                foreach ($product['dealerRecs'] as $distRec):
													if($distRec['quantity_available'] >= $product['qty']) {
														$qtyLft = 0;
														$abcd1 = $product['qty'];
													} else {
														$qtyLft = $qtyLft-$distRec['quantity_available'];
														$abcd1 = $qtyLft-$distRec['quantity_available'];
													}
													if( $product['dealer_qty'] > 0) {
														$abcd1 = $product['dealer_qty'];
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
                                                            Cost
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <?php echo $distributors[$distRec['distributor_id']]; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $distRec['quantity_available']; ?>
                                                        </td>
                                                        <td>
                                                            <?php
															if( $order['status'] == 'Processing' || $product['distributor_qty'] > 0 ) {
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
                                                            echo form_hidden('distributor_id[' . $product['partnumber'] . ']', $distRec['distributor_id']);
                                                            echo form_hidden('distributor_partnumber[' . $product['partnumber'] . ']', $distRec['part_number']);
                                                            echo form_input(array('name' => 'distributor_qty[' . $product['partnumber'] . ']',
                                                                'value' => @$qtyLft,
                                                                'class' => 'text year dist_qty',
                                                                'placeholder' => 'Qty'));
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $distRec['cost']; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </td>
                                <td><?php echo $product['stock_code']; ?></td>
                                <td><?php if ($partnumber != "COUPON") { echo form_checkbox('action', $product['partnumber']); } ?></td>
                                <td><?php echo (@$product['status']) ? $product['status'] : @$product['stock_code']; ?></td>
                                <td><?php
                                    echo $product['sale'];
                                    echo form_hidden('sale[' . $product['partnumber'] . ']', $product['sale']);
                                    $subtotal += $product['sale'];
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
                                Qty.<br />
                                <input name="search_qty" class="text mini" id="search_qty">
                            </div>
                            <div style="float:left; margin-right:8px;">
                                SKU<br />
                                <input name="search_sku" class="text medium" id="search_sku">
                            </div>
                            <div style="float:left; margin-top:11px">
                                <a href="javascript:void(0);" onclick="addProduct();" id="button">Go</a>
                            </div>
                            <div style="float:left; margin:11px 0 0 20px">
                                <a href="javascript:void(0);" onclick="searchProducts();" id="button">Find Product</a>
                            </div>
                        </td>
                        <td>Subtotal:</td>
                        <td>$<?php
                            //echo number_format($subtotal, 2);
                            echo number_format($order['sales_price'], 2);
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
                        <td rowspan="3">
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
                        <td>Paid:</td>
                        <td>$0.00</td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="float:right;">
            <a href="javascript:void(0);" onclick="saveForLater();" id="button">Save for Later</a>
            <!-- <a href="javascript:void(0);" onclick="addToBatch();" id="button">Add to Batch Order</a> -->
            <a href="javascript:void(0);" onclick="processOrder();" id="button">Update To Processing</a>
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
            window.location.replace(base_url + 'admin/order_edit/' + orderId + '/' + sku + '/' + qty);
        }
    }

    function addToBatch()
    {
        $.when(saveForLater()).done(updateStatus('Batch Order'));
    }

    function processOrder()
    {
        $.when(saveForLater()).done(updateStatus('Processing'));

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
		var sendPst = "<?php echo $order['sendPst'];?>";
		var ordr_status = "<?php echo $order['status']; ?>";
		if(sendPst == '1' || ordr_status == 'Processing') {
			alert('This order has already been Submitted and cannot be submitted twice');
		} else if( (lngth == ttlDlr && dlrQty > 0) || (ttlDlr == 0 && dlrQty > 0) ) {
			alert('Only items being shipped from distributor stock can be sent to PST.');
		} else {
			$.when(saveForLater()).done(updateStatusPST('Processing'));
			//alert();
		}
	}

    function saveForLater()
    {
        var data = $('#order_info').serialize();
        $.post(base_url + 'ajax/order_save/',
                data,
                function (orderId)
                {
                    $('input[name="order_id"]').attr('value', orderId);
                    return orderId;
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

    function sendTrackingEmail()
    {

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
                    location.reload();
                });
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

</script>
