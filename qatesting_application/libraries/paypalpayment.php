<?php

 
class PayPalPayment
{

  function __construct()
  {
    $this->CI =& get_instance();
		// Load authorizenet configuration file
		$GLOBALS['environment'] = $this->CI->config->item('environment');
		// Load PayPal SDK		
  }

	public function PPHttpPost($methodName_, $nvpStr_) {
		$environment = 'sandbox';	// or 'beta-sandbox' or 'live'
		//$environment = 'live';	// or 'beta-sandbox' or 'live'
	
		// Set up your API credentials, PayPal end point, and API version.
		//SANDBOX
		$API_UserName = urlencode('bre************.gmail.com');
		$API_Password = urlencode('GNK7********CP8V');
		$API_Signature = urlencode('Aj6RlFED6********************labe3**********BMM8dGcURwLn');
		
		
		$request['PARTNER'] = $this->CI->config->item('PARTNER');
	  $request['VENDOR'] = $this->CI->config->item('VENDOR');
	  $API_UserName = $this->CI->config->item('USER');
	  $API_Password = $this->CI->config->item('PWD');

        
		$API_Endpoint = "https://api-3t.paypal.com/nvp";
        //$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
        
        
		$version = urlencode('51.0');
        
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
	
		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
	
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	
		// Get response from the server.
		$httpResponse = curl_exec($ch);
	
		if(!$httpResponse) {
			exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}
	
		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);
	
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
	
		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
	
		return $httpParsedResponseAr;
	}

    
    //Get the featured items to display in the carousel
    public function processPayment($ccfname,$cclname,$ccaddr1,$ccaddr2,$cccity,$ccstate,$cczip,$price,$ccnumber,$cctype,$ccexpmo,$ccexpyr,$cccvc)
    {
		try {
			// Set request-specific fields.
			$paymentType = urlencode('Sale');		// or 'Sale'
			$firstName = urlencode($ccfname);
			$lastName = urlencode($cclname);
			$creditCardType = urlencode($cctype);
			$creditCardNumber = urlencode($ccnumber);
			$expDateMonth = $ccexpmo;
			// Month must be padded with leading zero
			$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
			$expDateYear = '20'.urlencode($ccexpyr);
			$cvv2Number = urlencode($cccvc);
			$address1 = urlencode($ccaddr1);
			$address2 = urlencode($ccaddr2);
			$city = urlencode($cccity);
			$state = urlencode($ccstate);
			$zip = urlencode($cczip);
			$country = urlencode('US');				// US or other valid country code
			$amount = urlencode($price);
			$currencyID = urlencode('USD');			// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
			
			// Add request-specific fields to the request string.
			$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
						"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
						"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";
			
			// Execute the API operation; see the PPHttpPost function above.
			$httpParsedResponseAr = self::PPHttpPost('DoDirectPayment', $nvpStr);
			
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
				//print_r('Direct Payment Completed Successfully: '.print_r($httpParsedResponseAr, true));
				return true;
			} else  {
				//print_r('DoDirectPayment failed: ' . print_r($httpParsedResponseAr, true));
				return $httpParsedResponseAr['L_LONGMESSAGE0'];
			}
		
		
		
		} catch (Exception $e) {
			$fp = @fopen("error.log","a");
			@fwrite($fp,'Class exception - '.$e.' - '.$report.PHP_EOL);
			@fclose($fp);
			return $e;
		}
		exit;
    }



}

?>
