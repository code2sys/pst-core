<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:57 AM
 *
 * I should have spelled it with an umlaut!
 *
 */

?>


<h1><i class="fa fa-motorcycle"></i>&nbsp;<?php if (@$new): ?>Add Unit<?php else: ?>Edit <?php echo $product["title"]; ?> - <?php echo $descriptor; ?><?php endif; ?></h1>
<?php if ($active == "edit"): ?>
<p><b>Please fill out all fields within required tabs with an *</b></p>
<?php endif; ?>
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


<!-- JLB 12-29-17 This is where we put in the status and we include the stock -->
<?php if (isset($source) && $source != ""): ?>
<div style="text-align: center; padding-bottom: 0.5em;">
    <div style="border: 1px solid black; padding: 3px; display: inline-block"><div style="display: inline-block; width: 12em;"><strong>Source:</strong><?php echo $source; ?></div><div style="display: inline-block; width: 12em;"><strong>Status:</strong><?php echo $source == "PST" ? "<span style='color: red'>Out Of Stock</span>" : "<span style='color: green; '>In Stock</span>"; ?></div></div>
</div>
<?php endif; ?>


<!-- TABS -->
<div class="tab">
    <ul>
        <li><a href="<?php echo base_url('admin/motorcycle_edit/' . $id); ?>" <?php if ($active == "edit"): ?>class="active"<?php endif; ?>><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
        <li><a href="<?php echo base_url('admin/motorcycle_description/' . $id); ?>" <?php if ($active == "description"): ?>class="active"<?php endif; ?>><i class="fa fa-file-text-o"></i>&nbsp;Description*</a></li>
        <li><a href="<?php echo base_url('admin/motorcycle_specs/' . $id); ?>" <?php if ($active == "specs"): ?>class="active"<?php endif; ?>><i class="fa fa-check-square-o"></i>&nbsp;Specifications*</a></li>
        <li><a href="<?php echo base_url('admin/motorcycle_images/' . $id); ?>" <?php if ($active == "images"): ?>class="active"<?php endif; ?>><i class="fa fa-image"></i>&nbsp;Images*</a></li>
        <li><a href="<?php echo base_url('admin/motorcycle_video/' . $id); ?>" <?php if ($active == "video"): ?>class="active"<?php endif; ?>><i class="fa fa-image"></i>&nbsp;Videos</a></li>
        <div class="clear"></div>
    </ul>
</div>
