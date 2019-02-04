<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/15/17
 * Time: 2:35 PM
 *
 * Originally developed by David B Mathewes some time in August-October 2017
 *
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* NOTE!!!  Need to make sure to turn on if checkbox is there and off it is not */

class Lightspeed_M extends Master_M {

    public function partNumberIsLightspeed($partnumber_id) {
        if (!$this->lightSpeedPartPricingRule()) {
            return false;
        }

        $query = $this->db->query("Select count(*) as cnt from partdealervariation join lightspeedpart using (partvariation_id) where partdealervariation.partnumber_id = ?", array($partnumber_id));
        $cnt = 0;
        foreach ($query->result_array() as $row) {
            $cnt = $row["cnt"];
        }
        return $cnt > 0;
    }

    public function lightspeedPrice($partnumber_id) {
        // OK, we need to get the price of this guy
        $query = $this->db->query("Select current_active_price from partdealervariation join lightspeedpart using (partvariation_id) where partdealervariation.partnumber_id = ?", array($partnumber_id));
        $cnt = 0;
        foreach ($query->result_array() as $row) {
            $cnt = $row["current_active_price"];
        }
        return $cnt;
    }

    public function partPriceFix() {
        if ($this->lightSpeedPartPricingRule()) {
            // fix the price if there is a current_active_price
            $this->db->query("update partnumber join partdealervariation using (partnumber_id) join lightspeedpart using (partvariation_id) set partnumber.price = lightspeedpart.current_active_price, partdealervariation.price = lightspeedpart.current_active_price, partnumber.dealer_sale = lightspeedpart.current_active_price where lightspeedpart.current_active_price > 0 and partdealervariation.quantity_available > 0;");

            // fix the cost if there is a cost
            $this->db->query("update partnumber join partdealervariation using (partnumber_id) join lightspeedpart using (partvariation_id) set partnumber.cost = lightspeedpart.cost, partdealervariation.cost = lightspeedpart.cost where lightspeedpart.cost > 0 and partdealervariation.quantity_available > 0;");
        }
    }

    public $headers = array();
    public $cred = array();
    public $serverUrl = 'https://int.LightspeedADP.com/lsapi/';
    private $call;
    private $all = array();
    private $store_url = '';
    private $boundary;

    public function fetchMotorcycleType($category_name) {
        // JLB: Invariably, we are going to have to do something about these..
        $lookup_table = array(
            "motorcycle" => "Street Bike",
            "atv" => "ATV",
            "utv" => "UTV",
            "street bike" => "Street Bike",
            "dirt" => "Off-Road",
            "power equipment" => "Generators",
            "scooter" => "Scooter",
            "ruv" => "RUV",
            "generators" => "Generators",
            "lawn and garden" => "Lawn and Garden",
            "dirt bike" => "Off-Road",
            "trailer" => "Trailer",
            "snowmobile" => "Snowmobile",
            "water craft" => "Water Craft",
            "watercraft" => "Water Craft"
        );

        if (array_key_exists(strtolower($category_name), $lookup_table)) {
            $category_name = $lookup_table[strtolower($category_name)];
        } else {
            print "NEW UNIT TYPE: $category_name \n";
        }

        $query = $this->db->query("Select * from motorcycle_type where name = ?", array($category_name));
        $category_id = 0;
        foreach ($query->result_array() as $row) {
            $category_id = $row["id"];
        }

        if ($category_id == 0) {
            return $this->fetchMotorcycleType("Street Bike"); // just go with it...
            // you have to insert it...
            $this->db->query("Insert into motorcycle_type (name) values (?)", array($category_name));
            $category_id = $this->db->insert_id();
        }

        return $category_id;
    }

    public function fetchMotorcycleCategory($category_name) {
        $query = $this->db->query("Select * from motorcycle_category where name = ?", array($category_name));
        $category_id = 0;
        foreach ($query->result_array() as $row) {
            $category_id = $row["id"];
        }

        if ($category_id == 0) {
            // you have to insert it...
            $this->db->query("Insert into motorcycle_category (name) values (?)", array(ucwords(strtolower($category_name))));
            $category_id = $this->db->insert_id();
        }

        return $category_id;
    }

    public function cleanColors($color) {
        $this->load->model("color_m");
        $color = trim($color);
        $color_found = $this->color_m->getColorByCode($color);
        if ($color_found == FALSE) {
            $color_found = $this->color_m->getColorByLabel($color, true);
        }
        return $color_found;
    }

    protected function _getMatchingBikes($stock_number, $dealer_cmf, &$final_sku) {
        global $PSTAPI;
        initializePSTAPI();

        $final_sku = $stock_number;
        $results = $PSTAPI->motorcycle()->fetch(array("sku" => $stock_number), true);
        if (count($results) > 0) {
            if (!is_null($results[0]["lightspeed_dealerID"]) && $results[0]["lightspeed_dealerID"] != "" && $results[0]["lightspeed_dealerID"] != $dealer_cmf) {
                $final_sku = $stock_number . "-" . $dealer_cmf;
                $results = $PSTAPI->motorcycle()->fetch(array("sku" => $final_sku, "lightspeed_dealerID" => $dealer_cmf), true);
            }
        }

        return $results;
    }

