<?php
    /*
        * Home Page - has Sample Buyer credentials, Camera (Product) Details and Instructiosn for using the code sample
    */
    include('apiCallsData.php');
    include('header.php');
    include('paypalConfig.php');

    //setting the environment for Checkout script
    if(SANDBOX_FLAG) {
        $environment = SANDBOX_ENV;
    } else {
        $environment = LIVE_ENV;
    }
?>
    <style>
        tr{
            line-height:30px;
        }
        td{
            padding:5px;
        }
    </style>
    <div class="row">
        <div class="col-md-3">
            <div>
                <img src="img/camera.jpg" width="200" height="150"/>
            </div>
            <h3>Digital SLR Camera </h3>
            <br/><br/>
            <table class="table table-striped">
                <tr><td colspan="2"><h4> Sample Sandbox Buyer Credentials:</h4></td></tr>
                <tr><th>Buyer Email</th><th>Password</th></tr>
                <tr><td>emily_doe@buyer.com</td><td>qwer1234</td></tr>
                <tr><td>bill_bong@buyer.com</td><td>qwer1234</td></tr>
                <tr><td>jack_potter@buyer.com</td><td>123456789</td></tr>
                <tr><td>harry_doe@buyer.com</td><td>123456789</td></tr>
                <tr><td>ron_brown@buyer.com</td><td>qwer1234</td></tr>
                <tr><td>bella_brown@buyer.com</td><td>qwer1234</td></tr>
            </table>
        </div>

        <div class="col-md-4">
            <h3> Pricing Details </h3>
            <form action="startPayment.php" method="POST">
                 <input type="text" name="csrf" value="<?php echo($_SESSION['csrf']);?>" hidden readonly/>
                 <table>
                    <!-- Item Details - Actual values set in apiCallsData.php -->
                     <tr><td>Camera </td><td><input class="form-control" type="text" name="camera_amount" value="300" readonly></input></td></tr>
                     <tr><td>Tax </td><td><input class="form-control" type="text" name="tax" value="5" readonly></input> </td></tr>
                     <tr><td>Insurance </td><td><input class="form-control" type="text" name="insurance" value="10" readonly></input> </td></tr>
                     <tr><td>Handling Fee </td><td><input class="form-control" type="text" name="handling_fee" value="5" readonly></input> </td></tr>
                     <tr><td>Estimated Shipping </td><td><input class="form-control" type="text" name="estimated_shipping" value="2" readonly></input> </td></tr>
                     <tr><td>Shipping Discount </td><td><input class="form-control" type="text" name="shipping_discount" value="-2" readonly></input> </td></tr>
                     <tr><td>Total Amount </td><td><input class="form-control" type="text" name="total_amount" value="320" readonly></input> </td></tr>
                     <tr><td>Currency</td><td>
                        <select class="form-control" name="currencyCodeType">
                        						<option value="AUD">AUD</option>
                        						<option value="BRL">BRL</option>
                        						<option value="CAD">CAD</option>
                        						<option value="CZK">CZK</option>
                        						<option value="DKK">DKK</option>
                        						<option value="EUR">EUR</option>
                        						<option value="HKD">HKD</option>
                        						<option value="MYR">MYR</option>
                        						<option value="MXN">MXN</option>
                        						<option value="NOK">NOK</option>
                        						<option value="NZD">NZD</option>
                        						<option value="PHP">PHP</option>
                        						<option value="PLN">PLN</option>
                        						<option value="GBP">GBP</option>
                        						<option value="RUB">RUB</option>
                        						<option value="SGD">SGD</option>
                        						<option value="SEK">SEK</option>
                        						<option value="CHF">CHF</option>
                        						<option value="THB">THB</option>
                        						<option value="USD" selected>USD</option>
                     </td></tr>

                 </table>

                <br/>
                <!--Container for Checkout with PayPal button-->
                <div id="myContainer"></div>
                <br/>
                <span style="margin-left:60px">OR</span>
                <br/><br/>
                <div>
                    <button class="btn btn-primary" formaction="shipping.php" role="button">Proceed to Checkout</button>
                </div>
            </form>
        </div>
        <div class="col-md-5">
            <h4> README: </h4>
            <h5>BEFORE YOU GET STARTED:</h5>
            This code sample shows the new checkout flow called In-Context checkout experience. You need to meet the <a href="https://developer.paypal.com/docs/classic/express-checkout/in-context/#eligibility-review" target="_blank">eligibility criteria </a> to determine whether your integration will be a good candidate for In-Context checkout experience option. Please refer to the <a href="https://developer.paypal.com/docs/classic/express-checkout/in-context/#eligibility-review" target="_blank">eligibility criteria </a>. <br>If you are eligible for In-Context checkout based on the eligibility requirements, please refer to the <a href="#incontext">'In-Context Checkout integration steps'</a> below. But, if you are not eligible, please refer to the <a href="#expresscheckout">'Express Checkout integration steps'</a> below.
            <br>
            <h5> PRE-READ: </h5>
            <p>
              1) Click on ‘Checkout with PayPal’ button and see the experience.
              <br>
              2) If you get any Firewall warning, add rule to the Firewall to allow incoming connections for your application.
              <br>
              3) Checkout with PayPal using a buyer sandbox account provided on this page. And you're done!
              <br>
              4) The sample code uses default sandbox REST App credentials which are set in paypalConfig.php. You can get your own REST app credentials by creating a REST app with the steps outlined  <i><a href="https://developer.paypal.com/docs/integration/direct/make-your-first-call/#create-a-paypal-app" target="_blank">here</a></i>.
              <br>
              5) Make following changes in paypal_config.php:<br>
              - If using your own Sandbox seller account, update MERCHANT_ID with your merchant id. To get your merchant id, log into your <a href="https://www.sandbox.paypal.com/" target="_blank">Sandbox</a> or <a href="https://www.paypal.com" target="_blank">Live</a> business account as per the integration. The merchant ID can be found under My Account &gt; Profile &gt; My business info &gt; Merchant account ID.<br/>
              - Also, update SANDBOX_CLIENT_ID and SANDBOX_CLIENT_SECRET values with your REST app seller sandbox credentials<br>
              - SANDBOX_FLAG: Kept true for working with Sandbox, it will be false for live.<br>
              </p>
               <h4 id="incontext"> In-Context Checkout integration steps: </h4>
                             1) Copy the files and folders under 'Checkout' package to the same location where you have your shopping cart page.
                             <br>
                             2) Include apiCallsData.php at the top of the shopping cart page.<br>
                             3) Copy the below  &lt;form&gt; .. &lt;/form&gt; to your shopping cart page.
                             <br><br>
                 <pre><code>&lt;form id=&quot;myContainer&quot; action=&quot;startPayment.php&quot; method=&quot;POST&quot;&gt;
    &lt;input type=&quot;hidden&quot; name=&quot;csrf&quot; value=&quot;&lt;?php echo($_SESSION['csrf']);?&gt;&quot;/&gt;
    Camera:&lt;input type=&quot;text&quot; name=&quot;camera_amount&quot; value=&quot;300&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
    Tax:&lt;input type=&quot;text&quot; name=&quot;tax&quot; value=&quot;5&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
    Insurance:&lt;input type=&quot;text&quot; name=&quot;insurance&quot; value=&quot;10&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
    Handling:&lt;input type=&quot;text&quot; name=&quot;handling_fee&quot; value=&quot;5&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
    Est. Shipping:&lt;input type=&quot;text&quot; name=&quot;estimated_shipping&quot; value=&quot;2&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
    Shipping Discount:&lt;input type=&quot;text&quot; name=&quot;shipping_discount&quot; value=&quot;-2&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
    Total:&lt;input type=&quot;text&quot; name=&quot;total_amount&quot; value=&quot;320&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
    Currency:&lt;input type=&quot;text&quot; name=&quot;currencyCodeType&quot; value=&quot;USD&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
