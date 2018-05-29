<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/22/17
 * Time: 11:42 AM
 */

class Productuploadermodel extends CI_Model {

    public function getPossibleColumns() {
        return array(
            array(
                "name" => "distributor",
                "label" => "Distributor",
                "alternates" => array(),
                "description" => "The distributor/source of this product.",
                "required" => true
            ),
            array(
                "name" => "part_number",
                "label" => "Distributor Part Number",
                "alternates" => array("part number", "partnumber"),
                "description" => "The distributor unique part number. The distributor must also be specified.",
                "required" => true
            ),
            array(
                "name" => "machine_type",
                "label" => "Fitment - Machine Type",
                "alternates" => array("machinetype", "machine", "machines"),
                "description" => "Store major machine type category, or leave blank for a universal part. Known values are: " . $this->implodeList("machinetype", "name"),
                "required" => false
            ),
            array(
                "name" => "make",
                "label" => "Fitment - Manufacturer",
                "alternates" => array(),
                "description" => "The vehicle manufacturer, or leave blank for a universal part. Known values are: " . $this->implodeList("make", "name"),
                "required" => false
            ),
            array(
                "name" => "model",
                "label" => "Fitment - Model",
                "alternates" => array(),
                "description" => "The corresponding vehicle model; must specify the make, model, year, and machine type.",
                "required" => false
            ),
            array(
                "name" => "year",
                "label" => "Fitment - Year",
                "alternates" => array(),
                "description" => "Speciy vehicle year; can be specified as YY, YYYY, or YYYY-YYYY for a range.",
                "required" => false
            ),
            array(
                "name" => "part",
                "label" => "Part Name",
                "alternates" => array("product", "productname", "product name"),
                "description" => "A unique product name.",
                "required" => true
            ),
            array(
                "name" => "description",
                "label" => "Part Description",
                "alternates" => array(),
                "description" => "The description for this product.",
                "required" => false
            ),
            array(
                "name" => "category",
                "label" => "Category",
                "alternates" => array("categories"),
                "description" => "Product categorie(s). Use > to denote nested categories; use semicolon (;) to separate multiple categories.",
                "required" => true
            ),
            array(
                "name" => "question",
                "label" => "Question",
                "alternates" => array(),
                "description" => "Question for matching the part with the fitment.",
                "required" => false
            ),
            array(
                "name" => "answer",
                "label" => "Answer",
                "alternates" => array(),
                "description" => "Answer for the corresponding question.",
                "required" => false
            ),
            array(
                "name" => "manufacturer",
                "label" => "Manufacturer/Brand",
                "alternates" => array("brand"),
                "description" => "Part manufacturer/brand. Currently, we know these: " . $this->implodeList("manufacturer", "name"),
                "required" => true
            ),
            array(
                "name" => "product_question",
                "label" => "Product Question",
                "alternates" => array("Product Question", "Product Question 1", "Product Question 2", "Product Question 3", "Product Question 4"),
                "description" => "Additional product question(s). Enter one column per question; must have corresponding answers in the same order left-to-right.",
                "required" => false,
                "multiple" => true
            ),
            array(
                "name" => "product_answer",
                "label" => "Product Answer",
                "alternates" => array("Product Answer", "Product Answer 1", "Product Answer 2", "Product Answer 3", "Product Answer 4"),
                "description" => "Additional product answer(s). Must be in corresponding order to the additional product question(s)",
                "required" => false,
                "multiple" => true
            ),
            array(
                "name" => "cost",
                "label" => "Cost",
                "description" => "Cost to you for this product.",
                "required" => false,
                "multiple" => false
            ),
            array(
                "name" => "price",
                "label" => "MSRP",
                "description" => "MSRP for this product.",
                "required" => false,
                "multiple" => false
            ),
            array(
                "name" => "closeout",
                "label" => "Closeout",
                "description" => "Enter a '1' or a 'Y' in this field to mark this as on closeout; '0' or 'N' if it is not on closeout. By default, we assume products are not on closeout.",
                "required" => false,
                "multiple" => false
            ),
            array(
                "name" => "quantity",
                "label" => "Quantity Available",
                "description" => "Enter the number of items you have in stock.",
                "required" => false,
                "multiple" => false
            ),
            array(
                "name" => "weight",
                "label" => "Shipping Weight",
                "description" => "Enter the shipping weight in pounds as a decimal number - e.g., 3.25.",
                "required" => false,
                "multiple" => false
            ),
            array(
                "name" => "image",
                "label" => "Image URL",
                "description" => "Provide a URL of a GIF, JPEG, or PNG image. These will be added to existing images for the part",
                "required" => false,
                "multiple" => true
            )

        );

    }

    protected $productupload_cache;
    protected $distributor_cache;

    public function __construct() {
        parent::__construct();
        $this->upload_directory = STORE_DIRECTORY . "/uploads";
        $this->productupload_cache = array();
        $this->distributor_cache = array();
    }

    public function implodeList($table, $column) {
        $query = $this->db->query("Select distinct $column from $table order by $column");
        $results = array();
        foreach ($query->result_array() as $row) {
            $results[] = $row[$column];
        }
        return implode(", ", $results);
    }

    /*
     * Return a list that we're going to shove in a table in the view...
     */
    public function getList() {
        return $this->db->query("Select * from productupload")->result_array();
    }

