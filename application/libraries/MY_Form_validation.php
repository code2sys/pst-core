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

}