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
		if(($url_parts[0] != 'www') && (ENVIRONMENT != 'development'))
		{
			redirect(base_url($_SERVER['PATH_INFO']));
		}
		ini_set('session.gc_maxlifetime', 21600);
		ini_set('session.cookie_httponly', 1);
		if(!session_id())
		  session_start();
		date_default_timezone_set ( 'America/New_York' );
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
		
		$desc = STYLED_HOSTNAME;
		$this->setMasterPageVars('descr', $desc);
		$keywords = "Dirt Bike, UTV, Street Bike, ATV";
		$this->setMasterPageVars('keywords', $keywords);
		$title = WEBSITE_NAME;
		$this->setMasterPageVars('title', $title);
		$logo = $this->config->item('assets')."/images/logo.png";
		$this->setMasterPageVars('logo', $logo);
		$s_logo = $this->config->item('s_assets')."/images/logo.png";
		$this->setMasterPageVars('s_logo', $s_logo);
		if(@$_SESSION['userRecord'])
			$_SESSION['garage'] = $this->account_m->getGarage($_SESSION['userRecord']['id']);
		$this->load->helper('simplemodal');
        //$this->loadJS(getSimpleModalHeadScript($this->_mainData['assets'], TRUE));
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
	


}