    public function updateLightspeedSettings($data) {
        global $PSTAPI;
        initializePSTAPI();

        // It is only going to be null or not present if it is not actually on the form. Otherwise, it will be "".
        if (array_key_exists("lightspeed_integration_type", $data) && !is_null($data["lightspeed_integration_type"])) {
            $integration_type_new = $data['lightspeed_integration_type'];
            $integration_type_old = $PSTAPI->config()->getKeyValue('lightspeed_integration_type', 'dealer_direct');
            $PSTAPI->config()->setKeyValue('lightspeed_integration_type', $integration_type_new);
        } else {
            $integration_type_new = $PSTAPI->config()->getKeyValue('lightspeed_integration_type', '');
        }

        // It is only going to be null or not present if it is not actually on the form. Otherwise, it will be "".
        $dealers_cmf_old = $PSTAPI->config()->getKeyValue('lightspeed_dealers_cmf', '');
        if (array_key_exists("lightspeed_dealers_cmf", $data) && !is_null($data["lightspeed_dealers_cmf"])) {
            $dealers_cmf_new = $data['lightspeed_dealers_cmf'];
            $PSTAPI->config()->setKeyValue('lightspeed_dealers_cmf', $dealers_cmf_new);
        } else {
            $dealers_cmf_new = $dealers_cmf_old;
        }


        if ($integration_type_new == 'pst' &&
            ($integration_type_old != $integration_type_new || $dealers_cmf_old != $dealers_cmf_new)) {
            $dealers_array = explode(",", $dealers_cmf_new);
            $this->addCMFsToPST($dealers_array);
        }
    }

    public function getLightspeedSettings() {
        global $PSTAPI;
        initializePSTAPI();

        $integration_type = $PSTAPI->config()->getKeyValue('lightspeed_integration_type', 'dealer_direct');
        $dealers_cmf = $PSTAPI->config()->getKeyValue('lightspeed_dealers_cmf', '');

        return array(
            "lightspeed_integration_type" => $integration_type,
            "lightspeed_dealers_cmf" => $dealers_cmf
        );
    }

    protected function addCMFsToPST($dealer_cmfs) {
        global $PSTAPI;
        initializePSTAPI();

        $integration_type = $PSTAPI->config()->getKeyValue('lightspeed_integration_type', 'dealer_direct');
        if ($integration_type != 'pst') {
            throw new \Exception("Unexpected Lightspeed integration type : ". $integration_type);
            return;
        }

        $cmfs_to_remove = array();
        $cmfs_to_add = $dealer_cmfs;
        $call = $this->call("Dealer");
        $dealers = json_decode($call);
        if(!empty($dealers)) {
            foreach($dealers as $dealer) {
                $found = array_search($dealer->Cmf, $cmfs_to_add);
                if ($found !== FALSE) {
                    array_splice($cmfs_to_add, $found, 1);
                } else {
                    $cmfs_to_remove[] = $dealer->Cmf;
                }
            }
        }
        

        foreach($cmfs_to_add as $cmf) {
            $endpoint = "License?LicenseKey=".LIGHTSPEED_PST_LICENSE_KEY."&Cmf=".$cmf;
            $call = $this->call($endpoint, "POST");
        }

        foreach($cmfs_to_remove as $cmf) {
            $endpoint = "License?LicenseKey=".LIGHTSPEED_PST_LICENSE_KEY."&Cmf=".$cmf;
            $call = $this->call($endpoint, "DELETE");
        }
    }

    /*
    * return CMF #s according to Lightspeed Integration Type setting
    */

    protected function getDealerCMFs() {

        global $PSTAPI;
        initializePSTAPI();

        $string = "Dealer";
        $call = $this->call($string);
        $dealers = json_decode($call);

        if($dealers == NULL) {
            throw new \Exception("An error occurred and no data was received from Lightspeed. Possible cause: incorrect Lightspeed username or password.");
        }

        $cmfs = array();
        foreach($dealers as $dealer) {
            $cmfs[] = $dealer->Cmf;
        }
        return $cmfs;
    }

    public function preserveMajorUnitsChangedField($field) {
        $CI =& get_instance();
        $CI->load->model("CRS_m");
        $CI->load->model("CRSCron_M");

        global $PSTAPI;
        initializePSTAPI();

        $dealer_cmfs = $this->getDealerCMFs();

        foreach($dealer_cmfs as $dealer_cmf) {
            $string = "Unit/" . $dealer_cmf;
            $call = $this->call($string);
            $bikes = json_decode($call);

            foreach ($bikes as $bike) {
                $sku = $bike->StockNumber;
                $results = $this->_getMatchingBikes($bike->StockNumber, $dealer_cmf, $sku);
                if (count($results) > 0) {
                    $motorcycle_array = $this->_subUnpackMajorUnit($bike, $dealer_cmf);
                    // OK, we have to see if this field is the same or not...
                    if ($motorcycle_array[$field] != $results[0]->get($field)) {
                        $results[0]->set("customer_set_" . $field, 1);
                        $results[0]->save();
                        print "Setting field on " . $results[0]->get("sku") . "\n";
                    }
                }
            }
        }
    }

