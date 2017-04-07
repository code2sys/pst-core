<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//$this->load->helper('async');
if (!function_exists('curl_request_async')){

	function curl_request_async(){
		
		$url = site_url("/welcome/manual_appeagle_amazon_doc_generation") . "?".time();
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