<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * active_dropdown_helper.php
 *
 * Contains helper functon(s) to use the active dropdown.
 *
 *
 * NOTES
 *
 * REVISION HISTORY
 * JR (04/03/2013) - Initial version
 *
 */

if ( ! function_exists('getActiveDropdownHeadScript'))
{
	function getActiveDropdownHeadScript($assetsPath)
	{
		$retStr = '';
		$CI =& get_instance();
		$CI->config->load('fw_core_settings');
		$data = array();
		$data['toolsPath'] = $CI->config->item('ToolsURL');
		$data['assetsPath'] = $assetsPath;
		$retStr = $CI->load->view('custom_helpers/active_dropdown_header_v', $data, TRUE);
		return $retStr; 
	}
}

if ( ! function_exists('getActiveDropdownFooterScript'))
{
	function getActiveDropdownFooterScript()
	{
		$retStr = '';
		$CI =& get_instance();
		$CI->config->load('fw_core_settings');
		$data = array();
		$retStr = $CI->load->view('custom_helpers/active_dropdown_footer_v', $data, TRUE);
		return $retStr; 
	}
}

if ( ! function_exists('customForm_active_dropdown'))
{
	function customForm_active_dropdown($name, $dropdowns, $selected = '', $additional = '')
	{
		$retStr = '';
		$CI =& get_instance();
		$CI->config->load('fw_core_settings');
		$data = array();
		$data['name'] = $name;
		$data['dropdowns'] = $dropdowns;
		$data['selected'] = $selected;
		$data['additional'] = $additional;
		$retStr = $CI->load->view('custom_helpers/active_dropdown_v', $data, TRUE);
		return $retStr; 
	}
}



/* End of file fw_simplemodal_helper.php */
/* Location: FRAMEWORK/helpers/fw_simplemodal_helper.php */