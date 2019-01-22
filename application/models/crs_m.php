<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 11/21/17
 * Time: 3:45 PM
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CRS_M extends Master_M
{

    public function scrubTrim($motorcycle_id) {
        // What happens if this trim must go away?
        // We have to clear the specs, we have to clear the CRS fields, and we have to remove the pictures.
        global $PSTAPI;
        initializePSTAPI();

        // Clean up the specs
        $specgroups = $PSTAPI->motorcyclespecgroup()->fetch(array("motorcycle_id" => $motorcycle_id));

        foreach ($specgroups as $sg) {
            $PSTAPI->motorcyclespec()->removeWhere(array(
                "source" => "PST",
                "motorcyclespecgroup_id" => $sg->id()
            ));

            if ($sg->get("source") == "PST") {
                $specs = $PSTAPI->motorcyclespec()->fetch(array("motorcyclespecgroup_id" => $sg->id()), true);
                if (count($specs) == 0) {
                    $sg->remove();
                }
            }
        }

        // Clean up the photos...
        $PSTAPI->motorcycleimage()->removeWhere(array(
            "source" => "PST",
            "motorcycle_id" => $motorcycle_id
        ));

        // clean it up...
        $PSTAPI->motorcycle()->update($motorcycle_id, array(
            "crs_trim_id" => null, "crs_machinetype" => null, "crs_model_id" => null, "crs_make_id" => null, "crs_year" => null, "crs_version_number" => 0
        ));
    }

    public function getMachineTypeMotoType($machine_type, $offroad_flag)
    {
        // JLB 1-22-19: Insert this thing.
        return $this->_getMachineTypeMotoType($machine_type, $offroad_flag, true);
    }



    protected $_preserveMachineMotoType;
    public function _getMachineTypeMotoType($machine_type, $offroad_flag, $insert_on_missing = false) {
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
            if ($insert_on_missing) {
                // I guess we are inserting this thing!
                global $PSTAPI;
                initializePSTAPI();

                $matches = $PSTAPI->motorcycletype()->fetch(array(
                    "name" => $machine_type,
                    "crs_type" => null
                ));

                if (count($matches) > 0) {
                    // let's just take the first one, update it, and call it good!
                    $match = $matches[0];
                    $match->set("crs_type", $machine_type);
                    $match->set("offroad", $offroad_flag);
                    $match->save();
                    $type_id = $match->id();
                } else {
                    $obj = $PSTAPI->motorcycletype()->add(array(
                        "name" => $machine_type,
                        "crs_type" => $machine_type,
                        "offroad_flag" => $offroad_flag
                    ));
                    $type_id = $obj->id();
                }
            } else {
                throw new \Exception("Could not find a match for _getMachineTypeMotoType($machine_type, $offroad_flag)");
            }
        }

        $this->_preserveMachineMotoType[$key] = $type_id;
        return $type_id;
    }


    public function getStockMotoCategory($name = "Dealer") {
        return $this->_getStockMotoCategory($name);
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

    public function matchIfYouCan($motorcycle_id, $vin, $make, $model, $year, $codename, $msrp, $scrub_trim = false) {
        $CI =& get_instance();
        $CI->load->model("CRSCron_M");

        // Now, what is the ID for this motorcycle?
        $vin_match = $this->findBestFit($vin, $make, $model, $year, $codename, $msrp);

        if (is_array($vin_match) && count($vin_match) > 0) {
            $vin_match = $vin_match[0];
        }

        if ($scrub_trim) {
            $this->scrubTrim($motorcycle_id);
        }

        if (array_key_exists("trim_id", $vin_match)) {

            // we should definitely mark this
            $this->db->query("Update motorcycle set crs_trim_id = ? where id = ? limit 1", array($vin_match["trim_id"], $motorcycle_id));

            // what about the fields? transmission, engine_type,
            $this->db->query("Update motorcycle set transmission = ? where id = ? and (transmission = '' or transmission is null)", array($vin_match["transmission"], $motorcycle_id));
            $this->db->query("Update motorcycle set engine_type = ? where id = ? and (engine_type = '' or engine_type is null)", array($vin_match["engine_type"], $motorcycle_id));

            // We insert the thumbnail, too?
            if (array_key_exists("trim_photo", $vin_match) && $vin_match["trim_photo"] != "") {
                $this->db->query("Insert into motorcycleimage (motorcycle_id, image_name, date_added, description, priority_number, external, version_number, source, crs_thumbnail) values (?, ?, now(), ?, 1, 1, ?, 'PST', 1) on duplicate key update source = 'PST'", array($motorcycle_id, $vin_match["trim_photo"], 'Trim Photo: ' . $vin_match['display_name'], $vin_match["version_number"]));
            }

            $this->fixCatsByTrim($motorcycle_id, $vin_match);
        }

    }

    public function fixCatsByTrim($motorcycle_id, $vin_match = null) {
        $CI =& get_instance();

        global $PSTAPI;
        initializePSTAPI();
        $motorcycle = $PSTAPI->motorcycle()->get($motorcycle_id);

        if (is_null($motorcycle)) {
            return;
        }

        if (is_null($vin_match)) {
            // Is there a trim?
            if ($motorcycle->get("crs_trim_id") > 0) {
                // We have to get that trim...
                $trim = $this->CRS_m->getTrim($motorcycle->get("crs_trim_id"));
                // OK, we need to insert it...
                if (count($trim) > 0) {
                    $vin_match = $trim[0];
                } else {
                    return;
                }
            } else {
                return;
            }
        }


        // refresh it...
        $CI->load->model("CRSCron_M");
        $CI->CRSCron_M->refreshCRSData($motorcycle_id);

        if ($motorcycle->get("customer_set_vehicle_type") == 0) {
            // Now, we attempt to fix the machine type...
            $corrected_category = $this->_getMachineTypeMotoType($vin_match["machine_type"], $vin_match["offroad"]);
            if ($corrected_category > 0) {
                $this->db->query("Update motorcycle set vehicle_type = ? where id = ? limit 1", array($corrected_category, $motorcycle_id));
                error_log("Setting motorcycle $motorcycle_id to vehicle_type $corrected_category ");
            }
        }

        // OK, we need to fix the category and we need to fix the type, if we've got it.
        if ($motorcycle->get("customer_set_category") == 0) {
            $corrected_category = 0;
            $query2 = $this->db->query("Select value from motorcyclespec where motorcycle_id = ? and crs_attribute_id = 10011", array($motorcycle_id));
            foreach ($query2->result_array() as $disRec) {
                $corrected_category = $this->_getStockMotoCategory($disRec["value"]);
            }
            if ($corrected_category > 0) {
                $this->db->query("Update motorcycle set category = ? where id = ? limit 1", array($corrected_category, $motorcycle_id));
                error_log("Setting motorcycle $motorcycle_id to category $corrected_category ");
            }
        }
    }



    /*
     * This batch of functionality is on the web service.  It's an extremely lightweight interface.
     */

    const BASE_CRS_URL = "https://crs1.internal.powersporttechnologies.com/api/index.php/";
    const BASE_CRS_HOST = "crs1.internal.powersporttechnologies.com";

    // get the machine type. Super simple.
    public function getMachineType()
    {
        return $this->postRequest("getMachineType", "records");
    }

    public function getMakes($args = array()) {
        return $this->postRequest("getMake", $args, "records");
    }

    public function getModels($args = array()) {
        return $this->postRequest("getModel", $args, "records");
    }

    public function getMakesByMachineType($machine_type) {
        return $this->getMakes(array("machine_type" => $machine_type));
    }

    // get the trims...we'll just pass trim
    public function getTrimsByFitment($machine_type = "", $make_id = "", $year = "") {
        $args = array();
        if ($machine_type != "") {
            $args["machine_type"] = $machine_type;
        }
        if ($make_id != "") {
            $args["make_id"] = $make_id;
        }
        if ($year != "") {
            $args["year"] = $year;
        }
        return $this->getTrims($args);
    }

    protected function _applyMutationToRecords($records) {
        if (function_exists("CRSMutateFunction")) {
            // There is a mutator, hence, you must mutate.
            for ($i = 0; $i < count($records); $i++) {
                $m = $records[$i];
                $crs_make = $m["make"];
                $crs_display_name = $m["display_name"];

                $rec = CRSMutateFunction($crs_make, $crs_display_name);
                $m["make"] = $rec["make"];
                $m["display_name"] = $rec["display_name"];
                $records[$i] = $m;
            }
        }
        return $records;
    }

    public function getTrims($args = array()) {
        return $this->_applyMutationToRecords($this->postRequest("getTrims", $args, "records"));
    }

    public function getTrim($trim_id) {
        return $this->_applyMutationToRecords($this->postRequest("getTrim", array("trim_id" => $trim_id), "trims"));
    }

    // get the extra details...
    public function getTrimAttributes($trim_id, $version_number = 0) {
        $records = $this->postRequest("getTrimAttributes", array("trim_id" => $trim_id, "version_number" => $version_number), "specifications");

        // JLB 11-15-18
        // There is no need to push down all these "NOT AVAILABLE"
        $clean_records = $records;

        foreach ($records as $rec) {
            if (strtolower(trim($rec["text_value"])) != "not available") {
                $clean_records[] = $rec;
            }
        }

        return $clean_records;
    }

    public function getTrimPhotos($trim_id, $version_number = 0) {
        return $this->postRequest("getTrimPhotos", array("trim_id" => $trim_id, "version_number" => $version_number), "photos");
    }

    // query the VIN...
    public function queryVin($vin_pattern, $fuzzy = false) {
        try {
            return $this->postRequest($fuzzy ? "fuzzyDecodeVin" : "decodeVin", array("vin" => $vin_pattern));
        } catch (Exception $e) {
            return array();
        }
    }

    public function bestTryDecodeVin($VIN, $Make, $ModelYear) {
        try {
            return $this->postRequest("bestTryDecodeVin", array("vin" => $VIN, "make" => $Make, "year" => $ModelYear));
        } catch (Exception $e) {
            return array();
        }
    }

    public function findBestFit($vin, $make, $model, $year, $codeword, $msrp = 0) {
        try {
            return $this->postRequest("findBestFit", array("vin" => $vin, "make" => $make, "year" => $year, "model" => $model, "codeword" => $codeword, "store" => STORE_NAME, "msrp" => $msrp), "matches");
        } catch (Exception $e) {
            return array();
        }
    }

    public function getColorCodes() {
        try {
            $result = $this->postRequest("colorcodes");
            if (!empty($result["matches"])) return $result["matches"];
            return array();
        } catch (Exception $e) {
            return array();
        }
    }

    protected function postRequest($function, $arguments = array(), $key = "")
    {
        //get the CRS webform data
        $ch = curl_init(self::BASE_CRS_URL . $function);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arguments);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);

        //Process CRS into arrays.
        $crsFullData = json_decode($output, true);
        if (is_array($crsFullData) && array_key_exists("success", $crsFullData)) {
            if ($crsFullData["success"]) {
                $data = $crsFullData["data"];
                if ($key != "") {
                    if (array_key_exists($key, $data)) {
                        return $data[$key];
                    } else {
                        throw new \Exception("Expected key $key for $function not found");
                    }
                } else {
                    return $data;
                }
            } else {
                // An error...
                throw new \Exception("Error in call to ${function} - " . $crsFullData["error_code"] . " - " . $crsFullData["error_string"]);
            }
        } else {
            throw new \Exception("Unrecognized return structure from ${function}: " . print_r($crsFullData, true));
        }
    }
}