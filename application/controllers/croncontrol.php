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


	public function refreshCRSData() {
        // OK, this is straightforward, we have to get the motorcycles that have trim IDs, and we have to update the specifications...
        $query = $this->db->query("Select motorcycle.id as motorcycle_id, crs_trim_id, IfNull(max(motorcyclespec.version_number), 0) as version_number from motorcycle left join motorcyclespec on motorcycle.id = motorcyclespec.motorcycle_id where crs_trim_id > 0 group by motorcycle.id");

        // we're going to refresh the data for this...
        $this->load->model("CRS_m");

        $matching_motorcycles = $query->result_array();

        print_r($matching_motorcycles);

        foreach ($matching_motorcycles as $m) {
            $motorcycle_id = $m["motorcycle_id"];
            $trim_id = $m["crs_trim_id"];
            $version_number = $m["version_number"];

            print "M: ";
            print_r($m);
            print "Attributes:" ;

            // get the attributes...
            $attributes = $this->CRS_m->getTrimAttributes($trim_id, $version_number);

            print_r($attributes);

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
