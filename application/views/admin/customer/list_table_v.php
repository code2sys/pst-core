<div class="tabular_data">
	<table width="100%" cellpadding="10" class="tblsrtr">
		<thead>
			<tr class="head_row">
				<td><b>Customer</b></td>
				<td><b>Phone</b></td>
				<td><b>Email</b></td>
				<td><b># Orders</b></td>
				<td><b>Actions</b></td>
			</tr>
		</thead>
		<tbody>
			<?php if(@$customers):
				foreach($customers as $customer): ?>
					<tr>
						<td><?php echo $customer['first_name'].' '.$customer['last_name']; ?></td>
						<td><?php echo $customer['phone']; ?></td>
						<td><?php echo $customer['uemail']; ?></td>
						<td><?php echo $customer['orders']; ?></td>
						<td>
							<a style="font-size:17px; margin:-4px 11px 0 0px; color:black; line-height:13px; padding:0px;" data-toggle="tooltip" href="<?php echo base_url('admin/customer_detail/'.$customer['id']); ?>" title="View" class="glyphicons"><span class="glyphicon">&#xe105;</span></a>
							
							<a style="vertical-align:super;" data-toggle="tooltip" href="<?php echo base_url('admin/customer_detail/'.$customer['id']); ?>" title="Edit" class="glyphicons edit"><i></i>&nbsp;</a>
							
							<a style="vertical-align:super;" data-toggle="tooltip" title="Delete" class="glyphicons delete" href="<?php echo base_url('admin/customer_delete/'.$customer['id']); ?>" onclick="return confirm('Are you sure you want to delete this item (1)? This cannot be undone.  Click &quot;OK&quot; to delete permanently.');"><i></i>&nbsp;</a>
							
							<!--<a title="View" style="font-size:16px; font-weight:bold; color:black;" href="<?php echo base_url('admin/customer_detail/'.$customer['id']); ?>"><i class="fa fa-eye" aria-hidden="true"></i></a> | 										
							<a title="Edit" style="font-size:16px; font-weight:bold; color:black;" href="<?php echo base_url('admin/customer_edit/'.$customer['id']); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> |
							<a title="Delete" style="background:black; color:white; margin:0px 0 0 5px; padding:0px 2px 1px 2px;" href="<?php echo base_url('admin/customer_delete/'.$customer['id']); ?>"><i class="fa fa-times" aria-hidden="true"></i></a> -->
						</td>
					</tr>
			<?php endforeach; ?>
		</tbody>
		<?php endif; ?>
	</table>
</div>
<script>
$(document).ready(function(){
    $(".tblsrtr").DataTable({
		"processing": true,
        "serverSide": true,
        "ajax": "<?php echo base_url('admin/load_customer_rec'); ?>",
		"searching": false
	});
});
</script>