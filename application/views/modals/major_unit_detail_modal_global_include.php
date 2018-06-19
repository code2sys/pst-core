<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 6/19/18
 * Time: 10:41 AM
 */

global $majorUnitDetailModalGlobalInclude;

if (!isset($majorUnitDetailModalGlobalInclude)) {
    $majorUnitDetailModalGlobalInclude = false;
}

// We are only going to load this once...
if (!$majorUnitDetailModalGlobalInclude) {
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $major_unit_detail_modal_global_include = mustache_tmpl_open("modals/major_unit_detail_modal_global_include.html");
    echo mustache_tmpl_parse($major_unit_detail_modal_global_include);
    $majorUnitDetailModalGlobalInclude = true;
}