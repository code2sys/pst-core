<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* NOTE!!!  Need to make sure to turn on if checkbox is there and off it is not */
class Admin_M extends Master_M
{
  	function __construct()
	{
		parent::__construct();
	}
	
	public function getAdminAddress()
	{
		$where = array('id' => 1);
		$record = $this->selectRecord('contact', $where);
		return $record;
	}
	
	public function getSliderImages($pageId)
	{
		$where = array('pageId' => $pageId);
		$this->db->order_by('order ASC');
		$records = $this->selectRecords('slider', $where);
		return $records;
	}
	
	public function updateSlider($uploadData)
	{
		$success = $this->createRecord('slider', $uploadData, FALSE);
		return $success;
	}
	
	public function removeImage($id, $uploadPath)
	{ 
	    $where = array('id' => $id);
	    $record = $this->selectRecord('slider', $where);
	    $this->deleteRecord('slider', $where);
	    @unlink($uploadPath . '/' .$record['image']);
	    return TRUE;
	}
			
	public function getParentCat()
	{
	  	$where = array('parent_category_id' => '');
	  	$records = $this->selectRecords('category', $where);
	  	$list = array(0 => '');
	  	if(@$records)
	  	{
	    	foreach($records as &$record)
	    	{
	      		$list[$record['parent_category_id']] = $record['name'];
	    	}
	  	}
	    return $list;
	}
	
	public function getProducts($cat = '20034', $filter=NULL, $orderBy=NULL, $limit=20, $offset=0)
	{	  
		if (!is_null($filter))
			$this->db->like('name', $filter);
		else
		{
			if($cat == 'featured')
				$this->db->where(array('featured' => 1));
			elseif($cat == 'mx')
				$this->db->where(array('mx' => 1));
			elseif(!is_null($cat))
			{
				$this->db->join('partcategory', 'partcategory.part_id = part.part_id');
				$where->db->where('partcategory.category_id = '.$cat);
			}
		}
		
		if (!is_null($limit))
		{
			if (!is_null($offset))
				$this->db->limit($limit, $offset);
			else
				$this->db->limit($limit);
		}
		
		$this->db->order_by('name ASC');
		$this->db->group_by('part.part_id');
		$this->db->select('part.part_id, 
										  part.name, 
										  part.featured,
										  part.mx, 
										  partnumber.partnumber, 
										  MIN(partnumber.sale) AS sale_min, 
										  MAX(partnumber.sale) AS sale_max,
										  MIN(partnumber.price) AS price_min, 
										  MAX(partnumber.price) AS price_max,
										  MIN(partnumber.cost) AS cost_min, 
										  MAX(partnumber.cost) AS cost_max,
										  MIN(partnumber.markup) AS markup,
										  ', 
										  FALSE);
		$this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
		$this->db->join('part', 'part.part_id = partpartnumber.part_id');
		$records = $this->selectRecords('partnumber');
		return $records;
	}
	
	public function getAdminProduct($part_id)
	{
		$where = array('part_id' => $part_id);
		$record = $this->selectRecord('part', $where);
		$where = array('partpartnumber.part_id' => $part_id);
		$this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
		$record['partnumbers'] = $this->selectRecords('partnumber', $where);
		return $record;
	}
	
	public function getProductCount($cat = NULL, $filter = NULL, $brand = NULL)
	{
		if (!is_null($filter))
			$this->db->like('name', $filter);
		else
		{
			if($cat == 'featured')
				$this->db->where(array('featured' => 1));
			elseif($cat == 'mx')
				$this->db->where(array('mx' => 1));
			elseif(!is_null($cat))
			{
				$this->db->join('partcategory', 'partcategory.part_id = part.part_id');
				$where->db->where('partcategory.category_id = '.$cat);
			}
		}
		$this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
		$this->db->join('part', 'part.part_id = partpartnumber.part_id');
		$this->db->from('partnumber');
		$num = $this->db->count_all_results();
		return $num;
	}
	
	public function getUserCount($filter = NULL)
	{
	  if(!is_null($filter))
	  {
			$this->db->like('lost_password_email', $filter);
      $this->db->or_like('username', $filter);
	  }
  	$this->db->from('user');
    $num = $this->db->count_all_results();
		return $num;
	}
	
