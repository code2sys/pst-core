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

        <div class="pagination"><?php echo @$pagination; ?></div>
        <div class="clear"></div>
        <!-- PRODUCT LIST -->
        <div class="tabular_data">

			<table width="100%" cellpadding="10">
				<tr class="head_row">
					<td><b>Application Type</b></td>
					<td><b>First Name</b></td>
					<td><b>Last Name</b></td>
					<td><b>Email</b></td>
					<td><b>Phone</b></td>
					<td><b>Co-Applicant First Name</b></td>
					<td><b>Co-Applicant Last Name</b></td>
					<td><b>Co-Applicant Email</b></td>
					<td><b>Co-Applicant Phone</b></td>
					<td><b>Year</b></td>
					<td><b>Make</b></td>
					<td><b>Model</b></td>
					<td><b>Status</b></td>
					<td><b>Application Date</b></td>
					<td><b>Action</b></td>
				</tr>

			</table>

        </div>
        <!-- END PRODUCT LIST -->

        <div class="pagination"><?php echo @$pagination; ?></div>
        <div class="clear"></div>

    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div id="productPage" class="hide">1</div>


<script type="application/javascript">
    $(window).load(function() {
        $(".tabular_data table").dataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "<?php echo base_url("admin/credit_applications_ajax"); ?>",
                "type" : "POST"
            },
            "data" : [],
            "paging" : true,
            "info" : true,
            "stateSave" : true,
            "columns" : [
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            ]
        });

    });


</script>