<?php if(@$brandImages): ?>
		<!-- BRAND SLIDER -->
		<div class="brand_wrap">
			<div class="brands">
				<ul id="flexiselDemo3">
					<?php foreach($brandImages as $brand): ?>
							<li>
								<div>
									<a href="<?php echo base_url('shopping/productlist/'.@$brand['link']); ?>" onclick="setNamedSearch(event, 'brand', '<?php echo $brand['brand_id']; ?>', '<?php echo addslashes($brand['name']); ?>');"><img src="<?php echo $s_baseURL.$media; ?>/<?php echo $brand['image']; ?>" /></a></div></li>						
					<?php endforeach; ?>                                              
				</ul>
				<div class="clear"></div>
			</div>
		</div>
		<!-- END BRAND SLIDER -->
<?php endif; ?>