<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-dashboard"></i>&nbsp;Social Media Settings</h1>
			<h3>Stay connected!</h3>
			<br>
			
			<!-- VALIDATION ALERT -->
			<?php if(validation_errors() || @$errors): ?>
			<div class="validation_error" id="login_validation_error">
			  <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
		    <h1>Error</h1>
		    <div class="clear"></div>
		    <p><?php echo validation_errors(); if(@$errors): foreach($errors as $error): echo $error; endforeach; endif; ?></p>
		    
			</div>
			<?php endif; ?>
			<!-- END VALIDATION ALERT -->
			
			<!-- SUCCESS MESSAGE -->
			<?php if(@$success): ?>
				<div class="success" id="login_validation_success">
				  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
			    <h1>Success</h1>
			    <div class="clear"></div>
			    <p>Your changes have been successfully submitted.</p>
				</div>
			<?php endif; ?>
			<!-- END SUCCESS MESSAGE -->
			
			<!-- PROCESS ERROR -->
			<div class="process_error hide">
			  <img src="<?php echo $assets; ?>/images/alert.png" style="float:left;margin-right:10px;">
		    <h1>Alert!!!</h1>
		    <div class="clear"></div>
		    <p>We have been unable to process this request.  Please try again in a few minutes.</p>
			</div>
			<!-- END PROCESS ERROR -->

			<form action="<?php echo base_url('admin_content/social_media'); ?>" method="post" id="form_example" class="form_standard">
				<div class="tabular_data">
					<table cellpadding="3" style="width:100%;">
						<tr><td>Facebook Link:</td><td><input id="label" name="sm_fblink" value="<?php echo @$SMSettings['sm_fblink']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Twitter Link:</td><td><input id="label" name="sm_twlink" value="<?php echo @$SMSettings['sm_twlink']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Blog Link:</td><td><input id="label" name="sm_blglink" value="<?php echo @$SMSettings['sm_blglink']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>YouTube Link:</td><td><input id="label" name="sm_ytlink" value="<?php echo @$SMSettings['sm_ytlink']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Google + Link:</td><td><input id="label" name="sm_gplink" value="<?php echo @$SMSettings['sm_gplink']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Google + Page Badge Id:</td><td><input id="label" name="sm_gpid" value="<?php echo @$SMSettings['sm_gpid']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Linkedin Link:</td><td><input id="label" name="sm_lilink" value="<?php echo @$SMSettings['sm_lilink']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Pinterest Link:</td><td><input id="label" name="sm_pilink" value="<?php echo @$SMSettings['sm_pilink']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Instragram Link:</td><td><input id="label" name="sm_insta" value="<?php echo @$SMSettings['sm_insta']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Amazon Link:</td><td><input id="label" name="sm_amazon" value="<?php echo @$SMSettings['sm_amazon']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>eBay Link:</td><td><input id="label" name="sm_ebay" value="<?php echo @$SMSettings['sm_ebay']; ?>" class="text large" style="width:400px;" /></td></tr>
						<tr><td>Snap Chat Link:</td><td><input id="label" name="sm_snapchat" value="<?php echo @$SMSettings['sm_snapchat']; ?>" class="text large" style="width:400px;" /></td></tr>
                        <tr><td>Display Links:</td><td><label><input type="radio" name="sm_show_upper_link" value="1" <?php if (array_key_exists("sm_show_upper_link", $SMSettings) && $SMSettings["sm_show_upper_link"] == 1): ?>checked="checked"<?php endif; ?> /> Header & Footer</label> <label><input type="radio" name="sm_show_upper_link" value="0" <?php if (!array_key_exists("sm_show_upper_link", $SMSettings) || $SMSettings["sm_show_upper_link"] != 1): ?>checked="checked"<?php endif; ?> /> Footer Only</label> </td></tr>
					</table>
				</div>
				<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Submit</button>
			</form>
			
		</div>
	</div>