<div class="tabular_data">
				
	<table cellpadding="3" style="width:100%;">
		<tr class="head_row">
			<td><b>Id</b></td>
			<td><b>Username</b></td>
			<td><b>Password</b></td>
			<td><b>Email</b></td>
			<td><b>Last Log In</b></td>
			<td><b>Wholesaler</b></td>
			<td><b>Tax Exempt</b></td>
			<td><b>Admin</b></td>
		</tr>
		<?php if(@$users): foreach(@$users as $user): ?>
		
		<tr>
			<td><?php echo $user['id']; ?><?php echo form_hidden('id[]', $user['id']); ?></td>
			<td><?php echo $user['username']; ?></td>
			<td><?php echo $user['password'] ? '<a href="javascript:void(0);" onclick="decryptPassword('.$user['id'].');">Decrypt Password</a>' : ''; ?></td>
			<td><?php echo $user['lost_password_email']; ?></td>
			<td><?php echo ($user['last_login']) ? date('m/d/y H:i:s', $user['last_login']) : ''; ?></td>
			<td><?php echo form_checkbox('wholesaler[]', 1, $user['wholesaler']); ?></td>
			<td><?php echo form_checkbox('no_tax[]', 1, $user['no_tax']); ?></td>
      <td><?php echo $user['admin'] ? 'Yes' : 'No'; ?></td>
		</tr>
		
		<?php endforeach; endif; ?>
	</table>
</div>
