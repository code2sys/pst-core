

  		<!-- VALIDATION ERROR -->
			<?php if(validation_errors()): ?>
			<div class="validation_error">
				<img src="<?php echo $assets; ?>/images/error.png">
				<h1>Error</h1>
				<div class="clear"></div>
				<p><?php echo validation_errors(); ?></p>
			</div>
			<?php endif; ?>
			<!-- END VALIDATION ERROR -->

			<!-- PROCESS ERROR -->
			<?php if(@$processingError): ?>
			<div class="process_error">
				<img src="<?php echo $assets; ?>/images/process_error.png">
				<h1>Error</h1>
				<div class="clear"></div>
				<p>We have had trouble processing your request.  Please try again in a few minutes.</p>
			</div>
			<?php endif; ?>
			<!-- END PROCESS ERROR -->

		  <!-- SUCCESS -->
		  <?php if(@$success): ?>
			<div class="success">
				<img src="<?php echo $assets; ?>/images/success.png">
				<h1>Success</h1>
				<div class="clear"></div>
				<p>Your Email has been successfully sent.</p>
			</div>
			<?php endif; ?>
			<!-- END SUCCESS -->
<?php if (defined('ENABLE_TRAFFICLOGPRO') && ENABLE_TRAFFICLOGPRO): ?>

