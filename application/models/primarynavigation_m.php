<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 10/11/17
 * Time: 10:53 AM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Primarynavigation_M extends Master_M {

    public function getPrimaryNavigation($active_only = true) {
        $query = $this->db->query("Select * from primarynavigation " . ($active_only ? " where active > 0 " : "") . " order by primarynavigation.ordinal");
        return $query->result_array();
    }

    // This moves it to the end of the list of ordinals..
    public function moveToEnd($primarynavigation_id) {

    }

    // This compacts all the ordinals...you should do this after you shift something around...
    public function compactOrdinals() {

    }

    public function getRecord($primarynavigation_id) {
        $query = $this->db->query("Select * from primarynavigation where primarynavigation_id = ?", array($primarynavigation_id));
        $rec = $query->result_array();
        return count($rec) > 0 ? $rec[0] : null;
    }

    public function createCustomRecord() {
        $this->db->query("Insert into primarynavigation (active, label, class) values (0, 'New Custom Button', 'Custom')");
        $id = $this->db->insert_id();
        $this->db->query("Update primarynavigation set span_id = concat('cust', primarynavigation_id) where primarynavigation_id = ?", array($id));
        return $this->getRecord($id);
    }

    public function updateNavigationOptions($primarynavigation_id, $change_array) {
        $values = array();
        $query = "Update primarynavigation set ";
        foreach ($change_array as $k => $v) {
            if (count($values) > 0) {
                $query .= ", ";
            }
            $query .= "$k = ? ";
            $values[] = $v;
        }

        if (count($values) > 0) {
            $values[] = $primarynavigation_id;
            $query .= " where primarynavigation_id = ? limit 1";
            $this->db->query($query, $values);
        }
    }

}