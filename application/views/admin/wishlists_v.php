		<!-- CONTENT -->
		<div class="content">	
						
			
			<!-- ADMIN SECTION -->
			<div style="width:95.6%;">
				
				<h1>Manage Wishlists</h1>
				<p>
					Add and edit categories.
				</p>

		<!-- VALIDATION ALERT -->
		<div class='validation_error hide' id="val_container">
	    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <p><div id="val_error_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END VALIDATION ALERT -->
		
		<!-- PHP ALERT -->
      <?php if(validation_errors()): ?>
        <div class="validation_error">
        <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
          <h1>Error</h1>
          <p>Please make the following corrections: </p>
          <?php echo validation_errors(); ?>
        </div>
        <br />
      <?php endif; ?>					
					<div class="clear"></div>
				
					<br>
				
			<!-- PRODUCTS -->
			<div class="tabular_data">
				<table>
				</table>
			</div>
		</div>
	</div>