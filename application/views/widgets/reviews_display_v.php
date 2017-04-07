<?php 	if (!function_exists('tag_creating')) {	
		function tag_creating($url) 
		{
			$url = str_replace(' ', '-', $url);			
			$url = preg_replace('~[^\\pL0-9_-]+~u', '', $url);
			$url = trim($url, "-");
			$url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
			$url = strtolower($url);
			$url = preg_replace('~[^-a-z0-9_-]+~', '', $url);
		   return $url;
		}
	}
	?>

<?php if(@$reviews): ?>
		<?php  foreach($reviews as $review): ?>
<div class="review_box" >
					<!-- LABEL -->
					<a href="<?php echo base_url('shopping/item/'.$review['part_id'].'/'.tag_creating($review['name'])); ?>"><?php echo $review['name'];  ?></a>
					<!-- END LABEL -->
					<div class="clear"></div>
					<!-- IMAGE -->
					<a href="<?php echo base_url('shopping/item/'.$review['part_id'].'/'.tag_creating($review['name'])); ?>">
					<?php if (@$review['images']): ?>
						<?php if(@$review['images'][1]): ?>
						<div class="review_photo">
							<img src="<?php echo base_url('productimages/'. $review['images'][0]['path']); ?>" onmouseover="this.src='<?php echo base_url('productimages/'.  $review['images'][1]['path']); ?>'" onmouseout="this.src='<?php echo base_url('productimages/'. $review['images'][0]['path']); ?>'">
						</div>
						<?php else: ?>
						<div class="review_photo" >
							<img src="<?php echo base_url('productimages/'. $review['images'][0]['path']); ?>">
						</div>
						<?php endif;
						 else: ?>
						<div class="review_photo">
								<img src="<?php echo $assets; ?>/images/test_image.jpg">
						</div>
						<?php endif; ?>
					</a>
						<!-- END IMAGE -->
<br />
	<table class="hidden_table">
		<tr><td><?php for($i=0; $i < $review['rating']; $i++): ?> <i class="fa fa-star" style="color:#FFD700"></i><?php endfor; ?></td></tr>
		<tr><td><?php echo @$review['first_name'] ? $review['first_name'] : 'Anonymous'; ?></td></tr>
		<tr><td></td></tr>
		<tr><td>
			<?php echo substr($review['review'],0,400); if(strlen($review['review']) > 400): echo '  ...'; endif;?>
		</td></tr>		
	</table>
	<div class="clear"></div>
	<a href="<?php echo base_url('shopping/item/'.$review['part_id'].'/'.tag_creating($review['name'])); ?>" style="color:#393; font-size:10px;float:right;">See all reviews for this product</a>
</div>
<?php endforeach; endif; ?>