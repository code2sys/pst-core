<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Parts_M extends Master_M {

    protected $cache_partIsRetail;
    protected $cache_partNumberIsRetail;
    protected $cache_partVariationIsRetail;

    public function getCategoryLongName($category_id) {
        $query = $this->db->query("Select long_name from category where category_id = ?", array($category_id));
        foreach ($query->result_array() as $row) {
            return $row["long_name"];
        }

        return "";
    }

    // It shouldn't take that long so let's justdo simple recursion.
    public function categoryLineage($category_id) {
        if ($category_id == 0 || is_null($category_id)) {
            return array();
        }

        $query = $this->db->query("SElect * from category where category_id = ?", array($category_id));
        $results = $query->result_array();

        if (count($results) > 0) {
            return array_merge($results, $this->categoryLineage($results[0]["parent_category_id"]));
        } else {
            return array();
        }
    }

    public function getPartCategories($part_id) {
        $query = $this->db->query("Select category.* from category join partcategory using (category_id) where part_id = ?", array($part_id));
        return $query->result_array();
    }

    function __construct() {
        parent::__construct();
        $this->cache_partIsRetail = array();
        $this->cache_partNumberIsRetail = array();
        $this->cache_partVariationIsRetail = array();
        $this->load->model("lightspeed_m");
    }

    public function getProduct($id, $activeMachine = NULL) {
        $where = array('part.part_id' => $id);
        $product = $this->selectRecord('part', $where);
        if (!$product['part_id'])
            redirect();
        $product['images'] = $this->getPartImages($product['part_id']);
        $where = array();
        if (!empty($product)) {
            // $category = $this->getSecondBreadCrumb( $product['part_id'] );
            // $category_markup = array();
            // foreach( $category as $cat ) {
            // $category_markup[] = $cat['id'];
            // }
            // $this->db->select('MIN(category.mark_up) as markup');
            // $this->db->where_in('category_id', $category_markup);
            // $where = array('category.mark_up > ' => 0);
            // //$this->db->join('partcategory', 'partcategory.category_id = category.category_id');
            // $categories = $this->selectRecord('category', $where);
            // $this->db->select('MIN(brand.mark_up) as markup, 
            // MAX(brand.exclude_market_place) as exclude_market_place, 
            // MAX(brand.closeout_market_place) as closeout_market_place');
            // $where = array('partbrand.part_id' => $id);
            // $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
            // $brand_markup = $this->selectRecord('brand', $where);

            $product['stock_code'] = $this->getStockCodeByPartId($id);
            $where = array('partpartnumber.part_id' => $product['part_id']);
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
            if (!is_null($activeMachine)) {
                $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partpartnumber.partnumber_id', 'LEFT');
                $where['partnumbermodel.year'] = $activeMachine['year'];
                $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
            }
            $this->db->where('partnumber.price > 0');
            // $this->db->select('partnumber, partnumber.partnumber_id,
            // count(partnumber) as cnt,
            // MIN(partnumber.dealer_sale) AS dealer_sale_min,
            // MAX(partnumber.dealer_sale) AS dealer_sale_max,
            // MIN(partnumber.price) AS price_min,
            // MAX(partnumber.price) AS price_max,
            // MIN(partnumber.sale) AS sale_min, 
            // MAX(partnumber.sale) AS sale_max', FALSE);
            // $this->db->group_by('part_id');
            // $partNumberRec = $this->selectRecord('partnumber', $where);
            $this->db->select('partnumber, partnumber.dealer_sale,partnumber.price, partnumber.sale, partdealervariation.quantity_available as dealer_quantity, partvariation.quantity_available');
            $this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN CASE WHEN partdealervariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
            $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
            $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id', 'left');
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

                // if( @$categories['markup'] ) {
                // $partNumberRec['sale_min'] = number_format(($v['cost']+(($v['cost']*$categories['markup'])/100)),2);
                // }
                // if( @$brand_markup['markup'] ) {
                // $partNumberRec['sale_min'] = number_format(($v['cost']+(($v['cost']*$brand_markup['markup'])/100)),2);
                // }
            }

            // echo '<pre>';
            // print_r($partNumberRec);
            // echo '</pre>';

            if (empty($partNumberRec)) {
                $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
                $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id', 'left');
                $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
                unset($where['partnumbermodel.year']);
                unset($where['partnumbermodel.model_id']);
                $this->db->where('partnumber.price > 0');
                // $this->db->select('partnumber, partnumber.partnumber_id,
                // count(partnumber) as cnt,
                // MIN(partnumber.dealer_sale) AS dealer_sale_min,
                // MAX(partnumber.dealer_sale) AS dealer_sale_max,
                // MIN(partnumber.price) AS price_min,
                // MAX(partnumber.price) AS price_max,
                // MIN(partnumber.sale) AS sale_min, 
                // MAX(partnumber.sale) AS sale_max', FALSE);
                // $this->db->group_by('part_id');
                // $partNumberRec = $this->selectRecord('partnumber', $where);


                $this->db->select('partnumber, partnumber.dealer_sale,partnumber.price, partnumber.sale, partdealervariation.quantity_available as dealer_quantity, partvariation.quantity_available');
                $this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN CASE WHEN partdealervariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
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
            }

            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
            $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partpartnumber.partnumber_id', 'LEFT');
            $this->db->where('partnumber.price > 0');
            $this->db->where('partdealervariation.quantity_available > 0');
            $this->db->select('partnumber, MIN(partnumber.dealer_sale) AS dealer_sale_min, MAX(partnumber.dealer_sale) AS dealer_sale_max', FALSE);
            $this->db->group_by('part_id');
            $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
            $partDealerRec = $this->selectRecord('partnumber', $where);
            //echo 'abcd';exit;

            $this->db->where("partdealervariation.partnumber_id = '" . $partNumberRec['partnumber_id'] . "'");
            $partDealerInventory = $this->selectRecord('partdealervariation');

            $this->db->where("partvariation.partnumber_id = '" . $partNumberRec['partnumber_id'] . "'");
            $partDistributorInventory = $this->selectRecord('partvariation');

            if ($partDistributorInventory['quantity_available'] == 0 && ( $partDealerInventory['quantity_available'] == '0' || !@$partDealerInventory )) {
                $partNumberRec['sale_min'] = $partNumberRec['sale_min'];
                $partNumberRec['sale_max'] = $partNumberRec['sale_max'];
            } else if ($partDistributorInventory['quantity_available'] == '0') {
                $partNumberRec['sale_min'] = $partNumberRec['dealer_sale_min'];
                $partNumberRec['sale_max'] = $partNumberRec['dealer_sale_max'];
            }

            // echo '<pre>';
            // print_r($partNumberRec);
            // echo '</pre>';

            $product['price'] = $this->calculateMarkup($partNumberRec['price_min'], $partNumberRec['price_max'], $partNumberRec['sale_min'], $partNumberRec['sale_max'], @$_SESSION['userRecord']['markup'], $partNumberRec['dealer_sale_min'], $partNumberRec['dealer_sale_max'], $partNumberRec['cnt']);


            $this->db->select('MIN(category.mark_up) as markup');
            $where = array('partcategory.part_id' => $id, 'category.mark_up > ' => 0);
            $this->db->join('partcategory', 'partcategory.category_id = category.category_id');
            $categories = $this->selectRecord('category', $where);

            $this->db->select('MIN(brand.mark_up) as markup, 
											  MAX(brand.exclude_market_place) as exclude_market_place, 
											  MAX(brand.closeout_market_place) as closeout_market_place');
            $where = array('partbrand.part_id' => $id);
            $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
            $brand_markup = $this->selectRecord('brand', $where);


            $product['partnumber'] = $partNumberRec['partnumber'];
            $product['reviews'] = $this->getAverageReviews($product['part_id']);
            // Check for combo
            $combopartIds = $this->checkForCombo($product['part_id']);
            if (is_array($combopartIds)) {
                $PriceArr = array();
                $finalPriceArr = array('retail_min' => 0, 'retail_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
                foreach ($combopartIds as $id)
                    $PriceArr[] = $this->getPriceRange($id, $activeMachine, FALSE);

                foreach ($PriceArr as $pa) {
                    if ($pa['sale_max'] == '') {
                        $pa['sale_max'] = $pa['sale_min'];
                    }
                    $finalPriceArr['retail_min'] += $pa['retail_min'];
                    $finalPriceArr['retail_max'] += $pa['retail_max'];
                    $finalPriceArr['sale_min'] += $pa['sale_min'];
                    $finalPriceArr['sale_max'] += $pa['sale_max'];
                }

                // echo '<pre>';
                // print_r($PriceArr);
                // print_r($finalPriceArr);
                // echo '</pre>';

                $product['price'] = $this->calculateMarkup($finalPriceArr['retail_min'], $finalPriceArr['retail_max'], $finalPriceArr['sale_min'], $finalPriceArr['sale_max'], @$_SESSION['userRecord']['markup']);
            }
        }
        return $product;
    }

    public function getPartByPartNumber($partnumber) {
        $where = array('partnumber' => $partnumber);
        $record = $this->selectRecord('partnumber', $where);
        return $record;
    }

    public function getPartImages($partId) {
        $where = array('part_id' => $partId);
        $images = $this->selectRecords('partimage', $where);
        return $images;
    }

    public function getQuestionAnswerByNumber_old($partnumber) {
        $where = array('partnumber.partnumber' => $partnumber);
        $this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
        $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
        $this->db->group_by('answer');
        $record = $this->selectRecord('partnumber', $where);
        // foreach( $records as $rcrd ) {
        // $record = $rcrd;
        // }
        return $record;
    }

    public function getQuestionAnswerByNumber($partId, $partnumber) {
        $where = array('partquestion.part_id' => $partId, 'productquestion' => 0, "answer != ''" => NULL);
        if (@$activeMachine['model']['model_id']) {
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        if (@$activeMachine['year']) {
            $where['partnumbermodel.year'] = $activeMachine['year'];
        }
        if ($partnumber != '') {
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

    public function checkForCombo($partid) {
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
                if (count($results) > 0) {
                    if (array_key_exists("part_id", $results[0])) {
                        $parts[] = $results[0]['part_id'];
                    }
                }
            }
            return count($parts) > 0 ? $parts : FALSE;
        } else
            return FALSE;
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

    public function validMachines($partId, $activeMachine = NULL) {
        $rides = array();
        $this->db->select('model.make_id, model.model_id, partnumbermodel.year, partnumber.partnumber', FALSE);
        $where = array('partpartnumber.part_id' => $partId);
        if (@$activeMachine['model']['model_id']) {
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        if (@$activeMachine['year']) {
            $where['partnumbermodel.year'] = $activeMachine['year'];
        }
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
        $this->db->join('model', 'model.model_id = partnumbermodel.model_id');
        $this->db->join('partnumber', 'partnumber.partnumber_id = partpartnumber.partnumber_id');
        $this->db->group_by('model.make_id, model.model_id, partnumbermodel.year');
        $this->db->order_by('model.make_id, model.model_id, partnumbermodel.year DESC');
        $rides = $this->selectRecords('partnumbermodel', $where);
        return $rides;
    }

    public function validGarragePartNumber($partId, $activeMachine = NULL) {
        $rides = array();
        $this->db->select('model.make_id, model.model_id, partnumbermodel.year, partnumber.partnumber', FALSE);
        $where = array('partpartnumber.part_id' => $partId);
        if (@$activeMachine['model']['model_id']) {
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        if (@$activeMachine['year']) {
            $where['partnumbermodel.year'] = $activeMachine['year'];
        }
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
        $this->db->join('model', 'model.model_id = partnumbermodel.model_id');
        $this->db->join('partnumber', 'partnumber.partnumber_id = partpartnumber.partnumber_id');
        $this->db->group_by('model.make_id, model.model_id, partnumbermodel.year');
        $this->db->order_by('model.make_id, model.model_id, partnumbermodel.year DESC');
        $rides = $this->selectRecord('partnumbermodel', $where);
        return $rides;
    }

    public function getMachinesDd($partId = NULL) {
        $recs = FALSE;
        if (@$partId) {
            $this->db->select('machinetype.machinetype_id, machinetype.label, machinetype.name');
            $this->db->join('make', 'make.machinetype_id = machinetype.machinetype_id');
            $this->db->join('model', 'model.make_id = make.make_id');
            $this->db->join('partnumbermodel', 'partnumbermodel.model_id = model.model_id');
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
            $this->db->where('partpartnumber.part_id = ' . $partId);
            $this->db->group_by('machinetype.machinetype_id');
        }
        $recs = $this->selectRecords('machinetype');
        if ($recs) {
            $loop = $recs;
            $recs = array();
            foreach ($loop as $rec)
                $recs[$rec['machinetype_id']] = $rec['label'];
        }
        return $recs;
    }

    public function getMakesDd($machineId, $partId = NULL) {
        $recs = FALSE;
        if (@$partId) {
            $this->db->select('make.make_id, make.label, make.name');
            $this->db->join('model', 'model.make_id = make.make_id');
            $this->db->join('partnumbermodel', 'partnumbermodel.model_id = model.model_id');
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
            $this->db->where('partpartnumber.part_id = ' . $partId);
            $this->db->group_by('make.make_id');
        }
        $where = array('machinetype_id' => $machineId, 'make.name IS NOT NULL' => NULL);
        $recs = $this->selectRecords('make', $where);
        if ($recs) {
            $loop = $recs;
            $recs = array();
            foreach ($loop as $rec)
                $recs[$rec['make_id']] = $rec['label'];
        }
        return $recs;
    }

    public function getModelsDd($makeId, $partId = NULL) {
        $recs = FALSE;
        if (@$partId) {
            $this->db->select('model.model_id, model.label, model.name');
            $this->db->join('partnumbermodel', 'partnumbermodel.model_id = model.model_id');
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
            $this->db->where('partpartnumber.part_id = ' . $partId);
            $this->db->group_by('model.model_id');
        }
        $where = array('make_id' => $makeId);
        $this->db->order_by('label');
        $recs = $this->selectRecords('model', $where);
        if ($recs) {
            $loop = $recs;
            $recs = array();
            foreach ($loop as $rec)
                $recs[$rec['model_id']] = $rec['label'];
        }
        return $recs;
    }

    public function getNewModelsDd($makeId, $year, $partId = NULL) {
        $recs = FALSE;
        if (@$partId) {
            $this->db->select('model.model_id, model.label, model.name');
            $this->db->join('partnumbermodel', 'partnumbermodel.model_id = model.model_id');
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
            $this->db->where('partpartnumber.part_id = ' . $partId);
            $this->db->group_by('model.model_id');
        } else {
            $this->db->select("distinct model.*", FALSE);
            $this->db->join('partnumbermodel', 'partnumbermodel.model_id = model.model_id');
        }
        $where = array('make_id' => $makeId, 'year' => $year);
        $this->db->order_by('label');
        $recs = $this->selectRecords('model ', $where);
        if ($recs) {
            $loop = $recs;
            $recs = array();
            foreach ($loop as $rec)
                $recs[$rec['model_id']] = $rec['label'];
        }
        return $recs;
    }

    /*
     * OK, so the idea here is that we have to return a list of acceptable years that could be queried back up...We have a make.
     */
    public function getNewYearsDd($makeId, $partId = NULL) {
        $recs = FALSE;
        if (@$partId) {
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
            $this->db->where('partpartnumber.part_id = ' . $partId);
        }
        $where = array('make_id' => $makeId);
        $this->db->select('distinct partnumbermodel.year ', FALSE);
        $this->db->order_by('year', 'DESC');
        $recs = $this->selectRecords('partnumbermodel join model using (model_id) ', $where);
        if ($recs) {
            $loop = $recs;
            $recs = array();
            foreach ($loop as $rec) {
                $recs[$rec['year']] = $rec['year'];
            }
        }
        return $recs;
    }

    public function getYearsDd($modelId, $partId = NULL) {
        $recs = FALSE;
        if (@$partId) {
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id');
            $this->db->where('partpartnumber.part_id = ' . $partId);
        }
        $where = array('model_id' => $modelId);
        $this->db->select('partnumbermodel.year, partnumbermodel.partnumbermodel_id ', FALSE);
        $this->db->group_by('year');
        $this->db->order_by('year', 'DESC');
        $recs = $this->selectRecords('partnumbermodel', $where);
        if ($recs) {
            $loop = $recs;
            $recs = array();
            foreach ($loop as $rec)
                $recs[$rec['partnumbermodel_id']] = $rec['year'];
        }
        return $recs;
    }

    public function getCategories($parentId = NULL, $filter = array()) {

        $finalRecords = array();
        if (is_null($parentId))
            $where = array('parent_category_id IS NULL' => NULL);
        else
            $where = array('parent_category_id' => $parentId);
        $records = $this->selectRecords('category', $where);
        if ($records) {
            foreach ($records as $rec) {
                $where = array('parent_category_id' => $rec['category_id']);
                $subCat = $this->selectRecords('category', $where);
                if ($subCat) {
                    $insideArray = array();
                    foreach ($subCat as $sub) {
                        // Count Parts
                        $where = array('category_id' => $sub['category_id']);
                        $this->db->where($where);
                        $this->db->from('partcategory');
                        $count = $this->db->count_all_results();
                        $url = $this->categoryReturnURL($sub['category_id']);
                        $insideArray[$sub['category_id']] = array('name' => $sub['name'] . " (" . $count . ")", 'link' => $url);
                    }
                    $finalRecords[$rec['category_id']] = array('label' => $rec['name'], 'subcats' => $insideArray);
                } else
                    $finalRecords[$rec['category_id']] = $rec['name'];
            }
        }
        return $finalRecords;
    }

    public function getCategoryByPartId($partId, $parentCategoryId = NULL) {
        $where = array('part_id' => $partId);
        if ($parentCategoryId) {
            $this->db->join('category', 'category.category_id = partcategory.category_id');
            $this->db->join('category cat2', 'cat2.category_id = category.parent_category_id');
            $where['(category.category_id = ' . $parentCategoryId . ' OR category.parent_category_id = ' . $parentCategoryId . ' OR cat2.category_id = ' . $parentCategoryId . ') '] = NULL;
        }
        $this->db->order_by('category.category_id', 'DESC');
        $this->db->limit(1);
        $record = $this->selectRecord('partcategory', $where);
        return $record['category_id'];
    }

    public function categoryReturnURL($categoryId) {
        $returnURL = '/';
        $this->load->model('parts_m');
        $categories = $this->parts_m->getParentCategores($categoryId);
        if (is_array($categories)) {
            foreach ($categories as $cat)
                $returnURL .= $this->tag_creating($cat) . '_';
        }
        return substr($returnURL, 0, -1);
        ;
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

    public function getFilteredCategories($catId = NULL, $filterArr = array()) {
        $finalRecords = array();
        if (is_null($catId))
            return FALSE;

        $where = array('category_id' => $catId);
        $record = $this->selectRecord('category', $where);
        if ($record) {
            $where = array('parent_category_id' => $record['category_id']);
            $subCat = $this->selectRecords('category', $where);
            if ($subCat) {
                $insideArray = array();
                foreach ($subCat as $sub) {
                    // Count Parts
                    $where = array('category_id' => $sub['category_id']);
                    $this->db->where($where);
                    if (@$filterArr['search']) {
                        if (is_array($filterArr['search'])) {
                            $this->db->join('part', 'part.part_id = partcategory.part_id');

                            $custom_where = "(";
                            $srchTrm = $filterArr['search'];
                            $searchTerm = '';
                            $end = end($srchTrm);
                            unset($srchTrm[count($srchTrm) - 1]);
                            $end1 = trim($end, 's');
                            if ($end1 == $end) {
                                $end1 == '';
                            }
                            $searchTerm = implode(' ', $srchTrm) . " (" . $end . " " . $end1 . ")";
                            $custom_where .= ' MATCH(part.name) AGAINST("' . trim($searchTerm) . '") OR';
                            $custom_where = rtrim($custom_where, 'OR') . ')';
                            $this->db->where($custom_where);

                            //foreach($filterArr['search'] as $search)
                            //{
                            //	$this->db->like('part.name',strtoupper($search));
                            //}
                        }
                    }
                    $this->db->from('partcategory');
                    $count = $this->db->count_all_results();
                    $url = $this->categoryReturnURL($sub['category_id']);
                    $insideArray[$sub['category_id']] = array('label' => $sub['name'], 'count' => $count, 'link' => $url);
                }
                $finalRecords[$record['category_id']] = array('label' => $record['name'], 'subcats' => $insideArray);
            } else {
                if (!is_null($record['parent_category_id'])) {
                    $where = array('category_id' => $record['parent_category_id']);
                    $record = $this->selectRecord('category', $where);
                    if ($record) {
                        $where = array('parent_category_id' => $record['category_id']);
                        $subCat = $this->selectRecords('category', $where);
                        if ($subCat) {
                            $insideArray = array();
                            $original = array();
                            foreach ($subCat as $sub) {
                                // Count Parts
                                $where = array('category_id' => $sub['category_id']);
                                $this->db->where($where);
                                if (@$filterArr['search']) {
                                    if (is_array($filterArr['search'])) {
                                        $this->db->join('part', 'part.part_id = partcategory.part_id');
                                        foreach ($filterArr['search'] as $search) {
                                            $this->db->like('part.name', strtoupper($search));
                                        }
                                    }
                                }
                                $this->db->from('partcategory');
                                $count = $this->db->count_all_results();

                                if ($sub['category_id'] == $catId)
                                    $original[$sub['category_id']] = $sub['name'] . ' (' . $count . ')';
                                $url = $this->categoryReturnURL($sub['category_id']);
                                $insideArray[$sub['category_id']] = array('label' => $sub['name'], 'count' => $count, 'link' => $url);
                            }
                            $insideArray = $original + $insideArray;
                            $finalRecords[$record['category_id']] = array('label' => $record['name'], 'subcats' => $insideArray);
                        } else
                            $finalRecords[$record['category_id']] = $record['name'];
                    }
                }
            }
        }
        return $finalRecords;
    }

    public function getCategory($categoryId) {
        $where = array('category_id' => $categoryId);
        $record = $this->selectRecord('category', $where);
        return $record;
    }

    public function getCategoryDD() {
        $records = $this->selectRecords('category');
        if ($records) {
            $finalArray = array();
            foreach ($records as &$rec) {
                $finalArray[$rec['category_id']] = $rec['long_name'];
            }
        }
        return $finalArray;
    }

    public function getCategoryByName($pieces = NULL) {
        $i = 0;
        foreach ($pieces as $name) {
            $where = array('name' => $name);
            if ($i > 0)
                $where['parent_category_id'] = $last_key;

            $record = $this->selectRecord('category', $where);
            $category_id = $record['category_id'];
            $linkArr[$category_id] = $name;
            $last_key = $category_id;
            $i++;
        }

        return $linkArr;
    }

    public function cleanUpCatAndBrand() {
        $this->db->where('category_id NOT IN (SELECT parent_category_id)');
        $categories = $this->selectRecords('category');
        if ($categories) {
            foreach ($categories as $cat) {
                $where = array('category_id' => $cat['category_id']);
                $this->db->where($where);
                $this->db->from('partcategory');
                $count = $this->db->count_all_results();
                if ($count == 0) {
                    $this->deleteRecord('category', $where);
                }
            }
        }
        $brands = $this->selectRecords('brand');
        if ($brands) {
            foreach ($brands as $brand) {
                $where = array('brand_id' => $brand['brand_id']);
                $this->db->where($where);
                $this->db->from('partbrand');
                $count = $this->db->count_all_results();
                if ($count == 0) {
                    $this->deleteRecord('brand', $where);
                }
            }
        }
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

    public function getSearchCategories($filterArr) {
        $uncategoryFilter = $filterArr;
        unset($uncategoryFilter['category']);
        $records = $this->getSearchResults($uncategoryFilter, 200);
        $finalRecords = array();
        if ($records) {
            foreach ($records as $key1 => $rec) {
                //print_r($rec);
                $where = array('part_id' => $rec['part_id']);
                $this->db->join('category', 'category.category_id = partcategory.category_id');
                $categories = $this->selectRecords('partcategory', $where);

                if (@$categories) {
                    foreach ($categories as $key2 => $cat) {
                        $parentCats = explode(' > ', $cat['long_name']);
                        if (count($parentCats) > 2)
                            continue;
                        //$parentCats = array_slice($parentCats, 0, 2);
                        //echo "<br />";
                        //print_r($parentCats);

                        foreach ($parentCats as $key3 => $parent) {
                            // Set Up Data
                            $i = 0;
                            if ($parent == $cat['name']) {
                                $id = $cat['category_id'];
                                $parent_id = $cat['parent_category_id'];
                            } else {
                                $where = array('name' => $parent);
                                if ((@$key3 - 1) >= 0) {
                                    $where['parent_category_id'] = $id;
                                }
                                $this->db->select('category_id, name, parent_category_id, long_name');
                                $category = $this->selectRecord('category', $where);
                                $id = $category['category_id'];
                                $parent_id = $category['parent_category_id'];
                            }
                            // Count
                            while ((isset($parentCategoryRec[$i][$key3]['id'])) && (@$parentCategoryRec[$i][$key3]['id'] != $id)) {
                                $i++;
                            }
                            if ((@$parentCategoryRec[$i][$key3]['id'] == $id) && ($parentCategoryRec[$i][$key3]['parentId'] == $parent_id))
                                $parentCategoryRec[$i][$key3]['count'] ++;
                            else {
                                $parentCategoryRec[$i][$key3]['label'] = $parent;
                                $parentCategoryRec[$i][$key3]['id'] = $id;
                                $parentCategoryRec[$i][$key3]['count'] = 1;
                                $parentCategoryRec[$i][$key3]['parentId'] = $parent_id;
                                $parentCategoryRec[$i][$key3]['link'] = $this->categoryReturnURL($id);
                            }
                        }
                    }
                }
            }
        }
        // Configure Array
        $finalArray = array();
        $arr = array();
        if (!empty($parentCategoryRec)) {
            foreach ($parentCategoryRec as $rec) {
                foreach ($rec as $key => $cat) {
                    switch ($key) {
                        case '0':
                            if (!$this->checkArrayDuplicacy(trim($cat['label']), $arr)) {
                                $arr[] = trim($cat['label']);
                                $finalArray[$cat['id']] = $cat;
                            }
                            break;
                        case '1':
                            foreach ($finalArray as &$fa) {
                                if ($fa['id'] == $cat['parentId'])
                                    $fa['subcats'][$cat['id']] = $cat;
                            }
                        default:
                            break;
                    }
                }
            }
        }
        /*
          echo "<pre>";
          print_r($arr);
          print_r($finalArray);
          echo "</pre>";
          exit();
         */
        return $finalArray;
    }

    public function getSearchCategoriesBrand($filterArr) {
        $uncategoryFilter = $filterArr;
        unset($uncategoryFilter['category']);
        $records = $this->getSearchResults($uncategoryFilter, NULL);
        $finalRecords = array();
        if ($records) {
            foreach ($records as $key1 => $rec) {
                //print_r($rec);
                $where = array('part_id' => $rec['part_id']);
                $this->db->join('category', 'category.category_id = partcategory.category_id');
                $categories = $this->selectRecords('partcategory', $where);

                if (@$categories) {
                    foreach ($categories as $key2 => $cat) {
                        //$this->checkChildCategory($cat['partcategory_id']);
                        $parentCategoryRec[$cat['partcategory_id']]['label'] = $cat['name'];
                        $parentCategoryRec[$cat['partcategory_id']]['id'] = $cat['category_id'];
                        $parentCategoryRec[$cat['partcategory_id']]['count'] = 0;
                        $parentCategoryRec[$cat['partcategory_id']]['parentId'] = $cat['parent_category_id'];
                        $parentCategoryRec[$cat['partcategory_id']]['link'] = $this->categoryReturnURL($cat['partcategory_id']);
                    }
                }
            }
        }
        // Configure Array
        $finalArray = array();
        $arr = array();
        if (!empty($parentCategoryRec)) {
            foreach ($parentCategoryRec as $rec) {
                if (!$this->checkArrayDuplicacy(trim($rec['label']), $arr)) {
                    if (@$this->checkChildCategory($rec['id'])) {
                        $arr[] = trim($rec['label']);
                        $finalArray[$rec['id']] = $rec;
                    }
                }
            }
        }
        /*
          echo "<pre>";
          print_r($arr);
          print_r($finalArray);
          echo "</pre>";
          exit();
         */
        //echo '<pre>';
        //print_r( $finalArray );
        //echo '</pre>';exit;
        return $finalArray;
    }

    public function checkChildCategory($catId) {
        $where = array('parent_category_id' => $catId);
        $this->db->select('COUNT(*) AS \'count\'', FALSE);

        $count = $this->selectRecord('category', $where);
        if ($count['count'] == 0) {
            return true;
        }
        return false;
        //$this->db->where('');
        //$records = $this->selectRecords('brand');
    }

    public function checkArrayDuplicacy($label, $arr) {
        foreach ($arr as $k => $v) {
            if (strtolower(trim($v)) == strtolower(trim($label))) {
                return true;
                break;
            }
        }
        return false;
    }

    public function returnBrandURL($parameters) {
        $returnURL = '/';
        if (@$parameters['category']) {
            $this->load->model('parts_m');
            if (is_array($parameters['category'])) {
                end($parameters['category']);
                $categoryId = key($parameters['category']);
                $categories = $this->parts_m->getParentCategores($categoryId);
            } else
                $categories = $this->parts_m->getParentCategores($parameters['category']);
            if (is_array($categories)) {
                foreach ($categories as $cat)
                    $returnURL .= $this->tag_creating($cat) . '_';
            }
        }
        if (@$parameters['brand']) {
            if (isset($parameters['brand']['name'])) {
                if ($parameters['brand']['name'] != 'brand')
                    $returnURL .= $this->tag_creating($parameters['brand']['name']) . '_';
            }
            else {
                $brand = $this->getBrand($parameters['brand']);
                $returnURL .= $this->tag_creating($brand['name']) . '_';
            }
        }
        return substr($returnURL, 0, -1);
    }

    public function getBrands($filterArr) {
        $unbrandedFilter = $filterArr;
        unset($unbrandedFilter['brand']);
        if (empty($unbrandedFilter)) {
            $finalRecords = array();
            $this->db->order_by('name');
            $records = $this->selectRecords('brand');

            if ($records) {
                foreach ($records as &$rec) {
                    // $where = array('brand_id' => $rec['brand_id']);
                    // $this->db->select('COUNT(*) AS \'count\'', FALSE);
                    // $count = $this->selectRecord('partbrand', $where);
                    // $rec['count'] = $count['count'];
                    $linkbrandedFilter = $unbrandedFilter;
                    $linkbrandedFilter['brand'] = $rec;
                    $brnd_id = $linkbrandedFilter['brand'];
                    if (is_array($linkbrandedFilter['brand'])) {
                        $brnd_id = $linkbrandedFilter['brand']['brand_id'];
                    }
                    $rec['link'] = $this->returnBrandURL($brnd_id);
                    if ($rec['brand_id'] == $filterArr['brand'])
                        $finalRecords[$rec['brand_id']] = $rec;
                }
                foreach ($records as $rec) {
                    if ($rec['brand_id'] != $filterArr['brand'])
                        $finalRecords[$rec['brand_id']] = $rec;
                }
            }
        }
        else {
            $records = $this->getSearchResults($unbrandedFilter, NULL);
            $finalRecords = array();
            if ($records) {
                foreach ($records as $rec) {
                    $where = array('part_id' => $rec['part_id']);
                    $this->db->join('brand', 'brand.brand_id = partbrand.brand_id');
                    $brands = $this->selectRecords('partbrand', $where);
                    if (@$brands) {
                        foreach ($brands as $brand) {

                            if (@$finalRecords[$brand['brand_id']])
                                ++$finalRecords[$brand['brand_id']]['count'];
                            else {
                                $brand['count'] = 1;
                                $linkbrandedFilter = $unbrandedFilter;
                                $linkbrandedFilter['brand'] = $brand;
                                $brand['link'] = $this->returnBrandURL($linkbrandedFilter);
                                $finalRecords[$brand['brand_id']] = $brand;
                            }
                        }
                    }
                }
            }
            if (@$filterArr['brand'] && !empty($finalRecords) && @$finalRecords[$moveBrandId]) {
                $moveBrandId = $filterArr['brand'];
                $moveBrand = $finalRecords[$moveBrandId];
                $oldFinalRecords = $finalRecords;
                $finalRecords = array($moveBrandId => $moveBrand);
                unset($oldFinalRecords[$moveBrandId]);
                if (!empty($oldFinalRecords)) {
                    foreach ($oldFinalRecords as $key => $rec) {
                        $finalRecords[$key] = $rec;
                    }
                }
            }
        }
        return $finalRecords;
    }

    public function getBrand($brandId) {
        $where = array('brand_id' => $brandId);
        $record = $this->selectRecord('brand', $where);
        return $record;
    }

    public function getBrandImages() {
        $where = array('image != \'\'' => NULL);
        $this->db->select('brand_id, image, name');
        $this->db->limit(8);
        $this->db->order_by('RAND()');
        $records = $this->selectRecords('brand', $where);
        if (@$records) {
            foreach ($records as &$rec) {
                $rec['link'] = $this->tag_creating($rec['name']);
            }
        }

        return $records;
    }

    public function getPriceRange($partId, $activeMachine = NULL, $checkCombo = TRUE) {
        $combopartIds = FALSE;
        if ($checkCombo)
            $combopartIds = $this->checkForCombo($partId);
        if (is_array($combopartIds)) {
            $PriceArr = array();
            $finalPriceArr = array('retail_min' => 0, 'retail_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
            foreach ($combopartIds as $id)
                $PriceArr[] = $this->getPriceRange($id, $activeMachine, FALSE);

            foreach ($PriceArr as $pa) {
                $finalPriceArr['retail_min'] += $pa['retail_min'];
                $finalPriceArr['retail_max'] += $pa['retail_max'];
                $finalPriceArr['sale_min'] += $pa['sale_min'];
                $finalPriceArr['sale_max'] += $pa['sale_max'];
            }
            $finalPriceArr = $this->calculateMarkup($finalPriceArr['retail_min'], $finalPriceArr['retail_max'], $finalPriceArr['sale_min'], $finalPriceArr['sale_max'], @$_SESSION['userRecord']['markup']);
        } else {
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

            $finalPriceArr = $this->calculateMarkup($partNumberRec['price_min'], $partNumberRec['price_max'], $partNumberRec['sale_min'], $partNumberRec['sale_max'], @$_SESSION['userRecord']['markup']);
        }
        return $finalPriceArr;
    }

    public function calculateMarkup($retailmin, $retailmax = 0, $min, $max = 0, $userMarkUp = NULL, $dealer_sale_min = 0, $dealer_sale_max = 0, $cnt = 0) {
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

        if ($dealer_sale_max > $returnArr['sale_max']) {
            $returnArr['sale_max'] = $dealer_sale_max;
            //$returnArr['sale_max'] = FALSE;
        }

        if ($dealer_sale_min < $returnArr['sale_min'] && $dealer_sale_min > 0) {
            $returnArr['sale_min'] = $dealer_sale_min;
            //$returnArr['sale_max'] = FALSE;
        }

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

        return $returnArr;
    }

    public function getStockCodeByPartId($partId) {
        $where = array('part_id' => $partId);
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partvariation.partnumber_id');
        $this->db->group_by('stock_code');
        $records = $this->selectRecords('partvariation', $where);
        if (count($records) == 2)
            return 'Closeout';
        else
            return $records[0]['stock_code'];
    }

    public function getFeaturedProducts($categoryId, $limit = NULL) {
        $returnArr = array();
        $productArr = array();
        if ($categoryId) {
            $categories = $this->getCategories($categoryId);
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');

            $where = ' (partcategory.category_id = ' . $categoryId;

            if (@$categories) {
                foreach ($categories as $catId => $catArr) {
                    $where .= ' OR partcategory.category_id = ' . $catId;
                    if (is_array(@$catArr['subcats'])) {
                        foreach ($catArr['subcats'] as $subCatId => $name) {
                            $where .= ' OR partcategory.category_id = ' . $subCatId;
                        }
                    }
                }
            }
            $where .= ' )';
            $this->db->where($where, NULL, FALSE);
        }
        $this->db->select('part.part_id, name as label, part.call_for_price, part.universal_fitment');
        $where = array('featured' => 1);
        $this->db->group_by('part.part_id');
        if (is_numeric($limit))
            $this->db->limit($limit);
        $productArr = $this->selectRecords('part', $where);

        if (!empty($productArr)) {
            foreach ($productArr as &$rec) {
                $rec['activeRide'] = FALSE;
                $rec['price'] = $this->getPriceRange($rec['part_id']);
                if ((@$_SESSION['garage']) && ($this->validMachines($rec['part_id'], @$_SESSION['activeMachine'])))
                    $rec['activeRide'] = TRUE;
                $where = array('part_id' => $rec['part_id']);
                $rec['images'] = $this->selectRecords('partimage', $where);
                $rec['reviews'] = $this->getAverageReviews($rec['part_id']);
                $rec['stock_code'] = $this->getStockCodeByPartId($rec['part_id']);
            }
        }

        $returnArr['label'] = 'Featured Products';
        $returnArr['page'] = 'shopping/productlist/featured/' . $categoryId . '/';
        $returnArr['products'] = $productArr;
        return $returnArr;
    }

    public function getTopRatedProducts($categoryId, $limit = NULL) {
        $returnArr = array();
        $productArr = array();
        if ($categoryId) {
            $categories = $this->getCategories($categoryId);
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');

            $where = ' (partcategory.category_id = ' . $categoryId;

            if (@$categories) {
                foreach ($categories as $catId => $catArr) {
                    $where .= ' OR partcategory.category_id = ' . $catId;
                    if (is_array(@$catArr['subcats'])) {
                        foreach ($catArr['subcats'] as $subCatId => $name) {
                            $where .= ' OR partcategory.category_id = ' . $subCatId;
                        }
                    }
                }
            }
            $where .= ' )';
            $this->db->where($where, NULL, FALSE);
        }
        $this->db->select('part.part_id, name as label, AVG(reviews.rating) as rating, reviews.review, part.call_for_price, part.universal_fitment');
        $this->db->join('reviews', 'reviews.part_id = part.part_id');
        $where = array();
        $this->db->group_by('part.part_id');
        $this->db->order_by('rating DESC');
        if (is_numeric($limit))
            $this->db->limit($limit);
        $productArr = $this->selectRecords('part', $where);

        if (!empty($productArr)) {
            foreach ($productArr as &$rec) {
                $rec['activeRide'] = FALSE;
                $rec['price'] = $this->getPriceRange($rec['part_id']);
                if ((@$_SESSION['garage']) && ($this->validMachines($rec['part_id'], @$_SESSION['activeMachine'])))
                    $rec['activeRide'] = TRUE;
                $where = array('part_id' => $rec['part_id']);
                $rec['images'] = $this->selectRecords('partimage', $where);
                $rec['stock_code'] = $this->getStockCodeByPartId($rec['part_id']);
            }
        }

        return $productArr;
    }

    public function getProductDeals($categoryId = NULL, $limit = NULL) {
        $where = array('key' => 'deal_percentage');
        $record = $this->selectRecord('config', $where);
        $dealPercent = $record['value'];
        $returnArr = FALSE;
        if ($categoryId)
            $categories = $this->getCategories($categoryId);

        $where = array();

        if (is_numeric($limit))
            $this->db->limit($limit);
        // Filter for Anything on Sale	
        $this->db->select('part.part_id, part.name as label, part.call_for_price, part.universal_fitment', FALSE);
        $where['(partnumber.price - partnumber.sale) > (.' . $dealPercent . ' * partnumber.price)'] = NULL;
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');
        $this->db->group_by('part.part_id');
        $this->db->order_by('RAND()');
        // Filter for Categories 3 levels deep - must go after part join
        if (is_numeric($categoryId)) {
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');

            $catwhere = ' (partcategory.category_id = ' . $categoryId;

            if (@$categories) {
                foreach ($categories as $catId => $catArr) {
                    $catwhere .= ' OR partcategory.category_id = ' . $catId;
                    if (is_array(@$catArr['subcats'])) {
                        foreach ($catArr['subcats'] as $subCatId => $name) {
                            $catwhere .= ' OR partcategory.category_id = ' . $subCatId;
                        }
                    }
                }
            }
            $catwhere .= ' )';
            $this->db->where($catwhere, NULL, FALSE);
        }
        // Run Query
        $returnArr['products'] = $this->selectRecords('partnumber', $where);
        // Finish up necessary fields
        if (!empty($returnArr['products'])) {
            $returnArr['label'] = 'Top Deals';
            $returnArr['page'] = 'shopping/productlist/deal/' . $categoryId . '/category/' . $categoryId . '/';
            foreach ($returnArr['products'] as &$product) {
                $product['activeRide'] = FALSE;
                $product['price'] = $this->getPriceRange($product['part_id']);
                if ((@$_SESSION['garage']) && ($this->validMachines($product['part_id'])))
                    $product['activeRide'] = TRUE;
                $where = array('part_id' => $product['part_id']);
                $product['images'] = $this->selectRecords('partimage', $where);
                $product['reviews'] = $this->getAverageReviews($product['part_id']);
                $product['stock_code'] = $this->getStockCodeByPartId($product['part_id']);
            }
            return $returnArr;
        } else
            return FALSE;
    }

    public function getAverageReviews($part_id) {
        $where = array('part_id' => $part_id);
        $this->db->select('SUM(rating) as rating, COUNT(id) as qty');
        $record = $this->selectRecord('reviews', $where);
        $returnRec = FALSE;
        if ($record['qty'] > 0) {
            $returnRec = array('average' => ($record['rating'] / $record['qty']), 'qty' => $record['qty']);
        }
        return $returnRec;
    }

    public function getTopSellers($categoryId = NULL) {
        $returnArr = FALSE;

        if ($categoryId)
            $categories = $this->getCategories($categoryId);
        $this->db->limit(4);
        // Filter for Anything on Sale	
        $this->db->select('product_sku, count(*) qty, part.part_id, part.name as label, part.image, part.call_for_price, part.universal_fitment');
        $this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku');
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');
        $this->db->join('order', 'order.id = order_product.order_id');
        $this->db->group_by('product_sku');
        $this->db->order_by('qty', 'DESC');
        // Filter for Categories 3 levels deep - must go after part join

        if ($categoryId) {
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');

            $where = ' (partcategory.category_id = ' . $categoryId;

            if (@$categories) {
                foreach ($categories as $catId => $catArr) {
                    $where .= ' OR partcategory.category_id = ' . $catId;
                    if (is_array(@$catArr['subcats'])) {
                        foreach ($catArr['subcats'] as $subCatId => $name) {
                            $where .= ' OR partcategory.category_id = ' . $subCatId;
                        }
                    }
                }
            }
            $where .= ' )';
            $this->db->where($where, NULL, FALSE);
        }
        $where = array('order_date IS NOT NULL' => NULL);

        // Run Query
        $returnArr['products'] = $this->selectRecords('order_product', $where);

        // Finish up necessary fields
        if (!empty($returnArr['products'])) {
            $returnArr['label'] = 'Top Sellers';
            foreach ($returnArr['products'] as &$product) {
                $product['activeRide'] = FALSE;
                $product['price'] = $this->getPriceRange($product['part_id']);
                if ((@$_SESSION['garage']) && ($this->validMachines($product['part_id'], @$_SESSION['activeMachine'])))
                    $product['activeRide'] = TRUE;
                $where = array('part_id' => $product['part_id']);
                $product['images'] = $this->selectRecords('partimage', $where);
                $product['reviews'] = $this->getAverageReviews($product['part_id']);
                $product['stock_code'] = $this->getStockCodeByPartId($product['part_id']);
            }
            return $returnArr;
        } else
            return FALSE;
    }

    public function getNewArrivalsBrand($brandId) {
        $returnArr = FALSE;

        $this->db->limit(4);
        $this->db->select('part.part_id, part.name as label, part.image, part.call_for_price, part.universal_fitment');
        //$this->db->group_by('part.part_id');
        $this->db->order_by('part_id', 'random');
        // Filter for Categories 3 levels deep - must go after part join

        if ($brandId) {
            $this->db->join('partbrand', 'partbrand.part_id = part.part_id');

            $where = ' (partbrand.brand_id = ' . $brandId;

            $where .= ' )';
            $this->db->where($where, NULL, FALSE);
        }

        //$cus_where = ' (part.created between now() and DATE_SUB(part.created, INTERVAL 7 MONTH)';
        //$cus_where .= ' )';
        //$this->db->where($where1, NULL, FALSE);
        //$cus_where = array('(part.created between now() and DATE_SUB(part.created, INTERVAL 7 MONTH))');

        $endDate = date('Y-m-d H:i:s', strtotime("-7 months", strtotime(date('Y-m-d H:i:s'))));
        $cus_where = array('part.created >=' => $endDate);

        // Run Query
        $returnArr['products'] = $this->selectRecords('part', $cus_where);

        // Finish up necessary fields
        if (!empty($returnArr['products'])) {
            $returnArr['label'] = 'New Arrivals';
            foreach ($returnArr['products'] as &$product) {
                $product['activeRide'] = FALSE;
                $product['price'] = $this->getPriceRange($product['part_id']);
                if ((@$_SESSION['garage']) && ($this->validMachines($product['part_id'], @$_SESSION['activeMachine'])))
                    $product['activeRide'] = TRUE;
                $where = array('part_id' => $product['part_id']);
                $product['images'] = $this->selectRecords('partimage', $where);
                $product['reviews'] = $this->getAverageReviews($product['part_id']);
                $product['stock_code'] = $this->getStockCodeByPartId($product['part_id']);
            }
            return $returnArr;
        }
        //return FALSE;
    }

    public function getTopSellersBrand($brandId = NULL) {
        $returnArr = FALSE;

        //if($categoryId)
        //	$categories = $this->getCategories($categoryId);
        $this->db->limit(4);
        // Filter for Anything on Sale	
        $this->db->select('product_sku, count(*) qty, part.part_id, part.name as label, part.image, part.call_for_price, part.universal_fitment');
        $this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku');
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');
        $this->db->join('order', 'order.id = order_product.order_id');
        $this->db->group_by('product_sku');
        $this->db->order_by('qty', 'DESC');
        // Filter for Categories 3 levels deep - must go after part join

        if ($brandId) {
            $this->db->join('partbrand', 'partbrand.part_id = part.part_id');

            $where = ' (partbrand.brand_id = ' . $brandId;

            $where .= ' )';
            $this->db->where($where, NULL, FALSE);
        }
        $where = array('order_date IS NOT NULL' => NULL);

        // Run Query
        $returnArr['products'] = $this->selectRecords('order_product', $where);

        // Finish up necessary fields
        if (!empty($returnArr['products'])) {
            $returnArr['label'] = 'Top Sellers';
            foreach ($returnArr['products'] as &$product) {
                $product['activeRide'] = FALSE;
                $product['price'] = $this->getPriceRange($product['part_id']);
                if ((@$_SESSION['garage']) && ($this->validMachines($product['part_id'], @$_SESSION['activeMachine'])))
                    $product['activeRide'] = TRUE;
                $where = array('part_id' => $product['part_id']);
                $product['images'] = $this->selectRecords('partimage', $where);
                $product['reviews'] = $this->getAverageReviews($product['part_id']);
                $product['stock_code'] = $this->getStockCodeByPartId($product['part_id']);
            }
            return $returnArr;
        } else {
            $this->db->limit(4);
            // Filter for Anything on Sale	
            $this->db->select('count(*) qty, part.part_id, part.name as label, part.image, part.call_for_price, part.universal_fitment');
            $this->db->join('partbrand', 'partbrand.part_id = part.part_id');
            $this->db->where('partbrand.brand_id = ' . $brandId);
            $this->db->order_by('part_id', 'RANDOM');
            $returnArr['products'] = $this->selectRecords('part');
            $returnArr['label'] = 'Top Sellers';
            foreach ($returnArr['products'] as &$product) {
                $product['activeRide'] = FALSE;
                $product['price'] = $this->getPriceRange($product['part_id']);
                if ((@$_SESSION['garage']) && ($this->validMachines($product['part_id'], @$_SESSION['activeMachine'])))
                    $product['activeRide'] = TRUE;
                $where = array('part_id' => $product['part_id']);
                $product['images'] = $this->selectRecords('partimage', $where);
                $product['reviews'] = $this->getAverageReviews($product['part_id']);
                $product['stock_code'] = $this->getStockCodeByPartId($product['part_id']);
            }
            return $returnArr;
        }
        //return FALSE;
    }

    public function getRecentlyViewed($categoryId = 0, $recentlyViewedList, $limit = NULL) {
        if (empty($recentlyViewedList))
            return FALSE;

        $returnArr = array();
        $returnArr['label'] = 'Recently Viewed';
        $returnArr['page'] = 'shopping/productlist/recentlyViewed/' . $categoryId . '/';
        if (!is_null($limit))
            $i = 0;
        foreach ($recentlyViewedList as $id) {
            if (!is_null($limit))
                $i++;
            if (is_numeric($categoryId)) {
                $this->db->join('partcategory', 'partcategory.part_id = part.part_id');
                $this->db->join('category', 'category.category_id = partcategory.category_id');
                $this->db->join('category cat2', 'cat2.category_id = category.parent_category_id');
                $where['(category.category_id = ' . $categoryId . ' OR category.parent_category_id = ' . $categoryId . ' OR cat2.category_id = ' . $categoryId . ') '] = NULL;
            }
            $this->db->select('part.part_id, part.name as label, part.image, part.call_for_price, part.universal_fitment ');
            $where = array('part.part_id' => $id);
            $product = $this->selectRecord('part', $where);

            if (!empty($product)) {
                $product['activeRide'] = FALSE;
                $product['price'] = $this->getPriceRange($id);
                if ((@$_SESSION['garage']) && ($this->validMachines($product['part_id'], @$_SESSION['activeMachine'])))
                    $product['activeRide'] = TRUE;
                $where = array('part_id' => $product['part_id']);
                $product['images'] = $this->selectRecords('partimage', $where);
                $product['reviews'] = $this->getAverageReviews($product['part_id']);
                $product['stock_code'] = $this->getStockCodeByPartId($product['part_id']);
                $returnArr['products'][] = $product;
            }
            if (!is_null($limit) && $i >= $limit)
                break;
        }
        return $returnArr;
    }

    /*     * **************************************** WISHLIST ************************************ */

    public function getWishList() {
        $where = array('user_id' => @$_SESSION['userRecord']['id']);
        if ($this->recordExists('wishlist', $where)) {
            $wishlist = $this->selectRecord('wishlist', $where);
            $where = array('wishlist_id' => $wishlist['id']);
            $this->db->select('wishlistpart_id, part.part_id, wishlist_part.rideName as name, partnumber.price, image, partnumber.partnumber, partnumber.sale, part.call_for_price, part.universal_fitment');
            $this->db->join('part', 'part.part_id = wishlist_part.part_id');
            $this->db->join('partnumber', 'partnumber.partnumber = wishlist_part.partnumber');
            $wishlistparts = $this->selectRecords('wishlist_part', $where);
        } else
            return FALSE;

        if (!empty($wishlistparts)) {
            foreach ($wishlistparts as &$rec) {
                $rec['activeRide'] = FALSE;
                if ((@$_SESSION['garage']) && ($this->validMachines($rec['part_id'], @$_SESSION['activeMachine'])))
                    $rec['activeRide'] = TRUE;
                $rec['images'] = $this->getPartImages($rec['part_id']);
            }
        }

        $returnArr['label'] = 'Wish List';
        $returnArr['products'] = $wishlistparts;
        return $returnArr;
    }

    public function updateWishList($partRec, $userId) {
        $where = array('user_id' => $userId);
        if ($this->recordExists('wishlist', $where)) {
            $wishlist = $this->selectRecord('wishlist', $where);
            $data = array('partnumber' => $partRec['partnumber'], 'wishlist_id' => $wishlist['id'], 'part_id' => $partRec['part_id'], 'rideName' => $partRec['display_name']);
            $this->createRecord('wishlist_part', $data, FALSE);
        } else {
            $data = array('user_id' => $userId, 'status' => 1, 'privacy' => 1, 'name' => 'default');
            $wishlistId = $this->createRecord('wishlist', $data, FALSE);
            $data = array('partnumber' => $partRec['partnumber'], 'wishlist_id' => $wishlistId, 'part_id' => $partRec['part_id']);
            $this->createRecord('wishlist_part', $data, FALSE);
        }
    }

    public function removeWishListItem($id) {
        $where = array('wishlistpart_id' => $id);
        $this->deleteRecord('wishlist_part', $where);
    }

    /*     * **************************************** END WISHLIST *********************************************** */

    public function updateCart() {
    
        foreach ($_SESSION['cart'] as $part => $rec) {
            $rec["finalPrice"] = 0.9 * floatVal(preg_replace("/[^0-9\.\-]/", "", (array_key_exists("finalPrice", $rec) ? $rec["finalPrice"] : $rec["price"])));
            $_SESSION['cart'][$part] = $rec;
        }
        $shoppingCart = json_encode($_SESSION['cart']);
        $data = array('cart' => $shoppingCart, 'user_id' => @$_SESSION['userRecord']['id']);
        $where = array('user_id' => @$_SESSION['userRecord']['id']);
        if (( @$_SESSION['userRecord']['id']) && ($this->recordExists('cart', $where)))
            $this->updateRecords('cart', $data, $where, FALSE);
        else
            $this->createRecord('cart', $data, FALSE);
    }

    public function getCart() {
        $where = array('user_id' => @$_SESSION['userRecord']['id']);
        $record = $this->selectRecord('cart', $where);
        if (@$record['cart'])
            $cart = json_decode($record['cart'], TRUE);
        return @$cart;
    }

    /*
     * JLB 10-04-17
     * You know what's insane? This looks so much like getSearchResults... Why couldn't some or any of this have been put in a single funciton? This sort of crap always bites us in the end.
     *
     * JLB 02-19-18
     * I remind you; whoever did this was a dumb fuck, let me tell you. Why are these so similar but different?
     *
     */
    public function getSearchCount($filterArr = NULL, $activeMachine = NULL) {
        // BEGIN DEAL -  Must get this before buidling Search SQL in case it is needed.
        $where = array('key' => 'deal_percentage');
        $record = $this->selectRecord('config', $where);
        $dealPercent = $record['value'];
        // END DEAL
        // BEGIN GET SUBCATEGORIES
        if (@$filterArr['category']) {
            end($filterArr['category']);
            $first_key = key($filterArr['category']);
            $categories = $this->getCategories($first_key, $filterArr);
        }
        // END GET SUBCATEGORIES 

        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');

        $this->setHighLevelSearchCriteria($filterArr, $dealPercent);
        if (@$filterArr['category']) {
            end($filterArr['category']);
            $first_key = key($filterArr['category']);
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');

            $where = ' (partcategory.category_id = ' . $first_key;

            if (@$categories) {
                foreach ($categories as $catId => $catArr) {
                    $where .= ' OR partcategory.category_id = ' . $catId;
                    if (is_array(@$catArr['subcats'])) {
                        foreach ($catArr['subcats'] as $subCatId => $name) {
                            $where .= ' OR partcategory.category_id = ' . $subCatId;
                        }
                    }
                }
            }
            $where .= ' )';
            $this->db->where($where, NULL, FALSE);
        }

        if (@$filterArr['brand']) {
            $this->db->join('partbrand', 'partbrand.part_id = part.part_id');
            $this->db->where('partbrand.brand_id = ' . $filterArr['brand']);
        }

        $partvariation_joined = false;
        if (@$filterArr['extra']) {
            if ($filterArr['extra'] == 'closeout') {
                $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
                $partvariation_joined = true;
                $this->db->where("partvariation.stock_code = 'Closeout'");
            } else if ($filterArr['extra'] == 'featured') {
                $this->db->where("part.featured = '1'");
            } else if ($filterArr['extra'] == 'arrival') {
                $endDate = date('Y-m-d H:i:s', strtotime("-7 months", strtotime(date('Y-m-d H:i:s'))));
                //$cus_where = array('part.created >=' =>  $endDate);
                $this->db->where("part.created >= '$endDate'");
            }
        }

        if (@$filterArr['search']) {
            if (is_array($filterArr['search'])) {
                // JLB 02-19-18
                // I am fairly certain the geniuses who made this were using
                // a different search in both cases...
                $custom_where = $this->_searchCustomWhere($filterArr);
                $this->db->where($custom_where);
            }
        }

        // JLB I bet this has to be up here, too...
        if (!is_null($activeMachine)) {
            $this->db->join('partnumbermodel', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id', 'LEFT');
            $this->db->where(sprintf(" (partnumbermodel.year is NULL OR partnumbermodel.year = %d) AND  (partnumbermodel.model_id is NULL OR partnumbermodel.model_id = %d) ", $activeMachine['year'], $activeMachine['model']['model_id']), NULL, FALSE);
//                    $where['partnumbermodel.year'] = $activeMachine['year'];
//                    $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }

        $this->db->group_by('part.part_id');
        if (!$partvariation_joined) {
            $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        }
        $this->db->where("partvariation.from_lightspeed", 0, FALSE);

        $records = $this->selectRecords('partnumber');
        $count = count($records);
        return $count;
    }

    public function getFilterQuestions($filter = NULL) {
        $where = array('productquestion' => 1, '(partnumber.universalfit > 0 OR partnumbermodel.partnumbermodel_id is NOT NULL)' => NULL);
        $this->db->select(' `productquestion_id` as id, `partquestion`.`question`,`partnumberpartquestion`.`answer`, COUNT(DISTINCT part.name) as qty', FALSE);
        $this->db->join('partnumber', 'partnumber.partnumber_id = partnumberpartquestion.partnumber_id');
        $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');

        if (@$filter['search']) {
            if (is_array($filter['search'])) {
                $custom_where = "(";
                $srchTrm = explode(' ', $filter['search'][0]);
                $searchTerm = '';
                $end = end($srchTrm);
                unset($srchTrm[count($srchTrm) - 1]);
                $end1 = trim($end, 's');
                if ($end1 == $end) {
                    $end1 = '';
                }
                $searchTerm = implode(' ', $srchTrm) . " " . $end . " " . $end1;
                $searchTerm1 = implode(' ', $srchTrm) . " " . $end . " " . $end1;
                if (strpos(trim($searchTerm), 'cl-17') !== false || strpos(trim($searchTerm), 'cl 17') !== false) {
                    $searchTerm = 'hjc 2015 cl-17';
                    $custom_where .= " part.name like '%" . trim($searchTerm) . "%' OR";
                } else if (strlen(trim($searchTerm)) < 5 || strpos(trim($searchTerm), '-') !== false) {
                    $custom_where .= " part.name like '%" . trim($searchTerm1) . "%' OR";
                } else {
                    $custom_where .= ' MATCH(part.name) AGAINST("' . trim(str_replace('-', ' ', $searchTerm)) . '") OR';
                }
                if (strpos($searchTerm1, '-') !== false) {
                    
                }

                $custom_where = rtrim($custom_where, 'OR') . ')';
                $this->db->where($custom_where);
            }
        } else {
            $filter['search'][0] = '';
        }


        // if (is_array(@$filter['search'])) {
        // foreach ($filter['search'] as $search) {
        // $this->db->like('part.name', strtoupper($search));
        // }
        // }
        if (@$filter['category']) {
            end($filter['category']);
            $categoryId = key($filter['category']);
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');
            $where['partcategory.category_id'] = $categoryId;
        }
        if (@$filter['brand']) {
            $this->db->join('partbrand', 'partbrand.part_id = part.part_id');
            $where['partbrand.brand_id'] = $filter['brand'];
        }

        $this->db->group_by('partnumberpartquestion.answer');
        $this->db->order_by('partquestion.partquestion_id, partnumberpartquestion.answer');
        $records = $this->selectRecords('partnumberpartquestion', $where);
        //echo $this->db->last_query();exit;

        if (is_array($records)) {
            $question = @$filter['question'];
            foreach ($records as &$rec) {
                $filter['question'] = $rec['answer'];
                $rec['link'] = $this->returnQuestionURL($filter);
            }
            $filter['question'] = $question;
        }
        return $records;
    }

    public function age_gender() {
        $where = array('productquestion' => 1, '(partnumber.universalfit > 0 OR partnumbermodel.partnumbermodel_id is NOT NULL)' => NULL);
        $this->db->select(' `productquestion_id` as id,`partnumberpartquestion`.`answer`', FALSE);
        $this->db->join('partnumber', 'partnumber.partnumber_id = partnumberpartquestion.partnumber_id');
        $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');
        $this->db->group_by('partnumberpartquestion.answer');
        $this->db->order_by('partquestion.partquestion_id, partnumberpartquestion.answer');
        $records = $this->selectRecords('partnumberpartquestion', $where);
        $rcrd = array();
        if (is_array($records)) {
            $question = @$filter['question'];
            foreach ($records as &$rec) {
                if (!is_numeric($rec['answer'][0])) {
                    $rcrd[] = $rec;
                }
            }
            $filter['question'] = $question;
        }
        return $rcrd;
    }

    private function returnQuestionURL($parameters) {
        $returnURL = '/';
        if (@$parameters['category']) {
            $this->load->model('parts_m');
            if (is_array($parameters['category'])) {
                end($parameters['category']);
                $categoryId = key($parameters['category']);
                $categories = $this->parts_m->getParentCategores($categoryId);
            } else
                $categories = $this->parts_m->getParentCategores($parameters['category']);
            if (is_array($categories)) {
                foreach ($categories as $cat)
                    $returnURL .= $this->tag_creating($cat) . '_';
            }
        }
        if (@$parameters['brand']) {
            if (isset($parameters['brand']['name'])) {
                if ($parameters['brand']['name'] != 'brand')
                    $returnURL .= $this->tag_creating($parameters['brand']['name']) . '_';
            }
            else {
                $brand = $this->getBrand($parameters['brand']);
                $returnURL .= $this->tag_creating($brand['name']) . '_';
            }
        }
        if (@$parameters['question']) {
            if (is_array($parameters['question'])) {
                foreach ($parameters['question'] as $key => $quest) {
                    $returnURL .= $this->tag_creating($quest) . '_';
                }
            } else
                $returnURL .= $this->tag_creating($parameters['question']) . '_';
        }
        return substr($returnURL, 0, -1);
    }

    public function createSearchParametersFromURL($pieces) {
        $searchArr = array();
        if (is_array($pieces)) {
            $categoryId = NULL;
            $level = 'category';
            foreach ($pieces as $key => $piece) {
                if ($piece == 'oneal') {
                    $piece = "o'neal";
                }
                if ($piece == 't-shirts') {
                    $piece = "t shirts";
                }
                if ($piece == 'closeout') {
                    $searchArr['extra'] = 'closeout';
                } else if ($piece == 'featured') {
                    $searchArr['extra'] = 'featured';
                } else if ($piece == 'arrival') {
                    $searchArr['extra'] = 'arrival';
                } else {
                    $data = $this->whatAmI($piece, $categoryId, $level, $searchArr);

                    if ($data[0] == 'category') {
                        $categoryId = $data[1];
                        $searchArr[$data[0]] = $data[1];
                    } elseif ($data[0] == 'brand')
                        $searchArr[$data[0]] = array('id' => $data[1], 'name' => $data[2]);
                    elseif ($data[0] == 'question')
                        $searchArr[$data[0]] = array($data[1] => $data[2]);
                }
            }
            return $searchArr;
        }
    }

    private function whatAmI($piece, $categoryId = NULL, $level, $searchArr) {
        $name = explode('-', $piece);
        if ($level == 'category') { // Skip these steps if we are already at the brand level
            // Category Search
            foreach ($name as $like)
                $this->db->like('name', $like);
            if (is_null($categoryId))
                $this->db->where('parent_category_id IS NULL');
            else
                $this->db->where(array('parent_category_id' => $categoryId));
            $category = $this->selectRecord('category');
            if ($category)
                return array('category', $category['category_id']);
            // Brand Search
            $bname = implode(' ', $name);
            //foreach($name as $like) {
            //$st=" name like '".$bname."%'";
            $st = 'name like "' . $bname . '%"';
            $this->db->where($st, NULL, FALSE);
            //}
            //$this->db->like('name', $like);
            $brand = $this->selectRecord('brand');
            if ($brand)
                return array('brand', $brand['brand_id'], $brand['name']);
        }
        // Question
        if (!empty($searchArr)) {
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');

            if ($searchArr['category']) {
                $this->db->join('partcategory', 'partpartnumber.part_id = partcategory.part_id');
                $this->db->where('category_id = ' . $searchArr['category']);
            }
            if (@$searchArr['brand']) {
                $this->db->join('partbrand', 'partpartnumber.part_id = partbrand.part_id');
                $this->db->where('brand_id = ' . $searchArr['brand']['id']);
            }
            $this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
            $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
            foreach ($name as $like)
                $this->db->like('answer', $like);
            $question = $this->selectRecord('partnumber');
            return array('question', $question['question'], $question['answer']);
        }
    }
    
    protected function _searchCustomWhere($filterArr) {
        $custom_where = "(";
        //$field = " FIELD(`ord`,".implode(',',$filterArr['search']).')';
        $srchTrm = explode(' ', $filterArr['search'][0]);

        $custom_where .= "partvariation.part_number = '" . implode("' OR partvariation.part_number = '", array_map("addslashes", $srchTrm)) . "' OR ";
        $custom_where .= "partvariation.manufacturer_part_number = '" . implode("' OR partvariation.manufacturer_part_number = '", array_map("addslashes", $srchTrm)) . "' OR ";


        $searchTerm = '';

        // JLB 02-19-18
        // I admit I have no idea why they are doing this. It looks like they're dancing around plural or not plural.
        $end = end($srchTrm);
        unset($srchTrm[count($srchTrm) - 1]);
        $end1 = trim($end, 's');
        if ($end1 == $end) {
            $end1 = '';
        }
        $searchTerm = implode(' ', $srchTrm) . " " . $end . " " . $end1;
        $searchTerm1 = implode(' ', $srchTrm) . " " . $end . " " . $end1;
        //echo $searchTerm;exit;
        //$srchField = implode(' ', $filterArr['search']);
        //foreach($filterArr['search'] as $search)
        //{
        //echo strlen(trim($searchTerm));
        // JLB 02-19-18 WTF is the point of this first one? I assume that this was something that they hardcoded to get past testing?
//                if (strpos(trim($searchTerm), 'cl-17') !== false || strpos(trim($searchTerm), 'cl 17') !== false) {
//                    $searchTerm = 'hjc 2015 cl-17';
//                    $custom_where .= " part.name like '%" . trim($searchTerm) . "%' OR";
//                } else\
        $this->load->helper("jonathan");
        if (strlen(trim($searchTerm)) < 5 || strpos(trim($searchTerm), '-') !== false) {
            $custom_where .= " part.name like '%" . trim(jonathan_escape_for_likes($searchTerm1, "=")) . "%' ESCAPE '=' OR ";
        } else {
            $custom_where .= ' MATCH(part.name) AGAINST("' . addslashes(trim(str_replace('-', ' ', $searchTerm))) . '") OR ';
        }
        if (strpos($searchTerm1, '-') !== false) {
            //$srchTrm1 = explode(' ', $filterArr['search'][0]);
            //foreach($srchTrm1 as $k => $v) {
            //$custom_where .= " part.name like '%".$searchTerm1."%' OR";
            //}
        }

        //$custom_where .= ' part.name like "%'.strtoupper(trim($search)).'%" OR';
        //$this->db->like('part.name',strtoupper(trim($search)));
        //}
        $custom_where = rtrim($custom_where, 'OR ') . ')';
        return $custom_where;
    }

    // JLB 10-03-17 I added this active machine parameter...why is it not here?
    public function getSearchResults($filterArr = NULL, $activeMachine = null, $limit = 20, $offset = 0) {
        // BEGIN DEAL -  Must get this before buidling Search SQL in case it is needed.
        $dealPercent = 0;
        if (@$filterArr['deal']) {
            $where = array('key' => 'deal_percentage');
            $record = $this->selectRecord('config', $where);
            $dealPercent = $record['value'];
        }
        // END DEAL
        // BEGIN GET SUBCATEGORIES
        $categories = array();
        if (@$filterArr['category']) {
            end($filterArr['category']);
            $first_key = key($filterArr['category']);
            $categories = $this->getCategories($first_key);
            //print_r($categories);
        }
        if (@$filterArr['featured']) {
            $categories = $this->getCategories($filterArr['featured']);
        }
        // END GET SUBCATEGORIES 
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');
        $this->setHighLevelSearchCriteria($filterArr, $dealPercent, $categories);


        if (@$filterArr['category']) {
            end($filterArr['category']);
            $first_key = key($filterArr['category']);
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');

            $where = ' (partcategory.category_id = ' . $first_key;

            if (@$categories) {
                foreach ($categories as $catId => $catArr) {
                    $where .= ' OR partcategory.category_id = ' . $catId;
                    if (is_array(@$catArr['subcats'])) {
                        foreach ($catArr['subcats'] as $subCatId => $name) {
                            $where .= ' OR partcategory.category_id = ' . $subCatId;
                        }
                    }
                }
            }
            $where .= ' )';
            $this->db->where($where, NULL, FALSE);
        }

        if (@$filterArr['brand']) {
            $this->db->join('partbrand', 'partbrand.part_id = part.part_id');
            $this->db->where('partbrand.brand_id = ' . $filterArr['brand']);
        }

        if (!empty($filterArr['question'])) {
            $this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
            $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
            $questionQuery = '( ';
            if (is_array($filterArr['question'])) {

                foreach ($filterArr['question'] as $question) {
                    $questionQuery .= "answer = '" . $question . "' OR ";
                }
            } else
                $questionQuery .= "answer = '" . $filterArr['question'] . "' OR ";
            $questionQuery = substr($questionQuery, 0, -3) . ') ';
            $this->db->where($questionQuery);
        }

        if (@$filterArr['extra']) {
            if ($filterArr['extra'] == 'closeout') {
                $this->db->where("partvariation.stock_code = 'Closeout'");
            } else if ($filterArr['extra'] == 'featured') {
                $this->db->where("part.featured_brand = '1'");
            } else if ($filterArr['extra'] == 'arrival') {
                $endDate = date('Y-m-d H:i:s', strtotime("-7 months", strtotime(date('Y-m-d H:i:s'))));
                //$cus_where = array('part.created >=' =>  $endDate);
                $this->db->where("part.created >= '$endDate'");
            }
        }

        if (@$filterArr['search']) {
            if (is_array($filterArr['search'])) {
                $custom_where = $this->_searchCustomWhere($filterArr);

                $this->db->where($custom_where);
                //$this->db->order_by($field);
            }
        } else {
            $filterArr['search'][0] = '';
        }

        // JLB I bet this has to be up here, too...
        if (!is_null($activeMachine)) {
            $this->db->join('partnumbermodel', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id', 'LEFT');
            $this->db->where(sprintf(" (partnumbermodel.year is NULL OR partnumbermodel.year = %d) AND  (partnumbermodel.model_id is NULL OR partnumbermodel.model_id = %d) ", $activeMachine['year'], $activeMachine['model']['model_id']), NULL, FALSE);
//                    $where['partnumbermodel.year'] = $activeMachine['year'];
//                    $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }



        if (!is_null($limit)) {
            if (!is_null($offset))
                $this->db->limit($limit, $offset);
            else
                $this->db->limit($limit);
        }
        //echo $filterArr['search'][0];
        if (isset($filterArr['search'][0]) && $filterArr['search'][0] != '') {
            $this->db->order_by('srch');
        } else {
            //$this->db->order_by('label', 'random');
            $this->db->order_by('ordering');
        }
        $this->db->group_by('part_id');
        $this->db->where('partnumber.price > 0');
        $this->db->select('part.call_for_price, part.name as label, partnumber.partnumber_id, part.universal_fitment, 
										  count(partnumber) as cnt,
										  part.part_id,
										  part.featured as featured,
										  "' . $filterArr['search'][0] . '" as srch,
										  partvariation.stock_code,
										  MIN(partnumber.dealer_sale) AS dealer_sale_min,
										  MAX(partnumber.dealer_sale) AS dealer_sale_max,
										  MIN(partnumber.price) AS price_min,
										  MAX(partnumber.price) AS price_max,
										  MIN(partnumber.sale) AS sale_min, 
										  MAX(partnumber.sale) AS sale_max', FALSE);
        $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        $this->db->where("partvariation.from_lightspeed", 0, FALSE);
        $records = $this->selectRecords('partnumber');

        //echo $this->db->last_query();
        //exit;
        if ($records) {
            foreach ($records as &$rec) {
                // $category = $this->getSecondBreadCrumb( $rec['part_id'] );
                // $category_markup = array();
                // foreach( $category as $cat ) {
                // $category_markup[] = $cat['id'];
                // }
                // $this->db->select('MIN(category.mark_up) as markup');
                // $this->db->where_in('category_id', $category_markup);
                // $where = array('category.mark_up > ' => 0);
                // //$this->db->join('partcategory', 'partcategory.category_id = category.category_id');
                // $categories = $this->selectRecord('category', $where);
                // $this->db->select('MIN(brand.mark_up) as markup, 
                // MAX(brand.exclude_market_place) as exclude_market_place, 
                // MAX(brand.closeout_market_place) as closeout_market_place');
                // $where = array('partbrand.part_id' => $rec['part_id']);
                // $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
                // $brand_markup = $this->selectRecord('brand', $where);

                $rec['stock_code'] = $this->getStockCodeByPartId($rec['part_id']);
                $where = array('partpartnumber.part_id' => $rec['part_id']);
                $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
                // JLB 10-03-17 - I found this $activeMachine variable undefined. I assume that's why it doesn't filter right...
                if (!is_null($activeMachine)) {
                    $this->db->join('partnumbermodel', 'partpartnumber.partnumber_id = partnumbermodel.partnumber_id', 'LEFT');
                    $this->db->where(sprintf(" (partnumbermodel.year is NULL OR partnumbermodel.year = %d) AND  (partnumbermodel.model_id is NULL OR partnumbermodel.model_id = %d) ", $activeMachine['year'], $activeMachine['model']['model_id']), NULL, FALSE);
//                    $where['partnumbermodel.year'] = $activeMachine['year'];
//                    $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
                }
                $this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN CASE WHEN partdealervariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
                $this->db->where('partnumber.price > 0');
                $this->db->select('partnumber, partnumber.dealer_sale,partnumber.price, partnumber.sale, partdealervariation.quantity_available as dealer_quantity, partvariation.quantity_available');
                $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
                $this->db->where("partvariation.from_lightspeed", 0, FALSE);
                $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id', 'left');
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

                    // if( @$categories['markup'] ) {
                    // $partNumberRec['sale_min'] = number_format(($v['cost']+(($v['cost']*$categories['markup'])/100)),2);
                    // }
                    // if( @$brand_markup['markup'] ) {
                    // $partNumberRec['sale_min'] = number_format(($v['cost']+(($v['cost']*$brand_markup['markup'])/100)),2);
                    // }
                }

                $this->db->where("partdealervariation.partnumber_id = '" . $rec['partnumber_id'] . "'");
                $partDealerInventory = $this->selectRecord('partdealervariation');

                $this->db->where("partvariation.partnumber_id = '" . $rec['partnumber_id'] . "'");
                $this->db->where("partvariation.from_lightspeed", 0, FALSE);
                $partDistributorInventory = $this->selectRecord('partvariation');

                if ($partDealerInventory['quantity_available'] == '0' || !@$partDealerInventory) {
                    //$rec['dealer_sale_min'] = 0;
                    //$rec['dealer_sale_max'] = 0;
                }

                if ($partDistributorInventory['quantity_available'] == '0') {
                    //$rec['sale_min'] = $rec['dealer_sale_min'];
                    //$rec['sale_max'] = $rec['dealer_sale_max'];
                }

                // Active Ride
                if ((@$_SESSION['garage']) && ($this->validMachines($rec['part_id'], @$_SESSION['activeMachine'])))
                    $rec['activeRide'] = TRUE;
                // Combo Processing
                $combopartIds = $this->checkForCombo($rec['part_id']);
                if (is_array($combopartIds)) {
                    $PriceArr = array();
                    $finalPriceArr = array('retail_min' => 0, 'retail_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
                    foreach ($combopartIds as $id) {
                        $PriceArr[] = $this->getPriceRange($id, FALSE, FALSE);
                    }
                    foreach ($PriceArr as $pa) {
                        if ($pa['sale_max'] == '') {
                            $pa['sale_max'] = $pa['sale_min'];
                        }
                        $finalPriceArr['retail_min'] += $pa['retail_min'];
                        $finalPriceArr['retail_max'] += $pa['retail_max'];
                        $finalPriceArr['sale_min'] += $pa['sale_min'];
                        $finalPriceArr['sale_max'] += $pa['sale_max'];
                    }


                    $rec['price'] = $this->calculateMarkup($finalPriceArr['retail_min'], $finalPriceArr['retail_max'], $finalPriceArr['sale_min'], $finalPriceArr['sale_max'], @$_SESSION['userRecord']['markup']);
                } else {
                    //partNumberRec
                    $rec['price'] = $this->calculateMarkup($partNumberRec['price_min'], $partNumberRec['price_max'], $partNumberRec['sale_min'], $partNumberRec['sale_max'], @$_SESSION['userRecord']['markup']);
                }

                //$rec['price'] = $this->calculateMarkup($rec['price_min'], $rec['price_max'], $rec['sale_min'], $rec['sale_max'], @$_SESSION['userRecord']['markup'], $rec['dealer_sale_min'], $rec['dealer_sale_max'], $rec['cnt']);

                $where = array('part_id' => $rec['part_id']);
                $rec['images'] = $this->selectRecords('partimage', $where);
                $rec['reviews'] = $this->getAverageReviews($rec['part_id']);
            }
        }


        return $records;
    }

    public function setHighLevelSearchCriteria($filterArr, $dealPercent = 0, $categories = array()) {

        if (@$filterArr['featured'] && !@$filterArr['category']) {
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');

            $where = ' (partcategory.category_id = ' . $filterArr['featured'];

            if (@$categories) {
                foreach ($categories as $catId => $catArr) {
                    $where .= ' OR partcategory.category_id = ' . $catId;
                    if (is_array(@$catArr['subcats'])) {
                        foreach ($catArr['subcats'] as $subCatId => $name) {
                            $where .= ' OR partcategory.category_id = ' . $subCatId;
                        }
                    }
                }
            }
            $where .= ' )';
            $this->db->where($where, NULL, FALSE);


            $this->db->where('part.featured = 1');
        } elseif (@$filterArr['featured'] && @$filterArr['category']) {
            $this->db->where('part.featured = 1');
        }
        if (@$filterArr['recentlyViewed']) {
            if ($_SESSION['recentlyViewed']) {
                $where = '( ';
                foreach ($_SESSION['recentlyViewed'] as $partId) {
                    $where .= 'part.part_id = ' . $partId . ' OR ';
                }
                $where = substr($where, 0, -3);
                $where .= ')';
                $this->db->where($where);
            }
        }
        if (@$filterArr['deal']) {
            $this->db->where('partnumber.price > 1.' . $dealPercent . ' * partnumber.sale');
        }
    }

    public function relatedProducts($partId, $limit = 4) {
        $returnArr = array();
        $productArr = array();
        $this->db->select('part_id, name as label, image');
        if (is_numeric($limit))
            $this->db->limit($limit);
        $this->db->join('part', 'part.part_id = related.related_part_id');
        $where = array('key_part_id' => $partId);
        $productArr = $this->selectRecords('related', $where);
        if (!empty($productArr)) {
            foreach ($productArr as &$rec) {
                $rec['activeRide'] = FALSE;
                $rec['price'] = $this->getPriceRange($rec['part_id']);
                if ((@$_SESSION['garage']) && ($this->validMachines($rec['part_id'], @$_SESSION['activeMachine'])))
                    $rec['activeRide'] = TRUE;
            }
        }

        $returnArr['label'] = 'Featured Products';
        $returnArr['page'] = 'shopping/productlist/related/' . $partId . '/';
        $returnArr['products'] = $productArr;
        return $returnArr;
    }

    public function getReviews($partId = NULL) {
        $where = array('approval_id IS NOT NULL' => NULL);
        if ($partId)
            $where['part_id'] = $partId;
        else {
            $this->db->join('part', 'part.part_id = reviews.part_id');
        }
        $this->db->join('user', 'user.id = reviews.user_id', 'LEFT');
        $this->db->join('contact', 'contact.id = user.billing_id', 'LEFT');
        $this->db->order_by('RAND()');
        $this->db->limit(4);
        $records = $this->selectRecords('reviews', $where);
        if (is_null($partId) && isset($records) && is_array($records)) {
            foreach ($records as &$rec) {
                $where = array('part_id' => $rec['part_id']);
                $rec['images'] = $this->selectRecords('partimage', $where);
            }
        }
        return $records;
    }

    function select_where($select, $tbl, $fld, $val) {

        $this->db->select($select);
        $this->db->from($tbl);
        $this->db->where($fld, $val);
        return $this->db->get();
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
            $thePriorities = array(TOP_LEVEL_CAT_STREET_BIKES, TOP_LEVEL_CAT_VTWIN_PARTS, TOP_LEVEL_CAT_DIRT_BIKES, TOP_LEVEL_CAT_ATV_PARTS, TOP_LEVEL_CAT_UTV_PARTS);

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

    public function getSizeChartByCategory($categories, $brand_id, $prod_id) {
        //$where = array('sizechart_url' => $sizeSlug);
        $where = '(';
        foreach ($categories as $val) {
            $where .= 'find_in_set("' . $val . '", categories) OR ';
        }
        $where = rtrim($where, 'OR ');
        $where .= ')';
        $this->db->where($where);
        $this->db->where('brand_id', $brand_id);
        $record = $this->selectRecords('brand_sizechart');
        $records = array();
        foreach ($record as $key => $val) {
            if ($val['partquestion_id'] == null || $val['partquestion_id'] == '0') {
                $records[] = $val;
            } else {
                $partquestions = explode(',', $val['partquestion_id']);
                foreach ($partquestions as $k => $v) {
                    $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
                    $this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
                    $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
                    $where = array('partpartnumber.part_id' => $prod_id, 'partnumberpartquestion.answer' => $v);
                    $this->db->group_by('partpartnumber.part_id');
                    $partNumberRec = $this->selectRecord('partnumber', $where);
                    if (!empty($partNumberRec)) {
                        $records[] = $val;
                    }
                }
            }
        }
        return $records;
    }

    function nav_categories_and_parent($partId, $pCAT) {

        $this->db->select("*");
        $this->db->from("partcategory");
        $this->db->where("part_id", $partId);
        $this->db->limit(1);
        $this->db->order_by('category_id', 'DESC');
        $get = $this->db->get();

        $data['navCategories'] = array();
        $data['parent'] = 0;
        if (!empty($pCAT)) {
            $data['navCategories'] = $this->getCategories($pCAT);
            $data['parent'] = $pCAT;
        } elseif ($get->num_rows() > 0) {

            $row = $get->row();
            $categories = $this->getParentCategores($row->category_id);

            if (!empty($categories) && (isset($categories[TOP_LEVEL_CAT_UTV_PARTS]) || isset($categories[TOP_LEVEL_CAT_STREET_BIKES]) || isset($categories[TOP_LEVEL_CAT_DIRT_BIKES]) || isset($categories[TOP_LEVEL_CAT_ATV_PARTS]) || isset($categories[TOP_LEVEL_CAT_MARINE]))) {
                reset($categories);
                $first_key = key($categories);
                $data['navCategories'] = $this->getCategories($first_key);
                $data['parent'] = $first_key;
            } else {

                $topCategories[0]['id'] = TOP_LEVEL_CAT_STREET_BIKES;
                $topCategories[1]['id'] = TOP_LEVEL_CAT_DIRT_BIKES;
                $topCategories[2]['id'] = TOP_LEVEL_CAT_ATV_PARTS;
                $topCategories[3]['id'] = TOP_LEVEL_CAT_UTV_PARTS;
                $topCategories[4]['id'] = TOP_LEVEL_CAT_VTWIN_PARTS;
                $topCategories[5]['id'] = TOP_LEVEL_CAT_MARINE;
                $keyToUse = array_rand($topCategories);
                $data['navCategories'] = $this->getCategories($topCategories[$keyToUse]['id']);
                $data['parent'] = $topCategories[$keyToUse]['id'];
            }
        } else {

            $topCategories[0]['id'] = TOP_LEVEL_CAT_STREET_BIKES;
            $topCategories[1]['id'] = TOP_LEVEL_CAT_DIRT_BIKES;
            $topCategories[2]['id'] = TOP_LEVEL_CAT_ATV_PARTS;
            $topCategories[3]['id'] = TOP_LEVEL_CAT_UTV_PARTS;
            $topCategories[4]['id'] = TOP_LEVEL_CAT_VTWIN_PARTS;
            $topCategories[5]['id'] = TOP_LEVEL_CAT_MARINE;
            $keyToUse = array_rand($topCategories);

            $data['navCategories'] = $this->getCategories($topCategories[$keyToUse]['id']);
            $data['parent'] = $topCategories[$keyToUse]['id'];
        }

        return $data;
    }

    public function buildRideName($idArray) {
        $where = array('model_id' => $idArray['model_id']);
        $model = $this->selectRecord('model', $where);
        $where = array('make_id' => $model['make_id']);
        $make = $this->selectRecord('make', $where);
        $record = array('name' => $make['label'] . ' ' . $model['label'] . ' ' . $idArray['year'], 'make' => $make, 'model' => $model, 'year' => $idArray['year']);
        return $record;
    }

    public function reconcilePricetoSale() {
        $where = array('sale' => 0);
        $records = $this->selectRecords('partnumber', $where);
        if ($records) {
            foreach ($records as $rec) {
                $where = array('partnumber_id' => $rec['partnumber_id']);
                $data = array('sale' => $rec['price']);
                $this->updateRecords('partnumber', $data, $where, FALSE);
            }
        }
    }

    // Custom Product Search
    public function getSearchResultsNew($filterArr = NULL, $limit = 20, $offset = 0) {
        // BEGIN DEAL -  Must get this before buidling Search SQL in case it is needed.
        $dealPercent = 0;
        if (@$filterArr['deal']) {
            $where = array('key' => 'deal_percentage');
            $record = $this->selectRecord('config', $where);
            $dealPercent = $record['value'];
        }
        // END DEAL
        // BEGIN GET SUBCATEGORIES
        $categories = array();
        if (@$filterArr['category']) {
            end($filterArr['category']);
            $first_key = key($filterArr['category']);
            $categories = $this->getCategories($first_key);
            //print_r($categories);
        }
        if (@$filterArr['featured']) {
            $categories = $this->getCategories($filterArr['featured']);
        }
        // END GET SUBCATEGORIES 
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        $this->db->join('part', 'part.part_id = partpartnumber.part_id');
        $this->setHighLevelSearchCriteria($filterArr, $dealPercent, $categories);


        if (@$filterArr['category']) {
            end($filterArr['category']);
            $first_key = key($filterArr['category']);
            $this->db->join('partcategory', 'partcategory.part_id = part.part_id');

            $where = ' (partcategory.category_id = ' . $first_key;

            if (@$categories) {
                foreach ($categories as $catId => $catArr) {
                    $where .= ' OR partcategory.category_id = ' . $catId;
                    if (is_array(@$catArr['subcats'])) {
                        foreach ($catArr['subcats'] as $subCatId => $name) {
                            $where .= ' OR partcategory.category_id = ' . $subCatId;
                        }
                    }
                }
            }
            $where .= ' )';
            $this->db->where($where, NULL, FALSE);
        }

        if (@$filterArr['brand']) {
            $this->db->join('partbrand', 'partbrand.part_id = part.part_id');
            $this->db->where('partbrand.brand_id = ' . $filterArr['brand']);
        }

        if (!empty($filterArr['question'])) {
            $this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
            $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
            $questionQuery = '( ';
            if (is_array($filterArr['question'])) {

                foreach ($filterArr['question'] as $question) {
                    $questionQuery .= "answer = '" . $question . "' OR ";
                }
            } else
                $questionQuery .= "answer = '" . $filterArr['question'] . "' OR ";
            $questionQuery = substr($questionQuery, 0, -3) . ') ';
            $this->db->where($questionQuery);
        }

        if (@$filterArr['search']) {
            if (is_array($filterArr['search'])) {
                //foreach($filterArr['search'] as $search)
                //{
                //	$this->db->like('part.name',strtoupper($search));
                //}
                $custom_where = "(";
                foreach ($filterArr['search'] as $search) {
                    $custom_where .= "MATCH(part.name) AGAINST('" . trim($search) . "')";
                }
                $custom_where = rtrim($custom_where, 'OR') . ')';
                $this->db->where($custom_where);
            }
        }

        if (!is_null($limit)) {
            if (!is_null($offset))
                $this->db->limit($limit, $offset);
            else
                $this->db->limit($limit);
        }
        $this->db->order_by('part.name ASC');
        $this->db->group_by('part_id');
        $this->db->where('partnumber.price > 0');
        $this->db->select('part.name as label, 
										  part.part_id, 
										  partvariation.stock_code,
										  MIN(partnumber.price) AS price_min,
										  MAX(partnumber.price) AS price_max,
										  MIN(partnumber.sale) AS sale_min, 
										  MAX(partnumber.sale) AS sale_max', FALSE);
        $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        $records = $this->selectRecords('partnumber');

        if ($records) {
            foreach ($records as &$rec) {

                // Active Ride
                if ((@$_SESSION['garage']) && ($this->validMachines($rec['part_id'], @$_SESSION['activeMachine'])))
                    $rec['activeRide'] = TRUE;
                // Combo Processing
                $combopartIds = $this->checkForCombo($rec['part_id']);
                if (is_array($combopartIds)) {
                    $PriceArr = array();
                    $finalPriceArr = array('retail_min' => 0, 'retail_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
                    foreach ($combopartIds as $id) {
                        $PriceArr[] = $this->getPriceRange($id, FALSE, FALSE);
                    }
                    foreach ($PriceArr as $pa) {
                        $finalPriceArr['retail_min'] += $pa['retail_min'];
                        $finalPriceArr['retail_max'] += $pa['retail_max'];
                        $finalPriceArr['sale_min'] += $pa['sale_min'];
                        $finalPriceArr['sale_max'] += $pa['sale_max'];
                    }
                    $rec['price'] = $this->calculateMarkup($finalPriceArr['retail_min'], $finalPriceArr['retail_max'], $finalPriceArr['sale_min'], $finalPriceArr['sale_max'], @$_SESSION['userRecord']['markup']);
                } else
                    $rec['price'] = $this->calculateMarkup($rec['price_min'], $rec['price_max'], $rec['sale_min'], $rec['sale_max'], @$_SESSION['userRecord']['markup']);

                $where = array('part_id' => $rec['part_id']);
                $rec['images'] = $this->selectRecords('partimage', $where);
                $rec['reviews'] = $this->getAverageReviews($rec['part_id']);
            }
        }

        return $records;
    }

    public function getBrandBySlug($brandSlug) {
        $where = array('slug' => $brandSlug);
        $record = $this->selectRecord('brand', $where);
        return $record;
        //return array('brand' => array('id' => $record['brand_id'], 'name' => $record['name'], 'image' => $record['image']) );
    }

    public function getBrandBySizeChart($sizeSlug) {
        $where = array('sizechart_url' => $sizeSlug);
        $record = $this->selectRecord('brand', $where);
        return $record;
    }

    public function getSizeChart($brand_id) {
        $where = array('brand_id' => $brand_id);
        $records = $this->selectRecords('brand_sizechart', $where);
        $recordArr = array();
        foreach ($records as $record) {
            $category = explode(',', $record['categories']);
            array_filter($category);
            $record['category'] = $category[0];

            $returnURL = '/';
            if (@$record['category']) {
                $categories = $this->getParentCategores($record['category']);
                if (is_array($categories)) {
                    foreach ($categories as $cat)
                        $returnURL .= $this->tag_creating_custom($cat) . '_';
                }
            }
            if (@$record['brand_id']) {
                $brnd = $this->getBrandById($record['brand_id']);
                if ($brnd['name'] != 'brand')
                    $returnURL .= $this->tag_creating_custom($brnd['name']) . '_';
            }

            $cat = explode(',', $record['categories']);
            array_filter($cat);
            $category = $this->getCategory($cat[0]);

            //$_SESSION['internal'] = TRUE;
            $record['catName'] = $category['name'];
            $record['catUrl'] = substr($returnURL, 0, -1);

            $recordArr[] = $record;
        }
        return $recordArr;
    }

    public function tag_creating_custom($url) {
        $url = str_replace(array(' - ', ' '), '-', $url);
        $url = preg_replace('~[^\\pL0-9_-]+~u', '', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_-]+~', '', $url);
        return $url;
    }

    //get brand by brand id
    public function getBrandById($brandId) {
        $where = array('brand_id' => $brandId);
        $record = $this->selectRecord('brand', $where);
        return $record;
        //return array('brand' => array('id' => $record['brand_id'], 'name' => $record['name'], 'image' => $record['image']) );
    }

    //getBrandByPart
    public function getBrandByPart($partId) {
        $where = array('partbrand.part_id' => $partId);
        $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
        $record = $this->selectRecord('brand', $where);
        return $record;
        //return array('brand' => array('id' => $record['brand_id'], 'name' => $record['name'], 'image' => $record['image']) );
    }

    public function getFeaturedBrands($limit = NULL) {
        $brandArr = array();
        $this->db->select('brand.name, brand.image, brand.slug');
        $where = array('brand.featured' => 1);
        if (is_numeric($limit))
            $this->db->limit($limit);
        $brandArr = $this->selectRecords('brand', $where);
        return $brandArr;
    }

    public function getFeaturedCategories() {
        $where = array('featured' => 1);
        $this->db->select('name, category_id, title, image');
        $records = $this->selectRecords('category', $where);
        return $records;
    }

    public function getProductSizeChartFE($part_id) {
        $where = array('part_id' => $part_id);
        $record = $this->selectRecords('part_sizechart', $where);
        return $record;
    }

    public function partIsRetailOnly($part_id) {
        if (array_key_exists($part_id, $this->cache_partIsRetail)) {
            return $this->cache_partIsRetail[$part_id];
        }

        // Look it up
        $query = $this->db->query("Select retail_price from part where part_id = ?", array($part_id));
        $retail = false;
        foreach ($query->result_array() as $row) {
            $retail = $row["retail_price"] > 0;
        }

        $this->cache_partIsRetail[$part_id] = $retail;
        return $retail;
    }

    public function partNumberIsRetailOnly($partnumber_id) {
        if (array_key_exists($partnumber_id, $this->cache_partNumberIsRetail)) {
            return $this->cache_partNumberIsRetail[$partnumber_id];
        }

        // Look it up
        $query = $this->db->query("Select max(retail_price) as use_retail_price from part join partpartnumber using (part_id) where partnumber_id = ?", array($partnumber_id));
        $retail = false;
        foreach ($query->result_array() as $row) {
            $retail = $row["use_retail_price"] > 0;
        }

        $this->cache_partNumberIsRetail[$partnumber_id] = $retail;
        return $retail;
    }

    public function partVariationIsRetailOnly($partvariation_id) {
        if (array_key_exists($partvariation_id, $this->cache_partVariationIsRetail)) {
            return $this->cache_partVariationIsRetail[$partvariation_id];
        }

        // Look it up
        $query = $this->db->query("Select max(retail_price) as use_retail_price from part join partpartnumber using (part_id) join partvariation using (partnumber_id) where partvariation_id = ?", array($partvariation_id));
        $retail = false;
        foreach ($query->result_array() as $row) {
            $retail = $row["use_retail_price"] > 0;
        }

        $this->cache_partVariationIsRetail[$partvariation_id] = $retail;
        return $retail;
    }


    //Closeout Reprising Calculations
    public function closeoutReprisingSchedule() {
        $where = array('status > 0 ' => null);
        $records = $this->selectRecords('closeout_rules', $where);
        foreach ($records as $key => $val) {
            $where = array('partnumber.price > ' => 0, 'datediff(now(), partvariation.closeout_on) = ' => $val['days']);
            $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id ');
            $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
            if ($val['brand_id'] != null) {
                $this->db->join('partbrand', 'partbrand.part_id = partpartnumber.part_id');
                $where['partbrand.brand_id = '] = $val['brand_id'];
            }
            $this->db->group_by('partvariation_id');
            $partvariations = $this->selectRecords('partnumber', $where);

            if (@$partvariations) {
                foreach ($partvariations as $partvariation) {
                    $this->db->select('MIN(category.mark_up) as markup');
                    $where = array('partcategory.part_id' => $partvariation['part_id'], 'category.mark_up > ' => 0);
                    $this->db->join('partcategory', 'partcategory.category_id = category.category_id');
                    $categories = $this->selectRecord('category', $where);

                    $this->db->select('MIN(brand.mark_up) as markup, 
													  MAX(brand.exclude_market_place) as exclude_market_place, 
													  MAX(brand.closeout_market_place) as closeout_market_place');
                    $where = array('partbrand.part_id' => $partvariation['part_id']);
                    $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
                    $brand_markup = $this->selectRecord('brand', $where);

                    $this->db->select('MIN(brand.map_percent) as map_percent, ');
                    $where = array('partbrand.part_id' => $partvariation['part_id'], 'brand.map_percent IS NOT NULL ' => NULL);
                    $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
                    $brand_map_percent = $this->selectRecord('brand', $where);

                    $partnumbers = $partvariation;
                    $categoryMarkUp = is_numeric(@$categories['markup']) ? $categories['markup'] : 0;
                    $brandMarkUp = is_numeric(@$brand_markup['markup']) ? $brand_markup['markup'] : 0;
                    $brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : NULL;

                    if ($partnumbers) {
                        //foreach ($partnumbers as $rec) {
                        $rec = $partvariation;
                        $finalMarkUp = 0;
                        $productMarkUp = $rec['markup'];

                        // JLB 03-05-18
                        // This overrides everything and forces a price in line with lightspeed
                        if ($this->lightspeed_m->partNumberIsLightspeed($rec['partnumber_id'])) {
                            $finalSalesPrice = $this->lightspeed_m->lightspeedPrice($rec['partnumber_id']);
                        } else if ($this->partNumberIsRetailOnly($rec['partnumber_id'])) {
                            // JLB 07-15-17
                            // This overrides the price and forces it to be
                            $finalSalesPrice = $rec["price"];
                        } elseif ($productMarkUp > 0) {
                            // Product Markup Trumps everything
                            $finalSalesPrice = ($rec['cost'] * $productMarkUp / 100) + $rec['cost'];
                        } else {
                            // Calculate category and Brand Percent Mark up
                            if ($categoryMarkUp > 0) {
                                $finalMarkUp = $categoryMarkUp;
                                if (($brandMarkUp > 0) && ($brandMarkUp < $finalMarkUp))
                                    $finalMarkUp = $brandMarkUp;
                            }
                            elseif ($brandMarkUp > 0)
                                $finalMarkUp = $brandMarkUp;

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
                        if (!isset($finalSalesPrice))
                            $finalSalesPrice = $rec['price'];

                        //Final Sale Price after closeout rule reprise
                        $salePrice = $rec['price'] - ($rec['price'] * ($val['percentage'] + $rec['percentage']) / 100);
                        if ($salePrice < $finalSalesPrice) {
                            $salePrice = $finalSalesPrice;
                        }
                        //echo $partvariation['partnumber_id'].' : '.$salePrice.' : '.$val['percentage'].' : '.$rec['percentage'].'<br>';

                        if ($val['status'] == 1) {
                            $data = array('sale' => $salePrice, 'percentage' => ($rec['percentage'] + $val['percentage']));
                        } else if ($val['status'] == 2) {
                            $data = array('sale' => $finalSalesPrice, 'percentage' => 0);
                        }

                        if ($val['mark_up'] == 1) {
                            $salePrice = $finalSalesPrice;
                            $data = array('sale' => $finalSalesPrice, 'percentage' => 0);
                        }

                        $where = array('partnumber_id' => $rec['partnumber_id']);
                        $this->updateRecord('partnumber', $data, $where, FALSE);
                        //}
                    }
                }
            }
            // echo '<pre>';
            // print_r($partvariations);
            // echo '</pre>';
        }

        if (defined("ENABLE_LIGHTSPEED") && ENABLE_LIGHTSPEED) {
            $this->lightspeed_m->partPriceFix();
        }
    }

    public function markCloseoutDate() {
        $where = array('stock_code = ' => 'Closeout');
        $this->db->where(' (closeout_on is null) ', NULL, FALSE);
        $data = array('closeout_on' => date('Y-m-d'));
        $this->updateRecords('partvariation', $data, $where, FALSE);
        $this->closeoutReprisingSchedule();
    }

    public function removeFinishedInventory() {
        $where = array('quantity_available = ' => '0', 'protect = ' => '1');
        $inventory = $this->selectRecords('partvariation', $where);
        foreach ($inventory as $k => $v) {
            $where = array('partvariation_id' => $v['partvariation_id']);
            $dealerinventory = $this->selectRecord('partdealervariation', $where);
            if (!@$dealerinventory || $dealerinventory['quantity_available'] == '0') {
                $parr = array('dealer_sale' => null);
                $whr = array('partnumber_id' => $v['partnumber_id']);
                $this->updateRecords('partnumber', $parr, $whr, FALSE);

                $where = array('partvariation_id' => $v['partvariation_id']);
                $this->deleteRecord('partdealervariation', $where);
                $data = array('protect' => '0');
                $this->updateRecords('partvariation', $data, $where, FALSE);
            }
        }
    }

    public function customProductSorting() {
        $this->db->select('part_id');
        $where = array();
        $records = $this->selectRecords('part', $where);
        $parts = array();
        foreach ($records as $record) {
            $parts[] = $record['part_id'];
        }
        shuffle($parts);
        foreach ($parts as $k => $part) {
            $where = array('part_id' => $part);
            $data = array('ordering' => $k);
            $this->updateRecord('part', $data, $where, FALSE);
        }
    }

    // JLB 02-19-18
    // This model is so fucking long.
    //
    // This function is added to get the data required to spit out the new "Part Number" screen.
    public function getPartNumberScreen($part_id, $activeMachine = NULL) {
        $extra_join = "";
        $extra_values = array();
        $extra_where = "";

        if (!is_null($activeMachine)) {
            $extra_join = " join partnumbermodel on partpartnumber.partnumber_id = partnumbermodel.partnumber_id ";
            $extra_where = " AND partnumbermodel.year = ? and partnumbermodel.model_id = ? ";
            $extra_values[] = $activeMachine["year"];
            $extra_values[] = $activeMachine["model"]["model_id"];
        }

        array_unshift($extra_values, $part_id);
        $query = $this->db->query("select group_concat(distinct partnumberpartquestion.answer order by partnumberpartquestion.answer separator '/') as answer , partvariation.part_number , partvariation.manufacturer_part_number , If(IfNull(partdealervariation.quantity_available, 0) + IfNull(partvariation.quantity_available, 0) > 0, 1, 0) as in_stock, partvariation.stock_code  from partvariation join partnumber using (partnumber_id) join partnumberpartquestion using (partnumber_id)  join partpartnumber using (partnumber_id) left join partdealervariation on partvariation.partvariation_id = partdealervariation.partvariation_id join partquestion on partnumberpartquestion.partquestion_id = partquestion.partquestion_id $extra_join where partpartnumber.part_id = ? and partquestion.part_id = partpartnumber.part_id and partquestion.productquestion = 0 $extra_where group by partvariation.partvariation_id;", $extra_values);
        return $query->result_array();
    }
}
