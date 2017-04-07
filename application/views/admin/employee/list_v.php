<style>
.main_nav ul li ul li a{width:157px !important;}
.main_nav ul li a{width:130px !important; height:43px !important;}
.main_nav ul li a.active{width:130px !important; height:43px !important;}
.main_nav ul li a:hover{width:130px !important; height:43px !important;}
.main_nav li ul a:hover, .main_nav li ul li ul li ul li:hover a{height:35px !important; width:157px !important;}
.tabular_data td{padding:8px 3px !important; font-family:'Open Sans',sans-serif;}
</style>
<link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">

<!-- BOOTSTRAP CSS AND JS CODE STARTS HERE -->
<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/css/bootstrap.css');?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/css/glyphicons.css');?>" />
<script src="<?php echo base_url('assets/css/bootstrap/js/bootstrap.js');?>"></script>
<!-- BOOTSTRAP CSS AND JS CODE ENDS HERE -->


<div class="content_wrap">
	<div class="content">
		<h1><i class="fa fa-users"></i>&nbsp;Employees</h1>
		<div class="" style="float:left; padding:2% 3% 3% 0%;">
			<a href="<?php echo base_url('admin/employee_edit'); ?>"><button style="color:black; padding:10px 15px;" type="button">Add New Employee</button></a>
		</div>
		<div class="admin_search_full" style="height:auto;">
			<form action="<?php echo base_url('admin/employees'); ?>/" method="post" id="moto_search" class="form_standard">
				<div class="hidden_table">
					<b>Lookup Customer </b>
					<input name="search" placeholder="Search <?php echo WEBSITE_NAME; ?>" class="text large" style="height:36px;" value="<?php echo $_POST['search'];?>"/>
					<input type="submit" value="Go!" class="button" style="margin-top:6px;">
				</div>
			</form>
		</div>
		
		<div id="listTable">
			<div class="tabular_data">
				<table width="100%" cellpadding="10">
					<tr class="head_row">
						<td><b>ID</b></td>
						<td><b>Name</b></td>
						<td><b>Status</b></td>
						<td><b>Super User</b></td>
						<td><b>Primary Phone</b></td>
						<td><b>Email</b></td>
						<td><b>Last Login</b></td>
						<td><b>Actions</b></td>
					</tr>
					<?php if(@$employees):
						foreach($employees as $employee): ?>
							<tr>
								<td><?php echo $employee['id']; ?></td>
								<td><?php echo $employee['first_name'].' '.$employee['last_name']; ?></td>
								<td><?php echo $employee['status'] == 0 ? 'Not Active' : 'Active'; ?></td>
								<td><?php echo $employee['admin'] == 1 ? 'Yes' : 'No'; ?></td>
								<td><?php echo $employee['phone']; ?></td>
								<td><?php echo $employee['email']; ?></td>
								<td><?php echo $employee['last_login'] != '' ? date('m/d/Y H:i:s', $employee['last_login']) : ''; ?></td>
								<td>
									<a data-toggle="tooltip" href="<?php echo base_url('admin/employee_edit/'.$employee['id']); ?>" title="Edit" class="glyphicons edit"><i></i>&nbsp;</a>	
									<a data-toggle="tooltip" title="Delete" class="glyphicons delete" href="<?php echo base_url('admin/employee_delete/'.$employee['id']);?>" onclick="return confirm('Are you sure you want to delete this item (1)? This cannot be undone.  Click &quot;OK&quot; to delete permanently.');"><i></i>&nbsp;</a>
								</td>
							</tr>
					<?php endforeach;
					endif; ?>
				</table>
			</div>
		</div>
	</div>
</div>