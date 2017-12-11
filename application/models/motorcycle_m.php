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

    public function getMotorcycles( $filter = array() , $limit = 6, $offset = 0) {
        $where = $this->buildWhere($filter);
        $this->db->_protect_identifiers=false;
        $this->db->join(' (select min(priority_number) as priority_number, motorcycle_id, external from motorcycleimage where disable = 0 group by motorcycle_id) motorcycleimageA', 'motorcycleimageA.motorcycle_id = motorcycle.id', 'left');
        $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id and motorcycleimage.priority_number = motorcycleimageA.priority_number ', 'left');
        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
        $this->db->group_by('motorcycle.id');
        $this->db->select('motorcycle.*,motorcycleimage.image_name, motorcycle_type.name  as type, motorcycleimage.external', FALSE);
        $this->db->limit($limit, $offset);
        $records = $this->selectRecords('motorcycle', $where);
        $this->db->_protect_identifiers=true;
        return $records;
    }

    public function getMotorcycleImages($id) {
        $query = $this->db->query("Select * from motorcycleimage where motorcycle_id = ? and disable = 0 order by priority_number ", array($id));
        return $query->result_array();
    }

    public function getMotorcycleSpecs($id) {
        $query = $this->db->query("Select motorcyclespec.*, motorcyclespecgroup.name as spec_group, motorcyclespecgroup.ordinal as group_ordinal from motorcyclespec join motorcyclespecgroup using (motorcyclespecgroup_id) where motorcycle_id = ? and motorcyclespecgroup.hidden = 0 and motorcyclespec.hidden = 0 and (crs_attribute_id is null OR ((crs_attribute_id < 230000) and (crs_attribute_id >= 20000) and crs_attribute_id not in (20005, 20008))) order by motorcyclespecgroup.ordinal, motorcyclespec.ordinal", array($id));
        return $query->result_array();
    }

    public function getMotorcycle( $id ){
        $where = array('motorcycle.id' => $id );
        $record = $this->selectRecord('motorcycle', $where);

//        $iwhere = array('motorcycleimage.motorcycle_id' => $id );
//        $this->db->order_by('motorcycleimage.priority_number asc');

        $record['images'] = $this->getMotorcycleImages($id); // $this->selectRecords('motorcycleimage', $iwhere);
        $vwhere = array('motorcycle_video.part_id' => $id );

        $this->db->order_by('motorcycle_video.id asc');
        $record['videos'] = $this->selectRecords('motorcycle_video', $vwhere);


        $record["specs"] = $this->getMotorcycleSpecs($id);

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

    public function getFilterTotal( $filter ) {
        $where = $this->buildWhere($filter);
        $this->db->select('count(id)');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['cnt'];
    }

    public function getMotorcycleCategory($filter = array()) {
        $where = $this->buildWhere($filter, false, false, true);
        $where['motorcycle_category.name != '] = '';
        $this->db->join('motorcycle', 'motorcycle.category = motorcycle_category.id');
        $this->db->select('motorcycle_category.*');
        $this->db->group_by('motorcycle_category.name');
        $record = $this->selectRecords('motorcycle_category', $where);
        return $record;
    }

    public function getMotorcycleCondition($filter = array()) {
        $where = $this->buildWhere($filter);
        $this->db->select('condition');
        $this->db->group_by('condition');
        $record = $this->selectRecords('motorcycle', $where);
        //echo $this->db->last_query();
        return $record;
    }

    public function getMotorcycleVehicle($filter = array()) {
        $where = $this->buildWhere($filter, false, true);
        $this->db->join('motorcycle', 'motorcycle.vehicle_type = motorcycle_type.id');
        $this->db->select('motorcycle_type.*');
        $this->db->group_by('motorcycle.vehicle_type');
        $record = $this->selectRecords('motorcycle_type', $where);
        return $record;
    }

    public function getMotorcycleMake($filter = array()) {
        $where = $this->buildWhere($filter);
        $this->db->select('make');
        $this->db->group_by('make');
        $record = $this->selectRecords('motorcycle', $where);
        return $record;
    }

    public function getMotorcycleYear($filter = array()) {
        $where = $this->buildWhere($filter, true);
        $this->db->select('year');
        $this->db->group_by('year');
        $record = $this->selectRecords('motorcycle', $where);
        return $record;
    }

    public function getFeaturedMonster() {
        $where = array(
            'motorcycle.featured' => '1',
            'motorcycleimage.priority_number' => '0',
        );
        $this->db->_protect_identifiers=false;

        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');

        $this->db->join(' (select min(priority_number) as priority_number, motorcycle_id, external from motorcycleimage where disable = 0 group by motorcycle_id) motorcycleimageA', 'motorcycleimageA.motorcycle_id = motorcycle.id', 'left');
        $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id and motorcycleimage.priority_number = motorcycleimageA.priority_number ', 'left');
        $this->db->select('motorcycle.*, motorcycleimage.image_name, motorcycle_type.name as type, motorcycleimage.external');
        $this->db->group_by('motorcycle.id');
        $this->db->_protect_identifiers=true;

        $records = $this->selectRecords('motorcycle', $where);
        return $records;
    }

    public function getReccentlyMotorcycles( $ids ) {
        $where = array();
        $this->db->where_in('motorcycle.id',$ids);
        //$where = array('motorcycle.featured' => '1' );
        $this->db->_protect_identifiers=false;
        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
        $this->db->join(' (select min(priority_number) as priority_number, motorcycle_id, external from motorcycleimage where disable = 0 group by motorcycle_id) motorcycleimageA', 'motorcycleimageA.motorcycle_id = motorcycle.id', 'left');
        $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id and motorcycleimage.priority_number = motorcycleimageA.priority_number ', 'left');
        $this->db->group_by('motorcycle.id');
        $this->db->limit("3");
        $this->db->order_by("motorcycle.id","DESC");
        $this->db->select('motorcycle.*, motorcycleimage.image_name, motorcycle_type.name as type, motorcycleimage.external');
        $this->db->_protect_identifiers=true;
        $records = $this->selectRecords('motorcycle', $where);
        return $records;
    }

    public function saveEnquiry( $data ) {
        $success = $this->createRecord('motorcycle_enquiry', $data, FALSE);
    }

    public function getSalesEmail() {
        $this->db->select('sales_email');
        $where = array('id' => '1');
        $record = $this->selectRecord('contact', $where);
        return $record['sales_email'];
    }

    public function getTotal( $filter ) {
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
        $this->db->select('count(id) as cnt', FALSE);
        $this->db->limit('6');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['cnt'];
    }

    public function getMotorcycleIdByTitle( $title ) {
        $where = array('trim(title)' => $title);
        $this->db->select('id');
        $record = $this->selectRecord('motorcycle', $where);
        return $record['id'];
    }

    public function getMotorcycleIdBySKU( $sku ) {
        $where = array('sku' => trim($sku));
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

        $where = jonathan_generate_likes(array("motorcycle.title", "motorcycle.make", "motorcycle.model", "motorcycle_category.name", "motorcycle.year", "motorcycle_type.name"), $filter, "WHERE");

        $total_count = 0;
        $query = $this->db->query("Select count(*) as cnt from motorcycle");
        foreach ($query->result_array() as $row) {
            $total_count = $row['cnt'];
        }

        // Now, is there a filter?
        $filtered_count = $total_count;
        if ($where != "") {
            // $query = $this->db->query("Select count(distinct part_id) as cnt from part left join partpartnumber using (part_id) left join partnumber  using (partnumber_id)  left join (select partvariation.*, concat(distributor.name, ' ', partvariation.part_number) as partlabel from partvariation join distributor using (distributor_id)) zpartvariation using (partnumber_id) left join partimage using (part_id) $where");
            $query = $this->db->query("Select count(distinct motorcycle.id) as cnt from motorcycle join motorcycle_category on motorcycle.category = motorcycle_category.id join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id $where");
            foreach ($query->result_array() as $row) {
                $filtered_count = $row["cnt"];
            }
        }

        // Finally, run it!
        $query = $this->db->query("Select motorcycle.id, motorcycle.sku, motorcycle_category.name as category_name, motorcycle_type.name as type_name, motorcycle.title, motorcycle.featured, motorcycle.status, motorcycle.condition, motorcycle.retail_price, motorcycle.sale_price, motorcycle.condition, IfNull(motorcycle.mileage, 0) as mileage, motorcycle.source, motorcycleimage.image_name, motorcycleimage.external from motorcycle join motorcycle_category on motorcycle.category = motorcycle_category.id join motorcycle_type on motorcycle.vehicle_type = motorcycle_type.id left join (select motorcycle_id, min(priority_number) as priority_number from motorcycleimage where disable = 0 group by motorcycle_id ) thumbnail_motorcycleimage on motorcycle.id = thumbnail_motorcycleimage.motorcycle_id left join motorcycleimage on thumbnail_motorcycleimage.motorcycle_id = motorcycleimage.motorcycle_id AND thumbnail_motorcycleimage.priority_number = motorcycleimage.priority_number  $where group by motorcycle.id $orderBy limit $limit offset $offset ");
        $rows = $query->result_array();

        return array($rows, $total_count, $filtered_count);
    }
}
