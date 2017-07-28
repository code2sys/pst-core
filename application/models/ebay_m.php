<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
error_reporting(E_ALL);
/* NOTE!!!  Need to make sure to turn on if checkbox is there and off it is not */

use \DTS\eBaySDK\Sdk;
use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\FileTransfer;
use \DTS\eBaySDK\BulkDataExchange;
use \DTS\eBaySDK\MerchantData;
use \DTS\eBaySDK\BulkDataExchange\Services;
use \DTS\eBaySDK\BulkDataExchange\Types;

class Ebay_M extends Master_M {

    public $headers = array();
    public $cred = array();
    public $serverUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
    private $compatibility_level = 849;
    private $call;
    private $siteID = 0;
    private $all = array();
    private $store_url = '';
    private $boundary;
    private $related_images = array();
    private $check_header_type_image = false; //check for header type
    public $call_from_cron = false;
    public $item_id;
    public $current_product_id;
    private $product_data = array();

	
    public function __construct() {
        parent::__construct();
    }

    function pr($d) {
        echo "<pre>";
        print_r($d);
        echo "</pre>";
    }

    public function generateEbayFeed($products_count, $upload_to_ebay = false) {
        //$products = $this->ebaylistings_no_variation(0, $products_count, 1);
		$newArray = [];
		$offset = 0;
        $products = $this->ebayListings($offset, $products_count, 1, $upload_to_ebay);
        $ebay_format_data = $this->convertToEbayFormat($products);
		//var_dump($ebay_format_data);
        $this->db->select('part_number');
        $this->db->from('ebay_ids');
        $query = $this->db->get();
        foreach ($query->result_array() as $single) {
            $newArray[] = $single['part_number'];
        }
		/*
		echo "TESTING12345";
		var_dump($ebay_format_data);
        foreach ($ebay_format_data as $title_key => $item) {
            if (in_array($item['product']['C:Manufacturer Part Number'], $newArray)) {
                $ebay_format_data_revised[$title_key] = $item;
            } else {
                $ebay_format_data_new[$title_key] = $item;
            }
        }
		*/
		$ebay_format_data_new = $ebay_format_data;
		//echo "TESTING54321";
		//var_dump($products);
		//die();
		//echo "RAWR";
        //create/NEW products XML
        $this->buildXmlAndHitEbay($ebay_format_data_new, 0, 1, 0);
        //updating products XML
//        $this->buildUpateXmlAndHitEbay($ebay_format_data_revised, 0, 1, 0);
    }

	
    private function convertToEbayFormat($data) {
        //$this->pr($data);
        //echo("convertToEbayFormat");
        $final = array();
        foreach ($data as $key => $value) {
            if (strpos($value['*Title'], 'COMBO') !== FALSE || isset($value['product_options'])) {
                $product_variation = $value['product_variation'];
                $different_variations = $value['product_options'];
                unset($value['product_variation']);
                unset($value['product_options']);

                $final[$value['*Title']] = array(
                    'product_variation' => $product_variation,
                    'product_options' => $different_variations
                );
                //$this->pr($different_variations);
                //die("aisi hoti hai");
            } 
			// else {
                if (trim($value['*Title']) != "") {
                    $product_data[$value['*Title']] = $value;
                    $title = $value['*Title'];
                    $variations = [];
                }

                if (trim(strtolower($value['Relationship'])) == "variation" || trim(strtolower($value['Relationship'])) == "compatibility") {
                    $variations[] = $value;
                }

                $final[$title]['product'] = $product_data[$title];
                $final[$title]['product_variation'] = $variations;

//				$final[$title] = array(
 //                   'product' => $product_data[$title],
 //                   'product_variation' => $variations
 //               );
            // }

		 }
        //echo "************************************";
        //$this->pr($final);
        //die("*");
        return $final;
    }

    public function getcategories() {
        $this->db->select("ebay_category_num");
        $this->db->from('category');
        $query = $this->db->get();
        return $query->result_array();
    }

    private function findTopPrority($all_category) {
//        $this->pr($all_category);
//        die("*");
        $street_find = array();
        $dirt_find = array();
        $utv_find = array();
        $atv_find = array();
        foreach ($all_category as $key => $category) {
            if (strpos($category, "STREET BIKE PARTS") !== false) {
                $street_find[$key] = $category;
            }

            if (strpos($category, "DIRT BIKE PARTS") !== false) {
                $dirt_find[$key] = $category;
            }
            if (strpos($category, "UTV PARTS") !== false) {
                $utv_find[$key] = $category;
            }
            if (strpos($category, "ATV PARTS ") !== false) {
                $atv_find[$key] = $category;
            }
        }

        $order = array("STREET BIKE PARTS" => $street_find, "DIRT BIKE PARTS" => $dirt_find, "UTV PARTS" => $utv_find, "ATV PARTS" => $atv_find);
//        pr($order);
        foreach ($order as $key => $value) {
            if (count($value) > 0) {
                break;
            }
        }
//        $category_long+string
//         if (strlen($cat['long_name']) > $endCategoryName)
//                            $endCategoryName = $cat['long_name'];
        $greaterThenFound = 0;
        $final_catId = 0;
        foreach ($value as $key => $value) {
            if (substr_count($value, '>') > $greaterThenFound) {
                $greaterThenFound = substr_count($value, '>'); //
                $final_catId = $key;
            }
        }
        return $final_catId;
    }

    public function update_ebay_feed_log($data) {
        $this->db->insert('ebay_feed_log', $data);
    }

    public function get_ebay_feed_log() {
        $sql = "SELECT * FROM ebay_feed_log order by run_at desc limit 1";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        if ($results) {
            return $results[0];
        } else {
            return array();
        }
    }

