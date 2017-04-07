	<!-- MAIN CONTENT =======================================================================================-->
	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-envelope-o"></i>&nbsp;Emails</h1>
			<p><b>Manage Emails</b></p>
			<br>
			
			<!-- ERROR -->
		  <?php if(validation_errors() || @$errors): ?>
			<div class="error">
				<h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
				<p><?php echo validation_errors(); if(@$errors): foreach($errors as $error): echo $error; endforeach; endif; ?> </p>
			</div>
			<?php endif; ?>
			<!-- END ERROR -->
			
			<!-- SUCCESS -->
			<?php if(@$success): ?>
			<div class="success">
				<h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
				<p><?php echo $success; ?></p>
			</div>
			<?php endif; ?>
			<!-- END SUCCESS -->	
			
			<!-- TABS -->
			<div class="tab">
				<ul>
					<li><a href="#" class="active"><i class="fa fa-bars"></i>&nbsp;Email Options</a></li>
<!--
					<li><a href="#" class="active"><i class="fa fa-envelope-o"></i>&nbsp;Dirt Bike Image Slider</a></li>
					<li><a href="#"><i class="fa fa-envelope-o"></i>&nbsp;ATV Image Slider</a></li>
-->
					<div class="clear"></div>
				</ul>
			</div>
			<!-- END TABS -->
			
			
			<!-- TAB CONTENT -->
			<div class="tab_content">
				<?php echo form_open_multipart('admin_content/email', array('class' => 'form_standard', 'id' => 'admin_email_form')); ?>  
				<?php echo form_hidden('post', '1'); ?>
				<!-- GENERIC EMAIL OPTIONS -->
				<p><b>Generic Email Options</b></p>

				<p>
					<b>Upload Email Logo:</b><br>
					<?php echo form_upload(array('name' => 'email_logo', 'value' => set_value('email_logo'), 'maxlength' => 50, 'class' => '')); ?>
				</p>
				
			<?php if($emailSettings['email_logo']): if(file_exists($upload_path.'/'.$emailSettings['email_logo'])): ?>
				<div class="image_holder">
					<img src="<?php echo base_url( $media.'/'.$emailSettings['email_logo']); ?>" height="120">
				</div>	
			<?php endif; endif; ?>
			
			<div class="divider"></div>
			<!-- END GENERIC EMAIL OPTIONS -->
			
                
				<p><b> Mass Email / Newsletter</b></p>                
				<?php echo form_textarea(array('name' => 'mass_email_text', 
                              			                                                      'value' => set_value('mass_email_text'),
                              			                                                      'placeholder' => 'Email Text',
                              			                                                      'style' => 'height:100px; width:80%;')); ?><br />
                              			                                                      
                
                <?php echo form_checkbox('mass_email_attachment_toggle', 1, set_value('mass_email_attachment'), 'onchange="toggleAttachment();" id="attachment_toggle"'); ?> Attach Document to Mass Email<br /><br />
                
              <!-- Upload Functionality for Mass Email Doc if checkbox is selected -->
              <div id="attachment" <?php if(!$emailSettings['mass_email_attachment']): ?> class="hide" <?php endif; ?>> 
              	Optional Upload Document to Attach: <?php echo form_upload(array('name' => 'mass_email_attachment', 'value' => set_value('mass_email_attachment_doc'), 'maxlength' => 50, 'class' => '')); ?>
              	<?php if($emailSettings['mass_email_attachment_doc']): if(file_exists($upload_path.'/'.$emailSettings['mass_email_attachment_doc'])): ?>
				<?php echo $emailSettings['mass_email_attachment_doc']; ?><br />
			<?php endif; endif; ?>
              </div>
              <!-- END Upload Functionality for Mass Email Doc -->
              
              <br />
              <p><b>Mass Email Recipiants</b></p>
				<?php echo form_radio('mass_email_list', 'all', set_radio('mass_email_list'), 'onclick="toggleCustomListOff();"'); ?> All Active Users
				<?php echo form_radio('mass_email_list', 'newsletter', set_radio('mass_email_list'), 'onclick="toggleCustomListOff();"'); ?> All Users Requesting Newsletter<br />
				<?php echo form_radio('mass_email_list', 'custom', set_radio('mass_email_list'), 'onclick="toggleCustomListOn();"'); ?> Upload Comma Separated List<br />
				
				<div id="email_list_doc" <?php if($emailSettings['mass_email_list'] != 'custom'): ?> class="hide" <?php endif; ?>> 
				 	<?php echo form_upload(array('name' => 'mass_email_list_doc', 'value' => set_value('mass_email_list_doc', ''), 'maxlength' => 50, 'class' => '')); ?> 
				</div>
				
				<br />
				
               <button type="submit" id="button">Submit Changes</button>
               <div class="clear"></div>
			</form>

			
		</div>
	</div>
	<!-- END MAIN CONTENT ==================================================================================-->
	
	<script>
	
		function toggleAttachment()
		{
			$('#attachment').toggle();
		}
		
		function toggleCustomListOn()
		{
			$('#email_list_doc').show();
		}
				
		function toggleCustomListOff()
		{
			$('#email_list_doc').hide();
		}
	</script>