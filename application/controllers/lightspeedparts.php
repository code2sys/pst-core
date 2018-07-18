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

    public function outstandingactivity_get() {
        $this->_subOutstandingActivity();
    }

    public function outstandingactivity_post() {
        $this->_subOutstandingActivity();
    }

    // To match their format, we are going to have to print the XML ourselves...drat.
    protected function _subOutstandingActivity() {
        global $PSTAPI;
        $orders = $PSTAPI->order()->fetch(array(
            "pending_to_lightspeed" => 1,
            "ack_pending_by_lightspeed" => 0,
            "cancel_to_lightspeed" => 0
        ));

        $cancellations = $PSTAPI->order()->fetch(array(
            "cancel_to_lightspeed" => 1,
            "ack_cancel_by_lightspeed" => 0
        ));

        $format = $this->_jlb_format;

        if ($format == "json") {
            $this->response(array(
                "orders" => array_map(function($x) {
                    return $x->toJSONArray();
                }, $orders),
                "cancellations" => array_map(function($x) {
                    list($date, $comment) = $x->getCancellationDate();
                    return array(
                        "orderID" => $x->id(),
                        "date" => $date,
                        "comment" => $comment
                    );
                }, $cancellations)
            ), 200);
        } else {
            header("Content-Type: text/xml");
            // Build it up...
            $xml_data = new SimpleXMLElement('<?xml version="1.0"?><outstandingActivityResponse></outstandingActivityResponse>');
            $order_node = $xml_data->addChild("orders");
            foreach ($orders as $order) {
                $order->toXMLStruct($order_node);
            }

            $cancellations = $xml_data->addChild("cancellations");

            foreach ($cancellations as $x) {
                list($date, $comment) = $x->getCancellationDate();
                $web_order = $cancellations->addChild("webOrderCancellation");
                $web_order->addChild("orderId", htmlspecialchars($x->id()));
                $web_order->addChild("date", htmlspecialchars($date));
                $web_order->addChild("comment", htmlspecialchars($comment));
            }

            $temp_file = tempnam("/tmp", "xml_output");
            $xml_data->asXML($temp_file);
            print file_get_contents($temp_file);
            // unlink($temp_file);

        }
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
            $node = array(
                "taxRuleID" => $tax["id"],
                "description" => $tax["country"] . " - " . $tax["state"] . " - " . $tax["mailcode"] . " - " . $tax["id"]
            );

            $data[] = $node;
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
            $input = json_decode(json_encode(simplexml_load_string($input)), true);
        } else {
            $input = json_decode($input, true);
        }

        error_log("Structured input: ");
        error_log(print_r($input, true));
        return $input;
    }

    public function order_get($order_id, $action = "", $item_id = 0) {
        $this->_sub_order($order_id, $action, $item_id);
    }

    public function order_post($order_id, $action = "", $item_id = 0) {
        $this->_sub_order($order_id, $action, $item_id);
    }

    protected function _sub_order($order_id, $action, $item_id = 0) {
        $action = strtolower($action);
        switch($action) {
            case "":
                // this is a void...
                $this->_sub_order_void($order_id);
                break;

            case "shipment":
                $this->_sub_order_shipment($order_id);
                break;

            case "item":
                $this->_sub_order_item($order_id, $item_id);
                break;

            default:
                $this->_printFailure("Unknown action: " . $action);
        }
    }

    protected function _sub_order_item($order_id, $item_id) {
        // get the input!
        $input = $this->_getCleanInput();
        if (array_key_exists("itemNumber", $input)) {
            $item_id = $input["itemNumber"];
        }

        global $PSTAPI;
        $order = $PSTAPI->order()->get($order_id);

        if (is_null($order)) {
            $this->_printFailure("Invalid ID");
            exit();
        }

        // OK, now, the next thing should be order item...which should belong to this order...
        $item = $PSTAPI->orderProduct()->fetch(array(
            "order_id" => $order_id,
            "lightspeed_partnumber" => $item_id
        ));

        if (count($item) == 0) {
            $this->_printFailure("Invalid Item ID");
            exit();
        }
        $item = $item[0];

        // OK, now, we need to know the action...
        switch(strtoupper($input["action"])) {
            case "CANCEL":
                // I guess this maps to refunded?
                if ($item->get("status") != "Refunded" && $item->get("status") != "Shipped") {
                    $item->set("status", "Refunded");
                    $item->save();

                    // If they are all refunded, we have to clear them out...
                    $items = $PSTAPI->orderProduct()->fetch(array(
                        "order_id" => $order_id
                    ));

                    $not_refunded = false;
                    foreach ($items as $i) {
                        if ($i->get("status") != "Refunded") {
                            $not_refunded = true;
                        }
                    }

                    if (!$not_refunded) {
                        $PSTAPI->orderStatus()->add(array(
                            "order_id" => $order_id,
                            "status" => "Refunded",
                            "datetime" => time(),
                            "notes" => "Lightspeed Item Cancel"
                        ));
                    }
                } else if ($item->get("status") == "Shipped") {
                    $this->_printFailure("Recorded already as shipped.");
                    exit();
                }
                break;

            case "SELL":
                // I guess this maps to blank?
                if ($item->get("status") == "Refunded") {
                    $this->_printFailure("Item was already marked as refunded.");
                } else if ($item->get("status") == "Shipped") {
                    $this->_printFailure("Item was already marked as shipped.");
                } else if ($item->get("status") == "Back Order") {
                    // I assume it has been found, so clear it?
                    $item->set("status", "");
                    $item->save();
                }
                break;

            default:
                $this->_printFailure("Unrecognized action: " . $item["action"]);
                exit();
        }


        // record this...
        $PSTAPI->orderproductlightspeedaction()->add(array(
            "order_product_id" => $item->id(),
            "action" => $input["action"],
            "quantity" => $input["quantity"],
            "lightspeed_date" => date("Y-m-d H:i:s", strtotime($input["date"]))
        ));

        $this->_printSuccess();
    }


    protected function _sub_order_void($order_id) {
        $input = $this->_getCleanInput();
        global $PSTAPI;
        $order = $PSTAPI->order()->get($order_id);

        if (is_null($order)) {
            $this->_printFailure("Invalid ID");
            exit();
        }

        if (array_key_exists("action", $input) && strtolower($input["action"]) == "void") {
            $order->void($input["date"], $input["reason"]);
            $this->_printSuccess();
        } else {
            $this->_printFailure("Unknown Action: " . $input["action"]);
        }

    }


    protected function _sub_order_shipment($order_id) {
        // get the input!
        $input = $this->_getCleanInput();
        global $PSTAPI;
        $order = $PSTAPI->order()->get($order_id);

        if (is_null($order)) {
            $this->_printFailure("Invalid ID");
            exit();
        }

        // OK, we have some information; so we have to stick things on the order itself, and then we have to try to mark products.
        // We also are going to record this into the lightspeed_shipment table...
        $order->lightspeedShip($input["date"], $input["shipmentCarrier"], $input["shipmentMethod"], $input["trackingNumber"], $input["items"]);
        $this->_printSuccess();
    }
}
