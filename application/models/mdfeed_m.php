<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 4/12/18
 * Time: 10:23 AM
 */

class Mdfeed_m extends CI_Model {

    public function get_major_units($debug = 0) {
        $CI =& get_instance();
        $CI->load->model("CRS_m");
        $CI->load->model("CRSCron_M");
        $CI->load->model("Lightspeed_m");

        $found_bikes = false;

        // OK, we need to get that feed, if it exists
        $feeds = $this->get_md_feed($debug = 0);

        if (count($feeds) > 0) {
            $found_bikes = true;

            // OK, we have to process in a manner similar to how we process stuff coming in from Lightspeed
            // See for reference Lightspeed_m::get_major_units
            initializePSTAPI();
            global $PSTAPI;

            // First, flag all of them...
            $PSTAPI->motorcycle()->updateWhere(array("mdfeed_flag" => 0), array("mdfeed" => 1));

            // Now, we have to iterate these guys...
            foreach ($feeds as $feed) {
                foreach ($feed["records"] as $record) {
                    $matches = $PSTAPI->motorcycle()->fetch(array("mdfeed" => 1, "mdrecord_recordid" => $record->get("recordid")), true);

                    if (count($matches) > 0) {
                        // OK, we have an existing one; we need to formulate the update...
                        $motorcycle_id = $matches[0]["id"];

                        $update_data = array(array(
                            "title" => $record->get("title"),
                            "description" => $record->get("description"),
                            "sale_price" => $record->get("dsrp"),
                            "sku" => $record->get("STOCKNO"),
                            "color" => $record->get("color"),
                            "condition" => strtolower($record->get("sale_condition")) == "used" ? 2 : 1,
                            "make" => $record->get("make"),
                            "model" => $record->get("model"),
                            "mileage" => $record->get("mileage"),
                            "location_description" => $record->get("store") . ", " . $record->get("location_city") . " " . $record->get("location_state"),
                            "vehicle_type" => $CI->Lightspeed_m->fetchMotorcycleType($record->get("vehicle_type")),
                            "category" => $CI->Lightspeed_m->fetchMotorcycleCategory($record->get("style")),
                            "vin_number" => $record->get("vin"),
                            "year" => $record->get("year"),
                            "retail_price" => $record->get("msrp"),
                            "mdfeed" => 1,
                            "mdfeed_flag" => 1,
                            "mdfeed_deleted" => 0
                        ));

                        if ($matches[0]["customer_set_price"] > 0) {
                            if ($update_data["retail_price"] == $matches[0]["retail_price"] && $update_data["sale_price"] == $matches[0]["sale_price"]) {
                                $update_data["customer_set_price"] = 0;
                            } else {
                                $update_data["retail_price"] = $matches[0]["retail_price"];
                                $update_data["sale_price"] = $matches[0]["sale_price"];
                            }

                        }

                        if ($matches[0]["customer_set_location_description"] > 0) {
                            if ($update_data["location_description"] == $matches[0]["location_description"]) {
                                $update_data["customer_set_location_description"] = 0;
                            } else {
                                $update_data["location_description"] = $matches[0]["location_description"];
                            }
                        }


                        if ($matches[0]["customer_set_description"] > 0) {
                            if ($record->get("description") == $matches[0]["description"]) {
                                // it's the same, so clear the flag
                                $update_data["customer_set_description"] = 0;
                            } else {
                                $update_data["description"] = $record->get("description");
                            }
                        }

                        $motorcycle = $PSTAPI->motorcycle()->update($motorcycle_id, $update_data);

                    } else {
                        // we have an add...
                        $motorcycle = $PSTAPI->motorcycle()->add(array(
                            "title" => $record->get("title"),
                            "description" => $record->get("description"),
                            "sale_price" => $record->get("dsrp"),
                            "sku" => $record->get("STOCKNO"),
                            "color" => $record->get("color"),
                            "condition" => strtolower($record->get("sale_condition")) == "used" ? 2 : 1,
                            "make" => $record->get("make"),
                            "model" => $record->get("model"),
                            "mileage" => $record->get("mileage"),
                            "location_description" => $record->get("store") . ", " . $record->get("location_city") . " " . $record->get("location_state"),
                            "vehicle_type" => $CI->Lightspeed_m->fetchMotorcycleType($record->get("vehicle_type")),
                            "category" => $CI->Lightspeed_m->fetchMotorcycleCategory($record->get("style")),
                            "vin_number" => $record->get("vin"),
                            "year" => $record->get("year"),
                            "retail_price" => $record->get("msrp"),
                            "mdfeed_recordid" => $record->get("recordid"),
                            "mdfeed" => 1,
                            "mdfeed_flag" => 1,
                            "mdfeed_deleted" => 0
                        ));

                        $motorcycle_id = $motorcycle->id();
                    }

                    // Are there images? If so, we need to process them...
                    $images = $PSTAPI->mdrecordimage()->fetch(array("mdrecord_id" => $record->id(), "active" => 1), true);
                    $seen_mdrecordimage = array();

                    $counter = 0;
                    foreach ($images as $image) {
                        $counter++;
                        $matching_image = $PSTAPI->motorcycleimage()->fetch(array(
                            "motorcycle_id" => $motorcycle_id,
                            "extra_data" => "MDRECORDIMAGE",
                            "image_name" => $image["url"]
                        ));

                        if (count($matching_image) > 0) {
                            $motorcycleimage = $matching_image[0];
                        } else {
                            // I guess we add it?
                            $motorcycleimage = $PSTAPI->motorcycleimage()->add(array(
                                "motorcycle_id" => $motorcycle_id,
                                "image_name" => $image["url"],
                                "date_added" => date("Y-m-d H:i:s"),
                                "priority_number" => $counter,
                                "external" => 1,
                                "source" => "MD Feed"
                            ));
                        }

                        $seen_mdrecordimage[] = $motorcycleimage->id();
                    }

                    $existing_images = $PSTAPI->motorcycleimage()->fetch(array(
                        "motorcycle_id" => $motorcycle_id,
                        "extra_data" => "MDRECORDIMAGE"
                    ));

                    foreach ($existing_images as $image) {
                        if (!in_array($image->id(), $seen_mdrecordimage)) {
                            $image->remove();
                        }
                    }

                    // In any case, we should now do the CRM matching for the motorcycle...
                    // Now, what is the ID for this motorcycle?
                    if ($motorcycle->get("crs_trim_id") == 0) {
                        $vin_match = $CI->CRS_m->findBestFit($motorcycle->get("vin_number"), $motorcycle->get("make"), $motorcycle->get("model"), $motorcycle->get("year"), $motorcycle->get("title"), $motorcycle->get("retail_price"));

                        if (is_array($vin_match) && count($vin_match) > 0) {
                            $vin_match = $vin_match[0];
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

                            // refresh it...
                            $CI->CRSCron_M->refreshCRSData($motorcycle_id);


                            // Now, we attempt to fix the machine type...
                            $corrected_category = $this->_getMachineTypeMotoType($vin_match["machine_type"],  $vin_match["offroad"]);
                            if ($corrected_category > 0) {
                                $this->db->query("Update motorcycle set vehicle_type = ? where id = ? limit 1", array($corrected_category, $motorcycle_id));
                            }

                            // OK, we need to fix the category and we need to fix the type, if we've got it.
                            $corrected_category = 0;
                            $query2 = $this->db->query("Select value from motorcyclespec where motorcycle_id = ? and crs_attribute_id = 10011", array($motorcycle_id));
                            foreach ($query2->result_array() as $disRec) {
                                $corrected_category = $this->_getStockMotoCategory($disRec["value"]);
                            }
                            if ($corrected_category > 0) {
                                $this->db->query("Update motorcycle set category = ? where id = ? limit 1", array($corrected_category, $motorcycle_id));
                            }
                        }
                    }
                }
            }


            // Now, once we've done all that...we have to delete them...
            $PSTAPI->motorcycle()->updateWhere(array("deleted" => 1, "mdfeed_deleted" => 1), array("deleted" => 0, "mdfeed" => 1, "mdfeed_flag" => 0)); // Dead if not in the feed
            $PSTAPI->motorcycle()->updateWhere(array("deleted" => 0, "mdfeed_deleted" => 0), array("deleted" => 1, "customer_deleted" => 0, "mdfeed" => 1, "mdfeed_flag" => 1)); // Resurrected if returned to the feed
        }

        if ($found_bikes) {
            $CI->CRSCron_M->removeExtraCRSBikes();
        }

    }

    public function get_md_feed($debug = 0) {
        initializePSTAPI();
        global $PSTAPI;

        $feeds = $PSTAPI->mdfeed()->fetch();
        if ($debug > 0) {
            print "Found " . count($feeds) . " feeds \n";
        }
        $return_feeds = array("feeds" => array());
        foreach ($feeds as $f) {
            if ($debug > 0) {
                print "Feed " . $f->get("source_url") . "\n";
            }
            $return_feeds[] = array("feed" => $f, "records" => $f->generateMDRecords($debug > 0));
        }
        return $return_feeds;
    }

}
