<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 10/10/17
 * Time: 10:16 AM
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once(__DIR__ . '/abstracttoplevelcategory.php');

class Marine extends Abstracttoplevelcategory {

    function __construct() {
        parent::__construct();
        $this->_pageId = TOP_LEVEL_PAGE_ID_MARINE;
        $this->_categoryId = TOP_LEVEL_CAT_MARINE;
        $this->_mainData['machinePageType'] = 'MARINE';
        $_SESSION['url'] = 'marine/';
    }
}
