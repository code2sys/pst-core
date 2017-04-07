<!-- CONTENT -->
<div class="content">	
	
	<!-- ACCOUNT SECTION -->
	<div class="main_sect">
		
		<h1><?php echo @$_SESSION['userRecord']['username']; ?> Account</h1>
		<p>
			To edit your account information just click in any of the 
			fields below and click submit to save changes.
		</p>
		<br>
		
		<h3>Account Info</h3>
	 

<?php if(@$validationErrors): ?>		
		<!-- VALIDATION ALERT -->
		<div class="validation_error">
		<img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <p><?php echo $validationErrors; ?></p>
	    <div class="clear"></div>
		</div>
		<!-- END VALIDATION ALERT -->
<?php endif; ?>
		
    <?php echo form_open('welcome/process_new_account', array('class' => 'form_standard')); ?>
		<?php echo form_input(array('name' => 'username', 
                                'value' => @$_SESSION['userRecord']['username'], 
                                'class' => 'text large', 
                                'placeholder' => 'User Name')); ?>
    <?php echo form_input(array('name' => 'password', 
                                'value' =>'', 
                                'class' => 'text large', 
                                'placeholder' => 'Password')); ?>
      <?php echo form_input(array('name' => 'conf_password', 
                                'value' => '', 
                                'class' => 'text large', 
                                'placeholder' => 'Confirm Password')); ?>
			<a href="javascript:void(0);" onclick="forgotPassword();"><u>Change password</u></a>
      
			</br></br>
			<div class="clear"></div>
		
		<!-- DIVIDER -->
		<div class="divider"></div>
		<!-- END DIVIDER -->
		
		<h3>Billing Info</h3>
		<p>Fill out your billing information to streamline your order process.</p>
	
		
  <?php echo form_input(array('name' => 'first_name', 
                              'value' => @$form['first_name'], 
                              'class' => 'text large', 
                              'placeholder' => 'First Name')); ?>
  <?php echo form_input(array('name' => 'last_name', 
                              'value' => @$form['last_name'], 
                              'class' => 'text large',
                              'placeholder' => 'Last Name')); ?>
  <?php echo form_input(array('name' => 'email', 
                              'value' => @$form['email'], 
                              'class' => 'text large',
                              'placeholder' => 'Email')); ?>
  <?php echo form_input(array('name' => 'phone', 
                              'value' => @$form['phone'], 
                              'class' => 'text large',
                              'placeholder' => 'Telephone')); ?>  
  <?php echo form_input(array('name' => 'company', 
                              'value' => @$form['company'], 
                              'class' => 'text large',
                              'placeholder' => 'Company')); ?>
  <?php echo form_input(array('name' => 'fax', 
                              'value' => @$form['fax'], 
                              'class' => 'text large',
                              'placeholder' => 'Fax')); ?> <br />
  <?php echo form_input(array('name' => 'street_address', 
                              'value' => @$form['street_address'], 
                              'class' => 'text large',
                              'placeholder' => 'Address')); ?>     
  <?php echo form_input(array('name' => 'address_2', 
                              'value' => @$form['address_2'], 
                              'class' => 'text large',
                              'placeholder' => 'Apt/Bld/Ste')); ?> </br>
  <?php echo form_input(array('name' => 'city', 
                              'value' => @$form['city'], 
                              'class' => 'text large',
                              'placeholder' => 'City')); ?>   
  <?php echo form_input(array('name' => 'state', 
                              'value' => @$form['state'], 
                              'class' => 'text mini',
                              'placeholder' => 'State')); ?>  
  <?php echo form_input(array('name' => 'zip', 
                              'value' => @$billingRecord['zip'], 
                              'class' => 'text mini',
                              'placeholder' => 'Zipcode')); ?>  <br />
  <?php echo form_input(array('name' => 'country', 
                              'value' => @$billingRecord['country'], 
                              'class' => 'text large',
                              'placeholder' => 'Country')); ?>     
                                                                                                                       
			</br></br>
			<?php echo form_submit('submit', 'Submit', 'class="input_button"'); ?>
			<div class="clear"></div>
		</form>
			
	</div>
	<!-- END ACCOUNT SECTION -->
	
	
</div>
<!-- END CONTENT -->
	
</div>
<!-- END CONTENT WRAP ===================================================================-->

<div class="clearfooter"></div>

