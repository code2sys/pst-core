	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		<?php  ?>
		<!-- MAIN CONTENT -->
		<div class="main_content fl-wdh">
			
			<script src="https://apis.google.com/js/platform.js"></script>
			<!-- CONTENT -->
			<?php if( $mainVideo != '' ) { ?>
				<div class="content_section rmv">
				<?php if( $mainVideo != '' ) { ?>
					<?php if (!empty($mainVideo)) { ?>
						<?php
						$CI =& get_instance();
						echo $CI->load->view("master/embedded_videos", array(
							"class_name" => "main-vdo",
							"mainVideo" => $mainVideo,
							"mainTitle" => $mainVideo['title'],
							"video" => $video,
							"rltdvdo_class" => "rltv-vdo",
							"autoplay" => true
						), true);
						?>
					<?php } ?>
				<?php } ?>
				</div>
			<?php } ?>
			
			
			<?php if(@$notice): ?>
				<div class="content_section">
					<h3><?php echo $notice; ?></h3>
				</div>
			<?php endif; ?>
                        
			<div class="clear"></div>
			<script>
				var page = "category";
			</script>
		<div id="mainProductBand"><?php echo @$mainProductBand; ?>	</div>
		<div class="pagination"><?php echo @$pagination; ?></div>
			<div class="clear"></div>
		<?php echo @$recentlyViewedBand; ?>	
		<?php //if(@$notice): ?>
			<!--<div class="content_section">	
				<h3><?php //echo $notice; ?></h3>			
			</div>-->
		<?php //endif; ?>			
		<div id="productPage" class="hide">1</div>
		</div>
		
		<!-- END MAIN CONTENT -->

		<?php echo @$sidebar; ?>
		<div class="clear"></div>
	</div>