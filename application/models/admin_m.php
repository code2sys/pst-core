<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* NOTE!!!  Need to make sure to turn on if checkbox is there and off it is not */
require_once("master_m.php");

class Admin_M extends Master_M {

    function __construct() {
        parent::__construct();
        $this->load->model("lightspeed_m");
    }

    public function getAdminAddress() {
        $where = array('id' => 1);
        $record = $this->selectRecord('contact', $where);
        return $record;
    }

    public function getSliderImages($pageId, $page_section_id) {
        $where = array('pageId' => $pageId, "page_section_id" => $page_section_id);
        $this->db->order_by('order ASC');
        $records = $this->selectRecords('slider', $where);
        return $records;
    }

    /*
     * JLB 07-07-17
     * WTF would anyone name this function "updateSlider" when they are calling the create function instead?
     * This makes no sense. This is horrible for future coders. If you call something an update, and it's really an add....
     * What would you call an update???
     *
     */
    public function updateSlider($uploadData) {
        $success = $this->createRecord('slider', $uploadData, FALSE);
        return $success;
    }

    public function removeImage($id, $uploadPath) {
        $where = array('id' => $id);
        $record = $this->selectRecord('slider', $where);
        $this->deleteRecord('slider', $where);
        @unlink($uploadPath . '/' . $record['image']);
        return TRUE;
    }

    public function getParentCat() {
        $where = array('parent_category_id' => '');
        $records = $this->selectRecords('category', $where);
        $list = array(0 => '');
        if (@$records) {
            foreach ($records as &$record) {
                $list[$record['parent_category_id']] = $record['name'];
            }
        }
        return $list;
    }