    protected function _subUnpackMajorUnit(&$bike, $cmf) {
        global $lightspeedDealerMap;

        if (!isset($lightspeedDealerMap) || !is_array($lightspeedDealerMap)) {
            $lightspeedDealerMap = array(); 
        }

        global $LIGHTSPEED_CONSIGNMENT_USED;
        if (isset($LIGHTSPEED_CONSIGNMENT_USED) && $LIGHTSPEED_CONSIGNMENT_USED && $bike->NewUsed == "C") {
            $bike->Newused = 2; // This was requested for IHM, but I bet this applies to every single Lightspeed. Still, let's not break anyone else out there, but I suspect we'll make this different in the future.
        } else {
            $bike->NewUsed = ($bike->NewUsed=="U")?2:1;
        }



        $bike->WebTitle = ($bike->WebTitle!="") ? $bike->WebTitle : $bike->ModelYear ." " . $bike->Make . " " . ($bike->CodeName != "" ? $bike->CodeName : $bike->Model);

        $data = array('total_cost' => $bike->totalCost, 'unit_cost' => $bike->totalCost, 'parts' => "", 'service' => "", 'auction_fee' => "", 'misc' => "");
        $bike->data = json_encode($data);

        // JLB 12-31-18
        if ($bike->DSRP > 0 && $bike->MSRP <= 0) {
            $bike->MSRP = $bike->DSRP;
        }

        $bike->WebPrice = ($bike->WebPrice <= 0) ? $bike->MSRP : $bike->WebPrice;
        

        // I expect these will be integers, numeric
        $location_description = (intVal($cmf) != 0 && array_key_exists(intVal($cmf), $lightspeedDealerMap)) ? $lightspeedDealerMap[intVal($cmf)] : "";

        $make = normalize_incoming_make($bike->Make);

        return array(
            "location_description" => $location_description,
            'lightspeed_dealerID' => $cmf,
            'sku' => $bike->StockNumber,
            'real_sku' => $bike->StockNumber,
            'vin_number' => $bike->VIN,
            'lightspeed_location' => $bike->Location,
            'mileage' => $bike->Odometer,
            'data' => $bike->data,
            'color' => $bike->Color,
            'sale_price' => $bike->WebPrice,
            'retail_price' => $bike->MSRP,
            'description' => $bike->WebDescription,
            'lightspeed_set_description' => trim($bike->WebDescription) != '',
            'call_on_price' => $bike->WebPriceHidden,
            // ($bike->DSRP > $bike->MSRP || $bike->FreightCost > 0) ? 1 : 0,
            "destination_charge" => ($c = $this->destinationOnAdd()) == 2 ? (($bike->DSRP > $bike->MSRP || $bike->FreightCost > 0) ? 1 : 0) : ($c > 0 ? 1 : 0),
            "lightspeed" => 1,
            "lightspeed_flag" => 1,
            "source" => "Lightspeed",
            "codename" => $bike->CodeName,


            'condition' => $bike->NewUsed,
            "vehicle_type" => $this->fetchMotorcycleType($bike->UnitType),
            'category' => $this->fetchMotorcycleCategory($bike->UnitType), // TODO
            'year' => $bike->ModelYear,
            'make' => $make,
            'model' => $bike->Model,
            'title' => $bike->WebTitle,
            "status" => $this->activeOnAdd() ? 1 : 0
        );
    }

