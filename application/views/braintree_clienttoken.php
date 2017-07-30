<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/29/17
 * Time: 9:28 PM
 *
 * I have no idea why the include works here but does not work if I try to more directly spell it out. Is it because this is a path in the vendor directory?
 *
 */

include('lib/Braintree.php');

try {
    Braintree_Configuration::environment($store_name['environment']);
    Braintree_Configuration::merchantId($store_name['merchant_id']);
    Braintree_Configuration::publicKey($store_name['public_key']);
    Braintree_Configuration::privateKey($store_name['private_key']);
    $clientToken = Braintree_ClientToken::generate();
} catch(\Exception $e) {
    error_log("Braintree not configured: " . $e->getMessage());
    $clientToken = "";
}