<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'models/master_m.php');

class ReportSection extends Master_M
{

	protected $secData = NULL;
	protected $sectionView = NULL;

	function __construct()
  {
    parent::__construct();
    $this->secData = array();
  }

	protected function setSectionView($view)
	{
		$this->sectionView = $view;
	}

	public function getData($parametersArray=NULL)
	{
	}

	public function loadView()
	{
		$str = FALSE;
		$str = $this->load->view('reporting/' . $this->sectionView, $this->secData, TRUE);
		return $str;
	}


}

/* End of file reportsection.php */
/* Location: libraries/reporting/reportsection.php */