    public function get_major_units($debug = false) {
        $CI =& get_instance();
        $CI->load->model("CRS_m");
        $CI->load->model("CRSCron_M");

        global $PSTAPI;
        initializePSTAPI();

        $lightspeed_new_unit_dealership_list = trim($PSTAPI->config()->getKeyValue('lightspeed_new_unit_dealership_list', ''));
        $lightspeed_new_unit_dealership_filter = preg_split("/\s*[,;]\s*/", $lightspeed_new_unit_dealership_list);
        $lightspeed_used_unit_dealership_list = trim($PSTAPI->config()->getKeyValue('lightspeed_used_unit_dealership_list', ''));
        $lightspeed_used_unit_dealership_filter = preg_split("/\s*[,;]\s*/", $lightspeed_used_unit_dealership_list);

        
        $this->db->query("Update motorcycle set lightspeed_flag = 0");

        $valid_count = 0;
        $crs_trim_matches = 0;

        $ts = date("Y-m-d H:i:s");
        $dealer_cmfs = $this->getDealerCMFs();

        foreach($dealer_cmfs as $dealer_cmf) {

            $string = "Unit/".$dealer_cmf;
            $call = $this->call($string);
            $bikes = json_decode($call);

            foreach($bikes as $bike) {


                $scrub_trim = false;
                $last_known_trim = 0;
                $motorcycle_array = $this->_subUnpackMajorUnit($bike, $dealer_cmf);
                $motorcycle_array["lightspeed_timestamp"] = $ts;


                // JLB 12-04-18
                // Filter by the dealer ID, if there is one.
                // I know this could be a little more compact, but I think it hurt readability.
                if ($motorcycle_array["condition"] == 1) {
                    if ($lightspeed_new_unit_dealership_list != "" && count($lightspeed_new_unit_dealership_filter) > 0) {
                        if (!in_array($dealer_cmf, $lightspeed_new_unit_dealership_filter)) {
                            if ($debug) print "FILTER skipping new " . $dealer_cmf . "\n";
                            continue; // skip it.
                        } else {
                            if ($debug) print "FILTER Dealer CMF in new filter: " . $dealer_cmf . "\n";
                        }
                    } else {
                        if ($debug) print "FILTER No new filter defined \n";
                    }
                } else {
                    if ($lightspeed_used_unit_dealership_list != "" && count($lightspeed_used_unit_dealership_filter) > 0) {
                        if (!in_array($dealer_cmf, $lightspeed_used_unit_dealership_filter)) {
                            if ($debug) print "FILTER skipping used " . $dealer_cmf . "\n";
                            continue; // skip it.
                        } else {
                            if ($debug) print "FILTER Dealer CMF in used filter: " . $dealer_cmf . "\n";
                        }
                    } else {
                        if ($debug) print "FILTER No pre-owned filter defined \n";
                    }
                }

                if (isset($bike->OnHold) && trim($bike->OnHold) != "") {
                    continue; // It's on hold for a deal. Not going to put that in tonight!
                }

                if (isset($bike->UnitStatus) && trim($bike->UnitStatus) == "R") {
                    continue; // It has been removed.
                }

                // JLB 12-04-18
                // We need to now apply the dealership ID once we know if this is a NEW or USED unit.

                // JLB 09-17-18
                // Added because of Al Lambs Dallas Honda
                // If the SKU is "TBA" or the SKU is "" or the SKU is null... do not add this bike.
                if (!array_key_exists("sku", $motorcycle_array) || $motorcycle_array["sku"] == "" || $motorcycle_array["sku"] == "TBA") {
                    continue; // skip this bike.
                }


                $update_array = array(
                    'lightspeed_dealerID' => $dealer_cmf,
                    'sku' => $motorcycle_array["sku"],
                    'real_sku' => $motorcycle_array["real_sku"],
                    'lightspeed_location' => $motorcycle_array["lightspeed_location"],
                    'lightspeed_timestamp' => $motorcycle_array["lightspeed_timestamp"],
                    'data' => $motorcycle_array["data"],
                    'sale_price' => $motorcycle_array["sale_price"],
                    'retail_price' => $motorcycle_array["retail_price"],
                    "lightspeed" => 1,
                    "lightspeed_flag" => 1,
                    "source" => "Lightspeed",
                    'call_on_price' => $motorcycle_array['call_on_price']
                );

                if ($motorcycle_array["location_description"] != "") {
                    $update_array["location_description"] = $motorcycle_array["location_description"];
                }


                global $PSTAPI;
                initializePSTAPI();
                $sku = $bike->StockNumber;
                $results = $this->_getMatchingBikes($sku, $update_array['lightspeed_dealerID'], $sku);
                // This might change in here. We therefore need to use the changed one.
                $update_array["sku"] = $sku;
                $motorcycle_array["sku"] = $sku;

                if(count($results) > 0) {
                    $last_known_trim = $results[0]["crs_trim_id"];
                    if ($results[0]["customer_set_price"] > 0) {
                        // OK, the customer set the price...so we can't do this...unless it matches exctly
                        if ($bike->MSRP == $results[0]["retail_price"] && $bike->WebPrice == $results[0]["sale_price"]) {
                            $update_array["customer_set_price"] = 0;
                        } else {
                            $update_array["retail_price"] = $results[0]["retail_price"];
                            $update_array["sale_price"] = $results[0]["sale_price"];
                        }
                    }

                    foreach (array("description", "vin_number", "mileage", "color", "call_on_price", "destination_charge", "condition", "category", "make", "model", "title", "year", "vehicle_type") as $k) {
                        $set_k = "customer_set_" . $k;
                        if ($results[0][$set_k] > 0) {
                            $comp_val = $results[0][$k];

                            if ($k == "category") {
                                $comp_val = $PSTAPI->motorcyclecategory()->get($comp_val);
                                $comp_val = is_null($comp_val) ? "" : $comp_val->get("name");
                            } else if ($k == "vehicle_type") {
                                $comp_val = $PSTAPI->motorcycletype()->get($comp_val);
                                $comp_val = is_null($comp_val) ? "" : $comp_val->get("name");
                            }

                            // JLB 11-02-18
                            // You can't get the description back because, what if they want it clear, and then CRS charges in?
                            if ($motorcycle_array[$k] == $comp_val && $k != "destination_charge" && $k != "description") {
                                $update_array[$set_k] = 0; // if it matches, well, we should clear this flag, since they have gotten around to matching it in Lightspeed.
                            }
                        } else  {
                            $update_array[$k] = $motorcycle_array[$k];
                        }
                    }

                    // JLB 11-02-18
                    // As part of adding in the description from lightspeed, we have to mark it.
                    if (array_key_exists("description", $update_array)) {
                        $update_array["lightspeed_set_description"] = trim($update_array["description"]) != "" ? 1 : 0; // we have to clear it out if it is zeroed.
                    }

                    // JLB 04-24-18
                    // New - if any of these essentials change, then we need to SCRUB THE TRIM. That means, we have to get rid of the stuff that was already there...
                    $scrub_trim = false;

                    foreach (array("vin_number", "make", "model", "year") as $k) {
                        if (array_key_exists($k, $update_array) && $results[0][$k] != $update_array[$k]) {
                            $scrub_trim = true;
                            print "Scrub trim on " . $sku . " for key $k change from " . $results[0][$k] . " to " .  $motorcycle_array[$k] . "\n";
                        }
                    }

                    if ($scrub_trim || $results[0]["codename"] == "") {
                        $update_array["codename"] = $motorcycle_array["codename"];
                    }

                    // JLB 08-16-18
                    // Location edits should be preserved until they match.
                    if ($results[0]["location_description"] != $update_array["location_description"]) {
                        if ( $results[0]["customer_set_location"] > 0 ) {
                            unset($update_array["location_description"]); // you have to clear it out.
                        }
                    } else {
                        $update_array["customer_set_location"] = 0;
                    }

                    $where = array('sku' => $sku);
                    $motorcycle = $this->updateRecord('motorcycle', $update_array, $where, FALSE);
                    if ($motorcycle === FALSE) {
                        print "Could not update: " . print_r($update_array, true) . "\n";
                    }
                    $valid_count++;


                } else {
                    // we have to set some nulls. I think this is stupid, too.
                    $motorcycle_array["engine_type"] = "";
                    $motorcycle_array["transmission"] = "";
                    $motorcycle_array["margin"] = $bike->WebPrice > 0 ?  round(($bike->WebPrice - $bike->totalCost) / $bike->WebPrice, 2) : 0;
                    $motorcycle_array["profit"] = $bike->WebPrice > 0 ? $bike->WebPrice - $bike->totalCost : 0;
                    $motorcycle_array["craigslist_feed_status"] = 0;
                    $motorcycle_array["cycletrader_feed_status"] = $this->unitCycleTraderDefault() ? 1 : 0;

                    $motorcycle = $this->createRecord('motorcycle', $motorcycle_array, FALSE);
                    if ($motorcycle === FALSE) {
                        print "Could not update: " . print_r($motorcycle_array, true) . "\n";
                    } else {
                        if ($debug) print "Updated: " . $motorcycle_array["sku"] . "\n";
                        global $PSTAPI;
                        initializePSTAPI();
                        $PSTAPI->denormalizedmotorcycle()->moveMotorcycle($motorcycle["id"]);
                    }

                    $valid_count++;
                }

                $motorcycle_id = 0;
                $crs_trim_id = 0;
                $query = $this->db->query("Select id, crs_trim_id from motorcycle where sku = ?", array($motorcycle_array["sku"]));
                foreach ($query->result_array() as $row) {
                    $motorcycle_id = $row["id"];
                    $crs_trim_id = $row["crs_trim_id"];
                }

                if ($motorcycle_id == 0) {
                    print "Could not find motorcycle for SKU: " . $motorcycle_array["sku"];
                    continue;
                }

                // Now, what is the ID for this motorcycle?
                if ($crs_trim_id == 0) {
                    $CI->CRS_m->matchIfYouCan($motorcycle_id, $motorcycle_array["vin_number"], $motorcycle_array["make"], $bike->Model, $bike->ModelYear, $bike->CodeName, $bike->MSRP, $scrub_trim);
                } else {
                    $CI->CRS_m->fixCatsByTrim($motorcycle_id);
                }

                // JLB 09-04-18
                // IF we can get the trim, and they have not set the title, consider the display name.
                // get it again

                // JLB 10-05-18
                // Brandt no longer wants model on the name.
                $motorcycle = $PSTAPI->motorcycle()->get($motorcycle_id);
                if ($motorcycle->get("crs_trim_id") > 0) {
                    fixCRSBike($motorcycle);
                    // if has match color, we remove the colorized photos
                    if (empty($motorcycle->get("color_code"))) {
                        $this->fixColorCode($motorcycle_id, $motorcycle->get("color"));
                    }
                }

                // Todo...
                // Does this motorcycle have a zero group or a general group of settings? We need to be able to flag the settings group that comes from Lightspeed in some way...
                // Finally, we need to optionally stick in the settings if they exist into this spec table...
                // At last, we should attempt to look up the trim of this by CRS and, if there is one, set the trim ID. We may also adjust the category and type if we get a match...
            }

        }

        if ($valid_count > 0) {
            $this->db->query("Update motorcycle set deleted = 1, lightspeed_deleted = 1 where lightspeed = 1 and lightspeed_flag = 0");
            $this->db->query("Update motorcycle set deleted = 0 where lightspeed_deleted = 1 and customer_deleted = 0 and lightspeed = 1 and lightspeed_flag = 1");
        }

        // JLB 12-29-17
        // At the end of this, we will remove any CRS items that overlap bikes from Lightspeed
        $CI->CRSCron_M->removeExtraCRSBikes();
    }

