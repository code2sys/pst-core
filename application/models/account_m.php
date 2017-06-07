<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_M extends Master_M
{
  	function __construct()
	{
		parent::__construct();
	}
	
	public function getPriceByPartNumber($partNumber)
	{
		$where = array('partnumber' => $partNumber);
		$this->db->select('partnumber.sale, partvariation.quantity_available, partvariation.stock_code');
		$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
		$partNumberRec = $this->selectRecord('partnumber', $where);
		return @$partNumberRec;
	}
	
	public function getDealerPriceByPartNumber( $partNumber ) {
		$where = array('partnumber' => $partNumber);
		$this->db->select('partnumber.sale, partdealervariation.quantity_available, partdealervariation.stock_code, partnumber.dealer_sale as sale');
		$this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
		$partNumberRec = $this->selectRecord('partnumber', $where);
		return @$partNumberRec;
	}
	
	public function getStockByPartId($partId)
	{
		$where = array('part.part_id' => $partId);
		$this->db->join('partpartnumber', 'partpartnumber.part_id = part.part_id');
		$this->db->join('partnumber', 'partnumber.partnumber_id = partpartnumber.partnumber_id');
		$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
		$partNumberRec = $this->selectRecord('part', $where);
		return $partNumberRec;
	}
	
	public function getDealerStockByPartId( $partId ) {
		$where = array('part.part_id' => $partId);
		$this->db->join('partpartnumber', 'partpartnumber.part_id = part.part_id');
		$this->db->join('partnumber', 'partnumber.partnumber_id = partpartnumber.partnumber_id');
		$this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
		$partNumberRec = $this->selectRecord('part', $where);
		return $partNumberRec;
	}
	
	public function getGarage($userId)
	{
		$garage = array();
		$record = array();
		$where = array('userId' => $userId);
		$record = $this->selectRecord('garage', $where);
		if($record)
		{
			$garage = json_decode($record['rideRecs'], TRUE);		
		}
	  	return $garage;
	}
	
	function array_unshift_assoc(&$arr, $key, $val) 
	{ 
		array_reverse($arr, true);
	    $arr[$key] = $val; 
	    return array_reverse($arr, true);; 
	}
	
	public function updateGarage($rideRecs = NULL, $existingGarage = array())
	{
		// Deactivate all Rides
		if(is_array($existingGarage))
		{
			foreach($existingGarage as &$ride)
				$ride['active'] = 0;
		}
		if(!is_null($rideRecs))
		{
			// Activate current ride
			$rideRecs['active'] = 1;
			
			// Merge existing Garage with New Ride
			if($existingGarage)
				$this->array_unshift_assoc($existingGarage, $rideRecs['name'], $rideRecs );
			else
				$existingGarage = array($rideRecs['name'] => $rideRecs);
		}
		$existingGarage = array_reverse($existingGarage);
		// Update Database for later retrieval
		$encodedRideRecs = json_encode($existingGarage);
		$data = array('rideRecs' => $encodedRideRecs, 'active' => 1, 'userId' => @$_SESSION['userRecord']['id']);
		$where = array('userId' => @$_SESSION['userRecord']['id'], 'active' => 1);
		if(( @$_SESSION['userRecord']['id']) && ($this->recordExists('garage', $where)))
			$this->updateRecords('garage', $data, $where, FALSE);
		else
			$this->createRecord('garage', $data, FALSE);
		return $existingGarage;
	}
	
	public function getAddresses($dd = false)
	{
		$where = array('user_id' => @$_SESSION['userRecord']['id']);
		$this->db->join('contact', 'contact.id = address.contact_id');
		$records = $this->selectRecords('address', $where);
		if($dd && $records)
		{
			$loop = $records;
			$records = array();
			foreach($loop as $rec)
			{
				$records[$rec['contact_id']] = $rec['street_address'] . ' ' . $rec['address_2'] . ', ' . $rec['city'];
			}
		}
		return $records;
	}
	
	public function getAddress($id)
	{
		$where = array('id' => @$id);
		$record = $this->selectRecord('contact', $where);
		return $record;
	}
	
	public function getOrderEmail($orderId)
	{
		$record = FALSE;
		$this->db->select('contact.email, order.sales_price');
		$where = array('order.id' => $orderId);
		$this->db->join('contact', 'contact.id = order.contact_id');
		$record = $this->selectRecord('order', $where);
		return $record;
	}
	
	public function buildRideName($idArray)
	{
		$where = array('make_id' => $idArray['make']);
		$make = $this->selectRecord('make', $where);
		$where = array('model_id' => $idArray['model']);
		$model = $this->selectRecord('model', $where);
		$record = array('name' =>$make['label'] . ' ' . $model['label'] . ' ' . $idArray['year'], 'make' =>$make, 'model' => $model, 'year' => $idArray['year']);
		return $record;
	}
		
	public function getFeaturedProducts()
	{
	  	$where = array('key' => 'featureProduct');
	  	$this->db->join('product', 'product.sku = config.value');
	  	$records = $this->selectRecords('config', $where);
	  	return $records;
	}
	
	public function verifyEmail($email)
	{
	  $record = FALSE;
		$where = array('lost_password_email' => $email);
		$record = $this->selectRecord('user', $where);
		return $record;
	}
	
	public function addNewsletterEmail($emailaddress, $user_id = NULL)
	{
		$where = array('emailaddress' => $emailaddress);
		if(!$this->recordExists('newsletter', $where))
		{
			$data = array('emailaddress' => $emailaddress );
			if($user_id)
				$data['user_id'] = $user_id;
			$this->createRecord('newsletter', $data, FALSE);
		}
	}
	
	public function addReview($data, $user_id = NULL)
	{
		if($user_id)
			$data['user_id'] = $user_id;
		$data['date'] = time();
		$this->createRecord('reviews', $data, FALSE);	
	}
	
	public function getUserPassword($id)
	{
	  $this->db->select('password');
  	$where = array('id' => $id);
  	$record = $this->selectRecord('user', $where);
  	return $record['password'];
	}
	
  public function createUser($data)
  {
    //$where = array('username' => $data['username']);
    $this->createRecord('user', $data, TRUE); 
    //$record = $this->selectRecord('user', $where); 
  }
  
 public function createNewAccount($data)
 {
    $this->load->library('encrypt');
    $contactData = array(
                          'email' =>  @$data['email'],
                          'first_name' => @$data['first_name'],
                          'last_name' => @$data['last_name'],
                          'street_address' => @$data['street_address'],
                          'address_2' => @$data['address_2'],
                          'city' => @$data['city'],
                          'state' => @$data['state'],
                          'zip' => @$data['zip'],
                          'country' => @$data['country'],
                          'phone' => @$data['phone'],
                          );
    $billingId = $this->createRecord('contact', $contactData, TRUE);
    $userData = array(
                      'username' =>            $data['email'],
                      'password' =>            $this->encrypt->encode($data['password']),
                      'lost_password_email' => $data['email'],
                      'billing_id' =>          @$billingId
                      );
    $userId = $this->createRecord('user', $userData, FALSE);
     return $userId;
  }
  
	public function updateUserInfo($data)
	{
		if(@$data['password'])
		{
			$this->load->library('encrypt');
			 $userData = array( 'password' => $this->encrypt->encode($data['password']));
			 $where = array('id' => $_SESSION['userRecord']['id']);
			 $this->updateRecord('user', $userData, $where, FALSE);
		}
		$where = array('id' => $_SESSION['userRecord']['billing_id']);
		$return = $this->updateRecord('contact', $data, $where, FALSE);
		return $return;
	}
	
	public function updateUserRec($data)
	{
		$where = array('id' => $_SESSION['userRecord']['id']);
		$return = $this->updateRecord('user', $data, $where, FALSE);
		return $return;	
	}
	
	public function updateAddress($id, $data)
	{
		$where = array('id' => $id);
		$return = $this->updateRecord('contact', $data, $where, FALSE);
		return $return;
	}
	
	public function createAddress($data)
	{
		$id = $this->createRecord('contact', $data,  FALSE);
		$addData = array('user_id' => $_SESSION['userRecord']['id'], 'contact_id' => $id);
		$this->createRecord('address', $addData,  FALSE);
	}
	
	public function verifyUserAddress($user_id, $contact_id)
	{
		$where = array('user_id' => $user_id, 'contact_id' => $contact_id);
		$success = $this->recordExists('address', $where);
		return $success;
	}

  	public function verifyUsername($username)
	{
	  	$record = FALSE;
	  	$this->db->select('user.id, username, password, admin, first_name, last_name, last_login, billing_id, shipping_id, mark_up, user_type, user.cc_permission, user.status');
		//$where = array('username' => $username, 'user_type' => 'normal');
		$where = array('username' => $username);
		$this->db->where("(user_type = 'normal'", NULL, FALSE);
		$this->db->or_where("user_type = 'employee')", NULL, FALSE);
		$this->db->join('contact', 'contact.id = user.billing_id', 'LEFT');
		$record = $this->selectRecord('user', $where);
		
		if( $record ) {
			$this->db->where('user_id', $record['id']);
			$permissions = $this->selectRecords('userpermissions');
			$userPerm = array();
			foreach( $permissions as $permission ) {
				$userPerm[$permission['id']] = $permission['permission'];
			}
			$record['permissions'] = $userPerm;
		}
		
		return $record;
	}
	
	public function updateUserTempCode($username, $tempCode)
	{
  		$record = FALSE;
		$where = array('username' => $username);
		$record = $this->selectRecord('user', $where);

		if(@$record)
  			$this->updateRecord('user', array('temp_code' => $tempCode), array('id' => $record['id']), FALSE);
		else
		    return FALSE;
		return $record['lost_password_email'];
	}
	
	public function getUserByTempCode($tempCode)
	{
  	$record = FALSE;
		$where = array('temp_code' => $tempCode);
		$record = $this->selectRecord('user', $where);
		return $record;
	}
	
  public function updateLogin($userId)
	{
		$where = array('id' => $userId);
		$record = $this->selectRecord('user', $where);
		$post = array();
		$post['last_login'] = $record['current_login'];
		$post['current_login'] = time();
		$this->updateRecord('user', $post, $where, FALSE);
	}
	
	public function updateUserMass($post)
	{
	  	if(!empty($post['id']))
	  	{
	    	foreach($post['id'] as $key => $user)
	    	{
	      	$data['wholesaler'] = @$post['wholesaler'][$key] ? 1 : 0;
	      	$data['no_tax'] = @$post['no_tax'][$key] ? 1 : 0;
	      	$success = $this->updateRecord('user', $data, array('id' => $user), FALSE);
	    	}
	  	}
	}
	
	public function updateUserWithEmail($data)
	{
		if((@$data['username']))
		{
			$newData['password'] = $data['password'];
			$newData['temp_code'] = '';
			$where = array('username' => $data['username']);
			return $this->updateRecord('user', $newData, $where, FALSE);
		}
		else
		  	return FALSE;
	}
	
	public function updateContact($post, $type = 'shipping', $userId = NULL)
	{
		$contactId = FALSE;
		if($type == 'shipping')
			$typeId = 'shipping_id';
		else
			$typeId = 'billing_id';
		if($userId) // Updating User Record
		{  
			$where = array('id' => $userId);
			$userRec = $this->selectRecord('user', $where);
			if(empty($post['email']))
				$post['email'] = $userRec['lost_password_email'];
			
			$contactId = $this->createRecord('contact', $post, FALSE);
			$post = array($typeId => $contactId);
			$this->updateRecord('user', $post, $where, FALSE);
		}
		else // Create Contact Record not associated with User Record
			$contactId = $this->createRecord('contact', $post, FALSE);
		
		return $contactId;
	}
	
	public function updateShippingTable($post, $userId, $shipping_id = NULL)
	{
		$where = array('id' => $userId);
		$userRec = $this->selectRecord('user', $where);	
		$where = array('id' => $userId);
		/*
		if(is_numeric($shipping_id)) // Updating an existing record already attached to a user record
		{  
		$where = array('id' => $shipping_id);
		$this->updateRecord('contact', $post, $where, FALSE);
		}
		else // Creating a new record and attach to a user
		{
		*/
		$shipping_id = $this->createRecord('contact', $post, FALSE);
		$post = array('contact_id' => $shipping_id, 'user_id' => $userId);
		$this->createRecord('user_shipping', $post, FALSE);
		//    }
		return $shipping_id;
	}
	
	public function getCountries($active = 1)
	{
		$returnArr = array();
		$this->db->select('country');
		$this->db->group_by('country');
		$where = array('active' => $active);
		$countries = $this->selectRecords('taxes', $where);
		if($countries)
		{
			foreach($countries as $country)
			{
				switch($country['country'])
				{
					case 'US':
						$returnArr['USA'] = 'USA';
						break;
					case 'CA':
						$returnArr['Canada'] = 'Canada';
						break;
				}
			}
		}
		return array_reverse($returnArr, true);
	}
	
	public function getTerritories($country = 'US', $active = 1)
	{
		$returnArr = array(0 => 'Please Select');
		$this->db->select('state, mailcode');
		$where = array('active' => $active, 'country' => $country);
		$territories = $this->selectRecords('taxes', $where);
		if($territories)
		{
			foreach($territories as $loc)
			{
				$returnArr[$loc['mailcode']] = $loc['state']; 
			}
		}
		return $returnArr;
	}
	
	public function getTaxes()
	{
		$territories = $this->selectRecords('taxes');
		if($territories)
		{
			foreach($territories as $loc)
			{
				$returnArr[$loc['mailcode']] = $loc; 
			}
		}
		return $returnArr;
	}
	
	public function getBillingInfo($id)
	{
  	$where = array('user.id' => $id);
  	$this->db->join('contact', 'contact.id = user.billing_id');
  	$record = $this->selectRecord('user', $where);
  	return $record;
	}
	
  public function getShippingInfo($id)
	{
  	$where = array('user.id' => $id);
  	$this->db->join('contact', 'contact.id = user.shipping_id');
  	$record = $this->selectRecord('user', $where);
  	return $record;
	}
	
  public function getShippingDDInfo($id)
	{
	  $records = array();
	  $retArr = array('default' => 'Create a New Shipping Address');
	  $this->db->select('user_shipping.contact_id, contact.first_name, contact.last_name, contact.city');
	  $where = array('user_id' => $id);
	  $this->db->join('contact', 'contact.id = user_shipping.contact_id');
  	$records = $this->selectRecords('user_shipping', $where);
	  $this->db->select('user.shipping_id AS contact_id, contact.first_name, contact.last_name, contact.city');
  	$where = array('user.id' => $id);
  	$this->db->join('contact', 'contact.id = user.shipping_id');
  	$records['default'] = $this->selectRecord('user', $where);
  	
  	if(is_array($records))
  	{
    	foreach($records as $rec)
    	{
    	  $retArr[@$rec['contact_id']] = $rec['first_name'] . ' ' . $rec['last_name'] . ' - ' . $rec['city'];
    	}
  	}
  	return $retArr;
	}
	
	public function getContactInfo($contactId)
	{
	  	$where = array('id' => $contactId);
	  	$record = $this->selectRecord('contact', $where);
	  	return $record;
	}
	
  public function getWholesalerId()
  {
      $wholesalerRecord = $this->selectRecord('user', array('wholesaler' => '1'));
      return $wholesalerRecord['id'];
  }
  
  public function updateOrderRecord($orderIds)
  {
      return $this->updateRecord('order',array('Retailer_Order_ID' => $orderIds['butterflyExpressOrderId'], 
      																		   'process_date' => date("U")), 
      																array('id' => $orderIds['wholesalerOrderId']), false);
  }
    
	public function getPDFOrder($orderId)
	{
		$this->db->select('order.id AS order_id, '.
										'order.user_id AS user_id, '.
										'order.contact_id AS contact_id, '.
										'order.shipping_id AS shipping_id,'.
										'order.tax AS tax,'.
										'order.sales_price AS sales_price, '.
										'order.shipping AS shipping, '.
										'order.order_date AS order_date '
										);
		$records = FALSE;
		$where = array('id' => $orderId);
		$record = $this->selectRecord('order', $where);
		if(@$record)
		{
			$where = array('order_id' => $orderId);
			$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
			$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
			$record['products'] = $this->selectRecords('order_product', $where);
			// check to see if product is a combo.  If so, join the combo and product name.
			if(@$record['products'])
			{
				foreach($record['products'] as &$prod)
				{
					$this->db->select('partnumberpartquestion.answer, partquestion.question');
					//$this->db->join("partnumberpartquestion", "partnumberpartquestion.partnumber_id = partnumber.partnumber_id");
					$where = array('partnumberpartquestion.partnumber_id' => $prod['partnumber_id'], 'productquestion' => 0);
					$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
					$questions = $this->selectRecords('partnumberpartquestion', $where);
					
					if( @$questions ) {
						foreach( $questions as $k => $v ) {
							$prod['question'] = $v['question'].' :: '.$v['answer'].'<br>';
						}
					}
					$where = array('partpartnumber.partnumber_id' => $prod['partnumber_id']);
					$this->db->join('part', 'part.part_id = partpartnumber.part_id');
					$parts = $this->selectRecords('partpartnumber', $where);
					if(count($parts) > 1)
					{
						foreach($parts as $part)
						{
							if(($part['part_id'] != $prod['part_id']) && (strpos($part['name'], 'COMBO') === FALSE))
							{
								$namepieces = explode('-', $part['name']);
								$prod['name'] .= ' - ' . $namepieces[1]; 
							}
						}
					}
				}
			}
			$this->db->select('username');
			$record['username'] = $this->selectRecord('user', array('id' => $orderId));
			$record['contactBilling'] = $this->selectRecord('contact', array('id' => $record['contact_id']));
			$record['contactShipping'] = $this->selectRecord('contact', array('id' => @$record['shipping_id']));
		}
		$records = array($record); // Put in Multi-Dimentional Array to match Orders call and use the same format for creation.
		return $records;
	}
	
	public function getPDFOrders($id, $date = NULL)
	{
	  $this->db->select('order.id AS order_id, '.
	                    'order.user_id AS user_id, '.
	                    'order.contact_id AS contact_id, '.
	                    'order.shipping_id AS shipping_id,'.
	                    'order.sales_price AS sales_price, '.
	                    'order.shipping AS shipping, '.
	                    'order.order_date AS order_date '
	                    );
	  $records = FALSE;
	  if(is_null($date))
      $where = array('process_date IS NULL' => NULL, 'order_date >' => 0);
    else
      $where = array('process_date' => $date, 'order_date >' => 0);
    $where['contact_id !='] = 0;
  	$this->db->order_by('order_date', 'DESC');
  	$records = $this->selectRecords('order', $where);
  	if(@$records)
  	{
    	foreach($records as &$row)
    	{
      	$where = array('order_id' => $row['order_id']);
      	$this->db->join('product', 'product.sku = order_product.product_sku', 'LEFT');
      	$row['products'] = $this->selectRecords('order_product', $where);
      	$this->db->select('username');
      	$row['username'] = $this->selectRecord('user', array('id' => $row['user_id']));
        $row['contactBilling'] = $this->selectRecord('contact', array('id' => $row['contact_id']));
        $row['contactShipping'] = $this->selectRecord('contact', array('id' => @$row['shipping_id']));
    	}
  	}
  	if(is_null($date))
  	  $this->updateOrders($id);
  	return $records;
	}
	
	public function getPrevOrderDates($limit, $offset)
	{
		$result = FALSE;
		$this->db->distinct();
		$this->db->select('process_date');
		$this->db->order_by('process_date', 'DESC');
		$query = $this->db->get('order', $limit, $offset);
		if($query->num_rows() > 0)
			$result = $query->result_array();
		$query->free_result();
		return $result;
	}
	
	private function updateOrders($id)
	{
	  $where = array('process_date IS NULL' => NULL, 'order_date >' => 0);
  	$this->updateRecords('order', array('process_date' => time(), 'process_user' => $id), $where);
	}
	
	public function getOrderCount($unprocessed = FALSE)
	{
		if($unprocessed)
			$where = array('process_date IS NULL' => NULL, 'order_date >' => 0);
		
		$this->db->distinct();
		$this->db->select('order_date');
		$this->db->from('order');
		$query = $this->db->get();
		$num = $query->num_rows();
		return $num;
	}
	
	public function getOrders($id = NULL, $unprocessed = FALSE, $limit = NULL, $all = NULL)
	{
		$this->db->select('order.id AS order_id, '.
		            'order.user_id AS user_id, '.
		            'order.contact_id AS contact_id, '.
		            'order.shipping_id AS shipping_id, '.
		            'order.sales_price AS sales_price, '.
		            'order.shipping AS shipping, '.
		            'order.weight AS weight, '.
		            'order.tax AS tax, '.
		            'order.will_call AS will_call, '.
		            'order.process_date AS process_date, '.
		            'order.batch_number AS batch_number, '.
		            'order.special_instr AS special_instr, '.
		            'order.Reveived_date AS Reveived_date,'.
		            'order.order_date AS order_date, '.
		            'contact.first_name AS first_name, '.
		            'contact.last_name AS last_name, '.
		            'contact.street_address AS street_address, '.
		            'contact.address_2 AS address_2, '.
		            'contact.city AS city, '.
		            'contact.state AS state, '.
		            'contact.zip AS zip, '.
		            'contact.company AS company,'.
		            'shipping.first_name AS shipping_first_name, '.
		            'shipping.last_name AS shipping_last_name, '.
		            'shipping.street_address AS shipping_street_address, '.
		            'shipping.address_2 AS shipping_address_2, '.
		            'shipping.city AS shipping_city, '.
		            'shipping.state AS shipping_state, '.
		            'shipping.zip AS shipping_zip, '.
		            'shipping.company AS shipping_company ');
		$records = FALSE;
		$where = array('user_id' => $id, 'order_date >' => 0);
		if($unprocessed)
			$where = array('process_date IS NULL' => NULL, 'order_date >' => 0);
		if($all)
			$where = array();
		if(!is_null($limit))
			$this->db->limit($limit);
			
		$this->db->order_by('order_date', 'DESC');
		$this->db->join('contact', 'contact.id = order.contact_id');
		$this->db->join('contact shipping', 'shipping.id = order.contact_id');
		$records = $this->selectRecords('order', $where);
		
		
		if($records)
		{
			foreach($records as &$row)
			{

					$this->db->select('part.name as display_name, order_product.price as price, order_product.qty as qty, order_product.product_sku as sku, order_product.distributor, part.part_id, partnumber.partnumber_id');
					$where = array('order_id' =>  $row['order_id']);
					$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
					$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
					
					$row['products'] = $this->selectRecords('order_product', $where);
					
					if(@$row['products'])
					{
						foreach($row['products'] as &$prod)
						{
							$where = array('partpartnumber.partnumber_id' => $prod['partnumber_id']);
							$this->db->join('part', 'part.part_id = partpartnumber.part_id');
							$parts = $this->selectRecords('partpartnumber', $where);
							if(count($parts) > 1)
							{
								foreach($parts as $part)
								{
									if(($part['part_id'] != $prod['part_id']) && (strpos($part['name'], 'COMBO') === FALSE))
									{
										$namepieces = explode('-', $part['name']);
										$prod['display_name'] .= ' - ' . @$namepieces[1]; 
									}
								}
							}
						}
					}
				}
			}

		
		
		return $records;
	}
	
	public function getfirstFiveProducts($orderNum)
	{
		$where = array('order_id' => $orderNum);
		$this->db->select(' CONCAT (\'http://' . WEBSITE_HOSTNAME . '/shopping/item/\', part.part_id) AS \'URL\', product_sku AS \'SKU\', partvariation.part_number AS \'GTIN\', order_product.price AS \'PRICE\'', FALSE);
		$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku');
		$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
		$this->db->join('part', 'part.part_id = order_product.part_id');
		$this->db->limit(5);
		$records = $this->selectRecords('order_product', $where);
		return $records;
	}
	
	public function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	public function validGarragePartNumber($partId, $activeMachine = NULL) {
        $rides = array();
        $this->db->select('model.name as model, make.name as make, partnumbermodel.year, partnumber.partnumber', FALSE);
        $where = array('partpartnumber.part_id' => $partId);
        if (@$activeMachine['model']['model_id']) {
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        if (@$activeMachine['year']) {
            $where['partnumbermodel.year'] = $activeMachine['year'];
        }
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
        $this->db->join('model', 'model.model_id = partnumbermodel.model_id');
        $this->db->join('make', 'model.make_id = make.make_id');
        $this->db->join('partnumber', 'partnumber.partnumber_id = partpartnumber.partnumber_id');
        $this->db->group_by('model.make_id, model.model_id, partnumbermodel.year');
        $this->db->order_by('model.make_id, model.model_id, partnumbermodel.year DESC');
        $rides = $this->selectRecord('partnumbermodel', $where);
        return $rides;
    }
	
	public function recordOrderCreation_old($contactInfo, $cart, $userId = NULL)
	{
		$orderRec = array();
		if(is_array($cart))
		{
			// Create Order record including total product sales and shipping
			$orderRec['contact_id'] = $contactInfo['billing_id'];
			$orderRec['shipping_id'] = $contactInfo['shipping_id'];
			$orderRec['shipping_type'] = @$cart['shipping']['type'];
			$orderRec['sales_price'] = $cart['transAmount'];
			$orderRec['shipping'] = $cart['shipping']['finalPrice'];
			$orderRec['tax'] = $cart['tax']['finalPrice'];
			$orderRec['IP'] = $this->get_client_ip();
			
			$orderRec['user_id'] = $userId;
			$orderId = $this->createRecord('order', $orderRec, FALSE);
			// Create order_product record for each item purchased including price charged for each item.
			foreach($cart as $key => $product)
			{
			  	if(($key != 'shipping') && ($key != 'transAmount') && ($key != 'tax') && (@$product['finalPrice']))
			  	{
			  		if(@$product['question'])
			  		{
				  		foreach($product['question'] as $sku)
				  		{
				  			$price = $this->getPriceByPartNumber($sku);
					  		$data = array('order_id' => $orderId, 'product_sku' => $sku, 'price' => str_replace(',', '',  $price['sale']), 'part_id' => $product['part_id'], 'qty' => @$product['qty']);
							if(@$_SESSION['garage']) {
								$dftmnt = $this->validGarragePartNumber($product['part_id'], $_SESSION['garage'][$_SESSION['activeMachine']['name']]);
								$data['fitment'] = $dftmnt['make'].' '.$dftmnt['model'].' '.$dftmnt['year'];
							}
							$data['fitment'] = $product['ftmnt'];
					  		$this->createRecord('order_product', $data, FALSE);
				  		}
							//Static Order Screen
							$this->db->select('partnumber.partnumber_id, 
									  part.name, 
									  partnumberpartquestion.answer, 
									  part.part_id, 
									  partnumber.partnumber,
									  partquestion.question,
									  order_product.qty,
									  order_product.fitment,
									  order_product.distributor,
									  partnumber.sale,
									  partvariation.stock_code,
									  order_product.product_sku,
									  order_product.status');
							$where = array('order_id' => $orderId, 'productquestion' => 0);
							$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
							$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
							$this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
							$this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
							$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
							$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
							$this->db->group_by('partnumber.partnumber');
							$staticOrder = $this->selectRecords('order_product', $where);
							if(@$staticOrder) {
								foreach( $staticOrder as $k => $v ) {
									$v['order_id'] = $orderId;
									$this->createRecord('order_product_details', $v, FALSE);
								}
							}
							//Static Order End
			  		}
			  		else
			  		{
					
						$data = array('order_id' => $orderId, 'product_sku' => $key, 'price' => str_replace(',', '',  $product['finalPrice']), 'part_id' => @$product['part_id'], 'qty' => @$product['qty']);
							if(@$_SESSION['garage']) {
								$dftmnt = $this->validGarragePartNumber($product['part_id'], $_SESSION['garage'][$_SESSION['activeMachine']['name']]);
								$data['fitment'] = $dftmnt['make'].' '.$dftmnt['model'].' '.$dftmnt['year'];
							}
							$data['fitment'] = $product['ftmnt'];
			  			$this->createRecord('order_product', $data, FALSE);
						//Static Order Screen
						$this->db->select('partnumber.partnumber_id, 
								  part.name, 
								  partnumberpartquestion.answer, 
								  part.part_id, 
								  partnumber.partnumber,
								  partquestion.question,
								  partnumber.sale,
								  partvariation.stock_code');
						$where = array('order_id' => $orderId, 'productquestion' => 0);
						$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
						$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
						$this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
						$this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
						$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
						$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
						$this->db->group_by('partnumber.partnumber');
						$staticOrder = $this->selectRecords('order_product', $where);
						if(@$staticOrder) {
							foreach( $staticOrder as $k => $v ) {
								$v['order_id'] = $orderId;
								$this->createRecord('order_product_details', $v, FALSE);
							}
						}
						//Static Order End
			  		}
			    }
			    elseif(@$product['couponCode'])
			    {
				    $data = array('order_id' => $orderId, 'product_sku' => $product['couponCode'], 'price' => str_replace(',', '',  $product['price']), 'qty' => 0);
					if(@$_SESSION['garage']) {
						$dftmnt = $this->validGarragePartNumber($product['part_id'], $_SESSION['garage'][$_SESSION['activeMachine']['name']]);
						$data['fitment'] = $dftmnt['make'].' '.$dftmnt['model'].' '.$dftmnt['year'];
					}
					$data['fitment'] = $product['ftmnt'];
			  		$this->createRecord('order_product', $data, FALSE);
							//Static Order Screen
							$this->db->select('partnumber.partnumber_id, 
									  part.name, 
									  partnumberpartquestion.answer, 
									  part.part_id, 
									  partnumber.partnumber,
									  partquestion.question,
									  order_product.qty,
									  order_product.fitment,
									  order_product.distributor,
									  partnumber.sale,
									  partvariation.stock_code,
									  order_product.product_sku,
									  order_product.status');
							$where = array('order_id' => $orderId, 'productquestion' => 0);
							$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
							$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
							$this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
							$this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
							$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
							$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
							$this->db->group_by('partnumber.partnumber');
							$staticOrder = $this->selectRecords('order_product', $where);
							if(@$staticOrder) {
								foreach( $staticOrder as $k => $v ) {
									$v['order_id'] = $orderId;
									$this->createRecord('order_product_details', $v, FALSE);
								}
							}
							//Static Order End
			    }
			}
		}
		return $orderId;
	}
	
	public function recordOrderCreation($contactInfo, $cart, $userId = NULL)
	{
		$orderRec = array();
		if(is_array($cart))
		{
			// Create Order record including total product sales and shipping
			$orderRec['contact_id'] = $contactInfo['billing_id'];
			$orderRec['shipping_id'] = $contactInfo['shipping_id'];
			$orderRec['shipping_type'] = @$cart['shipping']['type'];
			$orderRec['sales_price'] = $cart['transAmount'];
			$orderRec['shipping'] = $cart['shipping']['finalPrice'];
			$orderRec['tax'] = $cart['tax']['finalPrice'];
			$orderRec['IP'] = $this->get_client_ip();
			
			$orderRec['user_id'] = $userId;
			$orderId = $this->createRecord('order', $orderRec, FALSE);
			// Create order_product record for each item purchased including price charged for each item.
			foreach($cart as $key => $product)
			{
			  	if(($key != 'shipping') && ($key != 'transAmount') && ($key != 'tax') && (@$product['finalPrice']))
			  	{
			  		if(@$product['question'])
			  		{
				  		foreach($product['question'] as $sku)
				  		{
				  			$price = $this->getPriceByPartNumber($sku);
					  		$data = array('order_id' => $orderId, 'product_sku' => $sku, 'price' => str_replace(',', '',  $price['sale']), 'part_id' => $product['part_id'], 'qty' => @$product['qty']);
							if(@$_SESSION['garage']) {
								$dftmnt = $this->validGarragePartNumber($product['part_id'], $_SESSION['garage'][$_SESSION['activeMachine']['name']]);
								$data['fitment'] = $dftmnt['make'].' '.$dftmnt['model'].' '.$dftmnt['year'];
							}
							$data['fitment'] = $product['ftmnt'];
                                                        
                                                        $this->db->select('partnumber.partnumber_id');
                                                        $disWhere = array('partnumber' => $sku);
                                                        $distributorcs = $this->selectRecord('partnumber', $disWhere);
                                                        
                                                        $disWhere = array('partnumber_id' => $distributorcs['partnumber_id']);
                                                        $this->db->join('distributor', 'distributor.distributor_id=partvariation.distributor_id');
                                                        $distributorDtl = $this->selectRecord('partvariation', $disWhere);
                                                        
                                                        $data['distributor'] = array('id' => $distributorDtl['distributor_id'], 'qty' => @$product['qty'], 'part_number' => $distributorDtl['part_number'], 'distributor_name' => $distributorDtl['name'], 'dis_cost' => $distributorDtl['cost']);
                                                        $data['distributor'] = json_encode($data['distributor']);
					  		$this->createRecord('order_product', $data, FALSE);
				  		}
							//Static Order Screen
							$this->db->select('partnumber.partnumber_id, 
									  part.name, 
									  partnumberpartquestion.answer, 
									  part.part_id, 
									  partnumber.partnumber,
									  partquestion.question,
									  order_product.qty,
									  order_product.fitment,
									  order_product.distributor,
									  partnumber.sale,
									  partvariation.stock_code,
									  order_product.product_sku,
									  order_product.distributor,
									  order_product.status');
							$where = array('order_id' => $orderId, 'productquestion' => 0);
							$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
							$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
							$this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
							$this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
							$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
							$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
							$this->db->group_by('partnumber.partnumber');
							$staticOrder = $this->selectRecords('order_product', $where);
							if(@$staticOrder) {
								foreach( $staticOrder as $k => $v ) {
									$v['order_id'] = $orderId;
									$this->createRecord('order_product_details', $v, FALSE);
								}
							}
							//Static Order End
			  		}
			  		else
			  		{
					
						$data = array('order_id' => $orderId, 'product_sku' => $key, 'price' => str_replace(',', '',  $product['finalPrice']), 'part_id' => @$product['part_id'], 'qty' => @$product['qty']);
							if(@$_SESSION['garage']) {
								$dftmnt = $this->validGarragePartNumber($product['part_id'], $_SESSION['garage'][$_SESSION['activeMachine']['name']]);
								$data['fitment'] = $dftmnt['make'].' '.$dftmnt['model'].' '.$dftmnt['year'];
							}
							$data['fitment'] = $product['ftmnt'];
                                                        
                                                $this->db->select('partnumber.partnumber_id');
                                                $disWhere = array('partnumber' => $key);
                                                $distributorcs = $this->selectRecord('partnumber', $disWhere);

                                                $disWhere = array('partnumber_id' => $distributorcs['partnumber_id']);
                                                $this->db->join('distributor', 'distributor.distributor_id=partvariation.distributor_id');
                                                $distributorDtl = $this->selectRecord('partvariation', $disWhere);

                                                $data['distributor'] = array('id' => $distributorDtl['distributor_id'], 'qty' => @$product['qty'], 'part_number' => $distributorDtl['part_number'], 'distributor_name' => $distributorDtl['name'], 'dis_cost' => $distributorDtl['cost']);
                                                $data['distributor'] = json_encode($data['distributor']);
                                                
			  			$this->createRecord('order_product', $data, FALSE);
						//Static Order Screen
						$this->db->select('partnumber.partnumber_id, 
								  part.name, 
								  partnumberpartquestion.answer, 
								  part.part_id, 
								  partnumber.partnumber,
								  partquestion.question,
								  partnumber.sale,
								  partvariation.stock_code');
						$where = array('order_id' => $orderId, 'productquestion' => 0);
						$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
						$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
						$this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
						$this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
						$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
						$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
						$this->db->group_by('partnumber.partnumber');
						$staticOrder = $this->selectRecords('order_product', $where);
						if(@$staticOrder) {
							foreach( $staticOrder as $k => $v ) {
								$v['order_id'] = $orderId;
                                                                $v['distributor'] = $distributorDtl['distributor'];
								$this->createRecord('order_product_details', $v, FALSE);
							}
						}
						//Static Order End
			  		}
			    }
			    elseif(@$product['couponCode'])
			    {
				    $data = array('order_id' => $orderId, 'product_sku' => $product['couponCode'], 'price' => str_replace(',', '',  $product['price']), 'qty' => 0);
					if(@$_SESSION['garage']) {
						$dftmnt = $this->validGarragePartNumber($product['part_id'], $_SESSION['garage'][$_SESSION['activeMachine']['name']]);
						$data['fitment'] = $dftmnt['make'].' '.$dftmnt['model'].' '.$dftmnt['year'];
					}
					$data['fitment'] = $product['ftmnt'];
			  		$this->createRecord('order_product', $data, FALSE);
							//Static Order Screen
							$this->db->select('partnumber.partnumber_id, 
									  part.name, 
									  partnumberpartquestion.answer, 
									  part.part_id, 
									  partnumber.partnumber,
									  partquestion.question,
									  order_product.qty,
									  order_product.fitment,
									  order_product.distributor,
									  partnumber.sale,
									  partvariation.stock_code,
									  order_product.product_sku,
									  order_product.status');
							$where = array('order_id' => $orderId, 'productquestion' => 0);
							$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
							$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
							$this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
							$this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
							$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
							$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
							$this->db->group_by('partnumber.partnumber');
							$staticOrder = $this->selectRecords('order_product', $where);
							if(@$staticOrder) {
								foreach( $staticOrder as $k => $v ) {
									$v['order_id'] = $orderId;
									$this->createRecord('order_product_details', $v, FALSE);
								}
							}
							//Static Order End
			    }
			}
		}
		return $orderId;
	}

	public function createCFBillingRecord($data, $userBillingId)
	{
		$record = FALSE;
		$updateData = array(
											'billingid' => $userBillingId,
											'ccfname' => $data['cc_first_name'],
							                'cclname' => $data['cc_last_name'],
							                'ccnumber' => $data['cc'],
							                'ccexpmo' => $data['exp_date_mn'],
							                'ccexpyr' => $data['exp_date_yr'],
							                'cvc' => $data['cvc'],
							                'ccaddr' => $data['contactInfo']['street_address'] . ' ' . $data['contactInfo']['address_2'],
							                'cccity' => $data['contactInfo']['city'],
							                'ccstate' => $data['contactInfo']['state'],
							                'cczip' => $data['contactInfo']['zip'],
							                'cccountry' => $data['contactInfo']['country'],
							                'email' => $data['contactInfo']['email'],
							                'phone' => $data['contactInfo']['phone'],
							                'company' => $data['contactInfo']['company'],
							                'Extra' => 'auto_increment'
							                );
		$id = $this->createRecord('cf_user_billing_info', $updateData, FALSE);
		return $id;
	}
	
	public function recordPaidTransaction($orderId, $cart)
	{
		$orderRec = array('order_date' => time(), 'shipping' => $cart['shipping']['finalPrice'], 'shipping_type' => @$cart['shipping']['type']);
		$where = array('id' => $orderId);
		$this->updateRecord('order', $orderRec, $where, FALSE);
	}
	
	public function createTransaction($id, $transAmount, $response)
	{
		$data = array(  'userid' => $id,
					              'total' => $transAmount,
					              'tdate' => time(),
					              'status' => $response,
					              'Extra' => 'auto_increment'
					              );
		  $id = $this->createRecord('cf_cc_transactions', $data, FALSE);
		  return $id;
	}
	
	public function unsetCart()
	{	
		if( !empty($_SESSION['userRecord']) ){
			$where = array('user_id' => $_SESSION['userRecord']['id']);
		}else{
			$where = array('user_id' => $_SESSION['guestUser']['id']);
		}
		$data = array('cart' => '');
		$this->updateRecord('cart', $data, $where, FALSE);
	}
	
	public function validAccess( $action ) {
		$this->db->where('user_id', $_SESSION['userRecord']['id']);
		$this->db->where('permission', $action);
		$record = $this->selectRecord('userpermissions');
		return empty($record) ? false : true;
	}
	
	public function getOrderDistributorDetails( $order_id, $productsku ) {
		$productsku = array_flip($productsku);
		// $this->db->select('partnumber.partnumber_id, 
										  // part.name, 
										  // partnumberpartquestion.answer, 
										  // part.part_id, 
										  // partnumber.partnumber,
										  // partquestion.question,
										  // order_product.qty,
										  // order_product.fitment,
										  // order_product.distributor,
										  // order_product.price as sale,
										  // partvariation.stock_code,
										  // order_product.product_sku,
										  // order_product.status');
		// $where = array('order_id' => $order_id, 'productquestion' => 0, 'partvariation.quantity_available > ' => '0');
		// $this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
		// $this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
		// $this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
		// $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
		// $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
		// $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
		// $this->db->where_in('order_product.product_sku', $productsku);
		// $this->db->group_by('partnumber.partnumber');
		
		$this->db->select('partnumber.partnumber_id, 
										  part.name, 
										  partnumberpartquestion.answer, 
										  part.part_id, 
										  partnumber.partnumber,
										  partquestion.question,
										  order_product.qty,
										  order_product.fitment,
										  order_product.distributor,
										  partnumber.sale,
										  order_product.price as sale,
										  partvariation.stock_code,
										  order_product.product_sku,
										  order_product.status');
		$where = array('order_id' => $order_id, 'productquestion' => 0);
		$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
		$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
		$this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
		$this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
		$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
		$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
		$this->db->where_in('order_product.product_sku', $productsku);
		$this->db->group_by('partnumber.partnumber');

		$products = $this->selectRecords('order_product', $where);
		//echo $this->db->last_query();
		
		$this->db->select('order_product_details.partnumber_id, 
										  order_product_details.name, 
										  order_product_details.answer, 
										  order_product_details.part_id, 
										  order_product_details.partnumber,
										  order_product_details.question,
										  order_product.qty,
										  order_product.fitment,
										  order_product.distributor,
										  order_product_details.sale,
										  order_product_details.stock_code,
										  order_product.product_sku,
										  order_product.status');
		$where = array('order_product_details.order_id' => $order_id);
		$this->db->join('order_product', 'order_product.product_sku = order_product_details.partnumber');
		$this->db->where_in('order_product.product_sku', $productsku);
		$this->db->group_by('order_product_details.part_id');
		$staticProducts = $this->selectRecords('order_product_details', $where);
		
		if( count($products) < count($staticProducts)) {
			$products = $staticProducts;
		}
		// check to see if product is a combo.  If so, join the combo and product name.
		if(@$products)
		{
			foreach($products as &$prod)
			{
				// Get distributor id and partvariation.quantity_available
				$where = array('partnumber_id' => $prod['partnumber_id']);
				//$prod['distributorRecs'] = $this->selectRecords('partvariation', $where);
				$prod['distributorRecs'] = $this->selectRecords('partvariation', $where);
				$where = array('partnumber_id' => $prod['partnumber_id']);
				$prod['dealerRecs'] = $this->selectRecords('partdealervariation', $where);
				$where = array('partpartnumber.partnumber_id' => $prod['partnumber_id']);
				$this->db->join('part', 'part.part_id = partpartnumber.part_id');
				$parts = $this->selectRecords('partpartnumber', $where);
				if(count($parts) > 1)
				{
					foreach($parts as $part)
					{
						if(($part['part_id'] != $prod['part_id']) && (strpos($part['name'], 'COMBO') === FALSE))
						{
							$namepieces = explode('-', $part['name']);
							$prod['name'] .= ' - ' . $namepieces[1]; 
						}
					}
				}
			}
		}
		// echo '<pre>';
		// print_r($products);
		// echo '</pre>';exit;
		return $products;
	}
	
	public function updateOrderPST($orderId) {
		$data = array('sendPst' => 1);
		$where = array('id' => $orderId);
		$this->updateRecords('order', $data, $where, FALSE);
	}
	
	public function creditApplication( $data=array() ) {
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";exit;
	   $this->db->insert('finance_applications', $data );
	}
        
        public function createGuestCustomer( $customerData, $orderId ) {
            $where = array('id' => $orderId);
            $order = $this->selectRecord('order', $where);
            if(!@$order['user_id']) {
                return $this->createRecord('user', $customerData, FALSE);
            }
            return $order['user_id'];
        }
}
