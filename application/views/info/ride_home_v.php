	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		
		
		<!-- MAIN CONTENT -->
		<div class="main_content fl-wdh">
			
			<?php echo @$widgetBlock; ?>
		
			<?php echo @$featureBand; ?>
			
			<?php echo @$dealsBand; ?>
			
			<?php echo @$topSellersBand;  ?>			
			
			<?php echo @$recentlyViewedBand; ?>		
			
			<?php if(@$notice && $showNotice): ?>
				<div class="content_section">	
					<h3><?php echo @$notice; ?></h3>			
				</div>
			<?php endif; ?>		
	
		</div>
		<!-- END MAIN CONTENT -->
		
		
		<?php echo @$sidebar; ?>
		
		<div class="clear"></div>
		
	</div>
