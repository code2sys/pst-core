<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * JLB 07-09-17
 * There are insufficient words to describe the degree of assholishness associated with this code.
 *
 * The true function has been disguised. They have pretended to use the name of a real PHP function.
 * It turns around an generates some sort of Amazon document - because the original coder was just an
 * absolutely horrible person.
 */

//$this->load->helper('async');
if (!function_exists('curl_request_async')){

	function curl_request_async(){
        return; // JLB 07-09-17 NUKE IT FROM SPACE.
		
		$url = "http://" . WEBSITE_HOSTNAME . "/welcome/manual_appeagle_amazon_doc_generation?".time();
		$params = array();
		$type='GET';
		$post_params = array();
		
		foreach ($params as $key => &$val) {
			if (is_array($val)) $val = implode(',', $val);
			$post_params[] = $key.'='.urlencode($val);
		}
		$post_string = implode('&', $post_params);
	
		$parts=parse_url($url);
		$fp = fsockopen($parts['host'],
			(isset($parts['scheme']) && $parts['scheme'] == 'https')? 443 : 80,
			$errno, $errstr, 999999999);
	
		$out = "$type ".$parts['path'] . (isset($parts['query']) ? '?'.$parts['query'] : '') ." HTTP/1.1\r\n";
		$out.= "Host: ".$parts['host']."\r\n";
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out.= "Content-Length: ".strlen($post_string)."\r\n";
		$out.= "Connection: Close\r\n\r\n";
		// Data goes in the request body for a POST request
		if ('POST' == $type && isset($post_string)) $out.= $post_string;
		
		fwrite($fp, $out);
		fclose($fp);
	}
}
/* End of file async_helper.php */
/* Location: ./system/helpers/asyc_helper.php */
