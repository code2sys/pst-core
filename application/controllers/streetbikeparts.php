<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(__DIR__ . '/abstracttoplevelcategory.php');

class StreetBikeParts extends Abstracttoplevelcategory {

    function __construct() {
        parent::__construct();
        $this->_pageId = TOP_LEVEL_PAGE_ID_STREET;
        $this->_categoryId = TOP_LEVEL_CAT_STREET_BIKES;
        $this->_mainData['machinePageType'] = 'Street Bike';
        $_SESSION['url'] = 'streetbikeparts/';
    }
}

