<!-- ADD IMAGE-->
<div class="modal_wrap hide" id="add_image_box">
	
	<!-- MODAL -->
	<div class="modal_box">
		
		<h1>Add Product Image</h1>
		
		<!-- VALIDATION ALERT -->
		<div class='validation_error hide' id="login_validation_error">
	    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <p><div id="login_error_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END VALIDATION ALERT -->
				
<?php $attributes = array('class' => 'form_standard'); 
  echo form_open_multipart('admin/add_image', $attributes);?>		
		<?php echo form_hidden('table', $table); ?>
    <?php echo form_hidden('id', $id); ?>
		    
      <input type="file" name="userfile" size="20" />
      <br /><br />
      <input type="submit" value="Upload Image" class="input_button" />
		</form>
      <div class="dynamic_button">
				<a href="javascript:void(0);" onclick="$.modal.close();">Cancel</a>
			</div>
			<div class="clear"></div>
		
	</div>
	<!-- MODAL -->
	
</div>
<!-- END IMAGE -->