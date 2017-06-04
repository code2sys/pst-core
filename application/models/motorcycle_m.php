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

        if (isset($_GET['brands']) && (is_array($_GET['brands']) || $_GET['brands'] != "")) {
            if (!is_array($_GET['brands'])) {
                $brnds = explode('$', $_GET['brands']);
                $brnds = array_filter($brnds);
            } else {
                $brnds = $_GET['brands'];
            }
            $filter['brands'] = $brnds;
        }
        if (isset($_GET['years']) && (is_array($_GET['years']) || $_GET['years'] != "")) {
            if (!is_array($_GET['years'])) {
                $years = explode('$', $_GET['years']);
                $years = array_filter($years);
            } else {
                $years = $_GET['years'];
            }
            $filter['years'] = $years;
        }
        if (isset($_GET['categories']) && (is_array($_GET['categories']) || $_GET['categories'] != "")) {
            if (!is_array($_GET['categories'])) {
                $categories = explode('$', $_GET['categories']);
                $categories = array_filter($categories);
            } else {
                $categories = $_GET['categories'];
            }
            $filter['categories'] = $categories;
        }
        if (isset($_GET['vehicles']) && (is_array($_GET['vehicles']) || $_GET['vehicles'] != "")) {
            if (!is_array($_GET['vehicles'])) {
                $vehicles = explode('$', $_GET['vehicles']);
                $vehicles = array_filter($vehicles);
            } else {
                $vehicles = $_GET['vehicles'];
            }
            $filter['vehicles'] = $vehicles;
        }

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

    public function getMotorcycles( $filter = array() , $limit = 6, $offset = 0) {
        $where = $this->buildWhere($filter);
        $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id', 'left');
        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
        $this->db->group_by('motorcycle.id');
        $this->db->select('motorcycle.*,motorcycleimage.image_name, motorcycle_type.name as type', FALSE);
        $this->db->limit($limit, $offset);
        $records = $this->selectRecords('motorcycle', $where);
        return $records;
    }

    public function getMotorcycle( $id ){
        $where = array('motorcycle.id' => $id );
        $record = $this->selectRecord('motorcycle', $where);
        $iwhere = array('motorcycleimage.motorcycle_id' => $id );
        $this->db->order_by('motorcycleimage.priority_number asc');
        $record['images'] = $this->selectRecords('motorcycleimage', $iwhere);
        $vwhere = array('motorcycle_video.part_id' => $id );
        $this->db->order_by('motorcycle_video.id asc');
        $record['videos'] = $this->selectRecords('motorcycle_video', $vwhere);
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
        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
        $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id', 'left');
        $this->db->select('motorcycle.*, motorcycleimage.image_name, motorcycle_type.name as type');
        $this->db->group_by('motorcycle.id');
        $records = $this->selectRecords('motorcycle', $where);
        return $records;
    }

    public function getReccentlyMotorcycles( $ids ) {
        $where = array();
        $this->db->where_in('motorcycle.id',$ids);
        //$where = array('motorcycle.featured' => '1' );
        $this->db->join('motorcycle_type', 'motorcycle.vehicle_type = motorcycle_type.id', 'left');
        $this->db->join('motorcycleimage', 'motorcycleimage.motorcycle_id = motorcycle.id', 'left');
        $this->db->group_by('motorcycle.id');
        $this->db->limit("3");
        $this->db->order_by("motorcycle.id","DESC");
        $this->db->select('motorcycle.*, motorcycleimage.image_name, motorcycle_type.name as type');
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
                $bwhere .= " motorcycle.make = '" . $brand."' OR";
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
}
