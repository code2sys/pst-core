<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(APPPATH . 'libraries/fpdf.php');

class PoReport extends FPDF {

	private $parametersArray = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
	}

	public function Header()
	{
	
	}

	public function Footer()
	{
	}

	public function setParametersArray($parameters)
	{
		$this->parametersArray = $parameters;
	}

	public function runReport()
	{
		$str = '';
		$this->AddPage();
		$this->CI->load->model('reports/pobody');
		$this->CI->pobody->getData($this->parametersArray);
		$str = $this->CI->pobody->loadView();
	}
	
	public function runApplication()
	{
		$str = '';
		$this->AddPage();
		$this->CI->load->model('reports/credit');
		$this->CI->credit->getData($this->parametersArray);
		$str = $this->CI->credit->loadView();
	}

}

/* End of file poreport.php */
/* Location: libraries/poreport.php */