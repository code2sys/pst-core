<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-image"></i>&nbsp;Images for <?php echo $product['name']; ?></h1>

        <?php if ($product["mx"] == 0): ?>
            <h2>Dealer-Inventory Product</h2>
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
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <p>Success Message!</p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->			

        <!-- TABS -->
        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/product/edit_tab_subnav", array(
            "part_id" => $part_id,
            "tag" => "product_images"
        ), true);
        ?>
        <!-- END TABS -->

        <form class="form_standard">

            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <?php if ($product["mx"] == 1): ?>
                        <?php if (count($images) > 0): ?>
                    <table width="auto" cellpadding="12">
                        <?php $k = 0; ?>
                        <?php foreach ($images as $i): ?>
                            <?php $k++; ?>
                        <tr>
                            <td valign="top" style="width:130px;"><b>Image <?php echo $k; ?>:</b></td>
                            <td><img src="/productimages/t<?php echo $i["path"]; ?>" style="width: 144px; height: auto; margin-bottom: 1em;"><?php if ($i["description"] != ""): ?><br/><?php echo $i["description"]; ?><?php endif; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                        <?php else: ?>
                            <p>There are no images associated with this part.</p>
                        <?php endif; ?>
                    <?php else: ?>

                    <?php endif; ?>
                </div>
            </div>

        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
