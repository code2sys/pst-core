	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-dashboard"></i>&nbsp;Coupon Settings</h1>
			<h3>Create and Edit Coupons</h3>
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

			<form action="<?php echo base_url('admin/coupon'); ?>" method="post" id="form_example" class="form_standard">
			<?php echo form_submit('submit', 'Submit'); ?>
				<div class="tabular_data">
					<table cellpadding="3" style="width:100%;">
					<tr class="head_row">
						<td><b>Active</b></td>
						<td><b>Code</b></td>
						<td><b>Start Date</b></td>
						<td><b>End Date</b></td>
						
						<td><b>Total Uses</b></td>
						<td><b>Current Uses</b></td>
						<td><b>%</b></td>
						<td><b>Set Value</b></td>
						
						<td><b>Google Promotion</b></td>
						<td><b>Brand</b></td>
						<td><b>Closeouts</b></td>
						
						
						<td><b>Assoc. Product</b></td>
						<td><b>Special Constraints</b></td>
						<td><b>Actions</b></td>
					</tr>
					
					<?php if(@$coupons): $i=0; foreach($coupons as $coupon): ?>
					<tr>
						  <td><?php echo form_checkbox('active', 1, $coupon['active']); ?></td>
					  <td><?php echo $coupon['couponCode']; ?></td>
					  <td><?php echo ($coupon['startDate']) ? date('Y/m/d', $coupon['startDate']) : ''; ?></td>
					  <td><?php echo ($coupon['endDate']) ? date('Y/m/d', $coupon['endDate']) : ''; ?></td>
					  <td><?php echo $coupon['totalUses']; ?></td>
					  <td><?php echo $coupon['currentUses']; ?></td>
					  <td><?php echo $coupon['percentage']; ?></td>
					  <td><?php echo $coupon['value']; ?></td>
					  
					  <td><?php echo (!empty($coupon['google_promotion'])) ? "Yes" : "No"; ?></td>
					  <td><?php echo $coupon['brand_name']; ?></td>
					  <td><?php echo (!empty($coupon['closeout'])) ? "Yes" : "No"; ?></td>
					  
					  
					  <td><?php echo $coupon['associatedProductSKU']; ?></td>
					  <td>
						  <?php if(@$coupon['couponSpecialConstraintsId']):
						          $constraints = json_decode($coupon['couponSpecialConstraintsId']);
						          if( count($constraints) >= 1 ):
						            foreach($constraints as $opt):
						              echo $specialConstraintsDD[$opt] . ', ';
                      endforeach;
                    endif;
						        endif; ?>
					  </td>
					  <td><a href="javascript:void(0);" onclick="populateEdit('<?php echo $coupon['id']; ?>')">Edit</a> | <a href="<?php echo base_url('admin/coupon_delete/'.$coupon['id']); ?>">Delete</a>
					<?php $i++; endforeach; endif; ?>
					</tr>
				</table>
			</div>
		</form>
			
			
			<br /><br />
			   <?php echo form_open('admin/coupon', array('class' => 'form_standard', 'id' => 'new_coupon_form')); ?>
			    <?php echo form_submit('create_new', 'Create New', 'class="new"'); ?>
				<?php echo form_submit('edit', 'Edit',  'class="edit hide"'); ?>
				<input type="hidden" name="id" id="id" value="">
			    <div class="tabular_data">
					<table cellpadding="3" style="width:100%;">
						<tr><td><b>Code:</b></td><td> <input type="text" placeholder="Coupon Code" id="couponCode" name="couponCode" value="<?php echo set_value('couponCode'); ?>" class="text medium" /></td></tr>
				      <tr><td><b>Start: </b></td><td><input type="text" placeholder="YYYY-MM-DD" id="startDate" name="startDate" value="<?php echo set_value('startDate'); ?>" class="text mini" /></td></tr>
				      <tr><td><b>End:</b></td><td><input type="text" placeholder="YYYY-MM-DD" id="endDate" name="endDate" value="<?php echo set_value('endDate'); ?>" class="text mini" /></td></tr>
				      <tr><td><b>Uses:</b></td><td><input type="text" placeholder="Total Uses" id="totalUses" name="totalUses" value="<?php echo set_value('totalUses'); ?>" class="text medium" /></td></tr>
				      <tr><td><b>Percentage:</b></td><td> <?php echo form_radio('type', 'percentage', set_checkbox('percentage'), 'class="checkbox" id="percentage"'); ?></td></tr>
				      <tr><td><b>Set Value:</b></td><td> <?php echo form_radio('type', 'value', set_checkbox('value'), 'class="checkbox" id="value"'); ?></td></tr>
				      <tr><td><b>Amount:</b></td><td> <input type="text" placeholder="Amount" name="amount" value="<?php echo set_value('amount'); ?>" class="text medium"  id="amount"/></td></tr>
					  
					  <tr><td><b>Google Promotion:</b></td><td>
					  <label style="cursor:pointer;"><?php echo form_radio('google_promotion', 1, set_checkbox('google_promotion'), 'class="checkbox" id="google_promotion1"'); ?><strong>Yes</strong></label>
					  <label style="cursor:pointer;"><?php echo form_radio('google_promotion', 0, set_checkbox('google_promotion'), 'class="checkbox" id="google_promotion2" checked="checked"'); ?><strong>No</strong></label>
					  </td></tr>
					  <tr><td><b>Brand:</b></td><td>
					  <select name="brand_id" id="brand_id">
					  <?php if(!empty($brands_list)){
					  			$selected = $this->input->post('package');
					  			foreach($brands_list as $bKey=>$brand){
					  ?>
					  				<option value="<?php echo $bKey."-_-".$brand;?>"><?php echo $brand;?></option>
					  <?php 	}
					  		}?>
					  </select>
					  </td></tr>
					  <tr><td><b>Closeouts:</b></td><td>
					  <label style="cursor:pointer;"><?php echo form_radio('closeout', 1, set_checkbox('closeout'), 'class="checkbox" id="closeout1"'); ?><strong>Yes</strong></label>
					  <label style="cursor:pointer;"><?php echo form_radio('closeout', 0, set_checkbox('closeout'), 'class="checkbox" id="closeout2" checked="checked"'); ?><strong>No</strong></label>
					  </td></tr>
					  
					  
					  
				      <tr><td><b>Associated Product SKU:</b></td><td> <input type="text" placeholder="Associated Product SKU" name="associatedProductSKU" id="associatedProductSKU" value="<?php echo set_value('associatedProductSKU'); ?>" class="text medium" /></td></tr>
				      <?php if(@$specialConstraints): foreach($specialConstraints as $opt):?>
				      <tr><td><b><?php echo $opt['displayName']; ?></b></td><td> <?php echo form_checkbox($opt['ruleName'], $opt['couponSpecialConstraintsId'], set_checkbox($opt['ruleName']), 'class="checkbox" id="'.$opt['couponSpecialConstraintsId'].'"'); ?> </td></tr>
				      <?php endforeach; endif; ?>
					</table>
			    </div>
							<?php echo form_submit('create_new', 'Create New', 'class="new"'); ?>
				<?php echo form_submit('edit', 'Edit',  'class="edit hide"'); ?>
		</form>
			
		</div>
	</div>

