<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/20/18
 * Time: 2:42 PM
 */


if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(__DIR__ . '/abstracttoplevelcategory.php');

class Snowparts extends Abstracttoplevelcategory {

    function __construct() {


        parent::__construct();
        $this->_pageId = TOP_LEVEL_PAGE_ID_SNOW;
        $this->_categoryId = TOP_LEVEL_CAT_SNOW;
        $this->_mainData['machinePageType'] = 'Snow';
        $_SESSION['url'] = 'snowparts/';
    }
}
