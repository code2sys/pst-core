<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/27/18
 * Time: 11:48 AM
 *
 * The idea here is that we have two of these: Braintree and Stripe. There is always the chance he'll want a third,
 * like Authorize.Net, put back in. This is trying to harmonize Braintree and Stripe.
 */


class Genericpayments_m extends CI_Model {

    public function generateClientToken() {
        switch($this->merchant_type) {
            case "Braintree":
                $this->generateClientToken_braintree();
                break;

            default:
                return "";
                break;
        }
    }

    protected function generateClientToken_braintree() {
        $CI =& get_instance();
        return $CI->braintree_lib->create_client_token();
    }

    protected $merchant_type;


    public function initToProcessor($store_name, $processor) {
        $store_name["merchant_type"] = $processor;
        $this->init($store_name);
    }

    public function init($store_name) {
        switch($store_name["merchant_type"]) {
            case "Stripe":
                $this->init_stripe($store_name);
                $this->merchant_type = "Stripe";
                break;

            default:
                $this->merchant_type = "Braintree";
                $this->init_braintree($store_name);

                break;
        }
    }

    protected function init_braintree($store_name) {
        Braintree_Configuration::environment($store_name['environment']);
        Braintree_Configuration::merchantId($store_name['merchant_id']);
        Braintree_Configuration::publicKey($store_name['public_key']);
        Braintree_Configuration::privateKey($store_name['private_key']);
    }

    protected function init_stripe($store_name) {
        \Stripe\Stripe::setApiKey($store_name["stripe_secret_api_key"]);

    }

    public function sale($total, $short = false) {
        switch ($this->merchant_type) {
            case "Stripe":
                return $this->sale_stripe($total, $short);
                break;

            default:
                return $this->sale_braintree($total, $short);
        }
    }

    protected function sale_braintree($total, $short = false) {
        $data = [
            'amount' => $total,
            'paymentMethodNonce' => (array_key_exists("payment_method_nonce", $_POST) && $_POST["payment_method_nonce"] != "") ? $_POST["payment_method_nonce"] : (array_key_exists("paypal_nonce", $_SESSION) ? $_SESSION["paypal_nonce"] : ""),
            'options' => ['submitForSettlement' => True  ],
            'deviceData' => $_POST['device_data'],
            'channel' => 'MxConnectionLLC_SP_PayPalEC_BT'
        ];

        if (!$short && array_key_exists("contactInfo", $_SESSION) && array_key_exists("first_name", $_SESSION["contactInfo"]) && $_SESSION["contactInfo"] != "") {
            $data["customer"] = [
                'firstName' => $_SESSION['contactInfo']['first_name'],
                'lastName' => $_SESSION['contactInfo']['last_name'],
                'company' => $_SESSION['contactInfo']['company'],
                'phone' => $_SESSION['contactInfo']['phone'],
                'email' => $_SESSION['contactInfo']['email']
            ];
            $data["billing"] = [
                'firstName' => $_SESSION['contactInfo']['first_name'],
                'lastName' => $_SESSION['contactInfo']['last_name'],
                'company' => $_SESSION['contactInfo']['company'],
                'streetAddress' => $_SESSION['contactInfo']['street_address'],
                'extendedAddress' => $_SESSION['contactInfo']['address_2'],
                'locality' => $_SESSION['contactInfo']['state_shipping'],
                'postalCode' => $_SESSION['contactInfo']['zip']
            ];

            if (array_key_exists("shippingInfo", $_SESSION) && array_key_exists("first_name", $_SESSION["shippingInfo"]) && $_SESSION["shippingInfo"]["first_name"] != "") {
                $data["shipping"] = [
                    'firstName' => $_SESSION['shippingInfo']['first_name'],
                    'lastName' => $_SESSION['shippingInfo']['last_name'],
                    'company' => $_SESSION['shippingInfo']['company'],
                    'streetAddress' => $_SESSION['shippingInfo']['street_address'],
                    'extendedAddress' => $_SESSION['shippingInfo']['address_2'],
                    'locality' => $_SESSION['contactInfo']['state_shipping'],
                    'postalCode' => $_SESSION['shippingInfo']['zip']
                ];
            }
        }

        return Braintree_Transaction::sale($data);
    }

    protected function sale_stripe($total, $short = false) {
// Token is created using Checkout or Elements!
// Get the payment token ID submitted by the form:
        $token = $_POST['device_data'];

        $charge = \Stripe\Charge::create([
            'amount' => round(100.0 * $total, 0), // They want cents!
            'currency' => 'usd',
            'description' => 'Order #' . $_SESSION["newOrderNum"],
            'source' => $token,
        ]);

        error_log(print_r($charge, true));

        return $charge;
    }

    public function refund($transaction_id, $amount) {
        switch ($this->merchant_type) {
            case "Stripe":
                return $this->refund_stripe($transaction_id, $amount);
                break;

            default:
                return $this->refund_braintree($transaction_id, $amount);
        }

    }

    protected function refund_stripe($transaction_id, $amount) {
        try {
            $re = \Stripe\Refund::create(array(
                "charge" => $transaction_id,
                "amount" => round($amount * 100.0, 0)
            ));

            if (!is_null($re) && is_object($re) && $re->status == "succeeded") {
                return array($re->id, "");
            } else {
                return array("", "Error: Refund Failed");
            }

        } catch (\Exception $e) {
            return array("", "Exception: " . $e->getMessage());
        }
    }

    protected function refund_braintree($transaction_id, $amount) {
        $result = Braintree_Transaction::refund($transaction_id, $amount);

        if( !is_null($result) && is_object($result) && $result->success ) {
            return array($result->transaction->id, "");
        } else {
            return array("", "Error: " . $result->message);
        }
    }


    public function isSuccess(&$sale_result) {
        switch ($this->merchant_type) {
            case "Stripe":
                return $this->isSuccess_stripe($sale_result);
                break;

            default:
                return $this->isSuccess_braintree($sale_result);
        }
    }

    protected function isSuccess_braintree(&$sale_result) {
        return isset($sale_result) && is_object($sale_result) && $sale_result->success ;
    }

    protected function isSuccess_stripe(&$sale_result) {
        return $sale_result->status == "succeeded";
    }

    public function getTransactionID(&$sale_result) {
        switch ($this->merchant_type) {
            case "Stripe":
                return $this->getTransactionID_stripe($sale_result);
                break;

            default:
                return $this->getTransactionID_braintree($sale_result);
        }
    }

    protected function getTransactionID_braintree(&$sale_result) {
        return $sale_result->transaction->id ;
    }

    protected function getTransactionID_stripe(&$sale_result) {
        return $sale_result->id; // https://stripe.com/docs/api#retrieve_charge
    }

    public function getErrorMessage(&$sale_result) {
        switch ($this->merchant_type) {
            case "Stripe":
                return $sale_result->failure_message;
                break;

            default:
                return $sale_result->message;
        }

    }
}