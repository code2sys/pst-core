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
            "tag" => "product_category_brand"
        ), true);
        ?>
        <!-- END TABS -->

        <?php if ($product["mx"] == 0): ?>
        <form class="form_standard" method="POST" action="<?php echo base_url("adminproduct/product_category_brand_save/" . $part_id); ?>">

            <div class="tab_content">



                <div class="hidden_table">

                    <table width="100%" cellpadding="6">
                        <tr>
                            <td><b>Select Brand:</b></td>
                            <td style="width:85%;"><?php echo form_dropdown('manufacturer', $manufacturers, array_key_exists("name", $product_brand) ? $product_brand["name"] : "", ''); ?></td>
                        </tr>
                        <tr>
                            <td><b>OR</b></td>
                        </tr>
                        <tr>
                            <td><b>New Brand:</b></td>
                            <td style="width:85%;"><?php echo form_input(array('name' => 'new_manufacturer',
                                    'value' => "",
                                    'class' => 'text large',
                                    'placeholder' => 'New Brand')); ?></td>
                        </tr>
                        <tr>
                            <td><b>Product Categories:</b><br/>Please sepcify categories, one per line, using the complete category name, e.g. "Cat > Sub-Cat > Sub-Sub-Cat". <strong>Use a new line or a semicolon to separate multiple categories.</strong></td>
                            <td><?php echo form_textarea(array('name' => 'categories',
                                    'value' => implode("\n", array_map(function($x) {
                                        return $x["long_name"];
                                    }, $product_categories)),
                                    'cols' => 80,
                                    'rows' => 10,
                                    'placeholder' => 'Product Categories'), '', " style='width: auto;' "); ?></td>
                        </tr>
                    </table>
                </div>
            </div>




            <br>
            <!-- SUBMIT PRODUCT -->
                <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Categories and Brand</button>

        </form>

            <?php else: ?>
        <div class="tab_content">
            <p><strong>Brand: </strong> <?php echo array_key_exists("name", $product_brand) ? $product_brand["name"] : ""; ?></p>
            <p><strong>Categories:</strong> </p>
            <ul>
                <?php foreach ($product_categories as $c): ?>
                <li><?php echo $c["long_name"]; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
            <?php endif;?>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
