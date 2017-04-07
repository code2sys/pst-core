<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class CronControl extends Master_Controller {

	function __construct()
  {
  		parent::__construct();
		$this->load->helper('url');
		echo "[" . date( "Y-m-d G:i:s", time()) . "] CRON JOB<br>";
		$this->output->enable_profiler(TRUE);
  }

	public function index()
	{
		// Nothing here
	}

	public function alljobs()
	{
		// Not Yet
	}

	public function minute()
	{
		$this->load->model('cron/cronjobminute', 'TheCronJob');
		$this->_runJob();
	}

	public function hourly()
	{
		$this->load->model('cron/cronjobhourly', 'TheCronJob');
		$this->_runJob();
	}

	public function daily()
	{
		$this->load->model('cron/cronjobdaily', 'TheCronJob');
		$this->_runJob();
	}

	public function weekly()
	{
		$this->load->model('cron/cronjobweekly', 'TheCronJob');
		$this->_runJob();
	}

	public function monthly()
	{
		$this->load->model('cron/cronjobmonthly', 'TheCronJob');
		$this->_runJob();
	}
	
	private function _runJob()
	{
		$this->TheCronJob->runJob();
	}
	
	
}

/* End of file croncontrol.php */
/* Location: ./application/controllers/croncontrol.php */
