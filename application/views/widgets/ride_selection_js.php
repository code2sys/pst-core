<?php

$base_url_string = (isset($secure) && $secure) ? "s_base_url" : "base_url";

?>

/*
  ride_selection_js
 base_url should already be defined...

 */
<script type="application/javascript">


    function executeMachine() {
        $("#machine").selectbox({
            onChange: function (val, inst)
            {
                if(val != '')
                {
                    $.ajax(
                        {
                            async: false,
                            type: 'POST',
                            url: <?php echo $base_url_string; ?> + 'ajax/getMake/',
                            data : {'machineId' :  val,
                                <?php if(@$product['part_id']): ?>
                                'partId' : '<?php echo $product['part_id']; ?>',
                                <?php endif; ?>
                                'ajax' : true
                            },
                            success: function(encodeResponse)
                            {
                                responseData = JSON.parse(encodeResponse);
                                $('#make').selectbox("detach");
                                var mySelect = $('#make');
                                mySelect.html($('<option></option>').val('').html('-- Select Make --'));
                                $.each(responseData, function(val, text) {
                                    mySelect.append(
                                        $('<option></option>').val(val).html(text)
                                    );
                                });
                                executeMake();
                                $('#make').selectbox("attach");
                            }
                        });
                }
                else
                {
                    $('#make').selectbox("detach");
                    $('#make').html($('<option></option>').val('').html('-- Make --'));
                    executeMake();
                    $('#make').selectbox("attach");
                }
                $('#model').selectbox("detach");
                $('#year').selectbox("detach");
                $('#model').html($('<option></option>').val('').html('-- Model --'));
                executeModel();
                $('#model').selectbox("attach");
                $('#year').html($('<option></option>').val('').html('-- Year --'));
                executeYear();
                $('#year').selectbox("attach");
                $('#add').attr('class', 'button_no' );

            }
        });
    }

    function executeMake() {
        $("#make").selectbox({
            onChange: function (val, inst)
            {
                if(val != '')
                {
                    $.ajax(
                        {
                            async: false,
                            type: 'POST',
                            url: <?php echo $base_url_string; ?> + 'ajax/getNewYear/',
                            data : {'makeId' :  val,
                                <?php if(@$product['part_id']): ?>
                                'partId' : '<?php echo $product['part_id']; ?>',
                                <?php endif; ?>
                                'ajax' : true
                            },
                            success: function(encodeResponse)
                            {
                                responseData = JSON.parse(encodeResponse);

                                var arr = [];

                                for(var x in responseData){
                                    arr.push(responseData[x]);
                                }

                                arr.sort(function(a, b){return b - a});
                                console.log(["Array in executeMake success", arr]);
                                $('#year').selectbox("detach");
                                var mySelect = $('#year');
                                mySelect.html($('<option></option>').val('').html('-- Select Year --'));
                                $.each(arr, function(val, text) {
                                    mySelect.append(
                                        $('<option></option>').val(text).html(text)
                                    );
                                });
                                executeYear();
                                $('#year').selectbox("attach");
                            }
                        });
                }
                else
                {
                    $('#year').selectbox("detach");
                    $('#year').html($('<option></option>').val('').html('-- Model --'));
                    executeYear();
                    $('#year').selectbox("attach");
                }
                $('#model').selectbox("detach");
                $('#model').html($('<option></option>').val('').html('-- Year --'));
                executeModel();
                $('#model').selectbox("attach");
                $('#add').attr('class', 'button_no' );

            }
        });
    }

    function executeYear() {
        $("#year").selectbox({
            onChange: function (val, inst)
            {
                if(val != '')
                {
                    $.ajax(
                        {
                            async: false,
                            type: 'POST',
                            url: <?php echo $base_url_string; ?> + 'ajax/getNewModel/',
                            data : {
                                'year' :  val, // $("#update_garage_form [name=year] option:selected").text(),
                                'makeId' : $("#update_garage_form [name=make]").val(),
                                <?php if(@$product['part_id']): ?>
                                'partId' : '<?php echo $product['part_id']; ?>',
                                <?php endif; ?>
                                'ajax' : true
                            },
                            success: function(encodeResponse)
                            {
                                responseData = JSON.parse(encodeResponse);

                                var arr = [];

                                for(var x in responseData){
                                    arr.push(responseData[x]);
                                }

                                arr.sort(function(a, b){return b - a});

                                console.log(["Array in executeYear success", arr]);
                                $('#model').selectbox("detach");
                                var mySelect = $('#model');
                                mySelect.html($('<option></option>').val('').html('-- Select Model --'));
                                $.each(arr, function(val, text) {
                                    mySelect.append(
                                        $('<option></option>').val(val).html(text)
                                    );
                                });
                                executeModel();
                                $('#model').selectbox("attach");
                            }
                        });
                }
                else
                {
                    $('#model').selectbox("detach");
                    $('#model').html($('<option></option>').val('').html('-- Year --'));
                    executeModel();
                    $('#model').selectbox("attach");
                }
                $('#add').attr('class', 'button_no' );

            }
        });
    }

    function executeModel()
    {
        $("#model").selectbox({
            onChange: function (val, inst)
            {
                displayAdd(val);
            }
        });
    }


    function displayAdd(val)
    {
        if(val != '')
            $('#add').attr('class', 'button' );
        else
            $('#add').attr('class', 'button_no' );
    }

    function updateGarage()
    {
        var pathname = window.location.pathname;
        $('#update_garage_form').append('<input type="hidden" name="url" value="'+pathname +'" />');
        $('#update_garage_form').submit();

    }

    // JLB 09-27-17
    // I have no idea why these were run at the top before the functions were defined, and I fear running them does something bad...but it looks relatively safe.
    (function() {
        executeMachine();
        executeMake();
        executeModel();
        executeYear();
    })();

</script>
