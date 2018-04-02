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
                            <td colspan=2 style="width:85%;"><?php echo form_dropdown('manufacturer', $manufacturers, array_key_exists("name", $product_brand) ? $product_brand["name"] : "", ''); ?></td>
                        </tr>
                        <tr>
                            <td><b>OR</b></td>
                        </tr>
                        <tr>
                            <td><b>New Brand:</b></td>
                            <td colspan=2 style="width:85%;"><?php echo form_input(array('name' => 'new_manufacturer',
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
                                    <td valign="top">
                                    <button type="button" id="searchbutton"><i class="fa fa-search"></i>&nbsp;Search Available Categories</button>
                                    </td>
                        </tr>
                        <tr style="display: none" id="category_table_row">
                                    <td></td>
                                    <td colspan="2" id="category_table_cell"><table id="category_table" style="width: 100%"></table></td>
                        </tr>
                    </table>
                    
                </div>
            </div>


<script type="application/javascript">
var existingCategories = <?php echo json_encode($existingCategories); ?> 
var existingCategoriesArray = null;
var categoryIdMap = {};

(function() {
    $(document).on("ready", function() {
        existingCategoriesArray = [];
        for (var i = 0; i < existingCategories.length; i++) {
            var id = existingCategories[i].category_id;
            var long_name = existingCategories[i].long_name;
            categoryIdMap[id] = long_name;
            existingCategoriesArray.push(["<a href='#' class='addCategoryButton' data-categoryid='" + id + "'><i class='fa fa-plus'></i>&nbsp;Add</a>", long_name]);
        }

        // initialize the table...
        $("#category_table").DataTable({
            data: existingCategoriesArray,
            deferRender: true,
            columns : [
                { title: "Action"},
                { title: "Category"}
            ]
        });
    });

    $("#searchbutton").on("click", function(e) {
        e.preventDefault();
        $("#searchbutton").hide();
        $("#category_table_row").show();
    });

    $(document).on("click", ".addCategoryButton", function(e) {
        e.preventDefault();

        // Now, you have to figure out what the category is, and you have to add it...
        var id = e.target.dataset.categoryid;
        $("textarea[name='categories']").val($("textarea[name='categories']").val() + "\n" + categoryIdMap[id]);

        // finally, destroy the table and hide this and show the button
        $("#searchbutton").show();
        $("#category_table_row").hide();
    });
})();
</script>




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

            <?php if (count($product_questions) > 0): ?>
            <p><strong>Filter Questions</strong></p>

            <?php foreach ($product_questions as $pq): ?>
            <p>&nbsp;</p>
                <p><em><?php echo htmlentities($pq["question"]); ?></em></p>

                <table>
                    <thead>
                    <tr>
                        <th>Answer</th>
                        <th>Distributor</th>
                        <th>Part Number</th>
                        <th>Manufacturer Part Number</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pq["partvariations"] as $pv): ?>
                    <tr>
                        <td><?php echo $pv["answer"]; ?></td>
                        <td><?php echo $pv["name"]; ?></td>
                        <td><?php echo $pv["part_number"]; ?></td>
                        <td><?php echo $pv["manufacturer_part_number"]; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endforeach?>

            <?php endif; ?>



        </div>
            <?php endif;?>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
