<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("abstractcronjob.php");

class CronJobMinute extends AbstractCronJob
{

	public function runJob()
	{
        print "Do not run this routine directly. Call either email or parts routine instead.\n";
        error_log("Do not run this routine directly. Call either email or parts routine instead.");
	}

    public function fixPendingCycleTrader() {
        $query = $this->db->query("select * from cycle_feed_log where run_by = 'admin' and status = 0");
        $results = $query->result_array();

        if (count($results) > 0) {
            // do the eBay thing...
            $this->load->model("reporting_m");
            $this->reporting_m->getProductForcycletrader();
            foreach ($results as $row) {
                $this->db->query("Update cycle_feed_log set status = 1 where id = ?", $row["id"]);
            }
        }
    }

    public function fixPendingGoogle() {
        $query = $this->db->query("select * from google_feed_log where run_by = 'admin' and status = 0");
        $results = $query->result_array();

        if (count($results) > 0) {
            $this->load->model("reporting_m");
            $this->load->model("admin_m");

            // and generate that google feed!
            sub_googleSalesXMLNew();

            foreach ($results as $row) {
                $this->db->query("Update google_feed_log set status = 1 where id = ?", $row["id"]);
            }
        }
    }

	protected function feeds() {
        // Anything from cycle trader?
        $this->fixPendingCycleTrader();

        // Anything from Google?
        $this->fixPendingGoogle();

        $this->load->model("ebay_m");
        // PLEASE NOTE: That is going to die if it doesn't have credentials, so it MUST BE LAST.
        $this->ebay_m->dieSilentlyOnBadCredentials(true);
        $this->ebay_m->getOrders();
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
