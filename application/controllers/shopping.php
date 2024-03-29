<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
//require_once( site_url()  . 'lib/Braintree.php');

class Shopping extends Master_Controller {


    public function receiveFromHLSM() {
        $action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : "";
        $hlsmno = array_key_exists("hlsmno", $_REQUEST) ? $_REQUEST["hlsmno"] : 0;
        $transferid = array_key_exists("transferid", $_REQUEST) ? $_REQUEST["transferid"] : 0;

        global $PSTAPI;
        initializePSTAPI();
        $transfer = $PSTAPI->hlsmxmlfeed()->get($transferid);

        if ($action == "hlsmtransfer" && !is_null($transfer) && $transfer->get("hlsmno") == $hlsmno) {
            if (!array_key_exists("hlsmtransfers", $_SESSION)) {
                $_SESSION["hlsmtransfers"] = array();
            }

            if (!in_array($transfer->id(), $_SESSION["hlsmtransfers"])) {
                // it cannot be claimed...
                if ($transfer->get("claimed") > 0) {
                    print "Transfer failed - replay.";
                    exit();
                }
                $_SESSION["hlsmtransfers"][] = $transfer->id();
            }

            if ($transfer->get("claimed") == 0) {
                $transfer->set("claimed", 1);
                $transfer->save();

                // get all the partnumbers
                $partnumbers = $transfer->getPartNumbersForCart();

                // Put them in the cart
                foreach ($partnumbers as $pn) {
                    $_SESSION['cart'][$pn["partnumber"]] = array(
                        "part_id" => $pn["part_id"],
                        "display_name" => $pn["name"],
                        "images" => null,
                        "partnumber" => $pn["partnumber"],
                        "qty" => $pn["qty"],
                        "price" => $pn["price"],
                        "type" => "cart"
                    );

                }

                // This is just copied from shopping/item...
                if (@$_SESSION['userRecord']['id'])
                    $this->parts_m->updateCart();

                redirect('shopping/cart');


            }


        } else {
            print "Transfer failed - unrecognized. ($action, $transferid, $hlsmno)";
        }

    }

    protected $_pagination = 6;
    protected $_adpdtLimit = 48;

    function __construct() {
        parent::__construct();
        $this->load->model('pages_m');
        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');

        $this->load->model('parts_m');
        $this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
        //$this->output->enable_profiler(TRUE);
		$this->load->library("braintree_lib");
    }

