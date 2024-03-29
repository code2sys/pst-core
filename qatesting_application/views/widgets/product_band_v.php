	
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
			<div class="content_section">				
				<!-- FEATURED PRODUCTS -->
				<div class="section_head">
					<h4><?php echo $band['label']; ?></h4>
					<?php //print_r($breadcrumbs); ?>
					<?php if(!empty($breadcrumbs) && ($band['label'] == 'Search Results')): ?>
					<!-- BREADCRUMBS -->
						<h1 style="float:right; font-size:12px;">
							| &nbsp;
							<?php foreach($breadcrumbs as $name => $value ):  
								if(($name == 'category') && (is_array($value))):
									$i = 0;
					  					foreach($value as $id => $subname): $i++;?>
					  						<a href="javascript:void(0);" onclick="setMainSearch(event, 'category', '<?php echo $id; ?>');" id="<?php echo $id; ?>"><?php echo $subname; ?></a>  <?php if(count($value) == $i ): ?> &nbsp; | &nbsp;  <?php else: ?>><?php endif; ?> 
					  						
					  		<?php endforeach;
					
							
							elseif(($value != @$breadcrumbs['brand']) && ($value != @$breadcrumbs['featured']) && ($value != @$breadcrumbs['deal'])):  
									if(is_array($value)):
									foreach($value as $id => $subname):
										
										if($name == 'question'): ?>
							
										<a href="javascript:void(0);" onclick="removeMainSearch('question', '<?php echo $id; ?>' )" style="color:#F00;"><i class="fa fa-times"></i>	
								</a> 
										<?php 
										else: ?>
											<a href="javascript:void(0);" onclick="removeMainSearch('search', '<?php echo $id; ?>' )" style="color:#F00;"><i class="fa fa-times"></i>	
								</a>  
									<?php endif; 
										echo ucwords(preg_replace('/([A-Z])/',"\n".'$1',$subname)); ?> &nbsp; |  &nbsp; <?php 
										endforeach;  endif;

								elseif($name == 'brand'): 
									$category = 'brand'; ?>

									<a href="javascript:void(0);" onclick="removeMainSearch('<?php echo $category; ?>', '<?php echo $value; ?>' )" style="color:#F00;"><i class="fa fa-times"></i></a> <?php echo $_SESSION['search'][$category]['name']; ?> &nbsp; |  &nbsp; <?php 
								elseif(@$breadcrumbs['featured']): ?>
									FEATURED PRODUCTS
								<?elseif(@$breadcrumbs['deal']): ?>
								SITE DEALS
							<?php endif;  endforeach; ?>
							
						</h1>
						<!-- END BREADCRUMBS -->
						<?php endif; ?>
						
					<?php if(@$band['page']): ?>
						<a href="<?php echo base_url($band['page']); ?>/" class="button" style="float:right;">View All</a>
					<?php endif; ?>
					<div class="clear"></div>
				</div>
				
				<!-- PRODUCT LIST -->
				<?php $i = 0;  if(@$band['products']): foreach($band['products'] as $key => $prod): 
									$seoUrl = '';
									if((isset($name)) &&(@$name != 'brand') &&(@$name != 'featured') && (@$name != 'category') && (@$name != 'question'))
										$seoUrl .= tag_creating($name).'-'; 
									elseif((@$name == 'category')&& (isset($subname)))
										$seoUrl  .= tag_creating($subname).'-'; 
									$seoUrl .= tag_creating($prod['label']);
									if(substr($seoUrl, 0, 5) == 'brand')
										$seoUrl = substr($seoUrl, -5, 0);
				if(@$prod['price']['sale_min']): $i++;?>
				<div class="product_box " <?php if($i == 4): $i = 0; ?> style="margin-right:0px; " <?php endif; ?> >
				<?php if($prod['stock_code'] == 'Closeout'): ?>
					<div class="percentage">CLOSEOUT <?php if(@$prod['price']['percentage']): echo number_format($prod['price']['percentage'], 0); ?>% OFF <?php endif; ?></div>
				<?php endif; ?>
					<!-- IMAGE -->
					<a href="<?php echo base_url('shopping/item/'.$prod['part_id'].'/'.$seoUrl); ?>">
					<?php if (@$prod['images']): ?>

						<div class="product_photo" >
							<img <?php if(($key == 0) && ($band['label'] == 'Search Results')): ?>itemprop="image"<?php endif; ?> src="<?php echo base_url('productimages/'. $prod['images'][0]['path']); ?>">
						</div>
						<?php else: ?>
						<div class="product_photo">
								<img src="<?php echo $assets; ?>/images/test_image.jpg">
						</div>
						<?php endif; ?>
						<div class="product_icon" ><?php if(@$prod['activeRide']): ?><img src="<?php echo $assets; ?>/images/<?php echo $img; ?>" height="42" width="42" ></div><div class="clear"><?php endif; ?></div>
						</a>
						<!-- END IMAGE -->
					<div class="product_box_text">
						
						<h3><a href="<?php echo base_url('shopping/item/'.$prod['part_id'].'/'.$seoUrl); ?>"><?php echo $prod['label']; ?></a></h3>
					</div>
					<div style="float:left;"><div class="price">
						$<?php echo $prod['price']['sale_min']; if(@$prod['price']['sale_max']): echo ' - $' . $prod['price']['sale_max'];  endif; ?>
						</div><div class="discount">
						<?php if(@$prod['price']['percentage']):?>
							You save $<?php echo ($prod['price']['retail_min'] - $prod['price']['sale_min']); if(@$prod['price']['sale_max']): echo ' - $' . ($prod['price']['retail_max'] - $prod['price']['sale_max']);  endif;?> (<?php echo number_format($prod['price']['percentage'], 0); ?>%) 
						<?php  endif; ?>
						</div>
					</div>
					<div class="clear"></div>
					<div class="product_photo_small">
					<?php if (is_array(@$prod['images'])): $i = 0;  while($i < 3): ?>
						<!-- <img src="<?php echo base_url('productimages/'. $prod['images'][$i]['path']); ?>"> -->
						<img src="/productimages/<?php echo $prod['images'][$i]['path']; ?>">
						<?php if( !@$prod['images'][++$i]['path']): $i = 4; endif; ?>
					<?php endwhile; 
						if(count($prod['images']) >= 4): ?>
							<img src="<?php echo $assets; ?>/images/moreImages.png">
						
					<?php endif; endif; ?>
					</div>		
					<div class="reviews">
						<?php if(@$prod['reviews']): 
							$remainder = floor(5 - $prod['reviews']['average']);
							for($i=0; $i < $prod['reviews']['average']; $i++): ?> <i class="fa fa-star" style="color:#FFD700"></i><?php endfor;  
							if($remainder > 0)
							for($i=0; $i < $remainder; $i++): ?> <i class="fa fa-star" style="color:#b6b6b6"></i><?php endfor;  ?>
							(<?php echo $prod['reviews']['qty']; ?>)
						<?php endif; ?>
					</div>	
				</div>
				
				<?php endif; endforeach; else: ?>
				Sorry! No Products to Display.
				<?php endif; ?>
				<div class="clear"></div>
				<!-- END FEATURED PRODUCTS -->
			
			</div>
			<!-- END CONTENT -->


