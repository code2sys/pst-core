<?php
$online_in_stock_string = '<span class="online_only hide" style="display: inline-block">Online Only</span><span class="instock hide"  style="display: inline-block">Available For Store Pickup</span>';
$qty_input = form_input(array('name' => 'qty',
    'value' => 1,
    'maxlength' => 250,
    'class' => 'text mini qtyInput',
    'placeholder' => '0',
    'id' => 'qty'));
?>
<div class="questions_and_quantities_block">
    <div class="quantity_block">
        <div class="quantity_selector">
            QTY:
            <?php echo $qty_input; ?>
        </div>

        <div class="quantity_description">
            <div class="stock hide out_of_stock" id="out_of_stock">
                <span class="outOfStockStatus">OUT OF STOCK - PLEASE CALL TO ORDER</span>
            </div>
            <div class="stock hide in_stock" id="in_stock">
                <span class="stockStatus">In Stock</span>
                <?php echo $online_in_stock_string; ?>
                <div class="clear"></div>
                <div class="hide" id="low_stock" style="display:inline;">
                    ONLY
                    <div id="stock_qty" style="display:inline;">1</div>
                    REMAINING
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>

</div>

<script type="application/javascript">
    function isOutOfStock() {
        return !$('#out_of_stock').is(":hidden");
    }

    function figureStockStatusGroundState() {
        var $in_stock = $('#in_stock');
        var $out_stock = $('#out_of_stock');
        var $low_stock = $('#low_stock');
        $(".error").hide();
        $('#error_message').text('');
        $in_stock.hide();
        $out_stock.hide();
        $low_stock.hide();
    }

    // JLB 09-25-18
    // Why do they keep beign SO SLOPPY in naming? That's not a part. That's a part variation.
    function figureStockStatus(partObj) {


        var $in_stock = $('#in_stock');
        var $out_stock = $('#out_of_stock');
        var $low_stock = $('#low_stock');
        figureStockStatusGroundState();



        $("#submit_button").attr("onclick", "submitCart()");
        console.log(partObj.quantity_available);
        if (partObj.quantity_available > 0)
        {
            $in_stock.show();
            $low_stock.hide();
            if (partObj.quantity_available < 6)
            {
                $low_stock.show();
                $('#stock_qty').html(partObj.quantity_available);
            }

            if (partObj.dealer_quantity_available && parseInt(partObj.dealer_quantity_available, 10) > 0) {
                console.log("B Yes ");
                $("#in_stock .instock").show();
                $("#in_stock .online_only").hide();

            } else {
                console.log("B No  ");
                $("#in_stock .online_only").show();
                $("#in_stock .instock").hide();
            }
        } else
        {
            if (partObj.quantity_available <= 0) {
                $out_stock.show();
                $("#submit_button").attr("onclick", "outOfStockWarning()");
            }
        }

        return partObj.quantity_available > 0;
    }

</script>