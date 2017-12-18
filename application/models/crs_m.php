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

    public function getTrims($args = array()) {
        return $this->postRequest("getTrims", $args, "records");
    }

    public function getTrim($trim_id) {
        return $this->postRequest("getTrim", array("trim_id" => $trim_id), "trims");
    }

    // get the extra details...
    public function getTrimAttributes($trim_id, $version_number = 0) {
        return $this->postRequest("getTrimAttributes", array("trim_id" => $trim_id, "version_number" => $version_number), "specifications");
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