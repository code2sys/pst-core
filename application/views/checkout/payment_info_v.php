<?php
	include('lib/Braintree.php');
	Braintree_Configuration::environment($store_name['environment']);
	Braintree_Configuration::merchantId($store_name['merchant_id']);
	Braintree_Configuration::publicKey($store_name['public_key']);
	Braintree_Configuration::privateKey($store_name['private_key']);
    $clientToken = Braintree_ClientToken::generate();
?>
	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- CART -->
		<div class="main_content_full">
		<!-- CONTENT -->
			<div class="item_section">
			<h1>Purchase</h1>
			<h3>Please select shipping option and enter payment information</h3>
			<p>All fields are required.</p>
			
			<!-- VALIDATION ERROR -->
			<?php if(validation_errors()): ?>
			<div class="validation_error">
				<img src="<?php echo $s_assets; ?>/images/error.png">
				<h1>Error</h1>
				<div class="clear"></div>
				<p><?php echo validation_errors(); ?></p>
			</div>
			<?php endif; ?>
			<!-- END VALIDATION ERROR -->
			
		  <!-- PROCESS ERROR -->
			<?php if(@$processingError): ?>
			<div class="process_error">
				<img src="<?php echo $s_assets; ?>/images/process_error.png">
				<h1>Error</h1>
				<div class="clear"></div>
				<p><?php echo $processingError; ?></p>
			</div>
			<?php endif; ?>
			<!-- END PROCESS ERROR -->
			
		<form action="<?php echo $s_baseURL.'checkout/payment'; ?>" method="post" id="form_example" class="form_standard">				
		
		<!-- BILLING DETAILS -->
			<div class="cart_wrap_left">
				<h3 style="float:left;margin:5px 0 0;">
					<i class="fa fa-list"></i> Billing Details
				</h3>
				<div class="clear"></div>
				<br>
				<p><b>Company Name:</b>&nbsp;<?php echo $contactInfo['company']; ?></p>
				<p><b>First Name:</b>&nbsp;<?php echo $contactInfo['first_name']; ?></p>
				<p><b>Last Name:</b>&nbsp;<?php echo $contactInfo['last_name']; ?></p>
				<p><b>Email Address:</b>&nbsp;<?php echo $contactInfo['email']; ?></p>
				<p><b>Phone:</b>&nbsp;<?php echo $contactInfo['phone']; ?></p>
				<p><b>Address Line 1:</b>&nbsp;<?php echo $contactInfo['street_address']; ?></p>
				<p><b>Address Line 2:</b>&nbsp;<?php echo $contactInfo['address_2']; ?></p>
				<p><b>City:</b>&nbsp;<?php echo $contactInfo['city']; ?></p>
				<p><b>State:</b>&nbsp;<?php echo $contactInfo['state']; ?></p>
				<p><b>Zip:</b>&nbsp;<?php echo $contactInfo['zip']; ?></p>
			</div>
			<!-- END BILLING DETAILS -->
			
			<!-- SHIPPING DETAILS -->
			<div class="cart_wrap_right">
				<h3 style="float:left;margin:5px 0 0;">
					<i class="fa fa-home"></i> Shipping Details
				</h3>
				<div class="clear"></div>
				<br>
				<p><b>Company Name:</b>&nbsp;<?php echo $shippingInfo['company']; ?></p>
				<p><b>First Name:</b>&nbsp;<?php echo $shippingInfo['first_name']; ?></p>
				<p><b>Last Name:</b>&nbsp;<?php echo $shippingInfo['last_name']; ?></p>
				<p><b>Email Address:</b>&nbsp;<?php echo $shippingInfo['email']; ?></p>
				<p><b>Phone:</b>&nbsp;<?php echo $shippingInfo['phone']; ?></p>
				<p><b>Address Line 1:</b>&nbsp;<?php echo $shippingInfo['street_address']; ?></p>
				<p><b>Address Line 2:</b>&nbsp;<?php echo $shippingInfo['address_2']; ?></p>
				<p><b>City:</b>&nbsp;<?php echo $shippingInfo['city']; ?></p>
				<p><b>State:</b>&nbsp;<?php echo $shippingInfo['state']; ?></p>
				<p><b>Zip:</b>&nbsp;<?php echo $shippingInfo['zip']; ?></p>
			</div>
			<div class="clear"></div>
			<!-- END SHIPPING DETAILS -->
