<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/26/17
 * Time: 9:35 AM
 */

class Statusmodel extends CI_Model {

    public function __construct() {
        parent::__construct();

        $this->success = false;
        $this->error_message = "Unknown error (Uninitialized).";
        $this->success_message = "";
        $this->data = array();
    }

    public function setData($key, $value) {
        $this->data[$key] = $value;
    }

    public function setError($string) {
        $this->success = false;
        $this->error_message = $string;
    }

    public function setSuccess($string) {
        $this->success = true;
        $this->success_message = $string;
    }

    public function outputStatus() {
        // Header - JSON
        header('Content-Type: application/json');
        echo json_encode(array(
            "success" => $this->success,
            "error_message" => $this->error_message,
            "success_message" => $this->success_message,
            "data" => $this->data
        ));
        exit();
    }

}