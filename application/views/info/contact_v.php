
 <h1>Contact Us</h1>
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
  
  	
  	<!-- CONTACT FORM-->
			<div class="name_box_full">
				<h3>Fill Out Form And Send Us Your Message</h3>
				<div class="clear"></div>
				<?php echo form_open('pages/index/contactus', array('class' => 'form_standard', 'id' => 'form_example')); ?>
				<div class="hidden_table">
					<table width="100%" cellpadding="5">
						<tr>
							<td>
								<b>Name: </b>
								<input id="name" name="name" placeholder="Your Name" class="text reg" value="<?php echo set_value('name'); ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<b>Email: </b>
								<input id="email" name="email" placeholder="Your Email" class="text reg" value="<?php echo set_value('email'); ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<b>Subject: </b>
								<input id="subject" name="subject" placeholder="Subject" class="text reg" value="<?php echo set_value('subject', ''); ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<b>Message: </b><br>
								<textarea id="descr" name="message" rows="6" cols="50" style="width:97.8%;"><?php echo set_value('message', ''); ?></textarea>
							</td>
						</tr>
						<tr>
							<td>
                 <b>Please solve the following math question so that we can be sure that you are human.</b><br /><br />
                 <b>What is <?php echo @$captcha['first']; ?> + <?php echo @$captcha['second']; ?> = ?</b>
                 <input type="hidden" name="encrypted_answer" value="<?php echo @$captcha['encrypted_answer']; ?>" /><br />
                 <b>Answer: <input type="text" name="user_answer" value="" class="text mini"/></b>
							</td>
						</tr>
						<tr>
							<td><input type="submit" value="Submit" class="input_button" style="margin-top:10px"></td>
						</tr>
					</table>
					</form>
				</div>
			</div>
			
