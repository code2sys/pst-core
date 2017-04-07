<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Customer extends Master_Controller {

	function __construct()
	{
		parent::__construct();
		if(!@$_SESSION['userRecord']['admin'])
			redirect('welcome');
		$this->setFooterView('admin/footer_v.php');
		$this->load->model('customer_m');
	}
	
	public function index()
	{
		$this->load->model('reporting_m');
	
		$this->setNav('admin/nav_v', 0);
		$this->renderMasterPage('admin/master_v', 'admin/home_v', $this->_mainData);
	}
	
	public function order_list()
	{
		$this->setNav('admin/nav_v', 0);
		$this->renderMasterPage('admin/master_v', 'admin/home_v', $this->_mainData);
	}


}