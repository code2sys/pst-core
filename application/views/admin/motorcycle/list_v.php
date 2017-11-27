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
            <h1><i class="fa fa-motorcycle"></i>&nbsp;New & Used Motorcycles</h1>
            <p><b>To add a new product click the button below.</b></p>
            <br>
            <a href="<?php echo base_url('admin/motorcycle_edit'); ?>" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Product</a>
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
                    <th><b>Featured</b></th>
                    <th><b>Active</b></th>
                    <th><b>MSRP</b></th>
                    <th><b>Sale Price</b></th>
                    <th><b>Condition</b></th>
                    <th><b>Mileage</b></th>
                    <th><b>Source</b></th>
                    <th><b>Action</b></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- END PRODUCT LIST -->
        <a href="<?php echo base_url('admin/motorcycle_edit'); ?>" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Product</a>

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
                "type" : "POST"
            },
            "data" : [],
            "paging" : true,
            "info" : true,
            "stateSave" : true,
            "columns" : [
                { "width" : "15%" },
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