<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Checkout extends Master_Controller {

	function __construct()
	{
		parent::__construct();
		@session_start();
		$this->load->model('checkout_m');
		//$this->output->enable_profiler(TRUE);
		$this->load->model('admin_m');
		$this->_mainData['accountAddress'] = $this->admin_m->getAdminAddress();
		$this->load->library("braintree_lib");
	}

/******************************************** VALIDATION FUNCTIONS ***********************************/
  
	function _notZero($value, $id)
	{
		if(!$value)
		{
			if($id == 0)
				$this->form_validation->set_message('_notZero', 'Please choose a State/Province for Billing.');  
			else
				$this->form_validation->set_message('_notZero', 'Please choose a State/Province for Shipping.');  
			return FALSE;
		}
		return TRUE;
	}

	function _validateCreditcard_number($card_number)
	{

		// Get the first digit
	    $firstnumber = substr($card_number, 0, 1);
	    // Make sure it is the correct amount of digits. Account for dashes being present.
	    switch ($firstnumber)
	    {
	        case 3:
	            if (!preg_match('/^3\d{3}[ \-]?\d{6}[ \-]?\d{5}$/', $card_number))
	            {
	            	if($this->config->item('paymentTesting'))
	            	{
		            	if($card_number == '370000000000002')
		            		return TRUE;
	            	}
	            	
	            	$this->form_validation->set_message('_validateCreditcard_number', 'This is not a valid American Express card number.');  
					return FALSE;
	            }
	            break;
	        case 4:
	            if (!preg_match('/^4\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $card_number))
	            {
	            	if($this->config->item('paymentTesting'))
	            	{
		            	if(($card_number == '4007000000027') || ($card_number == '4012888818888'))
		            		return TRUE;
	            	}
	                $this->form_validation->set_message('_validateCreditcard_number', 'This is not a valid Visa card number.');  
					return FALSE;
	            }
	            break;
	        case 5:
	            if (!preg_match('/^5\d{3}[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $card_number))
	            {
	            	if($this->config->item('paymentTesting'))
	            	{
		            	if(($card_number == '5555555555554444') || ($card_number == '5105105105105100'))
		            		return TRUE;
	            	}
	            	
	                $this->form_validation->set_message('_validateCreditcard_number', 'This is not a valid MasterCard card number.');  
					return FALSE;
	            }
	            break;
	        case 6:
	            if (!preg_match('/^6011[ \-]?\d{4}[ \-]?\d{4}[ \-]?\d{4}$/', $card_number))
	            {
	            	if($this->config->item('paymentTesting'))
	            	{
		            	if($card_number == '6011000000000012')
		            		return TRUE;
	            	}
	            	
	            	$this->form_validation->set_message('_validateCreditcard_number', 'This is not a valid Discover card number.');  
					return FALSE;
	            }
	            break;
	        default:
	        	$this->form_validation->set_message('_validateCreditcard_number', 'This is not a valid credit card number.');  
				return FALSE;
				break;
	    }
	    
        $card_number = str_replace('-', '', $card_number);
	    $map = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
	                0, 2, 4, 6, 8, 1, 3, 5, 7, 9);
	    $sum = 0;
	    $last = strlen($card_number) - 1;
	    for ($i = 0; $i <= $last; $i++)
	    {
	        $sum += $map[$card_number[$last - $i] + ($i & 1) * 10];
	    }
	    if ($sum % 10 != 0)
	    {
	       $this->form_validation->set_message('_validateCreditcard_number', 'This is not a valid credit card number.');  
			return FALSE;
	    }
	    
	    return TRUE;
	}
	
	function _validateCreditCardExpirationDate($year)
	{
	
		$month = $this->input->post('exp_date_mn');
	    if (!preg_match('/^\d{1,2}$/', $month))
	    {
	    	$this->form_validation->set_message('_validateCreditCardExpirationDate', 'This is not a valid Expiration Date.');  
			return FALSE;
	    }
	    else if (!preg_match('/^\d{2}$/', $year))
	    {
	       $this->form_validation->set_message('_validateCreditCardExpirationDate', 'This is not a valid Expiration Date.');  
			return FALSE;
	    }
	    else if ($year < date("y"))
	    {
	    	$this->form_validation->set_message('_validateCreditCardExpirationDate', 'Your card has already expired.');  
			return FALSE;
	    }
	    else if ($month < date("m") && $year == date("y"))
	    {
	       $this->form_validation->set_message('_validateCreditCardExpirationDate', 'Your card has already expired.');  
			return FALSE;
	    }
	    return TRUE;
	}
	
	function _validateCVV($cvv)
	{

		$cardNumber = $this->input->post('cc');
	    // Get the first number of the credit card so we know how many digits to look for
	    $firstnumber = (int) substr($cardNumber, 0, 1);
	    if ($firstnumber === 3)
	    {
	        if (!preg_match("/^\d{4}$/", $cvv))
	        {
	        	$this->form_validation->set_message('_validateCVV', 'Your CVV number is incorrect.');  
				return FALSE;
	        }
	    }
	    else if (!preg_match("/^\d{3}$/", $cvv))
	    {
	        $this->form_validation->set_message('_validateCVV', 'Your CVV number is incorrect.');  
			return FALSE;
	    }
	    return TRUE;
	}
  
  	private function validateClientInfo()
	{
		$this->load->library('form_validation');
		$country = $this->input->post('country');
		if(@$country)
		{
			foreach($country as $id => $data)
		    {
		  		$this->form_validation->set_rules('first_name['.$id.']', 'First Name', 'required|xss_clean');
		  		$this->form_validation->set_rules('last_name['.$id.']', 'Last Name', 'required|xss_clean');
		  		$this->form_validation->set_rules('email['.$id.']', 'Email', 'required|xss_clean');
		  		$this->form_validation->set_rules('phone['.$id.']', 'Phone', 'required|xss_clean');
		  		$this->form_validation->set_rules('fax['.$id.']', 'Fax', 'xss_clean');
		  		$this->form_validation->set_rules('street_address['.$id.']', 'Street Address', 'required|xss_clean');
		  		$this->form_validation->set_rules('address_2['.$id.']', 'Apt/Suite', 'xss_clean');
		  		$this->form_validation->set_rules('city['.$id.']', 'City', 'required|xss_clean');
		  		$this->form_validation->set_rules('state['.$id.']', 'State', 'required|callback__notZero['.$id.']|xss_clean');
		  		$this->form_validation->set_rules('zip['.$id.']', 'Zip', 'required|xss_clean');
		  		$this->form_validation->set_rules('country['.$id.']', 'Country', 'xss_clean');
		  		$this->form_validation->set_rules('company['.$id.']', 'Company', 'xss_clean');
		    }
	    }
		$this->form_validation->set_rules('special_instr', 'Special Instructions', 'xss_clean');
		$this->form_validation->set_rules('calculate_shipping', 'Calculate', 'xss_clean');
		$this->form_validation->set_rules('shipping_id', 'Shipping Id', 'xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateCCInfoPaypal() {
	  	$this->load->library('form_validation');
	  	$this->form_validation->set_rules('shippingValue', 'Shipping Value', 'required|xss_clean');
	  	return $this->form_validation->run();
	}
	private function validateCCInfo()
	{
	  	$this->load->library('form_validation');
	  	$this->form_validation->set_rules('shippingValue', 'Shipping Value', 'required|xss_clean');
	  	$this->form_validation->set_rules('cc', 'Credit Card Number', 'required|callback__validateCreditcard_number|xss_clean');
	  	$this->form_validation->set_rules('cc_first_name', 'First Name On Card', 'required|xss_clean');
	  	$this->form_validation->set_rules('cc_last_name', 'Last Name On Card', 'required|xss_clean');
	  	$this->form_validation->set_rules('expiration', 'Exp. Date', 'required|number|max_length[2]|xss_clean');
	    $this->form_validation->set_rules('exp_date_yr', 'Exp. Date Year', 'required|numeric|max_length[2]|callback__validateCreditCardExpirationDate|xss_clean');
	  	$this->form_validation->set_rules('cvc', 'CVC', 'required|numeric|callback__validateCVV|xss_clean');
	  	return $this->form_validation->run();
	}
	
	private function validateCCInfoUpdate()
	{

	  	$this->load->library('form_validation');
	  	$this->form_validation->set_rules('shippingValue', 'Shipping Value', 'required|xss_clean');
	  	// $this->form_validation->set_rules('credit-card-number', 'Credit Card Number', 'required|callback__validateCreditcard_number|xss_clean');
	  	// $this->form_validation->set_rules('exp_date_mn', 'Exp. Date Month', 'required|max_length[7]|xss_clean');
	    // //$this->form_validation->set_rules('exp_date_yr', 'Exp. Date Year', 'required|numeric|max_length[2]|callback__validateCreditCardExpirationDate|xss_clean');
	  	// $this->form_validation->set_rules('cvv', 'CVC', 'required|numeric|callback__validateCVV|xss_clean');

        return $this->form_validation->run();
	}
	
	private function validatePaypalForm()
	{
	  	$this->load->library('form_validation');
	  	$this->form_validation->set_rules('payment_method_nonce', 'Invalid Form Details', 'required|xss_clean');
	  	return $this->form_validation->run();
	}
	
	private function validateEditUser()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone', 'required|xss_clean');
		$this->form_validation->set_rules('fax', 'Fax', 'xss_clean');
		$this->form_validation->set_rules('street_address', 'Street Address', 'required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('address_2', 'Apt/Suite', 'max_length[40]|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'required|xss_clean');
		$this->form_validation->set_rules('state', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('zip', 'Zip', 'required|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'matches[conf_password]|xss_clean');
		$this->form_validation->set_rules('conf_password', 'Password', 'xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateEditAddress()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone', 'required|xss_clean');
		$this->form_validation->set_rules('fax', 'Fax', 'xss_clean');
		$this->form_validation->set_rules('street_address', 'Street Address', 'required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('address_2', 'Apt/Suite', 'max_length[40]|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'required|xss_clean');
		$this->form_validation->set_rules('state', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('zip', 'Zip', 'required|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
		return $this->form_validation->run();
	}
	
	private function contactInfo()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone', 'required|xss_clean');
		$this->form_validation->set_rules('fax', 'Fax', 'xss_clean');
		$this->form_validation->set_rules('street_address', 'Street Address', 'required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('address_2', 'Apt/Suite', 'max_length[40]|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'required|xss_clean');
		$this->form_validation->set_rules('state', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('zip', 'Zip', 'required|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
		return $this->form_validation->run();
	}
	
  public function _uniqueUsername($username)
  {
	if(!$this->account_m->verifyUsername($username))
	  return TRUE;
	else
	{
	  $this->form_validation->set_message('_uniqueUsername', 'The username you selected is already in use.  Please select a different user name.');
	  return FALSE;
	}
  }
  
  public function _uniqueEmail($email)
  {
	if(!$this->account_m->verifyEmail($email))
	  return TRUE;
	else
	{
	  $this->form_validation->set_message('_uniqueEmail', 'The email address you selected is already in use. <a href="javascript:void(0);" onclick="forgotPassword();">If you have forgotten your password please click here.</a>');
	  return FALSE;
	}
  }
		
  public function validateRegistration()
  {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'required|callback__uniqueUsername|xss_clean');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'E-Mail', 'required|callback__uniqueEmail|valid_email|xss_clean');
		$this->form_validation->set_rules('conf_email', 'Confirm E-Mail', 'required|matches[email]|xss_clean');
		$this->form_validation->set_rules('encrypted_answer', 'Encrypted Answer', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|xss_clean');
		$this->form_validation->set_rules('conf_password', 'Confirm Password', 'required|matches[password]|xss_clean');
		$this->form_validation->set_rules('street_address', 'Address', 'required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('address_2', 'Address 2', 'max_length[40]|xss_clean');
		$this->form_validation->set_rules('city', 'City/Municipality', 'required|xss_clean');
		$this->form_validation->set_rules('state', 'State/Province', 'required|xss_clean');
		$this->form_validation->set_rules('zip', 'Zipcode/Postal Code', 'required|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone', 'required|xss_clean');
		$this->form_validation->set_rules('user_answer', 'Math Question', 'required|integer|callback__processCaptcha');
		return $this->form_validation->run();
  }

/************************************** PAGE AND CRITICAL PROCESSING FUNCTIONS *****************************************/
  	
	public function account()
	{
		//$user_id = @$_SESSION['guestUser']['id'];
		if( (ENVIRONMENT == 'production') && ( !isset($_SERVER['HTTPS'] ) ) && empty($_GET['u']) ){
			redirect($this->_mainData['s_baseURL'] . 'checkout/account');
		}
		if(!@$_SESSION['userRecord'] && !@$_SESSION['guestUser'])
		{
			redirect($this->_mainData['s_baseURL'] .'welcome/new_account');
		}
		
		// Main Page Info
		if($this->session->flashdata('errors'))
			$this->_mainData['validationErrors'] = $this->session->flashdata('errors');
		
		$this->_mainData['userRecord'] = (!empty($_SESSION['userRecord'])) ? $_SESSION['userRecord'] : $_SESSION['guestUser'];
		
		$this->_mainData['orders'] = $this->account_m->getOrders($this->_mainData['userRecord']['id'], FALSE, 5);
		// New Order Processing
		// Send Email with attachment Link
		if(@$_SESSION['orderNum'])
		{
			$this->load->model('order_m');
			$orderInfo = $this->order_m->getOrder($_SESSION['orderNum']);
			
			$totalRevenue = $orderInfo['sales_price'];
			$totalRevenue += $orderInfo['shipping'];
			$totalRevenue += $orderInfo['tax'];

            $store_name = $this->admin_m->getAdminShippingProfile();
            $fb_remarketing_pixel = $store_name["fb_remarketing_pixel"];
            $google_conversion_id = $store_name['google_conversion_id'];
            $google_conversion_label = $store_name['google_conversion_label'];

			$googleAdWordsScript = '<!-- Facebook Conversion Code for Checkout Complete -->
									<script>(function() {
									  var _fbq = window._fbq || (window._fbq = []);
									  if (!_fbq.loaded) {
									    var fbds = document.createElement(\'script\');
									    fbds.async = true;
									    fbds.src = \'//connect.facebook.net/en_US/fbds.js\';
									    var s = document.getElementsByTagName(\'script\')[0];
									    s.parentNode.insertBefore(fbds, s);
									    _fbq.loaded = true;
									    _fbq.push([\'addPixelId\', \'' . $fb_remarketing_pixel . '\']);
									  }
									})();
									window._fbq = window._fbq || [];
									window._fbq.push([\'track\', \'Purchase\', {\'value\':\''.@$totalRevenue.'\',\'currency\':\'USD\'}]);
									window._fbq.push([\'track\', \'PixelInitialized\', {}]);
									</script>
									<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=' . $fb_remarketing_pixel . '&amp;ev=PixelInitialized" /></noscript>
																													
									<script>
										ga(\'require\', \'ecommerce\');
										ga(\'ecommerce:addTransaction\', {
										  \'id\': \''.$_SESSION['orderNum'].'\',                     // Transaction ID. Required.
										  \'affiliation\': \'' . WEBSITE_NAME . '\',   // Affiliation or store name.
										  \'revenue\': \''.$totalRevenue.'\',               // Grand Total.
										  \'shipping\': \''.$orderInfo['shipping'].'\',                  // Shipping.
										  \'tax\': \''.$orderInfo['tax'].'\'                     // Tax.
										}); ';
										$trustedStore = '';
										$rating = '';
										if(is_array($orderInfo['products']))
										{
											foreach($orderInfo['products'] AS $products)
											{
												$googleAdWordsScript .= '
												ga(\'ecommerce:addItem\', {
											\'id\': \''.@$_SESSION['orderNum'].'\', 
											\'name\': \''.@$products['name'].'\', 
											\'sku\': \''.@$products['product_sku'].'\', 
											\'category\': \'Motocross\', // Category or variation.
											\'price\': \''.@$products['sale'].'\', 
											\'quantity\': \''.@$products['qty'].'\' 	});';
												$trustedStore .= '<span class="gts-item">
											    <span class="gts-i-name">'.@$products['name'].'</span>
											    <span class="gts-i-price">'.@$products['sale'].'</span>
											    <span class="gts-i-quantity">'.@$products['qty'].'</span>
											    <span class="gts-i-prodsearch-id">'.@$products['product_sku'].'</span>
											    <span class="gts-i-prodsearch-store-id">' . $google_conversion_id . '</span>
											    <span class="gts-i-prodsearch-country">US</span>
											    <span class="gts-i-prodsearch-language">en</span>
											  </span>';
																	
												$rating	.='\''.@$products['product_sku'].'\':\''.@$products['name'].'\',';
											}
											$rating = substr($rating, 0, -1);
										}
																
															$googleAdWordsScript .= 'ga(\'ecommerce:send\');</script>';
			
			
			$googleAdWordsScript .= '<!-- START Google Trusted Stores Order -->
<div id="gts-order" style="display:none;" translate="no"><!-- start order and merchant information -->
									  <span id="gts-o-id">'.$_SESSION['orderNum'].'</span>
									  <span id="gts-o-domain">' . STYLED_HOSTNAME . '</span>
									  <span id="gts-o-email">'.$orderInfo['email'].'</span>
									  <span id="gts-o-country">US</span>
									  <span id="gts-o-currency">USD</span>
									  <span id="gts-o-total">'.$orderInfo['sales_price'].'</span>
									  <span id="gts-o-discounts">0.00</span>
									  <span id="gts-o-shipping-total">'.$orderInfo['shipping'].'</span>
									  <span id="gts-o-tax-total">'.$orderInfo['tax'].'</span>
									  <span id="gts-o-est-ship-date">'.date('Y-m-d', strtotime('+1 days')).'</span>
									  <span id="gts-o-est-delivery-date">'.date('Y-m-d', strtotime('+3 days')).'</span>
									  <span id="gts-o-has-preorder">N</span>
									  <span id="gts-o-has-digital">N</span>
									  <!-- end order and merchant information -->' . $trustedStore . '<!-- end item 1 example -->
  <!-- end repeated item specific information -->

</div>';
					
					
				//$googleAdWordsScript .=	'<script type="text/javascript"> var sa_values = { \'site\':14227, \'orderid\':\''.$_SESSION['orderNum'].'\', \'name\':\''.$orderInfo['first_name'].' '.$orderInfo['last_name'].'\', \'email\':\''.$orderInfo['email'].'\', \'forcecomments\':1 };  function saLoadScript(src) { var js = window.document.createElement("script"); js.src = src; js.type = "text/javascript"; document.getElementsByTagName("head")[0].appendChild(js); } var d = new Date(); if (d.getTime() - 172800000 > 1429146877000) saLoadScript("//www.shopperapproved.com/thankyou/rate/14227.js"); else saLoadScript("//direct.shopperapproved.com/thankyou/rate/14227.js?d=" + d.getTime()); </script>';
				
				$googleAdWordsScript .=	'<script type="text/javascript">
/* Include all products in the object below \'product id\':\'Product Name\' */
var sa_products = { '.$rating.' };
</script>';				  
									  
			 $this->loadJS($googleAdWordsScript);
			 

			unset($_SESSION['newTotalAmount']);			 
			
			// Create PDF in Attachment Folder
			$filename = $this->order_pdf($_SESSION['orderNum'], 'F');
			$i = 0;
			// Verify file creation process complete and delay up to 3 seconds if not.
			$fileExists = file_exists($filename);
			while(($i < 3) && (!$filename))
			{
				$fileExists = file_exists($filename);
				sleep(1);
				$i++;
			}
			$this->load->model('admin_m');
			$settings = $this->admin_m->getEmailSettings();

			if($fileExists && (@$settings['email_order_complete'])) // If file was not created in 3 seconds do not send email
			{
				//$email = $_SESSION['userRecord']['username'];
				$email = $this->_mainData['userRecord']['username'];
				
				$orderEmail = $this->account_m->getOrderEmail($_SESSION['orderNum']);
				//if($orderEmail['email'] != $_SESSION['userRecord']['username'])
				if($orderEmail['email'] != $email)
					$email .= ', '.$orderEmail['email'];    
				$mailData = array(
				'fromEmailAddress' => $this->config->item('fromEmailAddress'),
				'fromName' => $this->config->item('fromEmailName'),
				'replyToEmailAddress' => $this->config->item('replyToEmailAddress'),
				'replyToName' => $this->config->item('replyToName'),
				'toEmailAddress' => $email,
				'subject' => WEBSITE_NAME . ' Order Confirmation');
				// Create the Mail Template Data
				$mailTemplateData = array('assets' => $this->_mainData['assets'], 
				'baseURL' => $this->_mainData['baseURL']);
				// Generate the Password Verification Email to the User
				$this->load->model('mail_gen_m');  
				$ret = $this->mail_gen_m->generateFromView($mailData, 
																			$mailTemplateData, 
																			'email/orderConf_html_v',
																			'email/orderConf_text_v',
																			$filename);
				
			}
			
		}
		if(@$_SESSION['error_message'])
		{
			$this->_mainData['error_message'] = $_SESSION['error_message'];
			unset($_SESSION['error_message']);
		}
		$this->_mainData['states'] = $this->load_states();
		$this->_mainData['provinces'] = $this->load_provinces(); 
		$this->loadCountries();
		$this->load->model('parts_m');
		$this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
		
		$this->_mainData['machines'] = $this->parts_m->getMachinesDd();
		$this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
		$this->load->model('pages_m');
		$this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
		$this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
		$this->_mainData['brandSlider'] = $this->load->view('info/s_brand_slider_v', $this->_mainData, TRUE);
		$this->_mainData['new_header']  = 1;
		$this->setFooterView('master/s_footer_v.php');
		$this->setNav('master/navigation_v', 7);
		$this->renderMasterPage('master/s_nav_master_v', 'account/account_v', $this->_mainData);
		
		unset($_SESSION['guestUser']);
		$_SESSION['url'] = 'checkout/account';
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
	
	public function account_edit()
	{
	
		$this->_mainData['userRecord'] = @$_SESSION['userRecord'];
		if($this->validateEditUser() !== FALSE)
		{
			$updated = $this->account_m->updateUserInfo($this->input->post());
		}
		if(@$this->_mainData['userRecord']['billing_id'])
		{
			$this->_mainData['billingRecord'] = $this->account_m->getBillingInfo($this->_mainData['userRecord']['id']);
		}
		if(@$this->_mainData['userRecord']['shipping_id'])
		{
			$this->_mainData['shippingRecord'] = $this->account_m->getShippingInfo($this->_mainData['userRecord']['id']);
		}

		$this->_mainData['states'] = $this->load_states();
		$this->_mainData['provinces'] = $this->load_provinces(); 
		$this->loadCountries();
		$this->load->model('parts_m');
		$this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
		$this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
    	$this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
		$this->_mainData['machines'] = $this->parts_m->getMachinesDd();
		$this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
		$this->_mainData['new_header']  = 1;
		$this->load->model('pages_m');
		$this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
		$this->setFooterView('master/s_footer_v.php');
		$this->setNav('master/navigation_v', 7);
		$this->renderMasterPage('master/s_nav_master_v', 'account/account_edit_v', $this->_mainData);
	}
	
	public function account_order()
	{
		if( $_SESSION['userRecord']['id'] != '' ) {
			$this->load->model('parts_m');
			$this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
			$this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
			$this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
			$this->_mainData['machines'] = $this->parts_m->getMachinesDd();
			$this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
			$this->_mainData['orders'] = $this->account_m->getOrders($_SESSION['userRecord']['id'], FALSE, 5);
			$this->_mainData['new_header']  = 1;
			$this->load->model('pages_m');
			$this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
			$this->setFooterView('master/s_footer_v.php');
			$this->setNav('master/navigation_v', 7);
			$this->renderMasterPage('master/s_nav_master_v', 'account/account_order_v', $this->_mainData);
		} else {
			redirect($this->_mainData['s_baseURL'] . 'checkout/account');
		}
	}
	
	public function account_address()
	{
		$this->load->model('parts_m');
		$this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
		$this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
    	$this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
		$this->_mainData['machines'] = $this->parts_m->getMachinesDd();
		$this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
		$this->_mainData['addresses'] = $this->account_m->getAddresses();
		$this->load->model('pages_m');
		$this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
		$this->setFooterView('master/s_footer_v.php');
		$this->setNav('master/navigation_v', 7);
		$this->_mainData['new_header']  = 1;
		$this->renderMasterPage('master/s_nav_master_v', 'account/account_address_v', $this->_mainData);
	}
	
	public function account_address_edit($id = NULL)
	{
		if(is_numeric($id))
		{
			if(($this->validateEditAddress() === TRUE) && ($this->account_m->verifyUserAddress($_SESSION['userRecord']['id'], $id)))
			{
				$this->account_m->updateAddress($id, $this->input->post());
				redirect('checkout/account_address');
			}
			$this->_mainData['address'] = $this->account_m->getAddress($id);
		}
		elseif($id == 'new')
		{
			if(($this->validateEditAddress() === TRUE))
			{
				$this->account_m->createAddress($this->input->post());
				redirect('checkout/account_address');
			}
			$this->_mainData['address']['id'] = 'new';
		}
			$this->_mainData['states'] = $this->load_states();
			$this->_mainData['provinces'] = $this->load_provinces(); 
			$this->loadCountries();
			
			
			$this->load->model('parts_m');
			$this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
			$this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
	    	$this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
	    	$this->load->model('pages_m');
			$this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
			$this->_mainData['machines'] = $this->parts_m->getMachinesDd();
			$this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
			$this->_mainData['new_header']  = 1;
			$this->setNav('master/navigation_v', 7);
			$this->renderMasterPage('master/s_nav_master_v', 'account/account_address_edit_v', $this->_mainData);
		
	}
	
	/********************************* CHECKOUT PAGES ***************************************/
	
	public function index()
	{ 
		$addresses = $this->account_m->getAddresses(TRUE);
		$addresses[0] = '--Select Address--';
		asort($addresses);
		$billing_addresses = $addresses;
		$shipping_addresses = $addresses;

		if(@$billing_addresses[@$_SESSION['userRecord']['billing_id']])
			unset($billing_addresses[@$_SESSION['userRecord']['billing_id']]);
		if(@$shipping_addresses[@$_SESSION['userRecord']['shipping_id']])
			unset($shipping_addresses[@$_SESSION['userRecord']['shipping_id']]);	
		$this->_mainData['billing_addresses'] = $billing_addresses;
		$this->_mainData['shipping_addresses'] = $shipping_addresses;
		
		if(isset($_SESSION['cart']['transAmount']))
			unset($_SESSION['cart']['transAmount']);
		
		$user_id = @$_SESSION['guestUser']['id'];
		if( empty($user_id) ){
			$user_id = @$_SESSION['userRecord']['id'];
		}
		
		$this->_mainData['shippingRecs'] = $this->account_m->getShippingDDInfo($user_id);
		
		$this->_mainData['billing'] = $this->account_m->getBillingInfo($user_id);
		$this->_mainData['shipping'] = $this->account_m->getShippingInfo($user_id);
		// Refresh Values from Validation Error or Calculate Shipping
		if(@$this->session->flashdata('values'))
		{
			$this->_mainData['billing'] = $this->separateCompoundArrays(0, $this->session->flashdata('values'));
			$this->_mainData['shipping'] = $this->separateCompoundArrays(1, $this->session->flashdata('values'));
		}
		if(@$_SESSION['contactInfo'])
			$this->_mainData['billing'] = $_SESSION['contactInfo'];
		if(@$_SESSION['shippingInfo'])
			$this->_mainData['shipping'] = $_SESSION['shippingInfo'];
		
		// Page Variables
		$this->_mainData['states'] = $this->load_states();
		$this->_mainData['provinces'] = $this->load_provinces();  
		if(@$this->session->flashdata('values'))
			$this->_mainData['value'] = @$this->session->flashdata('values');
		$this->loadCountries();
		$_SESSION['specialInstr'] = '';
		$this->renderMasterPage('master/s_master_v', 'checkout/client_info_v', $this->_mainData);
	}

  public function process_client_info()
  {
		  
  	if($this->validateClientInfo() !== FALSE)
  	{
  		
  	  	$post = $this->input->post();
	    $this->setRedirectValues('values', $this->input->post());
  	  if(is_array(@$_SESSION['cart']))
	  {
	  	$this->checkout_m->calculateOrder($post);
	  	$this->load->model('coupons_m');
        $this->coupons_m->calculateCoupon();

	   } 
	    else
	    {
  	    $this->setRedirectValues('validation', 'Your shopping cart has timed out. Please hit the back button and return to the main site.');
        redirect($this->_mainData['s_baseURL'].'checkout');
		}
		
		if( isset($_SESSION['guestUser']) ){
			unset($_SESSION['guestUser']);
		}
		if( !empty($post['guest']) && $post['guest']== "084e0343a0486ff05530df6c705c8bb4" ){
			
			/* GUEST CHECKOUT */
			$guestUser['username'] = str_replace("*","",$post['email'][0]);
			$guestUser['user_type'] = "guest";
			$guestUserId = $this->checkout_m->insert_it($guestUser, 'user');
			$_SESSION['guestUser']['id'] = $guestUserId;
			$_SESSION['guestUser']['username'] = $guestUser['username'];
		
		}
		
        $contactInfo[0]['first_name'] = str_replace("*","",$post['first_name'][0]); 
        $contactInfo[0]['last_name'] = str_replace("*","",$post['last_name'][0]); 
        $contactInfo[0]['street_address'] = str_replace("*","",$post['street_address'][0]); 
        $contactInfo[0]['address_2'] = str_replace("*","",$post['address_2'][0]); 
        $contactInfo[0]['city'] = str_replace("*","",$post['city'][0]); 
        $contactInfo[0]['state'] = str_replace("*","",$post['state'][0]);
        $contactInfo[0]['zip'] = str_replace("*","",$post['zip'][0]);
        $contactInfo[0]['email'] = str_replace("*","",$post['email'][0]);
        $contactInfo[0]['phone'] = str_replace("*","",$post['phone'][0]);
        $contactInfo[0]['country'] = str_replace("*","",$post['country'][0]);
        $contactInfo[0]['company'] = str_replace("*","",$post['company'][0]);
        $contactInfo[1]['first_name'] = str_replace("*","",$post['first_name'][1]); 
        $contactInfo[1]['last_name'] = str_replace("*","",$post['last_name'][1]); 
        $contactInfo[1]['street_address'] = str_replace("*","",$post['street_address'][1]); 
        $contactInfo[1]['address_2'] = str_replace("*","",$post['address_2'][1]); 
        $contactInfo[1]['city'] = str_replace("*","",$post['city'][1]); 
        $contactInfo[1]['state'] = str_replace("*","",$post['state'][1]);
        $contactInfo[1]['zip'] = str_replace("*","",$post['zip'][1]);
        $contactInfo[1]['email'] = str_replace("*","",$post['email'][1]);
        $contactInfo[1]['phone'] = str_replace("*","",$post['phone'][1]);
        $contactInfo[1]['country'] = str_replace("*","",$post['country'][1]);
        $contactInfo[1]['company'] = str_replace("*","",$post['company'][1]);
        $_SESSION['specialInstr'] .= ' **Customer Notes: '.str_replace("*","",$post['special_instr']).'** ';
  	    $_SESSION['contactInfo'] = $contactInfo[0];
  	    $_SESSION['shippingInfo'] = $contactInfo[1];
  	    $_SESSION['contactInfo']['state_shipping'] = $contactInfo[1]['state'];
  	    
		$user_id = @$_SESSION['guestUser']['id'];
		if( empty($user_id) ){
			$user_id = @$_SESSION['userRecord']['id'];
		}
		
  	    $_SESSION['contactInfo']['billing_id'] = $this->account_m->updateContact($contactInfo[0], 'billing', $user_id);
  	    $_SESSION['contactInfo']['shipping_id'] = $this->account_m->updateShippingTable($contactInfo[1], $user_id, str_replace("*","",@$post['shipping_id']));
  		

        if(@!is_numeric($_SESSION['cart']['shipping']['finalPrice']))
		{
			if(empty($_SESSION['postalOptions']))
			{
				$_SESSION['specialInstr'] .= ' **Shipping Notes: No Shipping Selected. Contact Customer if Shipping is required.** ';
				$_SESSION['cart']['shipping']['finalPrice'] = 0.00;
				$_SESSION['cart']['shipping']['price'] = 0.00;
			}
		}
        redirect($this->_mainData['s_baseURL'].'checkout/payment');

  	}
  	else
	{
	    $this->setRedirectValues('shipping_id', @$_POST['shipping_id']);
		$this->setRedirectValues('validation', validation_errors());
		$this->setRedirectValues('values', $this->input->post());
		redirect($this->_mainData['s_baseURL'].'checkout');
	}
  }
  
	public function payment($failedCCPayment = NULL)
	{
		$user_id = $guest_user_id = @$_SESSION['guestUser']['id'];
		if( empty($user_id) ){
			$user_id = @$_SESSION['userRecord']['id'];
		}
		
		// Needed for Order Record creation
		$_SESSION['newOrderNum'] = $this->account_m->recordOrderCreation($_SESSION['contactInfo'], $_SESSION['cart'], $user_id);

		// JLB 07-08-18
        // Record these...
        if (array_key_exists("hlsmtransfers", $_SESSION) && is_array($_SESSION["hlsmtransfers"]) && count($_SESSION["hlsmtransfers"]) > 0) {
            initializePSTAPI();
            global $PSTAPI;
            foreach ($_SESSION["hlsmtransfers"] as $tid) {
                $PSTAPI->hlsmxmlfeed()->update($tid, array(
                    "order_id" => $_SESSION['newOrderNum']
                ));
            }
        }


        if (array_key_exists("failed_validation", $_SESSION) && $_SESSION["failed_validation"] > 0 && !jverifyRecaptcha()) {
            $this->_mainData['processingError'] = 'ReCAPTCHA failure. Please try again.';
        } else


        if($this->validateCCInfoUpdate() === TRUE)
		{
            if (array_key_exists("failed_validation", $_SESSION) && $_SESSION["failed_validation"] > 0) {
                $_SESSION["failed_validation"] = 0;
            }

			$code = $this->input->post('shippingValue');
			$value = $_SESSION['postalOptions'][$code]['value'];
			$_SESSION['cart']['shipping']['finalPrice'] = $value;
			$_SESSION['cart']['shipping'] ['type'] = $code;
			
			$data = $_POST;
			$data['cc'] = str_replace(' ', '', $_POST['credit-card-number']);
			$data['cart'] = $_SESSION['cart'];
			$data['specialInstr'] = $_SESSION['specialInstr'];
			$data['contactInfo'] = $_SESSION['contactInfo'];
			
			$total = number_format($_SESSION['cart']['transAmount'] + @$_SESSION['cart']['tax']['finalPrice'] +  $_SESSION['cart']['shipping']['finalPrice'],2);
			$total = str_replace(',','', $total);
			// include('lib/Braintree.php');
			$this->load->model('admin_m');
			$this->load->model("genericpayments_m");
			$store_name = $this->admin_m->getAdminShippingProfile();
			$this->genericpayments_m->init($store_name);
            $result = $this->genericpayments_m->sale($total);


			if($total <= 0)
			{
				$this->_mainData['processingError'] = "We apologize but we are experiencing Technical difficulties with this order.  Please call us at 1-844-2Go-Moto for assistance.";
				$this->load->model('order_m');
				$this->order_m->updateStatus($_SESSION['newOrderNum'], 'Declined', 'Zero Balance at Payment Submit!');
			} elseif( $this->genericpayments_m->sale($result)) {
			    $transaction = $this->genericpayments_m->getTransactionID($result);
				$this->load->model('admin_m');
                $transaction = array('order_id' => $_SESSION['newOrderNum'], 'braintree_transaction_id' => $transaction->id, 'transaction_date' => time(), "processor" => $store_name["merchant_type"]);
                $transaction['amount'] = number_format($_SESSION['cart']['transAmount'] + @$_SESSION['cart']['tax']['finalPrice'] +  $_SESSION['cart']['shipping']['finalPrice'],2);
                $this->admin_m->addOrderTransaction($transaction);
                                
				$this->completeOrder(@$user_id);
			} else {
				$this->_mainData['processingError'] = "Your payment has failed to process.  " . $this->genericpayments_m->getErrorMessage($result) ."<br />Please check your billing information and card and try again.";
				$this->load->model('order_m');
				$this->order_m->updateStatus($_SESSION['newOrderNum'], 'Declined', "Payment Not Verified!");
			}
		}
		
		if(@$_SESSION['failed_validation'] == 2)
		{
			session_destroy();
			redirect('welcome/index/vallidation_error');
		}
		
		$this->createMonths();
		$this->createYears();
		$this->load->model('coupons_m');
		$this->coupons_m->calculateCoupon();
		$this->_mainData['postalOptDD'] = $this->checkout_m->subdividePostalOptions(@$_SESSION['postalOptions']);
		$this->_mainData['cart'] = $_SESSION['cart'];
		$this->_mainData['contactInfo'] = $_SESSION['contactInfo'];
		$this->_mainData['shippingInfo'] = $_SESSION['shippingInfo'];
		$this->renderMasterPage('master/s_master_v', 'checkout/payment_info_v', $this->_mainData);

	}
	
	// JLB 07-27-18
    // Let me just give this a giant WHAT THE FUCK
    // Why are there two of these functions, almost identical, back-to-back?
    // I am trying to ram in Stripe, and this is the BS I encounter.
    // As far as I can tell, this one doesn't include the address...and I do not know why.
	public function new_payment($failedCCPayment = NULL)
	{
		$user_id = $guest_user_id = @$_SESSION['guestUser']['id'];
		if( empty($user_id) ){
			$user_id = @$_SESSION['userRecord']['id'];
		}
		
		// Needed for Order Record creation
		$_SESSION['newOrderNum'] = $this->account_m->recordOrderCreation($_SESSION['contactInfo'], $_SESSION['cart'], $user_id);
		if($this->validateCCInfoPaypal() === TRUE)
		{
			$code = $this->input->post('shippingValue');
			$value = $_SESSION['postalOptions'][$code]['value'];
			$_SESSION['cart']['shipping']['finalPrice'] = $value;
			$_SESSION['cart']['shipping'] ['type'] = $code;
			
			$data = $_POST;
			$data['cc'] = str_replace(' ', '', $_POST['cc']);
			$data['cart'] = $_SESSION['cart'];
			$data['specialInstr'] = $_SESSION['specialInstr'];
			$data['contactInfo'] = $_SESSION['contactInfo'];
			
			$total = number_format($_SESSION['cart']['transAmount'] + @$_SESSION['cart']['tax']['finalPrice'] +  $_SESSION['cart']['shipping']['finalPrice'], 2);
			$this->load->model('admin_m');
            $this->load->model("genericpayments_m");
			$store_name = $this->admin_m->getAdminShippingProfile();
			//include('lib/Braintree.php');
            $this->genericpayments_m->init($store_name);
            $result = $this->genericpayments_m->sale($total);


			if($total <= 0)
			{
				$this->_mainData['processingError'] = "We apologize but we are experiencing Technical difficulties with this order.  Please call us at 1-844-2Go-Moto for assistance.";
				$this->load->model('order_m');
				$this->order_m->updateStatus($_SESSION['newOrderNum'], 'Declined', 'Zero Balance at Payment Submit!');
			} elseif( $this->genericpayments_m->sale($result)) {
                $transaction = $this->genericpayments_m->getTransactionID($result);
				$this->load->model('admin_m');
				$transaction = array('order_id' => $_SESSION['newOrderNum'], 'braintree_transaction_id' => $transaction->id, 'transaction_date' => time(), "processor" => $store_name["merchant_type"]);
				// JLB 08-13-17 WTF? Why would you format this thing into a number_format to shove it into a database decimal column unless you were wanting it to break above $999.
				//$transaction['amount'] = number_format($_SESSION['cart']['transAmount'] + @$_SESSION['cart']['tax']['finalPrice'] +  $_SESSION['cart']['shipping']['finalPrice'],2);
				$transaction['amount'] = $_SESSION['cart']['transAmount'] + @$_SESSION['cart']['tax']['finalPrice'] +  $_SESSION['cart']['shipping']['finalPrice'];
                $this->admin_m->addOrderTransaction($transaction);
				$this->completeOrder(@$user_id);
			} else {
				// echo '<pre>';
				// print_r($result);
				// echo '</pre>';exit;
				$this->_mainData['processingError'] = "Your payment has failed to process.  " . $this->genericpayments_m->getErrorMessage($result) ."<br />Please check your billing information and card and try again.";
				$this->load->model('order_m');
				$this->order_m->updateStatus($_SESSION['newOrderNum'], 'Declined', "Payment Not Verified!");
			}
		}
		
		if(@$_SESSION['failed_validation'] == 2)
		{
			session_destroy();
			redirect('welcome/index/vallidation_error');
		}
		
		$this->createMonths();
		$this->createYears();
		//$this->load->model('coupons_m');
		//$this->coupons_m->calculateCoupon();
		$this->_mainData['postalOptDD'] = $this->checkout_m->subdividePostalOptions(@$_SESSION['postalOptions']);
		$this->_mainData['cart'] = $_SESSION['cart'];
		$this->_mainData['contactInfo'] = $_SESSION['contactInfo'];
		$this->_mainData['shippingInfo'] = $_SESSION['shippingInfo'];
		$this->renderMasterPage('master/s_master_v', 'checkout/payment_info_v_new', $this->_mainData);

	}
	
	public function paypalpayment($failedCCPayment = NULL)
	{
		$user_id = $guest_user_id = @$_SESSION['guestUser']['id'];
		if( empty($user_id) ) {
			$user_id = @$_SESSION['userRecord']['id'];
		}
		
		$this->load->model('checkout_m');
		$this->load->model('account_m');
		//$this->checkout_m->calculateTotal();
		
		// Needed for Order Record creation
		//$_SESSION['newOrderNum'] = $this->account_m->recordOrderCreation($_SESSION['contactInfo'], $_SESSION['cart'], $user_id);

		if($this->validatePaypalForm() === TRUE)
		{
			if( isset($_SESSION['guestUser']) ) {
				unset($_SESSION['guestUser']);
			}
			
			$post = $this->input->post();
			$name = explode(' ', $post['recipientName']);
			
			if( !@$_SESSION['userRecord']['id'] ) {
				/* GUEST CHECKOUT */
				$guestUser['username'] = str_replace("*","",$post['email']);
				$guestUser['user_type'] = "guest";
				$guestUserId = $this->checkout_m->insert_it($guestUser, 'user');
				$_SESSION['guestUser']['id'] = $guestUserId;
				$_SESSION['guestUser']['username'] = $guestUser['username'];
			}
			
			$contactInfo[0]['first_name'] = str_replace("*","",$name[0]); 
			$contactInfo[0]['last_name'] = str_replace("*","",$name[1]); 
			$contactInfo[0]['street_address'] = str_replace("*","",$post['address1']); 
			$contactInfo[0]['address_2'] = str_replace("*","",$post['address2']); 
			$contactInfo[0]['city'] = str_replace("*","",$post['city']); 
			$contactInfo[0]['state'] = str_replace("*","",$post['state']);
			$contactInfo[0]['zip'] = str_replace("*","",$post['postalCode']);
			$contactInfo[0]['email'] = str_replace("*","",$post['email']);
			$contactInfo[0]['phone'] = str_replace("*","",$post['phone']);
			$contactInfo[0]['country'] = str_replace("*","","USA");
			$contactInfo[0]['company'] = str_replace("*","",$post['company']);
			$contactInfo[1]['first_name'] = str_replace("*","",$name[0]); 
			$contactInfo[1]['last_name'] = str_replace("*","",$name[1]); 
			$contactInfo[1]['street_address'] = str_replace("*","",$post['address1']); 
			$contactInfo[1]['address_2'] = str_replace("*","",$post['address2']); 
			$contactInfo[1]['city'] = str_replace("*","",$post['city']); 
			$contactInfo[1]['state'] = str_replace("*","",$post['state']);
			$contactInfo[1]['zip'] = str_replace("*","",$post['postalCode']);
			$contactInfo[1]['email'] = str_replace("*","",$post['email']);
			$contactInfo[1]['phone'] = str_replace("*","",$post['phone']);
			$contactInfo[1]['country'] = str_replace("*","","USA");
			$contactInfo[1]['company'] = str_replace("*","",$post['company']);
			$_SESSION['specialInstr'] .= ' **Customer Notes: ** ';
			$_SESSION['contactInfo'] = $contactInfo[0];
			$_SESSION['shippingInfo'] = $contactInfo[1];
			$_SESSION['contactInfo']['state_shipping'] = $contactInfo[1]['state'];
			
			$_SESSION['paypal_nonce'] = $_REQUEST["payment_method_nonce"];
			//$this->checkout_m->calculateParcel($post['postalCode'], 'USA');
			$this->load->model('account_m');
			$_SESSION['contactInfo']['billing_id'] = $this->account_m->updateContact($contactInfo[0], 'billing', $user_id);
			$_SESSION['contactInfo']['shipping_id'] = $this->account_m->updateShippingTable($contactInfo[1], $user_id, str_replace("*","",@$post['shipping_id']));
			
			$dt = array('state' => array('1' => $post['state']), 'country' => array('1' => 'USA'), 'zip' => array('1' => $post['postalCode']));
			$this->checkout_m->calculateOrder($dt);
			$this->load->model('coupons_m');
			$this->coupons_m->calculateCoupon();
			
			//$_SESSION['newOrderNum'] = $this->account_m->recordOrderCreation($_SESSION['contactInfo'], $_SESSION['cart'], $user_id);
			redirect('checkout/new_payment');
			exit;
		}
		
		if(@$_SESSION['failed_validation'] == 2)
		{
			session_destroy();
			redirect('welcome/index/vallidation_error');
		}
		
		redirect('shopping/cart');
		//$this->renderMasterPage('master/s_master_v', 'checkout/payment_info_v', $this->_mainData);
	}
	
	
	private function completeOrder($guest_user_id=0)
	{
	    $_SESSION["hlsmtransfers"] = array(); // JLB 07-08-18 - Just clear this list. It has run its course.
		$this->account_m->recordPaidTransaction($_SESSION['newOrderNum'], $_SESSION['cart']);
		$this->load->model('order_m');
		$this->order_m->updateStatus($_SESSION['newOrderNum'], 'Approved', 'System order');
		$this->account_m->unsetCart();
		unset($_SESSION['postalOptions']);
		$_SESSION['newTotalAmount'] = $_SESSION['cart']['transAmount'];
		$_SESSION['orderNum'] = $_SESSION['newOrderNum'];
		// Clear session order/product data.
		unset($_SESSION['cart']);
		unset($_SESSION['activeMachine']);
		
		//if( !empty($guest_user_id) ){
			redirect($this->_mainData['s_baseURL'] . 'checkout/account?u='.$guest_user_id.'-'.time());
		//}else{
		//	redirect(base_url("checkout/account"));
		//}
		$_SESSION['url'] = 'checkout/payment';
	}
	
	public function getAmt()
	{
		$code = $this->input->post('code');
		$value = $_SESSION['postalOptions'][$code]['value'];
		$_SESSION['cart']['shipping']['finalPrice'] = $value;
		$_SESSION['cart']['shipping'] ['type'] = $code;
		
		$transAmount = $_SESSION['cart']['transAmount'] + @$_SESSION['cart']['tax']['finalPrice'] +  $_SESSION['cart']['shipping']['finalPrice'];
		
		echo $transAmount;
	}
	
	public function check()
	{
		$result = Braintree_Transaction::sale([
			"amount" => $_POST['amount'],
			"paymentMethodNonce" => $_POST['payment_method_nonce'],
			"orderId" => $_POST['orderId'],
			"options" => [
			  "paypal" => [
				"customField" => $_POST["PayPal custom field"],
				"description" => $_POST["Description for PayPal email receipt"],
			  ],
			],
			'customer' => array(
							'firstName' => $_SESSION['shippingInfo']['first_name'],
							'lastName' => $_SESSION['shippingInfo']['last_name'],
							'phone' => $_SESSION['shippingInfo']['phone'],
							'email' => $_SESSION['shippingInfo']['email']
						  ),
		]);
		if ($result->success) {
			//print_r("Success ID: " . $result->transaction->id);
			//print_r(@$guest_user_id);
			// $this->completeOrder(@$guest_user_id);
			$this->account_m->recordPaidTransaction($_SESSION['newOrderNum'], $_SESSION['cart']);
			$this->load->model('order_m');
			$this->order_m->updateStatus($_SESSION['newOrderNum'], 'Approved', 'System order');
			$this->account_m->unsetCart();
			unset($_SESSION['postalOptions']);
			$_SESSION['newTotalAmount'] = $_SESSION['cart']['transAmount'];
			$_SESSION['orderNum'] = $_SESSION['newOrderNum'];
			// Clear session order/product data.
			unset($_SESSION['cart']);
			unset($_SESSION['activeMachine']);
			
			//if( !empty($guest_user_id) ){
				print_r($this->_mainData['s_baseURL'] . 'checkout/account?u='.@$guest_user_id.'-'.time());
			//}else{
			//	redirect(base_url("checkout/account"));
			//}
			$_SESSION['url'] = 'checkout/payment';
		} else {
		  print_r("Error Message: " . $result->message);
		}
	}
	
	public function PayPal()
	{
		// echo "<pre>";
		// print_r($_SESSION['newOrderNum']);
		// print_r($_SESSION['cart']);
		// print_r($_SESSION['contactInfo']);
		// print_r($_SESSION['shippingInfo']);
		// echo "</pre>";
		
		$result = Braintree_Transaction::sale(array(
				'amount' => $this->input->post('pay_amt'),
				'orderId' => 'order id',
				'creditCard' => array(
							'cardholderName' => $this->input->post('cr_name'),
							'number' => $this->input->post('cr_number'),
							'expirationDate' => $this->input->post('cr_month').'/'.$this->input->post('cr_year'),
							'cvv' => $this->input->post('cr_cvv'),
							),
				'customer' => array(
							'firstName' => $_SESSION['shippingInfo']['first_name'],
							'lastName' => $_SESSION['shippingInfo']['last_name'],
							'phone' => $_SESSION['shippingInfo']['phone'],
							'email' => $_SESSION['shippingInfo']['email']
						  ),
			));

		if ($result->success) {
		  print_r("success!: " . $result->transaction->id);
		  } else if ($result->transaction) {
			print_r("Error processing transaction:");
			print_r("\n  code: " . $result->transaction->processorResponseCode);
			print_r("\n  text: " . $result->transaction->processorResponseText);
			} else {
			  print_r("Validation errors: \n");
			  print_r($result->errors->deepAll());
			}


		$clientToken = Braintree_ClientToken::generate();
	}
	
	/************************************* HELPER FUNCTIONS *****************************************/
	
	public function createMonths()
	{
	
		for($i = 1; $i < 13; $i++)
		{
			$monthFormat = sprintf("%02s", $i);
			$dateObj   = DateTime::createFromFormat('!m', $i);
			$this->_mainData['months'][$monthFormat] =  $dateObj->format('F');
		}
	}
  
	public function createYears()
	{
		for($i = date('y'); $i < (date('y') + 13); $i++)
		{
			$dt = DateTime::createFromFormat('y', $i);
			$yyyy = $dt->format('Y');
			$this->_mainData['years'][$i] = $yyyy;
		}
	}
	
		public function make_billing($id)
	{
		if($this->account_m->verifyUserAddress($_SESSION['userRecord']['id'], $id))
		{
			$data = array('billing_id' => $id);
			$this->account_m->updateUserRec($data);
			$_SESSION['userRecord']['billing_id'] = $id;
		}
		redirect('checkout/account_address');
	}
	
	public function make_shipping($id)
	{
		if($this->account_m->verifyUserAddress($_SESSION['userRecord']['id'], $id))
		{
			$data = array('shipping_id' => $id);
			$this->account_m->updateUserRec($data);
			$_SESSION['userRecord']['shipping_id'] = $id;
		}
		redirect('checkout/account_address');
	}

	 public function loadCountries()
  	{
  		$this->_mainData['countries'] = $this->account_m->getCountries();
  	}
  
	public function load_provinces($ajax = FALSE)
	{
		$provinces = $this->account_m->getTerritories('CA');
		if($ajax)
		  echo json_encode($provinces);
		else
	  return $provinces;
	}
	
	public function load_states($ajax = FALSE)
	{
		$states = $this->account_m->getTerritories('US');
		if($ajax)
			echo json_encode($states);
		else
			return $states;
	}
  
  private function separateCompoundArrays($key, $array)
  {
    if(is_integer($key) && is_array($array))
    {
      $newArray = array();
      foreach($array as $ele => $value)
      {
        if(isset($value[$key]))
          $newArray[$ele] = $value[$key];
      }
    }
    return $newArray;
  }
  
   public function new_change_country()
  {
    $countryList = array('USA', 'Canada');
    $type = array('billing', 'acct_billing', 'shipping', 'acct_shipping');
    $country = $_POST['country'];
    if(in_array($_POST['country'], $countryList))
    {
      $funct = $country.'fields';
      $labelArr = $this->$funct();
      echo json_encode($labelArr);
    }
    else
      echo 'Invalid Country';
  }

  
  public function change_country()
  {
    $countryList = array('USA', 'Canada');
    $type = array('billing', 'acct_billing', 'shipping', 'acct_shipping');
    $country = $_POST['country'];
    if(in_array($_POST['country'], $countryList))
    {
      $funct = $country.'fields';
      $labelArr = $this->$funct();
      echo json_encode($labelArr);
    }
    else
      echo 'Invalid Country';
  }
  
  private function USAfields()
  {
    $labels = array('street_address' => '<b>Address:</b>',
                    'address_2' => '<b>Apt/Suite:</b>',
                    'city' => '<b>City:</b>',
                    'state' => '<b>State:</b>',
                    'zip' => '<b>Zipcode:</b>'
                    );
    return $labels;
  }
  
  private function Canadafields()
  {
    $labels = array('street_address' => '<b>Civic Address:</b>',
                    'address_2' => '<b>Apt/Suite:</b>',
                    'city' => '<b>Municipality:</b>',
                    'state' => '<b>Province:</b>',
                    'zip' => '<b>Postal Code:</b>'
                    );
    return $labels;
  }

  public function test($zip)
  {
	  $this->checkout_m->getZip($zip);
  }

}