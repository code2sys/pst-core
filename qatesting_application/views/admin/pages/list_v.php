	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-dashboard"></i>&nbsp;Page Settings</h1>
			<h3>Create your space!</h3>
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
		    <p><?php echo $success; ?></p>
			</div>
			<?php endif; ?>
			<!-- END SUCCESS MESSAGE -->
			
			<a href="<?php echo base_url('pages/edit'); ?>" class="button">Create New</a>
			<div class="clear"></div>
			<div class="divider"></div>
			<form action="<?php echo base_url('admin_content/pages'); ?>" method="post" id="form_example" class="form_standard">
			<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Submit</button>
			<div class="clear"></div>
				<div class="hidden_table">	
					<table width="100%" cellpadding="6">
						<tr><td>Active</td><td>Page Name</td><td>Actions</td></tr>
						<?php if($pages): foreach($pages as $page): ?>
							<tr>
								<td>
								<?php if($page['delete']): ?>
									<?php echo form_checkbox('active[]', $page['id'], $page['active']); ?>
								<?php else: ?>
									Active
								<?php endif; ?>
								</td>
								<td>
									<?php echo $page['label']; ?>
								</td>
								<td>
									<a href="<?php echo base_url('pages/edit/'.$page['id']); ?>">Edit</a>
									<?php if($page['delete']): ?> | 
										<a href="<?php echo base_url('pages/delete/'.$page['id']); ?>">Delete</a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; endif; ?>
					</table>
				</div>
			</form>
			
		</div>
	</div>