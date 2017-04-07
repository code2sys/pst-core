<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkout_M extends Master_M
{
  	function __construct()
	{
		parent::__construct();
	}

	public function calculateOrder($post = NULL)
	{
		$stateId = $post['state'][1];
		$countryId = $post['country'][1];
		$zip = $post['zip'][1];
		// Calculate Total
		if(@$_SESSION['cart'])
		{ 
			$productTotal = $this->calculateTotal();
			
			if(@$_SESSION['userRecord']['no_tax'] == 0)
			{ 
				$shippingValue =$this->calculateTax($stateId, $productTotal);
			}
			$groundShippingValue = FALSE;
			if(@$_SESSION['userRecord']['full_price_shipping'] == 0)
			{
				$groundShippingValue = $this->shippingRules($productTotal, $countryId, $zip);
			}
			$shippingValue = $this->calculateParcel($zip, $countryId, $groundShippingValue);
			
			unset($_SESSION['cart']['weight']);
	        $product = array('finalPrice' => $shippingValue, 
								'display_name' => 'Shipping');
			$_SESSION['cart']['shipping'] = $product;
		}
	}
	
	public function calculateTotal()
	{
		$productTotal = 0;
		$weight = 0;
		unset($_SESSION['cart']['tax']);
		unset($_SESSION['cart']['shipping']);
		unset($_SESSION['postalOptions']);
		foreach($_SESSION['cart'] as $key => $product) 	
		{
			if(@$product['qty'] > 0)  
			{
			  $productTotal += (str_replace( ',', '', @$product['finalPrice'] ));
			  $_SESSION['cart'][$key]['weight'] = $this->getProductWeight($key);
			  $weight += $_SESSION['cart'][$key]['weight'] * $_SESSION['cart'][$key]['qty'];
			}
			else
			  unset($_SESSION['cart'][$key]);
		}
		$_SESSION['cart']['weight'] = $weight;
		$_SESSION['cart']['transAmount'] = $productTotal;
		return $productTotal;
	}
	
	public function calculateTax($stateId, $productTotal)
	{
		$taxesArr = $this->account_m->getTaxes();
		if($taxesArr[$stateId]['percentage'])
		{
			$tax = array('finalPrice' => (($productTotal * $taxesArr[$stateId]['tax_value']) / 100), 
								  'price' => (($productTotal * $taxesArr[$stateId]['tax_value']) / 100),
			                      'display_name' => 'Sales Tax');
			$_SESSION['cart']['tax'] = $tax;
		}
		else
		{
			$tax = array('finalPrice' => ($productTotal + $taxesArr[$stateId]['tax_value']), 
								  'price' => ($productTotal + $taxesArr[$stateId]['tax_value']), 
			                      'display_name' => 'Sales Tax');
			$_SESSION['cart']['tax'] = $tax;
		}
		return $tax;
	}
	
	public function shippingRules($productTotal, $countryId, $zip)
	{
		$where = array('active' => 1);
		$shippingValue = 'unprocessed';
		$shippingRules = $this->selectRecords('shipping_rules', $where);
		if(@$shippingRules)
		{
			foreach($shippingRules as $rule)
			{
				$shippingValue = '';
				if(is_numeric($rule['weight_low']))
				{
					if($_SESSION['cart']['weight'] < $rule['weight_low'])
						$shippingValue = $rule['value'];
					else
						$shippingValue = FALSE;
				}
				if(($shippingValue !== FALSE) && is_numeric($rule['weight_high']))
				{
					if($_SESSION['cart']['weight'] > $rule['weight_high'])
						$shippingValue = $rule['value'];
					else
						$shippingValue = FALSE;
				}
				
				if(($shippingValue !== FALSE) && is_numeric($rule['price_low']))
				{
					if($productTotal > $rule['price_low'])
						$shippingValue = $rule['value'];
					else
						$shippingValue = FALSE;
				}
				if(($shippingValue !== FALSE) && is_numeric($rule['price_high']))
				{
					if($productTotal < $rule['price_high'])
						$shippingValue = $rule['value'];
					else
						$shippingValue = FALSE;
				}
				if(($shippingValue !== FALSE) && ($rule['country']))
				{
					$country = array('USA' => 'US', 'Canada' => 'CA');
					if($rule['country'] == $country[$countryId])
						$shippingValue = $rule['value'];
					else
						$shippingValue = FALSE;
				}
				if(is_numeric($shippingValue))
					return $shippingValue;
					
			}
		}
		return FALSE;
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
	
	public function calculateParcel($zip, $country, $gndValue = FALSE)
	{
		$furthestZip = $this->getZip($zip);
		$postal = array();
		$where = array('active' => 1);
		$this->db->order_by('order ASC');
		$shipment_types = $this->selectRecords('shipping_type', $where);
		 $this->load->library('UpsShippingQuote');
		$objUpsRate = new UpsShippingQuote();
		$objUpsRate->setShipperZip($furthestZip);
		$strDestinationZip = $zip;
		$strPackageLength = '8';
		$strPackageWidth = '8';
		$strPackageHeight = '8';
		$strPackageWeight = @$_SESSION['cart']['weight'];
		if($country == 'Canada')
			$strPackageCountry = 'CA';
		if($country == 'USA')
			$strPackageCountry = 'US';
		$boolReturnPriceOnly = true;
		if($strPackageWeight == 0)
			$strPackageWeight = 1;
		  if($shipment_types)
		  {
			  foreach($shipment_types as $type)
			  {
			  	
					$postal['UPS'][$type['code']] = $objUpsRate->GetShippingRate(
					$strDestinationZip, 
					$type['code'], 
					$strPackageLength, 
					$strPackageWidth,
					$strPackageHeight, 
					$strPackageWeight, 
					$boolReturnPriceOnly,
					$strPackageCountry
					);
					if(@$postal['UPS'][$type['code']] )
					{
						$postal['UPS'][$type['code']] = $this->objectsIntoArray($postal['UPS'][$type['code']]);
						
						$postal['UPS'][$type['code']]['RatedShipment']['TotalCharges']['MonetaryValue'] = $postal['UPS'][$type['code']]['RatedShipment']['TotalCharges']['MonetaryValue'];
						if(($type['code'] == 'GND') && ($gndValue !== FALSE))
							$postal['UPS'][$type['code']]['RatedShipment']['TotalCharges']['MonetaryValue'] = $gndValue;
					}
					else
					{
						unset($postal['UPS'][$type['code']] );
						if($gndValue === FALSE)
							$gndValue = 8;
						$postal['UPS']['GND']['RatedShipment']['TotalCharges']['MonetaryValue'] = $gndValue;
					}
			  }
		  }
		  $_SESSION['postalOptions'] = $postal;

		return 'shipping_options';
	}
	
	public function subdividePostalOptions($postalOptions)
	{
		$ddArray = array();
		$where = array('active' => 1);
		$this->db->group_by('carrier');
		$carriers = $this->selectRecords('shipping_type', $where);
		if($carriers)
		{
			foreach($carriers as $carrier)
			{
				if(@$postalOptions[$carrier['carrier']])
				{
					$where = array('active' => 1, 'carrier' => $carrier['carrier']);
					$shipment_types = $this->selectRecords('shipping_type', $where);
					foreach($shipment_types as $type)
						$newType[$type['code']] = $type;
					$segments = explode(',', $carrier['xml_structure']);
					
							
					
					foreach(@$postalOptions[$carrier['carrier']] as $code => $opt)
					{
							
						$value = $opt;
						
						foreach($segments as $seg)
							$value = $value[$seg];
							
							
							
						$valueArr = array('label' => $carrier['carrier'] . ' '. $newType[$code]['description'] . ': $'.number_format($value, 2),
														'value' => $value);
						
						$ddArray[$code] = $valueArr;
						$_SESSION['postalOptions'][$code] = $valueArr;
					}
				}
			}
		}
		if(@$_SESSION['postalOptions']['COUPON'])
			$ddArray['COUPON'] = $_SESSION['postalOptions']['COUPON'];
		return $ddArray;
	}  
	
	private function getProductWeight($id)
	{
		$where = array('partnumber' => $id);
		$record = $this->selectRecord('partnumber', $where);
		return $record['weight'];		
	}

	public function getZip($zip1)
	{
		$where = array('zip' => $zip1);
		$point1 = $this->selectRecord('zip_locations', $where);
		$point2Arr = array(83716, 93706, 38118, 17022, 32219, 60490, 18434, 76177, 93291, 80011, 97024);
	    $radius      = 3958;      // Earth's radius (miles)
	    $deg_per_rad = 57.29578;  // Number of degrees/radian (for conversion)
	    $longestDistance = 0;
		foreach($point2Arr as $zip2)
		{
			$where = array('zip' => $zip2);
			$point2 = $this->selectRecord('zip_locations', $where);
		    $distance = ($radius * pi() * sqrt(
		                ($point1['lat'] - $point2['lat'])
		                * ($point1['lat'] - $point2['lat'])
		                + cos($point1['lat'] / $deg_per_rad)  // Convert these to
		                * cos($point2['lat'] / $deg_per_rad)  // radians for cos()
		                * ($point1['long'] - $point2['long'])
		                * ($point1['long'] - $point2['long'])
		        ) / 180);
			if($distance > $longestDistance)
			{
				$longestDistance = $distance;
				$returnZip = $zip2;
			}
	    }
		return $zip2;
	}
	
	public function calculatePrice($viewShipping)
	{
		$total = 0;
		$qty = 0;
		if(@$_SESSION['cart']) 
		{
			$i = 0;
			foreach(@$_SESSION['cart'] as $key => $product)
			{   
				if(@$_SESSION['cart'][$key]['price'])
				{
					$_SESSION['cart'][$key]['price'] = str_replace('$', '', $_SESSION['cart'][$key]['price']);
					$total += (float)$_SESSION['cart'][$key]['price'];
										
					if(@$product['qty'] && (is_numeric(@$_SESSION['cart'][$key]['part_id'])))
					{
						if($key !=='transAmount')
							$qty += @$product['qty'];
						@$_SESSION['cart'][$key]['finalPrice'] = number_format((@$_SESSION['cart'][$key]['price'] * @$product['qty']), 2, '.', ',');
					}
					elseif((is_numeric(@$_SESSION['cart'][$key]['price'])))
						@$_SESSION['cart'][$key]['finalPrice'] = number_format((@$_SESSION['cart'][$key]['price']), 2, '.', ',');
				}
			
			}
			$_SESSION['cart']['qty'] = $qty;
		}
		return $total;
	}
	
	public function calculatePriceNew($itemsForPercentAge, $viewShipping)
	{
		$total = 0;
		$qty = 0;
		
		if(@$_SESSION['cart']) 
		{
			$i = 0;
			foreach(@$_SESSION['cart'] as $key => $product)
			{   
				if(@$_SESSION['cart'][$key]['price'] && !empty($itemsForPercentAge[$key]))
				{
					@$_SESSION['cart'][$key]['price'] = str_replace('$', '', @$_SESSION['cart'][$key]['price']);
					$price = (float)@$_SESSION['cart'][$key]['price'];
					if( !empty($product['qty']) )
					$price = $price * $product['qty'];
					
					$total += $price;
										
					if(@$product['qty'] && (is_numeric(@$_SESSION['cart'][$key]['part_id'])))
					{
						if($key !=='transAmount')
							$qty += @$product['qty'];
						@$_SESSION['cart'][$key]['finalPrice'] = number_format((@$_SESSION['cart'][$key]['price'] * @$product['qty']), 2, '.', ',');
					}
					elseif((is_numeric(@$_SESSION['cart'][$key]['price'])))
						@$_SESSION['cart'][$key]['finalPrice'] = number_format((@$_SESSION['cart'][$key]['price']), 2, '.', ',');
				}
			
			}
			@$_SESSION['cart']['qty'] = $qty;
		}
		
		return $total;
	}

	function insert_it($post_data, $table){
	   $this->db->insert($table, $post_data);
	   $insert_id = $this->db->insert_id();
	   return  $insert_id;
	}

	function update_it($post_data, $table, $whereFld, $id){
		
		$this->db->where($whereFld, $id);
		$this->db->update($table, $post_data); 
	}
	
	function debug($p){
		echo "<pre>";
		print_r($p);
		echo "</pre>";
	}
	
}