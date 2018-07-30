<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Motorcycle_M extends Master_M {

    function __construct() {
        parent::__construct();
    }

    public function assembleFilterFromRequest($copy_from_post = false) {
        $filter = array();

        if ($copy_from_post) {
            foreach (array("fltr", "condition", "brands", "years", "categories", "vehicles") as $k) {
                if (!array_key_exists($k, $_GET) && array_key_exists($k, $_POST)) {
                    $_GET[$k] = $_POST[$k];
                }
            }
        }

        /*
         * I reject the idea that vault is the default as a general principle. This seems like a horrible choice for the vault, since it is
         * supposed to be SPECIAL. JLB 06-04-17
         */
        if (array_key_exists('fltr', $_GET)) {
            //$filter['condition'] = $_GET['fltr'] == 'current' ? '1' : '2';
            if ($_GET['fltr'] == 'new'){
                $filter['condition'] = '1';
            } else{
                $filter['condition'] = '2';
            }
        } else if (array_key_exists('condition', $_GET)) {
            //$filter['condition'] = $_GET['fltr'] == 'current' ? '1' : '2';
            if ($_GET['condition'] == 'new'){
                $filter['condition'] = '1';
            } else{
                $filter['condition'] = '2';
            }
        } else {
            $filter["condition"] = 1;
        }

        $filter['brands'] = $this->processReturnValue($_GET['brands']);
        $filter['years'] = $this->processReturnValue($_GET['years']);
        $filter['categories'] = $this->processReturnValue($_GET['categories']);
        $filter['vehicles'] = $this->processReturnValue($_GET['vehicles']);

        return $filter;
    }

    protected function buildWhere($filter, $skip_year = false, $skip_vehicles = false, $skip_categories = false) {
        $where = array();
        if( !empty($filter['condition']) ) {
            $where['condition'] = $filter['condition'];
            $this->db->where("condition", $where['condition']);
        }

        // JLB 06-04-17
        // It is my understanding the vault flag no longer is used.
//        if( !empty($filter['vault']) ) {
//            $where['vault'] = $filter['vault'];
//        }

        // JLB 06-05-17
        // I cannot for the life of me understand why they wrote this one like this.
//        if (@$filter['brands']) {
//            $bwhere = ' (';
//            foreach ($filter['brands'] as $brand) {
//                $bwhere .= " motorcycle.make = '" . $brand."' OR";
//            }
//            $bwhere = rtrim($bwhere, 'OR');
//            $bwhere .= ' ) ';
//            $this->db->where($bwhere, NULL, FALSE);
//        }

        if (@$filter['brands']) {
            $this->db->where_in('motorcycle.make', $filter['brands']);
        }

        if (!$skip_year && @$filter['years']) {
            $this->db->where_in('motorcycle.year', $filter['years']);
        }
        if (!$skip_categories && @$filter['categories']) {
            $this->db->where_in('motorcycle.category', $filter['categories']);
        }
        if (!$skip_vehicles && @$filter['vehicles']) {
            $this->db->where_in('motorcycle.vehicle_type', $filter['vehicles']);
        }

        if (!$skip_vehicles && @$filter['status']) {
            $this->db->where_in('motorcycle.status', $filter['status']);
        }
    }
//
//    public function getMotorcycles( $filter = array() , $limit = 6, $offset = 0) {
//        $where = $this->buildWhere($filter);
//        $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id', 'left');
//        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
//        $this->db->group_by('motorcycle.id');
//        $this->db->select('motorcycle.*,motorcycleimage.image_name, motorcycle_type.name as type', FALSE);
//        $this->db->limit($limit, $offset);
//        $records = $this->selectRecords('motorcycle', $where);
//        return $records;
//    }

    public function getMotorcycles( $filter = array() , $limit = 5, $offset = 0, $sort_order = 1, $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter);
        $this->db->_protect_identifiers=false;
        $this->db->join(' (select min(priority_number) as priority_number, motorcycle_id, external from motorcycleimage where disable = 0 group by motorcycle_id) motorcycleimageA', 'motorcycleimageA.motorcycle_id = motorcycle.id', 'left');
        $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id and motorcycleimage.priority_number = motorcycleimageA.priority_number ', 'left');
        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
        $this->db->where("motorcycle.status", 1, false); // JLB 12-18-17 Show only active ones...
        $this->db->where("motorcycle.deleted", 0, false); // JLB 01-04-18 Show only undeleted ones...
        $relevance_search_extra = "";
        if ($major_units_featured_only > 0) {
            $this->db->where("motorcycle.featured", 1, false); // JLB 03-15-18 Show only the featured ones if selected
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $relevance_search_extra = ' , MATCH (motorcycle.sku, motorcycle.title, motorcycle.description) AGAINST ("' . addslashes($_SESSION["major_unit_search_keywords"]) . ')") as relevance ';
            $this->db->order_by(" relevance desc ");
        }
        $this->db->group_by('motorcycle.id');
        $this->db->select("motorcycle.*,motorcycleimage.image_name, motorcycle_type.name  as type, motorcycleimage.external $relevance_search_extra ", FALSE);
        $this->db->limit($limit, $offset);

        switch($sort_order) {
            case 1:
                // Price High to Low
                $this->db->order_by('If(sale_price = 0, retail_price, sale_price) desc');

                break;

            case 2:
                // Price Low to High
                $this->db->order_by('If(sale_price = 0, retail_price, sale_price) asc');

                break;

            case 3:
                // Year New to Old
                $this->db->order_by("motorcycle.year desc");
                break;

            case 4:
                // Year Old to New
                $this->db->order_by("motorcycle.year asc");
                break;

            default:
                // Relevance should be the default...

        }

        $records = $this->selectRecords('motorcycle', $where);
        $this->db->_protect_identifiers=true;
        return $records;
    }

    public function getMotorcycleImages($id) {
        $query = $this->db->query("Select * from motorcycleimage where motorcycle_id = ? and disable = 0 order by priority_number ", array($id));
        return $query->result_array();
    }

    public function getMotorcyclSpecGroups($id) {
        $query = $this->db->query("Select * from motorcyclespecgroup where motorcycle_id = ? and hidden = 0 and (crs_attributegroup_number is null OR crs_attributegroup_number = 0 or (crs_attributegroup_number >= 2 AND crs_attributegroup_number < 23)) order by ordinal", array($id));
        return $query->result_array();
    }

    public function getMotorcycleSpecs($id, $retail_price = false) {
        $exclude_attributes = array(20005, 20008);
        if (FALSE !== $retail_price) {
            // OK, we need to do some excludes, e.g., MSRP
            $no_price_match = true;
            $query = $this->db->query("Select * from motorcyclespec where motorcycle_id = ? and crs_attribute_id in (20002, 20007)", array($id));
            foreach ($query->result_array() as $row) {
                if (floatVal($row["final_value"]) == $retail_price) {
                    $no_price_match = false;
                }
            }

            if ($no_price_match) {
                $exclude_attributes[] = 20003;
                $exclude_attributes[] = 20006;
                $exclude_attributes[] = 20002;
                $exclude_attributes[] = 20007;
            }
        }


        $query = $this->db->query("Select motorcyclespec.*, motorcyclespecgroup.name as spec_group, motorcyclespecgroup.ordinal as group_ordinal from motorcyclespec join motorcyclespecgroup using (motorcyclespecgroup_id) where motorcyclespec.motorcycle_id = ? and motorcyclespecgroup.hidden = 0 and motorcyclespec.hidden = 0 and (crs_attribute_id is null OR ((crs_attribute_id < 230000) and (crs_attribute_id >= 20000) and crs_attribute_id not in (" . implode(",", $exclude_attributes) . "))) order by motorcyclespecgroup.ordinal, motorcyclespec.ordinal", array($id));
        return $query->result_array();
    }

    public function getMotorcycle( $id ){
        $record = array();
        $query = $this->db->query("select motorcycle.*, motorcycle_category.name as category, motorcycle_type.name as type from motorcycle left join motorcycle_category on motorcycle.category = motorcycle_category.id left join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id where motorcycle.id = ? and deleted = 0", array($id));
        foreach ($query->result_array() as $row) {
            $record = $row;
        }
//        $where = array('motorcycle.id' => $id );
//        $record = $this->selectRecord('motorcycle', $where);

//        $iwhere = array('motorcycleimage.motorcycle_id' => $id );
//        $this->db->order_by('motorcycleimage.priority_number asc');

        $record['images'] = $this->getMotorcycleImages($id); // $this->selectRecords('motorcycleimage', $iwhere);
        $vwhere = array('motorcycle_video.part_id' => $id );

        $this->db->order_by('motorcycle_video.id asc');
        $record['videos'] = $this->selectRecords('motorcycle_video', $vwhere);


        $record["specs"] = $this->getMotorcycleSpecs($id, $record["retail_price"]);

        return $record;
    }

