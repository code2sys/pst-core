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
            "power equipment" => "Generators",
            "scooter" => "Scooter",
            "ruv" => "RUV",
            "generators" => "Generators",
            "lawn and garden" => "Lawn and Garden",
            "dirt bike" => "Off-Road"
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
        $lut = array(
            // This came from Modesto
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
            "RED" => "Red",
            "" => "N/A",
            // This came from Holiday

            'ACTIVE YELLOW' =>'Active Yellow',
            'ATV RENEGADE XMR 1000 REFI R 1' =>'ATV RENEGADE XMR 1000 REFI R 1',
            'AVALANCE GREY/LIME SQUEEZE' =>'Avalance Grey/Lime Squeeze',
            'AVALANCGE GRAY/LIME SQUEEZE' =>'Avalancge Gray/Lime Squeeze',
            'AVALANCHE GRAY/PINK POWER' =>'Avalanche Gray/Pink Power',
            'BLACK' =>'Black',
            'BLACK PEARL' =>'Black Pearl',
            'BLACK RED WHITE' =>'Black Red White',
            'BLACK/CANDY ORANGE' =>'Black/Candy Orange',
            'BLACK/RED/WHITE' =>'Black/Red/White',
            'BLACK&CAN-AM RED' =>'Black&Can-Am Red',
            'BRIGHT YELLOW' =>'Bright Yellow',
            'BRUHED ALUMINUM CAN-AM RED' =>'Bruhed Aluminum Can-Am Red',
            'BRUSHED ALUMINUM & CAN-AM RED' =>'Brushed Aluminum & Can-Am Red',
            'CAN-AM RED' =>'Can-Am Red',
            'CAN-AM RED&BLACK' =>'Can-Am Red&Black',
            'CANDY RED' =>'Candy Red',
            'CARBON  BLACK&CAN-AM RED' =>'Carbon Black & Can-Am Red',
            'CARBON BLACK & SUNBURST YELLOW' =>'Carbon Black & Sunburst Yellow',
            'CRUISER BLACK' =>'Cruiser Black',
            'CRUISER BLACK/LIME SQUEEZE' =>'Cruiser Black/Lime Squeeze',
            'DIVER BLUE' =>'Diver Blue',
            'GHOST GRAY' =>'Ghost Gray',
            'GHOST GREY' =>'Ghost Gray',
            'GRAY' =>'Gray',
            'GRAY METALLIC' =>'Gray Metallic',
            'GRAY WITH PINK' =>'Gray With Pink',
            'GREEN' =>'Green',
            'GREY/LIME SQUEEZE' =>'Grey/Lime Squeeze',
            'HONDA PHANTOM CAMO' =>'Honda Phantom Camo',
            'HYPER SILVER & YELLOW SUNBURST' =>'Hyper Silver & Yellow Sunburst',
            'INDY RED' =>'Indy Red',
            'INTENSE RED' =>'Intense Red',
            'LIME SQUEEZE' =>'Lime Squeeze',
            'MATTE GRAY METALLIC' =>'Matte Gray Metallic',
            'MATTE PEARL WHITE' =>'Matte Pearl White',
            'MATTE SILVER' =>'Matte Silver',
            'MATTE SILVER METALLIC' =>'Matte Silver Metallic',
            'METALLIC BLUE' =>'Metallic Blue',
            'MIDNIGHT BLUE' =>'Midnight Blue',
            'MOSSY -OAK BREAK-UP COUNTRY CAMO' =>'Mossy Oak Break-Up Country Camo',
            'MOSSY OAK BREAK-UP COUNTRY CAMO' =>'Mossy Oak Break-Up Country Camo',
            'MOSSY OAK CAMO' =>'Mossy Oak Camo',
            'NARA BRONZE' =>'Nara Bronze',
            'NAVY BLUE METALLIC' =>'Navy Blue Metallic',
            'OLIVE' =>'Olive',
            'ORANGE' =>'Orange',
            'PEARL BLACK' =>'Pearl Black',
            'PEARL ORANGE' =>'Pearl Orange',
            'PEARL RED' =>'Pearl Red',
            'PEARL WHITE' =>'Pearl White',
            'PHANTON CAMO' =>'Phanton Camo',
            'PLATINUM SATIN' =>'Platinum Satin',
            'POLARIS PURSIT CAMO' =>'Polaris Pursit Camo',
            'POLARIS PURSUIT CAMO' =>'Polaris Pursuit Camo',
            'PPC' =>'Ppc',
            'PURE MAGNESIUM METALLIC' =>'Pure Magnesium Metallic',
            'PURSUIT CAMO' =>'Pursuit Camo',
            'RADAR BLUE' =>'Radar Blue',
            'RADAR BLUE METALLIC' =>'Radar Blue Metallic',
            'RED/BLACK' =>'Red/Black',
            'RED/BLACK/WHITE' =>'Red/Black/White',
            'RED/WHITE/BLUE' =>'Red/White/Blue',
            'RED&BLACK' =>'Red&Black',
            'RIDE COMMAND EDITION' =>'Ride Command Edition',
            'S. GREEN' =>'S. Green',
            'SAGE GREEN' =>'Sage Green',
            'SILVER PEARL' =>'Silver Pearl',
            'SOLAR RED' =>'Solar Red',
            'STEAL BLACK' =>'Steal Black',
            'STEALTH BLACK' =>'Stealth Black',
            'SUEDE METALLIC' =>'Suede Metallic',
            'SUNBURST YELLOW' =>'Sunburst Yellow',
            'SUNSET RED' =>'Sunset Red',
            'SUNSET RED FOX EDITION' =>'Sunset Red Fox Edition',
            'SUNSET RED METALLIC' =>'Sunset Red Metallic',
            'SUNST RED METALLIC' =>'Sunst Red Metallic',
            'TIMELESS BLACK' =>'Timeless Black',
            'TITANIUM' =>'Titanium',
            'TITANIUM MATTE METALLIC' =>'Titanium Matte Metallic',
            'TRIPLE BLACK' =>'Triple Black',
            'VAPOR WHITE' =>'Vapor White',
            'VELOCITY BLUE' =>'Velocity Blue',
            'VICTORY RED' =>'Victory Red',
            'VODOO BLUE' =>'Vodoo Blue',
            'WHITE' =>'White',
            'WHITE LIGHTING' =>'White Lightning',
            'WHITE LIGHTNING' =>'White Lightning',
            'WHITE LIGHTNING W/ REFLEX BLUE' =>'White Lightning W/ Reflex Blue',
            'WHITE, BLK, CAN AM RED' =>'White, Blk, Can Am Red',
            'WHITE,BLACK&CAN-AM RED' =>'White,Black&Can-Am Red',
            'WHITE/BLUE/RED' =>'White/Blue/Red',
            'WHITE/RED' =>'White/Red',
            'YELLOW' =>'Yellow',
            "LM" => "Lime",
"PURE MAGNESUIM METALLIC" => "PURE MAGNESUIM METALLIC",
"WHITE&CAN-AM RED" => "WHITE&CAN-AM RED",
"WHITE&BLACK& CAN-AM RED" => "WHITE&BLACK&CAN-AM RED",
"CARBON  BLACK&CAN-AM RED" => "CARBON BLACK&CAN-AM RED",
"CAN-AM RED & BLACK" => "CAN-AM RED&BLACK",
"BLACK &CAN-AM RED" => "BLACK&CAN-AM RED",
"BLUE" => "BLUE",
"GRAY/PINK" => "GRAY/PINK",
"BRIGHT WHITE/INDY RED" => "BRIGHT WHITE/INDY RED"
        );

        $color = trim($color);
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
                if (isset($bike->OnHold) && trim($bike->OnHold) != "") {
                    continue; // It's on hold for a deal. Not going to put that in tonight!
                }

                if (isset($bike->UnitStatus) && trim($bike->UnitStatus) == "R") {
                    continue; // It has been removed.
                }

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
                $vin_match = $CI->CRS_m->findBestFit($bike->VIN, $bike->Make, $bike->Model, $bike->ModelYear, $bike->CodeName, $bike->MSRP);

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


                // Todo...
                // Does this motorcycle have a zero group or a general group of settings? We need to be able to flag the settings group that comes from Lightspeed in some way...
                // Finally, we need to optionally stick in the settings if they exist into this spec table...
                // At last, we should attempt to look up the trim of this by CRS and, if there is one, set the trim ID. We may also adjust the category and type if we get a match...
            }

        }

        if ($valid_count > 0) {
            $this->db->query("Update motorcycle set deleted = 1 where lightspeed = 1 and lightspeed_flag = 0");
        }

        // JLB 12-29-17
        // At the end of this, we will remove any CRS items that overlap bikes from Lightspeed
        $CI->CRSCron_M->removeExtraCRSBikes();
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
            print_r($call);
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
            print_r($call);

        }

    }

    /*
     * The idea of this one is to simply shore up the import process enough to populate the lightspeed table.
     * There has to be a second routine that makes those things right. Thus, you can pull these in all you want -
     * you then have to do the right thing with them.
     */
    public function get_parts() {
        $string = "Dealer";
        $call = $this->call($string);
        $dealers = json_decode($call);

        // flag them all to clear them
        $uniqid = uniqid("get_parts_");
        $this->db->query("Update lightspeedpart set uniqid = ?, lightspeed_present_flag = 0", array($uniqid));

        foreach($dealers as $dealer) {
            $string = "Part/".$dealer->Cmf;
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
        $this->db->query("Update partnumber join partdealervariation using (partnumber_id) join lightspeedpart using (partvariation_id) set partnumber.price = partdealervariation.price, partnumber.cost = partdealervariation.cost, partnumber.dealer_sale = partdealervariation.price, partnumber.sale = partdealervariation.price, partnumber.inventory = partdealervairation.quantity_available");
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
    public function repair_parts() {
        print "A1\n";
        $CI =& get_instance();
        $CI->load->model("admin_m");
        $uniqid = uniqid("repair_parts+");
        $this->db->query("Update lightspeedpart set uniqid = ?, lightspeed_present_flag = 0", array($uniqid));

        $this->propagate_lightspeed_1();
        print "A2\n";


        // Step #2: We should attempt to flag them as being eligible for product receiving. This is the easiest, best case: It's just like our regular functionality for product receiving.
        $progress = false;
        $id = 0;
        global $LightspeedSupplierLookAside;
        $stock_codes = "('" . implode("', '", array_keys($LightspeedSupplierLookAside)) . "')";
        do {
            print "A3\n";
            // OK, try to get some...we only do batches of 200; this just seems like a good #
            print "Query: Select * From lightspeedpart where partvariation_id is null and supplier_code in $stock_codes limit 200 \n";
            $query = $this->db->query("Select * From lightspeedpart where partvariation_id is null and supplier_code in $stock_codes limit 200");
            $rows = $query->result_array();


            if (count($rows) > 0) {
                print "Row count: " .count($rows) ."\n";
                $progress = true;

                // OK, attempt to do them...
                foreach ($rows as &$row) {
                    $row["distributor"] = $LightspeedSupplierLookAside[$row["supplier_code"]];
                }

                // now, post them

                $ch = curl_init("http://" . WS_HOST . "/migrateparts/queryMatchingPart/");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data = json_encode($rows));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, count($data));
                $clean_rows = json_decode(curl_exec($ch), true);

                foreach ($clean_rows as $row) {
                    // attempt to receive it... distributor_id, partnumber, cost, quantity
                    if ($row["migrate"]) {
                        $CI->admin_m->updateDistributorInventory(array(
                            array(
                                "distributor_id" => ($row["distributor_id"] = $this->_distributorNameLookup($row["distributor"])),
                                "partnumber" => $row["part_number"],
                                "cost" => $row["cost"],
                                "quantity" => $row["on_hand"]
                            )
                        ));
                        $this->db->query("Update lightspeedpart join partvariation set lightspeedpart.partvariation_id = partvariation.partvariation_id where lightspeedpart.lightspeedpart_id = ? and partvariation.distributor_id = ? and partvariation.part_number = ?", array($row["lightspeedpart_id"], $row["distributor_id"], $row["part_number"]));
                    }
                }
            }

        } while($progress);

        // propagate it, again
        $this->propagate_lightspeed_1();


        // Step #3: Now, we look at those ones where there is something known about them from the distributor...We may need a distributor map and some way to find these things...And, in this case, we're going to find ourselves updating the partdealervariation quantity and reprocessing the part...


        // You have to queue these parts.
        $this->db->query("Insert into queued_parts (part_id) select distinct part_id from partpartnumber join partvariation using (partnumber_id) join lightspeedpart using (partvariation_id)");

        // TODO - you really should


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
