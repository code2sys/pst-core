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
            "power equipment" => "Lawn and Garden",
            "scooter" => "Scooter",
            "ruv" => "RUV"
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
            $this->db->query("Insert into motorcycle_category (name) values (?)", array($category_name));
            $category_id = $this->db->insert_id();
        }

        return $category_id;
    }

    public function cleanColors($color) {
        $lut = array(
            "BK" => "Black",
            "GN" => "Green",
            "RE" => "Red",
            "WTE" => "White",
            "OR" => "Orange",
            "GY" => "Gray",
            "LIM" => "Lime",
            "BL" => "Blue",
            "GRY" => "Gray",
            "WH" => "White",
            "YW" => "Yellow",
            "SIL" => "Silver",
            "WHT" => "White",
            "BE" => "Beige",
            "BLU" => "Blue",
            "CM GY" => "Camo Gray",
            "KRT" => "Kawasaki Racing Team",
            "SL" => "Silver",
            "BLK" => "Black",
            "CAMO" => "Camo",
            "GN CAMO" => "Green Camo",
            "TIT" => "Titanium",
            "GRN" => "Green",
            "RED" => "Red"
        );

        if (array_key_exists($color, $lut)) {
            $color = $lut[$color];
        } else {
            print "UNRECOGNIZED COLOR: $color \n";
        }
        return $color;
    }

    public function get_major_units() {
        $CI =& get_instance();
        $CI->load->model("CRS_m");
        $CI->load->model("CRSCron_M");

        $string = "Dealer";
        $call = $this->call($string);
        $dealers = json_decode($call);

        if($dealers == NULL) {
            throw new \Exception("An error occurred and no data was received from Lightspeed. Possible cause: incorrect Lightspeed username or password.");
        }

        $this->db->query("Update motorcycle set lightspeed_flag = 0");

        $valid_count = 0;
        $crs_trim_matches = 0;

        $ts = time();
        foreach($dealers as $dealer) {
            $string = "Unit/".$dealer->Cmf;
            $call = $this->call($string);
            $bikes = json_decode($call);

            foreach($bikes as $bike) {
                $bike->NewUsed = ($bike->NewUsed=="U")?2:1;
                $bike->WebTitle = ($bike->WebTitle!="") ? $bike->WebTitle : $bike->ModelYear ." " . $bike->Make . " " . ($bike->CodeName != "" ? $bike->CodeName : $bike->Model);

                $data = array('total_cost' => $bike->totalCost, 'unit_cost' => $bike->totalCost, 'parts' => "", 'service' => "", 'auction_fee' => "", 'misc' => "");
                $bike->data = json_encode($data);

                $where = array(
                    "sku" => $bike->StockNumber
                );

                $bike->WebPrice = ($bike->WebPrice <= 0) ? $bike->MSRP : $bike->WebPrice;
                $bike->Color = $this->cleanColors($bike->Color);

                $motorcycle_array = array(
                    'lightspeed_dealerID' => $bike->DealerId,
                    'sku' => $bike->StockNumber,
                    'condition' => $bike->NewUsed,
                    "vehicle_type" => $this->fetchMotorcycleType($bike->UnitType),
                    'category' => $this->fetchMotorcycleCategory($bike->UnitType), // TODO
                    'year' => $bike->ModelYear,
                    'make' => $bike->Make,
                    'model' => $bike->Model,
                    'vin_number' => $bike->VIN,
                    'lightspeed_location' => $bike->Location,
                    'lightspeed_timestamp' => $ts,
                    'mileage' => $bike->Odometer,
                    'data' => $bike->data,
                    'color' => $bike->Color,
                    'sale_price' => $bike->WebPrice,
                    'retail_price' => $bike->MSRP,
                    'description' => $bike->WebDescription,
                    'call_on_price' => $bike->WebPriceHidden,
                    'title' => $bike->WebTitle,
                    "destination_charge" => ($bike->DSRP > $bike->MSRP || $bike->FreightCost > 0) ? 1 : 0,
                    "lightspeed" => 1,
                    "lightspeed_flag" => 1,
                    "source" => "Lightspeed",
                    "status" => 1
                );

                $update_array = array(
                    'lightspeed_dealerID' => $bike->DealerId,
                    'sku' => $bike->StockNumber,
                    'vin_number' => $bike->VIN,
                    'lightspeed_location' => $bike->Location,
                    'lightspeed_timestamp' => $ts,
                    'mileage' => $bike->Odometer,
                    'data' => $bike->data,
                    'color' => $bike->Color,
                    'sale_price' => $bike->WebPrice,
                    'retail_price' => $bike->MSRP,
                    'description' => $bike->WebDescription,
                    'call_on_price' => $bike->WebPriceHidden,
                    "destination_charge" => ($bike->DSRP > $bike->MSRP || $bike->FreightCost > 0) ? 1 : 0,
                    "lightspeed" => 1,
                    "lightspeed_flag" => 1,
                    "source" => "Lightspeed",
                    "status" => 1
                );

                $results = $this->selectRecords('motorcycle', $where);
                if($results) {
                    $where = array('sku' => $bike->StockNumber);
                    $motorcycle = $this->updateRecord('motorcycle', $update_array, $where, FALSE);
                    $valid_count++;
                } else {
                    // we have to set some nulls. I think this is stupid, too.
                    $motorcycle_array["engine_type"] = "";
                    $motorcycle_array["transmission"] = "";
                    $motorcycle_array["margin"] = $bike->WebPrice > 0 ?  round(($bike->WebPrice - $bike->totalCost) / $bike->WebPrice, 2) : 0;
                    $motorcycle_array["profit"] = $bike->WebPrice > 0 ? $bike->WebPrice - $bike->totalCost : 0;
                    $motorcycle_array["craigslist_feed_status"] = 0;
                    $motorcycle_array["cycletrader_feed_status"] = 0;

                    $motorcycle = $this->createRecord('motorcycle', $motorcycle_array, FALSE);
                    $valid_count++;
                }

                $motorcycle_id = 0;
                $query = $this->db->query("Select id from motorcycle where sku = ?", array($motorcycle_array["sku"]));
                foreach ($query->result_array() as $row) {
                    $motorcycle_id = $row["id"];
                }

                // Now, what is the ID for this motorcycle?
                $vin_match = $CI->CRS_m->findBestFit($bike->VIN, $bike->Make, $bike->Model, $bike->ModelYear, $bike->CodeName);

                print_r($vin_match);

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
                }

                // Todo...
                // Does this motorcycle have a zero group or a general group of settings? We need to be able to flag the settings group that comes from Lightspeed in some way...
                // Finally, we need to optionally stick in the settings if they exist into this spec table...
                // At last, we should attempt to look up the trim of this by CRS and, if there is one, set the trim ID. We may also adjust the category and type if we get a match...
            }

        }

        if ($valid_count > 0) {
            $this->db->query("Delete from motorcycle where lightspeed = 1 and lightspeed_flag = 0");
        }
    }

    public function get_parts_xml() {

        $string = "Dealer";
        $call = $this->call($string);
        $dealers = json_decode($call);



        foreach($dealers as $dealer) {

            echo "<br>Dealer id: " . $dealer->Cmf;
            $string = "Part/".$dealer->Cmf;
            $call = $this->call($string);
            $call = json_decode($call);
            echo "<pre>";
            var_dump($call);

        }

    }
    public function get_units_xml() {

        $string = "Dealer";
        $call = $this->call($string);
        $dealers = json_decode($call);



        foreach($dealers as $dealer) {

            echo "<br>Dealer id: " . $dealer->Cmf;
            $string = "Unit/".$dealer->Cmf;
            $call = $this->call($string);
            $call = json_decode($call);
            echo "<pre>";
            var_dump($call);

        }

    }

    public function get_parts() {
        $string = "Dealer";
        $call = $this->call($string);
        $dealers = json_decode($call);



        foreach($dealers as $dealer) {

            echo "<br>Dealer id: " . $dealer->Cmf;
            $string = "Part/".$dealer->Cmf;
            $call = $this->call($string);
            //var_dump($call);
            $parts = json_decode($call);
            echo "parts: " . count($parts);
            foreach($parts as $part) {

                // Check data and tables before proceeding

                if($part->Description==NULL||$part->PartNumber==NULL||$part->UPC==NULL) continue;

                $partnumber_array = array('partnumber' => $part->PartNumber );

                $partnumber = $this->selectRecord('partnumber', $partnumber_array, FALSE);

                //if($partnumber) = continue;

                $part_array = array('name' => $part->Description );

                $part_id = $this->selectRecord('part', $part_array, FALSE);

                //if($part_id) continue;

                $partvariation_array = array( 'manufacturer_part_number' => iconv("UTF-8", "ISO-8859-1", $part->UPC) );

                $partvariation_id = $this->selectRecord('partvariation', $partvariation_array, FALSE);

                //if($partvariation_id) continue;

                if( ( $partnumber || $part_id || $partvariation_id ) ) {
                    // This is an update

                    // var_dump($partnumber);
                    // var_dump($part_id);
                    // var_dump($partvariation_id);
                    // die();
                    if($partnumber) echo "#### Partnumber found: ".$part->PartNumber."<br>";
                    if($part_id) echo "#### Part_id found: ".$part->Description."<br>";
                    if($partvariation_id) echo "#### Partvariation found: ".$part->UPC."<br>";
                    echo "#### UPDATE<br>";
                    $partnumber_array = array('partnumber' => $part->PartNumber,
                        'sale' => $part->CurrentActivePrice,
                        'cost' => $part->Cost,
                        'price' => $part->Retail,
                        'inventory' => $part->Avail,
                        'lightspeed_part' => 1);

                    echo "<br><br>Partnumber: ";
                    var_dump($partnumber);

                    $where = array('partnumber_id' => $partnumber['partnumber_id']);

                    $partnumber_id = $this->updateRecord('partnumber', $partnumber_array, $where, FALSE);

                    echo "<br>Partnumber ".$partnumber['partnumber_id']." updated<br>";

                    $partvariation_array = array('quantityAvailable' => $part->Avail,
                        'cost' => $part->Cost,
                        'price' => $part->Retail,
                        'manufacturer_part_number' => $part->UPC );

                    echo "<br><br>Partvariation: ";
                    var_dump($partvariation_array);

                    $where = array('partnumber_id' => $partnumber['partnumber_id']);

                    $partvariation_id = $this->updateRecord('partvariation', $partvariation_array, $where, FALSE);

                    echo "<br>Partvariation $partvariation_id updated<br>";

                } else {
                    // this is a new entry

                    echo "#### INSERT<br>";
                    $partnumber_array = array('partnumber' => $part->PartNumber,
                        'sale' => $part->CurrentActivePrice,
                        'cost' => $part->Cost,
                        'price' => $part->Retail,
                        'inventory' => $part->Avail,
                        'lightspeed_part' => 1);

                    echo "<br><br>";
                    var_dump($partnumber_array);

                    $partnumber_id = $this->createRecord('partnumber', $partnumber_array, FALSE);

                    echo "<br>Partnumber $partnumber_id created<br>";

                    $partvariation_array = array('partnumber_id' => $partnumber_id,
                        'quantityAvailable' => $part->Avail,
                        'cost' => $part->Cost,
                        'price' => $part->Retail,
                        'manufacturer_part_number' => $part->UPC );

                    echo "<br><br>";
                    var_dump($partvariation_array);

                    $partvariation_id = $this->createRecord('partvariation', $partvariation_array, FALSE);

                    echo "<br>Partvariation $partvariation_id created<br>";


                    var_dump($partnumber_id);

                    echo "<br><br>";
                    var_dump($part_array);

                    $part_id = $this->createRecord('part', $part_array, FALSE);

                    echo "<br>Part $part_id created<br>";

                    $partpartnumber_array = array('partnumber_id' => $partnumber_id, 'part_id' => $part_id );

                    echo "<br><br>";
                    var_dump($partpartnumber_array);

                    $partpartnumber = $this->createRecord('partpartnumber', $partpartnumber_array, FALSE);

                    echo "<br>Partpartnumber $partpartnumber created<br>";

                }

            }
            echo "<br>********************";


        }
    }


    /**
     * Function to call ebay API using the xml passed to it.
     * @param type $xml
     * @return type
     * @access private
     * @author Manish
     */
    protected function call($str) {
        $connection = curl_init();
        $this->credentials();
//set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl . $str);

//stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($connection, CURLOPT_USERPWD, $this->cred['Setting']['user'].':'.$this->cred['Setting']['pass']);

//set the headers using the array of headers
        curl_setopt($connection, CURLOPT_HTTPHEADER, $this->headers);

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
}
