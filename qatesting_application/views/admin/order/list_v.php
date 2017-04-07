	<div class="content_wrap">
		<div class="content">
		<a href="<?php echo base_url('admin/order_edit'); ?>" id="button">Create Order</a>
		<div class="clear"></div>
		
		<!-- VALIDATION ALERT -->
			<?php if(validation_errors() || @$errors): ?>
			<div class="validation_error" id="login_validation_error">
			  <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
		    <h1>Error</h1>
		    <div class="clear"></div>
		    <p><?php echo validation_errors(); if(@$errors): foreach($errors as $error): echo $error; endforeach; endif; ?></p>
		    
			</div>
			<?php endif; ?>
			<!-- END VALIDATION ALERT -->
		
		<div class="admin_search_left">
		
		<div class="clear"></div>
			<form action="<?php echo base_url('admin/orders'); ?>/" method="post" class="form_standard">
				<div class="hidden_table">
				<b>Show Only: </b>
					<table>
						<tr>
							<td><?php echo form_checkbox('pending', 'pending', '', 'class="show"'); ?>Pending</td>
							<td><?php echo form_checkbox('approved', 'approved', 1, 'class="show"'); ?>Approved</td>
							<td><?php echo form_checkbox('declined', 'declined', '', 'class="show"'); ?>Declined</td>
						</tr>
						<tr>
							<td><?php echo form_checkbox('batch', 'batch', '', 'class="show"'); ?>Batch Order</td>
							<td><?php echo form_checkbox('processing', 'processing', '', 'class="show"'); ?>Processing</td>
							<td><?php echo form_checkbox('back', 'back', '', 'class="show"'); ?>Back Order</td>
						</tr>
						<tr>
							<td><?php echo form_checkbox('partially', 'partially', '', 'class="show"'); ?>Partially Shipped</td>
							<td><?php echo form_checkbox('will_call', 'will_call', '', 'class="show"'); ?>Ready to Pick Up</td>
							<td><?php echo form_checkbox('shipped', 'shipped', '', 'class="show"'); ?>Shipped/Complete</td>
						</tr>
						<tr>
							<td><?php echo form_checkbox('return', 'return', '', 'class="show"'); ?>Returned</td>
							<td><?php echo form_checkbox('refunded', 'refunded', '', 'class="show"'); ?>Refunded</td>
						</tr>
					</table>
					<input type="submit" value="Go!" class="button" style="margin-top:35px;">
				</div>
			</form>
		</div>
		<div class="admin_search_right">
			<form action="<?php echo base_url('admin/orders'); ?>/" method="post" id="moto_search" class="form_standard">
				<div class="hidden_table">
				<b>Lookup Order </b><input id="search" name="search" placeholder="Search <?php echo WEBSITE_NAME; ?>" class="text large" style="height:25px;" />
					<table style="font-size:smaller; width:100%">
						<tr>
							<td valign="center"><?php echo form_radio('days', 1, ''); ?>Today</td>
							<td><?php echo form_radio('days', 7, ''); ?>&lt; 7 Days</td>
							<td><div style="margin-left:20px"><?php echo form_radio('days', 30, ''); ?>&lt; 30 Days</div></td>
							<td><?php echo form_radio('days', 'All', ''); ?>All Orders</td>
						</tr>
						<tr>
							<td><?php echo form_radio('days', 'Custom', ''); ?>Date Range</td>
							<td></td>
							<td>From: <input id="datepicker_from" name="date_search_from" placeholder="Date" class="text mini" style="height:25px;" /></td>
							<td>To: <input id="datepicker_to" name="date_search_to" placeholder="Date" class="text mini" style="height:25px;" /></td>
							
						</tr>
					</table>
					<input type="submit" value="Go!" class="button" style="margin-top:6px;">
				</div>
				
			</form>
		</div>
		<div class="clear"></div>
			<div id="listTable"><?php echo @$listTable; ?></div>
	</div>
	
	<script>
	$( ".show" ).on( "click", function() {
	     var allCks = [];
	     $( "input:checked" ).each(function() {
	       allCks.push($(this).val());
	     });
	     $.post(base_url + 'admin/generateListOrderTable', 
	     			{
	     				'filter' : allCks,
	     				'ajax' : true
	     			}, 
	     			function (returnData) {
			 			$('#listTable').html(returnData);
	     });
		  });
	</script>