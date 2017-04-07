<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-expand"></i>&nbsp;Size Chart for <?php echo $product['name']; ?></h1>

        <?php if ($product["mx"] == 0): ?>
            <h2>Dealer-Inventory Product</h2>
        <?php endif; ?>

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
            "tag" => "product_sizechart"
        ), true);
        ?>
        <!-- END TABS -->
        <!-- TAB CONTENT -->
        <div class="tab_content">
            <div class="hidden_table">

                <?php echo form_open_multipart('adminproduct/product_sizechart/' . $id, array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?>
                <?php $sizeChart = json_decode($product_sizechart['size_chart']); ?>
                <table width="100%" cellpadding="6">
                    <tr>
                        <td style="width: 150px;"><b>Sizing Chart Title:</b></td>
                        <td><input id="title" name="title" value="<?php echo $product_sizechart['title'];?>" class="text large" required placeholder="Enter Title"/></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Sizing Chart:</b>
                            <p style="font-size:10px; line-height:8px">Create table including header <input type="number" name="rows" value="<?php echo count($sizeChart);?>" style="width:30px;" min="1" required> Rows tall by <input type="number" name="cols" value="<?php echo count($sizeChart[0]);?>" style="width:30px;" min="1" required> Columns wide: <input type="button" name="ctable" class="button" value="Create Table"></p>
                        </td>
                    </tr>
                    <tr class="s-chart">
                        <td class="schart" colspan="3" style="height:100px;width: 200px;">
                            <table width='70%'>
                                <?php foreach ($sizeChart as $a => $b) { ?>
                                    <tr>
                                        <?php foreach ($b as $a1 => $b1) { ?>
                                            <td><input type="text" name="size[<?php echo $a ?>][<?php echo $a1 ?>]" value="<?php echo $b1; ?>"></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Text Box:</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <textarea name="content" style="width: 70%; height: 100px;"><?php echo $product_sizechart['content'];?></textarea>
                        </td>
                    </tr>
                </table>

            </div>

            <br>

            <!-- SUBMIT PRODUCT -->
            <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Product</button>

            <div style="clear: both"></div>

        </div>
        <!-- END TAB CONTENT -->

        <!-- SUBMIT DISABLED 
        <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>
        
        <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->


        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>
<script>
    $(document).on('click', '.add-row', function (e) {
        var str = "<tr><td><input type='text' name='video_url[]' value='' class='text large' placeholder='Enter video URL' style='height:30px;'></td><td><input id='title' name='title[]' value='' class='text large' placeholder='Enter video Title' class='text medium' style='height:30px;'/></td><td><input type='number' value='' name='ordering[]' class='text small' placeholder='Ordering' min='1' style='height:30px;'</td></tr>";
        $('.tbdy').append(str);
    });
    $(document).on('click', 'input[name=ctable]', function () {
        var cols = $('input[name=cols]', $(this).parent()).val();
        var rows = $('input[name=rows]', $(this).parent()).val();

        if (cols > 0 && rows > 0) {
            var str = "<table width='100%'>";
            for (i = 0; i < rows; i++) {
                str += "<tr>";
                for (a = 0; a < cols; a++) {
                    str += "<td><input type='text' name='size[" + i + "][" + a + "]' style='width:90%;'></td>";
                }
                str += "</tr>";
            }
            str += "</table>";
            $('.schart', $(this).parent().parent().parent().parent()).html(str);
            $('.s-chart', $(this).parent().parent().parent().parent()).show();
        } else {
            alert('Please enter Cols and Rows');
        }
    });

</script>


</div>
<!-- END WRAPPER =========================================================================================-->
