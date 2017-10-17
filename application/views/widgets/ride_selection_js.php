<?php

$base_url_string = (isset($secure) && $secure) ? "s_base_url" : "base_url";

?>
<script type="application/javascript">
    function updateGarage()
    {
        var pathname = window.location.pathname;
        $('#update_garage_form').append('<input type="hidden" name="url" value="'+pathname +'" />');
        $('#update_garage_form').submit();

    }

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


                                // JLB 09-28-17
                                // It would be great if these were alphabetized, otherwise, it's a little stupid.
                                var reverseMap = {};
                                var arr = [];
                                for (var x in responseData) {
                                    reverseMap[ responseData[x] ] = x;
                                    arr.push(responseData[x]);
                                }

                                // now, sort it
                                arr.sort(function(a, b){a = a.toLowerCase(); b = b.toLowerCase(); if (a < b) { return -1} else if (a > b) { return 1; } else { return 0; }});

                                // Now, iterate and make them in alphabetical order
                                for (var i = 0; i < arr.length; i++) {
                                    var text = arr[i];
                                    var val = reverseMap[text];
                                    mySelect.append(
                                        $('<option></option>').val(val).html(text)
                                    );
                                }

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
                                var reverseMap = {};

                                for(var x in responseData){
                                    arr.push(responseData[x]);
                                    reverseMap[responseData[x]] = x;
                                }

                                arr.sort(function(a, b){a = a.toLowerCase(); b = b.toLowerCase(); if (a < b) { return -1} else if (a > b) { return 1; } else { return 0; }});
                                $('#model').selectbox("detach");
                                var mySelect = $('#model');
                                mySelect.html($('<option></option>').val('').html('-- Select Model --'));
                                for (var i = 0; i < arr.length; i++) {
                                    var text = arr[i];
                                    var val = reverseMap[text];
                                    mySelect.append(
                                        $('<option></option>').val(val).html(text)
                                    );
                                }
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
                updateGarage();
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


    // JLB 09-27-17
    // I have no idea why these were run at the top before the functions were defined, and I fear running them does something bad...but it looks relatively safe.
    (function() {
        executeMachine();
        executeMake();
        executeModel();
        executeYear();
    })();

</script>