<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_M extends Master_M
{
  	function __construct()
	{
		parent::__construct();
	}
	
	public function getOrder($id)
	{
		$this->db->select('order.id AS order_id, '.
		            'order.user_id AS user_id, '.
		            'order.contact_id AS contact_id, '.
		            'order.shipping_id AS shipping_id, '.
		            'order.sales_price AS sales_price, '.
		            'order.shipping AS shipping, '.
		            'order.tax AS tax, '.
		            'order.special_instr AS special_instr, '.
		            'order.Reveived_date AS Reveived_date,'.
		            'order.order_date AS order_date, '.
		            'order.process_date AS process_date, '.
		            'order.ship_tracking_code AS ship_tracking_code, '.
		            'order.IP AS customer_ip, '.
		            'contact.first_name AS first_name, '.
		            'contact.last_name AS last_name, '.
		            'contact.street_address AS street_address, '.
		            'contact.address_2 AS address_2, '.
		            'contact.city AS city, '.
		            'contact.state AS state, '.
		            'contact.zip AS zip, '.
		            'contact.country AS country, '.
		            'contact.email AS email, '.
		            'contact.phone AS phone, '.
		            'contact.company AS company,'.
		            'shipping.first_name AS shipping_first_name, '.
		            'shipping.last_name AS shipping_last_name, '.
		            'shipping.street_address AS shipping_street_address, '.
		            'shipping.address_2 AS shipping_address_2, '.
		            'shipping.city AS shipping_city, '.
		            'shipping.state AS shipping_state, '.
		            'shipping.zip AS shipping_zip, '.
		            'shipping.country AS shipping_country, '.
		            'shipping.email AS shipping_email, '.
		            'shipping.phone AS shipping_phone, '.
		            'shipping.company AS shipping_company, '.
		            'shipping.phone AS shipping_phone, '.
		            'cf_user_billing_info.ccfname, '.
		            'cf_user_billing_info.cclname, '.
		            'cf_user_billing_info.ccexpmo, '.
		            'cf_user_billing_info.ccexpyr, '.
		            'cf_user_billing_info.ccnumber, '.
		            'cf_user_billing_info.cvc, '.
		            'order.sendPst, '.
		            'order.sales_price '
		            );
		$records = FALSE;
		$where = array('order.id' => $id);

		$this->db->join('contact', 'contact.id = order.contact_id');
		$this->db->join('contact shipping', 'shipping.id = order.shipping_id');
		$this->db->join('(SELECT * FROM cf_user_billing_info ORDER BY id DESC) AS cf_user_billing_info', 'cf_user_billing_info.billingid = order.id', 'LEFT');
		
		$record = $this->selectRecord('order', $where);
		if($record)
		{
			$this->load->library('encrypt');
			$record['ccnmbr'] = $this->encrypt->decode($record['ccnumber']);
			
			$record['ccnumber'] = $this->creditCardLast4($record['ccnumber']);
			$where = array('order_id' => $record['order_id']);
			$this->db->order_by('datetime DESC');
			$statusRec = $this->selectRecord('order_status', $where);
			$record['status'] = $statusRec['status'];
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
											  order_product.dealer_qty,
											  order_product.distributor_qty,
											  order_product.status');
			$where = array('order_id' => $id, 'productquestion' => 0);
			$this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
			$this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
			$this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
			$this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
			$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
			$this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
			$this->db->group_by('partnumber.partnumber');
			
			$record['products'] = $this->selectRecords('order_product', $where);
			
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
			$where = array('order_product_details.order_id' => $id);
			$this->db->join('order_product', 'order_product.product_sku = order_product_details.partnumber');
			$this->db->group_by('order_product_details.partnumber');
			$record['staticProducts'] = $this->selectRecords('order_product_details', $where);
			//echo $this->db->last_query();
			
			if( count($record['products']) < count($record['staticProducts']) || empty($record['products'])) {
				$record['products'] = $record['staticProducts'];
			}
			// echo '<pre>';
			// echo count($record['products']);
			// echo count($record['staticProducts']);
			// print_r($record);
			// echo '</pre>';
		// echo '<pre>';
		// print_r($record['products']);
		// print_r($record['staticProducts']);
		// echo '</pre>';exit;
			// echo '<pre>';
			// print_r($record);
			// echo '</pre>';
			// if( empty($record['products']) ) {
				// $record['products'] = $record['staticProducts'];
				// unset($record['staticProducts']);
			// }
			
			// $this->db->select('count(order_product.order_id) as totalProducts');
			// $where = array('order_product.order_id' => $id);
			// $totalProducts = $this->selectRecord('order_product', $where);
			
			// $products = array();
			// if( count($record['products']) < $totalProducts['totalProducts'] ) {
				// foreach( $record['staticProducts'] as $k => $v ) {
					// $products[$v['partnumber_id']] = $v;
				// }
				// $record['products'] = $products;
				// unset($record['staticProducts']);
			// }
			
			// check to see if product is a combo.  If so, join the combo and product name.
			if(@$record['products'])
			{
				foreach($record['products'] as &$prod)
				{
					// Get distributor id and partvariation.quantity_available
					$where = array('partnumber_id' => $prod['partnumber_id']);
					//$prod['distributorRecs'] = $this->selectRecords('partvariation', $where);
                    $prod['distributorRecs'] = $this->selectRecords('partvariation', $where);
					//echo $this->db->last_query();
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
			$record['products']['coupons'] = $this->checkForCoupons($id);
			if(!is_array($record['products']['coupons']))
				unset($record['products']['coupons']);
			
			
			$this->db->select('user_type');
			$this->db->from('user');
			$this->db->where('id',$record['user_id']);
			$user_type_query = $this->db->get();
			$user_type_res = $user_type_query->row();

			$record['user_type'] = "guest";
			if( !empty($user_type_res) ){
				$record['user_type'] = $user_type_res->user_type;
			}
			
		}
		// echo '<pre>';
		// print_r($record);
		// echo '</pre>';exit;
		return $record;
	}
	
	private function creditCardLast4($encryptedNumber)
	{
		$this->load->library('encrypt');
		$encryptedNumber = 'VzVZNgY0CjYDalI3BGALblBkAW4KZFFnAWBWMgNn';
		$decodedNumber = $this->encrypt->decode($encryptedNumber);
		$last4 = substr($decodedNumber, -4);
		if(is_numeric($last4))
			return $last4;
		else
			return 'XXXX';
	}
	
	private function checkForCoupons($id)
	{
		$query = $this->db->query("Select 'COUPON' as partnumber_id, substr(product_sku, 8)  as name, price as sale, qty as qty, 'COUPON' as partnumber, '' as question, '' as answer from order_product where product_sku like 'coupon_%' and order_id = ?", array($id));
		$list = $query->result_array();
		return count($list) > 0 ? $list[0] : null;

		$this->db->select('couponCode');
		$coupons = $this->selectRecords('coupon');
		if($coupons)
		{
			$i = 0;
			$ttl = count($coupons);
			$arr = array();
			foreach($coupons as $coupon)
			{
				$arr[] = "coupon_" . $coupon['couponCode'];
			}
			$this->db->where_in("product_sku", $arr);
		} else {
			return null;
		}
		$this->db->where('order_id = '.$id);
		$this->db->select("'COUPON' AS partnumber_id, product_sku AS name, price AS sale, qty AS qty, 'COUPON' AS partnumber, '' AS question, '' AS answer", FALSE);
		$couponProducts = $this->selectRecord('order_product');

        // You have to flag these as shipped. Otherwise, it will get stupid.
        $this->db->query("Update order_product set status = 'Shipped' where product_sku like 'coupon_%' and order_id = ?", array($id));
		return $couponProducts;
	}
	
	public function addProductToOrder($partNumber, $orderId, $qty)
	{
		$where = array('order_id' => $orderId, 'product_sku' => $partNumber);
		if($this->recordExists('order_product', $where))
		{
			$where = array('partnumber' => $partNumber);
			$partRec = $this->selectRecord('partnumber', $where);
			$data = array('order_id' => $orderId, 'product_sku' => $partNumber, 'price' => $partRec['price'], 'qty' => $qty);
			return $this->createRecord('order_product', $data, FALSE);
		}
	}
	
	public function getDistributors()
	{
		$ddArr= array();
		$this->db->select('distributor_id, name');
		$distributors = $this->selectRecords('distributor');
		if($distributors)
		{
			foreach($distributors as $dist)
			{
				$ddArr[$dist['distributor_id']] = $dist['name'];
			}
		}
		return $ddArr;
	}
	
	public function updateOrderProductsByOrderId($orderId, $products)
	{
		if(is_array($products))
		{
			foreach($products as $product_sku => $product)
			{
				$where = array('order_id' => $orderId, 'product_sku' => $product_sku);
				if($this->recordExists('order_product', $where))
					$this->updateRecord('order_product', $product, $where, FALSE);
				else
					$this->createRecord('order_product', $product, FALSE);
			}
		}
	}
	
	public function getProductsByOrderId($orderId)
	{
		$where = array('order_id' => $orderId);
		$products = $this->selectRecords('order_product', $where);
		return $products;
	}
	
	public function updateStatus($orderId, $status, $notes = NULL)
	{
		$data['order_id'] = $orderId;
		$data['status'] = $status;
		$data['datetime'] = time();
		$data['userId'] = @$_SESSION['userRecord']['id'];
		$data['notes'] = $notes;
		$this->createRecord('order_status', $data, FALSE);
	}
	
	public function updateDealerInventory( $arr ) {
		foreach( $arr as $k => $v ) {
			$where = array('partvariation_id' => $v['partnumber']);
			$partvariation = $this->selectRecord('partdealervariation', $where);
			$where = array('partvariation_id' => $v['partnumber']);
			$ar = array('quantity_available' => $partvariation['quantity_available']-$v['quantity']);
			$this->updateRecord('partdealervariation', $ar, $where, FALSE);
		}
	}
	
	public function updateStockOnOrder( $orderId, $data ) {
		foreach( $data as $k => $dt ) {
			$where = array('order_id' => $orderId, 'product_sku' => $k);
			$this->updateRecord('order_product', $dt, $where, FALSE);
		}
	}
	
	public function getPaymentInfo($id)
	{
		$where = array('id' => $id);
		$record = $this->selectRecord('cf_user_billing_info', $where);
		return $record;
	}
	
	public function updateProductStatus($orderId, $productSKU, $status, $notes = NULL)
	{
		$where = array('order_id' => $orderId, 'product_sku' =>$productSKU);
		$product = array('status' => $status, 'notes' => $notes);
		$this->updateRecord('order_product', $product, $where, FALSE);
	}
}
