<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("abstractcronjob.php");

class CronJobDaily extends AbstractCronJob
{

	public function runJob()
	{
        $this->markCloseoutDate();
		$this->priceToSaleCleanUp();
		$this->catAndBrandCleanUp();
		$this->closeoutReprisingSchedule();
		$this->customProductSorting();
        $this->load->model("reporting_m");
        $this->reporting_m->getProductForcycletrader();
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
