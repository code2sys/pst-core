<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_Controller extends CI_Controller {

    private $_header = '';
	private $_bodyTag = '';
	private $_nav = '';
	private $_footer = '';
	private $_js = '';
	private $_topjs = '';
	private $_sidebarpieces = '';
	private $_contactEmail = '';
	protected $_masterPageVars = array();
	private $_modalContent = '';
	private $_bottomJsScripts = '';
	public $_mainData = array();
	
 	function __construct()
	{
		parent::__construct();
		$url_parts = explode('.', $_SERVER['SERVER_NAME']);
		// JLB 10-30-16
		// Don't pull this sort of shit with secret knowledge. Check for the thing you want to check for. Don't be this horrible a person.
		//if(($url_parts[0] != 'www') && (ENVIRONMENT != 'development') && (ENVIRONMENT != 'qa'))
		if (
            !$this->input->is_cli_request()
            &&
			(ENVIRONMENT == 'production') 
			&& 
			($_SERVER['SERVER_NAME'] != WEBSITE_HOSTNAME)
		) {
			redirect(base_url($_SERVER['PATH_INFO']));
		}
		ini_set('session.gc_maxlifetime', 21600);
		ini_set('session.cookie_httponly', 1);
		if(!session_id())
		  session_start();
		date_default_timezone_set ( 'America/New_York' );
		
		if(@$_SESSION['userRecord']) { 
			if(time() - $_SESSION['userRecord']['timestamp'] > 900) { //subtract new timestamp from the old one
				echo"<script>alert('15 Minutes over!');</script>";
				unset($_SESSION['userRecord']);
				redirect();
				exit;
			} else {
				$_SESSION['userRecord']['timestamp'] = time(); //set new timestamp
			}
		}
		
		$this->load->model('master_m');
		$this->load->model('admin_m');
		$this->load->model('account_m');
		
		
		$this->_mainData['assets'] = $this->config->item('assets');
		$this->_mainData['baseURL'] = base_url();
		$this->_mainData['s_assets'] = $this->config->item('s_assets');
		$this->_mainData['s_baseURL'] = $this->config->item('s_base_url');
		$this->_mainData['contactEmail'] = $this->_contactEmail;
		$this->_mainData['upload_path'] = $this->config->item('upload_path');
    	$this->_mainData['media'] = $this->config->item('media');
    	$this->_mainData['accountAddress'] = $this->admin_m->getAdminAddress();
    	$this->_mainData['SMSettings'] = $this->admin_m->getSMSettings();
		
    	$this->_mainData['store_name'] = $this->admin_m->getAdminShippingProfile();

        $store_name = $this->admin_m->getAdminShippingProfile();
        define('GOOGLE_LOCATION', urlencode($store_name['company'] . "," . $store_name['city'] . " " . $store_name['state']));
        define('WEBSITE_NAME', $store_name['company']);
        define('SUPPORT_PHONE_NUMBER', $store_name['phone']);
        define('CLEAN_PHONE_NUMBER', preg_replace("/[^0-9]/", "", $store_name['phone']));
        define('STORE_ADDRESS', $store_name['street_address']);
        define('STORE_ADDRESS2', $store_name['address_2']);
        define('STORE_CITY', $store_name['city']);
        define('STORE_STATE', $store_name['state']);
        define('STORE_ZIP', $store_name['zip']);
        define('CONTACT_EMAIL', $store_name['email']);
        $smsettings = $this->admin_m->getSMSettings();
        if (array_key_exists("sm_ytlink", $smsettings) && $smsettings["sm_ytlink"] != "") {
            define('YOUTUBE_CHANNEL', basename($smsettings["sm_ytlink"]));
        } else {
            define('YOUTUBE_CHANNEL', '');
        }


		$desc = STYLED_HOSTNAME;
		$this->setMasterPageVars('descr', $desc);
		$keywords = "Dirt Bike, UTV, Street Bike, ATV";
		$this->setMasterPageVars('keywords', $keywords);
		$title = WEBSITE_NAME;
		$this->setMasterPageVars('title', $title);
		$logo = $this->config->item('assets')."/images/logo.png";
		$logo_new = $this->config->item('benz_assets')."/logo.png";
		$this->setMasterPageVars('logo', $logo);
		$s_logo = $this->config->item('s_assets')."/images/logo.png";
		$this->setMasterPageVars('s_logo', $s_logo);
		if(@$_SESSION['userRecord'])
			$_SESSION['garage'] = $this->account_m->getGarage($_SESSION['userRecord']['id']);
		$this->load->helper('simplemodal');
        //$this->loadJS(getSimpleModalHeadScript($this->_mainData['assets'], TRUE));

        // JLB 03-19-17
        // Factor all these things out here....
	}
	
	public function setMasterPageVars($key, $value)
	{
		$this->_masterPageVars[$key] = $value;
	} 
	
	public function loadSidebar($view)
	{
		$this->_sidebarpieces .= $this->load->view($view, $this->_mainData, TRUE);
	}
  
	public function loadDateFields($dateArr = NULL)
	{
		$str = '<script> $(function() {';
		if($dateArr)
		{
			foreach($dateArr as $dateId)
			{
				$str .= '$("#'.$dateId.'").datepicker({ picker: "<img class=\'picker\' align=\'middle\' alt=\'\'>" });  ';
				  
			}
		}
		$str .= '});</script>';
		$this->loadJS($str);
	}
	
  public function setView($var, $input)
	{
		$this->_masterPageVars[$var] = $this->load->view($input, $this->_mainData, TRUE);
	}

	public function loadJS($input)
	{
		$this->_js .= $input;
	}
	
	public function loadTopJS($input)
	{
		$this->_topjs .= $input;
	}

  public function setNav($input, $active = 0)
	{
		$this->_mainData['pageIndex'] = $active;
		$this->_nav .= $this->load->view($input, $this->_mainData, TRUE);
	}
	
	public function setBodyTag($input)
	{
		$this->_bodyTag .= $input;
	}
	
	public function setFooterView($input)
	{
  		$this->_footer .= $this->load->view($input, $this->_mainData, TRUE);
	}
	
	public function setRedirectValues($view, $formElements)
	{
		$this->session->set_flashdata($view, $formElements);
	}
	
	public function setHeaderVars($input)
	{
		$this->_header .= $input;
	}
		
  public function buildMasterPageVars()
	{
		$this->_masterPageVars['header'] = $this->_header;
		$this->_masterPageVars['script'] = $this->_js;
		$this->_masterPageVars['topscript'] = $this->_topjs;
		if(!empty($this->_sidebarpieces))
		{
			$this->_mainData['sidebarpieces'] = $this->_sidebarpieces;
			$this->_mainData['sidebar'] = $this->load->view('master/sidebar_v', $this->_mainData, TRUE);
		}
		$this->_masterPageVars['nav'] = $this->_nav;
		$this->_masterPageVars['footer'] = $this->_footer;
		$this->_masterPageVars['bodyTag'] = $this->_bodyTag;
		$this->_masterPageVars['assets'] = $this->config->item('assets');
		$this->_masterPageVars['baseURL'] = base_url();
		$this->_masterPageVars['s_assets'] = $this->config->item('s_assets');
		$this->_masterPageVars['s_baseURL'] = $this->config->item('s_base_url');
		$this->_masterPageVars['bottomJsScripts'] = $this->_bottomJsScripts;
		$this->_masterPageVars['contactEmail'] = $this->_contactEmail;
	}
	
  public function renderMasterPage($master, $content=NULL, $data=NULL, $nav=0)
	{

		$this->buildMasterPageVars();
		
		if(!is_null($content))
			$this->_masterPageVars['mainContent'] = $this->load->view($content, $this->_mainData, TRUE);
		$this->_masterPageVars['modalContent'] =	$this->_modalContent;

		$this->load->view($master, $this->_masterPageVars);
	}
	
	public function logout()
	{
	  	session_destroy();
	  	redirect();
	}
	
	
	public function generateShoppingCart($viewShipping = FALSE)
	{
		$this->load->model('checkout_m');
		$this->checkout_m->calculatePrice($viewShipping);
		$this->_mainData['cartProducts'] = @$_SESSION['cart'];
		$tableView = $this->load->view('tables/cart_v', $this->_mainData, TRUE);
		if(@$_POST['ajax'])
			echo $tableView;
		else
			return $tableView;
	} 
	
	public function checkValidAccess( $action ) {
		$this->load->model('account_m');
		return $this->account_m->validAccess( $action );
	}

    protected function enforceAdmin($action = "") {
        if(!$this->checkValidAccess($action) && !@$_SESSION['userRecord']['admin']) {
            redirect('');
            exit();
        }
    }

}
