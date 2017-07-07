<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(__DIR__ . '/abstracttoplevelcategory.php');

class UTVParts extends Abstracttoplevelcategory {

    function __construct() {
        parent::__construct();
        $this->_pageId = TOP_LEVEL_PAGE_ID_UTV;
        $this->_categoryId = TOP_LEVEL_CAT_UTV_PARTS;
        $this->_mainData['machinePageType'] = 'UTV';
        $_SESSION['url'] = 'utvparts/';
    }
}
