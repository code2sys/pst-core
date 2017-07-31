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
$CI =& get_instance();
$clientToken = $CI->braintree_lib->create_client_token();