    // Return a row from the database
    public function get($productupload_id) {
        if (array_key_exists($productupload_id, $this->productupload_cache)) {
            return $this->productupload_cache[$productupload_id];
        }

        $this->productupload_cache[$productupload_id] = array();
        $query = $this->db->query("Select * from productupload where productupload_id = ?", array($productupload_id));
        foreach ($query->result_array() as $row) {
            $this->productupload_cache[$productupload_id] = $row;
        }
        return $this->productupload_cache[$productupload_id];
    }

    protected function clearCache($productupload_id) {
        if (array_key_exists($productupload_id, $this->productupload_cache)) {
            unset($this->productupload_cache[$productupload_id]);
        }
    }

    // Return an ID number or 0 if error
    public function add($file_upload) {
        ini_set('auto_detect_line_endings', true); // for \r, \n, or \r\n

        $column_data = array(
            "columns" => array(),
            "first_row" => array(),
            "header" => array()
        );
        $row_count = 0;

        $handle = fopen($file_upload["tmp_name"], "r");
        if (!$handle) {
            // set an error message and return

            return 0;
        }
        $column_data["header"] = fgetcsv($handle);
        $column_data["first_row"] = fgetcsv($handle);

        if ($column_data["first_row"] !== FALSE) {
            $row_count++;
        }

        while (FALSE !== fgetcsv($handle)) {
            $row_count++;
        }

        fclose($handle);

        $new_filename = tempnam($this->upload_directory, "upload_file_");
        move_uploaded_file($file_upload["tmp_name"], $new_filename);

        $this->db->query("Insert into productupload (last_update, status, columndata, upload_file, upload_row_count, new_row_count, reject_row_count, processed_row_count, update_row_count, original_filename, mapped_count) values (now(), 'Uploaded', ?, ?, ?, 0, 0, 0, 0, ?, 0)", array(serialize($column_data), basename($new_filename), $row_count, $file_upload['name'])) or die("Query error inserting productupload.");

        return $this->db->insert_id();
    }

    public function getHeader($productupload_id) {
        $columndata = $this->getColumnData($productupload_id);
        return $columndata["header"];
    }

    public function getFirstRow($productupload_id) {
        $columndata = $this->getColumnData($productupload_id);
        return $columndata["first_row"];
    }

    public function getColumnData($productupload_id) {
        $array = $this->get($productupload_id);
        return array_key_exists("columndata", $array) ? unserialize($array['columndata']) : array("columns" => array(), "first_row" => array(), "header" => array());
    }

    public function updateColumnData($productupload_id, $columndata) {
        $this->db->query("Update productupload set columndata = ? where productupload_id = ? limit 1", array(serialize($columndata), $productupload_id));
        $this->clearCache($productupload_id);
    }

    protected function setStatus($productupload_id, $status) {
        $this->db->query("Update productupload set status = ? where productupload_id = ? limit 1", array($status, $productupload_id));
        $this->clearCache($productupload_id);
    }

    public function getColumnMapping($productupload_id) {
        $columndata = $this->getColumnData($productupload_id);
        return $columndata["columns"];
    }

    /*
     * This is supposed to also validate it and return an error string or "" if there is a problem...
     * It needs to pull stuff from $_REQUEST as appropriate
     */
    public function saveColumnMapping($productupload_id) {
        // Get the header
        $productupload = $this->get($productupload_id);
        $header = $this->getHeader($productupload_id);
        $columns = $this->getPossibleColumns();

        $error = "";

        $columnData = $this->getColumnData($productupload_id);
        $columnData["columns"] = array();

        $seen_columns = array();

        for ($i = 0; $i < count($header); $i++) {
            $columnData["columns"][] = $k = array_key_exists("column_" . $i, $_REQUEST) ? $_REQUEST["column_" . $i] : "";
            if ($k != "") {
                $seen_columns[] = $k;
            }
        }

        // now, iterate the columns
        foreach ($columns as $c) {
            if ($c["required"] && !in_array($c["name"], $seen_columns)) {
                $error .= "Required column missing: " . $c["label"] . "<br/>";
            }
        }

        $this->updateColumnData($productupload_id, $columnData);
        $this->setStatus($productupload_id, "Columns Assigned");


        // Create files...
        if ($error == "") {
            $this->regenerateMappingFiles($productupload_id);
        }

        return $error;
    }

    public function regenerateMappingFiles($productupload_id) {
        $productupload = $this->get($productupload_id);
        // First, we have to clear out any of these if they exist
        foreach (array("new_file", "reject_file", "update_file") as $f) {
            $file = $productupload[$f];
            if ($file != "") {
                $full_f = $this->upload_directory . "/" . $file;

                if (file_exists($full_f) && is_file($full_f)) {
                    unlink($full_f);
                }
            }
        }

        $new_file = tempnam($this->upload_directory, "new_upload_file_");
        $reject_file = tempnam($this->upload_directory, "reject_file_file_");
        $update_file = tempnam($this->upload_directory, "update_file_file_");

        // put the header in those files;;;
        $header = $this->getHeader($productupload_id);
        $handle = fopen($new_file, "w");
        fputcsv($handle, $header);
        fclose($handle);
        $handle = fopen($update_file, "w");
        fputcsv($handle, $header);
        fclose($handle);
        array_unshift($header, "Reason for Rejection");
        $handle = fopen($reject_file, "w");
        fputcsv($handle, $header);
        fclose($handle);

        $this->db->query("Update productupload set new_file = ?, update_file = ?, reject_file = ? where productupload_id = ? limit 1", array(basename($new_file), basename($update_file), basename($reject_file), $productupload_id));
        $this->clearCache($productupload_id);

    }

