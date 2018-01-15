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

		// JLB 12-18-17
        // Lightspeed, if you have it...
        if (defined('ENABLE_LIGHTSPEED') && ENABLE_LIGHTSPEED) {
            // insert into the table to log this..
            $this->db->query("Insert into lightspeed_feed_log (status, processing_start, run_by) values (1, now(), 'cron')");

            // OK, we should attempt to pull the major unit lightspeed parts..
            $error_string = "";
            try {
                $this->load->model("Lightspeed_m");
                $this->Lightspeed_m->get_major_units(); // that should fetch all those things, great.
                $this->Lightspeed_m->get_parts(); // that should fetch all those things, great.
            } catch(Exception $e) {
                $error_string = $e->getMessage();
                if ($e->getMessage() != "Lightspeed credentials not found.") {
                    print "Lightspeed error: " . $e->getMessage() . "\n";
                }
            }

            // and update it...
            $this->db->query("Update lightspeed_feed_log set status = 2, processing_end = now(), error_string = ? where run_by = 'cron' and status = 1", array($error_string));
        }


        $this->load->model("reporting_m");
        $this->reporting_m->getProductForcycletrader();
        $this->load->model("admin_m");
        $data = array('run_by' => 'cron', 'status' => '1');
        $this->admin_m->update_cycletrader_feeds_log($data);
        // and generate that google feed!
        sub_googleSalesXMLNew();
        // And now, generate the eBay feed
        $this->load->model("ebay_m");
        $csv = $this->ebay_m->generateEbayFeed(0, 1);
        $data = array('run_by' => 'cron', 'status' => '1');
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
