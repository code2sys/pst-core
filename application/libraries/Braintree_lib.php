<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH.'third_party/Braintree/Braintree.php';

/*
 *  Braintree_lib
 *	Braintree PHP SDK v3.*
 *  For Codeigniter 3.*
 */

class Braintree_lib{

    function create_client_token(){
        try {
            $CI =& get_instance();
            $store_name = $CI->admin_m->getAdminShippingProfile();

            if ($store_name["merchant_type"] != "Braintree") {
                return ""; // This is not enabled...Don't try.
            }

            Braintree_Configuration::environment($store_name['environment']);
            Braintree_Configuration::merchantId($store_name['merchant_id']);
            Braintree_Configuration::publicKey($store_name['public_key']);
            Braintree_Configuration::privateKey($store_name['private_key']);
            $clientToken = Braintree_ClientToken::generate();
            return $clientToken;
        } catch(Exception $e) {
            error_log("Error in Braintree Library: " . $e->getMessage());
            return "";
        }
    }
}
