<?php
$qty_input = form_input(array('name' => 'qty',
    'value' => 1,
    'maxlength' => 250,
    'class' => 'text mini qtyInput',
    'placeholder' => '0',
    'id' => 'qty'));
?>
<div class="questions_and_quantities_block">
    <?php


    if (isset($questions) && is_array($questions) && count($questions) > 0) {
        // Reassemble this into a structure that maps partquestion_id => an array of the question and answers, then display those answers
        $requestioned = array();
        foreach ($questions as $question) {
            $partquestion_id = $question["partquestion_id"];
            if (!array_key_exists($partquestion_id, $requestioned)) {
                $requestioned[$partquestion_id] = array(
                    "question" => $question["question"],
                    "partquestion_id" => $partquestion_id,
                    "answers" => array(
                        '0' => 'Select an option'
                    )
                );
            }
            $requestioned[$partquestion_id]["answers"][ $question['partnumber'] ] = $question['answer'];
        }

        print "<!-- Requestioned: ";
        print_r($requestioned);
        print "-->";

        $currentQuestion = "";

        foreach ($requestioned as $key => $quest) {
            $currentQuestion = $quest['partquestion_id'];
            ?>
            <div class="questionSelector">
                <div class="questionString">
                    <?php echo $quest['question']; ?>:
                </div>
                <div class="answerString">
                    <?php
                    echo form_dropdown('question[]', $quest['answers'], @$_SESSION['cart'][$product['part_id']][$quest['partquestion_id']], 'style="" class="question questionSelector ' . $currentQuestion . '" onchange="updatePrice(' . $currentQuestion . ');" data-partquestion-id="' . $quest['partquestion_id'] . '"'); ?>
                </div>
                <div style="clear: both"></div>
            </div>
            <div class="question_quantity_description" style="display: none" id="question_quantity_description<?php echo $quest['partquestion_id']; ?>">
                <div class="stock hide out_of_stock" id="out_of_stock<?php echo $quest['partquestion_id']; ?>">
                    <span class="outOfStockStatus">OUT OF STOCK - PLEASE CALL TO ORDER</span>
                </div>
                <div class="stock hide in_stock" id="in_stock<?php echo $quest['partquestion_id']; ?>">
                    <span class="stockStatus">In Stock</span>
                    <span class="online_only hide" style="display: inline-block" id="online_only<?php echo $quest['partquestion_id']; ?>">Online Only</span><span class="instock hide"  style="display: inline-block" id="instock<?php echo $quest['partquestion_id']; ?>">Available For Store Pickup</span>
                    <div class="clear"></div>
                    <div class="hide" id="low_stock" style="display:inline;">
                        ONLY
                        <div id="stock_qty" style="display:inline;">1</div>
                        REMAINING
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <?php
        }

    }

    ?>

    <div class="quantity_block">
        <div class="quantity_selector">
            QTY:
            <?php echo $qty_input; ?>
        </div>


    </div>

</div>
<script type="application/javascript">
    // The idea of this is to show all the little warnings, and, if something is not selected, show the big warning...but only if there's not already an error on the page.
    function isOutOfStock(hide_error) {
        var proceed;
        if ($(".question")[0])
        {
            $(".question").each(function ()
            {
                var partQuestionId = $(this).attr("data-partquestion-id");

                if (!$('#out_of_stock' + partQuestionId).is(":hidden"))
                {
                    proceed = 'error';
                    if (!hide_error) {
                        $('.error').show();
                        $('#error_message').text('One of the items you selected is OUT OF STOCK - PLEASE CALL TO ORDER.');
                    }
                }
                // Make sure all necessary questions are answered before processing
            });
        }
        return proceed == 'error' || isIncomplete(hide_error);
    }

    // Before, for some reason, it did this at the same time...which seemed a little silly, since the one error would clobber the other.
    function isIncomplete() {
        var proceed;
        if ($(".question")[0])
        {
            $(".question").each(function ()
            {
                var partQuestionId = $(this).attr("data-partquestion-id");

                if ($(this).val() == 0)
                {
                    proceed = 'error';
                    if (!hide_error) {
                        $('.error').show();
                        $('#error_message').text('Please select a dropdown option for this part.');
                    }
                }
            });
        }
        return proceed == 'error';
    }

    //
    function cleanStockState() {
        if (isOutOfStock(true)) {
            $("#submit_button").attr("onclick", "outOfStockWarning()");
        } else {
            $("#submit_button").attr("onclick", "submitCart()");

        }
    }

    // JLB 09-26-18
    // This suggests it's going to make several trips home, because we should be saving some state, somewhere.
    function updatePrice(questionId)
    {
        $('#price').html("<?php echo $original_price; ?>");

        var carried_price = 0;
        $(".question").each(function ()
        {
            if ($(this).val() != 0)
            {
                var partQuestionId = $(this).attr("data-partquestion-id");

                var $partnumber = $(this).val();
                $.post(base_url + 'ajax/getPriceByPartNumber/',
                    {
                        'partnumber': $partnumber,
                        'ajax': true
                    },
                    function (partRec)
                    {
                        var partObj = jQuery.parseJSON(partRec);
                        totalprice = parseFloat(partObj.sale);
                        carried_price = carried_price + totalprice;
                        $('#price').html('$' + parseFloat(carried_price).toFixed(2));
                        figureStockStatus(partQuestionId, partObj);
                    });
            } else {
                $("#question_quantity_description" + partQuestionId).hide(); // just hide it. They've made no selection..
            }
        });
    }

    // JLB 09-25-18
    // Why do they keep beign SO SLOPPY in naming? That's not a part. That's a part variation.
    function figureStockStatusForPart(partQuestionId, partObj) {


        var $in_stock = $('#in_stock' + partQuestionId);
        var $out_stock = $('#out_of_stock' + partQuestionId);
        var $low_stock = $('#low_stock' + partQuestionId );
        $(".error").hide();
        $('#error_message').text('');
        $in_stock.hide();
        $out_stock.hide();
        $low_stock.hide();


        if (proceed && partObj.quantity_available > 0)
        {
            $in_stock.show();
            $low_stock.hide();
            if (partObj.quantity_available < 6)
            {
                $low_stock.show();
                $('#stock_qty').html(partObj.quantity_available);
            }

            if (partObj.dealer_quantity_available && parseInt(partObj.dealer_quantity_available, 10) > 0) {
                $("#in_stock .instock").show();
                $("#in_stock .online_only").hide();

            } else {
                $("#in_stock .online_only").show();
                $("#in_stock .instock").hide();
            }
        } else
        {
            if (partObj.quantity_available <= 0) {
                $out_stock.show();
            }
        }

        cleanStockState();

    }


</script>