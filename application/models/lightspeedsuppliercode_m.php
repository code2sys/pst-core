<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 1/23/18
 * Time: 3:48 PM
 */

class LightSpeedSupplierCode_M extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->query_cache = array();
    }

    public function getAll() {
        $query = $this->db->query("Select lightspeed_suppliercode.*, distributor.name as distributor_name, brand.name as brand_name from lightspeed_suppliercode left join distributor using (distributor_id) left join brand using (brand_id) order by lightspeed_suppliercode.supplier_code");
        return $query->result_array();
    }

    protected $query_cache;
    public function query($supplier_code) {
        if (array_key_exists($supplier_code, $this->query_cache)) {
            return $this->query_cache[$supplier_code];
        }

        // OK, look it up...
        $row = null;
        $query = $this->db->query("Select lightspeed_suppliercode.*, distributor.name as distributor_name, brand.name as brand_name from lightspeed_suppliercode left join distributor using (distributor_id) left join brand using (brand_id) where supplier_code = ? ", array($supplier_code));
        foreach ($query->result_array() as $rec) {
            $row = $rec;
        }

        $this->query_cache[$supplier_code] = $row;
        return $this->query_cache[$supplier_code];
    }

    // This should be pretty straightforward -
    public function setCodes($code_map_set) {
        foreach ($code_map_set as $rec) {
            $this->db->query("Update lightspeed_suppliercode set type = ?, distributor_id = ?, brand_id = ? where supplier_code = ? limit 1", array($rec["type"], $rec["distributor_id"], $rec["brand_id"], $rec["supplier_code"]));
        }
    }

    public function registerMissingSuppliers() {
        $this->db->query(" insert into lightspeed_suppliercode (supplier_code) select distinct supplier_code from lightspeedpart on duplicate key update lightspeed_suppliercode_id = last_insert_id(lightspeed_suppliercode_id);");
    }

    public function getDistributorSupplierCodes() {
        $query = $this->db->query("Select supplier_code from lightspeed_suppliercode where type = 'Distributor'");
        return array_map(function($x) {
            return $x["supplier_code"];
        }, $query->result_array());
    }

    public function getBrandSupplierCodes() {
        $query = $this->db->query("Select supplier_code from lightspeed_suppliercode where type = 'Brand'");
        return array_map(function($x) {
            return $x["supplier_code"];
        }, $query->result_array());
    }

    public function getBrands() {
        return $this->db->query("Select brand_id, name from brand order by name")->result_array();
    }

    public function getDistributors() {
        return $this->db->query("Select distributor_id, name from distributor where name != 'Lightspeed Feed' order by name")->result_array();
    }

}