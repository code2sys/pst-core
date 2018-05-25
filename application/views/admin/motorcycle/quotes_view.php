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


        <div class="">
            <div class="clear"></div>
            <h1><i class="fa fa-motorcycle"></i> Unit Inquiry from <?php echo htmlentities($quote["firstName"] . " " . $quote["lastName"]); ?></h1>
        </div>

        <!-- TODO - Button to mark it as appropriate -->


        <div class="clear"></div>
        <!-- PRODUCT LIST -->

        <div>
            <table>
                <tbody>
                <?php
                foreach (array(
                    array("key" => "firstName", "label" => "First Name"),
                    array("key" => "lastName", "label" => "Last Name"),
                    array("key" => "email", "label" => "Email Address"),
                    array("key" => "phone", "label" => "Phone"),
                    array("key" => "address", "label" => "Address"),
                    array("key" => "city", "label" => "City"),
                    array("key" => "state", "label" => "State"),
                    array("key" => "zipcode", "label" => "Zip"),
                    array("key" => "date_of_ride", "label" => defined('WORDING_PLACEHOLDER_DATE_OF_RIDE') ? WORDING_PLACEHOLDER_DATE_OF_RIDE : "Date of Test Ride"),
                    array("key" => "motorcycle", "label" => "Major unit"),
                    array("key" => "make", "label" => "Make"),
                    array("key" => "model", "label" => "Model"),
                    array("key" => "year", "label" => "Year"),
                    array("key" => "miles", "label" => "Miles"),
                    array("key" => "accessories", "label" => "Accessories"),
                    array("key" => "questions", "label" => "Comments"),
                    array("key" => "created", "label" => "Request Date/Time"),
                    array("key" => "status", "label" => "Status")
                ) as $rec) {
                    $value = array_key_exists($rec["key"], $quote) ? $quote[ $rec["key"] ] : "";

                    if ($value != "") {

                        if ($rec["key"] == "created") {
                            $value = date("m/d/Y g:i a T", strtotime($value));
                        }


                        ?>

                        <tr>
                            <td valign="top"><strong><?php echo $rec["label"]; ?></strong></td>
                            <td valign="top"><?php echo htmlentities($value); ?><?php if ($rec["key"] == "status") {
                                    if ($value == "Received") {
                                        ?>
                                        <a href="/admin/motorcycle_quote_mark_as_sent/<?php echo $quote["id"]; ?>" class="fa fa-check"><i></i>&nbsp;Mark As Sent</a>
                                        <?php
                                    } else {
                                        echo " - " . date("m/d/Y g:i a T", strtotime($quote["sent_time"]));

                                    }

                                } ?></td>
                        </tr>
                        <?php
                    }

                } ?>
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