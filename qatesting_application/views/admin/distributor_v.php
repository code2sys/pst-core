		<!-- CONTENT -->
		<div class="content">	
						
			
			<!-- ADMIN SECTION -->
			<div style="width:95.6%;">
				
				<h1>Manage Distributor Accounts</h1>
				<p>
					Edit Distributor Accounts.
				</p>

		<!-- VALIDATION ALERT -->
		<div class='validation_error hide' id="val_container">
	    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <div class="clear"></div>
	    <p><div id="val_error_message"></div></p>
		</div>
		<!-- END VALIDATION ALERT -->
		
		<!-- PHP ALERT -->
      <?php if(validation_errors()): ?>
        <div class="validation_error">
        <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
          <h1>Error</h1>
	    <div class="clear"></div>
          <?php echo validation_errors(); ?>
        </div>
        <br />
      <?php endif; ?>					
					<div class="clear"></div>
				
					<br>
			<!-- DISTRIBUTORS -->
			
			<div class="tabular_data">
				<table cellpadding="3" style="width:100%;">
					<tr class="head_row"><td><b>Name</b></td>
															<td><b>Dealer Number</b></td>
															<td><b>Username</b></td>
															<td><b>Password</b></td>
															<td><b>Account #</b></td>
															<td><b>Site</b></td>
															<td><b>Actions</b></td>
					</tr>
					<?php if(@$distributors): foreach($distributors as $dis): ?>
						<tr>
							<td>
									<form action="<?php echo base_url('admin/distributors'); ?>" method="post" class="form_standard">
									<?php echo $dis['label']; echo form_hidden('id', $dis['id']); ?>
							</td>
							<td></td>
							<td><input id="username" name="username" value="<?php echo $dis['username']; ?>" class="text large" /></td>
							<td><?php echo form_password('password', $dis['password'], 'class="text large"'); ?></td>
							<td></td>
							<td><?php echo $dis['url']; ?></td>
							<td><?php echo form_submit('submit', 'Submit'); ?></form></td>
						</tr>
					<?php endforeach; endif; ?>
				</table>
			</div>
			
		</div>
	</div>