<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Motorcycle_M extends Master_M {

    function __construct() {
        parent::__construct();
        $this->_default_hang_tag_spec_labels = array('Length (in)','Seat Height (in)','Fuel Capacity (gal)','Wet Weight (lbs)','Engine Type','Displacement (ci)');
    }

    public function sub_assembleFilterInput(&$primary_source, &$secondary_source) {
        foreach (array("fltr", "condition", "brands", "years", "categories", "vehicles") as $k) {
            if (!array_key_exists($k, $primary_source) && array_key_exists($k, $secondary_source)) {
                $primary_source[$k] = $secondary_source[$k];
            }
        }
    }

    public function assembleFilterFromRequest($copy_from_post = false)
    {
        if ($copy_from_post) {
            $this->sub_assembleFilterInput($_GET, $_POST);
        }

        return $this->sub_assembleFilterFromRequest($_GET);
    }

    public function sub_assembleFilterFromRequest(&$data_source) {
        $filter = array();

        /*
         * I reject the idea that vault is the default as a general principle. This seems like a horrible choice for the vault, since it is
         * supposed to be SPECIAL. JLB 06-04-17
         */        
        
        if (array_key_exists('fltr', $data_source)) {
            //$filter['condition'] = $_GET['fltr'] == 'current' ? '1' : '2';
            // JLB 2018-09-13 - There's now SPECIAL
            if ($data_source["fltr"] == "special") {
                $filter["featured"] = 1;
            } else if ($data_source['fltr'] == 'New_Inventory'){
                $filter['condition'] = '1';
            } else{
                $filter['condition'] = '2';
            }
        } else if (array_key_exists('condition', $data_source)) {
            //$filter['condition'] = $_GET['fltr'] == 'current' ? '1' : '2';
            if ($data_source["condition"] == "special") {
                $filter["featured"] = 1;
            } else if ($data_source['condition'] == 'New_Inventory'){
                $filter['condition'] = '1';
            } else{
                $filter['condition'] = '2';
            }
        } else {
            $filter["condition"] = 1;
        }
        
        $filter_vehicles = array();
        if(array_key_exists('vehicles', $data_source)) {
            $vehicles = $this->getMotorcycleVehicle();
            $vhcls = $this->processReturnValue($data_source['vehicles']);

            foreach ($vehicles as $vehicle) {
                if(in_array($vehicle['name'], $vhcls) || in_array($vehicle['id'], $vhcls)) {
                    $filter_vehicles[] = $vehicle['id'];
                }
            }
        }
        $filter_categories = array();
        if(array_key_exists('categories', $data_source)) {
            $categories = $this->getMotorcycleCategory();
            $catgrs = $this->processReturnValue($data_source['categories']);

            foreach ($categories as $category) {
                if(in_array($category['name'], $catgrs) || in_array($category['id'], $catgrs)) {
                    $filter_categories[] = $category['id'];
                }
            }
        }

        $filter['brands'] = $this->processReturnValue($data_source['brands']);
        $filter['years'] = $this->processReturnValue($data_source['years']);
        $filter['categories'] = $filter_categories;
        $filter['vehicles'] = $filter_vehicles;

        return $filter;
    }

    public function getPageInfos() {
        $page_title = "New";
        $page_meta = "we offer a wide variety of ";
        if (array_key_exists('fltr', $_GET)) {
            if ($_GET["fltr"] == "New_Inventory") {
                $page_title = "New";
                $page_meta .= "New";
            } else if ($_GET["fltr"] == 'special'){
                $page_title = "Featured";
                $page_meta .= "Featured";
            } else{
                $page_title = 'Pre-Owned';
                $page_meta .= "Pre-Owned";
            }
        }

        if (array_key_exists('brands', $_GET)) {
            $brands = $this->processReturnValue($_GET['brands']);
            foreach( $brands as $brand ) {
                $page_title .= " ".$brand;
                $page_meta .= " ".$brand;
            }
        }
        if (array_key_exists('years', $_GET)) {
            $years = $this->processReturnValue($_GET['years']);
            foreach( $years as $year ) {
                $page_title .= " ".$year;
                $page_meta .= " ".$year;
            }
        }
        if (array_key_exists('vehicles', $_GET)) {
            $vehicles = $this->processReturnValue($_GET['vehicles']);
            foreach( $vehicles as $vehicle ) {
                $page_title .= " ".$vehicle;
                $page_meta .= " ".$vehicle;
            }
            $page_meta .= ".";
        }
        if (array_key_exists('categories', $_GET)) {
            $categories = $this->processReturnValue($_GET['categories']);
            foreach( $categories as $category ) {
                $page_title .= " ".$category;
            }
        }
        $page_meta .= " Come visit our dealer ship to see all we have to offer.";

        return array(
            'page_title' =>$page_title,
            'page_meta' =>$page_meta,
        );
    }
 
    protected function buildWhere($filter, $skip_year = false, $skip_vehicles = false, $skip_categories = false) {
        $where = array();
        if( !empty($filter['condition']) ) {
            $where['condition'] = $filter['condition'];
            $this->db->where("condition", $where['condition']);
        }

        if (array_key_exists("featured", $filter)) {
            $where['featured'] = $filter["featured"];
            $this->db->where("featured", $where['featured']);
        }

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

        $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');

        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);
        }
        $this->db->group_by('motorcycle.id');
        $this->db->select("motorcycle.*,motorcycleimage.image_name, motorcycle_type.name  as type, motorcycleimage.external $relevance_search_extra ", FALSE);
        if($limit > 0) {
            $this->db->limit($limit, $offset);
        }

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
                $this->db->order_by(" dm.numeric_sku desc ");


        }

        $records = $this->selectRecords('motorcycle', $where);

        // Load major unit payment option
        $this->load->model('motorcyclepaymentoption_m');
        $global_payment_option = $this->motorcyclepaymentoption_m->getGlobalPaymentOption();
        foreach($records as &$record) {
            $record['payment_option'] = $this->motorcyclepaymentoption_m->getActivePaymentOption($record['id'], $global_payment_option, $record['condition']);
        }
        //

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

    public function getMotorcycleSpecs($id, $retail_price = false, $only_hang_tag = false) {
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

        if ($only_hang_tag)
            $query = $this->db->query("Select motorcyclespec.*, motorcyclespecgroup.name as spec_group, motorcyclespecgroup.ordinal as group_ordinal from motorcyclespec join motorcyclespecgroup using (motorcyclespecgroup_id) where motorcyclespec.motorcycle_id = ? and motorcyclespecgroup.hidden = 0 and motorcyclespec.hidden = 0 and (crs_attribute_id is null OR ((crs_attribute_id < 230000) and (crs_attribute_id >= 20000) and crs_attribute_id not in (" . implode(",", $exclude_attributes) . "))) and motorcyclespec.hang_tag = 1 order by motorcyclespecgroup.ordinal, motorcyclespec.ordinal", array($id));
        else
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

        // Load major unit payment option
        $this->load->model('motorcyclepaymentoption_m');
        $global_payment_option = $this->motorcyclepaymentoption_m->getGlobalPaymentOption();
        $record['payment_option'] = $this->motorcyclepaymentoption_m->getActivePaymentOption($record['id'], $global_payment_option, $record['condition']);

        return $record;
    }

    public function getFilterTotal( $filter , $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter);
        $where["deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);

        }
        $this->db->select('count(id)');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['cnt'];
    }

    public function getMotorcycleCategory($filter = array(), $major_units_featured_only = 0) {
        return $this->sub_getMotorcycleCategory($filter, $major_units_featured_only, array_key_exists("major_unit_search_keywords", $_SESSION) ? $_SESSION["major_unit_search_keywords"] : "");
    }


    public function sub_getMotorcycleCategory($filter = array(), $major_units_featured_only = 0, $search_keywords = "") {
        $where = $this->buildWhere($filter, false, false, true);
        $where['motorcycle_category.name != '] = '';
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }

        $this->db->join('motorcycle', 'motorcycle.category = motorcycle_category.id');
        if ($search_keywords != "") {
            $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);
        }
        $this->db->select('motorcycle_category.*');
        $this->db->group_by('motorcycle_category.name');
        $record = $this->selectRecords('motorcycle_category', $where);
        return $record;
    }

    public function sub_getMotorcycleDistinctModels($filter = array(), $major_units_featured_only = 0, $search_keywords = "") {
        $where = $this->buildWhere($filter, false, false, true);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if ($search_keywords != "") {
            $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);
        }
        $this->db->distinct();
        $this->db->select('motorcycle.model');
        $record = $this->selectRecords('motorcycle', $where);
        return $record;
    }

    public function getMotorcycleCondition($filter = array(), $major_units_featured_only = 0) {
        $where = $this->buildWhere($filter);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if (array_key_exists("major_unit_search_keywords", $_SESSION) && $_SESSION["major_unit_search_keywords"] != "") {
            $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);
        }
        $this->db->select('condition');
        $this->db->group_by('condition');
        $record = $this->selectRecords('motorcycle', $where);
        //echo $this->db->last_query();
        return $record;
    }

    public function getMotorcycleVehicle($filter = array(), $major_units_featured_only = 0) {
        return $this->sub_getMotorcycleVehicle($filter, $major_units_featured_only, array_key_exists("major_unit_search_keywords", $_SESSION) ? $_SESSION["major_unit_search_keywords"] : "");
    }

    public function sub_getMotorcycleVehicle($filter = array(), $major_units_featured_only = 0, $search_keywords = "") {
        $where = $this->buildWhere($filter, false, true);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        $this->db->join('motorcycle', 'motorcycle.vehicle_type = motorcycle_type.id');
        if ($search_keywords != "") {
            $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);
        }

        $this->db->select('motorcycle_type.*');
        $this->db->group_by('motorcycle.vehicle_type');
        $record = $this->selectRecords('motorcycle_type', $where);
        return $record;
    }

    public function getMotorcycleMake($filter = array(), $major_units_featured_only = 0)
    {
        return $this->sub_getMotorcycleMake($filter, $major_units_featured_only, array_key_exists("major_unit_search_keywords", $_SESSION) ? $_SESSION["major_unit_search_keywords"] : "");
    }

    public function sub_getMotorcycleMake($filter = array(), $major_units_featured_only = 0, $search_keywords = "") {
        $where = $this->buildWhere($filter);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if ($search_keywords != "") {
            $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);

        }
        $this->db->select('make');
        $this->db->group_by('make');
        $record = $this->selectRecords('motorcycle', $where);
        return $record;
    }

    public function getMotorcycleYear($filter = array(), $major_units_featured_only = 0) {
        return $this->sub_getMotorcycleYear($filter, $major_units_featured_only, array_key_exists("major_unit_search_keywords", $_SESSION) ? $_SESSION["major_unit_search_keywords"] : "");
    }
    public function sub_getMotorcycleYear($filter = array(), $major_units_featured_only = 0, $search_keywords = "") {
        $where = $this->buildWhere($filter, true);
        $where["motorcycle.deleted"] = 0;
        if ($major_units_featured_only > 0) {
            $where["motorcycle.featured"] = 1;
        }
        if ($search_keywords != "") {
            $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);

        }
        $this->db->select('year');
        $this->db->group_by('year');
        $this->db->order_by('year', 'DESC');
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
        $records = $query->result_array();

        // Load major unit payment option
        $this->load->model('motorcyclepaymentoption_m');
        $global_payment_option = $this->motorcyclepaymentoption_m->getGlobalPaymentOption();
        if (isset($records)) {
            foreach($records as $record) {
                $record['payment_option'] = $this->motorcyclepaymentoption_m->getActivePaymentOption($record['id'], $global_payment_option, $record['condition']);
            }
        }
        
        return $records;

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
        if (!array_key_exists("ip_address", $data)) {
            $data["ip_address"] = returnClientIP();
        }
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
        if( !empty($filter['featured']) ) {
            $where['featured'] = $filter['featured'];
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
            $this->db->join('denormalized_motorcycle dm', 'motorcycle.id = dm.motorcycle_id ', 'left');
            $this->db->where(jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "dm.category", "motorcycle.year", "dm.type", "motorcycle.stock_status", "motorcycle.sku"), $_SESSION["major_unit_search_keywords"], ""), NULL, FALSE);

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
    public function processReturnValue($value)
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

        $where = jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "motorcycle_category.name", "motorcycle.year", "motorcycle_type.name", "motorcycle.stock_status", "motorcycle.sku"), $filter, "WHERE");

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
            // 10-05-18
            // Removed: left join motorcyclespec on motorcycle.id = motorcyclespec.motorcycle_id AND motorcyclespec.attribute_name = 'MfrModelID' AND motorcyclespec.hidden = 0
            $query = $this->db->query("Select count(distinct motorcycle.id) as cnt from motorcycle join motorcycle_category on motorcycle.category = motorcycle_category.id join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id   $where");
            foreach ($query->result_array() as $row) {
                $filtered_count = $row["cnt"];
            }
        } else {
            $where = " WHERE motorcycle.deleted = 0";
        }

        // Finally, run it!
        // Removed: motorcyclespec.final_value as model
        // left join motorcyclespec on motorcycle.id = motorcyclespec.motorcycle_id AND motorcyclespec.attribute_name = 'MfrModelID' AND motorcyclespec.hidden = 0
        $query = $this->db->query("Select If(motorcycle.crs_trim_id > 0, 'Yes', 'No') as matched, motorcycle.id, motorcycle.sku, motorcycle_category.name as category_name, motorcycle_type.name as type_name, motorcycle.title, motorcycle.featured, motorcycle.status, motorcycle.condition, motorcycle.retail_price, motorcycle.sale_price, motorcycle.condition, IfNull(motorcycle.mileage, 0) as mileage, motorcycle.source, motorcycleimage.image_name, motorcycleimage.external, motorcycle.stock_status, motorcycle.cycletrader_feed_status, motorcycle.manager_special, motorcycle.model from motorcycle join motorcycle_category on motorcycle.category = motorcycle_category.id join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id left join (select motorcycle_id, min(priority_number) as priority_number from motorcycleimage where disable = 0 group by motorcycle_id ) thumbnail_motorcycleimage on motorcycle.id = thumbnail_motorcycleimage.motorcycle_id left join motorcycleimage on thumbnail_motorcycleimage.motorcycle_id = motorcycleimage.motorcycle_id AND thumbnail_motorcycleimage.priority_number = motorcycleimage.priority_number  $where group by motorcycle.id $orderBy limit $limit offset $offset ");
        $rows = $query->result_array();

        return array($rows, $total_count, $filtered_count);
    }

    public function getMotorcycleImageColorCodes($id = 0, $include_deleted = false) {
        $colors = array();
        $pattern = "/_([a-zA-Z\-]+)\./";

        $this->db->select('image_name');
        $this->db->from('motorcycleimage');
        if ( $id > 0 ) {
            $this->db->where('motorcycle_id', $id);
        }
        if (!$include_deleted)
            $this->db->where('disable', 0);
        $this->db->where('description', 'Colorized Photo');
        $query = $this->db->get();

        $images = $query->result_array();

        foreach ($images as $image) {
            $matches = array();            
            preg_match($pattern , $image['image_name'], $matches);

            if (count($matches) > 1 && !in_array($matches[1], $colors)) {
                $colors[] = $matches[1];
            }
        }

        return $colors;
    }

    public function getCRSImageColorCodes($trim_id) {
        $colors = array();
        $pattern = "/_([a-zA-Z\-]+)\./";

        $this->load->model("crs_m");
        $images = $this->crs_m->getTrimPhotos($trim_id);

        foreach ($images as $image) {
            $matches = array();            
            preg_match($pattern , $image['photo_url'], $matches);

            if (count($matches) > 0 && $matches[1] != '' && !array_key_exists($matches[1], $colors)) {
                $colors[] = $matches[1];
            }
        }

        return $colors;
    }

    public function removeColorizedImage($id, $colors) {
        // get image colors
        $imageColors = $this->getMotorcycleImageColorCodes($id);


        // remove itself
        if (count($colors) > 0 ) {
            $colors_to_remove = array_diff($imageColors, $colors);
                
            $this->load->model('admin_m');

            if ( !empty($colors_to_remove) ) {  
                
                foreach ( $colors_to_remove as $ic ) {
                    $this->db->select('id');
                    $this->db->from('motorcycleimage');
                    $this->db->where('motorcycle_id', $id);
                    $this->db->where('description', 'Colorized Photo');
                    $this->db->like('image_name', '_'.$ic.'.');
                    $query = $this->db->get();

                    $images = $query->result_array();

                    foreach ($images as $image) {
                        $this->admin_m->deleteMotorcycleImage($image['id'], $id);
                    }
                }
            }

            // remove thumbnail
            $query = $this->db->query("Select id from motorcycleimage where motorcycle_id = ? and description like '%Trim Photo:%'", array($id));
            foreach ($query->result_array() as $row) {
                $this->admin_m->deleteMotorcycleImage($row['id'], $id);
            }

            if (!empty($colors)) {
                foreach ( $colors as $ic ) {
                    $this->db->query("Update motorcycleimage set disable = 0 where customer_deleted = 0 and motorcycle_id = ? and description = 'Colorized Photo' and image_name like '%\_".$ic.".%'", array($id));
                }
            }

            $this->reorderImages($id);
        }
    }

    public function reorderImages($id) {
        $query = $this->db->query("Select * from motorcycleimage where motorcycle_id = ? order by priority_number asc", array($id));
        $colorized_photos = array();
        $thumbnails = array();
        $other_photos = array();
        foreach($query->result_array() as $row) {
            if ($row['description'] == 'Colorized Photo') {
                $colorized_photos[] = $row;
            } else if (strpos($row['description'], 'Trim Photo') !== false) {
                $thumbnails[] = $row;
            } else {
                $other_photos[] = $row;
            }
        }

        $photos = array_merge($thumbnails, $colorized_photos, $other_photos);
        $order = 1;
        foreach($photos as $photo) {
            if ($photo['priority_number'] != $order)
                $this->db->query("update motorcycleimage set priority_number = ? where id = ?", array($order, $photo['id']));
            $order++;
        }
    }

}
