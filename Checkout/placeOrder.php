<?php
    /*
        * Place Order Page : part of the Express Checkout flow. Buyer can choose shipping option on this page.
    */
    if (session_id() == "")
        session_start();

    include('header.php');
    include('utilFunctions.php');
    include('paypalFunctions.php');

    $_SESSION['paymentID']= filter_input( INPUT_GET, 'paymentId', FILTER_SANITIZE_STRING );
    $_SESSION['payerID'] = filter_input( INPUT_GET, 'PayerID', FILTER_SANITIZE_STRING );
    $access_token = $_SESSION['access_token'];
    $lookUpPaymentInfo = lookUpPaymentDetails($_SESSION['paymentID'], $access_token);
    $recipientName= filter_var($lookUpPaymentInfo['payer']['payer_info']['shipping_address']['recipient_name'],FILTER_SANITIZE_SPECIAL_CHARS);
    $addressLine1= filter_var($lookUpPaymentInfo['payer']['payer_info']['shipping_address']['line1'],FILTER_SANITIZE_SPECIAL_CHARS);
    $addressLine2 =  (isset($lookUpPaymentInfo['payer']['payer_info']['shipping_address']['line2']) ?  filter_var($lookUpPaymentInfo['payer']['payer_info']['shipping_address']['line2'],FILTER_SANITIZE_SPECIAL_CHARS) :  "");
    $city= filter_var($lookUpPaymentInfo['payer']['payer_info']['shipping_address']['city'],FILTER_SANITIZE_SPECIAL_CHARS);
    $state= filter_var($lookUpPaymentInfo['payer']['payer_info']['shipping_address']['state'],FILTER_SANITIZE_SPECIAL_CHARS);
    $postalCode = filter_var($lookUpPaymentInfo['payer']['payer_info']['shipping_address']['postal_code'],FILTER_SANITIZE_SPECIAL_CHARS);
    $countryCode= filter_var($lookUpPaymentInfo['payer']['payer_info']['shipping_address']['country_code'],FILTER_SANITIZE_SPECIAL_CHARS);

?>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <h3>Ship To :</h3>
            <?php echo($recipientName);?><br/>
            <?php echo($addressLine1);?><br/>
            <?php echo($addressLine2);?><br/>
            <?php echo($city);?><br/>
            <?php echo($state.'-'.$postalCode);?><br/>
            <?php echo($countryCode);?>

            <form action="pay.php" method="POST">
                <input type="text" name="csrf" value="<?php echo($_SESSION['csrf']);?>" hidden readonly/>
                <label>Shipping methods:</label>
                <select class="form-control" name="shipping_method" id="shipping_method" style="width: 250px;" class="required-entry">
                    <optgroup label="United Parcel Service" style="font-style:normal;">
                        <option value="8.00">
                        Worldwide Expedited - 8.00</option>
                        <option value="4.00">
                        Worldwide Express Saver - 4.00</option>
                    </optgroup>
                    <optgroup label="Flat Rate" style="font-style:normal;">
                        <option value="2.00" selected>
                        Fixed - 2.00</option>
                    </optgroup>
                </select>
                <br/>
                <button type="submit" class="btn btn-primary">Confirm Order</button>
            </form>
            <br/>
        </div>
        <div class="col-md-4"></div>
    </div>
<?php
    include('footer.php');
?>