<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(__DIR__ . '/abstracttoplevelcategory.php');

class ATVParts extends Abstracttoplevelcategory {

	function __construct()
	{
		parent::__construct();
        $this->_pageId = TOP_LEVEL_PAGE_ID_ATV;
        $this->_categoryId = TOP_LEVEL_CAT_ATV_PARTS;
		$this->_mainData['machinePageType'] = 'ATV';
        $_SESSION['url'] = 'atvparts/';
    }
}
