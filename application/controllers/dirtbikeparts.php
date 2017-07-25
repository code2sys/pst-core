<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(__DIR__ . '/abstracttoplevelcategory.php');

class DirtBikeParts extends Abstracttoplevelcategory {
	function __construct()
	{
        parent::__construct();
        $this->_pageId = TOP_LEVEL_PAGE_ID_DIRT;
        $this->_categoryId = TOP_LEVEL_CAT_DIRT_BIKES;
        $this->_mainData['machinePageType'] = 'Dirt Bike';
        $_SESSION['url'] = 'dirtbikeparts/';
	}
	
}
