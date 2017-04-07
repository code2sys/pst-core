<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		<!-- MAIN CONTENT -->
		<div class="main_content">
			<h1>Sign In</h1>
			
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
			
			<?php if(@$processingError): ?>
		  <!-- PROCESS ERROR -->
  		<div class="process_error hide">
  		  <img src="<?php echo $assets; ?>/images/alert.png" style="float:left;margin-right:10px;">
  	    <h1>Alert!!!</h1>
  	    <div class="clear"></div>

  	    <p>We have been unable to process this request.  Please try again in a few minutes.</p>
  		</div>
  		<!-- END PROCESS ERROR -->
			<?php endif; ?>
			
			<!-- SIGN IN -->
			<div class="name_box_left">
				<h3>Sign In</h3>
			<form action="<?php echo $s_baseURL.'welcome/login'; ?>" method="post" id="form_example" class="form_standard">
					<p><b>Username</b></p>
					<input id="name" name="username" value="<?php echo @$username; ?>" placeholder="Enter Username" class="text reg" />
					<p><b>Password</b></p>
					<input type="password" id="password" name="password" value="<?php echo @$username; ?>" placeholder="Enter Password" class="text reg" />
					<input type="submit" value="Login" class="full_input_button" style="margin-top:10px">
					<p style="margin-top:4%;text-align:center;">
					  <a href="javascript:void(0);" onclick="$('.name_box_right').show();">( I Forgot My Password? )</a>
				  </p>
				</form>
			</div>
			<!-- END SIGN IN -->
			
			<!--
<div class="or">
				<h3>-OR-</h3>
			</div>
-->
			
			<!-- SIGN UP -->
			<div class="name_box_right hide">
				<h3>Forgot Password</h3>
				<p>
				  To reset your Password please follow the steps below. <br />
					1. Enter your username in the Username Box below.<br />
					2. Click the Send Email button below.<br />
					An email will be sent to your registration email address.<br />
					3. Click on the Reset Password button in the email.<br />
					You can then reset your password.
				</p>
				<form action="/welcome/forgot_password" method="post" id="form_example" class="form_standard">
          <p><b>Username</b></p>
					<input id="name" name="username" value="<?php echo @$username; ?>" placeholder="Enter Username" class="text reg" />
				<p style="margin-top:4%;text-align:center;">
					<input type="submit" value="Send Email" class="full_input_button" style="margin-top:-5px">
				</p>
				</form>
			</div>
			
			<!-- END SIGN UP -->
			<div class="clear"></div>
			
		</div>
		<!-- END MAIN CONTENT -->
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
</div>