<?php
    /*
        * Payment Confirmation page : has call to execute the payment and displays the Confirmation details
    */
    if (session_id() == "")
        session_start();

    include('utilFunctions.php');
    include('paypalFunctions.php');


    if( isset($_GET['paymentId']) && isset($_GET['PayerID'])){ //Proceed to Checkout or Mark flow

        //call to execute payment
        $response = doPayment(filter_input( INPUT_GET, 'paymentId', FILTER_SANITIZE_STRING ), filter_input( INPUT_GET, 'PayerID', FILTER_SANITIZE_STRING ), NULL);

    } else { //Express checkout flow

        if(verify_nonce()){
            $expressCheckoutFlowArray = json_decode($_SESSION['expressCheckoutPaymentData'], true);
                    $expressCheckoutFlowArray['transactions'][0]['amount']['total'] = (float)$expressCheckoutFlowArray['transactions'][0]['amount']['total'] + (float)$_POST['shipping_method'] - (float)$expressCheckoutFlowArray['transactions'][0]['amount']['details']['shipping'];
                    $expressCheckoutFlowArray['transactions'][0]['amount']['details']['shipping'] = $_POST['shipping_method'];
                    $transactionAmountUpdateArray = $expressCheckoutFlowArray['transactions'][0];
                    $_SESSION['expressCheckoutPaymentData'] = json_encode($expressCheckoutFlowArray);

                    //call to execute payment with updated shipping and overall amount details
                    $response = doPayment($_SESSION['paymentID'], $_SESSION['payerID'], $transactionAmountUpdateArray);
        } else {
            die('Session expired');
        }
    }
	
	// REST validation; route non-HTTP 200 to error page
	if ($response['http_code'] != 200 && $response['http_code'] != 201) {		
		$_SESSION['error'] = $response;
		header( 'Location: error.php');
		
		// need exit() here to maintain session data after redirect to error page
		exit();
	}
	
	$json_response = $response['json']; 
	
    $paymentID= $json_response['id'];
    $paymentState = $json_response['state'];
    $finalAmount = $json_response['transactions'][0]['amount']['total'];
    $currency = $json_response['transactions'][0]['amount']['currency'];
    $transactionID= $json_response['transactions'][0]['related_resources'][0]['sale']['id'];

    $payerFirstName = filter_var($json_response['payer']['payer_info']['first_name'],FILTER_SANITIZE_SPECIAL_CHARS);
    $payerLastName = filter_var($json_response['payer']['payer_info']['last_name'],FILTER_SANITIZE_SPECIAL_CHARS);
    $recipientName= filter_var($json_response['payer']['payer_info']['shipping_address']['recipient_name'],FILTER_SANITIZE_SPECIAL_CHARS);
    $addressLine1= filter_var($json_response['payer']['payer_info']['shipping_address']['line1'],FILTER_SANITIZE_SPECIAL_CHARS);
    $addressLine2 = (isset($json_response['payer']['payer_info']['shipping_address']['line2']) ? filter_var($json_response['payer']['payer_info']['shipping_address']['line2'],FILTER_SANITIZE_SPECIAL_CHARS) :  "" );
    $city= filter_var($json_response['payer']['payer_info']['shipping_address']['city'],FILTER_SANITIZE_SPECIAL_CHARS);
    $state= filter_var($json_response['payer']['payer_info']['shipping_address']['state'],FILTER_SANITIZE_SPECIAL_CHARS);
    $postalCode = filter_var($json_response['payer']['payer_info']['shipping_address']['postal_code'],FILTER_SANITIZE_SPECIAL_CHARS);
    $countryCode= filter_var($json_response['payer']['payer_info']['shipping_address']['country_code'],FILTER_SANITIZE_SPECIAL_CHARS);
	
    include('header.php');
?>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <h4>
                <?php echo($payerFirstName.' '.$payerLastName.', Thank you for your Order!');?><br/><br/>
                Shipping Address: </h4>
                <?php echo($recipientName);?><br/>
                <?php echo($addressLine1);?><br/>
                <?php echo($addressLine2);?><br/>
                <?php echo($city);?><br/>
                <?php echo($state.'-'.$postalCode);?><br/>
                <?php echo($countryCode);?>

                <h4>Payment ID: <?php echo($paymentID);?> <br/>
		Transaction ID : <?php echo($transactionID);?> <br/>
                State : <?php echo($paymentState);?> <br/>
                Total Amount: <?php echo($finalAmount);?> &nbsp;  <?php echo($currency);?> <br/>
            </h4>
            <br/>
            Return to <a href="index.php">home page</a>.
        </div>
        <div class="col-md-4"></div>
    </div>
<?php
    if (session_id() !== "") {
               session_unset();
               session_destroy();
            }
    include('footer.php');
?>