    public function ebaylistings_no_variation($offset = 0, $limit = 1000, $return_csv = FALSE) {
        $finalArray = array();
        if ($limit == 0) {
            $limit_query = '';
        } else {
            $limit_query = "LIMIT " . $offset . ", " . $limit;
        }
        $sql = "SELECT 
						part.part_id,
						'Add' AS '*Action(SiteID=eBayMotors|Country=US|Currency=USD|Version=745|CC=UTF-8)',	
						part.name AS '*Title',
						part.description AS '*Description',
						1000 AS '*ConditionID',
						CONCAT ('http://" . WEBSITE_HOSTNAME . "/productimages/', partimage.path) AS PicURL,
						'1' AS 'PayPalAccepted',
						'bvojcek@motomonster.com' AS 'PayPalEmailAddress',
						'FixedPrice' AS '*Format',
						'GTC' AS '*Duration',
						2 AS '*DispatchTimeMax', 
						'ReturnsAccepted' AS '*ReturnsAcceptedOption',
						'Days_30' AS 'ReturnsWithinOption',
						'Buyer' AS 'ShippingCostPaidByOption',
						brand.name AS 'C:Brand',
						partvariation.manufacturer_part_number AS 'C:Manufacturer Part Number',
						'28217' AS 'PostalCode',
						'UPSGround' AS 'ShippingService-1:Option',
						'1' AS 'ShippingService-1:FreeShipping',
						'' as 'CustomLabel',
						'' AS '*Quantity',
						'' AS '*StartPrice',
						'' AS 'Relationship',
						'' AS 'RelationshipDetails'
					FROM part
						JOIN partpartnumber ON partpartnumber.part_id = part.part_id
						JOIN partimage ON partimage.part_id = partpartnumber.part_id
						JOIN partnumber ON partnumber.partnumber_id = partpartnumber.partnumber_id
						JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
						JOIN brand ON brand.brand_id = partbrand.brand_id
						JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
						WHERE part.part_id IN (747613, 6754685) GROUP BY part.part_id $limit_query";

        $query = $this->db->query($sql);
        $parts = $query->result_array();

        //$this->pr($parts);

        $query->free_result();
        if (is_array($parts)) {
            foreach ($parts as &$part) {

                if (strpos($part['*Title'], 'COMBO') !== FALSE) {
//                    $this->pr($part);
//                    echo "***********************************************************";
//                    $this->pr($parts);
//                    continue;
                }
                $part_id = $part['part_id'];
                unset($part['part_id']);
                /*                 * ***********************************
                  Get Categories with longest string count
                 * ************************************ */
                $sql = "SELECT category.long_name,category.ebay_category_num
				FROM category
				JOIN partcategory ON partcategory.category_id = category.category_id
				WHERE partcategory.part_id = " . $part_id .
                        ' AND long_name NOT LIKE \'%UTV%\'';
                $query = $this->db->query($sql);
                $categories = $query->result_array();
//                print_r($categories);
//                die("category");
                $query->free_result();
                // Create Category Name;
                $endCategoryName = '';

                $all_categories = array();
                if ($categories) {
                    foreach ($categories as $cat) {
                        if ($cat['ebay_category_num'] != NULL) {
                            $all_categories[$cat['ebay_category_num']] = $cat['long_name'];
                            if (strlen($cat['long_name']) > $endCategoryName)
                                $endCategoryName = $cat['long_name'];
                        }
                    }
                }

//                $this->pr($endCategoryName);
//                die("category");
                // If no category, don't list the product
                if (empty($endCategoryName))
                    break;


//                $this->pr($all_categories);
//                die("*");
//                $part['*Category'] = $all_categories;
//                $part['*Category'] = $this->findTopPrority($all_categories);
                $part['EbayCategory'] = $this->findTopPrority($all_categories);

                $part['StoreCategory'] = $this->eBayStoreCategoryName($endCategoryName);

                /*                 * ************************
                  End Category Name.
                 * ************************* */

                // Get rest of records
                $sql = "SELECT
						'' AS '*Action(SiteID=eBayMotors|Country=US|Currency=USD|Version=745|CC=UTF-8)',
						
						'' AS '*Title',
						'' AS '*Description',
						'' AS '*ConditionID',
						'' AS PicURL,
						'' AS 'PayPalAccepted',
						'' AS 'PayPalEmailAddress',
						'' AS '*Format',
						'' AS '*Duration',
						'' AS 'DispatchTimeMax*', 
						'' AS 'ReturnsAcceptedOption*',
						'' AS 'ReturnsWithinOption',
						'' AS 'ShippingCostPaidByOption',
						'' AS 'C:Brand',
						'' AS 'C:Manufacturer Part Number',
						'' AS 'PostalCode',
						'' AS 'ShippingService-1:Option',
						'' AS 'ShippingService-1:FreeShipping',
						partnumber.partnumber_id as CustomLabel,
						partnumberpartquestion.answer AS 'answer',
						partquestion.question,
						partvariation.quantity_available AS '*Quantity',
						'' AS '*StartPrice',
						(partnumber.cost * 1.15) + 15 AS 'MIN_PRICE',
						CASE WHEN partnumber.price < 100 THEN partnumber.price + 13 ELSE partnumber.price END AS 'MAX_PRICE',
						partnumber.price,
						partvariation.stock_code,
						'' AS 'Relationship',
						'' AS 'RelationshipDetails',
						'' AS '*Category',
						'' AS 'StoreCategory'
					FROM partnumber
					JOIN partnumberpartquestion ON partnumberpartquestion.partnumber_id = partnumber.partnumber_id
					JOIN partquestion ON partquestion.partquestion_id = partnumberpartquestion.partquestion_id
					JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
					JOIN partimage ON partimage.part_id = partpartnumber.part_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					JOIN part ON part.part_id = partpartnumber.part_id
					WHERE part.part_id = " . $part_id . "
                                        AND partvariation.quantity_available > 0
					GROUP BY partnumber.partnumber";

                $query = $this->db->query($sql);
                $partnumbers = $query->result_array();
//                print_r($partnumbers);
//                die('-------');
                $query->free_result();
                if (is_array($partnumbers)) {
                    $categoryRec = array();
                    $fitmentArr = array();
                    $basicPrice = 0;
                    $samePrice = TRUE;
//                    $this->pr($partnumbers);
//                    die("test");
                    foreach ($partnumbers as $pn) {
                        if ($pn['*Quantity'] > 0) {
                            //Calculate MAP Price
                            $this->db->select('MIN(brand.map_percent) as map_percent');
                            $where = array('partbrand.part_id' => $part_id, 'brand.map_percent > ' => 0);
                            $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
                            $brand_map_percent = $this->selectRecord('brand', $where);

                            $brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : 0;


                            if (($brandMAPPercent > 0) && ($pn['stock_code'] != 'Closeout')) {

                                $mapPrice = (((100 - $brandMAPPercent) / 100) * $pn['price']);
                                if ($mapPrice > $pn['MIN_PRICE'])
                                    $pn['MIN_PRICE'] = $mapPrice;
                            }
                            $pn['*StartPrice'] = $pn['MIN_PRICE'];

                            if ($basicPrice == 0)
                                $basicPrice = $pn['*StartPrice'];
                            if (($samePrice) && ($pn['*StartPrice'] != $basicPrice))
                                $samePrice = FALSE;


                            // Record Prep
                            $part['Relationship'] = '';
                            $part['RelationshipDetails'] = '';
                            $part['CustomLabel'] = $pn['CustomLabel'];
                            $part['*Quantity'] = $pn['*Quantity'];
                            $part['*Description'] = preg_replace("/\r\n|\r|\n/", '', $part['*Description']);

                            unset($pn['stock_code']);
                            unset($pn['MIN_PRICE']);
                            unset($pn['MAX_PRICE']);
                            unset($pn['price']);


                            // Fitment compatability
                            $sql = "SELECT CONCAT ('Make=', make.name, '|Model=',  model.name, '|Year=', partnumbermodel.year) AS fitment 
									FROM (`partnumbermodel`) 
									JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
									JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
									WHERE `partnumbermodel`.`partnumber_id` =  '" . $pn['CustomLabel'] . "'
									AND make.machinetype_id != 43954;";
                            $query = $this->db->query($sql);
                            $rides = $query->result_array();
							
							//echo "FITMENT";
							//var_dump($rides);

                            $query->free_result();
                            $pn['CustomLabel'] = '';
                            if (!empty($rides)) { // Save Record for Fitment
                                unset($pn['answer']);
                                unset($pn['question']);
                                $samePrice = FALSE;
                                foreach ($rides as $ride) {
                                    $pn['Relationship'] = 'Compatibility';
                                    $pn['RelationshipDetails'] = $ride['fitment'];
                                    $fitmentArr[] = $pn;
                                }
                            } elseif (!empty($pn['question'])) { // Save record for Variations
                                $pn['Relationship'] = 'Variation';
                                $pn['RelationshipDetails'] = str_replace(' ', '', $pn['question'] . '=' . $pn['answer']);
                                unset($pn['answer']);
//                                unset($pn['question']);
                                $categoryRec[] = $pn;
                            }
                        }
                    }
                    if (($samePrice) && (@$categoryRec)) {
//                        $this->pr($categoryRec);
//                        echo '*************************************';
//                        die("trsut");
                        $part['*Quantity'] = '';
                        $part['*StartPrice'] = $basicPrice;
                        $part['item_id'] = $part_id;
                        $finalArray[] = $part;

                        foreach ($categoryRec as $rb) {
                            $rb['*StartPrice'] = $basicPrice;
                            $finalArray[] = $rb;
                        }
                    } elseif (!empty($categoryRec)) {
                        $variations = array();
//                        $this->pr($categoryRec);
//                        echo '*************************************';
//                        die("trsut");
//                        $this->pr($part);
//                        echo "*************************************";
//                        $this->pr($categoryRec);
//                        echo '*************************************';
                        $combopartIds = $this->checkForComboReporting($part_id);
//                        $this->pr($combopartIds);die("123");
                        if (is_array($combopartIds)) {
                            $PriceArr = array();
                            $finalPriceArr = array('retail_min' => 0, 'retail_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
                            foreach ($combopartIds as $id) {
                                $PriceArr[] = $this->getPriceRangeReporting($id, FALSE, FALSE);
                                $where = array('partpartnumber.part_id' => $id);
                                $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
                                $this->db->where('partnumber.price > 0');
                                $this->db->where('partdealervariation.quantity_available > 0');
                                $this->db->select('partnumber, MIN(partnumber.dealer_sale) AS dealer_sale_min, MAX(partnumber.dealer_sale) AS dealer_sale_max', FALSE);
                                $this->db->group_by('part_id');
                                $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
                                $partDealerRec = $this->selectRecord('partnumber', $where);

                                if (empty($partDealerRec)) {
                                    $PriceArr['dealer_sale_min'] = 0;
                                    $PriceArr['dealer_sale_max'] = 0;
                                }
                            }
                            foreach ($PriceArr as $pa) {
                                $finalPriceArr['retail_min'] += $pa['retail_min'];
                                $finalPriceArr['retail_max'] += $pa['retail_max'];
                                $finalPriceArr['sale_min'] += $pa['sale_min'];
                                $finalPriceArr['sale_max'] += $pa['sale_max'];
                                $finalPriceArr['dealer_sale_min'] += $pa['dealer_sale_min'];
                                $finalPriceArr['dealer_sale_max'] += $pa['dealer_sale_max'];
                            }
                            $combo_price = $this->calculateMarkupReporting($finalPriceArr['retail_min'], $finalPriceArr['retail_max'], $finalPriceArr['sale_min'], $finalPriceArr['sale_max'], @$_SESSION['userRecord']['markup'], $finalPriceArr['dealer_sale_min'], $finalPriceArr['dealer_sale_max'], $finalPriceArr['cnt'])['sale_min'];
//                            $this->pr($combo_price);
//                            die("*");
                        }
                        foreach ($categoryRec as $rb) {
                            $newArray = $part;
                            $newArray['*Quantity'] = $rb['*Quantity'];
                            $newArray['*StartPrice'] = $combo_price;
                            $newArray['*Description'] = '';
                            $newArray['Relationship'] = 'Combo';

                            $newArray['RelationshipDetails'] = $rb['RelationshipDetails'];
                            $newArray['*Title'] = '';
                            $combo_variations[] = $newArray;
                        }
                        $product_options = $this->getProductQuestions($part_id);
//                        $this->pr($product_options);die("test");
                        $options_vailable = array();
                        foreach ($product_options as $otions_array) {
                            $options_vailable[$otions_array['question']][] = $otions_array['answer'];
                        }
//                        $this->pr($options_vailable);die("test");
                        $part['*StartPrice'] = $combo_price;
                        $part['product_options'] = $options_vailable;
                        $part['product_variation'] = $combo_variations;
                        $finalArray[] = $part;
                    } elseif (!empty($fitmentArr)) {
//                        echo "*****************************************************";
//                        $this->pr($fitmentArr);
                        $part['*StartPrice'] = $rb['*StartPrice'];
//                        $this->pr($part);
//                        echo "*****************************************************";
                        $compatibility_array = array();
                        $item = array();
//                        foreach ($fitmentArr as $key => $single_fitment) {
//                            $data_explode = explode('|', $single_fitment['RelationshipDetails']);
//                            $compatibility_array[$data_explode[0]][$data_explode[1]][] = $data_explode[2];
//                        }
                        foreach ($fitmentArr as $key => $single_fitment) {
                            $change = $part;
//                            $this->pr($single_fitment);
//                            die("123");

                            $data_explode = explode('|', $single_fitment['RelationshipDetails']);
                            $make_explode = explode('=', $data_explode[0]);
                            $model_explode = explode('=', $data_explode[1]);
                            $year_explode = explode('=', $data_explode[2]);
                            $title = $part['*Title'] . ' For ' . $make_explode[1] . ' ' . $model_explode[1];
                            if (key_exists($title, $item)) {
                                $item[$title][] = $single_fitment;
                            } else {
                                $change['*Title'] = $title;
                                $item[$title][] = $change;
                            }
                        }
//                        $this->pr($item);
//                        die("8");
                        foreach ($item as $key => $single_array) {
                            $finalArray = array_merge($finalArray, $single_array);
                        }

//                        $finalArray[] = $part;
//                        foreach ($fitmentArr as $rb)
//                            $finalArray[] = $rb;
                    }
                }
            }
        }
        //$this->pr($finalArray);
        //echo "*****************************************";
        //die;
        if ($return_csv) {
            return $finalArray;
        }
        $csv = $this->array2csv($finalArray);
        return $csv;
    }

    public function ebayListings($offset = 0, $limit = 1000, $return_csv = FALSE, $send_to_ebay = FALSE) {
        // Filter quantity of 0, Price in 1 row only
		$nocat=0;
		$catg=0;
        $finalArray = array();
        if ($limit == 0) {
            $limit_query = '';
        } else {
            $limit_query = "LIMIT " . $offset . ", " . $limit;
        }
		//$where_part_id = " AND part.part_id = " . 6912593;  //  maxxis tire (combo?)
		//$where_part_id = " AND part.part_id = " . 4661025; //  honda shirt (variations, no compatibility)
		//$where_part_id = " AND part.part_id = " . 6131964;  //  acerbis kit (combo and compatibility)
		//$where_part_id = " AND part.part_id = " . 4661029;  //  honda long sleeve shirt, variations, no MAP
		//$where_part_id = " AND part.part_id = " . 4661416;  //  Firstgear jacket, variations, MAP
		//$where_part_id = " AND part.part_id = " . 6506255;

        $sql = "SELECT 
						part.part_id,
						'Add' AS '*Action(SiteID=eBayMotors|Country=US|Currency=USD|Version=745|CC=UTF-8)',	
						part.name AS '*Title',
						part.description AS '*Description',
						1000 AS '*ConditionID',
						CONCAT ('http://" . WEBSITE_HOSTNAME . "/productimages/', partimage.path) AS PicURL,
						'1' AS 'PayPalAccepted',
						'bvojcek@motomonster.com' AS 'PayPalEmailAddress',
						'FixedPrice' AS '*Format',
						'GTC' AS '*Duration',
						2 AS '*DispatchTimeMax', 
						'ReturnsAccepted' AS '*ReturnsAcceptedOption',
						'Days_30' AS 'ReturnsWithinOption',
						'Buyer' AS 'ShippingCostPaidByOption',
						brand.name AS 'C:Brand',
						partnumber.price as customprice,
						partnumber.sale as saleprice,
						partvariation.manufacturer_part_number AS 'C:Manufacturer Part Number',
						'28217' AS 'PostalCode',
						'UPSGround' AS 'ShippingService-1:Option',
						'1' AS 'ShippingService-1:FreeShipping',
						'' as 'CustomLabel',
						'' AS '*Quantity',
						'' AS '*StartPrice',
						'' AS 'Relationship',
						'' AS 'RelationshipDetails'
					FROM part
						JOIN partpartnumber ON partpartnumber.part_id = part.part_id
						JOIN partimage ON partimage.part_id = partpartnumber.part_id
						JOIN partnumber ON partnumber.partnumber_id = partpartnumber.partnumber_id
						JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
						JOIN brand ON brand.brand_id = partbrand.brand_id
						JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
						WHERE partnumber.exclude_market_place != 1
						AND partnumber.closeout_market_place != 1
						AND brand.closeout_market_place != 1
						AND brand.exclude_market_place != 1
						GROUP BY part.part_id $limit_query";
						$query = $this->db->query($sql);
        $parts = $query->result_array();
		
		echo "Initial query: " . count($parts);
		
        $query->free_result();
        $paypal_email = $this->get_paypalemail();
        $quantity = $this->get_quantity();
        if (is_array($parts)) {
            foreach ($parts as &$part) {
                if (strpos($part['*Title'], 'COMBO') !== FALSE) {
                    continue;
                }
				$part["PayPalEmailAddress"] = $paypal_email;
				$part["*Quantity"] = min($part["*Quantity"], $quantity);
                $part_id = $part['part_id'];
                unset($part['part_id']);
                /*                 * ***********************************
                  Get Categories with longest string count
                 * ************************************ */
                $sql = "SELECT category.long_name,category.ebay_category_num
				FROM category
				JOIN partcategory ON partcategory.category_id = category.category_id
				WHERE partcategory.part_id = " . $part_id .
                        ' AND long_name NOT LIKE \'%UTV%\'';
                $query = $this->db->query($sql);
                $categories = $query->result_array();
//                $this->pr($categories);
//                die("category");
                $query->free_result();
                // Create Category Name;
                $endCategoryName = '';

                $all_categories = array();
                if ($categories) {
                    foreach ($categories as $cat) {
                        if ($cat['ebay_category_num'] != NULL) {
                            $all_categories[$cat['ebay_category_num']] = $cat['long_name'];
                            if (strlen($cat['long_name']) > $endCategoryName)
                                $endCategoryName = $cat['long_name'];
                        }
                    }
                }

//                $this->pr($endCategoryName);
//                die("category");
                // If no category, don't list the product
                if (empty($endCategoryName)) {
					$nocat++;
					echo "<br>NOCAT".$part_id;
                    continue;
				} else {
					echo "<br>CAT".$part_id;
					
					$catg++;
				}
//                $this->pr($all_categories);
//                die("*");
//                $part['*Category'] = $all_categories;
//                $part['*Category'] = $this->findTopPrority($all_categories);
                $part['EbayCategory'] = $this->findTopPrority($all_categories);

                $part['StoreCategory'] = $this->eBayStoreCategoryName($endCategoryName);

                /*                 * ************************
                  End Category Name.
                 * ************************* */

                // Get rest of records
                $sql = "SELECT
						'' AS '*Action(SiteID=eBayMotors|Country=US|Currency=USD|Version=745|CC=UTF-8)',
						
						'' AS '*Title',
						'' AS '*Description',
						'' AS '*ConditionID',
						'' AS PicURL,
						'' AS 'PayPalAccepted',
						'' AS 'PayPalEmailAddress',
						'' AS '*Format',
						'' AS '*Duration',
						'' AS 'DispatchTimeMax*', 
						'' AS 'ReturnsAcceptedOption*',
						'' AS 'ReturnsWithinOption',
						'' AS 'ShippingCostPaidByOption',
						'' AS 'C:Brand',
						'' AS 'C:Manufacturer Part Number',
						'' AS 'PostalCode',
						'' AS 'ShippingService-1:Option',
						'' AS 'ShippingService-1:FreeShipping',
						partnumber.partnumber_id as CustomLabel,
						partnumber.price as customprice,
						partnumber.sale as saleprice,
						partnumberpartquestion.answer AS 'answer',
						partquestion.question,
						partvariation.quantity_available AS '*Quantity',
						'' AS '*StartPrice',
						(partnumber.cost * 1.15) + 15 AS 'MIN_PRICE',
						CASE WHEN partnumber.price < 100 THEN partnumber.price + 13 ELSE partnumber.price END AS 'MAX_PRICE',
						partnumber.sale as price,
						partvariation.stock_code,
						'' AS 'Relationship',
						'' AS 'RelationshipDetails',
						'' AS '*Category',
						'' AS 'StoreCategory'
					FROM partnumber
					JOIN partnumberpartquestion ON partnumberpartquestion.partnumber_id = partnumber.partnumber_id
					JOIN partquestion ON partquestion.partquestion_id = partnumberpartquestion.partquestion_id
					JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
					JOIN partimage ON partimage.part_id = partpartnumber.part_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					JOIN part ON part.part_id = partpartnumber.part_id
					WHERE part.part_id = " . $part_id . " 
					AND partnumber.exclude_market_place != 1
					AND partnumber.closeout_market_place != 1
					AND partquestion.productquestion = 0
					GROUP BY partnumber.partnumber";
                //					AND partvariation.quantity_available > 3
                $query = $this->db->query($sql);
                $partnumbers = $query->result_array();
                $query->free_result();


                                // Fitment compatability
                                $sql = "SELECT CONCAT ('Make=', make.name, '|Model=',  model.name, '|Year=', partnumbermodel.year) AS fitment 
									FROM (`partnumbermodel`) 
									JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
									JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
									WHERE `partnumbermodel`.`partnumber_id` =  '" . $partnumbers[0]['CustomLabel'] . "'
									AND make.machinetype_id != 43954;";
                                $query = $this->db->query($sql);
                                $rides = $query->result_array();
								

				echo "1";
                if (is_array($partnumbers)) {
					
					var_dump($partnumbers);
					//die("HEREWEARE");
					
                    $categoryRec = array();
                    $fitmentArr = array();
                    $basicPrice = 0;
                    $samePrice = TRUE;
                    if (count($partnumbers) > 0) {
				echo "2";
                        foreach ($partnumbers as $pn) {
				echo "3";

                            if ($pn['*Quantity'] > 0) {
                                //Calculate MAP Price
                                $this->db->select('MIN(brand.map_percent) as map_percent');
                                $where = array('partbrand.part_id' => $part_id);
                                $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
                                $brand_map_percent = $this->selectRecord('brand', $where);
								
								//var_dump($brand_map_percent);

                                $brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : 0;

								echo "here";
                                $pn['*StartPrice'] = $pn['price'];
                                if (($brand_map_percent['map_percent'] !== NULL) && ($pn['stock_code'] != 'Closeout')) {
									
                                    $mapPrice = (((100 - $brandMAPPercent) / 100) * $pn['customprice']);
// no longer using MIN_PRICE        if ($mapPrice > $pn['MIN_PRICE'])
// per Brandt, 7/24/17              $pn['MIN_PRICE'] = $mapPrice;
									if(!($pn['price'] < $pn['customprice']))
										$pn['*StartPrice'] = $mapPrice;
									echo "HERE";
									echo "customprice: " .$pn['customprice'];
									echo "MAP: ".$brand_map_percent['map_percent']. " and price: " . $pn['*StartPrice'];
									echo $pn['stock_code'];
                                } 
								if($brand_map_percent['map_percent'] === NULL || ($pn['stock_code'] == 'Closeout'))	{						
									$markup = 1 + ($this->get_markup()/100);
									$pn['*StartPrice'] = $pn['*StartPrice']*$markup;
									echo "THERE";
									echo $pn['stock_code'];
								}


                                if ($basicPrice == 0)
                                    $basicPrice = $pn['*StartPrice'];
                                if (($samePrice) && ($pn['*StartPrice'] != $basicPrice))
                                    $samePrice = FALSE;

								$quantity = $this->get_quantity();

                                // Record Prep
                                $part['Relationship'] = '';
                                $part['RelationshipDetails'] = '';
                                $part['CustomLabel'] = $pn['CustomLabel'];
								$part["*Quantity"] = min($pn["*Quantity"], $quantity);
								
                                $part['*Description'] = preg_replace("/\r\n|\r|\n/", '', $part['*Description']);

                                unset($pn['stock_code']);
                                unset($pn['MIN_PRICE']);
                                unset($pn['MAX_PRICE']);
                                unset($pn['price']);

				echo "4";

                                // Fitment compatability
                                $sql = "SELECT CONCAT ('Make=', make.name, '|Model=',  model.name, '|Year=', partnumbermodel.year) AS fitment 
									FROM (`partnumbermodel`) 
									JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
									JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
									WHERE `partnumbermodel`.`partnumber_id` =  '" . $pn['CustomLabel'] . "'
									AND make.machinetype_id != 43954;";
                                $query = $this->db->query($sql);
                                $rides = $query->result_array();
								
							
                                $query->free_result();
                                $pn['CustomLabel'] = '';
                                if (!empty($rides)) { // Save Record for Fitment
                                    //unset($pn['answer']);
                                    //unset($pn['question']);
                                    $samePrice = FALSE;
                                    foreach ($rides as $ride) {
                                        $pn['Relationship'] = 'Compatibility';
                                        $pn['RelationshipDetails'] = $ride['fitment'];
                                        $fitmentArr[] = $pn;
                                    }
                                } 
								if (!empty($pn['question'])) { // Save record for Variations
                                    $pn['Relationship'] = 'Variation';
                                    $pn['RelationshipDetails'] = str_replace(' ', '', $pn['question'] . '=' . $pn['answer']);
                                    unset($pn['answer']);
//                                unset($pn['question']);
                                    $categoryRec[] = $pn;
                                }
                            }
                        }
                    } else {
						$part["*Quantity"] = min($partnumbers[0]["*Quantity"], $quantity);
                        $part['*StartPrice'] = $partnumbers[0]['price'];
                        $finalArray[] = $part;
                    }
					
                //$this->pr($finalArray);
                //$this->pr("******************************************");
                //die("*!*!*");

				echo "5";
					
                    if (($samePrice) && (@$categoryRec)) {
                        $part['*Quantity'] = '';
                        $part['*StartPrice'] = $basicPrice;
                        $part['item_id'] = $part_id;
                        $finalArray[] = $part;

                        foreach ($categoryRec as $rb) {
                            $rb['*StartPrice'] = $basicPrice;
                            $finalArray[] = $rb;
                        }
				echo "6";
                    } elseif (!empty($categoryRec)) {
                        $variations = array();
                        //$this->pr($categoryRec);
                        $combopartIds = $this->checkForComboReporting($part_id);
                        if (is_array($combopartIds)) {
                            $PriceArr = array();
                            $finalPriceArr = array('retail_min' => 0, 'retail_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
                            foreach ($combopartIds as $id) {
                                $PriceArr[] = $this->getPriceRangeReporting($id, FALSE, FALSE);
                                $where = array('partpartnumber.part_id' => $id);
                                $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
                                $this->db->where('partnumber.price > 0');
                                $this->db->where('partdealervariation.quantity_available > 0');
                                $this->db->select('partnumber, MIN(partnumber.dealer_sale) AS dealer_sale_min, MAX(partnumber.dealer_sale) AS dealer_sale_max', FALSE);
                                $this->db->group_by('part_id');
                                $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
                                $partDealerRec = $this->selectRecord('partnumber', $where);

                                if (empty($partDealerRec)) {
                                    $PriceArr['dealer_sale_min'] = 0;
                                    $PriceArr['dealer_sale_max'] = 0;
                                }
                            }
                            foreach ($PriceArr as $pa) {
                                $finalPriceArr['retail_min'] += $pa['retail_min'];
                                $finalPriceArr['retail_max'] += $pa['retail_max'];
                                $finalPriceArr['sale_min'] += $pa['sale_min'];
                                $finalPriceArr['sale_max'] += $pa['sale_max'];
                                $finalPriceArr['dealer_sale_min'] += $pa['dealer_sale_min'];
                                $finalPriceArr['dealer_sale_max'] += $pa['dealer_sale_max'];
                            }
                            $combo_price = $this->calculateMarkupReporting($finalPriceArr['retail_min'], $finalPriceArr['retail_max'], $finalPriceArr['sale_min'], $finalPriceArr['sale_max'], @$_SESSION['userRecord']['markup'], $finalPriceArr['dealer_sale_min'], $finalPriceArr['dealer_sale_max'], $finalPriceArr['cnt'])['sale_min'];
////                            $this->pr($combo_price);
////                            die("*");
                        }
				echo "7";
                        foreach ($categoryRec as $rb) {
                            $newArray = $part;
                            $newArray['*Quantity'] = $rb['*Quantity'];
							if(isset($combo_price))
								$newArray['*StartPrice'] = $combo_price;
                            $newArray['*Description'] = '';
                            $newArray['Relationship'] = 'Combo';

                            $newArray['RelationshipDetails'] = $rb['RelationshipDetails'];
                            $newArray['*Title'] = '';
                            $combo_variations[] = $newArray;
                        }
                        $product_options = $this->getProductQuestions($part_id);
////                        $this->pr($product_options);die("test");
                        $options_vailable = array();
                        foreach ($product_options as $otions_array) {
                            $options_vailable[$otions_array['question']][] = $otions_array['answer'];
                        }
						if(isset($combo_price))
							$part['*StartPrice'] = $combo_price;
                        $part['product_options'] = $options_vailable;
                        $part['product_variation'] = $combo_variations;
                        $finalArray[] = $part;
                    } 
				echo "8";
					if (!empty($fitmentArr)) {
				echo "W";
                        $part['*StartPrice'] = $rb['*StartPrice'];
                        $compatibility_array = array();
                        $item = array();
						$quantity = $this->get_quantity();
                        foreach ($fitmentArr as $key => $single_fitment) {
				echo "H";
                            $change = $part;

                            $data_explode = explode('|', $single_fitment['RelationshipDetails']);
                            $make_explode = explode('=', $data_explode[0]);
                            $model_explode = explode('=', $data_explode[1]);
                            $year_explode = explode('=', $data_explode[2]);
							
                            $title = $part['*Title'] . ' For ' . $make_explode[1] . ' ' . $model_explode[1];
				echo ".";
							if($single_fitment["saleprice"]!=NULL) {
								echo "!";
								$single_fitment['*StartPrice'] = $single_fitment["saleprice"];
							} else {
								echo "?";
								$single_fitment['*StartPrice'] = $single_fitment["*StartPrice"];									
							}
				echo ",";
							if($brand_map_percent['map_percent'] === NULL)	{						
								$markup = 1 + ($this->get_markup()/100);
								$single_fitment['*StartPrice'] = $single_fitment['*StartPrice']*$markup;
							}
				echo "A";
                            if (key_exists($title, $item)) {

                                $item[$title][] = $single_fitment;
                            } else {
                                $change['*Title'] = $title;
								$change['*StartPrice'] = $single_fitment["*StartPrice"];									
								
								$change['*Quantity'] = min($single_fitment["*Quantity"], $quantity);
                                $item[$title][] = $change;
                                $item[$title][] = $single_fitment;
                            }
                        }
                        foreach ($item as $key => $single_array) {

                            $finalArray = array_merge($finalArray, $single_array);
                        }
				echo "T";
                    }
					echo "9";
                }
                if (empty($part['saleprice'])&&isset($part['customprice'])) {
                    $part['*StartPrice'] = $part['customprice'];
                } else {
                    $part['*StartPrice'] = $part['saleprice'];
				}
				echo "<br>DONE".$part_id;
				
            }
        }
		//echo "Array: " . count($finalArray);
		//echo "<br>No category: ".$nocat . "/" .$catg;
		//die();
	//var_dump($finalArray);
		//echo "BOOOM";
		//die();
		if(!$send_to_ebay) {
		} else {

			if ($return_csv) {
				return $finalArray;
			}
			$csv = $this->array2csv($finalArray);
			return $csv;
		}
	}

    private function eBayCategoryName($categoryName) {
        /*
          •	Banners / Flags    # 56420 (leaf)
          •	Boots    # 6751 (leaf)
          •	Eye Wear    # 50424 (leaf)
          •	Gloves    # 50425 (leaf)
          •	Hats & Caps    # 50426 (leaf)
          •	Helmets    # 6749 (leaf)
          •	Jackets & Leathers    # 6750 (leaf)
          •	Off-Road Gear    # 34353 (leaf)
          •	Other Merchandise    # 34355 (leaf)
          •	Pants & Chaps    # 34354 (leaf)
          •	Patches    # 50427 (leaf)
          •	Shirts    # 6752 (leaf)
          •	Sweats & Hoodies    # 177125 (leaf)
         */
        if (strpos($categoryName, 'PANT') !== FALSE)
            return 34354;
        if (strpos($categoryName, 'SPROCKET') !== FALSE)
            return 49831;
        if (strpos($categoryName, 'HAT') !== FALSE)
            return 50426;
        if (strpos($categoryName, 'BOOT') !== FALSE)
            return 6751;
        if (strpos($categoryName, 'GLASSES') !== FALSE)
            return 50424;
        if (strpos($categoryName, 'GOGGLES') !== FALSE)
            return 50424;
        if (strpos($categoryName, 'HELMET') !== FALSE)
            return 6749;
        if (strpos($categoryName, 'JACKET') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'HOODY') !== FALSE)
            return 177125;
        if (strpos($categoryName, 'SWEATSHIRT') !== FALSE)
            return 177125;
        if (strpos($categoryName, 'SHIRT') !== FALSE)
            return 6752;
        if (strpos($categoryName, 'TANK TOP') !== FALSE)
            return 6752;
        if (strpos($categoryName, 'RAIN') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'JERSEYS') !== FALSE)
            return 34353;
        if (strpos($categoryName, 'PROTECTION') !== FALSE)
            return 34353;
        if (strpos($categoryName, 'GLOVES') !== FALSE)
            return 34353;
        if (strpos($categoryName, 'TIRES & WHEELS') !== FALSE)
            return 124313;
        if (strpos($categoryName, 'PACKS & BAGS') !== FALSE)
            return 34355;
        if (strpos($categoryName, 'SWIM TRUNKS') !== FALSE)
            return 34353;
        if (strpos($categoryName, 'SHOES') !== FALSE)
            return 6751;
        if (strpos($categoryName, 'HEATED SOCKS') !== FALSE)
            return 6751;
        if (strpos($categoryName, 'RACESUITS') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'HEATED GLOVES') !== FALSE)
            return 50425;
        if (strpos($categoryName, 'HEATED GEAR ACCESSORIES') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'SUITS') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'BASEGEAR & LINERS') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'GEAR BAGS') !== FALSE)
            return 34355;
        if (strpos($categoryName, 'BACKPACKS') !== FALSE)
            return 34355;
        if (strpos($categoryName, 'CHAINS & MASTER LINKS') !== FALSE)
            return 49831;
        if (strpos($categoryName, 'CHEMICALS & OILS') !== FALSE)
            return 111112;
        if (strpos($categoryName, 'TRAILER ACCESSORIES') !== FALSE)
            return 50069;
        if (strpos($categoryName, 'TRAILER ELECTRICAL') !== FALSE)
            return 50069;
        if (strpos($categoryName, 'TRAILER TIRES & WHEELS') !== FALSE)
            return 50071;
        if (strpos($categoryName, 'TRAILERS') !== FALSE)
            return 50072;
        if (strpos($categoryName, 'TRAILERS') !== FALSE)
            return 50072;
        if (strpos($categoryName, 'TOOLS') !== FALSE)
            return 43990;
        if (strpos($categoryName, 'BARS & CONTROLS') !== FALSE)
            return 35564;
        return $categoryName;
    }

    private function eBayStoreCategoryName($categoryName) {
        if (strpos($categoryName, 'DIRT BIKE PARTS > CASUAL APPAREL') !== FALSE)
            return 8506710012;
        if (strpos($categoryName, 'SPROCKET') !== FALSE)
            return 8494715012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > RIDING GEAR') !== FALSE)
            return 8506717012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > PROTECTION') !== FALSE)
            return 8506711012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > TRAILERS') !== FALSE)
            return 8506712012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > DIRT BIKE PARTS') !== FALSE)
            return 8506716012;
        if (strpos($categoryName, 'STREET BIKE PARTS > PROTECTION') !== FALSE)
            return 8506718012;
        if (strpos($categoryName, 'STREET BIKE PARTS > STREET BIKE PARTS') !== FALSE)
            return 8506719012;
        if (strpos($categoryName, 'STREET BIKE PARTS > RIDING GEAR') !== FALSE)
            return 8506720012;
        if (strpos($categoryName, 'STREET BIKE PARTS > PACKS') !== FALSE)
            return 8506721012;
        if (strpos($categoryName, 'STREET BIKE PARTS > CASUAL APPAREL') !== FALSE)
            return 8506722012;
        if (strpos($categoryName, 'STREET BIKE PARTS > CHEMICALS') !== FALSE)
            return 8506723012;
        if (strpos($categoryName, 'STREET BIKE PARTS > CHEMICALS') !== FALSE)
            return 8506723012;
        if (strpos($categoryName, 'STREET BIKE PARTS > TRAILERS') !== FALSE)
            return 8506724012;
        if (strpos($categoryName, 'STREET BIKE PARTS > TOOLS') !== FALSE)
            return 8506725012;
        if (strpos($categoryName, 'ATV PARTS > TRAILERS') !== FALSE)
            return 8506726012;
        if (strpos($categoryName, 'ATV PARTS > RIDING GEAR') !== FALSE)
            return 8506717012;
        if (strpos($categoryName, 'ATV PARTS > PROTECTION') !== FALSE)
            return 8506711012;
        if (strpos($categoryName, 'ATV PARTS > TRAILERS') !== FALSE)
            return 8506712012;
        if (strpos($categoryName, 'ATV PARTS > HELMETS & ACCESSORIES > HELMETS') !== FALSE)
            return 8514794012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > JACKETS') !== FALSE)
            return 8514793012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > HOODYS & SWEATSHIRTS') !== FALSE)
            return 8514818012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > DUAL SPORT HELMETS') !== FALSE)
            return 8514646012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > OPEN FACE HELMETS') !== FALSE)
            return 8514647012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > FULL FACE HELMETS') !== FALSE)
            return 8514648012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > MODULAR HELMETS') !== FALSE)
            return 8514650012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > HALF SHELL HELMETS') !== FALSE)
            return 8514650012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > COMMUNICATION') !== FALSE)
            return 8514653012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > HELMET CASES & BAGS') !== FALSE)
            return 8514654012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > T-SHIRTS') !== FALSE)
            return 8514812012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > SWIM TRUNKS') !== FALSE)
            return 8514816012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > RAIN GEAR') !== FALSE)
            return 8514786012;
        if (strpos($categoryName, 'ATV PARTS > ATV PARTS > DRIVE > CHAINS & MASTER LINKS > CHAIN') !== FALSE)
            return 8514864012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > CHEMICALS & OILS > ENGINE OIL') !== FALSE)
            return 8514866012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > GEAR OIL') !== FALSE)
            return 8514868012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > SUSPENSION FLUID') !== FALSE)
            return 8514870012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > 2-STROKE OIL') !== FALSE)
            return 8514867012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > CLEANING SUPPLIES') !== FALSE)
            return 8514869012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > BRAKE FLUID') !== FALSE)
            return 8514873012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > AIR FILTER OIL') !== FALSE)
            return 8514871012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > GLUE-SEALANT') !== FALSE)
            return 8514865012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > HAND TOOLS') !== FALSE)
            return 8514743012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > CARB & FUEL TOOLS') !== FALSE)
            return 8514745012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > SUSPENSION TOOLS') !== FALSE)
            return 8514750012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > ELECTRICAL TOOLS') !== FALSE)
            return 8514750012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > ENGINE TOOLS') !== FALSE)
            return 8514744012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > TIRE & WHEEL TOOLS') !== FALSE)
            return 8514753012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > CHAIN TOOLS') !== FALSE)
            return 8514747012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > GRIP TOOLS') !== FALSE)
            return 8514754012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > SECURITY CABLES & LOCKS') !== FALSE)
            return 8514756012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > MOTORCYCLE COVERS') !== FALSE)
            return 8514757012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > TIE DOWNS & ANCHORS') !== FALSE)
            return 8514755012;
        return $categoryName;
    }

    private function xmlEscape($string) {
        return str_replace(array('&', '<', '>', '\'', '"', "'"), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;', '&rsquo;'), $string);
    }

    /**
     * convertToEbayFormat
     *
     * This function get the data from reporting_m model and converts into the 
     * ebay specific format like,product---> variation inside a single array
     *
     * @param (array) (data) this is the input array which we like to manupulate
     * @return (array) (final) this is the output of the function
     */

    /**
     * Function to build xml for adding new product to ebay or to revise the
     * existing product on eBay
     * @param type $product_data
     * @return type
     * @access public
     * @author Manish
     */
    private function buildXmlAndHitEbay($products, $update = false, $store_feed = false) {
		
        $count = 1;
        $store_url = base_url();
        $this->_setHeader("AddItems", FALSE);
        $uploadXML = '<?xml version="1.0" encoding="utf-8"?>';
//		$uploadXML .= '<BulkDataExchangeRequests xmlns="urn:ebay:apis:eBLBaseComponents">';

       $uploadXML .= '<BulkDataExchangeRequests>';
        $uploadXML .= '<Header>';
        $uploadXML .= '<SiteID>100</SiteID>';
        $uploadXML .= '<Version>987</Version>';
        $uploadXML .= '</Header>';
		//$products = array_reverse($products);
        foreach ($products as $product) {
            //$this->pr($products);
            //die("*");
//            $string = utf8_encode($product['product']['*Description']);

            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            $string = $this->xmlEscape($product['product']['*Description']);
			$quantity = $this->get_quantity();
			//die();
            $string = substr($string, 0, 500000);
            $UUID = md5(uniqid(rand(), true));

            $uploadXML .= '<AddFixedPriceItemRequest xmlns = "urn:ebay:apis:eBLBaseComponents">';
            $uploadXML .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $uploadXML .= '<WarningLevel>High</WarningLevel>';
            $uploadXML .= '<Version>987</Version>';
            $uploadXML .= '<MessageID>' . $product['product']['C:Manufacturer Part Number'] . '</MessageID>';
            $uploadXML .= '<Item>';

            $uploadXML .= '<SKU>' . $product['product']['C:Manufacturer Part Number'] . '</SKU>';
            $uploadXML .= '<CategoryMappingAllowed>true</CategoryMappingAllowed>';
            $uploadXML .= '<Country>US</Country>';
            $uploadXML .= '<location>US</location>';
            $uploadXML .= '<Currency>USD</Currency>';


            $uploadXML .= '<ConditionID>' . $product['product']['*ConditionID'] . '</ConditionID>';
            $uploadXML .= '<Description>' . $string . '</Description>';
            $uploadXML .= '<DispatchTimeMax>' . $product['product']['*DispatchTimeMax'] . '</DispatchTimeMax>';
            $uploadXML .= '<ListingDuration>' . $product['product']['*Duration'] . '</ListingDuration>';
            $uploadXML .= '<ListingType>FixedPriceItem</ListingType>';
            $uploadXML .= '<PaymentMethods>PayPal</PaymentMethods>';
            $paypal_email = $this->get_paypalemail();
            $uploadXML .= '<PayPalEmailAddress>' . $paypal_email . '</PayPalEmailAddress>';

            $uploadXML .= '<PictureDetails>';

			
			if(isset($product['product']['item_id'])) {
				$pic_sql = "SELECT * from partimage where partimage.part_id = " . $product['product']['item_id'];
				$query = $this->db->query($pic_sql);
				$pics = $query->result_array();
				if (is_array($pics)) {
					foreach($pics as $pic) {

						$uploadXML .= '<PictureURL>http://' . WEBSITE_HOSTNAME . '/productimages/' . $this->xmlEscape($pic['path']) . '</PictureURL>';
					}

				}
			} else if(isset($product['product']['PicURL'])) {
				$uploadXML .= '<PictureURL>' . $this->xmlEscape($product['product']['PicURL']) . '</PictureURL>';				
			}

			$uploadXML .= '</PictureDetails>';

            $uploadXML .= '<PostalCode>' . $product['product']['PostalCode'] . '</PostalCode>';
            $uploadXML .= '<PrimaryCategory>';

            $uploadXML .= '<CategoryID>' . $product['product']['EbayCategory'] . '</CategoryID>';
            //$uploadXML .= '<CategoryID>63850</CategoryID>';

            $uploadXML .= '</PrimaryCategory>';
            $uploadXML .= '<ReturnPolicy>';
            $uploadXML .= '<ReturnsAcceptedOption>' . $this->xmlEscape($product['product']['*ReturnsAcceptedOption']) . '</ReturnsAcceptedOption>';
            $uploadXML .= '<RefundOption>MoneyBack</RefundOption>';
            $uploadXML .= '<ReturnsWithinOption>' . $this->xmlEscape($product['product']['ReturnsWithinOption']) . '</ReturnsWithinOption>';
            $uploadXML .= '<Description></Description>';
            $uploadXML .= '<ShippingCostPaidByOption>' . $this->xmlEscape($product['product']['ShippingCostPaidByOption']) . '</ShippingCostPaidByOption>';
            $uploadXML .= '</ReturnPolicy>';
//            $shipping_first_key = $product['product']['shipping_options'];
//            $uploadXML .= '<ShippingDetails>';
//            $uploadXML .= '<ShippingType>';
//            $uploadXML .= 'Flat';
//            $uploadXML .= '</ShippingType>';
//
//            $uploadXML .= '<ShippingServiceOptions>';
//            $uploadXML .= '<ShippingServicePriority>1</ShippingServicePriority>';
//            $uploadXML .= '<ShippingService>UPSGround</ShippingService>';
//            $shipping_cost = $this->get_shipping_cost();
//            $uploadXML .= '<ShippingServiceCost>12.50</ShippingServiceCost>';
//            $uploadXML .= '<ShippingServiceAdditionalCost>0.00</ShippingServiceAdditionalCost>';
//            $uploadXML .= '</ShippingServiceOptions>';
//            $uploadXML .= '</ShippingDetails>';
            $check_compatibility = FALSE;
			$itemspecifics_XML = "<ItemSpecifics>";
			$itemspecifics_XML .= "<NameValueList>";
			$itemspecifics_XML .= "<Name>Brand</Name>";
			$itemspecifics_XML .= "<Value>".$product['product']['C:Brand']."</Value>";		
			$itemspecifics_XML .= "</NameValueList>";

			
            if ($product['product_variation']) {
                $product['product_variation'] = array_unique($product['product_variation'], SORT_REGULAR);
//                $this->pr($product['product_variation']);
                $check_combo = FALSE;
                $check_combo_again = FALSE;
                $check_compatibility = FALSE;
                $variation_XML = '';
                $compatibility_XML = '';
                $variation_XML .= '<Variations>';
                $variation_XML .= '<VariationSpecificsSet>';
                $compatibility_XML .= '<ItemCompatibilityList>';

                $done = 0;
                $years = array();
                foreach ($product['product_variation'] as $combination) {
//                    $combination = array_unique($combination);
//                    $this->pr($variation_key);
                    if (trim(strtolower($combination['Relationship'])) == 'variation') {
                        $variation = explode("=", $combination['RelationshipDetails']);
                        if ($done < 1) {
                            $variation_XML .= '<NameValueList>';
                            $variation_XML .= '<Name>';
                            $variation_XML .= $variation['0'];
                            $variation_XML .= '</Name>';
                        }
                        $variation_XML .= '<Value>';
                        $variation_XML .= $variation['1'];
                        $variation_XML .= '</Value>';
                        $done++;
                    } elseif (trim(strtolower($combination['Relationship'])) == 'compatibility') {
                        $check_compatibility = TRUE;
//                        $this->pr($combination['RelationshipDetails']);
//                        die("*");
                        $compatibilities = explode('|', $combination['RelationshipDetails']);
                        $compatibility_XML .= '<Compatibility>';
                        foreach ($compatibilities as $compatibility_key => $compatibility) {
                            $name_values = explode('=', $compatibility);
                            $product['product']['*StartPrice'] = $combination['*StartPrice'];
                            if (trim(strtolower($name_values[0])) == 'year') {
                                $years[] = $name_values[1];
                            }
                            $compatibility_XML .= "<NameValueList>";
                            $compatibility_XML .= "<Name>$name_values[0]</Name>";
                            $compatibility_XML .= "<Value>$name_values[1]</Value>";
                            $compatibility_XML .= "</NameValueList>";
                        }
                        $compatibility_XML .= '</Compatibility>';

						if(isset($combination['question'])&&isset($combination['answer'])) {
							
							$itemspecifics_XML .= "<NameValueList>";
                            $itemspecifics_XML .= "<Name>".$combination['question']."</Name>";
                            $itemspecifics_XML .= "<Value>".$combination['answer']."</Value>";		
							$itemspecifics_XML .= "</NameValueList>";
							
						}

						
                    } else {
//                        $this->pr($product['product_options']);
//                        die("1234");


                        if (!$check_combo) {
                            foreach ($product['product_options'] as $option_type => $option_value_array) {
                                $variation_XML .= '<NameValueList>';
                                $variation_XML .= '<Name>';
                                $variation_XML .= $option_type;
                                $variation_XML .= '</Name>';
                                foreach ($option_value_array as $option_value) {
                                    $variation_XML .= '<Value>';
                                    $variation_XML .= $option_value;
                                    $variation_XML .= '</Value>';
                                }
                                $variation_XML .= '</NameValueList>';
                            }
                            $check_combo = TRUE;
//                        $this->pr($variation_XML);
//                        die("testing xml");
                        }
                    }
                }
                if (!$check_combo) {
                    $variation_XML .= '</NameValueList>';
                }
                $variation_XML .= '</VariationSpecificsSet>';

                foreach ($product['product_variation'] as $combination) {
                    if (trim(strtolower($combination['Relationship'])) == 'variation') {
                        $variations = explode("=", $combination['RelationshipDetails']);
                        $variation_XML .= '<Variation>';
                        $product_price = $combination['*StartPrice'];
                        $variation_XML .='<StartPrice>' . $combination['*StartPrice'] . '</StartPrice>';

                        $variation_XML .='<Quantity>' . min($combination['*Quantity'], $quantity) . '</Quantity>';

                        $variation_XML .= '<VariationSpecifics>';
                        $variation_XML .= '<NameValueList>';

                        if (trim(strtolower($combination['Relationship'])) == 'variation') {
                            $variation_XML .= '<Name>' . $variations['0'] . '</Name>';
                            $variation_XML .= '<Value>' . $variations['1'] . '</Value>';
                        }

                        $variation_XML .= '</NameValueList>';
                        $variation_XML .= '</VariationSpecifics>';
                        $variation_XML .= '</Variation>';
                    } elseif (trim(strtolower($combination['Relationship'])) == 'combo') {
                        //$this->pr($product['product_options']);
                        //die('test');
                        if (!$check_combo_again) {
							
							echo "MARKUP: " . $markup;

/*							if($product['product_options']['COLOR']) {

								foreach ($product['product_options']['COLOR'] as $option_value_glove_array) {
									$variation_XML .= '<Variation>';
									$variation_XML .='<StartPrice>' . $product['product']['*StartPrice'] . '</StartPrice>';
									$variation_XML .='<Quantity>' . min($combination['*Quantity'], $quantity) . '</Quantity>';
									$variation_XML .= '<VariationSpecifics>';
									$variation_XML .= '<NameValueList>';
									$variation_XML .= '<Name>COLOR</Name>';
									$variation_XML .= '<Value>' . $option_value_glove_array . '</Value>';
									$variation_XML .= '</NameValueList>';
									$variation_XML .= '</VariationSpecifics>';
									$variation_XML .= '</Variation>';
								}

							}
*/
							if($product['product_options']['GLOVE']) {
							
								$variation_XML .= '<NameValueList>';
								$variation_XML .= '<Name>GLOVE</Name>';

								foreach ($product['product_options']['GLOVE'] as $option_value_glove_array) {
									$variation_XML .= '<Value>' . $option_value_glove_array . '</Value>';
								}

								$variation_XML .= '</NameValueList>';

							}

							if($product['product_options']['JERSEY']) {

								$variation_XML .= '<NameValueList>';
								$variation_XML .= '<Name>JERSEY</Name>';
								foreach ($product['product_options']['JERSEY'] as $option_value_jersey_array) {
									$variation_XML .= '<Value>' . $option_value_jersey_array . '</Value>';
								}
								$variation_XML .= '</NameValueList>';
							}

							if($product['product_options']['PANT']) {

								$variation_XML .= '<NameValueList>';
								$variation_XML .= '<Name>PANT</Name>';
								foreach ($product['product_options']['PANT'] as $option_value_pant_array) {
									$variation_XML .= '<Value>' . $option_value_pant_array . '</Value>';
								}
								$variation_XML .= '</NameValueList>';
                            }
                            $check_combo_again = TRUE;
                        }
                    }
                }
                $compatibility_XML .= '</ItemCompatibilityList>';
                $variation_XML .= '</Variations>';
				//echo($check_compatibility);
				//die("STUFF");
                if ($check_compatibility) {
                    $uploadXML .= $compatibility_XML;
                    $uploadXML .= '<Quantity>' . min($product['product']['*Quantity'], $quantity) . '</Quantity>';
                    $product_price = $product['product']['*StartPrice'];
                    $uploadXML .= '<StartPrice currencyID="USD">' . $product['product']['*StartPrice'] . '</StartPrice>';
                } else {
                    $uploadXML .= $variation_XML;
                }
            } else {
                $uploadXML .= '<Quantity>' . min($product['product']['*Quantity'], $quantity) . '</Quantity>';
                $product_price = $product['product']['*StartPrice'];
                $uploadXML .= '<StartPrice currencyID="USD">' . $product['product']['*StartPrice'] . '</StartPrice>';
            }

			$itemspecifics_XML .= "</ItemSpecifics>";
			$uploadXML .= $itemspecifics_XML;

            $uploadXML .= '<ShippingDetails>';
            $uploadXML .= '<ShippingType>';
            $uploadXML .= 'Flat';
            $uploadXML .= '</ShippingType>';

            $uploadXML .= '<ShippingServiceOptions>';
            $uploadXML .= '<ShippingServicePriority>1</ShippingServicePriority>';
            $uploadXML .= '<ShippingService>UPSGround</ShippingService>';
            $shipping_cost = $this->get_shipping_cost($product_price);
            $uploadXML .= '<ShippingServiceCost>' . $shipping_cost . '</ShippingServiceCost>';
            $uploadXML .= '<ShippingServiceAdditionalCost>0.00</ShippingServiceAdditionalCost>';
            $uploadXML .= '</ShippingServiceOptions>';
            $uploadXML .= '</ShippingDetails>';
            $title = $this->xmlEscape($product['product']['*Title']);
            if ($check_compatibility) {
                $start_year = min($years);
                $end_year = max($years);
                if ($start_year != $end_year) {
                    $uploadXML .= '<Title>' . substr(strip_tags($title . ' ' . $start_year . '-' . $end_year), 0, 78) . '</Title>';
                } else {
                    $uploadXML .= '<Title>' . substr(strip_tags($title . ' ' . $start_year), 0, 78) . '</Title>';
                }
            } else {
                $uploadXML .= '<Title>' . substr(strip_tags($title), 0, 78) . '</Title>';
            }
            $uploadXML .= '</Item>';
            $uploadXML .= '</AddFixedPriceItemRequest>
';

            $count++;
            //if ($count > 30)
                //break;
        }


        $uploadXML .= '</BulkDataExchangeRequests>';
		/*
		$uploadXML = '<?xml version="1.0" encoding="utf-8"?><BulkDataExchangeRequests><Header><SiteID>100</SiteID><Version>987</Version></Header><AddFixedPriceItemRequest xmlns = "urn:ebay:apis:eBLBaseComponents"><ErrorLanguage>en_US</ErrorLanguage><WarningLevel>High</WarningLevel><Version>987</Version><MessageID>2171900145</MessageID><Item><SKU>2171900145</SKU><CategoryMappingAllowed>true</CategoryMappingAllowed><Country>US</Country><location>US</location><Currency>USD</Currency><ConditionID>1000</ConditionID><Description>ACERBIS DIRT BIKE STANDARD PLASTIC KITS&lt;br /&gt;&lt;br /&gt;&amp;bull; Full kit includes front number plate and universal fork covers&lt;br /&gt;&amp;bull; Standard kit includes front and rear fender side panels and radiator scoops&lt;br /&gt;&amp;bull; All kits have the original factory shape of the according model&lt;br /&gt;&amp;bull; Colors marked &amp;ldquo;Original&amp;rdquo; correspond with the original factory colors of the model and year</Description><DispatchTimeMax>2</DispatchTimeMax><ListingDuration>GTC</ListingDuration><ListingType>FixedPriceItem</ListingType><PaymentMethods>PayPal</PaymentMethods><PayPalEmailAddress>dbmathewes@gmail.com</PayPalEmailAddress><PictureDetails><PictureURL>http://dbmathewes.powersporttechnologies.com/productimages/63694-ACERBIS-DIRT-BIKE-STANDARD-PLASTIC-KITS.png</PictureURL></PictureDetails><PostalCode>28217</PostalCode><PrimaryCategory><CategoryID>35560</CategoryID></PrimaryCategory><ReturnPolicy><ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption><RefundOption>MoneyBack</RefundOption><ReturnsWithinOption>Days_30</ReturnsWithinOption><Description></Description><ShippingCostPaidByOption>Buyer</ShippingCostPaidByOption></ReturnPolicy><Variations><VariationSpecificsSet><NameValueList><Name>COLOR</Name><Value>BLACK</Value><Value>BLUE</Value><Value>BLUE ORIGINAL `13</Value><Value>BLUE ‘14-15</Value><Value>ORIGINAL ‘05</Value><Value>ORIGINAL ‘08</Value><Value>ORIGINAL ‘09</Value><Value>ORIGINAL ‘10</Value><Value>ORIGINAL ‘11</Value><Value>ORIGINAL ‘11/’12</Value><Value>ORIGINAL ‘12</Value><Value>ORIGINAL ‘13</Value><Value>ORIGINAL ‘14</Value><Value>ORIGINAL ‘14/15</Value><Value>ORIGINAL ‘15</Value><Value>WHITE</Value><Value>WHITE ‘14-15</Value><Value>YZ BLUE</Value></NameValueList></VariationSpecificsSet><Variation><StartPrice>111.573</StartPrice><Quantity>1</Quantity><VariationSpecifics><NameValueList><Name>COLOR</Name><Value>BLACK</Value><Value>BLUE</Value><Value>BLUE ORIGINAL `13</Value><Value>BLUE ‘14-15</Value><Value>ORIGINAL ‘05</Value><Value>ORIGINAL ‘08</Value><Value>ORIGINAL ‘09</Value><Value>ORIGINAL ‘10</Value><Value>ORIGINAL ‘11</Value><Value>ORIGINAL ‘11/’12</Value><Value>ORIGINAL ‘12</Value><Value>ORIGINAL ‘13</Value><Value>ORIGINAL ‘14</Value><Value>ORIGINAL ‘14/15</Value><Value>ORIGINAL ‘15</Value><Value>WHITE</Value><Value>WHITE ‘14-15</Value><Value>YZ BLUE</Value></NameValueList></VariationSpecifics></Variation></Variations><Quantity>1</Quantity><StartPrice currencyID="USD">111.573</StartPrice><ShippingDetails><ShippingType>Flat</ShippingType><ShippingServiceOptions><ShippingServicePriority>1</ShippingServicePriority><ShippingService>UPSGround</ShippingService><ShippingServiceCost>0</ShippingServiceCost><ShippingServiceAdditionalCost>0.00</ShippingServiceAdditionalCost></ShippingServiceOptions></ShippingDetails><Title>ACERBIS DIRT BIKE STANDARD PLASTIC KITS</Title></Item></AddFixedPriceItemRequest></BulkDataExchangeRequests>';
*/

		$unicode = ["\x01", "\x00", "\x02"];
		$uploadXML = str_replace($unicode, "", str_replace("&B", "&amp;B", str_replace("&G", "&amp;G", $uploadXML)));
		//$uploadXML = str_replace($unicode, "", $uploadXML);

  
        if ($store_feed == true) {
            $file_path = STORE_DIRECTORY . '/ebayFeeds/ebayfeed_un.xml';
            if (file_exists($file_path)) {
                unlink($file_path);
            }
//               print_r($doc);
//            die('-------');
//            $myfile = fopen($file_path, "w");
            file_put_contents($file_path, $uploadXML, LOCK_EX);
            $xml = file_get_contents($file_path, LOCK_EX);
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = FALSE;
            $doc->loadXML($xml);
            $doc->formatOutput = TRUE;
//Save XML as a file
            $file = STORE_DIRECTORY . '/ebayFeeds/ebayfeed.xml';
            if (file_exists($file)) {
                unlink($file);
            }
            $doc->save($file);
        }
		
		$this->pr($uploadXML);
		echo "***STOP***<br>";
		//die();
		$this->sendBulkXML($uploadXML, "AddFixedPriceItem");		
		
	}
	
	
	public function endAll($x, $y) {
        $this->_setHeader("EndItems", FALSE);
        $uploadXML = '<?xml version="1.0" encoding="utf-8"?>';

       $uploadXML .= '<BulkDataExchangeRequests>';
        $uploadXML .= '<Header>';
        $uploadXML .= '<SiteID>0</SiteID>';
        $uploadXML .= '<Version>849</Version>';
        $uploadXML .= '</Header>';
		//110206538682
		for($x=110212343213;$x<=110212366458;$x++) {
				$uploadXML .= '<EndFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <EndingReason>Incorrect</EndingReason>
		  <ItemID>'.$x.'</ItemID>
		<Version>849</Version>
		</EndFixedPriceItemRequest>';
		}

		$this->sendBulkXML($uploadXML, "EndFixedPriceItem");		
		
	}

	
	private function sendBulkXML($xml, $job_type) {
		
		
		require '../vendor/autoload.php';
		
		/**
		 * Include some utility functions.
		 */
		//require __DIR__.'/utils.php';
		/**
		 * Include the configuration values.
		 *
		 * Ensure that you have edited the configuration.php file
		 * to include your application keys.
		 */
		//$config = require __DIR__.'/../configuration.php';
		/**
		 * The namespaces provided by the SDK.
		 */
		/**
		 * Create the service objects.
		 *
		 * This example uses both the File Transfer and Bulk Data Exchange services.
		 */
		 
		 
		 
		$sdk = new Sdk($this->cred['Setting']);
		$exchangeService = $sdk->createBulkDataExchange();
		$transferService = $sdk->createFileTransfer();
		$merchantData = new MerchantData\MerchantData();
		/**
		 * Before anything can be uploaded a request needs to be made to obtain a job ID and file reference ID.
		 * eBay needs to know the job type and a way to identify it.
		 */
		if($this->clear_jobs()) {			
			$createUploadJobRequest = new BulkDataExchange\Types\CreateUploadJobRequest();
			$createUploadJobRequest->uploadJobType = $job_type;
			$createUploadJobRequest->UUID = uniqid();
			/**
			 * Send the request.
			 */
			print('Requesting job Id from eBay...');
			$createUploadJobResponse = $exchangeService->createUploadJob($createUploadJobRequest);
			print("Done\n");
			/**
			 * Output the result of calling the service operation.
			 */
			if (isset($createUploadJobResponse->errorMessage)) {
				foreach ($createUploadJobResponse->errorMessage->error as $error) {
					printf(
						"%s: %s\n\n",
						$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
						$error->message
					);
				}
			}
			if ($createUploadJobResponse->ack !== 'Failure') {
				printf(
					"JobId [%s] FileReferenceId [%s]\n",
					$createUploadJobResponse->jobId,
					$createUploadJobResponse->fileReferenceId
				);
				$job['jobId'] = $createUploadJobResponse->jobId;
				$job['fileReferenceId'] = $createUploadJobResponse->fileReferenceId;
			}
		
			/**
			 * Pass the required values to the File Transfer service.
			 */
			$uploadFileRequest = new FileTransfer\Types\UploadFileRequest();
			$uploadFileRequest->fileReferenceId = $job['fileReferenceId'];
			$uploadFileRequest->taskReferenceId = $job['jobId'];
			$uploadFileRequest->fileFormat = 'gzip';
			//$payload = buildPayload();
			/**
			 * Convert our payload to XML.
			 */
			//$payloadXml = $payload->toRequestXml();
			$payloadXml = $xml;
			/**
			 * GZip and attach the XML payload.
			 */
			$uploadFileRequest->attachment(gzencode($payloadXml, 9));
			/**
			 * Now upload the file.
			 */
			print('Uploading fixed price item requests...');
			$uploadFileResponse = $transferService->uploadFile($uploadFileRequest);
			print("Done\n");
			if (isset($uploadFileResponse->errorMessage)) {
				foreach ($uploadFileResponse->errorMessage->error as $error) {
					printf(
						"%s: %s\n\n",
						$error->severity === FileTransfer\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
						$error->message
					);
				}
			}
			if ($uploadFileResponse->ack !== 'Failure') {
				/**
				 * Once the file has uploaded we can tell eBay to start processing it.
				 */
				$startUploadJobRequest = new BulkDataExchange\Types\StartUploadJobRequest();
				$startUploadJobRequest->jobId = $job['jobId'];
				print('Request processing of fixed price items...');
				$startUploadJobResponse = $exchangeService->startUploadJob($startUploadJobRequest);
				print("Done\n");
				if (isset($startUploadJobResponse->errorMessage)) {
					foreach ($startUploadJobResponse->errorMessage->error as $error) {
						printf(
							"%s: %s\n\n",
							$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
							$error->message
						);
					}
				}
				if ($startUploadJobResponse->ack !== 'Failure') {
					/**
					 * Now wait for the job to be processed.
					 */
					$getJobStatusRequest = new BulkDataExchange\Types\GetJobStatusRequest();
					$getJobStatusRequest->jobId = $job['jobId'];
					$done = false;
					while (!$done) {
						$getJobStatusResponse = $exchangeService->getJobStatus($getJobStatusRequest);
						if (isset($getJobStatusResponse->errorMessage)) {
							foreach ($getJobStatusResponse->errorMessage->error as $error) {
								printf(
									"%s: %s\n\n",
									$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
									$error->message
								);
							}
						}
						if ($getJobStatusResponse->ack !== 'Failure') {
							printf("Status is %s\n", $getJobStatusResponse->jobProfile[0]->jobStatus);
							switch ($getJobStatusResponse->jobProfile[0]->jobStatus) {
								case BulkDataExchange\Enums\JobStatus::C_COMPLETED:
									$downloadFileReferenceId = $getJobStatusResponse->jobProfile[0]->fileReferenceId;
									$done = true;
									break;
								case BulkDataExchange\Enums\JobStatus::C_ABORTED:
								case BulkDataExchange\Enums\JobStatus::C_FAILED:
									$done = true;
									break;
								default:
									sleep(5);
									break;
							}
						} else {
							$done = true;
						}
					}
					if (isset($downloadFileReferenceId)) {
						$downloadFileRequest = new FileTransfer\Types\DownloadFileRequest();
						$downloadFileRequest->fileReferenceId = $downloadFileReferenceId;
						$downloadFileRequest->taskReferenceId = $job['jobId'];
						print('Downloading fixed price item responses...');
						$downloadFileResponse = $transferService->downloadFile($downloadFileRequest);
						print("Done\n");
						if (isset($downloadFileResponse->errorMessage)) {
							foreach ($downloadFileResponse->errorMessage->error as $error) {
								printf(
									"%s: %s\n\n",
									$error->severity === FileTransfer\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
									$error->message
								);
							}
						}
						if ($downloadFileResponse->ack !== 'Failure') {
							/**
							 * Check that the response has an attachment.
							 */
							if ($downloadFileResponse->hasAttachment()) {
								$attachment = $downloadFileResponse->attachment();
								/**
								 * Save the attachment to file system's temporary directory.
								 */
								$filename = $this->saveAttachment($attachment['data']);
								if ($filename !== false && class_exists(ZipArchive)) {
									$xml = $this->unZipArchive($filename);
									if ($xml !== false) {
										$responses = $merchantData->addFixedPriceItem($xml);
										foreach ($responses as $response) {
											if (isset($response->Errors)) {
												foreach ($response->Errors as $error) {
													printf(
														"%s: %s\n%s\n\n",
														$error->SeverityCode === MerchantData\Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
														$error->ShortMessage,
														$error->LongMessage
													);
												}
											}
											if ($response->Ack !== 'Failure') {
												printf(
													"The item was listed to the eBay Sandbox with the Item number %s\n",
													$response->ItemID
												);
											}
										}
									}
								}
							} else {
								print("Unable to locate attachment\n\n");
							}
						}
					}
				}
			}
		} 

		
	}
	
	private function clear_jobs() {
		
		echo "clearing jobs...";
		
		$service = new Services\BulkDataExchangeService($this->cred['Setting']);

		/**
		 * Create the request object.
		 */
		$request = new Types\GetJobsRequest();
		/**
		 * Send the request.
		 */
		$response = $service->getJobs($request);
		/**
		 * Output the result of calling the service operation.
		 */
		 //var_dump($response);
		if (isset($response->errorMessage)) {
			foreach ($response->errorMessage->error as $error) {
				printf(
					"%s: %s\n\n",
					$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
					$error->message
				);
			}
		}
		if ($response->ack !== 'Failure') {
			/**
			 * Just display the first 3 jobs from the response.
			 */
			$upTo = min(count($response->jobProfile), 3);
			$upTo = 0;
			if(isset($response->jobProfile)) {
				foreach($response->jobProfile as $job) {		
					printf(
						"ID: %s\nType: %s\nStatus: %s\nInput File Reference ID: %s\nFile Reference ID: %s\nPercent Complete: %s\nCreated: %s\nCompleted: %s\n\n",
						$job->jobId,
						$job->jobType,
						$job->jobStatus,
						$job->inputFileReferenceId,
						$job->fileReferenceId,
						$job->percentComplete,
						$job->creationTime->format('H:i (\G\M\T) \o\n l jS F Y'),
						isset($job->completionTime) ? $job->completionTime->format('H:i (\G\M\T) \o\n l jS F Y') : ''
					);
					if($job->jobStatus!="Aborted"&&$job->jobStatus!="Completed"&&$job->jobStatus!="Failed") {
						
						$request = new Types\AbortJobRequest();
						$request->jobId = $job->jobId;
						/**
						 * Send the request.
						 */
						$response = $service->abortJob($request);
						if (isset($response->errorMessage)) {
							foreach ($response->errorMessage->error as $error) {
								printf(
									"%s: %s\n\n",
									$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
									$error->message
								);
							}
						}
						if ($response->ack !== 'Failure') {
							echo "Job aborted\n";
						}
					}
				}
			}
			return true;
		}
		return true;
	}
	
	
	public function updateEbayTracking($order_id, $ship, $carrier) {

		$store_url = base_url();
        $this->_setHeader("CompleteSale", FALSE);
			error_reporting(E_ALL);
		$xml = '<?xml version="1.0" encoding="utf-8"?>
<CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <RequesterCredentials>
			<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>
		  </RequesterCredentials>
  <OrderID>'.$order_id.'</OrderID>
  <Shipment> 
    <ShipmentTrackingDetails>
      <ShipmentTrackingNumber>'.$ship.'</ShipmentTrackingNumber>
      <ShippingCarrierUsed>'.$carrier.'</ShippingCarrierUsed>
    </ShipmentTrackingDetails>
  </Shipment>
  <TransactionID> string </TransactionID>
  <Version>849</Version>
  <WarningLevel>High</WarningLevel>
</CompleteSaleRequest>';
		//echo $xml;
        $response = json_decode(json_encode((array) simplexml_load_string($this->call($xml))), 1);
		if($response['Ack']=="Success") echo "Tracking info successfully sent to eBay."; else echo "An unexpected error occurred.";

		
	}
	

	public function getOrders() {

		$store_url = base_url();
        $this->_setHeader("GetOrders", FALSE);
		
			error_reporting(E_ALL);
			$xml='<?xml version="1.0" encoding="utf-8"?>
				<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				  <RequesterCredentials>
					<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>
				  </RequesterCredentials>
				<NumberOfDays>30</NumberOfDays>
				</GetOrdersRequest>';	
				

        $response = json_decode(json_encode((array) simplexml_load_string($this->call($xml))), 1);
		//echo $xml;
		print_r($response);
		echo "<br><br><br>****************<br><br>";
		foreach($response['OrderArray']['Order'] as $ebayOrder) {
						
            $where = array('ebay_order_id' => $ebayOrder['OrderID']);
			$results = $this->selectRecords('order', $where);
			if($results) {
				// to do: update ordersc
				echo "Order" . $results[0]['id'] . " updated.<br/>";				
			} else {

				$name = explode(" ", $ebayOrder['ShippingAddress']['Name']);
				$contact_array = array('first_name' => $name[0], 
				'last_name' => $name[1], 
				'street_address' => $ebayOrder['ShippingAddress']['Street1'], 
				'address_2' => implode(" ", $ebayOrder['ShippingAddress']['Street2']), 
				'city' => $ebayOrder['ShippingAddress']['CityName'], 
				'state' => $ebayOrder['ShippingAddress']['StateOrProvince'], 
				'zip' => $ebayOrder['ShippingAddress']['PostalCode'], 
				'country' => $ebayOrder['ShippingAddress']['Country']);
				$contactId = $this->createRecord('contact', $contact_array, FALSE);
				echo "<br>Contact " . $contactId . " created.<br/>";

				$order = array('sales_price' => $ebayOrder['Subtotal'], 
							'contact_id' => $contactId.'',
							'shipping_id' => $contactId.'',
							'shipping' => $ebayOrder['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'], 
							'tax' => '0.00', 
							'order_date' => time($ebayOrder['CreatedTime']), 
							'user_id' => '0', 
							'created_by' => '1', 
							'source' => 'eBay', 
							'ebay_order_id' => $ebayOrder['OrderID']);
				$orderId = $this->createRecord('order', $order, FALSE);
				echo "<br>Order" . $orderId . " created.<br/>";
				$transaction_array = array('order_id' => $orderId, 
							'amount' => $ebayOrder['AmountPaid'],
							'transaction_date' => strtotime($ebayOrder['CreatedTime']));
				$this->createRecord('order_transaction', $transaction_array, FALSE);
				echo "<br>Order transaction for " . $orderId . " created.<br/>";
				$status_array = array('order_id' => $orderId, 
							'status' => 'Processing',
							'datetime' => time($ebayOrder['CreatedTime']));
				//			'status' => $ebayOrder['OrderStatus'],
				$this->createRecord('order_status', $status_array, FALSE);
				echo "<br>Order status for " . $orderId . " created.<br/>";
				
				if(isset($ebayOrder['TransactionArray']['Transaction']['Item']))
					$transactions = [0 => $ebayOrder['TransactionArray']['Transaction']];
				else 
					$transactions = $ebayOrder['TransactionArray']['Transaction'];
				
				foreach( $transactions as $product) {
					
					$this->db->select('partpartnumber.part_id, partnumber.partnumber as SKU');
					
		            $where = array('manufacturer_part_number' => $product['Item']['SKU']);
					$this->db->join('partnumber', 'partvariation.partnumber_id = partnumber.partnumber_id');
					$this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
					$SKUresults = $this->selectRecords('partvariation', $where);
					if($SKUresults) {
						$part_id = $SKUresults[0]['part_id'];
						$real_SKU = $SKUresults[0]['SKU'];
						echo "SKU: ".$real_SKU;
						//die();

						// Check whether this order_product already exists (eBay allows duplicates but our system does not) 
						// If already exists, add to quantity of existing order_product
						$where = array('order_id' => $orderId, 'product_sku' => $real_SKU);
						$order_product_exists = $this->selectRecords('order_product', $where);
						if($order_product_exists) {
							$update_array = ['qty' => $order_product_exists[0]['qty']+$product['QuantityPurchased']];
							$this->updateRecord('order_product', $update_array, $where, FALSE);						
							echo "<br>Order product updated: $orderId - $real_SKU";
						} else {
							$product_array = array('order_id' => $orderId, 
										'product_sku' => $real_SKU, 
										'price' => $product['TransactionPrice'], 
										'qty' => $product['QuantityPurchased'],
										'part_id' => $part_id);
							$this->createRecord('order_product', $product_array, FALSE);
							echo "<br>Order product added: $orderId - $real_SKU";
						}
						

						
					} else { echo "<br>Product not found for ".$product['Item']['SKU']; }
				}
				
			}
			//$orderId = $this->db->insert_id();
			//$orderStatus = array('order_id' => $orderId, 'status' => 'Pending', 'datetime' => time(), 'userId' => 1, 'notes' => 'Admin Order');
			//$this->createRecord('order_status', $orderStatus, FALSE);
			//return $orderId;
			

		}
		
	}
	
	public function setNotifications() {

		$store_url = base_url();
        $this->_setHeader("SetNotificationPreferences", FALSE);
		
			error_reporting(E_ALL);
			$xml='<?xml version="1.0" encoding="utf-8"?>
		<SetNotificationPreferencesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <RequesterCredentials>
			<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>
		  </RequesterCredentials>
		  <ApplicationDeliveryPreferences>
			<AlertEmail>mailto:dbmathewes@gmail.com</AlertEmail>
			<AlertEnable>Enable</AlertEnable>
			<ApplicationEnable>Enable</ApplicationEnable>
			<ApplicationURL>'.site_url('incoming/ebay_notifications').'</ApplicationURL>
			<DeviceType>Platform</DeviceType>
			<DeliveryURLDetails>
			  <DeliveryURL>'.site_url('incoming/ebay_notifications').'</DeliveryURL>
			  <DeliveryURLName>Set Notifications</DeliveryURLName>
			  <Status>Enable</Status>
			</DeliveryURLDetails>
		  </ApplicationDeliveryPreferences>
		  <UserDeliveryPreferenceArray>
			<NotificationEnable>
			  <EventType>FixedPriceTransaction</EventType>
			  <EventEnable>Enable</EventEnable>
			</NotificationEnable>
			<NotificationEnable>
			  <EventType>AskSellerQuestion</EventType>
			  <EventEnable>Enable</EventEnable>
			</NotificationEnable>
		  </UserDeliveryPreferenceArray>
		</SetNotificationPreferencesRequest>';	

        $response = json_decode(json_encode((array) simplexml_load_string($this->call($xml))), 1);
		print_r($response);

		
	}

	public function getNotifications() {

		$store_url = base_url();
        $this->_setHeader("GetNotificationPreferences", FALSE);
		
			error_reporting(E_ALL);
			$xml='<?xml version="1.0" encoding="utf-8"?>
			<GetNotificationPreferencesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <RequesterCredentials>
			<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>
		  </RequesterCredentials>
  <PreferenceLevel>Application</PreferenceLevel>
</GetNotificationPreferencesRequest>';	

        $response = json_decode(json_encode((array) simplexml_load_string($this->call($xml))), 1);
		
		print_r($response);

		
	}

	
    private function buildUpateXmlAndHitEbay($products, $update = false, $store_feed = false) {

        $count = 1;
        $store_url = base_url();
        $this->_setHeader("ReviseItems", FALSE);
        $uploadXML = '<?xml version="1.0" encoding="utf-8"?>';
        $uploadXML .= '<BulkDataExchangeRequests>';
        $uploadXML .= '<Header>';
        $uploadXML .= '<SiteID>100</SiteID>';
        $uploadXML .= '<Version>663</Version>';
        $uploadXML .= '</Header>';



//        if (!$update) {
//            $uploadXML .= '<AddFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
//        } else {
//        $uploadXML .= '<ReviseFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
//        }
//        $uploadXML .= '<ErrorLanguage>en_US</ErrorLanguage>';
//        $uploadXML .= '<WarningLevel>High</WarningLevel>';
//        $uploadXML .= '<RequesterCredentials>';
//        $uploadXML .= '<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>';
//        $uploadXML .= '</RequesterCredentials>';

        foreach ($products as $product) {
//            $this->pr($product);
//            die("*");
            $part_number = $product['product']['C:Manufacturer Part Number'];
//            $this->pr($part_number);
            $sql = 'SELECT ebay_id FROM ebay_ids WHERE part_number =' . "'$part_number'";
            $query = $this->db->query($sql);
            $results = $query->result_array();
//            $this->pr($results);die("*");
            $string = utf8_encode($product['product']['*Description']);
            $string = $this->xmlEscape($product['product']['*Description']);

            $string = substr($string, 0, 500000);
            $UUID = md5(uniqid(rand(), true));

            $uploadXML .= '<ReviseFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $uploadXML .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $uploadXML .= '<WarningLevel>High</WarningLevel>';
            $uploadXML .= '<Version>663</Version>';
            $uploadXML .= '<MessageID>' . $product['product']['C:Manufacturer Part Number'] . '</MessageID>';
            $uploadXML .= '<Item>';
            $uploadXML .= '<ItemID>' . $results[0]['ebay_id'] . '</ItemID>';
            $uploadXML .= '<SKU>' . $product['product']['C:Manufacturer Part Number'] . '</SKU>';
            $uploadXML .= '<CategoryMappingAllowed>true</CategoryMappingAllowed>';
            $uploadXML .= '<Country>US</Country>';
            $uploadXML .= '<location>US</location>';
            $uploadXML .= '<Currency>USD</Currency>';


            $uploadXML .= '<ConditionID>' . $product['product']['*ConditionID'] . '</ConditionID>';
            $uploadXML .= '<Description>' . $string . '</Description>';
            $uploadXML .= '<DispatchTimeMax>' . $product['product']['*DispatchTimeMax'] . '</DispatchTimeMax>';
            $uploadXML .= '<ListingDuration>' . $product['product']['*Duration'] . '</ListingDuration>';
            $uploadXML .= '<ListingType>FixedPriceItem</ListingType>';
            $uploadXML .= '<PaymentMethods>PayPal</PaymentMethods>';
            $uploadXML .= '<PayPalEmailAddress>pushpender.techmarbles@gmail.com</PayPalEmailAddress>';

            $uploadXML .= '<PictureDetails>';

            $uploadXML .= '<PictureURL>' . $product['product']['PicURL'] . '</PictureURL>';
            $uploadXML .= '</PictureDetails>';

            $uploadXML .= '<PostalCode>' . $product['product']['PostalCode'] . '</PostalCode>';
            $uploadXML .= '<PrimaryCategory>';

            $uploadXML .= '<CategoryID>' . $product['product']['EbayCategory'] . '</CategoryID>';
            $uploadXML .= '</PrimaryCategory>';
            $uploadXML .= '<ReturnPolicy>';
            $uploadXML .= '<ReturnsAcceptedOption>' . $product['product']['*ReturnsAcceptedOption'] . '</ReturnsAcceptedOption>';
            $uploadXML .= '<RefundOption>MoneyBack</RefundOption>';
            $uploadXML .= '<ReturnsWithinOption>' . $product['product']['ReturnsWithinOption'] . '</ReturnsWithinOption>';
            $uploadXML .= '<Description>TEST</Description>';
            $uploadXML .= '<ShippingCostPaidByOption>' . $product['product']['ShippingCostPaidByOption'] . '</ShippingCostPaidByOption>';
            $uploadXML .= '</ReturnPolicy>';
//            $shipping_first_key = $product['product']['shipping_options'];
            $uploadXML .= '<ShippingDetails>';
            $uploadXML .= '<ShippingType>';
            $uploadXML .= 'Flat';
            $uploadXML .= '</ShippingType>';

            $uploadXML .= '<ShippingServiceOptions>';
            $uploadXML .= '<ShippingServicePriority>1</ShippingServicePriority>';
            $uploadXML .= '<ShippingService>UPSGround</ShippingService>';
            $uploadXML .= '<ShippingServiceCost>14.50</ShippingServiceCost>';
            $uploadXML .= '<ShippingServiceAdditionalCost>5.00</ShippingServiceAdditionalCost>';
            $uploadXML .= '</ShippingServiceOptions>';
            $uploadXML .= '</ShippingDetails>';
            $check_compatibility = FALSE;
            if ($product['product_variation']) {
                $product['product_variation'] = array_unique($product['product_variation'], SORT_REGULAR);
//                $this->pr($product['product_variation']);
                $check_combo = FALSE;
                $check_combo_again = FALSE;
                $check_compatibility = FALSE;
                $variation_XML = '';
                $compatibility_XML = '';
                $variation_XML .= '<Variations>';
                $variation_XML .= '<VariationSpecificsSet>';
//                $variation_XML .= '<NameValueList>';
                $compatibility_XML .= '<ItemCompatibilityList>';

                $done = 0;
                $years = array();
                foreach ($product['product_variation'] as $combination) {
//                    $combination = array_unique($combination);
//                    $this->pr($variation_key);
                    if (trim(strtolower($combination['Relationship'])) == 'variation') {
                        $variation = explode("=", $combination['RelationshipDetails']);
                        if ($done < 1) {
                            $variation_XML .= '<NameValueList>';
                            $variation_XML .= '<Name>';
                            $variation_XML .= $variation['0'];
                            $variation_XML .= '</Name>';
                        }
                        $variation_XML .= '<Value>';
                        $variation_XML .= $variation['1'];
                        $variation_XML .= '</Value>';
                        $done++;
                    } elseif (trim(strtolower($combination['Relationship'])) == 'compatibility') {
                        $check_compatibility = TRUE;
//                        $this->pr($combination['RelationshipDetails']);
//                        die("*");
                        $compatibilities = explode('|', $combination['RelationshipDetails']);
                        $compatibility_XML .= '<Compatibility>';
                        foreach ($compatibilities as $compatibility_key => $compatibility) {
                            $name_values = explode('=', $compatibility);
                            $product['product']['*StartPrice'] = $combination['*StartPrice'];
                            if (trim(strtolower($name_values[0])) == 'year') {
                                $years[] = $name_values[1];
                            }
                            $compatibility_XML .= "<NameValueList>";
                            $compatibility_XML .= "<Name>$name_values[0]</Name>";
                            $compatibility_XML .= "<Value>$name_values[1]</Value>";
                            $compatibility_XML .= "</NameValueList>";
                        }
                        $compatibility_XML .= '</Compatibility>';
                    } else {
//                        $this->pr($product['product_options']);
//                        die("1234");
                        if (!$check_combo) {
                            foreach ($product['product_options'] as $option_type => $option_value_array) {
                                $variation_XML .= '<NameValueList>';
                                $variation_XML .= '<Name>';
                                $variation_XML .= $option_type;
                                $variation_XML .= '</Name>';
                                foreach ($option_value_array as $option_value) {
                                    $variation_XML .= '<Value>';
                                    $variation_XML .= $option_value;
                                    $variation_XML .= '</Value>';
                                }
                                $variation_XML .= '</NameValueList>';
                            }
                            $check_combo = TRUE;
//                        $this->pr($variation_XML);
//                        die("testing xml");
                        }
                    }
                }
                if (!$check_combo) {
                    $variation_XML .= '</NameValueList>';
                }
                $variation_XML .= '</VariationSpecificsSet>';

                foreach ($product['product_variation'] as $combination) {
                    if (trim(strtolower($combination['Relationship'])) == 'variation') {
                        $variations = explode("=", $combination['RelationshipDetails']);
                        $variation_XML .= '<Variation>';

                        $variation_XML .='<StartPrice>' . $combination['*StartPrice'] . '</StartPrice>';

                        $variation_XML .='<Quantity>' . $combination['*Quantity'] . '</Quantity>';

                        $variation_XML .= '<VariationSpecifics>';
                        $variation_XML .= '<NameValueList>';

                        if (trim(strtolower($combination['Relationship'])) == 'variation') {
                            $variation_XML .= '<Name>' . $variations['0'] . '</Name>';
                            $variation_XML .= '<Value>' . $variations['1'] . '</Value>';
                        }

                        $variation_XML .= '</NameValueList>';
                        $variation_XML .= '</VariationSpecifics>';
                        $variation_XML .= '</Variation>';
                    } elseif (trim(strtolower($combination['Relationship'])) == 'combo') {
                        //$this->pr($product['product_options']);
//                        die('test');
                        if (!$check_combo_again) {
                            $variation_XML .= '<Variation>';
                            $variation_XML .='<StartPrice>' . $product['product']['*StartPrice'] . '</StartPrice>';
                            $variation_XML .='<Quantity>' . $product['product']['*Quantity'] . '</Quantity>';
                            $variation_XML .= '<VariationSpecifics>';
                            $variation_XML .= '<NameValueList>';
                            $variation_XML .= '<Name>GLOVE</Name>';

                            foreach ($product['product_options']['GLOVE'] as $option_value_glove_array) {
                                $variation_XML .= '<Value>' . $option_value_glove_array . '</Value>';
                            }

                            $variation_XML .= '</NameValueList>';
                            $variation_XML .= '<NameValueList>';
                            $variation_XML .= '<Name>JERSEY</Name>';
                            foreach ($product['product_options']['JERSEY'] as $option_value_jersey_array) {
                                $variation_XML .= '<Value>' . $option_value_jersey_array . '</Value>';
                            }
                            $variation_XML .= '</NameValueList>';
                            $variation_XML .= '<NameValueList>';
                            $variation_XML .= '<Name>PANT</Name>';
                            foreach ($product['product_options']['PANT'] as $option_value_pant_array) {
                                $variation_XML .= '<Value>' . $option_value_pant_array . '</Value>';
                            }
                            $variation_XML .= '</NameValueList>';
                            $variation_XML .= '</VariationSpecifics>';
                            $variation_XML .= '</Variation>';
                            $check_combo_again = TRUE;
                        }
                    }
                }
                $compatibility_XML .= '</ItemCompatibilityList>';
                $variation_XML .= '</Variations>';
                if ($check_compatibility) {
                    $uploadXML .= $compatibility_XML;
                    $uploadXML .= '<Quantity>' . $product['product']['*Quantity'] . '</Quantity>';
                    $uploadXML .= '<StartPrice currencyID="USD">' . $product['product']['*StartPrice'] . '</StartPrice>';
                } else {
                    $uploadXML .= $variation_XML;
                }
            } else {
                $uploadXML .= '<Quantity>' . $product['product']['*Quantity'] . '</Quantity>';
                $uploadXML .= '<StartPrice currencyID="USD">' . $product['product']['*StartPrice'] . '</StartPrice>';
            }
			

//            $title = utf8_encode($product['product']['*Title']);
            $title = $this->xmlEscape($product['product']['*Title']);
            if ($check_compatibility) {
//                $this->pr($start_year);die("1234");
                $start_year = min($years);
                $end_year = max($years);
                if ($start_year != $end_year) {
                    $uploadXML .= '<Title>' . substr(strip_tags($title . ' ' . $start_year . '-' . $end_year), 0, 78) . '</Title>';
                } else {
                    $uploadXML .= '<Title>' . substr(strip_tags($title . ' ' . $start_year), 0, 78) . '</Title>';
                }
            } else {
                $uploadXML .= '<Title>' . substr(strip_tags($title), 0, 78) . '</Title>';
            }
            $uploadXML .= '</Item>';
            $uploadXML .= '</ReviseFixedPriceItemRequest>';

            $count++;
//            if ($count > 2)
//                break;
        }

        $uploadXML .= '</BulkDataExchangeRequests>';
//        preg_match('/<meta.*?charset=(|\")(.*?)("|\")/i', $uploadXML, $matches);
//        $charset = $matches[2];
//
//        if ($charset)
//            $XML = mb_convert_encoding($uploadXML, 'UTF-8', $charset);
//        else
//            $XML = $uploadXML;
//        print_r($uploadXML);
//        $XML = iconv(mb_detect_encoding($uploadXML, mb_detect_order(), true), "UTF-8", $uploadXML);
//        $this->pr($uploadXML);
//        die("1234");
        if ($store_feed == true) {
            $file_path = STORE_DIRECTORY . '/ebayFeeds/ebayfeed_update_un.xml';
            if (file_exists($file_path)) {
                unlink($file_path);
            }
//               print_r($doc);
//            die('-------');
//            $myfile = fopen($file_path, "w");
            file_put_contents($file_path, $uploadXML, LOCK_EX);
            $xml = file_get_contents($file_path, LOCK_EX);
            $doc = new DOMDocument();
//            $doc->preserveWhiteSpace = FALSE;
            $doc->loadXML($xml);
            $doc->formatOutput = TRUE;
//Save XML as a file
            $file = STORE_DIRECTORY . '/ebayFeeds/ebayfeed_update.xml';
            if (file_exists($file)) {
                unlink($file);
            }
            $doc->save($file);
        }

//        $response = json_decode(json_encode((array) simplexml_load_string($this->call($uploadXML))), 1);
    }

    /**
     * Function to call ebay API using the xml passed to it.
     * @param type $xml
     * @return type
     * @access private
     * @author Manish
     */
    private function call($xml) {
        $connection = curl_init();
//set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl);

//stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

//set the headers using the array of headers
        curl_setopt($connection, CURLOPT_HTTPHEADER, $this->headers);

//set method as POST
        curl_setopt($connection, CURLOPT_POST, 1);

//set the XML body of the request
        curl_setopt($connection, CURLOPT_POSTFIELDS, $xml);

//set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

//Send the Request
        $response = curl_exec($connection);

//close the connection
        curl_close($connection);
		//var_dump(curl_error($connection));
		//die();
        return $response;
    }

    private function getHeaders() {
        $this->boundary = "MIME_boundary";
        $this->getEbayAuthSettingsFromDb();
        if ($this->check_header_type_image) {
            $data = 'Content-Type: multipart/form-data; boundary=' . $this->boundary;
        } else {
            $data = "";
        }
        $this->headers = array(
            $data,
            //Regulates versioning of the XML interface for the API
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatibility_level,
            //set the keys
            'X-EBAY-API-DEV-NAME:' . $this->cred['Setting']['dev_id'],
            'X-EBAY-API-APP-NAME:' . $this->cred['Setting']['app_id'],
            'X-EBAY-API-CERT-NAME:' . $this->cred['Setting']['cert_id'],
            //the name of the call we are requesting
            'X-EBAY-API-CALL-NAME: ' . $this->call,
            //SiteID must also be set in the Request's XML
//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
//SiteID Indicates the eBay site to associate the call with
            'X-EBAY-API-SITEID: ' . 0
        );
    }

    /**
     * Function to get all ebay auth setting from db
     * @access private
     * @author Anik Goel
     */
    private function getEbayAuthSettingsFromDb() {


/*
        $this->cred['Setting'] = array(
            'dev_id' => "1a45cdf5-d592-4f43-96dd-83f5c03c29a6",
            'app_id' => 'pushpend-test-SBX-39f29112f-385610c5',
            'cert_id' => "SBX-9f29112f4a9a-1827-4270-911d-d034",
            'user_token' => "AgAAAA**AQAAAA**aAAAAA**k7CZWA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GiAJeCpwSdj6x9nY+seQ**hPYDAA**AAMAAA**58pYXe/Al5AAklWeiyoZzZ/GdVZz7I4Ze2rSB9OejaKTHGF7tF0+fkDwHvT9r+pGQ6bwDo6qN+lENnn7Z/baOHZIMe+xv0BNCYNBFw1cxGgMbOOAYP4jd6oyxcvUpKVJrUEFJiMqX533V/npXTQ03VRuQXh700oouz30DqV08gMt4QksaJkOcNz1paOmvVfdtes1ZDKnORvpUldYBoDoeJuZZxqG9u14HVnMymwOZByicT+4f3K9Ek55QDdDrLGRBx0Z3WTmWxr0OwCKPrDsBM1SKFUeOVjksG7VtI1BX72PcUeQrjPPBkpVQVDnEwHbqfyYqqfOz4klzjwq+Y/wRHSC3LPNf52G2brXQ9Xs/DXE0z1v466Hk4H8nbpltSdtXPiSo2FUZbp2OkenoyMRXSoYrHjLPgU7fnS6hGfhXXZZ+H+d+RihkrTniQmtHeZn9OZBgErRsE6wMHf307jXhiv+tMiHMcqytlR8/iV/Y6OCuN6m3TuOuaiuC/sSp59SXJWj1Yahk1cRKax2crO3tecLZJbYgSoAEjJtrdpST9KWuMg5jYbvkGxAvQ+3ckoT/bjD1+7+1GuS8eka59v4ee0d3hZJ+5jVDau5nO3u1QjBUiL1IAlyVczXsZxjpYFlgw/YyzdrYMuyzKC9FYZB33odB0ER4kIXsCQO/BvO2uzWCDkL6Tbj/iwyCEb4Rw690k5lUGeL8YJkWFtzViid1Vc3mU0aoZa/frtQu3r4vGQfE/LaNVCpFAurSxshAwqq"
        );
		
		$this->cred['Setting']['credentials']['appId']  = $this->cred['Setting']['app_id'];
		$this->cred['Setting']['credentials']['certId'] = $this->cred['Setting']['cert_id'];
		$this->cred['Setting']['credentials']['devId'] = $this->cred['Setting']['dev_id'];
		$this->cred['Setting']['authToken'] = $this->cred['Setting']['user_token'];
*/

        $this->cred['Setting'] = array(
            'dev_id' => "b75ef97a-a42e-4373-ae99-019f78f9abdb",
            'app_id' => 'DavidMat-PowerSpo-SBX-18e3a12c2-738fe25f',
            'cert_id' => "SBX-8e3a12c29b5e-694a-4fe0-9801-5259",
            'user_token' => "AgAAAA**AQAAAA**aAAAAA**YzBIWQ**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GkC5CEoQ2dj6x9nY+seQ**ejwEAA**AAMAAA**dUAyijkm2T0cZZBxcXNjQKPoIVvWgmzN/cWR6R5BYgHMHd+8sfUtJpgfAbyM3DN4lHAt4314vce8UNPA6ZlDmY3uFWgZDrIkgkof2lGZn1cXMCkGwb/MZhLawxoTgsSRcq0ErYc+GiWzV7H08IqKwJ0QUfO+0ivRbcn1MgXXja1j1t2nUV8PArKAZAGp9t/g8T6w/bBmbPdaCRKh70PJuFmThp6yHLVoAPUNj7lkc9znlvMiHwoMmB0zrSd3HCCVoR2vVFJpVZ0H5YN1Zjed5I0DSe1k1BN7TZlC6GayjEsYhut3Kojn5g2y7oL8OlJ5vcCgUUJYfdLJtnURdKFcJqe2vaqyaCPPVV/MzjxypLbyFfGq/z0sSv1LAQdlWncJH7toNTYwO1gsKV9al8dLdnGOjoSSyWc1LYkllLUC5uaeEA5ewxq7vCNnR2UcdoytOKnR5LV/LEIKHbpBCzZcz0RuGRQbZ9U1xj7rUuOBOe8C89202kL2TUueX5BQkvcBKRTYxJk1r8zjVWPxQWL4hg1HrrTZsjwS69oh3sI1OJSAPxNoq9xr6DZsxioORvytJKtKmq4IMKUHNQsi8ctqAd6kZNM8GKlLT1DgElsKLYtKcrN+rtjm58B23aplULHBLGJOsTxLo3f/JUHXRSmeTz19sfJqZ4CiSgQ/S6KMAvLymiecRGl0EjtW7ZUKCYFmYCubJ3drtNiqHSNwdslprBtUHMhxhlGvrydoZCE+uU6pqEJZCt/AVVmVRXNUwVkX"
        );
		$this->cred['Setting']['credentials']['appId']  = $this->cred['Setting']['app_id'];
		$this->cred['Setting']['credentials']['certId'] = $this->cred['Setting']['cert_id'];
		$this->cred['Setting']['credentials']['devId'] = $this->cred['Setting']['dev_id'];
		$this->cred['Setting']['authToken'] = $this->cred['Setting']['user_token'];
		$this->cred['Setting']['sandbox'] = true;


		$sql = "SELECT ebay_app_id, ebay_cert_id, ebay_dev_id, ebay_user_token  
				FROM contact 
				WHERE id = 1";
		$query = $this->db->query($sql);
		$cred = $query->result_array();		

		if($cred[0]['ebay_dev_id'] == "" || $cred[0]['ebay_app_id'] == "" || $cred[0]['ebay_cert_id'] == "" || $cred[0]['ebay_user_token'] == "") {
			die("Missing eBay developer credentials!");
		}
        $this->cred['Setting'] = array(
            'dev_id' => $cred[0]['ebay_dev_id'],
            'app_id' => $cred[0]['ebay_app_id'],
            'cert_id' => $cred[0]['ebay_cert_id'],
            'user_token' => $cred[0]['ebay_user_token']
        );
		$this->cred['Setting']['credentials']['appId']  = $this->cred['Setting']['app_id'];
		$this->cred['Setting']['credentials']['certId'] = $this->cred['Setting']['cert_id'];
		$this->cred['Setting']['credentials']['devId'] = $this->cred['Setting']['dev_id'];
		$this->cred['Setting']['authToken'] = $this->cred['Setting']['user_token'];
		
	}

    /**
     * Function to set header for calls
     * @param type $call_name
     * @param type $status
     * @author  Anil Gautam
     * @return  name of the image
     */
    private function _setHeader($call_name, $status) {
        $this->call = $call_name;
        $this->check_header_type_image = $status;
        $this->getHeaders();
    }

    public function update_ebay_feeds_log($data) {
        $this->db->insert('ebay_feed_log', $data);
    }

    public function insertEbayIds($data) {
        $this->db->insert('ebay_ids', $data);
    }

    public function checkForComboReporting($partid) {
        $sql = "SELECT partpartnumber.partnumber_id
					FROM (`partnumberpartquestion`)
					JOIN `partnumber` ON `partnumber`.`partnumber_id` = `partnumberpartquestion`.`partnumber_id`
					LEFT JOIN `partnumbermodel` ON `partnumbermodel`.`partnumber_id` = `partnumber`.`partnumber_id`
					JOIN `partpartnumber` ON `partpartnumber`.`partnumber_id` = `partnumber`.`partnumber_id`
					JOIN `partquestion` ON `partquestion`.`partquestion_id` = `partnumberpartquestion`.`partquestion_id`
					WHERE `partquestion`.`part_id` =  '" . $partid . "'
					AND `productquestion` =  0
					AND  (partnumber.universalfit > 0 OR partnumbermodel.partnumbermodel_id is not null) 
					GROUP BY `question`";

        $query = $this->db->query($sql);
        $partnumbers = $query->result_array();
        $query->free_result();
        if (count($partnumbers) > 1) {
            $parts = array();
            foreach ($partnumbers as $rec) {
                $sql = "SELECT part.part_id
							FROM part
							JOIN partpartnumber ON partpartnumber.part_id = part.part_id
							WHERE partpartnumber.partnumber_id = '" . $rec['partnumber_id'] . "'
							AND part.part_id != '" . $partid . "'	";
                $query = $this->db->query($sql);
                $results = $query->result_array();
                $query->free_result();
                $parts[] = @$results[0]['part_id'];
            }
            return $parts;
        } else
            return FALSE;
    }

    public function getPriceRangeReporting($partId, $activeMachine = NULL, $checkCombo = TRUE) {
        $combopartIds = FALSE;
        // $where = array('partpartnumber.part_id' => $partId);
        // $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        // $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        // $this->db->where('partnumber.price > 0');
        // $this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN 0 ELSE 1 END )");
        // $this->db->select('partnumber, MIN(partnumber.sale) AS sale_min, MIN(partnumber.price) AS price_min, MAX(partnumber.price) AS price_max, MAX(partnumber.sale) AS sale_max, count(partnumber) as cnt, MIN(partnumber.dealer_sale) AS dealer_sale_min, MAX(partnumber.dealer_sale) AS dealer_sale_max');
        // $this->db->group_by('part_id');
        // $partNumberRec = $this->selectRecord('partnumber', $where);

        $where = array('partpartnumber.part_id' => $partId);
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        if (!is_null($activeMachine)) {
            $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partpartnumber.partnumber_id', 'LEFT');
            $where['partnumbermodel.year'] = $activeMachine['year'];
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id', 'left');
        $this->db->where('partnumber.price > 0');
        //$this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN 0 ELSE 1 END )");
        $this->db->where("(CASE WHEN partdealervariation.quantity_available = 0 AND partdealervariation.stock_code = 'Closeout' THEN CASE WHEN partvariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
        // $this->db->select('partnumber, 
        // MIN(partnumber.dealer_sale) AS dealer_sale_min,
        // MAX(partnumber.dealer_sale) AS dealer_sale_max,
        // MIN(partnumber.price) AS price_min, 
        // MAX(partnumber.price) AS price_max, 
        // MIN(partnumber.sale) AS sale_min, 
        // MAX(partnumber.sale) AS sale_max');
        // $this->db->group_by('part_id');
        // $partNumberRec = $this->selectRecord('partnumber', $where);

        $this->db->select('partnumber, partnumber.dealer_sale,partnumber.price, partnumber.sale, partdealervariation.quantity_available as dealer_quantity, partvariation.quantity_available');
        //$this->db->group_by('part_id');
        $partNumberRec1 = $this->selectRecords('partnumber', $where);

        $partNumberRec = array('price_min' => 0, 'price_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
        foreach ($partNumberRec1 as $k => $v) {
            if ($v['dealer_quantity'] > 0) {
                if ($k == '0') {
                    $partNumberRec['sale_min'] = $v['dealer_sale'];
                } else if ($partNumberRec['sale_min'] > 0 && $partNumberRec['sale_min'] > $v['dealer_sale']) {
                    $partNumberRec['sale_min'] = $v['dealer_sale'];
                }
                if ($k == '0') {
                    $partNumberRec['sale_max'] = $v['dealer_sale'];
                } else if ($partNumberRec['sale_max'] > 0 && $partNumberRec['sale_max'] < $v['dealer_sale']) {
                    $partNumberRec['sale_max'] = $v['dealer_sale'];
                }
            } else {
                if ($k == '0') {
                    $partNumberRec['sale_min'] = $v['sale'];
                } else if ($partNumberRec['sale_min'] > 0 && $partNumberRec['sale_min'] > $v['sale']) {
                    $partNumberRec['sale_min'] = $v['sale'];
                }
                if ($k == '0') {
                    $partNumberRec['sale_max'] = $v['sale'];
                } else if ($partNumberRec['sale_max'] > 0 && $partNumberRec['sale_max'] < $v['sale']) {
                    $partNumberRec['sale_max'] = $v['sale'];
                }
            }
            if ($k == '0') {
                $partNumberRec['price_min'] = $v['price'];
            } else if ($partNumberRec['price_min'] > 0 && $partNumberRec['price_min'] > $v['price']) {
                $partNumberRec['price_min'] = $v['price'];
            }
            if ($k == '0') {
                $partNumberRec['price_max'] = $v['price'];
            } else if ($partNumberRec['price_max'] > 0 && $partNumberRec['price_max'] < $v['price']) {
                $partNumberRec['price_max'] = $v['price'];
            }
        }
        return $partNumberRec;
    }

    public function calculateMarkupReporting($retailmin, $retailmax = 0, $min, $max = 0, $userMarkUp = NULL, $dealer_sale_min = 0, $dealer_sale_max = 0, $cnt = 0) {
        $returnArr = array('retail_min' => $retailmin, 'retail_max' => $retailmax);
        if (@$userMarkUp) {
            $userMin = (($retailmin * $userMarkUp) / 100) + $retailmin;
            $userMax = (($retailmax * $userMarkUp) / 100) + $retailmax;
            if ($userMin < $min)
                $min = $userMin;
            if ($userMax < $max)
                $max = $userMax;
        }
        if ($min == $max) {
            $returnArr['sale_min'] = $min;
            $returnArr['sale_max'] = FALSE;
        } else {
            $returnArr['sale_min'] = $min;
            $returnArr['sale_max'] = $max;
        }
        if ($min < $retailmin) {
            $returnArr['percentage'] = 100 - (($min * 100) / $retailmin);
        } else
            $returnArr['percentage'] = FALSE;

        $sale_min = 0;
        if ($returnArr['sale_min'] > $dealer_sale_min && $dealer_sale_min > 0) {
            $sale_min = $returnArr['sale_min'];
            $returnArr['sale_min'] = $dealer_sale_min;
        }
        if ($returnArr['sale_min'] > $dealer_sale_max && $dealer_sale_max > 0) {
            $sale_min = $returnArr['sale_min'];
            $returnArr['sale_min'] = $dealer_sale_max;
        }

        if ($returnArr['sale_max'] < $dealer_sale_min && $dealer_sale_min > 0 && $returnArr['sale_max'] > 0) {
            $returnArr['sale_max'] = $dealer_sale_min;
        }
        if ($returnArr['sale_max'] < $dealer_sale_max && $dealer_sale_max > 0 && $returnArr['sale_max'] > 0) {
            $returnArr['sale_max'] = $dealer_sale_max;
        }

        if ($returnArr['sale_max'] == '' && $returnArr['sale_min'] < $sale_min) {
            $returnArr['sale_max'] = $sale_min;
        }

        if ($cnt == 1 && $returnArr['sale_min'] <= $dealer_sale_max) {
            $returnArr['sale_min'] = $dealer_sale_max;
            $returnArr['sale_max'] = FALSE;
        }

        if ($cnt == 1 && $returnArr['sale_min'] <= $dealer_sale_min) {
            $returnArr['sale_min'] = $dealer_sale_min;
            $returnArr['sale_max'] = FALSE;
        }

        if ($returnArr['sale_min'] > $returnArr['sale_max']) {
            $returnArr['sale_max'] = FALSE;
        }
        return $returnArr;
    }

    public function getProductQuestions($partId, $activeMachine = NULL) {
        $where = array('partquestion.part_id' => $partId, 'productquestion' => 0, "answer != ''" => NULL);
        if (@$activeMachine['model']['model_id']) {
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        if (@$activeMachine['year']) {
            $where['partnumbermodel.year'] = $activeMachine['year'];
        }
        $this->db->where($where);
        $this->db->where(' (partnumber.universalfit > 0 OR partnumbermodel.partnumbermodel_id is not null) ', NULL, FALSE);
        //$this->db->where('partnumber.sale != 0');
        $this->db->where("(CASE WHEN partdealervariation.quantity_available != 0 AND partdealervariation.stock_code = 'Closeout' THEN partnumber.dealer_sale != 0 ELSE partnumber.sale != 0 END )");

        //$this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN CASE WHEN partdealervariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
        $this->db->where("(CASE WHEN partdealervariation.quantity_available = 0 AND partdealervariation.stock_code = 'Closeout' THEN CASE WHEN partvariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
        $this->db->join('partnumber', 'partnumber.partnumber_id = partnumberpartquestion.partnumber_id');
        $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id', 'left');
        $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
        $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');

        $this->db->order_by('partquestion.partquestion_id, answer');
        $this->db->group_by('partquestion.partquestion_id, answer');
        $partNumberRecs = $this->selectRecords('partnumberpartquestion');
        //echo $this->db->last_query();
//        echo '<pre>';
//        print_r($partNumberRecs);
//        echo '</pre>';
//        exit;
        return $partNumberRecs;
    }

    public function get_shipping_cost($product_price) {
        $this->db->select("*");
        $this->db->from("ebay_shipping_rates");
        $query = $this->db->get();
        $shipping_cost_range = $query->result_array();
        foreach ($shipping_cost_range as $single_shipping_range) {
            if ($product_price >= $single_shipping_range['min_value'] && $product_price <= $single_shipping_range['max_value']) {
                $shipping_cost = $single_shipping_range['shipping_cost'];
                break;
            } elseif ($product_price >= $single_shipping_range['min_value'] && $single_shipping_range['max_value'] == 0) {
                $shipping_cost = 0;
                break;
            } else {
                $shipping_cost = 0;
            }
        }
        return $shipping_cost;
    }

    public function get_paypalemail() {
        $this->db->select("*");
        $this->db->from("ebay_settings");
        $this->db->where("key", "paypal_email");		
        $query = $this->db->get();
        if (is_array($query->result_array())) {
            foreach ($query->result_array() as $paypal_value_check) {
                if (key_exists('value', $paypal_value_check) && $paypal_value_check['value'] != '') {
                    return $paypal_value_check['value'];
                }
            }
        }
        exit('Enter paypal email address in admin.');
    }

    public function get_markup() {
        $this->db->select("*");
        $this->db->from("ebay_settings");
        $this->db->where("key", "ebay_markup");		
        $query = $this->db->get();
        if (is_array($query->result_array())) {
            foreach ($query->result_array() as $paypal_value_check) {
                if (key_exists('value', $paypal_value_check) && $paypal_value_check['value'] != '') {
                    return $paypal_value_check['value'];
                }
            }
        }
        exit('Enter paypal email address in admin.');
    }
	
	
    public function get_quantity() {
        $this->db->select("*");
        $this->db->from("ebay_settings");
        $this->db->where("key", "quantity");		
        $query = $this->db->get();
        if (is_array($query->result_array())) {
            foreach ($query->result_array() as $quantity_value_check) {
                if (key_exists('value', $quantity_value_check) && $quantity_value_check['value'] != '') {
                    return $quantity_value_check['value'];
                }
            }
        }
        exit('Enter quantity in admin.');
    }
	
	
    public function getPriceByPartNumber($part_number) {
        if ($this->validPartNumber() === TRUE) {
            $partDealerNumberRec = $this->account_m->getDealerPriceByPartNumber($part_number);
            //$price = $this->account_m->getPriceByPartNumber($this->input->post('partnumber'));
            if (!empty($partDealerNumberRec) && $partDealerNumberRec['quantity_available'] > 0) {
                echo json_encode($partDealerNumberRec);
            } else {
                $price = $this->account_m->getPriceByPartNumber($part_number);
                //$partDealerNumberRec = $this->account_m->getDealerPriceByPartNumber($this->input->post('partnumber'));
                echo json_encode($price);
            }
        }
    }
	
	private function saveAttachment($data)
	{
		$tempFilename = tempnam(sys_get_temp_dir(), 'attachment').'.zip';
		$fp = fopen($tempFilename, 'wb');
		if ($fp) {
			fwrite($fp, $data);
			fclose($fp);
			return $tempFilename;
		} else {
			printf("Failed. Cannot open %s to write!\n", $tempFilename);
			return false;
		}
	}
	private function unzipArchive($filename)
	{
		printf("Unzipping %s...", $filename);
		$zip = new ZipArchive();
		if ($zip->open($filename)) {
			/**
			 * Assume there is only one file in archives from eBay.
			 */
			$xml = $zip->getFromIndex(0);
			if ($xml !== false) {
				print("Done\n");
				return $xml;
			} else {
				printf("Failed. No XML found in %s\n", $filename);
				return false;
			}
		} else {
			printf("Failed. Unable to unzip %s\n", $filename);
			return false;
		}
	}	
	

}
