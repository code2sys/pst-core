<!-- LOGIN MODAL-->
<div class="modal_wrap" id="login_box">
	
	<!-- MODAL -->
	<div class="modal_box" style="width:300px">
		
		<h1><i class="fa fa-list"></i> &nbsp; Create Account</h1>	
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
		    <p>We have been unable to process this request.  Please try again in a few minutes.</p>
		    <div class="clear"></div>
			</div>
			<!-- END PROCESS ERROR -->
			
			<div id="create_new">
				<form action="<?php echo $s_baseURL.'welcome/login'; ?>" method="post" id="user_form" class="form_standard">
					<b>First Name:</b><br><input id="first_name" name="first_name" class="text large"/><br />
					<b>Last Name:</b><br><input id="last_name" name="last_name" class="text large"/><br />
					<b>Email:</b><br><input id="email" name="email" class="text large"/><br />
					<b>Password:</b><br><input type="password" id="password" name="password" class="text large"/><br />
					<b>Confirm Password:</b><br><input type="password" id="confPassword" name="conf_password" class="text large"/>
				</form>
			</div>
		</div>
			
		  
		<a href="javascript:void(0);" class="button" onclick="submitNewUser();">Submit</a>
	    
	  <div class="clear"></div>
		
	</div>
	<!-- END LOGIN MODAL-->

<script>
  /* Submit on Enter */
  $(document).ready(function()
  {
    $('#confPassword').keyup(function(e)
    {
      if(e.keyCode == 13)
      {
        submitNewUser();
        return false;
      }
    });
  });
  
    function submitNewUser()
	{
	  	var data = $('#user_form').serialize(); 
		$.post(s_base_url + 'welcome/process_new_account/',
			data,
			function(encodeResponse)
			{
				  responseData = JSON.parse(encodeResponse);
				  
				  if(responseData['error'] == true)
				  {
		        $('#login_error_message').html(responseData['error_message']);
		        $('#login_validation_error').fadeIn(1000);
		        $('#simplemodal-container').height('auto'); 
		        $(window).resize();
		          $('#login_validation_error').delay(2000).fadeOut(3000, function (){});
			  }
			  else
			  {
		          $('#login_success_message').html(responseData['success_message']);
		          $('#login_validation_success').fadeIn(500, function()
		            {
		              $('#login_validation_success').fadeOut(1000, function (){
		                $.modal.close();
		                window.location.replace(base_url);
		              });
	            });
		          $('#simplemodal-container').height('auto');		  
			  }
	   	});
	  return false;
	}
  
</script>

</div>

