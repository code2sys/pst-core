<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AuthorizeNet {


	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct()
    {
        $this->CI =& get_instance();
		// Load authorizenet configuration file
		$GLOBALS['environment'] = $this->CI->config->item('environment');
    }
    
    
    public function processPayment($data)
    {
        $response = '';
        try {
            //////$post_url = "https://test.authorize.net/gateway/transact.dll";
            $post_url = "https://secure.authorize.net/gateway/transact.dll";
            
            //print_r($data);
            
            $fname = trim($data['ccfname']);
            $lname = trim($data['cclname']);
            $addr  = trim($data['ccaddr']);
            $city  = trim($data['cccity']);
            $state = trim($data['ccstate']);
            $zip   = trim($data['cczip']);
            $ccnum = trim($data['ccnumber']);
            $ccexp = trim($data['ccexpmo']).trim($data['ccexpyr']);
            $purchnumber = trim($data['orderNum']);
            $price = trim($data['transAmount']);
            
            $post_values = array(
                "x_login"			=> "8Es78287sK7",
                "x_tran_key"		=> "54NWvbk2257KTC67",
                "x_version"			=> "3.1",
                "x_delim_data"		=> "TRUE",
                "x_delim_char"		=> "|",
                "x_relay_response"	=> "FALSE",
                "x_type"			=> "AUTH_CAPTURE",
                "x_method"			=> "CC",
                "x_card_num"		=> "$ccnum",
                "x_exp_date"		=> "$ccexp",
                "x_amount"			=> "$price",
                "x_description"		=> "Purchase: ".$purchnumber,
                "x_first_name"		=> "$fname",
                "x_last_name"		=> "$lname",
                "x_address"			=> "$addr",
                "x_state"			=> "$state",
                "x_zip"				=> "$zip"
            );
            
            $response = $this->callCurl($post_url, $post_values);
            
            
        } catch (Exception $e) {
            
        }
        return $response;
    }
    
    public function callCurl($post_url, $post_values)
    {
        $resp = '';
        try {
            $post_string = "";
            foreach( $post_values as $key => $value )
                { $post_string .= "$key=" . urlencode( $value ) . "&"; }
            $post_string = rtrim( $post_string, "& " );
            
            $request = curl_init($post_url); // initiate curl object
            curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
            curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
            $post_response = curl_exec($request); // execute curl post and store results in $post_response
            curl_close ($request); // close curl object
            $response_array = explode($post_values["x_delim_char"],$post_response);
            //foreach ($response_array as $key => $value)
            //{
            //    echo '<span style="color: #000;">k/v:'.$key.'-'.$value.'</span><br>';
            //}
            $resp = $response_array[3];            
        } catch (Exception $e) {
            
        }
        return $resp;
        exit;
    }
    
    
    

}