    public function clearMappingCount($productupload_id, $count = 0) {
        $this->db->query("Update productupload set mapped_count = ? where productupload_id = ?", array($count, $productupload_id));
        $this->clearCache($productupload_id);
    }

    public function getUnmappedRowCount($productupload_id) {
        $p = $this->get($productupload_id);
        return $p["upload_row_count"] - $p["mapped_count"];
    }

    public function getRowCount($productupload_id) {
        $p = $this->get($productupload_id);
        return $p["upload_row_count"];
    }

    public function getMappedRowCount($productupload_id) {
        $p = $this->get($productupload_id);
        return $p["mapped_count"];
    }

    protected function getDealerDistributor() {
        $this->distributor_cache = array();
        $query = $this->db->query("Select distributor_id, name from distributor");
        foreach ($query->result_array() as $row) {
            $clean_name = $this->clean_name($row["name"]);
            $this->distributor_cache[$clean_name] = $row["distributor_id"];
        }
    }

    protected function findDealerPartNumber($distributor, $part_number) {
        $distributor_id = $this->queryPart($distributor);
        if ($distributor_id > 0) {
            return $this->queryPart($distributor_id, $part_number);
        } else {
            return 0;
        }
    }

    protected function clean_name($string) {
        return preg_replace("/[^a-z0-9]/", "", strtolower(trim($string)));
    }

    protected function queryDistributor($distributor_name) {
        if (count($this->distributor_cache) == 0) {
            $this->getDealerDistributor();
        }
        $clean_name = $this->clean_name($distributor_name);
        return array_key_exists($clean_name, $this->distributor_cache) ? $this->distributor_cache[$clean_name] : 0;
    }

    protected function queryPart($distributor_id, $part_number) {
        $query = $this->db->query("Select partvariation_id from partvariation where distributor_id = ? and (part_number = ? or clean_part_number = ?)", array($distributor_id, $part_number, $part_number));
        foreach ($query->result_array() as $row) {
            return $row["partvariation_id"];
        }
        return 0;
    }

    protected function getInvertedColumns($columndata) {
        $inverted_columns = array();
        for ($i = 0; $i < count($columndata["columns"]); $i++) {
            $col = $columndata["columns"][$i];
            if ($col != "") {
                if (array_key_exists($col, $inverted_columns)) {
                    if (!is_array($inverted_columns[$col])) {
                        $inverted_columns[$col] = array($inverted_columns[$col]);
                    }
                    $inverted_columns[$col][] = $i;
                } else {
                    $inverted_columns[$col] = $i;
                }
            }
        }
        return $inverted_columns;
    }

