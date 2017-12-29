<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class CronControl extends Master_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('url');
        // Require them to be on the CLI.
        if (!$this->input->is_cli_request()) {
            print "CLI Only.\n";
            exit();
        }
    }

    public function encryptPassword($password = "") {

        if ($password == "") {
            print "Enter password: \n";
            $f = fopen( 'php://stdin', 'r' );
            $password = trim(fgets($f));
        }
        print "Encrypting password: $password \n";
        $this->load->library('encrypt');
        print $this->encrypt->encode($password) . "\n";
    }

	public function external_process_parts() {
        $this->load->model('admin_m');
        $this->admin_m->processParts(10000);
    }

	public function index()
	{
		// Nothing here
	}

	public function alljobs()
	{
		// Not Yet
	}

	public function feeds() {
        $this->_runJob('feeds');
    }

	public function emails() {
        $this->_runJob('mail');
    }

    public function processparts() {
        $this->_runJob('parts');
    }

	public function minute()
	{
		$this->_runJob('minute');
	}

	public function hourly()
	{
	    $this->checkForCRSMigration(); // let's put it on the front...
		$this->_runJob('hourly');
	}

	public function daily()
	{
		$this->_runJob('daily');
	}

	public function weekly()
	{
		$this->_runJob('weekly');
		$this->refreshCRSData();
	}

	public function monthly()
	{
		$this->_runJob('monthly');
	}
	
	private function _runJob($job_model)
	{
        // Can we obtain the lock?
        $fp = fopen(STORE_DIRECTORY . "/cronlock/" . $job_model . ".lock", "w") or die("Could not obtain cronlock: " . STORE_DIRECTORY . "/cronlock/" . $job_model . ".lock");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            // Load the model
            $this->load->model('cron/cronjob' . $job_model, 'TheCronJob');

            // Begin run of job type - record in the database
            $this->db->query("Insert into cronjobstats (jobtype) values (?)", array($job_model));
            $stat_id = $this->db->insert_id();


            $this->TheCronJob->runJob();

            // Record completion of the job
            $this->db->query("Update cronjobstats set completed = 1, when_completed = now() where cronjobstats_id = ?", array($stat_id));

            // release the lock
            flock($fp, LOCK_UN);
            fclose($fp);

        } else {
            print "Could not obtain lock $job_model\n";
            error_log("Could not obtain lock $job_model");
        }


	}
	
	public function markCloseoutDate() {
		$this->load->model('parts_m');
		$this->parts_m->markCloseoutDate();
	}
	
	public function closeoutReprisingSchedule() {
		$this->load->model('parts_m');
		$this->parts_m->closeoutReprisingSchedule();
	}

	protected $stock_moto_category_cache;
	protected function _getStockMotoCategory($name = "Dealer") {

	    if (!isset($this->stock_moto_category_cache) || !is_array($this->stock_moto_category_cache)) {
	        $this->stock_moto_category_cache = array();
        }

        if (array_key_exists($name, $this->stock_moto_category_cache)) {
	        return $this->stock_moto_category_cache[$name];
        }

        $query = $this->db->query("Select * from motorcycle_category where name = ?", array($name));
        $id = 0;
        foreach ($query->result_array() as $row) {
            $id = $row["id"];
        }

        if ($id == 0) {
            $this->db->query("Insert into motorcycle_category (name, date_added) values (?, now())", array($name));
            $id = $this->db->insert_id();
        }

        $this->stock_moto_category_cache[$name] = $id;

        return $id;
    }


    protected $_preserveMachineMotoType;
    protected function _getMachineTypeMotoType($machine_type, $offroad_flag) {
        if (!isset($this->_preserveMachineMotoType)) {
            $this->_preserveMachineMotoType = array();
        }

        $key = sprintf("%s-%d", $machine_type, $offroad_flag);
        if (array_key_exists($key, $this->_preserveMachineMotoType)) {
            return $this->_preserveMachineMotoType[$key];
        }

        $type_id = 0;
        $query = $this->db->query("Select id from motorcycle_type where crs_type = ? and offroad = ?", array($machine_type, $offroad_flag));
        foreach ($query->result_array() as $row) {
            $type_id = $row["id"];
        }

        if ($type_id == 0) {
            throw new \Exception("Could not find a match for _getMachineTypeMotoType($machine_type, $offroad_flag)");
        }

        $this->_preserveMachineMotoType[$key] = $type_id;
        return $type_id;
    }

    protected function SKUInUse($sku) {
        $query = $this->db->query("select count(*) as cnt from motorcycle where sku = ?", array($sku));
        return 0 < $query->result_array()[0]["cnt"];
    }

    protected function getNextCRSSKU() {
        $query = $this->db->query("select count(*) as cnt from motorcycle where sku like 'D%';");
        $count = $query->result_array()[0]["cnt"];

        $count++;
        while ($this->SKUInUse("D" . $count)) {
            $count++;
        }
        return "D" . $count;
    }

    /*
     * This is to check for CRS migration
     */
    public function checkForCRSMigration() {
        // Is there anything pending?
        $query = $this->db->query("select * from crspull_feed_log where status = 0");
        $results = $query->result_array();
        if (count($results) == 0) {
            return;
        } else {
            $this->db->query("update crspull_feed_log set status = 1, processing_start = now() where status = 0");
        }

        // is there a CRS configuration file?
        $filename = "/var/www/crs_configs/" . STORE_NAME;

        if (file_exists($filename)) {
            $crs_struct = json_decode(file_get_contents($filename), true);

            $uniqid = uniqid("delete_crs");
            $this->db->query("Update motorcycle set uniqid = ? where source = 'PST' and crs_trim_id > 0", array($uniqid));

            // Now, you have to add each of those, in order...
            foreach ($crs_struct as $c) {
                $this->addProductLine($c["crs_machinetype"], $c["crs_make_id"], "N", $c["year"], $c["year"]);
            }

            // we should delete all other things hanging around
            $this->db->query("Delete from motorcycle where source = 'PST' and crs_trim_id > 0 and uniqid = ? and `condition` = 1 ", array($uniqid));

            // clear it
            $this->db->query("Update crspull_feed_log set status = 2, processing_end = now() where status = 1");

        } else {
            print "Not found: $filename \n";
        }

    }

    public function getExcludedTrimIDs() {
        $query = $this->db->query("Select distinct crs_trim_id from motorcycle where source != 'PST' and crs_trim_id > 0");
        $trim_LUT = array();
        foreach ($query->result_array() as $row) {
            $trim_LUT[$row["crs_trim_id"]] = true;
        }
        return $trim_LUT;
    }

    protected function _getStockStatusCRS() {
        $query = $this->db->query("Select out_of_stock_active from contact where id = 1");
        $status = 0;

        foreach ($query->result_array() as $row) {
            $status = $row["out_of_stock_active"];
        }

        return $status;
    }

	/*
	 * The point of this one is to be able to request some specific information and then to load them.
	 * Basically, you have some modes:
	 * "C": Just gets the current ones
	 * "N": Gets the new ones
	 * "A": Adds them
	 *
	 */
	public function addProductLine($machine_type, $make_id, $mode = "C", $starting_year = 0, $ending_year = 0) {
        $this->load->model("CRS_m");

        $uniqid = uniqid("");

        if ($mode == "C") {
            $matching_motorcycles = $this->CRS_m->getTrims(array(
                "current" => true,
                "make_id" => $make_id,
                "machine_type" => $machine_type
            ));


        } else if ($starting_year > 0) {
            if ($ending_year < $starting_year) {
                $ending_year = $starting_year;
            }

            $matching_motorcycles = array();
            for ($y = $starting_year; $y <= $ending_year; $y++) {
                $matching_motorcycles = array_merge($matching_motorcycles, $this->CRS_m->getTrims(array(
                    "make_id" => $make_id,
                    "machine_type" => $machine_type,
                    "year" => $y
                )));
            }
        } else {
            throw new Exception("You must provide a starting year.");
        }

        // clear the unique IDs...
        $this->db->query("Update motorcycle set uniqid = '' where crs_machinetype = ? and crs_make_id = ? and `condition` = 1 and source = 'PST'", array($machine_type, $make_id));

        $stock_status = $this->_getStockStatusCRS();

        // We sometimes need this in hand - the off-road type...

        // Now we have to get $category_id .. for now, we're going to call it Stock...

        // Now, we have to enter them...
        foreach ($matching_motorcycles as $m) {
            // Is there one of these?
            $crs_model = $m["model"];
            $crs_model_id = $m["model_id"];
            $crs_make = $m["make"];
            $crs_make_id = $m["make_id"];
            $crs_machinetype = $m["machine_type"];
            $crs_trim = $m["trim"];
            $crs_display_name = $m["display_name"];
            $crs_trim_id = $m["trim_id"];

            // Is there one of these?
            $query = $this->db->query("Select * from motorcycle where `condition` = 1 and crs_trim_id = ?", array($crs_trim_id));
            $results = $query->result_array();

            if (count($results) == 0) {
                // you need to add them...
                $trim = $this->CRS_m->getTrim($crs_trim_id);
                // OK, we need to insert it...
                if (count($trim) > 0) {
                    $trim = $trim[0];

                    $category_id = $this->_getStockMotoCategory();


                    // OK, we have to add it, and then we have to add the motorcycle... but first we have to get some of the specs
                    $retail_price = $sale_price = $trim["msrp"];
                    $this_machine_type = $this->_getMachineTypeMotoType($machine_type, $trim["offroad"]);

                    $engine_type = ""; // 30003
                    $transmission = ""; // 40002
                    // look for 20002 for a msrp + destination fee..
                    // JLB 2017-11-27
                    // 10011 is the category...
                    // Further, if is Off-Road, then we have to re-type the machine it's a MOT from Street Bike to Off-Road.

                    foreach ($trim["specifications"] as $s) {
                        $attribute_id = $s["attribute_id"];

                        if ($attribute_id == 30003) {
                            $engine_type = $s["text_value"];
                        } else if ($attribute_id == 40002) {
                            $transmission = $s["text_value"];
                        } else if ($attribute_id == 20002) {
                            $retail_price = $sale_price= $s["text_value"];
                        } else if ($attribute_id == 10011) {
                            $category_id = $this->_getStockMotoCategory($s["text_value"]);
                        }
                    }

                    // JLB 11-27-17: We just set the destination charge = 1.
                    $this->db->query("Insert into motorcycle (title, description, status, `condition`, sku, engine_type, transmission, retail_price, sale_price, data, margin, profit, category, vehicle_type, year, make, model, color, craigslist_feed_status, cycletrader_feed_status, crs_trim_id, crs_machinetype, crs_model_id, crs_make_id, crs_year, uniqid, source, crs_version_number, destination_charge, stock_status) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'Out Of Stock')", array(
                        preg_replace("/[^" . $this->config->item("permitted_uri_chars") . "]/i", "", ($title = $trim["year"]. " " . $trim["make"] . " " . $trim["display_name"])),
                        $title,
                        $stock_status,
                        1,
                        $this->getNextCRSSKU(),
                        $engine_type,
                        $transmission,
                        $retail_price,
                        $sale_price,
                        json_encode(array(
                            "total_cost" => $retail_price,
                            "unit_cost" => $retail_price,
                            "parts" => "",
                            "service" => "",
                            "auction_fee" => "",
                            "misc" => ""
                        )),
                        0.00,
                        0.00,
                        $category_id,
                        $this_machine_type,
                        $trim["year"],
                        $trim["make"],
                        $trim["display_name"],
                        "N/A",
                        0,
                        0,
                        $trim["trim_id"],
                        $trim["machine_type"],
                        $trim["model_id"],
                        $trim["make_id"],
                        $trim["year"],
                        $uniqid,
                        'PST',
                        $trim["version_number"]

                    ));

                    $motorcycle_id = $this->db->insert_id();

                    // We need to insert the trim_photo
                    $this->db->query("Insert into motorcycleimage (motorcycle_id, image_name, date_added, description, priority_number, external, version_number, source, crs_thumbnail) values (?, ?, now(), ?, 1, 1, ?, 'PST', 1) on duplicate key update source = 'PST'", array($motorcycle_id, $trim["trim_photo"], 'Trim Photo: ' . $trim['display_name'], $trim["version_number"]));

                }
            }
        }

        // We have to purge them...
        if ($mode == 'C' || $mode == 'A') {
            $this->db->query("Delete from motorcycle where uniqid = '' and source = 'PST' and crs_machinetype = ? and crs_make_id = ? and `condition` = 1", array($machine_type, $make_id));
        }

        $this->refreshCRSData();
    }


	public function refreshCRSData() {
	    $this->load->model("CRSCron_m");
	    $this->CRSCron_m->refreshCRSData();
    }

    /*
     * JLB Added these for Lightspeed
     */
    public function getLightspeedUnitsXML() {
        $this->load->model("Lightspeed_m");
        $this->Lightspeed_m->get_units_xml();
    }
    public function getLightspeedPartsXML() {
        $this->load->model("Lightspeed_m");
        $this->Lightspeed_m->get_parts_xml();
    }

}

/* End of file croncontrol.php */
/* Location: ./application/controllers/croncontrol.php */
