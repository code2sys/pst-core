<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("abstractcronjob.php");

class CronJobMonthly extends AbstractCronJob
{

	public function runJob()
	{
		$this->changeHomePage();

        // Delete any cron job stats that are more than a year old...
        $this->db->query("Delete from cronjobstats where created < NOW() - INTERVAL 1 YEAR");
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
