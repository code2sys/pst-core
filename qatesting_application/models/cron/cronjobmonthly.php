<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CronJobMonthly extends Master_M  
{
	
	function __construct()
  {
    parent::__construct();
  }

	public function runJob()
	{
		$this->changeHomePage();
	}
	
	public function changeHomePage()
	{
		$currentHomePage = $this->account_m->getCurrentMonthPage();
		$nextMonthHomePage = $this->account_m->getNextMonthPage();
		$this->account_m->homePageUpdate($nextMonthHomePage, $currentHomePage);
	}

}

/* End of file cronjobmonthly.php */
/* Location: ./Application/models/cronjobmonthly.php */
