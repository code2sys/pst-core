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

			<table width="100%" cellpadding="10" id="admin_finance_list">
				<tr class="head_row">
                    <thead>
					<th><b>Application Type</b></th>
					<th><b>First Name</b></th>
					<th><b>Last Name</b></th>
					<th><b>Email</b></th>
					<th><b>Phone</b></th>
					<th><b>Co-Applicant First Name</b></th>
					<th><b>Co-Applicant Last Name</b></th>
					<th><b>Co-Applicant Email</b></th>
					<th><b>Co-Applicant Phone</b></th>
					<th><b>Year</b></th>
					<th><b>Make</b></th>
					<th><b>Model</b></th>
					<th><b>Status</b></th>
					<th><b>Application Date</b></th>
					<th><b>Action</b></th>
                    </thead>
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
            "table_id" : "credit_applications",
            "id" : "credit_applications",
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