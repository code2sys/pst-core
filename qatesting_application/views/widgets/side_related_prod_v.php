<?php if(@$band['products']): ?>
<!-- GARAGE -->
<div class="side_header">
	<h1>Related Products</h1>
</div>
<div class="side_section">
<?php $i = 0;  foreach($band['products'] as $prod): $i++; ?>		
	<div class="related_wrap">
	
		<div class="related_photo">
			<div class="popup-gallery" >
			<?php if (file_exists($upload_path.'/'.@$prod['image'])):  // Image is there ?>
				
				<a href="<?php echo base_url( $media ); ?>/<?php echo $prod['image']; ?>" title="<?php echo $prod['label']; ?>">
					<img src="<?php echo base_url( $media ); ?>/<?php echo $prod['image']; ?>">
				</a>
				
			<?php elseif(strpos($prod['image'], ',')):  // Array of Images
							$images = explode(',', $prod['image']); 
								if (file_exists($upload_path.'/'.@$images[0])): // Images are valid ?>
								
									<a href="<?php echo base_url( $media );  ?>/<?php echo $images[0]; ?>" title="<?php echo $prod['label']; ?>">
										<img src="<?php echo base_url( $media );  ?>/<?php echo $images[0]; ?>" onmouseover="this.src='<?php echo base_url( $media );  ?>/<?php echo $images[1]; ?>'" onmouseout="this.src='<?php echo base_url( $media );  ?>/<?php echo $images[0]; ?>'">
									</a>

				
								<?php else: // Array of images are not valid images ?>
								
									<a href="<?php echo $assets; ?>/images/test_image.jpg" title="Product Name">
										<img src="<?php echo $assets; ?>/images/test_image.jpg">
									</a>
									
								<?php endif; ?>
			
			<?php else: // Individual image is not valid and no array ?>
			
				<a href="<?php echo $assets; ?>/images/test_image.jpg" title="Product Name">
					<img src="<?php echo $assets; ?>/images/test_image.jpg">
				</a>
			
			<?php endif; ?>
			</div>
		</div>
		
		<div class="related_info">
			<h3><a href="#"><b><?php echo $prod['label']; ?></b></a></h3>
		</div>
		<div class="clear"></div>
		<h3> &nbsp; Price: &nbsp; <span class="price">$<?php echo $prod['price']; ?></span></h3>
		<a href="<?php echo base_url('shopping/item/'.$prod['part_id']); ?>" class="button_full">Details</a>
		<div class="clear"></div>
	
	</div>
<?php endforeach; ?>
	</div>
<!-- END GARAGE -->
<?php endif; ?>