    public function getProducts($cat = '20034', $filter = NULL, $orderBy = NULL, $limit = 20, $offset = 0) {
        if (!is_null($filter)) {
            if (is_array($filter)) {
                $custom_where = "(";
                foreach ($filter as $search) {
                    $custom_where .= 'MATCH(part.name) AGAINST("' . trim($search) . '")';
                }
                $custom_where = rtrim($custom_where, 'OR') . ')';
                $this->db->where($custom_where);
            } else {
                $this->db->like('name', $filter);
            }
        } else {
            if ($cat == 'featured')
                $this->db->where(array('featured' => 1));
            elseif ($cat == 'mx')
                $this->db->where(array('mx' => 1));
            elseif (!is_null($cat)) {
                $this->db->join('partcategory', 'partcategory.part_id = part.part_id');
                $where->db->where('partcategory.category_id = ' . $cat);
            }
        }

        if (!is_null($limit)) {
            if (!is_null($offset))
                $this->db->limit($limit, $offset);
            else
                $this->db->limit($limit);
        }

        $this->db->order_by('srch');
        $this->db->group_by('part.part_id');
        $this->db->select('part.part_id, 
										  part.name, 
										  part.featured, "' . $filter[0] . '" as srch, part.mx, 
										  partnumber.partnumber, 
										  MIN(partnumber.sale) AS sale_min, 
										  MAX(partnumber.sale) AS sale_max,
										  MIN(partnumber.price) AS price_min, 
										  MAX(partnumber.price) AS price_max,
										  MIN(partnumber.cost) AS cost_min, 
										  MAX(partnumber.cost) AS cost_max,
										  MIN(partnumber.markup) AS markup,
										  ', FALSE);
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');
        $records = $this->selectRecords('partnumber');
        //echo $this->db->last_query();exit;
        return $records;
    }

    public function getMotorcycleProducts($cat = NULL, $filter = NULL, $orderBy = NULL, $limit = 20, $offset = 0) {
//        if (!is_null($filter)) {
//            if (is_array($filter)) {
//                $custom_where = "(";
//                $custom_where1 = "(";
//                foreach ($filter as $search) {
//                    //$custom_where .= 'MATCH(motorcycle.title) AGAINST("' . trim($search) . '")';
//                    $custom_where .= 'title like "%' . trim($search) . '%"';
//                    $custom_where1 .= 'sku like "%' . trim($search) . '%"';
//                }
//                $custom_where = rtrim($custom_where, 'OR') . ')';
//                $custom_where1 = rtrim($custom_where1, 'OR') . ')';
//                $this->db->where($custom_where);
//                $this->db->or_where($custom_where1);
//            } else {
//                $this->db->like('motorcycle.title', $filter);
//                $this->db->or_like('motorcycle.sku', $filter);
//            }
//        }        
        if (!is_null($filter)) {
            if (is_array($filter)) {
                if( $filter['search'] != '' ) {
                $custom_where = "(";
                    $custom_where .= 'title like "%' . trim($filter['search']) . '%" OR '.'sku like "%' . trim($filter['search']) . '%" OR ';
                    $custom_where = rtrim($custom_where, ' OR ') . ')';
                    $this->db->where($custom_where);
                }
                //$custom_where .= "condition = '".$filter['condition']."' OR make = '".$filter['brand']."' OR year = '".$filter['year']."' OR category = '".$filter['category']."' OR vehicle = '".$filter['vehicle']."'";
                
                $custom_where1 = "";
                if($filter['condition'] != '') {
                    $this->db->where('condition', $filter['condition']);
                }
                if (@$filter['brand']) {
                    $this->db->where_in('motorcycle.make', $filter['brand']);
                }

                if (@$filter['year']) {
                    $this->db->where_in('motorcycle.year', $filter['year']);
                }
                if (@$filter['category']) {
                    $this->db->where_in('motorcycle.category', $filter['category']);
                }
                if (@$filter['vehicle']) {
                    $this->db->where_in('motorcycle.vehicle_type', $filter['vehicle']);
                }
                
                if( $custom_where1 != '' ) {
                    $custom_where1 = "(".rtrim($custom_where1, ' AND ') . ')';
                    $this->db->where($custom_where1);
                }
            } else {
                $this->db->like('motorcycle.title', $filter);
                $this->db->or_like('motorcycle.sku', $filter);
            }
        }

        if (!is_null($limit)) {
            if (!is_null($offset))
                $this->db->limit($limit, $offset);
            else
                $this->db->limit($limit);
        }

        $this->db->order_by('srch');
        $this->db->group_by('motorcycle.id');
        $this->db->select('motorcycle.id, 
										  motorcycle.sku,
										  motorcycle.status,
										  motorcycle.title, 
										  motorcycle.mileage,
										  motorcycle.condition,
										  motorcycle.featured, "' . $filter[0] . '" as srch,
										  motorcycle.sale_price, 
										  motorcycle.retail_price, 
										  ', FALSE);
        $records = $this->selectRecords('motorcycle');
        //echo $this->db->last_query();exit;
        return $records;
    }

    public function getAdminProduct($part_id) {
        $where = array('part_id' => $part_id);
        $record = $this->selectRecord('part', $where);
        $where = array('partpartnumber.part_id' => $part_id);
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $record['partnumbers'] = $this->selectRecords('partnumber', $where);
        return $record;
    }

    public function getAdminMotorcycle($part_id) {
        $where = array('motorcycle.id' => $part_id);
        $this->db->join('motorcycle_category', 'motorcycle_category.id = motorcycle.category');
        $record = $this->selectRecord('motorcycle', $where);
        return $record;
    }

    public function getProductVideo($part_id) {
        $where = array('part_id' => $part_id);
        $record = $this->selectRecords('part_video', $where);
        return $record;
    }

    public function getProductSizeChart($part_id) {
        $where = array('part_id' => $part_id);
        $record = $this->selectRecord('part_sizechart', $where);
        return $record;
    }

    public function getProductCount($cat = NULL, $filter = NULL, $brand = NULL) {
        if (!is_null($filter))
            $this->db->like('name', $filter);
        else {
            if ($cat == 'featured')
                $this->db->where(array('featured' => 1));
            elseif ($cat == 'mx')
                $this->db->where(array('mx' => 1));
            elseif (!is_null($cat)) {
                $this->db->join('partcategory', 'partcategory.part_id = part.part_id');
                $where->db->where('partcategory.category_id = ' . $cat);
            }
        }
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');
        $this->db->from('partnumber');
        $num = $this->db->count_all_results();
        return $num;
    }

    public function getUserCount($filter = NULL) {
        if (!is_null($filter)) {
            $this->db->like('lost_password_email', $filter);
            $this->db->or_like('username', $filter);
        }
        $this->db->from('user');
        $num = $this->db->count_all_results();
        return $num;
    }

    public function getUsers($filter = NULL, $limit = 20, $offset = 0) {
        if (!is_null($filter)) {
            $this->db->like('lost_password_email', $filter);
            $this->db->or_like('username', $filter);
        }
        if (!is_null($limit)) {
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

    public function getEmailSettings() {
        $finalArray = array();
        $this->db->like('key', 'email');
        $records = $this->selectRecords('config');
        if ($records) {
            foreach ($records as $rec)
                $finalArray[$rec['key']] = $rec['value'];
        }
        return $finalArray;
    }

    public function updateSMSettings($post) {
        $finalArray = array();
        if ($post) {
            foreach ($post as $key => $value) {
                $where = array('key' => $key);
                $data = array('value' => $value);
                $this->updateRecord('config', $data, $where, FALSE);
            }
        }
        return TRUE;
    }

    public function getSMSettings() {
        $finalArray = array();
        $this->db->like('key', 'sm');
        $records = $this->selectRecords('config');
        if ($records) {
            foreach ($records as $rec)
                $finalArray[$rec['key']] = $rec['value'];
        }
        return $finalArray;
    }

    //********************REVIEWS**********************//

    public function getNewReviews() {
        $where = array('approval_id IS NULL' => NULL);
        $this->db->select('*, reviews.id as id');
        $this->db->join('user', 'user.id = reviews.user_id', 'LEFT');
        $this->db->join('part', 'part.part_id = reviews.part_id');
        $records = $this->selectRecords('reviews', $where);
        return $records;
    }

    public function approveReview($reviewId, $userId) {
        $where = array('id' => $reviewId);
        $data = array('approval_id' => $userId);
        $success = $this->updateRecord('reviews', $data, $where, FALSE);
        return $success;
    }

    public function deleteReview($reviewId) {
        $where = array('id' => $reviewId);
        $success = $this->deleteRecord('reviews', $where);
        return $success;
    }

    //*****************CATEGORIES********************//

    public function getCategories($dd = TRUE) {
        $this->db->order_by('parent_category_id');
        $records = $this->selectRecords('category');

        if ($dd) {
            $list = array(0 => '---');
            if (@$records) {
                foreach ($records as &$record) {
                    $list[$record['category_id']] = $record['name'];
                }
            }
            return $list;
        } else
            return $records;
    }

    public function getCategory($id) {
        $where = array('category_id' => $id);
        $record = $this->selectRecord('category', $where);
        return $record;
    }

    public function getCategoryByPartId($part_id) {
        $this->db->select('category_id');
        $where = array('part_id' => $part_id);
        $records = $this->selectRecords('partcategory', $where);
        if ($records) {
            $newRecArr = $records;
            $records = array();
            foreach ($newRecArr as $key => $rec) {
                $records[$rec['category_id']] = $rec;
            }
        }
        return $records;
    }

	public function updateCategoryImage($post) {
		$where = array('category_id' => $post['category_id']);
        $data = array('image' => $post['image']);
        $this->updateRecord('category', $data, $where, FALSE);
	}
	
    public function updateCategory($post) {
        $data = array();
        $data['title'] = $post['title'];
        $data['featured'] = $post['featured'];
        $data['name'] = $post['name'];
        $data['keywords'] = $post['keywords'];
        $data['meta_tag'] = $post['meta_tag'];
        $data['notice'] = @$post['notice'];
        $data['google_category_num'] = @$post['google_category_num'];
        $data['ebay_category_num'] = @$post['ebay_category_num'];

        if ($post['parent_category_id'] == '0')
            $data['parent_category_id'] = '';
        else
            $data['parent_category_id'] = $post['parent_category_id'];
        $data['long_name'] = $this->createCategoryLongName($data['parent_category_id'], $data['name']);
        if (@$post['category_id']) {
            $success = $this->updateRecord('category', $data, array('category_id' => $post['category_id']), FALSE);
            $this->updateCategoryLongNames($post['category_id']);
        } else {
            $data['mx'] = 0;
            $post['category_id'] = $this->createRecord('category', $data, FALSE);
        }

        $this->updateCategoryMarkUp(@$post['category_id'], $post['mark-up']);
    }

    public function updateCategoryMarkUp($category_id, $markup) {
        $where = array('category_id' => $category_id);
        $data = array('mark_up' => $markup);
        $this->updateRecord('category', $data, $where, FALSE);

        $now = time(); // I don't want the query to somehow do multiples
        $this->db->query("Insert into queued_parts (part_id, recCreated) select distinct partcategory.part_id, $now from partcategory LEFT OUTER JOIN queued_parts on partcategory.part_id = queued_parts.part_id where queued_parts.part_id is null and partcategory.category_id = ?", array($category_id));
//
//        $this->db->select('part_id');
//        $records = $this->selectRecords('partcategory', $where);
//        if ($records) {
//            foreach ($records as $rec) {
//                $where = array('part_id' => $rec['part_id']);
//                if (!$this->recordExists('queued_parts', $where)) {
//                    $data = array('part_id' => $rec['part_id'], 'recCreated' => time());
//                    $this->createRecord('queued_parts', $data, FALSE);
//                }
//            }
//        }
        $where = array('parent_category_id' => $category_id);
        $categories = $this->selectRecords('category', $where);
        if ($categories) {
            foreach ($categories as $cat) {
                $this->updateCategoryMarkUp($cat['category_id'], $markup);
            }
        }
    }

    /*
     * JLB 10-22-17
     * I don't want to have to keep looking these things up; I just want to make my list of categories, which is probably small, and cache it in memory.
     */

    protected $fitment_categories;
    protected $fitment_category_map;
    protected function initializeFitmentCategories() {
        $this->fitment_categories = array();
        $this->fitment_category_map = array();

        // get the first round of categories that do fitments
        $query = $this->db->query("Select category_id from category where fitment_based = 1");
        foreach ($query->result_array() as $row) {
            $this->fitment_categories[] = $row["category_id"];
        }
        $parent_categories = $this->fitment_categories;

        if (count($parent_categories) > 0) {
            do {
                $new_categories = array();

                $query = $this->db->query("Select category_id from category where parent_category_id in (" . implode(",", $parent_categories) . ")");
                foreach ($query->result_array() as $row) {
                    $new_categories[] = $row["category_id"];
                }

                if (count($new_categories) > 0) {
                    $this->fitment_categories = array_merge($this->fitment_categories, $new_categories);
                    $parent_categories = $new_categories;
                }

            } while(count($new_categories) > 0);
        }

        foreach ($this->fitment_categories as $c_id) {
            $this->fitment_category_map["c" . $c_id] = true;
        }
    }

    public function processParts($limit = 4000) {
        $CI =& get_instance();
        $CI->load->model("parts_m");
        $debug = false;

        $this->initializeFitmentCategories();

        $this->db->limit($limit);
        $this->db->order_by('recCreated ASC');
        $records = $this->selectRecords('queued_parts');
        // echo '<pre>';
        // print_r($records);
        // echo '</pre>';
        if ($records) {
            for ($i = 0; $i < count($records); $i++) {
                $use_retail_price = $CI->parts_m->partIsRetailOnly($records[$i]['part_id']);
                $category = $this->getSecondBreadCrumb($records[$i]['part_id']);
                $category_markup = array();
                foreach ($category as $cat) {
                    $category_markup[] = $cat['id'];
                }

                $this->db->select('MIN(category.mark_up) as markup');
                $where = array('category.mark_up > ' => 0);
                $this->db->where_in('category_id', $category_markup);
                //$this->db->join('partcategory', 'partcategory.category_id = category.category_id');
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
                $partnumbers = $this->selectRecords('partnumber', $where);

                $this->db->select('partnumber.*, partdealervariation.stock_code, partdealervariation.cost as dealer_cost');
                $where = array('partpartnumber.part_id' => $records[$i]['part_id'], 'partnumber.price > ' => 0);
                $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id ');
                $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
                $partdealernumbers = $this->selectRecords('partnumber', $where);

                $exclude = $brand_markup['exclude_market_place'];
                $closeout = $brand_markup['closeout_market_place'];
                $categoryMarkUp = is_numeric(@$categories['markup']) ? $categories['markup'] : 0;
                $brandMarkUp = is_numeric(@$brand_markup['markup']) ? $brand_markup['markup'] : 0;
                //$brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : NULL;
                $brandMAPPercent = (array_key_exists("map_percent", $brand_map_percent) && !is_null($brand_map_percent["map_percent"]) && is_numeric(@$brand_map_percent['map_percent'])) ? $brand_map_percent['map_percent'] : NULL;

                if ($partnumbers) {
                    foreach ($partnumbers as $rec) {
                        if ($debug) {
                            print "Distributor part: ";
                            print_r($rec);
                        }

                        if ($this->lightspeed_m->partNumberIsLightspeed($rec['partnumber_id'])) {
                            $finalSalesPrice = $this->lightspeed_m->lightspeedPrice($rec['partnumber_id']);
                            if ($debug) {
                                print "Use Lightspeed: Final sales price: $finalSalesPrice \n";
                            }
                        } elseif ($use_retail_price) {
                            $finalSalesPrice = $rec['price']; // JLB 07-15-17 New override.
                            if ($debug) {
                                print "Use retail: Final sales price: $finalSalesPrice \n";
                            }
                        } else {
                            //echo $categoryMarkUp.' : '. $brandMarkUp.' : '.$brandMAPPercent.' : '.$productMarkUp;
                            $finalMarkUp = 0;
                            $productMarkUp = $rec['markup'];

                            if ($productMarkUp > 0) { // Product Markup Trumps everything
                                $finalSalesPrice = ($rec['cost'] * $productMarkUp / 100) + $rec['cost'];
                                if ($debug) {
                                    print "Using product markup $productMarkUp to get sales price $finalSalesPrice \n";
                                }
                            } else {
                                // Calculate category and Brand Percent Mark up

                                if ($brandMarkUp > 0) {
                                    $finalMarkUp = $brandMarkUp;
                                    if ($debug) {
                                        print "Using brand markup $brandMarkUp \n";
                                    }

                                } else if ($categoryMarkUp > 0) {
                                    $finalMarkUp = $categoryMarkUp;
                                    if ($debug) {
                                        print "Using category markup $categoryMarkUp \n";
                                    }
                                    if (($brandMarkUp > 0) && ($brandMarkUp < $finalMarkUp)) {
                                        $finalMarkUp = $brandMarkUp;
                                    }
                                    if ($debug) {
                                        print "Using final markup $finalMarkUp \n";
                                    }
                                }
                                //else
                                // Get Final Sales Price for Calculating vs MAP Pricing

                                if ($finalMarkUp > 0) {
                                    $finalSalesPrice = ($rec['cost'] * $finalMarkUp / 100) + $rec['cost'];
                                }

                                if ($debug) {
                                    print "Final sales price: $finalSalesPrice \n";
                                }
                                // Calculate MAP Pricing

                                if ((!is_null($brandMAPPercent)) && (isset($finalSalesPrice)) && ($rec['stock_code'] != 'Closeout')) {
                                    if ($debug) {
                                        print "Applying brand MAP percent $brandMAPPercent\n";
                                    }

                                    $mapPrice = (((100 - $brandMAPPercent) / 100) * $rec['price']);
                                    if ($mapPrice > $finalSalesPrice) {
                                        $finalSalesPrice = $mapPrice;
                                    }

                                    if ($debug) {
                                        print "Final sales price $finalSalesPrice\n";
                                    }
                                }
                            }
                        }

                        if (!isset($finalSalesPrice)) {
                            $finalSalesPrice = $rec['price'];
                            if ($debug) {
                                print "Final sales undefined using price $finalSalesPrice\n";
                            }
                        }

                        if ($finalSalesPrice > $rec['price'] && !$this->lightspeed_m->partNumberIsLightspeed($rec['partnumber_id'])) {
                            $finalSalesPrice = $rec['price'];
                            if ($debug) {
                                print "Final sales price too big using price $finalSalesPrice\n";
                            }
                        }

                        if ($finalSalesPrice < $rec['cost']) {
                            $finalSalesPrice = max($rec['price'], $rec['cost']);
                            if ($debug) {
                                print "Final sales price too small using price $finalSalesPrice\n";
                            }
                        }

                        if ($debug) {
                            print "Final price $finalSalesPrice\n";
                        }


                        $data = array('sale' => $finalSalesPrice,
                            'exclude_market_place' => $exclude,
                            'closeout_market_place' => $closeout);
                        $where = array('partnumber_id' => $rec['partnumber_id']);
                        $this->updateRecord('partnumber', $data, $where, FALSE);
                    }
                }

                //Dealer Inventory
                if ($partdealernumbers) {
                    foreach ($partdealernumbers as $rec) {
                        if ($debug) {
                            print "Dealer part: ";
                            print_r($rec);
                        }

                        if ($this->lightspeed_m->partNumberIsLightspeed($rec['partnumber_id'])) {
                            $finalSalesPrice = $this->lightspeed_m->lightspeedPrice($rec['partnumber_id']);
                            if ($debug) {
                                print "Use Lightspeed: Final sales price: $finalSalesPrice \n";
                            }
                        } else if ($use_retail_price) {
                            $finalSalesPrice = $rec['price'];
                            if ($debug) {
                                print "Use retail: Final sales price: $finalSalesPrice \n";
                            }
                        } else {

                            $finalMarkUp = 0;
                            $productMarkUp = $rec['markup'];

                            if ($productMarkUp > 0) { // Product Markup Trumps everything
                                $finalSalesPrice = ($rec['dealer_cost'] * $productMarkUp / 100) + $rec['dealer_cost'];
                                if ($debug) {
                                    print "Using product markup $productMarkUp to get sales price $finalSalesPrice \n";
                                }
                            } else {
                                // Calculate category and Brand Percent Mark up
                                if ($brandMarkUp > 0) {
                                    $finalMarkUp = $brandMarkUp;
                                    if ($debug) {
                                        print "Using brand markup $brandMarkUp \n";
                                    }
                                } else if ($categoryMarkUp > 0) {
                                    $finalMarkUp = $categoryMarkUp;
                                    if ($debug) {
                                        print "Using category markup $categoryMarkUp \n";
                                    }
                                    if (($brandMarkUp > 0) && ($brandMarkUp < $finalMarkUp)) {
                                        $finalMarkUp = $brandMarkUp;
                                    }
                                    if ($debug) {
                                        print "Using final markup $finalMarkUp \n";
                                    }
                                }
                                //else
                                // Get Final Sales Price for Calculating vs MAP Pricing

                                if ($finalMarkUp > 0) {
                                    $finalSalesPrice = ($rec['dealer_cost'] * $finalMarkUp / 100) + $rec['dealer_cost'];
                                }
                                if ($debug) {
                                    print "Final sales price: $finalSalesPrice \n";
                                }

                                // Calculate MAP Pricing
                                if ((!is_null($brandMAPPercent)) && (isset($finalSalesPrice)) && ($rec['stock_code'] != 'Closeout')) {
                                    if ($debug) {
                                        print "Applying brand MAP percent $brandMAPPercent\n";
                                    }

                                    $mapPrice = (((100 - $brandMAPPercent) / 100) * $rec['price']);
                                    if ($mapPrice > $finalSalesPrice) {
                                        $finalSalesPrice = $mapPrice;
                                    }

                                    if ($debug) {
                                        print "Final sales price $finalSalesPrice\n";
                                    }
                                }
                            }
                        }

                        if (!isset($finalSalesPrice)) {
                            $finalSalesPrice = $rec['price'];
                            if ($debug) {
                                print "Final sales undefined using price $finalSalesPrice\n";
                            }
                        }

                        if ($finalSalesPrice > $rec['price'] && !$this->lightspeed_m->partNumberIsLightspeed($rec['partnumber_id'])) {
                            $finalSalesPrice = $rec['price'];
                            if ($debug) {
                                print "Final sales price too big using price $finalSalesPrice\n";
                            }
                        }

                        if ($finalSalesPrice < $rec['dealer_cost']) {
                            $finalSalesPrice = max($rec['price'], $rec['dealer_cost']);
                            if ($debug) {
                                print "Final sales price too small using price $finalSalesPrice\n";
                            }
                        }

                        if ($debug) {
                            print "Final price $finalSalesPrice\n";
                        }

                        $data = array('dealer_sale' => $finalSalesPrice,
                            'exclude_market_place' => $exclude,
                            'closeout_market_place' => $closeout);
                        $where = array('partnumber_id' => $rec['partnumber_id']);
                        $this->updateRecord('partnumber', $data, $where, FALSE);
                    }
                }


                /*
                 * JLB 10-22-17
                 * We added a new feature where we are flagging the part as universal fitment or not.
                 */
                $universal_fitment = 0;

                if ($this->part_hasFitmentAncestorCategory($records[$i]["part_id"])) {
                    $universal_fitment = $this->part_hasFitment($records[$i]["part_id"]) ? 0 : 1;
                }
                $this->db->query("Update part set universal_fitment = ? where part_id = ? limit 1", array($universal_fitment, $records[$i]["part_id"]));


                $where = array('part_id' => $records[$i]['part_id']);
                $this->deleteRecord('queued_parts', $where);
            }
        }


        $CI =& get_instance();
        if (defined("ENABLE_LIGHTSPEED") && ENABLE_LIGHTSPEED) {
            $this->lightspeed_m->partPriceFix();
        }
        $this->load->model('cron/cronjobhourly', 'TheCronJob');
        $this->TheCronJob->fixNullManufacturers();
        $this->TheCronJob->fixBrandSlugs();
        $this->TheCronJob->fixBrandLongNames();
    }

    public function part_hasFitmentAncestorCategory($part_id) {
        $query = $this->db->query("Select category_id from partcategory where part_id = ?", array($part_id));

        foreach ($query->result_array() as $row) {
            if (array_key_exists("c" . $row["category_id"], $this->fitment_category_map)) {
                return true;
            }
        }

        return false;
    }

    public function part_hasFitment($part_id) {
        $query = $this->db->query("Select count(*) as cnt from part join partpartnumber using (part_id) join partnumbermodel using (partnumber_id) where part.part_id = ?", array($part_id));
        $count = 0;
        foreach ($query->result_array() as $row) {
            $count = $row['cnt'];
        }
        return $count > 0;
    }

    public function processPartsInventoryReceiving($limit = 10) {
        $this->db->limit($limit);
        $this->db->order_by('recCreated ASC');
        $records = $this->selectRecords('queued_parts');
        if ($records) {
            for ($i = 0; $i < count($records); $i++) {
                $category = $this->getSecondBreadCrumb($records[$i]['part_id']);
                $category_markup = array();
                foreach ($category as $cat) {
                    $category_markup[] = $cat['id'];
                }

                $this->db->select('MIN(category.mark_up) as markup');
                $where = array('category.mark_up > ' => 0);
                $this->db->where_in('category_id', $category_markup);
                //$this->db->join('partcategory', 'partcategory.category_id = category.category_id');
                $categories = $this->selectRecord('category', $where);

                // $this->db->select('MIN(category.mark_up) as markup');
                // $where = array('partcategory.part_id' => $records[$i]['part_id'], 'category.mark_up > ' => 0);
                // $this->db->join('partcategory', 'partcategory.category_id = category.category_id');
                // $categories = $this->selectRecord('category', $where);

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
                $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
                $partnumbers = $this->selectRecords('partnumber', $where);

                $exclude = $brand_markup['exclude_market_place'];
                $closeout = $brand_markup['closeout_market_place'];
                $categoryMarkUp = is_numeric(@$categories['markup']) ? $categories['markup'] : 0;
                $brandMarkUp = is_numeric(@$brand_markup['markup']) ? $brand_markup['markup'] : 0;
                //     $brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : NULL;
                $brandMAPPercent = (array_key_exists("map_percent", $brand_map_percent) && !is_null($brand_map_percent["map_percent"]) && is_numeric(@$brand_map_percent['map_percent'])) ? $brand_map_percent['map_percent'] : NULL;
                if ($partnumbers) {
                    foreach ($partnumbers as $rec) {
                        $finalMarkUp = 0;
                        $productMarkUp = $rec['markup'];

                        if ($this->lightspeed_m->partNumberIsLightspeed($rec['partnumber_id'])) {
                        $finalSalesPrice = $this->lightspeed_m->lightspeedPrice($rec['partnumber_id']);
                        } elseif ($productMarkUp > 0) { // Product Markup Trumps everything
                            $finalSalesPrice = ($rec['cost'] * $productMarkUp / 100) + $rec['cost'];
                        } else {
                            // Calculate category and Brand Percent Mark up

                            if ($brandMarkUp > 0) {
                                $finalMarkUp = $brandMarkUp;
                            } else if ($categoryMarkUp > 0) {
                                $finalMarkUp = $categoryMarkUp;
                                if (($brandMarkUp > 0) && ($brandMarkUp < $finalMarkUp))
                                    $finalMarkUp = $brandMarkUp;
                            }
                            // elseif ($brandMarkUp > 0)
                            // $finalMarkUp = $brandMarkUp;
                            // Get Final Sales Price for Calculating vs MAP Pricing

                            if ($finalMarkUp > 0)
                                $finalSalesPrice = ($rec['cost'] * $finalMarkUp / 100) + $rec['cost'];

                            // Calculate MAP Pricing

                            if ((!is_null($brandMAPPercent)) && (isset($finalSalesPrice)) && ($rec['stock_code'] != 'Closeout')) {
                                $mapPrice = (((100 - $brandMAPPercent) / 100) * $rec['price']);
                                if ($mapPrice > $finalSalesPrice)
                                    $finalSalesPrice = $mapPrice;
                            }
                        }
                        /*
                         * JLB  07-23-17
                         * On this day, we discovered that Tucker Rocky will accidentally price a part wrong and put it below cost.
                         * Therefore, we have to change this third check to be the max of price and cost so we never sell below cost.
                         */
                        if (!isset($finalSalesPrice)) {
                            $finalSalesPrice = $rec['price'];
                        }

                        if ($finalSalesPrice > $rec['price'] && !$this->lightspeed_m->partNumberIsLightspeed($rec['partnumber_id'])) {
                            $finalSalesPrice = $rec['price'];
                        }

                        if ($finalSalesPrice < $rec['cost']) {
                            $finalSalesPrice = max($rec['price'], $rec['cost']);
                        }

                        $data = array('dealer_sale' => $finalSalesPrice);
                        $where = array('partnumber_id' => $rec['partnumber_id']);
                        $this->updateRecord('partnumber', $data, $where, FALSE);
                    }
                }
                $where = array('part_id' => $records[$i]['part_id']);
                $this->deleteRecord('queued_parts', $where);
            }
        }
    }

    public function updatePart($id, $post) {
        echo '<pre>';
        print_r($post);
        print_r($id);

        die("********");
        $markup = $post['markup'];
        $excludeMarketPlace = ($post['market_places'] == 'exclude_market_place') ? 1 : 0;
        $closeoutMarketPlace = ($post['market_places'] == 'closeout_market_place') ? 1 : 0;
        $featured = ($post['featured'] == 1) ? 1 : 0;
        $post['featured'] = $featured;
        $featured_brand = ($post['featured_brand'] == 1) ? 1 : 0;
        $post['featured_brand'] = $featured_brand;
        unset($post['market_places']);
        unset($post['markup']);
        unset($post['exclude_market_place']);
        unset($post['closeout_market_place']);
        $where = array('part_id' => $id);
        if (!empty($post))
            $this->updateRecord('part', $post, $where, FALSE);

        $where = array('partpartnumber.part_id' => $id, 'price > ' => 0);
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id ');
        $partnumbers = $this->selectRecords('partnumber', $where);

        if (@$partnumbers) {
            foreach ($partnumbers as $pn) {
                $data = array('markup' => $markup,
                    'exclude_market_place' => $excludeMarketPlace,
                    'closeout_market_place' => $closeoutMarketPlace);

                $where = array('partnumber_id' => $pn['partnumber_id']);
                $this->updateRecord('partnumber', $data, $where, FALSE);
            }
        }
        $where = array('part_id' => $id);
        if (!$this->recordExists('queued_parts', $where)) {
            $data = array('part_id' => $id, 'recCreated' => time());
            $this->createRecord('queued_parts', $data, FALSE);
        }
    }

    public function deleteCategory($id) {
        $where = array('parent_category_id' => $id);
        if ($this->recordExists('category', $where)) {
            $this->db->where($where);
            $categories = $this->getCategories(FALSE);
            $mainCat = $this->getCategory($id);
            foreach ($categories as $cat) {
                $data['long_name'] = $this->createCategoryLongName($mainCat['parent_category_id'], $cat['name']);
                $success = $this->updateRecord('category', $data, array('category_id' => $cat['category_id']), FALSE);
            }
        }
        $where = array('category_id' => $id);
        $this->deleteRecord('category', $where);
    }

    public function createCategoryLongName($parentId, $name) {
        $parentCatRec = $this->getCategory($parentId);
        $longName = $parentCatRec['long_name'];
        if (@$parentCatRec['long_name'])
            $longName .=' > ';
        $longName .= $name;
        return $longName;
    }

    public function updateCategoryLongNames($id) {
        $where = array('parent_category_id' => $id);
        if ($this->recordExists('category', $where)) {
            $this->db->where($where);
            $categories = $this->getCategories(FALSE);
            foreach ($categories as $cat) {
                $data['long_name'] = $this->createCategoryLongName($id, $cat['name']);
                $success = $this->updateRecord('category', $data, array('category_id' => $cat['category_id']), FALSE);
            }
        }
    }

    //*****************END CATEGORIES********************//
    //*****************BRANDS********************//
    public function getBrands($dd = TRUE) {
        $this->db->order_by('name');
        $records = $this->selectRecords('brand');
        $list = array(0 => '---');
        if ($dd) {
            if (@$records) {
                foreach ($records as &$record) {
                    $list[$record['brand_id']] = $record['name'];
                }
            }
            return $list;
        } else
            return $records;
    }

    public function getBrand($id) {
        $where = array('brand_id' => $id);
        $record = $this->selectRecord('brand', $where);
        return $record;
    }

    public function getBrandVideos($id) {
        $where = array('brand_id' => $id);
        $records = $this->selectRecords('brand_video', $where);
        $list = array();
        if (@$records) {
            foreach ($records as &$record) {
                $list[$record['id']] = $record;
            }
        }
        return $list;
    }

	public function getCategoryVideos( $id ) {
        $where = array('category_id' => $id);
        $records = $this->selectRecords('category_video', $where);
        $list = array();
        if (@$records) {
            foreach ($records as &$record) {
                $list[$record['id']] = $record;
            }
        }
        return $list;
	}

    public function getProductVideos($id) {
        $where = array('part_id' => $id);
        $records = $this->selectRecords('part_video', $where);
        $list = array();
        if (@$records) {
            foreach ($records as &$record) {
                $list[$record['id']] = $record;
            }
        }
        return $list;
    }

    public function updateBrandVideos($id, $arr) {
        $this->db->delete('brand_video', array('brand_id' => $id));
        if (!empty($arr)) {
            $this->db->insert_batch('brand_video', $arr);
        }
    }

	public function updateCategoryVideos( $id, $arr ) {
        $this->db->delete('category_video', array('category_id' => $id));
        if (!empty($arr)) {
            $this->db->insert_batch('category_video', $arr);
        }
	}

    public function insertSizeChart($arr) {
        if (!empty($arr)) {
            $this->db->insert('brand_sizechart', $arr);
        }
    }

    public function updateSizeChart($id, $arr) {
        if (!empty($arr)) {
            $this->db->where('id', $id);
            $this->db->update('brand_sizechart', $arr);
        }
    }

    public function updateBrandSizeChart($id, $arr) {
        if (!empty($arr)) {
            $this->db->where('brand_id', $id);
            $this->db->update('brand', $arr);
        }
    }

    public function updateProductVideos($id, $arr) {
        $this->db->delete('part_video', array('part_id' => $id));
        if (!empty($arr)) {
            $this->db->insert_batch('part_video', $arr);
        }
    }

    public function updateProductSizeChart($arr) {
        $where = array('part_id' => $arr['part_id']);
        $part_sizechart = $this->selectRecord('part_sizechart', $where);
        //$part_sizechart = 
        if (empty($part_sizechart)) {
            $this->db->insert('part_sizechart', $arr);
        } else {
            $this->db->where('part_id', $arr['part_id']);
            unset($arr['part_id']);
            $this->db->update('part_sizechart', $arr);
        }
    }

    public function getBrandByPartId($part_id) {
        $this->db->select('brand_id');
        $where = array('part_id' => $part_id);
        $records = $this->selectRecords('partbrand', $where);
        if ($records) {
            $newRecArr = $records;
            $records = array();
            foreach ($newRecArr as $key => $rec) {
                $records[$rec['brand_id']] = $rec;
            }
        }
        return $records;
    }

    public function getSizeChart($id) {
        $this->db->select('*');
        $this->db->where('brand_id', $id);
        $records = $this->selectRecords('brand_sizechart');
        return $records;
    }

    public function updateBrand($post) {
        $data = array();
        $data['active'] = @$post['active'] ? 1 : 0;
        $data['featured'] = @$post['featured'] ? 1 : 0;
        $data['exclude_market_place'] = ($post['market_places'] == 'exclude_market_place') ? 1 : 0;
        $data['closeout_market_place'] = ($post['market_places'] == 'closeout_market_place') ? 1 : 0;
        $data['name'] = $post['name'];
        $data['meta_tag'] = $post['meta_tag'];
        $data['image'] = @$post['image'];
        $data['mark_up'] = @$post['mark-up'];
        $data['keywords'] = @$post['keywords'];
        $data['slug'] = @$post['slug'];
        $data['title'] = @$post['title'];
        $data['notice'] = @$post['notice'];
        $data['map_percent'] = @$post['map_percent'];
        if (@$post['MAP_NULL'])
            $data['map_percent IS NULL'] = NULL;
        $data['long_name'] = $data['name'];
        if (@$post['brand_id'])
            $success = $this->updateRecord('brand', $data, array('brand_id' => $post['brand_id']), FALSE);
        else {
            $data['mx'] = 0;
            $post['brand_id'] = $this->createRecord('brand', $data, FALSE);
        }
        $this->updateBrandMarkUp($post['brand_id']);


        // JLB 05-29-18
        $query = $this->db->query("Select manufacturer_id from manufacturer where brand_id = ?", array($post['brand_id']));
        $manufacturer_id = 0;
        foreach ($query->result_array() as $row) {
            $manufacturer_id = $row["manufacturer_id"];
        }

        if ($manufacturer_id == 0) {
            // we must add it!
            $this->db->query("Insert into manufacturer (brand_id, name, label) values (?, ?)", array($post['brand_id'], $data['name'], $data['name']));
        } else if ($data['mx'] == 0) {
            // we must update it!
            $this->db->query("Update manufacturer set name = ?, label = ? where brand_id = ?", array($data['name'],$data['name'], $post['brand_id']));
        }

    }

    public function updateBrandMarkUp($brand_id) {

        $where = array('brand_id' => $brand_id);
        $this->db->select('part_id');
        $records = $this->selectRecords('partbrand', $where);
        if ($records) {
            foreach ($records as $rec) {
                $where = array('part_id' => $rec['part_id']);
                if (!$this->recordExists('queued_parts', $where)) {
                    $data = array('part_id' => $rec['part_id'], 'recCreated' => time());
                    $this->createRecord('queued_parts', $data, FALSE);
                }
            }
        }
    }

    public function deleteBrand($id) {
        $where = array('brand_id' => $id);
        $this->deleteRecord('brand', $where);
    }

    public function updateBrandLongNames($id) {
        $where = array('parent_brand_id' => $id);
        if ($this->recordExists('brand', $where)) {
            $this->db->where($where);
            $brands = $this->getBrands(FALSE);
            foreach ($brands as $brand) {
                $data['long_name'] = $this->createBrandLongName($id, $brand['name']);
                $success = $this->updateRecord('brand', $data, array('brand_id' => $brand['brand_id']), FALSE);
            }
        }
    }

    public function checkBrandSlug($str, $brand_id) {
        //$query = $this->db->query("SELECT brand_id FROM brand WHERE brand.slug='".$str."'");
        $query = $this->db->query('SELECT brand_id FROM brand WHERE brand.slug="' . $str . '"');
        $dt = $query->result_array();
        $query->free_result();
        if ($dt[0]['brand_id'] == $brand_id || empty($dt)) {
            return true;
        } else {
            return false;
        }
    }

    //********************************* END BRANDS *************************************//
    //*********************************** WISHLISTS **************************************//

    public function getWishlists() {
        $records = FALSE;
        $records = $this->selectRecords('wishlist');
        if ($records) {
            foreach ($records as &$rec) {
                $query = $this->db->query('SELECT * FROM wishlist_part
												JOIN part ON part.part_id = wishlist_part.part_id
												JOIN partpartnumber ON partpartnumber.part_id = part.part_id
												JOIN partnumber ON partnumber.partnumber_id = partpartnumber.partnumber_id
												WHERE wishlist_part.wishlist_id = ' . $rec['id']);
                $rec['parts'] = $query->result_array();
                $query->free_result();
            }
        }
        return $records;
    }

    //********************************** END WISHLISTS *********************************//
    //********************************** TAXES ***********************************************//

    public function getTaxes() {
        $records = FALSE;
        $records = $this->selectRecords('taxes');
        return $records;
    }

    public function updateTaxes($post) {
        if (!empty($post['id'])) {
            foreach ($post['id'] as $key => $id) {
                $where = array('id' => $id);
                $data = array();
                $data['active'] = @$post['active'][$id] ? 1 : 0;
                $data['percentage'] = @$post['active'][$id] ? 1 : 0;
                $data['tax_value'] = $post['tax_value'][$id];
                $success = $this->updateRecord('taxes', $data, $where, FALSE);
            }
        }
    }

    //********************************** END TAXES ***************************************//
    //********************************** SHIPPING RULES *******************************//

    public function getShippingRules() {
        $records = FALSE;
        $records = $this->selectRecords('shipping_rules');
        return $records;
    }

    public function getShippingRule($id) {
        $record = FALSE;
        $where = array('id' => $id);
        $record = $this->selectRecord('shipping_rules', $where);
        return $record;
    }

    public function updateShippingRules($formFields) {
        if (@$formFields['create_new']) {
            unset($formFields['create_new']);
            unset($formFields['id']);
            $this->createRecord('shipping_rules', $formFields, FALSE);
        } else {
            unset($formFields['edit']);
            $where = array('id' => $formFields['id']);
            $this->updateRecord('shipping_rules', $formFields, $where, FALSE);
        }
    }

    public function deleteShippingRule($id) {
        $where = array('id' => $id);
        $this->deleteRecord('shipping_rules', $where);
    }

    //********************************** END SHIPPING RULES *********************************//
    //************************************** DISTRIBUTORS *****************************************//

    public function getDistributors() {
        $records = FALSE;
        $where = array('type' => 'distributor');
        $records = $this->selectRecords('accounts', $where);
        return $records;
    }

    public function getDistributorForProductReceiving() {
        $records = FALSE;
        //$where = array('active' => '1');
        $records = $this->selectRecords('distributor', $where);
        return $records;
    }

    public function getAllCloseoutRepringRule($brand_id = null) {
        $records = FALSE;
        $where = array('brand_id' => $brand_id, 'status != 2 ' => null);
        $records = $this->selectRecords('closeout_rules', $where);
        return $records;
    }

    public function updateCloseoutRules($data) {
        foreach ($data as $k => $v) {
            $where = array('id' => $v['id']);
            if (@$v['brand_id']) {
                $where['brand_id'] = $v['brand_id'];
            }
            $record = $this->selectRecord('closeout_rules', $where);
            if (@$record) {
                $where = array('id' => $v['id']);
                unset($v['id']);
                $this->updateRecord('closeout_rules', $v, $where, FALSE);
            } else {
                unset($v['id']);
                $this->db->insert('closeout_rules', $v);
            }
        }
    }

    public function updateDistributors($formFields) {
        $where = array('id' => $formFields['id']);
        $this->updateRecord('accounts', $formFields, $where, FALSE);
    }

    public function deleteCloseoutRepringRule($rule_id) {
        $where = array('id' => $rule_id);
        $data = array('status' => 2);
        $this->updateRecord('closeout_rules', $data, $where, FALSE);
    }

    //***************************************** END DISTRIBUTORS *********************************//

    public function updateAdminShippingProfile($data) {
        if ($data['deal']) {
            //$where = array('key' => 'deal_percentage');
            //$configdata = array('value' => $data['deal']);
            //$return = $this->updateRecord('config', $configdata, $where, FALSE);
            unset($data['data']);
        }
        $data['google_trust'] = json_encode($data['google_trust']);
        $where = array('id' => 1);
        $return = $this->updateRecord('contact', $data, $where, FALSE);

        // JLB 10-11-17
        // We have to hack the navigation...
        if (array_key_exists("partsfinder_link", $data) && $data["partsfinder_link"] != "") {
            // does it exist?
            $query = $this->db->query("Select * from primarynavigation where class = 'last oemparts'");
            $matches = $query->result_array();
            if (count($matches) == 0) {
                $query = $this->db->query("Select max(ordinal) as max_ordinal from primarynavigation");
                $matches = $query->result_array();
                $max_ordinal = $matches[0]["max_ordinal"];

                $this->db->query("Insert into primarynavigation (active, url, label, class, span_id, image_url, external, ordinal, mobile_label) values (1, ?, 'Shop OEM Parts', 'last oemparts', 'sop', '/assets/oem_parts.png', 1, ?, 'Shop OEM Parts')", array($data["partsfinder_link"], $max_ordinal + 1));
            } else {
                $this->db->query("Update primarynavigation set url = ? where class = 'last oemparts' limit 1;", array($data["partsfinder_link"]));
            }
        } else {
            $this->db->query("Delete from primarynavigation where class = 'last oemparts' limit 1");
        }


        return $return;
    }

    public function getAdminShippingProfile() {
        $where = array('id' => 1);
        $record = $this->selectRecord('contact', $where);
        return $record;
    }

    public function getDealPercentage() {
        $where = array('key' => 'deal_percentage');
        $record = $this->selectRecord('config', $where);
        return $record['value'];
    }

    public function updateImage($imageName, $tableName, $id) {
        switch ($tableName) {
            case 'product':
                $this->updateRecord('product', array('image' => $imageName), array('sku' => $id), FALSE);
                break;
            case 'category':
                $this->updateRecord('category', array('image' => $imageName), array('code' => $id), FALSE);
                break;
        }
    }

    public function updateSettings($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $inputData = array('value' => $value);
                $where = array('key' => $key);
                $this->updateRecord('config', $inputData, $where, FALSE);
            }
        }
    }

    public function updateOrderTrackingNumber($post) {
        $where = array('id' => $post['id']);
        $this->db->select('ship_tracking_code');
        $record = $this->selectRecord('order', $where);
        if ($record['ship_tracking_code'])
            $trackingCodes = json_decode($record['ship_tracking_code']);
        else
            $trackingCodes = array();
        $trackingCodes[] = array($post['carrier'], $post['ship_tracking_code']);
        $encoded = json_encode($trackingCodes);
        $orderRec = array('ship_tracking_code' => $encoded);
        $this->updateRecord('order', $orderRec, $where, FALSE);
        return $trackingCodes; // JLB 08-24-17 Send It Back!
    }

    public function removeTrackingFromOrder($post) {
        $where = array('id' => $post['id']);
        $this->db->select('ship_tracking_code');
        $record = $this->selectRecord('order', $where);
        if ($record['ship_tracking_code']) {
            $trackingCodes = json_decode($record['ship_tracking_code'], TRUE);
            unset($trackingCodes[$post['key']]);
            $trackingCodes = array_values($trackingCodes);
            if (!empty($trackingCodes))
                $encoded = json_encode($trackingCodes);
            else
                $encoded = NULL;
            $orderRec = array('ship_tracking_code' => $encoded);
            $this->updateRecord('order', $orderRec, $where, FALSE);
            return $trackingCodes;
        }  else {
            return NULL;
        }
    }

    public function recordOrderCreation($order) {
        $orderRec = array();
        // Create Order record including total product sales and shipping
        $orderRec['contact_id'] = $order['billing_id'];
        $orderRec['shipping_id'] = $order['shipping_id'];
        $orderRec['order_id'] = $order['order_id'];
        $orderRec['product_cost'] = $order['product_cost'];
        $orderRec['shipping_cost'] = $order['shipping_cost'];
        $orderRec['user_id'] = $order['user_id'];
        if (@$order['transAmount'])
            $orderRec['sales_price'] = @$order['transAmount'] - @$order['shipping'] - @$order['tax'];
        $orderRec['shipping'] = @$order['shipping'];
        
        $where1 = array('order_id' => $order['order_id']);
        $products = $this->selectRecords('order_product', $where1);

        $grandTotal = 0;
        foreach( $products as $productData ) {
            if( $productData['status'] != 'Refunded' ) {
                $grandTotal += $productData['price'];
            }
        }
        
        if (@$grandTotal)
            $orderRec['sales_price'] = @$grandTotal;
        
        $orderRec['tax'] = @$order['tax'];
        if (@$order['special_instr'])
            $orderRec['special_instr'] = @$order['special_instr'];
        if (@$order['order_id']) {
            $where = array('id' => $order['order_id']);
            $this->updateRecord('order', $orderRec, $where, FALSE);
        } else
            $order['order_id'] = $this->createRecord('order', $orderRec, FALSE);
        // Create order_product record for each item purchased including price charged for each item.
        if (is_array(@$cart['products'])) {
            foreach ($cart['products'] as $key => $product) {
                $data = array('order_id' => $orderId, 'product_sku' => $key, 'price' => @$product['finalPrice'], 'qty' => @$product['qty']);
                $this->createRecord('order_product', $data, FALSE);
            }
        }
        return $order['order_id'];
    }

    public function getOrders($filter, $limit = NULL) {
        $this->db->select('order.id AS order_id, ' .
                'order.user_id AS user_id, ' .
                'order.contact_id AS contact_id, ' .
                'order.shipping_id AS shipping_id, ' .
                'order.sales_price AS sales_price, ' .
                'order.shipping AS shipping, ' .
                'order.weight AS weight, ' .
                'order.tax AS tax, ' .
                'order.source AS source, ' .
                'order.Reveived_date AS processed_date, ' .
                'order.will_call AS will_call, ' .
                'order.process_date AS process_date, ' .
                'order.batch_number AS batch_number, ' .
                'order.special_instr AS special_instr, ' .
                'order.Reveived_date AS Reveived_date,' .
                'order.order_date AS order_date, ' .
                'contact.first_name AS first_name, ' .
                'contact.last_name AS last_name, ' .
                'contact.street_address AS street_address, ' .
                'contact.address_2 AS address_2, ' .
                'contact.city AS city, ' .
                'contact.state AS state, ' .
                'contact.zip AS zip, ' .
                'contact.company AS company,' .
                'shipping.first_name AS shipping_first_name, ' .
                'shipping.last_name AS shipping_last_name, ' .
                'shipping.street_address AS shipping_street_address, ' .
                'shipping.address_2 AS shipping_address_2, ' .
                'shipping.city AS shipping_city, ' .
                'shipping.state AS shipping_state, ' .
                'shipping.zip AS shipping_zip, ' .
                'shipping.company AS shipping_company, ' .
                ' sum(order_transaction.amount) as paid');
        $records = FALSE;

        if ($filter['limit']) {
            $this->db->limit($filter['limit'], $filter['offset']);
        }

        //if (!is_null($limit))
        //$this->setOrderFilter(@$filter);
        if (@$filter) {
            if (isset($filter['search']) && $filter['search'] != '') {
                $custom_where = "(";
                $custom_where .= ' order.id like "%' . strtoupper(trim($filter['search'])) . '%" OR';
                $custom_where .= ' shipping.phone like "%' . strtoupper(trim($filter['search'])) . '%" OR';
                $custom_where .= ' shipping.email like "%' . strtoupper(trim($filter['search'])) . '%" OR';
                $custom_where .= ' concat(shipping.first_name," ", shipping.last_name) like "%' . strtoupper(trim($filter['search'])) . '%" OR';

                $custom_where .= ' CONCAT_WS(" ", shipping.street_address,shipping.address_2,shipping.city,shipping.state,shipping.zip) like "%' . strtoupper(trim($filter['search'])) . '%" OR';
                //$custom_where .= ' shipping.last_name like "%'.strtoupper(trim($filter['search'])).'%" OR';
                $custom_where = rtrim($custom_where, 'OR') . ')';
                $this->db->where($custom_where);
            }
            if (isset($filter['date_search_from']) && isset($filter['date_search_to']) && $filter['date_search_from'] != '' && $filter['date_search_to'] != '' && $filter['days'] == 'Custom') {
                if (in_array('pending', $filter['status']) || in_array('declined', $filter['status'])) {
                    //$custom_where1 = 'order.order_date is null';
                    //$this->db->where($custom_where1);
                    //$from = date("Y-m-d", date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-".$filter['days']." day" );
                    $from = date("Y-m-d", strtotime($filter['date_search_from']));
                    $to = date("Y-m-d", strtotime(date("Y-m-d", strtotime($filter['date_search_to'])) . "+1 day"));
                    //$to = date("Y-m-d", strtotime($filter['date_search_to']));
                    $custom_where1 = '(order.order_date is null and order.Reveived_date >= "' . $from . '" AND order.Reveived_date <= "' . $to . '") OR (order.order_date >= "' . strtotime($from) . '" AND order.order_date <= "' . strtotime($to) . '")';
                    $this->db->where($custom_where1);
                } else if (in_array('pending', $filter['status'])) {
                    $custom_where1 = 'order.order_date is null';
                    $this->db->where($custom_where1);
                    //$from = date("Y-m-d", date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-".$filter['days']." day" );
                    $from = date("Y-m-d", strtotime($filter['date_search_from']));
                    $to = date("Y-m-d", strtotime($filter['date_search_to']));
                    $custom_where1 = 'order.Reveived_date >= "' . $from . '" && order.Reveived_date <= "' . $to . '"';
                    $this->db->where($custom_where1);
                } else {
                    $from = strtotime($filter['date_search_from']);
                    $to = strtotime($filter['date_search_to']);
                    $custom_where1 = 'order.order_date >= "' . $from . '" && order.order_date <= "' . $to . '"';
                    $this->db->where($custom_where1);
                }
            }
            if (isset($filter['days']) && $filter['days'] != '' && $filter['days'] != 'Custom') {
                if (in_array('pending', $filter['status']) || in_array('declined', $filter['status'])) {
                    $from = strtotime(date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-" . $filter['days'] . " day")));
                    $to = strtotime(date("Y-m-d") . "+1 day");
                    $custom_where12 = '(order.order_date is null or (order.order_date >= "' . $from . '" and order.order_date <= "' . $to . '"))';
                    $this->db->where($custom_where12);
                    //$from = date("Y-m-d", date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-".$filter['days']." day" );
                    $from = date("Y-m-d", strtotime(date("Y-m-d") . "-" . $filter['days'] . ' day'));
                    $to = date("Y-m-d", strtotime(date("Y-m-d") . '+1 day'));
                    $custom_where1 = 'order.Reveived_date >= "' . $from . '" && order.Reveived_date <= "' . $to . '"';
                    $this->db->where($custom_where1);
                } else {
                    $from = strtotime(date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-" . $filter['days'] . " day")));
                    $to = strtotime(date("Y-m-d") . "+1 day");
                    $custom_where1 = 'order.order_date >= "' . $from . '" && order.order_date <= "' . $to . '"';
                    $this->db->where($custom_where1);
                }
            }

            //$custom_where2 = "(";
            //foreach($filter['status'] as $key => $piece) {
            //	$custom_where2 .= ' status = "'.$piece.'" OR';
            //}
            //$custom_where2 = rtrim($custom_where2, 'OR').')';
            //$this->db->where($custom_where2);
        }
        //if(count($filter['status']) == 1 && $filter['status'][0] == 'approved') {
        //	$predate = strtotime(date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) ));
        //	$curdate = date("Y-m-d");
        //	$this->db->where('order.order_date >',$predate);
        //}
        $this->db->order_by('order.id DESC');
        $this->db->join('contact', 'contact.id = order.contact_id', 'left');
        $this->db->join('contact shipping', 'shipping.id = order.contact_id', 'left');
        $this->db->join('order_transaction', 'order.id = order_transaction.order_id', 'left');
        //$this->db->join('order_status order_status', 'order_status.order_id = order.id', 'left');

        $this->db->group_by('order.id');
        $records = $this->selectRecords('order');
        //echo $this->db->last_query();exit;
        if ($records) {
            foreach ($records as $k => &$row) {
                $this->db->select('distributor');
                $where = array('order_id' => $row['order_id']);
                $row['products'] = $this->selectRecords('order_product', $where);
                $this->db->order_by('datetime DESC');
                $statusRec = $this->selectRecord('order_status', $where);
                if (in_array(strtolower($statusRec['status']), $filter['status']) || (!$statusRec['status'] && in_array('pending', $filter['status']))) {
                    $row['status'] = $statusRec['status'];
                } else {
                    unset($records[$k]);
                }
            }
        }
        return $records;
    }

    private function setOrderFilter($filter) {
        $where = array();
        if (is_array($filter)) {
            foreach ($filter as $key => $piece) {
                switch ($piece) {
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

    public function getUserEmails() {
        $query = $this->db->query("SELECT DISTINCT username as email FROM `user` WHERE admin!=1");
        return $query->result_array();
    }

    public function getContactTable() {
        $query = $this->db->query("SELECT email FROM `contact` group by email");
        return $query->result_array();
    }

    public function getNewsLetters() {
        $query = $this->db->query("SELECT emailaddress AS email FROM `newsletter` group by emailaddress");
        return $query->result_array();
    }

    public function update_feed_log($data) {
        $this->db->insert('google_feed_log', $data);
    }

    public function update_cycletrader_feeds_log($data) {
        $this->db->insert('cycle_feed_log', $data);
    }

    public function update_craglist_feeds_log($data) {
//        $this->db->insert('google_feed_log', $data);
    }

    public function get_feed_log() {
        $sql = "SELECT * FROM google_feed_log order by run_at desc limit 1";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results[0];
    }

    public function get_craglist_feed_log() {
        $sql = "SELECT * FROM google_feed_log order by run_at desc limit 1";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results[0];
    }

    public function get_cycletrader_feed_log() {
        $sql = "SELECT * FROM cycle_feed_log order by run_at desc limit 1";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        return $results[0];
    }

    public function setDistributorInventory($partvariation_id, $quantity, $cost)
    {
        $quantity = intVal($quantity);

        if ($quantity > 0) {
            // Insert it into partdealervariation
            $present_in_partdealervariation = false;

            $query = $this->db->query("Select count(*) as cnt from partdealervariation where partvariation_id = ?", array($partvariation_id));
            foreach ($query->result_array() as $row) {
                $present_in_partdealervariation = $row["cnt"] > 0;
            }

            if ($present_in_partdealervariation) {
                $this->db->query("Update partdealervariation set quantity_available = ?, cost = ? where partvariation_id = ?", array($quantity, $cost, $partvariation_id));
            } else {
                $distributorInventory = $this->selectRecord('partvariation', array("partvariation_id" => $partvariation_id));
                $data = $distributorInventory;
                $data['cost'] = $cost;
                $data['quantity_available'] = $quantity;
                unset($data['bulk_insert_round']);
                unset($data['ext_partvariation_id']);
                unset($data['protect']);
                unset($data['customerdistributor_id']);
                unset($data['from_lightspeed']);
                $this->db->insert('partdealervariation', $data);
            }

            $this->db->query("Update partvariation set protect = 1 where partvariation_id = ?", array($partvariation_id));
        } else {
            // OK, well, that means you have to release it...
            $this->db->query("Delete from partdealervariation where partvariation_id = ?", array($partvariation_id));
            $this->db->query("Update partvariation set protect = 0 where partvariation_id = ?", array($partvariation_id));
        }

    }

    public function updateDistributorInventory($arr) {
        $error = array();
        $scs = array();
        $lightspeed = array();
        $imprt = array();
        foreach ($arr as $k => $v) {
            //$where = array('partnumber_id' => $v['partnumber'], 'distributor_id' => $v['distributor_id']);
            $where = array('distributor_id' => $v['distributor_id']);
            $distributor = $this->selectRecord('distributor', $where);
            $where = array('part_number' => $v['partnumber'], 'distributor_id' => $v['distributor_id']);
            $distributorInventory = $this->selectRecord('partvariation', $where);
            $dealerInventory = $this->selectRecord('partdealervariation', $where);
            $imported = false;
            if (empty($distributorInventory)) {
                if (USE_PORTAL_WS) {
                    $name = $distributor["name"];
                    if (strtolower($name) == "o'neal") {
                        $name = "ONeal";
                    }
                    $output = file_get_contents("http://" . WS_HOST . "/migrateparts/index/" . STORE_NAME . "/" . urlencode($name) . "/" . urlencode($v["partnumber"]));
                } else {
                    exec(sprintf('/usr/bin/php /var/www/portal.powersporttechnologies.com/html/index.php "cron/migratePartByVendorPartNumberToStore/%s/%s/%s"', STORE_NAME, $distributor['name'], $v['partnumber']), $output);
                }

                if (empty($output)) {
                    $imprt[] = $v;
                    $imported = true;
                    $where = array('part_number' => $v['partnumber'], 'distributor_id' => $v['distributor_id']);
                    $distributorInventory = $this->selectRecord('partvariation', $where);
                    $scs[$v['partnumber']] = $v;
                }
            }

            // JLB 01-12-18
            // This is an emergency breaker. If this is coming from lightspeed, just stop.
            //// It would be coming from Lightspeed if there was existing dealer inventory...
            $partvariation_id = 0;
            if (!empty($distributorInventory) && array_key_exists("partvariation_id", $distributorInventory) && $distributorInventory["partvariation_id"] > 0) {
                $partvariation_id = $distributorInventory["partvariation_id"];
            } elseif (!empty($dealerInventory) && array_key_exists("partvariation_id", $dealerInventory) && $dealerInventory["partvariation_id"] > 0) {
                $partvariation_id = $dealerInventory["partvariation_id"];
            }

            if ($partvariation_id > 0 ) {
                // Is this one in lightspeed?
                $query = $this->db->query("Select count(*) as cnt from lightspeedpart where partvariation_id = ?", array($partvariation_id));
                $cnt = $query->result_array();
                $cnt = $cnt[0]["cnt"];

                if ($cnt > 0) {
                    $lightspeed[$v['partnumber']] = $v['partnumber'];
                    continue; // this should go to the next part, I hope.
                }
            }

            if (empty($dealerInventory) && !empty($distributorInventory)) {
                $data = $distributorInventory;
                $data['cost'] = $v['cost'];
                $data['quantity_available'] = $v['quantity'];
                unset($data['bulk_insert_round']);
                unset($data['ext_partvariation_id']);
                unset($data['protect']);
                unset($data['customerdistributor_id']);
                unset($data['from_lightspeed']);
                $this->db->insert('partdealervariation', $data);

                $dt = array('protect' => 1);
                $cwhere = array('partvariation_id' => $data['partvariation_id']);
                $this->updateRecord('partvariation', $dt, $cwhere, FALSE);

                $where = array('part_number' => $v['partnumber'], 'distributor_id' => $v['distributor_id']);
                $dealerInventory = $this->selectRecord('partdealervariation', $where);

                $scs[$v['partnumber']] = $v;
            } else if (!empty($dealerInventory)) {
                $data = array('quantity_available' => $dealerInventory["quantity_available"] + $v['quantity']);
                if ($v['cost'] > 0) {
                    // JLB 07-15-17
                    // Brandt told me that Pardy should not have done this, that the price should never be assigned to the cost, and that this is crazy.
//                    $data['price'] = $v['cost'];
                    $data['cost'] = $v['cost'];
                }
                $success = $this->updateRecord('partdealervariation', $data, $where, FALSE);
                $dt = array('protect' => 1);
                $cwhere = array('partvariation_id' => $data['partvariation_id']);
                $this->updateRecord('partvariation', $dt, $cwhere, FALSE);
                $scs[$v['partnumber']] = $v;
            }

            $where = array('partpartnumber.partnumber_id' => $dealerInventory['partnumber_id'], 'partnumber.price > ' => 0);
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id ');
            $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
            $partnumbers = $this->selectRecord('partnumber', $where);

            // $this->db->select('MIN(category.mark_up) as markup');
            // $where = array('partcategory.part_id' => $partnumbers['part_id'], 'category.mark_up > ' => 0);
            // $this->db->join('partcategory', 'partcategory.category_id = category.category_id');
            // $categories = $this->selectRecord('category', $where);
            $category = $this->getSecondBreadCrumb($partnumbers['part_id']);
            $category_markup = array();
            foreach ($category as $cat) {
                $category_markup[] = $cat['id'];
            }

            $this->db->select('MIN(category.mark_up) as markup');
            $where = array('category.mark_up > ' => 0);
            $this->db->where_in('category_id', $category_markup);
            //$this->db->join('partcategory', 'partcategory.category_id = category.category_id');
            $categories = $this->selectRecord('category', $where);

            $this->db->select('MIN(brand.mark_up) as markup, 
											  MAX(brand.exclude_market_place) as exclude_market_place, 
											  MAX(brand.closeout_market_place) as closeout_market_place');
            $where = array('partbrand.part_id' => $partnumbers['part_id']);
            $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
            $brand_markup = $this->selectRecord('brand', $where);

            $this->db->select('MIN(brand.map_percent) as map_percent, ');
            $where = array('partbrand.part_id' => $partnumbers['part_id'], 'brand.map_percent IS NOT NULL ' => NULL);
            $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
            $brand_map_percent = $this->selectRecord('brand', $where);

            $where = array('partpartnumber.part_id' => $partnumbers['part_id'], 'partnumber.price > ' => 0);
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id ');
            $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
            $partnumbers = $this->selectRecords('partnumber', $where);
            $categoryMarkUp = is_numeric(@$categories['markup']) ? $categories['markup'] : 0;
            $brandMarkUp = is_numeric(@$brand_markup['markup']) ? $brand_markup['markup'] : 0;
            $brandMAPPercent = (array_key_exists("map_percent", $brand_map_percent) && !is_null($brand_map_percent["map_percent"]) && is_numeric(@$brand_map_percent['map_percent'])) ? $brand_map_percent['map_percent'] : NULL;

            if ($partnumbers) {
                foreach ($partnumbers as $rec) {
                    $finalMarkUp = 0;
                    $productMarkUp = $rec['markup'];

                    if ($this->lightspeed_m->partNumberIsLightspeed($rec['partnumber_id'])) {
                        $finalSalesPrice = $this->lightspeed_m->lightspeedPrice($rec['partnumber_id']);
                    } elseif ($productMarkUp > 0) { // Product Markup Trumps everything
                        $finalSalesPrice = ($rec['cost'] * $productMarkUp / 100) + $rec['cost'];
                        //echo 'product Markup : '.$productMarkUp.' : '.$finalSalesPrice.'<br>';
                    } else {
                        // Calculate category and Brand Percent Mark up

                        if ($brandMarkUp > 0) {
                            $finalMarkUp = $brandMarkUp;
                        } else if ($categoryMarkUp > 0) {
                            $finalMarkUp = $categoryMarkUp;
                            if (($brandMarkUp > 0) && ($brandMarkUp < $finalMarkUp))
                                $finalMarkUp = $brandMarkUp;
                        }
                        // if ($categoryMarkUp > 0) {
                        // $finalMarkUp = $categoryMarkUp;
                        // if (($brandMarkUp > 0) && ($brandMarkUp < $finalMarkUp))
                        // $finalMarkUp = $brandMarkUp;
                        // }
                        // elseif ($brandMarkUp > 0)
                        // $finalMarkUp = $brandMarkUp;
                        // Get Final Sales Price for Calculating vs MAP Pricing

                        if ($finalMarkUp > 0)
                            $finalSalesPrice = ($rec['cost'] * $finalMarkUp / 100) + $rec['cost'];

                        //echo 'category/brand Markup : '.$finalMarkUp.' : '.$finalSalesPrice.'<br>';
                        // Calculate MAP Pricing
                        if ((!is_null($brandMAPPercent)) && (isset($finalSalesPrice)) && ($rec['stock_code'] != 'Closeout')) {
                            $mapPrice = (((100 - $brandMAPPercent) / 100) * $rec['price']);
                            if ($mapPrice > $finalSalesPrice) {
                                $finalSalesPrice = $mapPrice;
                            }
                            //echo 'final saleprice : '.$mapPrice.' : '.$finalSalesPrice.'<br>';
                        }
                    }
                    if (!isset($finalSalesPrice)) {
                        $finalSalesPrice = $rec['price'];
                        //echo 'final price : '.$finalSalesPrice.'<br>';
                    }

                    $data = array('dealer_sale' => $finalSalesPrice);
                    $where = array('partnumber_id' => $rec['partnumber_id']);
                    $this->updateRecord('partnumber', $data, $where, FALSE);
                }
            }
        }

        foreach ($arr as $k => $v) {
            if (empty($scs[$v['partnumber']]) && !array_key_exists($v["partnumber"], $lightspeed)) {
                $error[$v['partnumber']] = $v;
            }
        }

        // foreach( $imprt as $key => $val ) {
        // $where = array('part_number' => $val['partnumber'], 'distributor_id' => $val['distributor_id']);
        // $dealerInventory = $this->selectRecord('partdealervariation', $where);
        // $where = array('partnumber.partnumber_id' => $dealerInventory['partnumber_id']);
        // $partnumbers = $this->selectRecord('partnumber', $where);
        // $where = array('partnumber.partnumber_id' => $dealerInventory['partnumber_id']);
        // $data = array('sale' => $partnumbers['dealer_sale']);
        // $this->updateRecord('partnumber', $data, $where, FALSE);
        // }
//exit;
        $msg = array('success' => $scs, 'error' => $error, "lightspeed" => $lightspeed);
        return $msg;
    }

    public function deleteSizeChart($id) {
        $where = array('id' => $id);
        $this->deleteRecord('brand_sizechart', $where);
    }

    public function getAllCustomers($filter = null, $limit = false, $offset = 0) {
        if ($filter['sort'] == 'orders') {
            $this->db->select('contact.first_name AS first_name, ' .
                    'contact.last_name AS last_name, ' .
                    'contact.street_address AS street_address, ' .
                    'contact.address_2 AS address_2, ' .
                    'contact.city AS city, ' .
                    'contact.state AS state, ' .
                    'contact.zip AS zip, ' .
                    'contact.country AS country, ' .
                    'contact.email AS email, ' .
                    'contact.phone AS phone, ' .
                    'contact.company AS company, user.username as uemail, user.id, user.last_login, user.admin, user.status,' .
                    "(select count(distinct `order_status`.`order_id`) from `order_status` inner join `order` on `order`.`id` = `order_status`.`order_id` where `order`.`user_id` = `user`.`id`) as orders, (select c.first_name from contact as c inner join user as u on u.billing_id = c.id where u.id=user.created_by) as employee"
            );
            //select count(distinct order_status.order_id), order_status.status from `order_status` inner join `order` on order.id = order_status.order_id where order.user_id = 8;
            $this->db->join('contact', 'contact.id = user.billing_id');
            //$this->db->join('order', 'order.contact_id = user.billing_id', 'left');
            // if( strtolower($filter['sorter']) == 'desc' ) {
            // $this->db->join('order_status', 'order_status.order_id = order.id', 'left');
            // $sts = array('declined', 'batch order', 'processing', 'back order', 'partially shipped', 'will_call', 'shipped/complete', 'returned', 'refunded', 'approved');
            // $this->db->where_in('order_status.status', $sts);
            // //$this->db->where('order.order_date is not null');
            // }

            if (isset($filter['search']) && $filter['search'] != '') {
                $this->db->where('( concat(contact.first_name, " ", contact.last_name) like "%' . $filter['search'] . '%"', null);
                $this->db->or_where('email like "%' . $filter['search'] . '%"');
                $this->db->or_where('phone like "%' . $filter['search'] . '%" )');
            }

            if ($filter['custom'] == 'own') {
                $this->db->where('user.created_by', $_SESSION['userRecord']['id']);
            } else if ($filter['custom'] == 'web') {
                $this->db->where('user.created_by IS NULL');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            if ($filter['sort'] == 'orders') {
                $this->db->order_by('orders ' . $filter['sorter']);
            }

            $this->db->group_by('user.id');
            $records = $this->selectRecords('user', $where);

            foreach ($records as &$record) {
                $dt = date('Y-m-d H:i:s');
                $this->db->where('user_id', $record['id']);
                $this->db->where('is_completed', '0');
                $this->db->where('start_datetime <=', $dt);
                //$this->db->where('end_datetime >=', $dt);
                $reminders = $this->selectRecord('user_reminder');

                $record['reminder'] = $reminders;
            }

            //echo $this->db->last_query();exit;
            // $this->db->select('count(order.id) AS orders, user.billing_id');
            // $this->db->join('user', 'user.billing_id = order.contact_id');
            // $this->db->join('order_status', 'order_status.order_id = order.id');
            // $sts = array('declined', 'batch order', 'processing', 'back order', 'partially shipped', 'will_call', 'shipped/complete', 'returned', 'refunded');
            // $this->db->where_in('order_status.status', $sts);
            // if( $limit > 0 ) {
            // $this->db->limit($limit, $offset);
            // }
            // $this->db->group_by('user.billing_id');
            // $this->db->order_by('orders '.$filter['sorter']);
            // $ordr = $this->selectRecords('order');
            // $records = array();
            // foreach( $ordr as $k => $v ) {
            // $this->db->select('contact.first_name AS first_name, '.
            // 'contact.last_name AS last_name, '.
            // 'contact.street_address AS street_address, '.
            // 'contact.address_2 AS address_2, '.
            // 'contact.city AS city, '.
            // 'contact.state AS state, '.
            // 'contact.zip AS zip, '.
            // 'contact.country AS country, '.
            // 'contact.email AS email, '.
            // 'contact.phone AS phone, '.
            // 'contact.company AS company, user.username as uemail, user.id, user.last_login, user.admin, user.status'
            // );
            // $this->db->join('contact', 'contact.id = user.billing_id');
            // if( isset($filter['search']) && $filter['search'] != '' ) {
            // $this->db->where('( concat(contact.first_name, " ", contact.last_name) like "%'.$filter['search'].'%"',null);
            // $this->db->or_where('email like "%'.$filter['search'].'%"');
            // $this->db->or_where('phone like "%'.$filter['search'].'%" )');
            // }
            // if( isset($filter['user_type']) && $filter['user_type'] != '' ) {
            // $this->db->where('user.user_type = "'.$filter['user_type'].'"');
            // }
            // if( $filter['sort'] == 'first_name' ) {
            // $this->db->order_by('contact.first_name ASC');
            // }
            // $this->db->where("user.id = '".$v['billing_id']."'",null);
            // $record = $this->selectRecord('user', $where);
            // $record['orders'] = $v['orders'];
            // $records[] = $record;
            // }
        } else if ($filter['sort'] == 'reminders') {
            $dt = date('Y-m-d H:i:s');
            $this->db->select('contact.first_name AS first_name, ' .
                    'contact.last_name AS last_name, ' .
                    'contact.street_address AS street_address, ' .
                    'contact.address_2 AS address_2, ' .
                    'contact.city AS city, ' .
                    'contact.state AS state, ' .
                    'contact.zip AS zip, ' .
                    'contact.country AS country, ' .
                    'contact.email AS email, ' .
                    'contact.phone AS phone, ' .
                    'contact.company AS company, user.username as uemail, user.id, user.last_login, user.admin, user.status, ' .
                    '(select c.first_name from contact as c inner join user as u on u.billing_id = c.id where u.id=user.created_by) as employee,' .
                    "(select 10 from user_reminder as ur inner join user as us on us.id = ur.user_id where ur.start_datetime <= '" . $dt . "' and ur.user_id=user.id and ur.is_completed = '0' limit 1) as strt_tm"
            );
            $this->db->join('contact', 'contact.id = user.billing_id');

            if (isset($filter['search']) && $filter['search'] != '') {
                $this->db->where('( concat(contact.first_name, " ", contact.last_name) like "%' . $filter['search'] . '%"', null);
                $this->db->or_where('email like "%' . $filter['search'] . '%"');
                $this->db->or_where('phone like "%' . $filter['search'] . '%" )');
            }

            if (isset($filter['user_type']) && $filter['user_type'] != '') {
                $this->db->where('user.user_type = "' . $filter['user_type'] . '"');
            }

            if ($filter['custom'] == 'own') {
                $this->db->where('user.created_by', $_SESSION['userRecord']['id']);
            } else if ($filter['custom'] == 'web') {
                $this->db->where('user.created_by IS NULL');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            $this->db->order_by("strt_tm DESC");

            $this->db->group_by('user.id');
            $records = $this->selectRecords('user', $where);

            foreach ($records as &$record) {
                $this->db->select('order.id AS order_id, order_status.status ');
                $this->db->join('user', 'user.id = order.user_id');
                $this->db->join('order_status', 'order_status.order_id = order.id');
                $sts = array('declined', 'batch order', 'processing', 'back order', 'partially shipped', 'will_call', 'shipped/complete', 'returned', 'refunded', 'approved');
                $this->db->where('order.user_id', $record['id']);
                $this->db->where_in('order_status.status', $sts);
                $this->db->order_by('order_status.datetime DESC');
                //$this->db->group_by('order.id');
                $ordr = $this->selectRecords('order');
                $odr = array();
                foreach ($ordr as $order) {
                    $where = array('order_id' => $order['order_id']);
                    $this->db->order_by('datetime DESC');
                    $statusRec = $this->selectRecord('order_status', $where);
                    $rcrd = $order;
                    $rcrd['status'] = $statusRec['status'];
                    if (empty($odr[$order['order_id']])) {
                        $odr[$order['order_id']] = $rcrd;
                    }
                }

                $this->db->where('user_id', $record['id']);
                $this->db->where('is_completed', '0');
                $this->db->where('start_datetime <=', $dt);
                //$this->db->where('end_datetime >=', $dt);
                $reminders = $this->selectRecord('user_reminder');

                $record['reminder'] = $reminders;
                $record['orders'] = count($odr);
            }
        } else {
            $this->db->select('contact.first_name AS first_name, ' .
                    'contact.last_name AS last_name, ' .
                    'contact.street_address AS street_address, ' .
                    'contact.address_2 AS address_2, ' .
                    'contact.city AS city, ' .
                    'contact.state AS state, ' .
                    'contact.zip AS zip, ' .
                    'contact.country AS country, ' .
                    'contact.email AS email, ' .
                    'contact.phone AS phone, ' .
                    'contact.company AS company, user.username as uemail, user.id, user.last_login, user.admin, user.status, ' .
                    '(select c.first_name from contact as c inner join user as u on u.billing_id = c.id where u.id=user.created_by) as employee'
            );
            $this->db->join('contact', 'contact.id = user.billing_id');

            if (isset($filter['search']) && $filter['search'] != '') {
                $this->db->where('( concat(contact.first_name, " ", contact.last_name) like "%' . $filter['search'] . '%"', null);
                $this->db->or_where('email like "%' . $filter['search'] . '%"');
                $this->db->or_where('phone like "%' . $filter['search'] . '%" )');
            }

            if (isset($filter['user_type']) && $filter['user_type'] != '') {
                $this->db->where('user.user_type = "' . $filter['user_type'] . '"');
            }

            if ($filter['custom'] == 'own') {
                $this->db->where('user.created_by', $_SESSION['userRecord']['id']);
            } else if ($filter['custom'] == 'web') {
                $this->db->where('user.created_by IS NULL');
            }

            if ($limit > 0) {
                $this->db->limit($limit, $offset);
            }

            if ($filter['sort'] == 'first_name') {
                $this->db->order_by('contact.first_name ' . $filter['sorter']);
            }

            $this->db->group_by('user.id');
            $records = $this->selectRecords('user', $where);

            foreach ($records as &$record) {
                $this->db->select('order.id AS order_id, order_status.status ');
                $this->db->join('user', 'user.id = order.user_id');
                $this->db->join('order_status', 'order_status.order_id = order.id');
                $sts = array('declined', 'batch order', 'processing', 'back order', 'partially shipped', 'will_call', 'shipped/complete', 'returned', 'refunded', 'approved');
                $this->db->where('order.user_id', $record['id']);
                $this->db->where_in('order_status.status', $sts);
                $this->db->order_by('order_status.datetime DESC');
                //$this->db->group_by('order.id');
                $ordr = $this->selectRecords('order');
                $odr = array();
                foreach ($ordr as $order) {
                    $where = array('order_id' => $order['order_id']);
                    $this->db->order_by('datetime DESC');
                    $statusRec = $this->selectRecord('order_status', $where);
                    $rcrd = $order;
                    $rcrd['status'] = $statusRec['status'];
                    if (empty($odr[$order['order_id']])) {
                        $odr[$order['order_id']] = $rcrd;
                    }
                }

                $dt = date('Y-m-d H:i:s');
                $this->db->where('user_id', $record['id']);
                $this->db->where('is_completed', '0');
                $this->db->where('start_datetime <=', $dt);
                //$this->db->where('end_datetime >=', $dt);
                $reminders = $this->selectRecord('user_reminder');

                $record['reminder'] = $reminders;
                $record['orders'] = count($odr);
            }
        }

        // echo '<pre>';
        // print_r($records);
        // echo '</pre>';
        return $records;
    }

    public function getAllCustomersCount($filter) {
        $this->db->select('count(user.id) as cnt');
        $this->db->join('contact', 'contact.id = user.billing_id');

        if (isset($filter['search']) && $filter['search'] != '') {
            $this->db->where('concat(contact.first_name, " ", contact.last_name) like "%' . $filter['search'] . '%"', null);
            $this->db->or_where('email like "%' . $filter['search'] . '%"');
            $this->db->or_where('phone like "%' . $filter['search'] . '%"');
        }

        if (isset($filter['user_type']) && $filter['user_type'] != '') {
            $this->db->where('user.user_type = "' . $filter['user_type'] . '"');
        }

        $record = $this->selectRecord('user', $where);
        return $record['cnt'];
    }

    public function getCustomerDetail($user_id = null, $emp = false) {
        if ($user_id == null) {
            return array();
        }

        $this->db->select('contact.first_name AS first_name, ' .
                'contact.last_name AS last_name, ' .
                'contact.street_address AS street_address, ' .
                'contact.address_2 AS address_2, ' .
                'contact.city AS city, ' .
                'contact.state AS state, ' .
                'contact.zip AS zip, ' .
                'contact.country AS country, ' .
                'contact.email AS email, ' .
                'contact.phone AS phone, ' .
                'contact.company AS company, user.id, user.status, user.admin, user.cc_permission, user.username, user.notes, user.user_type'
        );
        //$where = array("user.first_name != ''" => null);
        $this->db->join('contact', 'contact.id = user.billing_id');

        if ($user_id != '') {
            $this->db->where('user.id = "' . $user_id . '"', null);
        }

        if (isset($filter['user_type']) && $filter['user_type'] != '') {
            $this->db->where('user.user_type = "' . $filter['user_type'] . '"');
        }

        $this->db->order_by('contact.first_name ASC');
        $record = $this->selectRecord('user', $where);

        if ($emp) {
            $this->db->where('user_id', $user_id);
            $permissions = $this->selectRecords('userpermissions');
            $userPerm = array();
            foreach ($permissions as $permission) {
                $userPerm[$permission['id']] = $permission['permission'];
            }
            $record['permissions'] = $userPerm;
        } else {
            //Get user orders
            $this->db->select('order.id AS order_id, ' .
                    'order.user_id AS user_id, ' .
                    'order.contact_id AS contact_id, ' .
                    'order.shipping_id AS shipping_id, ' .
                    'order.sales_price AS sales_price, ' .
                    'order.shipping AS shipping, ' .
                    'order.weight AS weight, ' .
                    'order.tax AS tax, ' .
                    'order.Reveived_date AS processed_date, ' .
                    'order.will_call AS will_call, ' .
                    'order.process_date AS process_date, ' .
                    'order.batch_number AS batch_number, ' .
                    'order.special_instr AS special_instr, ' .
                    'order.Reveived_date AS Reveived_date,' .
                    'order.order_date AS order_date, order_status.status ');
            $this->db->join('user', 'user.id = order.user_id');
            $this->db->join('order_status', 'order_status.order_id = order.id');
            $sts = array('declined', 'batch order', 'processing', 'back order', 'partially shipped', 'will_call', 'shipped/complete', 'returned', 'refunded', 'approved');
            $this->db->where('order.user_id', $record['id']);
            $this->db->where_in('order_status.status', $sts);
            $this->db->order_by('order_status.datetime DESC');
            //$this->db->group_by('order.id');
            $ordr = $this->selectRecords('order');
            $odr = array();
            foreach ($ordr as $order) {
                $where = array('order_id' => $order['order_id']);
                $this->db->order_by('datetime DESC');
                $statusRec = $this->selectRecord('order_status', $where);
                $rcrd = $order;
                $rcrd['status'] = $statusRec['status'];
                if (empty($odr[$order['order_id']])) {
                    $odr[$order['order_id']] = $rcrd;
                }
            }

            $record['orders'] = $odr;
        }
        return $record;
    }

    public function getUserBillingId($user_id) {
        $this->db->select('billing_id');
        $this->db->where('id = "' . $user_id . '"');
        $record = $this->selectRecord('user');
        return $record['billing_id'];
    }

    public function updateCustomerInfo($data) {
        $where = array('id' => $data['id']);
        unset($data['id']);
        $this->updateRecord('contact', $data, $where, FALSE);
    }

    public function createNewEmployee($data) {
        $this->load->library('encrypt');
        $contactData = array(
            'email' => @$data['email'],
            'first_name' => @$data['first_name'],
            'last_name' => @$data['last_name'],
            'street_address' => @$data['street_address'],
            'address_2' => @$data['address_2'],
            'city' => @$data['city'],
            'state' => @$data['state'],
            'zip' => @$data['zip'],
            'country' => @$data['country'],
            'phone' => @$data['phone'],
        );
        $billingId = $this->createRecord('contact', $contactData, FALSE);
        $userData = array(
            'username' => $data['username'],
            'password' => $this->encrypt->encode($data['password']),
            'lost_password_email' => $data['email'],
            'billing_id' => @$billingId,
            'user_type' => 'employee',
            'cc_permission' => $data['cc_permission'] == 1 ? 1 : 0,
            'admin' => $data['admin'] == 1 ? 1 : 0,
            'status' => $data['status'] == 1 ? 1 : 0,
        );
        $userId = $this->createRecord('user', $userData, FALSE);
        $permissions = $this->db->query("delete from userpermissions where user_id = '" . $userId . "'");

        if ($data['prmsion'] != '') {
            $data['permission'][$data['prmsion']] = $data['prmsion'];
        }
        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';exit;
        foreach ($data['permission'] as $val) {
            $dt = array('user_id' => $userId, 'permission' => $val);
            $this->createRecord('userpermissions', $dt, FALSE);
        }
        return $userId;
    }

    public function updateEmployeeInfo($data) {
        $userData = array();
        if (@$data['password']) {
            $this->load->library('encrypt');
            $userData['password'] = $this->encrypt->encode($data['password']);
        }
        $userData['cc_permission'] = $data['cc_permission'] == 1 ? 1 : 0;
        $userData['admin'] = $data['admin'] == 1 ? 1 : 0;
        $userData['status'] = $data['status'] == 1 ? 1 : 0;
        if (array_key_exists("username", $data)) {
            $userData["username"] = $data["username"];
        }
        if (array_key_exists("email", $data)) {
            $userData["lost_password_email"] = $data["email"];
        }
        $where = array('id' => $data['id']);
        $this->updateRecord('user', $userData, $where, FALSE);

        $permissions = $this->db->query("delete from userpermissions where user_id = '" . $data['id'] . "'");

        if ($data['prmsion'] != '') {
            $data['permission'][$data['prmsion']] = $data['prmsion'];
        }
        foreach ($data['permission'] as $val) {
            $dt = array('user_id' => $data['id'], 'permission' => $val);
            $this->createRecord('userpermissions', $dt, FALSE);
        }

        $where = array('id' => $data['billing_id']);
        unset($data['id']);
        unset($data['billing_id']);
        $return = $this->updateRecord('contact', $data, $where, FALSE);
        return $return;
    }

    public function createNewCustomer($data) {
        $this->load->library('encrypt');
        $contactData = array(
            'email' => @$data['email'],
            'first_name' => @$data['first_name'],
            'last_name' => @$data['last_name'],
            'street_address' => @$data['street_address'],
            'address_2' => @$data['address_2'],
            'city' => @$data['city'],
            'state' => @$data['state'],
            'zip' => @$data['zip'],
            'country' => @$data['country'],
            'phone' => @$data['phone'],
        );
        $billingId = $this->createRecord('contact', $contactData, FALSE);
        $userData = array(
            'username' => $data['email'],
            'password' => $this->encrypt->encode($data['password']),
            'lost_password_email' => $data['email'],
            'billing_id' => @$billingId,
            'user_type' => 'normal',
            'admin' => 0,
            'created_by' => $data['created_by']
        );
        $userId = $this->createRecord('user', $userData, FALSE);
        return $userId;
    }

    public function updateContact($post, $type = 'billing', $userId = NULL, $notes) {
        $contactId = FALSE;
        $typeId = 'billing_id';
        if ($userId) { // Updating User Record
            $where = array('id' => $userId);
            $userRec = $this->selectRecord('user', $where);
            if (empty($post['email']))
                $post['email'] = $userRec['lost_password_email'];

            $cwhere = array('id' => $userRec['billing_id']);
            $contactId = $this->updateRecord('contact', $post, $cwhere, FALSE);
            $post = array('notes' => $notes);
            $this->updateRecord('user', $post, $where, FALSE);
        }
        else // Create Contact Record not associated with User Record
            $contactId = $this->createRecord('contact', $post, FALSE);

        return $contactId;
    }

    public function deleteEmployee($user_id = null) {
        $where = array('id' => $user_id);
        $this->deleteRecord('user', $where);
    }

    public function getParentCategores($childid) {
        $where = array('category_id' => $childid);
        $cat = $this->selectRecord('category', $where);
        $parentCats = explode(' > ', $cat['long_name']);
        $returnArr = array();
        foreach ($parentCats as $key => $parent) {
            // Set Up Data
            $i = 0;
            if ($parent == $cat['name']) {
                $returnArr[$cat['category_id']] = $cat['name'];
                $id = $cat['category_id'];
            } else {
                $where = array('name' => $parent);
                if ((@$key - 1) >= 0) {
                    $where['parent_category_id'] = $id;
                }
                //print_r($where);
                $this->db->select('category_id, name, parent_category_id, long_name');
                $category = $this->selectRecord('category', $where);
                $returnArr[$category['category_id']] = $category['name'];
                $id = $category['category_id'];
            }
        }
        return $returnArr;
    }

    public function tag_creating($url) {
        $url = str_replace(array(' - ', ' '), '-', $url);
        $url = preg_replace('~[^\\pL0-9_-]+~u', '', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_-]+~', '', $url);
        return $url;
    }

    function getSecondBreadCrumb($partId) {

        $this->db->select("*");
        $this->db->from("partcategory");
        $this->db->where("part_id", $partId);
        //$this->db->limit(1);
        $this->db->order_by('category_id', 'DESC');
        $get = $this->db->get();


        if ($get->num_rows() > 0) {

            /*
              Streetbike	- 20409	- Priority 1
              Dirtbike		- 20416	- Priority 2,
              Atv			- 20419	- Priority 3,
              Utv			- TOP_LEVEL_CAT_UTV_PARTS	- Priority 4
             */
            $getPriorityCategories = array();

            $caTids = array();
            $cats = array();
            foreach ($get->result() as $row) {
                $cats[] = $this->getParentCategores($row->category_id);
            }


            $getPriorityCategories = array();
            $thePriorities = array(TOP_LEVEL_CAT_STREET_BIKES, TOP_LEVEL_CAT_DIRT_BIKES, TOP_LEVEL_CAT_ATV_PARTS, TOP_LEVEL_CAT_UTV_PARTS);

            for ($i = 0; $i <= 3; $i++) {

                foreach ($cats as $c) {

                    if (is_array($c)) {

                        foreach ($c as $Ckey => $cData) {
                            if ($Ckey == $thePriorities[$i]) {

                                $getPriorityCategories = array("data" => $c, "size" => count($c));
                                $i = 4;
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        $breadCrumb = array();
        $tot = 1;

        if (!empty($getPriorityCategories)) {

            $returnURL = '';
            $counter = 0;
            foreach ($getPriorityCategories['data'] as $key => $cat) {

                $breadCrumb[$counter]['id'] = $key;
                $breadCrumb[$counter]['name'] = $cat;

                if ($key == TOP_LEVEL_CAT_STREET_BIKES) {
                    $breadCrumb[$counter]['link'] = "streetbikeparts";
                    $returnURL .= $this->tag_creating($cat) . '_';
                } else if ($key == TOP_LEVEL_CAT_DIRT_BIKES) {
                    $breadCrumb[$counter]['link'] = "dirtbikeparts";
                    $returnURL .= $this->tag_creating($cat) . '_';
                } else if ($key == TOP_LEVEL_CAT_ATV_PARTS) {
                    $breadCrumb[$counter]['link'] = "atvparts";
                    $returnURL .= $this->tag_creating($cat) . '_';
                } else if ($key == TOP_LEVEL_CAT_UTV_PARTS) {
                    $breadCrumb[$counter]['link'] = "utvparts";
                    $returnURL .= $this->tag_creating($cat) . '_';
                } else {
                    $breadCrumb[$counter]['link'] = "shopping/productlist/" . $returnURL . $this->tag_creating($cat) . '_';
                    $returnURL .= $this->tag_creating($cat) . '_';
                }

                $counter++;
            }

            if (!empty($breadCrumb)) {
                foreach ($breadCrumb as $p_key => $p_row) {
                    if (isset($p_row['link'])) {
                        $breadCrumb[$p_key]['link'] = substr($p_row['link'], 0, -1);
                    }
                }
            }

            $tot = count($breadCrumb);
        } else {

            $topCategories[0]['id'] = TOP_LEVEL_CAT_STREET_BIKES;
            $topCategories[0]['link'] = "streetbikeparts";
            $topCategories[0]['name'] = "STREET BIKE PARTS";
            $topCategories[1]['id'] = TOP_LEVEL_CAT_DIRT_BIKES;
            $topCategories[1]['link'] = "dirtbikeparts";
            $topCategories[1]['name'] = "DIRT BIKE PARTS";
            $topCategories[2]['id'] = TOP_LEVEL_CAT_ATV_PARTS;
            $topCategories[2]['link'] = "atvparts";
            $topCategories[2]['name'] = "ATV PARTS";
            $topCategories[3]['id'] = TOP_LEVEL_CAT_UTV_PARTS;
            $topCategories[3]['link'] = "utvparts";
            $topCategories[3]['name'] = "UTV PARTS";
            $keyToUse = array_rand($topCategories);

            $breadCrumb[0]['id'] = $topCategories[$keyToUse]['id'];
            $breadCrumb[0]['name'] = $topCategories[$keyToUse]['name'];
            $breadCrumb[0]['link'] = $topCategories[$keyToUse]['link'];
        }

        /*
          UN-COMMENT THIS SCRIPT, IF YOU WANT TO DISPLAY PRODUCT/PART/ITEM IN THE BREAD CRUMB AT THE END
          $part_info = $this->db->query("SELECT part_id, name FROM part WHERE part_id=$partId");
          $part_info = $part_info->row();
          $breadCrumb[$tot]['id'] = $part_info->part_id;
          $breadCrumb[$tot]['name'] = $part_info->name;
          $breadCrumb[$tot]['link'] = "shopping/item/$part_info->part_id/".$this->tag_creating($part_info->name);
         */

        return $breadCrumb;
    }

    public function saveCustomerReminder($data) {
        $success = '';
        if ($data['id'] == '') {
            $success = $this->createRecord('user_reminder', $data, FALSE);
        } else {
            $where = array('id' => $data['id']);
            unset($data['id']);
            $success = $this->updateRecord('user_reminder', $data, $where, FALSE);
        }
        return $success;
    }

    public function getMonthReminders($month, $year, $user_id) {
        $this->db->where('YEAR(start_datetime)', $year);
        $this->db->where('MONTH(start_datetime)', $month);
        $this->db->where('user_id', $user_id);
        $records = $this->selectRecords('user_reminder');
        $reminders = array();
        foreach ($records as $record) {
            $dt = date('Y-m-d', strtotime($record['start_datetime']));
            $reminders[$dt][] = $record;
        }
        return $reminders;
    }

    public function getReminder($id) {
        $this->db->where('id', $id);
        $record = $this->selectRecord('user_reminder');
        return $record;
    }

    public function deleteCustomerEvent($id) {
        $where = array('id' => $id);
        $this->deleteRecord('user_reminder', $where);
    }

    public function getReminderRecurrences($month, $year, $user_id) {
        $this->db->where('user_id', $user_id);
        //$this->db->where('is_completed', '0');
        $reminders = $this->selectRecords('user_reminder');
        $events = array();
        foreach ($reminders as $reminder) {
            //$events[date('d-m-Y', strtotime($reminder['start_datetime']))][] = json_decode($reminder['data']);
            $events[date('Y-m-d', strtotime($reminder['start_datetime']))][] = $reminder;
        }
        return $events;
    }

    public function completeEvent($id) {
        $data = array('is_completed' => '1');
        $where = array('id' => $id);
        $this->updateRecord('user_reminder', $data, $where, FALSE);
    }

    //completeRecurEvent
    public function completeRecurEvent($id, $rmvd) {
        // $data = array('is_completed' => '1');
        // $where = array('id'=>$id);
        // $this->updateRecord('user_reminder', $data, $where, FALSE);
        $data = array('is_completed' => '1');
        $where = array('parent' => $id);
        $this->db->where('id > ', $rmvd);
        $this->deleteRecord('user_reminder', $where);
    }

    public function insertEventRecurrence($arr, $parent) {
        $where = array('parent' => $parent);
        $this->deleteRecord('user_reminder', $where);
        $this->db->insert_batch('user_reminder', $arr);
    }

    public function updateMotorcycle($id, $post) {

        if ($post['craigslist_feed_status']) {
            $post['craigslist_feed_status'] == TRUE;
        } else {
            $post['craigslist_feed_status'] == FALSE;
        }
        if ($post['cycletrader_feed_status']) {
            $post['cycletrader_feed_status'] == TRUE;
        } else {
            $post['cycletrader_feed_status'] == FALSE;
        }

        $cwhere = array('name' => $post['category']);
        $category = $this->selectRecord('motorcycle_category', $cwhere);

        if (empty($category)) {
            $cdata = array('name' => $post['category']);
            $this->db->insert('motorcycle_category', $cdata);
            $cwhere = array('name' => $post['category']);
            $category = $this->selectRecord('motorcycle_category', $cwhere);
        }

        $featured = ($post['featured'] == 1) ? 1 : 0;
        $post['category'] = $category['id'];
        $post['featured'] = $featured;
        $status = ($post['status'] == 1) ? 1 : 0;
        $post['status'] = $status;
        $data = array('total_cost' => $post['total_cost'], 'unit_cost' => $post['unit_cost'], 'parts' => $post['parts'], 'service' => $post['service'], 'auction_fee' => $post['auction_fee'], 'misc' => $post['misc']);
        $post['data'] = json_encode($data);

        if (!array_key_exists("call_on_price", $post)) {
            $post["call_on_price"] = 0;
        }
        if (!array_key_exists("destination_charge", $post)) {
            $post["destination_charge"] = 0;
        }

        unset($post['total_cost']);
        unset($post['unit_cost']);
        unset($post['parts']);
        unset($post['service']);
        unset($post['auction_fee']);
        unset($post['misc']);
        if ($id != null) {
            $where = array('id' => $id);
        }
        // echo '<pre>';
        // print_r($post);
        // echo '</pre>';exit;

        if (!empty($post)) {
            if ($id == null) {
                $motorcycle_id = $this->createRecord('motorcycle', $post, FALSE);
            } else {
                $this->updateRecord('motorcycle', $post, $where, FALSE);
                $motorcycle_id = $id;
            }
        }
        return $motorcycle_id;
    }

    public function isNewPrice($motorcycle_id, $retail_price, $sale_price) {
        $query = $this->db->query("Select retail_price, sale_price from motorcycle where id = ?", array($motorcycle_id));
        $is_new = false;
        foreach ($query->result_array() as $row) {
            if ($row["retail_price"] != $retail_price) {
                $is_new = true;
            }
            if ($row["sale_price"] != $sale_price) {
                $is_new = true;
            }
        }

        return $is_new;
    }

    public function isNewDescription($motorcycle_id, $description) {
        $query = $this->db->query("Select description from motorcycle where id = ?", array($motorcycle_id));
        $is_new = false;
        foreach ($query->result_array() as $row) {
            if ($row["description"] != $description) {
                $is_new = true;
            }
        }
        return $is_new;
    }

    public function updateMotorcycleDesc($id, $post) {

        // echo '<pre>';
        // print_r(htmlentities($post['descr']));
        // echo '</pre>';exit;
        $data = array('description' => $post['descr']);

        if ($this->isNewDescription($id, $post["descr"])) {
            $data["customer_set_description"] = 1;
        }

        $where = array('id' => $id);
        $this->updateRecord('motorcycle', $data, $where, FALSE);
    }

    public function updateMotorcycleVideos($id, $arr) {
        $this->db->delete('motorcycle_video', array('part_id' => $id));
        if (!empty($arr)) {
            $this->db->insert_batch('motorcycle_video', $arr);
        }
    }

    public function getMotorcycleVideo($part_id) {
        $where = array('part_id' => $part_id);
        $record = $this->selectRecords('motorcycle_video', $where);
        return $record;
    }

    public function updateMotorcycleImage($part_id, $arr) {
        $this->createRecord('motorcycleimage', $arr, FALSE);
    }

    // public function deleteMotorcycleImage( $id, $motorcycle_id ) {
    // $this->db->delete('motorcycle_video', array('id' => $id, 'motorcycle_id' => $motorcycle_id));
    // }

    public function getMotorcycleImage($id) {
        $where = array('motorcycle_id' => $id, "disable" => 0);
        $orderBy = 'priority_number asc';
        //$this->db->order_by('priority_number asc');
        $record = $this->selectRecords('motorcycleimage', $where, $orderBy);
        return $record;
    }

    public function fetchSpecificMotorcycleImage($motorcycleimage_id) {
        $query = $this->db->query("Select * from motorcycleimage where id = ?", array($motorcycleimage_id));
        $results = $query->result_array();
        return count($results) > 0 ? $results[0] : null;
    }

    public function getMotorcycleCategory() {
        $where = array();
        $record = $this->selectRecords('motorcycle_category', $where);
        return $record;
    }

    public function getMotorcycleVehicle() {
        $where = array();
        $record = $this->selectRecords('motorcycle_type', $where);
        return $record;
    }

    public function deleteMotorcycleImage($id, $motorcycle_id) {
        // JLB 12-07-17
        // We have to see if this is an external or internal image... If it's an external image, we have to just mark it as disabled.
        // If it's an internal image, we also have to go remove the file. I can't believe they would never remove the files.
        $image = $this->fetchSpecificMotorcycleImage($id);

        if (is_null($image)) {
            return;
        }

        if ($image["external"] > 0) {
            $this->db->query("Update motorcycleimage set disable = 1 where id = ? limit 1", array($id));
        } else {
            $this->db->delete('motorcycleimage', array('id' => $id, 'motorcycle_id' => $motorcycle_id));
            // go purge the image...
            $filename = STORE_DIRECTORY . "/html/media/" . $image["image_name"];
            if (file_exists($filename) && is_file($filename) && is_writable($filename)) {
                unlink($filename);
            }
        }
    }

    public function updateMotorcycleImageDescription($id, $pst) {
        //$data = array('description' => $post['descr']);
        $where = array('id' => $id);
        $this->updateRecord('motorcycleimage', $pst, $where, FALSE);
    }

    public function updateImageOrder($id, $ord) {
        $where = array('id' => $id);
        $data = array('priority_number' => $ord);
        $this->updateRecord('motorcycleimage', $data, $where, FALSE);
    }

    public function updateSliderOrder($id, $ord) {
        $this->db->query("Update slider set `order` = ? where id = ?", array($ord, $id));
//        $where = array('id' => $id);
//        $data = array('order' => $ord);
//        $this->updateRecord('slider', $data, $where, FALSE);
    }

    public function deleteMotorcycle($prod_id) {
        $where = array('id' => $prod_id);
        $this->db->delete('motorcycle', $where);
        $cwhere = array('motorcycle_id' => $prod_id);
        $this->db->delete('motorcycleimage', $cwhere);
        $bwhere = array('part_id' => $prod_id);
        $this->db->delete('motorcycle_video', $bwhere);
    }

    public function getAllRelatedCategories($cats = array()) {
        $return = array();
        if (!empty($cats)) {
            foreach ($cats as $cat) {
                $where = array('category_id' => $cat);
                $this->db->select('category_id, name');
                $catName = $this->selectRecord('category', $where);
                $return[] = $catName['category_id'];

                $cwhere = array('name' => $catName['name']);
                $this->db->select('category_id');
                $cat1 = $this->selectRecords('category', $cwhere);
                foreach ($cat1 as $cat2) {
                    $return[] = $cat2['category_id'];
                }
            }
        }
        $return = array_unique($return);
        return $return;
    }

    public function enhancedGetCreditApplications($filter = NULL, $orderBy = NULL, $limit = 20, $offset = 0) {
        $this->load->helper("jonathan");

        $where = jonathan_generate_likes(array("first_name", "last_name", "email", "co_first_name", "co_last_name", "co_email", "year", "make", "model", "application_status"), $filter, "WHERE");

        $total_count = 0;
        $query = $this->db->query("Select count(*) as cnt from finance_applications");
        foreach ($query->result_array() as $row) {
            $total_count = $row['cnt'];
        }

        // Now, is there a filter?
        $filtered_count = $total_count;
        if ($where != "") {
            // $query = $this->db->query("Select count(distinct part_id) as cnt from part left join partpartnumber using (part_id) left join partnumber  using (partnumber_id)  left join (select partvariation.*, concat(distributor.name, ' ', partvariation.part_number) as partlabel from partvariation join distributor using (distributor_id)) zpartvariation using (partnumber_id) left join partimage using (part_id) $where");
            $query = $this->db->query("Select count(distinct id) as cnt from finance_applications $where");
            foreach ($query->result_array() as $row) {
                $filtered_count = $row["cnt"];
            }
        }

        // Finally, run it!
        $query = $this->db->query("Select * from finance_applications  $where $orderBy limit $limit offset $offset ");
        $rows = $query->result_array();

        return array($rows, $total_count, $filtered_count);
    }

    public function getCreditApplications() {
        $this->db->order_by('application_date', 'DESC');
        $records = $this->selectRecords('finance_applications');
        return $records;
    }

    public function getCreditApplication($id) {
        $this->db->order_by('application_date', 'DESC');
        $this->db->where('id', $id);
        $records = $this->selectRecord('finance_applications');
        return $records;
    }

    public function update_finance($id, $data) {
        $where = array('id' => $id);
        $this->updateRecord('finance_applications', $data, $where, FALSE);
    }

    public function delete_finance($id) {
        $this->db->delete('finance_applications', array('id' => $id));
    }

    public function getCustomerByDetail($filter, $limit = 5, $offset = 0) {
        $this->db->select('contact.first_name AS first_name, ' .
                'contact.last_name AS last_name, ' .
                'contact.street_address AS street_address, ' .
                'contact.address_2 AS address_2, ' .
                'contact.city AS city, ' .
                'contact.state AS state, ' .
                'contact.zip AS zip, ' .
                'contact.country AS country, ' .
                'contact.email AS email, ' .
                'contact.phone AS phone, ' .
                'contact.company AS company, user.username as uemail, user.id as userId, user.last_login, user.admin, user.status, '
        );
        $this->db->join('contact', 'contact.id = user.billing_id');

        if (isset($filter['search']) && $filter['search'] != '') {
            $this->db->where('( concat(contact.first_name, " ", contact.last_name) like "%' . $filter['search'] . '%"', null);
            $this->db->or_where('email like "%' . $filter['search'] . '%"');
            $this->db->or_where('street_address like "%' . $filter['search'] . '%"');
            $this->db->or_where('address_2 like "%' . $filter['search'] . '%"');
            $this->db->or_where('phone like "%' . $filter['search'] . '%" )');
        }

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        if ($filter['sort'] == 'first_name') {
            $this->db->order_by('contact.first_name ' . $filter['sorter']);
        }

        $this->db->group_by('user.id');
        $records = $this->selectRecords('user', $where);
        return $records;
    }

    public function getAllCustomerAddresses($email) {
        $addresses = array();
        $this->db->where('contact.email', $email);
        $this->db->join('user', 'user.billing_id=contact.id', 'INNER');
        $this->db->select('contact.*, user.id as userId');
        $records = $this->selectRecord('contact', array());
        $addresses['billing'] = $records;

        $this->db->where('contact.email', $email);
        $this->db->join('user', 'user.shipping_id=contact.id', 'INNER');
        $this->db->select('contact.*, user.id as userId');
        $records1 = $this->selectRecord('contact', array());
        if (!$records1) {
            $addresses['shipping'] = $records;
        } else {
            $addresses['shipping'] = $records1;
        }
        return $addresses;
    }

    public function updateCustomerInOrder($orderId, $order) {
        $where = array('id' => $orderId);
        $this->updateRecord('order', $order, $where, FALSE);
    }

    public function updateOrderPaymentByAdmin($orderId, $order) {
        $data = array('order_id' => $orderId, 'amount' => $order['sales_price'], 'braintree_transaction_id' => $order['braintree_transaction_id'], 'transaction_date' => time());
        //$where = array('id' => $orderId);
        $this->createRecord('order_transaction', $data, FALSE);
    }

    public function getZip($zip1) {
        $where = array('zip' => $zip1);
        $point1 = $this->selectRecord('zip_locations', $where);
        $point2Arr = array(83716, 93706, 38118, 17022, 32219, 60490, 18434, 76177, 93291, 80011, 97024);
        $radius = 3958;      // Earth's radius (miles)
        $deg_per_rad = 57.29578;  // Number of degrees/radian (for conversion)
        $longestDistance = 0;
        foreach ($point2Arr as $zip2) {
            $where = array('zip' => $zip2);
            $point2 = $this->selectRecord('zip_locations', $where);
            $distance = ($radius * pi() * sqrt(
                            ($point1['lat'] - $point2['lat'])
                            * ($point1['lat'] - $point2['lat'])
                            + cos($point1['lat'] / $deg_per_rad)  // Convert these to
                            * cos($point2['lat'] / $deg_per_rad)  // radians for cos()
                            * ($point1['long'] - $point2['long'])
                            * ($point1['long'] - $point2['long'])
                    ) / 180);
            if ($distance > $longestDistance) {
                $longestDistance = $distance;
                $returnZip = $zip2;
            }
        }
        return $zip2;
    }

    public function calculateParcel($zip, $country, $gndValue = FALSE, $weight) {
        $furthestZip = $this->getZip($zip);
        $postal = array();
        $where = array('active' => 1);
        $this->db->order_by('order ASC');
        $shipment_types = $this->selectRecords('shipping_type', $where);
        $this->load->library('UpsShippingQuote');
        $objUpsRate = new UpsShippingQuote();
        $objUpsRate->setShipperZip($furthestZip);
        $strDestinationZip = $zip;
        $strPackageLength = '8';
        $strPackageWidth = '8';
        $strPackageHeight = '8';
        $strPackageWeight = $weight;
        if ($country == 'Canada')
            $strPackageCountry = 'CA';
        if ($country == 'USA')
            $strPackageCountry = 'US';
        $boolReturnPriceOnly = true;
        if ($strPackageWeight == 0)
            $strPackageWeight = 1;
        if ($shipment_types) {
            foreach ($shipment_types as $type) {

                $postal['UPS'][$type['code']] = $objUpsRate->GetShippingRate(
                        $strDestinationZip, $type['code'], $strPackageLength, $strPackageWidth, $strPackageHeight, $strPackageWeight, $boolReturnPriceOnly, $strPackageCountry
                );
                if (@$postal['UPS'][$type['code']]) {
                    $postal['UPS'][$type['code']] = $this->objectsIntoArray($postal['UPS'][$type['code']]);

                    $postal['UPS'][$type['code']]['RatedShipment']['TotalCharges']['MonetaryValue'] = $postal['UPS'][$type['code']]['RatedShipment']['TotalCharges']['MonetaryValue'];
                    if (($type['code'] == 'GND') && ($gndValue !== FALSE))
                        $postal['UPS'][$type['code']]['RatedShipment']['TotalCharges']['MonetaryValue'] = $gndValue;
                } else {
                    unset($postal['UPS'][$type['code']]);
                    if ($gndValue === FALSE)
                        $gndValue = 8;
                    $postal['UPS']['GND']['RatedShipment']['TotalCharges']['MonetaryValue'] = $gndValue;
                }
            }
        }
        $_SESSION['postalOptionsAdmin'] = $postal;

        return 'shipping_options';
    }

    private function objectsIntoArray($arrObjData, $arrSkipIndices = array()) {
        $arrData = array();

        if (is_object($arrObjData))
            $arrObjData = get_object_vars($arrObjData);

        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {
                if (is_object($value) || is_array($value))
                    $value = $this->objectsIntoArray($value, $arrSkipIndices);

                if (in_array($index, $arrSkipIndices))
                    continue;

                $arrData[$index] = $value;
            }
        }
        return $arrData;
    }

    public function subdividePostalOptions($postalOptions) {
        $ddArray = array();
        $where = array('active' => 1);
        $this->db->group_by('carrier');
        $carriers = $this->selectRecords('shipping_type', $where);
        if ($carriers) {
            foreach ($carriers as $carrier) {
                if (@$postalOptions[$carrier['carrier']]) {
                    $where = array('active' => 1, 'carrier' => $carrier['carrier']);
                    $shipment_types = $this->selectRecords('shipping_type', $where);
                    foreach ($shipment_types as $type)
                        $newType[$type['code']] = $type;
                    $segments = explode(',', $carrier['xml_structure']);

                    foreach (@$postalOptions[$carrier['carrier']] as $code => $opt) {
                        $value = $opt;
                        foreach ($segments as $seg)
                            $value = $value[$seg];

                        $valueArr = array('label' => $carrier['carrier'] . ' ' . $newType[$code]['description'] . ': $' . number_format($value, 2),
                            'value' => $value);

                        $ddArray[$code] = $valueArr;
                        $_SESSION['postalOptionsAdmin'][$code] = $valueArr;
                    }
                }
            }
        }
        if (@$_SESSION['postalOptionsAdmin']['COUPON'])
            $ddArray['COUPON'] = $_SESSION['postalOptionsAdmin']['COUPON'];
        return $ddArray;
    }

    public function shippingRules($productTotal, $countryId, $zip, $weight) {
        $where = array('active' => 1);
        $shippingValue = 'unprocessed';
        $shippingRules = $this->selectRecords('shipping_rules', $where);
        if (@$shippingRules) {
            foreach ($shippingRules as $rule) {
                $shippingValue = '';
                if (is_numeric($rule['weight_low'])) {
                    if ($weight < $rule['weight_low'])
                        $shippingValue = $rule['value'];
                    else
                        $shippingValue = FALSE;
                }
                if (($shippingValue !== FALSE) && is_numeric($rule['weight_high'])) {
                    if ($weight > $rule['weight_high'])
                        $shippingValue = $rule['value'];
                    else
                        $shippingValue = FALSE;
                }

                if (($shippingValue !== FALSE) && is_numeric($rule['price_low'])) {
                    if ($productTotal > $rule['price_low'])
                        $shippingValue = $rule['value'];
                    else
                        $shippingValue = FALSE;
                }
                if (($shippingValue !== FALSE) && is_numeric($rule['price_high'])) {
                    if ($productTotal < $rule['price_high'])
                        $shippingValue = $rule['value'];
                    else
                        $shippingValue = FALSE;
                }
                if (($shippingValue !== FALSE) && ($rule['country'])) {
                    $country = array('USA' => 'US', 'Canada' => 'CA');
                    if ($rule['country'] == $country[$countryId])
                        $shippingValue = $rule['value'];
                    else
                        $shippingValue = FALSE;
                }
                if (is_numeric($shippingValue))
                    return $shippingValue;
            }
        }
        return FALSE;
    }
    
    public function addShippingToOrder( $data ) {
        $where = array('id' => $data['orderId']);
        $order = $this->selectRecord('order', $where);
        $orderArr = array();
        //$orderArr['sales_price'] = (($order['sales_price']-$order['shipping'])+$data['price']);
        $orderArr['shipping'] = ($data['price']);
        $orderArr['shipping_type'] = ($data['shiping']);
        $this->updateRecord('order', $orderArr, $where, FALSE);
    }
    
    public function addOrderTransaction( $transaction ) {
        $this->createRecord('order_transaction', $transaction, FALSE);
    }
    public function updateSliderLink( $id, $link ) {
        $where = array('id' => $id);
        $data = array('banner_link' => $link);
        $this->updateRecord('slider', $data, $where, FALSE);
    }

}