	public function getUsers($filter = NULL, $limit=20, $offset=0)
	{
		  if (!is_null($filter))
		  {
				$this->db->like('lost_password_email', $filter);
	      $this->db->or_like('username', $filter);
			}
	    if (!is_null($limit))
			{
				if (!is_null($offset))
					$this->db->limit($limit, $offset);
				else
					$this->db->limit($limit);
			}
			$this->db->from('user');
			$query = $this->db->get();
			if ($query->num_rows() > 0)
				$records = $query->result_array();
			$query->free_result();
	  	return @$records;
	}
	
	public function getEmailSettings()
	{
		$finalArray = array();
		$this->db->like('key', 'email');
		$records = $this->selectRecords('config');
		if($records)
		{
			foreach($records as $rec)
				$finalArray[$rec['key']] = $rec['value'];
		}
		return $finalArray;
	}
	
	public function updateSMSettings($post)
	{	
		$finalArray = array();
		if($post)
		{
			foreach($post as $key => $value)
			{
				$where = array('key' => $key);
				$data = array('value' => $value);
				$this->updateRecord('config', $data, $where, FALSE);
			}
		}
		return TRUE;		
		
	}
	
	public function getSMSettings()
	{
		$finalArray = array();
		$this->db->like('key', 'sm');
		$records = $this->selectRecords('config');
		if($records)
		{
			foreach($records as $rec)
				$finalArray[$rec['key']] = $rec['value'];
		}
		return $finalArray;
	}
	
	//********************REVIEWS**********************//
	
	public function getNewReviews()
	{
		$where = array('approval_id IS NULL' => NULL);
		$this->db->select('*, reviews.id as id');
		$this->db->join('user', 'user.id = reviews.user_id', 'LEFT');
		$this->db->join('part', 'part.part_id = reviews.part_id');
		$records = $this->selectRecords('reviews', $where);
		return $records;
	}
	
	public function approveReview($reviewId, $userId)
	{
		$where = array('id' => $reviewId);
		$data = array('approval_id' => $userId);
		$success = $this->updateRecord('reviews', $data, $where, FALSE);
		return $success;
	}
	
	public function deleteReview($reviewId)
	{
		$where = array('id' => $reviewId);
		$success = $this->deleteRecord('reviews', $where);
		return $success;
	}
	
	//*****************CATEGORIES********************//
	
	public function getCategories($dd = TRUE)
	{
		$this->db->order_by('parent_category_id');
	  	$records = $this->selectRecords('category');
	    
	    if($dd)
	    {
	    	$list = array(0 => '---');
	    	if(@$records)
	    	{
		      	foreach($records as &$record)
		      	{
		        	$list[$record['category_id']] = $record['name'];
		      	}
	    	}
	        return $list;
	  	}
	  	else
	  		return $records;
	}
	
	public function getCategory($id)
	{
		$where = array('category_id' => $id);
		$record = $this->selectRecord('category', $where);
		return $record;
	}
	
	public function getCategoryByPartId($part_id)
	{
		$this->db->select('category_id');
		$where = array('part_id' => $part_id);
		$records = $this->selectRecords('partcategory',  $where);
		if($records)
		{
			$newRecArr = $records;
			$records = array();
			foreach($newRecArr as $key => $rec)
			{
				$records[$rec['category_id']] = $rec;
			}
		}
		return $records;
	}
	
	public function updateCategory($post)
	{	 
    	$data = array();
		$data['active'] = @$post['active'] ? 1 : 0;
		$data['title'] = $post['title'];
		$data['name'] = $post['name'];
		$data['keywords'] = $post['keywords'];
		$data['meta_tag'] = $post['meta_tag'];
		$data['notice'] = @$post['notice'];
		$data['google_category_num'] = @$post['google_category_num'];
		if($post['parent_category_id'] == '0')
			$data['parent_category_id'] = '';
		else
			$data['parent_category_id'] = $post['parent_category_id'];
		$data['long_name'] = $this->createCategoryLongName($data['parent_category_id'], $data['name'] );
		if(@$post['category_id'])
		{
			$success = $this->updateRecord('category', $data, array('category_id' => $post['category_id']), FALSE);
			$this->updateCategoryLongNames($post['category_id']);
		}
		else
		{
			$data['mx'] = 0;
			$post['category_id'] = $this->createRecord('category', $data, FALSE);
		}
		
		$this->updateCategoryMarkUp(@$post['category_id'], $post['mark-up']);
	}
	
