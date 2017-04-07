<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CronJobMinute extends Master_M 
{
	
	function __construct()
  {
    parent::__construct();
  }

	public function runJob()
	{
		// Create Email for any parts at 0.00 or null
		$this->procmail();
		$this->procParts();
	}

    public function procmail($limit = 50)
	{
		$this->load->model('mail_queue_m');
		$this->mail_queue_m->processMailQueue($limit);
	}

    public function procParts($limit = 500)
	{
		$this->load->model('admin_m');
		$this->admin_m->processParts($limit);

	}

}

/* End of file cronjobminute.php */
/* Location: ./Application/models/cronjobminute.php */
