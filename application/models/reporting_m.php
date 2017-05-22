<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reporting_M extends Master_M {

    function __construct() {
        parent::__construct();
    }

    /*
     *  JLB 05-21-17
     * Why didn't we just make one function instead of making so many duplicate calculations?
     */
    public function getDashboardStatsByHour($start_date_time, $end_date_time) {
        $matches = $this->sub_getDashboardStatsByInterval($start_date_time, $end_date_time, "year(from_unixtime(order_date)) as year, month(from_unixtime(order_date)) as month, day(from_unixtime(order_date)) as day, hour(from_unixtime(order_date)) as hour", "year, month, hour");

        return $this->sub_explodeForAllTime($matches, $start_date_time, $end_date_time, "1 hour");
    }

    protected function sub_explodeForAllTime($matches, $start_date_time, $end_date_time, $time_increment) {

        // now, explode out all the options in the range...
        $out_rows = array();

        // We're going to do a modified merge sort.
        $start_timestamp = strtotime($start_date_time);
        $end_timestamp = strtotime($end_date_time);

        $current_out_row = 0;

        $current_timestamp = $start_timestamp;

        while ($current_timestamp <= $end_timestamp) {
            $year = intVal(date("Y", $current_timestamp));
            $month = intVal(date("m", $current_timestamp));
            $day = intVal(date("d", $current_timestamp));
            $hour = intVal(date("H", $current_timestamp));

            if ($current_out_row < count($matches) && (!array_key_exists("year", $matches[$current_out_row]) || intVal($matches[$current_out_row]["year"]) == $year) && (!array_key_exists("month", $matches[$current_out_row]) || intVal($matches[$current_out_row]["month"]) == $month) && (!array_key_exists("day", $matches[$current_out_row]) || intVal($matches[$current_out_row]["day"]) == $day) && (!array_key_exists("hour", $matches[$current_out_row]) || intVal($matches[$current_out_row]["hour"]) == $hour)) {
                // then we have to shove it along
                $out_rows[] = $matches[$current_out_row];
                $current_out_row++;
            } else {
                $out_rows[] = array(
                    "total_sales_dollars" => 0,
                    "number_orders" => 0,
                    "distinct_customers" => 0,
                    "year" => $year,
                    "month" => $month,
                    "date" => $day,
                    "hour" => $hour
                );
            }

            $current_timestamp = strtotime($time_increment, $current_timestamp);
        }

        return $out_rows;
    }

    public function getDashboardStatsByDay($start_date_time, $end_date_time) {
        $matches = $this->sub_getDashboardStatsByInterval($start_date_time, $end_date_time, "year(from_unixtime(order_date)) as year, month(from_unixtime(order_date)) as month, day(from_unixtime(order_date)) as day", "year, month, day");

        // now, explode out all the options in the range...
        return $this->sub_explodeForAllTime($matches, $start_date_time, $end_date_time, "1 day");
    }

    public function getDashboardStatsByMonth($start_date_time, $end_date_time) {
        $matches = $this->sub_getDashboardStatsByInterval($start_date_time, $end_date_time, "year(from_unixtime(order_date)) as year, month(from_unixtime(order_date)) as month", "year, month");

        // now, explode out all the options in the range...
        return $this->sub_explodeForAllTime($matches, $start_date_time, $end_date_time, "1 month");
    }

    protected function sub_getDashboardStatsByInterval($start_date_time, $end_date_time, $selection_labels, $grouping_labels) {
        $query = $this->db->query("select sum(sales_price) as total_sales_dollars, count(distinct `order`.id) as number_orders, count(distinct user_id) as distinct_customers, $selection_labels from `order` join (select distinct order_id from order_status where status = 'Approved') order_status on `order`.id = order_status.order_id  where order_date >= unix_timestamp(?) and order_date <= unix_timestamp(?) group by $grouping_labels order by $grouping_labels", array($start_date_time, $end_date_time));
        return $query->result_array();
    }

    public function getRevenueWithinDateRange($start_date_time, $end_date_time) {
        $query = $this->db->query("Select sum(sales_price) as cnt from `order` join (select distinct order_id from order_status where status = 'Approved') order_status on `order`.id = order_status.order_id where order_date >= unix_timestamp(?) and order_date <= unix_timestamp(?) ", array($start_date_time, $end_date_time));
        $cnt = 0;
        foreach ($query->result_array() as $row) {
            $cnt = $row['cnt'];
        }
        return $cnt;
    }

    public function getOrdersWithinDateRange($start_date_time, $end_date_time) {
        $cnt = 0;
        $query = $this->db->query("Select count(distinct `order`.id) as cnt from `order` join (select distinct order_id from order_status where status = 'Approved') order_status on `order`.id = order_status.order_id where order_date >= unix_timestamp(?) and order_date <= unix_timestamp(?) ", array($start_date_time, $end_date_time));
        foreach ($query->result_array() as $row) {
            $cnt = $row['cnt'];
        }
        return $cnt;
    }

    public function getCountCustomersforDashboard() {
        $cnt = 0;
        $query = $this->db->query("Select count(distinct user.id) as cnt from user join  contact on user.billing_id = contact.id;", array());
        foreach ($query->result_array() as $row) {
            $cnt = $row['cnt'];
        }
        return $cnt;
    }

    /*
     *
     */
    public function getOrdersPerMonth($monthTS) {
        $year = date("Y", strtotime($monthTS));


        $i = 0;
        $num = array();
        $mn = date('m', $monthTS);
        $st = strtotime(date('Y-m-01', $monthTS));
        $ed = strtotime(date('Y-m-t 23:59:59', $monthTS));
        while ($i < 12) {
            $this->db->where('order_date >=', $st);
            $this->db->where('order_date <=', $ed);
            $this->db->from('order');
            $num[$mn] = $this->db->count_all_results();
            $i++;
            $st = strtotime("-1 month", $st);
            $ed = strtotime(date('Y-m-t', $st));
            $mn = date('m', $st);
        }

        return $num;
    }

    //Custom code to get ever month orders by pradep
    public function getOrdersPerMonthDashboard($monthTS) {
        
        $year = date('Y', strtotime($monthTS));
        $previousYear = ($year-1);
        
        $data = array();
        
        $st = strtotime($previousYear.'-01-01');
        $ed = strtotime($previousYear.'-12-31 23:59:59');

        $query = $this->db->query("Select count(distinct `order`.id) as cnt from `order` join (select * from order_status where status = 'Approved') order_status on `order`.id = order_status.order_id where order_date >= ? and order_date <= ?", array($st, $ed));
        foreach ($query->result_array() as $row) {
            $data[$previousYear] = $row['cnt'];
        }

        $st = strtotime(date('Y').'-01-01');
        $ed = strtotime(date('Y').'-12-31 23:59:59');


        $query = $this->db->query("Select count(distinct `order`.id) as cnt from `order` join (select * from order_status where status = 'Approved') order_status on `order`.id = order_status.order_id where order_date >= ? and order_date <= ?", array($st, $ed));
        foreach ($query->result_array() as $row) {
            $data[date('Y')] = $row['cnt'];
        }

        return $data;
    }
    //End Pradeep Custom Code
    
    //Custom code to get customers by pradep
    public function getCusomersPerMonthDashboard() {
        //$this->db->where('user_type', 'customer');
        $this->db->from('user');
        $num = $this->db->count_all_results();
        return $num;
    }
    //End Pradeep Custom Code
    
    //Custom code to get reviews by pradep
    public function getTotalReviews() {
        $where = array('approval_id IS NULL' => NULL);
        $this->db->where('approval_id IS NULL', NULL);
        $this->db->where('user_id IS NOT NULL', NULL);
        $this->db->from('reviews');
        $num = $this->db->count_all_results();
        return $num;
    }
    //End Pradeep Custom Code
    
    public function getOrderForMonthChart() {
        $month = date('m');
        $number = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $i = 1;
        $num = array();
        while ($i <= $number) {
            $dt = date('Y-m-').$i;
            $st = strtotime($dt);
            $ed = strtotime($dt.' 23:59:59');
            
            $this->db->where('order_date >', $st);
            $this->db->where('order_date <', $ed);
            
            $this->db->from('order');
            $num[$i] = $this->db->count_all_results();
            $i++;
        }
        return $num;
    }
    
    public function getOrderForDailyChart() {
        $month = date('m');
        $number = 23;
        $i = 0;
        $num = array();
        while ($i <= $number) {
            $dt = date('Y-m-d');
            $st = strtotime($dt.' '.$i.':00:00');
            $ed = strtotime($dt.' '.$i.':59:59');
            
            $this->db->where('order_date >= ', $st);
            $this->db->where('order_date <= ', $ed);
            
            $this->db->from('order');
            $num[$i] = $this->db->count_all_results();
            $i++;
        }
        return $num;
    }
    
    public function getOrderForWeeklyChart() {
        $date = date('d')-date("N");
        //$date = date('d', strtotime(date('Y-m-d').' -'.date("N").' days'));//date('');
        $number = ($date+7);
        $i = $date;
        $num = array();
        while ($i < $number) {
            $dt = date('Y-m-').$i;
            $st = strtotime($dt);
            $ed = strtotime($dt.' 23:59:59');
            
            $this->db->where('order_date >= ', $st);
            $this->db->where('order_date <= ', $ed);
            
            $this->db->from('order');
            $num[$i] = $this->db->count_all_results();
            $i++;
        }
        return $num;
    }
    
    public function getOrderForYearlyChart() {
        $number = 13;
        $i = 01;
        $num = array();
        while ($i < $number) {
            $dt = date('Y-').sprintf("%02d", $i).'-01';
            $st = strtotime($dt);
            $ed = strtotime(date('Y-').sprintf("%02d", $i).'-30'.' 23:59:59');
            
            $this->db->where('order_date >= ', $st);
            $this->db->where('order_date <= ', $ed);
            
            $this->db->from('order');
            $num[sprintf("%02d", $i)] = $this->db->count_all_results();
            $i++;
        }
        return $num;
    }
    
    
//    public function getTotalRevenue($monthTS) {
//
//        $year = date('Y', strtotime($monthTS));
//        $previousYear = ($year-1);
//
//        $data = array();
//
//        $st = strtotime($previousYear.'-01-01');
//        $ed = strtotime($previousYear.'-12-31 23:59:59');
//
//        $this->db->where('order_date >= ', $st);
//        $this->db->where('order_date <= ', $ed);
//        //$this->db->from('order');
//        $this->db->select('sum(sales_price) as total');
//        $record = $this->db->get('order');
//        $total = $record->row_array();
//        $data[$previousYear] = $total['total'];
//
//        $st = strtotime(date('Y').'-01-01');
//        $ed = strtotime(date('Y').'-12-31');
//
//        $this->db->where('order_date >', $st);
//        $this->db->where('order_date <', $ed);
//        //$this->db->from('order');
//        $this->db->select('sum(sales_price) as total');
//        $record = $this->db->get('order');
//        $total = $record->row_array();
//        $data[date('Y')] = $total['total'];
//        //$data[date('Y')] = $this->db->count_all_results();
//        return $data;
//    }
    
    private function array2csv(array &$array) {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    public function getProductsForXML() {
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

        print_r($partnumbers);
        die;
        $query->free_result();
        $csv = $this->array2csv($partnumbers);
        return $csv;
    }

    public function array_to_pipe_download($array, $filename = 'test.csv', $delimiter = "|", $type = '') {

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        $f = fopen('php://output', 'w');
        $x = 1;
        foreach ($array as $line) {
            echo '<pre>';
            print_r($line);
            die("****");
            if ($x == 1) {

                $header = array_keys($line);
                fputcsv($f, $header, $delimiter);
            }
            fputcsv($f, array_values($line), $delimiter);
            $x++;
        }
    }

    public function getProductForcraglist() {
        
    }

    public function get_category_name($category_id) {
        $sql = "SELECT motorcycle_category.name  from motorcycle_category where motorcycle_category.id = '" . $category_id . "'";
        $query = $this->db->query($sql);
        $category = $query->result_array();
        if (isset($category[0]['name']) && !empty($category[0]['name'])) {
            return $category[0]['name'];
        } else {
            return;
        }
    }

    public function get_motercycle_type_name($type_id) {
        $sql = "SELECT motorcycle_type.name  from motorcycle_type where motorcycle_type.id = '" . $type_id . "'";
        $query = $this->db->query($sql);
        $type = $query->result_array();
        if (isset($type[0]['name']) && !empty($type[0]['name'])) {
            return $type[0]['name'];
        } else {
            return;
        }
    }

    public function get_dealer_info() {
        $sql = "SELECT contact.*  from contact where environment = 'sandbox' ";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data[0];
    }

    public function get_motercycle_image($moter_cycle_id) {
        $sql = "SELECT motorcycleimage.image_name  from motorcycleimage WHERE motorcycleimage.motorcycle_id = '" . $moter_cycle_id . "'";
        $query = $this->db->query($sql);
        $motorcycleimages = $query->result_array();
        if (!empty($motorcycleimages)) {
            return $motorcycleimages;
        } else {
            return;
        }
    }

    public function getProductForcycletrader() {
        $sql = "SELECT motorcycle.*  from motorcycle where cycletrader_feed_status =" . TRUE;
        $query = $this->db->query($sql);
        $allmotorcycle = $query->result_array();
        $header = 'uniqueadindentifier|classid|categoryname|manufacturer|model|year|price|newused|itemurl|miles|engine|weight|primarycolor|secondarycolor|stocknumber|description|dealername|dealerlocalphone|adlocation|dealercity|dealerstate|dealerpostalcode|dealerareacode|dealerurl|photo1|photo2|photo3|photo4|photo5|photo6|photo7|photo8|photo9|photo10|photo11|photo12|photo13|photo14|photo15|photo16|photo17|photo18|photo19|photo20|photo21|photo22|photo23|photo24|photo25';
        $header = $header . PHP_EOL;
        $file_path = STORE_DIRECTORY . '/cycletraderFeed/cycle_trader_feed.txt';
        unlink($file_path);
        $dealer_info = $this->get_dealer_info();
        $dealer_name = $dealer_info['company'];
        $dealer_phone_no = $dealer_info['phone'];
        $dealer_location = $dealer_info['country'];
        $dealer_city = $dealer_info['city'];
        $dealer_state = $dealer_info['state'];
        $dealer_post_code = $dealer_info['zip'];
        $dealer_area_code = 844;
        file_put_contents($file_path, $header);
        
        foreach ($allmotorcycle as $key => $motorcycle) {
            if ($motorcycle['vehicle_type'] == 1) {
                $classid = 528553;
            } elseif ($motorcycle['vehicle_type'] == 2) {
                $classid = 1049211046;
            } elseif ($motorcycle['vehicle_type'] == 3) {
                $classid = 356953;
            } elseif ($motorcycle['vehicle_type'] == 4) {
                $classid = 356953;
            } elseif ($motorcycle['vehicle_type'] == 5) {
                $classid = 9404794;
            } elseif ($motorcycle['vehicle_type'] == 6) {
                $classid = 301857;
            } elseif ($motorcycle['vehicle_type'] == 7) {
                $classid = 1049211046;
            }





            $title = str_replace(' ', '_', trim($motorcycle['title']));
            $motercycle_type = $this->get_motercycle_type_name($motorcycle['vehicle_type']);
            $motercycle_images = $this->get_motercycle_image($motorcycle['id']);
            $desc = preg_replace("/\r|\n/", "", $motorcycle['description']);

            if ($motorcycle['condition'] == 1) {
                $condition = 'New';
            } elseif ($motorcycle['condition'] == 2) {
                $condition = 'Used';
            }

            $data = array();
            $data['uniqueadindentifier'] = $motorcycle['sku'];
            $data['classid'] = $classid;
            $data['categoryname'] = $this->get_category_name($motorcycle['category']);
            $data['manufacturer'] = $motorcycle['make'];
            $data['model'] = $motorcycle['model'];
            $data['year'] = $motorcycle['year'];
            $data['price'] = $motorcycle['sale_price'];
            $data['newused'] = $condition;
            $data['itemurl'] = base_url(strtolower($motercycle_type) . '/' . $title . '/' . $motorcycle['sku']);
            $data['miles'] = $motorcycle['mileage'];
            $data['engine'] = $motorcycle['engine_type'];
            $data['weight'] = '';
            $data['primarycolor'] = $motorcycle['color'];
            $data['secondarycolor'] = '';
            $data['stocknumber'] = $motorcycle['sku'];
            $data['description'] = '"' . $desc . '"';
            $data['dealername'] = $dealer_name;
            $data['dealerlocalphone'] = $dealer_phone_no;
            $data['adlocation'] = '';
            $data['dealercity'] = $dealer_city;
            $data['dealerstate'] = $dealer_state;
            $data['dealerpostalcode'] = $dealer_post_code;
            $data['dealerareacode'] = $dealer_area_code;
            $data['dealerurl'] = base_url();
            for ($x = 1; $x <= 25; $x++) {
                if (isset($motercycle_images[$x - 1]) && !empty($motercycle_images[$x - 1])) {
                    $data['Photo_' . $x] = base_url('media' . '/' . $motercycle_images[$x - 1]['image_name']);
                } else {
                    $data['Photo_' . $x] = '';
                }
            }

            $values = strip_tags(implode("|", $data));
            $values = $values . PHP_EOL;
            file_put_contents($file_path, $values, FILE_APPEND);
        }
//        header('Content-Type: application/octet-stream');
//        header('Content-Disposition: attachment; filename=' . $file_path);
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate');
//        header('Pragma: public');
//        header('Content-Length: ' . filesize($file_path));
//        readfile($file_path);
//        exit;
//
//        die;
////         unlink($file_path);
//        die("******");
    }

	public function getQuestionAnswerByNumberFeed($partId, $partnumber){
        $where = array('partquestion.part_id' => $partId, 'productquestion' => 0, "answer != ''" => NULL);
        if (@$activeMachine['model']['model_id']) {
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        if (@$activeMachine['year']) {
            $where['partnumbermodel.year'] = $activeMachine['year'];
        }
		if( $partnumber != '' ) {
			$where['partnumber.partnumber'] = $partnumber;
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
        $partNumberRecs = $this->selectRecord('partnumberpartquestion');
		//echo $this->db->last_query();exit;
		return $partNumberRecs;
	}
	
    public function getProductsForGoogle($handle = null)
    {
        $this->sub_getProductsForGoogle($handle, 0, is_null($handle) ? 0 : 5000);
    }

    public function sub_getProductsForGoogle($handle, $offset = 0, $limit = 0) {
        $part_count = 0;
        $limit_string = "";
        if ($limit > 0) {
            // what's the count?
            $sql = "Select count(*) as cnt FROM (Select distinct part.name, partnumber.partnumber_id, part.part_id FROM part
					JOIN partpartnumber ON partpartnumber.part_id = part.part_id
					JOIN partnumber ON partnumber.partnumber_id = partpartnumber.partnumber_id
					JOIN partnumberpartquestion ON partnumberpartquestion.partnumber_id = partnumber.partnumber_id
					JOIN partimage ON partimage.part_id = partpartnumber.part_id
					JOIN partcategory ON partcategory.part_id = partpartnumber.part_id
					JOIN category ON category.category_id = partcategory.category_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					LEFT JOIN partdealervariation ON partdealervariation.partnumber_id = partnumber.partnumber_id
					JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
					JOIN brand ON brand.brand_id = partbrand.brand_id
					WHERE sale > 0 AND partnumber.price != 0) AS CountTab";
            $query = $this->db->query($sql);
            foreach ($query->result_array() as $row) {
                $part_count = $row['cnt'];
            }
            $limit_string = " LIMIT $limit OFFSET $offset ";
        }

        if ($part_count < $offset) {
            return;
        }

        //partnumber.promotion_id AS promotion_id
        //CASE WHEN partnumber.closeout_market_place = 0 THEN 'current' ELSE 'closeout' END AS custom_lable_0,
        //CASE WHEN answer = 'mens' THEN part.name WHEN answer = 'womens' THEN part.name WHEN answer = 'boys' THEN part.name WHEN answer = 'girls' THEN part.name WHEN answer != '' THEN CONCAT (part.name, ' - ', answer) ELSE part.name END AS title,
        //partvariation.manufacturer_part_number AS 'mpn',
//		partnumber.sale AS 'price',
        $sql = "SELECT  
						   partnumber.partnumber_id AS ref_id,
						   partnumber.partnumber AS id,
						   answer,
						   part.name AS title,
						   CASE WHEN answer != '' THEN CONCAT (part.name, ' - ', answer) ELSE part.name END AS description,
						   category.long_name AS product_type,
						   brand.name AS 'brand',
						   partvariation.part_number AS 'mpn',
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS link,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/shopping/item/', part.part_id) AS mobile_link,
						   CONCAT ('http://" . WEBSITE_HOSTNAME . "/productimages/', partimage.path) AS image_link,
						   'new' AS 'condition',
						   CASE WHEN partvariation.quantity_available > 0 THEN 'in stock' ELSE 'out of stock' END AS 'availability',
						   CASE WHEN partdealervariation.quantity_available > 0 THEN 'in stock' ELSE 'out of stock' END AS 'dealeravailability',
						   CASE WHEN partdealervariation.quantity_available > 0 THEN partnumber.dealer_sale ELSE partnumber.sale END AS 'price',
						   CASE WHEN partnumber.weight != '' THEN partnumber.weight ELSE '0.5' END AS 'weight',
						   
						   CASE WHEN partvariation.stock_code != 'Closeout' THEN 'current' ELSE 'closeout' END AS custom_label_0,
						   
						   part.part_id AS part_own_id,
						   brand.promotion_data AS promotion_data,
						   brand.brand_id

					FROM part
					JOIN partpartnumber ON partpartnumber.part_id = part.part_id
					JOIN partnumber ON partnumber.partnumber_id = partpartnumber.partnumber_id
					JOIN partnumberpartquestion ON partnumberpartquestion.partnumber_id = partnumber.partnumber_id
					JOIN partimage ON partimage.part_id = partpartnumber.part_id
					JOIN partcategory ON partcategory.part_id = partpartnumber.part_id
					JOIN category ON category.category_id = partcategory.category_id
					JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
					LEFT JOIN partdealervariation ON partdealervariation.partnumber_id = partnumber.partnumber_id
					JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
					JOIN brand ON brand.brand_id = partbrand.brand_id
					WHERE sale > 0 AND partnumber.price != 0
					GROUP BY title, ref_id, part_own_id order by title $limit_string";

        //GROUP BY title, ref_id, part_own_id order by title";
        //echo $sql;exit;
        $query = $this->db->query($sql);
        $partnumbers = $query->result_array();

        $query->free_result();
        $cpn = array();
        $qry = $this->db->query("select * from coupon where active=1 and google_promotion=1");
        $coupons = $qry->result_array();
        $qry->free_result();
        if ($coupons) {
            foreach ($coupons as $k => $v) {
                $cpn[$v['brand_id']][] = array('coupon' => $v['couponCode'], 'closeout' => $v['closeout']);
            }
        }

        $allCurrentCoupons = null;
        $allCoupons = null;
        if (isset($cpn[0])) {
            foreach ($cpn[0] as $k => $v) {
                if ($v['closeout'] == 1) {
                    $allCurrentCoupons .= ' ' . $v['coupon'];
                    $allCoupons .= ',' . $v['coupon'];
                } else {
                    $allCurrentCoupons .= ' ' . $v['coupon'];
                }
            }
        }
        //$allCoupon = implode(',', $cpn[0]);
        //unset($cpn[0]);
        $allCurrentCoupons = trim($allCurrentCoupons);
        $allCoupons = trim($allCoupons);
        //echo $allCurrentCoupons.'<br>'.$allCoupons;
        // echo '<pre>';
        // print_r($partnumbers);
        // echo '</pre>';exit;
        $productsArr = array();
        $partnumbers1 = array();

        // //
        // $qry = "SELECT partpartnumber.partnumber_id, `partquestion`.`part_id` as part_id
        // FROM (`partnumberpartquestion`)
        // JOIN `partnumber` ON `partnumber`.`partnumber_id` = `partnumberpartquestion`.`partnumber_id`
        // LEFT JOIN `partnumbermodel` ON `partnumbermodel`.`partnumber_id` = `partnumber`.`partnumber_id`
        // JOIN `partpartnumber` ON `partpartnumber`.`partnumber_id` = `partnumber`.`partnumber_id`
        // JOIN `partquestion` ON `partquestion`.`partquestion_id` = `partnumberpartquestion`.`partquestion_id`
        // WHERE `productquestion` =  0
        // AND  (partnumber.universalfit > 0 OR partnumbermodel.partnumbermodel_id is not null) 
        // group by `question`";
        // //group by `question`";
        // //echo $qry;exit;
        // $query = $this->db->query($qry);
        // $ptnum = $query->result_array();
        // $query->free_result();
        // //echo '<pre>';
        // //print_r($ptnum);
        // //echo '</pre>';exit;
        // if(count($ptnum) > 0) {
        // $parts1 = array();
        // foreach($ptnum as $krec => $rec) {
        // if($rec['part_id'] != $rec['pid']) {
        // }
        // //$arr = explode(',', $rec['part_ids']);
        // $sql = "SELECT part.part_id
        // FROM part
        // JOIN partpartnumber ON partpartnumber.part_id = part.part_id
        // WHERE partpartnumber.partnumber_id = '".$rec['partnumber_id']."'";
        // $query = $this->db->query($sql);
        // $results = $query->result_array();
        // $query->free_result();
        // //if(!in_array(@$results[0]['part_id'], $parts1[$rec['part_id']])) {
        // //$parts1[$rec['part_id']][] = @$results[0]['part_id'];
        // //}
        // }
        // }
        //echo '<pre>';
        //print_r($partnumbers);
        //echo '</pre>';exit;
        //
			
		$gender_arr = array('mens', 'womens', 'boys', 'girls');

        if ($partnumbers) {
            foreach ($partnumbers as &$part) {

                //$part['title'] = trim( $part['title'], ' - MENS');
                //$part['title'] = trim( $part['title'], ' - WOMENS');
                //$part['title'] = trim( $part['title'], ' - BOYS');
                //$part['title'] = trim( $part['title'], ' - GIRLS');
                //$combopartIds = $this->checkForComboReporting($part['part_own_id']);

                if ($part['dealeravailability'] == 'in stock') {
                    $part['availability'] = $part['dealeravailability'];
                }
                unset($part['dealeravailability']);

                if (!empty($parts1[$part['part_own_id']])) {
                    $PriceArr = array();
                    $finalPriceArr = array('retail_min' => 0, 'retail_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
                    foreach ($parts1[$part['part_own_id']] as $id)
                        $PriceArr[] = $this->getPriceRangeReporting($id, null, FALSE);

                    $sale_min = 0;
                    foreach ($PriceArr as $pa) {
                        $sale_min = $sale_min + $pa['sale_min'];
                    }
                    $part['price'] = $sale_min;
                }

                $combopartIds = $this->checkForComboReporting($part['part_own_id']);
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
                    $part['price'] = $this->calculateMarkupReporting($finalPriceArr['retail_min'], $finalPriceArr['retail_max'], $finalPriceArr['sale_min'], $finalPriceArr['sale_max'], @$_SESSION['userRecord']['markup'], $finalPriceArr['dealer_sale_min'], $finalPriceArr['dealer_sale_max'], $finalPriceArr['cnt'])['sale_min'];
                }

                $currentCoupons = null;
                $coupons = null;
                $part['custom_label_1'] = "";
                $part['google_product_category'] = "";
                $part['promotion_id'] = "";

                if (!array_search($part['answer'], $a)) {
					$prt = $this->getQuestionAnswerByNumberFeed($part['part_own_id'], $part['id']);
                    $part['title'] = $part['title'] . ' - ' . $prt['answer'];
                }
                unset($part['answer']);

                $tempArr = array();
                if (array_key_exists($part['brand_id'], $cpn)) {
                    foreach ($cpn[$part['brand_id']] as $key => $val) {
                        if ($part['custom_label_0'] == 'closeout' && $val['closeout'] == 1) {
                            $coupons .= ' ' . $val['coupon'];
                        } else {
                            $currentCoupons .= ' ' . $val['coupon'];
                        }
                    }
                }
                $currentCoupons = trim($currentCoupons);
                $coupons = trim($coupons);
                unset($part['brand_id']);
                if ($part['custom_label_0'] == 'closeout') {
                    $part['promotion_id'] = trim($allCoupons . ' ' . $coupons);
                } else {
                    $part['promotion_id'] = trim($allCurrentCoupons . ' ' . $currentCoupons);
                }
                //$part['coupons'] = trim($allCoupon.','.$tempArr, ',');

                $get_least_specific_category_sql = "SELECT long_name, google_category_num FROM category WHERE category.category_id
													IN (SELECT partcategory.category_id FROM partcategory WHERE partcategory.part_id=" . $part['part_own_id'] . ")
													ORDER BY LENGTH(category.long_name) DESC LIMIT 1";
                $get_least_specific_category = $this->db->query($get_least_specific_category_sql);
                if ($get_least_specific_category->num_rows() > 0) {
                    $cat_array = explode('>', $get_least_specific_category->row()->long_name);
                    if (count($cat_array) > 1) {
                        $part['custom_label_1'] = $cat_array[0] . ' > ' . end($cat_array);
                    } else {
                        $part['custom_label_1'] = $get_least_specific_category->row()->long_name;
                    }
                    $part['google_product_category'] = $get_least_specific_category->row()->google_category_num;
                }

                $get_least_specific_category->free_result();

                if (!empty($part['promotion_data'])) {
                    $exploded_promotion_data = explode("_*_*_", $part['promotion_data']);
                    if ($exploded_promotion_data[1] == 1 && !empty($exploded_promotion_data[0]) && $part['custom_label_0'] == 'closeout') {
                        //$part['promotion_id'] = $exploded_promotion_data[0];
                    } elseif ($exploded_promotion_data[1] == 0 && !empty($exploded_promotion_data[0]) && $part['custom_label_0'] == 'current') {
                        //$part['promotion_id'] = $exploded_promotion_data[0];
                    }
                }

                if (strpos($part['title'], 'COMBO') !== FALSE)
                    $part['id'] .= 'C';
                $rides = array();

                $sql = "SELECT CONCAT (make.name, ' ',  model.name, ' ', partnumbermodel.year) AS fitment 
							FROM (`partnumbermodel`) 
							JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
							JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
							WHERE `partnumbermodel`.`partnumber_id` =  '" . $part['ref_id'] . "'";
                $query = $this->db->query($sql);
                $rides = $query->result_array();
                $query->free_result();

                if ($rides) {
                    foreach ($rides as $ride) {
                        $part['description'] .= ' / ' . $ride['fitment'];
                    }
                }
                unset($part['ref_id']);
                unset($part['part_own_id']);
                unset($part['promotion_data']);

                //if( !$this->check_array_duplicacy($part['title'], $productsArr) ) {
                if (end($productsArr) != $part['title']) {
                    $part['title'] = str_replace(PHP_EOL, '', $part['title']);
                    $part['description'] = str_replace(PHP_EOL, '', $part['description']);
                    $part['mpn'] = str_replace(PHP_EOL, '', $part['mpn']);
                    //$part['description'] = nl2br($part['description']);
                    $productsArr[] = $part['title'];
                    $partnumbers1[] = $part;
                }
//                echo '<pre>';
//                print_r($partnumbers);
//                die("*******");
            }
        }


        // echo '<pre>';
        // print_r( $partnumbers1);
        // echo '</pre>';
        // exit;
        //return $partnumbers;
        if (is_null($handle)) {
            $csv = $this->array2csv($partnumbers);
            $csv = str_replace('"', '', $csv);
            return $csv;
        } else {
            if ($offset == 0) {
                $keys = array_keys(reset($partnumbers));
                fputcsv($handle, $keys);
            }

            foreach ($partnumbers as $row) {
                fputcsv($handle, $row);
            }
        }

        if ($limit > 0) {
            return $this->sub_getProductsForGoogle($handle, $offset + $limit, $limit);
        }
    }

    public function check_array_duplicacy($title, $arr) {
        //$arr = array_reverse($arr);
        //foreach($arr as $k => $v) {
        if (end($arr) == $title) {
            return true;
        }
        //}
        return false;
    }

    public function getProductsForFB() {
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

        if ($partnumbers) {
            foreach ($partnumbers as &$part) {
                if (strpos($part['title'], 'COMBO') !== FALSE)
                    $part['id'] .= 'C';
                $part['title'] = strtolower($part['title']);
                $part['title'] = ucwords($part['title']);
                $rides = array();

                $sql = "SELECT CONCAT (make.name, ' ',  model.name, ' ', partnumbermodel.year) AS fitment 
							FROM (`partnumbermodel`) 
							JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
							JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
							WHERE `partnumbermodel`.`partnumber_id` =  '" . $part['ref_id'] . "'";
                $query = $this->db->query($sql);
                $rides = $query->result_array();
                $query->free_result();

                if ($rides) {
                    foreach ($rides as $ride) {
                        $part['description'] .= ' / ' . $ride['fitment'];
                    }
                }
                unset($part['ref_id']);
            }
        }
        $csv = $this->array2csv($partnumbers);
        $csv = str_replace('"', '', $csv);
        return $csv;
    }

    public function getProductsForBing() {
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

        if ($partnumbers) {
            foreach ($partnumbers as &$part) {
                if (strpos($part['title'], 'COMBO') !== FALSE)
                    $part['id'] .= 'C';
                $rides = array();

                $sql = "SELECT CONCAT (make.name, ' ',  model.name, ' ', partnumbermodel.year) AS fitment 
							FROM (`partnumbermodel`) 
							JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
							JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
							WHERE `partnumbermodel`.`partnumber_id` =  '" . $part['ref_id'] . "'";
                $query = $this->db->query($sql);
                $rides = $query->result_array();
                $query->free_result();

                if ($rides) {
                    foreach ($rides as $ride) {
                        $part['description'] .= ' / ' . $ride['fitment'];
                    }
                }
                unset($part['ref_id']);
            }
        }

        return $partnumbers;
    }

    public function getAppeagleAmazonXML() {
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
        if ($partnumbers) {
            foreach ($partnumbers as $part) {
                $this->db->select('MIN(brand.map_percent) as map_percent');
                $where = array('partbrand.part_id' => $part['part_id'], 'brand.map_percent > ' => 0);
                $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
                $brand_map_percent = $this->selectRecord('brand', $where);
                $brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : 0;

                if (($brandMAPPercent > 0) && ($part['stock_code'] != 'Closeout')) {
                    $mapPrice = (((100 - $brandMAPPercent) / 100) * $part['price']);
                    if ($mapPrice > $part['MIN_PRICE'])
                        $part['MIN_PRICE'] = $mapPrice;
                }
                if ($part['MIN_PRICE'] > $part['MAX_PRICE'])
                    $part['MAX_PRICE'] = $part['MIN_PRICE'];

                if (!empty($part['partnumber_closeout_market_place'])) {
                    // Checking Product as First Priority
                    if ($part['stock_code'] != 'Closeout') {
                        $part['INVENTORY'] = 0;
                    }
                } elseif (!empty($part['closeout_market_place'])) {
                    // Checking Brand as Second Priority
                    if ($part['stock_code'] != 'Closeout') {
                        $part['INVENTORY'] = 0;
                    }
                }

                if (!empty($part['partnumber_exclude_market_place'])) {
                    // Checking Product as First Priority
                    $part['INVENTORY'] = 0;
                } elseif (!empty($part['exclude_market_place'])) {
                    // Checking Brand as Second Priority
                    $part['INVENTORY'] = 0;
                }

                $finalArr[] = array(
                    'SKU' => $part['Distributor Part Number'],
                    'INVENTORY' => $part['INVENTORY'],
                    'MARKETPLACE_ID' => $part['MARKETPLACE_ID'],
                    'MIN_PRICE' => number_format($part['MIN_PRICE'], 2),
                    'MAX_PRICE' => $part['MAX_PRICE'],
                    'CURRENT_SHIPPING' => $part['CURRENT_SHIPPING']/* ,
                          'closeout_market_place' => $part['closeout_market_place'],
                          'exclude_market_place' => $part['exclude_market_place'],
                          'brand_id' => $part['brand_id'],
                          'stock_code' => $part['stock_code'],
                          'partnumber_id' => $part['partnumber_id'],
                          'part_id' => $part['part_id'],
                          'partnumber_closeout_market_place' => $part['partnumber_closeout_market_place'],
                          'partnumber_exclude_market_place' => $part['partnumber_exclude_market_place'] */
                );
                $finalArr[] = array(
                    'SKU' => $part['Manufacturer Name'] . ' ' . $part['Manufacturer Part Number'],
                    'INVENTORY' => $part['INVENTORY'],
                    'MARKETPLACE_ID' => $part['MARKETPLACE_ID'],
                    'MIN_PRICE' => $part['MIN_PRICE'],
                    'MAX_PRICE' => $part['MAX_PRICE'],
                    'CURRENT_SHIPPING' => $part['CURRENT_SHIPPING']/* ,
                          'closeout_market_place' => $part['closeout_market_place'],
                          'exclude_market_place' => $part['exclude_market_place'],
                          'brand_id' => $part['brand_id'],
                          'stock_code' => $part['stock_code'],
                          'partnumber_id' => $part['partnumber_id'],
                          'part_id' => $part['part_id'],
                          'partnumber_closeout_market_place' => $part['partnumber_closeout_market_place'],
                          'partnumber_exclude_market_place' => $part['partnumber_exclude_market_place'] */
                );
            }
        }

        return $finalArr;
    }

    public function getAppEagleVariationOne() {
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

    public function getProductsForSaleZilla() {
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
        if ($partnumbers) {
            foreach ($partnumbers as &$part) {
                $part['Description'] = preg_replace("/&#?[a-z0-9]+;/i", '', $part['Description']);
                $part['Description'] = strip_tags($part['Description']);
                $part['Description'] = str_replace(',', ' ', $part['Description']);
                $sql2 = 'SELECT * 
							FROM partnumberpartquestion 
							JOIN partquestion ON partquestion.partquestion_id = partnumberpartquestion.partquestion_id
							WHERE partnumber_id = ' . $part['Unique ID'] . ' AND productquestion = 0';
                $query = $this->db->query($sql2);
                $partquestions = $query->result_array();
                $query->free_result();
                if (is_array($partquestions)) {
                    $titleArr = array();
                    foreach ($partquestions as $question) {
                        $titleArr[$question['answer']] = $question['answer'];
                        switch ($question['question']) {
                            case 'COLOR / SIZE':
                                $answerArr = explode(' / ', $question['answer']);
                                if (@$answerArr[1]) {
                                    $part['Color'] = $answerArr[0];
                                    $part['Size'] = $answerArr[1];
                                }
                                break;
                        }
                    }
                    if (!empty($titleArr)) {
                        foreach ($titleArr as $title) {
                            $part['Title'] .= ' ' . $title;
                        }
                    }
                }
            }
        }
        return $partnumbers;
    }

    public function ebayListings($offset = 0, $limit = 1000,$return_csv = FALSE) {
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
						LIMIT " . $offset . ", " . $limit . ";";
       
        $query = $this->db->query($sql);
        
        $parts = $query->result_array();
        $query->free_result();
        if (is_array($parts)) {
            foreach ($parts as &$part) {

                if (strpos($part['*Title'], 'COMBO') !== FALSE)
                    continue;
                $part_id = $part['part_id'];
                unset($part['part_id']);
                /*                 * ***********************************
                  Get Categories with longest string count
                 * ************************************ */
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
                if ($categories) {
                    foreach ($categories as $cat) {
                        if (strlen($cat['long_name']) > $endCategoryName)
                            $endCategoryName = $cat['long_name'];
                    }
                }
                // If no category, don't list the product
                if (empty($endCategoryName))
                    break;

                $part['*Category'] = $this->eBayCategoryName($endCategoryName);
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
					WHERE part.part_id = " . $part_id . "
					AND partvariation.quantity_available > 3
					GROUP BY partnumber.partnumber";
                $query = $this->db->query($sql);
                $partnumbers = $query->result_array();
                $query->free_result();
                if (is_array($partnumbers)) {
                    $categoryRec = array();
                    $fitmentArr = array();
                    $basicPrice = 0;
                    $samePrice = TRUE;
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
                                unset($pn['question']);
                                $categoryRec[] = $pn;
                            }
                        }
                    }
                    if (($samePrice) && (@$categoryRec)) {

                        $part['*Quantity'] = '';
                        $part['*StartPrice'] = $basicPrice;
                        $finalArray[] = $part;
                        foreach ($categoryRec as $rb) {
                            $rb['*StartPrice'] = $basicPrice;
                            $finalArray[] = $rb;
                        }
                    } elseif (!empty($categoryRec)) {
                        foreach ($categoryRec as $rb) {
                            $newArray = $part;
                            $newArray['*Quantity'] = $rb['*Quantity'];
                            $newArray['*StartPrice'] = $rb['*StartPrice'];
                            $rb['RelationshipDetails'] = str_replace('=', '/', $rb['RelationshipDetails']);
                            $newArray['*Title'] .= ' - ' . $rb['RelationshipDetails'];
                            $finalArray[] = $newArray;
                        }
                    } elseif (!empty($fitmentArr)) {
                        $part['*StartPrice'] = $rb['*StartPrice'];
                        $finalArray[] = $part;
                        foreach ($fitmentArr as $rb)
                            $finalArray[] = $rb;
                    }
                }
            }
        }
        if($return_csv){
            return $finalArray;
        }
        $csv = $this->array2csv($finalArray);
        return $csv;
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

    private function eBayCalculateRemainingOunces($weight) {
        $lbs = floor($weight);
        $ouncePercentage = $weight - $lbs;
        $ounces = $ouncePercentage * 16;
        return $ounces;
    }

    function debug($d) {
        echo "<pre>";
        print_r($d);
        echo "</pre>";
    }

    // public function getPriceRangeReporting($partId, $activeMachine = NULL, $checkCombo = TRUE) {
    // $combopartIds = FALSE;
    // $where = array('partpartnumber.part_id' => $partId);
    // $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
    // $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
    // $this->db->where('partnumber.price > 0');
    // $this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN 0 ELSE 1 END )");
    // $this->db->select('partnumber, MIN(partnumber.sale) AS sale_min');
    // $this->db->group_by('part_id');
    // $partNumberRec = $this->selectRecord('partnumber', $where);
    // return $partNumberRec;
    // }

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

    // public function calculateMarkupReporting($retailmin, $retailmax = 0, $min, $max = 0, $userMarkUp = NULL) {
    // $returnArr = array('retail_min' => $retailmin, 'retail_max' => $retailmax);
    // if (@$userMarkUp) {
    // $userMin = (($retailmin * $userMarkUp) / 100) + $retailmin;
    // $userMax = (($retailmax * $userMarkUp) / 100) + $retailmax;
    // if ($userMin < $min)
    // $min = $userMin;
    // if ($userMax < $max)
    // $max = $userMax;
    // }
    // if ($min == $max) {
    // $returnArr['sale_min'] = $min;
    // $returnArr['sale_max'] = FALSE;
    // } else {
    // $returnArr['sale_min'] = $min;
    // $returnArr['sale_max'] = $max;
    // }
    // if ($min < $retailmin) {
    // $returnArr['percentage'] = 100 - (($min * 100) / $retailmin);
    // } else
    // $returnArr['percentage'] = FALSE;
    // return $returnArr;
    // }


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

    public function getAllCustomersExcel() {
        $where = array('admin' => 0);
        $this->db->order_by('first_name ASC');
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
                'contact.company AS company, ' .
                'count(order.id) as orders,');
        $this->db->join('contact', 'contact.id = user.billing_id', 'LEFT');
        $this->db->join('order', 'order.user_id = user.id', 'LEFT');
        $this->db->where("contact.first_name != ''");

        $this->db->group_by('order.user_id');
        $records = $this->selectRecords('user', $where);
        $csv = $this->array2csv($records);
        //return $records;
        return $csv;
    }

}
