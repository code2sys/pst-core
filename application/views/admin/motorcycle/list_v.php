<style>
    #button.show_active_button {
        color: white;
        text-shadow: none;
        background: -webkit-linear-gradient(#00E 1%, #00C 100%);
        background: -moz-linear-gradient(#00E 0%, #00C 100%);
        background: -ms-linear-gradient(#00E 0%, #00C 100%);
    }
</style>
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
            <h1><i class="fa fa-motorcycle"></i>&nbsp;New & Used Unit Inventory</h1>
            <p><b>To add a new product click the button below.</b></p>
            <br>
            <a href="<?php echo base_url('admin/motorcycle_edit'); ?>" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Unit</a>
        </div>

        <div class="admin_search_right">
            <h3>Out-of-Stock Actions:</h3>

            <a href="<?php echo base_url('admin/motorcycle_outofstock_inactive'); ?>" id="button" class="<?php if ($out_of_stock_active == 0): ?>show_active_button<?php endif; ?>"><i class="fa fa-pause"></i>&nbsp;Make All Out-of-Stock Units Inactive</a>
            <a href="<?php echo base_url('admin/motorcycle_outofstock_active'); ?>" id="button" class="<?php if ($out_of_stock_active > 0): ?>show_active_button<?php endif; ?>"><i class="fa fa-play"></i>&nbsp;Make All Out-of-Stock Units Active</a>

            <div style="clear: both"></div>

            <h3>Store Stock Status Visibility:</h3>

            <form>
                <label style="display: inline-block"><input type="radio" name="display_status_button" value="3" <?php if ($stock_status_mode == 3): ?>checked="checked"<?php endif; ?>> Display inventory status on website</label>
                <label style="display: inline-block"><input type="radio" name="display_status_button" value="2" <?php if ($stock_status_mode == 2): ?>checked="checked"<?php endif; ?>> Display in-stock status only</label>
                <label style="display: inline-block"><input type="radio" name="display_status_button" value="1" <?php if ($stock_status_mode == 1): ?>checked="checked"<?php endif; ?>> Display out-of-stock status only</label>
                <label style="display: inline-block"><input type="radio" name="display_status_button" value="0" <?php if ($stock_status_mode == 0): ?>checked="checked"<?php endif; ?>> Do not show stock status</label>
            </form>

        </div>


        <div class="pagination"><?php echo @$pagination; ?></div>
        <div class="clear"></div>
        <!-- PRODUCT LIST -->
        <div class="tabular_data">
            <table width="100%" cellpadding="10" id="admin_motorcycle_list_table_v">
                <thead>
                <tr>
                    <th><b>SKU</b></th>
                    <th><b>Category</b></th>
                    <th><b>Type</b></th>
                    <th><b>Image</b></th>
                    <th><b>Title</b></th>
                    <th><b>Model</b></th>
                    <th><b>Featured</b></th>
                    <th><b>Active</b></th>
                    <th><b>MSRP</b></th>
                    <th><b>Sale Price</b></th>
                    <th><b>Condition</b></th>
                    <th><b>Mileage</b></th>
                    <th><b>Source</b></th>
                    <th><b>Stock Status</b></th>
                    <th><b>Cycle Trader</b></th>
                    <th><b>Matched</b></th>
                    <th><b>Action</b></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- END PRODUCT LIST -->
        <a href="<?php echo base_url('admin/motorcycle_edit'); ?>" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Unit</a>

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
                "url" : "<?php echo base_url("admin/minventory_ajax"); ?>",
                "type" : "POST",
                "cache" : false
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
                null,
                null,
                null
            ]
        });

    });

    $("input[name='display_status_button']").on("change", function(e) {
        $.ajax({
            "type" : "POST",
            "dataType": "json",
            "url" : "<?php echo site_url("admin/ajax_set_stock_status_mode"); ?>/" + $("input[name='display_status_button']:checked").val(),
            "data" : {}
        });
    });

    function submitAjaxAction(id, action) {
        if (action == "edit") {
            // we just have to redirect it.
            window.location.href = "<?php echo site_url('admin/motorcycle_edit'); ?>/" + id;
            return false; // all done..
        }

        if (action == "remove") {
            if (!confirm("Are you sure? This will remove the unit record from the database.")) {
                return false;
            }
        }

        // we have to do a callback...
        $.ajax({
            "type" : "POST",
            "dataType": "json",
            "url" : "<?php echo site_url("admin/motorcycle_ajax_"); ?>" + action + "/" + id,
            "data" : {},
            "success" : function(data) {
                // OK, we need to make the table refresh
                if (data.success) {
                    // we just need to refresh the table
                    $(".tabular_data table").DataTable().ajax.reload(null, false);
                } else {
                    // throw the error.
                    alert("Error: " + data.error_message);
                }
            }
        });

    }

    $(document).ready(function() {
        // We need to bind these actions
        $(".tabular_data").on("click", ".edit-button", function(e) {
            e.preventDefault();
            submitAjaxAction(e.target.dataset.motorcycleId, "edit");
        });

        // We need to bind these actions
        $(".tabular_data").on("click", ".remove-button", function(e) {
            e.preventDefault();
            submitAjaxAction(e.target.dataset.motorcycleId, "remove");
        });

        // We need to bind these actions
        $(".tabular_data").on("click", ".active-button", function(e) {
            e.preventDefault();
            submitAjaxAction(e.target.dataset.motorcycleId, "active");
        });

        // We need to bind these actions
        $(".tabular_data").on("click", ".inactive-button", function(e) {
            e.preventDefault();
            submitAjaxAction(e.target.dataset.motorcycleId, "inactive");
        });


        $(document).on("change", "input.editable_sale_price", function(e) {
            var motorcycle_id = $(e.target).attr("data-motorcycle-id");
            var price = $(e.target).val();


            $.ajax({
                "type" : "POST",
                "dataType": "json",
                "url" : "<?php echo site_url("admin/minventory_ajax_updateprice"); ?>/" + motorcycle_id,
                "data" : {
                    "sale_price" : price
                },
                "success" : function(data) {
                    // OK, we need to make the table refresh
                    if (data.success) {
                        // Do nothing...
                    } else {
                        // throw the error.
                        alert("Error: " + data.error_message);
                    }
                }
            });

        })
    });
</script>