<?php if(@$band['products']): ?>
	
	<?php $img = 'test_image.jpg'; 
	if(@$_SESSION['garage'] ): foreach($_SESSION['garage'] as $label => $rideRecs): 
		 switch(@$rideRecs['make']['machinetype_id']):
			case '13':
				$img = 'icon_dirtbike_check.png';
				break;
			default:
				$img = 'icon_dirtbike_check.png';
				break;
		endswitch; endforeach; endif;
	
	if (!function_exists('tag_creating')) {	
		function tag_creating($url) 
		{
			$url = str_replace(array(' - ', ' '), '-', $url);			
			$url = preg_replace('~[^\\pL0-9_-]+~u', '', $url);
			$url = trim($url, "-");
			$url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
			$url = strtolower($url);
			$url = preg_replace('~[^-a-z0-9_-]+~', '', $url);
		   return $url;
		}
	}
	?>
			<!-- CONTENT -->
			<div class="mobile_section">				
				<!-- FEATURED PRODUCTS -->
				<div class="section_head">
					<div class="grg"><?php echo $band['label']; ?></div>
					
					<?php if(!empty($breadcrumbs)): ?>
					<!-- BREADCRUMBS -->
						<p style="float:right;">
							| &nbsp;
							<?php foreach($breadcrumbs as $name => $value ):  
								if(($name == 'category') && (is_array($value))):
									$i = 0;
					  					foreach($value as $id => $name): $i++;?>
					  						<a href="<?php echo base_url('shopping/productlist/category'); ?>/<?php echo $id; ?>/"><?php echo $name; ?></a>  <?php if(count($value) == $i ): ?> &nbsp; | &nbsp;  <?php else: ?>><?php endif; ?> 
					  		<?php endforeach;
					
							
							elseif($value != @$breadcrumbs['brand']):  
								if(strpos($name, 'question') === 0):
									echo ucwords(preg_replace('/([A-Z])/',"\n".'$1',$value)); 
								else:
									echo ucwords(preg_replace('/([A-Z])/',"\n".'$1',$name)); 
								endif;	
								?>
									<a href="<?php echo base_url('shopping/productlist');  // REBUILD SEARCH URL ?>/
												<?php  foreach($breadcrumbs as $filter => $sortValue): 
																if(($filter != $name) && ($filter != 'brand')): // Do not put the ugliness of the long name in the URL
																	if(is_array($sortValue)):?>category
																	<?php elseif($sortValue == @$breadcrumbs['brand']): ?>brand
																	<?php  else: echo $filter; 
																	endif;  ?>/
														<?php if($filter == 'search'): 
																		echo 1; 
																	elseif(is_array($sortValue)):
																		end($sortValue);
																		echo key($sortValue);
																	else: 
																		echo $sortValue;  
																	endif; // Do not put search results in URL.
																echo '/';
																endif; 
															endforeach;  ?>" style="color:#F00;"><i class="fa fa-times"></i>	
								</a>  &nbsp; |  &nbsp; 
								<?php elseif($name != 'brand'):
									$brandlist = $breadcrumbs;
									unset($brandlist['brand']);
									$key = array_search($breadcrumbs['brand'], $brandlist);
									unset($brandlist[$key]);
									$sortURL = 'shopping/productlist';
									
									
									foreach($brandlist as $nameinside => $inside ):  
										if(($nameinside == 'category') && (is_array($inside))):
											end($inside);
											$id = key($inside);
											$sortURL .= '/category/'.$id;	
									
										elseif($name):
									
									  else: 
									    foreach($breadcrumbs as $filter => $sortValue): 
												if(($filter != $name) && ($filter != 'category') && ($filter != 'brand')): // Do not put the ugliness of the long name in the URL
													if(($sortValue != @$breadcrumbs['category']) && ($sortValue == @$breadcrumbs['brand'])):
															$sortURL .= '/'.$filter.'/';	
													elseif($filter == 'search'): 
														$sortURL .= 1; 
													else: 
														$sortURL .= $sortValue;  
													endif; // Do not put search results in URL.
												endif; 
											endforeach; 
										endif;
									endforeach; ?>
									<a href="<?php echo base_url($sortURL); ?>" style="color:#F00;"><i class="fa fa-times"></i></a> <?php echo $name; ?> 
							<?php endif;  endforeach; ?>
							
						</p>
						<!-- END BREADCRUMBS -->
						<?php endif; ?>
						
					<?php if(@$band['page']): ?>
						<a href="<?php echo base_url($band['page']); ?>/" class="button" style="float:right;">View All</a>
					<?php endif; ?>
					<div class="clear"></div>
				</div>
				
				<!-- PRODUCT LIST -->
				
				<?php $i = 0;  if(@$band['products']): foreach($band['products'] as $prod): 
				if(@$prod['price']['sale_min']): $i++;?>
				
				<div class="product_box <?php echo $prod['part_id']; ?>" <?php if($i == 4): $i = 0; ?> style="margin-right:0px; " <?php endif; ?> >
				<?php if($prod['stock_code'] == 'Closeout'): ?>
					<div class="percentage">CLOSEOUT <?php if(@$prod['price']['percentage']): echo number_format($prod['price']['percentage'], 0); ?>% OFF <?php endif; ?></div>
				<?php endif; ?>
					<!-- IMAGE -->
					<?php if (@$prod['images']): ?>
						<?php if(@$prod['images'][1]): ?>
						<div class="product_photo" style="float:left;">
						
							<img src="<?php echo base_url('productimages/'. $prod['images'][0]['path']); ?>" onmouseover="this.src='<?php echo base_url('productimages/'.  $prod['images'][1]['path']); ?>'" onmouseout="this.src='<?php echo base_url('productimages/'. $prod['images'][0]['path']); ?>'">
						</div>
						<?php else: ?>
						<div class="product_photo" >
							<img src="<?php echo base_url('productimages/'. $prod['images'][0]['path']); ?>">
						</div>
						<?php endif;
						 else: ?>
						<div class="product_photo">
								<img src="<?php echo $assets; ?>/images/test_image.jpg">
						</div>
						<?php endif; ?>
						<?php if(@$prod['activeRide']): ?><div class="product_icon" ><img src="<?php echo $assets; ?>/images/<?php echo $img; ?>" height="42" width="42" ></div><div class="clear"></div><?php endif; ?>
						<!-- END IMAGE -->
										
					<div class="product_box_text">
						<?php $seoUrl = '';
									if(@$name)
									{
										$seoUrl .= tag_creating($name).'-'; 
									}
									$seoUrl .= tag_creating($prod['label']);
										?>
						<h3><a href="<?php echo base_url('shopping/item/'.$prod['part_id'].'/'.$seoUrl); ?>"><?php echo $prod['label']; ?></a></h3>
					</div>
					<div style="float:left;"><div class="price">
						$<?php echo $prod['price']['sale_min']; if(@$prod['price']['sale_max']): echo ' - $' . $prod['price']['sale_max'];  endif;
						if(@$prod['price']['percentage']):?></div>
							<span style="font-size:10px;">You save $<?php echo ($prod['price']['retail_min'] - $prod['price']['sale_min']); if(@$prod['price']['sale_max']): echo ' - $' . ($prod['price']['retail_max'] - $prod['price']['sale_max']);  endif;?> (<?php echo number_format($prod['price']['percentage'], 0); ?>%) </span><?php else: ?>
							</div>
						<?php  endif; ?>
					</div>
										
					<div class="clear"></div>					
				</div>
				
				<script>
					$('.<?php echo $prod['part_id']; ?>').click(function() {
					  window.location.replace(base_url + 'shopping/item/<?php echo $prod['part_id'].'/'.$seoUrl; ?>');
					});
					$('.<?php echo $prod['part_id']; ?>').mouseover(function(){
						$('.<?php echo $prod['part_id']; ?>').css('cursor', 'pointer');
					});
				</script>
				<?php endif; endforeach; else: ?>
				No Products to Display.
				<?php endif; ?>
				<div class="clear"></div>
				<!-- END FEATURED PRODUCTS -->
			
			</div>
			<!-- END CONTENT -->


<?php endif; ?>