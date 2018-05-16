	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		
		
		<!-- MAIN CONTENT -->
		<div class="main_content fl-wdh <?php if ($full_info_content): ?>full_info_content<?php endif; ?>" <?php if ($full_info_content): ?>style="float: none !important
		; width: 100% !important;"<?php endif; ?>>
			
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

            <?php if (isset($pageRec) && is_array($pageRec) && array_key_exists("page_custom_js", $pageRec) && $pageRec["page_custom_js"] != ""): ?>
            <script type="application/javascript">
            <?php echo $pageRec["page_custom_js"]; ?>
            </script>
            <?php endif; ?>

		</div>
		<!-- END MAIN CONTENT -->
		
		
		<?php echo @$sidebar; ?>
		
		<div class="clear"></div>
		
	</div>
