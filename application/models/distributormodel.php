<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/10/17
 * Time: 10:09 PM
 */

class Distributormodel extends CI_Model {

    public function fetchByName($name) {
        $query = $this->db->query("Select * from distributor where name = ? and active > 0", array($name));
        foreach ($query->result_array() as $row) {
            return $row;
        }
        return array();
    }

    public function getIndex() {
        $query = $this->db->query("Select * from distributor where name != 'Lightspeed Feed' order by name");
        return $query->result_array();
    }

    public function get($distributor_id) {
        $query = $this->db->query("Select * from distributor where distributor_id = ?", array($distributor_id));
        foreach ($query->result_array() as $row) {
            return $row;
        }
        return array();
    }

    public function remove($distributor_id) {
        $this->db->query("Delete from distributor where distributor_id = ? and customer_distributor = 1", array($distributor_id));
    }

    public function update($distributor_id, $kvpArray) {
        $table = "distributor";
        $id_column = "distributor_id";
        $id = $distributor_id;

        $query = "update $table set ";
        $values = array();

        foreach ($kvpArray as $k => $v) {
            if (count($values) > 0) {
                $query .= ", ";
            }

            $query .= $k . " = ? ";
            $values[] = $v;
        }

        $values[] = $id;
        $this->db->query($query . " where $id_column = ? limit 1", $values);
    }

    public function add($kvpArray) {
        $kvpArray["customer_distributor"] = 1;
        $table = "distributor";
        $id_column = "distributor_id";
        $query = "insert into $table (";
        $value_query = ") values (";
        $values = array();
        $duplicate = " on duplicate key update $id_column = last_insert_id($id_column) ";
        $match_where = "";

        $id = 0;

        foreach ($kvpArray as $k => $v) {
            // $v = htmlentities($v, ENT_QUOTES, 'UTF-8');
            if (count($values) > 0) {
                $value_query .= ", ";
                $query .= ", ";
                $match_where .= " AND ";
            }

            $value_query .= "?";
            $query .= $k;
            $values[] = $v;
            $duplicate .= ", $k = values($k) ";
            $match_where .= " $k = ? ";
        }

        $this->db->query($query . $value_query . ")" . $duplicate, $values);
        $query2 = $this->db->query("Select last_insert_id()");
        foreach ($query2->result_array() as $row) {
            $id = $row['last_insert_id()'];
        }

        if ($id == 0) {
            print "Failure: " . $query . $value_query . ")" . $duplicate . "\n";
            print_r($values);
        }
        return $id;
    }

}