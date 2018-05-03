<style>
.tabular_data td{border:1px solid white;}
.bnz-frm input{width:270px; height:20px; padding:4px; background:#F6F6F6; border:1px solid #AAA; border-radius:3px;}
.dsply-dtl{width:45%; float:left; height:30px;}
.nts{width:100%; float:left; margin:15px 0 15px 0;}
.nts label{width:100%; display:block; font-weight:bold;}
.nts textarea{width:99%; height:80px;}
.nts input[type=submit]{float:right; margin:7px 0 0 0; padding:5px 12px;}
</style>
<div class="content_wrap" style="background:white; float:left;">
	<div class="content">
		<h1 style="padding:5px; letter-spacing:0px; font-size:26px;"><i class="fa fa-users"></i>&nbsp;Customer Details</h1>
		<div id="listTable">
			<div class="tabular_data">
			<form method="post" action="">
				<table width="45%" style="float:left; margin:0 3% 0 0;">
					<?php if(@$customer): ?>
						<tr class="billing_display">
							<td><?php echo $customer['first_name'].' '.$customer['last_name']; ?></td>
						</tr>
						<tr class="billing_display">
							<td><?php echo $customer['street_address']; ?></td>
						</tr>
						<tr class="billing_display">
							<td><?php echo $customer['address_2']; ?></td>
						</tr>
						<tr class="billing_display">
							<td><?php echo $customer['city'].' '.$customer['state'].' '.$customer['zip']; ?></td>
						</tr>
						<tr class="billing_display">
							<td>&nbsp;</td>
						</tr>
						<tr class="billing_display">
							<td><a href="mailto:<?php echo $customer['email']; ?>"><?php echo $customer['email']; ?></a></td>
						</tr>
						<tr class="billing_display">
							<td><?php echo $customer['phone']; ?></td>
						</tr>
						<tr class="billing_display">
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><button type="button" onclick="$('.billing_display').hide(); $('.billing_edit').show();">Edit Customer</button></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<!--<tr>
							<td><b>Customer Since:</b> <?php echo $customer['phone']; ?></td>
						</tr>-->
						
						<tr>
							<td>
								<div class="hidden_table billing_edit hide bnz-frm" style="border:1px solid silver;">
									<table width="100%" cellpadding="8">
										<tr>
											<td><b>Company Name:</b></td>
											<td><?php
												echo form_input(array('name' => 'company[]',
													'value' => @$customer['company'],
													'placeholder' => 'Enter Company Name',
													'id' => 'billing_company',
													'class' => 'text large'));
												?>
											</td>
										</tr>
										<tr>
											<td><b>First Name:*</b></td>
											<td><?php
												echo form_input(array('name' => 'first_name[]',
													'value' => @$customer['first_name'],
													'id' => 'billing_first_name',
													'placeholder' => 'Enter First Name',
													'class' => 'text large'));
												?></td>
										</tr>
										<tr>
											<td><b>Last Name:*</b></td>
											<td><?php
												echo form_input(array('name' => 'last_name[]',
													'value' => @$customer['last_name'],
													'id' => 'billing_last_name',
													'class' => 'text large',
													'placeholder' => 'Enter Last Name'));
												?></td>
										</tr>
										<tr>
											<td><b>Email Address:*</b></td>
											<td><?php
												echo form_input(array('name' => 'email[]',
													'value' => @$customer['email'],
													'id' => 'billing_email',
													'placeholder' => 'Enter Email Address',
													'class' => 'text large'));
												?></td>
										</tr>
										<tr>
											<td><b>Phone:</b></td>
											<td><?php
												echo form_input(array('name' => 'phone[]',
													'value' => @$customer['phone'],
													'id' => 'billing_phone',
													'placeholder' => 'Enter Phone Number',
													'class' => 'text large'));
												?></td>
										</tr>
										<tr>
											<td id="billing_street_address_label"><b>Address Line 1:*</b></td>
											<td><?php
												echo form_input(array('name' => 'street_address[]',
													'value' => @$customer['street_address'],
													'id' => 'billing_street_address',
													'class' => 'text large',
													'placeholder' => 'Enter Address'));
												?></td>
										</tr>
										<tr>
											<td id="billing_address_2_label"><b>Address Line 2:</b></td>
											<td><?php
												echo form_input(array('name' => 'address_2[]',
													'value' => @$customer['address_2'],
													'id' => 'billing_address_2',
													'class' => 'text large',
													'placeholder' => 'Apt. Bld. Etc'));
												?></td>
										</tr>
										<tr>
											<td id="billing_city_label"><b>City:*</b></td>
											<td><?php
												echo form_input(array('name' => 'city[]',
													'value' => @$customer['city'],
													'id' => 'billing_city',
													'placeholder' => 'Enter City',
													'class' => 'text large'));
												?></td>
										</tr>
										<tr>
											<td id="billing_state_label"><b>State:*</b></td>
											<td><?php echo form_dropdown('state[]', $states, @$customer['state'], 'id="billing_state"'); ?></td>
										</tr>
										<tr>
											<td id="billing_zip_label"><b>Zip:*</b></td>
											<td><?php
												echo form_input(array('name' => 'zip[]',
													'value' => @$customer['zip'],
													'id' => 'billing_zip',
													'class' => 'text large',
													'placeholder' => 'Zipcode'));
												?></td>
										</tr>
										<tr>
											<td><b>Country:*</b></td>
											<td>
												<?php
												echo form_dropdown('country[]', $countries, @$customer['country'], 'id="billing_country" onChange="newChangeCountry(\'billing\');"');
												?>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td>
							<div class="nts">
								<label>Notes</label>
								<textarea name="notes"><?php echo $customer['notes'];?></textarea>
								<input type="submit" name="submit" value="Save">
							</div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="clndr">
							<?php echo $calendar;?>
						</td>
					</tr>
				</table>
				<div style="width:10px;"></div>
				<div class="dsply-dtl">
					<p>Displaying <strong> <?php echo count($customer['orders']);?> </strong> - <strong> <?php echo count($customer['orders']);?> </strong> of <strong> <?php count($customer['orders']);?> </strong> Order(s)</p>
				</div>
				<table width="45%" cellpadding="10" style="float:left; border:1px solid silver; border-collapse:separate;">
					<tr class="head_row">
						<td><b>Order #</b></td>
						<td><b>Order Date</b></td>
						<td><b>Total</b></td>
						<td><b>Status</b></td>
					</tr>
					<?php if(@$customer['orders']):
						foreach($customer['orders'] as $order): ?>
							<tr>
								<td><a href="<?php echo base_url('admin/order_edit/'.$order['order_id']); ?>"><?php echo $order['order_id']; ?></a></td>
								<td><?php echo ($order['order_date']) ?  date('m/d/Y', $order['order_date']) : $order['processed_date']; ?></td>
								<td>$<?php echo $order['sales_price'] + $order['shipping'] + $order['tax']; ?></td>
								<td><?php echo $order['status'] ? $order['status'] : 'Pending'; ?></td>
							</tr>
					<?php endforeach;
					endif; ?>
				</table>
				</form>

				<?php if (ENABLE_CUSTOMER_PRICING) {
					$CI =& get_instance();
					?>
					<div style="width: 45%">
					<?php echo $CI->load->view("admin/customer/backbone_customerpricing_widget", array("user_id" => $user_id), true); ?>
					</div>
					<?php
				} ?>

			</div>
		</div>
	</div>
</div>
<div class="cstm-pup"></div>
<script>
$(document).on('click', '.prev, .next', function() {
	$('#loading-background').show();
	var mnth = $(this).data('month');
	var year = $(this).data('year');
	if( $(this).hasClass('prev') ) {
		mnth = parseInt(mnth)-1;
		if( mnth == 0 ) {
			mnth = 12;
			year = parseInt(year)-1;
		}
	} else if( $(this).hasClass('next') ) {
		mnth = parseInt(mnth)+1;
		if( mnth == 13 ) {
			mnth = 1;
			year = parseInt(year)+1;
		}
	}
	var ajax_url = "<?php echo site_url('admin/getCalendarCustomer/');?>/"+mnth+'/'+year+"/<?php echo $user_id;?>"+'/true';
	$.post( ajax_url, {}, function( result ){
		$('.clndr').html(result);
		$('#loading-background').hide();
		//alert(result);
	});
	//alert("Pradeep Clicked");
});

$(document).on('click', '.calendar-day', function() {
	if( !$(this).hasClass('childOpened') ) {
		$('#loading-background').show();
		var dt = $(this).data('dt');
		var ajax_url = "<?php echo site_url('admin/getReminderPopUpCustomer/');?>";
		$.post( ajax_url, {'dt':dt, 'user_id': "<?php echo $user_id;?>"}, function( result ){
			$('.cstm-pup').html(result);
			$('#loading-background').hide();
		});
	}
});
$(document).on('click', '.day-rem', function() {
	var _this = $(this);
	$(this).parent().addClass('childOpened');
	$('#loading-background').show();
	var dt = $(this).data('dt');
	var id = $(this).data('id');
	var ajax_url = "<?php echo site_url('admin/getReminderPopUpCustomer/');?>/"+id;
	$.post( ajax_url, {'dt':dt, 'user_id': "<?php echo $user_id;?>"}, function( result ){
		_this.parent().removeClass('childOpened');
		$('.cstm-pup').html(result);
		$('#loading-background').hide();
	});
});
$(document).on('click', '.clspopup', function() {
	$('.cstm-pup').html('');
});
$(document).on('click', '.svcls', function() {
	$('#reminder_form').submit();
});
$(document).on('click', '.dlt', function() {
	if( confirm("Are you sure you want to delete this event")) {
		var id = $(this).data('id');
		var user = $(this).data('user');
		window.location.href = "<?php echo site_url('admin/deleteReminderPopUpCustomer/');?>/"+id+'/'+user;
	}
});
</script>