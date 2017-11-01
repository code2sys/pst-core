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
                "label" => "Machine Type",
                "alternates" => array("machinetype"),
                "description" => "Store major machine type category, or leave blank for a universal part. Known values are: " . $this->implodeList("machinetype", "name"),
                "required" => false
            ),
            array(
                "name" => "make",
                "label" => "Vehicle Manufacturer",
                "alternates" => array(),
                "description" => "The vehicle manufacturer, or leave blank for a universal part. Known values are: " . $this->implodeList("make", "name"),
                "required" => false
            ),
            array(
                "name" => "model",
                "label" => "Vehicle Model",
                "alternates" => array(),
                "description" => "The corresponding vehicle model; must specify the make, model, year, and machine type.",
                "required" => false
            ),
            array(
                "name" => "year",
                "label" => "Vehicle Year",
                "alternates" => array(),
                "description" => "Speciy vehicle year; can be specified as YY, YYYY, or YYYY-YYYY for a range.",
                "required" => false
            ),
            array(
                "name" => "part",
                "label" => "Part Name",
                "alternates" => array(),
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
                "label" => "Additional Product Question",
                "alternates" => array("Product Question"),
                "description" => "Additional product question(s). Enter one column per question; must have corresponding answers in the same order left-to-right.",
                "required" => false,
                "multiple" => true
            ),
            array(
                "name" => "product_answer",
                "label" => "Additional Product Answer",
                "alternates" => array("Product Answer"),
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
            )

        );

    }

    protected $productupload_cache;

    public function __construct() {
        parent::__construct();
        $this->upload_directory = STORE_DIRECTORY . "/uploads";
        $this->productupload_cache = array();
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

    }

    protected function findDealerPartNumber($distributor, $part_number) {

    }

    public function applyMapping($productupload_id, $limit = 200) {
        // OK, you have to open the file handles for append...
        $p = $this->get($productupload_id);
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

        $header = $this->getHeader($productupload_id);

        // now, we have to map them...
        $new_count = $update_count = $reject_count = 0;
        while (FALSE !== ($row = fgetcsv($handle)) && ($newly_mapped < $limit)) {
            $new = false;
            $update = false;
            $reject = false;
            $reject_reason = "Uninitialized.";

            if ($new) {
                $new_count++;
                fputcsv($new_handle, $row);
            } else if ($update) {
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

    public function download($productupload_id, $mode = "all") {
        // Just have to find the correct file and dump it... If you are here, then you are requesting it all...
        $p = $this->get($productupload_id);


    }

    public function confirm($productupload_id) {

        $this->db->query("Update productupload set status = 'Processed', processed_row_count = 0, new_row_count = 0, reject_row_count = 0, update_row_count = 0 where productupload_id = ?", array($productupload_id));
        $this->clearCache($productupload_id);
    }

    public function getStatusCounts($productupload_id, $status = "New") {
        $p = $this->get($productupload_id);
        switch (strtolower($status)) {
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

        $fullname = $this->upload_directory . $basename;

        // OK, shove it down as an attachment...
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $basename . '.csv"');
        print $fullname . "\n";
        print file_get_contents($fullname);
    }

    public function process($productupload_id, $limit = 100) {

    }

    public function getUnprocessedRowCount($productupload_id) {
        $p = $this->get($productupload_id);
        return $p["upload_row_count"] - $p["processed_row_count"];
    }
}