<?php if(@$cart): $i = 0; $total = 0;  ?>			
			<!-- ORDERS -->
			<div class="cart_wrap">
				<h3 style="float:left;margin:5px 0 0;">
					<i class="fa fa-shopping-cart"></i> Order Items
				</h3>
				<div class="clear"></div>
				<br>
				<div class="hidden_table">
					<table width="100%" cellpadding="6">
						<?php foreach($cart as $key => $product): if(isset($product['display_name'])):?>
						
						<tr>
							<?php if(@$product['display_name'] != 'Shipping'): ?>
							<td>
								<?php if(@$product['images']): ?>			
							<img src="<?php echo $s_baseURL . "productimages/".$product['images']['path']; ?>" title="<?php echo @$product['display_name']; ?>" border="0" width="80"><br />
							<?php echo @$product['images']['description']; ?>
						<?php endif; ?>	
							
							</td>
							<td>
								<b><?php $product['display_name'] = str_replace('|||', '<br /><span class="smaller_font">', $product['display_name']);
									$product['display_name'] = str_replace('||', '</span>', $product['display_name']);
									echo $product['display_name'];?></b><br>
									<?php
									$product['price'] = (float)str_replace("$","",$product['price']);
									?>
								<b>Unit Price:</b> $<?php echo number_format( str_replace(',', '', $product['price']), 2) ; ?>
							</td>
							<td width="20%">
								<b>Quantity:</b> <?php echo @$product['qty']; ?><br>
								<b>Sub Total:</b> <?php if(@$product['finalPrice']): ?>$<?php echo number_format(str_replace(',', '', $product['finalPrice']), 2); endif; ?>
							</td>
						</tr>
						<?php endif; endif; endforeach; ?>
						<tr>
							
						</tr>
					</table>
				</div>
			</div>
			<!-- END ORDERS -->
<?php  endif; ?>

			<!-- SHIPPING -->
			<div class="cart_wrap_left" style="height:415px">
				<h3 style="float:left;margin:5px 0 0;">
					<i class="fa fa-truck"></i> Shipping Method
				</h3>
				<div class="clear"></div>
				<br>
				<h3>Please Select Your Shipping Method</h3>
				<p>Shipping is required.</p>
				<div class="hidden_table">
					<table width="100%" cellpadding="6">
						
						<?php if(@$postalOptDD): foreach($postalOptDD as $code => $arr):
										$set = FALSE; ?>
						<?php if(@$_POST['shippingValue'] == $code):
								 		$set = TRUE; 
								 	elseif(!isset($_POST['shippingValue']) && ($code == 'GND')):
								 		$set = TRUE; 
								 	endif; ?>	
						<tr>
							<td><?php echo form_radio('shippingValue', $code, $set, 'onclick="changeTotal('.$arr['value'].');"'); ?></td>
							<td><b><?php echo $arr['label']; ?></b></td>
							
						</tr>
						<?php endforeach; endif; ?>
					</table>
					
				</div>
			</div>
			<!-- END CREDIT CARD -->


			
			<!-- CREDIT CARD -->
			<div class="cart_wrap_right">
				<h3 style="float:left;margin:5px 0 0;">
					<i class="fa fa-credit-card"></i> Payment Details
				</h3>
				<h1 style="margin:0; float:right;">Total: $<span id="total"></span></h1>
				<div class="clear"></div>
				<br>
				
				<div class="hidden_table">
					<div class="pay">
						<p>Field marked with a * are required</p>
						
						<div class="fld1" style="margin-top:50px;">
						  <label for="card-number" style="width:30%;float:left;">Card Number *</label>
						  <div id="card-number" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
						</div>

						<div class="fld1" style="margin-top:100px;">
						  <label for="cvv" style="width:30%;float:left;">CVV *</label>
						  <div id="cvv" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
						</div>

						<div class="fld1" style="margin-top:150px;">
						  <label for="expiration-date" style="width:30%;float:left;">Expiration Date *</label>
						  <div id="expiration-date" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
						</div>
						<!--<table width="100%" cellpadding="6">
							<tr>
								<td><b>First Name On Card:*</b></td>
								<td><input id="name" name="cc_first_name" class="text large" value="<?php echo set_value('cc_first_name'); ?>" /></td>
							</tr>
							<tr>
								<td><b>Last Name On Card:*</b></td>
								<td><input id="name" name="cc_last_name" class="text medium" value="<?php echo set_value('cc_last_name'); ?>" /></td>
							</tr>
							<tr>
								<td><b>Card No:*</b></td>
								<td><input id="name" name="cc" class="text large" value="<?php echo set_value('cc'); ?>"/></td>
							</tr>
							<tr>
								<td><b>Exp. Month:*</b></td>
								<td>
									<?php echo form_dropdown('exp_date_mn', $months, set_select('exp_date_mn')); ?>
								</td>
							</tr>
							<tr>
								<td><b>Exp. Year:*</b></td>
								<td>
									<?php echo form_dropdown('exp_date_yr', $years, set_select('exp_date_yr')); ?>
								</td>
							</tr>
							<tr>
								<td><b>CVV:*</b></td>
								<td><input id="name" name="cvc" class="text mini" value="<?php echo set_value('cvc'); ?>" /></td>
							</tr>
						</table>-->
						<?php if(@validation_errors() || @$processingError): if(@$_SESSION['failed_validation']): $_SESSION['failed_validation']++; else: $_SESSION['failed_validation'] = 1; endif; ?>
							<div class="g-recaptcha" data-sitekey="6Lc22wMTAAAAAE3FDQa5VE0uk4ac2rxE3kIl_XHa"></div>
						<?php endif; ?>
						
						<input type="submit" class="input_button_purple" style="float:right;margin-top:50px;" value="Process Your Order">
						<!--<button type="submit" class="input_button_purple" style="float:right;">Process Your Order</button>-->
						<div class="clear"></div>
					</div>
				</form>
				</div>
			</div>
		
			<div class="clear"></div>
			<!-- END CREDIT CARD -->
				
			<!-- END CHECK OUT -->
		
		</div>
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
	


</div>
<!-- END WRAPPER ==========================================================================-->

<script>
// $('#myform').submit(function submitClick(e)
// {
    // e.preventDefault();
// });

</script>

<script src="https://code.jquery.com/jquery-2.1.1.js"></script>
<script src="https://js.braintreegateway.com/web/3.7.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.7.0/js/hosted-fields.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.7.0/js/data-collector.min.js"></script>
<script>
  var form = document.querySelector('#form_example');
  var submit = document.querySelector('input[type="submit"]');
  
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
		  $("#form_example").append(addNonce);
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
	    $("#form_example").append(addNonce);
		var deviceData = dataCollectorInstance.deviceData;
	});
  });
</script>

<script>
$(document).ready(function(){
	$("#pay").click(function(){
		$(".pay").show();
		$(".paypal").hide();
	});
	$("#payment").click(function(){
		$(".pay").hide();
		$(".paypal").show();
	})
});
</script>

<script>
	$('#total').html('<?php echo number_format(($cart['transAmount'] + @$_SESSION['cart']['tax']['finalPrice'] + $arr['value']), 2, '.', ''); ?>');
	//$('#paypal_amt').val('<?php echo number_format(($cart['transAmount'] + @$_SESSION['cart']['tax']['finalPrice'] + $arr['value']), 2, '.', ''); ?>');
	
	function changeTotal(value)
	{
		caltotal = <?php echo number_format(($cart['transAmount'] + @$_SESSION['cart']['tax']['finalPrice']), 2, '.', ''); ?> + value;
		$('#total').html(caltotal.toFixed(2));
		//$('#paypal_amt').val(caltotal.toFixed(2));
	}
</script>