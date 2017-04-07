		<!-- CONTENT -->
		<div class="content">	
						
			
			<!-- ADMIN SECTION -->
			<div style="width:95.6%;">
				
				<h1>Manage Taxes</h1>
				<p>
					Edit taxes.
				</p>

		<!-- VALIDATION ALERT -->
		<div class='validation_error hide' id="val_container">
	    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <div class="clear"></div>
	    <p><div id="val_error_message"></div></p>
		</div>
		<!-- END VALIDATION ALERT -->
		
		<!-- PHP ALERT -->
      <?php if(validation_errors()): ?>
        <div class="validation_error">
        <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
          <h1>Error</h1>
	    <div class="clear"></div>
          <?php echo validation_errors(); ?>
        </div>
        <br />
      <?php endif; ?>					
					<div class="clear"></div>
				
					<br>
				
			<!-- TAXES -->
			<form action="<?php echo base_url('admin/taxes'); ?>" method="post" id="form_example" class="form_standard">
			<div class="tabular_data">
				<?php echo form_submit('submit', 'Submit'); ?>
				<table cellpadding="3" style="width:100%;">
					<tr class="head_row">
						<td><b>Country</b></td>
						<td><b>State/Providence</b></td>
						<td><b>Shipping Active</b></td>
						<td><b>Percentage</b></td>
						<td><b>Value</b></td></tr>
					<?php if(@$taxes): foreach($taxes as $tax): ?>
						<tr>
							<td><?php echo $countries[$tax['country']]; echo form_hidden('id[]', $tax['id']); ?></td>
							<td><?php echo $tax['state']; ?></td>
							<td><?php echo form_checkbox('active[]', 1, $tax['active']); ?></td>
							<td><?php echo form_checkbox('percentage[]', 1, $tax['percentage']); ?></td>
							<td><input id="tax_value" name="tax_value[]" value="<?php echo @$tax['tax_value']; ?>" class="text large" /></td>
						</tr>
					<?php endforeach; endif; ?>
				</table>
			</div>
			</form>
		</div>
	</div>