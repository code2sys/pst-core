<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Wholesale extends Master_Controller {

  function __construct()
	{
		parent::__construct();
    $this->setFooterView('footer_v.php');
  	//$this->output->enable_profiler(TRUE);
  }
  
  public function index()
  {
    $this->setNav('navigation_v', 0);
		$this->renderMasterPage('master_v', 'wholesale/main_v', $this->_mainData);
  }
  
  public function logo()
  {
    $this->setNav('navigation_v', 0);
		$this->renderMasterPage('master_v', 'wholesale/logo_v', $this->_mainData);
  }
  
  public function program()
  {
    $this->setNav('navigation_v', 0);
		$this->renderMasterPage('master_v', 'wholesale/program_v', $this->_mainData);
  }
  
}