    public function fixColorCode($motorcycle_id, $colorLabel) {
        $CI =& get_instance();
        $CI->load->model("motorcycle_m");
        $CI->load->model("color_m");
        $motorcycle_colors_codes = $CI->motorcycle_m->getMotorcycleImageColorCodes($motorcycle_id, true);
        $color = $CI->color_m->getColorByLabel($colorLabel, true, $motorcycle_colors_codes);
        if ($color !== FALSE) {
            $this->db->query("Update motorcycle set color_code = ? where id = ?", array($color['code'], $motorcycle_id));
            $CI->motorcycle_m->removeColorizedImage($motorcycle_id, array($color['code']));
        }
    }

    public function scrubTrim($motorcycle_id) {
        $CI =& get_instance();
        $CI->load->model("CRS_m");
        $this->CRS_m->scrubTrim($motorcycle_id);
    }

    public function getStockMotoCategory($name = "Dealer") {
        $CI =& get_instance();
        $CI->load->model("CRS_m");
        return $this->CRS_m->getStockMotoCategory($name);
    }


    public function getMachineTypeMotoType($machine_type, $offroad_flag)
    {
        $CI =& get_instance();
        $CI->load->model("CRS_m");
        return $this->CRS_m->getMachineTypeMotoType($machine_type, $offroad_flag);
    }




    public function get_parts_xml() {

        $dealer_cmfs = $this->getDealerCMFs();

        foreach($dealer_cmfs as $dealer_cmf) {

            echo "<br>Dealer id: " . $dealer_cmf;
            $string = "Part/".$dealer_cmf;
            $call = $this->call($string);
            $call = json_decode($call);
            echo "<pre>";
            print_r($call);
        }
    }
    public function get_units_xml() {

        $dealer_cmfs = $this->getDealerCMFs();

        foreach($dealer_cmfs as $dealer_cmf) {

            echo "<br>Dealer id: " . $dealer_cmf;
            $string = "Unit/".$dealer_cmf;
            $call = $this->call($string);
            $call = json_decode($call);
            echo "<pre>";
            print_r($call);

        }

    }

