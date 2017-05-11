<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("abstractcronjob.php");

class CronJobDaily extends AbstractCronJob
{

	public function runJob()
	{
        error_log("1");
        $this->markCloseoutDate();
        error_log("2");
		$this->priceToSaleCleanUp();
        error_log("3");
		$this->catAndBrandCleanUp();
        error_log("4");
		$this->closeoutReprisingSchedule();
        error_log("5");
		$this->customProductSorting();
        error_log("6");
        // and generate that google feed!
        sub_googleSalesXMLNew();
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
		
	private function closeoutReprisingSchedule() {
		$this->load->model('parts_m');
		$this->parts_m->closeoutReprisingSchedule();
	}
	
	private function customProductSorting() {
		$this->load->model('parts_m');
		$this->parts_m->customProductSorting();
	}


    protected function markCloseoutDate() {
        $this->load->model('parts_m');
        $this->parts_m->markCloseoutDate();
    }
}

/* End of file cronjobdaily.php */
/* Location: ./Application/models/cronjobdaily.php */
