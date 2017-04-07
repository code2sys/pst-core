
			<!-- CART -->
			<div class="side_header">
				<h1>Shopping Cart</h1>
			</div>
			<?php if(@$cartProducts): $i = 0; $total = 0;?>
			<div class="side_section">
			<?php foreach($cartProducts as $key => $product): if(($key !== 'qty') && ($key !== 'transAmount') &&($key !== 'tax') && (strpos($key, 'shipping') === FALSE) ): ?>
				<div class="side_item">
					<p><b><?php echo @$product['display_name']; ?></b>  
					<br />$<?php echo $product['finalPrice']; ?> | <a href="javascript:void(0);" onclick="removeItemFromCart('<?php echo @$product['sku']; ?>');"><u>Remove Item</u></a></p>
					<div class="clear"></div>
				</div>
			<?php endif; endforeach; ?>
			</div>
			<?php  endif; ?>
			<!-- END CART -->


<script>
	function removeItemFromCart(sku)
	{
	  $.post(base_url + 'welcome/update_shopping_cart/', 
	  {'sku' : sku,
	   'qty' : '0'
	  }, 
			function(responseText)
			{
			  location.reload();
			});
	}

</script>