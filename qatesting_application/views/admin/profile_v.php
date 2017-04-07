	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		
		<!-- MAIN -->
		<div class="content">
		<?php if(@validation_errors()): ?>		
			<!-- VALIDATION ALERT -->
			<div class="validation_error">
			<img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
		    <h1>Error</h1>
		    <p><?php echo validation_errors(); ?></p>
		    <div class="clear"></div>
			</div>
			<!-- END VALIDATION ALERT -->
		<?php endif; ?>
		
		<?php if(@$success): ?>
		<!-- SUCCESS MESSAGE -->
  		<div class="success">
  		  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
  	    <h1>Success</h1>
  	    <div class="clear"></div>
  	    <p>
  	      Your changes have been made.
  	    </p>
  	    <div class="clear"></div>
  		</div>
		<!-- END SUCCESS MESSAGE -->
		<?php endif; ?>
		<?php echo form_open('admin/profile', array('class' => 'form_standard')); ?>		
			<!-- EDIT PROFILE -->
			<div class="account_section">
				<h1><i class="fa fa-pencil"></i> Store Information</h1>
				<div class="hidden_table">
					
					<table width="100%" cellpadding="6">
						<tr>
							<td><b>Store Deal Percentage:</b></td>
							<td><?php echo form_input(array('name' => 'deal', 
                              'value' => @$dealPercentage, 
                              'class' => 'text large',
                              'placeholder' => 'Deal Percentage')); ?></td>
						</tr>
						<tr>
							<td><b>Store Name:</b></td>
							<td><?php echo form_input(array('name' => 'company', 
                              'value' => @$address['company'], 
                              'class' => 'text large',
                              'placeholder' => 'Store Name')); ?></td>
						</tr>
						<tr>
							<td style="width:200px"><b>Contact First Name:</b></td>
							<td><?php echo form_input(array('name' => 'first_name', 
                              'value' => @$address['first_name'], 
                              'class' => 'text large', 
                              'placeholder' => 'First Name')); ?></td>
						</tr>
						<tr>
							<td><b>Contact Last Name:</b></td>
							<td><?php echo form_input(array('name' => 'last_name', 
                              'value' => @$address['last_name'], 
                              'class' => 'text large',
                              'placeholder' => 'Last Name')); ?></td>
						</tr>
						<tr>
							<td><b>Phone:</b></td>
							<td><?php echo form_input(array('name' => 'phone', 
                              'value' => @$address['phone'], 
                              'class' => 'text large',
                              'placeholder' => 'Phone')); ?></td>
						</tr>
						<tr>
							<td><b>Email:</b></td>
							<td><?php echo form_input(array('name' => 'email', 
                              'value' => @$address['email'], 
                              'class' => 'text large',
                              'placeholder' => 'Phone')); ?></td>
						</tr>
						<tr>
							<td id="billing_street_address_label"><b>Store Address Line 1:*</b></td>
							<td><?php echo form_input(array('name' => 'street_address', 
  	                              'value' => @$address['street_address'], 
  	                              'id' => 'billing_street_address',
  	                              'class' => 'text large',
  	                              'placeholder' => 'Enter Store Address')); ?></td>
						</tr>
						<tr>
							<td id="billing_address_2_label"><b>Store Address Line 2:</b></td>
							<td><?php echo form_input(array('name' => 'address_2', 
  	                               'value' => @$address['address_2'], 
  	                               'id' => 'billing_address_2',
  	                               'class' => 'text large',
  	                               'placeholder' => 'Apt. Bld. Etc')); ?></td>
						</tr>
						<tr>
							<td id="billing_city_label"><b>Store City:*</b></td>
							<td><?php echo form_input(array('name' => 'city', 
  	                              'value' => @$address['city'], 
  	                              'id' => 'billing_city',
  	                              'placeholder' => 'Enter City', 
  	                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td id="billing_state_label"><b>Store State:*</b></td>
							<td><?php echo form_dropdown('state', $states, @$address['state'], 'id="billing_state"'); ?></td>
						</tr>
						<tr>
							<td id="billing_zip_label"><b>Store Zip:*</b></td>
							<td><?php echo form_input(array('name' => 'zip', 
  	                              'value' => @$address['zip'], 
  	                              'id' => 'billing_zip',
  	                              'class' => 'text large',
  	                              'placeholder' => 'Zipcode')); ?></td>
						</tr>
						<tr>
							<td><b>Store Country:*</b></td>
							<td><?php echo form_dropdown('country', 
							                                      $countries, 
							                                      @$address['country'], 
							                                      'id="billing_country" onChange="newChangeCountry(\'billing\');"'); ?></td>
						</tr>
							<td></td>
							<td>
								<button type="submit" class="button">Save Changes</button>
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
	<!-- END CONTENT WRAP ===================================================================-->
<script>
	function newChangeCountry(addressType)
{
  country = $('#'+addressType+'_country').val();
  currentValue = $('#'+addressType+'_state').val();
  $('#'+addressType+'_state').empty();
	if(country == 'US')
	{
	  addressDD = $.post(base_url + 'admin/load_states/1',
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
	
	if(country == 'CA')
	{
		addressDD = $.post(base_url + 'admin/load_provinces/1',
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
  
  $.post(base_url + 'admin/new_change_country',
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
