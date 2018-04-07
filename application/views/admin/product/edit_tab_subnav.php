<?php

$CI =& get_instance();
$product = $CI->admin_m->getAdminProduct($part_id);

?>
<div class="tab">
    <ul>
        <li><a href="<?php echo base_url('adminproduct/product_edit/' . $part_id); ?>" class="<?php echo ($tag == "product_edit") ? "active" : ""; ?>"><i class="fa fa-bars"></i>&nbsp;General Options</a></li>
        <li><a href="<?php echo base_url('adminproduct/product_category_brand/' . $part_id); ?>" class="<?php echo ($tag == "product_category_brand") ? "active" : ""; ?>"><i class="fa fa-file-text-o"></i>&nbsp;Categories and Brand</a></li>
        <li><a href="<?php echo base_url('adminproduct/product_description/' . $part_id); ?>" class="<?php echo ($tag == "product_description") ? "active" : ""; ?>"><i class="fa fa-file-text-o"></i>&nbsp;Description</a></li>
        <li><a href="<?php echo base_url('adminproduct/personalization/' . $part_id); ?>" class="<?php echo ($tag == "personalization") ? "active" : ""; ?>"><i class="fa fa-file-text-o"></i>&nbsp;SKUs, Quantities, and Personalization</a></li>
        <li><a href="<?php echo base_url('adminproduct/dealerinventory/' . $part_id); ?>" class="<?php echo ($tag == "dealerinventory") ? "active" : ""; ?>"><i class="fa fa-clipboard"></i>&nbsp;Inventory</a></li>
        <li><a href="<?php echo base_url('adminproduct/fitments/' . $part_id); ?>" class="<?php echo ($tag == "fitments") ? "active" : ""; ?>"><i class="fa fa-gears"></i>&nbsp;Fitments</a></li>
        <!-- <li><a href="<?php echo base_url('adminproduct/product_meta/' . $part_id); ?>" class="<?php echo ($tag == "product_meta") ? "active" : ""; ?>"><i class="fa fa-list-alt"></i>&nbsp;Meta Data</a></li> -->
        <!--                <li><a href="<?php //echo base_url('admin/product_shipping/' . $part_id); ?>"><i class="fa fa-truck"></i>&nbsp;Shipping*</a></li>-->
        <li><a href="<?php echo base_url('adminproduct/product_images/' . $part_id); ?>" class="<?php echo ($tag == "product_images") ? "active" : ""; ?>"><i class="fa fa-image"></i>&nbsp;Images</a></li>
        <!-- <li><a href="<?php echo base_url('admin/product_reviews/' . $part_id); ?>" class="<?php echo ($tag == "product_reviews") ? "active" : ""; ?>"><i class="fa fa-image"></i>&nbsp;Reviews</a></li> -->
        <li><a href="<?php echo base_url('adminproduct/product_video/' . $part_id); ?>" class="<?php echo ($tag == "product_video") ? "active" : ""; ?>"><i class="fa fa-image"></i>&nbsp;Videos</a></li>
        <li><a href="<?php echo base_url('adminproduct/product_sizechart/' . $part_id); ?>" class="<?php echo ($tag == "product_sizechart") ? "active" : ""; ?>"><i class="fa fa-image"></i>&nbsp;Size Chart</a></li>
        <div class="clear"></div>
    </ul>
</div>