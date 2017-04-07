<script> var shopping_cart_count = 0;</script><?php $qty = 0; $total = 0;?>

		<!-- SIDEBAR -->
		<div class="sidebar">
			<h3>Your Cart </h3>
			<p style="font-size:12px;">Change the quantity to update your cart</p>
			
			<!-- SHOPPING CART -->
			<div class="side_sect">
			
			<?php echo form_open('', array('class' => 'form_standard')); ?>		
				<!-- ITEM -->
				<?php if(@$cartProducts): $i = 0; foreach($cartProducts as $key => $product): if($key !== 'qty'): ?>
				<div class="tabular_data cartProducts">
					<table width="100%">
						<tr class="head_row">
							<td colspan="2"><a href=""><b><?php echo @$product['name']; ?></b></a></td>
						</tr>
						<tr>
							<td style="width:60px;"><b>Qty:</b></td>
							<td>
  							 <?php if(!@$hideButton && (strpos($key, '_FREE') === FALSE)): ?>
        				  <?php echo form_input(array('name' => 'qty', 
          			                              'value' => $product['qty'], 
          			                              'maxlength' => 250, 
          			                              'placeholder' => '0',
          			                              'class' => 'text year',
          			                              'id' => @$product['sku'].'_cart',
          			                              'onChange' => 'updateCart(\''.@$product['sku'].'_cart\', false); updateCount(0);'
          			                              )); ?>
        				  <?php else: echo @$product['qty']; endif; ?>
							</td>
						</tr>
						<tr>
							<td><b>Price:</b></td>
							<td>$<?php echo @$product['finalPrice'] ?></td>
						</tr>
						<tr>
							<td colspan="2"><a href="javascript:void(0);" onclick="removeItemFromCart('<?php echo @$product['sku']; ?>');"><u>Remove Item</u></a></td>
						</tr>
					</table>
					<script>shopping_cart_count += <?php echo (@$product['qty']) ? @$product['qty'] : 0; 
            $qty += @$product['qty']; 
            $total +=  str_replace( ',', '', @$product['finalPrice'] ); ?>; 
          </script>

				</div>
				<?php endif; endforeach; endif; ?>
				<!-- END ITEM -->
				
				<!-- COUPON -->
				<?php if(!@$hideCoupon): ?>
				<div class="tabular_data">
					<table width="100%">
						<tr class="head_row">
							<td colspan="2"><a href=""><b> Coupon Code : </b></a></td>
						</tr>
            <tr>
      				<td colspan="2" style="border-bottom:1px #666 solid;">
      				
      				 
        				<?php echo form_input(array('name' => 'couponCode', 
        			                              'value' => '', 
        			                              'maxlength' => 250,
        			                              'id' => 'coupon_cart',
        			                              'class' => 'text medium',
        			                              'onChange' => 'updateCart(\'coupon_cart\', false); updateCount(0);',
        			                              'style' => 'width:120px')); ?>
      				</td>
      			</tr>
					</table>
				</div>
        <?php endif; ?>
				<!-- END COUPON -->
				
				<div class="total_side">
				<h3><a href="javascript:void(0);" onclick="checkout('<?php echo @$_SESSION['cart']['qty']; ?>');">Total: $<?php echo number_format($total, 2, '.', ','); ?> Check Out</a></h3>
				
				</div>
				<script> 
		  updateCount(0);
		$('.shopping_count').html('<em>' + <?php echo $qty; ?> + '</em>');
		 </script>
				</form>
			</div>
			<!-- END SHOPPING CART -->
			
		</div>
		<!-- END SIDEBAR -->

      