&lt;/form&gt;</code></pre><br>
                             4) Include the following script on your shopping cart page:
                             <br><br>
                 <pre><code>&lt;script type="text/javascript"&gt;
   window.paypalCheckoutReady = function () {
       paypal.checkout.setup('Your merchant id', {
           container: 'myContainer', //{String|HTMLElement|Array} where you want the PayPal button to reside
           environment: 'sandbox' //or 'production' depending on your environment
       });
   };
&lt;/script&gt;
&lt;script src="//www.paypalobjects.com/api/checkout.js" async&gt;&lt;/script&gt;</code></pre><br>
                             5) Open your browser and navigate to your Shopping cart page. Click on 'Checkout with PayPal' button and complete the flow.<br>
                             6) Read overview of REST API <a href="https://developer.paypal.com/docs/integration/direct/rest-payments-overview/" target=_blank>here</a> and find the API reference <a href="https://developer.paypal.com/docs/api/" target="_blank">here</a>.<br/><br/>
                           <br><br>
                           <h4 id="expresscheckout"> Express Checkout integration steps: </h4>
                          1) Copy the files and folders under 'Checkout' package to the same location where you have your shopping cart page.
                                         <br>
                          2) Include apiCallsData.php at the top of the shopping cart page.<br>
                          3) Copy the below  &lt;form&gt; .. &lt;/form&gt; to your shopping cart page.
                                         <br><br>
                <pre><code>&lt;form action=&quot;startPayment.php&quot; method=&quot;POST&quot;&gt;
   &lt;input type=&quot;hidden&quot; name=&quot;csrf&quot; value=&quot;&lt;?php echo($_SESSION['csrf']);?&gt;&quot;/&gt;
   Camera:&lt;input type=&quot;text&quot; name=&quot;camera_amount&quot; value=&quot;300&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
   Tax:&lt;input type=&quot;text&quot; name=&quot;tax&quot; value=&quot;5&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
   Insurance:&lt;input type=&quot;text&quot; name=&quot;insurance&quot; value=&quot;10&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
   Handling:&lt;input type=&quot;text&quot; name=&quot;handling_fee&quot; value=&quot;5&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
   Est. Shipping:&lt;input type=&quot;text&quot; name=&quot;estimated_shipping&quot; value=&quot;2&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
   Shipping Discount:&lt;input type=&quot;text&quot; name=&quot;shipping_discount&quot; value=&quot;-2&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
   Total:&lt;input type=&quot;text&quot; name=&quot;total_amount&quot; value=&quot;320&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
   Currency:&lt;input type=&quot;text&quot; name=&quot;currencyCodeType&quot; value=&quot;USD&quot; readonly&gt;&lt;/input&gt;&lt;br&gt;
   &lt;input type=&quot;image&quot; src=&quot;https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-large.png&quot; alt=&quot;Check out with PayPal&quot;&gt;&lt;/input&gt;
&lt;/form&gt;</code></pre><br>
                          4) Open your browser and navigate to your Shopping cart page. Click on 'Checkout with PayPal' button and complete the flow.<br>
                          5) Read overview of REST API <a href="https://developer.paypal.com/docs/integration/direct/rest-payments-overview/" target=_blank>here</a> and find the API reference <a href="https://developer.paypal.com/docs/api/" target="_blank">here</a>.<br/><br/>

        </div>
    </div>

    <!-- PayPal In-Context Checkout script -->
    <script type="text/javascript">
     window.paypalCheckoutReady = function () {
         paypal.checkout.setup('<?php echo(MERCHANT_ID); ?>', {
             container: 'myContainer', //{String|HTMLElement|Array} where you want the PayPal button to reside
             environment: '<?php echo($environment); ?>' //or 'production' depending on your environment
         });
     };
     </script>
     <script src="//www.paypalobjects.com/api/checkout.js" async></script>
<?php
     include('footer.php');
?>