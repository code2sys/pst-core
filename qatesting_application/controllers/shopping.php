<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Shopping extends Master_Controller {

  protected $_pagination = 6;
  protected $_adpdtLimit = 48;

  	function __construct()
	{
		parent::__construct();
		$this->load->model('pages_m');
		$this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
		
		$this->load->model('parts_m');
		$this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
    	$this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
		//$this->output->enable_profiler(TRUE);
	}
  
  	private function validateProduct()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('part_id', 'Part Id', 'required|exists[part.part_id]|xss_clean');
		$this->form_validation->set_rules('question', 'Question', 'xss_clean');
		$this->form_validation->set_rules('qty', 'Qty', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('type', 'Type', 'required|xss_clean');
		$this->form_validation->set_rules('images', 'Image', 'xss_clean');
		$this->form_validation->set_rules('display_name', 'Name', 'required|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateAdPdtPageBundle()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('page', 'Page', 'numeric|xss_clean');
		return $this->form_validation->run();
	}
	
	private function adpdtPagination($count)
	{
		$pages = 0;
		if(is_numeric($count))
			$pages = ceil($count / $this->_adpdtLimit); // Number of records per page
		if($pages < 0)
			$pages = 0;
		return $pages;
	}
	public function generateProductListPaginate($direction = NULL)
	{
		if(is_numeric(@$_POST['page']))
		{
			$this->_mainData['pages'] = $this->adpdtPagination($this->parts_m->getSearchCount($_SESSION['breadcrumbs']));
			$this->_mainData['currentPage'] = ($direction == 'up') ? (@$_POST['page'] + ($this->_pagination * 2)) : (@$_POST['page'] - ($this->_pagination * 2));
			$this->_mainData['display_pages'] = $this->_pagination;
			$pagination = $this->load->view('master/pagination/productlist_v', $this->_mainData, TRUE);
		}
		if(@$_POST['ajax'])
			echo @$pagination;
		else
			return @$pagination;	
	}
	
	public function generateAdPdtListTable($order = 'name', $dir = 'DESC', $page = 1)
	{
		if(@$_POST['ajax'] && ($this->validateAdPdtPageBundle() !== FALSE))// If form validation passes use passed sorting
		{
			$page = $this->input->post('page');
			$filter = $_SESSION['breadcrumbs'];
		}
		$order = $order . ' ' . $dir;
		$filter = ($filter == 'NULL') ? NULL : $filter;
		$offset = ($page - 1) * $this->_adpdtLimit;
		$this->_mainData['band']['label'] = 'Search Results';
		$this->_mainData['band']['products'] = $this->parts_m->getSearchResults($filter, $this->_adpdtLimit, $offset);
		$tableView = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);
		if(@$_POST['ajax'])
		{
			echo $tableView;
		}
		else
			return $tableView;	  	
	}
    
  	public function index($cat = 'ES', $sub = NULL)
	{

	  	$this->_mainData['cat'] = $cat;
	  	$this->_mainData['openCat'] = $cat;
	  
	  	$this->_mainData['productTable'] = $this->product_table($cat, $sub);	
	  	$this->_mainData['pagination'] = $this->pagination();	
		$this->_mainData['categories'] = $this->products_m->getCategories(TRUE);
		
		$this->_mainData['subCategories'] = $this->products_m->getSubCategories($cat);
		$this->_mainData['subCategory'] = $sub;
		$this->_mainData['sidebar'] = $this->load->view('account/category_sidebar_v', $this->_mainData, TRUE);
		
		$this->_mainData['shoppingCart'] = $this->generateShoppingCart();
		
		
		$this->setNav('master/navigation_v', 1);
		$this->setFooterView('master/footer_v.php');
		$this->renderMasterPage('master/master_v', 'account/shopping_v', $this->_mainData);	
  }
  
  	public function cart()
	{
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
		$this->_mainData['new_header']  = 1;

		$this->setNav('master/navigation_v', 2);
		$this->setFooterView('master/footer_v.php');
		$this->renderMasterPage('master/master_v', 'account/cart_v_new', $this->_mainData);
	}
	
	public function wishlist()
	{
		$_SESSION['url'] = 'shopping/wishlist';
		if(!@$_SESSION['userRecord']['id'])
			redirect('welcome/new_account');
		
		if(@$_SESSION['wishlist'])
		{
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
		$this->_mainData['new_header']  = 1;
    	
		$this->setNav('master/navigation_v', 0);
		
		$this->setFooterView('master/footer_v.php');
		$this->renderMasterPage('master/master_v', 'info/product_list_v', $this->_mainData);
	}
	
	public function remove_wish_item($id)
	{
		if(is_numeric($id))
		{
			$this->parts_m->removeWishListItem($id);
		}
		redirect('shopping/wishlist');
	}
	
	public function check_value(&$value) 
	{
    	$value = strip_tags($value);
	}
	
	private function array_map_r( $func, $arr )
	{
	    $newArr = array();
	
	    foreach( $arr as $key => $value )
	    {
	        $newArr[ $key ] = ( is_array( $value ) ? array_map_r( $func, $value ) : ( is_array($func) ? call_user_func_array($func, $value) : $func( $value ) ) );
	    }
	
	    return $newArr;
	}
	
	public function productlist()
	{
		$_SESSION['url'] = 'shopping/productlist/';
		$metaTag = '';
		if(@$_SESSION['internal'] === TRUE)
		{
			$listParameters = @$_SESSION['search'];
			$_SESSION['internal'] = FALSE;
		}
		else
		{
			$listParameters = $this->uri->uri_to_assoc();
			$stringSearch = $this->uri->segment(3);
			$underscoreCheck = substr($stringSearch, -1); // returns "_"
			if($underscoreCheck == '_')
				$stringSearch = substr($stringSearch, 0, -1);
			$pieces = explode('_', $stringSearch);
			if(empty($pieces[0]))
				redirect();
			elseif(($pieces[0] != 'featured') && ($pieces[0] != 'deal') )
			{
				if($pieces[0] == 'search')
				{
					$_SESSION['search'] = array();
					foreach($pieces as $piece)
					{
						if($piece == 'search')
							continue;
						$_SESSION['search']['search'][] = $piece;
						$listParameters = @$_SESSION['search'];
					}
				}
				else
					$listParameters = $this->parts_m->createSearchParametersFromURL($pieces);
				$_SESSION['search'] = $listParameters;
			}
			if(empty($listParameters))
				redirect();
			
		}
		// SIDEBAR ITEMS
		$this->loadSidebar('widgets/garage_v');
		// Filter options for current search
		
		foreach($listParameters as $section => &$value)
		{
			if(($section == 'question') || ($section == 'search'))
			{
				foreach($value as &$answer)
					$answer = strip_tags($answer);

			}
			elseif(is_array($value))
				$value = strip_tags($value['id']);
			else
				$value = strip_tags($value);
		}


		if(@$listParameters['category'])
		{
			$this->_mainData['category'] = $this->parts_m->getFilteredCategories($listParameters['category'], $listParameters);
			$categoryRec = $this->parts_m->getCategory($listParameters['category']);
			if(@$categoryRec['notice'])
				$this->_mainData['notice'] = $categoryRec['notice'];
			if(strpos($categoryRec['long_name'], ' > ') !== FALSE)
    		{
				$pieces = explode(' > ', $categoryRec['long_name']);
				$linkArr = array($listParameters['category'] => $categoryRec['long_name']);
			}
			else
				$pieces = array($listParameters['category'] => $categoryRec['long_name']);
				
			// Set Category SEO
			$title = '';
			if(is_null($categoryRec['title']))
				$title .= str_replace(' > ', ', ', $categoryRec['long_name']);
			else
				$title .= $categoryRec['title'];
			$this->setMasterPageVars('title', $title);
			if($categoryRec['meta_tag'])
				$this->setMasterPageVars('descr', $categoryRec['meta_tag']);
			if($categoryRec['keywords'])
				$this->setMasterPageVars('keywords', $categoryRec['keywords']);
			// End Category SEO
			$listParameters['category'] = $this->parts_m->getCategoryByName($pieces);
			$this->loadJS('<script>$( document ).ready(function() { $("#category").slideToggle("fast");});</script>');
		}
		else
		{
			$this->_mainData['category'] = $this->parts_m->getSearchCategories($listParameters);
		}

		$this->_mainData['brand'] = $this->parts_m->getBrands($listParameters);
		
		unset($this->_mainData['band']);
		
				// ACTUAL PRODUCT SEARCH IS DONE IN THIS MODEL FUNCTION
		$this->_mainData['band']['products'] = $this->parts_m->getSearchResults($listParameters, $this->_adpdtLimit);
		$this->_mainData['questions'] = $this->parts_m->getFilterQuestions($listParameters);
		$this->_mainData['band']['label'] = 'Search Results';
		$_SESSION['breadcrumbs'] = $listParameters;
		$this->_mainData['breadcrumbs'] = $listParameters;
		$this->_mainData['mainProductBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);
		
		// TOP ITEMS
		
		$this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
    	$this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
    	
		$this->_mainData['machines'] = $this->parts_m->getMachinesDd();
		$this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
				
		// MAIN SECTION
		
		$this->_mainData['band'] = $this->parts_m->getRecentlyViewed(0,  @$_SESSION['recentlyViewed'], 4);
		$this->_mainData['recentlyViewedBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);
		
		unset($this->_mainData['band']);
		
		$this->_mainData['band']['products'] = $this->parts_m->getSearchResults($listParameters, NULL);
		// Created Variables for it in Category section, but calling it here to take advantage of breadcrumbs.
		$this->loadSidebar('widgets/category_filter_v');
		$this->loadSidebar('widgets/brand_filter_v');	
		$this->loadSidebar('widgets/question_filter_v');	
		// PAGINATION
		$this->_mainData['pages'] = $this->adpdtPagination($this->parts_m->getSearchCount($listParameters));
		$this->_mainData['currentPage'] = 1;
		$this->_mainData['display_pages'] = $this->_pagination;
		$this->_mainData['pagination'] = $this->load->view('master/pagination/productlist_v', $this->_mainData, TRUE);
		if(@$this->_mainData['band']['products'][0]['images'][0]['path'])
    	{
			$metaTag .= '<meta property="og:image" content="' . site_url('/productimages/'.$this->_mainData['band']['products'][0]['images'][0]['path']).'"/>';
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
		
		$this->_mainData['new_header']  = 1;
		
		$getTopParentTemp = $this->uri->segment(3);
		$getTopParentTemp = explode("_", $getTopParentTemp);

		if( isset($getTopParentTemp[0]) && $getTopParentTemp[0]== 'dirt-bike-parts' || $getTopParentTemp[0]== 'atv-parts' ||
		 	$getTopParentTemp[0]== 'street-bike-parts' || $getTopParentTemp[0]== 'utv-parts' ){

				$top_parent = TOP_LEVEL_CAT_DIRT_BIKES;
				
				if($getTopParentTemp[0]=="street-bike-parts"){
					$top_parent = TOP_LEVEL_CAT_STREET_BIKES;
				}else if($getTopParentTemp[0]=="atv-parts"){
					$top_parent = TOP_LEVEL_CAT_ATV_PARTS;
				}else if($getTopParentTemp[0]=="utv-parts"){
					$top_parent = TOP_LEVEL_CAT_UTV_PARTS;
				}
				
				$this->_mainData['cat_header']  = 1;
				$this->_mainData['top_parent']  = $top_parent;
				
				/*  GETTING CATEGORIES FOR TOP NAV*/
				$nav_categories_and_parent = $this->parts_m->nav_categories_and_parent(0, $top_parent);
				$this->_mainData['nav_categories'] = $nav_categories_and_parent['navCategories'];
			
		}
		
		
		$this->setFooterView('master/footer_v.php');
		$this->renderMasterPage('master/master_v', 'info/product_list_v', $this->_mainData);
	}
	
	public function item($partId = NULL)
	{
		$_SESSION['url'] = 'shopping/item/'.$partId;
		$this->setHeaderVars('<script type="text/javascript" src="'.$this->_mainData['assets'].'/js/rating.js"></script>
		<link rel="stylesheet" type="text/css" href="'.$this->_mainData['assets'].'/css/rating.css" />');
											
		if(is_null($partId) || !is_numeric($partId))
			redirect('shopping/productlist/');
			
		// *************ACTIVE RIDE EVALUATION ****************** //
		$this->_mainData['garageNeeded'] = FALSE;
    	$this->_mainData['validRide'] = TRUE;
    	$garageNeeded = $this->parts_m->validMachines($partId);
    	if($garageNeeded)
    	{
    		// If Ride is needed assume the activeMachine is not the right one.
    		$this->_mainData['garageNeeded'] = TRUE;
    		$this->_mainData['validRide'] = FALSE;
    		if(!empty($_SESSION['garage']) && !empty($_SESSION['activeMachine']))
    		{
		    	foreach($garageNeeded as $ride)
		    	{
				    if(($ride['model_id'] == $_SESSION['activeMachine']['model']['model_id']) && ($ride['year'] == $_SESSION['activeMachine']['year']))
				    {
					    $this->_mainData['validRide'] = TRUE;  // Active Machine has been verified
					    break;
				    }
		    	}
	    	}
    	}
    	// ******************** END ACTIVE RIDE EVALUATION ********************* //
    	
    	// *********************	 FORM SUBMISSION ********************************//
		if($this->validateProduct() === TRUE)
		{
			$post = $this->input->post();
			if(@$post['question'])
			{
				$post['partnumber'] = $post['question'][0];
				foreach($post['question'] as $partnumber)
				{
					$questAns = $this->parts_m->getQuestionAnswerByNumber($partnumber);
					$post['display_name'] .= '|||'.$questAns['question'].' :: '. $questAns['answer'] . '||';
					// get answer and add it to display name
				}
				if(count($post['question']) == 1)
				unset($post['question']);
			}
			if(($this->_mainData['garageNeeded']) && ($this->_mainData['validRide']) && (@$_SESSION['garage'] ))
			{	
				if(!is_numeric(strpos($post['display_name'], '|||Fits')))
				$post['display_name'] .= '|||Fits :: '. $_SESSION['activeMachine']['name'].'||';
			}
			if($post['type'] == 'cart')  // ADD TO CART
			{
				$_SESSION['cart'][$post['partnumber']] = $post;
				if(@$_SESSION['userRecord']['id'])
					$this->parts_m->updateCart();
				redirect('shopping/cart');
			} 
			elseif($post['type'] == 'wishlist') // ADD TO WISHLIST
			{
				if(@$_SESSION['userRecord']['id'])
				{
					$this->parts_m->updateWishList($post, $_SESSION['userRecord']['id']);
					redirect('shopping/wishlist');
				}
				else
				{
					$_SESSION['wishlist'] = $post;
					redirect('shopping/wishlist');
				}
			}	
		}
		
		// ********************************* END FORM SUBMISSION ********************************************** //
		
		// Widgets
		$this->loadSidebar('widgets/garage_v');
		$this->_mainData['band'] = $this->parts_m->relatedProducts($partId, @$_SESSION['activeMachine']);
		$this->loadSidebar('widgets/side_related_prod_v');
		
		// Main Page Data
		if(@$_SESSION['recentlyViewed'] )
			$_SESSION['recentlyViewed'] = array_reverse( $_SESSION['recentlyViewed'], TRUE );
		$_SESSION['recentlyViewed'][$partId] = $partId;
		$_SESSION['recentlyViewed'] = array_reverse( $_SESSION['recentlyViewed'], TRUE );
		$this->_mainData['validMachines'] = $this->parts_m->validMachines($partId, @$_SESSION['activeMachine']);
		if($this->_mainData['garageNeeded'])
		{	
			$this->_mainData['product'] = $this->parts_m->getProduct($partId, @$_SESSION['activeMachine']);
			if($this->_mainData['validRide'] )
	    		$this->_mainData['questions'] = $this->parts_m->getProductQuestions($partId, @$_SESSION['activeMachine']);
		}
		else
		{
			$this->_mainData['product'] = $this->parts_m->getProduct($partId, NULL);
	    	$this->_mainData['questions'] = $this->parts_m->getProductQuestions($partId, NULL);
		}
		// Build Page    	
    	$this->_mainData['band'] = $this->parts_m->getRecentlyViewed(NULL, @$_SESSION['recentlyViewed'], 4);
    	if($this->_mainData['band'])
    		$this->_mainData['recentlyViewedBand'] = $this->load->view('widgets/product_band_v', $this->_mainData, TRUE);
    		
    	//BREADCRUMBS PREPPING
    	$this->_mainData['breadcrumbs'] = @$_SESSION['breadcrumbs'];
    	if(@$this->_mainData['breadcrumbs']['parent_category_id'])
    	{
    		 $categoryId = $this->parts_m->getCategoryByPartId($partId, $this->_mainData['breadcrumbs']['parent_category_id']);
    		 if($categoryId)
    		 	$this->_mainData['breadcrumbs']['category'] = $categoryId;
    		 unset($this->_mainData['breadcrumbs']['parent_category_id']);
    	}
    	if(@$this->_mainData['breadcrumbs']['category'])
    	{
	    	if(is_array($this->_mainData['breadcrumbs']['category']))
	    	{
		    	foreach($this->_mainData['breadcrumbs']['category'] as $id => &$cat)
		    	{
			    	$url = $this->parts_m->categoryReturnURL($id);
			    	$cat = array('name' => $cat, 'link' => $url);
		    	}
	    	}
	    	else
	    	{
		    	$url = $this->parts_m->categoryReturnURL($this->_mainData['breadcrumbs']['category']);
		    	$this->_mainData['breadcrumbs']['category'] = array('name' => $this->_mainData['breadcrumbs']['category'], 'link' => $url);
	    	}
    	}
    	if(@$this->_mainData['breadcrumbs']['brand'])
    	{
	    	$url = $this->parts_m->returnBrandURL($this->_mainData['breadcrumbs']);
	    	$this->_mainData['breadcrumbs']['brand'] = array('name' => $this->_mainData['breadcrumbs']['brand'], 'link' => $url);
    	}
    	if(@$this->_mainData['product']['images'][0]['path'])
    	{
	    	$metaTag = '<meta property="og:image" content="' . site_url('/productimages/'.$this->_mainData['product']['images'][0]['path']) . '"/>';
    		$this->setMasterPageVars('metatag', $metaTag);
    	}
    	$desc = 'I found Discount Prices on '.$this->_mainData['product']['name'];
		$this->setMasterPageVars('descr', $desc);
		$googleAdWordsScript = '<script type="text/javascript">
								var google_tag_params = {
								ecomm_prodid: \''.$this->_mainData['product']['partnumber'].'\',
								ecomm_pagetype: \'Product\',
								ecomm_totalvalue: \''.$this->_mainData['product']['price']['sale_min'].'\'
								};
								</script>';
		$this->loadTopJS($googleAdWordsScript);
		$title = $this->_mainData['product']['name']." - " . WEBSITE_NAME;
		$this->setMasterPageVars('title', $title);
    	// END BREADCRUMBS PREPPING
    				
		$this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
    	$this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
    	$this->_mainData['machines'] = $this->parts_m->getMachinesDd($partId);
    	$this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
		
		/*Deciding if the page browsed innerly or from outside source*/
		$is_inside = 0;
		$referer = ( isset($_SERVER['HTTP_REFERER']) ) ? $_SERVER['HTTP_REFERER']: '';
		if (strpos($referer,strtolower(WEBSITE_HOSTNAME)) !== false) {
			$is_inside = 1;
		}
		
		$this->_mainData['is_inside'] = $is_inside;
		
		/*	MAKING DYNAMIC BREADCRUMB, IT WILL BE USED IF PRODUCT WILL BE OPENED FROM EXTERNAL URL*/
		$this->_mainData['secondBreadCrumb'] = $this->parts_m->getSecondBreadCrumb($partId);
		
		
		/*Preparing Top Parent Category*/
		$parentt = "";
		
		if( $is_inside == 1 && !empty($this->_mainData['breadcrumbs']) ){
			
			$session_based_breadcrumb = $this->_mainData['breadcrumbs'];
			if( !empty($session_based_breadcrumb['category']) ){
				
				if( !empty($session_based_breadcrumb['category'][TOP_LEVEL_CAT_STREET_BIKES]) ){
					$parentt = TOP_LEVEL_CAT_STREET_BIKES;
				}elseif( !empty($session_based_breadcrumb['category'][TOP_LEVEL_CAT_DIRT_BIKES]) ){
					$parentt = TOP_LEVEL_CAT_DIRT_BIKES;
				}elseif( !empty($session_based_breadcrumb['category'][TOP_LEVEL_CAT_ATV_PARTS]) ){
					$parentt = TOP_LEVEL_CAT_ATV_PARTS;
				}elseif( !empty($session_based_breadcrumb['category'][TOP_LEVEL_CAT_UTV_PARTS]) ){
					$parentt = TOP_LEVEL_CAT_UTV_PARTS;
				}
				
			}
			
		}
		
		if( empty($parentt) && !empty($this->_mainData['secondBreadCrumb'][0]['id']) ){
			$parentt = $this->_mainData['secondBreadCrumb'][0]['id']; // Getting top level Category from second breadcrumb.
		}
		
		/*  GETTING CATEGORIES FOR TOP NAV*/
		$nav_categories_and_parent = $this->parts_m->nav_categories_and_parent($partId, (!empty($parentt)) ? $parentt : 0);
		$this->_mainData['nav_categories'] = $nav_categories_and_parent['navCategories'];
		$this->_mainData['top_parent'] = (!empty($parentt)) ? $parentt : TOP_LEVEL_CAT_STREET_BIKES;
		
		$this->setNav('master/navigation_v', 0);
		$this->setFooterView('master/footer_v.php');
		$this->renderMasterPage('master/master_v_new', 'account/shopping_v_new', $this->_mainData);
	}
	

}