	public function updateCategoryMarkUp($category_id, $markup)
	{
		$where = array('category_id' => $category_id);	
		$data = array('mark_up' => $markup);
		$this->updateRecord('category', $data, $where, FALSE);
		$this->db->select('part_id');
		$records = $this->selectRecords('partcategory', $where);
		if($records)
		{
			foreach($records as $rec)
			{
				$where = array('part_id' => $rec['part_id']);
				if(!$this->recordExists('queued_parts', $where))
				{
					$data = array('part_id' => $rec['part_id'], 'recCreated' => time());
					$this->createRecord('queued_parts', $data, FALSE);
				}
			}
		}
		$where = array('parent_category_id' => $category_id);
		$categories = $this->selectRecords('category', $where);
		if($categories)
		{
			foreach($categories as $cat)
			{
				$this->updateCategoryMarkUp($cat['category_id'], $markup);
			}
		}
	}
	
	public function processParts($limit = 10)
	{
		$this->db->limit($limit);
		$this->db->order_by('recCreated ASC');
		$records = $this->selectRecords('queued_parts');
		if($records)
		{
			for($i = 0; $i < count($records); $i++)
			{
				$this->db->select('MIN(category.mark_up) as markup');
				$where = array('partcategory.part_id' => $records[$i]['part_id'], 'category.mark_up > ' => 0);
				$this->db->join('partcategory', 'partcategory.category_id = category.category_id');
				$categories = $this->selectRecord('category', $where);
				
				$this->db->select('MIN(brand.mark_up) as markup, 
												  MAX(brand.exclude_market_place) as exclude_market_place, 
												  MAX(brand.closeout_market_place) as closeout_market_place');
				$where = array('partbrand.part_id' => $records[$i]['part_id']);
				$this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
				$brand_markup = $this->selectRecord('brand', $where);
				
				$this->db->select('MIN(brand.map_percent) as map_percent, ');
				$where = array('partbrand.part_id' => $records[$i]['part_id'], 'brand.map_percent IS NOT NULL ' => NULL);
				$this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
				$brand_map_percent = $this->selectRecord('brand', $where);
									
				$where = array('partpartnumber.part_id' => $records[$i]['part_id'], 'partnumber.price > ' => 0);
				$this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id ');
				$this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
				$partnumbers  = $this->selectRecords('partnumber', $where);
				
				$exclude = $brand_markup['exclude_market_place'];
				$closeout = $brand_markup['closeout_market_place'];
				$categoryMarkUp = is_numeric(@$categories['markup']) ? $categories['markup'] : 0;
				$brandMarkUp = is_numeric(@$brand_markup['markup']) ? $brand_markup['markup'] : 0;
				$brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : NULL;
				if($partnumbers)
				{			
					foreach($partnumbers as $rec)
					{
						$finalMarkUp = 0;
						$productMarkUp = $rec['markup'];
						
						if($productMarkUp > 0) // Product Markup Trumps everything
						{
							$finalSalesPrice = ($rec['cost'] * $productMarkUp / 100) + $rec['cost'];
						}
						else
						{
							// Calculate category and Brand Percent Mark up
							
							if($categoryMarkUp > 0)
							{
								$finalMarkUp = $categoryMarkUp;
								if(($brandMarkUp > 0) && ($brandMarkUp < $finalMarkUp))
									$finalMarkUp = $brandMarkUp;
							}
							elseif($brandMarkUp > 0)
									$finalMarkUp = $brandMarkUp;
							// Get Final Sales Price for Calculating vs MAP Pricing
								
							if($finalMarkUp > 0)
								$finalSalesPrice = ($rec['cost'] * $finalMarkUp / 100) + $rec['cost'];
								
							// Calculate MAP Pricing
							
							if((!is_null($brandMAPPercent))	&& (isset($finalSalesPrice)) && ($rec['stock_code'] != 'Closeout'))			
							{
								$mapPrice = (((100 - $brandMAPPercent)/100) * $rec['price']);
								 if($mapPrice > $finalSalesPrice)
								 	$finalSalesPrice = $mapPrice;
							}	
						}
						if(!isset($finalSalesPrice ))
							$finalSalesPrice = $rec['price'];
							
						if($finalSalesPrice > $rec['price'])
							$finalSalesPrice = $rec['price'];
							
						if($finalSalesPrice < $rec['cost'])
							$finalSalesPrice = $rec['price'];
						$data = array('sale' => $finalSalesPrice, 
												'exclude_market_place' => $exclude,  
												'closeout_market_place' => $closeout);
						$where = array('partnumber_id' => $rec['partnumber_id']);
						$this->updateRecord('partnumber', $data, $where, FALSE);
					}
				}
				$where = array('part_id' => $records[$i]['part_id']);
				$this->deleteRecord('queued_parts', $where);
			}
		}

	}
	
	public function updatePart($id, $post)
	{	
		
		$markup = $post['markup'];
		$excludeMarketPlace = ($post['market_places']=='exclude_market_place') ? 1 : 0;
		$closeoutMarketPlace = ($post['market_places']=='closeout_market_place') ? 1 : 0;
		unset($post['market_places']);
		unset($post['markup']);
		unset($post['exclude_market_place']);
		unset($post['closeout_market_place']);
		$where = array('part_id' => $id);
		if(!empty($post))
			$this->updateRecord('part', $post, $where, FALSE);
		
		$where = array('partpartnumber.part_id' => $id, 'price > ' => 0);
		$this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id ');
		$partnumbers  = $this->selectRecords('partnumber', $where);
		
		if(@$partnumbers)
		{
			foreach($partnumbers as $pn)
			{
				$data = array('markup' => $markup, 
										'exclude_market_place' => $excludeMarketPlace , 
										'closeout_market_place' => $closeoutMarketPlace);
										
				$where = array('partnumber_id' => $pn['partnumber_id']);
				$this->updateRecord('partnumber', $data, $where, FALSE);
			}
		}
		$where = array('part_id' => $id);
		if(!$this->recordExists('queued_parts', $where))
		{
			$data = array('part_id' => $id, 'recCreated' => time());
			$this->createRecord('queued_parts', $data, FALSE);
		}
		
	}
	
	public function deleteCategory($id)
	{
		$where =  array('parent_category_id' => $id);
		if($this->recordExists('category', $where))
		{
			$this->db->where($where);
			$categories = $this->getCategories(FALSE);
			$mainCat = $this->getCategory($id);
			foreach($categories as $cat)
			{
				$data['long_name'] = $this->createCategoryLongName($mainCat['parent_category_id'], $cat['name']);
				$success = $this->updateRecord('category', $data, array('category_id' => $cat['category_id']), FALSE);
			}
		}
		$where = array('category_id' => $id);
		$this->deleteRecord('category', $where);
	}
		
	public function createCategoryLongName($parentId, $name)
	{
		$parentCatRec = $this->getCategory($parentId);
		$longName = $parentCatRec['long_name']; 
		if(@$parentCatRec['long_name'])
			$longName .=' > ';
		 $longName .= $name;
		return $longName;
	}
	
	public function updateCategoryLongNames($id)
	{
		$where =  array('parent_category_id' => $id);
		if($this->recordExists('category', $where))
		{
			$this->db->where($where);
			$categories = $this->getCategories(FALSE);
			foreach($categories as $cat)
			{
				$data['long_name'] = $this->createCategoryLongName($id, $cat['name']);
				$success = $this->updateRecord('category', $data, array('category_id' => $cat['category_id']), FALSE);
			}
		}
	}
	
	//*****************END CATEGORIES********************//
	
	
	//*****************BRANDS********************//
	public function getBrands($dd = TRUE)
	{
		$this->db->order_by('name');
	  	$records = $this->selectRecords('brand');
	    $list = array(0 => '---');
	    if($dd)
	    {
	    	if(@$records)
	    	{
		      	foreach($records as &$record)
		      	{
		        	$list[$record['brand_id']] = $record['name'];
		      	}
	    	}
	      return $list;
	  	}
	  	else
	  	  return $records;
	}
	
	public function getBrand($id)
	{
		$where = array('brand_id' => $id);
		$record = $this->selectRecord('brand', $where);
		return $record;
	}
	
	public function getBrandByPartId($part_id)
	{
		$this->db->select('brand_id');
		$where = array('part_id' => $part_id);
		$records = $this->selectRecords('partbrand',  $where);
		if($records)
		{
			$newRecArr = $records;
			$records = array();
			foreach($newRecArr as $key => $rec)
			{
				$records[$rec['brand_id']] = $rec;
			}
		}
		return $records;
	}

	public function updateBrand($post)
	{	 
    	$data = array();
		$data['active'] = @$post['active'] ? 1 : 0;
		$data['featured'] = @$post['featured'] ? 1 : 0;
		$data['exclude_market_place'] = ($post['market_places']=='exclude_market_place') ? 1 : 0;
		$data['closeout_market_place'] = ($post['market_places']=='closeout_market_place') ? 1 : 0;
		$data['name'] = $post['name'];
		$data['image'] = @$post['image'];
		$data['mark_up'] = @$post['mark-up'];
		$data['map_percent'] = @$post['map_percent'];
		if(@$post['MAP_NULL'])
			$data['map_percent IS NULL'] = NULL;
		$data['long_name'] = $data['name'];
		if(@$post['brand_id'])
			$success = $this->updateRecord('brand', $data, array('brand_id' => $post['brand_id']), FALSE);
		else
		{
			$data['mx'] = 0;
			$post['brand_id'] = $this->createRecord('brand', $data, FALSE);
		}
		$this->updateBrandMarkUp($post['brand_id']);
	}
	
	public function updateBrandMarkUp($brand_id)
	{
		
		$where = array('brand_id' => $brand_id);		
		$this->db->select('part_id');
		$records = $this->selectRecords('partbrand', $where);
		if($records)
		{
			foreach($records as $rec)
			{
				$where = array('part_id' => $rec['part_id']);
				if(!$this->recordExists('queued_parts', $where))
				{
					$data = array('part_id' => $rec['part_id'], 'recCreated' => time());
					$this->createRecord('queued_parts', $data, FALSE);
				}
			}
		}
	}
	
	public function deleteBrand($id)
	{
		$where = array('brand_id' => $id);
		$this->deleteRecord('brand', $where);
	}
	
	public function updateBrandLongNames($id)
	{
		$where =  array('parent_brand_id' => $id);
		if($this->recordExists('brand', $where))
		{
			$this->db->where($where);
			$brands = $this->getBrands(FALSE);
			foreach($brands as $brand)
			{
				$data['long_name'] = $this->createBrandLongName($id, $brand['name']);
				$success = $this->updateRecord('brand', $data, array('brand_id' => $brand['brand_id']), FALSE);
			}
		}
	}
 
 //********************************* END BRANDS *************************************//
 
 
 //*********************************** WISHLISTS **************************************//
 	
 	public function getWishlists()
 	{
 		$records = FALSE;
	 	$records = $this->selectRecords('wishlist');
	 	if($records)
	 	{
		 	foreach($records as &$rec)
		 	{
			 	$query = $this->db->query('SELECT * FROM wishlist_part
												JOIN part ON part.part_id = wishlist_part.part_id
												JOIN partpartnumber ON partpartnumber.part_id = part.part_id
												JOIN partnumber ON partnumber.partnumber_id = partpartnumber.partnumber_id
												WHERE wishlist_part.wishlist_id = '.$rec['id']);
			 	$rec['parts'] = $query->result_array();
			 	$query->free_result();	
		 	}
	 	}
	 	return $records;
 	}
 
 //********************************** END WISHLISTS *********************************//
 
 //********************************** TAXES ***********************************************//
 
 	public function getTaxes()
 	{
 		$records = FALSE;
	 	$records = $this->selectRecords('taxes');
	 	return $records;
 	}
 	
 	public function updateTaxes($post)
 	{
		if(!empty($post['id']))
		{
			foreach($post['id'] as $key => $id)
			{
				$where = array('id' => $id);
				$data = array();
				$data['active'] = @$post['active'][$key] ? 1 : 0;
				$data['percentage'] = @$post['active'][$key] ? 1 : 0;
				$data['tax_value'] = $post['tax_value'][$key];
				$success = $this->updateRecord('taxes', $data, $where, FALSE);
			}
		}
	
 	}
 	
 //********************************** END TAXES ***************************************//
 
 //********************************** SHIPPING RULES *******************************//
 
 	public function getShippingRules()
 	{
 		$records = FALSE;
	 	$records = $this->selectRecords('shipping_rules');
	 	return $records;	
 	}
 	
 	public function getShippingRule($id)
 	{
	 	$record = FALSE;
	 	$where = array('id' => $id);
	 	$record = $this->selectRecord('shipping_rules', $where);
	 	return $record;
 	}
 	
 	public function updateShippingRules($formFields)
 	{
		if(@$formFields['create_new'])
		{
			unset($formFields['create_new']);
			unset($formFields['id']);
			$this->createRecord('shipping_rules', $formFields, FALSE);
		}
		else
		{
			unset($formFields['edit']);
			$where = array('id' => $formFields['id']);
			$this->updateRecord('shipping_rules', $formFields, $where, FALSE);
		}
 	}
 	
 	public function deleteShippingRule($id)
 	{
	 	$where = array('id' => $id);
		$this->deleteRecord('shipping_rules', $where);
 	}
 
 //********************************** END SHIPPING RULES *********************************//
 
 //************************************** DISTRIBUTORS *****************************************//
 
 	public function getDistributors()
 	{
 		$records = FALSE;
	 	$where = array('type' => 'distributor');
	 	$records = $this->selectRecords('accounts', $where);
	 	return $records;
 	}
 	
 	public function updateDistributors($formFields)
 	{	
		$where = array('id' => $formFields['id']);
		$this->updateRecord('accounts', $formFields, $where, FALSE);
 	}
 
 //***************************************** END DISTRIBUTORS *********************************//
	
	public function updateAdminShippingProfile($data)
	{
		if($data['deal'])
		{
			$where = array('key' => 'deal_percentage');
			$configdata= array('value' => $data['deal']);
			$return = $this->updateRecord('config', $configdata, $where, FALSE);
			unset($data['data']);
		}
		$where = array('id' => 1);
		$return = $this->updateRecord('contact', $data, $where, FALSE);
		

	}
	
	public function getAdminShippingProfile()
	{
		$where = array('id' => 1);
		$record = $this->selectRecord('contact', $where);
		return $record;
	}
	
	public function getDealPercentage()
	{
		$where = array('key' => 'deal_percentage');
		$record = $this->selectRecord('config', $where);
		return $record['value'];
	}
	
	public function updateImage($imageName, $tableName, $id)
	{ 
	    switch($tableName)
	    {
	      case 'product':
	        $this->updateRecord('product', array('image' => $imageName), array('sku' => $id), FALSE);
	      break;
	      case 'category':
	        $this->updateRecord('category', array('image' => $imageName), array('code' => $id), FALSE);
	      break;
	    }
	}
	
	public function updateSettings($data)
	{
		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				$inputData = array('value' => $value);
				$where = array('key' => $key);
				$this->updateRecord('config', $inputData, $where, FALSE);
			}
		}
	}
	
