<div class="tabular_data">
				
					<table cellpadding="3" style="width:100%;">
						<tr class="head_row">
							<td><b>#</b></td>
							<td><b>SKU</b></td>
							<td><b>Product</b></td>
							<td><b>W/Sale</b></td>
							<td><b>Retail</b></td>
							<td><b>WS Sale</b></td>
							<td><b>Ret Sale</b></td>
							<td><b>Weight</b></td>
							<td><b>Desc</b></td>
						</tr>
						<?php if(@$products): $i=0; foreach($products as $key => $product): ?>
						<tr<?php echo ($i%2) ? ' class="row_dark"' : ' class="row_light"' ?>>
							<td>
							  <?php echo form_hidden('product_id[]', $product['id']); // used in javascript for serialization processing ?>
							  <?php echo form_input(array('name' => 'display_page[]', 
				                              'value' => @$product['display_page'], 
				                              'class' => 'text year', 
				                              'placeholder' => "0",
				                              'style' => 'height:16px')); ?>
				      </td>
							<td> <?php echo $product['sku']; ?>
                   <?php echo form_hidden('sku[]', @$product['sku']); ?>
              </td>
							<td>
							  <?php echo form_input(array('name' => 'display_name[]', 
      				                              'value' => @$product['display_name'], 
      				                              'class' => 'text large', 
      				                              'placeholder' => 'Product Name',
      				                              'style' => 'height:16px;width:92%;')); ?>
							</td>
							<td>
							  <?php echo form_input(array('name' => 'wholesale[]', 
				                              'value' => @$product['wholesale'], 
				                              'class' => 'text year', 
				                              'placeholder' => '$00.00',
				                              'style' => 'height:16px')); ?>
							</td>
							<td>
							  <?php echo form_input(array('name' => 'retail[]', 
				                              'value' => @$product['retail'], 
				                              'class' => 'text year', 
				                              'placeholder' => '$00.00',
				                              'style' => 'height:16px')); ?>
						  </td>
							<td>
							  <?php echo form_input(array('name' => 'saleWs[]', 
				                              'value' => @$product['saleWs'], 
				                              'class' => 'text year', 
				                              'placeholder' => '$00.00',
				                              'style' => 'height:16px')); ?>
						  </td>
							<td>
							  <?php echo form_input(array('name' => 'sale[]', 
				                              'value' => @$product['sale'], 
				                              'class' => 'text year',
				                              'placeholder' => '$00.00', 
				                              'style' => 'height:16px')); ?>
						  </td>
							<td>
							  <?php echo form_input(array('name' => 'weight[]', 
				                              'value' => @$product['weight'], 
				                              'class' => 'text year',
				                              'placeholder' => '$00.00', 
				                              'style' => 'height:16px')); ?>
						  </td>
							<td>
							  <?php echo form_input(array('name' => 'description[]', 
				                              'value' => @$product['description'], 
				                              'class' => 'text small', 
				                              'placeholder' => 'Description',
				                              'style' => 'height:16px;width:92%;')); ?>
						  </td>
						  
						</tr>
						<tr<?php echo ($i%2) ? ' class="row_dark"' : ' class="row_light"' ?>>
						<td>Coupon?:<br /> <?php echo form_checkbox('applyCoupon[]', 1, @$product['applyCoupon'], 'class="checkbox"'); ?></td>
							<td style="text-align:right;"><b>Link:</b></td>
							<td>
							  <?php echo form_input(array('name' => 'link[]', 
				                              'value' => @$product['link'], 
				                              'class' => 'text large', 
				                              'placeholder' => 'Product Link',
				                              'style' => 'height:16px;width:92%;')); ?>
						  </td>
							
							<td colspan="2">
								<?php echo form_dropdown('category[]', $categories, @$product['code'], 'style="width:125px";'); ?>
							</td>
							<td><b>Taxable</b> <?php echo form_checkbox('taxable[]', 1, @$product['taxable'], 'class="checkbox"'); ?></td>
							<td><b>On Sale</b> <?php echo form_checkbox('onSale[]', 1, @$product['onSale'], 'class="checkbox"'); ?></td>
							<td><b>Active</b> <?php echo form_checkbox('active[]', 1, @$product['active'], 'class="checkbox"'); ?></td>
							<td>
							  <?php if(@$product['image']): ?>
                	<a href="javascript:void(0);" onclick="removeImage('product', '<?php echo $product['sku']; ?>');">
                	  <img src="<?php echo $assets; ?>/images/icon_image.png" border="0" align="middle"> <u>Remove Image</u>
                  </a>
                <?php else: ?>
                	<a href="javascript:void(0);" onclick="addImage('product', '<?php echo $product['sku']; ?>');">
                	  <img src="<?php echo $assets; ?>/images/icon_image.png" border="0" align="middle"> <u>Add Image</u>
                  </a>
              	<?php endif; ?>
						  </td>
						</tr>
						<?php $i++; endforeach; endif; ?>						
					</table>	
				</div>
