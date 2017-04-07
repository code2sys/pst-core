<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * fwpaypaladv.php
 *
 * This file contains the implementation of the FWPayPalAdv Class.  The 
 * FWPayPalAdv class facilitates credit card transaction processing using the
 * PayPal Advanced Service Provider.
 *
 * DEPENDENCIES
 *
 * NOTES
 *
 * The following fields can be populated (in $request) when submitting
 * a transaction (i.e. Generating a Token Response):
 *

		  "TRXTYPE" => "S",
		  "AMT" => "1.00",
		  "CURRENCY" => "USD",

		  "RETURNURL" => script_url(),
		  "CANCELURL" => script_url(),
		  "ERRORURL" => script_url(),

		  "BILLTOFIRSTNAME" => "John",
		  "BILLTOLASTNAME" => "Smith",
		  "BILLTOSTREET" => "123 Main St.",
		  "BILLTOCITY" => "San Jose",
		  "BILLTOSTATE" => "CA",
		  "BILLTOZIP" => "95101",
		  "BILLTOCOUNTRY" => "US",
		
		  "SHIPTOFIRSTNAME" => "John",
		  "SHIPTOLASTNAME" => "Smith",
		  "SHIPTOSTREET" => "1234 Park Ave",
		  "SHIPTOCITY" => "San Jose",
		  "SHIPTOSTATE" => "CA",
		  "SHIPTOZIP" => "95101",
		  "SHIPTOCOUNTRY" => "US",

 * TRANSACTION INFORMATION
 * =======================
 * AMT 										string				 	Amount of Transaction Ex. "1.00"
 * CURRENCY								string					Ex. "USD" 
 *
 * CROSS-REFERENCE INFORMATION (For App-Specific Info)
 * ===================================================
 * NOTE: The fields below are to be used to store application-specific reference IDs and
 *       string values.  These fields are indexed for fast search and retrieval.  You should
 *       use these to intelligently tie your application-specific tables to the 
 *       payment transactions.  There are 5 numeric (unsigned) IDs and 2 varchar ids.
 *			 These fields are stored in the fw_cc_ref table.
 * refId1									int(11)					App-Specific Reference (unsigned default NULL)
 * refId1Desc							varchar(30)			Internal Notes (ex. "ref. vendorId")
 * refId2									int(11)					App-Specific Reference (unsigned default NULL)
 * refId2Desc							varchar(30)			Internal Notes (ex. "batch->batchId")
 * refId3									int(11)					App-Specific Reference (unsigned default NULL)
 * refId3Desc							varchar(30)			Internal Notes (ex. "ref. eventId")
 * refId4									int(11)					App-Specific Reference (unsigned default NULL)
 * refId4Desc							varchar(30)			Internal Notes (ex. "ref. eventId")
 * refId5									int(11)					App-Specific Reference (unsigned default NULL)
 * refId5Desc							varchar(30)			Internal Notes (ex. "ref. eventId")
 * refVal1								varchar(25)			App-Specific String Reference (default NULL)
 * refVal1Desc						varchar(30)			Internal Notes (ex. "Site Code")
 * refVal2								varchar(25)			App-Specific String Reference (default NULL)
 * refVal2Desc						varchar(30)			Internal Notes (ex. "Server Code")
 *
 *
 * BILLING INFORMATION
 * ===================
 * NOTE: The fields below accommodate the billing (bill-to) information about a transaction.
 *       Most commonly, this collection of information should be populated with credit card
 *       information.  The credit card number IS NOT stored in the database in clear text. 
 *			 These fields are stored in the fw_cc_bill table.
 *
 * BILLTOFIRSTNAME				varchar					Ex. "John"		
 * BILLTOLASTNAME					varchar 				Ex. "Smith"
 * BILLTOSTREET						varchar 				Ex. "123 Main St."
 * BILLTOCITY							varchar 				Ex. "San Jose"
 * BILLTOSTATE						varchar 				Ex. "CA"
 * BILLTOZIP							varchar 				Ex. "95101"
 * BILLTOCOUNTRY					varchar 				Ex. "US"
 *
 * SHIPPING INFORMATION
 * ====================
 * NOTE: The fields below accommodate the shipping (ship-to) information about a transaction.
 *			 These fields are stored in the fw_cc_ship table.
 *
 * SHIPTOFIRSTNAME				varchar 				Ex. "John"
 * SHIPTOLASTNAME					varchar 				Ex. "Smith"
 * SHIPTOSTREET						varchar 				Ex. "1234 Park Ave"
 * SHIPTOCITY							varchar 				Ex. "San Jose"
 * SHIPTOSTATE						varchar					Ex. "CA"
 * SHIPTOZIP							varchar 				Ex. "95101"
 * SHIPTOCOUNTRY					varchar 				Ex. "US"
 * 
 *
 *
 * REVISION HISTORY
 *
 * @package			WMG Website Development Framework
 * @subpackage	Models
 * @category		Credit Card Processing
 * @author			Jeffrey Necciai <jrn@waywardmediagroup.com>
 * @copyright		2010-2013 Wayward Media Group, LLC.
 * @link				http://www.waywardmediagroup.com



 */

