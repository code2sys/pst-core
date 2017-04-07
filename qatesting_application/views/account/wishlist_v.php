
	
	<?php $img = 'test_image.jpg'; 
	if(@$_SESSION['garage'] ): foreach($_SESSION['garage'] as $label => $rideRecs): 
		 switch(@$rideRecs['make']['machinetype_id']):
			case '13':
				$img = 'icon_dirtbike.png';
				break;
			default:
				$img = 'test_image.jpg';
				break;
		endswitch; endforeach; endif;
	?>
			<!-- CONTENT -->
			<div class="content_section">				
				<!-- FEATURED PRODUCTS -->
				<div class="section_head">
					<h1>Wish List</h1>
					<?php if(!empty($breadcrumbs)): ?>
						<p style="float:right;">
							| &nbsp;<?php foreach($breadcrumbs as $name => $value ):  if(($name != 'category') && ($name != 'brand')): ?>  
								<a href="<?php echo base_url('shopping/productlist'); ?>/
												<?php  foreach($breadcrumbs as $filter => $sortValue): 
																if(($filter != $name) && ($filter != 'category') && ($filter != 'brand')): // Do not put the ugliness of the long name in the URL
																	if($sortValue == @$breadcrumbs['category']):?>category
																	<?php elseif($sortValue == @$breadcrumbs['brand']): ?>brand
																	<?php  else: echo $filter; 
																	endif;  ?>/
														<?php if($filter == 'search'): 
																		echo 1; 
																	else: 
																		echo $sortValue;  
																	endif; // Do not put search results in URL.
																endif; 
															endforeach;  ?>">x	
								</a> 
									<?php echo ucwords(preg_replace('/([A-Z])/',"\n".'$1',$name)); ?> &nbsp; |  &nbsp; 
							<?php endif;  endforeach; ?>
						</p>
						<?php endif; ?>
					<?php if(@$band['page']): ?>
						<a href="<?php echo base_url($band['page']); ?>/" class="button" style="float:right;">View All</a>
					<?php endif; ?>
					<div class="clear"></div>
				</div>
				<?php $i = 0;  if(@$band['products']): foreach($band['products'] as $product): $i++; ?>
				
				<div class="item_photo" style="height:250px">
				<?php if(@$product['images'][0]['path']): ?>
					<?php if (@$product['images']): ?>
						<?php if(@$product['images'][1]): ?>
						<img src="<?php echo base_url('productimages/'. $product['images'][0]['path']); ?>" onmouseover="this.src='<?php echo base_url('productimages/'.  $product['images'][1]['path']); ?>'" onmouseout="this.src='<?php echo base_url('productimages/'. $product['images'][0]['path']); ?>'"  id="base_image">
						<?php else: ?>
							<img src="<?php echo base_url('productimages/'. $product['images'][0]['path']); ?>" id="base_image">
						<?php endif;
						 else: ?>
								<img src="<?php echo $assets; ?>/images/test_image.jpg" id="base_image">
						<?php endif; 
						 else: ?>
						 <img src="<?php echo $assets; ?>/images/test_image.jpg" id="base_image">
						<?php endif; ?>
				</div>
				
				
				
				
				
				<div class="item_info">
					<?php echo form_open('shopping/item/'.$product['part_id'], array('class' => 'form_standard', 'id' => 'productDetailForm')); ?>
					<?php echo form_hidden('part_id', $product['part_id']); ?>
					<?php echo form_hidden('display_name', $product['name']); ?>
					<?php echo form_hidden('type', 'cart'); ?>
					<?php echo form_hidden('price', '$'.$product['price']); ?>
					<?php echo form_hidden('partnumber', $product['partnumber']); ?>
					<?php echo form_hidden('images', $product['images'][0]); ?>

					<div class="hidden_table">
						<table width="100%" cellpadding="4">
							<tr>
								<td colspan="2">
									<h1><?php 
									$product['name'] = str_replace('|||', '<br /><span class="smaller_font">', $product['name']);
									$product['name'] = str_replace('||', '</span>', $product['name']);
									echo $product['name'];
									?></h1>
								</td>
							</tr>
							<tr>
								<td style="width:120px;"><b>PRICE:</b></td>
								<td><div id="price" class="price">$<?php echo $product['price']; ?></div></td>
							</tr>
							<tr>
								<td><b>QTY:</b></td>
								<td>
									<?php echo form_input(array('name' => 'qty', 
																			            'value' => 1, 
																			            'maxlength' => 250, 
																			            'class' => 'text mini', 
																			            'placeholder' => '0',
																			            'id' => 'qty')); 
				           ?>
								</td>
							</tr>
						</table>
					</div>
					
					
					<?php echo @$product['description']; ?> 
					<br>
					<input type="submit" value="Add to Cart" class="button">
					<a href="<?php echo base_url('shopping/remove_wish_item/'.$product['wishlistpart_id']); ?>">Remove Item</a>
					<?php echo form_close(); ?>
					<div class="clear"></div>
					
					<div class="clear"></div>
					<br>

					
					
					
				</div>
				<!-- END ITEM -->
				<div class="divider"></div>
				<?php endforeach; else: ?>
				You currently do not have products in your wish list.
				<?php endif; ?>
				<div class="clear"></div>
				<!-- END FEATURED PRODUCTS -->
			
			</div>
			<!-- END CONTENT -->
