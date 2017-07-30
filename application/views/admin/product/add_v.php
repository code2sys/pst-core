<!-- CONTENT WRAP =========================================================================-->
<div class="content_wrap">


    <!-- MAIN -->
    <div class="content">

        <?php echo form_open('adminproduct/product_add_save', array('class' => 'form_standard admin_product_add_form')); ?>
        <!-- EDIT PROFILE -->
        <div class="account_section">
            <h1><i class="fa fa-pencil"></i> Basic Part Information</h1>

            <p><br/>You will have the opportunity to provide specifics on product questions and pricing after you add this basic part information.</p>

            <?php if(isset($error) && $error != ""): ?>
                <!-- VALIDATION ALERT -->
                <div class="validation_error">
                    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
                    <h1>Error</h1>
                    <p><br/><?php echo $error; ?></p>
                    <div class="clear"></div>
                </div>
                <!-- END VALIDATION ALERT -->
            <?php endif; ?>

            <div class="hidden_table">

                <table width="100%" cellpadding="6">
                    <tr>
                        <td><b>Product Name:</b></td>
                        <td style="width:85%;"><?php echo form_input(array('name' => 'name',
                                'value' => @$name,
                                'class' => 'text large',
                                'placeholder' => 'Product Name')); ?></td>
                    </tr>
                    <tr>
                        <td><b>Select Brand:</b></td>
                        <td style="width:85%;"><?php echo form_dropdown('manufacturer', $manufacturers, @$manufacturer, ''); ?></td>
                    </tr>
                    <tr>
                        <td><b>OR</b></td>
                    </tr>
                    <tr>
                        <td><b>New Brand:</b></td>
                        <td style="width:85%;"><?php echo form_input(array('name' => 'new_manufacturer',
                                'value' => @$new_manufacturer,
                                'class' => 'text large',
                                'placeholder' => 'New Brand')); ?></td>
                    </tr>
                    <tr>
                        <td><b>Product Description:</b></td>
                        <td><?php echo form_textarea(array('name' => 'description',
                                'value' => @$description,
                                'cols' => 80,
                                'rows' => 10,
                                'placeholder' => 'Product Description')); ?></td>
                    </tr>
                    <tr>
                        <td><b>Product Categories:</b><br/>Please sepcify categories, one per line, using the complete category name, e.g. "Cat > Sub-Cat > Sub-Sub-Cat".</td>
                        <td><?php echo form_textarea(array('name' => 'categories',
                                'value' => @$categories,
                                'cols' => 80,
                                'rows' => 10,
                                'placeholder' => 'Product Categories'), "", " style='width: auto;' "); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" class="button admin_product_add_button">Add Product</button>
                        </td>
                    </tr>
                </table>

                </form>
            </div>
        </div>
        <!-- END EDIT PROFILE -->

    </div>
    <!-- END MAIN -->


</div>
<script type="application/javascript">
    (function() {

        var validationFunction = function(e) {
            var error_string = "";

            // Check #1: They provided a name
            var name = $("input[name=name]").val();
            if (!name || name == "") {
                error_string += "Please provide the product name. ";
            }

            // Check #2: They provided a manufacturer
            var manufacturer = $("[name=manufacturer]").val();
            if (!manufacturer || manufacturer == "" || manufacturer == "-- Select Existing Manufacturer --") {
                manufacturer = $("[name=new_manufacturer").val();
            }

            if (!manufacturer || manufacturer == "") {
                error_string += "Please select a manufacturer or enter a new manufacturer. ";
            }

            if (error_string != "") {
                alert(error_string);
                e.stopPropagation();
                e.preventDefault();
                return false;
            }

            return true;
        };

        $(".admin_product_add_form").on("submit", validationFunction);
        $(".admin_product_add_button").on("click", validationFunction);

    })();


</script>