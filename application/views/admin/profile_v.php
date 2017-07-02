<?php
$google_trust = (array) json_decode($address['google_trust']);
?>
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
		<?php echo form_open('admin/profile', array('class' => 'form_standard', 'enctype' => 'multipart/form-data')); ?>
			<!-- EDIT PROFILE -->
			<div class="account_section">
				<h1><i class="fa fa-pencil"></i> Store Information</h1>
				<div class="hidden_table">
					
					<table width="100%" cellpadding="6">
						<!--<tr>
							<td><b>Store Deal Percentage:</b></td>
							<td><?php echo form_input(array('name' => 'deal', 
                              'value' => @$dealPercentage, 
                              'class' => 'text large',
                              'placeholder' => 'Deal Percentage')); ?></td>
						</tr>-->
						<tr>
							<td><b>Store Name:</b></td>
							<td style="width:85%;"><?php echo form_input(array('name' => 'company', 
                              'value' => @$address['company'], 
                              'class' => 'text large',
                              'placeholder' => 'Store Name')); ?></td>
						</tr>
						<!--<tr>
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
						</tr>-->
						<tr>
							<td><b>Phone:*</b></td>
							<td><?php echo form_input(array('name' => 'phone', 
                              'value' => @$address['phone'], 
                              'class' => 'text large',
                              'placeholder' => 'Phone')); ?></td>
						</tr>
						<tr>
							<td><b>Email:*</b></td>
							<td><?php echo form_input(array('name' => 'email', 
                              'value' => @$address['email'], 
                              'class' => 'text large',
                              'placeholder' => 'Email')); ?></td>
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
						<tr>
							<td><b>Sales Email:</b></td>
							<td><?php echo form_input(array('name' => 'sales_email', 
                              'value' => @$address['sales_email'], 
                              'class' => 'text large',
                              'placeholder' => 'Store Email')); ?></td>
						</tr>
						<tr>
							<td><b>Service Email:</b></td>
							<td><?php echo form_input(array('name' => 'service_email', 
                              'value' => @$address['service_email'], 
                              'class' => 'text large',
                              'placeholder' => 'Service Email')); ?></td>
						</tr>
						<tr>
							<td><b>Finance Email:</b></td>
							<td><?php echo form_input(array('name' => 'finance_email', 
                              'value' => @$address['finance_email'], 
                              'class' => 'text large',
                              'placeholder' => 'Finance Email')); ?></td>
						</tr>
						<tr>
							<td><strong>Logo:</strong><br/><em>The logo should usually be 200px wide. Please provide as GIF, JPG, or PNG format.</em></td>
							<td>
								<?php
								$file = STORE_DIRECTORY . "/html/logo.png";
								if (file_exists($file)) {
									?>
									<em>Existing Logo:</em> </br>
									<a href="/logo.png?time=<?php echo time(); ?>" download="logo.png"><img src="/logo.png?time=<?php echo time(); ?>" /></a>
									<br/>
									<br/>
									<em>Upload a New Logo:</em><br/>
									<?php
								}
								?>
								<input type="file" accept="image/*" name="logo"/>
							</td>
						</tr>
						<tr>
							<td><strong>Favicon:</strong><br/><em>The favicon should be a square, e.g. 64x64 pixels. Please provide as GIF, JPG, PNG, or ICO format.</em></td>
							<td>
								<?php
									$file = STORE_DIRECTORY . "/html/favicon.ico";
									if (file_exists($file)) {
										?>
										<em>Existing Favicon:</em> </br>
										<a href="/favicon.ico?time=<?php echo time(); ?>" download="favicon.ico"><img src="/favicon.ico?time=<?php echo time(); ?>" /></a>
										<br/>
										<br/>
										<em>Upload a New Favicon:</em><br/>
										<?php
									}
								?>
								<input type="file" accept="image/*" name="favicon"/>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="https://s3.amazonaws.com/braintree-badges/braintree-badge-wide-dark.png" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Merchant ID:</b></td>
										<td><?php echo form_input(array('name' => 'merchant_id', 
										  'value' => @$address['merchant_id'], 
										  'class' => 'text large',
										  'placeholder' => 'Merchant ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Public Key:</b></td>
										<td><?php echo form_input(array('name' => 'public_key', 
										  'value' => @$address['public_key'], 
										  'class' => 'text large',
										  'placeholder' => 'Public Key')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Private Key:</b></td>
										<td><?php echo form_input(array('name' => 'private_key', 
										  'value' => @$address['private_key'], 
										  'class' => 'text large',
										  'placeholder' => 'Private Key')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Environment:</b></td>
										<td><?php echo form_input(array('name' => 'environment', 
										  'value' => @$address['environment'], 
										  'class' => 'text large',
										  'placeholder' => 'Environment')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="<?php echo base_url().'assets/benz_assets/gtrust.png';?>" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>ID:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[id]', 
										  'value' => @$google_trust['id'], 
										  'class' => 'text large',
										  'placeholder' => 'ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Badge Position:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[badge_position]', 
										  'value' => @$google_trust['badge_position'], 
										  'class' => 'text large',
										  'placeholder' => 'Badge Position')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Locale:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[locale]', 
										  'value' => @$google_trust['locale'], 
										  'class' => 'text large',
										  'placeholder' => 'Locale')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Base Subaccount ID:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[google_base_subaccount_id]', 
										  'value' => @$google_trust['google_base_subaccount_id'], 
										  'class' => 'text large',
										  'placeholder' => 'Google Base Subaccount ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Base Country:</b></td>
										<td><?php echo form_input(array('name' => 'google_trust[google_base_country]', 
										  'value' => @$google_trust['google_base_country'], 
										  'class' => 'text large',
										  'placeholder' => 'Google Base Country')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="<?php echo base_url().'assets/benz_assets/remarketing.jpg';?>" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Conversion ID :</b></td>
										<td><?php echo form_input(array('name' => 'google_conversion_id', 
										  'value' => @$address['google_conversion_id'], 
										  'class' => 'text large',
										  'placeholder' => 'ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Conversion Label :</b></td>
										<td><?php echo form_input(array('name' => 'google_conversion_label',
										  'value' => @$address['google_conversion_label'],
										  'class' => 'text large',
										  'placeholder' => 'Label')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="<?php echo base_url().'assets/benz_assets/analytics.png';?>" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Analytics ID :</b></td>
										<td><?php echo form_input(array('name' => 'analytics_id', 
										  'value' => @$address['analytics_id'], 
										  'class' => 'text large',
										  'placeholder' => 'Google Analytics ID')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Google Site Verification Code:</b></td>
										<td><?php echo form_input(array('name' => 'google_site_verification',
										  'value' => @$address['google_site_verification'],
										  'class' => 'text large',
										  'placeholder' => 'Google Site Verification Code')); ?></td>
									</tr>
									<tr>
										<td style="width:30%;"><b>Bing Webmaster Site Verification Code:</b></td>
										<td><?php echo form_input(array('name' => 'bing_site_verification',
										  'value' => @$address['bing_site_verification'],
										  'class' => 'text large',
										  'placeholder' => 'Bing Site Verification Code')); ?></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table width="100%" style="background-color:white;">
									<tr>
										<td colspan="2">
											<img src="<?php echo base_url().'assets/benz_assets/fremarketing.png';?>" width="233px" border="0"/>
										</td>
									</tr>
									<tr>
										<td style="width:30%;"><b>FB Remarketing pixel ID :</b></td>
										<td><?php echo form_input(array('name' => 'fb_remarketing_pixel', 
										  'value' => @$address['fb_remarketing_pixel'], 
										  'class' => 'text large',
										  'placeholder' => 'FB Remarketing pixel ID')); ?></td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>
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
