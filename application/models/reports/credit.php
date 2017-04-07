<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/reporting/reportsection.php');

class credit extends ReportSection
{

	function __construct()
  {
    parent::__construct();
    $this->setSectionView('credit_v');
  }

	public function getData($parametersArray=NULL)
	{
		// Check to see if required parameter has been provided
		if (is_null($parametersArray))
			return FALSE;
		$this->secData = $parametersArray;
		return TRUE;
	}

}

/* End of file credit.php */
/* Location: models/reports/credit.php */
