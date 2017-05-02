		<!-- CONTENT -->
		<div class="content">	
						
			<!-- ADMIN SECTION -->
			<div style="width:95.6%;">
				
				<h1>Manage Shipping Rules</h1>
				<p>
					Add and edit Shipping Rules.
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
				
			<!-- SHIPPING RULES -->
			<p> All shipping rules must be mutually exclusive.  The system will apply the first rule it encounters that matches the order parameters.<br />
				If no shipping rules apply to an order, weight based shipping will be applied.
			</p>
			<form action="<?php echo base_url('admin/shipping_rules'); ?>" method="post" id="form_example" class="form_standard">
			<div class="tabular_data">
			<?php if(@$shippingRules): ?>
				<?php echo form_submit('edit', 'Submit Changes', 'id="button"'); ?>
				<div class="clear"></div>
				<table cellpadding="3" style="width:100%;">
					
					<tr class="head_row"><td><b>Name</b></td><td><b>Country</b></td><td><b>Price</b></td><td><b>Active</b></td><td><b>Actions</b></td></tr>
					<?php foreach($shippingRules as $rule): ?>
					 <input type="hidden" name="id" value='<?php echo $rule['id']; ?>'>
					 <input type="hidden"name="name" value='<?php echo $rule['name']; ?>'>
					 <input type="hidden" name="country" value='<?php echo $rule['country']; ?>'>
						<tr>
							<td><?php echo $rule['name']; ?></td>
							<td><?php echo $countries[$rule['country']]; ?></td>
							<td><input name="value" value="<?php echo $rule['value']; ?>" class="text large" /></td>
							<td><?php echo form_checkbox('active', 1, $rule['active']); ?></td>
							<td><a href="javascript:void(0);" id="button" onclick="populateEdit(<?php echo $rule['id']; ?>);">Edit</a> <a href="<?php echo base_url('admin/shipping_rule_delete/'.$rule['id']); ?>" id="button">Delete</a>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
			<?php endif; ?>
			</form>
			
						<form action="<?php echo base_url('admin/shipping_rules'); ?>" method="post" id="form_example" class="form_standard">
			<div class="tabular_data">
				<?php //echo form_submit('create_new', 'Create New', 'class="new" id="button"'); ?>
				<?php //echo form_submit('edit', 'Edit',  'class="edit hide" id="button"'); ?>
				<div class="clear"></div>
				 <input type="hidden" name="id" value='' id="id">
				<table cellpadding="3" style="width:100%;">
					<tr><td><b>Name*</b></td><td><input id="name" name="name" value="<?php set_value('name'); ?>" class="text large" /></td></tr>
					<tr><td><b>Price*</b></td><td><input id="value" name="value" value="<?php set_value('value'); ?>" class="text large" /></td>
					<tr><td><b>Weight (Low)</b></td><td><input id="weight_low" name="weight_low" value="<?php set_value('weight_low'); ?>" class="text large" /></td></tr>
					<tr><td><b>Weight (High)</b></td><td><input id="weight_high" name="weight_high" value="<?php set_value('weight_high'); ?>" class="text large" /></td></tr>
					<tr><td><b>Cart Price (Low)</b></td><td><input id="price_low" name="price_low" value="<?php set_value('price_low'); ?>" class="text large" /></td></tr>
					<tr><td><b>Cart Price (High)</b></td><td><input id="price_high" name="price_high" value="<?php set_value('price_high'); ?>" class="text large" /></td></tr>
					<!--
<tr><td><b>Width (Low)</b></td><td><input id="width_low" name="width_low" value="<?php set_value('width_low'); ?>" class="text large" /></td></tr>
					<tr><td><b>Width (High)</b></td><td><input id="width_high" name="width_high" value="<?php set_value('width_high'); ?>" class="text large" /></td></tr>
					<tr><td><b>Height (Low)</b></td><td><input id="height_low" name="height_low" value="<?php set_value('height_low'); ?>" class="text large" /></td></tr>
					<tr><td><b>Height (High)</b></td><td><input id="height_high" name="height_high" value="<?php set_value('height_high'); ?>" class="text large" /></td></tr>
-->
					<tr><td><b>Country</b></td><td><?php echo form_dropdown('country', $countries, set_select('country')); ?></td></tr>
					<tr><td><b>Active</b></td>	<td><?php echo form_checkbox('active', 1, 1); ?></td></tr>
				</table>
				<br />
				<?php echo form_submit('create_new', 'Create New', 'class="new" id="button"'); ?>
				<?php echo form_submit('edit', 'Edit',  'class="edit hide" id="button"'); ?>
				<a href="<?php echo base_url('admin/shipping_rules'); ?>" class="button edit hide">Cancel</a>

			</div>
			</form>
			
		</div>
	</div>
	
<script>
	
	function populateEdit(id)
	{
		
		$.post(base_url + 'admin/load_shipping_rules/' + id,
			{},
			function(encodeResponse)
			{
				 responseData = JSON.parse(encodeResponse);
		  		  console.log(responseData);
	       		  $('.edit').show();
	       		  $('.new').hide();
	       		  $('#id').val(responseData['id']);
	       		  $('#country').val(responseData['country']);
	       		  if(responseData['active'] == 1)
	       		  	$('#active').prop('checked', true);
	       		  if(responseData['active'] == 0)
	       		  	$('#active').prop('checked', false);
	       		 	       		  	
	       		  $('#name').val(responseData['name']);
	       		  $('#value').val(responseData['value']);
	       		  $('#weight_low').val(responseData['weight_low']);
	       		  $('#weight_high').val(responseData['weight_high']);
	       		  $('#price_low').val(responseData['price_low']);
	       		  $('#price_high').val(responseData['price_high']);
	       		  $('#width_low').val(responseData['width_low']);
	       		  $('#width_high').val(responseData['width_high']);
	       		  $('#height_low').val(responseData['height_low']);
	       		  $('#height_high').val(responseData['height_high']);
			});
	
	}
	
</script>