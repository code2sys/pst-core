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


    protected function _printSuccess() {
        if ($this->_jlb_format == "xml") {
            header("Content-Type: text/xml");
            print '<?xml version="1.0" encoding="utf-8"?><acknowledgeResponse><responseStatus>SUCCESS</responseStatus></acknowledgeResponse>';
            exit();
        } else {
            $this->response(array(
                "responseStatus" => "SUCCESS"
            ), 200);
        }
    }

    protected function _printFailure($error = "") {
        if ($this->_jlb_format == "xml") {
            header("Content-Type: text/xml");

            $error = ($error != "") ? "<responseMessage>" . $error . "</responseMessage>" : "";

            print '<?xml version="1.0" encoding="utf-8"?><acknowledgeResponse><responseStatus>FAILURE</responseStatus>' . $error . '</acknowledgeResponse>';
            exit();
        } else {
            $data = array(
                "responseStatus" => "FAILURE"
            );

            if ($error != "") {
                $data["message"] = $error;
            }

            $this->response($data, 200);
        }
    }

    protected function _verifyAuthorization() {
        $headers = getallheaders();
        $authorization = array_key_exists("AUTHORIZATION", $headers) ? $headers['AUTHORIZATION'] : "";

        if ($authorization == "") {
            $this->response(array(
                "error" => "No authorization header received."
            ), 403);
            exit();
        } else {
            $base64 = substr($authorization, 6);
            global $PSTAPI;
            $lightspeed_feed_username = $PSTAPI->config()->getKeyValue('lightspeed_feed_username', '');
            $lightspeed_feed_password = $PSTAPI->config()->getKeyValue('lightspeed_feed_password', '');

            if ($base64 != base64_encode($lightspeed_feed_username . ":" . $lightspeed_feed_password)) {
                $this->response(array(
                    "error" => "Invalid authorization header received."
                ), 403);
                exit();
            }
        }
    }

    protected function _fidgetFormat() {
        $format = $this->_getContentType();

        if (preg_match("/xml/i", $format)) {
            $this->response->format = "xml";
            $this->_jlb_format = "xml";
        } else {
            $this->response->format = "json";
            $this->_jlb_format = "json";
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->_fidgetFormat();

        global $PSTAPI;
        initializePSTAPI();
        $this->_verifyAuthorization();
    }

    public function index_get() {
        $this->index_post();
    }

    public function index_post() {
        global $REAL_BASE_NODE_XML;
        $REAL_BASE_NODE_XML = "versions";

        $data = array("1.0");
        $format = $this->_jlb_format;
        if ($format == "json") {
            $data = array("versions" => $data);
        }

        $this->response($data, 200);
    }

    public function taxrules_get() {
        $this->_subtaxrules();
    }

    public function taxrules_post() {
        $this->_subtaxrules();
    }

    protected function _subtaxrules() {
        $format = $this->_jlb_format;

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

        if ($format == "json") {
            $data = array("taxRules" => $data);
        }

        $this->response($data, 200);
    }

    // This is acknowledgement of outstanding items...
    public function acknowledgerequest_post() {
        $this->_acknowledgeRequest();
    }

    public function acknowledgerequest_get() {
        $this->_acknowledgeRequest();
    }

    protected function _acknowledgeRequest() {
        // Step 1: Get the input...
        $clean_input = $this->_getCleanInput();

        // Next, we are expecting an id, which is an order ID, and to do something with it...
        if (array_key_exists("id", $clean_input)) {
            global $PSTAPI;
            $order = $PSTAPI->order()->get($clean_input["id"]);

            if (is_null($order)) {
                $this->_printFailure("Invalid ID");
                exit();
            }

            switch ($clean_input["responseType"]) {
                case "WEB_ORDER":
                    $order->set("ack_pending_by_lightspeed", 1);
                    $order->set("ack_pending_by_lightspeed_timestamp", date("Y-m-d H:i:s", strtotime($clean_input["date"])));
                    $order->save();
                    break;

                case "WEB_ORDER_CANCELLATION":
                    $order->set("ack_cancel_by_lightspeed", 1);
                    $order->set("ack_cancel_by_lightspeed_timestamp", date("Y-m-d H:i:s", strtotime($clean_input["date"])));
                    $order->save();
                    break;
            }

            // I suppose there could be issues...
            if (array_key_exists("responseIssues", $clean_input)) {
                foreach ($clean_input["responseIssues"] as $rec) {
                    $PSTAPI->orderresponseissue()->add(array(
                        "order_id" => $order->id(),
                        "code" => $rec["code"],
                        "message" => $rec["message"],
                        "responseStatus" => $rec["responseStatus"]
                    ));
                }
            }

            $this->_printSuccess();
        } else {
            $this->_printFailure("No ID Received.");
        }
    }


    // TODO: I need you to hammer the XML into the exact same structure as the JSON...
    protected function _getCleanInput()
    {
        $this->_fidgetFormat();
        $input = file_get_contents("php://input");
        error_log("Raw input");
        error_log($input);
        if ($this->_jlb_format == "xml") {
            return simplexml_load_string($input);
        } else {
            return json_decode($input, true);
        }
    }

}
