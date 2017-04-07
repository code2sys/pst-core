<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupons_M extends Master_M
{
  function __construct()
	{
		parent::__construct();
	}
	
	public function getCoupons()
	{
  		$records = $this->selectRecords('coupon');
  		return $records;
	}
	
	public function getSpecialConstraints()
	{
  	$records = $this->selectRecords('coupon_special_constraints');
  	return $records;
	}

	public function getSpecialConstraintsDD($rule = FALSE)
	{
	  $records = FALSE;
	  $retArr = array();
  	$records = $this->selectRecords('coupon_special_constraints');
  	if($records)
  	{
    	foreach($records as $rec)
    	{
    	  if($rule)
      	  $retArr[$rec['couponSpecialConstraintsId']] = $rec['ruleName']; 
    	  else
      	  $retArr[$rec['couponSpecialConstraintsId']] = $rec['displayName']; 
    	}
  	}
  	return $retArr;
	}
	
	public function getCouponByCode($code)
	{
		$record = FALSE;
		$where = array('couponCode' => $code, 'startDate <' => time(), 'endDate >' => time(), 'active' => 1);
		$record = $this->selectRecord('coupon', $where);
		if(is_numeric($record['totalUses']) && ($record['totalUses'] <= $record['currentUses']))
			return FALSE;
		
		return $record;
	}
	
	public function getCouponByCodeNew($code, $brand_id, $closeout)
	{
		$record = FALSE;
		$where = array('couponCode' => $code, 'startDate <' => time(), 'endDate >' => time(), 'active' => 1, 'brand_id' => $brand_id, 'closeout' => $closeout);
		$record = $this->selectRecord('coupon', $where);
		if(is_numeric($record['totalUses']) && ($record['totalUses'] <= $record['currentUses']))
			return FALSE;
		
		return $record;
	}
	
	public function getCouponById($id)
	{
		$record = FALSE;
		$where = array('id' => $id);
		$record = $this->selectRecord('coupon', $where);
		return $record;
	}
	
	public function checkExistingOrders($userId)
	{
  	$where = array('user_id' => $userId);
  	$exists = $this->recordExists('order', $where);
  	return $exists;
	}
	
	public function createCoupon($postData)
	{
  	$inputData = array();
  	$inputData['active'] = 1;
  	$inputData['couponCode'] = $postData['couponCode'];
  	if(@$postData['startDate'])
  	  $inputData['startDate'] = strtotime($postData['startDate']);
    if(@$postData['endDate'])
  	  $inputData['endDate'] = strtotime($postData['endDate']);
    if(@$postData['totalUses'])
      $inputData['totalUses'] = $postData['totalUses'];
    if($postData['type'] == 'percentage')
      $inputData['percentage'] = $postData['amount'];
    if($postData['type'] == 'value')
      $inputData['value'] = $postData['amount'];
    if($postData['associatedProductSKU'])
      $inputData['associatedProductSKU'] = $postData['associatedProductSKU'];
	  
    if($postData['google_promotion'])
      $inputData['google_promotion'] = $postData['google_promotion'];
    if($postData['brand_id']){
	  $brand = explode("-_-", $postData['brand_id']);
      $inputData['brand_id'] = $brand[0];
	  $inputData['brand_name'] = $brand[1];
	}
    if($postData['closeout'])
      $inputData['closeout'] = $postData['closeout'];
	  
    $specialconstraints = $this->getSpecialConstraints();
    if($specialconstraints)
    {
      $constraints = array();
      foreach($specialconstraints as $opt)
      {
        if(@$postData[$opt['ruleName']])
          $constraints[] = $postData[$opt['ruleName']];
      }
      if(count($constraints) >= 1)
        $inputData['couponSpecialConstraintsId'] = json_encode($constraints);
    }
    $success = $this->createRecord('coupon', $inputData, FALSE);
    return $success;
	}
	
	public function freeShipping($coupon)
	{
		if(is_null($coupon))
			return $coupon;
		if(@$_SESSION['cart']['shipping']) // Calculate Price
		{
			$_SESSION['cart']['shipping']['finalPrice'] = 0;
			$_SESSION['postalOptions']['COUPON']['value'] = 0;
			$_SESSION['postalOptions']['COUPON']['label'] = 'COUPON: FREE SHIPPING';
		}
		return $coupon;
	}
	
	public function firstOrder($coupon)
	{
	  if(is_null($coupon))
	    return $coupon;
	  if(@$_SESSION['userRecord'])
	  {
	    $couponInvalid = $this->coupons_m->checkExistingOrders($_SESSION['userRecord']['id']);
	    if($couponInvalid)
	      $coupon = NULL;
	  }
	  return $coupon;
	}
	
	public function freeProduct($coupon)
	{
		if(is_null($coupon))
			return $coupon;
		if($coupon['associatedProductSKU'])
		{
			$this->load->model('products_m');
			$product = $this->products_m->getProductBySKU($coupon['associatedProductSKU'], @$_SESSION['userRecord']['wholesaler']);
			$product['qty'] = 1;
			$product['wholesale'] = 0.00;
			$product['finalPrice'] = 0.00;
			$_SESSION['cart'][$coupon['associatedProductSKU'].'_FREE'] = $product;
		}
		return $coupon;
	}
	
	public function processPercentageValue($coupon, $viewShipping, $total = NULL)
	{
		if(is_null($total))
		{
			$this->load->model('checkout_m');
			$total = $this->checkout_m->calculatePrice($viewShipping, FALSE);
		}
		if(@$_SESSION['cart']['shipping']['finalPrice'] > 0)
			$total -= $_SESSION['cart']['shipping']['finalPrice'];
		if($coupon['percentage'])
		{
			if(@$coupon['wholesale'])
				$coupon['wholesale'] = -abs($total * $coupon['percentage'] / 100);
			else
				$coupon['wholesale'] = -abs($total * $coupon['percentage'] / 100);
		}
		elseif($coupon['value'])
		{
			if(@$coupon['wholesale'])
				$coupon['wholesale'] -= abs($coupon['value']);
			else
				$coupon['wholesale'] = -abs($coupon['value']);
		}
		elseif(!@$coupon['wholesale'])
			$coupon['wholesale'] = 0;
		return $coupon;
	}
	
	public function processPercentageValueNew($itemsForPercentAge, $coupon, $viewShipping, $total = NULL)
	{	
	
		if(is_null($total))
		{
			$this->load->model('checkout_m');
			$total = $this->checkout_m->calculatePriceNew($itemsForPercentAge, $viewShipping, FALSE);
		}
		if(@$_SESSION['cart']['shipping']['finalPrice'] > 0)
			$total -= $_SESSION['cart']['shipping']['finalPrice'];
		
		if($coupon['percentage'])
		{
			if(@$coupon['wholesale'])
				$coupon['wholesale'] = -abs($total * $coupon['percentage'] / 100);
			else
				$coupon['wholesale'] = -abs($total * $coupon['percentage'] / 100);
		}
		elseif($coupon['value'])
		{
			if(@$coupon['wholesale'])
				$coupon['wholesale'] -= abs($coupon['value']);
			else
				$coupon['wholesale'] = -abs($coupon['value']);
		}
		elseif(!@$coupon['wholesale'])
			$coupon['wholesale'] = 0;
	
		return $coupon;
	}
	
	public function getBrandByPartId($part_id)
	{
		$this->db->select('brand_id');
		$this->db->where('part_id', $part_id);
		$this->db->from('partbrand');
		$record = $this->db->get();
		return $record;
	}
	
	public function getPartByPartNumber($partnumber)
	{
		$where = array('partnumber' => $partnumber);
		$record = $this->selectRecord('partnumber', $where);
		return $record;
	}
    
    public function checkCloseoutStockCodeByPartId($partId)
    {
	    $is_on_closeout = 0;
		$where = array('part_id' => $partId);
	    $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partvariation.partnumber_id');
	    $this->db->group_by('stock_code');
	    $records = $this->selectRecords('partvariation', $where);
		if( !empty($records) ){
			foreach( $records as $r ){
				if( $r['stock_code']=='Closeout' || count($records) == 2 ){
				    $is_on_closeout = 1;
				}
			}
		}
		
		return $is_on_closeout;
    }
	
	public function addCoupon($post)
	{	
		$cart = @$_SESSION['cart'];
		$coupon = $this->getCouponByCode($post['qty']);
		$itemsForPercentAge = array();
		$brand_to_do = array();
		
		if( !empty($cart) && !empty($coupon)){
			
			unset($cart['transAmount']);unset($cart['tax']);unset($cart['shipping']);unset($cart['qty']);
			foreach($cart as $k=>$cartItem){
			
				if( !empty($coupon['brand_id']) && !empty($cartItem['part_id']) ){

					$brand = $this->getBrandByPartId($cartItem['part_id'])->result_array();
					$closeout = $this->checkCloseoutStockCodeByPartId($cartItem['part_id']);
					$brand_id = ( isset($brand[0]) && !empty($brand[0]['brand_id'])) ? $brand[0]['brand_id'] : 1;
					
					$coupon2 = $this->getCouponByCodeNew($post['qty'], $brand_id, $closeout);

					if( !empty($coupon2) ){
						if( empty($coupon['value']) ){
							$itemsForPercentAge[$k] = $k;
						}
						$brand_to_do['brand_id'] = $brand_id;
						$brand_to_do['closeout'] = $closeout;
					}else{
						unset($cart[$k]);
					}
				}
				
			}
			
			if( !empty($coupon['google_promotion']) && !empty($brand_to_do['brand_id']) && isset($brand_to_do['closeout']) ){
				/*$data = array(
				   'promotion_id' => $coupon['couponCode']
				);
				$this->db->where('partnumber', $k);
				$this->db->update('partnumber', $data);*/
				
				$data = array(
				   'promotion_data' => $coupon['couponCode']."_*_*_".$brand_to_do['closeout']
				);
				$this->db->where('brand_id', $brand_to_do['brand_id']);
				$this->db->update('brand', $data);
			}
			
		}
		
		$coupon = $this->getCouponByCode($post['qty']);

		if( !$coupon || empty($cart) ){
			return FALSE;
		}
		if(!@$_SESSION['cart'][$post['sku'].'_'.$coupon['couponCode']])
		{  
			$coupon['qty'] = 1;
			$coupon['display_name'] = 'Coupon '.$coupon['couponCode'];
			$coupon['sku'] = 'coupon_'.$coupon['couponCode'];
			if( empty($itemsForPercentAge) ){
				$coupon = $this->processPercentageValue($coupon, FALSE);
			}else{
				$coupon = $this->processPercentageValueNew($itemsForPercentAge, $coupon, FALSE);
			}
			
			if($coupon['couponSpecialConstraintsId'])
			{
				$couponConstraints = json_decode($coupon['couponSpecialConstraintsId'], TRUE);
				$constraintList = $this->getSpecialConstraintsDD(TRUE);
				foreach($couponConstraints as $const)
				{
					$coupon = $this->$constraintList[$const]($coupon);
					if(is_null($coupon))
					return FALSE;
				}
			}
			
			$coupon['price'] = $coupon['wholesale'];
			$coupon['finalPrice'] = $coupon['wholesale'];
			$_SESSION['cart']['coupon_'.$coupon['couponCode']] = $coupon;
		}
		return TRUE;
	}
	
		
	public function calculateCoupon()
	{
		$this->load->model('coupons_m');
		
		foreach($_SESSION['cart'] as $key => &$coupon) 	
		{
			if(strpos($key, 'coupon') !== FALSE)
			{
				if($coupon)
				{
					  if($coupon['couponSpecialConstraintsId'])
					  {
					    $couponConstraints = json_decode($coupon['couponSpecialConstraintsId'], TRUE);
					    $constraintList = $this->coupons_m->getSpecialConstraintsDD(TRUE);
					    $coupon['wholesale'] = 0.00;
						$coupon = $this->processPercentageValue($coupon, TRUE); // Keep Shipping value inside Checkout process
					    foreach($couponConstraints as $const)
					    {
					      $coupon = $this->$constraintList[$const]($coupon);
					      if(is_null($coupon))
					        return FALSE;
					    } 
					    $coupon['price'] = $coupon['wholesale'];

					  }
				}
			}
		}		
	}
	
	public function deleteCoupon($id)
	{
		$where = array('id' => $id);
		$this->deleteRecord('coupon', $where);
	}
	
	function debug($p){
		echo "<pre>";
		print_r($p);
		echo "</pre>";
	}
	
}