<?php  
setlocale(LC_MONETARY, 'en_US'); 
?>
	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- CART -->
		<div class="main_content_full">
		
		  <div style="float:left;">
			  <h1>Check Out</h1>
        <h3>Please Fill Out The Forms Below</h3>
        <p>All fields are required.</p>
		  </div>

			<div class="clear"></div>
				<!-- VALIDATION ALERT -->
				<?php if(@$message): ?>
				<div class="validation_error">
			    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
			    <h1>Error</h1>
			    <div class="clear"></div>
			    <p><?php echo @$message; ?></p>
			    
				</div>
				<?php endif; ?>
				<!-- END VALIDATION ALERT -->			
				<!-- VALIDATION ALERT -->
				<?php if(@$this->session->flashdata('validation')): ?>
				<div class="validation_error">
			    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
			    <h1>Error</h1>
			    <div class="clear"></div>
			    <p><?php echo @$this->session->flashdata('validation') ?></p>
				</div>
				 <?php endif; ?>
				<!-- END VALIDATION ALERT -->	
			
			<br>
			
			
			<?php echo form_open($s_baseURL.'checkout/process_client_info', array('class' => 'form_standard', 'id' => 'client_info')); ?>
			<!-- BILLING DETAILS -->
			<div class="cart_wrap_left">
				<h3 style="float:left;margin:5px 0 0;">
					<i class="fa fa-list"></i> 2. Billing Details
				</h3>
				<div class="clear"></div>
				<?php echo form_dropdown('billing_address_change', $billing_addresses, 0, 'id="billing_address_selector" onChange="changeBillingAddress();"'); ?>
				<br>
				<p>Field marked with a * are required</p>
				<div class="hidden_table">
					<input name="guest" type="hidden" value="<?php echo @$_GET['g'];?>" />
					<table width="100%" cellpadding="6">
						<tr>
							<td><b>Company Name:</b></td>
							<td><?php echo form_input(array('name' => 'company[]', 
							                              'value' =>  @$value['company'][0] ? @$value['company'][0] : @$billing['company'], 
							                              'placeholder' => 'Enter Company Name',
							                              'id' => 'billing_company',
							                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td><b>First Name:*</b></td>
							<td><?php echo form_input(array('name' => 'first_name[]', 
							                              'value' => @$value['first_name'][0] ?@$value['first_name'][0] : @$billing['first_name'], 
							                              'id' => 'billing_first_name',
							                              'placeholder' => 'Enter First Name', 
							                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td><b>Last Name:*</b></td>
							<td><?php echo form_input(array('name' => 'last_name[]', 
							                              'value' => @$value['last_name'][0] ? @$value['last_name'][0] : @$billing['last_name'], 
							                              'id' => 'billing_last_name',
							                              'class' => 'text large',
							                              'placeholder' => 'Enter Last Name')); ?></td>
						</tr>
						<tr>
							<td><b>Email Address:*</b></td>
							<td><?php echo form_input(array('name' => 'email[]', 
							                              'value' => @$value['email'][0] ? @$value['email'][0] : @$billing['email'], 
							                              'id' => 'billing_email',
							                              'placeholder' => 'Enter Email Address',
							                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td><b>Phone:*</b></td>
							<td><?php echo form_input(array('name' => 'phone[]', 
							                              'value' => @$value['phone'][0] ? @$value['phone'][0] : @$billing['phone'], 
							                              'id' => 'billing_phone',
							                              'placeholder' => 'Enter Phone Number',
							                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td id="billing_street_address_label"><b>Address Line 1:*</b></td>
							<td><?php echo form_input(array('name' => 'street_address[]', 
  	                              'value' => @$billing['street_address'], 
  	                              'id' => 'billing_street_address',
  	                              'class' => 'text large',
  	                              'placeholder' => 'Enter Address')); ?></td>
						</tr>
						<tr>
							<td id="billing_address_2_label"><b>Address Line 2:</b></td>
							<td><?php echo form_input(array('name' => 'address_2[]', 
  	                               'value' => @$billing['address_2'], 
  	                               'id' => 'billing_address_2',
  	                               'class' => 'text large',
  	                               'placeholder' => 'Apt. Bld. Etc')); ?></td>
						</tr>
						<tr>
							<td id="billing_city_label"><b>City:*</b></td>
							<td><?php echo form_input(array('name' => 'city[]', 
  	                              'value' => @$billing['city'], 
  	                              'id' => 'billing_city',
  	                              'placeholder' => 'Enter City', 
  	                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td id="billing_state_label"><b>State:*</b></td>
							<td><?php echo form_dropdown('state[]', $states, @$billing['state'], 'id="billing_state"'); ?></td>
						</tr>
						<tr>
							<td id="billing_zip_label"><b>Zip:*</b></td>
							<td><?php echo form_input(array('name' => 'zip[]', 
  	                              'value' => @$billing['zip'], 
  	                              'id' => 'billing_zip',
  	                              'class' => 'text large',
  	                              'placeholder' => 'Zipcode')); ?></td>
						</tr>
						<tr>
							<td><b>Country:*</b></td>
							<td><?php echo form_dropdown('country[]', 
							                                      $countries, 
							                                      (@$value['country'][1] ? @$value['country'][1] : @$billing['country']), 
							                                      'id="billing_country" onChange="newChangeCountry(\'billing\');"'); ?></td>
						</tr>
					</table>
				</div>
			</div>
			<!-- END BILLING DETAILS -->
			
			<!-- SHIPPING DETAILS -->
			<div class="cart_wrap_right">
				<h3 style="float:left;margin:5px 0 0;">
					<i class="fa fa-home"></i> 3. Shipping Details
				</h3>
				<p style="float:right;"><b>Same As Billing</b><?php echo form_checkbox('auto_pop_bill_to_ship', 1, 0, 'onclick="populateShipping();"'); ?></p>
				<div class="clear"></div>
				<?php echo form_dropdown('shipping_address_change', $shipping_addresses, 0, 'id="shipping_address_selector" onChange="changeShippingAddress()"' ); ?>
				<br>
				<p>Field marked with a * are required</p>
				<div class="hidden_table">
					<table width="100%" cellpadding="6">
						<tr>
							<td><b>Company Name:</b></td>
							<td><?php echo form_input(array('name' => 'company[]', 
							                              'value' => @$value['company'][1] ? @$value['company'][1] : @$shipping['company'], 
							                              'id' => 'shipping_company',
							                              'placeholder' => 'Company',
							                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td><b>First Name:*</b></td>
							<td><?php echo form_input(array( 'name' => 'first_name[]',
															                              'value' => @$value['first_name'][1] ? @$value['first_name'][1] : @$shipping['first_name'], 
															                              'id' => 'shipping_first_name',
															                              'placeholder' => 'Enter First Name', 
															                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td><b>Last Name:*</b></td>
							<td><?php echo form_input(array('name' => 'last_name[]', 
							                              'value' => @$value['last_name'][1] ? @$value['last_name'][1] : @$shipping['last_name'], 
							                              'id' => 'shipping_last_name',
							                              'class' => 'text large',
							                              'placeholder' => 'Enter Last Name')); ?></td>
						</tr>
						<tr>
							<td><b>Email Address:*</b></td>
							<td><?php echo form_input(array('name' => 'email[]', 
							                              'value' => @$value['email'][1] ? @$value['email'][1] : @$shipping['email'], 
							                              'id' => 'shipping_email',
							                              'placeholder' => 'Enter Email Address',
							                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td><b>Phone:*</b></td>
							<td><?php echo form_input(array('name' => 'phone[]', 
							                              'value' => @$value['phone'][1] ? @$value['phone'][1] : @$shipping['phone'], 
							                              'id' => 'shipping_phone',
							                              'placeholder' => 'Enter Phone Number',
							                              'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td id="shipping_street_address_label"><b>Address Line 1:*</b></td>
							<td><?php echo form_input(array('name' => 'street_address[]', 
                                            'value' =>  @$shipping['street_address'], 
                                            'id' => 'shipping_street_address',
                                            'class' => 'text large',
                                            'placeholder' => 'Enter Address')); ?></td>
						</tr>
						<tr>
							<td id="shipping_address_2_label"><b>Address Line 2:</b></td>
							<td><?php echo form_input(array('name' => 'address_2[]', 
                                           'value' => @$shipping['address_2'], 
                                           'id' => 'shipping_address_2',
                                           'class' => 'text large',
                                           'placeholder' => 'Apt. Bld. Etc')); ?></td>
						</tr>
						<tr>
							<td id="shipping_city_label"><b>City:*</b></td>
							<td><?php echo form_input(array('name' => 'city[]', 
                                            'value' =>  @$shipping['city'], 
                                            'id' => 'shipping_city',
                                            'placeholder' => 'Enter City', 
                                            'class' => 'text large')); ?></td>
						</tr>
						<tr>
							<td id="shipping_state_label"><b>State:*</b></td>
							<td><?php echo form_dropdown('state[]', $states, @$shipping['state'], 'id="shipping_state"'); ?></td>
						</tr>
						<tr>
							<td id="shipping_zip_label"><b>Zip:*</b></td>
							<td><?php echo form_input(array('name' => 'zip[]', 
                                            'value' => @$shipping['zip'], 
                                            'id' => 'shipping_zip',
                                            'class' => 'text large',
                                            'placeholder' => 'Enter Zipcode')); ?></td>
						</tr>
						<tr>
							<td><b>Country:*</b></td>
							<td><?php echo form_dropdown('country[]', 
							                                      $countries, 
							                                      (@$value['country'][1] ? @$value['country'][1] : @$shipping['country']), 
							                                      'id="shipping_country" onChange="newChangeCountry(\'shipping\');"'); ?></td>
						</tr>
					</table>
				</div>
			</div>
			
			<div class="clear"></div>
			<!-- END SHIPPING DETAILS -->
			<div class="cart_wrap">
			<div style="float:left; margin:10px"><b>Special Instructions:</b></div> <?php echo form_textarea(array('name' => 'special_instr', 
                          			                                                      'value' => set_value('special_instr'),
                          			                                                      'style' => 'height:50px; width:80%;')); ?>
							<div class="clear"></div>
			</div>
			
			<input type="submit" value="Next >" class="input_button_purple" style="float:right;">
			
			</form>
			
			<div class="clear"></div>
			</br>
		</div>
		<!-- END CHECK OUT -->
		
	</div>
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
	
<?php if(!@$this->session->flashdata('values')): ?>
<script>


  updateContact($('#shipping_id').val());
  
  $('#client_info').submit(function(){
    $('input[type=submit]', this).attr('disabled', 'disabled');
});

function updateContact(id)
{
  
  if($.isNumeric(id))
  {
    
    $.post(base_url + 'ajax/get_contact_info/' + id,
		{},
		function(returnData)
		{
      var dataArr = jQuery.parseJSON(returnData);
      if($('#shipping_country').val() != dataArr.country)
      {
        $('#shipping_country').val(dataArr.country);
        newChangeCountry('shipping');
        setTimeout(function()
        {
          $('#shipping_first_name').val(dataArr.first_name);
          $('#shipping_last_name').val(dataArr.last_name);
          $('#shipping_street_address').val(dataArr.street_address);
          $('#shipping_address_2').val(dataArr.address_2);
          $('#shipping_city').val(dataArr.city);
          $('#shipping_state').val(dataArr.state);
          $('#shipping_zip').val(dataArr.zip);
          $('#shipping_email').val(dataArr.email);
          $('#shipping_phone').val(dataArr.phone);
          $('#shipping_fax').val(dataArr.fax);
          $('#shipping_company').val(dataArr.company);
        }, 100);
      }
      else
      {
        $('#shipping_first_name').val(dataArr.first_name);
        $('#shipping_last_name').val(dataArr.last_name);
        $('#shipping_street_address').val(dataArr.street_address);
        $('#shipping_address_2').val(dataArr.address_2);
        $('#shipping_city').val(dataArr.city);
        $('#shipping_state').val(dataArr.state);
        $('#shipping_zip').val(dataArr.zip);
        $('#shipping_email').val(dataArr.email);
        $('#shipping_phone').val(dataArr.phone);
        $('#shipping_fax').val(dataArr.fax);
        $('#shipping_company').val(dataArr.company);
      }
      
		});
  }
}

function populateShipping()
{
    $('#shipping_id').val('default');
    $('#shipping_first_name').val($('#billing_first_name').val());
    $('#shipping_last_name').val($('#billing_last_name').val());
    $('#shipping_street_address').val($('#billing_street_address').val());
    $('#shipping_address_2').val($('#billing_address_2').val());
    $('#shipping_city').val($('#billing_city').val());
    $('#shipping_state').val($('#billing_state').val());
    $('#shipping_zip').val($('#billing_zip').val());
    $('#shipping_email').val($('#billing_email').val());
    $('#shipping_phone').val($('#billing_phone').val());
    $('#shipping_fax').val($('#billing_fax').val());
    $('#shipping_company').val($('#billing_company').val());
}

function newChangeCountry(addressType)
{
  country = $('#'+addressType+'_country').val();
  currentValue = $('#'+addressType+'_state').val();
  $('#'+addressType+'_state').empty();
	if(country == 'USA')
	{
	  addressDD = $.post(base_url + 'checkout/load_states/1',
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
		addressDD = $.post(base_url + 'checkout/load_provinces/1',
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
  
  $.post(base_url + 'checkout/new_change_country',
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

function changeBillingAddress()
{
	contactId = $('#billing_address_selector').val();
	 $.post(base_url + 'ajax/get_contact_info/' + contactId,
		{},
		function(returnData)
		{
			var dataArr = jQuery.parseJSON(returnData);
		      if($('#billing_country').val() != dataArr.country)
		      {
		        $('#billing_country').val(dataArr.country);
		        newChangeCountry('billing');
		        setTimeout(function()
		        {
		          $('#billing_first_name').val(dataArr.first_name);
		          $('#billing_last_name').val(dataArr.last_name);
		          $('#billing_street_address').val(dataArr.street_address);
		          $('#billing_address_2').val(dataArr.address_2);
		          $('#billing_city').val(dataArr.city);
		          $('#billing_state').val(dataArr.state);
		          $('#billing_zip').val(dataArr.zip);
		          $('#billing_email').val(dataArr.email);
		          $('#billing_phone').val(dataArr.phone);
		          $('#billing_fax').val(dataArr.fax);
		          $('#billing_company').val(dataArr.company);
		        }, 100);
		      }
		      else
		      {
		        $('#billing_first_name').val(dataArr.first_name);
		        $('#billing_last_name').val(dataArr.last_name);
		        $('#billing_street_address').val(dataArr.street_address);
		        $('#billing_address_2').val(dataArr.address_2);
		        $('#billing_city').val(dataArr.city);
		        $('#billing_state').val(dataArr.state);
		        $('#billing_zip').val(dataArr.zip);
		        $('#billing_email').val(dataArr.email);
		        $('#billing_phone').val(dataArr.phone);
		        $('#billing_fax').val(dataArr.fax);
		        $('#billing_company').val(dataArr.company);
		      }
      
		});

	
}

function changeShippingAddress()
{
	contactId = $('#shipping_address_selector').val();
	 $.post(base_url + 'ajax/get_contact_info/' + contactId,
		{},
		function(returnData)
		{
			var dataArr = jQuery.parseJSON(returnData);
		      if($('#shipping_country').val() != dataArr.country)
		      {
		        $('#shipping_country').val(dataArr.country);
		        newChangeCountry('shipping');
		        setTimeout(function()
		        {
		          $('#shipping_first_name').val(dataArr.first_name);
		          $('#shipping_last_name').val(dataArr.last_name);
		          $('#shipping_street_address').val(dataArr.street_address);
		          $('#shipping_address_2').val(dataArr.address_2);
		          $('#shipping_city').val(dataArr.city);
		          $('#shipping_state').val(dataArr.state);
		          $('#shipping_zip').val(dataArr.zip);
		          $('#shipping_email').val(dataArr.email);
		          $('#shipping_phone').val(dataArr.phone);
		          $('#shipping_fax').val(dataArr.fax);
		          $('#shipping_company').val(dataArr.company);
		        }, 100);
		      }
		      else
		      {
		        $('#shipping_first_name').val(dataArr.first_name);
		        $('#shipping_last_name').val(dataArr.last_name);
		        $('#shipping_street_address').val(dataArr.street_address);
		        $('#shipping_address_2').val(dataArr.address_2);
		        $('#shipping_city').val(dataArr.city);
		        $('#shipping_state').val(dataArr.state);
		        $('#shipping_zip').val(dataArr.zip);
		        $('#shipping_email').val(dataArr.email);
		        $('#shipping_phone').val(dataArr.phone);
		        $('#shipping_fax').val(dataArr.fax);
		        $('#shipping_company').val(dataArr.company);
		      }
      
		});

	
}


</script>
<?php endif; ?>