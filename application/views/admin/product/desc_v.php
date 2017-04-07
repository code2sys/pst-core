<?php

$not_is_new = !isset($new) || !$new;

?>
<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

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
            "tag" => "product_description"
        ), true);
        ?>
        <!-- END TABS -->

        <?php if ($product["mx"] == 0): ?>
        <form class="form_standard" method="POST" action="<?php echo base_url("adminproduct/product_description_save/" . $part_id); ?>">
        <?php endif;?>

            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">
                        <tr>
                            <td style="width:130px;" valign="top"><b>Description:</b></td>
                            <td>
                                <?php if ($product["mx"] == 0): ?>
                                <textarea id="description" name="description" rows="6" placeholder="Enter Description" cols="50" style="width:100%;"><?php echo htmlentities($product["description"]); ?></textarea>
                                <?php else: ?>
                                <?php echo $product["description"]; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
            <!-- END TAB CONTENT -->
            <br>
            <!-- SUBMIT PRODUCT -->
            <?php if ($product["mx"] == 0): ?>
            <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Description</button>
            <?php endif; ?>

        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