/**
 * FWPayPalAdv Class
 *
 * This class facilitates PayPal payment transactions.
 *
 */

class PayPal {

	private $CI = NULL;
	private $returnURL = NULL;
	private $cancelURL = NULL;
	private $errorURL = NULL;

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct()
  {
    $this->CI =& get_instance();
		// Load authorizenet configuration file
		$GLOBALS['environment'] = $this->CI->config->item('environment');
		// Load PayPal SDK		
		require_once(LIBPATH . 'payflownvpapi.php');
  }

  public function getMode()
  {
	  $mode = 'TEST';
	  if($GLOBALS['environment'] == "sandbox" || $GLOBALS['environment'] == "pilot") $mode='TEST'; else $mode='LIVE';
	  return $mode;
  }

  public function setReturnURL($url)
  {
	  $this->returnURL = $url;
  }

  public function setCancelURL($url)
  {
	  $this->cancelURL = $url;
  }

  public function setErrorURL($url)
  {
	  $this->errorURL = $url;
  }

  public function generateTokenResponse($request)
  {
	  $response = NULL;
	  // Add some constant (config) data to the request
	  $request['TRXTYPE'] = "S";
	  $request['PARTNER'] = $this->CI->config->item('PARTNER');
	  $request['VENDOR'] = $this->CI->config->item('VENDOR');
	  $request['USER'] = $this->CI->config->item('USER');
	  $request['PWD'] = $this->CI->config->item('PWD');
	  $request['CREATESECURETOKEN'] = "Y";
	  $request['SECURETOKENID'] = uniqid('MySecTokenID-');
	  $request['RETURNURL'] = $this->returnURL;
	  $request['CANCELURL'] = $this->cancelURL;
	  $request['ERRORURL'] = $this->errorURL;
		//Run request and get the secure token response
		$response = run_payflow_call($request);
		return $response;
  }
  
  public function processPaymentResponse()
  {
	  $ret = FALSE;
		if (isset($_POST['RESULT']) || isset($_GET['RESULT']) ) 
		{
			$this->CI->session->set_userdata('payflowresponse', array_merge($_GET, $_POST));
			$ret = TRUE;
		}
		return $ret;
  }

  public function getPaymentResponse()
  {
	  $response = $this->CI->session->userdata('payflowresponse');
	  $this->CI->session->unset_userdata('payflowresponse');
	  return $response; 
  }
  
  public function paymentResponseReturned()
  {
	  $ret = FALSE;
	  if (isset($_POST['RESULT']) || isset($_GET['RESULT']) )
	  	$ret = TRUE;
	  return $ret;
  }

}

/* End of file fwpaypaladv.php */
/* Location: ./Application/models/fwpaypaladv.php */
