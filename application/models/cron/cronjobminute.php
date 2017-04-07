<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("abstractcronjob.php");

class CronJobMinute extends AbstractCronJob
{

	public function runJob()
	{
        print "Do not run this routine directly. Call either email or parts routine instead.\n";
        error_log("Do not run this routine directly. Call either email or parts routine instead.");
	}

	protected function procmail($limit = 50)
	{
		$this->load->model('mail_queue_m');
		$this->mail_queue_m->processMailQueue($limit);
	}

    protected function procParts($limit = 500)
	{
		$this->load->model('admin_m');
		$this->admin_m->processParts($limit);
	}

}

/* End of file cronjobminute.php */
/* Location: ./Application/models/cronjobminute.php */
