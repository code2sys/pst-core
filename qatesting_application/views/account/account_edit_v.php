	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- SIDEBAR -->
		<div class="sidebar">
			<div class="acct_nav_wrap">
			<div class="acct_nav_content">
				<div class="acct_menu-link">
					<a href="<?php echo base_url(); ?>" style="color:#333;">
						<i class="fa fa-navicon"></i> <b>Account Settings</b>
					</a>
				</div>
				<div id="acct_menu" class="acct_menu">
					<ul>
		      	<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>"><i class="fa fa-user"></i> My Profile</a></li>
		      	<li><a href="<?php echo $s_baseURL.'checkout/account_edit'; ?>"><i class="fa fa-pencil"></i> Edit Profile</a></li>
		      	<li><a href="<?php echo base_url('shopping/cart'); ?>"><i class="fa fa-shopping-cart"></i> Shopping Cart</a></li>
		      	<li><a href="<?php echo $s_baseURL.'checkout/account_address'; ?>"><i class="fa fa-book"></i> Saved Addresses</a></li>
		      	<li><a href="<?php echo base_url('/shopping/wishlist'); ?>"><i class="fa fa-heart"></i> Wishlist</a></li>
		      	<li><a href="<?php echo $s_baseURL.'checkout/account_order'; ?>"><i class="fa fa-inbox"></i> Order History</a></li>
		      	<?php if($_SESSION['userRecord']['admin']): ?>
		      		<li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-gears"></i> Admin panel</a></li>
				<?php endif; ?>
		      	<li><a href="<?php echo $s_baseURL.'welcome/logout'; ?>"><i class="fa fa-sign-out"></i> Logout</a></li>
					</ul>
				</div>
			</div>
			</div>
			
		</div>
		<!-- END SIDEBAR -->	
		
		
		<!-- MAIN -->
		<div class="main_content">
		
		<?php if(@validation_errors()): ?>		
			<!-- VALIDATION ALERT -->
			<div class="validation_error">
			<img src="<?php echo $s_assets; ?>/images/error.png" style="float:left;margin-right:10px;">
		    <h1>Error</h1>
		    <p><?php echo validation_errors(); ?></p>
		    <div class="clear"></div>
			</div>
			<!-- END VALIDATION ALERT -->
		<?php endif; ?>

		<?php echo form_open($s_baseURL.'checkout/account_edit', array('class' => 'form_standard')); ?>		
			<!-- EDIT PROFILE -->
			<div class="account_section">
				<h1><i class="fa fa-pencil"></i> Edit Profile</h1>
				<div class="hidden_table">
					<table width="100%" cellpadding="6">
						<tr>
							<td colspan="2">
								<h3 style="margin:0;">Login Details</h3>
							</td>
						</tr>
						<tr>
							<td style="width:120px;"><b>Username:</b></td>
							<td><?php echo @$_SESSION['userRecord']['username']; ?></td>
						</tr>
						<tr>
							<td><b>Change Password:</b></td>
							<td><?php echo form_password(array('name' => 'password', 
																                                'value' =>'', 
																                                'class' => 'text large', 
																                                'placeholder' => 'Password')); ?></td>
						</tr>
						<tr>
							<td><b>Confirm Password:</b></td>
							<td><?php echo form_password(array('name' => 'conf_password', 
																                                'value' =>'', 
																                                'class' => 'text large', 
																                                'placeholder' => 'Confirm Password')); ?></td>
						</tr>
					</table>
					
					<div class="divider"></div>
					
					<table width="100%" cellpadding="6">
						<tr>
							<td colspan="2">
								<h3 style="margin:0;">Billing Details</h3>
							</td>
						</tr>
						<tr>
							<td><b>Company:</b></td>
							<td><?php echo form_input(array('name' => 'company', 
                              'value' => @$billingRecord['company'], 
                              'class' => 'text large',
                              'placeholder' => 'Company')); ?></td>
						</tr>
						<tr>
							<td style="width:120px"><b>First Name:</b></td>
							<td><?php echo form_input(array('name' => 'first_name', 
                              'value' => @$billingRecord['first_name'], 
                              'class' => 'text large', 
                              'placeholder' => 'First Name')); ?></td>
						</tr>
						<tr>
							<td><b>Last Name:</b></td>
							<td><?php echo form_input(array('name' => 'last_name', 
                              'value' => @$billingRecord['last_name'], 
                              'class' => 'text large',
                              'placeholder' => 'Last Name')); ?></td>
						</tr>
						<tr>
							<td><b>Phone:</b></td>
							<td><?php echo form_input(array('name' => 'phone', 
                              'value' => @$billingRecord['phone'], 
                              'class' => 'text large',
                              'placeholder' => 'Phone')); ?></td>
						</tr>
						<tr>
							<td><b>Email:</b></td>
							<td><?php echo form_input(array('name' => 'email', 
                              'value' => @$billingRecord['email'], 
                              'class' => 'text large',
                              'placeholder' => 'Phone')); ?></td>
						</tr>
						<tr>
							<td id="billing_street_address_label"><b>Address Line 1:*</b></td>
							<td><?php echo form_input(array('name' => 'street_address', 
  	                              'value' => @$billingRecord['street_address'], 
  	                              'id' => 'billing_street_address',
  	                              'class' => 'text large',
  	                              'placeholder' => 'Enter Address')); ?></td>
						</tr>
						<tr>
							<td id="billing_address_2_label"><b>Address Line 2:</b></td>
							<td><?php echo form_input(array('name' => 'address_2', 
  	                               'value' => @$billingRecord['address_2'], 
  	                               'id' => 'billing_address_2',
  	                               'class' => 'text large',
  	                               'placeholder' => 'Apt. Bld. Etc')); ?></td>
						</tr>
						<tr>
							<td id="billing_city_label"><b>City:*</b></td>
							<td><?php echo form_input(array('name' => 'city', 
  	                              'value' => @$billingRecord['city'], 
  	                              'id' => 'billing_city',
  	                              'placeholder' => 'Enter City', 
  	                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td id="billing_state_label"><b>State:*</b></td>
							<td><?php echo form_dropdown('state', $states, @$billingRecord['state'], 'id="billing_state"'); ?></td>
						</tr>
						<tr>
							<td id="billing_zip_label"><b>Zip:*</b></td>
							<td><?php echo form_input(array('name' => 'zip', 
  	                              'value' => @$billingRecord['zip'], 
  	                              'id' => 'billing_zip',
  	                              'class' => 'text large',
  	                              'placeholder' => 'Zipcode')); ?></td>
						</tr>
						<tr>
							<td><b>Country:*</b></td>
							<td><?php echo form_dropdown('country', 
							                                      $countries, 
							                                      $billingRecord['country'], 
							                                      'id="billing_country" onChange="newChangeCountry(\'billing\');"'); ?></td>
						</tr>
							<td></td>
							<td>
								<button type="submit" class="button">Save Changes</button>
								<a href="<?php echo $s_baseURL.'checkout/account'; ?>" class="button">Cancel</button>
							</td>
						</tr>
					</table>
					
					</form>
				</div>
			</div>
			<!-- END EDIT PROFILE -->
			
		</div>
		<!-- END MAIN -->		
		
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
<script>
	function newChangeCountry(addressType)
{
  country = $('#'+addressType+'_country').val();
  currentValue = $('#'+addressType+'_state').val();
  $('#'+addressType+'_state').empty();
	if(country == 'USA')
	{
	  addressDD = $.post(s_base_url + 'checkout/load_states/1',
		{},
		function(returnData)
		{
		  var dataArr = jQuery.parseJSON(returnData);
      var html = '';
      $.each(dataArr, function(i, value) 
      {
        if(currentValue == i)
          html += '<option selected="selected" value="' + i + '">' + value + '</option>';
        else
        html += '<option value="' + i + '">' + value + '</option>';
      })
      $('#'+addressType+'_state').append(html);

		});
	}
	
	if(country == 'Canada')
	{
		addressDD = $.post(s_base_url + 'checkout/load_provinces/1',
		{},
		function(returnData)
		{
      var dataArr = jQuery.parseJSON(returnData);
      var html = '';
      $.each(dataArr, function(i, value) 
      {
        html += '<option value="' + i + '">' + value + '</option>';
      })
      $('#'+addressType+'_state').append(html);

		});
	}
  
  $.post(s_base_url + 'checkout/new_change_country',
	{
	  'country' : country
	},
	function(returnData)
	{
    var dataArr = jQuery.parseJSON(returnData);
    $('#'+addressType+'_street_address_label').html(dataArr.street_address);
    $('#'+addressType+'_address_2_label').html(dataArr.address_2);
    $('#'+addressType+'_city_label').html(dataArr.city);
    $('#'+addressType+'_state_label').html(dataArr.state);
    $('#'+addressType+'_zip_label').html(dataArr.zip);

	});

}

</script>	
