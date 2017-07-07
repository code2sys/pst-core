<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/7/17
 * Time: 4:53 PM
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

class Abstracttoplevelcategory extends Master_Controller {

    protected $_pageId;
    protected $_categoryId;

    function __construct()
    {
        parent::__construct();
        $this->load->model('pages_m');
        $this->load->model('parts_m');
        // $this->_mainData['pages'] = $this->pages_m->getPages($this->_pageId, 'footer');
        unset($_SESSION['search']);
    }

    public function index()
    {
        $this->_mainData['pageRec'] = $this->pages_m->getPageRecByTag($this->_pageId);
        $this->setMasterPageVars('metatag', html_entity_decode($this->_mainData['pageRec']['metatags']));
        $this->setMasterPageVars('css', html_entity_decode($this->_mainData['pageRec']['css']));
        $this->setMasterPageVars('script', html_entity_decode($this->_mainData['pageRec']['javascript']));

        $_SESSION['breadcrumbs'] = array('parent_category_id' => $this->_categoryId);
        $this->_mainData['categories'] = $this->parts_m->getCategories($this->_categoryId);
        $this->_mainData['catRecord'] = $this->parts_m->getCategory($this->_categoryId);

        $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
        $this->loadSidebar('widgets/garage_v');
        $this->loadSidebar('widgets/ride_parts_v');
        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);

        $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
        $this->_mainData['categoryId'] = $this->_categoryId;
        $this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);

        $this->_mainData['pageRec'] = $this->pages_m->getPageRec($this->_pageId);
        $notice = $this->pages_m->getTextBoxes($this->_pageId);

        $this->_mainData['notice'] = $notice[0]['text'];
        $this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
        $this->setMasterPageVars('title', $this->_mainData['pageRec']['title']);
        $this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
        $this->_mainData['widgetBlock'] = $this->pages_m->widgetCreator($this->_pageId, $this->_mainData['pageRec']);
        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'comp_info');
        $this->loadSidebar('widgets/info_v');

        $this->_mainData['buttonPrice'] = TRUE;

        $this->_mainData['band'] = $this->parts_m->getFeaturedProducts($this->_categoryId, 4);
        $this->_mainData['featureBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);

        $this->_mainData['band'] = $this->parts_m->getProductDeals($this->_categoryId, 4);
        $this->_mainData['dealsBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);

        $this->_mainData['band'] = $this->parts_m->getTopSellers($this->_categoryId, 4);
        $this->_mainData['topSellersBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);

        $this->_mainData['band'] = $this->parts_m->getRecentlyViewed(0, @$_SESSION['recentlyViewed'], 4);
        if($this->_mainData['band']) {
            $this->_mainData['recentlyViewedBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);
        }

        $googleAdWordsScript = '<script>
		var google_tag_params = {
		ecomm_pagetype: \'home\'
		};
		</script>';
        $this->loadTopJS($googleAdWordsScript);
        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');

        $this->_mainData['new_header']  = 1;
        $this->_mainData['cat_header']  = 1;
        $this->_mainData['top_parent']  = $this->_categoryId;

        /*  GETTING CATEGORIES FOR TOP NAV*/
        $nav_categories_and_parent = $this->parts_m->nav_categories_and_parent(0, $this->_categoryId);
        $this->_mainData['nav_categories'] = $nav_categories_and_parent['navCategories'];

        $this->setFooterView('master/footer_v.php');
        $this->setNav('master/navigation_v', 0);
        $this->renderMasterPage('master/master_v', 'info/ride_home_v', $this->_mainData);
    }
}
