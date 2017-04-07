<?php if((@$validRide) && (@$garageNeeded) && (!empty($_SESSION['activeMachine']))):  
		$img = 'test_image.jpg'; 
	 switch(@$_SESSION['activeMachine']['make']['machinetype_id']):
			case '13':
				$img = 'icon_dirtbike_check.png';
				break;
			default:
				$img = 'icon_dirtbike_check.png';
				break;
		endswitch;
		endif; ?>
<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- MAIN CONTENT -->
		<div class="main_content">
		
			<!-- CONTENT -->
			<div class="item_section">
			<!-- ERROR -->
		   <?php if(validation_errors()): ?>
			<div class="error">
				<h4><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h4>
				<p><?php echo validation_errors(); ?> </p>
			</div>
			<?php endif; ?>
			<!-- END ERROR -->
			
			<!-- ERROR -->
			<div class="error hide">
				<h4><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h4>
				<p id="error_message"></p>
			</div>
			<!-- END ERROR -->
			
			<!-- SUCCESS -->
			<?php if(@$success): ?>
			<div class="success">
				<h4><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h4>
				<p><?php echo $success; ?></p>
			</div>
			<?php endif; ?>
			<!-- END SUCCESS -->	
			
			<p style="float:right;">
			<?php if(!empty($breadcrumbs)): ?>
			
			<?php foreach($breadcrumbs as $name => $value ):  if(($name == 'parent_category_id') | ($name == 'category') || ($name == 'brand') || ($name == 'search')): ?> 
						
							| &nbsp; 
								<?php if(($name == 'category') && (is_array($value))):
									if(@$value['name']): ?>
										<a href="<?php echo base_url('shopping/productlist'.$value['link']); ?>" onclick="setMainSearch(event, 'category', '<?php echo $value['name']; ?>');">Category</a>
									<?php else:
									$i=0;
					  					foreach($value as $id => $cat):  $i++;?>
					  						<a href="<?php echo base_url('shopping/productlist'.$cat['link']); ?>" onclick="setMainSearch(event, 'category', '<?php echo $id; ?>');"><?php echo $cat['name']; ?></a>
					  						<?php if(count($value) == $i ): ?>&nbsp; <?php else: ?> > <?php endif; ?>
					  					<?php endforeach; endif;
								elseif($name == 'search'): 
									$urlstring = 'search_';
									foreach($value as $v)
									{
										$urlstring .= $v.'_';
									}
									$urlstring = substr($urlstring, 0, -1);
								?>
							
									<a href="<?php echo base_url('shopping/productlist'); ?>/<?php echo $urlstring; ?>">Search</a>
								<?php elseif($name == 'brand'): ?>
									<a href="<?php echo base_url('shopping/productlist'.$value['link']); ?>">Brand</a>   &nbsp; 
						<?php endif; endif;  endforeach;  endif; ?>
						</p>
				
				<!-- ITEM-->
				<div style="width:340px; float:left;">
					<div class="item_photo">
						
						<?php if (@$product['images']): ?>
								<img itemprop="image" src="/productimages/<?php echo $product['images'][0]['path']; ?>" id="base_image">
							 <?php else: ?>
									<img src="<?php echo $assets; ?>/images/test_image.jpg" id="base_image">
							<?php endif; ?>	
							<div class="item_photo_bar">
									<div id="image_name"><?php echo $product['images'][0]['description']; ?></div>
									<?php if(@$product['images']): foreach($product['images'] as $key => $image): ?>
										<div class="hide" id="image_name_<?php echo $key; ?>"><?php echo $product['images'][$key]['description']; ?></div>
									<?php endforeach; endif;?>
									<div class="gallery_frame">
										<div class="gallery_inner">
											
												<?php if(@$product['images']): foreach($product['images'] as $key => $image): ?>
												<div class="gallery_image">
												<a href="javascript:void(0);" onclick="changeImage('<?php echo $key; ?>');" ><img src="/productimages/<?php echo $image['path']; ?>" id="small_image_<?php echo $key; ?>"></a>
												</div>
												<?php endforeach; endif;?>
											
										</div>
									</div>
							</div>
					</div>
					
					<div class="product_display_icon">
						<?php if(@$img): ?><img src="<?php echo $assets; ?>/images/<?php echo $img; ?>" height="42" width="42" ></div><div class="clear"><?php endif; ?>
					</div>
					
				</div>
				<div class="item_info">
					<?php echo form_open('shopping/item/'.$product['part_id'], array('class' => 'form_standard', 'id' => 'productDetailForm')); ?>
					<?php echo form_hidden('part_id', $product['part_id']); ?>
					<?php echo form_hidden('display_name', $product['name']); ?>
					<?php echo form_hidden('images', $product['images'][0]); ?>

					<div class="hidden_table">
						<table width="100%" cellpadding="4">
							<tr>
								<td colspan="2">
									<h1><?php echo $product['name']; ?></h1>
								</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td>
												<b>PRICE:</b> &nbsp; &nbsp; 
											</td>
											<td>
												<?php if($product['price']['sale_min'] < $product['price']['retail_min']): ?>
													<strike id="retail_price">$<?php echo $product['price']['retail_min']; if(@$product['price']['sale_max']): echo ' - $' . $product['price']['retail_max'];  endif; ?></strike>
												
											</td>
										</tr>
										<tr>
											<td></td>
											<?php endif; ?>
											<td>
												<div id="price" style="display:inline;">
													$<?php echo $product['price']['sale_min']; if(@$product['price']['sale_max']): echo ' - $' . $product['price']['sale_max'];  endif; ?>
												</div>
												<div class="discount" style="display:inline; padding-left: 5px;">
										<?php if(@$product['price']['percentage']):?>
										You save $<?php echo ($product['price']['retail_min'] - $product['price']['sale_min']); if(@$product['price']['sale_max']): echo ' - $' . ($product['price']['retail_max'] - $product['price']['sale_max']);  endif;?> (<?php echo number_format($product['price']['percentage'], 0); ?>%) 
										<?php  endif; ?>
									</div>
											</td>
										</tr>
									</table>
									
									
								</td>
							</tr>
							<tr>
									<td>
										<div class="reviews">
											<?php if(@$product['reviews']): 
												$remainder = floor(5 - $product['reviews']['average']);
												for($i=0; $i < $product['reviews']['average']; $i++): ?> <i class="fa fa-star" style="color:#FFD700"></i><?php endfor;  
												if($remainder > 0)
												for($i=0; $i < $remainder; $i++): ?> <i class="fa fa-star" style="color:#b6b6b6"></i><?php endfor;  ?>
												(<?php echo $product['reviews']['qty']; ?>)
											<?php endif; ?>
										</div>	
									</td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td>
												<b>
												<?php if(@$questions):  $currentQuestion = ''; foreach($questions as $key => $quest):  	// Building Question Options
												if($quest['partquestion_id'] == $currentQuestion): $answers[$quest['partnumber']] = $quest['answer'];?>
													
												<?php 
												// First Time Through
												elseif($currentQuestion == ''): 
												
												echo $quest['question']; ?> : </b></td><td><b><?php $answers = array('0' => 'Select an option', $quest['partnumber'] => $quest['answer']); ?>
												
												<?php 
												// End old question and create New Question.  New Question will never be the first question.
												else: ?>
													<div class="stock hide" id="out_of_stock_<?php echo $currentQuestion; ?>"<img src="<?php echo $assets; ?>/images/Ambox_warning_yellow.png" width="30px;"/>&nbsp;OUT OF STOCK - PLEASE CALL TO ORDER</div>
									<div class="stock hide"  id="in_stock_<?php echo $currentQuestion; ?>"><div style="color:#093; display:inline;"><i class="fa fa-check"></i>&nbsp;In Stock</div></div>
												<?php
													echo form_dropdown('question[]', $answers, @$_SESSION['cart'][$product['part_id']][$quest['partquestion_id']], 'style="height:25px;", class="question '.$currentQuestion.'", onchange="updatePrice('.$currentQuestion.');"');?></b> </td></tr><tr><td>
													
												
													<b>
													<?php echo $quest['question']; ?> : </b></td><td><b><?php $answers = array('0' => 'Select an option', $quest['partnumber'] => $quest['answer']); ?>
															
												<?php endif; $currentQuestion = $quest['partquestion_id']; endforeach;  ?> 
												<div class="stock hide" id="out_of_stock_<?php echo $currentQuestion; ?>"><img src="<?php echo $assets; ?>/images/Ambox_warning_yellow.png" width="30px;"/>&nbsp;OUT OF STOCK - PLEASE CALL TO ORDER</div>
									<div class="stock hide"  id="in_stock_<?php echo $currentQuestion; ?>"><div style="color:#093; display:inline;"><i class="fa fa-check"></i>&nbsp;In Stock</div> <div class="hide" id="low_stock_<?php echo $currentQuestion; ?>" style="display:inline;">- ONLY <div id="stock_qty_<?php echo $currentQuestion; ?>" style="display:inline;">1</div> REMANING</div></div>
												<?php 
													// Last time Through
													echo form_dropdown('question[]', $answers, @$_SESSION['cart'][$product['part_id']][$quest['partquestion_id']], 'style="height:25px;", class="question '.$currentQuestion.'", onchange="updatePrice('.$currentQuestion.');" ');?>
													<?php else: ?>
														<div class="stock hide" id="out_of_stock_<?php echo $product['part_id']; ?>"><img src="<?php echo $assets; ?>/images/Ambox_warning_yellow.png" width="30px;"/>&nbsp;OUT OF STOCK - PLEASE CALL TO ORDER</div>
									<div class="stock hide"  id="in_stock_<?php echo $product['part_id']; ?>"><div style="color:#093; display:inline;"><i class="fa fa-check"></i>&nbsp;In Stock.</div><div class="stock hide" id="low_stock_<?php echo $product['part_id']; ?>" style="display:inline;"> - ONLY <div id="stock_qty_<?php echo $product['part_id']; ?>" style="display:inline;">1</div> REMANING</div></div>
												<?php endif;?>
												</b>
									
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td><b>QTY:</b> &nbsp; &nbsp;  &nbsp; &nbsp; 
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
					<?php echo form_close(); ?>
					
					<br>
					<a href="javascript:void(0);" onclick="submitCart();" class="button" id="submit_button">Add to Cart</a>
					<div style="padding-top:6px;"><a href="javascript:void(0);" onclick="submitWishlist();"><i class="fa fa-magic"></i>Add to Wishlist</a></div>
					<div class="clear"></div>
					
					<div class="clear"></div>
					<br>
					<div class="share">
						<p><b>Share This!</b></p>
						<h2>
							<a href="http://www.facebook.com/share.php?u=<?php echo base_url('shopping/item/'.$product['part_id']); ?>" target="_blank" style="color:#369;"><i class="fa fa-facebook-square"></i></a>
							<a href="https://twitter.com/share" data-lang="en" style="color:#4099FF" target="_blank"><i class="fa fa-twitter-square"></i></a>
							<a href="mailto:?subject=Check out this Part&amp;body=Check out this site <?php echo base_url('shopping/item/'.$product['part_id']); ?>." title="Share by Email" style="color:#F00;"><i class="fa fa-envelope-square"></i></a>
						</h2>
					</div>
					<div class="clear"></div>
					
					
					
				</div>
				<!-- END ITEM -->
				
				<div class="clear"></div>
				<br>
				
				<!-- TABS -->
				<div class="tab">
					<ul>
						<?php if(@$product['description']): ?><li><a href="javascript:void(0);" onclick="changeTabs('description')" id="description" class="active"><i class="fa fa-bars"></i>&nbsp;Description</a></li><?php endif; ?>
						<li><a href="javascript:void(0);" onclick="changeTabs('reviews')" id="reviews"><i class="fa fa-star"></i>&nbsp;Reviews</a></li>
						<?php if(@$this->_mainData['garageNeeded']): ?><li><a href="javascript:void(0);" onclick="changeTabs('fitment')" id="fitment"><i class="fa fa-gears"></i>&nbsp;Fitment</a></li><?php endif; ?>
						<div class="clear"></div>
					</ul>
				</div>
				<!-- END TABS -->
				
				<!-- TAB CONTENT -->
				<div class="tab_content">
					<p><div id="tab_stuff"><?php echo @$product['description']; ?></div></p>
				</div>
				<!-- END TAB CONTENT -->
				

			</div>
			
	
			
			<?php echo @$recentlyViewedBand; ?>	
		
		</div>
		<!-- END CONTENT -->
		
			<?php echo @$sidebar; ?>
		</div>
		<!-- END MAIN CONTENT -->

	</div>
	<script>
	
	
