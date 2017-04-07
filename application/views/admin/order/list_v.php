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
		
	  <form action="<?php echo base_url('admin/orders'); ?>/" method="get" class="form_standard">
		<div class="admin_search_left">
		
		<div class="clear"></div>
				<div class="hidden_table">
				<b>Show Only: </b>
					<table>
						<tr>
						<?php $pending = in_array('pending', $_GET['status']) ? 1 : 0; ?>
						<?php $approved = in_array('approved', $_GET['status']) || empty($_GET['status']) ? 1 : 0; ?>
						<?php $declined = in_array('declined', $_GET['status']) ? 1 : 0;?>
							<td><?php echo form_checkbox('status[]', 'pending', $pending, 'class="show"'); ?>Pending</td>
							<td><?php echo form_checkbox('status[]', 'approved', $approved, 'class="show"'); ?>Approved</td>
							<td><?php echo form_checkbox('status[]', 'declined', $declined, 'class="show"'); ?>Declined</td>
						</tr>
						<tr>
						<?php //$batch = in_array('batch order', $_GET['status']) ? 1 : 0; ?>
						<?php $processing = in_array('processing', $_GET['status']) || empty($_GET['status']) ? 1 : 0; ?>
						<?php //$processing = in_array('processing', $_GET['status']) ? 1 : 0; ?>
						<?php $back = in_array('back order', $_GET['status']) ? 1 : 0;?>
						<?php $partially = in_array('partially shipped', $_GET['status']) ? 1 : 0; ?>
							<!--<td><?php echo form_checkbox('status[]', 'batch', $batch, 'class="show"'); ?>Batch Order</td>-->
							<td><?php echo form_checkbox('status[]', 'processing', $processing, 'class="show"'); ?>Processing</td>
							<td><?php echo form_checkbox('status[]', 'back order', $back, 'class="show"'); ?>Back Order</td>
							<td><?php echo form_checkbox('status[]', 'partially shipped', $partially, 'class="show"'); ?>Partially Shipped</td>
						</tr>
						<tr>
						<?php $will_call = in_array('will_call', $_GET['status']) ? 1 : 0; ?>
						<?php $shipped = in_array('shipped/complete', $_GET['status']) ? 1 : 0;?>
						<?php $return = in_array('returned', $_GET['status']) ? 1 : 0; ?>
							<td><?php echo form_checkbox('status[]', 'will_call', $will_call, 'class="show"'); ?>Ready to Pick Up</td>
							<td><?php echo form_checkbox('status[]', 'shipped/complete', $shipped, 'class="show"'); ?>Shipped/Complete</td>
							<td><?php echo form_checkbox('status[]', 'returned', $return, 'class="show"'); ?>Returned</td>
						</tr>
						<tr>
						<?php $refunded = in_array('refunded', $_GET['status']) ? 1 : 0; ?>
							<td><?php echo form_checkbox('status[]', 'refunded', $refunded, 'class="show"'); ?>Refunded</td>
						</tr>
					</table>
					<input type="submit" value="Go!" class="button" style="margin-top:35px;">
				</div>
			<!--</form>-->
		</div>
		<div class="admin_search_right">
			<!--<form action="<?php echo base_url('admin/orders'); ?>/" method="post" id="moto_search" class="form_standard">-->
				<div class="hidden_table">
				<b>Lookup Order </b><input id="search" name="search" placeholder="Search <?php echo WEBSITE_NAME; ?>" class="text large" style="height:25px;" value="<?php echo $filter['search'];?>"/>
					<table style="font-size:smaller; width:100%">
						<tr>
						<?php $one_day = $filter['days']==1 ? 'checked' : ''; ?>
						<?php $seven_day = $filter['days']==7 ? 'checked' : ''; ?>
						<?php $thirty_day = $filter['days']==30 ? 'checked' : ''; ?>
						<?php $all = $filter['days']=='All' ? 'checked' : ''; ?>
						<?php $custom = $filter['days']=='Custom' ? 'checked' : ''; ?>
							<td valign="center"><?php echo form_radio('days', 1, $one_day); ?>Today</td>
							<td><?php echo form_radio('days', 7, $seven_day); ?>&lt; 7 Days</td>
							<td><div style="margin-left:20px"><?php echo form_radio('days', 30, $thirty_day); ?>&lt; 30 Days</div></td>
							<td><?php echo form_radio('days', 'All', $all); ?>All Orders</td>
						</tr>
						<tr>
							<td><?php echo form_radio('days', 'Custom', $custom); ?>Date Range</td>
							<td></td>
							<td>From: <input id="datepicker_from" name="date_search_from" placeholder="Date" class="text mini" style="height:25px;"  value="<?php echo $filter['date_search_from'];?>"/></td>
							<td>To: <input id="datepicker_to" name="date_search_to" placeholder="Date" class="text mini" style="height:25px;" value="<?php echo $filter['date_search_to'];?>"/></td>
						</tr>
						<!--<tr>
							<td>Phone No</td>
							<td><input id="phone" name="phone" placeholder="Phone" class="text mini" style="height:20px;width:200px;" /></td>
							<td>Order No</td>
							<td><input id="order" name="order_no" placeholder="Order" class="text mini" style="height:20px;width:200px;" /></td>
						</tr>
						<tr>
							<td>First Name</td>
							<td><input id="first_name" name="first_name" placeholder="First Name" class="text mini" style="height:20px;width:200px;" /></td>
							<td>Last Name</td>
							<td><input id="last_name" name="last_name" placeholder="Last Name" class="text mini" style="height:20px; width:200px;" /></td>
						</tr>
						<tr>
							<td>Email</td>
							<td><input id="email" name="email" placeholder="Email" class="text mini" style="height:20px;width:200px;" /></td>
							<td></td>
							<td><input type="submit" value="Go!" class="button" style="margin-top:6px;"></td>
						</tr>-->
					</table>
					<input type="submit" value="Go!" class="button" style="margin-top:6px;">
				</div>
				
		</div>
		<div class="clear"></div>
	  </form>
			<div id="listTable"><?php echo @$listTable; ?></div>
	</div>
	
	<script>
	$( ".show_test" ).on( "click", function() {
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
