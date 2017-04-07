<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Admin extends Master_Controller {

	protected $_adpdtLimit = 50;
	protected $_adusrLimit = 49;
	protected $_adOrderLimit = 50;
	protected $_pagination = 6;

 	function __construct()
	{
		parent::__construct();
		if(!@$_SESSION['userRecord']['admin'])
			redirect('welcome');
		$this->setFooterView('admin/footer_v.php');
		$this->load->model('admin_m');
		//$this->output->enable_profiler(TRUE);
  	}
  	
  	/******************************************** VALIDATION *************************************************************/
  
	private function validateEditCategory()
	{
		$this->load->library('form_validation');
	    $this->form_validation->set_rules('category_id', 'Category Id', 'xss_clean');
	    $this->form_validation->set_rules('parent_category_id', 'Parent Category', 'required|xss_clean');
	    $this->form_validation->set_rules('active', 'Active', 'xss_clean');
	    $this->form_validation->set_rules('featured', 'Featured', 'xss_clean');
	    $this->form_validation->set_rules('name', 'Name', 'xss_clean');
	    $this->form_validation->set_rules('title', 'Title', 'xss_clean');
	    $this->form_validation->set_rules('meta_tag', 'Meta Tag', 'xss_clean');
	    $this->form_validation->set_rules('keywords', 'Keywords', 'xss_clean');   
	    $this->form_validation->set_rules('mark-up', 'Mark-up', 'integer|xss_clean');  
	    $this->form_validation->set_rules('notice', 'Notice', 'xss_clean'); 
		return $this->form_validation->run();
	}
	
	private function validateEditBrand()
	{
		$this->load->library('form_validation');
	    $this->form_validation->set_rules('brand_id', 'Brand Id', 'xss_clean');
	    $this->form_validation->set_rules('active', 'Active', 'xss_clean');
	    $this->form_validation->set_rules('featured', 'Featured', 'xss_clean');
	    $this->form_validation->set_rules('exclude_market_place', 'exclude_market_place', 'xss_clean');
	    $this->form_validation->set_rules('closeout_market_place', 'closeout_market_place', 'xss_clean');
	    $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
	    $this->form_validation->set_rules('meta_tag', 'Meta Tag', 'xss_clean');
	    $this->form_validation->set_rules('keywords', 'Keywords', 'xss_clean');   
	    $this->form_validation->set_rules('mark-up', 'Mark-up', 'is_natural|xss_clean');  
	    $this->form_validation->set_rules('map_percent', 'MAP Pricing', 'integer|xss_clean');  
		return $this->form_validation->run();
	}
  
	private function validateEditTaxes()
	{
		$this->load->library('form_validation');
		$taxes= $this->input->post('id');
		if(!empty($taxes))
		{
			foreach($taxes as $key => $id)
			{
				$this->form_validation->set_rules('id['.$key.']', 'Id # '.($key + 1), 'required|xss_clean');
				$this->form_validation->set_rules('active['.$key.']', 'Active # '.($key +1), 'xss_clean');
				$this->form_validation->set_rules('percentage['.$key.']', 'Percentage # '.($key + 1), 'xss_clean');
				$this->form_validation->set_rules('tax_value['.$key.']', 'Value # '.($key + 1), 'required|is_numeric|xss_clean');
			}
		}
		return $this->form_validation->run();
	}
	
	private function validateEditDistributor()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
		$this->form_validation->set_rules('username', 'Username', 'required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateEditShippingRules()
	{
		$this->load->library('form_validation');
		$formFields = $this->input->post();
		if(@$formFields['edit'])
		{
			$this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
  		}

		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
    	$this->form_validation->set_rules('weight_low', 'Weight Low', 'is_numeric|xss_clean');
    	$this->form_validation->set_rules('weight_high', 'Weight High', 'is_numeric|xss_clean');
    	$this->form_validation->set_rules('price_low', 'Price Low', 'is_numeric|xss_clean');
    	$this->form_validation->set_rules('price_high', 'Price High', 'is_numeric|xss_clean');
    	$this->form_validation->set_rules('width_low', 'Width Low', 'is_numeric|xss_clean');
    	$this->form_validation->set_rules('width_high', 'Width High', 'is_numeric|xss_clean');
    	$this->form_validation->set_rules('height_low', 'Height Low', 'is_numeric|xss_clean');
    	$this->form_validation->set_rules('height_high', 'Height High', 'is_numeric|xss_clean');
    	$this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
    	$this->form_validation->set_rules('active', 'Active', 'xss_clean');
    	$this->form_validation->set_rules('value', 'Price', 'requiredis_numeric|xss_clean');
		return $this->form_validation->run();
	}
  
  private function validateSearch()
  {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('qty', 'Qty', 'required|xss_clean');
		return $this->form_validation->run();
  }
  
   private function validateSku()
  {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('qty', 'Qty', 'required|xss_clean');
		$this->form_validation->set_rules('sku', 'partnumber', 'required|xss_clean');
		return $this->form_validation->run();
  }
  
  private function validateImage()
  {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('table', 'Table', 'required|xss_clean');
		$this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
		return $this->form_validation->run();
  }

  private function validateEditUser()
  {
    	$this->load->library('form_validation');
		$post = $this->input->post();
		$user = @$post['id'];
		if(!empty($user))
		{
	  		foreach($user as $key => $id)
	  		{
	    		$this->form_validation->set_rules('id['.$key.']', 'Id '.$key, 'xss_clean');
	    		$this->form_validation->set_rules('wholesaler['.$key.']', 'Wholesaler '.$key, 'xss_clean');
	    		$this->form_validation->set_rules('no_tax['.$key.']', 'No Tax '.$key, 'xss_clean');
	  		}
		}
		return $this->form_validation->run();
  }
	
	function _validateDate($date, $type)
	{
		if($date)
		{
		    $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
		    if(!preg_match($date_regex, $date))
		    {
		      $this->form_validation->set_message('_validateDate', 'Please provide a valid '.$type.' date.');  
		      return FALSE;
		    }
		    return $date;    
	    }
	    return TRUE; // Validate Requiment in set_rules line.
	}
	
	private function validateCoupon()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('couponCode', 'Coupon Code', 'required|xss_clean');
		$this->form_validation->set_rules('startDate', 'Start Date', 'callback__validateDate[start]|xss_clean');
		$this->form_validation->set_rules('endDate', 'End Date', 'callback__validateDate[end]|xss_clean');
		$this->form_validation->set_rules('totalUses', 'Total Uses', 'integer|xss_clean');
		$this->form_validation->set_rules('type', 'Percentage or Set Value', 'required|xss_clean');
		$this->form_validation->set_rules('amount', 'Amount', 'required|xss_clean');
		$this->form_validation->set_rules('associatedProductSKU', 'Associated Product SKU', 'xss_clean');
		$this->load->model('coupons_m');
		$specialConstraints = $this->coupons_m->getSpecialConstraints();
		if($specialConstraints)
		{
			foreach($specialConstraints as $opt)
			{
				$this->form_validation->set_rules($opt['ruleName'], $opt['displayName'], 'xss_clean');
			}
		}
		return $this->form_validation->run();
	}
	
	private function validateShipping()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('zip', 'Zip/Postal Code', 'required|xss_clean');
		$this->form_validation->set_rules('weight', 'Weight', 'required|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'xss_clean');
		
		return $this->form_validation->run();
	}
	
	private function validateAdPdtPageBundle()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('order', 'Order', 'xss_clean');
		$this->form_validation->set_rules('page', 'Page', 'numeric|xss_clean');
		$this->form_validation->set_rules('filter', 'Filter', 'xss_clean');
		$this->form_validation->set_rules('dir', 'Direction', 'xss_clean');
		$this->form_validation->set_rules('cat', 'Category', 'xss_clean');
		return $this->form_validation->run();
	}
	
	 private function validateProduct()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('sku', 'SKU', 'required|is_unique[product.sku]|xss_clean');
		$this->form_validation->set_rules('display_name', 'Product Name', 'required|xss_clean');
		$this->form_validation->set_rules('wholesale', 'Wholesale', 'required|xss_clean');
		$this->form_validation->set_rules('retail', 'Retail', 'required|xss_clean');
		$this->form_validation->set_rules('sale', 'Sales Price', 'xss_clean');
		$this->form_validation->set_rules('saleWs', 'Wholesales Sales Price', 'xss_clean');
		$this->form_validation->set_rules('weight', 'Weight', 'required|xss_clean');
		$this->form_validation->set_rules('description', 'Desc', 'xss_clean');
		$this->form_validation->set_rules('category', 'Category', 'required|xss_clean');
		$this->form_validation->set_rules('code', 'code', 'xss_clean');
		$this->form_validation->set_rules('taxable', 'Taxable', 'xss_clean');
		$this->form_validation->set_rules('onSale', 'onSale', 'xss_clean');
		$this->form_validation->set_rules('active', 'Active', 'xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateProfile()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('deal', 'Deal Percentage', 'integer|xss_clean');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone', 'required|xss_clean');
		$this->form_validation->set_rules('street_address', 'Street Address', 'required|max_length[40]|xss_clean');
		$this->form_validation->set_rules('address_2', 'Apt/Suite', 'max_length[40]|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'required|xss_clean');
		$this->form_validation->set_rules('state', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('zip', 'Zip', 'required|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
		$this->form_validation->set_rules('company', 'Company', 'xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateOrderSearchFilter()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ajax', 'Approved', 'required|xss_clean');
		$this->form_validation->set_rules('filter', 'Filter', 'xss_clean');
		$this->form_validation->set_rules('days', 'days', 'xss_clean');
		$this->form_validation->set_rules('date_search_from', 'date_search_from', 'callback__validateDate[from]|xss_clean');
		$this->form_validation->set_rules('date_search_to', 'date_search_to', 'callback__validateDate[to]|xss_clean');
		
		return $this->form_validation->run();
	}
	
	private function validatePart()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('featured', 'Featured', 'xss_clean');
		$this->form_validation->set_rules('markup', 'markup', 'integer|xss_clean');
		$this->form_validation->set_rules('exclude_market_place', 'Exclude from Market Place', 'integer|xss_clean');
		$this->form_validation->set_rules('closeout_market_place', 'Display Closeout from Market Place', 'integer|xss_clean');
		return $this->form_validation->run();
	}

	/************************************************ END VALIDATION **********************************************/
	
	/*********************************************** PAGINATION ***************************************************/

  private function adUsrPagination($count)
  {
  	$pages = 0;
  	if(is_numeric($count))
	  	$pages = ceil($count / $this->_adusrLimit); // Number of records per page
	  if($pages < 0)
	  	$pages = 0;
	  return $pages;
  }
  
  private function adOrderPagination($count)
  {
  	$pages = 0;
  	if(is_numeric($count))
	  	$pages = ceil($count / $this->_adOrderLimit); // Number of records per page
	  if($pages < 0)
	  	$pages = 0;
	  return $pages;
  }
  
  public function generatePaginate($direction = NULL)
  {
    if(is_numeric(@$_POST['page']))
    {
      $this->_mainData['pages'] = $this->adpdtPagination($this->admin_m->getProductCount(@$_POST['cat'], @$_POST['filter'], @$_POST['brand']));
  	  $this->_mainData['currentPage'] = ($direction == 'up') ? (@$_POST['page'] + ($this->_pagination * 2)) : (@$_POST['page'] - ($this->_pagination * 2));
  	  $this->_mainData['display_pages'] = $this->_pagination;
  		$pagination = $this->load->view('pagination_v', $this->_mainData, TRUE);
		}
	  if(@$_POST['ajax'])
	  	echo @$pagination;
	  else
	  	return @$pagination;	
  }
  
  public function generateAdUsrTable($page = 1, $filter = NULL)
  {
    if(@$_POST['ajax'])
    {
      $page = $this->input->post('page');
      $filter = $this->input->post('filter');
    }
    $this->_mainData['pages'] = $this->adUsrPagination($this->admin_m->getUserCount($filter));
    $this->_mainData['currentPage'] = $page;
    $this->_mainData['display_pages'] = $this->_pagination;
    $this->_mainData['pagination'] = $this->load->view('admin/pagination_v', $this->_mainData, TRUE);
    $offset = ($page - 1) * $this->_adusrLimit;
    $filter = ($filter == 'NULL') ? NULL : $filter;
    $this->_mainData['users'] = $this->admin_m->getUsers($filter, $this->_adusrLimit, $offset);
    $tableView = $this->load->view('tables/admin_users_v', $this->_mainData, TRUE);
	  if(@$_POST['ajax'])
	  {
	  	echo $tableView;
	  }
	  else
	  	return $tableView;	 
  }

  public function generateUsrPaginate($direction = NULL)
  {
    if(is_numeric(@$_POST['page']))
    {
      $this->_mainData['pages'] = $this->adUsrPagination($this->admin_m->getUserCount());
  	  $this->_mainData['currentPage'] = ($direction == 'up') ? (@$_POST['page'] + ($this->_pagination * 2)) : (@$_POST['page'] - ($this->_pagination * 2));
  	  $this->_mainData['display_pages'] = $this->_pagination;
  		$pagination = $this->load->view('admin/pagination_v', $this->_mainData, TRUE);
		}
	  if(@$_POST['ajax'])
	  	echo @$pagination;
	  else
	  	return @$pagination;	
  }
  
  	public function generateAdPdtListTable($order = 'name', $dir = 'DESC', $page = 1, $filter = NULL, $cat = NULL, $brand = NULL)
	{
		if(@$_POST['ajax'] && ($this->validateAdPdtPageBundle() !== FALSE))// If form validation passes use passed sorting
		{
			$order = $this->input->post('order');
			$page = $this->input->post('page');
			$filter = $this->input->post('filter');
			$dir = $this->input->post('dir');
			$cat = $this->input->post('cat');
	 }
	
		$order = $order . ' ' . $dir;
		$filter = ($filter == 'NULL') ? NULL : $filter;
		$offset = ($page - 1) * $this->_adpdtLimit;
		$this->_mainData['products'] = $this->admin_m->getProducts($cat, $filter, $order, $this->_adpdtLimit, $offset);
		$this->_mainData['categories'] = $this->admin_m->getCategories();
		$tableView = $this->load->view('admin/product/list_table_v', $this->_mainData, TRUE);
		if(@$_POST['ajax'])
		{
			echo $tableView;
		}
		else
			return $tableView;	  	
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
			$this->_mainData['pages'] = $this->adpdtPagination($this->admin_m->getProductCount(@$_POST['cat'], @$_POST['filter'], @$_POST['brand']));
			$this->_mainData['currentPage'] = ($direction == 'up') ? (@$_POST['page'] + ($this->_pagination * 2)) : (@$_POST['page'] - ($this->_pagination * 2));
			$this->_mainData['display_pages'] = $this->_pagination;
			$pagination = $this->load->view('admin/pagination/product_list_v', $this->_mainData, TRUE);
		}
		if(@$_POST['ajax'])
			echo @$pagination;
		else
			return @$pagination;	
	}
	
	public function generateListOrderTable($filter = NULL)
	{
		if($this->validateOrderSearchFilter() === TRUE)
			$this->_mainData['orders'] = $this->admin_m->getOrders($this->input->post());
		else
			$this->_mainData['orders'] = $this->admin_m->getOrders($filter);
		
		$pagination = $this->load->view('admin/order/list_table_v', $this->_mainData, TRUE);
		if(@$_POST['ajax'])
			echo @$pagination;
		else
			return @$pagination;	
	}
  
  /********************************************************** END VALIDATION ************************************************************/
  
  /**************************************************** INDIVIDUAL PAGE FUNCTIONS *********************************************/
	public function index()
	{
		$this->load->model('reporting_m');
		$this->setNav('admin/nav_v', 0);
		$this->renderMasterPage('admin/master_v', 'admin/home_v', $this->_mainData);
	}
	
	public function test()
	{
		$this->load->view('admin/test_v');
	}
	
	public function wishlists()
	{
		$this->setNav('admin/nav_v', 2);
		$this->_mainData['wishlists'] = $this->admin_m->getWishlists();
		$this->renderMasterPage('admin/master_v', 'admin/wishlists_v', $this->_mainData);
	}
	
	public function taxes()
	{
		if($this->validateEditTaxes() !== FALSE) // Display Form
		{
			$this->admin_m->updateTaxes($this->input->post());
		}
		$this->setNav('admin/nav_v', 2);
		$this->_mainData['taxes'] = $this->admin_m->getTaxes();
		$this->load_countries();
		$this->renderMasterPage('admin/master_v', 'admin/taxes_v', $this->_mainData);
	}
	
	public function distributors()
	{
		if($this->validateEditDistributor() !== FALSE) // Display Form
		{
			$this->admin_m->updateDistributors($this->input->post());
		}
		$this->setNav('admin/nav_v', 3);
		$this->_mainData['distributors'] = $this->admin_m->getDistributors();
		$this->renderMasterPage('admin/master_v', 'admin/distributor_v', $this->_mainData);
	}
	
	public function profile()
	{
		if($this->validateProfile() !== FALSE) // Display Form
		{
			$this->admin_m->updateAdminShippingProfile($this->input->post());
			$this->_mainData['success'] = TRUE;
		}
		$this->_mainData['address'] = $this->admin_m->getAdminShippingProfile();
		$this->_mainData['dealPercentage'] = $this->admin_m->getDealPercentage();
		$this->_mainData['states'] = $this->load_states();
		$this->_mainData['provinces'] = $this->load_provinces(); 
		$this->_mainData['countries'] = array('US' => 'USA', 'CA' => 'Canada');
		$this->setNav('admin/nav_v', 3);
		$this->renderMasterPage('admin/master_v', 'admin/profile_v', $this->_mainData);
	}
  
  /************************************ END INDIVIDUAL PAGE FUNCTIONS ***********************************/
  
  /********************************PRODUCT SECTION ******************************************/
	  
	public function product($cat = NULL)
	{
		//$this->load->model('parts_m');
		$this->_mainData['productListTable'] = $this->generateAdPdtListTable('category.display_page', 'ASC', 1, NULL, $cat);
		
		// Pagination
		$this->_mainData['pages'] = $this->adpdtPagination($this->admin_m->getProductCount());
		$this->_mainData['currentPage'] = 1;
		$this->_mainData['display_pages'] = $this->_pagination;
		$this->_mainData['pagination'] = $this->load->view('admin/pagination/product_list_v', $this->_mainData, TRUE);
			
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/product/list_v', $this->_mainData);
	} 
	
	public function update_part($id)
	{	
		$this->load->helper('async');
		
		if(is_null($id))
			redirect('admin/product');
		if(!is_numeric($id))
			redirect('admin/product');
		if($this->validatePart() === TRUE)
		{
			$this->admin_m->updatePart($id, $this->input->post());
		}
		curl_request_async();
		redirect('admin/product_edit/'.$id);
	}
	
	public function product_edit($id = NULL)
	{
		if(is_null($id))
		{
			$this->_mainData['new'] = TRUE;
		}
		else
		{
			$this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
		}
		$this->_mainData['part_id'] = $id;
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/product/edit_v', $this->_mainData);
	}
	
	public function product_category_brand($id = NULL)
	{		
		if(is_null($id))
		{
			$this->_mainData['new'] = TRUE;
		}
		else
		{
			$this->_mainData['product_cats'] = $this->admin_m->getCategoryByPartId($id);
			$this->_mainData['product_brand'] = $this->admin_m->getBrandByPartId($id);
		}
		$mainCategoryList = $this->admin_m->getCategories(FALSE);
		if($mainCategoryList)
		{
			foreach($mainCategoryList as $cat)
			{
				if(isset($this->_mainData['product_cats'][$cat['category_id']] ))
					$cat['selected'] = TRUE;
				$this->_mainData['categories'][$cat['parent_category_id']][] = $cat;
				
			}
		}
		$this->_mainData['brands'] = $this->admin_m->getBrands(FALSE);
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/product/cat_brand_v', $this->_mainData);
	}
	
	public function product_images($id = NULL)
	{
		if(is_null($id))
		{
			$this->_mainData['new'] = TRUE;
		}
		else
		{
			$this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
		}
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/product/images_v', $this->_mainData);
	}
	
	public function product_meta($id = NULL)
	{
		if(is_null($id))
		{
			$this->_mainData['new'] = TRUE;
		}
		else
		{
			$this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
		}
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/product/meta_v', $this->_mainData);
	}
	
	public function product_description($id = NULL)
	{
		if(is_null($id))
		{
			$this->_mainData['new'] = TRUE;
		}
		else
		{
			$this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
		}
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/product/desc_v', $this->_mainData);
	}
	
	public function product_shipping($id = NULL)
	{
		if(is_null($id))
		{
			$this->_mainData['new'] = TRUE;
		}
		else
		{
			$this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
		}
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/product/ship_v', $this->_mainData);
	}
	
 /************************************************END PRODUCT SECTION ******************************************/
  
  /************************************************** COUPON *****************************************/
	public function coupon()
	{
		$this->load->model('coupons_m');
		
		if($this->validateCoupon() === TRUE)
		{
			$success = $this->coupons_m->createCoupon($this->input->post());
		}
		
		$this->_mainData['specialConstraintsDD'] = $this->coupons_m->getSpecialConstraintsDD();
		$this->_mainData['specialConstraints'] = $this->coupons_m->getSpecialConstraints();
		$this->_mainData['brands_list'] = $this->admin_m->getBrands(TRUE);

		$this->_mainData['coupons'] = $this->coupons_m->getCoupons();
		$this->setNav('admin/nav_v', 5);
		$this->renderMasterPage('admin/master_v', 'admin/coupon_v', $this->_mainData);
	}
	
	public function coupon_delete($id)
	{
		if(is_numeric($id))
		{
			$this->load->model('coupons_m');
			$record = $this->coupons_m->deleteCoupon($id);
		}
		redirect('admin/coupon');
	}
	
	public function load_coupon($id)
	{
		if(is_numeric($id))
		{
			$this->load->model('coupons_m');
			$record = $this->coupons_m->getCouponById($id);
			echo json_encode($record);
		}
	}
	
	/************************************************* END COUPON *********************************************/
	
	/************************************************ IMAGE *************************************************/
  
	public function load_image()
	{
	if($this->validateImage() !== FALSE) // Display Form
	{
	  $this->_mainData['table'] = $this->input->post('table');
	  $this->_mainData['id'] = $this->input->post('id');
	  $tableView = $this->load->view('modals/add_image_v', $this->_mainData, TRUE);
	  echo $tableView;
		}
  }
  
  public function add_image()
  {
    if(@$_FILES['userfile'])
    {
    	$this->load->model('file_handling_m');
    	$data = $this->file_handling_m->add_new_file('userfile');
      if(!@$data['error'] )
      {
        $this->setRedirectValues('Error', $data['the_errors']);
        redirect('admin/product');
      }
      else
      {
        // Update image name into database
        $fileData = $data;
        $this->admin_m->updateImage($fileData['file_name'], $this->input->post('table'), $this->input->post('id'));
        
      }
    }
    redirect('admin/'.$this->input->post('table'));

  }
  
  public function remove_image()
  {
    $imageName = '';
    $this->admin_m->updateImage($imageName, $this->input->post('table'), $this->input->post('id'));  
    return TRUE;
  }
  
  /******************************************** END IMAGE ***************************************/
  
  /**************************** PRODUCT ********************************/
  
  public function process_edit_product()
  {
    if($this->validateEditProduct() !== FALSE) // Display Form
    {
      $this->load->model('products_m');
      $this->products_m->updateProducts($this->input->post());
    }
	    $this->_mainData['productFormTable'] = $this->generateAdPdtTable();
   }
  
  public function load_new_product()
  {
	  $this->_mainData['categories'] = $this->admin_m->getCategories();
    $tableView = $this->load->view('modals/new_product_v', $this->_mainData, TRUE);
  	echo $tableView;
  }
  
  public function process_new_product()
  {
    $data['error'] = FALSE;
	  if($this->validateProduct() !== FALSE)
	  {
	    $this->load->model('products_m');
	    $success = $this->products_m->createProduct($this->input->post());
	    if($success)
	    {
  	   $data['success_message'] = "You have successfully created your product!";
      }
  	  else
  	    $data['success_message'] = "There has been an issue.  Please refresh your page and try again.";
    }
    else
    {
      $data['error'] = TRUE;
      $data['error_message'] = validation_errors();
    }
    echo json_encode($data);
    exit();
  }
  
  	public function search_product()
	{
		if($this->validateSearch() === TRUE)
		{
			$this->load->model('parts_m');
			$products = $this->parts_m->getSearchResults($this->input->post(), NULL);
		}
		echo json_encode($products);
	}
	
	/**************************** END PRODUCT ********************************/
	
	/**************************** CATEGORY ************************************/
  
	public function category()
	{
		$mainCategoryList = $this->admin_m->getCategories(FALSE);
		if($mainCategoryList)
		{
			foreach($mainCategoryList as $cat)
			{
				$this->_mainData['categories'][$cat['parent_category_id']][] = $cat;
			}
		}
		
		$this->_mainData['parent_categories'] = $this->admin_m->getCategories(TRUE);
		
		

		if($this->validateEditCategory() !== FALSE && !empty($_POST)) // Display Form
		{
			$categories = $this->_mainData['categories'];
			$postData = $this->input->post();
			
			$updateCategories = array();
			$updateCategories[0]['parent_category_id'] = $postData['parent_category_id'];
			$updateCategories[0]['category_id'] = $postData['category_id'];
			$updateCategories[0]['active'] = $postData['active'];
			$updateCategories[0]['name'] = $postData['name'];
			$updateCategories[0]['title'] = $postData['title'];
			$updateCategories[0]['meta_tag'] = $postData['meta_tag'];
			$updateCategories[0]['keywords'] = $postData['keywords'];
			$updateCategories[0]['mark-up'] = $postData['mark-up'];
			$updateCategories[0]['google_category_num'] = $postData['google_category_num'];
			$updateCategories[0]['notice'] = $postData['notice'];
			
			$counter = 1;
			//!empty($postData['google_category_num']) && 
			if(@$categories[$postData['category_id']]){
				foreach($categories[$postData['category_id']] as $subCat){
				
				$updateCategories[$counter]['parent_category_id'] = $subCat['parent_category_id'];
				$updateCategories[$counter]['category_id'] = $subCat['category_id'];
				$updateCategories[$counter]['active'] = $subCat['active'];
				$updateCategories[$counter]['name'] = $subCat['name'];
				$updateCategories[$counter]['title'] = $subCat['title'];
				$updateCategories[$counter]['meta_tag'] = $subCat['meta_tag'];
				$updateCategories[$counter]['keywords'] = $subCat['keywords'];
				$updateCategories[$counter]['mark-up'] = $subCat['mark_up'];
				$updateCategories[$counter]['google_category_num'] = $postData['google_category_num'];
				$updateCategories[$counter]['notice'] = $subCat['notice'];
				
					if(@$categories[$subCat['category_id']]){
						foreach($categories[$subCat['category_id']] as $subsubCat){
						
						$secondCounter = count($updateCategories);
						$updateCategories[$secondCounter]['parent_category_id'] = $subsubCat['parent_category_id'];
						$updateCategories[$secondCounter]['category_id'] = $subsubCat['category_id'];
						$updateCategories[$secondCounter]['active'] = $subsubCat['active'];
						$updateCategories[$secondCounter]['name'] = $subsubCat['name'];
						$updateCategories[$secondCounter]['title'] = $subsubCat['title'];
						$updateCategories[$secondCounter]['meta_tag'] = $subsubCat['meta_tag'];
						$updateCategories[$secondCounter]['keywords'] = $subsubCat['keywords'];
						$updateCategories[$secondCounter]['mark-up'] = $subsubCat['mark_up'];
						$updateCategories[$secondCounter]['google_category_num'] = $postData['google_category_num'];
						$updateCategories[$secondCounter]['notice'] = $subsubCat['notice'];
					
							if(@$categories[$subsubCat['category_id']]){
								foreach($categories[$subsubCat['category_id']] as $subsubsubCat){
								
								$thirdCounter = count($updateCategories);
								$updateCategories[$thirdCounter]['parent_category_id'] = $subsubsubCat['parent_category_id'];
								$updateCategories[$thirdCounter]['category_id'] = $subsubsubCat['category_id'];
								$updateCategories[$thirdCounter]['active'] = $subsubsubCat['active'];
								$updateCategories[$thirdCounter]['name'] = $subsubsubCat['name'];
								$updateCategories[$thirdCounter]['title'] = $subsubsubCat['title'];
								$updateCategories[$thirdCounter]['meta_tag'] = $subsubsubCat['meta_tag'];
								$updateCategories[$thirdCounter]['keywords'] = $subsubsubCat['keywords'];
								$updateCategories[$thirdCounter]['mark-up'] = $subsubsubCat['mark_up'];
								$updateCategories[$thirdCounter]['google_category_num'] = $postData['google_category_num'];
								$updateCategories[$thirdCounter]['notice'] = $subsubsubCat['notice'];
			
								
								}
							}
						
						
						}
					}
					
				$counter++;
				}
			}
			
			/*echo "<pre>";
			print_r($updateCategories);
			echo "</pre>";
			exit;*/
			foreach($updateCategories as $category){
				$this->admin_m->updateCategory($category);
			}
			redirect('admin/category');
		}

		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/category_v', $this->_mainData);
	}
	
	public function category_delete($id)
	{
		if(is_numeric($id))
		{
			$this->admin_m->deleteCategory($id);
		}
		redirect('admin/category');
	}
  
	public function load_category_rec($id)
	{
		if(is_numeric($id))
		{
			$record =  $this->admin_m->getCategory($id);
			if(is_null($record['title']))
			{
				$record['title'] = str_replace(' > ', ', ', $record['long_name']);
				//$record['title'] = $record['long_name'];
			}
			echo json_encode($record);
		}
		exit();
	}
	
	/********************************* END CATEGORY **************************************/
	
	/********************************** BRAND ***********************************************/

	public function brand()
	{	
		$this->load->helper('async');
		if($this->validateEditBrand() !== FALSE) // Display Form
		{
			$this->admin_m->updateBrand($this->input->post());
			//redirect('admin/brand?update=1');	
		}
		$this->_mainData['brands'] = $this->admin_m->getBrands(FALSE);
		$this->_mainData['parent_brands'] = $this->admin_m->getBrands(TRUE);
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/brand/brand_v', $this->_mainData);
		curl_request_async();
		
	}
	
	public function brand_image($id = NULL)
	{
		if(is_null($id))
		{
			redirect('admin/brand');
		}
		else
		{
			$brandData = $this->admin_m->getBrand($id);
			$this->_mainData['brands'] = array($brandData);
			$this->_mainData['id'] = $id;
		}
		if(@$_FILES['image']['name'])
		{
			$config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
			$this->load->model('file_handling_m');
			$data = $this->file_handling_m->add_new_file('image', $config);
			if(@$data['error'])
				$this->_mainData['errors'] = $data['the_errors'];
			else
			{
				$brandData['image'] = $data['file_name'];
				$this->admin_m->updateBrand($brandData);
			}
			
  		}

		
		$this->setNav('admin/nav_v', 2);
		$this->renderMasterPage('admin/master_v', 'admin/brand/brand_images_v', $this->_mainData);
	}
	
	public function brand_delete($id)
	{
		if(is_numeric($id))
		{
			$this->admin_m->deleteBrand($id);
		}
		redirect('admin/brand');
	}
  
	public function load_brand_rec($id)
	{
		if(is_numeric($id))
		{
			$record =  $this->admin_m->getBrand($id);
			echo json_encode($record);
		}
		exit();
	}
	
	/************************************ END BRAND *********************************/
	
	/*************************** ORDERS ********************************/
		    
	public function orders($page = 1)
	{
		$this->load->model('account_m');
		$this->_mainData['currentPage'] = $page;
		$this->_mainData['listTable'] = $this->generateListOrderTable(array('filter' => array('approved')));
		$this->_mainData['pages'] =  $this->adOrderPagination($this->account_m->getOrderCount());
		$offset = ($page - 1) * $this->_adOrderLimit;
		$this->_mainData['prev_orders'] = $this->account_m->getPrevOrderDates($this->_adOrderLimit, $offset);
		$this->loadDateFields(array('datepicker_from', 'datepicker_to'));
		$this->setNav('admin/nav_v', 3);
		$this->renderMasterPage('admin/master_v', 'admin/order/list_v', $this->_mainData);
	}
	
	public function order_edit($id = 'new', $newPartNumber = NULL, $qty = 1)
	{
		$this->createMonths();
		$this->createYears();
		$this->_mainData['states'] = $this->load_states();
		$this->_mainData['provinces'] = $this->load_provinces(); 
		$this->loadCountries();
		$this->load->model('order_m');
		$this->_mainData['distributors'] = $this->order_m->getDistributors();
		
		if(!is_null($newPartNumber) && ($id != 'new'))
			$this->order_m->addProductToOrder($newPartNumber, $id, $qty);

		if($id != 'new')
			$this->_mainData['order'] = $this->order_m->getOrder($id);
		
		$this->setNav('admin/nav_v', 3);
		$this->renderMasterPage('admin/master_v', 'admin/order/edit_v', $this->_mainData);  
	}

  public function orders_pdf($date = NULL)
  {
    // set up PDF Helper files
 		$this->load->helper('fpdf_view');
 		$parameters = array();	
		pdf_init('reporting/poreport.php');
		
		// Send Variables to PDF
		//update process date and process user info
		$parameters['orders'] = $this->account_m->getPDFOrders($_SESSION['userRecord']['id'], $date);
		$fileName = 'OrderReport_'.time().'.pdf';
		
		// Create PDF
		$this->PDF->setParametersArray($parameters);
		$this->PDF->runReport();
		$this->PDF->Output($fileName, 'D'); // I

  }
  
	public function orders_csv($date = NULL)
	{
		$orders = $this->account_m->getPDFOrders($_SESSION['userRecord']['id'], $date);
		print_r($orders);
		//echo $this->array2csv($orders);
	}
	
		
	/************************************* END ORDERS *************************************/
  
  // Check Admin doc from Brandt for Details.  Add Wishlist to Users View page.
  
  public function users()
  {
    if($this->session->flashdata('errors'))
      $this->_mainData['errors'] = $this->session->flashdata('errors');
    $this->_mainData['userTable'] = $this->generateAdUsrTable(1);
    $this->setNav('admin/nav_v', 4);
		$this->renderMasterPage('master_v', 'admin/users_v', $this->_mainData);
  }
  
  public function process_edit_users()
  {
    if($this->validateEditUser() !== FALSE) // Display Form
    {
      $this->account_m->updateUserMass($this->input->post());
    }
	  $this->generateAdUsrTable();
  }
	
	/********************* SHIPPING ****************************/
	
	public function shipping_rules()
	{
		if($this->validateEditShippingRules() === TRUE) 
		{
			$this->admin_m->updateShippingRules($this->input->post());
		}
	    $this->setNav('admin/nav_v', 2);
		$this->_mainData['shippingRules'] = $this->admin_m->getShippingRules();
		$this->load_countries();
		$this->renderMasterPage('admin/master_v', 'admin/shipping_rules_v', $this->_mainData);
	}
	
	public function load_shipping_rules($id)
	{
		if(is_numeric($id))
		{
			$record =  $this->admin_m->getShippingRule($id);
			echo json_encode($record);
		}
		exit();
	}
	
	public function shipping_rule_delete($id)
	{
		if(is_numeric($id))
		{
			$this->admin_m->deleteShippingRule($id);
		}
		redirect('admin/shipping_rules');
	}
  
	public function test_shipping()
	{
		if($this->validateShipping() !== FALSE) // Display Form
		{
			$this->_mainData['weight'] = $this->input->post('weight');
			$this->_mainData['zip'] = $this->input->post('zip');
			$this->_mainData['country'] = $this->input->post('country');
			// UPS Rates
			$this->load->library('UpsShippingQuote');
			
			$objUpsRate = new UpsShippingQuote();
			
			$strDestinationZip = $this->input->post('zip');
			$strMethodShortName = 'GND';
			$strPackageLength = '8';
			$strPackageWidth = '8';
			$strPackageHeight = '8';
			$strPackageWeight = $this->input->post('weight');
			$strPackageCountry = $this->_mainData['country'];
			$boolReturnPriceOnly = true;
		
			$this->_mainData['postalOptions']['UPS'] = $objUpsRate->GetShippingRate(
			$strDestinationZip, 
			$strMethodShortName, 
			$strPackageLength, 
			$strPackageWidth,
			$strPackageHeight, 
			$strPackageWeight, 
			$boolReturnPriceOnly,
			$strPackageCountry
		);
		/*
		
		print_r($this->_mainData['postalOptions']['UPS']);
		exit();
		
		*/
		
		
		// USPS Rates
		$this->load->helper('usps');
		$this->_mainData['postalOptions']['USPS'] = USPSParcelRate($strPackageWeight, $strDestinationZip, $strPackageCountry);    
		
		}
		
		$this->setNav('admin/nav_v', 6);
		$this->renderMasterPage('admin/master_v', 'admin/shipping_v', $this->_mainData);
	}
	
	/***************************** END SHIPPING *******************************/
	
	/*************************************** HELPER FUNCTIONS **************************************/
	
	private function load_countries()
	{
		$this->_mainData['countries'] = array('US' => 'USA', 'CA' => 'Canada');
	}

	public function load_provinces($ajax = FALSE)
	{
		$provinces = $this->account_m->getTerritories('CA');
		if($ajax)
		  echo json_encode($provinces);
		else
	  return $provinces;
	}
	
	public function loadCountries()
  	{
  		$this->_mainData['countries'] = $this->account_m->getCountries();
  	}
  
	public function change_country()
	{
		$countryList = array('USA', 'Canada');
		$type = array('billing', 'acct_billing', 'shipping', 'acct_shipping');
		$country = $_POST['country'];
		if(in_array($_POST['country'], $countryList))
		{
			$funct = $country.'fields';
			$labelArr = $this->$funct();
			echo json_encode($labelArr);
		}
		else
			echo 'Invalid Country';
	}
  
  private function USAfields()
  {
    $labels = array('street_address' => '<b>Address:</b>',
                    'address_2' => '<b>Apt/Suite:</b>',
                    'city' => '<b>City:</b>',
                    'state' => '<b>State:</b>',
                    'zip' => '<b>Zipcode:</b>'
                    );
    return $labels;
  }
  
  private function Canadafields()
  {
    $labels = array('street_address' => '<b>Civic Address:</b>',
                    'address_2' => '<b>Apt/Suite:</b>',
                    'city' => '<b>Municipality:</b>',
                    'state' => '<b>Province:</b>',
                    'zip' => '<b>Postal Code:</b>'
                    );
    return $labels;
  }

	
	public function load_states($ajax = FALSE)
	{
		$states = $this->account_m->getTerritories('US');
		if($ajax)
			echo json_encode($states);
		else
			return $states;
	}
	
	public function createMonths()
	{
	
		for($i = 1; $i < 13; $i++)
		{
			$monthFormat = sprintf("%02s", $i);
			$dateObj   = DateTime::createFromFormat('!m', $i);
			$this->_mainData['months'][$monthFormat] =  $dateObj->format('F');
		}
	}
  
	public function createYears()
	{
		for($i = date('y'); $i < (date('y') + 13); $i++)
		{
			$dt = DateTime::createFromFormat('y', $i);
			$yyyy = $dt->format('Y');
			$this->_mainData['years'][$i] = $yyyy;
		}
	}
	
	private function array2csv(array &$array)
	{
		if (count($array) == 0) 
		{
			return null;
		}
		ob_start();
		$df = fopen("php://output", 'w');
		fputcsv($df, array_keys(reset($array)));
		foreach ($array as $row) 
		{
			fputcsv($df, $row);
		}
		fclose($df);
		return ob_get_clean();
	}
	
	public function decrypt($id)
	{
		$password = $this->account_m->getUserPassword($id);
		$this->load->library('encrypt');
		$password = $this->encrypt->decode($password);
		echo $password;
	}
	
	public function decryptcc($cc, $digits = NULL)
	{
		
		$this->load->library('encrypt');
		$cc = $this->encrypt->decode($cc);
		if($digits)
			return substr($cc, -4);
		else
			return $cc;
	}
	
	
/*
	public function decryptcc($id, $return = FALSE)
	{
		$cc = 'A2dQPVBiCDIBYAZoB2MAYgI+UzAKYwYxUzICYwBvVmM=';
		$this->load->model('order_m');
		$paymentRecord = $this->order_m->getPaymentInfo($id);
		$this->load->library('encrypt');
		$cc = $this->encrypt->decode($paymentRecord['ccnumber']);
		if($return)
			return $cc;
		else
			echo $cc;
	}
*/

	
	public function new_change_country()
	{
		$countryList = array('US' => 'USA', 'CA' => 'Canada');
		$type = array('billing', 'acct_billing', 'shipping', 'acct_shipping');
		$country = $_POST['country'];
		if($countryList[$country])
		{
			$funct = $countryList[$country].'fields';
			$labelArr = $this->$funct();
			echo json_encode($labelArr);
		}
		else
			echo 'Invalid Country';
	}
	
	public function updateStatus()
	{
		$this->load->model('order_m');
		$this->order_m->updateStatus(1, 'Approved', 'System order');
	}
	
	/********************************************** END HELPER FUNCTIONS ********************************************/
	

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */