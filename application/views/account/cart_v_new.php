<?php
//require_once( echo site_url()  . 'lib/Braintree.php');
require(__DIR__ . "/../braintree_clienttoken.php");

/*
 * Just an FYI - I think whoever did this thing with Braintree is the biggest fucking hack.
 * So, there's the PayPal button, which appears on the cart, which appears to cut off everything..or does it?
 * It sure looks like it just bypasses most everything in a PARALLEL execution of the checkout process.
 *
 */

?>
<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		<!-- CART -->
		<div class="main_content_full">

			<h1>Shopping Cart</h1>
			<h3 style="margin:0;"><i class="fa fa-home"></i> Items In your Cart (<span id="shopping_count"><?php echo @$_SESSION['cart']['qty']; ?></span>)</h3>
			<p>Please review your selected items</p>
			<!-- CART WRAP -->
			<div class="cart_wrap">
			<?php if(@$cartProducts): $i = 0; $total = 0; foreach($cartProducts as $key => $product): if(($key !== 'qty') && ($key !== 'tax') && ($key !== 'transAmount') && (strpos($key, 'shipping') === FALSE) ):   echo ''; ?>	
				<!-- CART ITEM -->
				<form action="" method="post" id="form_example" class="form_standard">
				<div class="cart_item" >
				<?php if(strpos($product['display_name'], 'Coupon') === FALSE) : ?>
					<div class="cart_photo">		
						<?php if(@$product['images']): ?>			
							<img src="<?php echo base_url("productimages/".$product['images']['path']); ?>" title="<?php echo @$product['display_name']; ?>"><br />
							
						<?php else: ?>
							<img src="<?php echo $assets; ?>/images/test_image.jpg">
						<?php endif; ?>
					</div>
					
					<?php endif; ?>
					<div class="cart_price" style="margin-top:-40px;">
						<h2 class="prdt-ttl-h2"><?php $product['display_name'] = str_replace('|||', '<br /><span class="smaller_font">', $product['display_name']);
									$product['display_name'] = str_replace('||', '</span>', $product['display_name']);
									echo $product['display_name']; ?></h2>
						<h3>Unit Price: <span id="price"> $<?php echo @$product['price'] ? $product['price'] : '0.00'; 
																									$_SESSION['cart'][$key]['price'] = (float)str_replace('$', '', $_SESSION['cart'][$key]['price']);
																									$total += $_SESSION['cart'][$key]['price'] * @$product['qty'];  ?>
													</span></h3>
					</div>
					<div class="cart_amount">
						<?php  if(strpos($product['display_name'], 'Coupon') === FALSE) :  ?>
        				  <?php echo form_input(array('name' => 'qty', 
          			                              'value' => @$product['qty'], 
          			                              'maxlength' => 250, 
          			                              'placeholder' => 'Add Quanity',
          			                              'class' => 'text mini',
          			                              'style' => 'height:16px;',
          			                              'id' => @$product['partnumber'],
          			                              'onChange' => 'updateCart(\''.@$product['partnumber'].'\');'
          			                              )); ?>
          			       
        				  <?php endif; ?>
							<a href="javascript:void(0);" class="button cl-btn" onclick="removeItemFromCart('<?php echo @$key ?>');"><i class="fa fa-times"></i></a>
						
					</div>
					
					<div class="clear"></div>

				</div>
				<!-- END CART ITEM -->
				
				<?php endif; endforeach; endif; ?>
				
				<!-- CART PROMO & TOTAL -->
				<div class="promo_code">
					<h3 style="margin:0;">
						Discount Code:
						<?php echo form_input(array('name' => 'couponCode', 
									  			                              'value' => '', 
									  			                              'maxlength' => 250,
									  			                              'id' => 'coupon',
									  			                              'class' => 'text medium',
									  			                              'placeholder' => 'Enter Code')); ?>
							<button class="button" onclick="updateCart('coupon'); return false;"><i class="fa fa-tag"></i> Apply</button>
					</h3>
				</div>
				<div class="cart_total">
					<h3 style="margin:0;">Cart Total: $<?php if(@$total) { echo number_format($total, 2, '.', ','); } else { ?>0.00<?php } ?></h3>
					
					<a href="<?php echo base_url('/shopping/productlist/'); ?>" class="button fwb"><i class="fa fa-shopping-cart"></i> Continue Shopping</a>
					
					
					<a href="javascript:void(0);" onclick="checkout('<?php echo @$_SESSION['cart']['qty']; ?>');" class="button_purple fwb" style="margin-right:0px;"><i class="fa fa-arrow-cricle-right"></i> Check Out</a>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<!-- END CART PROMO & TOTAL -->
			</form>
			</div>
            <?php
			$new_assets_url = jsite_url("/qatesting/newassets/");
			$new_assets_url1 = jsite_url("/qatesting/benz_assets/");
			?>
			<!-- END CART WRAP -->

            <?php if (isset($clientToken) && $clientToken != ""): ?>
			<script src="https://www.paypalobjects.com/api/button.js?"
			  data-merchant="braintree"
			  data-id="paypal-button"
			  data-button="checkout"
			></script>

			<!-- PayPal Credit Button -->
			<script src="https://www.paypalobjects.com/api/button.js?"
			  data-merchant="braintree"
			  data-id="paypal-credit-button"
			  data-button="credit"
			></script>
            <?php endif; ?>
			
			<link rel="stylesheet" href="<?php echo $new_assets_url;?>stylesheet/custom-widget-pst.com.css" />

			
			
			<!-- CHECK OUT -->
		</div>
	</div>		
	
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
	<!--Script to dynamically choose a buyer account to render on index page-->
    <form id="checkout" class="form-horizontal" method="post" action="/checkout/paypalpayment">
      <div id="payment-form"></div>
    </form>

