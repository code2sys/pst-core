<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-cube"></i>&nbsp;General Options for <?php echo $product['name']; ?></h1>

        <?php if ($product["mx"] == 0): ?>
            <h2>Dealer-Inventory Product</h2>
        <?php endif; ?>

        <br>

        <!-- ERROR -->
        <?php if (@$error): ?>
            <div class="error">
                <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                <div style="clear: both"></div>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <!-- END ERROR -->

        <!-- SUCCESS -->
        <?php if (@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <div style="clear: both"></div>
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->


        <!-- TABS -->
        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/product/edit_tab_subnav", array(
            "part_id" => $part_id,
            "tag" => "product_edit"
        ), true);
        ?>
        <!-- END TABS -->
        
        <?php echo form_open('adminproduct/part_update/' . $part_id, array('class' => 'form_standard')); ?>
        <!-- TAB CONTENT -->
        <div class="tab_content">
            <div class="hidden_table">
                <table width="100%" cellpadding="6">

                    <tr>
                        <td><b>Product Title:</b></td>
                        <td>
                            <?php if (@$product['mx']): ?>
                                <?php echo $product['name']; ?>
                            <?php else: ?>
                                <input id="name" name="name" placeholder="Enter Title" class="text medium" value="<?php echo htmlentities($product['name']); ?>" />
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Display Pricing:</strong><br/><em>Products marked as &quot;Call for Price&quot; cannot be added to the shopping cart.</em></td>
                        <td>
                            <label><input type="radio" name="call_for_price" value="0" <?php if (!array_key_exists("call_for_price", $product) || $product["call_for_price"] == 0): ?>checked="checked"<?php endif; ?> /> Display Price (Default)</label>
                            <label><input type="radio" name="call_for_price" value="1" <?php if (array_key_exists("call_for_price", $product) && $product["call_for_price"] > 0): ?>checked="checked"<?php endif; ?> /> Call For Price (Do Not Show Price)</label>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Retail Price:</strong><br/><em>When selected, no discounting will be done for this product.</em></td>
                        <td>
                            <input type="checkbox" name="retail_price" value="1" <?php if (array_key_exists("retail_price", $product) && $product["retail_price"] > 0): ?>checked="checked"<?php endif; ?> />
                        </td>
                    </tr>
                    <?php if (count($product['partnumbers']) > 0): ?>
                    <tr>
                        <td><b>Markup:</b></td>
                        <td><input id="markup" name="markup" placeholder="Enter %" class="text mini" value="<?php echo @number_format($product['partnumbers'][0]['markup'], 0); ?>"/></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><b>Feature Product:</b></td>
                        <td>
                            <?php echo form_checkbox('featured', 1, $product['featured']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Feature Brand Product:</b></td>
                        <td>
                            <?php echo form_checkbox('featured_brand', 1, $product['featured_brand']); ?>
                        </td>
                    </tr>
                    <?php if (count($product['partnumbers']) > 0): ?>
                    <?php
                    $market_places = "default";
                    if (!empty($product['partnumbers'][0]['closeout_market_place'])) {
                        $market_places = 'closeout_market_place';
                    } elseif (!empty($product['partnumbers'][0]['exclude_market_place'])) {
                        $market_places = 'exclude_market_place';
                    }
                    ?>
                    <tr>
                        <td><b>Only display inventory if products are on closeout for Market Places:</b></td>
                        <td>

                            <input type="radio" name="market_places" value="closeout_market_place"<?php if ($market_places == 'closeout_market_place') { ?> checked="checked"<?php } ?>>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Do not display inventory for Market Places:</b></td>
                        <td>
                            <input type="radio" name="market_places" value="exclude_market_place"<?php if ($market_places == 'exclude_market_place') { ?> checked="checked"<?php } ?>>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Active State for Market Places:</b></td>
                        <td>
                            <input type="radio" name="market_places" value="default"<?php if ($market_places == 'default') { ?> checked="checked"<?php } ?>>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>

                <!-- SUBMIT PRODUCT -->
                <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Product</button>
                <div style="clear: both"></div>

            </div>
        </div>
        <!-- END TAB CONTENT -->
        <br>


        <!-- SUBMIT DISABLED 
        <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>
        
        <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->


        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->

<script type="text/javascript">
    $("#sortable").sortable({
        revert: true,
        stop: function (event, ui) {
            if (!ui.item.data('tag') && !ui.item.data('handle')) {
                ui.item.data('tag', true);
            }
        },
        receive: function (event, ui) {
            $("ul#sortable").find('.dragRemove').css("display", "inline");
        }
    }).droppable({});
    $(".draggable").draggable({
        connectToSortable: '#sortable',
        helper: 'clone',
        revert: 'invalid'
    });

    $("ul, li").disableSelection();

    function removeCategory()
    {
        $(this).remove();
    }

</script>

