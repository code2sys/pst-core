<?php

// Require this file if you're not using composer's vendor/autoload

// Required PHP extensions
if (!function_exists('curl_init')) {
  throw new Exception('EasyPost needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('EasyPost needs the JSON PHP extension.');
}

// Config and Utilities
require(LIBPATH . '/EasyPost/EasyPost.php');
require(LIBPATH . '/EasyPost/Util.php');
require(LIBPATH . '/EasyPost/Error.php');

// Guts
require(LIBPATH . '/EasyPost/Object.php');
require(LIBPATH . '/EasyPost/Resource.php');
require(LIBPATH . '/EasyPost/Requestor.php');

// API Resources
require(LIBPATH . '/EasyPost/Address.php');
require(LIBPATH . '/EasyPost/ScanForm.php');
require(LIBPATH . '/EasyPost/CustomsItem.php');
require(LIBPATH . '/EasyPost/CustomsInfo.php');
require(LIBPATH . '/EasyPost/Parcel.php');
require(LIBPATH . '/EasyPost/Rate.php');
require(LIBPATH . '/EasyPost/PostageLabel.php');
require(LIBPATH . '/EasyPost/Shipment.php');
require(LIBPATH . '/EasyPost/Refund.php');
require(LIBPATH . '/EasyPost/Batch.php');
require(LIBPATH . '/EasyPost/Tracker.php');


function calculateShipping($post, $parcel_params)
{
  
   

    \easypost\EasyPost::setApiKey('-nLDGszU2y07dc0NrE41iQ');
    $from_address_params= array(
                          	  	  "name"	  => "ButterflyExoress",
                                  "street"  => "500 N Main Hwy  Clifton, ID 83228",
                                  "city"    => "Clifton",
                                  "state"   => "Id",
                                  "zip"     => "83228",
                                  "phone"   => "208-747-3021"
                                );
    $from_address = \EasyPost\Address::create($from_address_params);
    $to_address_params = array(
                               "zip"     => $post['zip'][1]);
    $to_address = \EasyPost\Address::create($to_address_params);
    
    $parcel = \EasyPost\Parcel::create($parcel_params);


    // create shipment
    $shipment_params = array("from_address" => $from_address,
                             "to_address"   => $to_address,
                             "parcel"       => $parcel,
                             "options" => array('residential_to_address' => '1')
    );
    $shipment = \EasyPost\Shipment::create($shipment_params);
/*
    echo "<pre>";
    print_r($shipment);
    echo "</pre>";
    exit();
    
    
     if(@$this->input->post('predefined') && ($this->input->post('predefined') != 'None'))
        $predefined = $this->input->post('predefined');
      else
        $predefined = NULL;
      $weight = $this->input->post('weight');
      $weight = $weight * 16;
      $parcel_params = array("length"             => 8,
                         "width"              => 8,
                         "height"             => 8,
                         "predefined_package" => $predefined,
                         "weight"             => $weight
                         );
      $this->load->helper('easy_post');
      $post = array('zip' => array(1 => $this->input->post('zip')));
      $this->_mainData['postalOptions'] = calculateShipping($post, $parcel_params);
      
      
      if(@$postalOptions): 
      $j = count($postalOptions);
      for($i = 0; $i < $j; $i++): if(in_array($postalOptions[$i]->service, $rateArray)): ?>
        <?php echo $postalOptions[$i]->carrier; ?> - 
        <?php echo $postalOptions[$i]->service; ?> :
        <?php echo $postalOptions[$i]->rate; ?>
        <br />
      <?php endif; 
      endfor; endif; ?>
    
    
*/
    return $shipment->rates;
  }
