<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/17/17
 * Time: 11:37 AM
 */

class Portalmodel extends Master_M {

    /*
     * This is copied over from the old admin_m model, but without the DIE statement....
     */
    public function classicUpdatePart($id, $post) {
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
        if (!empty($post)) {
            $this->updateRecord('part', $post, $where, FALSE);
        }

        $where = array('partpartnumber.part_id' => $id);
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


    /*
     * JLB 02-16-17
     * Moved from admin_m.php
     * I do not think this function does quite the right thing, so I'm augmenting it to do the admin part search.
     *
     * This returns list($products, $total_count, $filtered_count);
     *
     */
    public function enhancedGetProducts($cat = '20034', $filter = NULL, $orderBy = NULL, $limit = 20, $offset = 0) {
        $this->load->helper("jonathan");

        $where = jonathan_generate_likes(array("part.name", "zpartvariation.partlabel"), $filter, "WHERE");

        $total_count = 0;
        $query = $this->db->query("Select count(*) as cnt from part");
        foreach ($query->result_array() as $row) {
            $total_count = $row['cnt'];
        }

        // Now, is there a filter?
        $filtered_count = $total_count;
        if ($where != "") {
            $query = $this->db->query("Select count(distinct part_id) as cnt from part left join partpartnumber using (part_id) left join partnumber  using (partnumber_id)  left join (select partvariation.*, concat(distributor.name, ' ', partvariation.part_number) as partlabel from partvariation join distributor using (distributor_id)) zpartvariation using (partnumber_id) left join partimage using (part_id) $where");
            foreach ($query->result_array() as $row) {
                $filtered_count = $row["cnt"];
            }
        }

        // Finally, run it!
        $query = $this->db->query("Select part.part_id, 
                                          partimage.path,
										  part.name, 
										  part.featured, 
										  part.mx, 
										  group_concat(distinct zpartvariation.partlabel order by partlabel separator ', ') as partnumber, 
										  MIN(If(partnumber.dealer_sale > 0, partnumber.dealer_sale, partnumber.sale)) AS sale_min, 
										  MAX(If(partnumber.dealer_sale > 0, partnumber.dealer_sale, partnumber.sale)) AS sale_max,
										  MIN(partnumber.price) AS price_min, 
										  MAX(partnumber.price) AS price_max,
										  MIN(partnumber.cost) AS cost_min, 
										  MAX(partnumber.cost) AS cost_max,
										  MIN(partnumber.markup) AS markup from part left join partpartnumber using (part_id) left join partnumber using (partnumber_id) left join (select partvariation.*, concat(distributor.name, ' ', partvariation.part_number) as partlabel from partvariation join distributor using (distributor_id)) zpartvariation using (partnumber_id) left join partimage using (part_id) $where group by part.part_id $orderBy limit $limit offset $offset ");
        $rows = $query->result_array();

        return array($rows, $total_count, $filtered_count);
    }

    public function buildCategoryFromLong($category_long_name, $revisionset_id) {
        $decomposed_name = $this->decomposeCategory($category_long_name);

        $parent_category_id = null;
        foreach ($decomposed_name as $name) {
            $data = array(
                "name" => $name, "parent_category_id" => $parent_category_id, "revisionset_id" => $revisionset_id
            );
            $result = $this->matchByAttributes($data);

            if (count($result) > 0) {
                $parent_category_id = $result[0]["category_id"];
            } else {
                $parent_category_id = $this->insert($data);
            }
        }

        return $parent_category_id;
    }

    public function getPartIDByName($name) {
        return $this->fetchByColumn("part", "part_id", "name", $name);
    }

    protected function fetchByColumn($table, $key_field, $column, $value) {
        $query = $this->db->query("Select $key_field from $table where $column = ?", array($value));

        foreach ($query->result_array() as $row) {
            return $row[$key_field];
        }

        return 0;
    }

    public function makePartByName($part_name, $description) {
        $this->db->query("Insert into part (name, description, mx, revisionset_id, protect) values (?, ?, 0, 1, 1) on duplicate key update part_id = last_insert_id(part_id)", array($part_name, $description));
        return $this->db->insert_id();
    }

    public function getManufacturer($manufacturer) {
        return $this->fetchByColumn("manufacturer", "manufacturer_id", "name", $manufacturer);
    }

    public function makeBrandSlug($name) {
        return str_replace(array(" ", ".", ",", "'", "&"), array("_", "", "", "", "_and_"), $name);
    }

    public function getOrMakeManufacturer($manufacturer) {
        $manufacturer_id = $this->getManufacturer($manufacturer);
        if ($manufacturer_id == 0) {
            $this->db->query("Insert into manufacturer (label, name, revisionset_id) values (?, ?, 1)", array($manufacturer, $manufacturer));
            $manufacturer_id = $this->db->insert_id();

            // you have to make a brand...
            $this->db->query("Insert into brand (name, long_name, slug, title, active, mx, meta_tag) values (?, ?, ?, ?, 1, 0, ?)", array($manufacturer, $manufacturer, $this->makeBrandSlug($manufacturer), $manufacturer, $manufacturer));
            $brand_id = $this->db->insert_id();

            $this->db->query("Update manufacturer set brand_id = ? where manufacturer_id = ?", array($brand_id, $manufacturer_id));
        }
        return $manufacturer_id;
    }

    public function getDealerDistributor() {
        return $this->fetchByColumn("distributor", "distributor_id", "name", "Dealer");
    }

    public function createPartVariation($part_number, $quantity_available, $stock_code, $cost, $price, $customerdistributor_id, $customerdistributor) {
        $this->db->query("Insert into partvariation (part_number, distributor_id, stock_code, quantity_last_updated, cost, price, protect, customerdistributor_id, clean_part_number) values (?, ?, ?, now(), ?, ?, 1, ?, ?) on duplicate key update partvariation_id = last_insert_id(partvariation_id), stock_code = values(stock_code), quantity_last_updated = values(quantity_last_updated), cost = values(cost), price = values(price), protect = values(protect), customerdistributor_id = values(customerdistributor_id)", array($customerdistributor . "-" . $part_number, $this->getDealerDistributor(), $stock_code, $cost, $price, $customerdistributor_id, $customerdistributor . "-" . $part_number));
        $partvariation_id = $this->db->insert_id();

        // now, you must enter this into partdealervariation
        $this->db->query("Insert into partdealervariation (partvariation_id, part_number, distributor_id, quantity_available, stock_code, quantity_last_updated, cost, price, clean_part_number) select partvariation_id, part_number, distributor_id, ?, stock_code, quantity_last_updated, cost, price, clean_part_number from partvariation where partvariation_id = ?  on duplicate key update stock_code = values(stock_code), quantity_last_updated = values(quantity_last_updated), cost = values(cost), price = values(price), quantity_available = values(quantity_available) ", array($quantity_available, $partvariation_id));

        return $partvariation_id;
    }

    public function newCreatePartVariation($part_number, $quantity_available, $stock_code, $cost, $price, $distributor_id) {
        $this->db->query("Insert into partvariation (part_number, distributor_id, stock_code, quantity_last_updated, cost, price, protect, clean_part_number) values (?, ?, ?, now(), ?, ?, 1, ?) on duplicate key update partvariation_id = last_insert_id(partvariation_id), stock_code = values(stock_code), quantity_last_updated = values(quantity_last_updated), cost = values(cost), price = values(price), protect = values(protect), customerdistributor_id = values(customerdistributor_id)", array($part_number, $distributor_id, $stock_code, $cost, $price, preg_replace("/[^a-z0-9]/i", "", $part_number)));
        $partvariation_id = $this->db->insert_id();

        // now, you must enter this into partdealervariation
        $this->db->query("Insert into partdealervariation (partvariation_id, part_number, distributor_id, quantity_available, stock_code, quantity_last_updated, cost, price, clean_part_number) select partvariation_id, part_number, distributor_id, ?, stock_code, quantity_last_updated, cost, price, clean_part_number from partvariation where partvariation_id = ?  on duplicate key update stock_code = values(stock_code), quantity_last_updated = values(quantity_last_updated), cost = values(cost), price = values(price), quantity_available = values(quantity_available) ", array($quantity_available, $partvariation_id));

        return $partvariation_id;
    }

    public function setPartVariationPartNumber($partvariation_id, $partnumber_id) {
        $this->db->query("Update partvariation set partnumber_id = ? where partvariation_id = ? limit 1", array($partnumber_id, $partvariation_id));
        $this->db->query("Update partdealervariation set partnumber_id = ? where partvariation_id = ? limit 1", array($partnumber_id, $partvariation_id));
    }

    public function createPartNumber($part_number, $cost, $price, $customerdistributor) {
        $this->db->query("Insert into partnumber (partnumber, price, cost, inventory, universalfit, protect) values (?, ?, ?, 0, 1, 1) on duplicate key update price = values(price), cost = values(cost), inventory = values(inventory), universalfit = values(universalfit), protect = values(protect), partnumber_id = last_insert_id(partnumber_id)", array($customerdistributor . "-" . $part_number, $price, $cost));
        return $this->db->insert_id();
    }

    public function queuePart($part_id) {
        $this->db->query("Insert into queued_parts (part_id) values (?)", array($part_id));
    }

    public function insertPartPartNumber($part_id, $partnumber_id) {
        $this->db->query("Insert into partpartnumber (part_id, partnumber_id) values (?, ?) on duplicate key update partpartnumber_id = values(partpartnumber_id)", array($part_id, $partnumber_id));
    }

    public function getCustomerDistributor($customerdistributor) {
        return $this->fetchByColumn("customerdistributor", "customerdistributor_id", "name", $customerdistributor);
    }

    public function getOrMakeCustomerDistributor($customerdistributor) {
        $customerdistributor_id = $this->getCustomerDistributor($customerdistributor);
        if ($customerdistributor_id == 0) {
            $this->db->query("Insert into customerdistributor (name) values (?)", array($customerdistributor));
            $customerdistributor_id = $this->db->insert_id();
        }
        return $customerdistributor_id;
    }

    public function getBrandForManufacturer($manufacturer_id) {
        return $this->fetchByColumn("manufacturer", "brand_id", "manufacturer_id", $manufacturer_id);
    }

    public function assignPartManufacturer($part_id, $manufacturer_id) {
        $this->db->query("Update part set manufacturer_id = ? where part_id = ?", array($manufacturer_id, $part_id));
        // And you have to go get that brand to insert into partbrand
        $brand_id = $this->getBrandForManufacturer($manufacturer_id);
        $this->db->query("Insert into partbrand (part_id, brand_id) values (?, ?) on duplicate key update brand_id = values(brand_id) ", array($part_id, $brand_id));
    }

    public function getOrCreateCategory($category_long_name) {
        $orig_category_long_name = $category_long_name = trim($category_long_name);
        if ($category_long_name == "") {
            return null;
        }
        $pieces = explode(">", $category_long_name);
        $last_piece = array_pop($pieces);
        $parent_category_id = $this->getOrCreateCategory(implode(">", $pieces));
        $category_id = 0;
        $args = is_null($parent_category_id) ? array(trim($last_piece)) : array(trim($last_piece), $parent_category_id);
        $where = is_null($parent_category_id) ? " and parent_category_id is null " : " and parent_category_id = ?";
        $query = $this->db->query("Select category_id from category where name = ? $where", $args);
        foreach ($query->result_array() as $row) {
            $category_id = $row["category_id"];
        }


        if ($category_id == 0) {
            // You have to insert it....
            $this->db->query("Insert into category (parent_Category_id, name, long_name, mx) values (?, ?, ?, 0)", array($parent_category_id, trim($last_piece), $orig_category_long_name));
            $category_id = $this->db->insert_id();
        }

        return $category_id;
    }

    public function addPartCategory($part_id, $category_id) {
        $this->db->query("Insert into partcategory (part_id, category_id) values (?, ?) on duplicate key update partcategory_id = last_insert_id(partcategory_id)", array($part_id, $category_id));
    }

    public function updatePart($part_id, $column, $value) {
        $this->db->query("Update part set $column = ? where part_id = ? limit 1", array($value, $part_id));
    }

    public function getPartCategories($part_id) {
        $query = $this->db->query("Select long_name from category join partcategory using (category_id) where part_id = ? order by category.long_name", array($part_id));
        return $query->result_array();
    }

    public function removePartCategory($part_id, $category_id) {
        $this->db->query("Delete from partcategory where part_id = ? and category_id = ?", array($part_id, $category_id));
    }

    public function getCategoryByLongname($longname) {
        return $this->fetchByColumn("category", "category_id", "long_name", $longname);
    }

    public function getPartBrand($part_id) {
        $query = $this->db->query("Select brand.* from brand join partbrand using (brand_id) where partbrand.part_id = ?", array($part_id));
        $result = $query->result_array();
        return count($result) > 0 ? $result[0] : array();
    }

    public function getPartImages($part_id) {
        $query = $this->db->query("Select * from partimage where part_id = ?", array($part_id));
        return $query->result_array();
    }

    public function fetchQuestions($part_id) {
        $rows = array();

        $query = $this->db->query("Select * from partquestion where part_id = ? order by created", array($part_id));

        foreach ($query->result_array() as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function genericFetch($table, $column, $value) {
        $query = $this->db->query("Select * from $table where $column = ?", array($value));
        $result = array();
        foreach ($query->result_array() as $row) {
            $result = $row;
        }
        return $result;
    }

    public function getPartQuestion($partquestion_id) {
        return $this->genericFetch("partquestion", "partquestion_id", $partquestion_id);
    }


    public function matchByAttributes($table, $kvpArray) {
        $values = array();
        $query = "Select * from $table";
        $count = 0;

        foreach ($kvpArray as $key => $value) {
            if ($count == 0) {
                $query .= " WHERE ";
                $count++;
            } else {
                $query .= " AND ";
            }

            if (is_null($value)) {
                $query .= " $key is null ";
            } else {
                $query .= " $key = ? ";
                $values[] = $value;
            }
        }

        $results = array();

        try {
            $query = $this->db->query($query, $values);
            foreach ($query->result_array() as $row) {
                $results[] = $row;
            }
        } catch(Exception $e) {
            error_log("Error: matchByAttributes(" . print_r($kvpArray, true) . "): " . $e->getMessage());
        }

        return $results;
    }

    public function insert($table, $id_column, $kvpArray) {
        $query = "insert into $table (";
        $value_query = ") values (";
        $values = array();
        $duplicate = " on duplicate key update $id_column = last_insert_id($id_column) ";
        $match_where = "";

        $id = 0;

        try {
            foreach ($kvpArray as $k => $v) {
                // $v = htmlentities($v, ENT_QUOTES, 'UTF-8');
                if (count($values) > 0) {
                    $value_query .= ", ";
                    $query .= ", ";
                    $match_where .= " AND ";
                }

                $value_query .= "?";
                $query .= $k;
                $values[] = $v;
                $duplicate .= ", $k = values($k) ";
                $match_where .= " $k = ? ";
            }

            $this->db->query($query . $value_query . ")" . $duplicate, $values);
            $query2 = $this->db->query("Select last_insert_id()");
            foreach ($query2->result_array() as $row) {
                $id = $row['last_insert_id()'];
            }

        } catch(Exception $e) {
            log_message("error", "Error: Virtualadminmodel::insert($table, " . print_r($kvpArray, true) . "):" . $e->getMessage());
        }

        if ($id == 0) {
            print "Failure: " . $query . $value_query . ")" . $duplicate . "\n";
            print_r($values);
        }
        return $id;
    }

    public function update($table, $id_column, $id, $kvpArray) {
        $query = "update $table set ";
        $values = array();

        try {
            foreach ($kvpArray as $k => $v) {
                // $v = htmlentities($v, ENT_QUOTES, 'UTF-8');
                if (count($values) > 0) {
                    $query .= ", ";
                }

                $query .= $k . " = ? ";
                $values[] = $v;
            }

            $values[] = $id;
            $this->db->query($query . " where $id_column = ? limit 1", $values);
        } catch(Exception $e) {
            error_log("Error: Virtualadminmodel::update($id, $id_column, $table, " . print_r($kvpArray, true) . "):" . $e->getMessage());
        }
    }

    public function remove($table, $id_column, $id) {
        $this->db->query("Delete from $table where $id_column = ?", array($id));
    }

    /*
     * These came from revisionmodel
     */

    public function _getPartCollection($part_id) {
        $results = array();
        $query = $this->db->query("Select part.* from part where part_id = ? and mx = 0", array($part_id));
        foreach ($query->result_array() as $row) {
            $results[] = $row;
        }
        return $results;
    }

    // to get answers, we have to merge partquestionanswer to partquestion
    public function _getPartQuestionAnswerCollection($part_id) {
        $rows = array();
        $query = $this->db->query("Select partquestionanswer.*, partquestion.*, part.name from partquestionanswer join partquestion using (partquestion_id) join part using (part_id) where part.part_id = ? and part.mx = 0", array($part_id));

        foreach ($query->result_array() as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function _getPartQuestionAnswerFitmentCollection() {
        return array();
    }

    public function _getPartQuestionAnswerPartVariationCollection() {
        return array();
    }

    public function _getPartVariationCollection($part_id) {
        $rows = array();

        $query = $this->db->query("Select distinct partvariation.*, distributor.name as distributor_name, partdealervariation.cost, partdealervariation.quantity_available as qty_available, partdealervariation.stock_code, partdealervariation.price from partvariation join distributor using (distributor_id) join partdealervariation using (partvariation_id) join partpartnumber on partvariation.partnumber_id = partpartnumber.partnumber_id where partpartnumber.part_id = ?", array($part_id));

        foreach ($query->result_array() as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function _getDistributorCollection() {
        $rows = array();
        $query = $this->db->query("Select * from distributor where active = 1 ", array());
        foreach ($query->result_array() as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function _getManufacturerCollection() {
        $results = array();
        $query = $this->db->query("Select * from manufacturer");
        foreach ($query->result_array() as $row) {
            $results[] = $row;
        }

        return $results;
    }

    public function _getPartNumberCollection($part_id) {
        $results = array();

        $query = $this->db->query("Select distinct partnumber.* from partnumber join partpartnumber using (partnumber_id) where part_id = ? order by partnumber ", array($part_id));

        foreach ($query->result_array() as $row) {
            $results[] = $row;
        }

        return $results;
    }

    public function _getPartPartNumberCollection($part_id) {
        $results = array();
        $query = $this->db->query("Select partpartnumber.* from partpartnumber join part using (part_id) where part.part_id = ? ", array($part_id));
        foreach ($query->result_array() as $row) {
            $results[] = $row;
        }
        return $results;
    }

    public function getDataStructure($part_id, $subset) {
        $results = array();
        if ($subset != "") {
            switch ($subset) {
                case "PartCollection":
                    $results["PartCollection"] = $this->_getPartCollection($part_id);
                    break;
                case "PartQuestionCollection":
                    $results["PartQuestionCollection"] = $this->_getPartQuestionCollection($part_id);
                    break;
                case "PartQuestionAnswerCollection":
                    $results["PartQuestionAnswerCollection"] = $this->_getPartQuestionAnswerCollection($part_id);
                    break;
                case "PartVariationCollection":
                    $results["PartVariationCollection"] = $this->_getPartVariationCollection($part_id);
                    break;
                case "DistributorCollection":
                    $results["DistributorCollection"] = $this->_getDistributorCollection();
                    break;
                case "PartQuestionAnswerPartVariationCollection":
                    $results["PartQuestionAnswerPartVariationCollection"] = $this->_getPartQuestionAnswerPartVariationCollection($part_id);
                    break;
                case "PartQuestionAnswerFitmentCollection":
                    $results["PartQuestionAnswerFitmentCollection"] = $this->_getPartQuestionAnswerFitmentCollection($part_id);
                    break;
                case "ManufacturerCollection":
                    $results["ManufacturerCollection"] = $this->_getManufacturerCollection();
                    break;
                case "PartNumberCollection":
                    $results["PartNumberCollection"] = $this->_getPartNumberCollection($part_id);
                    break;
                case "PartPartNumberCollection":
                    $results["PartPartNumberCollection"] = $this->_getPartPartNumberCollection($part_id);
                    break;
                case "PartNumberPartQuestionCollection":
                    $results["PartNumberPartQuestionCollection"] = $this->_getPartNumberPartQuestionCollection($part_id);
                    break;
                case "PartNumberModelCollection":
                    $results["PartNumberModelCollection"] = $this->_getPartNumberModelCollection($part_id);
                    break;
                case "KnownModelCollection" :
                    $results["KnownModelCollection"] = $this->_getKnownModelCollection();
                    break;
            }
        } else {
            $results["PartCollection"] = $this->_getPartCollection($part_id);
            $results["PartQuestionCollection"] = $this->_getPartQuestionCollection($part_id);
            $results["PartQuestionAnswerCollection"] = $this->_getPartQuestionAnswerCollection($part_id);
            $results["PartVariationCollection"] = $this->_getPartVariationCollection($part_id);
            $results["DistributorCollection"] = $this->_getDistributorCollection();
            $results["PartQuestionAnswerPartVariationCollection"] = $this->_getPartQuestionAnswerPartVariationCollection($part_id);
            $results["PartQuestionAnswerFitmentCollection"] = $this->_getPartQuestionAnswerFitmentCollection($part_id);
            $results["ManufacturerCollection"] = $this->_getManufacturerCollection();
            $results["PartNumberCollection"] = $this->_getPartNumberCollection($part_id);
            $results["PartPartNumberCollection"] = $this->_getPartPartNumberCollection($part_id);
            $results["PartNumberPartQuestionCollection"] = $this->_getPartNumberPartQuestionCollection($part_id);
            $results["PartNumberModelCollection"] = $this->_getPartNumberModelCollection($part_id);
            $results["KnownModelCollection"] = $this->_getKnownModelCollection();
        }

        return $results;
    }

    public function _getKnownModelCollection() {
        $results = array();

        $query = $this->db->query("select distinct machinetype.label as machinetype, make.label as make, model.label  as model from machinetype join make using (machinetype_id) join model using (make_id) order by machinetype, make, model");

        foreach ($query->result_array() as $row) {
            $results[] = $row;
        }

        return $results;
    }

    public function _getPartNumberModelCollection($part_id) {
        $results = array();
        $query = $this->db->query("Select distinct partnumbermodel.partnumber_id, partnumbermodel.partnumbermodel_id, year, UPPER(model.name) as model_name, UPPER(make.name) as make_name, UPPER(machinetype.name) as machinetype_name, model.model_id, make.make_id, machinetype.machinetype_id from partnumbermodel join partnumber using (partnumber_id) join model using (model_id) join make using (make_id) join machinetype using (machinetype_id) join partpartnumber on partnumber.partnumber_id = partpartnumber.partnumber_id where partpartnumber.part_id = ? ", array($part_id));

        foreach ($query->result_array() as $row) {
            $results[] = $row;
        }

        return $results;
    }

    public function _getPartNumberPartQuestionCollection($part_id) {
        $results = array();

        $query = $this->db->query("Select distinct partnumber.*, partnumberpartquestion.answer, partnumberpartquestion.partquestion_id, partnumberpartquestion_id from partnumber join partnumberpartquestion using (partnumber_id) join partpartnumber on partnumber.partnumber_id = partpartnumber.partnumber_id where partpartnumber.part_id = ? ", array($part_id));

        foreach ($query->result_array() as $row) {
            $results[] = $row;
        }

        return $results;
    }

    public function _getPartQuestionCollection($part_id) {
        $rows = array();

        $query = $this->db->query("Select partquestion.*, part.name from partquestion join part using (part_id) where part.part_id = ? ", array($part_id));

        foreach ($query->result_array() as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function removeVariation($partquestionanswer_id, $partvariation_id) {
        // get the part question answer
        $query = $this->db->query("Select * from partquestionanswer where partquestionanswer_id = ?", array($partquestionanswer_id));
        $partquestionanswer = $query->result_array();

        if (count($partquestionanswer) == 0) {
            return;
        } else {
            $partquestionanswer = $partquestionanswer[0];
        }

        // Now, you better get the part question...
        $query = $this->db->query("Select * from partquestion where partquestion_id = ?", array($partquestionanswer["partquestion_id"]));
        $partquestion = $query->result_array();

        if (count($partquestion) == 0) {
            return;
        } else {
            $partquestion = $partquestion[0];
        }

        // OK, we have to delete it
        $this->db->query("Delete from partnumberpartquestion where partquestion_id = ? and answer = ? and partnumber_id in (select partnumber_id from partvariation where partvariation_id = ?)", array($partquestion["partquestion_id"], $partquestionanswer["answer"], $partvariation_id));

        // now, if there aren't any, remove it.
        $this->db->query("Delete from partquestionanswer where partquestionanswer_id = ? and partquestionanswer_id not in (Select partquestionanswer_id from partquestionanswerpartvariation) limit 1", array($partquestionanswer_id));
    }

    public function fetchAnswers($partquestion_id) {
        $rows = array();

        $query = $this->db->query("Select partquestionanswer.*, partquestion.*, part.name from partquestionanswer join partquestion using (partquestion_id) join part using (part_id) where partquestion.partquestion_id = ? order by partquestionanswer.created", array($partquestion_id));
        foreach ($query->result_array() as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function removePartQuestionAnswer($part_id, $id)
    {
        // First, get your hands on it.
        $data = $this->genericFetch("partquestionanswer", "partquestionanswer_id", $id);
        if (is_array($data) && array_key_exists("answer", $data)) {
            $matches = $this->matchByAttributes("partnumberpartquestion", array("partquestion_id" => $data["partquestion_id"], "answer" => $data["answer"]));
//            // OK, you gots to kill the entries in partnumberpartquestion too
//            $this->db->query("Delete from partnumberpartquestion where partquestion_id = ? and answer = ?", array($data["partquestion_id"], $data["answer"]));
            foreach ($matches as $match) {
                $this->removePartQuestionNumber($part_id, $match["partquestion_id"], $match["partnumber_id"]);
            }
        }
        return $this->remove("partquestionanswer", "partquestionanswer_id", $id); // TODO: Change the autogenerated stub
    }

    public function addAnswer($revisionset_id, $partquestion_id, $answer, $distributor_id, $part_number, $fitments) {
        /*
         *  First make sure that answer is in there.
         */
        $matching_answers = $this->matchByAttributes("partquestionanswer", array(
            "answer" => $answer, "partquestion_id" => $partquestion_id
        ));

        // This value itself is almost completely useless.  We gutted these tables.
        if (count($matching_answers) == 0) {
            $partquestionanswer_id = $this->insert("partquestionanswer", "partquestionanswer_id", array("partquestion_id" => $partquestion_id, "answer" => $answer));
        } else {
            $partquestionanswer_id = $matching_answers[0]["partquestionanswer_id"];
        }

        // - If the part variation is already there, use it.
        $matching_variations = $this->matchByAttributes("partvariation", array(
            "part_number" => $part_number, "distributor_id" => $distributor_id
        ));

        if (count($matching_variations) == 0) {
            // I think we just create a new one?
            $partnumber_id = $this->insert("partnumber", "partnumber_id", array(
                "universalfit" => (count($fitments) == 0 ? 1 : 0), "protect" => 1
            ));
            $partvariation_id = $this->insert("partvariation", "partvariation_id", array(
                "part_number" => $part_number, "distributor_id" => $distributor_id,
                "partnumber_id" => $partnumber_id, "protect" => 1
            ));

        } else {
            $partvariation_id = $matching_variations[0]["partvariation_id"];
            // fetch that partnumber_id
            $partnumber_id = $matching_variations[0]["partnumber_id"];
        }

        // Just mark them.
        $this->update("partnumber", "partnumber_id", $partnumber_id, array("protect" => 1, "price" => $_REQUEST["price"], "cost" => $_REQUEST["cost"], "sale" => $_REQUEST["price"], "dealer_sale" => $_REQUEST["price"], "weight" => $_REQUEST["weight"]));
        $this->update("partvariation", "partvariation_id", $partvariation_id, array("protect" => 1, "price" => $_REQUEST["price"], "cost" => $_REQUEST["cost"], "stock_code" => $_REQUEST["stock_code"], "weight" => $_REQUEST["weight"]));

        // Fix the label, if required...
        $this->db->query("Update partnumber join partvariation using (partnumber_id) join distributor using (distributor_id) set partnumber = concat(distributor.name, '-', partvariation.part_number) where partnumber.partnumber_id = ? and partvariation.partvariation_id = ?", array($partnumber_id, $partvariation_id));

        // Is this part from Lightspeed?
        $query = $this->db->query("Select count(*) as cnt from lightspeedpart where partvariation_id = ?", array($partvariation_id));
        $count = $query->result_array();
        $count = $count[0]["cnt"];

        // Insert into partdealervariation, if quired
        if ($count == 0) {
            $this->db->query("Insert into partdealervariation (partvariation_id, part_number, partnumber_id, distributor_id, quantity_available, quantity_ten_plus, quantity_last_updated, cost, price, clean_part_number, revisionset_id, manufacturer_part_number, weight, stock_code) select partvariation_id, part_number, partnumber_id, distributor_id, ?, ?, now(), ?, ?, clean_part_number, revisionset_id, manufacturer_part_number, ?, ? from partvariation where partvariation_id = ? on duplicate key update quantity_available = values(quantity_available), quantity_ten_plus = values(quantity_ten_plus), quantity_last_updated = now(), cost = values(cost), price = values(price), weight = values(weight), stock_code = values(stock_code)", array($_REQUEST["qty_available"], $_REQUEST["qty_available"] > 9 ? 1 : 0, $_REQUEST["cost"], $_REQUEST["price"], $_REQUEST["weight"], $_REQUEST["stock_code"], $partvariation_id));
        }

        // Insert into partpartnumber...
        $this->db->query("Insert into partpartnumber (part_id, partnumber_id) values (?, ?) on duplicate key update partpartnumber_id = last_insert_id(partpartnumber_id)", array($revisionset_id, $partnumber_id));

        // insert into partnumberpartquestion
        $this->db->query("Insert into partnumberpartquestion (partquestion_id, partnumber_id, answer) values (?, ?, ?) on duplicate key update answer = values(answer)", array($partquestion_id, $partnumber_id, $answer));

        // insert the fitments!
        if ($partnumber_id > 0) {
            if (count($fitments) > 0) {
                foreach ($fitments as $fitment) {
                    // machine type
                    $machinetypes = $this->matchByAttributes("machinetype", array(
                        "revisionset_id" => $revisionset_id,
                        "name" => $fitment["machinetype_name"]
                    ));

                    if (count($machinetypes) > 0) {
                        $machinetype_id = $machinetypes[0]["machinetype_id"];
                    } else {
                        $machinetype_id = $this->insert("machinetype", "machinetype_id", array(
                            "name" => $fitment["machinetype_name"],
                            "label" => $fitment["machinetype_name"],
                            "revisionset_id" => $revisionset_id
                        ));
                    }

                    $makes = $this->matchByAttributes("make", array(
                        "machinetype_id" => $machinetype_id,
                        "name" => $fitment["make_name"]
                    ));

                    if (count($makes) > 0) {
                        $make_id = $makes[0]["make_id"];
                    } else {
                        $make_id = $this->insert("make", "make_id", array(
                            "name" => $fitment["make_name"],
                            "label" => $fitment["make_name"],
                            "machinetype_id" => $machinetype_id
                        ));
                    }

                    $models = $this->matchByAttributes("model", array(
                        "make_id" => $make_id,
                        "name" => $fitment["model_name"]
                    ));

                    if (count($models) > 0) {
                        $model_id = $models[0]["model_id"];
                    } else {
                        $model_id = $this->insert("model", "model_id", array(
                            "name" => $fitment["model_name"],
                            "label" => $fitment["model_name"],
                            "make_id" => $make_id
                        ));
                    }

                    // insert the match
                    $this->insert("partnumbermodel", "partnumbermodel_id", array(
                        "model_id" => $model_id,
                        "year" => $fitment["year"],
                        "partnumber_id" => $partnumber_id
                    ));
                }

                $this->update("partnumber", "partnumber_id", $partnumber_id, array("universalfit" => 0));
            } else{
                $this->update("partnumber", "partnumber_id", $partnumber_id, array("universalfit" => 1));
            }
        }

        $this->db->query("Insert into queued_parts (part_id) values (?)", array($revisionset_id));

        return $partquestionanswer_id;
    }

    public function getPartImage($partimage_id) {
        $results = array();

        $query = $this->db->query("Select * from partimage where partimage_id = ?", array($partimage_id));
        foreach ($query->result_array() as $row) {
            return $row;
        }

        return $results;
    }

    public function addImage($part_id, $upload) {
        require_once(__DIR__ . "/../../simpleimage.php");

        $path = tempnam(STORE_DIRECTORY . "/html/storeimages/", "t");
        $thumbnail_file = basename($path) . ".png";
        $image_file = substr($thumbnail_file, 1);

        $image = new SimpleImage();
        $image->load($upload['tmp_name']);
        $image->save(STORE_DIRECTORY . "/html/storeimages/" . $image_file, IMAGETYPE_PNG);
        $image->setMaxDimension(144);
        $image->save(STORE_DIRECTORY . "/html/storeimages/" . $thumbnail_file, IMAGETYPE_PNG);

        $this->db->query("insert into partimage (part_id, original_filename, path) values (?, ?, ?)", array($part_id, $upload['name'], "store/" . $image_file));
        $partimage_id = $this->db->insert_id();
        return $partimage_id;
    }

    public function removePartQuestion($partquestion_id) {
        $this->db->query("Delete from partquestion where partquestion_id = ?", array($partquestion_id));
    }

    public function removeImage($partimage_id) {
        $image = $this->getPartImage($partimage_id);
        $store_directory = STORE_DIRECTORY . "/html/storeimages/";
        $base_name = basename($image["path"]);

        $file = $store_directory . $base_name;
        if (file_exists($file) && is_file($file)) {
            unlink($file);
        }

        $file = $store_directory . "t" . $base_name;
        if (file_exists($file) && is_file($file)) {
            unlink($file);
        }

        // delete it
        $this->db->query("Delete from partimage where partimage_id = ?", array($partimage_id));
    }

    public function fetchPartQuestions($part_id) {
        $rows = array();

        $query = $this->db->query("Select * from partquestion where part_id = ? order by created", array($part_id));

        foreach ($query->result_array() as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function cleanPartNumber($partnumber_id) {
        $count = 0;
        $query = $this->db->query("Select count(*) as cnt from partpartnumber where partnumber_id = ?", array($partnumber_id));
        foreach ($query->result_array() as $row) {
            $count = $row['cnt'];
        }

        if ($count == 0) {
            // Leave the pruner to decide it, but no more point in protecting it...
            $this->db->query("Update partnumber set protect = 0 where partnumber_id = ?", array($partnumber_id));
            $this->db->query("Update partvariation set protect = 0 where partnumber_id = ?", array($partnumber_id));
        }
    }

    public function removePartQuestionNumber($part_id, $partquestion_id, $partnumber_id) {
        $this->db->query("Delete from partnumberpartquestion where partnumber_id = ? and partquestion_id = ?", array($partnumber_id, $partquestion_id));

        // Now, what if this has no relationship?
        $query = $this->db->query("Select count(*) as cnt from partnumberpartquestion join partquestion using (partquestion_id) where partnumber_id = ? and partquestion.part_id = ?", array($partnumber_id, $part_id));
        $count = 0;
        foreach ($query->result_array() as $row) {
            $count = $row['cnt'];
        }

        if ($count == 0) {
            $this->db->query("Delete from partpartnumber where part_id = ? and partnumber_id = ?", array($part_id, $partnumber_id));
            $this->cleanPartNumber($partnumber_id);
        }
    }


    public function cleanPart($part_id) {
        // If the part numbers have no part variations, then this is crap, remove them.

        // If the part numbers aren't in partnumberpartquestion, then this is crap, remove them.
        $query = $this->db->query("Select partnumber_id from partpartnumber where part_id = ? and partnumber_id not in (select partnumber_id from partvariation)", array($part_id));
        $partnumber = array();
        foreach ($query->result_array() as $row) {
            $partnumber[] = $row["partnumber_id"];
        }

        if (count($partnumber) > 0) {
            $this->db->query("Delete from partpartnumber where partnumber_id in (" . implode(", ", $partnumber) . ")");
        }

        // If the part numbers aren't in partnumberpartquestion, then this is crap, remove them.
        $query = $this->db->query("Select partnumber_id from partpartnumber where part_id = ? and partnumber_id not in (select partnumber_id from partnumberpartquestion join partquestion using (partquestion_id) where partquestion.part_id = ?)", array($part_id, $part_id));
        $partnumber = array();
        foreach ($query->result_array() as $row) {
            $partnumber[] = $row["partnumber_id"];
        }

        if (count($partnumber) > 0) {
            $this->db->query("Delete from partpartnumber where partnumber_id in (" . implode(", ", $partnumber) . ")");
        }

        // We should get rid of marooned partnumbers and marooned part variations.
        $this->db->query("Delete from partnumber where partnumber_id not in (select partnumber_id from partpartnumber)");
        $this->db->query("Delete from partvariation where partnumber_id not in (select partnumber_id from partnumber)");
        $this->db->query("Delete from partdealervariation where partnumber_id not in (select partnumber_id from partnumber)");

        // We have to make sure there are partdealervariation entries.
        $this->db->query("insert into partdealervariation (partvariation_id, part_number, partnumber_id, distributor_id, quantity_available, quantity_ten_plus, stock_code, cost, price, clean_part_number, revisionset_id) select partvariation_id, part_number, partnumber_id, distributor_id, 0, 0, 'Normal', 0, 0, clean_part_number, 1 from partvariation where partnumber_id in (select partnumber_id from partpartnumber where part_id = ?) on duplicate key update partdealervariation.partvariation_id = partdealervariation.partvariation_id", array($part_id));

        // Queue this part.
        $this->db->query("Insert into queued_parts (part_id) values (?)", array($part_id));
    }

}