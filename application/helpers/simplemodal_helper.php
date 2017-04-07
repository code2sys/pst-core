<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * simplemodal_helper.php
 *
 * Contains helper functon(s) to use JQuery SimpleModal.
 *
 * DEPENDENCIES
 *
 * NOTES
 *
 * REVISION HISTORY
 * JR (4/4/2013) - Created from fw_simplemodal_helper.  Updated to include styling and jquery functions to streamline usage.
 *
 */

if ( ! function_exists('getSimpleModalHeadScript'))
{
	function getSimpleModalHeadScript($assetsPath)
	{
		$retStr = '';
		$CI =& get_instance();
		$data = array();
		$data['assetsPath'] = $assetsPath;
		$retStr = $CI->load->view('custom_helpers/simplemodal_header_v', $data, TRUE);
		return $retStr; 
	}
}

if ( ! function_exists('customForm_modal'))
{
	function customForm_modal($divId, $text, $width = 400, $height = 400, $addfuncs = '', $styles = '')
	{
		$retStr = '';
		$CI =& get_instance();
		$data = array();
		$data['div'] = $divId;
		$data['text'] = $text;
		$data['width'] = $width;
		$data['height'] = $height;
		$data['addfuncs'] = $addfuncs;
		$data['styles'] = $styles;
		$retStr = $CI->load->view('custom_helpers/simplemodal_v', $data, TRUE);
		return $retStr; 
	}
}


/* End of file simplemodal_helper.php */

