<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 5/31/18
 * Time: 6:24 PM
 */

require_once(__DIR__ . "/../libraries/REST_Controller.php");

class Lightspeedparts extends REST_Controller {


    function index_get() {
        $this->response(array(
            "success" => 1,
            "method" => "GET"
        ), 200);
    }

    function index_post() {
        $this->response(array(
            "success" => 1,
            "method" => "POST"
        ), 200);
    }

}
