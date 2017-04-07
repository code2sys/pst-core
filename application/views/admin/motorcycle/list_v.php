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
            <form action="<?php echo base_url('admin/mInventory'); ?>/" method="post" class="form_standard">
                <div class="hidden_table">
                    <b>Show Only: </b>
                    <table>
                        <tr>
                            <td>Product</td>
                            <td><input id="name" name="name" placeholder="Name" class="text mini" style="height:20px;width:200px;" /></td>
                            <td></td>
                            <td><input type="submit" value="Go!" class="button" style="margin-top:6px;"></td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>

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
<?php echo $productListTable; ?>
        </div>
        <!-- END PRODUCT LIST -->
        <a href="<?php echo base_url('admin/motorcycle_edit'); ?>" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Product</a>

        <div class="pagination"><?php echo @$pagination; ?></div>
        <div class="clear"></div>

    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div id="productPage" class="hide">1</div>