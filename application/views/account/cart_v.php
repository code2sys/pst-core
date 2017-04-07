<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		<!-- CART -->
		<div class="main_content_full">

			<h1>Shopping Cart</h1>
			<h3 style="margin:0;"><i class="fa fa-home"></i> Items In your Cart (<span id="shopping_count"><?php echo @$_SESSION['cart']['qty']; ?></span>)</h3>
			<p>Please review your selected items</p>
			<!-- CART WRAP -->
			<div class="cart_wrap">
			<?php if(@$cartProducts): $i = 0; $total = 0; foreach($cartProducts as $key => $product): if(($key !== 'qty') && ($key !== 'tax') && ($key !== 'transAmount') && (strpos($key, 'shipping') === FALSE) ):   echo @$product['images']['description']; ?>	
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
						<h2><?php $product['display_name'] = str_replace('|||', '<br /><span class="smaller_font">', $product['display_name']);
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
					<a href="<?php echo base_url('/shopping/productlist/'); ?>" class="button"><i class="fa fa-shopping-cart"></i> Continue Shopping</a>
					<a href="javascript:void(0);" onclick="checkout('<?php echo @$_SESSION['cart']['qty']; ?>');" class="button_purple" style="margin-right:0px;"><i class="fa fa-arrow-cricle-right"></i> Check Out</a>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<!-- END CART PROMO & TOTAL -->
			</form>
			</div>
			<!-- END CART WRAP -->
			
			
			<!-- CHECK OUT -->
		</div>
	</div>		
	
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
	
<script>
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