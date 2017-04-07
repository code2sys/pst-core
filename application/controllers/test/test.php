<?php 

class Test extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  
  function index()
  {
    /*
          $this->load->helper('easy_post');

\easypost\EasyPost::setApiKey('05a7O13t6a0RTpqAQezJmA');
     $from_address = \EasyPost\Address::create(array(
                                  	  	  "name"	  => "ButterflyExoress",
                                          "street"  => "500 N Main Hwy  Clifton, ID 83228",
                                          "city"    => "Clifton",
                                          "state"   => "Id",
                                          "zip"     => "83228",
                                          "phone"   => "208-747-3021"
                                        ));
*/
  }
  
 public function phpsettings()
  {
    phpinfo();
  }
  
  public function nxs_cURLTest($url, $msg, $testText){  
  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)"); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  $response = curl_exec($ch); 
  $errmsg = curl_error($ch); 
  $cInfo = curl_getinfo($ch); 
  curl_close($ch); 
  echo "Testing ... ".$url." - ".$cInfo['url']."<br />";
  if (stripos($response, $testText)!==false) 
    echo "....".$msg." - OK<br />"; 
  else 
  { 
    echo "....<b style='color:red;'>".$msg." - Problem</b><br /><pre>"; 
    print_r($errmsg); 
    print_r($cInfo); 
    print_r(htmlentities($response)); 
    echo "</pre>There is a problem with cURL. You need to contact your server admin or hosting provider.";
  }
}

  
  public function testcurl()
  {
	  $this->nxs_cURLTest("http://www.google.com/intl/en/contact/", "HTTP to Google", "Mountain View, CA");
	 $this->nxs_cURLTest("https://www.facebook.com/", "HTTPS to Facebook", 'id="facebook"');
	 $this->nxs_cURLTest("https://www.linkedin.com/", "HTTPS to LinkedIn", 'link rel="canonical" href="https://www.linkedin.com/"');
	 $this->nxs_cURLTest("https://twitter.com/", "HTTPS to Twitter", 'link rel="canonical" href="https://twitter.com/"');
	 
  }
  
  public function ups_curl()
  {
	   $this->load->library('UpsShippingQuote');
		$objUpsRate = new UpsShippingQuote();
		$objUpsRate->setShipperZip(84010);
		$strDestinationZip = 28625;
		$strPackageLength = '8';
		$strPackageWidth = '8';
		$strPackageHeight = '8';
		$strPackageWeight = 1;
		$strPackageCountry = 'US';
		$boolReturnPriceOnly = true;
		$returnObj = $objUpsRate->GetShippingRate(
		$strDestinationZip, 
		'GND', 
		$strPackageLength, 
		$strPackageWidth,
		$strPackageHeight, 
		$strPackageWeight, 
		$boolReturnPriceOnly,
		$strPackageCountry
		);
		
		print_r($returnObj);
  }
  
  	private function objectsIntoArray($arrObjData, $arrSkipIndices = array())
	{
		$arrData = array();
		
		if(is_object($arrObjData))
			$arrObjData = get_object_vars($arrObjData);
			
		if(is_array($arrObjData))
		{
			foreach($arrObjData as $index => $value)
			{
				if(is_object($value) || is_array($value))
					$value = $this->objectsIntoArray($value, $arrSkipIndices);
				
				if(in_array($index, $arrSkipIndices))
					continue;
					
				$arrData[$index] = $value;
			}
		}
		return $arrData;
	}
  
	public function pdf()
	{	
 		// set up PDF Helper files
 		$this->load->helper('fpdf_view');
 		$parameters = array();	
		pdf_init('reporting/poreport.php');
		
		// Send Variables to PDF
		//update process date and process user info
		$parameters['orders'] = array(array('id' => '1', // order info
		                             'order_date' => '11111111',
		                             'contact_id' => 1, // If user then if shipping use shipping contact id.  Else use contact id. 
		                             'sales_price' => '2.50',
		                             'shipping' => '4.95',
		                             'products' => array( array(
                                                      'qty' => '1',
                                                      'description' => 'Product Description goes here',
                                                      'price' => '2.50'
                                                      )),
                                  'contact' => array('id' => '1', // If user then if shipping use shipping contact id.  Else use contact id.
          		                                'first_name' => 'Jessica',
                                              'last_name' => 'Rawlins', 
                                              'street_address' => '3384 South 50 West',
                                              'city' => 'Bountiful',
                                              'state' => 'Utah',
                                              'zip' => '84020' 
		                            )), 
		                            array('id' => '2', // order info
		                             'order_date' => '3333333333',
		                             'contact_id' => 1, // If user then if shipping use shipping contact id.  Else use contact id. 
		                             'sales_price' => '4.50',
		                             'shipping' => '4.95',
		                             'products' => array( array(
                                                      'qty' => '1',
                                                      'description' => 'Product Description goes here',
                                                      'price' => '4.50'
                                                      )),
                                  'contact' => array('id' => '1', // If user then if shipping use shipping contact id.  Else use contact id.
          		                                'first_name' => 'Jed',
                                              'last_name' => 'Rawlins', 
                                              'street_address' => '3384 South 50 West',
                                              'city' => 'Bountiful',
                                              'state' => 'Utah',
                                              'zip' => '84020' 
		                            ))
		                            );


		$fileName = 'OrderReport_'.time().'.pdf';
		
		// Create PDF
		$this->PDF->setParametersArray($parameters);
		$this->PDF->runReport();
		$this->PDF->Output($fileName, 'D');
    }
  }
?>