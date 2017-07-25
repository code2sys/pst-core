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
            <form action="<?php echo base_url('admin/mInventory'); ?>/" method="get" class="form_standard">
                <div class="hidden_table">
                    <b>Show Only: </b>
                    <table style="width: 100%;">
                        <tr>
                            <td>Product</td>
                            <td><input id="name" name="name" placeholder="Name" class="text mini" style="height:20px;width:200px;" value="<?php echo $filter['name'];?>" /></td>
                            <td>Condition</td>
                            <td>
                                <select name="condition" onchange="jQuery('.form_standard').submit();">
                                    <option value="">Select Condition</option>
                                    <?php if(@$condition) { ?>
                                    <?php $cndtn = array('1' => 'New', '2' => 'Pre-Owned'); ?>
                                        <?php foreach($condition as $cndn) { ?>
                                            <option value="<?php echo strtolower($cndtn[$cndn['condition']]);?>" <?php echo $_GET['condition'] == strtolower($cndtn[$cndn['condition']]) ? 'selected':'';?>><?php echo $cndtn[$cndn['condition']];?></option>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <option value="new" <?php echo $filter['condition'] == 'new' ? 'selected':'';?>>New</option>
                                        <option value="pre-owned" <?php echo $filter['condition'] == 'pre-owned' ? 'selected':'';?>>Pre-Owned</option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Year</td>
                            <td>
                                <select name="years[]" onchange="jQuery('.form_standard').submit();">
                                    <option value="">Select Year</option>
                                    <?php foreach ($years as $year) { ?>
                                        <option value="<?php echo $year['year']; ?>" <?php echo in_array($year['year'],$filter['years']) ? 'selected':'';?>><?php echo $year['year']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>Brand</td>
                            <td>
                                <select name="brands[]" onchange="jQuery('.form_standard').submit();">
                                    <option value="">Select Brand</option>
                                    <?php foreach ($brands as $brand) { ?>
                                        <option value="<?php echo $brand['make']; ?>" <?php echo in_array($brand['make'], $filter['brands']) ? 'selected':'';?>><?php echo $brand['make']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Category</td>
                            <td>
                                <select name="categories[]" onchange="jQuery('.form_standard').submit();">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category) { ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $filter['categories']) ? 'selected':'';?>><?php echo $category['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>Vehicle</td>
                            <td>
                                <select name="vehicles[]" onchange="jQuery('.form_standard').submit();">
                                    <option value="">Select Vehicle</option>
                                    <?php foreach ($vehicles as $vehicle) { ?>
                                        <option value="<?php echo $vehicle['id']; ?>" <?php echo in_array($vehicle['id'],$filter['vehicles']) ? 'selected':'';?>><?php echo $vehicle['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><input type="submit" value="Go!" class="button" style="margin-top:6px;"></td>
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