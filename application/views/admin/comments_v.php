	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-dashboard"></i>&nbsp;Comments</h1>
			<h3>What are your customers saying!</h3>
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
			<div class="success hide" id="login_validation_success">
			  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
		    <h1>Success</h1>
		    <div class="clear"></div>
		    <p><div id="login_success_message"></div></p>
			</div>
			<!-- END SUCCESS MESSAGE -->
			
			<!-- PROCESS ERROR -->
			<div class="process_error hide">
			  <img src="<?php echo $assets; ?>/images/alert.png" style="float:left;margin-right:10px;">
		    <h1>Alert!!!</h1>
		    <div class="clear"></div>
		    <p>We have been unable to process this request.  Please try again in a few minutes.</p>
			</div>
			<!-- END PROCESS ERROR -->
			<form action="<?php echo base_url('admin_content/reviews'); ?>" method="post" id="form_example" class="form_standard">
				<div class="hidden_table">
					<table width="100%" cellpadding="6">
						<tr><td>Date</td>
								<td>Email Address</td>
								<td>Product</td>
								<td>Review</td>
								<td width="150px">Action</td>
						</tr>
						<?php if(@$reviews): foreach($reviews as $review): ?>
							<tr><td><?php echo date('m/d/Y H:i:s', $review['date']); ?></td>
									<td><?php echo $review['username'] ? $review['username'] : 'Anonymous'; ?></td>
									<td><?php echo $review['name']; ?></td>
									<td><?php echo $review['review']; ?></td>
									<td><a href="<?php echo base_url('admin_content/review_approval/'.$review['id']); ?>">Approve</a> - <a href="<?php echo base_url('admin_content/review_reject/'.$review['id']); ?>">Reject</a></td>
							</tr>
						<?php endforeach; endif; ?>
					</table>
				</div>
			</form>
		</div>
	</div>