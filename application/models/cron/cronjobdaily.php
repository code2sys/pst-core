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
        $this->load->model("ebay_m");
        $credentials = $this->ebay_m->sub_getEbayAuthSettingsFromDb();
        if (array_key_exists("ebay_app_id", $credentials) && $credentials["ebay_app_id"] != "") {
            $this->db->query("Insert into ebay_feed_log (run_at, run_by, status) values (now(), 'cron', 0)"); // Let the regular routine handle it!
        }
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