<?php if (isset($clientToken) && $clientToken != ""): ?>
<script src="https://js.braintreegateway.com/web/3.6.3/js/paypal.min.js"></script>
<script>
  var ppButton = document.getElementById('paypal-button');
  var ppCreditButton = document.getElementById('paypal-credit-button');

  braintree.client.create({
	authorization: '<?=$clientToken?>'
  }, function (err, clientInstance) {
	braintree.paypal.create({
	  client: clientInstance
	}, function (err, paypalInstance) {
	  // Regular PayPal tokenization
	  ppButton.addEventListener('click', function () {
		paypalInstance.tokenize({
		  flow: 'checkout',
		  amount: <?php echo $total; ?>,
		  currency: 'USD',
		  enableShippingAddress: true,
		}, function (err, payload) {
		  if (err) {
			console.error('Regular PayPal tokenization failed:', err);
		  } else {
			console.log('Regular PayPal tokenization result:', payload);
			console.log('Regular PayPal tokenization result:', payload.details);
			console.log('Regular PayPal tokenization result:', payload.details.shippingAddress);
			//console.log('Regular PayPal tokenization result:', payload);
			var addNnce = "<input type='hidden' name='city' value='"+ payload.details.shippingAddress.city +"'>";
			addNnce += "<input type='hidden' name='countryCode' value='"+ payload.details.shippingAddress.countryCode +"'>";
			addNnce += "<input type='hidden' name='address1' value='"+ payload.details.shippingAddress.line1 +"'>";
			addNnce += "<input type='hidden' name='address2' value='"+ payload.details.shippingAddress.line2 +"'>";
			addNnce += "<input type='hidden' name='postalCode' value='"+ payload.details.shippingAddress.postalCode +"'>";
			addNnce += "<input type='hidden' name='recipientName' value='"+ payload.details.shippingAddress.recipientName +"'>";
			addNnce += "<input type='hidden' name='state' value='"+ payload.details.shippingAddress.state +"'>";
			addNnce += "<input type='hidden' name='email' value='"+ payload.details.email +"'>";
			var addNonce = "<input type='hidden' id='payment_method_nonce' name='payment_method_nonce' value='"+ payload.nonce +"'>";
			$("#payment-form").append(addNonce);
			$("#payment-form").append(addNnce);
			// submit the form
			var form = document.getElementById('checkout');
			HTMLFormElement.prototype.submit.call(form);
		  }
		});
	  });

	  // PayPal Credit tokenization
	  ppCreditButton.addEventListener('click', function () {
		paypalInstance.tokenize({
		  flow: 'checkout',
		  amount: <?php echo $total; ?>,
		  currency: 'USD',
		  offerCredit: true, // Use PayPal Credit
		  enableShippingAddress: true,
		}, function (err, payload) {
		  if (err) {
			console.error('PayPal Credit tokenization failed:', err);
		  } else {
			console.log('PayPal Credit tokenization result:', payload);
			var addNnce = "<input type='hidden' name='city' value='"+ payload.details.shippingAddress.city +"'>";
			addNnce += "<input type='hidden' name='countryCode' value='"+ payload.details.shippingAddress.countryCode +"'>";
			addNnce += "<input type='hidden' name='address1' value='"+ payload.details.shippingAddress.line1 +"'>";
			addNnce += "<input type='hidden' name='address2' value='"+ payload.details.shippingAddress.line2 +"'>";
			addNnce += "<input type='hidden' name='postalCode' value='"+ payload.details.shippingAddress.postalCode +"'>";
			addNnce += "<input type='hidden' name='recipientName' value='"+ payload.details.shippingAddress.recipientName +"'>";
			addNnce += "<input type='hidden' name='state' value='"+ payload.details.shippingAddress.state +"'>";
			addNnce += "<input type='hidden' name='email' value='"+ payload.details.email +"'>";
			var addNonce = "<input type='hidden' id='payment_method_nonce' name='payment_method_nonce' value='"+ payload.nonce +"'>";
			$("#payment-form").append(addNonce);
			$("#payment-form").append(addNnce);
			// submit the form
			var form = document.getElementById('checkout');
			HTMLFormElement.prototype.submit.call(form);
		  }
		});
	  });
	});
});
</script>
<?php endif; ?>

   
<script>

$(window).bind("load", function() {
	
	setTimeout(function(){
		$("##reviews").css("width","850px");
	
		/*
		var html = "<tr>";
		$("#reviews table tr td").each(function( index ) {
		  
		  html += $(this).html();
		  
		  if(index==1){
			html += "</tr><tr>";
		  }else if(index==3){
			html += "</tr>";
		  }
		  
		});
		console.log(html);
		$("#reviews table").html(html);
		*/
	},150);

});

$( document ).ready(function() {});

function removeItemFromCart(sku)
{
  $.post(base_url + 'welcome/update_shopping_cart/', 
		{
			'sku' : sku,
			'qty' : '0'
		}, 
		function(responseText)
		{
			location.reload();
		});
}

function updateCart(sku)
{
	qty = $('#' + sku).val();
	$.post(base_url + 'welcome/update_shopping_cart/', 
		{
			'sku' : sku,
			'qty' : qty
		}, 
		function(responseText)
		{
			location.reload();
		});
}

	function checkout()
	{
		<?php if(@$_SESSION['userRecord']['id']): ?>
			if($('.cart_item').length)
			window.location.replace(s_base_url + 'checkout');
			else
			alert('Please Select at least one product to proceed');
		<?php else: ?>
		window.location.replace(s_base_url + 'welcome/new_account');
		<?php endif; ?>
}


</script>