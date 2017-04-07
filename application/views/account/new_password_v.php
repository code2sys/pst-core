	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- MAIN CONTENT -->
		<div class="main_content">
			<h1>Create New Password</h1>
			<p>
        If you have reached this page by clicking on your Forgot Password email you should see your Username in the form below. <br /> 
        If you have received an error message instead you may have an invalid or previously used code.  <br />
        Please verify that you have copied the URL from your email correctly.  <br />
        If you have done this and are still getting an error delete that email and return to the login page and complete the Forgot Password request area again.
			</p>
			
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
  		<div class="process_error">
  		  <img src="<?php echo $assets; ?>/images/alert.png" style="float:left;margin-right:10px;">
  	    <h1>Alert!!!</h1>
  	    <div class="clear"></div>
  	    <p>We have been unable to process this request.  Please try again in a few minutes.</p>
  		</div>
  		<!-- END PROCESS ERROR -->
			<?php endif; ?>
			
			<?php if(@$success): ?>
			<!-- SUCCESS MESSAGE -->
  		<div class="success">
  		  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
  	    <h1>Success</h1>
  	    <div class="clear"></div>
  	    <p>
  	      Your password has been successfully changed!
  	    </p>
  	    <div class="clear"></div>
  		</div>
		<!-- END SUCCESS MESSAGE -->
		<?php endif; ?>
			
			<!-- NEW PASSWORD-->
			<form action="/welcome/reset_password/<?php echo @$tempCode; ?>" method="post" id="form_example" class="form_standard">
			<div class="hidden_table">
				<table width="100%" cellpadding="5">
				  <tr>
						<td>
              <b>Email Address</b><br />
              <input id="name" name="email" value="<?php echo @$email; ?>" class="text medium" />
            </td>
					</tr>
					<tr>
						<td>
							<b>New Password:</b><br>
							<input type="password" id="name" name="password" placeholder="Password" class="text medium" />
						</td>
					</tr>
					<tr>
						<td>
							<b>Confirm Password:</b><br>
							<input type="password" id="name" name="conf_password" placeholder="Confirm Password" class="text medium" />
						</td>
					</tr>
					<tr>
						<td><input type="submit" value="Submit" class="input_button" style="margin-top:10px;float:left"></td>
					</tr>
				</table>
			</div>
			</form>
			<!-- END NEW PASSWORD -->
			
		</div>
		<!-- END MAIN CONTENT -->
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
	


</div>
<!-- END WRAPPER ==========================================================================-->


<script>
	$(window).load( function(){
		var leftHeight = $(".main_content").height();
		var rightHeight = $(".sidebar").height();
		if (leftHeight > rightHeight)
		{ 
			$(".sidebar").height(leftHeight);
		}
		else
		{ 
			$(".main_content").height(rightHeight);
		}
		
	});
</script>