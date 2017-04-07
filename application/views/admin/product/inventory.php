<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">
        <div class="clear"></div>
        <h1><i class="fa fa-cube"></i>&nbsp;<?php if (@$new): ?>New<?php else: ?>Edit<?php endif; ?> Product<?php if ($not_is_new): ?>: <?php echo $product['name']; ?><?php endif; ?></h1>

        <?php if ($not_is_new && $product["mx"] == 0): ?>
            <h2>Dealer-Inventory Product</h2>
        <?php endif; ?>

        <br>

        <!-- ERROR -->
        <?php if (@$error): ?>
            <div class="error">
                <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                <p><br/><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <!-- END ERROR -->

        <!-- SUCCESS -->
        <?php if (@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <p><br/><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->

        <!-- TABS -->
        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/product/edit_tab_subnav", array(
            "part_id" => $part_id,
            "tag" => "inventory"
        ), true);
        ?>

        <div class="clear"></div>

    </div>
</div>
<!-- END MAIN CONTENT