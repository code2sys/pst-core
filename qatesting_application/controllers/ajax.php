<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Ajax extends Master_Controller {
	
	 function __construct()
	{
		parent::__construct();	
		//$this->output->enable_profiler(TRUE);
	}
	
	function alpha_dash_space($str)
	{
	    return ( ! preg_match("/^([-a-z_ ])+$/i", $str)) ? FALSE : TRUE;
	}
	
	private function validateUpdateGarage()
	{
	  	$this->load->library('form_validation');
	  	$this->form_validation->set_rules('machine', 'Machine', 'numeric|xss_clean');
	  	$this->form_validation->set_rules('make', 'Make', 'required|numeric|xss_clean');
	  	$this->form_validation->set_rules('model', 'Model', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('year', 'Year', 'required|numeric|xss_clean');
	  	return $this->form_validation->run();
	}
	
	private function validateEmail()
	{
	  	$this->load->library('form_validation');
	  	$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
	  	return $this->form_validation->run();
	}
	
	private function validateReview()
	{
		$this->load->library('form_validation');
	  	$this->form_validation->set_rules('review', 'Review', 'required|xss_clean');
	  	$this->form_validation->set_rules('rating', 'Rating', 'required|xss_clean');
	  	$this->form_validation->set_rules('part_id', 'Part Id', 'required|xss_clean');
	  	return $this->form_validation->run();
	}
	
	private function validateBase()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ajax', 'Ajax', 'required|xss_clean');
		$this->form_validation->set_rules('machineId', 'MachineId', 'numeric|xss_clean');
		$this->form_validation->set_rules('makeId', 'MakeId', 'numeric|xss_clean');
		$this->form_validation->set_rules('modelId', 'ModelId', 'numeric|xss_clean');
		$this->form_validation->set_rules('partId', 'PartId', 'numeric|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateSearch()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ajax', 'Ajax', 'required|xss_clean');
		$this->form_validation->set_rules('section', 'Section', 'required|xss_clean');
		$this->form_validation->set_rules('name', 'Name', 'xss_clean');
		$this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validPartNumber()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('partnumber', 'Part Id', 'required|exists[partnumber.partnumber]|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validPartId()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('partId', 'Part Id', 'required|exists[part.part_id]|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateOrder()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('orderId', 'Order Id', 'required|exists[order.id]|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateOrderSave()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('order_id', 'Order ID', 'xss_clean');
		$country = $this->input->post('country');
		if(is_array($country))
		{
			foreach($country as $id => $data)
		    {
		  		$this->form_validation->set_rules('first_name['.$id.']', 'First Name', 'xss_clean');
		  		$this->form_validation->set_rules('last_name['.$id.']', 'Last Name', 'xss_clean');
		  		$this->form_validation->set_rules('email['.$id.']', 'Email', 'xss_clean');
		  		$this->form_validation->set_rules('phone['.$id.']', 'Phone', 'xss_clean');
		  		$this->form_validation->set_rules('fax['.$id.']', 'Fax', 'xss_clean');
		  		$this->form_validation->set_rules('street_address['.$id.']', 'Street Address', 'xss_clean');
		  		$this->form_validation->set_rules('address_2['.$id.']', 'Apt/Suite', 'xss_clean');
		  		$this->form_validation->set_rules('city['.$id.']', 'City', 'xss_clean');
		  		$this->form_validation->set_rules('state['.$id.']', 'State', 'xss_clean');
		  		$this->form_validation->set_rules('zip['.$id.']', 'Zip', 'xss_clean');
		  		$this->form_validation->set_rules('country['.$id.']', 'Country', 'xss_clean');
		  		$this->form_validation->set_rules('company['.$id.']', 'Company', 'xss_clean');
		    }
	    }
		$this->form_validation->set_rules('cc', 'Credit Card Number', 'xss_clean');
	  	$this->form_validation->set_rules('cc_first_name', 'First Name On Card', 'xss_clean');
	  	$this->form_validation->set_rules('cc_last_name', 'Last Name On Card', 'xss_clean');
	  	$this->form_validation->set_rules('exp_date_mn', 'Exp. Date Month', 'numeric|max_length[2]|xss_clean');
	    $this->form_validation->set_rules('exp_date_yr', 'Exp. Date Year', 'numeric|max_length[2]|xss_clean');
	  	$this->form_validation->set_rules('cvc', 'CVC', 'numeric|xss_clean');	

		return $this->form_validation->run();
	}
	
	private function validateTrackingNumber()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ship_tracking_code', 'Tracking Number', 'alpha_numeric|xss_clean');
		$this->form_validation->set_rules('id', 'Order Id', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('carrier', 'Shipping Carrier', 'xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateTrackingKey()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('key', 'Key', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('id', 'Order Id', 'required|numeric|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateOrderStatus()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('status', 'Status', 'required|callback__alpha_dash_space|xss_clean');
		$this->form_validation->set_rules('orderId', 'Order Id', 'required|numeric|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateProductOrderStatus()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('status', 'Status', 'required|callback__alpha_dash_space|xss_clean');
		$this->form_validation->set_rules('orderId', 'Order Id', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('products', 'Products', 'required|xss_clean');
		return $this->form_validation->run();
	}
	
	public function getPriceByPartNumber()
	{
		if($this->validPartNumber() === TRUE)
		{
			$price = $this->account_m->getPriceByPartNumber($this->input->post('partnumber'));
			echo json_encode($price);
		}
	}
	
	public function getStockByPartId()
	{
		if($this->validPartId() === TRUE)
		{
			$partNumberRec = $this->account_m->getStockByPartId($this->input->post('partId'));
			echo json_encode($partNumberRec);
		}
	}
	
	public function update_garage()
	{
		$this->load->model('account_m');
		if($this->validateUpdateGarage() === TRUE)
		{
			$rideRec = $this->account_m->buildRideName($this->input->post());
			$_SESSION['garage'] = $this->account_m->updateGarage($rideRec, @$_SESSION['garage']);
		}
		$_SESSION['activeMachine'] = $rideRec;
		$currentlocation = str_replace('/qatesting/index.php', '', $_POST['url']);
		//echo $currentlocation;
		if(  strpos($currentlocation, 'productlist') !== FALSE )
			$_SESSION['internal'] = TRUE;
		redirect($currentlocation);
	}
	
	public function delete_from_garage()
	{
		unset($_SESSION['garage'][$_POST['garageLabel']]);
		if(!empty($_SESSION['garage']))
		{
			$activeRide = FALSE; // Check to see if we have deleted active ride
			$i = 0;
			$firstLabel = 0;
			foreach($_SESSION['garage'] as $label => &$ride)
			{
				$i++;
				if($i == 1) // Set up to pull first ride out if none are active
					$firstLabel = $label;
				if($ride['active'] == 1) // Pull this ride out and submit seperatly to work properly with the Update Garage function
				{
					$rideRec = $ride;
					unset($_SESSION['garage'][$label] );

					$activeRide = TRUE;
				}
			}
			if(!$activeRide) // There are no active rides but still items in garage
			{
				$_SESSION['garage'][$firstLabel]['active'] = 1; // Set the first ride to active
				$rideRec = $_SESSION['garage'][$firstLabel];
				unset($_SESSION['garage'][$firstLabel] );
			}
		}
		$_SESSION['garage'] = $this->account_m->updateGarage(@$rideRec, @$_SESSION['garage']);
	}
	
	public function change_active_garage()
	{	
		if(@$_SESSION['garage'])
		{
			foreach($_SESSION['garage'] as $label => &$ride)
			{
				if($label == $_POST['garageLabel'])
				{
					$rideRec = $ride;
					unset($_SESSION['garage'][$label] );
				}
				else
					$ride['active'] = 0;
			}
			$_SESSION['garage'] = $this->account_m->updateGarage($rideRec, @$_SESSION['garage']);
			$_SESSION['activeMachine'] = $rideRec;
		}
	}
	
	public function getMake()
	{
		$makes = FALSE;
		if($this->validateBase() === TRUE)
		{
			$this->load->model('parts_m');
			$makes = $this->parts_m->getMakesDd($this->input->post('machineId'), @$this->input->post('partId'));
		}
		echo json_encode($makes);
	}
	
	public function getModel()
	{
		$makes = FALSE;
		if($this->validateBase() === TRUE)
		{
			$this->load->model('parts_m');
			$makes = $this->parts_m->getModelsDd($this->input->post('makeId'), @$this->input->post('partId'));
		}
		echo json_encode($makes);
	}
	
	public function getYear()
	{
		$makes = FALSE;
		if($this->validateBase() === TRUE)
		{
			$this->load->model('parts_m');
			$makes = $this->parts_m->getYearsDd($this->input->post('modelId'), @$this->input->post('partId'));
		}
		echo json_encode($makes);
	}
	
	public function getActiveSection()
	{
		$sections = array('description' => 1, 'reviews' => 2, 'fitment' => 3);
		if(@$sections[@$_POST['activeSection']] && is_numeric($_POST['part_id']))
		{
			$post = $_POST;
			$this->load->model('parts_m');
			switch($sections[@$_POST['activeSection']])
			{
				case 1:
					$product = $this->parts_m->getProduct($post['part_id'], NULL);
					$block = $product['description'];
					break;
				case 2:
					$this->_mainData['reviews'] = $this->parts_m->getReviews($post['part_id']);
					$this->_mainData['part_id'] = $_POST['part_id'];
					$block = $this->load->view('widgets/reviews_v', $this->_mainData, TRUE);
					break;
				case 3:
					$this->_mainData['validMachines'] = $this->parts_m->validMachines($post['part_id']);
					if(@$this->_mainData['validMachines'])
					{
						foreach($this->_mainData['validMachines'] as &$mac)
						{
							$mac['record'] = $this->parts_m->buildRideName($mac);
						}
					}
					$block = $this->load->view('widgets/fitment_v', $this->_mainData, TRUE);
					break;
			}
			echo $block;
					
		}
	}
	
	public function updateNewsletterList()
	{
		if($this->validateEmail() === TRUE)
		{
			$this->load->model('account_m');
			$this->account_m->addNewsletterEmail($this->input->post('email'), @$_SESSION['userRecord']['id']);
		}
	}
	
	public function createReview()
	{
		if($this->validateReview() === TRUE)
		{
			$this->load->model('account_m');
			$this->account_m->addReview($this->input->post(), @$_SESSION['userRecord']['id']);
		}
	}
	
	public function order_save()
	{
		if($this->validateOrderSave() === TRUE)
		{
			$post = $this->input->post();
			$contactInfo[0]['first_name'] = $post['first_name'][0]; 
			$contactInfo[0]['last_name'] = $post['last_name'][0]; 
			$contactInfo[0]['street_address'] = $post['street_address'][0]; 
			$contactInfo[0]['address_2'] = $post['address_2'][0]; 
			$contactInfo[0]['city'] = $post['city'][0]; 
			$contactInfo[0]['state'] = $post['state'][0];
			$contactInfo[0]['zip'] = $post['zip'][0];
			$contactInfo[0]['email'] = $post['email'][0];
			$contactInfo[0]['phone'] = $post['phone'][0];
			$contactInfo[0]['country'] = $post['country'][0];
			$contactInfo[0]['company'] = $post['company'][0];
			$contactInfo[1]['first_name'] = $post['first_name'][1]; 
			$contactInfo[1]['last_name'] = $post['last_name'][1]; 
			$contactInfo[1]['street_address'] = $post['street_address'][1]; 
			$contactInfo[1]['address_2'] = $post['address_2'][1]; 
			$contactInfo[1]['city'] = $post['city'][1]; 
			$contactInfo[1]['state'] = $post['state'][1];
			$contactInfo[1]['zip'] = $post['zip'][1];
			$contactInfo[1]['email'] = $post['email'][1];
			$contactInfo[1]['phone'] = $post['phone'][1];
			$contactInfo[1]['country'] = $post['country'][1];
			$contactInfo[1]['company'] = $post['company'][1];
			$order['special_instr'] = $post['special_instr'];	
			$order['order_id'] = $post['order_id'];
			$order['billing_id'] = $this->account_m->updateContact($contactInfo[0], 'billing');
			$order['shipping_id'] = $this->account_m->updateContact($contactInfo[1], 'shipping');
			$orderId = $this->admin_m->recordOrderCreation($order);
			$products = $this->setupProducts($post, $subtotal);
			$this->load->model('order_m');
			$this->order_m->updateOrderProductsByOrderId($orderId, $products);
			echo $orderId;
		}
		else echo validation_errors();
	}
	
	public function setupProducts($post, &$subtotal)
	{
		$products = array();
		if(is_array($post['product_sku']))
		{
			foreach($post['product_sku'] as $part)
			{
				$distributorArr = array('id' => $post['distributor_id'][$part], 'qty' => $post['distributor_qty'][$part], 'part_number' => $post['distributor_partnumber'][$part]);
				$distributorStr = json_encode($distributorArr);
				$products[$part]['distributor'] = $distributorStr;
			}
		}
		return $products;
	}
	
	public function changeOrderStatus()
	{
		if($this->validateOrderStatus() === TRUE)
		{
			$post = $this->input->post();
			$this->load->model('order_m');
			$this->order_m->updateStatus($post['orderId'], $post['status'], 'Ajax Update');
		}
	}
	
	public function changeProductOrderStatus()
	{
		if($this->validateProductOrderStatus() === TRUE)
		{
			$post = $this->input->post();
			$this->load->model('order_m');
			foreach($post['products'] as $product)
				$this->order_m->updateProductStatus($post['orderId'], $product, $post['status'], 'Ajax Update');
						
			$products = $this->order_m->getProductsByOrderId($post['orderId']);
			// Get status for all Products
			$orderStatus = $this->calculateOrderStatus($products);
			$this->order_m->updateStatus($post['orderId'], $orderStatus, 'Ajax Update');
		}
	}
	
	public function calculateOrderStatus($products)
	{
		$count = count($products);
		$orderStatus = 'Approved';
		$rtpu = 0;
		$complete = 0;
		$returned = 0;
		$refunded = 0;
		foreach($products as $product)
		{
			switch($product['status'])
			{
				case 'Back Order':
					$orderStatus = 'Back Order';
					break;
				case 'Shipped':
					$orderStatus = 'Partially Shipped';
					$complete++;
					$rtpu++;
					if($complete == $count)
						$orderStatus = 'Shipped/Complete';
					break;
				case 'Ready to Pick Up':
					$rtpu++;
					if($rtpu == $count)
						$orderStatus = 'Ready to Pick Up';
					break;
				case 'Returned':
					$complete++;
					$rtpu++;
					$returned++;
					if($returned == $count)
						$orderStatus = 'Returned';
					break;
				case 'Refunded':
					$complete++;
					$rtpu++;
					$refunded++;
					if($refunded == $count)
						$orderStatus = 'Refunded';
					break;
					
			}
			if($orderStatus == 'Back Order')
				break;
		}
		echo $refunded . ' | ' . $count;
		return $orderStatus;
	}
	
	public function email_tracking()
	{
		if($this->validateTrackingNumber() === TRUE)
		{
			$post = $this->input->post();
			// Send email
			if(!empty($post['ship_tracking_code']))
				$this->admin_m->updateOrderTrackingNumber($post);
			$filename = $this->order_pdf($post['id'], 'F');
			$i = 0;
			// Verify file creation process complete and delay up to 3 seconds if not.
			$fileExists = file_exists($filename);
			while(($i < 3) && (!$filename))
			{
				$fileExists = file_exists($filename);
				sleep(1);
				$i++;
			}
			if($fileExists)
			{
				$this->config->load('sitesettings');
				$emailRec = $this->account_m->getOrderEmail($post['id']);
				if(@$post['carrier'])
				{
					switch($post['carrier'])
					{
						case 'FedEx':
							$trackingURL = 'https://www.fedex.com/fedextrack/?tracknumbers='.$post['ship_tracking_code'];
							break;
						case 'UPS':
							$trackingURL = 'http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&button_index=201&loc=en_US&InquiryNumber1='.$post['ship_tracking_code'].'+&AgreeToTermsAndConditions=yes&track.y=8';
							break;
						case 'USPS':
							$trackingURL = 'https://tools.usps.com/go/TrackConfirmAction.action?tLabels='.$post['ship_tracking_code'];
							break;
					}
				}
				$mailData = array('fromEmailAddress' => $this->config->item('fromEmailAddress'),
												'fromName' => $this->config->item('fromEmailName'),
												'replyToEmailAddress' => $this->config->item('replyToEmailAddress'),
												'replyToName' => $this->config->item('replyToName'),
												'toEmailAddress' => $emailRec['email'],
												'subject' => WEBSITE_NAME . ' Order Confirmation');
				$mailTemplateData = array('assets' => $this->_mainData['assets'], 
																'baseURL' => $this->_mainData['baseURL']);
				if(isset($trackingURL))
					$mailTemplateData['trackingURL'] = $trackingURL;
				if(@$post['carrier'])
					$mailTemplateData['carrier'] = $post['carrier'];
				if(@$post['ship_tracking_code'])
					$mailTemplateData['ship_tracking_code'] = $post['ship_tracking_code'];
				// Generate the Password Verification Email to the User
				$this->load->model('mail_gen_m');  
				$ret = $this->mail_gen_m->generateFromView($mailData, 
																			$mailTemplateData, 
																			'email/orderConf_html_v',
																			'email/orderConf_text_v',
																			$filename);
				if($ret)
					echo "success";
				else
					echo "Our system was unable to completely your request at this time.  Please try again in a few minutes.";
	  		}
		}
		else
		{
			echo validation_errors();
		}
	}

	public function remove_tracking()
	{
		if($this->validateTrackingKey() == TRUE)
		{
			$this->admin_m->removeTrackingFromOrder($this->input->post());
		}
	}
	
	public function order_product_search()
	{
		if($this->validateOrder() === TRUE)
		{
			$_SESSION['OrderProductSearch'] = $this->input->post('orderId');
		}
	}
	
	  public function get_contact_info($contactId)
	  {
	    if(is_numeric($contactId))
	    {
	      $record = json_encode($this->account_m->getContactInfo($contactId));
	      echo $record;
	    }
	  }
	  
	  public function setSearch()
	  {
		  if($this->validateSearch() === TRUE)
		  {
		  		if($this->input->post('section') == 'question')
			  		$_SESSION['search']['question'][$this->input->post('id')] = $this->input->post('name');
			  	elseif($this->input->post('section') == 'search')
			  	{
				  	unset($_SESSION['search']);
			  		$_SESSION['search']['search'] = explode(' ', $this->input->post('name'));
			  	}
			    elseif(@$this->input->post('name'))
			    	$_SESSION['search'][$this->input->post('section')] = array('id' => $this->input->post('id'), 'name' => $this->input->post('name')) ;
			    else
			  		$_SESSION['search'][$this->input->post('section')] = $this->input->post('id');
			  	if($this->input->post('section') == 'category')
			  	{
			  		unset($_SESSION['search']['brand']);
			  		unset($_SESSION['search']['question']);
			  	}
		  }
		  echo $this->returnURL();
	  }
	  
	  public function removeSearch()
	  {
		  if($this->validateSearch() === TRUE)
		  {
		  		if(($this->input->post('section') == 'question') || ($this->input->post('section') == 'search'))
		  		{
			  		unset($_SESSION['search'][$this->input->post('section')][$this->input->post('id')]);
			  		if(empty($_SESSION['search'][$this->input->post('section')]))	
			  			unset($_SESSION['search'][$this->input->post('section')]);
			  	}
		  		else
			  		unset($_SESSION['search'][$this->input->post('section')]);
		  }
		   echo $this->returnURL();		  
	  }
	  
		public function tag_creating($url) 
		{
			$url = str_replace(array(' - ', ' '), '-', $url);			
			$url = preg_replace('~[^\\pL0-9_-]+~u', '', $url);
			$url = trim($url, "-");
			$url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
			$url = strtolower($url);
			$url = preg_replace('~[^-a-z0-9_-]+~', '', $url);
		   return $url;
		}
		
		public function createURL()
		{
			$originalSession = @$_SESSION['search'];
			if($this->validateSearch() === TRUE)
			{
					if($this->input->post('section') == 'question')
			  		$_SESSION['search']['question'][$this->input->post('id')] = $this->input->post('name');
			  	elseif($this->input->post('section') == 'search')
			  		$_SESSION['search']['search'] = explode(' ', $this->input->post('name'));
			    elseif(@$this->input->post('name'))
			    	$_SESSION['search'][$this->input->post('section')] = array('id' => $this->input->post('id'), 'name' => $this->input->post('name')) ;
			    else
			  		$_SESSION['search'][$this->input->post('section')] = $this->input->post('id');
			  	if($this->input->post('section') == 'category')
			  	{
			  		unset($_SESSION['search']['brand']);
			  		unset($_SESSION['search']['question']);
			  	}
			}
			echo $this->returnURL();
			$_SESSION['search'] = $originalSession;
		}

	  
	  private function  returnURL()
	  {
	  		$returnURL = '/';
	  		if(@$_SESSION['search']['category'])
	  		{
	  			$this->load->model('parts_m');
	  			$categories = $this->parts_m->getParentCategores($_SESSION['search']['category']);
	  			if(is_array($categories))
	  			{
	  				foreach($categories as $cat)
	  					$returnURL .= $this->tag_creating($cat).'_'; 
	  			}
	  		}
	  		if(@$_SESSION['search']['brand'])
	  		{
		  		if($_SESSION['search']['brand']['name'] != 'brand')
	  				$returnURL .= $this->tag_creating($_SESSION['search']['brand']['name']).'_';
	  		}
	  		if(@$_SESSION['search']['question'])
	  		{
		  		foreach($_SESSION['search']['question'] as $key => $quest)
		  		{
			  		$returnURL .= $this->tag_creating($quest).'_';
		  		}
	  		}
	  		if(@$_SESSION['search']['search'])
	  		{	
		  		$returnURL .= 'search_';
		  		foreach($_SESSION['search']['search'] as $search)
		  		{
		  			$returnURL .= $search.'_';
		  		}
	  		}
	  		$_SESSION['internal'] = TRUE;
		  	return substr($returnURL, 0, -1);
	  }
	  
	public function order_pdf($orderNum, $output = 'D')
	{
		// set up PDF Helper files
			$this->load->helper('fpdf_view');
			$parameters = array();	
		pdf_init('reporting/poreport.php');
		
		// Send Variables to PDF
		//update process date and process user info
		$parameters['orders'] = $this->account_m->getPDFOrder($orderNum);
		$fileName = 'OrderReport_'.$orderNum.'.pdf';
		if($output == 'F')
		  $fileName = $this->config->item('attachments').'OrderReport_'.$orderNum.'.pdf';
		// Create PDF
		$this->PDF->setParametersArray($parameters);
		$this->PDF->runReport();
		$this->PDF->Output($fileName, $output);
		return $fileName;
	}

}