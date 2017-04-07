<!-- LOGIN MODAL-->
<div class="modal_wrap" id="login_box">
	
	<!-- MODAL -->
	<div class="modal_box">
		
		<div style="padding:9px 15px;  border-bottom:1px solid #eee; text-align: center"><h1><i class="fa fa-arrow-circle-o-right"></i>&nbsp;Login to Your Account</h1></div>
		<br>
		
		<div class="modal-body">
			<!-- VALIDATION ALERT -->
			<div class="validation_error hide" id="login_validation_error">
			  <img src="<?php echo $s_assets; ?>/images/error.png" style="float:left;margin-right:10px;">
		    <h1>Error</h1>
		    <div class="clear"></div>
		    <p><div id="login_error_message"></div></p>
		    
			</div>
			<!-- END VALIDATION ALERT -->
			
			<!-- SUCCESS MESSAGE -->
			<div class="success hide" id="login_validation_success">
			  <img src="<?php echo $s_assets; ?>/images/success.png" style="float:left;margin-right:10px;">
		    <h1>Success</h1>
		    <div class="clear"></div>
		    <p><div id="login_success_message"></div></p>
			</div>
			<!-- END SUCCESS MESSAGE -->
			
			<!-- PROCESS ERROR -->
			<div class="process_error hide">
			  <img src="<?php echo $s_assets; ?>/images/alert.png" style="float:left;margin-right:10px;">
		    <h1>Alert!!!</h1>
		    <div class="clear"></div>
		    <p>We have been unable to process this request.  Please try again in a few minutes.</p>
			</div>
			
			<!-- END PROCESS ERROR -->

			<div class="login hidden_table">
				<form action="<?php echo $s_baseURL.'welcome/modal_login'; ?>" method="post" id="login_form" class="form_standard">
				<table>
				<tr>
					<td>Email Address:</td><td><input id="newusername" name="email" class="text large"/></td>
				</tr>
				<tr>
					<td>Password:</td><td><input type="password" id="loginPassword" name="password" class="text large"/></td>	
				</tr>
				</table>
					 
					<p style="font-size:12px;">
						<a href="javascript:void(0);" onclick="forgotPassword();"><u>Forget Your Password?</u></a>
					</p>
				</form>
			</div>
			
			<div class="forgot hide">
				<p>
				  <b>Password Reset.</b> <br />
					<b>1.</b> Enter your Email Address in the Field below.<br />
					<b>2.</b> Click Send Email.<br />
					<b>3.</b> Click on the <em>Reset Password Button</em> in the email.
				</p>
					<form action="<?php echo $s_baseURL.'welcome/new_account/forgot'; ?>" method="post" id="forgot_password_form" class="form_standard">
					  <b>Email Address:</b>	<input id="forgotField" name="email" class="text large" />
					</form>
			</div>
			
			<div class="clear"></div>
		        <a href="javascript:void(0);" onclick="submitLogin();" style="float:right;" class="login button">Submit</a>
		        <a href="javascript:void(0);" onclick="submitForgotPassword();" style="float:right;" class="forgot hide button">Send Email</a>
	      <div class="clear"></div>
	</div>
	<!-- END LOGIN MODAL-->



















<script>
  /* Submit on Enter */
  $(document).ready(function(){
    $('#loginPassword').keyup(function(e){
      if(e.keyCode == 13)
      {
        submitLogin();
        return false;
      }
    });
    $('#forgotField').keyup(function(e){
	    if(e.keyCode == 13)
	      {
	        submitForgotPassword();
	        return false;
	      }
    });
  });
  
    function submitLogin()
	{
	  	var data = $('#login_form').serialize(); 
		$.post(s_base_url + 'welcome/modal_login/',
			data,
			function(encodeResponse)
			{
				  responseData = JSON.parse(encodeResponse);
				  
				  if(responseData['error'] == true)
				  {
			        $('#login_error_message').html(responseData['error_message']);
			        $('#login_validation_error').fadeIn();
			        $('#simplemodal-container').height('auto'); 
			        $(window).resize();
			        setTimeout( function(){
			          $('#login_validation_error').fadeOut(1000, function (){});
			         }, 2000); 
				  }
				  else
				  {
			          $('#login_success_message').html(responseData['success_message']);
			          $('#login_validation_success').fadeIn(500, function()
			            {
			              $('#login_success_message').fadeOut(1000, function (){
			                $.modal.close();
			                window.location.reload();
			              });
			            });
			          $('#simplemodal-container').height('auto');		  
				  }
		   	});
	  return false;
	}
	
	function forgotPassword()
	{
		$('.login').hide();
		$('.forgot').show();
		$(window).resize();
	}
	
	function submitForgotPassword()
	{
		var data = $('#forgot_password_form').serialize(); 
		$.post(s_base_url + 'welcome/modal_forgot_password',
			data,
			function(encodeResponse)
			{
				 responseData = JSON.parse(encodeResponse);
				  
				  if(responseData['error'] == true)
				  {
			        $('#login_error_message').html(responseData['error_message']);
			        $('#login_validation_error').fadeIn();
			        $('#simplemodal-container').height('auto'); 
			        $(window).resize();
			        setTimeout( function(){
			          $('#login_validation_error').fadeOut(1000, function (){});
			         }, 2000); 
				  }
				  else
				  {
			          $('#login_success_message').html(responseData['success_message']);
			          $('#login_validation_success').fadeIn(500, function()
			            {
			              $('#login_success_message').fadeOut(1000, function (){
			                $.modal.close();
			                window.location.replace(base_url);
			              });
			            });
			          $('#simplemodal-container').height('auto');		  
				  }
			});

	}

</script>

</div>