    protected function extractStructureAsCSV($struct) {
        $output = fopen("php://output", "w");
        // We have to extract all the headers...
        $headers  = array();
        foreach ($struct as $s) {
            foreach (array_keys($s) as $k) {
                if (!in_array($k, $headers)) {
                    $headers[] = $k;
                }
            }
        }

        fputcsv($output, $headers);

        foreach ($struct as $s) {
            $row = array();
            foreach ($headers as $h) {
                if (array_key_exists($h, $s)) {
                     if (is_array($s[$h])) {
                         $row[] = implode(",", $s[$h]);
                     } else {
                         $row[] = $s[$h];
                     }
                } else {
                    $row[] = "";
                }
            }
            fputcsv($output, $row);
        }

    }

    public function get_units_csv() {

        $dealer_cmfs = $this->getDealerCMFs();

        foreach($dealer_cmfs as $dealer_cmf) {

            echo "Dealer id: " . $dealer_cmf . "\n";
            $string = "Unit/".$dealer_cmf;
            $call = $this->call($string);
            $call = json_decode($call, true);
            $this->extractStructureAsCSV($call);

        }
    }

    /*
     * The idea of this one is to simply shore up the import process enough to populate the lightspeed table.
     * There has to be a second routine that makes those things right. Thus, you can pull these in all you want -
     * you then have to do the right thing with them.
     */
    public function get_parts() {

        // flag them all to clear them
        $uniqid = uniqid("get_parts_");
        $this->db->query("Update lightspeedpart set uniqid = ?, lightspeed_present_flag = 0", array($uniqid));

        $dealer_cmfs = $this->getDealerCMFs();

        foreach($dealer_cmfs as $dealer_cmf) {
            $string = "Part/".$dealer_cmf;
            $call = $this->call($string);
            $parts = json_decode($call);
            foreach($parts as $part) {
                // David had used Description and UPC as well...
                if ($part->PartNumber == NULL) {
                    continue;
                }

                // We are simply going to do an insert/update on this table..
                if (!$this->db->query("Insert into lightspeedpart (part_number, supplier_code, description, on_hand, available, on_order, on_order_available, last_sold, last_received, reorder_method, min_qty, max_qty, cost, current_active_price, order_unit, order_unit_qty, last_count_date, superseded_to, upc, bin1, bin2, bin3, category, lightspeed_last_seen, uniqid, lightspeed_present_flag, retail) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , now(), ?, 1, ?) on duplicate key update on_hand = values(on_hand), available = values(available), on_order = values(on_order), on_order_available = values(on_order_available), last_sold = values(last_sold), last_received = values(last_received), reorder_method = values(reorder_method), min_qty = values(min_qty), max_qty = values(max_qty), cost = values(cost), current_active_price = values(current_active_price), order_unit = values(order_unit), description = values(description), order_unit_qty = values(order_unit_qty), last_count_date = values(last_count_date), superseded_to = values(superseded_to), upc = values(upc), bin1 = values(bin1), bin2 = values(bin2), bin3 = values(bin3), category = values(category), lightspeed_last_seen = values(lightspeed_last_seen), uniqid = values(uniqid), lightspeed_present_flag = values(lightspeed_present_flag), retail = values(retail)", array($part->PartNumber, $part->SupplierCode, $part->Description, $part->OnHand, $part->Avail, $part->OnOrder, $part->OnOrderAvail, date("Y-m-d H:i:s", strtotime($part->LastSoldDate)), date("Y-m-d H:i:s", strtotime($part->LastReceivedDate)), $part->ReOrderMethod, $part->MinimumQty, $part->MaximumQty, $part->Cost, $part->CurrentActivePrice, $part->OrderUnit, $part->OrderUnitQty, date("Y-m-d H:i:s", strtotime($part->LastCountDate)), $part->SupersededTo, $part->UPC, $part->Bin1, $part->Bin2, $part->Bin3, $part->category, $uniqid, $part->Retail))) {
                    print sprintf("Insert into lightspeedpart (part_number, supplier_code, description, on_hand, available, on_order, on_order_available, last_sold, last_received, reorder_method, min_qty, max_qty, cost, current_active_price, order_unit, order_unit_qty, last_count_date, superseded_to, upc, bin1, bin2, bin3, category, lightspeed_last_seen, uniqid, lightspeed_present_flag, retail) values ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' , now(), '%s', 1, '%s') on duplicate key update on_hand = values(on_hand), available = values(available), on_order = values(on_order), on_order_available = values(on_order_available), last_sold = values(last_sold), last_received = values(last_received), reorder_method = values(reorder_method), min_qty = values(min_qty), max_qty = values(max_qty), cost = values(cost), current_active_price = values(current_active_price), order_unit = values(order_unit), description = values(description), order_unit_qty = values(order_unit_qty), last_count_date = values(last_count_date), superseded_to = values(superseded_to), upc = values(upc), bin1 = values(bin1), bin2 = values(bin2), bin3 = values(bin3), category = values(category), lightspeed_last_seen = values(lightspeed_last_seen), uniqid = values(uniqid), lightspeed_present_flag = values(lightspeed_present_flag), retail = values(retail)", $part->PartNumber, $part->SupplierCode, $part->Description, $part->OnHand, $part->Avail, $part->OnOrder, $part->OnOrderAvail, date("Y-m-d H:i:s", strtotime($part->LastSoldDate)), date("Y-m-d H:i:s", strtotime($part->LastReceivedDate)), $part->ReOrderMethod, $part->MinimumQty, $part->MaximumQty, $part->Cost, $part->CurrentActivePrice, $part->OrderUnit, $part->OrderUnitQty, date("Y-m-d H:i:s", strtotime($part->LastCountDate)), $part->SupersededTo, $part->UPC, $part->Bin1, $part->Bin2, $part->Bin3, $part->category, $part->Retail) . "\n";
                    print "Database error: \n";
                    print $this->db->_error_number() . " - " . $this->db->_error_message() . "\n";
                    exit();

                }


            }
        }

        // OK, now, you should be able to delete the ones you skipped.
        $this->db->query("Delete from lightspeedpart where uniqid = ? and lightspeed_present_flag = 0", array($uniqid));

        // Repair the parts...
        $this->repair_parts();
    }


