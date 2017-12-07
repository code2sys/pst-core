<script type="text/javascript" src="/assets/js/ckeditor/ckeditor.js"></script>

<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-motorcycle"></i>&nbsp;Edit <?php echo $product["title"]; ?> - Description</h1>
        <p><b>Please fill out all fields within required tabs with an *</b></p>
        <br>

        <!-- ERROR -->
        <?php if (validation_errors()): ?>
            <div class="error">
                <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                <p><?php echo validation_errors(); ?></p>
            </div>
        <?php endif; ?>
        <!-- END ERROR -->

        <!-- SUCCESS -->
        <?php if (@$success): ?>
			<div class="success">
			  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
			<h1>Success</h1>
			<div class="clear"></div>
			<p>
			  Your changes have been made.
			</p>
			<div class="clear"></div>
			</div>
        <?php endif; ?>
        <!-- END SUCCESS -->						

        <!-- TABS -->
        <div class="tab">
            <ul>
                <li><a href="<?php echo base_url('admin/motorcycle_edit/' . $id); ?>"><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
                <li><a href="<?php echo base_url('admin/motorcycle_description/' . $id); ?>" class="active"><i class="fa fa-file-text-o"></i>&nbsp;Description*</a></li>
                <li><a href="<?php echo base_url('admin/motorcycle_images/' . $id); ?>"><i class="fa fa-image"></i>&nbsp;Images*</a></li>
                <li><a href="<?php echo base_url('admin/motorcycle_video/' . $id); ?>"><i class="fa fa-image"></i>&nbsp;Videos</a></li>
                <div class="clear"></div>
            </ul>
        </div>
        <!-- END TABS -->

        <form class="form_standard" method="post">

            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">
                        <tr>
                            <td style="width:130px;" valign="top"><b>Description:</b></td>
                            <td>
                                <textarea id="editor1" name="descr" rows="6" placeholder="Enter Description" cols="50" style="width:100%;"><?php echo $product['description']?></textarea>
								<script type="text/javascript">
									// LOAD THE CUSTOM CONFIGURATION FOR THIS INSTANCE
									CKEDITOR.replace( 'editor1', { customConfig : '<?php echo $edit_config; ?>' } );
								</script>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
            <!-- END TAB CONTENT -->
            <br>
            <!-- SUBMIT PRODUCT -->
            <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Save</button>

            <!-- CANCEL BUTTON -->
            <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>

        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
