	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- ACCOUNT -->
		<div class="main_content_full">

			<!-- ACCOUNT SECTION -->
			<div class="main_sect">
					<?php if(validation_errors()): ?>	
					<!-- VALIDATION ERROR -->
					<div class="validation_error">
						<img src="<?php echo $assets; ?>/images/error.png">
						<h1>Error</h1>
						<div class="clear"></div>
						<p><?php echo validation_errors(); ?></p>
					</div>
					<!-- END VALIDATION ERROR -->
					<?php endif; ?>
				
		      <?php if(@$processError): ?>
					<!-- PROCESS ERROR -->
					<div class="process_error">
						<img src="<?php echo $assets; ?>/images/process_error.png">
						<h1>Error</h1>
						<div class="clear"></div>
						<p>Our system is having trouble processing your request  Please try again in a few minutes.</p>
					</div>
					<!-- END PROCESS ERROR -->
					<?php endif; ?>
					<!-- SIGN UP FORM-->

			
			
					<form action="<?php echo $s_baseURL.'welcome/new_account/create'; ?>" method="post" class="form_standard">
			<div class="cart_wrap_left">
				<h3 style="float:left;margin:5px 0 0;">
					<i class="fa fa-list"></i> Create an Account
				</h3>
				<div class="clear"></div>
				<br>
				
				<div class="hidden_table">
					
					<table width="100%" cellpadding="6">
						<tr><td colspan="2"><h3>Fill Out Form To Register</h3></td></tr>
						<tr>
							<td><b>First Name:</b></td>
						  <td><?php echo form_input(array('name' => 'first_name', 
                              'value' => set_value('first_name'), 
                              'class' => 'text reg', 
                              'placeholder' => 'Enter First Name',
                              'style' => 'width:90%')); ?></td>
						</tr>
						<tr>
							<td><b>Last Name:</b></td>
						  <td><?php echo form_input(array('name' => 'last_name', 
                              'value' => set_value('last_name'), 
                              'class' => 'text reg', 
                              'placeholder' => 'Enter Last Name',
                              'style' => 'width:90%')); ?></td>
						</tr>
						
						<tr>
							<td ><b>Email:</b></td>
						  <td><?php echo form_input(array('name' => 'email', 
                              'value' => set_value('email'), 
                              'class' => 'text reg', 
                              'placeholder' => 'Enter Email',
                              'style' => 'width:90%')); ?></td>
						</tr>
						<tr>
							<td><b>Password:</b></td>
						  <td><?php echo form_password(array('name' => 'password', 
                              'value' => set_value('password'), 
                              'class' => 'text reg', 
                              'placeholder' => 'Enter Password',
                              'style' => 'width:90%')); ?></td>
						</tr>
						<tr>
							<td><b>Confirm Password:</b></td>
						  <td><?php echo form_password(array('name' => 'conf_password', 
                              'value' => set_value('conf_password'), 
                              'class' => 'text reg', 
                              'placeholder' => 'Confirm Password',
                              'style' => 'width:90%')); ?></td>
						</tr>
						<tr>
							<td colspan="2">
								<!-- CAPTCHA -->
								    <div>
											<div class="captcha">
								       <p><b>Please solve the following math question so that we can be sure that you are human</b></p>
								       <b>What is <?php echo @$captcha['first']; ?> + <?php echo @$captcha['second']; ?> = ?</b>
								       <input type="hidden" name="encrypted_answer" value="<?php echo @$captcha['encrypted_answer']; ?>" />
								       <b>Answer: <input type="text" name="user_answer" value="" class="text mini" style="background:#FFF;"/></b>
											</div>
								    </div>
													<!-- END CAPTCHA -->
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" value="Create Account" class="input_button">
							</td>
						</tr>
					</table>
				</div>
			</div>
					</form>



			<div class="cart_wrap_right">
		<form action="<?php echo $s_baseURL.'welcome/new_account/login'; ?>" method="post" id="form_example" class="form_standard">
			<h3 style="float:left;margin:5px 0 0;">
				<i class="fa fa-home"></i> Login to Your Account
			</h3>
				<div class="clear"></div>
				<br>

				<div class="hidden_table">
				<h3></h3>
					<table width="100%" cellpadding="6">
						<tr>
							<td><b>Email Address:</b></td>
							<td><?php echo form_input(array('name' => 'email', 
  	                              'value' =>'',
  	                              'class' => 'text reg',
  	                              'placeholder' => 'Enter Email Address',
  	                              'style' => 'width:90%')); ?>
  	          </td>
						</tr>
						<tr>
							<td ><b>Password:</b></td>
							<td><?php echo form_password(array('name' => 'password', 
  	                               'value' => '' , 
  	                               'class' => 'text reg',
  	                               'placeholder' => 'Enter Password',
  	                               'style' => 'width:90%')); ?>
  	                               								
  	                               
							</td>
						</tr>
						<tr>
							<td >
								<input type="submit" value="Login" class="input_button">
							</td>
							<td valign="top">
								<p><a href="javascript:void(0);" onclick="$('.name_box_right').show();">( I Forgot My Password? )</a></p>
							</td>
								</form>
							</td>
						</tr>
						<tr class="name_box_right hide">
							<td colspan="2">
								<h3>Forgot Password</h3>
								<p>
								  To reset your Password please follow the steps below. <br />
									1. Enter your Email Address in the Email Address Box below.<br />
									2. Click the Send Email button below.<br />
									If you have a valid account an email will be sent to you.<br />
									3. Click on the Reset Password button in the email.<br />
									You can then reset your password.
								</p>
								<form action="<?php echo $s_baseURL.'welcome/new_account/forgot'; ?>" method="post" id="form_example" class="form_standard">
				          <p><b>Email Address</b></p>
									<input id="name" name="email" value="<?php echo @$username; ?>" placeholder="Enter Email Address" class="text reg" />
								<p style="margin-top:4%;text-align:center;">
									<input type="submit" value="Send Email" class="input_button" style="margin-top:-5px">
								</p>
								</form>

							</td>
						</tr>
					</table>
				</div>
			</div>
			

		
	</div>
	</div>
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->