	public function updateOrderTrackingNumber($post)
	{
		$where = array('id' => $post['id']);
		$this->db->select('ship_tracking_code');
		$record = $this->selectRecord('order', $where);
		if($record['ship_tracking_code'])
			$trackingCodes = json_decode($record['ship_tracking_code']);
		else
			$trackingCodes = array();
		$trackingCodes[] = array($post['carrier'], $post['ship_tracking_code']);
		$encoded = json_encode($trackingCodes);
		$orderRec = array('ship_tracking_code' => $encoded);
		$this->updateRecord('order', $orderRec, $where, FALSE);
	}
	
	public function removeTrackingFromOrder($post)
	{
		$where = array('id' => $post['id']);
		$this->db->select('ship_tracking_code');
		$record = $this->selectRecord('order', $where);
		if($record['ship_tracking_code'])
		{
			$trackingCodes = json_decode($record['ship_tracking_code'], TRUE);
			unset($trackingCodes[$post['key']]);
			if(!empty($trackingCodes))
				$encoded = json_encode($trackingCodes);
			else
				$encoded = NULL;
			$orderRec = array('ship_tracking_code' => $encoded);
			$this->updateRecord('order', $orderRec, $where, FALSE);
		}
	}
	
	public function recordOrderCreation($order)
	{
		$orderRec = array();
		// Create Order record including total product sales and shipping
		$orderRec['contact_id'] = $order['billing_id'];
		$orderRec['shipping_id'] = $order['shipping_id'];
		if(@$order['transAmount'])
			$orderRec['sales_price'] = @$order['transAmount'] - @$order['shipping'] - @$order['tax'];
		$orderRec['shipping'] = @$order['shipping'];
		$orderRec['tax'] = @$order['tax'];
		if(@$order['special_instr'])
			$orderRec['special_instr'] = @$order['special_instr'];
		if(@$order['order_id'])
		{
			$where = array('id' => $order['order_id']);
			$this->updateRecord('order', $orderRec, $where, FALSE);
		}
		else
			$order['order_id'] = $this->createRecord('order', $orderRec, FALSE);
		// Create order_product record for each item purchased including price charged for each item.
		if(is_array(@$cart['products']))
		{
			foreach($cart['products'] as $key => $product)
			{
				$data = array('order_id' => $orderId, 'product_sku' => $key, 'price' => @$product['finalPrice'], 'qty' => @$product['qty']);
				$this->createRecord('order_product', $data, FALSE);
			}
		}
		return $order['order_id'];
	}
	