    /*
     * We have to check the following:
     * - We have to recognize the distributor
     * - We can look for that part number to figure out if we know it or not
     * - If we are provided a machine, make, model, we need to have a year (check the whole chain...)
     * - Enforce the required fields...
     * - We can create a new manufacturer
     */
    public function applyMapping($productupload_id, $limit = 200) {
        // OK, you have to open the file handles for append...
        $p = $this->get($productupload_id);
        $columndata = unserialize($p["columndata"]);
        $new_handle = fopen($this->upload_directory . "/" . $p["new_file"], "a");
        $update_handle = fopen($this->upload_directory . "/" . $p["update_file"], "a");
        $reject_handle = fopen($this->upload_directory . "/" . $p["reject_file"], "a");

        // OK, now we have to load that main file and figure out where our spot is...
        $mapped_count = $p["mapped_count"];
        $newly_mapped = 0;
        $row_count = 0;
        $handle = fopen($this->upload_directory . "/" . $p["upload_file"], "r");
        fgetcsv($handle);

        while ($row_count < $mapped_count) {
            fgetcsv($handle);
            $row_count++;
        }

        // get the flags
        $inverted_columns = $this->getInvertedColumns($columndata);

        // now, we have to map them...
        $new_count = $update_count = $reject_count = 0;
        while (FALSE !== ($row = fgetcsv($handle)) && ($newly_mapped < $limit)) {
            $new = false;
            $update = false;
            $reject = false;
            $reject_reason = "Uninitialized.";

            // OK, we have to try all these out...
            $distributor_value = trim(array_key_exists("distributor", $inverted_columns) ? $row[$inverted_columns["distributor"]] : "");
            // part number part_number
            $part_number_value = trim(array_key_exists("part_number", $inverted_columns) ? $row[$inverted_columns["part_number"]] : "");
            // part name part
            $part_value = trim(array_key_exists("part", $inverted_columns) ? $row[$inverted_columns["part"]] : "");
            // manufacturer manufacturer
            $manufacturer_value = trim(array_key_exists("manufacturer", $inverted_columns) ? $row[$inverted_columns["manufacturer"]] : "");
            // category category
            $category_value = trim(array_key_exists("category", $inverted_columns) ? $row[$inverted_columns["category"]] : "");

            if ($distributor_value == "") {
                $reject = true;
                $reject_reason = "Distributor value is required.";
            } else if ($part_number_value == "") {
                $reject = true;
                $reject_reason = "Distributor part number value is required.";
            } else if ($part_value == "") {
                $reject = true;
                $reject_reason = "Part name value is required.";
            } else if ($manufacturer_value == "") {
                $reject = true;
                $reject_reason = "Manufacturer value is required.";
            } else if ($category_value == "") {
                $reject = true;
                $reject_reason = "Category value is required.";
            }

            if (!$reject) {
                // OK, if we are here, then we are looking at a possible value...
                $distributor_id = $this->queryDistributor($distributor_value);
                if ($distributor_id > 0) {
                    // QUESTION - Should we be restricting machine typ?
                    // OK, if fitment is provided...is it complete?
                    $machine_type_value = trim(array_key_exists("machine_type", $inverted_columns) ? $row[$inverted_columns["machine_type"]] : "");
                    $make_value = trim(array_key_exists("make", $inverted_columns) ? $row[$inverted_columns["make"]] : "");
                    $model_value = trim(array_key_exists("model", $inverted_columns) ? $row[$inverted_columns["model"]] : "");
                    $year_value = trim(array_key_exists("year", $inverted_columns) ? $row[$inverted_columns["year"]] : "");

                    if (($machine_type_value != "" || $make_value != "" || $model_value != "" || $year_value != "") && ($machine_type_value == "" || $make_value == "" || $model_value == "" || $year_value == "")) {
                        $reject = true;
                        $reject_reason = "Please specify all four values for fitment - machine type, make, model, and year.";
                    }

                    if (!$reject) {
                        // If we are still here, we are going to add this thing, we just need to know if we need to add or update it...
                        $part_id = $this->queryPart($distributor_id, $part_number_value);

                        $update = $part_id > 0;
                        $new = !$update;
                    }

                } else {
                    $reject = true;
                    $reject_reason = "Sorry, that distributor is unknown. Please first define new distributors under Content > Distributors.";
                }

            }


            // We have to check on these images and see if they exist, if it's defined...
            if (!$reject && array_key_exists("image", $inverted_columns)) {
                if (!is_array($inverted_columns["image"])) {
                    $inverted_columns["image"] = array($inverted_columns["image"]);
                }

                // Now, verify that we can get this...
                for ($im = 0; $im < count($inverted_columns["image"]) && !$reject; $im++) {
                    $idx = $inverted_columns["image"][$im];
                    if (count($row) > $idx && $row[$idx] != "") {
                        // If it's a real image, we have to check it
                        if (!$this->_isValidURL($row[$idx])) {
                            $reject = true;
                            $reject_reason = "Image " . ($im + 1) . " does not have a valid, reachable URL.";
                        }
                    }
                }

            }

            if (!$reject && $new) {
                $new_count++;
                fputcsv($new_handle, $row);
            } else if (!$reject && $update) {
                $update_count++;
                fputcsv($update_handle, $row);
            } else {
                $reject_count++;
                array_unshift($row, $reject_reason);
                fputcsv($reject_handle, $row);
            }

            $newly_mapped++;
        }

        // update the count...
        $this->db->query("Update productupload set mapped_count = ?, new_row_count = new_row_count + ?, update_row_count = update_row_count + ?, reject_row_count = reject_row_count + ? where productupload_id = ?", array($mapped_count + $newly_mapped, $new_count, $update_count, $reject_count, $productupload_id));
        $this->clearCache($productupload_id);

        if ($mapped_count + $newly_mapped >= $this->getRowCount($productupload_id)) {
            $this->setStatus($productupload_id, "Mapped");
        }
    }

    protected function _isValidURL($url) {
        // This checks that it's a valid URL and we can actually fetch it...
        //https://stackoverflow.com/questions/11797680/curl-getting-http-code#12629254
        if(!$url || !is_string($url) || ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)){
            return false;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode == 200; // If it's not 200, we can't do anything with it.
    }