    protected function propagate_lightspeed_1() {
        // Step #1: For known items, we need to update the quantity, and the cost, and maybe some other stuff...and we need to ripple it up to the part number object, and we need to then flag tehse as processed.
        $this->db->query("Update lightspeedpart join partdealervariation using (partvariation_id) set partdealervariation.cost = lightspeedpart.cost, partdealervariation.price = lightspeedpart.current_active_price,  partdealervariation.quantity_available = lightspeedpart.available, partdealervariation.quantity_last_updated = lightspeedpart.lightspeed_last_seen, lightspeedpart.lightspeed_present_flag = 1");

        // Do we have to update the partnumber?
        if ($this->lightSpeedPartPricingRule()) {
            $this->db->query("Update partnumber join partdealervariation using (partnumber_id) join lightspeedpart using (partvariation_id) set partnumber.price = partdealervariation.price, partnumber.cost = partdealervariation.cost, partnumber.dealer_sale = partdealervariation.price, partnumber.sale = partdealervariation.price, partnumber.inventory = partdealervariation.quantity_available");
        }
    }

    protected $_distributorNameLookup;
    protected function _getDistributorByName($distributor_name) {
        if (!isset($this->_distributorNameLookup)) {
            $this->_distributorNameLookup = array();
        }

        if (array_key_exists($distributor_name, $this->_distributorNameLookup)) {
            return $this->_distributorNameLookup[$distributor_name];
        }

        // we have to go get it...
        $query = $this->db->query("Select distributor_id from distributor where name = ?", array($distributor_name));
        foreach ($query->result_array() as $row) {
            $this->_distributorNameLookup[$distributor_name] = $row["distributor_id"];
        }

        return $this->_distributorNameLookup[$distributor_name];
    }

    // TODO - how do we piece this all together?
    public function repair_parts($debug = 0) {
        $debug = $debug > 0;
        $CI =& get_instance();
        $CI->load->model("admin_m");
        $CI->load->model("migrateparts_m");
        $uniqid = uniqid("repair_parts+");
        $this->db->query("Update lightspeedpart set uniqid = ?, lightspeed_present_flag = 0", array($uniqid));

        $this->propagate_lightspeed_1();


        // Step #2: We should attempt to flag them as being eligible for product receiving. This is the easiest, best case: It's just like our regular functionality for product receiving.
        $progress = false;
        $id = 0;
        $CI =& get_instance();
        $CI->load->model("Lightspeedsuppliercode_m");

        $stock_codes = "('" . implode("', '", $CI->Lightspeedsuppliercode_m->getDistributorSupplierCodes()) . "')";

        if ($debug) {
            print "Stock codes: $stock_codes \n";
        }

        do {
            $progress = false;

            // OK, try to get some...we only do batches of 200; this just seems like a good #
            $query = $this->db->query("Select * From lightspeedpart where available > 0 and partvariation_id is null and supplier_code in $stock_codes and lightspeedpart_id > ? order by lightspeedpart_id limit 10", array($id));
            $rows = $query->result_array();

            if (count($rows) > 0) {
                if ($debug) {
                    print "Considering " . count($rows) . " rows \n";
                }
                $progress = true;

                // OK, attempt to do them...
                foreach ($rows as &$row) {
                    if ($id < $row["lightspeedpart_id"]) {
                        $id = $row["lightspeedpart_id"];
                    }
                    $m = $CI->Lightspeedsuppliercode_m->query($row["supplier_code"]);
                    $row["distributor"] = $m["distributor_name"];
                }

                // now, post them
                $clean_rows = $this->migrateparts_m->queryMatchingPart($rows);

                if ($debug) {
                    print "Clean rows return: \n";
                    print count($clean_rows) . " total rows \n";
                    $pv = $epv = 0;
                    foreach ($clean_rows as $cr) {
                        if ($cr["migrate"]) {
                            $pv++;
                        } else if ($cr["epv"]["eternalpartvariation_id"] > 0) {
                            $epv++;
                        }
                    }
                    print "PV: $pv \n";
                    print "EPV: $epv \n";
                }

                foreach ($clean_rows as $row) {
                    // attempt to receive it... distributor_id, partnumber, cost, quantity
                    if ($row["migrate"]) {
                        // JLB 03-05-18
                        // We expect and eternal partvariation id, and so we just have to fetch it and then add to local inventory.
                        $query = $this->db->query("Select * From partvariation where ext_partvariation_id = ?", array($row["ext_partvariation_id"]));
                        foreach ($query->result_array() as $zrow) {
                            // JLB 03-05-18
                            // I can't unwind the code around line 1730 in admin_m, but this is the same thing...yuck.
                            $data = $zrow;
                            $data['cost'] = $row["cost"];
                            $data['price'] = $row["current_active_price"];
                            $data['quantity_available'] = $row["available"];
                            unset($data['bulk_insert_round']);
                            unset($data['ext_partvariation_id']);
                            unset($data['protect']);
                            unset($data['customerdistributor_id']);
                            unset($data['from_lightspeed']);
                            unset($data['from_hlsm']);
                            $this->db->insert('partdealervariation', $data);


                            $this->db->query("Update lightspeedpart set lightspeedpart.partvariation_id = ?, lightspeed_present_flag = 1 where lightspeedpart.lightspeedpart_id = ? ", array($zrow["partvariation_id"], $row["lightspeedpart_id"]));
                        }
//                        $CI->admin_m->updateDistributorInventory(array(
//                            array(
//                                "distributor_id" => ($row["distributor_id"] = $this->_getDistributorByName($row["distributor"])),
//                                "partnumber" => $row["part_number"],
//                                "price" => $row["current_active_price"],
//                                "cost" => $row["cost"],
//                                "quantity" => $row["available"]
//                            )
//                        ));

                    } elseif ($row["inventory"]) {
                        // We have found the eternal part variation...
                        $this->db->query("Update lightspeedpart set eternalpartvariation_id = ?, lightspeed_present_flag = 1 where lightspeedpart_id = ?", array($row["epv"]["eternalpartvariation_id"], $row["lightspeedpart_id"]));
                    }
                }
            }

        } while($progress);

        // propagate it, again
        $this->propagate_lightspeed_1();


        // Step #3: Now, we look at those ones where there is something known about them from the distributor...We may need a distributor map and some way to find these things...And, in this case, we're going to find ourselves updating the partdealervariation quantity and reprocessing the part...


        // You have to queue these parts.
        $this->db->query("Insert into queued_parts (part_id) select distinct part_id from partpartnumber join partvariation using (partnumber_id) join lightspeedpart using (partvariation_id)");

        // TODO - you really should chew through that parts queue.
    }


