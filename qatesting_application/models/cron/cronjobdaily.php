<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CronJobDaily extends Master_M
{
	
	function __construct()
  {
    parent::__construct();
  }

	public function runJob()
	{
		$this->priceToSaleCleanUp();
		$this->catAndBrandCleanUp();
	}
	
	private function priceToSaleCleanUp()
	{
		$this->load->model('parts_m');
		$this->parts_m->reconcilePricetoSale();
	}
	
	private function catAndBrandCleanUp()
	{
		$this->load->model('parts_m');
		$this->parts_m->cleanUpCatAndBrand();
	}
}

/* End of file cronjobdaily.php */
/* Location: ./Application/models/cronjobdaily.php */