    private function validateProduct() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('part_id', 'Part Id', 'required|exists[part.part_id]|xss_clean');
        $this->form_validation->set_rules('question', 'Question', 'xss_clean');
        $this->form_validation->set_rules('qty', 'Qty', 'required|numeric|xss_clean');
        $this->form_validation->set_rules('type', 'Type', 'required|xss_clean');
        $this->form_validation->set_rules('images', 'Image', 'xss_clean');
        $this->form_validation->set_rules('display_name', 'Name', 'required|xss_clean');
        return $this->form_validation->run();
    }

    private function validateAdPdtPageBundle() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('page', 'Page', 'numeric|xss_clean');
        return $this->form_validation->run();
    }

    private function adpdtPagination($count) {
        $pages = 0;
        if (is_numeric($count))
            $pages = ceil($count / $this->_adpdtLimit); // Number of records per page
        if ($pages < 0)
            $pages = 0;
        return $pages;
    }

    public function generateProductListPaginate($direction = NULL) {
        if (is_numeric(@$_POST['page'])) {
            $this->_mainData['pages'] = $this->adpdtPagination($this->parts_m->getSearchCount($_SESSION['breadcrumbs']));
            $this->_mainData['currentPage'] = ($direction == 'up') ? (@$_POST['page'] + ($this->_pagination * 2)) : (@$_POST['page'] - ($this->_pagination * 2));
            $this->_mainData['display_pages'] = $this->_pagination;
            $pagination = $this->load->view('master/pagination/productlist_v', $this->_mainData, TRUE);
        }
        if (@$_POST['ajax'])
            echo @$pagination;
        else
            return @$pagination;
    }

    protected function getActiveMachine() {
        return (array_key_exists("garage", $_SESSION) && is_array($_SESSION["garage"]) && count($_SESSION["garage"]) > 0 && array_key_exists('activeMachine', $_SESSION) && !is_null($_SESSION['activeMachine'])) ? $_SESSION['activeMachine'] : null;

    }

    public function generateAdPdtListTable($order = 'name', $dir = 'DESC', $page = 1) {
        if (@$_POST['ajax'] && ($this->validateAdPdtPageBundle() !== FALSE)) {// If form validation passes use passed sorting
            $page = $this->input->post('page');
            $filter = $_SESSION['breadcrumbs'];
        }
        $order = $order . ' ' . $dir;
        $filter = ($filter == 'NULL') ? NULL : $filter;
        $offset = ($page - 1) * $this->_adpdtLimit;
        $this->_mainData['band']['label'] = 'Search Results';
        $this->_mainData['band']['products'] = $this->parts_m->getSearchResults($filter, $this->getActiveMachine(), $this->_adpdtLimit, $offset);
        $tableView = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);
        if (@$_POST['ajax']) {
            echo $tableView;
        } else
            return $tableView;
    }

    /*
     * JLB 2017-10-23
     * I think this function is unused. It generates an error.
     */
    public function index($cat = 'ES', $sub = NULL) {
        redirect("/shopping/cart");
        exit();
    }

    public function cart() {
        unset($_SESSION['guestUser']);
        $_SESSION['url'] = 'shopping/cart';
        $this->generateShoppingCart();
        $googleAdWordsScript = '<script>
								var id = new Array();
								var price = jQuery(\'.cart_total h3\')[0].innerHTML.replace("Cart Total: $","");
								var len = jQuery(\input[placeholder="Add Quanity"]\').length;
								for(i=0;i<len;i++)
								{
								id.push(jQuery(\'input[placeholder="Add Quanity"]\')[i].id);
								}
								var google_tag_params = {
								ecomm_prodid: id,
								ecomm_pagetype: \'cart\',
								ecomm_totalvalue: price
								};
								</script>';
        $this->_mainData['footerscript'] = $googleAdWordsScript;

        $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
        $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
        $this->_mainData['new_header'] = 1;
        $this->_mainData['cart'] = 1;

        // Now, tell me, is this PayPal? It is only PayPal if this is Braintree


        $this->setNav('master/navigation_v', 2);
        $this->setFooterView('master/footer_v.php');
        $this->renderMasterPage('master/master_v', 'account/cart_v_new', $this->_mainData);
    }

	public function paypal_ec()
	{
		require_once("paypal/paypal_functions.php");
  
	   //Call to SetExpressCheckout using the shopping parameters collected from the shopping form on index.php and few from config.php 

	   $returnURL = RETURN_URL;
	   $cancelURL = CANCEL_URL; 
	   
	   if(isset($_POST["PAYMENTREQUEST_0_ITEMAMT"]))
	   $_POST["L_PAYMENTREQUEST_0_AMT0"]=$_POST["PAYMENTREQUEST_0_ITEMAMT"];

	   $resArray = CallShortcutExpressCheckout ($_POST, $returnURL, $cancelURL);
	   $ack = strtoupper($resArray["ACK"]);
	   if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")  //if SetExpressCheckout API call is successful
	   {
		RedirectToPayPal ( $resArray["TOKEN"] );
	   } 
	   else  
	   {
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
		
		echo "SetExpressCheckout API call failed. ";
		echo "Detailed Error Message: " . $ErrorLongMsg;
		echo "Short Error Message: " . $ErrorShortMsg;
		echo "Error Code: " . $ErrorCode;
		echo "Error Severity Code: " . $ErrorSeverityCode;
	   }
	   
	}
	
    public function wishlist() {
        $_SESSION['url'] = 'shopping/wishlist';
        if (!@$_SESSION['userRecord']['id'])
            redirect('welcome/new_account');

        if (@$_SESSION['wishlist']) {
            $this->parts_m->updateWishList($_SESSION['wishlist'], $_SESSION['userRecord']['id']);
            unset($_SESSION['wishlist']);
        }

        $this->loadSidebar('widgets/garage_v');
        $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);

        $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);

        $this->_mainData['addCart'] = TRUE;

        $this->_mainData['band'] = $this->parts_m->getWishList();
        $this->_mainData['mainProductBand'] = $this->load->view('account/wishlist_v', $this->_mainData, TRUE);
        
        $this->_mainData['new_header'] = 1;

        $this->setNav('master/navigation_v', 0);

        $this->setFooterView('master/footer_v.php');
        $this->renderMasterPage('master/master_v', 'info/product_list_v', $this->_mainData);
    }

    public function remove_wish_item($id) {
        if (is_numeric($id)) {
            $this->parts_m->removeWishListItem($id);
        }
        redirect('shopping/wishlist');
    }

    public function check_value(&$value) {
        $value = strip_tags($value);
    }

    private function array_map_r($func, $arr) {
        $newArr = array();

        foreach ($arr as $key => $value) {
            $newArr[$key] = ( is_array($value) ? array_map_r($func, $value) : ( is_array($func) ? call_user_func_array($func, $value) : $func($value) ) );
        }

        return $newArr;
    }

    public function productlist() {
        $activeMachine = $this->getActiveMachine();
        $_SESSION['url'] = 'shopping/productlist/';
        $metaTag = '';
        //$_SESSION['internal'] = FALSE;

        //$_SESSION['internal'] = FALSE;
        if (@$_SESSION['internal'] === TRUE && false) {
            $listParameters = @$_SESSION['search'];
            $_SESSION['internal'] = FALSE;
        } else {
            $listParameters = $this->uri->uri_to_assoc();
            $stringSearch = $this->uri->segment(3);
            $underscoreCheck = substr($stringSearch, -1); // returns "_"
            if ($underscoreCheck == '_')
                $stringSearch = substr($stringSearch, 0, -1);
            $pieces = explode('_', $stringSearch);
            if (empty($pieces[0]))
                redirect();
            elseif (($pieces[0] != 'featured') && ($pieces[0] != 'deal')) {
                if ($pieces[0] == 'search') {
                    $_SESSION['search'] = array();
                    foreach ($pieces as $piece) {
                        if ($piece == 'search')
                            continue;
                        $_SESSION['search']['search'][] = $piece;
                        $listParameters = @$_SESSION['search'];
                    }
                } else
                    $listParameters = $this->parts_m->createSearchParametersFromURL($pieces);
                $_SESSION['search'] = $listParameters;
            }
            if (empty($listParameters))
                redirect();
        }

        if (count($listParameters) == 1 && !empty($listParameters['brand'])) {
            $brand = $this->parts_m->getBrandById($listParameters['brand']['id']);
            redirect(site_url($brand['slug']));
        }
        // SIDEBAR ITEMS
        $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
        $this->loadSidebar('widgets/garage_v');
        // Filter options for current search

        $brandTitle = '';
        if (@$listParameters['brand']) {
            $brandTitle = $listParameters['brand']['name'] . ' ';
        }

        foreach ($listParameters as $section => &$value) {
            if (($section == 'question') || ($section == 'search')) {
                foreach ($value as &$answer)
                    $answer = strip_tags($answer);
            } elseif (is_array($value))
                $value = strip_tags($value['id']);
            else
                $value = strip_tags($value);
        }

        if (array_key_exists("category", $listParameters) && $listParameters['category'] != "") {
            jonathan_saveCategoryToStack($listParameters["category"]);
            $this->_mainData['category'] = $this->parts_m->getFilteredCategories($listParameters['category'], $listParameters);
            $categoryRec = $this->parts_m->getCategory($listParameters['category']);
            if (@$categoryRec['notice'])
                $this->_mainData['notice'] = $categoryRec['notice'];
            if (strpos($categoryRec['long_name'], ' > ') !== FALSE) {
                $pieces = explode(' > ', $categoryRec['long_name']);
                $linkArr = array($listParameters['category'] => $categoryRec['long_name']);
            } else
                $pieces = array($listParameters['category'] => $categoryRec['long_name']);

            // Set Category SEO
            $title = $brandTitle . ' ';
            if (is_null($categoryRec['title']))
                $title .= str_replace(' > ', ', ', $categoryRec['long_name']);
            else
                $title .= $categoryRec['title'];

            if (!is_null($activeMachine)) {
                $title .= ' for ' . $activeMachine['name'];
            }

            $this->setMasterPageVars('title', $title);
            if ($categoryRec['meta_tag'])
                $this->setMasterPageVars('descr', $categoryRec['meta_tag']);
            if ($categoryRec['keywords'])
                $this->setMasterPageVars('keywords', $categoryRec['keywords']);
            // End Category SEO
            $listParameters['category'] = $this->parts_m->getCategoryByName($pieces);
            $this->loadJS('<script>$( document ).ready(function() { $("#category").slideToggle("fast");});</script>');
        } else {
            $this->_mainData['category'] = $this->parts_m->getSearchCategories($listParameters);
        }

		//echo 'abcd';exit;
        $this->_mainData['brand'] = $this->parts_m->getBrands($listParameters);

        unset($this->_mainData['band']);

		if( @$listParameters['category'] ) {
			$videoParams = $listParameters['category'];
			end($videoParams);
			$endCategory = key($videoParams);
			
			$categoryVideo = $this->admin_m->getCategoryVideos($endCategory);
			$mainVideo = $mainTitle = '';
			foreach ($categoryVideo as $key => $val) {
				if ($val['ordering'] == 1) {
					$mainVideo = $val['video_url'];
					$mainTitle = $val['title'];
					unset($categoryVideo[$key]);
					break;
				}
			}
			if ($mainVideo == '') {
				$mainVideo = $categoryVideo[0];
				unset($categoryVideo[0]);
			}
			$this->_mainData['mainVideo'] = $mainVideo;
			$this->_mainData['mainTitle'] = $mainTitle;
			$this->_mainData['video'] = $categoryVideo;
		}
                
        // ACTUAL PRODUCT SEARCH IS DONE IN THIS MODEL FUNCTION
        $listParameters1 = $listParameters;
        unset($listParameters1['search']);
        if (!empty($listParameters['search'])) {
            $listParameters1['search'][] = implode(' ', $listParameters['search']);
        }
        if (@$listParameters['brand']) {
            $this->_mainData['brandMain'] = $this->parts_m->getBrand($listParameters['brand']);
        }
        $this->_mainData['band']['products'] = $this->parts_m->getSearchResults($listParameters1, $activeMachine, $this->_adpdtLimit);
        $this->_mainData['questions'] = $this->parts_m->getFilterQuestions($listParameters);
		
		// echo '<pre>';
		// print_r($this->_mainData['questions']);
		// echo '</pre>';
		
        $this->_mainData['band']['label'] = 'Search Results' . (!is_null($activeMachine) ? " for " . $activeMachine["name"] : "");
        $_SESSION['breadcrumbs'] = $listParameters;
        $this->_mainData['breadcrumbs'] = $listParameters;
        $this->_mainData['mainProductBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);

        // TOP ITEMS

        $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);

        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);

        // MAIN SECTION

        $this->_mainData['band'] = $this->parts_m->getRecentlyViewed(0, @$_SESSION['recentlyViewed'], 4);
        if (empty($this->_mainData['band'])) {
            $this->_mainData['recentlyViewedBand'] = '';
        } else {
            $this->_mainData['recentlyViewedBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);
        }

        unset($this->_mainData['band']);

        $this->_mainData['band']['products'] = $this->parts_m->getSearchResults($listParameters, $this->getActiveMachine(), NULL);
        // Created Variables for it in Category section, but calling it here to take advantage of breadcrumbs.
        $this->loadSidebar('widgets/category_filter_v_product');
        $this->loadSidebar('widgets/brand_filter_v_product');
        $this->loadSidebar('widgets/question_filter_v_product');
        // PAGINATION
        $this->_mainData['pages'] = $this->adpdtPagination($this->parts_m->getSearchCount($listParameters, $activeMachine));
        $this->_mainData['currentPage'] = 1;
        $this->_mainData['display_pages'] = $this->_pagination;
        $this->_mainData['pagination'] = $this->load->view('master/pagination/productlist_v', $this->_mainData, TRUE);
        if (@$this->_mainData['band']['products'][0]['images'][0]['path']) {
            $metaTag .= '<meta property="og:image" content="' . jsite_url('/productimages/') . $this->_mainData['band']['products'][0]['images'][0]['path'] . '"/>';
            $this->setMasterPageVars('metatag', $metaTag);
        }

        $googleAdWordsScript = '<script>
								var google_tag_params = {
								ecomm_pagetype: \'category\'
								};
								</script>';
        $this->loadJS($googleAdWordsScript);

        $this->setNav('master/navigation_v', 0);
        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
        $this->_mainData['custom_sidebar'] = true;

        $this->_mainData['new_header'] = 1;

        $getTopParentTemp = $this->uri->segment(3);
        $getTopParentTemp = explode("_", $getTopParentTemp);

        // JLB 10-18-17
        // OK, this is another retrofit. I think that the thing that is going on is that you have to work backwards through the name
        // through the navigation to get this correct thing to show up...This is still not right, but it extends with teh navigation now...
        if (isset($getTopParentTemp[0]) && $getTopParentTemp[0] != "") {

            $top_parent = -1;
            global $active_primary_navigation;

            if (!isset($active_primary_navigation)) {
                $active_primary_navigation = jonathan_prepareGlobalPrimaryNavigation();
            }

            foreach ($active_primary_navigation as $rec) {
                if ($rec["category_description"] != "") {
                    $clean_name = str_replace(" ", "-", strtolower($rec["category_description"]));
                    if ($clean_name == $getTopParentTemp[0]) {
                        $top_parent = $rec["category_id"];
                    }
                }
            }

            if ($top_parent > 0) {

                $this->_mainData['cat_header'] = 1;
                $this->_mainData['top_parent'] = $top_parent;

                /*  GETTING CATEGORIES FOR TOP NAV */
                $nav_categories_and_parent = $this->parts_m->nav_categories_and_parent(0, $top_parent);
                $this->_mainData['nav_categories'] = $nav_categories_and_parent['navCategories'];
            }
        }


        $this->setFooterView('master/footer_v.php');
        $this->renderMasterPage('master/master_v', 'info/product_list_v', $this->_mainData);
    }

    /*     * ***************************** Search Function Start ********************************* */

    public function search_product()
    {
        $metaTag = '';
        $trimmed_search = trim(str_replace(array('"', "'"), '', $_GET['search']));
        $listParameters['search'][] = $trimmed_search;
        $listParameters1['search'][] = $trimmed_search;

        global $PSTAPI;
        initializePSTAPI();

        // JLB: Bypass #1: If you enter the brand name, then go to the brand page.
        if (!array_key_exists("brand_bypass", $_GET) || $_GET["brand_bypass"] == 0) {
            $brand_match = $PSTAPI->brand()->fetch(array(
                "name" => $trimmed_search
            ), true);

            if (count($brand_match) == 1) {
                // GO TO IT...
                header("Location: " . site_url($brand_match[0]["slug"]));
                exit();
            }

            $brand_match = $PSTAPI->brand()->fetch(array(
                "title" => $trimmed_search
            ), true);

            if (count($brand_match) == 1) {
                // GO TO IT...
                header("Location: " . site_url($brand_match[0]["slug"]));
                exit();
            }
        } else {
            if (array_key_exists("brand_id", $_GET)) {
                $listParameters['brand'] = intVal($_GET['brand_id']);
                $listParameters1['brand'] = intVal($_GET['brand_id']);

            }
        }

        // JLB: Bypass #2: If you enter a product name, exactly, then you go to that product.
        $part_match = $PSTAPI->part()->fetch(array(
            "name" => $trimmed_search,
            "invisible" => 0
        ), true);


        if (count($part_match) == 1) {
            // GO TO IT...
            header("Location: " . site_url("shopping/item/" . $part_match[0]["part_id"] . "/" . tag_creating($part_match[0]["name"])));
            exit();
        }

        $_SESSION['url'] = 'shopping/search_product/';


        if (empty($listParameters) || (!array_key_exists("brand_bypass", $_GET) && (empty($_GET['search']) || $trimmed_search == ""))) {
            redirect();
        }
        $this->loadSidebar('widgets/garage_v');
        // Filter options for current search

        $this->_mainData['category'] = $this->parts_m->getSearchCategories($listParameters);
        $this->_mainData['brand'] = array_key_exists("brand_bypass", $_GET) ? array() : $this->parts_m->getBrands($listParameters);
        unset($this->_mainData['band']);

        // ACTUAL PRODUCT SEARCH IS DONE IN THIS MODEL FUNCTION
        $this->_mainData['band']['products'] = $this->parts_m->getSearchResults($listParameters1, $this->getActiveMachine(), $this->_adpdtLimit);
        //usort($this->_mainData['band']['products'], 'sortByOrder');
        $this->_mainData['questions'] = $this->parts_m->getFilterQuestions($listParameters);
        $this->_mainData['band']['label'] = 'Search Results';

        $_SESSION['breadcrumbs'] = $listParameters;

        $this->_mainData['breadcrumbs'] = $_SESSION['breadcrumbs'];
        //$_SESSION['breadcrumbs'] = array('search' => array($_GET['search']));
        //$this->_mainData['breadcrumbs'] = array('search' => array($_GET['search']));

        $this->_mainData['mainProductBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);

        // TOP ITEMS

        $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);

        $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);

        $this->loadSidebar('widgets/category_filter_v');
        $this->loadSidebar('widgets/brand_filter_v');
        $this->loadSidebar('widgets/question_filter_v');
        // PAGINATION
        $this->_mainData['pages'] = $this->adpdtPagination($this->parts_m->getSearchCount($listParameters));
        $this->_mainData['currentPage'] = 1;
        $this->_mainData['display_pages'] = $this->_pagination;
        $this->_mainData['pagination'] = $this->load->view('master/pagination/productlist_v', $this->_mainData, TRUE);
        if (@$this->_mainData['band']['products'][0]['images'][0]['path']) {
            $metaTag .= '<meta property="og:image" content="' . jsite_url('/productimages/') . $this->_mainData['band']['products'][0]['images'][0]['path'] . '"/>';
            $this->setMasterPageVars('metatag', $metaTag);
        }

        $googleAdWordsScript = '<script>
								var google_tag_params = {
								ecomm_pagetype: \'category\'
								};
								</script>';
        $this->loadJS($googleAdWordsScript);

        $this->setNav('master/navigation_v', 0);
        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');

        $this->_mainData['new_header'] = 1;


        if (array_key_exists("brand_id", $_GET)) {
            global $PSTAPI;
            initializePSTAPI();
            $brand = $PSTAPI->brand()->get($_GET["brand_id"]);
            if (!is_null($brand)) {
                $this->_mainData["brandMain"] = $brand->to_array();
                $this->_mainData["title"] = 'All ' . ($brand->get("title") != "" ? $brand->get("title") : $brand->get("name")) . ' Products ';

                $activeMachine = $this->getActiveMachine();
                if (!is_null($activeMachine)) {
                    $this->_mainData["title"] .= ' for ' . $activeMachine['name'];
                }

                $this->setMasterPageVars('title', $this->_mainData["title"]);
            }
        }

        $this->setFooterView('master/footer_v.php');
        $this->renderMasterPage('master/master_v', 'info/product_list_v', $this->_mainData);
    }

    /*     * ***************************** Search Function End ********************************* */

    /*     * ***************************** Search Function Start ********************************* */

    public function brand($brand = null, $brandId = null) {
        $_SESSION['url'] = '';
        $metaTag = '';
        $record = $this->parts_m->getBrandBySlug($brand);
        if (is_null($brand) || empty($record)) {
            // I suppose this could be a sizing chart because this is STUPID.
            $brand_sizing_chart = $this->parts_m->getBrandBySizeChart( $brand);
            if (!is_null($brand_sizing_chart) && !empty($brand_sizing_chart) && FALSE != $brand_sizing_chart) {
                $this->size_chart($brand);
            } else {
                // Just go on home.
                header("Location: " . site_url(""));
                exit();
            }
        } else {
			unset($_SESSION['search']);
			$_SESSION['search']['brand'] = array('id' => $record['brand_id'], 'name' => $record['name']);
            $listParameters = array('brand' => array('id' => $record['brand_id'], 'name' => $record['name'], 'image' => $record['image']));
            if ($record['title'] == '') {
                $record['title'] = $record['name'];
            }
            $title = $record['title'] . ' - ' . WEBSITE_NAME;
            $this->setMasterPageVars('title', $title);
            $this->setMasterPageVars('descr', $record['meta_tag']);
            $this->setMasterPageVars('keywords', $record['keywords']);

            $this->_mainData['band']['topSeller'] = $this->parts_m->getTopSellersBrand($record['brand_id'], 4);
            $this->_mainData['band']['newArrivals'] = $this->parts_m->getNewArrivalsBrand($record['brand_id'], 4);
            //$this->_mainData['topSellersBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);
            //echo '<pre>';
            //print_r( $this->_mainData['topSellersBand'] );
            //echo '</pre>';
            //$listParameters = $this->parts_m->getBrandBySlug( $brand );
            if (empty($listParameters))
                redirect();
            $brandName = $listParameters['brand']['name'];
            $brandVideo = $this->admin_m->getBrandVideos($listParameters['brand']['id']);
            $mainVideo = $mainTitle = '';
            foreach ($brandVideo as $key => $val) {
                if ($val['ordering'] == 1) {
                    $mainVideo = $val['video_url'];
                    $mainTitle = $val['title'];
                    unset($brandVideo[$key]);
                    break;
                }
            }
            if ($mainVideo == '') {
                $mainVideo = $brandVideo[0];
                unset($brandVideo[0]);
            }
            $this->_mainData['headTitle'] = $record['title'];
            $this->_mainData['notice'] = $record['notice'];
            $this->_mainData['mainVideo'] = $mainVideo;
            $this->_mainData['brand'] = $brandName;
            $this->_mainData['sizechart_status'] = $record['size_chart_status'];
            $this->_mainData['sizechart_url'] = $record['sizechart_url'];
            $this->_mainData['brandImg'] = $listParameters['brand']['image'];
            $this->_mainData['mainTitle'] = $mainTitle;
            $this->_mainData['video'] = $brandVideo;
            $this->_mainData['video'] = $brandVideo;
            if (isset($_GET['v']) && $_GET['v'] != '') {
                unset($this->_mainData['mainVideo']);
                unset($this->_mainData['video']);
            }
			$this->_mainData['machines'] = $this->parts_m->getMachinesDd();
            // SIDEBAR ITEMS
            $this->loadSidebar('widgets/garage_v');
            // Filter options for current search

            foreach ($listParameters as $section => &$value) {
                if (($section == 'question') || ($section == 'search')) {
                    foreach ($value as &$answer)
                        $answer = strip_tags($answer);
                } elseif (is_array($value))
                    $value = strip_tags($value['id']);
                else
                    $value = strip_tags($value);
            }
            $this->_mainData['category'] = $this->parts_m->newGetSearchCategoriesBrand($record['brand_id']);

            $listParameters['extra'] = 'featured';
            $featured = $this->parts_m->getSearchResults($listParameters, $this->getActiveMachine(), 1000);
            $listParameters['extra'] = 'closeout';
            $closeouts = $this->parts_m->getSearchResults($listParameters, $this->getActiveMachine(), 1000);
            unset($listParameters['extra']);

            if (isset($_GET['v']) && $_GET['v'] != '') {
                if ($_GET['v'] == 'closeout') {
                    unset($this->_mainData['band']['featured']);
                    $this->_mainData['band']['closeouts'] = $closeouts;
                } else if ($_GET['v'] == 'featured') {
                    $this->_mainData['band']['featured'] = $featured;
                    unset($this->_mainData['band']['closeouts']);
                }
            } else {
                $featured = array_slice($featured, 0, 4);
                krsort($closeouts);
                $closeouts = array_slice($closeouts, 0, 4);
                $this->_mainData['band']['featured'] = $featured;
                $this->_mainData['band']['closeouts'] = $closeouts;
            }

            //echo '<pre>';
            //print_r( $closeouts );
            //echo '</pre>';
            $this->_mainData['questions'] = $this->parts_m->getFilterQuestions($listParameters);
            $this->_mainData['band']['label'] = $brandName;
            $this->_mainData['band']['page'] = $brand;
            $_SESSION['breadcrumbs'] = $listParameters;
            $this->_mainData['breadcrumbs'] = $listParameters;
            $this->_mainData['mainProductBand'] = $this->load->view('widgets/product_band_v_new', $this->_mainData, TRUE);

            $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
            // Created Variables for it in Category section, but calling it here to take advantage of breadcrumbs.
            $this->loadSidebar('widgets/category_filter_v_brandlist');
            $this->loadSidebar('widgets/question_filter_v');
            if (@$this->_mainData['band']['products'][0]['images'][0]['path']) {
                $metaTag .= '<meta property="og:image" content="' . jsite_url('/productimages/') . $this->_mainData['band']['products'][0]['images'][0]['path'] . '"/>';
                $this->setMasterPageVars('metatag', $metaTag);
            }

            $googleAdWordsScript = '<script>
								var google_tag_params = {
								ecomm_pagetype: \'category\'
								};
								</script>';
            $this->loadJS($googleAdWordsScript);

            $this->setNav('master/navigation_v', 0);
            $this->_mainData['new_header'] = 1;
            $this->_mainData['brand_id'] = $record['brand_id'];
            $this->_mainData['brand_slug'] = $record['slug'];
            $this->_mainData['brandName'] = $record['name'];

            $this->setFooterView('master/footer_v.php');
            $this->renderMasterPage('master/master_v_brand_list', 'info/product_list_v_brand', $this->_mainData);
        }
    }

    /*     * ***************************** Brand Function End ********************************* */
    public function item($partId = NULL) {
        global $PSTAPI;
        initializePSTAPI();
        $part = $PSTAPI->part()->get($partId);
        if (!is_null($part) && $part->get("invisible") > 0) {
            $partId = null; // Make it null so we don't show this screen.
        }


        // echo '<pre>';
        // print_r($_SESSION);
        // echo '</pre>';
        //unset($_SESSION['search']['search']);
        $_SESSION['url'] = 'shopping/item/' . $partId;
        $this->setHeaderVars('<script type="text/javascript" src="' . $this->_mainData['assets'] . '/js/rating.js"></script>
		<link rel="stylesheet" type="text/css" href="' . $this->_mainData['assets'] . '/css/rating.css" />');

        if (is_null($partId) || !is_numeric($partId))
            redirect('shopping/productlist/');

        // *************ACTIVE RIDE EVALUATION ****************** //
        $this->_mainData['garageNeeded'] = FALSE;
        $this->_mainData['validRide'] = TRUE;
        $garageNeeded = $this->parts_m->validMachines($partId);
        if ($garageNeeded) {
            // If Ride is needed assume the activeMachine is not the right one.
            $this->_mainData['garageNeeded'] = TRUE;
            $this->_mainData['validRide'] = FALSE;
            if (!empty($_SESSION['garage']) && !empty($_SESSION['activeMachine'])) {
                foreach ($garageNeeded as $ride) {
                    if (($ride['model_id'] == $_SESSION['activeMachine']['model']['model_id']) && ($ride['year'] == $_SESSION['activeMachine']['year'])) {
                        $this->_mainData['validRide'] = TRUE;  // Active Machine has been verified
                        break;
                    }
                }
            }
        }
        // ******************** END ACTIVE RIDE EVALUATION ********************* //
        // *********************	 FORM SUBMISSION ********************************//
        if ($this->validateProduct() === TRUE) {
            $post = $this->input->post();
			if(@$_SESSION['garage']) {
				$garragePartNumber = $this->parts_m->validGarragePartNumber($partId, $_SESSION['garage'][$_SESSION['activeMachine']['name']]);
				if(@$garragePartNumber['partnumber'] && $garragePartNumber['partnumber'] != '' ) {
					$post['partnumber'] = $garragePartNumber['partnumber'];
				}
			}
            if (@$post['question']) {
                $post['partnumber'] = $post['question'][0];
                foreach ($post['question'] as $partnumber) {
                    $questAns = $this->parts_m->getQuestionAnswerByNumber($post['part_id'], $partnumber);
                    $post['display_name'] .= '|||' . $questAns['question'] . ' :: ' . $questAns['answer'] . '||';
                    // get answer and add it to display name
                }
                if (count($post['question']) == 1)
                    unset($post['question']);
            } else if (!array_key_exists('partnumber', $post)) {
                // If we are here, this is the source of the Phantom!
                // we better get the one part number associated with this...
                global $PSTAPI;
                initializePSTAPI();
                $partpartnumber = $PSTAPI->partPartNumber()->fetch(array("part_id" => $partId));
                if (!is_null($partpartnumber) && count($partpartnumber) > 0) {
                    $partpartnumber = $partpartnumber[0];
                    $partnumber = $PSTAPI->partNumber()->get($partpartnumber->get("partnumber_id"));
                    if (!is_null($partpartnumber)) {
                        $post['partnumber'] = $partnumber->get("partnumber");
                    }
                }
            }
            if (($this->_mainData['garageNeeded']) && ($this->_mainData['validRide']) && (@$_SESSION['garage'] )) {
                if (!is_numeric(strpos($post['display_name'], '|||Fits')))
                    $post['display_name'] .= '|||Fits :: ' . $_SESSION['activeMachine']['name'] . '||';
					$post['ftmnt'] = $_SESSION['activeMachine']['name'];
            }
            if ($post['type'] == 'cart') {  // ADD TO CART
                // JLB 09-07-18
                // They call me the fixxxer.
                // The problem we had was that the price that was coming in was of the last item added to this...which meant you would undercharge on bundles.
                // Thus, what we have to do is recompute the price part.
                // This is really and truly utterly horrible that they are even trusting input from the front end in this way.
                // The original coder should be ashamed.
                global $PSTAPI;
                initializePSTAPI();
                if (count($post["question"]) > 1) {
                    // refigure that price, kemosabe.
                    $post["price"] = $PSTAPI->partnumber()->figureSalePrice($post["question"]);
                    $post["finalPrice"] = intVal($post["qty"]) * floatVal($post["price"]); // the dumbest shittery.
                }


                $_SESSION['cart'][$post['partnumber']] = $post;
                $this->load->model('coupons_m');
                $this->coupons_m->updateCartCoupons();
                if (@$_SESSION['userRecord']['id'])
                    $this->parts_m->updateCart();
                redirect('shopping/cart');
            }
            elseif ($post['type'] == 'wishlist') { // ADD TO WISHLIST
                if (@$_SESSION['userRecord']['id']) {
                    $this->parts_m->updateWishList($post, $_SESSION['userRecord']['id']);
                    redirect('shopping/wishlist');
                } else {
                    $_SESSION['wishlist'] = $post;
                    redirect('shopping/wishlist');
                }
            }
        }

        // ********************************* END FORM SUBMISSION ********************************************** //
        // Widgets
        //$this->loadSidebar('widgets/garage_v');
        $this->_mainData['band'] = $this->parts_m->relatedProducts($partId, @$_SESSION['activeMachine']);
        $this->_mainData['part_sizechart'] = $this->parts_m->getProductSizeChartFE($partId);
        $this->loadSidebar('widgets/side_related_prod_v');
        $productVideo = $this->admin_m->getProductVideos($partId);
        $mainVideo = $mainTitle = '';
        foreach ($productVideo as $key => $val) {
            if ($val['ordering'] == 1) {
                $mainVideo = $val['video_url'];
                $mainTitle = $val['title'];
                unset($productVideo[$key]);
                break;
            }
        }
        if ($mainVideo == '') {
            $mainVideo = $productVideo[0];
            unset($productVideo[0]);
        }
        $this->_mainData['mainVideo'] = $mainVideo;
        $this->_mainData['mainTitle'] = $mainTitle;
        $this->_mainData['video'] = $productVideo;

        // Main Page Data
        if (@$_SESSION['recentlyViewed'])
            $_SESSION['recentlyViewed'] = array_reverse($_SESSION['recentlyViewed'], TRUE);
        $_SESSION['recentlyViewed'][$partId] = $partId;
        $_SESSION['recentlyViewed'] = array_reverse($_SESSION['recentlyViewed'], TRUE);
        $this->_mainData['validMachines'] = $this->parts_m->validMachines($partId, @$_SESSION['activeMachine']);
        if ($this->_mainData['garageNeeded']) {
            $this->_mainData['product'] = $this->parts_m->getProduct($partId, @$_SESSION['activeMachine']);
            $this->_mainData['validMachines'] = $this->parts_m->validMachines($partId);
            if ($this->_mainData['validRide'])
                $this->_mainData['questions'] = $this->parts_m->getProductQuestions($partId, @$_SESSION['activeMachine']);
        }
        else {
            $this->_mainData['product'] = $this->parts_m->getProduct($partId, NULL);
            $this->_mainData['questions'] = $this->parts_m->getProductQuestions($partId, NULL);
        }
        
        $stock = false;
        if (empty($this->_mainData['questions'])) {
            $stock = true;
			$this->load->model('account_m');
            $partnumber = $this->account_m->getStockByPartId($partId);
			$this->_mainData['partnumbercustom'] = $partnumber['partnumber'];
        }
		
        if (!empty($_SESSION['garage']) && !empty($this->_mainData['validMachines'])) {
            $stock = true;
        }

        if (!empty($this->_mainData['validMachines']) && empty($_SESSION['garage'])) {
            $stock = false;
        }

        $this->_mainData['stock'] = $stock;
        $this->_mainData['brandMain'] = $this->parts_m->getBrandByPart($partId);

        // Build Page    	
        $this->_mainData['band'] = $this->parts_m->getRecentlyViewed(NULL, @$_SESSION['recentlyViewed'], 4);
        if ($this->_mainData['band'])
            $this->_mainData['recentlyViewedBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);

        //BREADCRUMBS PREPPING
        $this->_mainData['breadcrumbs'] = @$_SESSION['breadcrumbs'];
        if (@$this->_mainData['breadcrumbs']['parent_category_id']) {
            $categoryId = $this->parts_m->getCategoryByPartId($partId, $this->_mainData['breadcrumbs']['parent_category_id']);
            if ($categoryId)
                $this->_mainData['breadcrumbs']['category'] = $categoryId;
            unset($this->_mainData['breadcrumbs']['parent_category_id']);
        }
        if (@$this->_mainData['breadcrumbs']['category']) {
            if (is_array($this->_mainData['breadcrumbs']['category'])) {
                foreach ($this->_mainData['breadcrumbs']['category'] as $id => &$cat) {
                    $url = $this->parts_m->categoryReturnURL($id);
                    $cat = array('name' => $cat, 'link' => $url);
                }
            } else {
                $url = $this->parts_m->categoryReturnURL($this->_mainData['breadcrumbs']['category']);
                $this->_mainData['breadcrumbs']['category'] = array('name' => $this->_mainData['breadcrumbs']['category'], 'link' => $url);
            }
        }
        if (@$this->_mainData['breadcrumbs']['brand']) {
            $url = $this->parts_m->returnBrandURL($this->_mainData['breadcrumbs']);
            $brandDtl = $this->parts_m->getBrandById($this->_mainData['breadcrumbs']['brand']);
            $this->_mainData['breadcrumbs']['brand'] = array('name' => $this->_mainData['breadcrumbs']['brand'], 'link' => $url, 'label' => $brandDtl['name'], 'slug' => $brandDtl['slug']);
        }
        if (@$this->_mainData['product']['images'][0]['path']) {
            $metaTag = '<meta property="og:image" content="' . jsite_url('/productimages/') . $this->_mainData['product']['images'][0]['path'] . '"/>';
            $this->setMasterPageVars('metatag', $metaTag);
        }
        $desc = 'I found Discount Prices on ' . $this->_mainData['product']['name'];
        $this->setMasterPageVars('descr', $desc);
        $googleAdWordsScript = '<script type="text/javascript">
								var google_tag_params = {
								ecomm_prodid: \'' . $this->_mainData['product']['partnumber'] . '\',
								ecomm_pagetype: \'Product\',
								ecomm_totalvalue: \'' . $this->_mainData['product']['price']['sale_min'] . '\'
								};
								</script>';
        $this->loadTopJS($googleAdWordsScript);
        $title = $this->_mainData['product']['name'] . " - " . WEBSITE_NAME;
        $this->setMasterPageVars('title', $title);
        // END BREADCRUMBS PREPPING

        $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
        $this->_mainData['machines'] = $this->parts_m->getMachinesDd($partId);
        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);

        /* Deciding if the page browsed innerly or from outside source */
        $category_stack = jonathan_getCategoryStack();
        $is_inside = count($category_stack) > 0 ? 1 : 0;
        $this->_mainData['is_inside'] = $is_inside;

        // JLB 10-19-17
        // This code is just insane. I'm attempting to force down order on it.
        $leftmost_favoritism = true;
        $top_parent_category_id = TOP_LEVEL_CAT_STREET_BIKES; // This is an old bias that almost completely does not matter.
        $cats = array(); // We have to get the list of category IDs, in order, so as to preserve the sizing charts.


        global $active_primary_navigation;

        if (!isset($active_primary_navigation)) {
            $active_primary_navigation = jonathan_prepareGlobalPrimaryNavigation();
        }


        // We need to set the top parent category ID to the first, leftmost one...
        $part_categories = $this->parts_m->getPartCategories($partId);

        // Now, make an LUT
        $part_cat_LUT = array();
        $part_cat_name_LUT = array();
        foreach ($part_categories as $c) {
            $part_cat_LUT[$c["category_id"]] = $c;
            $part_cat_name_LUT[$c["long_name"]] = $c["category_id"];
//            // This is a rollup. Hopefully this doesn't take too long. What we really need is a common prefix function and structure.
//            $category_genealogy = $this->parts_m->categoryLineage($c["category_id"]);
//            foreach ($category_genealogy as $cd) {
//                $part_cat_LUT[$cd["category_id"]] = $cd;
//            }
        }

        // This just means they've looked around on the website this session...
        $matching_category_id = 0;
        $matching_category = null;

        if ($is_inside > 0) {
            // So, here's what is required: I need to get the categories for this part. I need to then figure out if you looked at that category. If you did, great, we have the drill down. If you did not, I just need to fall through to leftmost favoritism.
            $match = false;

            // I think that the next one will do this same work, but will avoid the pathological cases.
//            for ($i = 0; !$match && ($i < count($category_stack)); $i++) {
//                // get the genealogy for this category
//                if (array_key_exists($category_stack[$i], $part_cat_LUT)) {
//                    // OK, we have our hit...
//                    $matching_category_id = $category_stack[$i];
//                    $matching_category = $part_cat_LUT[$matching_category_id];
//                    $match = true;
//                }
//            }

            if ($match) {
                $leftmost_favoritism = false;
            } else {
                // OK, one more try - we are going to attempt a substring match...
                $category_names = jonathan_getCategoryNames();
                $part_category_names = array_keys($part_cat_name_LUT);

                $current_match_score = -1;

                for ($i = 0; !$match && ($i < count($category_stack)); $i++) {
                    $this_category_id = $category_stack[$i];

                    if (array_key_exists($this_category_id, $category_names)) {
                        $this_category_name = strtolower($category_names[$this_category_id]);
                        // we have to go look for the substring...
                        foreach ($part_category_names as $pcn) {
                            if ($this_category_name == substr(strtolower($pcn), 0, strlen($this_category_name))) {
                                // Since we're plumbing this way, we really should go for the deepest we can go...
                                $depth_score = substr_count($pcn, ">");

                                if ($depth_score > $current_match_score) {
                                    $current_match_score = $depth_score;

                                    // we've found one...
                                    $matching_category_id = $part_cat_name_LUT[$pcn];
                                    $matching_category = $part_cat_LUT[$matching_category_id];
                                    $match = true;
                                }
                            }
                        }
                    }
                }

                if ($match) {
                    $leftmost_favoritism = false;
                }
            }
        }

        // OK, in this case, they have not been browsing around
        if ($leftmost_favoritism) {
            // I need to figure out the categories for this one and then see which ones live within the leftmost navigation.
            // OK, so this is going to be something like - GO DEEP. We've not hit a category that they were looking at, so we better GO DEEP.
            // OK, instead of making it N^2, we have to compute the scores.
            $tld_scores = array();
            $tld_deep_choice = array();
            for ($i = 0; $i < count($part_categories); $i++) {
                $pc = $part_categories[$i];
                $top_category = strtolower(trim(substr($pc["long_name"], 0, strpos($pc["long_name"], ">") - 1)));
                $depth_score = substr_count($pc["long_name"], ">");
                if (!array_key_exists($top_category, $tld_scores)) {
                    $tld_scores[$top_category] = 0;
                }

                if ($depth_score > $tld_scores[$top_category]) {
                    $tld_scores[$top_category] = $depth_score;
                    $tld_deep_choice[$top_category] = $pc;
                }
            }

            // OK, now we should have scores, we should be able to do a left-to-right match.
            $match = false;

            for ($i = 0; !$match && $i < count($active_primary_navigation); $i++) {
                // OK, we're looking for our choice...
                $candidate_label = strtolower(trim($active_primary_navigation[$i]["category_description"]));
                if ($candidate_label != "") {
                    if (array_key_exists($candidate_label, $tld_scores) && $tld_scores[$candidate_label] > 0) {
                        // OK, great.
                        $match = true;
                        $matching_category = $tld_deep_choice[$candidate_label];
                        $matching_category_id = $matching_category["category_id"];
                    }
                }
            }

        }

        // OK, now that we have a category in hand, $matching_category_id, we can roll up to generate the whole sequence of categories for the purpose of making breadcrumbs.
        // Note that this means we are always going to have a list of categories...
        $this->_mainData["breadCrumbCategories"] = array_reverse($this->parts_m->categoryLineage($matching_category_id));

        /*  GETTING CATEGORIES FOR TOP NAV */
        if (($c = count($this->_mainData["breadCrumbCategories"])) > 0) {
            $top_parent_category_id = $this->_mainData["breadCrumbCategories"][0]["category_id"];
            foreach ($this->_mainData["breadCrumbCategories"] as $cat) {
                $cats[] = $cat["category_id"];
            }
        }
        $nav_categories_and_parent = $this->parts_m->nav_categories_and_parent($partId, $top_parent_category_id);
        $this->_mainData['nav_categories'] = $nav_categories_and_parent['navCategories'];
        $this->_mainData['top_parent'] = $top_parent_category_id;

        // we have to get the category name...
        foreach ($active_primary_navigation as $a) {
            if ($a["category_id"] == $top_parent_category_id) {
                $this->_mainData['catd'] = $a["category_description"];
            }
        }

        // Because the sizing charts are truly a glittering jewel of how not to code, we need all the cats.
        $cats = array_keys($part_cat_LUT);

        if (count($cats) > 0) {
            $this->_mainData['sizeChart'] = $this->parts_m->getSizeChartByCategory($cats, $this->_mainData['brandMain']['brand_id'], $partId);
        }

        $this->setNav('master/navigation_v', 0);
        $this->setFooterView('master/footer_v.php');

        $this->renderMasterPage('master/master_v_new', 'account/shopping_v_new', $this->_mainData);
    }

    public function brands() {
        unset($_SESSION['search']);
        $brands = $this->parts_m->getBrands();
        $featured = $brand = array();

        foreach ($brands as $key => $val) {
            if ($val['featured'] == 1) {
                $featured[$key] = $val;
            }
            $brand[strtoupper($val['name'][0])][$key] = $val;
        }
        $this->load->model('pages_m');
        $this->_mainData['pageRec'] = $this->pages_m->getPageRec(12);
        $this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
        $this->setMasterPageVars('metatag', ''); // JLB - I removed msvalidate.01 from here because I think the new topheader handles it.
        $this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
        $this->setMasterPageVars('title', @$this->_mainData['pageRec']['title'] . ' - ' . WEBSITE_NAME);
        $this->_mainData['title1'] = $this->_mainData['pageRec']['title'];
        $this->_mainData['textboxes'] = $this->pages_m->getTextBoxes(12);

        $this->_mainData['brands'] = $brand;
        $this->_mainData['featured'] = $featured;
        $this->setFooterView('master/footer_v.php');
        $this->renderMasterPage('master/master_v_brand', 'info/brand_list_v', $this->_mainData);
    }

    public function size_chart($slug = null) {
        $brand = $this->parts_m->getBrandBySizeChart( $slug );
        
        $title = $brand['name'] . " Sizing Charts - " . WEBSITE_NAME;
        $this->setMasterPageVars('title', $title);

        $this->_mainData['brand'] = $brand;
        $this->_mainData['sizeChart'] = $this->parts_m->getSizeChart( $brand['brand_id'] );
        $this->setFooterView('master/footer_v.php');
        $this->renderMasterPage('master/master_v_brand', 'info/size_chart_v', $this->_mainData);
    }

	public function getCustomProduct() {
		$this->parts_m->customProductSorting();
	}
}