$( document ).ready(function() {

		<?php if(empty($questions)): ?>
			getStock('<?php echo $product['part_id']; ?>');
		<?php endif; ?>
		
		$('.gallery_inner').width('<?php echo @$width; ?>px');
	});
	
		<?php if(!@$product['description']): ?>
			changeTabs('reviews');
		<?php endif; ?>
		
		function changeImage(key)
		{
			small = $('#small_image_' + key).attr("src");
			 $('#base_image').attr("src", small);
			 $('#image_name').html($('#image_name_' + key).html());
 		}
		
		function changeTabs(tab)
		{
			if ($('#description').length)
			{
				$('#description').removeClass('active');
			}
			if ($('#fitment').length)
			{
				$('#fitment').removeClass('active');
			}
			$('#reviews').removeClass('active');
			$('#' + tab).addClass('active');
			
			$.post(base_url + 'ajax/getActiveSection/',
			{ 
			 'activeSection' : tab,
			 'part_id' : '<?php echo $product['part_id']; ?>',
			 'ajax' : true
			},
			function(displayblock)
			{
				$('#tab_stuff').html(displayblock);
			});
		}
		
		function getStock(partId)
		{
			 $.post(base_url + 'ajax/getStockByPartId/',
			{ 
			 'partId' : partId,
			 'ajax' : true
			},
			function(partRec)
			{
				var partObj = jQuery.parseJSON(partRec );
				$('#in_stock_'+partId).hide();
				$('#out_of_stock_'+partId).hide();
				$("#submit_button").attr("onclick","submitCart()");
				if(partObj.quantity_available > 0)
				{
					$('#in_stock_'+partId).show();
					$('#low_stock_'+partId).hide();
					if(partObj.quantity_available < 6)
					{
						$('#low_stock_'+partId).show();
						$('#in_stock_'+partId).show();
						$('#stock_qty_'+partId).html(partObj.quantity_available);
					}
				}
				else
				{
					$('#out_of_stock_'+partId).show();
					$("#submit_button").attr("onclick","outOfStockWarning()");
				}
			});
		}
		
		function outOfStockWarning()
		{
			alert('OUT OF STOCK - PLEASE CALL TO ORDER');
		}
	
		function updatePrice(questionId)
		{
			$('#price').html(0.00);
			
			$(".question").each(function() 
			{
			    if($(this).val() != 0)
			    {
				    $.post(base_url + 'ajax/getPriceByPartNumber/',
					{ 
					 'partnumber' : $(this).val(),
					 'ajax' : true
					},
					function(partRec)
					{
						var partObj = jQuery.parseJSON(partRec );
						var currentPrice = $('#price').html(); 
						currentPrice = currentPrice.replace("$", "");
						totalprice = parseFloat(currentPrice) +  parseFloat(partObj.sale);
						$('#price').html('$' + parseFloat(totalprice).toFixed(2));
						$('#in_stock_'+questionId).hide();
						$('#out_of_stock_'+questionId).hide();
						$("#submit_button").attr("onclick","submitCart()");
						if(partObj.quantity_available > 0)
						{
							
							$('#in_stock_'+questionId).show();
							$('#low_stock_'+questionId).hide();
							if(partObj.quantity_available < 6)
							{
								$('#low_stock_'+questionId).show();
								$('#in_stock_'+questionId).show();
								$('#stock_qty_'+questionId).html(partObj.quantity_available);
							}
						}
						else
						{
							$('#out_of_stock_'+questionId).show();
							$("#submit_button").attr("onclick","outOfStockWarning()");
						}
					});
			    }
			});
		}
		
		function submitCart()
		{		
			var proceed;
			if ($(".question")[0])
			{
				$(".question").each(function() 
				{
					// For Combos, check to see if any of them are out of stock before processing
					questClassList = $(this).attr('class').split(/\s+/);
					console.log(questClassList[1]);
					if(!$('#out_of_stock_'+questClassList[1]).is(":hidden"))
					{
						proceed = 'error';
						 $('.error').show();
						$('#error_message').text('One of the items you selected is OUT OF STOCK - PLEASE CALL TO ORDER.');
						return false;
					}
					// Make sure all necessary questions are answered before processing
				    if($(this).val() == 0)
				    {
				    	proceed = 'error';
					    $('.error').show();
						$('#error_message').text('Please select a dropdown option for this part.');
						return false;
				    }
				});
			}
			if(proceed != 'error' )
			{
			<?php if($garageNeeded):
				if($validRide): ?>
					if($.isNumeric($('#qty').val()))
					{
						<?php if(@$_SESSION['userRecord']['admin'] && @$_SESSION['OrderProductSearch']): ?>
						window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/<?php echo $validMachines[0]['partnumber']; ?>');
						<?php else: ?>
						$('#productDetailForm').append('<input type="hidden" name="partnumber" value="<?php echo $validMachines[0]['partnumber']; ?>" />');
					  	$('#productDetailForm').append('<input type="hidden" name="price" value="'+$('#price').html()+'" />');
					  	$('#productDetailForm').append('<input type="hidden" name="type" value="cart" />');
					  	$("#productDetailForm").submit();	
					  	<?php endif; ?>
					}
					else
					{
						$('.error').show();
						$('#error_message').text('Please enter a valid quantity.');
					}

				<?php elseif(@$_SESSION['garage']): // No Active Ride ?>
					$('.error').show();
					$('#error_message').text('Your machine does not match this item.  Please change your active machine above to add this item to cart.');
				<?php else: // No Valid Ride ?>
					$('.error').show();
					$('#error_message').text('You currently do not have a machine selected.  Please create an active machine by using tool in the purple bar above.');

				<?php endif; ?>
			<?php else: // No Ride Needed ?>
					if($.isNumeric($('#qty').val()))
					{
						<?php if(@$_SESSION['userRecord']['admin'] && @$_SESSION['OrderProductSearch']): ?>
						window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/<?php echo @$product['partnumber']; ?>');
						<?php else: ?>
						$('#productDetailForm').append('<input type="hidden" name="partnumber" value="<?php echo @$product['partnumber']; ?>" />');
					  	$('#productDetailForm').append('<input type="hidden" name="price" value="'+$('#price').html()+'" />');
					  	$('#productDetailForm').append('<input type="hidden" name="type" value="cart" />');
					  	$("#productDetailForm").submit();	
					  	<?php endif; ?>					
					 }
					 else
					 {
						$('.error').show();
						$('#error_message').text('Please enter a valid quantity.');
					 }
				
				<?php endif; ?>
			}
	
				return false;
		}
		
		function submitWishlist()
		{
			var proceed;
			if ($(".question")[0])
			{
				$(".question").each(function() 
				{
				    if($(this).val() == 0)
				    {
				    	proceed = 'error';
					    $('.error').show();
						$('#error_message').text('Please select a dropdown option for this part.');
						return false;
				    }
				});
			}
			if(proceed != 'error' )
			{		
<?php if($garageNeeded):
				if($validRide): ?>
					if($.isNumeric($('#qty').val()))
					{
						
						$('#productDetailForm').append('<input type="hidden" name="partnumber" value="<?php echo $validMachines[0]['partnumber']; ?>" />');
					  	$('#productDetailForm').append('<input type="hidden" name="price" value="'+$('#price').html()+'" />');
					  	$('#productDetailForm').append('<input type="hidden" name="type" value="wishlist" />');
					  	$("#productDetailForm").submit();	
					}
					else
					{
						$('.error').show();
						$('#error_message').text('Please enter a valid quantity.');
					}

				<?php elseif(@$_SESSION['garage']): // No Active Ride ?>
					$('.error').show();
					$('#error_message').text('Your machine does not match this item.  Please change your active machine above to add this item to cart.');
				<?php else: // No Valid Ride ?>
					$('.error').show();
					$('#error_message').text('You currently do not have a machine selected.  Please create an active machine by using tool in the purple bar above.');

				<?php endif; ?>
			<?php else: // No Ride Needed ?>
					if($.isNumeric($('#qty').val()))
					{
						$('#productDetailForm').append('<input type="hidden" name="partnumber" value="<?php echo @$product['partnumber']; ?>" />');
					  	$('#productDetailForm').append('<input type="hidden" name="price" value="'+$('#price').html()+'" />');
					  	$('#productDetailForm').append('<input type="hidden" name="type" value="wishlist" />');
					  	$("#productDetailForm").submit();						
					 }
					 else
					 {
						$('.error').show();
						$('#error_message').text('Please enter a valid quantity.');
					 }
				
				<?php endif; ?>
				}
				
				return false;
		}
		
	</script>