<script>
	function populateEdit(id)
	{
		
		$.post(base_url + 'admin/load_coupon/' + id,
			{},
			function(encodeResponse)
			{
				 responseData = JSON.parse(encodeResponse);
		  		  console.log(responseData);
	       		  $('.edit').show();
	       		  $('.new').hide();
	       		  $('#id').val(responseData['id']);
	       		  $('#couponCode').val(responseData['couponCode']);
	       		  $('#startDate').val(responseData['startDate']);
	       		  $('#endDate').val(responseData['endDate']);
	       		  $('#totalUses').val(responseData['totalUses']);
	       		  
	       		  if(responseData['percentage'] == 1)
	       		  	$('#percentage').prop('checked', true);
	       		  if(responseData['percentage'] == 0)
	       		  	$('#percentage').prop('checked', false);
	       		  	
	       		  if(responseData['value'] == 1)
	       		  	$('#value').prop('checked', true);
	       		  if(responseData['value'] == 0)
	       		  	$('#value').prop('checked', false);
	       		
	       		 $('#amount').val(responseData['value']);  	
	       		 $('#associatedProductSKU').val(responseData['associatedProductSKU']);  	
	       		  specialConstraints = JSON.parse(responseData['couponSpecialConstraintsId']);
		  		  $( specialConstraints ).each(function( index, value ) {
	       		  	$('#' + value).prop('checked', true);
			  	  });	
				  
				  if(responseData['google_promotion'] == 1)
	       		  	$('#google_promotion1').prop('checked', true);
	       		  if(responseData['google_promotion'] == 0)
	       		  	$('#google_promotion2').prop('checked', false);
					
				  $('#brand_id').val(responseData['brand_id']+"-_-"+responseData['brand_name']);
				  
				  if(responseData['closeout'] == 1)
	       		  	$('#closeout1').prop('checked', true);
	       		  if(responseData['closeout'] == 0)
	       		  	$('#closeout2').prop('checked', false);

			});
	
	}
	
</script>

