<!-- CHANGE PW MODAL-->
<div class="modal_wrap hide" id="new_password_box">
	
	<!-- MODAL -->
	<div class="modal_box">
		
		<h1>Change Password</h1>
		<p>Please create a new password using the fields below.</p>
		
		<!-- VALIDATION ALERT -->
		<div class='validation_error hide' id="password_validation_error">
		<img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <p><div id="new_password_error_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END VALIDATION ALERT -->
		
		<!-- SUCCESS MESSAGE -->
		<div class="success hide" id="password_validation_success">
		  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
	    <h1>Success</h1>
	    <p><div id="password_success_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END SUCCESS MESSAGE -->
		
		<form action="/welcome/new_password" method="post" id="passwordForm" class="form_standard">
			<input id="username" placeholder="User Name" name="username" class="text large"/>
			<input id="email" placeholder="Email" name="lost_password_email" class="text large"/>
			<input type="password" id="password" placeholder="Password" name="password" class="text large"/>
			<input type="password" id="conf_password" placeholder="Confirm Password" name="conf_password" class="text large"/>
			</br></br>
			<div class="dynamic_button">
        <a href="javascript:void(0);" onclick="submitNewPassword();" style="float:right;">Create New Password</a>
      </div>
			<div class="dynamic_button">
				<a href="javascript:void(0);" onclick="$.modal.close();">Cancel</a>
			</div>
			<div class="clear"></div>
		</form>
		
	</div>
	<!-- MODAL -->
	
</div>
<!-- END CHANGE PW MODAL-->



