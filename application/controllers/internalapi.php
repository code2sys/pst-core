<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 10/1/18
 * Time: 5:44 PM
 */


class Internalapi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        global $Permitted_InternalAPI_IPs;
        if (!isset($Permitted_InternalAPI_IPs)) {
            $Permitted_InternalAPI_IPs = array(
                "10.132.45.182"
            );
        }
        if (!in_array($_SERVER["REMOTE_ADDR"], $Permitted_InternalAPI_IPs)) {
            print "IP not permitted.\n";
            exit();
        }
    }

    public function dealertrack() {


        $file = $_FILES["upload"];

        if ($file["size"] > 0) {
            ini_set('max_execution_time', 300); //300 seconds = 5 minutes

            global $PSTAPI;
            initializePSTAPI();

            $this->load->model("CRS_m");
            $this->load->model("Lightspeed_M");

            // OK, start a new log entry
            $log_id = $PSTAPI->dealerTrackFeedLog()->begin($file["name"]);


            $default_type = $PSTAPI->config()->getKeyValue("dealer_track_default_vehicle_type", 0);
            if ($default_type == 0) {
                $default_type = $this->Lightspeed_M->fetchMotorcycleType("motorcycle");
            }

            $default_category = $PSTAPI->config()->getKeyValue("dealer_track_default_category", 0);
            if ($default_category == 0) {
                $default_category = $this->Lightspeed_M->fetchMotorcycleCategory("Motorcycle");
            }


            // Get the header
            $handle = fopen($file["tmp_name"], "r");
            $header = fgetcsv($handle);

            while (FALSE !== ($row = fgetcsv($handle))) {
                $data = array();
                for ($i = 0; $i < count($header); $i++) {
                    $data[trim($header[$i])] = $row[$i];
                }

                $motorcycle_id = $PSTAPI->dealerTrackFeedLog()->processRow($data, $default_type, $default_category);

                // Now, attempt to CSR this puppy.
                $motorcycle = $PSTAPI->motorcycle()->get($motorcycle_id);

                // Now, what is the ID for this motorcycle?
                if ($motorcycle->get("crs_trim_id") == 0) {
                    $motorcycle_array = $motorcycle->to_array();
                    $this->CRS_m->matchIfYouCan($motorcycle_id, $motorcycle_array["vin_number"], $motorcycle_array["make"], $motorcycle_array["model"], $motorcycle_array["year"], $motorcycle_array["codename"], $motorcycle_array["retail_price"], false);
                } else {
                    // try to fix those categories, anyway.
                    $this->CRS_m->fixCatsByTrim($motorcycle_id);
                }


                // denormalize that sucker.
                $PSTAPI->denormalizedmotorcycle()->moveMotorcycle($motorcycle_id);
            }

            // Delete the marked motorcycles that have not been refreshed...
            $PSTAPI->dealerTrackFeedLog()->end($log_id);
            $this->load->model("CRSCron_M");
            $this->CRSCron_M->removeExtraCRSBikes();

        } else {
            print "No file received.\n";
        }
    }

}
