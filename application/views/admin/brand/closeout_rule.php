	<!-- MAIN CONTENT =======================================================================================-->
	<div class="content_wrap">
		<div class="content">		
			<div style="width:45.6%; float:left;">
		<h1><i class="fa fa-tags"></i>&nbsp;Brands</h1>
			<p><b>View, edit and create a new Brand.</b></p>
			<br>
			
			<!-- ADD NEW / PAGINATION -->
			<?php if (@$pagination): ?>
			<h3 style="float:right;margin-top:20px;">
				<a href=""><i class="fa fa-chevron-circle-left"></i></a>
				<a href="">1</i></a>
				<a href="">2</i></a>
				<a href="">3</i></a>
				<a href="">4</i></a>
				<a href="">5</i></a>
				<a href="">6</i></a>
				<a href=""><i class="fa fa-chevron-circle-right"></i></a>
			</h3>
			
			<?php endif; ?>
			<div class="clear"></div>
			<!-- END ADD NEW / PAGINATION -->
			
			<!-- PRODUCT LIST -->
			<div class="tabular_data">
				<table width="100%" cellpadding="10">
					<tr class="head_row">
						<td><b>#</b></td>
						<td><b>Name</b></td>
						<td><b>Parent Brand</b></td>
						<td><b>Actions</b></td>
					</tr>
					<?php if(@$brands): $i=0; foreach($brands as $key => $brand): ?>
					<tr>
						<td><?php echo $key; ?></td>
						<td><?php echo $brand['name']; ?></td>
						<td><b><?php echo @$parent_brands[$brand['parent_brand_id']]; ?></b></td>
						<td>
							<a href="javascript:void(0);" onclick="populateEdit('<?php echo $brand['brand_id']; ?>');"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a></a>
							<?php if(!@$brand['mx']): ?>
									| <a href="<?php echo base_url('admin/brand_delete/'.$brand['brand_id']); ?>"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a>
								<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; endif; ?>
				</table>
			</div>
			<!-- END PRODUCT LIST -->
			
			<!-- ADD NEW / PAGINATION -->
			
			<?php if (@$pagination): ?>
			<h3 style="float:right;margin-top:5px;">
				<a href=""><i class="fa fa-chevron-circle-left"></i></a>
				<a href="">1</i></a>
				<a href="">2</i></a>
				<a href="">3</i></a>
				<a href="">4</i></a>
				<a href="">5</i></a>
				<a href="">6</i></a>
				<a href=""><i class="fa fa-chevron-circle-right"></i></a>
			</h3>
			
			<?php endif; ?>
			<div class="clear"></div>
		</div>	
		
		
		<div style="width:45.6%; float:left; margin-left:10px;">
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
			<!-- PHP ALERT -->
     
		   <!-- ERROR -->
		   <?php if(validation_errors() || @$errors): ?>
			<div class="error">
				<h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
				<p><?php echo validation_errors() . @$errors; ?> </p>
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
					<li><a href="<?php echo base_url('admin/brand'); ?>"><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
					<li><a href="<?php echo base_url('admin/brand_image/'.$id); ?>"><i class="fa fa-image"></i>&nbsp;Image*</a></li>
					<li><a href="<?php echo base_url('admin/brand_video/'.$id); ?>"><i class="fa fa-image"></i>&nbsp;Videos*</a></li>
					<li><a href="<?php echo base_url('admin/brand_sizechart/'.$id); ?>"><i class="fa fa-image"></i>&nbsp;Size Charts*</a></li>
					<li><a href="<?php echo base_url('admin/brand_rule/'.$id); ?>" class="active"><i class="fa fa-image"></i>&nbsp;Closeout Schedule*</a></li>
					<div class="clear"></div>
				</ul>
			</div>
			<!-- END TABS -->
					
			<!-- TAB CONTENT -->
			<div class="tab_content">
				<div class="hidden_table">
				<?php echo form_open_multipart('admin/brand_rule/'. $id, array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?> 
					<table width="100%" cellpadding="6">
						<thead>
							<tr>
								<td colspan="2">Set Rules:</td>
							</tr>
						</thead>
						<tbody class="clsots-rls">
							<?php foreach($closeout_rules as $k => $rule) { ?>
								<tr id="<?php echo $rule['id']?>">
									<td><?php echo $k+1;?></td>
									<td>
										Decrease closeout product pricing after: 
										<input type="number" name="days[<?php echo $rule['id'];?>]" value="<?php echo $rule['days'];?>" required style="width:20px;"> Days To: 
										<input type="number" name="percentage[<?php echo $rule['id'];?>]" value="<?php echo $rule['percentage'] > 0 ? $rule['percentage'] : '';?>" style="width:20px;">
										%Off retail Or Mark Up %.
										<input type="checkbox" name="mark_up[<?php echo $rule['id'];?>]" value="1" <?php echo $rule['mark_up'] == 1 ? 'checked' : '';?>>
									</td>
									<td>
										<a href="javascript:void(0)" data-delete="<?php echo $rule['id'];?>" class="delete-rule">delete</a>
									</td>
								</tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3"><span class="add-new">Add New Rule</span></td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="submit" name="Save" value="Save">
								</td>
							</tr>
						</tfoot>
					</table>
				</form>
				</div>
			</div>
				
			<!-- END TAB CONTENT -->
			<br>
			<!-- SUBMIT PRODUCT -->
			<a href="javascript:void(0);" onclick="submitForm()" id="button" class="hide edit"><i class="fa fa-plus"></i>&nbsp;Save Rule</a>
		</div>
	</div>
</div>
	<!-- END MAIN CONTENT ==================================================================================-->


<script>
function submitForm() {
	$(".form_standard").submit();
}
</script>
<style>
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
</style>
<script>
$(document).on('click', '.add-new', function() {
	var lnth = $('.clsots-rls tr').length+1;
	var str = "<tr id='"+lnth+"'><td>"+lnth+"</td><td>Decrease closeout product pricing after: <input type='number' name='days["+lnth+"]' value='' style='width:20px;' required> Days To:";
	str += '<input type="number" name="percentage['+lnth+']" value="" style="width:20px;"> %Off retail Or Mark Up %.';
	str += '<input type="checkbox" name="mark_up['+lnth+']" value="1" ></td>';
	str += '<td><a href="javascript:void(0)" data-delete="'+lnth+'" class="delete-rule-a">delete</a></td></tr>';
	$('.clsots-rls').append(str);
});
$(document).on('click', '.delete-rule', function() {
	if( confirm("Are you sure you want to delete this rule.") ) {
		var id = $(this).data('delete');
		$.post( "<?php echo site_url('admin/deleteRule');?>", {'id':id}, function( result ){
			if(parseInt(result) > 0) {
				$('#'+id).remove();
			} else {
				alert('Error in Deleting Rule');
			}
		});
	}
});
$(document).on('click', '.delete-rule-a', function() {
	var id = $(this).data('delete');
	$('#'+id).remove();
});
</script>