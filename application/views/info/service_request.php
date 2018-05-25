		<h1 style="color:#3f51b5">
			<span style="margin-right:10px;" class="glyphicon <?php echo $pageRec['icon'];?>"></span><?php echo $pageRec['label'];?>
		</h1>

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


  	<!-- Service FORM-->
		<?php echo form_open(secure_site_url('/pages/index/servicerequest'), array('class' => 'form_standard', 'id' => 'form_example', 'autocomplete' => 'off')); ?>
			<div class="name_box_full">
				<h5 style="text-align:center">Asterick indicates required field</h5>
				<div class="clear"></div>
				<div class="hidden_table">
					<table width="100%" cellpadding="5">
						<tr>
							<td>
								<label for="fname" style="display:none">First Name</label>
								<input id="fname" name="fname" placeholder="First Name" class="text reg" value="<?php echo $_POST['fname'];?>" />
								<div class="star">*</div>
							</td>
							<td>
								<label for="lname" style="display:none">Last Name</label>
								<input id="lname" name="lname" placeholder="Last Name" class="text reg" value="<?php echo $_POST['lname'];?>" />
								<div class="star">*</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="email" style="display:none">Email</label>
								<input id="email" name="email" placeholder="Email" class="text reg" value="<?php echo $_POST['email'];?>" />
								<div class="star">*</div>
							</td>
							<td>
								<input id="phone" name="phone" placeholder="Phone" class="text reg" value="<?php echo $_POST['phone'];?>" />
								<div class="star">*</div>
							</td>
						</tr>
						<tr>
							<td>
								<input id="address" name="address" placeholder="Address" class="text reg" value="<?php echo $_POST['address'];?>" />
							</td>
							<td>
								<input id="city" name="city" placeholder="City" class="text reg" value="<?php echo $_POST['city'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<input id="state" name="state" placeholder="State" class="text reg" value="<?php echo $_POST['state'];?>" />
							</td>
							<td>
								<input id="zipcode" name="zipcode" placeholder="ZipCode" class="text reg" value="<?php echo $_POST['zipcode'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<h3 style="color:#3f51b5">Vehicle Being Serviced</h3>
							</td>
						</tr>
						<tr>
							<td>
								<label for="make" style="display:none">Make</label>
								<input name="make" placeholder="Make" class="text reg" value="<?php echo $_POST['make'];?>" />
								<div class="star">*</div>
							</td>
							<td>
								<label for="model" style="display:none">Model</label>
								<input name="model" placeholder="Model" class="text reg" value="<?php echo $_POST['model'];?>" />
								<div class="star">*</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="_year" style="display:none">Year</label>
								<input name="_year" placeholder="Year" class="text reg" value="<?php echo $_POST['_year'];?>" />
								<div class="star">*</div>
							</td>
							<td>
								<input id="vin" name="vin" placeholder="Vin#" class="text reg" value="<?php echo $_POST['vin'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<input id="miles" name="miles" placeholder="Miles/Hours" class="text reg" value="<?php echo $_POST['miles'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<h3 style="color:#3f51b5">Describe Service Needs</h3>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label for="needs" style="display:none">Needs</label>
								<textarea id="descr" name="needs" rows="3" placeholder="What kind of service do you need one ?" cols="50" style="height:50px;width:98%;"><?php echo $_POST['needs'];?></textarea>
								<div class="star">*</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label for="appointment" style="display:none">Appointment</label>
								<input name="appointment" type="date" placeholder="Appointment Date" class="text reg" value="<?php echo $_POST['appointment'];?>" />
							</td>
						</tr>
						<tr>
							<td>
								<h3 style="color:#3f51b5">Prior Service History</h3>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<p><b>Have we serviced your vehicle before ? &nbsp;&nbsp;</b></p>
								<input type="radio" id="yes" name="serviced" value="Yes" <?php echo $_POST['serviced'] == 'Yes' ? 'checked' : '';?> />
								<label for="yes">Yes</label>
								<input type="radio" id="no" name="serviced" value="No" <?php echo $_POST['serviced'] == 'No' ? 'checked' : '';?>/>
								<label for="no">No</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input name="lastin" placeholder="Last In" class="text reg" value="<?php echo $_POST['lastin'];?>" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<textarea name="workdone" placeholder="Work Done" rows="3" cols="50" style="height:50px;width:98%;"><?php echo $_POST['workdone'];?></textarea>
							</td>
						</tr>

					</table>
				</div>
			</div>
			<div style="width:100%;text-align:center;margin-top: 40px;">
				<input style="float: inherit;background:#3f51b5;color:white;font-weight: normal;padding: 20px 90px 20px 90px;border-radius: 0;" type="submit" value="SUBMIT" class="input_button">
			</div>
		</form>
	<script>
		$(document).ready(function(){
			var success = '<?php echo @$success; ?>';
			//alert(success);
			if(success==1)
			{
				//alert(success);
				$(":input").val('');
				$(".input_button").val('SUBMIT');
			}
		});
	</script>

<?php $this->view('modals/customer_exit_modal.php'); ?>