	public function getOrders($filter, $limit = NULL)
	{
		$this->db->select('order.id AS order_id, '.
		            'order.user_id AS user_id, '.
		            'order.contact_id AS contact_id, '.
		            'order.shipping_id AS shipping_id, '.
		            'order.sales_price AS sales_price, '.
		            'order.shipping AS shipping, '.
		            'order.weight AS weight, '.
		            'order.tax AS tax, '.
		            'order.Reveived_date AS processed_date, '.
		            'order.will_call AS will_call, '.
		            'order.process_date AS process_date, '.
		            'order.batch_number AS batch_number, '.
		            'order.special_instr AS special_instr, '.
		            'order.Reveived_date AS Reveived_date,'.
		            'order.order_date AS order_date, '.
		            'contact.first_name AS first_name, '.
		            'contact.last_name AS last_name, '.
		            'contact.street_address AS street_address, '.
		            'contact.address_2 AS address_2, '.
		            'contact.city AS city, '.
		            'contact.state AS state, '.
		            'contact.zip AS zip, '.
		            'contact.company AS company,'.
		            'shipping.first_name AS shipping_first_name, '.
		            'shipping.last_name AS shipping_last_name, '.
		            'shipping.street_address AS shipping_street_address, '.
		            'shipping.address_2 AS shipping_address_2, '.
		            'shipping.city AS shipping_city, '.
		            'shipping.state AS shipping_state, '.
		            'shipping.zip AS shipping_zip, '.
		            'shipping.company AS shipping_company ');
		$records = FALSE;
		if(!is_null($limit))
			$this->db->limit($limit);
		$this->setOrderFilter(@$filter['filter']);
		$this->db->order_by('order.id DESC');
		$this->db->join('contact', 'contact.id = order.contact_id');
		$this->db->join('contact shipping', 'shipping.id = order.contact_id');
		
		$this->db->group_by('order.id');
		$records = $this->selectRecords('order');
		if($records)
		{
			foreach($records as &$row)
			{
				$this->db->select('distributor');
				$where = array('order_id' => $row['order_id']);
				$row['products'] = $this->selectRecords('order_product', $where);
				$this->db->order_by('datetime DESC');
				$statusRec = $this->selectRecord('order_status', $where);
				$row['status'] = $statusRec['status'];
			}
		}
		return $records;
	}
	
	private function setOrderFilter($filter)
	{
		$where = array();
		if(is_array($filter))
		{
			foreach($filter as $piece)
			{
				switch($piece)
				{
					case 'pending': 
						$where['order_date IS NULL'] = NULL;
						break;
					case 'approved':
						$where['order_date IS NOT NULL'] = NULL;
						break;
					case 'partially':
						$where['shipped_status'] = 'partial';
						break;
					case 'shipped':
						$where['shipped_status'] = 'complete';
						break;
					case 'batch':
						$where['batch_status'] = 'complete';
						break;
				}
			}
		}
		$this->db->or_where($where);
		return $where;
	}
	
	public function getUserEmails()
 	{
		$query = $this->db->query("SELECT DISTINCT username as email FROM `user` WHERE admin!=1");
		return $query->result_array();
	}
	
	public function getContactTable()
 	{
		$query = $this->db->query("SELECT email FROM `contact` group by email");
		return $query->result_array();
	}
	
	public function getNewsLetters()
 	{
		$query = $this->db->query("SELECT emailaddress AS email FROM `newsletter` group by emailaddress");
		return $query->result_array();
	}

	
}