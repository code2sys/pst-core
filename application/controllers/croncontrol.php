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

	protected function _getStockMotoCategory() {
        $name = "Dealer";
        $query = $this->db->query("Select * from motorcycle_category where name = ?", array($name));
        $id = 0;
        foreach ($query->result_array() as $row) {
            $id = $row["id"];
        }

        if ($id == 0) {
            $this->db->query("Insert into motorcycle_category (name, date_added) values (?, now())", array($name));
            $id = $this->db->insert_id();
        }
        return $id;
    }

    protected function _getMachineTypeMotoType($machine_type) {
        $type_id = 0;
        $query = $this->db->query("Select id from motorcycle_type where crs_type = ?", array($machine_type));
        foreach ($query->result_array() as $row) {
            $type_id = $row["id"];
        }
        return $type_id;
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
        $this->db->query("Update motorcycle set uniqid = '' where crs_machinetype = ? and crs_make_id = ? and `condition` = 1", array($machine_type, $make_id));

        $vehicle_type = $this->_getMachineTypeMotoType($machine_type);
        if ($vehicle_type == 0) {
            throw new Exception("No type found for $machine_type ");
        }

        // Now we have to get $category_id .. for now, we're going to call it Stock...
        $category_id = $this->_getStockMotoCategory();

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
            $query = $this->db->query("Select * from motorcycle where `condition` = 1 and source = 'CRS' and crs_trim_id = ?", array($crs_trim_id));
            $results = $query->result_array();

            if (count($results) == 0) {
                // you need to add them...
                $trim = $this->CRS_m->getTrim($crs_trim_id);
                // OK, we need to insert it...
                if (count($trim) > 0) {
                    $trim = $trim[0];

                    // OK, we have to add it, and then we have to add the motorcycle... but first we have to get some of the specs
                    $retail_price = $sale_price = $trim["msrp"];

                    $engine_type = ""; // 30003
                    $transmission = ""; // 40002
                    // look for 20007 for a msrp + destination fee..

                    foreach ($trim["specifications"] as $s) {
                        $attribute_id = $s["attribute_id"];

                        if ($attribute_id == 30003) {
                            $engine_type = $s["text_value"];
                        } else if ($attribute_id == 40002) {
                            $transmission = $s["text_value"];
                        } else if ($attribute_id == 20007) {
                            $retail_price = $sale_price= $s["text_value"];
                        }
                    }

                    $this->db->query("Insert into motorcycle (title, description, status, `condition`, sku, engine_type, transmission, retail_price, sale_price, data, margin, profit, category, vehicle_type, year, make, model, color, craigslist_feed_status, cycletrader_feed_status, crs_trim_id, crs_machinetype, crs_model_id, crs_make_id, crs_year, uniqid, source, crs_version_number) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
                        $trim["year"]. " " . $trim["make"] . " " . $trim["display_name"],
                        '',
                        1,
                        1,
                        'CRS' . $trim['trim_id'],
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
                        $vehicle_type,
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
                        'CRS',
                        $trim["version_number"]
                    ));

                    $motorcycle_id = $this->db->insert_id();

                    // We need to insert the trim_photo
                    $this->db->query("Insert into motorcycleimage (motorcycle_id, image_name, date_added, description, priority_number, external, version_number, source) values (?, ?, now(), ?, 1, 1, ?, 'CRS')", array($motorcycle_id, $trim["trim_photo"], 'Trim Photo: ' . $trim['display_name'], $trim["version_number"]));

                }
            }
        }

        // We have to purge them...
        if ($mode == 'C' || $mode == 'A') {
            $this->db->query("Delete from motorcycle where uniqid = '' and source = 'CRS' and crs_machinetype = ? and crs_make_id = ? and `condition` = 1", array($machine_type, $make_id));
        }

        $this->refreshCRSData();
    }

	public function refreshCRSData() {
        // OK, this is straightforward, we have to get the motorcycles that have trim IDs, and we have to update the specifications...
        $query = $this->db->query("Select motorcycle.id as motorcycle_id, crs_trim_id, IfNull(max(motorcyclespec.version_number), 0) as version_number from motorcycle left join motorcyclespec on motorcycle.id = motorcyclespec.motorcycle_id where crs_trim_id > 0 group by motorcycle.id");

        // we're going to refresh the data for this...
        $this->load->model("CRS_m");

        $matching_motorcycles = $query->result_array();

        foreach ($matching_motorcycles as $m) {
            $motorcycle_id = $m["motorcycle_id"];
            $trim_id = $m["crs_trim_id"];
            $version_number = $m["version_number"];

            // get the attributes...
            $attributes = $this->CRS_m->getTrimAttributes($trim_id, $version_number);

            // Now, you have to update them all...
            foreach ($attributes as $a) {
                $this->db->query("Insert into motorcyclespec (version_number, value, feature_name, attribute_name, type, external_package_id, motorcycle_id, final_value, source, crs_attribute_id) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) on duplicate key update value = If(source = 'CRS', values(value), value), final_value = If(source = 'CRS' AND override = 0, values(final_value), final_value)", array(
                    $a["version_number"],
                    $a["text_value"],
                    $a["feature_name"],
                    $a["label"],
                    $a["type"],
                    $a["package_id"],
                    $motorcycle_id,
                    $a["text_value"],
                    "CRS",
                    $a["attribute_id"]
                ));
            }
        }

        // Now, we need to get the photos...
        $query = $this->db->query("Select motorcycle.id as motorcycle_id, crs_trim_id, IfNull(max(motorcycleimage.version_number), 0) as version_number, IfNull(max(motorcycleimage.priority_number), 0) as ordinal from motorcycle left join motorcycleimage on motorcycle.id = motorcycleimage.motorcycle_id where crs_trim_id > 0 group by motorcycle.id");

        $matching_motorcycles = $query->result_array();

        foreach ($matching_motorcycles as $m) {
            $motorcycle_id = $m["motorcycle_id"];
            $trim_id = $m["crs_trim_id"];
            $version_number = $m["version_number"];
            $ordinal = $m["ordinal"];

            // get the photos...
            $photos = $this->CRS_m->getTrimPhotos($trim_id, $version_number);

            foreach ($photos as $p) {
                $ordinal++;
                // this needs to be inserted...
                $this->db->query("Insert into motorcycleimage (motorcycle_id, image_name, date_added, description, priority_number, external, version_number, source) values (?, ?, now(), ?, ?, 1, ?, 'CRS')", array(
                    $motorcycle_id,
                    $p["photo_url"],
                    $p["photo_label"],
                    $ordinal,
                    $p["version_number"]
                ));
            }
        }
    }
}

/* End of file croncontrol.php */
/* Location: ./application/controllers/croncontrol.php */
