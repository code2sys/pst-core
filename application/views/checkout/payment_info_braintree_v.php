<?php
//require_once( echo site_url()  . 'lib/Braintree.php');
require(__DIR__ . "/../braintree_clienttoken.php");
?>
<script src="https://js.braintreegateway.com/web/3.7.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.7.0/js/hosted-fields.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.7.0/js/data-collector.min.js"></script>
<script>
    var form = document.querySelector('#form_example');
    var submit = document.querySelector('input[type="submit"]');

    braintree.client.create({
        authorization: '<?php echo $clientToken;?>'
    }, function (clientErr, clientInstance) {
        if (clientErr) {
            console.error(clientErr);
            return;
        }

        // This example shows Hosted Fields, but you can also use this
        // client instance to create additional components here, such as
        braintree.hostedFields.create({
            client: clientInstance,
            styles: {
                'input': {
                    'font-size': '14px'
                },
                'input.invalid': {
                    'color': 'red'
                },
                'input.valid': {
                    'color': 'green'
                }
            },
            fields: {
                number: {
                    selector: '#card-number',
                    placeholder: '4111 1111 1111 1111'
                },
                cvv: {
                    selector: '#cvv',
                    placeholder: '123'
                },
                expirationDate: {
                    selector: '#expiration-date',
                    placeholder: '10/2019'
                }
            }
        }, function (hostedFieldsErr, hostedFieldsInstance) {
            if (hostedFieldsErr) {
                console.error(hostedFieldsErr);
                return;
            }

            submit.removeAttribute('disabled');

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
                    if (tokenizeErr) {
                        console.error(tokenizeErr);
                        //alert('All fields are required.');
                        $('.fld').css('border', '1px solid red');
                        return;
                    }

                    // If this was a real integration, this is where you would
                    // send the nonce to your server.
                    var addNonce = "<input type='hidden' id='payment_method_nonce' name='payment_method_nonce' value='"+ payload.nonce +"'>";
                    $("#form_example").append(addNonce);
                    console.log('Got a nonce: ' + payload.nonce);
                    HTMLFormElement.prototype.submit.call(form);
                });
            }, false);
        });
        // PayPal or Data Collector.
        braintree.dataCollector.create({
            client: clientInstance,
            kount: true
        }, function (err, dataCollectorInstance) {
            if (err) {
                //alert(err);
                return;
            } else {
                //alert(dataCollectorInstance.deviceData)
            }
            // At this point, you should access the dataCollectorInstance.deviceData value and provide it
            // to your server, e.g. by injecting it into your form as a hidden input.
            var addNonce = "<input type='hidden' id='device_data' name='device_data' value='"+ dataCollectorInstance.deviceData +"'>";
            $("#form_example").append(addNonce);
            var deviceData = dataCollectorInstance.deviceData;
        });
    });
</script>