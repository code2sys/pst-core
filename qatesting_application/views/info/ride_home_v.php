	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		
		
		<!-- MAIN CONTENT -->
		<div class="main_content">
			
			<?php echo @$widgetBlock; ?>
		
			<?php echo @$featureBand; ?>
			
			<?php echo @$dealsBand; ?>
			
			<?php echo @$topSellersBand;  ?>			
			
			<?php echo @$recentlyViewedBand; ?>		
			
			<?php if(@$catRecord['notice']): ?>
				<div class="content_section">	
					<h3><?php echo @$catRecord['notice']; ?></h3>			
				</div>
			<?php endif; ?>		
	
		</div>
		<!-- END MAIN CONTENT -->
		
		
		<?php echo @$sidebar; ?>
		
		<div class="clear"></div>
		
	</div>
	