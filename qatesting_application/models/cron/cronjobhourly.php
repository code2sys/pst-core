<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CronJobHourly extends Master_M
{
	
	function __construct()
  {
    parent::__construct();
  }

	public function runJob()
	{
		$this->documentGeneration();
	}
	
	private function documentGeneration()
	{
		$this->load->model('reporting_m');
		$dataArr = $this->reporting_m->getAppeagleAmazonXML();
		$filename = $this->config->item('upload_path')."/Appeagle-Export.txt";
		
		$flag = false;
		foreach($dataArr as $row) 
		{
			if(!$flag) 
			{
			  // display field/column names as first row
			  $data = implode("\t", array_keys($row)) . "\r\n";
			  $flag = true;
			}
			array_walk($row, array($this, 'cleanData'));
			$data .= implode("\t", array_values($row)) . "\r\n";
			
		}
		$fp = fopen($filename, 'w');
		fwrite($fp, $data);
		fclose($fp);
	}

}

/* End of file cronjobhourly.php */
/* Location: ./Application/models/cronjobhourly.php */
