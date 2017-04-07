<script src="https://apis.google.com/js/platform.js" async defer></script>
<p class="vdottl"><?php echo $mainTitle;?></p>
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
		<script src="https://apis.google.com/js/platform.js"></script>
			<!-- CONTENT -->
		<?php if( $mainVideo != '' ) { ?>
			<div class="content_section rmv">
			<?php if( $mainVideo != '' ) { ?>
				<div class="main-vdo">
					<iframe src="https://www.youtube.com/embed/<?php echo $mainVideo;?>" data-id="<?php echo $mainVideo;?>" id="mainVideo" frameborder="0" allowfullscreen></iframe>
					<ul>
						<li><strong>Share :</strong>
                                                        <div class="ggl" style="min-width:100px;">
                                                            <div class="fb-share-button" data-href="https://www.youtube.com/embed/<?php echo $mainVideo;?>" data-layout="button_count"></div>
                                                        </div>
							<div class="ggl">
								<div class="g-plus fixwdth" data-action="share" data-href="https://www.youtube.com/embed/<?php echo $mainVideo;?>" data-width="250"></div>
							</div>
						</li>
						<li class="subs"><strong>Subscribe to us :</strong>
							<div class="g-ytsubscribe" data-channel="<?php echo YOUTUBE_CHANNEL; ?>" data-layout="default" data-count="default"></div>
						</li>
					</ul>
				</div>
			<script type="text/javascript">gapi.plus.go();</script>
			<?php } ?>
				<div class="rltv-vdo">
					<ul>
						<li onClick="showVideo('<?php echo $mainVideo; ?>', '<?php echo $mainTitle; ?>');" id="<?php echo $mainVideo; ?>" style="display:none;">
							<img class="ply" src="/qatesting/newassets/images/play.png">
							<img src="http://img.youtube.com/vi/<?php echo $mainVideo;?>/default.jpg" class="active">
							<p><?php echo $mainTitle;?></p>
						</li>
					<?php foreach( $video as $k => $v ) { ?>
						<li onClick="showVideo('<?php echo $v['video_url']; ?>', '<?php echo $v['title']; ?>');" id="<?php echo $v['video_url']; ?>">
							<img class="ply" src="/qatesting/newassets/images/play.png">
							<img src="http://img.youtube.com/vi/<?php echo $v['video_url'];?>/default.jpg" class="active">
							<p><?php echo $v['title'];?></p>
						</li>
					<?php } ?>
					</ul>
				</div>
			</div>
		<?php } ?>
			
			<?php if(@$band['featured']): ?>
			<div class="content_section">
				<!-- FEATURED PRODUCTS ----->
				<div class="section_head">
					<p class="rplch1"><?php echo $band['label'];?> Featured Products</p>
					<?php if( $_GET['v'] == '' ) { ?>
						<a href="<?php echo base_url($band['page']).'?v=featured'; ?>" onclick="setNamedSearch(event, 'extra', 'extra', 'featured');" class="button" style="float:right;">View All</a>
					<?php } ?>
					<div class="clear"></div>
				</div>
				
				<!-- PRODUCT LIST -->
				<?php $i = 0;  if(@$band['featured']): foreach($band['featured'] as $key => $prod): 
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
						<span class="nwprdct"><a href="<?php echo base_url('shopping/item/'.$prod['part_id'].'/'.$seoUrl); ?>"><?php echo $prod['label']; ?></a></span>
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
						<img src="<?php echo jsite_url("/productimages/"); ?><?php echo $prod['images'][$i]['path']; ?>">
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
				
				<?php endif; endforeach; ?>
				<?php endif; ?>
				<div class="clear"></div>
				<!-- END FEATURED PRODUCTS -->
			
			</div>
			<!-- END CONTENT -->
			<?php endif; ?>

			
			<?php if(@$band['newArrivals']['products']): ?>
			<div class="content_section">
				<!-- FEATURED PRODUCTS ----->
				<div class="section_head">
					<p class="rplch1"><?php echo $band['label'];?> New Arrivals</p>
					<?php if( $_GET['v'] == '' ) { ?>
						<a href="<?php echo base_url($band['page']).'?v=arrival'; ?>" onclick="setNamedSearch(event, 'extra', 'extra', 'arrival');" class="button" style="float:right;">View All</a>
					<?php } ?>
					<div class="clear"></div>
				</div>
				
				<!-- PRODUCT LIST -->
				<?php $i = 0;  if(@$band['newArrivals']['products']): foreach($band['newArrivals']['products'] as $key => $prod): 
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
						<span class="nwprdct"><a href="<?php echo base_url('shopping/item/'.$prod['part_id'].'/'.$seoUrl); ?>"><?php echo $prod['label']; ?></a></span>
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
						<img src="<?php echo jsite_url("/productimages/"); ?><?php echo $prod['images'][$i]['path']; ?>">
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
				
				<?php endif; endforeach; ?>
				<?php endif; ?>
				<div class="clear"></div>
				<!-- END FEATURED PRODUCTS -->
			
			</div>
			<!-- END CONTENT -->
			<?php endif; ?>

			
			
			<?php if(@$band['topSeller']['products']): ?>
			<div class="content_section">
				<!-- FEATURED PRODUCTS ----->
				<div class="section_head">
					<p class="rplch1"><?php echo $band['label'];?> Top Sellers</p>
					<div class="clear"></div>
				</div>
				
				<!-- PRODUCT LIST -->
				<?php $i = 0;  if(@$band['topSeller']['products']): foreach($band['topSeller']['products'] as $key => $prod): 
									$seoUrl = '';
									if((isset($name)) &&(@$name != 'brand') &&(@$name != 'featured') && (@$name != 'category') && (@$name != 'question'))
										$seoUrl .= tag_creating($name).'-'; 
									elseif((@$name == 'category')&& (isset($subname)))
										$seoUrl  .= tag_creating($subname).'-'; 
									$seoUrl .= tag_creating($prod['label']);
									if(substr($seoUrl, 0, 5) == 'brand')
										$seoUrl = substr($seoUrl, -5, 0);
				$i++;?>
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
						<span class="nwprdct"><a href="<?php echo base_url('shopping/item/'.$prod['part_id'].'/'.$seoUrl); ?>"><?php echo $prod['label']; ?></a></span>
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
						<img src="<?php echo jsite_url("/productimages/"); ?><?php echo $prod['images'][$i]['path']; ?>">
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
				
				<?php endforeach; ?>
				<?php endif; ?>
				<div class="clear"></div>
				<!-- END FEATURED PRODUCTS -->
			
			</div>
			<!-- END CONTENT -->
			<?php endif; ?>
			

			<?php if(@$band['closeouts']): ?>
			<!-- END CONTENT -->
			<div class="content_section">
				<!-- FEATURED PRODUCTS -->
				<div class="section_head">
					<p class="rplch1"><?php echo $band['label'];?> Closeouts</p>
					<?php if( $_GET['v'] == '' ) { ?>
						<a href="<?php echo base_url($band['page']).'?v=closeout'; ?>" onclick="setNamedSearch(event, 'extra', 'extra', 'closeout');" class="button" style="float:right;">View All</a>
					<?php } ?>
					<div class="clear"></div>
				</div>
				
				<!-- PRODUCT LIST -->
				<?php $i = 0;  if(@$band['closeouts']): foreach($band['closeouts'] as $key => $prod): 
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
						
						<span class="nwprdct"><a href="<?php echo base_url('shopping/item/'.$prod['part_id'].'/'.$seoUrl); ?>"><?php echo $prod['label']; ?></a></span>
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
						<img src="<?php echo jsite_url("/productimages/"); ?><?php echo $prod['images'][$i]['path']; ?>">
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
				
				<?php endif; endforeach; ?>
				<?php endif; ?>
				<div class="clear"></div>
				<!-- END FEATURED PRODUCTS -->
			
			</div>
			<!-- END CONTENT -->
			<?php endif; ?>
<div id="fb-root"></div>
