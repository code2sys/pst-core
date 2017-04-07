<?php
$contact_info = json_decode($application['contact_info']);
$physical_address = json_decode($application['physical_address']);
$housing_info = json_decode($application['housing_info']);
$banking_info = json_decode($application['banking_info']);
$previous_add = json_decode($application['previous_add']);
$employer_info = json_decode($application['employer_info']);
$reference = json_decode($application['reference']);

?>
<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-cube"></i>&nbsp;<?php if (@$new): ?>New<?php else: ?>Edit<?php endif; ?> Credit Application</h1>
        <p><b>Please fill out all fields within required tabs with an *</b></p>
        <br>

        <!-- ERROR -->
        <?php if (validation_errors()): ?>
            <div class="error">
                <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                <p><?php echo validation_errors(); ?></p>
            </div>
        <?php endif; ?>
        <!-- END ERROR -->

        <!-- SUCCESS -->
        <?php if (@$success): ?>
			<div class="success">
			  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
			<h1>Success</h1>
			<div class="clear"></div>
			<p>
			  Your changes have been made.
			</p>
			<div class="clear"></div>
			</div>
        <?php endif; ?>
        <!-- END SUCCESS -->
		
		<a href="/admin/finance_print/<?php echo $id;?>" target="_blank">Print</a> | 
		<a href="/admin/finance_pdf/<?php echo $id;?>">PDF</a> 

        <?php echo form_open('admin/finance_edit/' . $id, array('class' => 'form_standard')); ?>	
        <!-- SUBMIT PRODUCT -->
			<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Save</button>
        <!-- SUBMIT PRODUCT -->
        <!-- TAB CONTENT -->
        <div class="tab_content">
            <div class="hidden_table">
					<table width="100%" cellpadding="5">
						<tr>
							<td colspan="2">
								<select name="application_status">
									<option value='new' <?php echo $application['application_status'] == 'new' ? 'selected': '';?>>New</option>
									<option value="processing" <?php echo $application['application_status'] == 'processing' ? 'selected': '';?>>Processing</option>
									<option value="approved" <?php echo $application['application_status'] == 'approved' ? 'selected': '';?>>Approved</option>
									<option value="declined" <?php echo $application['application_status'] == 'declined' ? 'selected': '';?>>Declined</option>
								</select>
							</td>
						</tr>
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
								<select name="tpe">
									<option value="">Select Type</option>
									<option value="ATV" <?php echo $application['type'] == 'ATV' ? 'selected' : '';?>>ATV</option>
									<option value="UTV" <?php echo $application['type'] == 'UTV' ? 'selected' : '';?>>UTV</option>
									<option value="Street Bike" <?php echo $application['type'] == 'Street Bike' ? 'selected' : '';?>>Street Bike</option>
									<option value="Dirt Bike" <?php echo $application['type'] == 'Dirt Bike' ? 'selected' : '';?>>Dirt Bike</option>
									<option value="Snowmobile" <?php echo $application['type'] == 'Snowmobile' ? 'selected' : '';?>>Snowmobile</option>
									<option value="Watercraft" <?php echo $application['type'] == 'Watercraft' ? 'selected' : '';?>>Watercraft</option>
									<option value="Utility" <?php echo $application['type'] == 'Utility' ? 'selected' : '';?>>Utility</option>
									<option value="Motorcycle/Scooter" <?php echo $application['type'] == 'Motorcycle/Scooter' ? 'selected' : '';?>>Motorcycle/Scooter</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="condition" >Condition</label>
							</td>
							<td>
								<select name="condition">
									<option value="">Select Condition</option>
									<option value="new" <?php echo $application['condition'] == 'new' ? 'selected' : '';?>>New</option>
									<option value="old" <?php echo $application['condition'] == 'old' ? 'selected' : '';?>>Pre-Owned</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="year" >Year</label>
							</td>
							<td>
								<select name="year">
									<option value="">Select Year</option>
									<?php for($i=1990;$i<=date('Y');$i++) { ?>
									<option value="<?php echo $i;?>" <?php echo $application['year'] == $i ? 'selected' : '';?>><?php echo $i;?></option>
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
								<!--<select name="make">
									<option value="">Select Make</option>
									<option value="yamaha" <?php echo $application['make'] == 'yamaha' ? 'selected' : '';?>>Yamaha</option>
								</select>-->
								<input name="make" placeholder="" value="<?php echo $application['make'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="model" >Model</label>
							</td>
							<td>
								<input name="model" placeholder="" value="<?php echo $application['model'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="down_payment" >Down Payment</label>
							</td>
							<td>
								<input name="down_payment" placeholder="" value="<?php echo $application['down_payment'];?>" />
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Your Contact Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="fname" >First Name</label>
							</td>
							<td>
								<input name="fname" placeholder="" value="<?php echo $application['first_name'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="mname" >Middle Name</label>
							</td>
							<td>
								<input name="contact_info[mname]" placeholder="" value="<?php echo $contact_info->mname;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="lname" >Last Name</label>
							</td>
							<td>
								<input name="lname" placeholder="" value="<?php echo $application['last_name'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="dl" >Driver's Lisence</label>
							</td>
							<td>
								<input name="dl" placeholder="" value="<?php echo $application['driver_licence'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="wphone" >Work Phone</label>
							</td>
							<td>
								<input name="contact_info[wphone]" placeholder="" value="<?php echo $contact_info->wphone;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="rphone" >Residence Phone</label>
							</td>
							<td>
								<input name="contact_info[rphone]" placeholder="" value="<?php echo $contact_info->rphone;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="email" >E-mail</label>
							</td>
							<td>
								<input name="email" placeholder="" value="<?php echo $application['email'];?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="ssno" >Social Security Number</label>
							</td>
							<td>
								<input name="contact_info[ssno]" placeholder="" value="<?php echo $contact_info->ssno;?>" />
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
									<option value="single" <?php echo $contact_info->marital_status == 'single' ? 'selected' : '';?>>Single</option>
									<option value="married" <?php echo $contact_info->marital_status == 'married' ? 'selected' : '';?>>Married</option>
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
									<option value="male"<?php echo $contact_info->gender == 'male' ? 'selected' : '';?>>Male</option>
									<option value="female" <?php echo $contact_info->gender == 'female' ? 'selected' : '';?>>Female</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="dob" >Date of Birth</label>
							</td>
							<td>
								<input type="date" name='contact_info[dob]' value="<?php echo $contact_info->dob;?>">
								<!--<select name="contact_info[day]">
									<option value="">Day</option>
									<?php for($d=1;$d<=31;$d++) { ?>
									<option value="<?php echo $d;?>" <?php echo $contact_info->day == $d ? 'selected' : '';?>><?php echo $d;?></option>
									<?php } ?>
								</select>
								<select name="contact_info[month]">
									<option value="">Month</option>
									<?php for($m=1;$m<=12;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo $contact_info->month == $m ? 'selected' : '';?>><?php echo $m;?></option>
									<?php } ?>
								</select>
								<select name="contact_info[year]">
									<option value="">Year</option>
									<?php for($y=1950;$y<=2017;$y++) { ?>
									<option value="<?php echo $y;?>" <?php echo $contact_info->year == $y ? 'selected' : '';?>><?php echo $y;?></option>
									<?php } ?>
								</select>-->
								<span><b>*</b></span>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Physical Address Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="paddress" >Physical Address</label>
							</td>
							<td>
								<input name="physical_address[paddress]" placeholder="" value="<?php echo $physical_address->paddress;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="city" >City</label>
							</td>
							<td>
								<input name="physical_address[city]" placeholder="" value="<?php echo $physical_address->city;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="state" >State</label>
							</td>
							<td>
								<?php echo form_dropdown('physical_address[state]', $states, $physical_address->state, 'id="billing_state"'); ?>
								<!--<select name="physical_address[state]">
									<option value="">Select</option>
									<option value="delhi" <?php echo $physical_address->state == 'delhi' ? 'selected' : '';?>>Delhi</option>
								</select>-->
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="zip" >Zip</label>
							</td>
							<td>
								<input name="physical_address[zip]" placeholder="" value="<?php echo $physical_address->zip;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="country" >Country</label>
							</td>
							<td>
								<input name="physical_address[country]" placeholder="" value="<?php echo $physical_address->country;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Housing Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="owns" >Do you rent or own your home, or other ?</label>
							</td>
							<td>
								<select name="housing_info[owns]">
									<option value="">Choose</option>
									<option value="Rent" <?php echo $housing_info->owns=='Rent'?'selected':'';?>>Rent</option>
									<option value="Own" <?php echo $housing_info->owns=='Own'?'selected':'';?>>Own</option>
									<option value="Other" <?php echo $housing_info->owns=='Other'?'selected':'';?>>Other</option>
								</select>
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="landlord" >Landlord / Mortgage</label>
							</td>
							<td>
								<input name="housing_info[landlord]" placeholder="" value="<?php echo $housing_info->landlord;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="rent" >Rent / Mortgage Monthly Amount</label>
							</td>
							<td>
								<input name="housing_info[rent]" placeholder="" value="<?php echo $housing_info->rent;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="mort_balance" >Mortgage Balance</label>
							</td>
							<td>
								<input name="housing_info[mort_balance]" placeholder="" value="<?php echo $housing_info->mort_balance;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="time" >Time at Current Residence</label>
							</td>
							<td>
								<select name="housing_info[months]">
									<option value="">Months</option>
									<?php for($m=1;$m<=12;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo $housing_info->months==$m?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Months
								<select name="housing_info[years]">
									<option value="">Years</option>
									<?php for($y=1;$y<=100;$y++) { ?>
									<option value="<?php echo $y;?>" <?php echo $housing_info->years==$y?'selected':'';?>><?php echo $y;?></option>
									<?php } ?>
								</select>Years
								<span><b>*</b></span>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Banking Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="bank_name" >Name of Bank</label>
							</td>
							<td>
								<input name="banking_info[bank_name]" placeholder="" value="<?php echo $banking_info->bank_name;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="ac_type" >Account Types</label>
							</td>
							<td>
								<input name="banking_info[ac_type]" placeholder="" value="<?php echo $banking_info->ac_type;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="bank_name1" >Name of Bank</label>
							</td>
							<td>
								<input name="banking_info[bank_name1]" placeholder="" value="<?php echo $banking_info->bank_name1;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="ac_type1" >Account Types</label>
							</td>
							<td>
								<input name="banking_info[ac_type1]" placeholder="" value="<?php echo $banking_info->ac_type1;?>" />
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Previous Residence (If less then 5 years at current address..)</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="address1" >Address</label>
							</td>
							<td>
								<input name="previous_add[address]" placeholder="" value="<?php echo $previous_add->address;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="city" >City</label>
							</td>
							<td>
								<input name="previous_add[city]" placeholder="" value="<?php echo $previous_add->city;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="st_zip" >State</label>
							</td>
							<td>
								<?php echo form_dropdown('previous_add[state]', $states, $previous_add->state, 'id="billing_state"'); ?>
								<!--<input name="previous_add[state]" placeholder="" value="<?php echo $previous_add->state;?>" />-->
							</td>
						</tr>
						<tr>
							<td>
								<label for="st_zip" >Zip</label>
							</td>
							<td>
								<input name="previous_add[zip]" placeholder="" value="<?php echo $previous_add->zip;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="how_long" >How long at previous address ?</label>
							</td>
							<td>
								<select name="previous_add[months]">
									<option value="">Months</option>
									<?php for($m=1;$m<=12;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo $previous_add->months==$m?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Months
								<select name="previous_add[years]">
									<option value="">Years</option>
									<?php for($m=1;$m<=100;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo $previous_add->years==$m?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Years
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
								<p style="padding:5px;margin: 10px 0px 10px 0px;color:#ccc;background: #555;"><b>Employer Information:</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<label for="occupation" >Occupation</label>
							</td>
							<td>
								<input name="employer_info[occupation]" placeholder="" value="<?php echo $employer_info->occupation;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_name" >Employer Name</label>
							</td>
							<td>
								<input name="employer_info[emp_name]" placeholder="" value="<?php echo $employer_info->emp_name;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_addr" >Employer Address</label>
							</td>
							<td>
								<input name="employer_info[emp_addr]" placeholder="" value="<?php echo $employer_info->emp_addr;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_city" >Employer City</label>
							</td>
							<td>
								<input name="employer_info[emp_city]" placeholder="" value="<?php echo $employer_info->emp_city;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_state" >Employer State</label>
							</td>
							<td>
								<?php echo form_dropdown('employer_info[state]', $states, $employer_info->state, 'id="billing_state"'); ?>
								<!--<input name="employer_info[emp_state]" placeholder="" value="<?php echo $employer_info->emp_state;?>" />-->
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_zip" >Employer Zip</label>
							</td>
							<td>
								<input name="employer_info[emp_zip]" placeholder="" value="<?php echo $employer_info->emp_zip;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_phone" >Employer Phone</label>
							</td>
							<td>
								<input name="employer_info[emp_phone]" placeholder="" value="<?php echo $employer_info->emp_phone;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="salary" >Salary(Annually Gross)</label>
							</td>
							<td>
								<input name="employer_info[salary]" placeholder="" value="<?php echo $employer_info->salary;?>" />
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label for="emp_time" >Time at Employer</label>
							</td>
							<td>
								<select name="employer_info[month]">
									<option value="">Months</option>
									<?php for($m=1;$m<=12;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo $employer_info->month==$m?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Months
								<select name="employer_info[year]">
									<option value="">Years</option>
									<?php for($m=1;$m<=100;$m++) { ?>
									<option value="<?php echo $m;?>" <?php echo $employer_info->year==$m?'selected':'';?>><?php echo $m;?></option>
									<?php } ?>
								</select>Years
								<span><b>*</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<label>Type of Employment</label>
							</td>
							<td>
								<input type="radio" id="full" name="employer_info[emp_type]" placeholder="" value="Full" <?php echo $employer_info->emp_type == 'Full' ? 'checked' : '';?>/>Full
								<input type="radio" id="part" name="employer_info[emp_type]" placeholder="" value="Part-Time" <?php echo $employer_info->emp_type == 'Part-Time' ? 'checked' : '';?>/>Part-Time
							</td>
						</tr>
						<tr>
							<td>
								<label for="other_income" >Other Income</label>
							</td>
							<td>
								<input name="employer_info[other_income]" placeholder="" value="<?php echo $employer_info->other_income;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="income_frequency" >Other Income Frequency</label>
							</td>
							<td>
								<input name="employer_info[income_frequency]" placeholder="" value="<?php echo $employer_info->income_frequency;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="comments" >Additional Comments<br/>Please include any information that you feel may help us process your application</label>
							</td>
							<td>
								<textarea name="employer_info[comments]" placeholder="" value="" ><?php echo $employer_info->comments;?></textarea>
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
							<td style="width:25%"><input style="width:60%" name="reference[name1]" placeholder="" value="<?php echo $reference->name1;?>" /></td>
							<td><label for="phone1" >Phone</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[phone1]" placeholder="" value="<?php echo $reference->phone1;?>" /></td>
							<td><label for="city1" >City</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[city1]" placeholder="" value="<?php echo $reference->city1;?>" /></td>
							<td><label for="state1" >State</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[state1]" placeholder="" value="<?php echo $reference->state1;?>" /></td>
						</tr>
						<tr>
							<td><label for="name2" >Name</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[name2]" placeholder="" value="<?php echo $reference->name2;?>" /></td>
							<td><label for="phone2" >Phone</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[phone2]" placeholder="" value="<?php echo $reference->phone2;?>" /></td>
							<td><label for="city2" >City</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[city2]" placeholder="" value="<?php echo $reference->city2;?>" /></td>
							<td><label for="state2" >State</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[state2]" placeholder="" value="<?php echo $reference->state2;?>" /></td>
						</tr>
						<tr>
							<td><label for="name3" >Name</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[name3]" placeholder="" value="<?php echo $reference->name3;?>" /></td>
							<td><label for="phone3" >Phone</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[phone3]" placeholder="" value="<?php echo $reference->phone3;?>" /></td>
							<td><label for="city3" >City</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[city3]" placeholder="" value="<?php echo $reference->city3;?>" /></td>
							<td><label for="state3" >State</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[state3]" placeholder="" value="<?php echo $reference->state3;?>" /></td>
						</tr>
						<tr>
							<td><label for="name4" >Name</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[name4]" placeholder="" value="<?php echo $reference->name4;?>" /></td>
							<td><label for="phone4" >Phone</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[phone4]" placeholder="" value="<?php echo $reference->phone4;?>" /></td>
							<td><label for="city4" >City</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[city4]" placeholder="" value="<?php echo $reference->city4;?>" /></td>
							<td><label for="state4" >State</label></td>
							<td style="width:25%"><input style="width:60%" name="reference[state4]" placeholder="" value="<?php echo $reference->state4;?>" /></td>
						</tr>
					</table>
            </div>
        </div>
        <!-- END TAB CONTENT -->
        <br>

        <!-- SUBMIT PRODUCT -->
        <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Save</button>
        <!-- SUBMIT PRODUCT -->

        <!-- SUBMIT DISABLED 
        <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>
        
        <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->


        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
<style>
.small-hndr {width:100px !important;}
.frst {margin-left: 55px !important;}
.inr-td {width:200px;}
</style>
<script type="text/javascript">
	$(document).on('keyup','.sm', function() {
		var ttl = 0;
		$('.sm').each(function() {
			if($(this).val()) {
				ttl = parseInt($(this).val())+ttl;
			}
			//alert();
		});
		$('.sm-ttl').val(ttl);
		
		var cst = parseInt($('.ttl-cst').val());
		var sale = parseInt($('.sl-prc').val());
		var mrgn = parseFloat((cst*100)/sale).toFixed(2);
		$('.mrgn').val(mrgn);
		if( cst > 0 && sale > 0 ) {
			$('.prft').val(sale-cst);
		}
	});
	
	$(document).on('keyup','.mlg', function() {
		var vl = $(this).val();
		if( vl != '' ) {
			$('.eh').val('');
			$('.eh').attr('readonly', true);
		} else {
			$('.eh').attr('readonly', false);
		}
	});
	
	$(document).on('keyup','.eh', function() {
		var vl = $(this).val();
		if( vl != '' ) {
			$('.mlg').attr('readonly', true);
			$('.mlg').val('');
		} else {
			$('.mlg').attr('readonly', false);
		}
	});
	
	//ttl-1
	$(document).on('keyup','.ttl-1', function() {
		var ttl = "";
		$('.ttl-1').each(function() {
			if($(this).val()) {
				ttl = ttl + ' ' + $(this).val();
			}
			//alert();
		});
		$('.ttl').val(ttl);
	});
	
	$(document).on('keyup', '.ttl-cst', function() {
		var cst = parseInt($(this).val());
		var sale = parseInt($('.sl-prc').val());
		if( cst > 0 && sale > 0 ) {
			$('.prft').val(sale-cst);
		}
	});
	$(document).on('keyup', '.sl-prc', function() {
		var cst = parseInt($('.ttl-cst').val());
		var sale = parseInt($(this).val());
		var mrgn = parseFloat((cst*100)/sale).toFixed(2);
		$('.mrgn').val(mrgn);
		if( cst > 0 && sale > 0 ) {
			$('.prft').val(sale-cst);
		}
	});

	
    $("#sortable").sortable({
        revert: true,
        stop: function (event, ui) {
            if (!ui.item.data('tag') && !ui.item.data('handle')) {
                ui.item.data('tag', true);
            }
        },
        receive: function (event, ui) {
            $("ul#sortable").find('.dragRemove').css("display", "inline");
        }
    }).droppable({});
    $(".draggable").draggable({
        connectToSortable: '#sortable',
        helper: 'clone',
        revert: 'invalid'
    });

    $("ul, li").disableSelection();

    function removeCategory()
    {
        $(this).remove();
    }

</script>

