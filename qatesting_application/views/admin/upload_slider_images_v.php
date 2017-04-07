	<!-- MAIN CONTENT =======================================================================================-->
	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-image"></i>&nbsp;Site Images</h1>
			<p><b>Upload and manage slider images.<br />
				Images must be 1024px wide by 400px high.
			</b></p>
			<br>
			
			<!-- ERROR -->
			<?php if(validation_errors() || @$errors): ?>
			<div class="error">
				<h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
				<p><?php echo validation_errors(); if(@$errors): foreach($errors as $error): echo $error; endforeach; endif; ?></p>
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
					<?php if(@$pages): foreach($pages as $id => $page): ?>
					<li><a href="<?php echo base_url('admin_content/images/'.$id); ?>" <?php if($id == $activePage): ?> class="active" <?php endif; ?>><i class="fa fa-image"></i>&nbsp;<?php echo $page; ?></a></li>
					<?php endforeach; endif; ?>
					<div class="clear"></div>
				</ul>
			</div>
			<!-- END TABS -->

	<!-- TAB CONTENT -->
	<?php echo form_open_multipart('admin_content/images/'. $activePage, array('class' => 'form_standard', 'id' => 'admin_banner_form')); ?>  
	<?php echo form_hidden('page', $activePage); ?>
			<div class="tab_content">
				<div class="hidden_table">
					<table width="auto" cellpadding="12">
						<tr>
							<td colspan="3">Images must be 1024px wide by 400px high.<br /><br />
								<?php echo form_upload(array('name' => 'image', 'value' => set_value('main'), 'maxlength' => 50, 'class' => '')); ?><br />
								<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Upload New Banner</button>
							</td>
						</tr>
						<?php if(@$bannerImages): foreach($bannerImages as $img): ?>
						<tr>
							<td valign="top" style="width:130px;"><b>Image Set 1:</b></td>
							<td><img src="<?php echo base_url($media); ?>/<?php echo $img['image']; ?>" width="200px"></td>
							<td valign="top">
								<b><a href="<?php echo base_url('admin_content/remove_image/'.$img['id'].'/'.$activePage); ?>">Remove Image</a></b>
							</td>
						</tr>
						<?php endforeach; endif; ?>
					</table>
				</div>
			</div>
			<!-- END TAB CONTENT -->
			<br>
			<!-- SUBMIT PRODUCT -->
			
									
			</form>
			
			
			
		</div>
	</div>
	<!-- END MAIN CONTENT ==================================================================================-->
	<div class="clearfooter"></div>