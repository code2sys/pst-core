<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 5/31/18
 * Time: 6:24 PM
 */

require_once(__DIR__ . "/../libraries/REST_Controller.php");

class Lightspeedparts extends REST_Controller {

    protected function _getContentType() {
        $headers = getallheaders();
//        error_log(print_r($headers, true));
        return array_key_exists("CONTENT-TYPE", $headers) ? $headers["CONTENT-TYPE"] : "application/json";
    }


    protected function _fidgetFormat() {
        $format = $this->_getContentType();

        if (preg_match("/xml/i", $format)) {
            $this->response->format = "xml";
            return "xml";
        } else {
            $this->response->format = "json";
            return "json";
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->_fidgetFormat();

        global $PSTAPI;
        initializePSTAPI();
    }

    public function index_get() {
        $this->index_post();
    }

    public function index_post() {
        global $REAL_BASE_NODE_XML;
        $REAL_BASE_NODE_XML = "versions";
        $this->response(array(
            "version" => 1.0
        ), 200);
    }

    public function taxrules_get() {
        $this->_subtaxrules();
    }

    public function taxrules_post() {
        $this->_subtaxrules();
    }

    protected function _subtaxrules() {
        $format = $this->_fidgetFormat();

        // Now, we need the tax rules...
        global $PSTAPI;
        $taxes = $PSTAPI->taxes()->fetch(array("active" => 1), true);

        global $REAL_BASE_NODE_XML;
        $REAL_BASE_NODE_XML = "taxRules";

        $data = array();
        foreach ($taxes as $tax) {
            if ($tax["tax_value"] > 0) {
                $node = array(
                    "taxRuleID" => $tax["id"],
                    "description" => $tax["country"] . " - " . $tax["state"] . " - " . $tax["mailcode"] . " - " . $tax["id"]
                );

                    $data[] = $node;
            }
        }

        $this->response($data, 200);
    }


}
