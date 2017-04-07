<?php
require_once(APPPATH . 'controllers/Master_Controller.php');

/**
 * Example CodeIgniter QuickBooks Web Connector integration
 * 
 * This file servers as a controller which servers up .QWC configuration files, 
 * also also acts as the Web Connector SOAP endpoint. Download your .QWC file 
 * by visiting:
 * 	http://path/to/ci/quickbooks/config
 * 
 * The Web Connector will get pointed to this endpoint:
 * 	http://path/to/ci/quickbooks/qbwc
 * 
 * This particular example adds dummy customers to QuickBooks, but you could 
 * easily extend it to perform other operations on QuickBooks too. The final 
 * piece of this is just throwing things into the queue to be processed - for 
 * an example of that, see: 
 * 	docs/example_web_connector_queueing.php
 * 
 * @author Keith Palmer <keith@consolibyte.com>
 * 
 * @package QuickBooks
 * @subpackage Documentation
 */

 /*
 * Example CodeIgniter controller for QuickBooks Web Connector integrations
 */
class WebConnector extends Master_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		
		// Require the framework
		$this->load->helper('quickbooks');
		// QuickBooks config
		$this->load->config('quickbooks');
		
		
		// Load your other models here... 
		
		//$this->load->model('yourmodel2');
		//$this->load->model('yourmodel3');
	}
	
	/**
	 * Generate and return a .QWC Web Connector configuration file
	 */
	public function config()
	{
		$name = 'Butterfly Express QuickBooks';			// A name for your server (make it whatever you want)
		$descrip = 'Butterfly Express QuickBooks';		// A description of your server 

		$appurl = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/qbwc';		// This *must* be httpS:// (path to your QuickBooks SOAP server)
		$appsupport = $appurl; 		// This *must* be httpS:// and the domain name must match the domain name above

		$username = $this->config->item('quickbooks_user');		// This is the username you stored in the 'quickbooks_user' table by using QuickBooks_Utilities::createUser()

		$fileid = QuickBooks_WebConnector_QWC::fileID();		// Just make this up, but make sure it keeps that format
		$ownerid = QuickBooks_WebConnector_QWC::ownerID();		// Just make this up, but make sure it keeps that format

		$qbtype = QUICKBOOKS_TYPE_QBFS;	// You can leave this as-is unless you're using QuickBooks POS

		$readonly = false; // No, we want to write data to QuickBooks

		$run_every_n_seconds = 600; // Run every 600 seconds (10 minutes)

		// Generate the XML file
		$QWC = new QuickBooks_WebConnector_QWC($name, $descrip, $appurl, $appsupport, $username, $fileid, $ownerid, $qbtype, $readonly, $run_every_n_seconds);
		$xml = $QWC->generate();

		// Send as a file download
		header('Content-type: text/xml');
		//header('Content-Disposition: attachment; filename="my-quickbooks-wc-file.qwc"');
		print($xml);
		exit;

	}
	
	/**
	 * SOAP endpoint for the Web Connector to connect to
	 */
	public function qbwc()
	{
		$user = $this->config->item('quickbooks_user');
		$pass = $this->config->item('quickbooks_pass');
		
		// Memory limit
		ini_set('memory_limit', $this->config->item('quickbooks_memorylimit'));
		
		// We need to make sure the correct timezone is set, or some PHP installations will complain
		if (function_exists('date_default_timezone_set'))
		{
			// * MAKE SURE YOU SET THIS TO THE CORRECT TIMEZONE! *
			// List of valid timezones is here: http://us3.php.net/manual/en/timezones.php
			date_default_timezone_set($this->config->item('quickbooks_tz'));
		}
				
		// Map QuickBooks actions to handler functions
		$map = array(
			QUICKBOOKS_QUERY_ITEM => array( array( $this, '_queryItem' ), array( $this, '_queryItemResponse' ) ),
			QUICKBOOKS_QUERY_SALESORDER => array( array( $this, '_querySalesOrder' ), array( $this, '_querySalesOrderResponse' ) ),
			QUICKBOOKS_QUERY_PRICELEVEL => array( array( $this, '_queryPriceLevel' ), array( $this, '_queryPriceLevelResponse' ) ),
			QUICKBOOKS_QUERY_CUSTOMER => array( array( $this, '_queryCustomer' ), array( $this, '_queryCustomerResponse' ) ),
			QUICKBOOKS_ADD_SALESORDER => array( array( $this, '_addSalesOrder' ), array( $this, '_addSalesOrderResponse' ) ),
			);
		
		// Catch all errors that QuickBooks throws with this function 
		$errmap = array(
			'*' => array( $this, '_catchallErrors' ),
			);
		
		// Call this method whenever the Web Connector connects
		$hooks = array(
			//QuickBooks_WebConnector_Handlers::HOOK_LOGINSUCCESS => array( array( $this, '_loginSuccess' ) ), 	// Run this function whenever a successful login occurs
			);
		
		// An array of callback options
		$callback_options = array();
		
		// Logging level
		$log_level = $this->config->item('quickbooks_loglevel');
		
		// What SOAP server you're using 
		//$soapserver = QUICKBOOKS_SOAPSERVER_PHP;			// The PHP SOAP extension, see: www.php.net/soap
		$soapserver = QUICKBOOKS_SOAPSERVER_BUILTIN;		// A pure-PHP SOAP server (no PHP ext/soap extension required, also makes debugging easier)
		
		$soap_options = array(		// See http://www.php.net/soap
			);
		
		$handler_options = array(
			'deny_concurrent_logins' => false, 
			);		// See the comments in the QuickBooks/Server/Handlers.php file
		
		$driver_options = array(		// See the comments in the QuickBooks/Driver/<YOUR DRIVER HERE>.php file ( i.e. 'Mysql.php', etc. )
			'max_log_history' => 32000,	// Limit the number of quickbooks_log entries to 1024
			'max_queue_history' => 1024, 	// Limit the number of *successfully processed* quickbooks_queue entries to 64
			);
		
		// Build the database connection string
		$dsn = 'mysql://' . $this->db->username . ':' . $this->db->password . '@' . $this->db->hostname . '/' . $this->db->database;
		
		// Check to make sure our database is set up 
		if (!QuickBooks_Utilities::initialized($dsn))
		{
			// Initialize creates the neccessary database schema for queueing up requests and logging
			QuickBooks_Utilities::initialize($dsn);
			
			// This creates a username and password which is used by the Web Connector to authenticate
			QuickBooks_Utilities::createUser($dsn, $user, $pass);
		}
		
		// Set up our queue singleton
		QuickBooks_WebConnector_Queue_Singleton::initialize($dsn);
		
		// Create a new server and tell it to handle the requests
		// __construct($dsn_or_conn, $map, $errmap = array(), $hooks = array(), $log_level = QUICKBOOKS_LOG_NORMAL, $soap = QUICKBOOKS_SOAPSERVER_PHP, $wsdl = QUICKBOOKS_WSDL, $soap_options = array(), $handler_options = array(), $driver_options = array(), $callback_options = array()
		$Server = new QuickBooks_WebConnector_Server($dsn, $map, $errmap, $hooks, $log_level, $soapserver, QUICKBOOKS_WSDL, $soap_options, $handler_options, $driver_options, $callback_options);
		$response = $Server->handle(true, true);
	}
	
	public function _queryCustomer($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{
		$xml = '
		<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="7.0"?>
			<QBXML>
			  <QBXMLMsgsRq>
				<CustomerQueryRq requestID="' . $requestID . '">
          <OwnerID>0</OwnerID>
        </CustomerQueryRq>  
			</QBXML>';
		return $xml;
	}
	
	public function _queryPriceLevel($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{
		$xml = '
		<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="7.0"?>
			<QBXML>
			  <QBXMLMsgsRq>
				<PriceLevelQueryRq requestID="' . $requestID . '" >
				</PriceLevelQueryRq>
			  </QBXMLMsgsRq>
			</QBXML>';
		return $xml;
	}
	
	public function _addSalesOrder($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{	
		$xml = '<?xml version="1.0"?><?qbxml version="4.0"?>
            <QBXML>
                <QBXMLMsgsRq onError="stopOnError">
                    <SalesOrderAddRq requestID="' . $requestID . '">
                        <SalesOrderAdd>
                            <CustomerRef>
                              <ListID>1690000-1113673539</ListID>
                            </CustomerRef>
                            <RefNumber>5</RefNumber>
                            <BillAddress>
                              <Addr1>Tony and Michelle Westover</Addr1>
                              <Addr2>1738 S Hwy 36</Addr2>
                              <City>Weston</City>
                              <State>ID</State>
                              <PostalCode>83286</PostalCode>
                            </BillAddress>
                            <ShipAddress>
                              <Addr1>Tony and Michelle Westover</Addr1>
                              <Addr2>1738 S Hwy 36</Addr2>
                              <City>Weston</City>
                              <State>ID</State>
                              <PostalCode>83286</PostalCode>
                            </ShipAddress>
                            <ItemSalesTaxRef>
                                <ListID>20000-1081807687</ListID>
                            </ItemSalesTaxRef>
                            <Memo>TESTING ORDER ONLY!!!!!</Memo>
                            <SalesOrderLineAdd>
                                <ItemRef>
                                  <ListID>80001169-1384979401</ListID>
                                </ItemRef>
                                <Desc>2 ounce Acknowledge</Desc>
                                <Quantity>1</Quantity>
                                <Amount>109.50</Amount>
                            </SalesOrderLineAdd>
                        </SalesOrderAdd>
                    </SalesOrderAddRq>
                </QBXMLMsgsRq>
            </QBXML>'; 
            
		return $xml;
	}
	
	public function _addCustomer()
	{
  	$xml = '<?xml version="1.0" encoding="utf-8"?><?qbxml version="2.0"?>
        		<QBXML>
        			<QBXMLMsgsRq onError="stopOnError">
        				<CustomerAddRq requestID="' . $requestID . '">
        					<CustomerAdd>
        						<Name>DUMMIE</Name>
        						<CompanyName>DUMMIE, LLC</CompanyName>
        						<FirstName>Keith</FirstName>
        						<LastName>Palmer</LastName>
        						<BillAddress>
        							<Addr1>ConsoliBYTE, LLC</Addr1>
        							<Addr2>134 Stonemill Road</Addr2>
        							<City>Mansfield</City>
        							<State>CT</State>
        							<PostalCode>06268</PostalCode>
        							<Country>United States</Country>
        						</BillAddress>
        						<Phone>860-634-1602</Phone>
        						<AltPhone>860-429-0021</AltPhone>
        						<Fax>860-429-5183</Fax>
        						<Email>Keith@ConsoliBYTE.com</Email>
        						<Contact>Keith Palmer</Contact>
        					</CustomerAdd>
        				</CustomerAddRq>
        			</QBXMLMsgsRq>
        		</QBXML>';
	}
	
	public function _querySalesOrder($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{
		$xml = '
		<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="4.0"?>
			<QBXML>
			  <QBXMLMsgsRq>
				<SalesOrderQueryRq requestID="' . $requestID . '" >
				</SalesOrderQueryRq>
			  </QBXMLMsgsRq>
			</QBXML>';
  		
		return $xml;
	}
	
	public function _queryItem($requestID, $user, $action, $id, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{
	  if(is_numeric($id))
	  {
  	  $this->load->model('quickbooks_model');
  		$listId = $this->quickbooks_model->getItemListIdById($id);
      $xml = '<?xml version="1.0" encoding="utf-8"?><?qbxml version="7.0"?>
          			<QBXML>
          			  <QBXMLMsgsRq onError="stopOnError">
          				<ItemQueryRq requestID="' . $requestID . '" >
          				<ListID>'.$listId.'</ListID>
                  <OwnerID>0</OwnerID>
          				</ItemQueryRq>
          			  </QBXMLMsgsRq>
          			</QBXML>';
    }
    else
    {
      $xml = '<?xml version="1.0" encoding="utf-8"?><?qbxml version="7.0"?>
          			<QBXML>
          			  <QBXMLMsgsRq onError="stopOnError">
          				<ItemQueryRq requestID="' . $requestID . '" >
          				<NameFilter>
                    <MatchCriterion>Contains</MatchCriterion>
                    <Name>'.$id.'</Name>
                  </NameFilter> 
                  
                  <IncludeRetElement>ListID</IncludeRetElement>
                  <IncludeRetElement>Name</IncludeRetElement>
                  <IncludeRetElement>FullName</IncludeRetElement>
                  <IncludeRetElement>DataExtRet</IncludeRetElement>
                  <OwnerID>0</OwnerID>
                  
          				</ItemQueryRq>
          			  </QBXMLMsgsRq>
          			</QBXML>';
    }
		return $xml;
	}

  public function _queryCustomerResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		//$this->load->model('quickbooks_model');
		//$this->quickbooks_model->querySalesOrder($xml);
		return true; 
	}
	
  public function _queryPriceLevelResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		//$this->load->model('quickbooks_model');
		//$this->quickbooks_model->querySalesOrder($xml);
		return true; 
	}
	
  public function _addSalesOrderResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		//$this->load->model('quickbooks_model');
		//$this->quickbooks_model->querySalesOrder($xml);
		return true; 
	}
	
  public function _querySalesOrderResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		//$this->load->model('quickbooks_model');
		//$this->quickbooks_model->querySalesOrder($xml);
		return true; 
	}

	public function _queryItemResponse($requestID, $user, $action, $id, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		$this->load->model('quickbooks_model');
		$this->quickbooks_model->queryItemUpdate($id, $xml);
		return true; 
	}
	
	/**
	 * Catch and handle errors from QuickBooks
	 */		
	public function _catchallErrors($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg)
	{
		return false;
	}
	
	/**
	 * Whenever the Web Connector connects, do something (e.g. queue some stuff up if you want to)
	 */
	public function _loginSuccess($requestID, $user, $hook, &$err, $hook_data, $callback_config)
	{
		return true;
	}
}
	