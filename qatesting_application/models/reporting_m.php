<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporting_M extends Master_M
{
  	function __construct()
	{
		parent::__construct();
	}
	
	public function getOrdersPerMonth($monthTS)
	{
		$i = 0;
		$num = array();
		$mn = date('m', $monthTS);
		$st = strtotime(date('Y-m-01', $monthTS));
		$ed = strtotime(date('Y-m-t', $monthTS));
		while($i < 12)
		{
		  $this->db->where('order_date >', $st);
		  $this->db->where('order_date <', $ed);
		  $this->db->from('order');
		  $num[$mn] = $this->db->count_all_results();
		  $i++;
		  $st = strtotime("-1 month", $st); 
		  $ed = strtotime(date('Y-m-t', $st));
		  $mn = date('m', $st);
		}
		return $num;
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
  
	public function getProductsForXML()
	{
		$sql = "SELECT  partvariation.part_number AS SKU, 
									   brand.name AS 'Manufacturer', 
									   partvariation.manufacturer_part_number AS 'Manufacturer Part #',
									   partvariation.quantity_available AS 'Quantity for Sale',
									   partnumber.sale AS Price,
									   partnumber.weight AS Weight
								FROM partnumber
								JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
								JOIN partimage ON partimage.part_id = partpartnumber.part_id
								JOIN partcategory ON partcategory.part_id = partpartnumber.part_id
								JOIN category ON category.category_id = partcategory.category_id
								JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
								JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
								JOIN brand ON brand.brand_id = partbrand.brand_id
								WHERE sale > 50 AND partnumber.price != 0
								AND (category.long_name LIKE '%HELMET%' )
								AND partvariation.quantity_available > 0
								GROUP BY partnumber.partnumber_id
								LIMIT 50
								";
								//AND (category.long_name LIKE '%HELMET%' OR category.long_name LIKE '%JERSEY%' OR category.long_name LIKE '%PANT%' OR category.long_name LIKE '%GLOVE%')
		$query = $this->db->query($sql);
		$partnumbers = $query->result_array();
		$query->free_result();
		$csv = $this->array2csv($partnumbers);
		return $csv;
	}

	public function getProductsForGoogle()
	{
		//partnumber.promotion_id AS promotion_id
		//CASE WHEN partnumber.closeout_market_place = 0 THEN 'current' ELSE 'closeout' END AS custom_lable_0,
		$sql = "SELECT  
						   partnumber.partnumber_id AS ref_id,
						   partnumber.partnumber AS id,
						   CASE WHEN answer != '' THEN CONCAT (part.name, ' - ', answer) ELSE part.name END AS title,
						   CASE WHEN answer != '' THEN CONCAT (part.name, ' - ', answer) ELSE part.name END AS description,
						   category.long_name AS product_type,
						   brand.name AS 'brand',
						   partvariation.manufacturer_part_number AS 'mpn',
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS link,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS mobile_link,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/productimages/', partimage.path) AS image_link,
						   'new' AS 'condition',
						   CASE WHEN partvariation.quantity_available > 0 THEN 'in stock' ELSE 'out of stock' END AS 'availability',
						   partnumber.sale AS 'price',
						   CASE WHEN partnumber.weight != '' THEN partnumber.weight ELSE '0.5' END AS 'weight',
						   
						   CASE WHEN partvariation.stock_code != 'Closeout' THEN 'current' ELSE 'closeout' END AS custom_lable_0,
						   
						   part.part_id AS part_own_id,
						   brand.promotion_data AS promotion_data

					FROM partnumber
					JOIN partnumberpartquestion ON partnumberpartquestion.partnumber_id = partnumber.partnumber_id
					JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
					JOIN part ON part.part_id = partpartnumber.part_id
					JOIN partimage ON partimage.part_id = partpartnumber.part_id
					JOIN partcategory ON partcategory.part_id = partpartnumber.part_id
					JOIN category ON category.category_id = partcategory.category_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
					JOIN brand ON brand.brand_id = partbrand.brand_id
					WHERE sale > 15 AND partnumber.price != 0
					GROUP BY partnumber.partnumber ";

		$query = $this->db->query($sql);
		$partnumbers = $query->result_array();
		$query->free_result();
		if($partnumbers)
		{
			foreach($partnumbers as &$part)
			{

				$part['custom_lable_1'] = "";
				$part['google_product_category'] = "";
				$part['promotion_id'] = "";
				
				$get_least_specific_category_sql = "SELECT long_name, google_category_num FROM category WHERE category.category_id
													IN (SELECT partcategory.category_id FROM partcategory WHERE partcategory.part_id=".$part['part_own_id'].")
													ORDER BY LENGTH(category.long_name) DESC LIMIT 1";
				$get_least_specific_category = $this->db->query($get_least_specific_category_sql);
				if ($get_least_specific_category->num_rows() > 0){
					$part['custom_lable_1'] = $get_least_specific_category->row()->long_name;
					$part['google_product_category'] = $get_least_specific_category->row()->google_category_num;
				}
				
				$get_least_specific_category->free_result();
				
				if( !empty($part['promotion_data']) ){
					$exploded_promotion_data = explode("_*_*_", $part['promotion_data']);
					if( $exploded_promotion_data[1]==1 && !empty($exploded_promotion_data[0]) && $part['custom_lable_0']=='closeout'){
						$part['promotion_id'] = $exploded_promotion_data[0];
					}elseif( $exploded_promotion_data[1]==0 && !empty($exploded_promotion_data[0]) && $part['custom_lable_0']=='current'){
						$part['promotion_id'] = $exploded_promotion_data[0];
					}
				}
				
				if(  strpos($part['title'], 'COMBO') !== FALSE )
					$part['id'] .= 'C';
				$rides = array();
				
				$sql = "SELECT CONCAT (make.name, ' ',  model.name, ' ', partnumbermodel.year) AS fitment 
							FROM (`partnumbermodel`) 
							JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
							JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
							WHERE `partnumbermodel`.`partnumber_id` =  '".$part['ref_id']."'";
				$query = $this->db->query($sql);
				$rides = $query->result_array();
				$query->free_result();
				
				if($rides)
				{
					foreach($rides as $ride)
					{
						$part['description'] .=  ' / ' . $ride['fitment'] ;
					}
				}
				unset($part['ref_id']);
				unset($part['part_own_id']);
				unset($part['promotion_data']);
			}
		}

		$csv = $this->array2csv($partnumbers);
		$csv = str_replace('"', '', $csv);
		return $csv;
	}
	
	public function getProductsForFB()
	{
		$sql = "SELECT  
						   partnumber.partnumber_id AS ref_id,
						   partnumber.partnumber AS id,
						   CASE WHEN answer != '' THEN CONCAT (part.name, ' - ', answer) ELSE part.name END AS title,
						   CASE WHEN answer != '' THEN CONCAT (part.name, ' - ', answer) ELSE part.name END AS description,
						   category.long_name AS product_type,
						   brand.name AS 'brand',
						   partvariation.manufacturer_part_number AS 'mpn',
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS link,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS mobile_link,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/productimages/', partimage.path) AS image_link,
						   'new' AS 'condition',
						   CASE WHEN partvariation.quantity_available > 0 THEN 'in stock' ELSE 'out of stock' END AS 'availability',
						   partnumber.sale AS 'price',
						   CASE WHEN partnumber.weight != '' THEN partnumber.weight ELSE '0.5' END AS 'weight'
					FROM partnumber
					JOIN partnumberpartquestion ON partnumberpartquestion.partnumber_id = partnumber.partnumber_id
					JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
					JOIN part ON part.part_id = partpartnumber.part_id
					JOIN partimage ON partimage.part_id = partpartnumber.part_id
					JOIN partcategory ON partcategory.part_id = partpartnumber.part_id
					JOIN category ON category.category_id = partcategory.category_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
					JOIN brand ON brand.brand_id = partbrand.brand_id
					WHERE sale > 15 AND partnumber.price != 0
					GROUP BY partnumber.partnumber ";

		$query = $this->db->query($sql);
		$partnumbers = $query->result_array();
		$query->free_result();
		
		if($partnumbers)
		{
			foreach($partnumbers as &$part)
			{
				if(  strpos($part['title'], 'COMBO') !== FALSE )
					$part['id'] .= 'C';
				$part['title'] = strtolower($part['title']);
				$part['title'] = ucwords($part['title']);
				$rides = array();
				
				$sql = "SELECT CONCAT (make.name, ' ',  model.name, ' ', partnumbermodel.year) AS fitment 
							FROM (`partnumbermodel`) 
							JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
							JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
							WHERE `partnumbermodel`.`partnumber_id` =  '".$part['ref_id']."'";
				$query = $this->db->query($sql);
				$rides = $query->result_array();
				$query->free_result();
				
				if($rides)
				{
					foreach($rides as $ride)
					{
						$part['description'] .=  ' / ' . $ride['fitment'] ;
					}
				}
				unset($part['ref_id']);
			}
		}
		$csv = $this->array2csv($partnumbers);
		$csv = str_replace('"', '', $csv);
		return $csv;
	}
	
		public function getProductsForBing()
	{
		$sql = "SELECT  
						   partnumber.partnumber_id AS ref_id,
						   partnumber.partnumber AS id,
						   CASE WHEN answer != '' THEN CONCAT (part.name, ' - ', answer) ELSE part.name END AS title,
						   CASE WHEN answer != '' THEN CONCAT (part.name, ' - ', answer) ELSE part.name END AS description,
						   category.long_name AS product_type,
						   brand.name AS 'brand',
						   partvariation.manufacturer_part_number AS 'mpn',
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS link,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS mobile_link,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/productimages/', partimage.path) AS image_link,
						   'new' AS 'condition',
						   CASE WHEN partvariation.quantity_available > 0 THEN 'in stock' ELSE 'out of stock' END AS 'availability',
						   partnumber.sale AS 'price',
						   CASE WHEN partnumber.weight != '' THEN partnumber.weight ELSE '0.5' END AS 'weight'
					FROM partnumber
					JOIN partnumberpartquestion ON partnumberpartquestion.partnumber_id = partnumber.partnumber_id
					JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
					JOIN part ON part.part_id = partpartnumber.part_id
					JOIN partimage ON partimage.part_id = partpartnumber.part_id
					JOIN partcategory ON partcategory.part_id = partpartnumber.part_id
					JOIN category ON category.category_id = partcategory.category_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
					JOIN brand ON brand.brand_id = partbrand.brand_id
					WHERE sale > 15 AND partnumber.price != 0
					GROUP BY partnumber.partnumber ";

		$query = $this->db->query($sql);
		$partnumbers = $query->result_array();
		$query->free_result();
		
		if($partnumbers)
		{
			foreach($partnumbers as &$part)
			{
				if(  strpos($part['title'], 'COMBO') !== FALSE )
					$part['id'] .= 'C';
				$rides = array();
				
				$sql = "SELECT CONCAT (make.name, ' ',  model.name, ' ', partnumbermodel.year) AS fitment 
							FROM (`partnumbermodel`) 
							JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
							JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
							WHERE `partnumbermodel`.`partnumber_id` =  '".$part['ref_id']."'";
				$query = $this->db->query($sql);
				$rides = $query->result_array();
				$query->free_result();
				
				if($rides)
				{
					foreach($rides as $ride)
					{
						$part['description'] .=  ' / ' . $ride['fitment'] ;
					}
				}
				unset($part['ref_id']);
			}
		}

		return $partnumbers;
	}

	
	public function getAppeagleAmazonXML()
	{
		sleep(120);
		$sql = "SELECT  
				   part.part_id,
				   partvariation.stock_code,
				   partnumber.price,
				   partvariation.part_number AS 'Distributor Part Number',
				   brand.name AS 'Manufacturer Name',
				   brand.closeout_market_place, brand.exclude_market_place, brand.brand_id, partnumber.partnumber_id, partnumber.closeout_market_place AS 'partnumber_closeout_market_place', partnumber.exclude_market_place AS 'partnumber_exclude_market_place',
				   partvariation.manufacturer_part_number AS 'Manufacturer Part Number',
				   partvariation.quantity_available AS 'INVENTORY',
				   '9261' AS 'MARKETPLACE_ID',
				   (partnumber.cost * 1.15) + 15 AS 'MIN_PRICE',
				   CASE WHEN partnumber.price < 120 THEN partnumber.price + 23 ELSE partnumber.price END AS 'MAX_PRICE',
				   '0' AS 'CURRENT_SHIPPING'
					FROM partnumber
					JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
					JOIN part ON part.part_id = partpartnumber.part_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
					JOIN brand ON brand.brand_id = partbrand.brand_id
					WHERE partnumber.price != 0
					GROUP BY partnumber.partnumber_id;
								";
		/*
			SCRIPT THAT CAN BE USED FOR TESTING/DEBUGGING PURPOSE
			limit 0, 2000
			
			'closeout_market_place' => $part['closeout_market_place'],
			'exclude_market_place' => $part['exclude_market_place'],
			'brand_id' => $part['brand_id'],
			'stock_code' => $part['stock_code'],
			'partnumber_id' => $part['partnumber_id']
			
			echo "Size:".count($finalArr)."<br>";
			echo "<pre>";		
			print_r($finalArr);
			echo "</pre>";
			exit;	
		*/
		
		$query = $this->db->query($sql);
		$partnumbers = $query->result_array();
		$query->free_result();
		$finalArr = array();
		if($partnumbers)
		{
			foreach($partnumbers as $part)
			{
				$this->db->select('MIN(brand.map_percent) as map_percent');
				$where = array('partbrand.part_id' => $part['part_id'], 'brand.map_percent > ' => 0);
				$this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
				$brand_map_percent = $this->selectRecord('brand', $where);
				$brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : 0;
			
				if(($brandMAPPercent > 0)	&& ($part['stock_code'] != 'Closeout'))			
				{
					$mapPrice = (((100 - $brandMAPPercent)/100) * $part['price']);
					 if($mapPrice > $part['MIN_PRICE'])
					 	$part['MIN_PRICE'] = $mapPrice;
				}
				if($part['MIN_PRICE'] > $part['MAX_PRICE'])
					$part['MAX_PRICE'] = $part['MIN_PRICE'];
				
				if( !empty($part['partnumber_closeout_market_place']) ){
					// Checking Product as First Priority
					if($part['stock_code'] != 'Closeout'){
						$part['INVENTORY'] = 0;
					}
				}elseif( !empty($part['closeout_market_place']) ){
					// Checking Brand as Second Priority
					if($part['stock_code'] != 'Closeout'){
						$part['INVENTORY'] = 0;
					}
				}
				
				if( !empty($part['partnumber_exclude_market_place']) ){
					// Checking Product as First Priority
					$part['INVENTORY'] = 0;
				}elseif( !empty($part['exclude_market_place']) ){
					// Checking Brand as Second Priority
					$part['INVENTORY'] = 0;
				}
				
				$finalArr[] = array(
												'SKU' => $part['Distributor Part Number'],
												'INVENTORY' => $part['INVENTORY'],
												'MARKETPLACE_ID' => $part['MARKETPLACE_ID'],
												'MIN_PRICE' => number_format($part['MIN_PRICE'],2),
												'MAX_PRICE' => $part['MAX_PRICE'],
												'CURRENT_SHIPPING' => $part['CURRENT_SHIPPING']/*,
												'closeout_market_place' => $part['closeout_market_place'],
												'exclude_market_place' => $part['exclude_market_place'],
												'brand_id' => $part['brand_id'],
												'stock_code' => $part['stock_code'],
												'partnumber_id' => $part['partnumber_id'],
												'part_id' => $part['part_id'],
												'partnumber_closeout_market_place' => $part['partnumber_closeout_market_place'],
												'partnumber_exclude_market_place' => $part['partnumber_exclude_market_place']*/
												);
				$finalArr[] = array(
												'SKU' => $part['Manufacturer Name'] . ' ' . $part['Manufacturer Part Number'] ,
												'INVENTORY' => $part['INVENTORY'],
												'MARKETPLACE_ID' => $part['MARKETPLACE_ID'],
												'MIN_PRICE' => $part['MIN_PRICE'],
												'MAX_PRICE' => $part['MAX_PRICE'],
												'CURRENT_SHIPPING' => $part['CURRENT_SHIPPING']/*,
												'closeout_market_place' => $part['closeout_market_place'],
												'exclude_market_place' => $part['exclude_market_place'],
												'brand_id' => $part['brand_id'],
												'stock_code' => $part['stock_code'],
												'partnumber_id' => $part['partnumber_id'],
												'part_id' => $part['part_id'],
												'partnumber_closeout_market_place' => $part['partnumber_closeout_market_place'],
												'partnumber_exclude_market_place' => $part['partnumber_exclude_market_place']*/
												);
												
			}
		}
			
		return $finalArr;
	}
	
	public function getAppEagleVariationOne()
	{
		$sql = "SELECT  brand.name AS 'Brand',
									   partvariation.manufacturer_part_number AS 'Manufacturer Part Number',
									   partvariation.part_number AS SKU,
									   partnumber.price AS Price,
									   partvariation.quantity_available AS 'Quantity'
								FROM partnumber
								JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
								JOIN part ON part.part_id = partpartnumber.part_id
								JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
								JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
								JOIN brand ON brand.brand_id = partbrand.brand_id
								WHERE partnumber.price != 0
								GROUP BY partnumber.partnumber_id;
								";
		$query = $this->db->query($sql);
		$partnumbers = $query->result_array();
		$query->free_result();			
		return $partnumbers;
	}
	
	public function getProductsForSaleZilla()
	{
		$sql = "SELECT  
						   partnumber.partnumber_id AS 'Unique ID',
						   part.name AS Title,
						   part.description AS Description,
						   category.long_name AS Category,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS 'Product URL',
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/productimages/', partimage.path) AS 'Image URL',
						   'New' AS 'Condition',
						   'In Stock' AS 'Availability',
						   partnumber.sale AS 'Current Price',
						   '' AS 'Item Group ID',
						   brand.name AS 'Brand',
						   partvariation.part_number AS 'GTIN',
						   partvariation.manufacturer_part_number AS 'MPN',
						   '' AS 'Gender',
						   '' AS 'Age Group',
						   '' AS 'Size',
						   '' AS 'Color',
						   '' AS 'Material',
						   '' AS 'Pattern',
						   '' AS 'Additional Image URL',
						   CASE WHEN (partnumber.price != partnumber.sale) THEN partnumber.price ELSE '' END AS 'Original Price',
						   '' AS 'ASIN',
						   CASE WHEN partnumber.sale > 100 THEN 0 ELSE '' END AS 'Ship Cost',
						   CASE WHEN partnumber.sale > 100 THEN '' ELSE partnumber.weight END AS 'Ship Weight',
						   '' AS 'Bid',
						   '' AS 'Promo Text'
					FROM partnumber
					JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
					JOIN part ON part.part_id = partpartnumber.part_id
					JOIN partimage ON partimage.part_id = partpartnumber.part_id
					JOIN partcategory ON partcategory.part_id = partpartnumber.part_id
					JOIN category ON category.category_id = partcategory.category_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
					JOIN brand ON brand.brand_id = partbrand.brand_id
					WHERE sale > 15 AND partnumber.price != 0
					AND partvariation.quantity_available > 0
					GROUP BY partnumber.partnumber_id;
					";
		$query = $this->db->query($sql);
		$partnumbers = $query->result_array();
		$query->free_result();
		if($partnumbers)
		{
			foreach($partnumbers as &$part)
			{
				$part['Description'] = preg_replace("/&#?[a-z0-9]+;/i",'',$part['Description']);
				$part['Description'] = strip_tags($part['Description']);
				$part['Description'] = str_replace(',', ' ', $part['Description']);
				$sql2 = 'SELECT * 
							FROM partnumberpartquestion 
							JOIN partquestion ON partquestion.partquestion_id = partnumberpartquestion.partquestion_id
							WHERE partnumber_id = '.$part['Unique ID'].' AND productquestion = 0';
				$query = $this->db->query($sql2);
				$partquestions = $query->result_array();
				$query->free_result();
				if(is_array($partquestions))
				{
					$titleArr = array();
					foreach($partquestions as $question)
					{
						$titleArr[$question['answer']] = $question['answer'];
						switch($question['question'])
						{
							case 'COLOR / SIZE':
								$answerArr = explode(' / ', $question['answer']);
								if(@$answerArr[1])
								{
									$part['Color'] = $answerArr[0];
									$part['Size'] = $answerArr[1];
								}
								break;
						}
					}
					if(!empty($titleArr))
					{
						foreach($titleArr as $title)
						{
							$part['Title'] .= ' ' . $title;
						}
					}
				}
			}
		}						
		return $partnumbers;
	}
	
	public function ebayListings($offset = 0, $limit = 1000)
	{
		// Filter quantity of 0, Price in 1 row only
		$finalArray = array();
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
						GROUP BY part.part_id
						LIMIT ".$offset.", ".$limit.";";
		$query = $this->db->query($sql);
		$parts = $query->result_array();
		$query->free_result();
		if(is_array($parts))
		{
			foreach($parts as &$part)
			{
				
				if(strpos($part['*Title'], 'COMBO') !== FALSE)
					continue;
				$part_id = $part['part_id'];
				unset($part['part_id']);
				/*************************************
					Get Categories with longest string count
				**************************************/
				$sql = "SELECT category.long_name
				FROM category
				JOIN partcategory ON partcategory.category_id = category.category_id
				WHERE partcategory.part_id = " . $part_id .
				' AND long_name NOT LIKE \'%UTV%\'';
				$query = $this->db->query($sql);
				$categories = $query->result_array();
				$query->free_result();
				// Create Category Name;
				$endCategoryName = '';
				if($categories)
				{
					foreach($categories as $cat)
					{
						if(strlen($cat['long_name']) > $endCategoryName)
							$endCategoryName = $cat['long_name'];
					}
				}
				// If no category, don't list the product
				if(empty($endCategoryName))
					break;
				
				$part['*Category'] = $this->eBayCategoryName($endCategoryName);
				$part['StoreCategory'] = $this->eBayStoreCategoryName($endCategoryName);
				
				/**************************
					End Category Name.
				***************************/
				
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
						1 AS '*Quantity',
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
					WHERE part.part_id = ".$part_id."
					AND partvariation.quantity_available > 3
					GROUP BY partnumber.partnumber";
				$query = $this->db->query($sql);
				$partnumbers = $query->result_array();
				$query->free_result();
				
				if(is_array($partnumbers))
				{
					$categoryRec = array();
					$fitmentArr = array();
					$basicPrice = 0;
					$samePrice = TRUE;
					foreach($partnumbers as $pn)
					{
						if($pn['*Quantity'] > 0)
						{
							//Calculate MAP Price
							$this->db->select('MIN(brand.map_percent) as map_percent');
							$where = array('partbrand.part_id' => $part_id, 'brand.map_percent > ' => 0);
							$this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
							$brand_map_percent = $this->selectRecord('brand', $where);

							$brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : 0;
							
							
							if(($brandMAPPercent > 0) && ($pn['stock_code'] != 'Closeout'))			
							{
								
								$mapPrice = (((100 - $brandMAPPercent)/100) * $pn['price']);
								 if($mapPrice > $pn['MIN_PRICE'])
								 	$pn['MIN_PRICE'] = $mapPrice;
							}
							$pn['*StartPrice'] = $pn['MIN_PRICE'];
							
							if($basicPrice == 0)
								$basicPrice = $pn['*StartPrice'];
							if(($samePrice) && ($pn['*StartPrice'] != $basicPrice))
								$samePrice = FALSE;
							
							
							// Record Prep
							$part['Relationship'] = '';
							$part['RelationshipDetails'] = '';
							$part['CustomLabel'] = $pn['CustomLabel'];
							$part['*Quantity'] = $pn['*Quantity'];
							$part['*Description'] = preg_replace("/\r\n|\r|\n/",'',$part['*Description']);
							
							unset($pn['stock_code']);
							unset($pn['MIN_PRICE']);
							unset($pn['MAX_PRICE']);
							unset($pn['price']);
							
							
							// Fitment compatability
							$sql = "SELECT CONCAT ('Make=', make.name, '|Model=',  model.name, '|Year=', partnumbermodel.year) AS fitment 
									FROM (`partnumbermodel`) 
									JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
									JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
									WHERE `partnumbermodel`.`partnumber_id` =  '".$pn['CustomLabel']."'
									AND make.machinetype_id != 43954;";
							$query = $this->db->query($sql);
							$rides = $query->result_array();
							$query->free_result();
							$pn['CustomLabel'] = '';
							if(!empty($rides)) // Save Record for Fitment
							{
								unset($pn['answer']);
								unset($pn['question']);
								$samePrice = FALSE;
								foreach($rides as $ride)
								{
									$pn['Relationship'] = 'Compatibility';
									$pn['RelationshipDetails'] = $ride['fitment'];
									$fitmentArr[] = $pn;
								}
							}
							elseif(!empty($pn['question'])) // Save record for Variations
							{
								$pn['Relationship'] = 'Variation';
								$pn['RelationshipDetails'] = str_replace(' ', '', $pn['question'] .'='.$pn['answer']);
								unset($pn['answer']);
								unset($pn['question']);
								$categoryRec[] = $pn;		
							}
						}
					}
					if(($samePrice) && (@$categoryRec))	
					{
						
						$part['*Quantity'] = '';
						$part['*StartPrice'] = $basicPrice;
						$finalArray[] = $part;
						foreach($categoryRec as $rb)
						{
							$rb['*StartPrice'] = $basicPrice;
							$finalArray[] = $rb;
						}
					}
					elseif(!empty($categoryRec))
					{
						foreach($categoryRec as $rb)
						{	
							$newArray = $part;
							$newArray['*Quantity'] = $rb['*Quantity'];
							$newArray['*StartPrice'] = $rb['*StartPrice'];
							$rb['RelationshipDetails'] = str_replace('=', '/', $rb['RelationshipDetails']);
							$newArray['*Title'] .= ' - ' .$rb['RelationshipDetails'];
							$finalArray[] = $newArray;
						}
					}
					elseif(!empty($fitmentArr))
					{
						$part['*StartPrice'] = $rb['*StartPrice'];
						$finalArray[] = $part;
						foreach($fitmentArr as $rb)
							$finalArray[] = $rb;
					}
				}
			}	
			
		}
		$csv = $this->array2csv($finalArray);
		return $csv;		
	}
	
	private function eBayCategoryName($categoryName)
	{
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
		if(strpos($categoryName, 'PANT') !== FALSE)
			return 34354;
		if(strpos($categoryName, 'SPROCKET') !== FALSE)
			return 49831;
		if(strpos($categoryName, 'HAT') !== FALSE)
			return 50426;
		if(strpos($categoryName, 'BOOT') !== FALSE)
			return 6751;
		if(strpos($categoryName, 'GLASSES') !== FALSE)
			return 50424;
		if(strpos($categoryName, 'GOGGLES') !== FALSE)
			return 50424;
		if(strpos($categoryName, 'HELMET') !== FALSE)
			return 6749;
		if(strpos($categoryName, 'JACKET') !== FALSE)
			return 6750;
		if(strpos($categoryName, 'HOODY') !== FALSE)
			return 177125;
		if(strpos($categoryName, 'SWEATSHIRT') !== FALSE)
			return 177125;
		if(strpos($categoryName, 'SHIRT') !== FALSE)
			return 6752;
		if(strpos($categoryName, 'TANK TOP') !== FALSE)
			return 6752;
		if(strpos($categoryName, 'RAIN') !== FALSE)
			return 6750;
		if(strpos($categoryName, 'JERSEYS') !== FALSE)
			return 34353;
		if(strpos($categoryName, 'PROTECTION') !== FALSE)
			return 34353;
		if(strpos($categoryName, 'GLOVES') !== FALSE)
			return 34353;
		if(strpos($categoryName, 'TIRES & WHEELS') !== FALSE)
			return 124313;
		if(strpos($categoryName, 'PACKS & BAGS') !== FALSE)
			return 34355;
		if(strpos($categoryName, 'SWIM TRUNKS') !== FALSE)
			return 34353;
		if(strpos($categoryName, 'SHOES') !== FALSE)
			return 6751;
		if(strpos($categoryName, 'HEATED SOCKS') !== FALSE)
			return 6751;
		if(strpos($categoryName, 'RACESUITS') !== FALSE)
			return 6750;
		if(strpos($categoryName, 'HEATED GLOVES') !== FALSE)
			return 50425;
		if(strpos($categoryName, 'HEATED GEAR ACCESSORIES') !== FALSE)
			return 6750;
		if(strpos($categoryName, 'SUITS') !== FALSE)
			return 6750;
		if(strpos($categoryName, 'BASEGEAR & LINERS') !== FALSE)
			return 6750;
		if(strpos($categoryName, 'GEAR BAGS') !== FALSE)
			return 34355;
		if(strpos($categoryName, 'BACKPACKS') !== FALSE)
			return 34355;
		if(strpos($categoryName, 'CHAINS & MASTER LINKS') !== FALSE)
			return 49831;
		if(strpos($categoryName, 'CHEMICALS & OILS') !== FALSE)
			return 111112;
		if(strpos($categoryName, 'TRAILER ACCESSORIES') !== FALSE)
			return 50069;
		if(strpos($categoryName, 'TRAILER ELECTRICAL') !== FALSE)
			return 50069;
		if(strpos($categoryName, 'TRAILER TIRES & WHEELS') !== FALSE)
			return 50071;
		if(strpos($categoryName, 'TRAILERS') !== FALSE)
			return 50072;
		if(strpos($categoryName, 'TRAILERS') !== FALSE)
			return 50072;
		if(strpos($categoryName, 'TOOLS') !== FALSE)
			return 43990;
		if(strpos($categoryName, 'BARS & CONTROLS') !== FALSE)
			return 35564;	
		return $categoryName;
	}
	
	private function eBayStoreCategoryName($categoryName)
	{
		if(strpos($categoryName, 'DIRT BIKE PARTS > CASUAL APPAREL') !== FALSE)
			return 8506710012;
		if(strpos($categoryName, 'SPROCKET') !== FALSE)
			return 8494715012;
		if(strpos($categoryName, 'DIRT BIKE PARTS > RIDING GEAR') !== FALSE)
			return 8506717012;
		if(strpos($categoryName, 'DIRT BIKE PARTS > PROTECTION') !== FALSE)
			return 8506711012;
		if(strpos($categoryName, 'DIRT BIKE PARTS > TRAILERS') !== FALSE)
			return 8506712012;
		if(strpos($categoryName, 'DIRT BIKE PARTS > DIRT BIKE PARTS') !== FALSE)
			return 8506716012;
		if(strpos($categoryName, 'STREET BIKE PARTS > PROTECTION') !== FALSE)
			return 8506718012;
		if(strpos($categoryName, 'STREET BIKE PARTS > STREET BIKE PARTS') !== FALSE)
			return 8506719012;
		if(strpos($categoryName, 'STREET BIKE PARTS > RIDING GEAR') !== FALSE)
			return 8506720012;
		if(strpos($categoryName, 'STREET BIKE PARTS > PACKS') !== FALSE)
			return 8506721012;
		if(strpos($categoryName, 'STREET BIKE PARTS > CASUAL APPAREL') !== FALSE)
			return 8506722012;
		if(strpos($categoryName, 'STREET BIKE PARTS > CHEMICALS') !== FALSE)
			return 8506723012;
		if(strpos($categoryName, 'STREET BIKE PARTS > CHEMICALS') !== FALSE)
			return 8506723012;
		if(strpos($categoryName, 'STREET BIKE PARTS > TRAILERS') !== FALSE)
			return 8506724012;
		if(strpos($categoryName, 'STREET BIKE PARTS > TOOLS') !== FALSE)
			return 8506725012;
		if(strpos($categoryName, 'ATV PARTS > TRAILERS') !== FALSE)
			return 8506726012;
		if(strpos($categoryName, 'ATV PARTS > RIDING GEAR') !== FALSE)
			return 8506717012;
		if(strpos($categoryName, 'ATV PARTS > PROTECTION') !== FALSE)
			return 8506711012;
		if(strpos($categoryName, 'ATV PARTS > TRAILERS') !== FALSE)
			return 8506712012;
		if(strpos($categoryName, 'ATV PARTS > HELMETS & ACCESSORIES > HELMETS') !== FALSE)
			return 8514794012;
		if(strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > JACKETS') !== FALSE)
			return 8514793012;
		if(strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > HOODYS & SWEATSHIRTS') !== FALSE)
			return 8514818012;	
		if(strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > DUAL SPORT HELMETS') !== FALSE)
			return 8514646012;
		if(strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > OPEN FACE HELMETS') !== FALSE)
			return 8514647012;
		if(strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > FULL FACE HELMETS') !== FALSE)
			return 8514648012;
		if(strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > MODULAR HELMETS') !== FALSE)
			return 8514650012;
		if(strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > HALF SHELL HELMETS') !== FALSE)
			return 8514650012;
		if(strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > COMMUNICATION') !== FALSE)
			return 8514653012;	
		if(strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > HELMET CASES & BAGS') !== FALSE)
			return 8514654012;
		if(strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > T-SHIRTS') !== FALSE)
			return 8514812012;	
		if(strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > SWIM TRUNKS') !== FALSE)
			return 8514816012;	
		if(strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > RAIN GEAR') !== FALSE)
			return 8514786012;	
		if(strpos($categoryName, 'ATV PARTS > ATV PARTS > DRIVE > CHAINS & MASTER LINKS > CHAIN') !== FALSE)
			return 8514864012;
		if(strpos($categoryName, 'DIRT BIKE PARTS > CHEMICALS & OILS > ENGINE OIL') !== FALSE)
			return 8514866012;
		if(strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > GEAR OIL') !== FALSE)
			return 8514868012;
		if(strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > SUSPENSION FLUID') !== FALSE)
			return 8514870012;
		if(strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > 2-STROKE OIL') !== FALSE)
			return 8514867012;
		if(strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > CLEANING SUPPLIES') !== FALSE)
			return 8514869012;
		if(strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > BRAKE FLUID') !== FALSE)
			return 8514873012;
		if(strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > AIR FILTER OIL') !== FALSE)
			return 8514871012;
		if(strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > GLUE-SEALANT') !== FALSE)
			return 8514865012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > HAND TOOLS') !== FALSE)
			return 8514743012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > CARB & FUEL TOOLS') !== FALSE)
			return 8514745012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > SUSPENSION TOOLS') !== FALSE)
			return 8514750012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > ELECTRICAL TOOLS') !== FALSE)
			return 8514750012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > ENGINE TOOLS') !== FALSE)
			return 8514744012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > TIRE & WHEEL TOOLS') !== FALSE)
			return 8514753012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > CHAIN TOOLS') !== FALSE)
			return 8514747012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > GRIP TOOLS') !== FALSE)
			return 8514754012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > SECURITY CABLES & LOCKS') !== FALSE)
			return 8514756012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > MOTORCYCLE COVERS') !== FALSE)
			return 8514757012;
		if(strpos($categoryName, 'ATV PARTS > TOOLS > TIE DOWNS & ANCHORS') !== FALSE)
			return 8514755012;
		return $categoryName;
	}
	
	private function eBayCalculateRemainingOunces($weight)
	{
		$lbs = floor($weight);
		$ouncePercentage = $weight - $lbs;
		$ounces = $ouncePercentage * 16;
		return $ounces;
	}
	
	function debug($d){
		echo "<pre>";
		print_r($d);
		echo "</pre>";
	}

}