<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-cube"></i>&nbsp;<?php if (@$new): ?>New<?php else: ?>Edit<?php endif; ?> Product</h1>
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
                <li><a href="<?php echo base_url('admin/motorcycle_description/' . $id); ?>"><i class="fa fa-file-text-o"></i>&nbsp;Description*</a></li>
                <li><a href="<?php echo base_url('admin/motorcycle_images/' . $id); ?>" class="active"><i class="fa fa-image"></i>&nbsp;Images*</a></li>
                <li><a href="<?php echo base_url('admin/motorcycle_video/' . $id); ?>"><i class="fa fa-image"></i>&nbsp;Videos</a></li>
                <div class="clear"></div>
            </ul>
        </div>
        <!-- END TABS -->

            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="auto" cellpadding="12">
						<?php foreach( $image as $k => $v ) { ?>
							<tr>
								<td valign="top"><img height="50" width="50" src="<?php echo base_url($media); ?>/<?php echo $v['image_name']; ?>"></td>
								<td valign="top">
									<form class="form_standard" enctype="multipart/form-data" method="post">
										<input id="name" name="description[<?php echo $v['id'];?>]" placeholder="Description" value="<?php echo $v['description'];?>" class="text medium" /><br>
										<input type="submit" value="Update Description" name="update">
										<a href="<?php echo site_url('admin/deleteMotorcycleImage/'.$v['id'].'/'.$v['motorcycle_id']);?>">
											<input type="button" class="dlt" value="Delete Image">
										</a>
									</form>
								</td>
							</tr>
						<?php } ?>
						<tr>
							<td valign="top"><b>Add Image:</b></td>
							<td valign="top">
								<form class="form_standard" enctype="multipart/form-data" method="post">
									<input type="file" name="file" value="" required>
									<input id="name" name="description" placeholder="Description" class="text medium" /><br>
									<input type="submit" name="submit" value="Add Image"></b>
								</form>
							</td>
						</tr>
                    </table>
                </div>
            </div>
            <!-- END TAB CONTENT -->
    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>
<script>
$(document).on('click', '.dlt', function() {
	var id = $(this).data('id');
});
</script>


</div>
<!-- END WRAPPER =========================================================================================-->
