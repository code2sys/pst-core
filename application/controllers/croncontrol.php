<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class CronControl extends Master_Controller {

    // JLB 01-06-19
    // This is here as an example
    public function setUsernamePassword($username, $password) {
        $this->load->model("Ftpusers");
        if ($this->Ftpusers->setUsernamePassword($username, $password)) {
            print "Success! Try it out.\n";
        } else {
            print "Failure!\n";
        }
    }

    // JLB 11-25-18
    // Fix all those showcase pages that already exist...
    public function fixShowcasePages() {
        global $PSTAPI;
        initializePSTAPI();
        $pages = $PSTAPI->pages()->fetch();
        foreach ($pages as $p) {
            $p->fixShowcaseSegment();
        }
    }

    // JLB 11-09-18
    // Fix the description from CRS...
    public function fixCRSDescriptions() {
        global $PSTAPI;
        initializePSTAPI();

        // get the matching bikes...
        $bikes = $PSTAPI->motorcycle()->fetch(array(
            "source" => "PST",
            "customer_set_description" => 0,
            "lightspeed_set_description" => 0
        ));

        foreach ($bikes as $bike) {
            fixCRSBike($bike);
        }
    }



    public function encryptWord($word) {
        $this->load->library("encrypt");
        print $this->encrypt->encode($word);
    }

    // JLB 09-18-18
    public function fixOrders() {
        $query = $this->db->query("select `order`.id, `order_product`.part_id from `order` join order_product on `order`.id = order_product.order_id join order_transaction on `order`.id = order_transaction.order_id where order_product.product_sku = '';");

        global $PSTAPI;
        initializePSTAPI();

        foreach ($query->result_array() as $row) {
            $order_id = $row["id"];
            $part_id = $row["part_id"];
            print "Fixing order $order_id part $part_id \n";
            $partpartnumber = $PSTAPI->partPartNumber()->fetch(array("part_id" => $part_id));
            if (count($partpartnumber) == 1) {
                $partnumber_id = $partpartnumber[0]->get("partnumber_id");
                $partnumber = $PSTAPI->partnumber()->get($partnumber_id);
                $mx_partnumber = $partnumber->get("partnumber");

                // Step #1: Update the order product
                $this->db->query("Update order_product set product_sku = ? where order_id = ? and part_id = ?", array(
                    $mx_partnumber, $order_id, $part_id
                ));

                // Step #2: We have to get the part name
                $part = $PSTAPI->part()->get($part_id);

                $this->db->query("Insert into order_product_details (order_id, partnumber_id, name, answer, part_id, partnumber, question, sale, stock_code) values (?, ?, ?, '', ?, ?, '', ?, 'Normal')", array(
                    $order_id, $partnumber->id(), $part->get("name"), $part_id, $mx_partnumber, $partnumber->get("price")
                ));

                print "Fixed order $order_id part $part_id \n";

            } else {
                print "Wrong number of partnumbers: " . count($partpartnumber) . "\n";
            }
        }

    }

    // JLB 09-04-18
    // Accelerate all of these so that we can do the query on them...
    public function denormalizeUnits() {
        global $PSTAPI;
        initializePSTAPI();
        $PSTAPI->denormalizedmotorcycle()->moveAllMotorcycles();
    }

    // JLB 08-21-18
    // We need something that just tries again to match the available bikes for CRS
    public function matchIfYouCanCRS() {
        global $PSTAPI;
        initializePSTAPI();

        // OK, the goal here is to query those bikes that have null trims
        $motorcycles = $PSTAPI->motorcycle()->fetch(array(
            "crs_trim_id" => null
        ), true);

        $this->load->model("CRS_m");

        foreach ($motorcycles as $m) {
            $this->CRS_m->matchIfYouCan($m["id"], $m["vin_number"], $m["make"], $m["model"], $m["year"], $m["codename"] == "" ? $m["title"] : $m["codename"], $m["retail_price"]) ;
        }
    }

    // JLB 07-17-18
    // We need to be able to run this manually, sometimes
    public function introspectForLightspeed() {
        global $PSTAPI;
        initializePSTAPI();
        $PSTAPI->order()->introspectLightspeed();
    }

    // JLB 06-21-18
    // Who knew that you couldn't just ask for a run?
    public function runEbay($debug = 0) {
        $this->load->model("ebay_m");
        $this->ebay_m->debug = ($debug > 0);
        $this->ebay_m->generateEbayFeed(0, 1, $debug > 0);
    }

    // JLB 04-25-18
    // This is designed to review everything for CRS...a deep cleaning
    public function deepCleanCRS() {
        global $PSTAPI;
        initializePSTAPI();
        $matches = $PSTAPI->motorcycle()->getCRSMatched();

        $this->load->model("crscron_m");

        foreach ($matches as $m) {
            $this->crscron_m->refreshCRSData($m->id(), true);
        }
    }

    // This is to pull a stream
    public function fetchMotorcycleDealerFeeds($debug = 0) {
        $this->load->model("Mdfeed_m");
        $this->Mdfeed_m->get_md_feed();
    }

    // We have to just migrate it
    public function migratePagesIssue80() {
        $query = $this->db->query("Select * from pages");
        foreach ($query->result_array() as $page) {
            $page_id = $page["id"];

            $ordinal = 1;

            // are there any videos?
            $video_query = $this->db->query("Select id from top_videos where page_id = ?", array($page_id));
            $videos = $video_query->result_array();

            if (count($videos) > 0) {
                $this->db->query("insert into page_section (page_id, ordinal, type) values (?, ?, 'Video')", array($page_id, $ordinal));
                $ordinal++;
                $page_section_id = $this->db->insert_id();

                // now, update them...
                $this->db->query("Update top_videos set page_section_id = ? where page_id = ?", array($page_section_id, $page_id));
            }

            // are there any sliders?
            $slider_query = $this->db->query("Select id from slider where pageId = ?", array($page_id));
            $sliders = $slider_query->result_array();

            if (count($sliders) > 0) {
                $this->db->query("insert into page_section (page_id, ordinal, type) values (?, ?, 'Slider')", array($page_id, $ordinal));
                $ordinal++;
                $page_section_id = $this->db->insert_id();

                // now, update them...
                $this->db->query("Update slider set page_section_id = ? where pageId = ?", array($page_section_id, $page_id));
            }


            // Finally, do the textboxes...
            $textbox_query = $this->db->query("Select id from textbox where pageId = ? order by `order`", array($page_id));
            foreach ($textbox_query->result_array() as $textbox) {
                $this->db->query("insert into page_section (page_id, ordinal, type) values (?, ?, 'Textbox')", array($page_id, $ordinal));
                $ordinal++;
                $page_section_id = $this->db->insert_id();

                // now, update them...
                $this->db->query("Update textbox set page_section_id = ? where id = ?", array($page_section_id, $textbox["id"]));

            }
        }
    }

    function __construct()
    {
        parent::__construct();
		$this->load->helper('url');
        // Require them to be on the CLI.
        if (!$this->input->is_cli_request()) {
            print "CLI Only.\n";
            exit();
        }

        @set_time_limit(7200);

    }

    public function fixPendingLightspeed() {
        $this->load->model("cron/cronjobhourly", "cronjobhourly");
        $this->cronjobhourly->fixPendingLightspeed();
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
        $this->introspectForLightspeed(); // shift them flags!
		$this->_runJob('hourly');
	}

	public function daily()
	{
	    // New - MDFeed daily
        $this->dailyMDFeedUnits();

	    // First, run the lightspeed units
        $this->dailyLightspeedUnits();

        // Denormalize everything
        $this->denormalizeUnits();

        // Then, do the regular daily routine
		$this->_runJob('daily');

		// Then, do the lightspeed parts
        $this->dailyLightspeedParts();

        // Then, migrate CRS color codes
        $this->getCRSColorCodes();
	}

	public function cleanUpCRS() {
        $this->load->model("CRSCron_m");
        $this->CRSCron_m->cleanUpCRS();
    }

	public function weekly()
	{
	    if (false !== getCRSStructure()) {
            $this->checkForCRSMigration(1);
        }
        $this->refreshCRSData();
		$this->_runJob('weekly');
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
        if (is_null($offroad_flag)) {
            $offroad_flag = 0;
        }

        if ($offroad_flag !== 1) {
            $offroad_flag = 0;
        }

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
    public function checkForCRSMigration($force = 0, $debug = 0) {
        if ($debug > 0) {
            error_reporting(E_ALL);
        }

        // Is there anything pending?
        $query = $this->db->query("select * from crspull_feed_log where status = 0");
        $results = $query->result_array();
        if (count($results) == 0 && $force == 0) {
            return;
        } else if (count($results) == 0) {
            // Record something.
            $this->db->query("insert into crspull_feed_log (run_at, run_by, status, processing_start) values (now(), 'cron', 1, now())");
        } else {
            $this->db->query("update crspull_feed_log set status = 1, processing_start = now() where status = 0");
        }

        // is there a CRS configuration file?
        $crs_struct = getCRSStructure();

        if (FALSE !== $crs_struct) {

            $uniqid = uniqid("delete_crs");
            $this->db->query("Update motorcycle set uniqid = ? where source = 'PST' and crs_trim_id > 0", array($uniqid));

            // Now, you have to add each of those, in order...
            foreach ($crs_struct as $c) {
                if ($debug > 0) {
                    print "Requesting product line: " . $c["crs_machinetype"] . ", " . $c["crs_make_id"] . ", " . $c["year"] . "\n";
                }
                $this->addProductLine($c["crs_machinetype"], $c["crs_make_id"], "N", $c["year"], $c["year"], $debug);
            }

            // we should delete all other things hanging around
            $this->db->query("Delete from motorcycle where source = 'PST' and crs_trim_id > 0 and uniqid = ? and `condition` = 1 ", array($uniqid));

            // clear it
            $this->db->query("Update crspull_feed_log set status = 2, processing_end = now() where status = 1");

        } else {
            print "No CRS structure found. \n";
        }

        $this->cleanUpCRS();
    }

    public function getExcludedTrimIDs() {
        $query = $this->db->query("Select distinct crs_trim_id from motorcycle where source != 'PST' and crs_trim_id > 0 and deleted = 0");
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
    protected function _getCRSDestinationFee() {
        $query = $this->db->query("Select crs_destination_charge from contact where id = 1");
        $status = 0;

        foreach ($query->result_array() as $row) {
            $status = $row["crs_destination_charge"];
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
	public function addProductLine($machine_type, $make_id, $mode = "C", $starting_year = 0, $ending_year = 0, $debug = 0) {
        $this->load->model("CRS_m");

        $uniqid = uniqid("");

        if ($debug > 0) {
            print "Fetching trims \n";
        }


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

        if ($debug > 0) {
            print "Fetched trims.\n";
        }


        // clear the unique IDs...
        $this->db->query("Update motorcycle set uniqid = '' where crs_machinetype = ? and crs_make_id = ? and `condition` = 1 and source = 'PST'", array($machine_type, $make_id));

        $stock_status = $this->_getStockStatusCRS();
        $crs_destination_fee = $this->_getCRSDestinationFee();

        // We sometimes need this in hand - the off-road type...

        // Now we have to get $category_id .. for now, we're going to call it Stock...

        // Now, we have to enter them...
        foreach ($matching_motorcycles as $m) {

            // JLB 11-15-18 - I added a more complicated approval function. This may be redundant.
            $crs_make = $m["make"];
            $crs_display_name = $m["display_name"];
            if (function_exists("CRSApproveFunction") && !CRSApproveFunction($crs_make, $crs_display_name)) {
                continue; // skip it; we are not doing this bike.
            }

            // Is there one of these?
            $crs_model = $m["model"];
            $crs_model_id = $m["model_id"];
            $crs_make_id = $m["make_id"];
            $crs_machinetype = $m["machine_type"];
            $crs_trim = $m["trim"];
            $crs_trim_id = $m["trim_id"];

            if ($debug > 0) {
                print "Processing Trim $crs_trim_id make $crs_make model $crs_model \n";
            }

            // Is there one of these?
            $query = $this->db->query("Select * from motorcycle where `condition` = 1 and crs_trim_id = ? and (source = 'PST' or deleted = 0)", array($crs_trim_id));
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
                    $this->db->query("Insert into motorcycle (title, description, status, `condition`, sku, engine_type, transmission, retail_price, sale_price, data, margin, profit, category, vehicle_type, year, make, model, color, craigslist_feed_status, cycletrader_feed_status, crs_trim_id, crs_machinetype, crs_model_id, crs_make_id, crs_year, uniqid, source, crs_version_number, destination_charge, stock_status) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Out Of Stock')", array(
                        preg_replace("/[^" . $this->config->item("permitted_uri_chars") . "]/i", "", ($title = $trim["year"]. " " . $trim["make"] . " " . $trim["display_name"])),
                        generateCRSDescription($title, $trim["description"]),
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
                        $trim["version_number"],
                        $crs_destination_fee
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


    public function preserveMajorUnitsChangedField($field) {
	    $this->load->model("lightspeed_m");
	    $this->lightspeed_m->preserveMajorUnitsChangedField($field);
    }

	public function refreshCRSData($motorcycle_id = 0, $deep_clean = 0) {
	    $this->load->model("CRSCron_m");
	    $this->CRSCron_m->refreshCRSData($motorcycle_id, intVal($deep_clean) > 0);
	    $this->cleanUpCRS();
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
    public function getLightspeedUnitsCSV() {
        $this->load->model("Lightspeed_m");
        $this->Lightspeed_m->get_units_csv();
    }
    public function getLightspeedParts() {
        $this->load->model("Lightspeed_m");
        $this->Lightspeed_m->get_parts();
    }
    public function repairLightspeedParts($debug = 0) {
        $this->load->model("Lightspeed_m");
        $this->Lightspeed_m->repair_parts($debug);
    }
    public function getMajorUnits() {
        $this->load->model("Lightspeed_m");
        $this->Lightspeed_m->get_major_units();
    }
    public function getCRSColorCodes() {
        $this->load->model("color_m");
        $this->color_m->getCRSColorCodes();
    }

    public function dailyLightspeedParts() {
        $this->sub_dailyLightspeed("lightspeedpart_feed_log", "get_parts");
    }

    protected function sub_dailyLightspeed($log_table, $function_to_call) {
        // JLB 12-18-17
        // Lightspeed, if you have it...
        if (defined('ENABLE_LIGHTSPEED') && ENABLE_LIGHTSPEED) {
            $this->sub_sub_loggedDaily($log_table, $function_to_call, "Lightspeed_m", "Lightspeed");
        }
    }

    protected function sub_sub_loggedDaily($log_table, $function_to_call, $model, $name, $debug = 0) {
        // insert into the table to log this..
        if ($debug > 0) {
            print "Inserting into the $log_table table as CRON \n";
        }
        $this->db->query("Insert into $log_table (status, processing_start, run_by) values (1, now(), 'cron')");

        // OK, we should attempt to pull the major unit lightspeed parts..
        $error_string = "";
        try {
            $this->load->model("$model");
            if ($debug > 0) {
                print "Calling $model $function_to_call \n";
            }
            $this->$model->$function_to_call($debug); // that should fetch all those things, great.
        } catch(Exception $e) {
            $error_string = $e->getMessage();
            if ($e->getMessage() != "$name credentials not found.") {
                print "$name error: " . $e->getMessage() . "\n";
            }
        }

        if ($debug > 0) {
            print "All done \n";
        }

        // and update it...
        $this->db->query("Update $log_table set status = 2, processing_end = now(), error_string = ? where run_by = 'cron' and status = 1", array($error_string));
    }

    public function dailyLightspeedUnits() {
        $this->sub_dailyLightspeed("lightspeed_feed_log", "get_major_units");
    }

    public function dailyMDFeedUnits($debug = 0) {
        if (defined('ENABLE_MD_FEED') && ENABLE_MD_FEED) {
            if ($debug > 0) {
                print "Calling get_major_units \n";
            }
            $this->sub_sub_loggedDaily("mdfeed_feed_log", "get_major_units", "Mdfeed_m", "MDFeed", $debug);
        } else {
            if ($debug > 0) {
                print "ENABLE_MD_FEED not defined \n";
            }
        }
    }

    // JLB 11-16-18
    public function loadFactoryShowroom() {
        // Temporarily prevent the loading of the showcase.
        if (!defined('FACTORY_SHOWROOM') || !FACTORY_SHOWROOM) {
            return;
        }
        $this->load->model("Showcasemodel");
        $this->Showcasemodel->loadShowcase();
        $this->fixShowcasePages();
    }

    // JLB 12-14-18
    // Gronifies disgronified image filenames.
    public function gronifyImageNames() {
        $query = $this->db->query(" select * from motorcycleimage where image_name  REGEXP '[^\-a-zA-Z0-9\_\.]' and source = 'Admin'");

        foreach ($query->result_array() as $row) {
            print "Motorcycle Image ID " . $row["id"] . " filename " . $row["image_name"] . "\n";
            $gronified_filename = gronifyForFilename($row["image_name"]);
            print "Will be gronified to: " .$gronified_filename . "\n";
            $full_filename = STORE_DIRECTORY . "/html/media/" . $row["image_name"];
            $new_filename = STORE_DIRECTORY . "/html/media/" . $gronified_filename;

            if (file_exists($full_filename)) {
                print "Moving from $full_filename to $new_filename \n";
                rename($full_filename, $new_filename);
                $this->db->query("update motorcycleimage set image_name = ? where id = ? limit 1", array($gronified_filename, $row["id"]));
            }
        }
    }

}

/* End of file croncontrol.php */
/* Location: ./application/controllers/croncontrol.php */