    /**
     * Function to call ebay API using the xml passed to it.
     * @param type $xml
     * @return type
     * @access private
     * @author Manish
     */
    protected function call($str, $method = null) {
        $connection = curl_init();
        $this->credentials();
//set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl . $str);

//stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

        if (!empty($method)) {
            curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $method);
        }

        global $PSTAPI;
        initializePSTAPI();
        $integration_type = $PSTAPI->config()->getKeyValue('lightspeed_integration_type', 'dealer_direct');
        if ($integration_type == 'dealer_direct') {
            /*
            * Old way: user can set credentials under the Store Profile in admin panel
            */
            curl_setopt($connection, CURLOPT_USERPWD, $this->cred['Setting']['user'].':'.$this->cred['Setting']['pass']);
        } else if ($integration_type == 'pst'){
            /*
            * New way: we use PST specific credentials, user need to specify CMF #s under the Store Profile in admin panel
            */
            curl_setopt($connection, CURLOPT_USERPWD, LIGHTSPEED_PST_USERNAME.':'.LIGHTSPEED_PST_PASSWORD);
        } else {
            throw new \Exception("Unknown Lightspeed integration type : ".$integration_type);
        }

//set the headers using the array of headers
        if (!empty($method)) {
            curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
        } else {
            curl_setopt($connection, CURLOPT_HTTPHEADER, $this->headers);
        }

//set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

//Send the Request
        $response = curl_exec($connection);

//close the connection
        curl_close($connection);
        return $response;
    }

    private function getHeaders() {
        $this->boundary = "MIME_boundary";
        if ($this->check_header_type_image) {
            $data = 'Content-Type: multipart/form-data; boundary=' . $this->boundary;
        } else {
            $data = "";
        }
        $this->headers = array(
            $data
        );
    }

    /**
     * Function to get all ebay auth setting from db
     * @access private
     * @author Anik Goel
     */

    private function credentials($die_on_error = true) {
        $sql = "SELECT lightspeed_username, lightspeed_password  FROM contact WHERE id = 1";
        $query = $this->db->query($sql);

        if( ( !$cred = $query->result_array() ) || $cred[0]['lightspeed_username'] == "" || $cred[0]['lightspeed_password'] == "" ) {
            if ($die_on_error) {
                // HKofModesto
                throw new Exception("Lightspeed credentials not found.");
            }
        }

        $this->cred['Setting']['user']  = trim($cred[0]['lightspeed_username']);
        $this->cred['Setting']['pass']  = trim($cred[0]['lightspeed_password']);
    }

    public function getCredentials() {
        $this->credentials(false);
        return array(
            "user" => $this->cred['Setting']['user'],
            "pass" => $this->cred['Setting']['pass']
        );
    }

    public function activeOnAdd() {
        return $this->_subContactFetch("lightspeed_active_load") > 0;
    }

    public function setActiveOnAdd($setting = 0) {
        $this->_subContactSet("lightspeed_active_load", $setting);
    }

    public function destinationOnAdd() {
        return $this->_subContactFetch("lightspeed_default_destination_charge");
    }

    public function setDestinationOnAdd($setting = 0) {
        $this->_subContactSet("lightspeed_default_destination_charge", $setting);
    }

    public function unitCycleTraderDefault() {
        return $this->_subContactFetch("lightspeed_cycletrader_load") > 0;
    }

    public function setUnitCycleTraderDefault($value = 0) {
        $this->_subContactSet("lightspeed_cycletrader_load", $value);
    }


    public function lightSpeedPartPricingRule() {
        return $this->_subContactFetch("lightspeed_override_parts_pricing") > 0;
    }

    public function setLightSpeedPartPricingRule($value = 0) {
        $this->_subContactSet("lightspeed_override_parts_pricing", $value);
    }

    protected function _subContactSet($key, $value) {
        $this->db->query("Update contact set $key = ? where id = 1", array($value));
    }

    protected function _subContactFetch($key) {
        $query = $this->db->query("Select $key from contact where id = 1");
        $lightspeed_active_load = 0;

        foreach ($query->result_array() as $row) {
            $lightspeed_active_load = $row["$key"];
        }

        return $lightspeed_active_load;
    }

}
