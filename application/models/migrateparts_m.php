<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 1/10/18
 * Time: 11:43 AM
 */

class Migrateparts_m extends CI_Model {

    // This wraps the call from lightspeed_m
    public function queryMatchingPart($rows) {
        // now, post them
        $ch = curl_init("http://" . WS_HOST . "/migrateparts/queryMatchingPart/");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data = json_encode($rows));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, count($data));
        return json_decode(curl_exec($ch), true);
    }

    public function getEternalPartVariations($eternalpartvariation_ids) {
        // now, post them
        $ch = curl_init("http://" . WS_HOST . "/migrateparts/getEternalPartVariations/");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data = http_build_query($eternalpartvariation_ids));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, count($data));
        return json_decode(curl_exec($ch), true);
    }

    public function getEternalPartVariation($eternalpartvariation_id) {
        // now, post them
        $ch = curl_init("http://" . WS_HOST . "/migrateparts/getEternalPartVariation/" . $eternalpartvariation_id);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return json_decode(curl_exec($ch), true);
    }
}