<?php
$google_trust = (array) json_decode($address['google_trust']);
if (!defined("ENABLE_OEMPARTS_BUTTON")) {
	define("ENABLE_OEMPARTS_BUTTON", false);
}
?>
<link rel="stylesheet" href="/assets/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="/assets/jqwidgets/js/jqxcore.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxcolorpicker.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxradiobutton.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxdropdownbutton.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxscrollview.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxbuttons.js"></script>


	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		
		<!-- MAIN -->
		<div class="content">
		<?php if(@validation_errors()): ?>		
			<!-- VALIDATION ALERT -->
			<div class="validation_error">
			<img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
		    <h1>Error</h1>
		    <p><?php echo validation_errors(); ?></p>
		    <div class="clear"></div>
			</div>
			<!-- END VALIDATION ALERT -->
		<?php endif; ?>
		
		<?php if(@$success): ?>
		<!-- SUCCESS MESSAGE -->
  		<div class="success">
  		  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
  	    <h1>Success</h1>
  	    <div class="clear"></div>
  	    <p>
  	      Your changes have been made.
  	    </p>
  	    <div class="clear"></div>
  		</div>
		<!-- END SUCCESS MESSAGE -->
		<?php endif; ?>
		<?php echo form_open('admin/profile', array('class' => 'form_standard', 'enctype' => 'multipart/form-data')); ?>
			<!-- EDIT PROFILE -->
			<div class="account_section">
				<h1><i class="fa fa-pencil"></i> Store Information</h1>
				<div class="hidden_table">
					
					<table width="100%" cellpadding="6">
						<!--<tr>
							<td><b>Store Deal Percentage:</b></td>
							<td><?php echo form_input(array('name' => 'deal', 
                              'value' => @$dealPercentage, 
                              'class' => 'text large',
                              'placeholder' => 'Deal Percentage')); ?></td>
						</tr>-->
						<tr>
							<td><b>Store Name:</b></td>
							<td style="width:85%;"><?php echo form_input(array('name' => 'company', 
                              'value' => array_key_exists("company", $address) ? $address["company"] : "", 
                              'class' => 'text large',
                              'placeholder' => 'Store Name')); ?></td>
						</tr>
						<!--<tr>
							<td style="width:200px"><b>Contact First Name:</b></td>
							<td><?php echo form_input(array('name' => 'first_name', 
                              'value' => @$address['first_name'], 
                              'class' => 'text large', 
                              'placeholder' => 'First Name')); ?></td>
						</tr>
						<tr>
							<td><b>Contact Last Name:</b></td>
							<td><?php echo form_input(array('name' => 'last_name', 
                              'value' => @$address['last_name'], 
                              'class' => 'text large',
                              'placeholder' => 'Last Name')); ?></td>
						</tr>-->
						<tr>
							<td><b>Phone:*</b></td>
							<td><?php echo form_input(array('name' => 'phone', 
                              'value' => array_key_exists("phone", $address) ? $address["phone"] : "", 
                              'class' => 'text large',
                              'placeholder' => 'Phone')); ?></td>
						</tr>
						<tr>
							<td><b>Email:*</b></td>
							<td><?php echo form_input(array('name' => 'email', 
                              'value' => array_key_exists("email", $address) ? $address["email"] : "", 
                              'class' => 'text large',
                              'placeholder' => 'Email')); ?></td>
						</tr>
						<tr>
							<td id="billing_street_address_label"><b>Store Address Line 1:*</b></td>
							<td><?php echo form_input(array('name' => 'street_address', 
  	                              'value' => array_key_exists("street_address", $address) ? $address["street_address"] : "", 
  	                              'id' => 'billing_street_address',
  	                              'class' => 'text large',
  	                              'placeholder' => 'Enter Store Address')); ?></td>
						</tr>
						<tr>
							<td id="billing_address_2_label"><b>Store Address Line 2:</b></td>
							<td><?php echo form_input(array('name' => 'address_2', 
  	                               'value' => array_key_exists("address_2", $address) ? $address["address_2"] : "", 
  	                               'id' => 'billing_address_2',
  	                               'class' => 'text large',
  	                               'placeholder' => 'Apt. Bld. Etc')); ?></td>
						</tr>
						<tr>
							<td id="billing_city_label"><b>Store City:*</b></td>
							<td><?php echo form_input(array('name' => 'city', 
  	                              'value' => array_key_exists("city", $address) ? $address["city"] : "", 
  	                              'id' => 'billing_city',
  	                              'placeholder' => 'Enter City', 
  	                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td id="billing_state_label"><b>Store State:*</b></td>
							<td><?php echo form_dropdown('state', $states, array_key_exists("state", $address) ? $address["state"] : "", 'id="billing_state"'); ?></td>
						</tr>
						<tr>
							<td id="billing_zip_label"><b>Store Zip:*</b></td>
							<td><?php echo form_input(array('name' => 'zip', 
  	                              'value' => array_key_exists("zip", $address) ? $address["zip"] : "", 
  	                              'id' => 'billing_zip',
  	                              'class' => 'text large',
  	                              'placeholder' => 'Zipcode')); ?></td>
						</tr>
						<tr>
							<td><b>Store Country:*</b></td>
							<td><?php echo form_dropdown('country', 
							                                      $countries, 
							                                      array_key_exists("country", $address) ? $address["country"] : "", 
							                                      'id="billing_country" onChange="newChangeCountry(\'billing\');"'); ?></td>
						</tr>
						<tr>
							<td><b>Sales Email:</b></td>
							<td><?php echo form_input(array('name' => 'sales_email', 
                              'value' => array_key_exists("sales_email", $address) ? $address["sales_email"] : "", 
                              'class' => 'text large',
                              'placeholder' => 'Store Email')); ?></td>
						</tr>
						<tr>
							<td><b>Service Email:</b></td>
							<td><?php echo form_input(array('name' => 'service_email', 
                              'value' => array_key_exists("service_email", $address) ? $address["service_email"] : "", 
                              'class' => 'text large',
                              'placeholder' => 'Service Email')); ?></td>
						</tr>
						<tr>
							<td><b>Finance Email:</b></td>
							<td><?php echo form_input(array('name' => 'finance_email', 
                              'value' => array_key_exists("finance_email", $address) ? $address["finance_email"] : "", 
                              'class' => 'text large',
                              'placeholder' => 'Finance Email')); ?></td>
						</tr>
                        <tr>
                            <td style="width:30%;"><b>Store Hours:</b></td>
                            <td><label>Free-Form <input type="radio" value="1" name="free_form_hours" <?php if($address['free_form_hours']== 1) echo 'checked="checked"'; ?></label> <label>Daily Hours <input type="radio" value="0" name="free_form_hours" <?php if($address['free_form_hours'] != 1) echo 'checked="checked"'; ?></label>
                            </td>
                        </tr>

                        <tr class="hours_structured">
                            <td><b>Monday Hours:</b></td>
                            <td><?php echo form_input(array('name' => 'monday_hours',
                                    'value' => array_key_exists("monday_hours", $address) ? $address["monday_hours"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>
                        <tr class="hours_structured">
                            <td><b>Tuesday Hours:</b></td>
                            <td><?php echo form_input(array('name' => 'tuesday_hours',
                                    'value' => array_key_exists("tuesday_hours", $address) ? $address["tuesday_hours"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>
                        <tr class="hours_structured">
                            <td><b>Wednesday Hours:</b></td>
                            <td><?php echo form_input(array('name' => 'wednesday_hours',
                                    'value' => array_key_exists("wednesday_hours", $address) ? $address["wednesday_hours"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>
                        <tr class="hours_structured">
                            <td><b>Thursday Hours:</b></td>
                            <td><?php echo form_input(array('name' => 'thursday_hours',
                                    'value' => array_key_exists("thursday_hours", $address) ? $address["thursday_hours"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>
                        <tr class="hours_structured">
                            <td><b>Friday Hours:</b></td>
                            <td><?php echo form_input(array('name' => 'friday_hours',
                                    'value' => array_key_exists("friday_hours", $address) ? $address["friday_hours"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>
                        <tr class="hours_structured">
                            <td><b>Saturday Hours:</b></td>
                            <td><?php echo form_input(array('name' => 'saturday_hours',
                                    'value' => array_key_exists("saturday_hours", $address) ? $address["saturday_hours"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>
                        <tr class="hours_structured">
                            <td><b>Sunday Hours:</b></td>
                            <td><?php echo form_input(array('name' => 'sunday_hours',
                                    'value' => array_key_exists("sunday_hours", $address) ? $address["sunday_hours"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>



                        <tr class="hours_structured">
                            <td style="width:30%;"><strong>Additional Hours Note:</strong></td>
                            <td><?php echo form_textarea(array('name' => 'hours_note',
                                    'value' => array_key_exists("hours_note", $address) ? $address["hours_note"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>

                        <tr class="hours_free_form">
                            <td style="width:30%;"><strong>Store Hours:</strong></td>
                            <td><?php echo form_textarea(array('name' => 'free_form_hour_blob',
                                    'value' => array_key_exists("free_form_hour_blob", $address) ? $address["free_form_hour_blob"] : "",
                                    'class' => 'text large',
                                    'placeholder' => '')); ?></td>
                        </tr>

                        <script type="application/javascript">
                            $(document).on("ready", function() {
                                // we have to hide and bind these things...
                                var $show_hide_function = function() {
                                    if ($("input[name=free_form_hours][value=1]:checked").length > 0) {
                                        $(".hours_structured").hide();
                                        $(".hours_free_form").show();
                                    } else {
                                        $(".hours_structured").show();
                                        $(".hours_free_form").hide();
                                    }
                                };

                                $("input[name=free_form_hours]").on("change", $show_hide_function);
                                $show_hide_function();

                            });


                        </script>




<!--						<tr>-->
<!--							<td><strong>Logo:</strong><br/><em>The logo should usually be 200px wide. Please provide as GIF, JPG, or PNG format.</em></td>-->
<!--							<td>-->
<!--								--><?php
//								$file = STORE_DIRECTORY . "/html/logo.png";
//								if (file_exists($file)) {
//									?>
<!--									<em>Existing Logo:</em> </br>-->
<!--									<a href="/logo.png?time=--><?php //echo time(); ?><!--" download="logo.png"><img src="/logo.png?time=--><?php //echo time(); ?><!--" /></a>-->
<!--									<br/>-->
<!--									<br/>-->
<!--									<em>Upload a New Logo:</em><br/>-->
<!--									--><?php
//								}
//								?>
<!--								<input type="file" accept="image/*" name="logo"/>-->
<!--							</td>-->
<!--						</tr>-->
<!--						<tr>-->
<!--							<td><strong>Favicon:</strong><br/><em>The favicon should be a square, e.g. 64x64 pixels. Please provide as GIF, JPG, PNG, or ICO format.</em></td>-->
<!--							<td>-->
<!--								--><?php
//									$file = STORE_DIRECTORY . "/html/favicon.ico";
//									if (file_exists($file)) {
//										?>
<!--										<em>Existing Favicon:</em> </br>-->
<!--										<a href="/favicon.ico?time=--><?php //echo time(); ?><!--" download="favicon.ico"><img src="/favicon.ico?time=--><?php //echo time(); ?><!--" /></a>-->
<!--										<br/>-->
<!--										<br/>-->
<!--										<em>Upload a New Favicon:</em><br/>-->
<!--										--><?php
//									}
//								?>
<!--								<input type="file" accept="image/*" name="favicon"/>-->
<!--							</td>-->
<!--						</tr>-->
                        <tr>
                            <td colspan="2">
                                <table width="100%" style="background-color:white;">
                                    <tr>
                                        <td colspan="2">
                                            <h2>Store Header Banner</h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <p><em>The banner appears on all pages, immediately below the header.</em></p>
                                        </td>
                                    </tr>
                                    <!-- Radio yes/no  store_header_banner_enable 0|1 -->

                                    <tr>
                                        <td style="width:30%;"><b>Enable:</b></td>
                                        <td><label><input type="radio" name="store_header_banner_enable" value="1" <?php if ($store_header_banner_enable == 1): ?>checked="checked"<?php endif; ?>/> Yes</label> <label><input type="radio" name="store_header_banner_enable" value="0" <?php if ($store_header_banner_enable != 1): ?>checked="checked"<?php endif; ?>/> No</label></td>
                                    </tr>

                                    <!-- Contents - can we get an editor ?  store_header_banner_contents -->
                                    <tr class="store_header_banner_enable_1">
                                        <td style="width:30%;"><b>Contents:</b></td>
                                        <td><textarea id="store_header_banner_contents" name="store_header_banner_contents" rows="10" cols="80"><?php echo htmlentities($store_header_banner_contents); ?></textarea></td>
                                    </tr>



                                    <!-- background color store_header_banner_bgcolor -->
                                    <tr class="store_header_banner_enable_1">
                                        <td style="width:30%;"><b>Background Color:</b></td>
                                        <td><input type="text" id="store_header_banner_bgcolor" name="store_header_banner_bgcolor" size="16" value="<?php echo htmlentities($store_header_banner_bgcolor); ?>" style="display: none"/>
                                            <div style="margin: 3px; float: left;" id="dropDownButton">
                                                <div style="padding: 3px;">
                                                    <div id="store_header_banner_bgcolor_div">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </table>
                            </td>

                        </tr>

                        <tr>
                            <td colspan="2">
                                <table width="100%" style="background-color:white;">
                                    <tr>
                                        <td colspan="2">
                                            <h2>Header Marquee</h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <p><em>The marquee is a rotating text message that appears across the header of your store.</em></p>
                                        </td>
                                    </tr>
                                    <!-- Radio yes/no  store_header_banner_enable 0|1 -->

                                    <tr>
                                        <td style="width:30%;"><b>Enable:</b></td>
                                        <td><label><input type="radio" name="store_header_marquee_enable" value="1" <?php if ($store_header_marquee_enable == 1): ?>checked="checked"<?php endif; ?>/> Yes</label> <label><input type="radio" name="store_header_marquee_enable" value="0" <?php if ($store_header_marquee_enable != 1): ?>checked="checked"<?php endif; ?>/> No</label></td>
                                    </tr>

                                    <!-- Contents - can we get an editor ?  store_header_banner_contents -->
                                    <tr class="store_header_marquee_enable_1">
                                        <td style="width:30%;"><b>Contents:</b></td>
                                        <td><textarea id="store_header_banner_contents" name="store_header_marquee_contents" rows="10" cols="80"><?php echo htmlentities($store_header_marquee_contents); ?></textarea></td>
                                    </tr>



                                    <!-- background color store_header_banner_bgcolor -->
                                    <tr class="store_header_marquee_enable_1">
                                        <td style="width:30%;"><b>Text Color:</b></td>
                                        <td><input type="text" id="store_header_marquee_color" name="store_header_marquee_color" size="16" value="<?php echo htmlentities($store_header_marquee_color); ?>" style="display: none"/>
                                            <div style="margin: 3px; float: left;" id="store_header_marquee_color_dropDownButton">
                                                <div style="padding: 3px;">
                                                    <div id="store_header_marquee_color_div">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                </table>
                            </td>

                        </tr>


                        <script type="text/javascript">
                            function getTextElementByColor(color) {
                                if (color == 'transparent' || color.hex == "") {
                                    return $("<div style='text-shadow: none; position: relative; padding-bottom: 2px; margin-top: 2px;'>transparent</div>");
                                }
                                var element = $("<div style='text-shadow: none; position: relative; padding-bottom: 2px; margin-top: 2px;'>#" + color.hex + "</div>");
                                var nThreshold = 105;
                                var bgDelta = (color.r * 0.299) + (color.g * 0.587) + (color.b * 0.114);
                                var foreColor = (255 - bgDelta < nThreshold) ? 'Black' : 'White';
                                element.css('color', foreColor);
                                element.css('background', "#" + color.hex);
                                element.addClass('jqx-rc-all');
                                return element;
                            }

                            $(document).ready(function () {

                                CKEDITOR.replace( 'store_header_banner_contents' );

                                var showHeaderBannerParts = function() {
                                    if ($("input[name='store_header_banner_enable'][value=1]:checked").length > 0) {
                                        $(".store_header_banner_enable_1").show();
                                    } else {
                                        $(".store_header_banner_enable_1").hide();
                                    }
                                };

                                $(document).on("change", "input[name='store_header_banner_enable']", showHeaderBannerParts);
                                showHeaderBannerParts();


                                var showMarqueeParts = function() {
                                    if ($("input[name='store_header_marquee_enable'][value=1]:checked").length > 0) {
                                        $(".store_header_marquee_enable_1").show();
                                    } else {
                                        $(".store_header_marquee_enable_1").hide();
                                    }
                                };

                                $(document).on("change", "input[name='store_header_marquee_enable']", showMarqueeParts);
                                showMarqueeParts();


                                $("#store_header_banner_bgcolor_div").jqxColorPicker({ width: 200, height: 200 });
                                $("#store_header_banner_bgcolor_div").jqxColorPicker("setColor", "<?php echo substr($store_header_banner_bgcolor, 1); ?>");
                                $('#store_header_banner_bgcolor_div').bind('colorchange', function (event)
                                {
                                    var color = event.args.color;
                                    $("#store_header_banner_bgcolor").val('#' + color.hex);

                                    $("#dropDownButton").jqxDropDownButton('setContent', getTextElementByColor(event.args.color));

                                });

                                $("#dropDownButton").jqxDropDownButton({ width: 150, height: 22});
                                $("#dropDownButton").jqxDropDownButton('setContent', getTextElementByColor(new $.jqx.color({ hex: "<?php echo substr($store_header_banner_bgcolor, 1); ?>" })));

                                $("#store_header_marquee_color_div").jqxColorPicker({ width: 200, height: 200 });
                                $("#store_header_marquee_color_div").jqxColorPicker("setColor", "<?php echo substr($store_header_marquee_color, 1); ?>");
                                $('#store_header_marquee_color_div').bind('colorchange', function (event)
                                {
                                    var color = event.args.color;
                                    $("#store_header_marquee_color").val('#' + color.hex);

                                    $("#store_header_marquee_color_dropDownButton").jqxDropDownButton('setContent', getTextElementByColor(event.args.color));

                                });

                                $("#store_header_marquee_color_dropDownButton").jqxDropDownButton({ width: 150, height: 22});
                                $("#store_header_marquee_color_dropDownButton").jqxDropDownButton('setContent', getTextElementByColor(new $.jqx.color({ hex: "<?php echo substr($store_header_marquee_color, 1); ?>" })));

                            });

                        </script>

                        <tr>
                            <td style="width:30%;"><b>Payment Processor:</b></td>
                            <td><label><input type="radio" name='merchant_type' value="Braintree" <?php if($address['merchant_type']=="Braintree") echo 'checked="checked"'; ?> />Braintree</label>
                                <label><input type="radio" name='merchant_type' value="Stripe" <?php if($address['merchant_type']=="Stripe") echo 'checked="checked"'; ?> /> Stripe</label>
                            </td>
                        </tr>

						<tr class="Stripe_ProcessorSettings ProcessorSettings">
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="https://stripe.com/img/about/logos/logos/blue.png" style="width: 233px; height: auto;" alt="Stripe" />
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Stripe Publishable API Key:</b></td>
										<td><?php echo form_input(array('name' => 'stripe_api_key',
										  'value' => array_key_exists("stripe_api_key", $address) ? $address["stripe_api_key"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Stripe Publishable API Key')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Stripe Secret API Key:</b></td>
										<td><?php echo form_input(array('name' => 'stripe_secret_api_key',
										  'value' => array_key_exists("stripe_secret_api_key", $address) ? $address["stripe_secret_api_key"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Stripe Secret API Key')); ?></td>
									</tr>
								</table>
							</td>
						</tr>

                        <tr class="Braintree_ProcessorSettings ProcessorSettings">
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="https://s3.amazonaws.com/braintree-badges/braintree-badge-wide-dark.png" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Merchant ID:</b></td>
										<td><?php echo form_input(array('name' => 'merchant_id',
										  'value' => array_key_exists("merchant_id", $address) ? $address["merchant_id"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Merchant ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Public Key:</b></td>
										<td><?php echo form_input(array('name' => 'public_key',
										  'value' => array_key_exists("public_key", $address) ? $address["public_key"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Public Key')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Private Key:</b></td>
										<td><?php echo form_input(array('name' => 'private_key',
										  'value' => array_key_exists("private_key", $address) ? $address["private_key"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Private Key')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Environment:</b></td>
										<td><?php echo form_input(array('name' => 'environment',
										  'value' => array_key_exists("environment", $address) ? $address["environment"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Environment')); ?></td>
									</tr>
								</table>
							</td>
						</tr>

                        <script type="application/javascript">
                            (function() {
                                var merchant_typeFN = function() {
                                    $(".ProcessorSettings").hide();
                                    var val = $("input[name='merchant_type']:checked").val();
                                    if (val && val != "") {
                                        $("." + val + "_ProcessorSettings").show();
                                    }
                                };
                                $("input[name='merchant_type']").on("click", merchant_typeFN);
                                merchant_typeFN();
                            })();
                        </script>

                        <?php

                        if (!defined('HIDE_EBAY_FEED')) {
                            define('HIDE_EBAY_FEED', false);
                        }

                        if (!HIDE_EBAY_FEED):
                        ?>
						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="<?php echo $assets; ?>/images/ebay_logo.png" width="230px" border="0">
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>App ID:</b></td>
										<td><?php echo form_input(array('name' => 'ebay_app_id', 
										  'value' => array_key_exists("ebay_app_id", $address) ? $address["ebay_app_id"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'App ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Dev ID:</b></td>
										<td><?php echo form_input(array('name' => 'ebay_dev_id', 
										  'value' =>  array_key_exists("ebay_dev_id", $address) ? $address["ebay_dev_id"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'Dev ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Cert ID:</b></td>
										<td><?php echo form_input(array('name' => 'ebay_cert_id', 
										  'value' =>  array_key_exists("ebay_cert_id", $address) ? $address["ebay_cert_id"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'Cert ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>User Token:</b></td>
										<td><?php echo form_input(array('name' => 'ebay_user_token', 
										  'value' => array_key_exists("ebay_user_token", $address) ? $address["ebay_user_token"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'User Token')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Environment:</b></td>
										<td>Sandbox <input type="radio" name='ebay_environment' value="Sandbox" <?php if($address['ebay_environment']=="Sandbox") echo 'checked="checked"'; ?> /> 
										Live <input type="radio" name='ebay_environment' value="Live" <?php if($address['ebay_environment']=="Live") echo 'checked="checked"'; ?> /> 
</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Paypal Email:</b></td>
										<td><?php echo form_input(array('name' => 'ebay_paypal_email', 
										  'value' => array_key_exists("ebay_paypal_email", $address) ? $address["ebay_paypal_email"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'Paypal Email')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
                        <?php endif; ?>
						
						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="<?php echo base_url().'assets/benz_assets/gtrust.png';?>" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>ID:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[id]', 
										  'value' => array_key_exists("id", $google_trust) ? $google_trust["id"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Badge Position:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[badge_position]', 
										  'value' => array_key_exists("badge_position", $google_trust) ? $google_trust["badge_position"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'Badge Position')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Locale:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[locale]', 
										  'value' => array_key_exists("locale", $google_trust) ? $google_trust["locale"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'Locale')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Base Subaccount ID:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[google_base_subaccount_id]', 
										  'value' => array_key_exists("google_base_subaccount_id", $google_trust) ? $google_trust["google_base_subaccount_id"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'Google Base Subaccount ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Base Country:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[google_base_country]', 
										  'value' => array_key_exists("google_base_country", $google_trust) ? $google_trust["google_base_country"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'Google Base Country')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="<?php echo base_url().'assets/benz_assets/remarketing.jpg';?>" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Conversion ID :</b></td>
										<td><?php echo form_input(array('name' => 'google_conversion_id',
												'value' => array_key_exists("google_conversion_id", $address) ? $address["google_conversion_id"] : "",
												'class' => 'text large',
												'placeholder' => 'ID')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<h2>Analytics and Tracking Codes</h2>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Analytics ID :</b></td>
										<td><?php echo form_input(array('name' => 'analytics_id', 
										  'value' => array_key_exists("analytics_id", $address) ? $address["analytics_id"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'Google Analytics ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Site Verification Code:</b></td>
										<td><?php echo form_input(array('name' => 'google_site_verification',
										  'value' => array_key_exists("google_site_verification", $address) ? $address["google_site_verification"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Google Site Verification Code')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Bing Webmaster Site Verification Code:</b></td>
										<td><?php echo form_input(array('name' => 'bing_site_verification',
										  'value' => array_key_exists("bing_site_verification", $address) ? $address["bing_site_verification"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Bing Site Verification Code')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><strong>Additional Header Code:</strong><br/><em>(Enter Full Script Tags)</em></td>
										<td><?php echo form_textarea(array('name' => 'additional_tracking_code',
										  'value' => array_key_exists("additional_tracking_code", $address) ? $address["additional_tracking_code"] : "",
										  'class' => 'text large',
										  'placeholder' => '')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><strong>Additional Footer Code:</strong><br/><em>(Enter Full Script Tags)</em></td>
										<td><?php echo form_textarea(array('name' => 'additional_footer_code',
										  'value' => array_key_exists("additional_footer_code", $address) ? $address["additional_footer_code"] : "",
										  'class' => 'text large',
										  'placeholder' => '')); ?></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="<?php echo base_url().'assets/benz_assets/fremarketing.png';?>" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>FB Remarketing pixel ID :</b></td>
										<td><?php echo form_input(array('name' => 'fb_remarketing_pixel', 
										  'value' => array_key_exists("fb_remarketing_pixel", $address) ? $address["fb_remarketing_pixel"] : "", 
										  'class' => 'text large',
										  'placeholder' => 'FB Remarketing pixel ID')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
<?php if (ENABLE_OEMPARTS_BUTTON): ?>
						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="/assets/oem_parts.png"  border="0"/> HLSM OEM Parts
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>HLSM OEM Parts URL:</b></td>
										<td><?php echo form_input(array('name' => 'partsfinder_link',
										  'value' => array_key_exists("partsfinder_link", $address) ? $address["partsfinder_link"] : "",
										  'class' => 'text large',
										  'placeholder' => 'Enter complete onlinemicrofiche.com URL here...')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
<?php endif; ?>

<?php if (defined('ENABLE_LIGHTSPEED') && ENABLE_LIGHTSPEED): ?>
    <tr>
        <td colspan="2">
            <table width="100%" style="background-color:white;">
                <tr>
                    <td colspan="2">
                        <strong><img src="/assets/ADP-Lightspeed-logo.jpg" alt="Lightspeed DMS Integration" /></strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><em>Lightspeed DMS has an API for pulling major units (e.g., motorcycles). This requires a username and password to access.</em></td>
                </tr>
                <tr>
                    <td style="width:30%;"><b>Lightspeed Username:</b></td>
                    <td><?php echo form_input(array('name' => 'lightspeed_username',
                            'value' => array_key_exists("lightspeed_username", $address) ? $address["lightspeed_username"] : "",
                            'class' => 'text large')); ?></td>
                </tr>
                <tr>
                    <td style="width:30%;"><b>Lightspeed Password:</b></td>
                    <td><?php echo form_input(array('name' => 'lightspeed_password',
                            'value' => array_key_exists("lightspeed_password", $address) ? $address["lightspeed_password"] : "",
                            'class' => 'text large')); ?></td>
                </tr>
            </table>
        </td>
    </tr>

<?php endif; ?>

<?php if (defined('BLUFFPOWERSPORTS_VIEW') && BLUFFPOWERSPORTS_VIEW): ?>
    <tr>
        <td colspan="2">
            <table width="100%" style="background-color:white;">
                <tr>
                    <td colspan="2">
                        <strong><img src="/assets/Traffic_log_Pro_Logo.png" alt="Lightspeed DMS Integration" /></strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><em>Traffic log pro requires a API key and Dealership code to access.</em></td>
                </tr>
                <tr>
                    <td style="width:30%;"><b>Traffic Log Pro API Key:</b></td>
                    <td><?php echo form_input(array('name' => 'trafficLogProApiKey',
                            'value' => array_key_exists("trafficLogProApiKey", $address) ? $address["trafficLogProApiKey"] : "",
                            'class' => 'text large')); ?></td>
                </tr>
                <tr>
                    <td style="width:30%;"><b>Traffic Log Pro Dealership Code:</b></td>
                    <td><?php echo form_input(array('name' => 'trafficLogProDealerCode',
                            'value' =>array_key_exists("trafficLogProDealerCode", $address) ? $address["trafficLogProDealerCode"] : "",
                            'class' => 'text large')); ?></td>
                </tr>
            </table>
        </td>
    </tr>

<?php endif; ?>


						<tr>
							<td></td>
							<td>
								<button type="submit" class="button">Save Changes</button>
							</td>
						</tr>
					</table>
					
					</form>
				</div>
			</div>
			<!-- END EDIT PROFILE -->
			
		</div>
		<!-- END MAIN -->		
		
	
	</div>
	<!-- END CONTENT WRAP ===================================================================-->
<script>
	function newChangeCountry(addressType)
{
  country = $('#'+addressType+'_country').val();
  currentValue = $('#'+addressType+'_state').val();
  $('#'+addressType+'_state').empty();
	if(country == 'US')
	{
	  addressDD = $.post(base_url + 'admin/load_states/1',
		{},
		function(returnData)
		{
		  var dataArr = jQuery.parseJSON(returnData);
      var html = '';
      $.each(dataArr, function(i, value) 
      {
        if(currentValue == i)
          html += '<option selected="selected" value="' + i + '">' + value + '</option>';
        else
        html += '<option value="' + i + '">' + value + '</option>';
      })
      $('#'+addressType+'_state').append(html);

		});
	}
	
	if(country == 'CA')
	{
		addressDD = $.post(base_url + 'admin/load_provinces/1',
		{},
		function(returnData)
		{
      var dataArr = jQuery.parseJSON(returnData);
      var html = '';
      $.each(dataArr, function(i, value) 
      {
        html += '<option value="' + i + '">' + value + '</option>';
      })
      $('#'+addressType+'_state').append(html);

		});
	}
  
  $.post(base_url + 'admin/new_change_country',
	{
	  'country' : country
	},
	function(returnData)
	{
    var dataArr = jQuery.parseJSON(returnData);
    $('#'+addressType+'_street_address_label').html(dataArr.street_address);
    $('#'+addressType+'_address_2_label').html(dataArr.address_2);
    $('#'+addressType+'_city_label').html(dataArr.city);
    $('#'+addressType+'_state_label').html(dataArr.state);
    $('#'+addressType+'_zip_label').html(dataArr.zip);

	});

}

</script>	