//
//	public function getFilterMotorcycles( $filter, $limit ) {
//
//		if (@$filter['categories']) {
//			$cwhere = ' (';
//			foreach ($filter['categories'] as $category) {
//				$cwhere .= " motorcycle.category = '" . $category."' OR";
//			}
//			$cwhere = rtrim($cwhere, 'OR');
//			$cwhere .= ' ) ';
//			$this->db->where($cwhere, NULL, FALSE);
//		}
//		if (@$filter['brands']) {
//			$bwhere = ' (';
//			foreach ($filter['brands'] as $brand) {
//				$bwhere .= " motorcycle.make = '" . $brand."' OR";
//			}
//			$bwhere = rtrim($bwhere, 'OR');
//			$bwhere .= ' ) ';
//			$this->db->where($bwhere, NULL, FALSE);
//		}
//		if (@$filter['years']) {
//			$ywhere = ' (';
//			foreach ($filter['years'] as $year) {
//				$ywhere .= " motorcycle.year = '" . $year."' OR";
//			}
//			$ywhere = rtrim($ywhere, 'OR');
//			$ywhere .= ' ) ';
//			$this->db->where($ywhere, NULL, FALSE);
//		}
//		if (@$filter['vehicles']) {
//			$vwhere = ' (';
//			foreach ($filter['vehicles'] as $vehicle) {
//				$vwhere .= " motorcycle.vehicle_type = '" . $vehicle."' OR";
//			}
//			$vwhere = rtrim($vwhere, 'OR');
//			$vwhere .= ' ) ';
//			$this->db->where($vwhere, NULL, FALSE);
//		}
//		if ( $filter['condition'] != '' ) {
//			$cndn = $filter['condition'] == 'new' ? '1' : '2';
//			$cwhr = ' motorcycle.condition = '.$cndn;
//			$this->db->where($cwhr, NULL, FALSE);
//		}
//
//		$where = array();
//		$this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
//		$this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id', 'left');
//		$this->db->group_by('motorcycle.id');
//		$this->db->select('motorcycle.*, motorcycleimage.image_name, motorcycle_type.name as type');
//		$this->db->limit('6', $limit);
//		$records = $this->selectRecords('motorcycle', $where);
//		return $records;
//	}

    public function getFilterTotal( $filter , $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter);
        $where["deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->where('MATCH (motorcycle.sku, motorcycle.title, motorcycle.description) AGAINST ("' . addslashes($_SESSION["major_unit_search_keywords"]) . ')")', NULL, FALSE);
        }
        $this->db->select('count(id)');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['cnt'];
    }

    public function getMotorcycleCategory($filter = array(), $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter, false, false, true);
        $where['motorcycle_category.name != '] = '';
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->where('MATCH (motorcycle.sku, motorcycle.title, motorcycle.description) AGAINST ("' . addslashes($_SESSION["major_unit_search_keywords"]) . ')")', NULL, FALSE);
        }
        $this->db->join('motorcycle', 'motorcycle.category = motorcycle_category.id');
        $this->db->select('motorcycle_category.*');
        $this->db->group_by('motorcycle_category.name');
        $record = $this->selectRecords('motorcycle_category', $where);
        return $record;
    }

    public function getMotorcycleCondition($filter = array(), $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->where('MATCH (motorcycle.sku, motorcycle.title, motorcycle.description) AGAINST ("' . addslashes($_SESSION["major_unit_search_keywords"]) . ')")', NULL, FALSE);
        }
        $this->db->select('condition');
        $this->db->group_by('condition');
        $record = $this->selectRecords('motorcycle', $where);
        //echo $this->db->last_query();
        return $record;
    }

    public function getMotorcycleVehicle($filter = array(), $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter, false, true);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->where('MATCH (motorcycle.sku, motorcycle.title, motorcycle.description) AGAINST ("' . addslashes($_SESSION["major_unit_search_keywords"]) . ')")', NULL, FALSE);
        }
        $this->db->join('motorcycle', 'motorcycle.vehicle_type = motorcycle_type.id');
        $this->db->select('motorcycle_type.*');
        $this->db->group_by('motorcycle.vehicle_type');
        $record = $this->selectRecords('motorcycle_type', $where);
        return $record;
    }

    public function getMotorcycleMake($filter = array(), $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->where('MATCH (motorcycle.sku, motorcycle.title, motorcycle.description) AGAINST ("' . addslashes($_SESSION["major_unit_search_keywords"]) . ')")', NULL, FALSE);
        }
        $this->db->select('make');
        $this->db->group_by('make');
        $record = $this->selectRecords('motorcycle', $where);
        return $record;
    }

    public function getMotorcycleYear($filter = array(), $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter, true);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->where('MATCH (motorcycle.sku, motorcycle.title, motorcycle.description) AGAINST ("' . addslashes($_SESSION["major_unit_search_keywords"]) . ')")', NULL, FALSE);
        }
        $this->db->select('year');
        $this->db->group_by('year');
        $record = $this->selectRecords('motorcycle', $where);
        return $record;
    }

    public function getFeaturedMonster()
    {
        $query = "Select motorcycle.*, motorcycleimage.image_name, motorcycle_type.name as type, motorcycleimage.external from motorcycle join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id left join (select min(priority_number) as priority_number, motorcycle_id, external from motorcycleimage where disable = 0 group by motorcycle_id) motorcycleimageA on motorcycleimageA.motorcycle_id = motorcycle.id left join motorcycleimage on motorcycleimage.motorcycle_id = motorcycle.id and motorcycleimage.priority_number = motorcycleimageA.priority_number where motorcycle.featured = 1 and motorcycle.deleted = 0 group by motorcycle.id";
        $query = $this->db->query($query);
        return $query->result_array();
    }

    // JLB 01-24-18 This typo just grates on me.
    public function getReccentlyMotorcycles( $ids ) {
        // JLB 01-24-18
        // Those IDs are a list in time order of what's been viewed...we're just going to take the top 3
        $display_limit = 3; // because this magic number otherwise is buried in a query...
        $time_ordered = array();
        $seen = array();
        $ids = array_values($ids);
        for ($i = count($ids) - 1; $i >= max(0, count($ids) - $display_limit); $i--) {
            $id = $ids[$i];
            if (!array_key_exists($id, $seen)) {
                $time_ordered[] = $id;
                $seen[$id] = true;
            }
        }

        if (count($time_ordered) == 0) {
            return array(); // none!
        }

        // JLB 01-24-18
        // I discovered that not only is this not ordered by latest first, it's not even ordered based on how you view things.
        // Instead, it's ordered based on how new the bikes are....that makes no sense to me.
        // https://stackoverflow.com/questions/4979424/sql-order-by-sequence-of-in-values-in-query
        $query = $this->db->query("Select motorcycle.*, motorcycleimage.image_name, motorcycle_type.name as type, motorcycleimage.external from motorcycle left join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id left join (select min(priority_number) as priority_number, motorcycle_id, external from motorcycleimage where disable = 0 group by motorcycle_id) motorcycleimageA on motorcycleimageA.motorcycle_id = motorcycle.id left join motorcycleimage on motorcycleimage.motorcycle_id = motorcycle.id and motorcycleimage.priority_number = motorcycleimageA.priority_number where motorcycle.deleted = 0 and motorcycle.id in (" . ($imp = implode(",", $time_ordered)) . ") group by motorcycle.id order by FIELD(`motorcycle`.`id`, " . $imp . ") limit $display_limit" );
        return $query->result_array();

        // $where = array();
        // $this->db->where_in('motorcycle.id',$ids);
        // $this->db->where("motorcycle.deleted", 0, false);
        //$where = array('motorcycle.featured' => '1' );
//        $this->db->_protect_identifiers=false;
//        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
        // $this->db->join(' (select min(priority_number) as priority_number, motorcycle_id, external from motorcycleimage where disable = 0 group by motorcycle_id) motorcycleimageA', 'motorcycleimageA.motorcycle_id = motorcycle.id', 'left');
        // $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id and motorcycleimage.priority_number = motorcycleimageA.priority_number ', 'left');
        // $this->db->group_by('motorcycle.id');
        // $this->db->limit(display_limit);
        // $this->db->order_by("motorcycle.id","DESC");
        // $this->db->select('motorcycle.*, motorcycleimage.image_name, motorcycle_type.name as type, motorcycleimage.external');
        // $this->db->_protect_identifiers=true;
        // $records = $this->selectRecords('motorcycle', $where);
        // return $records;
    }

    // JLB 04-23-18
    // This kept throwing some weird error frmo the active recod
    public function saveEnquiry( $data ) {
        global $PSTAPI;
        initializePSTAPI();
        $PSTAPI->motorcycleenquiry()->add($data);
    }

    public function getSalesEmail() {
        $this->db->select('sales_email');
        $where = array('id' => '1');
        $record = $this->selectRecord('contact', $where);
        return $record['sales_email'];
    }

    public function getTotal( $filter , $major_units_featured_only = 0) {
        $where = array();
        if( !empty($filter['condition']) ) {
            $where['condition'] = $filter['condition'];
        }

        if (@$filter['brands']) {
            $bwhere = ' (';
            foreach ($filter['brands'] as $brand) {
                $bwhere .= " motorcycle.make = '" . addslashes($brand)."' OR";
            }
            $bwhere = rtrim($bwhere, 'OR');
            $bwhere .= ' ) ';
            $this->db->where($bwhere, NULL, FALSE);
        }
        if (@$filter['years']) {
            $this->db->where_in('motorcycle.year', $filter['years']);
        }
        if (@$filter['categories']) {
            $this->db->where_in('motorcycle.category', $filter['categories']);
        }
        if (@$filter['vehicles']) {
            $this->db->where_in('motorcycle.vehicle_type', $filter['vehicles']);
        }
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->where('MATCH (motorcycle.sku, motorcycle.title, motorcycle.description) AGAINST ("' . addslashes($_SESSION["major_unit_search_keywords"]) . ')")', NULL, FALSE);
        }
        $this->db->where("motorcycle.status", 1, FALSE);
        $this->db->where("motorcycle.deleted", 0, FALSE);
        $this->db->select('count(id) as cnt', FALSE);
        // $this->db->limit('6');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['cnt'];
    }

    // JLB 12-15-17
    // This is the URL title.
    public function getMotorcycleIdByTitle( $title ) {
        $where = array('trim(url_title)' => $title, 'deleted' => 0);
        $this->db->select('id');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['id'];
    }

    public function getMotorcycleIdBySKU( $sku ) {
        $where = array('sku' => trim($sku), 'deleted' => 0);
        $this->db->select('id');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['id'];
    }

    /**
     * @param $value
     * @param $filter
     * @return mixed
     */
    private function processReturnValue($value)
    {
        if (isset($value) && (is_array($value) || $value != "")) {
            if (!is_array($value)) {
                $value = explode('$', $value);
                $value = array_filter($value);
            } else {
                $value = $value;
            }
        }
        return $value;
    }

    // This is modeled on the function from Portalmodel for the search results
    public function enhancedGetMotorcycles($filter = NULL, $orderBy = NULL, $limit = 20, $offset = 0) {
        $this->load->helper("jonathan");

        $where = jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "motorcycle_category.name", "motorcycle.year", "motorcycle_type.name", "motorcycle.stock_status", "motorcycle.sku", "motorcyclespec.final_value"), $filter, "WHERE");

        $total_count = 0;
        $query = $this->db->query("Select count(*) as cnt from motorcycle where deleted = 0");
        foreach ($query->result_array() as $row) {
            $total_count = $row['cnt'];
        }

        // Now, is there a filter?
        $filtered_count = $total_count;
        if ($where != "") {
            $where .= " AND motorcycle.deleted = 0 ";
            // $query = $this->db->query("Select count(distinct part_id) as cnt from part left join partpartnumber using (part_id) left join partnumber  using (partnumber_id)  left join (select partvariation.*, concat(distributor.name, ' ', partvariation.part_number) as partlabel from partvariation join distributor using (distributor_id)) zpartvariation using (partnumber_id) left join partimage using (part_id) $where");
            $query = $this->db->query("Select count(distinct motorcycle.id) as cnt from motorcycle join motorcycle_category on motorcycle.category = motorcycle_category.id join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id $where");
            foreach ($query->result_array() as $row) {
                $filtered_count = $row["cnt"];
            }
        } else {
            $where = " WHERE motorcycle.deleted = 0";
        }

        // Finally, run it!
        $query = $this->db->query("Select motorcycle.id, motorcycle.sku, motorcycle_category.name as category_name, motorcycle_type.name as type_name, motorcycle.title, motorcycle.featured, motorcycle.status, motorcycle.condition, motorcycle.retail_price, motorcycle.sale_price, motorcycle.condition, IfNull(motorcycle.mileage, 0) as mileage, motorcycle.source, motorcycleimage.image_name, motorcycleimage.external, motorcycle.stock_status, motorcycle.cycletrader_feed_status, motorcycle.manager_special, motorcyclespec.final_value as model from motorcycle join motorcycle_category on motorcycle.category = motorcycle_category.id join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id left join (select motorcycle_id, min(priority_number) as priority_number from motorcycleimage where disable = 0 group by motorcycle_id ) thumbnail_motorcycleimage on motorcycle.id = thumbnail_motorcycleimage.motorcycle_id left join motorcycleimage on thumbnail_motorcycleimage.motorcycle_id = motorcycleimage.motorcycle_id AND thumbnail_motorcycleimage.priority_number = motorcycleimage.priority_number left join motorcyclespec on motorcycle.id = motorcyclespec.motorcycle_id AND motorcyclespec.attribute_name = 'MfrModelID' AND motorcyclespec.hidden = 0  $where group by motorcycle.id $orderBy limit $limit offset $offset ");
        $rows = $query->result_array();

        return array($rows, $total_count, $filtered_count);
    }
}
