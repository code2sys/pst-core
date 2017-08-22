<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 8/21/17
 * Time: 2:20 PM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* NOTE!!!  Need to make sure to turn on if checkbox is there and off it is not */
require_once("master_m.php");

class Vault_M extends Master_M {

    public function getVaultImages() {
        $where = array();
        $orderBy = 'priority_number asc';
        $record = $this->selectRecords('vault_images', $where, $orderBy);
        return $record;
    }

    public function updateVaultImage( $arr ) {
        $this->createRecord('vault_images', $arr, FALSE);
    }

    public function deleteVaultImage( $id, $motorcycle_id ) {
        $this->db->delete('vault_images', array('id' => $id));
    }

    public function updateVaultImageDescription( $id, $pst ) {
        //$data = array('description' => $post['descr']);
        $where = array('id' => $id);
        $this->updateRecord('vault_images', $pst, $where, FALSE);
    }

    public function updateVaultImageOrder( $id, $ord ) {
        $where = array('id' => $id);
        $data = array('priority_number'=>$ord);
        $this->updateRecord('vault_images', $data, $where, FALSE);
    }

}