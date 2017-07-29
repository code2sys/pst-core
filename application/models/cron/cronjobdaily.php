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
        $this->load->model("admin_m");
        $data = array('run_by' => 'cron', 'status' => '1');
        $this->admin_m->update_cycletrader_feeds_log($data);
        // and generate that google feed!
        sub_googleSalesXMLNew();
        // And now, generate the eBay feed
        $csv = $this->ebay_m->generateEbayFeed(0, 1);
        $data = array('run_by' => 'admin', 'status' => '1');
        $this->ebay_m->update_ebay_feeds_log($data);
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