    public function download($productupload_id, $mode = "all") {
        // Just have to find the correct file and dump it... If you are here, then you are requesting it all...
        $p = $this->get($productupload_id);
        switch (strtolower($mode)) {
            case "new":
                $basename = $p["new_file"];
                break;
            case "update":
                $basename = $p["update_file"];
                break;

            case "reject":
                $basename = $p["reject_file"];
                break;
            default:
                $basename = $p["upload_file"];
        }

        $fullname = $this->upload_directory . '/' . $basename;

        // OK, shove it down as an attachment...
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $basename . '.csv"');
        print file_get_contents($fullname);
    }

    public function confirm($productupload_id) {

        $this->db->query("Update productupload set status = 'Processed', processed_row_count = 0 where productupload_id = ?", array($productupload_id));
        $this->clearCache($productupload_id);
    }

    public function getStatusCounts($productupload_id, $status = "New") {
        $p = $this->get($productupload_id);
        switch (strtolower($status)) {
            case "new":
                return $p["new_row_count"];
                break;
            case "update":
                return $p["update_row_count"];
                break;

            case "reject":
                return $p["reject_row_count"];
                break;
            default:
                return 0;
        }
    }

    /*
     * This will turn this row, which is number-ordered array, into an associative array
     */
    protected function explodeToAssoc($inverted_column, $row) {
        $result = array();
        foreach ($inverted_column as $k => $n) {
            if (!is_array($n)) {
                $n = array($n);
            }
            foreach ($n as $m) {
                if ($m < count($row)) {
                    if ($row[$m] == "" || is_null($row[$m])) {
                        continue;
                    }

                    if (array_key_exists($k, $result)) {
                        if (!is_array($result[$k])) {
                            $result[$k] = array($result[$k]);
                        }
                        $result[$k][] = $row[$m];
                    } else {
                        $result[$k] = $row[$m];
                    }
                }
            }
        }
        file_put_contents(tempnam("/tmp", "explodeToAssoc_"), print_r($inverted_column, true) . " " . print_r($row, true)  . " " . print_r($result, true));

        return $result;
    }

    // We're doing an update, we can be a whole lot more lenient about this part
    protected function applyUpdate($assoc_row) {
        // OK, we know what this is...
        $distributor_id = $this->queryDistributor($assoc_row["distributor"]);
        $partvariation_id = $this->queryPart($distributor_id, $assoc_row["part_number"]);
        $this->sub_apply($assoc_row, $distributor_id, $partvariation_id);
    }

    // Now, for this one, we must create enough to keep that thing in place. This should mirror the code when you create a new part in the admin one-at-a-time.
    protected function applyNew($assoc_row) {
        $distributor_id = $this->queryDistributor($assoc_row["distributor"]);
        $this->sub_apply($assoc_row, $distributor_id);


    }

    // Reference: Controllers/Adminproduct::product_add_save
    protected function sub_apply($row, $distributor_id, $partvariation_id = 0) {

        file_put_contents(tempnam("/tmp", "dump_of_row_"), print_r($row, true));

        // you should plow through all of it - name, manufacturer, description, categories... that's what we put into product_add_save...
        $part_name = trim($row["part"]);
        $manufacturer = trim($row["manufacturer"]);
        if ($manufacturer == "") {
            $manufacturer = trim($row["new_manufacturer"]);
        }
        $description = array_key_exists("description", $row) ? $row["description"] : "";
        $categories = $row["category"];

        $CI =& get_instance();
        $CI->load->model("Portalmodel");
        $part_id = $CI->Portalmodel->makePartByName($part_name, $description);

        // Make the manufacturer
        $manufacturer_id = $CI->Portalmodel->getOrMakeManufacturer($manufacturer);

        // Assign the manufacturer
        $CI->Portalmodel->assignPartManufacturer($part_id, $manufacturer_id);

        // OK, time for the categories...
        $categories = preg_split("/\s*;\s*/", $categories);
        foreach ($categories as $c) {
            $c = trim($c);
            $c = $CI->Portalmodel->getOrCreateCategory($c);
            $CI->Portalmodel->addPartCategory($part_id, $c);
        }

        // TODO:
        // Should we delete categories?

        // OK, now, the part should be there, one way, or another.
        // What you have to do now is go down to part variation...
        if ($partvariation_id == 0) {
            // you better create it...
            $partvariation_id = $CI->Portalmodel->newCreatePartVariation($row["part_number"], array_key_exists("quantity", $row) ? $row["quantity"] : 0, (array_key_exists("closeout", $row) && ($row["closeout"] == 1 || trim(strtolower($row["closeout"])) == "yes" || trim(strtolower($row["closeout"])) == "y")) ? "Closeout":"Normal", array_key_exists("cost", $row) ? $row["cost"] : 0, array_key_exists("price", $row) ? $row["price"] : 0, $distributor_id);

            // And, I guess, make this thing a part number?
            $partnumber_id = $CI->Portalmodel->createPartNumber($row["part_number"], array_key_exists("cost", $row) ? $row["cost"] : 0, array_key_exists("price", $row) ? $row["price"] : 0, $distributor_id);

            // And link them..
            $CI->Portalmodel->setPartVariationPartNumber($partvariation_id, $partnumber_id);
        } else {
            // Get the partnumber associated with this partvariation_id...
            $partnumber_id = 0;
            $query = $this->db->query("Select partnumber_id from partvariation where partvariation_id = ?", array($partvariation_id));
            foreach ($query->result_array() as $datarow) {
                $partnumber_id = $datarow["partnumber_id"];
            }

            // Best make sure there's an entry in dealer part variation...
            $present = false;
            $query = $this->db->query("Select * from partdealervariation where partvariation_id = ?", array($partvariation_id));
            foreach ($query->result_array() as $this_row) {
                $present = true;
            }

            if (!$present) {
                $this->db->query("Insert into partdealervariation (partvariation_id, part_number, partnumber_id, distributor_id, quantity_available, quantity_ten_plus, quantity_last_updated, cost, price, clean_part_number, revisionset_id, manufacturer_part_number, weight, stock_code) select partvariation_id, part_number, partnumber_id, distributor_id, 0, 0, now(), cost, price, clean_part_number, revisionset_id, manufacturer_part_number, weight, stock_code from partvariation where partvariation_id = ? on duplicate key update quantity_available = values(quantity_available), quantity_ten_plus = values(quantity_ten_plus), quantity_last_updated = now(), cost = values(cost), price = values(price), weight = values(weight), stock_code = values(stock_code)", array($partvariation_id));
            }

            // TODO
            // Should we be updating categories? Description?

        }

        $partnumber_rollup = array();

        // OK, if it's not there, you'll have to insert into partpartnumber...
        $CI->Portalmodel->insertPartPartNumber($part_id, $partnumber_id);

        $update_query = "Update partdealervariation set ";
        $values = array();

        // If quantity, price, etc is provided, we need to consider adding this to the dealer part variation and setting it accordingly...
        if (array_key_exists("quantity", $row) && $row["quantity"] != "") {
            $update_query .= " quantity_available = ? ";
            $values[] = $row["quantity"];
        }
        if (array_key_exists("price", $row) && $row["price"] != "") {
            if (count($values) > 0) {
                $update_query .= " , ";
            }
            $update_query .= " price = ? ";
            $values[] = $row["price"];
            $partnumber_rollup[] = array("key" => "price", "value" => $row["price"]);
            $partnumber_rollup[] = array("key" => "dealer_sale", "value" => $row["price"]);
        }
        // JLB 12-27-17
        // Add in the shipping weight here. It is going only on partdealervariation.
        if (array_key_exists("weight", $row) && $row["weight"] != "") {
            // It looks like we are overriding the part variation entry, too, so I guess we set this...
            $this->db->query("Update partvariation set weight = ? where partvariation_id = ? limit 1", array(floatVal($row["weight"]), $partvariation_id));

            if (count($values) > 0) {
                $update_query .= " , ";
            }
            $update_query .= " weight = ? ";
            $values[] = $v = floatVal($row["weight"]);
            $partnumber_rollup[] = array("key" => "weight", "value" => $v);
        }
        if (array_key_exists("cost", $row) && $row["cost"] != "") {
            if (count($values) > 0) {
                $update_query .= " , ";
            }
            $update_query .= " cost = ? ";
            $values[] = $row["cost"];
            $partnumber_rollup[] = array("key" => "cost", "value" => $row["cost"]);
        }
        if (array_key_exists("closeout", $row) && $row["closeout"] != "") {
            if (count($values) > 0) {
                $update_query .= " , ";
            }
            $update_query .= " stock_code = ? ";
            $values[] = (1 == $row["closeout"] || "y" == strtolower(trim($row["closeout"])) || "yes" == strtolower(trim($row["closeout"]))) ? "Closeout" : "Normal";
        }

        if (count($values) > 0) {
            $values[] = $partvariation_id;
            $this->db->query($update_query . " where partvariation_id = ? limit 1", $values);
        }

        if (count($partnumber_rollup) > 0) {
            // OK, we have to do an update of partnumber, too...
            $values_string = "";
            $values_array = array();
            foreach ($partnumber_rollup as $p) {
                if ($values_string != "") {
                    $values_string .= " , ";
                }
                $values_string .= $p["key"] . " = If(IsNull(" . $p["key"] . ") OR " . $p["key"] . " = 0, ?, " . $p["key"] . ")";
                $values_array[] = $p["value"];
            }

            // Now, we run another update
            $values_array[] = $partnumber_id;
            $this->db->query("Update partnumber set $values_string where partnumber_id = ? limit 1", $values_array);
        }

        if (array_key_exists("question", $row) && $row["question"] != "") {
            // OK, is there a question for this that is not a product question?
            $partquestion_id = 0;

            $query = $this->db->query("Select partquestion_id from partquestion where part_id = ? and question = ? ", array($part_id, $row["question"]));
            foreach ($query->result_array() as $datarow) {
                $partquestion_id = $datarow["partquestion_id"];

            }

            if ($partquestion_id == 0) {
                $this->db->query("Insert into partquestion (part_id, question) values (?, ?)", array($part_id, $row["question"]));
                $partquestion_id = $this->db->insert_id();
            }

            // Now, the answer...
            $this->db->query("Insert into partnumberpartquestion (partquestion_id, partnumber_id, answer, mx) values (?, ?, ?, 0) on duplicate key update answer = values(answer), partnumberpartquestion_id = last_insert_id(partnumberpartquestion_id)", array($partquestion_id, $partnumber_id, $row["answer"]));

            $this->db->query("Insert into partquestionanswer (partquestion_id, answer) values (?, ?) on duplicate key update partquestionanswer_id = last_insert_id(partquestionanswer_id)", array($partquestion_id, $row["answer"]));
        }

        if (array_key_exists("product_question", $row)) {
            if (!is_array($row["product_question"])) {
                if ($row["product_question"] != "") {
                    $row["product_question"] = array($row["product_question"]);
                }
            }
        }

        if (array_key_exists("product_answer", $row)) {
            if (!is_array($row["product_answer"])) {
                if ($row["product_answer"] != "") {
                    $row["product_answer"] = array($row["product_answer"]);
                }
            }
        }

        if (array_key_exists("product_question", $row) && is_array($row["product_question"]) && count($row["product_question"]) > 0) {
            // OK, we need to put these in... we probably have to look for better stuff...
            $question_map = array();
            $seen_questions = array();
            $query = $this->db->query("Select question, partquestion_id from partquestion where part_id = ? and productquestion > 0", array($part_id));
            foreach ($query->result_array() as $datarow) {
                $question_map[strtoupper(trim($datarow["question"]))] = $partquestion_id;
            }

            for ($i = 0; $i < count($row["product_question"]); $i++) {
                $q = $row["product_question"][$i];
                if (array_key_exists(strtoupper(trim($q)), $question_map)) {
                    $partquestion_id = $question_map[strtoupper(trim($q))];
                } else {
                    // we have to insert it...
                    $this->db->query("Insert into partquestion (question, part_id, productquestion) values (?, ?, 1)", array($q, $part_id));
                    $partquestion_id = $this->db->insert_id();
                }
                $seen_questions[] = $partquestion_id;

                // set the answer...
                $this->db->query("Insert into partnumberpartquestion (partnumber_id, partquestion_id, answer) values (?, ?, ?) on duplicate key update answer = values(answer)", array($partnumber_id, $partquestion_id, $row["product_answer"][$i]));

                $this->db->query("Insert into partquestionanswer (partquestion_id, answer) values (?, ?) on duplicate key update partquestionanswer_id = last_insert_id(partquestionanswer_id)", array($partquestion_id, $row["product_answer"][$i]));

            }
            
            // for each you haven't seen, you have to delete it...
            foreach ($question_map as $q => $id) {
                if (!in_array($id, $seen_questions)) {
                    $this->db->query("Delete from partquestion where partquestion_id = ? and part_id = ? limit 1", array($id, $part_id));
                }
            }
        }

        // And we should look at the product questions, if required...

        // And I guess we should consider fitment if it exists, right?
        if (array_key_exists("machine_type", $row) && $row["machine_type"] != "") {
            // does this machine type exist?
            $machinetype_id = 0;
            $this->db->query("Insert into machinetype (name, label, revisionset_id) values (?, ?, 1) on duplicate key update machinetype_id = last_insert_id(machinetype_id)", array($row["machine_type"], $row["machine_type"]));
            $machinetype_id = $this->db->insert_id();

            // does the make exist?
            $make_id = 0;
            $this->db->query("Insert into make (name, label, machinetype_id) values (?, ?, ?) on duplicate key update make_id = last_insert_id(make_id)", array($row["make"], $row["make"], $machinetype_id));
            $make_id = $this->db->insert_id();

            // Does the model exist?
            $model_id = 0;
            $this->db->query("Insert into model (label, make_id, name) values (?, ?, ?) on duplicate key update model_id = last_insert_id(model_id)", array($row["model"], $make_id, $row["model"]));
            $model_id = $this->db->insert_id();

            // break up the years
            $start_year = $end_year = 0;

            $matches = array();
            $year = trim($row["year"]);
            $cy = intVal(date("Y"));
            $my = $cy % 100;
            if (preg_match("/^[0-9]{4}$/", $year)) {
                $start_year = $end_year = intVal($year);
            } else if (preg_match("/^[0-9]{2}$/", $year)) {
                $start_year = intVal($year);
                if ($start_year > 2 + $my) {
                    $start_year = $start_year + 100 * floor($cy / 100.0);
                } else {
                    $start_year = $start_year + 100 * (floor($cy / 100.0) - 1);
                }
                $end_year = $start_year;
            } else if (preg_match("/^\s*([0-9]{4})\s*\-\s*([0-9]{4})\s*$/", $year, $matches)) {
                $start_year = intVal($matches[1]);
                $end_year = intVal($matches[2]);
            } else if (preg_match("/^\s*([0-9]{2})\s*\-\s*([0-9]{2})\s*$/", $year, $matches)) {
                $start_year = intVal($matches[1]);
                if ($start_year > 2 + $my) {
                    $start_year = $start_year + 100 * floor($cy / 100.0);
                } else {
                    $start_year = $start_year + 100 * (floor($cy / 100.0) - 1);
                }
                $end_year = intVal($matches[2]);
                if ($end_year > 2 + $my) {
                    $end_year = $end_year + 100 * floor($cy / 100.0);
                } else {
                    $end_year = $end_year + 100 * (floor($cy / 100.0) - 1);
                }
            }

            if ($start_year > 0) {
                for ($i = $start_year; $i <= $end_year; $i++) {
                    $this->db->query("Insert into partnumbermodel (partnumber_id, model_id, year) values (?, ?, ?) on duplicate key update partnumbermodel_id = values(partnumbermodel_id)", array($partnumber_id, $model_id, $i));
                }
            }
        }

        // JLB 12-28-17
        // What about images?
        if (array_key_exists("image", $row)) {
            if (!is_array($row["image"])) {
                $row["image"] = array($row["image"]);
            }

            // Now, verify that we can get this...
            for ($im = 0; $im < count($row["image"]); $im++) {
                $url = $row["image"][$im];
                //error_log("Image: "  . $url);
                // we have to get a filename that doesn't exist...
                $basename = basename($url);
                $candidate_filename = tempnam(STORE_DIRECTORY . "/html/storeimages/", "") . ".png";
                //error_log("Candidate filename: " . $candidate_filename);

                // now, stick it somewhere
                $this->downloadFileToUrl($url,  $candidate_filename);

                // OK, so we need to look and update, or not.
                $query = $this->db->query("Select * from partimage where part_id = ? and mx = 0 and external_url = ?", array($part_id, $url));

                $partimage_id = 0;
                foreach ($query->result_array() as $rec) {
                    $partimage_id = $rec['partimage_id'];
                }

                if ($partimage_id > 0) {
                    // update it.
                    $this->db->query("update partimage set path = ? where partimage_id = ? limit 1", array("store/" . basename($candidate_filename), $partimage_id));
                } else {
                    // otherwise, insert it
                    $this->db->query("Insert into partimage (part_id, original_filename, path, mx, external_url) values (?, ?, ?, 0, ?)", array($part_id, $basename, "store/" . basename($candidate_filename), $url));
                }
            }

        }


        // We need to reprocess this part.
        $this->db->query("Insert into queued_parts (part_id) values (?)", array($part_id));
    }

    // https://stackoverflow.com/questions/6476212/save-image-from-url-with-curl-php#6476232
    protected function downloadFileToUrl($url, $filename) {
        //error_log("downloadFileToUrl($url, $filename)");
        $temp_file = tempnam("/tmp", "img");
        $fp = fopen ($temp_file, 'w+');              // open file handle

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);          // output to file
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);      // some large value to allow curl to run for a long time
        curl_exec($ch);

        curl_close($ch);                              // closing curl handle
        fclose($fp);

        // we need to resize it
        $CI =& get_instance();

        require_once(__DIR__ . "/../../simpleimage.php");

        $image = new SimpleImage();
        $image->load($temp_file);
        $image->save($filename, IMAGETYPE_PNG);
        $image->setMaxDimension(144);
        $image->save(dirname($filename) . "/t" . basename($filename), IMAGETYPE_PNG);
    }

    public function process($productupload_id, $limit = 100) {
        $debug = false;

        // First, we have to process these guys in order...
        $p = $this->get($productupload_id);
        $columndata = unserialize($p["columndata"]);

        if ($debug) {
            print_r($p);
        }

        // process all the rejects...
        if ($p["processed_row_count"] == 0) {
            $p["processed_row_count"] = $p["reject_row_count"];
        }

        if ($debug) {
            print "Processed row count: " . $p["processed_row_count"] . "\n";
        }

        $inverted_columns = $this->getInvertedColumns($columndata);

        if ($debug) {
            print "Inverted columns: \n";
            print_r($inverted_columns);
        }


        // Now, we are going to do updates, then we are going to do news...
        if (($p["processed_row_count"] < $p["reject_row_count"] + $p["update_row_count"]) && $limit > 0) {
            if ($debug) {
                print "Within the update leg \n";
            }
            // well, we have to process the update
            $upload_fh = fopen($this->upload_directory . "/" . $p["update_file"], "r");
            // discard the header
            fgetcsv($upload_fh);

            // We have to chew through to our position
            $starting_position = $p["reject_row_count"];
            while ($starting_position < $p["processed_row_count"]) {
                if ($debug) {
                    print "Chewing up $starting_position to get to " . $p["processed_row_count"] . "\n";
                }
                // you just have to chew them up...
                fgetcsv($upload_fh);
                $starting_position++;
            }

            while ($limit > 0) {
                $row = fgetcsv($upload_fh);
                if (FALSE !== $row) {
                    if ($debug) {
                        print "Fetching another row from the file\n";
                    }
                    // OK, we have a live one...
                    $assoc_row = $this->explodeToAssoc($inverted_columns, $row);
                    if ($debug) {
                        print "Row: ";
                        print_r($assoc_row);
                    }
                    $this->applyUpdate($assoc_row);
                    $limit--;
                    $p["processed_row_count"]++;
                } else {
                    break; // escape the while loop since the file is exhausted...
                }
            }

            fclose($upload_fh);
        }


        if ($p["processed_row_count"] < $p["upload_row_count"] && $limit > 0) {
            if ($debug) {
                print "In the new leg \n";
            }
            // we have to process the new stuff
            $upload_fh = fopen($this->upload_directory . "/" . $p["new_file"], "r");
            // discard the header
            fgetcsv($upload_fh);

            // We have to chew through to our position
            $starting_position = $p["reject_row_count"] + $p["update_row_count"];
            while ($starting_position < $p["processed_row_count"]) {
                if ($debug) {
                    print "Chewing up $starting_position \n";
                }
                // you just have to chew them up...
                fgetcsv($upload_fh);
                $starting_position++;
            }

            while ($limit > 0) {
                $row = fgetcsv($upload_fh);
                if (FALSE !== $row) {
                    // OK, we have a live one...
                    if ($debug) {
                        print "Fetching another row from the file \n";
                    }
                    $assoc_row = $this->explodeToAssoc($inverted_columns, $row);
                    if ($debug) {
                        print "Found row: ";
                        print_r($assoc_row);
                    }
                    $this->applyUpdate($assoc_row);
                    $p["processed_row_count"]++;
                    $limit--;
                } else {
                    break; // escape the while loop since the file is exhausted...
                }
            }

            fclose($upload_fh);
        }

        // update it...
        $this->db->query("Update productupload set processed_row_count = ? where productupload_id = ?", array($p["processed_row_count"], $productupload_id));
    }

    public function getUnprocessedRowCount($productupload_id) {
        $p = $this->get($productupload_id);
        return $p["upload_row_count"] - $p["processed_row_count"];
    }
}