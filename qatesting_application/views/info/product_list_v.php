	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		
		
		<!-- MAIN CONTENT -->
		<div class="main_content">
			
		<div class="pagination"><?php echo @$pagination; ?></div>
			<div class="clear"></div>
			<script>
				var page = "category";
			</script>
		<div id="mainProductBand"><?php echo @$mainProductBand; ?>	</div>
		<div class="pagination"><?php echo @$pagination; ?></div>
			<div class="clear"></div>
		<?php echo @$recentlyViewedBand; ?>	
		<?php if(@$notice): ?>
			<div class="content_section">	
				<h3><?php echo $notice; ?></h3>			
			</div>
		<?php endif; ?>			
		<div id="productPage" class="hide">1</div>
		</div>
		
		<!-- END MAIN CONTENT -->

		<?php echo @$sidebar; ?>
		<div class="clear"></div>
	</div>