<?php  setlocale(LC_MONETARY, 'en_US');  ?>

	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		
		<!-- CHECK OUT -->
		<div class="main_content_full">
			<h1 style="float:left;margin:5px 0 0;">
					<i class="fa fa-truck"></i> Shipping Method
				</h1>
				<div class="clear"></div>
			<h3>Please Select Your Shipping Method</h3>
			<p>Shipping is required.</p>
			<br>

			<?php echo form_open('checkout/select_shipping', array('class' => 'form_standard', 'id' => 'shipment_type')); ?>
			
			
		  <?php echo form_dropdown('shipping', $postalOptDD, '', 'id="select_shipping" onChange="updateShipping()"'); ?>

      <div class="clear"></div>
			<br>
			<div class="cart_table">
				<table width="100%" cellpadding="4">
					<tr>
					  <td><b>Qty</b></td>
						<td><b>Products</b></td>
						<td><b>Weight</b></td>
						<td><b>Price</b></td>
					</tr>
					<?php if(@$cartProducts): $i = 0; foreach($cartProducts as $key => $product): if(( is_array($product) ) && ($product['display_name'] !== 'Shipping')): ?>
					<tr>
					  <td><?php echo @$product['qty']; ?></td>
						<td><?php echo @$product['display_name']; ?></td>
						<td><?php echo @$product['weight']; ?></td>
						<td><?php echo @$product['finalPrice']; ?></td>
					</tr>
					 <?php $total += @$product['finalPrice']; endif; endforeach; endif; ?>
					<tr>
					  <td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr style="background:#faf9e8;">
					  <td></td>
						<td><b>Shipping:</b></td>
						<td><?php $totalWeight; ?></td>
						<td><div id="shipping_total">$00.00</div></td>
					</tr>
					
					<tr style="background:#eff7ee;">
						<td></td>
						<td style="height:50px;"><h3>Your Total:</h3></td>
						<td></td>
						<td><h3><div id="order_total">$<?php echo $_SESSION['cart']['transAmount'] = number_format($total, 2); ?></div></h3></td>
					</tr>
				</table>
			</div>
			<input type="hidden" name="shippingValue" id="submit_value" value="">
			<input type="submit" value="Next >" class="input_button">
			
			</form>
			
			<div class="clear"></div>
		</div>
		<!-- END CHECK OUT -->
		
		
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
	


</div>
<!-- END WRAPPER ==========================================================================-->
<?php if($_SESSION['postalOptions']): foreach($_SESSION['postalOptions'] as $code => $value): ?>
<div id="<?php echo $code; ?>" class="hide"><?php echo $value; ?></div> 
<?php endforeach; endif; ?>
<script>
disableNext();
  function disableNext()
  {
    shipping_code = $('#select_shipping').val();
    if(shipping_code == '0.00')
    {
      $('input[type=submit]').attr('disabled', 'disabled');
    }
    else
    {
      $('input[type=submit]').removeAttr("disabled");
    }
  }

  function updateShipping()
  {
    shipping_code = $('#select_shipping').val();
    shipping_value = $('#' + shipping_code).html();
    $('#shipping_total').html('$' + shipping_value);
    total = parseFloat(<?php echo $total; ?>) + parseFloat(shipping_value);
    $('#order_total').html('$' + parseFloat(total).toFixed(2)); 
    $('#submit_value').val(parseFloat(shipping_value).toFixed(2)); 
    disableNext();
  }
  
  $('#shipment_type').submit(function(){
    $('input[type=submit]', this).attr('disabled', 'disabled');
  });

</script>