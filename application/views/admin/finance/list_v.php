<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">
        <div class="clear"></div>

        <!-- VALIDATION ALERT -->
        <?php if (validation_errors() || @$errors): ?>
            <div class="validation_error" id="login_validation_error">
                <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
                <h1>Error</h1>
                <div class="clear"></div>
                <p><?php echo validation_errors();
        if (@$errors): foreach ($errors as $error): echo $error;
            endforeach;
        endif; ?></p>

            </div>
<?php endif; ?>
        <!-- END VALIDATION ALERT -->
<?php
	$arr = array();
	if(isset($_POST['name']))
	{
		$arr = explode(" ",$_POST['name']);
		// echo "<pre>";
		// print_r($arr);
		// echo "</pre>";
	}
	// echo "<pre>";
	// print_r($applications);
?>
        <div class="admin_search_left">
            <div class="clear"></div>
            <h1><i class="fa fa-cubes"></i>&nbsp;Credit Applications</h1>
            <p></p>
            <br>
        </div>
        <div class="admin_search_right">
            <div class="clear"></div>
            <form action="<?php echo base_url('admin/credit_applications'); ?>/" method="post" class="form_standard">
                <div class="hidden_table">
                    <b>Show Only: </b>
                    <table>
                        <tr>
                            <td>Credit Applications</td>
                            <td><input id="name" name="name" placeholder="Name" class="text mini" value="<?php echo $_POST['name'] ?>" style="height:20px;width:200px;" /></td>
                            <td></td>
                            <td><input type="submit" value="Go!" class="button" style="margin-top:6px;"></td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>

        <div class="pagination"><?php echo @$pagination; ?></div>
        <div class="clear"></div>
        <!-- PRODUCT LIST -->
        <div class="tabular_data">

			<table width="100%" cellpadding="10">
				<tr class="head_row">
					<td><b>First Name</b></td>
					<td><b>Last Name</b></td>
					<td><b>Phone</b></td>
					<td><b>Email</b></td>
					<td><b>Status</b></td>
					<td><b>Application Date</b></td>
					<td><b>Action</b></td>
				</tr>

				<?php if(empty($arr)){ ?>
				<?php if (@$applications): foreach ($applications as $application): ?>
				<?php $contact_info = json_decode($application['contact_info']); ?>
						<tr>
							<td><?php echo $application['first_name']; ?></td>
							<td><?php echo $application['last_name']; ?></td>
							<td><?php echo $contact_info->rphone; ?></td>
							<td><?php echo $application['email']; ?></td>
							<td><?php echo $application['application_status']; ?></td>
							<td><?php echo date('Y-M-d H:i:s', strtotime($application['application_date']))?></td>
							<td>
								<a href="<?php echo base_url('admin/finance_edit/' . $application['id']); ?>"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a>
								| <a href="<?php echo base_url('admin/finance_delete/' . $application['id']); ?>" onclick="return confirm('Are you sure you would like to delete this credit application')"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a>
							</td>
						</tr>
				<?php endforeach;
				endif; ?>
				<?php }else{ ?>
				<?php if (@$applications): foreach ($applications as $application): ?>
				<?php $contact_info = json_decode($application['contact_info']); ?>
				<?php if(($arr[0]==$application['first_name'])||($arr[1]==$application['last_name'])||($arr[1]==$application['first_name'])||($arr[0]==$application['last_name'])||($arr[0]=="")){ ?>
						<tr>
							<td><?php echo $application['first_name']; ?></td>
							<td><?php echo $application['last_name']; ?></td>
							<td><?php echo $contact_info->rphone; ?></td>
							<td><?php echo $application['email']; ?></td>
							<td><?php echo $application['application_status']; ?></td>
							<td><?php echo date('Y-M-d H:i:s', strtotime($application['application_date']))?></td>
							<td>
								<a href="<?php echo base_url('admin/finance_edit/' . $application['id']); ?>"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a>
								| <a href="<?php echo base_url('admin/finance_delete/' . $application['id']); ?>" onclick="return confirm('Are you sure you would like to delete this credit application')"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a>
							</td>
						</tr>
				<?php } ?>
				<?php endforeach;
				endif; ?>
				<?php } ?>
				
			</table>

        </div>
        <!-- END PRODUCT LIST -->

        <div class="pagination"><?php echo @$pagination; ?></div>
        <div class="clear"></div>

    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div id="productPage" class="hide">1</div>
