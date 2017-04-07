			<div class="tabular_data">
				<form action="" method="post" id="form_example" class="form_standard">
				<table cellpadding="0" style="width:100%;">
					<tr class="head_row">
						<td colspan="2"><b>Product</b></td>
						<td><b>Price</b></td>
						<td><b>Quantity</b></td>
					</tr>


						<?php if(@$products): $i=0; foreach($products as $product): 
						      @$product['image'] = $product['image'] ? $product['image'] : $image; 
						      
						      if(@$cartProducts[$product['sku']]): $product['qty'] = $cartProducts[$product['sku']]['qty']; endif; // Populate shopping cart values.?>
									<tr>
										<td style="width:60px;">
											<div class="popup-gallery">
						    		    <?php if(@$product['image']): ?>
						    		    <a href="<?php echo base_url($this->config->item('media')) .'/'. $product['image']; ?>" title=" <?php echo $product['display_name']; ?>">
						    		      <img src="<?php echo base_url($this->config->item('media')) .'/'. $product['image']; ?>" height="50" border="0">
						    		    </a>
						    		    <?php endif; ?>
											</div>
										</td>
										<td>
											<a href="#">
												<b>
							    		    <div class="desc hide"> <?php if(@$product['description']): echo $product['description']; endif; ?></div>
							    		    <?php if(@$product['link']): ?>
							    		      <a href="<?php echo $product['link']; ?>"><?php echo $product['display_name']; ?></a>
							    		    <?php else: ?>
							    		      <?php echo $product['display_name']; ?>
							    		    <?php endif; ?>
												</b>
											</a>
										</td>
										<td>
											<b>
							    		  <?php if($product['onSale']): ?>
							    		    <strike><?php echo $product['price']; ?></strike><div style="color:red"><?php echo $product['salePrice']; ?></div>
							    		  <?php else: ?>
							    		    <?php echo $product['price']; ?>
							    		  <?php endif; ?>								
											</b>
										</td>
										<td>
					    		    <?php echo form_input(array('name' => 'qty', 
					      			                             'value' => @$product['qty'], 
					      			                             'maxlength' => 250, 
					      			                             'placeholder' => '0',
					      			                             'class' => 'text medium update',
					      			                             'id' => $product['sku'],
					      			                             'style' => 'width:25px')); ?>							
										</td>
									</tr>						
		    		<?php endforeach; endif; ?>

					
				</table>
				</form>
			</div>
			<!-- END ITEMS -->

<script>
    $('.update').keyup(function(){
      updateCart(this.id, false);
      updateCount(0);
      });

</script>