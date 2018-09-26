<div class="questions_and_quantities_block">
    <?php
    $is_qty_displayed = 0;

    // JLB 08-31-18
    // This was a shitpile before I got here. It's still weird, but it's not as awful as it was!
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
                    echo form_dropdown('question[]', $quest['answers'], @$_SESSION['cart'][$product['part_id']][$quest['partquestion_id']], 'style="", class="question questionSelector ' . $currentQuestion . '", onchange="updatePrice(' . $currentQuestion . ');"'); ?>
                </div>
                <div style="clear: both"></div>
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
        var proceed;
        if ($(".question")[0])
        {
            $(".question").each(function ()
            {

                if (!$('#out_of_stock').is(":hidden"))
                {
                    proceed = 'error';
                    $('.error').show();
                    $('#error_message').text('One of the items you selected is OUT OF STOCK - PLEASE CALL TO ORDER.');
                    return false;
                }
                // Make sure all necessary questions are answered before processing

                if ($(this).val() == 0)
                {
                    proceed = 'error';
                    $('.error').show();
                    $('#error_message').text('Please select a dropdown option for this part.');
                    return false;
                }
            });
        }
    }

    function updatePrice(questionId)
    {
        $('#price').html("<?php echo $original_price; ?>");
        figureStockStatusGroundState();

        var carried_price = 0;
        $(".question").each(function ()
        {
            if ($(this).val() != 0)
            {
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

                        stillOutOfStock[$partnumber] = !figureStockStatus(partObj);
                        tailOutOfStock();
                    });
            }
        });
    }

    // JLB 09-25-18
    // Why do they keep beign SO SLOPPY in naming? That's not a part. That's a part variation.
    function figureStockStatus(partObj) {


        var $in_stock = $('#in_stock');
        var $out_stock = $('#out_of_stock');
        var $low_stock = $('#low_stock');
        figureStockStatusGroundState();


        var proceed = true;
        if ($(".question")[0])
        {
            $(".question").each(function ()
            {

                if ($(this).val() == 0)
                {
                    proceed = false;
                }
            });
        }

        // if (!proceed) {
        //     console.log(["partObj", partObj])
        //     console.log("Dying on proceed");
        //     return; // nothing to do here. not all questions are filled in...
        // }



        $("#submit_button").attr("onclick", "submitCart()");
        console.log(partObj.quantity_available);
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

    // JLB 09-25-18
    // Basically, we are going to have to keep a list of what part numbers are out of stock, and, if those are still alive, we have to reject them.
    var stillOutOfStock = {};
    function tailOutOfStock() {
        $(".question").each(function ()
        {

            if (stillOutOfStock[$(this).val()])
            {
                $('#in_stock').hide();
                $('#low_stock').hide();
                $('#out_of_stock').show();
            }
        });
    }

</script>