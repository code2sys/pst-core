<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 11/21/17
 * Time: 3:45 PM
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once("master_m.php");
class Color_M extends Master_M
{

    public function getColorByLabel($label, $lookup_mapping_table = false, $available_codes = NULL) {
        $this->db->select("code, label");
        $this->db->from('motorcycle_color');
        $this->db->where('label', $label);
        if (!empty($available_codes)) {
            $this->db->where_in('code', $available_codes);
        }
        $query = $this->db->get();
        $query->result_array();
        foreach ($query->result_array() as $row) {
            return array(
                "code" => $row['code'],
                "label" => $row['label']
            );
        }

        if ($lookup_mapping_table) {
            $this->db->select("code, color");
            $this->db->from('motorcycle_color_mapping');
            $this->db->where('color', $label);
            if (!empty($available_codes)) {
                $this->db->where_in('code', $available_codes);
            }
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                return $this->getColorByCode($row['code']);
            }
        }
        return FALSE;
    }

    public function getColorByCode($color_code) {
        $query = $this->db->query("select code, label from motorcycle_color where code = ? ", array($color_code));
        foreach ($query->result_array() as $row) {
            return array(
                "code" => $row['code'],
                "label" => $row['label']
            );
        }

        return FALSE;
    }

    public function getColorsByCodes($color_codes) {
        $this->db->select("code, label");
        $this->db->from('motorcycle_color');
        $this->db->where_in('code', $color_codes);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function addColorMapping($color_code, $color) {

        // If this is new color code, we need to register
        $query = $this->db->query("select count(*) as cnt from motorcycle_color where  code = ?", array($color_code));
        $count = 0;
        foreach ($query->result_array() as $row) {
            $count = $row['cnt'];
        }
        if ($count == 0 && !empty($color)) {
            $this->db->query("Insert into motorcycle_color (code, label) values (?, ?)", array($color_code, $color));
            return $this->db->insert_id();
        }

        // If it's new pair, we need to learn
        $query = $this->db->query("select count(*) as cnt from motorcycle_color_mapping where  code = ? and color = ?", array($color_code, $color));
        $count = 0;
        foreach ($query->result_array() as $row) {
            $count = $row['cnt'];
        }
        if ($count == 0) {
            $this->db->query("Insert into motorcycle_color_mapping (code, color) values (?, ?)", array($color_code, $color));
            return $this->db->insert_id();
        }
        return FALSE;
    }

    public function getAllColors() {
        $where = array();
        $orderBy = 'label asc';
        $records = $this->selectRecords('motorcycle_color', $where, $orderBy);

        $data = array();
        if($records) {
            foreach($records as $rec){
                $data[] = array(
                    "code" => $rec["code"],
                    "label" => $rec["label"]
                );
            }
        }
        return $data;
    }

    public function getCRSColorCodes() {
        $this->load->model('crs_m');
        $colors = $this->crs_m->getColorCodes();
        foreach($colors as $color) {
            if (empty($color['colorcode_id']) || empty($color['crs_code'])) { continue; }

            $id = $color['colorcode_id'];
            $code = $color['crs_code'];
            $label = empty($color['label']) ? $code : $color['label'];
            $this->db->query("INSERT INTO motorcycle_color (id, code, label) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE code=?, label=?", array($id, $code, $label, $code, $label));
        }
    }

    
}