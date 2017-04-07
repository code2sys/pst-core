<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getCaptchaDisplayElements'))
{
	function getCaptchaDisplayElements()
	{
		$captchaElements = array();
		// Get an instance to CI		
		$CI =& get_instance();
		// Load encryption library
		$CI->load->library('encrypt');
		// Create and populate the captcha display elements
    $captchaElements['first'] = rand(1,10);
    $captchaElements['second'] = rand(1,10);
    $answer = $captchaElements['first'] + $captchaElements['second'];
    $captchaElements['encrypted_answer'] = $CI->encrypt->encode($answer);
		return $captchaElements;
  }
}

if ( ! function_exists('validateCaptcha'))
{
	function validateCaptcha($encryptedAnswer=NULL, $userAnswer=NULL)
	{
		$ret = FALSE;
		if ( is_null($encryptedAnswer) || is_null($userAnswer) )
			return $ret;
		// Get an instance to CI		
		$CI =& get_instance();
		// Load encryption library
		$CI->load->library('encrypt');
		$ret = ($CI->encrypt->decode($encryptedAnswer) == $userAnswer);
		return $ret;
  }
}

/* End of file easy_captcha_helper.php */
/* Location: APPPATH/helpers/easy_captcha_helper.php */
