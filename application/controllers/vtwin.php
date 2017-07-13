<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once(__DIR__ . '/abstracttoplevelcategory.php');

class VTwin extends Abstracttoplevelcategory {

    function __construct() {
        parent::__construct();
        $this->_pageId = TOP_LEVEL_PAGE_ID_VTWIN;
        $this->_categoryId = TOP_LEVEL_CAT_VTWIN_PARTS;
        $this->_mainData['machinePageType'] = 'VTWIN';
        $_SESSION['url'] = 'vtwin/';
    }
}