<?php endif; ?>

  	<!-- Finance FORM-->
			<div class="name_box_full" style="background:rgba(153, 153, 153, 0.28);padding: 30px;">
				<div class="clear"></div>
				<?php echo form_open(secure_site_url('/pages/index/financerequest'), array(/*'class' => 'form_standard',*/ 'id' => 'form_example', 'autocomplete' => 'off')); ?>
				<div class="hidden_table">
					<table width="100%" cellpadding="5" style="margin-top:20px">
						<tr>
							<td colspan="2">
								<p style="text-align: justify;">
									I certify that the information provided by me is correct. I also understand that you will be checking with credit reporting agencies. I authorize an investigation of my credit and employment history and the release of information about my credit experience.
								</p>
								<br/>
								<p style="text-align: justify;">
									Please initial below to indicate that you have recieved a copy of our <a href="<?php echo base_url().'pages/index/privacypolicy'?>">Privacy Notice</a> and agree to all of the above.
								</p>
								<br/>
								<p style="text-align: justify;">
									YOUR CREDIT APPLICATION IS GOING THROUGH A SECURE WEBSITE AND YOUR IDENTITY IS SAFE.
								</p>
								<br/><br/>
							</td>
						</tr>
                        <tr>
                            <td><strong>Application Type</strong></td>
                            <td><label><input type="radio" name="joint" value="0" <?php if ($_REQUEST['joint'] != 1): ?>checked="checked" <?php endif;?> /> Individual</label><label><input type="radio"  name="joint" value="1" <?php if ($_REQUEST['joint'] == 1): ?>checked="checked" <?php endif;?> /> Joint</label></td>

                        </tr>
						<tr>
							<td>
								<label for="initial">(Initial Here)</label>
							</td>
							<td>
								<input name="initial" placeholder="" value="<?php echo set_value('initial');?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="initial">(Co-Application Initial Here)</label>
							</td>
							<td>
								<input name="co_initial" placeholder="" value="<?php echo set_value('co_initial');?>" />
								<span><b>*</b></span>
							</td>
						</tr>
					</table>
					<table width="100%" cellpadding="5">
						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Vehicle Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="type" >Type</label>
							</td>
							<td>
								<select name="type">
									<option value="">Select Type</option>
									<option value="ATV" <?php echo set_value('type') == 'ATV' ? 'selected' : '';?>>ATV</option>
									<option value="UTV" <?php echo set_value('type') == 'UTV' ? 'selected' : '';?>>UTV</option>
									<option value="Street Bike" <?php echo set_value('type') == 'Street Bike' ? 'selected' : '';?>>Street Bike</option>
									<option value="Dirt Bike" <?php echo set_value('type') == 'Dirt Bike' ? 'selected' : '';?>>Dirt Bike</option>
									<option value="Snowmobile" <?php echo set_value('type') == 'Snowmobile' ? 'selected' : '';?>>Snowmobile</option>
									<option value="Watercraft" <?php echo set_value('type') == 'Watercraft' ? 'selected' : '';?>>Watercraft</option>
									<option value="Utility" <?php echo set_value('type') == 'Utility' ? 'selected' : '';?>>Utility</option>
								</select>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="condition" >Condition</label>
							</td>
							<td>
								<select name="condition">
									<option value="">Select Condition</option>
									<option value="new" <?php echo set_value('condition') == 'new' ? 'selected' : '';?>>New</option>
									<option value="old" <?php echo set_value('condition') == 'old' ? 'selected' : '';?>>Pre-Owned</option>
								</select>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="year" >Year</label>
							</td>
							<td>
								<select name="year">
									<option value="">Select Year</option>
									<?php
                                    $current_year = intVal(date("Y"));

                                    // I don't know why 1990..it was there...
                                    for($i=$current_year + 1;$i >= 1990;$i--) { ?>
									<option value="<?php echo $i;?>" <?php echo set_value('year') == $i ? 'selected' : '';?>><?php echo $i;?></option>
									<?php } ?>
								</select>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="make" >Make</label>
							</td>
							<td>
								<input type="text" name="make" value="<?php echo set_value('make');?>" placeholder="">
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="model" >Model</label>
							</td>
							<td>
								<input name="model" placeholder="" value="<?php echo set_value('model');?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="down_payment" >Down Payment</label>
							</td>
							<td>
								<input name="down_payment" placeholder="" value="<?php echo set_value('down_payment');?>" />
								<span><b>*</b></span>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Applicant Contact Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="fname" >First Name</label>
							</td>
							<td>
								<input name="fname" placeholder="" value="<?php echo set_value('fname');?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="mname" >Middle Name</label>
							</td>
							<td>
								<input name="contact_info[mname]" placeholder="" value="<?php echo $_POST['contact_info']['mname'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="lname" >Last Name</label>
							</td>
							<td>
								<input name="lname" placeholder="" value="<?php echo set_value('lname');?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="dl" >Driver's License</label>
							</td>
							<td>
								<input name="dl" placeholder="" value="<?php echo $_POST['dl'];?>" />
                                <span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="dl" >Driver's License Expiration</label>
							</td>
							<td>
								<input name="driver_licence_expiration" type="date" placeholder="" value="<?php echo $_POST['driver_licence_expiration'];?>" />
                                <span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="wphone" >Work Phone</label>
							</td>
							<td>
								<input name="contact_info[wphone]" placeholder="" value="<?php echo $_POST['contact_info']['wphone'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="rphone" >Residence Phone</label>
							</td>
							<td>
								<input name="contact_info[rphone]" placeholder="" value="<?php echo $_POST['contact_info']['rphone'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="email" >E-mail</label>
							</td>
							<td>
								<input name="email" placeholder="" value="<?php echo $_POST['email'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="ssno" >Social Security Number</label>
							</td>
							<td>
								<input name="contact_info[ssno]" placeholder="" value="<?php echo $_POST['contact_info']['ssno'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="marital_status" >Marital Status</label>
							</td>
							<td>
								<select name="contact_info[marital_status]">
									<option value="">Please Select</option>
									<option value="single" <?php echo $_POST['contact_info']['marital_status'] == 'single' ? 'selected' : '';?>>Single</option>
									<option value="married" <?php echo $_POST['contact_info']['marital_status'] == 'married' ? 'selected' : '';?>>Married</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="gender" >Male/Female</label>
							</td>
							<td>
								<select name="contact_info[gender]">
									<option value="">Please Select</option>
									<option value="male" <?php echo $_POST['contact_info']['gender'] == 'male'?'selected':'';?>>Male</option>
									<option value="female" <?php echo $_POST['contact_info']['gender']=='female'?'selected':'';?>>Female</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="dob" >Date of Birth</label>
							</td>
							<td>
								<input type="date" name='contact_info[dob]' value="<?php echo $_POST['contact_info']['dob'];?>">
								<span><b>*</b></span>
							</td>
						</tr>



						<tr class="joint-row">
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Co-Applicant Contact Information:</b></p>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_fname" >First Name</label>
							</td>
							<td>
								<input name="co_fname" placeholder="" value="<?php echo set_value('co_fname');?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_mname" >Middle Name</label>
							</td>
							<td>
								<input name="co_contact_info[mname]" placeholder="" value="<?php echo $_POST['co_contact_info']['mname'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_lname" >Last Name</label>
							</td>
							<td>
								<input name="co_lname" placeholder="" value="<?php echo set_value('co_lname');?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_dl" >Driver's License</label>
							</td>
							<td>
								<input name="co_dl" placeholder="" value="<?php echo $_POST['co_dl'];?>" />
                                <span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_dl" >Driver's License Expiration</label>
							</td>
							<td>
								<input name="co_driver_licence_expiration" type="date" placeholder="" value="<?php echo $_POST['co_driver_licence_expiration'];?>" />
                                <span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_contact_info[wphone]" >Work Phone</label>
							</td>
							<td>
								<input name="co_contact_info[wphone]" placeholder="" value="<?php echo $_POST['co_contact_info']['wphone'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_contact_info[rphone]" >Residence Phone</label>
							</td>
							<td>
								<input name="co_contact_info[rphone]" placeholder="" value="<?php echo $_POST['co_contact_info']['rphone'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_email" >E-mail</label>
							</td>
							<td>
								<input name="co_email" placeholder="" value="<?php echo $_POST['co_email'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_contact_info[ssno]" >Social Security Number</label>
							</td>
							<td>
								<input name="co_contact_info[ssno]" placeholder="" value="<?php echo $_POST['co_contact_info']['ssno'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_contact_info[marital_status]" >Marital Status</label>
							</td>
							<td>
								<select name="co_contact_info[marital_status]">
									<option value="">Please Select</option>
									<option value="single" <?php echo $_POST['co_contact_info']['marital_status'] == 'single' ? 'selected' : '';?>>Single</option>
									<option value="married" <?php echo $_POST['co_contact_info']['marital_status'] == 'married' ? 'selected' : '';?>>Married</option>
								</select>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_contact_info[gender]" >Male/Female</label>
							</td>
							<td>
								<select name="co_contact_info[gender]">
									<option value="">Please Select</option>
									<option value="male" <?php echo $_POST['co_contact_info']['gender'] == 'male'?'selected':'';?>>Male</option>
									<option value="female" <?php echo $_POST['co_contact_info']['gender']=='female'?'selected':'';?>>Female</option>
								</select>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_contact_info[dob]" >Date of Birth</label>
							</td>
							<td>
								<input type="date" name='co_contact_info[dob]' value="<?php echo $_POST['co_contact_info']['dob'];?>">
								<span><b>*</b></span>
							</td>
						</tr>




						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Applicant Physical Address:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="paddress" >Physical Address</label>
							</td>
							<td>
								<input name="physical_address[paddress]" placeholder="" value="<?php echo $_POST['physical_address']['paddress'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="city" >City</label>
							</td>
							<td>
								<input name="physical_address[city]" placeholder="" value="<?php echo $_POST['physical_address']['city'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="state" >State</label>
							</td>
							<td>
								<?php echo form_dropdown('physical_address[state]', $states, $_POST['physical_address']['state'], 'id="billing_state"'); ?>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="zip" >Zip</label>
							</td>
							<td>
								<input name="physical_address[zip]" placeholder="" value="<?php echo $_POST['physical_address']['zip'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="country" >Country</label>
							</td>
							<td>
								<input name="physical_address[country]" placeholder="" value="<?php echo $_POST['physical_address']['country'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>



						<tr class="joint-row">
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Co-Applicant Physical Address:</b></p>
							</td>
						</tr>
                        <tr class="joint-row">
                            <td colspan="2">
                                <em>If you are married and filing a joint credit application, you and your spouse must have the same address.</em>
                            </td>
                        </tr>
						<tr class="joint-row">
							<td>
								<label for="co_physical_address[paddress]" >Physical Address</label>
							</td>
							<td>
								<input name="co_physical_address[paddress]" placeholder="" value="<?php echo $_POST['co_physical_address']['paddress'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_physical_address[city]" >City</label>
							</td>
							<td>
								<input name="co_physical_address[city]" placeholder="" value="<?php echo $_POST['co_physical_address']['city'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_physical_address[state]" >State</label>
							</td>
							<td>
								<?php echo form_dropdown('co_physical_address[state]', $states, $_POST['co_physical_address']['state'], 'id="billing_state"'); ?>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_physical_address[zip]" >Zip</label>
							</td>
							<td>
								<input name="co_physical_address[zip]" placeholder="" value="<?php echo $_POST['co_physical_address']['zip'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_physical_address[country]" >Country</label>
							</td>
							<td>
								<input name="co_physical_address[country]" placeholder="" value="<?php echo $_POST['co_physical_address']['country'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>




						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Applicant Housing Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="owns" >Do you rent or own your home, or other ?</label>
							</td>
							<td>
								<select name="housing_info[owns]">
									<option value="">Choose</option>
									<option value="Rent" <?php echo $_POST['housing_info']['owns']=='Rent'?'selected':'';?>>Rent</option>
									<option value="Own" <?php echo $_POST['housing_info']['owns']=='Own'?'selected':'';?>>Own</option>
									<option value="Other" <?php echo $_POST['housing_info']['owns']=='Other'?'selected':'';?>>Other</option>
								</select>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="landlord" >Landlord / Mortgage</label>
							</td>
							<td>
								<input name="housing_info[landlord]" placeholder="" value="<?php echo $_POST['housing_info']['landlord'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="rent" >Rent / Mortgage Monthly Amount</label>
							</td>
							<td>
								<input name="housing_info[rent]" placeholder="" value="<?php echo $_POST['housing_info']['rent'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="mort_balance" >Mortgage Balance</label>
							</td>
							<td>
								<input name="housing_info[mort_balance]" placeholder="" value="<?php echo $_POST['housing_info']['mort_balance'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="time" >Time at Current Residence</label>
							</td>
							<td>
                                <select name="housing_info[years]">
                                    <option value="">Years</option>
                                    <?php for($y=0;$y<=100;$y++) { ?>
                                        <option value="<?php echo $y;?>" <?php echo ($_POST['housing_info']['years']==$y  && !is_null($_POST['housing_info']['years']) && $_POST['housing_info']['years'] !== "") ?'selected':'';?>><?php echo $y;?></option>
                                    <?php } ?>
                                </select>Years
								<select name="housing_info[months]">
									<option value="">Months</option>
									<?php for($m=0;$m<=12;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo ($_POST['housing_info']['months']==$m && !is_null($_POST['housing_info']['months']) && $_POST['housing_info']['months'] !== "")?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Months

								<span><b>*</b></span>
							</td>
						</tr>




                        <tr class="applicant_previous_resident">
                            <td colspan="2">
                                <p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Applicant Previous Residence: (Required if less than 2 years at current address.)</b></p>
                            </td>
                        </tr>
                        <tr class="applicant_previous_resident">
                            <td>
                                <label for="address1" >Address</label>
                            </td>
                            <td>
                                <input name="previous_add[address]" placeholder="" value="<?php echo $_POST['previous_add']['address'];?>" />
                            </td>
                        </tr>
                        <tr class="applicant_previous_resident">
                            <td>
                                <label for="city" >City</label>
                            </td>
                            <td>
                                <input name="previous_add[city]" placeholder="" value="<?php echo $_POST['previous_add']['city'];?>" />
                            </td>
                        </tr>
                        <tr class="applicant_previous_resident">
                            <td>
                                <label for="st_zip" >State</label>
                            </td>
                            <td>
                                <?php echo form_dropdown('previous_add[state]', $states, $_POST['previous_add']['state'], 'id="billing_state"'); ?>

                            </td>
                        </tr>
                        <tr class="applicant_previous_resident">
                            <td>
                                <label for="st_zip" >Zip</label>
                            </td>
                            <td>
                                <input name="previous_add[zip]" placeholder="" value="<?php echo $_POST['previous_add']['zip'];?>" />
                            </td>
                        </tr>
                        <tr class="applicant_previous_resident">
                            <td>
                                <label for="how_long" >How long at previous address ?</label>
                            </td>
                            <td>
                                <select name="previous_add[years]">
                                    <option value="">Years</option>
                                    <?php for($m=0;$m<=100;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['previous_add']['years']==$m && !is_null($_POST['previous_add']['years']) && $_POST['previous_add']['years'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Years
                                <select name="previous_add[months]">
                                    <option value="">Months</option>
                                    <?php for($m=0;$m<=12;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['previous_add']['months']==$m && !is_null($_POST['previous_add']['months']) && $_POST['previous_add']['months'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Months

                            </td>
                        </tr>




                        <tr class="joint-row">
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Co-Applicant Housing Information:</b></p>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_housing_info[owns]" >Do you rent or own your home, or other ?</label>
							</td>
							<td>
								<select name="co_housing_info[owns]">
									<option value="">Choose</option>
									<option value="Rent" <?php echo $_POST['co_housing_info']['owns']=='Rent'?'selected':'';?>>Rent</option>
									<option value="Own" <?php echo $_POST['co_housing_info']['owns']=='Own'?'selected':'';?>>Own</option>
									<option value="Other" <?php echo $_POST['co_housing_info']['owns']=='Other'?'selected':'';?>>Other</option>
								</select>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_housing_info[landlord]" >Landlord / Mortgage</label>
							</td>
							<td>
								<input name="co_housing_info[landlord]" placeholder="" value="<?php echo $_POST['co_housing_info']['landlord'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_housing_info[rent]" >Rent / Mortgage Monthly Amount</label>
							</td>
							<td>
								<input name="co_housing_info[rent]" placeholder="" value="<?php echo $_POST['co_housing_info']['rent'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_housing_info[mort_balance]" >Mortgage Balance</label>
							</td>
							<td>
								<input name="co_housing_info[mort_balance]" placeholder="" value="<?php echo $_POST['co_housing_info']['mort_balance'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_housing_info[months" >Time at Current Residence</label>
							</td>
							<td>
                                <select name="co_housing_info[years]">
                                    <option value="">Years</option>
                                    <?php for($y=0;$y<=100;$y++) { ?>
                                        <option value="<?php echo $y;?>" <?php echo ($_POST['co_housing_info']['years']==$y  && !is_null($_POST['co_housing_info']['years']) && $_POST['co_housing_info']['years'] !== "") ?'selected':'';?>><?php echo $y;?></option>
                                    <?php } ?>
                                </select>Years
								<select name="co_housing_info[months]">
									<option value="">Months</option>
									<?php for($m=0;$m<=12;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo ($_POST['co_housing_info']['months']==$m && !is_null($_POST['co_housing_info']['months']) && $_POST['co_housing_info']['months'] !== "")?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Months
								<span><b>*</b></span>
							</td>
						</tr>








                        <tr class="joint-row co_applicant_previous_residence">
                            <td colspan="2">
                                <p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Co-Applicant Previous Residence: (Required if less than 2 years at current address.)</b></p>
                            </td>
                        </tr>

                        <tr class="joint-row co_applicant_previous_residence">
                            <td>
                                <label for="co_previous_add[address]" >Address</label>
                            </td>
                            <td>
                                <input name="co_previous_add[address]" placeholder="" value="<?php echo $_POST['co_previous_add']['address'];?>" />
                            </td>
                        </tr>
                        <tr class="joint-row co_applicant_previous_residence">
                            <td>
                                <label for="co_previous_add[city]" >City</label>
                            </td>
                            <td>
                                <input name="co_previous_add[city]" placeholder="" value="<?php echo $_POST['co_previous_add']['city'];?>" />
                            </td>
                        </tr>
                        <tr class="joint-row co_applicant_previous_residence">
                            <td>
                                <label for="co_previous_add[state]" >State</label>
                            </td>
                            <td>
                                <?php echo form_dropdown('co_previous_add[state]', $states, $_POST['co_previous_add']['state'], 'id="co_billing_state"'); ?>

                            </td>
                        </tr>
                        <tr class="joint-row co_applicant_previous_residence">
                            <td>
                                <label for="co_previous_add[zip]" >Zip</label>
                            </td>
                            <td>
                                <input name="co_previous_add[zip]" placeholder="" value="<?php echo $_POST['co_previous_add']['zip'];?>" />
                            </td>
                        </tr>
                        <tr class="joint-row co_applicant_previous_residence">
                            <td>
                                <label for="co_previous_add[years]" >How long at previous address ?</label>
                            </td>
                            <td>
                                <select name="co_previous_add[years]">
                                    <option value="">Years</option>
                                    <?php for($m=0;$m<=100;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['co_previous_add']['years']==$m && !is_null($_POST['co_previous_add']['years']) && $_POST['co_previous_add']['years'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Years
                                <select name="co_previous_add[months]">
                                    <option value="">Months</option>
                                    <?php for($m=0;$m<=12;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['co_previous_add']['months']==$m && !is_null($_POST['co_previous_add']['months']) && $_POST['co_previous_add']['months'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Months

                            </td>
                        </tr>






                        <tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Applicant Banking Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="bank_name" >Name of Bank</label>
							</td>
							<td>
								<input name="banking_info[bank_name]" placeholder="" value="<?php echo $_POST['banking_info']['bank_name'];?>" /><span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="ac_type" >Account Types</label>
							</td>
							<td>
								<input name="banking_info[ac_type]" placeholder="" value="<?php echo $_POST['banking_info']['ac_type'];?>" /><?php if (!defined('BLUFFPOWERSPORTS_VIEW')): ?><span><b>*</b></span><?php endif; ?>
							</td>
						</tr>
						<tr>
							<td>
								<label for="bank_name1" >Name of Bank</label>
							</td>
							<td>
								<input name="banking_info[bank_name1]" placeholder="" value="<?php echo $_POST['banking_info']['bank_name1'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="ac_type1" >Account Types</label>
							</td>
							<td>
								<input name="banking_info[ac_type1]" placeholder="" value="<?php echo $_POST['banking_info']['ac_type1'];?>" />
							</td>
						</tr>



						<tr class="joint-row">
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Co-Applicant Banking Information:</b></p>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_banking_info[bank_name]" >Name of Bank</label>
							</td>
							<td>
								<input name="co_banking_info[bank_name]" placeholder="" value="<?php echo $_POST['co_banking_info']['bank_name'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_banking_info[ac_type]" >Account Types</label>
							</td>
							<td>
								<input name="co_banking_info[ac_type]" placeholder="" value="<?php echo $_POST['co_banking_info']['ac_type'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_banking_info[bank_name1]" >Name of Bank</label>
							</td>
							<td>
								<input name="co_banking_info[bank_name1]" placeholder="" value="<?php echo $_POST['co_banking_info']['bank_name1'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_banking_info[ac_type1]" >Account Types</label>
							</td>
							<td>
								<input name="co_banking_info[ac_type1]" placeholder="" value="<?php echo $_POST['co_banking_info']['ac_type1'];?>" />
							</td>
						</tr>





						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Applicant Employer Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="occupation" >Occupation</label>
							</td>
							<td>
								<input name="employer_info[occupation]" placeholder="" value="<?php echo $_POST['employer_info']['occupation'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_name" >Employer Name</label>
							</td>
							<td>
								<input name="employer_info[emp_name]" placeholder="" value="<?php echo $_POST['employer_info']['emp_name'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_addr" >Employer Address</label>
							</td>
							<td>
								<input name="employer_info[emp_addr]" placeholder="" value="<?php echo $_POST['employer_info']['emp_addr'];?>" /><?php if (!defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?>
								
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_city" >Employer City</label>
							</td>
							<td>
								<input name="employer_info[emp_city]" placeholder="" value="<?php echo $_POST['employer_info']['emp_city'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_state" >Employer State</label>
							</td>
							<td>
								<?php echo form_dropdown('employer_info[state]', $states, $_POST['employer_info']['state'], 'id="billing_state"'); ?>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_zip" >Employer Zip</label>
							</td>
							<td>
								<input name="employer_info[emp_zip]" placeholder="" value="<?php echo $_POST['employer_info']['emp_zip'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_phone" >Employer Phone</label>
							</td>
							<td>
								<input name="employer_info[emp_phone]" placeholder="" value="<?php echo $_POST['employer_info']['emp_phone'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="salary" >Salary(Annually Gross)</label>
							</td>
							<td>
								<input name="employer_info[salary]" placeholder="" value="<?php echo $_POST['employer_info']['salary'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_time" >Time at Employer</label>
							</td>
							<td>
                                <select name="employer_info[year]">
                                    <option value="">Years</option>
                                    <?php for($m=0;$m<=100;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['employer_info']['year']==$m && !is_null($_POST['employer_info']['year']) && $_POST['employer_info']['year'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Years
								<select name="employer_info[month]">
									<option value="">Months</option>
									<?php for($m=0;$m<=12;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo ($_POST['employer_info']['month']==$m && !is_null($_POST['employer_info']['month']) && $_POST['employer_info']['month'] !== "")?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Months
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label>Type of Employment</label>
							</td>
							<td>
								<input type="radio" id="full" name="employer_info[emp_type]" placeholder="" value="Full" <?php echo $_POST['employer_info']['emp_type']=='full'?'selected':'';?>/><label for="full">Full</label>
								<input type="radio" id="part" name="employer_info[emp_type]" placeholder="" value="Part-Time" <?php echo $_POST['employer_info']['emp_type']=='Part-Time'?'selected':'';?>/><label for="part">Part-Time</label>
							</td>
						</tr>
						<tr>
							<td>
								<label for="other_income" >Other Income</label>
							</td>
							<td>
								<input name="employer_info[other_income]" placeholder="" value="<?php echo $_POST['employer_info']['other_income'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="income_frequency" >Other Income Frequency</label>
							</td>
							<td>
								<input name="employer_info[income_frequency]" placeholder="" value="<?php echo $_POST['employer_info']['income_frequency'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="comments" >Additional Comments<br/>Please include any information that you feel may help us process your application</label>
							</td>
							<td>
								<textarea name="employer_info[comments]" placeholder="" value="" ><?php echo $_POST['employer_info']['comments'];?></textarea>
							</td>
						</tr>

						<?php if (defined('LIFESTYLESHONDA_VIEW') ): ?>

						<tr>
							<td>
								<label for="income_frequency" >Sales Rep</label>
							</td>
							<td>
								<input name="employer_info[sales_rep]" placeholder="" value="<?php echo $_POST['employer_info']['sales_rep'];?>" />
							</td>
						</tr>

						<?php endif; ?>


                        <tr class="prior_employment_history">
                            <td colspan="2">
                                <p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Applicant Previous Employer Information:</b></p>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td colspan="2"><em>Two years' employment history is required for your credit application.</em></td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_occupation" >Previous Occupation</label>
                            </td>
                            <td>
                                <input name="prior_employer_info[occupation]" placeholder="" value="<?php echo $_POST['prior_employer_info']['occupation'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_emp_name" >Previous Employer Name</label>
                            </td>
                            <td>
                                <input name="prior_employer_info[emp_name]" placeholder="" value="<?php echo $_POST['prior_employer_info']['emp_name'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_emp_addr" >Previous Employer Address</label>
                            </td>
                            <td>
                                <input name="prior_employer_info[emp_addr]" placeholder="" value="<?php echo $_POST['prior_employer_info']['emp_addr'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_emp_city" >Previous Employer City</label>
                            </td>
                            <td>
                                <input name="prior_employer_info[emp_city]" placeholder="" value="<?php echo $_POST['prior_employer_info']['emp_city'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_emp_state" >Previous Employer State</label>
                            </td>
                            <td>
                                <?php echo form_dropdown('prior_employer_info[state]', $states, $_POST['prior_employer_info']['state'], 'id="billing_state"'); ?>
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_emp_zip" >Previous Employer Zip</label>
                            </td>
                            <td>
                                <input name="prior_employer_info[emp_zip]" placeholder="" value="<?php echo $_POST['prior_employer_info']['emp_zip'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_emp_phone" >Previous Employer Phone</label>
                            </td>
                            <td>
                                <input name="prior_employer_info[emp_phone]" placeholder="" value="<?php echo $_POST['prior_employer_info']['emp_phone'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_salary" >Previous Salary(Annually Gross)</label>
                            </td>
                            <td>
                                <input name="prior_employer_info[salary]" placeholder="" value="<?php echo $_POST['prior_employer_info']['salary'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label for="prior_emp_time" >Previous Time at Employer</label>
                            </td>
                            <td>
                                <select name="prior_employer_info[year]">
                                    <option value="">Years</option>
                                    <?php for($m=0;$m<=100;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['prior_employer_info']['year']==$m && !is_null($_POST['prior_employer_info']['year']) && $_POST['prior_employer_info']['year'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Years
                                <select name="prior_employer_info[month]">
                                    <option value="">Months</option>
                                    <?php for($m=0;$m<=12;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['prior_employer_info']['month']==$m && !is_null($_POST['prior_employer_info']['month']) && $_POST['prior_employer_info']['month'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Months
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="prior_employment_history">
                            <td>
                                <label>Previous Type of Employment</label>
                            </td>
                            <td>
                                <input type="radio" id="prior_full" name="prior_employer_info[emp_type]" placeholder="" value="Full" <?php echo $_POST['prior_employer_info']['emp_type']=='full'?'selected':'';?>/><label for="prior_full">Full</label>
                                <input type="radio" id="prior_part" name="prior_employer_info[emp_type]" placeholder="" value="Part-Time" <?php echo $_POST['prior_employer_info']['emp_type']=='Part-Time'?'selected':'';?>/><label for="prior_part">Part-Time</label>
                            </td>
                        </tr>







                        <tr class="joint-row">
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Co-Applicant Employer Information:</b></p>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[occupation]" >Occupation</label>
							</td>
							<td>
								<input name="co_employer_info[occupation]" placeholder="" value="<?php echo $_POST['co_employer_info']['occupation'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[emp_name]" >Employer Name</label>
							</td>
							<td>
								<input name="co_employer_info[emp_name]" placeholder="" value="<?php echo $_POST['co_employer_info']['emp_name'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[emp_addr]" >Employer Address</label>
							</td>
							<td>
								<input name="co_employer_info[emp_addr]" placeholder="" value="<?php echo $_POST['co_employer_info']['emp_addr'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[emp_city]" >Employer City</label>
							</td>
							<td>
								<input name="co_employer_info[emp_city]" placeholder="" value="<?php echo $_POST['co_employer_info']['emp_city'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[state]" >Employer State</label>
							</td>
							<td>
								<?php echo form_dropdown('co_employer_info[state]', $states, $_POST['co_employer_info']['state'], 'id="co_billing_state"'); ?>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[emp_zip]" >Employer Zip</label>
							</td>
							<td>
								<input name="co_employer_info[emp_zip]" placeholder="" value="<?php echo $_POST['co_employer_info']['emp_zip'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[emp_phone]" >Employer Phone</label>
							</td>
							<td>
								<input name="co_employer_info[emp_phone]" placeholder="" value="<?php echo $_POST['co_employer_info']['emp_phone'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[salary]" >Salary(Annually Gross)</label>
							</td>
							<td>
								<input name="co_employer_info[salary]" placeholder="" value="<?php echo $_POST['co_employer_info']['salary'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[year]" >Time at Employer</label>
							</td>
							<td>
                                <select name="co_employer_info[year]">
                                    <option value="">Years</option>
                                    <?php for($m=0;$m<=100;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['co_employer_info']['year']==$m && !is_null($_POST['co_employer_info']['year']) && $_POST['co_employer_info']['year'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Years
								<select name="co_employer_info[month]">
									<option value="">Months</option>
									<?php for($m=0;$m<=12;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo ($_POST['co_employer_info']['month']==$m && !is_null($_POST['co_employer_info']['month']) && $_POST['co_employer_info']['month'] !== "")?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Months
								<span><b>*</b></span>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label>Type of Employment</label>
							</td>
							<td>
								<input type="radio" id="co_full" name="co_employer_info[emp_type]" placeholder="" value="Full" <?php echo $_POST['co_employer_info']['emp_type']=='full'?'selected':'';?>/><label for="co_full">Full</label>
								<input type="radio" id="co_part" name="co_employer_info[emp_type]" placeholder="" value="Part-Time" <?php echo $_POST['co_employer_info']['emp_type']=='Part-Time'?'selected':'';?>/><label for="co_part">Part-Time</label>
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[other_income]" >Other Income</label>
							</td>
							<td>
								<input name="co_employer_info[other_income]" placeholder="" value="<?php echo $_POST['co_employer_info']['other_income'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[income_frequency]" >Other Income Frequency</label>
							</td>
							<td>
								<input name="co_employer_info[income_frequency]" placeholder="" value="<?php echo $_POST['co_employer_info']['income_frequency'];?>" />
							</td>
						</tr>
						<tr class="joint-row">
							<td>
								<label for="co_employer_info[comments]" >Additional Comments<br/>Please include any information that you feel may help us process your application</label>
							</td>
							<td>
								<textarea name="co_employer_info[comments]" placeholder="" value="" ><?php echo $_POST['co_employer_info']['comments'];?></textarea>
							</td>
						</tr>



                        <tr class="joint-row co_prior_employment_history">
                            <td colspan="2">
                                <p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Co-Applicant Previous Employer Information:</b></p>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td colspan="2"><em>Two years' employment history is required for your credit application.</em></td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_occupation" >Previous Occupation</label>
                            </td>
                            <td>
                                <input name="co_prior_employer_info[occupation]" placeholder="" value="<?php echo $_POST['co_prior_employer_info']['occupation'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_emp_name" >Previous Employer Name</label>
                            </td>
                            <td>
                                <input name="co_prior_employer_info[emp_name]" placeholder="" value="<?php echo $_POST['co_prior_employer_info']['emp_name'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_emp_addr" >Previous Employer Address</label>
                            </td>
                            <td>
                                <input name="co_prior_employer_info[emp_addr]" placeholder="" value="<?php echo $_POST['co_prior_employer_info']['emp_addr'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_emp_city" >Previous Employer City</label>
                            </td>
                            <td>
                                <input name="co_prior_employer_info[emp_city]" placeholder="" value="<?php echo $_POST['co_prior_employer_info']['emp_city'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_emp_state" >Previous Employer State</label>
                            </td>
                            <td>
                                <?php echo form_dropdown('co_prior_employer_info[state]', $states, $_POST['co_prior_employer_info']['state'], 'id="billing_state"'); ?>
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_emp_zip" >Previous Employer Zip</label>
                            </td>
                            <td>
                                <input name="co_prior_employer_info[emp_zip]" placeholder="" value="<?php echo $_POST['co_prior_employer_info']['emp_zip'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_emp_phone" >Previous Employer Phone</label>
                            </td>
                            <td>
                                <input name="co_prior_employer_info[emp_phone]" placeholder="" value="<?php echo $_POST['co_prior_employer_info']['emp_phone'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_salary" >Previous Salary(Annually Gross)</label>
                            </td>
                            <td>
                                <input name="co_prior_employer_info[salary]" placeholder="" value="<?php echo $_POST['co_prior_employer_info']['salary'];?>" />
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label for="co_prior_emp_time" >Previous Time at Employer</label>
                            </td>
                            <td>
                                <select name="co_prior_employer_info[year]">
                                    <option value="">Years</option>
                                    <?php for($m=0;$m<=100;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['co_prior_employer_info']['year']==$m && !is_null($_POST['co_prior_employer_info']['year']) && $_POST['co_prior_employer_info']['year'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Years
                                <select name="co_prior_employer_info[month]">
                                    <option value="">Months</option>
                                    <?php for($m=0;$m<=12;$m++) { ?>
                                        <option value="<?php echo $m;?>" <?php echo ($_POST['co_prior_employer_info']['month']==$m && !is_null($_POST['co_prior_employer_info']['month']) && $_POST['co_prior_employer_info']['month'] !== "")?'selected':'';?>><?php echo $m;?></option>
                                    <?php } ?>
                                </select>Months
                                <span><b>*</b></span>
                            </td>
                        </tr>
                        <tr class="joint-row co_prior_employment_history">
                            <td>
                                <label>Previous Type of Employment</label>
                            </td>
                            <td>
                                <input type="radio" id="co_prior_full" name="co_prior_employer_info[emp_type]" placeholder="" value="Full" <?php echo $_POST['co_prior_employer_info']['emp_type']=='full'?'selected':'';?>/><label for="co_prior_full">Full</label>
                                <input type="radio" id="co_prior_part" name="co_prior_employer_info[emp_type]" placeholder="" value="Part-Time" <?php echo $_POST['co_prior_employer_info']['emp_type']=='Part-Time'?'selected':'';?>/><label for="co_prior_part">Part-Time</label>
                            </td>
                        </tr>





                    </table>
					<table cellpadding="5">
						<tr>
							<td colspan="8">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>References:</b></p>
							</td>
						</tr>
						<tr>
							<td><label for="name1" >Name</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[name1]" placeholder="" value="<?php echo $_POST['reference']['name1'];?>" /><span><b>*</b></span></td>
							<td><label for="phone1" >Phone</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[phone1]" placeholder="" value="<?php echo $_POST['reference']['phone1'];?>" /><span><b>*</b></span></td>
							<td><label for="city1" >City</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[city1]" placeholder="" value="<?php echo $_POST['reference']['city1'];?>" /><span><b>*</b></span></td>
							<td><label for="state1" >State</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[state1]" placeholder="" value="<?php echo $_POST['reference']['state1'];?>" /><span><b>*</b></span></td>
						</tr>
						<tr>
							<td><label for="name1" >Name</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[name2]" placeholder="" value="<?php echo $_POST['reference']['name2'];?>" /><?php if (defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?></td>
							<td><label for="phone1" >Phone</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[phone2]" placeholder="" value="<?php echo $_POST['reference']['phone2'];?>" /><?php if (defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?></td>
							<td><label for="city1" >City</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[city2]" placeholder="" value="<?php echo $_POST['reference']['city2'];?>" /><?php if (defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?></td>
							<td><label for="state1" >State</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[state2]" placeholder="" value="<?php echo $_POST['reference']['state2'];?>" /><?php if (defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?></td>
						</tr>
						<tr>
							<td><label for="name1" >Name</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[name3]" placeholder="" value="<?php echo $_POST['reference']['name3'];?>" /><?php if (defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?></td>
							<td><label for="phone1" >Phone</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[phone3]" placeholder="" value="<?php echo $_POST['reference']['phone3'];?>" /><?php if (defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?></td>
							<td><label for="city1" >City</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[city3]" placeholder="" value="<?php echo $_POST['reference']['city3'];?>" /><?php if (defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?></td>
							<td><label for="state1" >State</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[state3]" placeholder="" value="<?php echo $_POST['reference']['state3'];?>" /><span><?php if (defined('BLUFFPOWERSPORTS_VIEW') ): ?><span><b>*</b></span><?php endif; ?></td>

							<?php echo defined('BLUFFPOWERSPORTS_VIEW').'sdfds'; ?>
						</tr>
						<tr>
							<td><label for="name1" >Name</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[name4]" placeholder="" value="<?php echo $_POST['reference']['name4'];?>" /></td>
							<td><label for="phone1" >Phone</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[phone4]" placeholder="" value="<?php echo $_POST['reference']['phone4'];?>" /></td>
							<td><label for="city1" >City</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[city4]" placeholder="" value="<?php echo $_POST['reference']['city4'];?>" /></td>
							<td><label for="state1" >State</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[state4]" placeholder="" value="<?php echo $_POST['reference']['state4'];?>" /></td>
						</tr>
					</table>
					<div style="margin-left: -27px;width: 108%;padding-top: 10px;border-top: 2px solid #555555;margin-top: 10px;">
						<p>* indicates requires field</p>
					</div>

				</div>
			</div>
			<div style="width:100%;text-align:center;margin-top: 40px;">
				<input style="float: inherit;background:#3f51b5;color:white;font-weight: normal;padding: 20px 90px 20px 90px;border-radius: 0;" type="submit" value="APPLY NOW" class="input_button">
			</div>
			</form>
	<script type="application/javascript">
        (function() {
            // This next bit is supposed to address the four "under 2 year" situations
            // {applicant, co-applicant} x {employment, housing}
            var applicant_housing_show_hide = function() {
                if (parseInt($("select[name='housing_info[years]']").val(), 10) < 2) {
                    // Then we must show the other one
                    $(".applicant_previous_resident").show();
                } else {
                    // Then we hide the other one...
                    $(".applicant_previous_resident").hide();
                }
            };

            $("select[name='housing_info[years]']").on("change", applicant_housing_show_hide);
            applicant_housing_show_hide();

            var co_applicant_housing_show_hide = function() {
                if (parseInt($("select[name='co_housing_info[years]']").val(), 10) < 2) {
                    // Then we must show the other one
                    $(".co_applicant_previous_residence").show();
                } else {
                    // Then we hide the other one...
                    $(".co_applicant_previous_residence").hide();
                }
            };

            $("select[name='co_housing_info[years]']").on("change", co_applicant_housing_show_hide);
            co_applicant_housing_show_hide();


            // Now for employment...
            var applicant_employment_show_hide = function() {
                if (parseInt($("select[name='employer_info[year]']").val(), 10) < 2) {
                    // Then we must show the other one
                    $(".prior_employment_history").show();
                } else {
                    // Then we hide the other one...
                    $(".prior_employment_history").hide();
                }
            };

            $("select[name='employer_info[year]']").on("change", applicant_employment_show_hide);
            applicant_employment_show_hide();

            var co_applicant_employment_show_hide = function() {
                if (parseInt($("select[name='co_employer_info[year]']").val(), 10) < 2) {
                    // Then we must show the other one
                    $(".co_prior_employment_history").show();
                } else {
                    // Then we hide the other one...
                    $(".co_prior_employment_history").hide();
                }
            };

            $("select[name='co_employer_info[year]']").on("change", co_applicant_employment_show_hide);
            co_applicant_employment_show_hide();


            var jointShowHide = function(e) {
                if ($("input[name='joint'][value=1]:checked").length > 0) {
                    $(".joint-row").show();
                    co_applicant_employment_show_hide();
                    co_applicant_housing_show_hide();
                } else {
                    $(".joint-row").hide();
                }
            };

            $("input[name='joint']").on("click", jointShowHide);
            jointShowHide();
        })();

		$(document).ready(function(){
			var success = '<?php echo @$success; ?>';
			//alert(success);
			if(success==1)
			{
				//alert(success);
				$(":input").val('');
				$(".input_button").val('APPLY NOW');
			}



		});
	</script>

<?php $this->view('modals/customer_exit_modal.php'); ?>
