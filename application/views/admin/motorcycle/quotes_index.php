<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">
        <div class="clear"></div>

        <style >
            span.nowrap {
                white-space:nowrap;
            }
        </style>

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


                <?php if(@$success): ?>
                    <!-- SUCCESS MESSAGE -->
                    <div class="success">
                        <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
                        <h1>Success</h1>
                        <div class="clear"></div>
                        <p>
                            Your changes have been made.
                        </p>
                        <div class="clear"></div>
                    </div>
                    <!-- END SUCCESS MESSAGE -->
                <?php endif; ?>

            </div>
        <?php endif; ?>
        <!-- END VALIDATION ALERT -->


        <div class="admin_search_left">
            <div class="clear"></div>
            <h1><i class="fa fa-motorcycle"></i> Quote Requests</h1>
        </div>


        <div class="clear"></div>
        <!-- PRODUCT LIST -->
        <div class="tabular_data">
            <table width="100%" cellpadding="10" id="admin_motorcycle_quote_form">
                <thead>
                <tr>
                    <th><b>Submitted</b></th>
                    <th><b>Status</b></th>
                    <th><b>Name</b></th>
                    <th><b>Email</b></th>
                    <th><b>Phone</b></th>
                    <th><b>Motorcycle</b></th>
<?php if (!defined("DISABLE_TEST_DRIVE") || !DISABLE_TEST_DRIVE): ?>
                    <th><b><?php if (defined('WORDING_PLACEHOLDER_DATE_OF_RIDE')) { echo WORDING_PLACEHOLDER_DATE_OF_RIDE; } else { ?>Date of Test Ride<?php } ?></b></th>
                    <?php endif; ?>
                    <th><b>Action</b></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div class="clear"></div>

    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->


<script type="application/javascript">
    $(window).load(function() {
        $(".tabular_data table").dataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "<?php echo base_url("admin/motorcycle_quote_ajax"); ?>",
                "type" : "POST"
            },
            "data" : [],
            "paging" : true,
            "info" : true,
            "stateSave" : true,
            "columns" : [
                { "width" : "15%", "type" : "datetime" },
                null,
                null,
                null,
                null,
                null,
                <?php if (!defined("DISABLE_TEST_DRIVE") || !DISABLE_TEST_DRIVE): ?>
                null,
                <?php endif; ?>
                null
            ]
        });

    });


    function submitAjaxAction(id, action) {
        if (action == "view") {
            // we just have to redirect it.
            window.location.href = "<?php echo site_url('admin/motorcycle_quote_view'); ?>/" + id;
            return false; // all done..
        }

        if (action == "remove") {
            if (!confirm("Are you sure? This will remove the quote request from the database.")) {
                return false;
            }
        }

        // we have to do a callback...
        $.ajax({
            "type" : "POST",
            "dataType": "json",
            "url" : "<?php echo site_url("admin/motorcycle_quote_ajax_"); ?>" + action + "/" + id,
            "data" : {},
            "success" : function(data) {
                // OK, we need to make the table refresh
                if (data.success) {
                    // we just need to refresh the table
                    $(".tabular_data table").DataTable().ajax.reload();
                } else {
                    // throw the error.
                    alert("Error: " + data.error_message);
                }
            }
        });

    }

    $(document).ready(function() {
        // We need to bind these actions
        $(".tabular_data").on("click", ".view-button", function(e) {
            e.preventDefault();
            submitAjaxAction(e.target.dataset.motorcycleId, "view");
        });

        // We need to bind these actions
        $(".tabular_data").on("click", ".remove-button", function(e) {
            e.preventDefault();
            submitAjaxAction(e.target.dataset.motorcycleId, "remove");
        });
    });
</script>