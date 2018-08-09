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

        <!-- SUCCESS -->
        <?php if(@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <div class="clear"></div>
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->

        <div class="admin_search_left">
            <div class="clear"></div>
            <h1><i class="fa fa-cubes"></i>&nbsp;Products</h1>
            <p><b>To add a new product click the button below.</b></p>
            <br>
            <a href="<?php echo base_url("adminproduct/product_add"); ?>" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Product</a>
            <a href="<?php echo base_url("adminproductuploader/index"); ?>" id="button"><i class="fa fa-upload"></i>&nbsp;Upload Multiple Products</a>
        </div>


        <div class="clear"></div>
        <!-- PRODUCT LIST -->
        <div class="tabular_data">
            <table width="100%" cellpadding="10" id="admin_product_list_table_v">
                <thead>
                <tr>
                    <th><b>Product Code</b></th>
                    <th><b>Image</b></th>
                    <th><b>Title</b></th>
                    <th><b>Dealer Only?</b></th>
                    <th><b>Featured</b></th>
                    <th><b>Visible In Store</b></th>
                    <th><b>$ Cost</b></th>
                    <th><b>$ Retail</b></th>
                    <th><b>$ Markup</b></th>
                    <th><b>$ Sale</b></th>
                    <th><b>Action</b></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- END PRODUCT LIST -->
        <a href="<?php echo base_url("adminproduct/product_add"); ?>" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Product</a>
        <a href="<?php echo base_url("adminproductuploader/index"); ?>" id="button"><i class="fa fa-upload"></i>&nbsp;Upload Multiple Products</a>

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
                "url" : "<?php echo base_url("adminproduct/product_ajax"); ?>",
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
                null
            ]
        });
    });
</script>