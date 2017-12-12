<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/29/17
 * Time: 9:39 PM
 */

class MY_Form_validation extends CI_Form_validation {

    /**
     * Minimum Length
     *
     * @access	public
     * @param	string
     * @param	value
     * @return	bool
     */
    public function null_min_length($str, $val)
    {
        if (is_null($str) || $str == "") {
            return true; // we skip if null.
        }

        if (preg_match("/[^0-9]/", $val))
        {
            return FALSE;
        }

        if (function_exists('mb_strlen'))
        {
            return (mb_strlen($str) < $val) ? FALSE : TRUE;
        }

        return (strlen($str) < $val) ? FALSE : TRUE;
    }


    public function sku_not_in_use($str, $val) {
        // the SKU cannot be in use for any other bike...
        $CI =& get_instance();
        $CI->load->helper("url");
        $url = uri_string();
        $url = explode("/", $url);
        $last_number = $url[count($url) - 1];
        $last_number = intVal($last_number);

        $query = $CI->db->query("Select count(*) as cnt from motorcycle where sku = ? and id != ?", array($str, $last_number));
        $count = $query->result_array();
        $count = $count[0]["cnt"];
        return $count == 0;
    }

}