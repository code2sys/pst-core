<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/19/17
 * Time: 7:16 AM
 */

require_once("admin.php");

class Adminproduct extends Admin {

    public function __construct() {
        parent::__construct();
        $this->load->model("Portalmodel");
        $this->load->model("Statusmodel");
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
            exit();
        }
    }

    public function index() {
        $this->product();
    }

    public function product_add() {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $query = $this->db->query("Select distinct name from manufacturer order by name");
        $this->_mainData["manufacturers"] = array("" => "-- Select Existing Manufacturer --");
        foreach ($query->result_array() as $row) {
            $this->_mainData["manufacturers"][ $row["name"] ] = $row["name"];
        }

        $fields = array("name", "manufacturer", "new_manufacturer", "description", "categories");

        // How do we push through errors?
        if (array_key_exists("product_add_errors", $_SESSION) && $_SESSION["product_add_errors"] != "") {
            $this->_mainData["error"] = $_SESSION["product_add_errors"];
            $_SESSION["product_add_errors"] = "";

            foreach ($fields as $key) {
                $this->_mainData[$key] = $_SESSION["product_add_" . $key];
                $_SESSION["product_add_" . $key] = "";
            }

        } else {
            foreach ($fields as $k) {
                $this->_mainData[$k] = "";
            }
        }

        // We're just interested in getting the error input, if there is any, so we can echo this form.
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/add_v', $this->_mainData);
    }

    public function product_add_save() {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        // OK, get those inputs, assuming the JavaScript did the right thing and added them in there...
        $part_name = trim($_REQUEST["name"]);
        $manufacturer = trim($_REQUEST["manufacturer"]);
        if ($manufacturer == "") {
            $manufacturer = trim($_REQUEST["new_manufacturer"]);
        }
        $description = $_REQUEST["description"];
        $categories = $_REQUEST["categories"];

        // OK, errors
        $_SESSION["product_add_errors"] = "";

        if ($part_name == "") {
            $_SESSION["product_add_errors"] .= "Sorry, part name must be provided. ";
        } else if (0 < $this->Portalmodel->getPartIDByName($part_name)) {
            $_SESSION["product_add_errors"] .= "Sorry, that part already exists. ";
        }

        if ($manufacturer == "") {
            $_SESSION["product_add_errors"] .= "Sorry, please provide a brand. ";
        }

        if ($_SESSION["product_add_errors"] != "") {
            foreach (array("name", "manufacturer", "new_manufacturer", "description", "categories") as $key) {
                $_SESSION["product_add_" . $key] = array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : "";
            }
            header("Location: " . base_url("adminproduct/product_add"));
        } else {
            // Make the part by name
            $part_id = $this->Portalmodel->makePartByName($part_name, $description);

            // Make the manufacturer
            $manufacturer_id = $this->Portalmodel->getOrMakeManufacturer($manufacturer);

            // Assign the manufacturer
            $this->Portalmodel->assignPartManufacturer($part_id, $manufacturer_id);

            // OK, time for the categories...
            $categories = preg_split("/\r\n|\n|\r/", $categories);
            foreach ($categories as $c) {
                $c = trim($c);
                $c = $this->Portalmodel->getOrCreateCategory($c);
                $this->Portalmodel->addPartCategory($part_id, $c);
            }

            $this->Portalmodel->queuePart($part_id);

            // Now, redirect to the edit screen...
            $_SESSION["product_add_success"] = "Product added successfully; please provide additional details for pricing and configuration.";
            header("Location: " . base_url("adminproduct/product_edit/" . $part_id));
        }
    }

    public function product_ajax() {
        $columns = array(
            "partnumber",
            "",
            "part.name",
            "part.mx",
            "part.featured",
            "min_cost",
            "min_price",
            "markup",
            "min_sale",
            "part.name"
        );

        $length = array_key_exists("length", $_REQUEST) ? $_REQUEST["length"] : 500;
        $start = array_key_exists("start", $_REQUEST) ? $_REQUEST["start"] : 0;

        $order_string = "order by part.name asc ";

        if (array_key_exists("order", $_REQUEST) && is_array($_REQUEST["order"]) && count($_REQUEST["order"]) > 0) {
            // OK, there's a separate order string...
            $order_string = "order by ";
            $orderings = $_REQUEST["order"];
            if (count($orderings) == 0) {
                $order_string .= " part.name asc";
            } else {
                for ($i = 0; $i < count($orderings); $i++) {
                    if ($i > 0) {
                        $order_string .= ", ";
                    }

                    $field = $columns[$orderings[$i]["column"]];
                    $order_string .=  $field . " " . $orderings[$i]["dir"];
                }
            }
        }

        $this->load->model("Portalmodel");
        list($products, $total_count, $filtered_count) = $this->Portalmodel->enhancedGetProducts(null, $s = (array_key_exists("search", $_REQUEST) && array_key_exists("value", $_REQUEST["search"]) ? $_REQUEST["search"]["value"] : ""), $order_string, $length, $start);

        // Now, order them...
        $rows = array();
        foreach ($products as $p) {
            $rows[] = array(
                $p["partnumber"], "<img style='width: 60px; height: auto;' src='" . ($p["path"] != "" ? ("/productimages/" . ("store/" == substr($p["path"], 0, 6) ? (str_replace("store/", "store/t", $p["path"]) ) : ("t" . $p["path"]))) : "data:image/jpeg;base64,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") . "' />", $p["name"], $p["mx"] == 0 ? "YES" : "NO", $p["featured"] > 0 ? "YES" : "NO",
                $p["cost_min"] != "" ? ($p["cost_min"] != $p["cost_max"] ? '$' . $p['cost_min'] . ' - ' . $p['cost_max'] : '$' . $p['cost_min']): "",
                $p["price_min"] != "" ? ($p["price_min"] != $p["price_max"] ? '$' . $p['price_min'] . ' - ' . $p['price_max'] : '$' . $p['price_min']): "",
                $p['markup'],
                $p["sale_min"] != "" ? ($p["sale_min"] != $p["sale_max"] ? '$' . $p['sale_min'] . ' - ' . $p['sale_max'] : '$' . $p['sale_min']): "",
                '<a href="' . base_url('adminproduct/product_edit/' . $p['part_id']) . '"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a>' . ($p['mx'] == 0 ? ' | <a href="' . base_url('adminproduct/product_remove/' . $p['part_id']) . '"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a>' : '')
            );
        }

        print json_encode(array(
            "data" => $rows,
            "draw" => array_key_exists("draw", $_REQUEST) ? $_REQUEST["draw"] : 0,
            "recordsTotal" => $total_count,
            "recordsFiltered" => $filtered_count,
            "limit" => $length,
            "offset" => $start,
            "order_string" => $order_string,
            "search" => $s
        ));
    }

    public function product_remove($part_id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        // OK, we need to go see if that part is, in fact, an MX part...
        $this->db->query("Delete from part where mx = 0 and part_id = ?", array($part_id));

        $_SESSION["jonathan_product_message"] = "Product removed successfully.";

        redirect('adminproduct/product');
    }

    /*
     * JLB 02-16-17
     * I changed this to gut that old table and use the functions above
     */
    public function product($cat = NULL) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if (array_key_exists("jonathan_product_message", $_SESSION) && $_SESSION["jonathan_product_message"] != "") {
            $this->_mainData['success'] = "<br/>" . $_SESSION["jonathan_product_message"];

            $_SESSION["jonathan_product_message"] = "";
        }

        $this->_mainData['pagination'] = $this->load->view('admin/pagination/product_list_v', $this->_mainData, TRUE);
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/list_v', $this->_mainData);
    }

    public function mark_product_status($part_id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $product = $this->admin_m->getAdminProduct($part_id);

        if (!array_key_exists("part_id", $product) && $product["mx"] != 0) {
            print json_encode(array("success" => 0, "error_message" => "Part not found."));
        } else {
            $matches = $this->Portalmodel->matchByAttributes("partpartnumber", array(
                "part_id" => $part_id,
                "partnumber_id" => $_REQUEST["partnumber_id"]
            ));

            if (count($matches) > 0) {
                $this->db->query("Update partdealervariation set stock_code = ?, closeout_on = now() where partnumber_id = ?", array($_REQUEST["stock_code"], $_REQUEST["partnumber_id"]));
                $this->db->query("Update partvariation set stock_code = ?, closeout_on = now() where partnumber_id = ?", array($_REQUEST["stock_code"], $_REQUEST["partnumber_id"]));
                $this->db->query("insert into queued_parts (part_id) values (?)", array($part_id));
                print json_encode(array("success" => 1));
            } else {
                print json_encode(array("success" => 0, "error_message" => "Part variation not found."));
            }
        }
    }

    public function removeAnswerPart($part_id, $partquestion_id, $partnumber_id)
    {
        if (!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $product = $this->admin_m->getAdminProduct($part_id);

        if (!array_key_exists("part_id", $product) && $product["mx"] != 0) {
            print json_encode(array("success" => 0, "error_message" => "Part not found."));
        } else {
            $partquestion = $this->Portalmodel->genericFetch("partquestion", "partquestion_id", $partquestion_id);

            if ($partquestion["part_id"] != $part_id) {
                print json_encode(array("success" => 0, "error_message" => "Part question not found."));
            } else {
                // OK, I guess we better go delete it and then clean the part....
                $this->db->query("Delete from partnumberpartquestion where partquestion_id = ? and partnumber_id = ?", array($partquestion_id, $partnumber_id));
                $this->Portalmodel->cleanPart($part_id);
                print json_encode(array("success" => 1, "PartNumberPartQuestionCollection" => $this->Portalmodel->_getPartNumberPartQuestionCollection($part_id)));

            }
        }
    }

    public function update_local_settings($part_id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $product = $this->admin_m->getAdminProduct($part_id);

        if (!array_key_exists("part_id", $product) && $product["mx"] != 0) {
            print json_encode(array("success" => 0, "error_message" => "Part not found."));
        } else {
            $matches = $this->Portalmodel->matchByAttributes("partpartnumber", array(
                "part_id" => $part_id,
                "partnumber_id" => $_REQUEST["partnumber_id"]
            ));

            if (count($matches) > 0) {
                // OK, update it
                $this->db->query("Update partdealervariation set quantity_available = ?, quantity_ten_plus = ?, quantity_last_updated = now(), cost = ?, price = ?, weight = ? where partnumber_id = ?", array($_REQUEST["qty_available"], $_REQUEST["qty_available"] > 9 ? 1 : 0, $_REQUEST["cost"], $_REQUEST["price"], $_REQUEST["weight"], $_REQUEST["partnumber_id"]));
                $this->db->query("Update partvariation set cost = ?, price = ?, weight = ? where partnumber_id = ?", array($_REQUEST["cost"], $_REQUEST["price"], $_REQUEST["weight"], $_REQUEST["partnumber_id"]));
                $this->db->query("Update partnumber set cost = ?, price = ?, dealer_sale = ?, weight = ? where partnumber_id = ?", array($_REQUEST["cost"], $_REQUEST["price"], $_REQUEST["price"], $_REQUEST["weight"], $_REQUEST["partnumber_id"]));
                $this->db->query("insert into queued_parts (part_id) values (?)", array($part_id));
                print json_encode(array("success" => 1));
            } else {
                print json_encode(array("success" => 0, "error_message" => "Part variation not found."));
            }
        }
    }

    public function product_image_update_description($part_id, $partimage_id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $partimage = $this->Portalmodel->getPartImage($partimage_id);
        $product = $this->admin_m->getAdminProduct($part_id);

        if (!array_key_exists("part_id", $product) && $product["mx"] != 0) {
            print json_encode(array("success" => 0, "error_message" => "Image not found."));
        } elseif (array_key_exists("part_id", $partimage) && $part_id == $partimage["part_id"]) {
            $this->Portalmodel->update("partimage", "partimagE_id", $partimage_id, array("description" => $_REQUEST["description"]));
            print json_encode(array("success" => 1, "success_message" => "Image updated successfully."));
        } else {
            print json_encode(array("success" => 0, "error_message" => "Image not found."));
        }
    }

    public function part_update($id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->helper('async');

        if (is_null($id)) {
            redirect('adminproduct/product');
        }

        if (!is_numeric($id)) {
            redirect('adminproduct/product');
        }

        $error = "";

        $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
        if ($this->_mainData["product"]["mx"] == 0) {
            if (!array_key_exists("name", $_REQUEST) || trim($_REQUEST["name"]) == "") {
                $error = "Please specify a valid part name.";
            } else {
                $_REQUEST["name"] = trim($_REQUEST["name"]);
                // check that this part doesn't already exist by name...
                $by_name = $this->Portalmodel->getPartIDByName(trim($_REQUEST["name"]));
                if ($by_name != 0 && $by_name != $id) {
                    $error = "Sorry, that name is already in use.";
                }
            }
        }

        if ($error == "") {
            $data = $this->input->post();
            if (!array_key_exists("retail_price", $data)) {
                // Well, then clear it explicitly...
                $data["retail_price"] = 0;
            }
            $this->Portalmodel->classicUpdatePart($id, $data);

            // Specifically queue and process this part.
            $this->db->query("Insert into queued_parts (part_id) values (?)", array($id));

            // now, process, just a couple
            $this->load->model("admin_m");
            @$this->admin_m->processParts(1); // hopefully, that's you. This might generate output, so I actually am suppressing this.

            $_SESSION["product_edit_success"] = "Product updated successfully.";
        } else {
            $_SESSION["product_edit_error"] = $error;
        }
        redirect('adminproduct/product_edit/' . $id);
    }

    public function product_edit($id = NULL) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            $this->_mainData['new'] = TRUE;
        } else {
            $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
        }

        $this->_mainData["success"] = $_SESSION["product_edit_success"];
        $this->_mainData["error"] = $_SESSION["product_edit_error"];
        $_SESSION["product_edit_success"] = "";
        $_SESSION["product_edit_error"] = "";

        // You have to go get the part numbers, too....
        $this->_mainData['partnumbers'] = $this->Portalmodel->_getPartNumberCollection($id);

        $this->_mainData['part_id'] = $id;
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/edit_v', $this->_mainData);
    }

    public function product_category_brand_save($id = NULL) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $product = $this->admin_m->getAdminProduct($id);
        // Check that this exists and it really has an mx = 0
        if (is_array($product) && array_key_exists("mx", $product) && $product["mx"] == 0) {
            $manufacturer = trim(array_key_exists("manufacturer", $_REQUEST) ? $_REQUEST["manufacturer"] : "");
            if ($manufacturer == "") {
                $manufacturer = trim(array_key_exists("new_manufacturer", $_REQUEST) ? $_REQUEST["new_manufacturer"] : "");
            }

            if ($manufacturer == "") {
                $_SESSION["product_category_brand_error"] = "Please provide a brand (manufacturer).";
            } else {
                $manufacturer_id = $this->Portalmodel->getOrMakeManufacturer($manufacturer);
                $this->Portalmodel->assignPartManufacturer($id, $manufacturer_id);

                // Now, what about them categories?
                $existing_categories = array_map(function($x) {
                    return $x["long_name"];
                }, $this->Portalmodel->getPartCategories($id));

                $seen = array();

                $categories = preg_split("/;|\r\n|\n|\r/", array_key_exists("categories", $_REQUEST) ? $_REQUEST["categories"] : "");
                print_r($categories);
                foreach ($categories as $c) {
                    $c = trim($c);
                    $seen[] = strtolower($c);
                    $c = $this->Portalmodel->getOrCreateCategory($c);
                    $this->Portalmodel->addPartCategory($id, $c);
                }

                // now, you have to remove them...
                foreach ($existing_categories as $ln) {
                    if (!in_array(strtolower($ln), $seen)) {
                        $category_id = $this->Portalmodel->getCategoryByLongname($ln);
                        $this->Portalmodel->removePartCategory($id, $category_id);
                    }
                }
                $_SESSION["product_category_brand_success"] = "Category/brand updated successfully.";
            }

        } else {
            $_SESSION["product_category_brand_error"] = "Sorry, that cannot be edited.";
        }
        header("Location: " . base_url("adminproduct/product_category_brand/$id"));
    }

    public function product_category_brand($id = NULL) {
        $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);

        $query = $this->db->query("Select distinct name from manufacturer order by name");
        $this->_mainData["manufacturers"] = array("" => "-- Select Existing Manufacturer --");
        foreach ($query->result_array() as $row) {
            $this->_mainData["manufacturers"][ $row["name"] ] = $row["name"];
        }
        
        if (array_key_exists("product_category_brand_success", $_SESSION) && $_SESSION["product_category_brand_success"] != "") {
            $this->_mainData["success"] = $_SESSION["product_category_brand_success"];
            $_SESSION["product_category_brand_success"] = "";
        }

        if (array_key_exists("product_category_brand_error", $_SESSION) && $_SESSION["product_category_brand_error"] != "") {
            $this->_mainData["error"] = $_SESSION["product_category_brand_error"];
            $_SESSION["product_category_brand_error"] = "";
        }

        $this->_mainData['product_categories'] = $this->Portalmodel->getPartCategories($id);
        $this->_mainData['product_brand'] = $this->Portalmodel->getPartBrand($id);
        $query = $this->db->query("Select category_id, long_name from category order by long_name");
        $this->_mainData['existingCategories'] = $query->result_array();

        $this->_mainData["part_id"] = $id;
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/cat_brand_v', $this->_mainData);
    }

    public function product_images($id = NULL) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
        $this->_mainData['part_id'] = $id;
        $this->_mainData['images'] = $this->Portalmodel->getPartImages($id);

        $this->setNav('admin/nav_v', 2);
        if ($this->_mainData['product']['mx'] > 0) {
            $this->renderMasterPage('admin/master_v', 'admin/product/images_v', $this->_mainData);
        } else {
            $this->renderMasterPage('admin/master_v', 'admin/product/images_edit_v', $this->_mainData);
        }
    }

    public function product_image_remove($part_id, $partimage_id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $partimage = $this->Portalmodel->getPartImage($partimage_id);
        $product = $this->admin_m->getAdminProduct($part_id);

        if (!array_key_exists("part_id", $product) && $product["mx"] != 0) {
            print json_encode(array("success" => 0, "error_message" => "Image not found."));
        } elseif (array_key_exists("part_id", $partimage) && $part_id == $partimage["part_id"]) {
            $this->Portalmodel->removeImage($partimage_id);
            print json_encode(array("success" => 1, "success_message" => "Image removed successfully."));
        } else {
            print json_encode(array("success" => 0, "error_message" => "Image not found."));
        }

    }

    public function product_image_add($part_id) {
        $result = array(
            "success" => 0,
            "success_message" => "",
            "error_message" => "Unknown error."
        );

        if (!array_key_exists("file", $_FILES)) {
            $result["error_message"] = "Sorry, no file received.";
        } elseif ($_FILES["file"]['size'] == 0) {
            $error_message = "Sorry, empty file received.";
        } else {
            // add this bulk image...
            $partimage_id = $this->Portalmodel->addImage($part_id, $_FILES["file"]);
            $result["success"] = 1;
            $result["success_message"] = "Image added successfully.";
            $result["partimage"] = $this->Portalmodel->getPartImage($partimage_id);
        }

        print json_encode($result);
    }

    public function product_video($id = NULL) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);

        $this->_mainData['product_video'] = $this->admin_m->getProductVideo($id);
        $this->_mainData['id'] = $id;
        $this->_mainData['part_id'] = $id;
        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('video_url') as $k => $v) {
                if ($v != '') {
                    $url = $v;
                    // JLB 03-05-17
                    // I have no idea why this was done. That looks like it wants a URL string. I have no idea what
                    // print "URL : $url";
                    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
                    // print_r($my_array_of_vars);
                    $my_array_of_vars['v'];
                    // $arr[] = array('video_url' => $my_array_of_vars['v'], 'ordering' => $this->input->post('ordering')[$k], 'part_id' => $this->input->post('part_id'), 'title' => $this->input->post('title')[$k]);
                    $arr[] = array('video_url' => !is_null($my_array_of_vars['v']) ? $my_array_of_vars['v'] : "", 'ordering' => $this->input->post('ordering')[$k], 'part_id' => $this->input->post('part_id'), 'title' => $this->input->post('title')[$k]);
                }
            }
            $this->admin_m->updateProductVideos($this->input->post('part_id'), $arr);
            redirect('adminproduct/product_video/' . $this->input->post('part_id'));
        }
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/video_v', $this->_mainData);
    }

    public function product_sizechart( $id = NULL ) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);

        $this->_mainData['product_sizechart'] = $this->admin_m->getProductSizeChart($id);
        $this->_mainData['id'] = $id;
        $this->_mainData['part_id'] = $id;
        if ($this->input->post()) {
            $arr = array('title' => $this->input->post('title'), 'part_id' => $id, 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
            $this->admin_m->updateProductSizeChart($arr);
            redirect('adminproduct/product_sizechart/' . $id);
        }
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/sizechart_v', $this->_mainData);
    }

    public function product_meta($id = NULL) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            $this->_mainData['new'] = TRUE;
        } else {
            $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
        }
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/meta_v', $this->_mainData);
    }

    public function product_description($id = NULL) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if (is_null($id)) {
            header("Location: " . site_url("adminproduct/product_add"));
        } else {
            $this->_mainData["part_id"] = $id;
            $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
        }

        if (array_key_exists("product_description_save_success", $_SESSION)) {
            $this->_mainData["success"] = $_SESSION["product_description_save_success"];
            $_SESSION["product_description_save_success"] = "";
        }
        if (array_key_exists("product_description_save_error", $_SESSION)) {
            $this->_mainData["error"] = $_SESSION["product_description_save_error"];
            $_SESSION["product_description_save_error"] = "";
        }

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/desc_v', $this->_mainData);
    }

    public function product_description_save($id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $product = $this->admin_m->getAdminProduct($id);
        // Check that this exists and it really has an mx = 0
        if (is_array($product) && array_key_exists("mx", $product) && $product["mx"] == 0) {
            // Update it...
            $this->Portalmodel->updatePart($id, "description", array_key_exists("description", $_REQUEST) ? $_REQUEST["description"] : "");
            // set success
            $_SESSION["product_description_save_success"] = "Description updated successfully.";
        } else {
            $_SESSION["product_description_save_error"] = "Sorry, that cannot be edited.";
        }
        header("Location: " . base_url("adminproduct/product_description/$id"));
    }

    public function product_shipping($id = NULL) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            $this->_mainData['new'] = TRUE;
        } else {
            $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);
        }
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/ship_v', $this->_mainData);
    }

    public function process_edit_product() {
        if ($this->validateEditProduct() !== FALSE) { // Display Form
            $this->load->model('products_m');
            $this->products_m->updateProducts($this->input->post());
        }
        $this->_mainData['productFormTable'] = $this->generateAdPdtTable();
    }

    public function load_new_product() {
        $this->_mainData['categories'] = $this->admin_m->getCategories();
        $tableView = $this->load->view('modals/new_product_v', $this->_mainData, TRUE);
        echo $tableView;
    }

    public function process_new_product() {
        $data['error'] = FALSE;
        if ($this->validateProduct() !== FALSE) {
            $this->load->model('products_m');
            $success = $this->products_m->createProduct($this->input->post());
            if ($success) {
                $data['success_message'] = "You have successfully created your product!";
            } else
                $data['success_message'] = "There has been an issue.  Please refresh your page and try again.";
        }
        else {
            $data['error'] = TRUE;
            $data['error_message'] = validation_errors();
        }
        echo json_encode($data);
        exit();
    }

    public function search_product() {
        if ($this->validateSearch() === TRUE) {
            $this->load->model('parts_m');
            $products = $this->parts_m->getSearchResults($this->input->post(), NULL, NULL);
        }
        echo json_encode($products);
    }

    /*
     * These came from portal/revision.php
     */

    /*
     * CRUD questions
     *
     */
    public function getQuestions($revisionset_id, $part_id) {
        $part = $this->admin_m->getAdminProduct($part_id);
        if ($part["mx"] == 1) {
            $this->Statusmodel->setError("Sorry, that is not an editable part.");
        } else {
            $this->Statusmodel->setSuccess("Data retrieved.");
            $this->Statusmodel->setData("data", $this->Portalmodel->fetchQuestions($part_id));
        }
        $this->Statusmodel->outputStatus();
    }

    public function deleteQuestion($revisionset_id, $part_id, $partquestion_id) {
        $part = $this->admin_m->getAdminProduct($part_id);

        if ($part["mx"] == 1) {
            $this->Statusmodel->setError("Sorry, that is not an editable part.");
        } else {
            $question = $this->Portalmodel->getPartQuestion($partquestion_id);
            if ($question["part_id"] != $part_id) {
                $this->Statusmodel->setError("Sorry, that question is not associated with that part.");
            } else {
                $this->Portalmodel->removePartQuestion($partquestion_id);
                $this->Statusmodel->setSuccess("Question removed.");
                $this->Statusmodel->setData("data", $this->Portalmodel->fetchPartQuestions($part_id));
            }
        }
        $this->Statusmodel->outputStatus();
    }

    public function addQuestion($revisionset_id) {
        $part_id = $revisionset_id;
        $part = $this->admin_m->getAdminProduct($part_id);
        if ($part["mx"] == 1) {
            $this->Statusmodel->setError("Sorry, that is not an editable part.");
        } else {
            $question = trim($this->input->post("question"));

            // look it up...
            $matches = $this->Portalmodel->matchByAttributes("partquestion", array("question" => $question, "part_id" => $part_id));

            if (count($matches) > 0) {
                // this already exists.
                $this->Statusmodel->setError("Sorry, that question already exists for this part.");
            } else {
                // let's insert this fucker.
                $this->Portalmodel->insert("partquestion", "partquestion_id", array("part_id" => $part_id, "question" => $question));
                $this->Statusmodel->setSuccess("Question added.");
                $this->Statusmodel->setData("data", $this->Portalmodel->fetchQuestions($part_id));
            }
        }
        $this->Statusmodel->outputStatus();
    }

    public function updateQuestion($revisionset_id, $part_id, $partquestion_id) {
        $part = $this->admin_m->getAdminProduct($part_id);
        if ($part["mx"] == 1) {
            $this->Statusmodel->setError("Sorry, that is not an editable part.");
        } else {
            $question = $this->Portalmodel->getPartQuestion($partquestion_id);
            if ($question["part_id"] != $part_id) {
                $this->Statusmodel->setError("Sorry, that question is not associated with that part.");
            } else {
                $new_question = trim($this->input->post("question"));

                // look it up...
                $matches = $this->Portalmodel->matchByAttributes("partquestion", array("question" => $new_question, "part_id" => $part_id));

                if ((count($matches) > 0) && ($matches[0]["partquestion_id"] != $partquestion_id)) {
                    // this already exists.
                    $this->Statusmodel->setError("Sorry, that question already exists for this part.");
                } else {
                    // let's update this fucker.
                    $this->Portalmodel->update("partquestion", "partquestion_id", $partquestion_id, array("question" => $new_question));
                    $this->Statusmodel->setSuccess("Question updated.");
                    $this->Statusmodel->setData("data", $this->Portalmodel->fetchQuestions($part_id));
                }
            }
        }
        $this->Statusmodel->outputStatus();

    }

    public function changeProductQuestion($part_id, $partquestion_id) {
        $part = $this->admin_m->getAdminProduct($part_id);
        if ($part["mx"] == 1) {
            $this->Statusmodel->setError("Sorry, that is not an editable part.");
        } else {
            $question = $this->Portalmodel->getPartQuestion($partquestion_id);
            if ($question["part_id"] != $part_id) {
                $this->Statusmodel->setError("Sorry, that question is not associated with that part.");
            } else {
                $this->db->query("Update partquestion set productquestion = ? where partquestion_id = ?", array(intVal($_REQUEST["productquestion"]), $partquestion_id));
                $this->Statusmodel->setSuccess("Question updated.");
                $this->Statusmodel->setData("data", $this->Portalmodel->fetchQuestions($part_id));
            }
        }
        $this->Statusmodel->outputStatus();

    }


    public function deletePartVariation($part_id, $partquestion_id, $partvariation_id) {
        $part = $this->admin_m->getAdminProduct($part_id);
        if ($part["mx"] == 1) {
            $this->Statusmodel->setError("Sorry, that is not an editable part.");
        } else {
            // The change here is that we're going to remove the entry from partpartnumber, and, only if it's not linked to anything else will we remove it entirely.
            $match = $this->Portalmodel->genericFetch("partvariation", "partvariation_id", $partvariation_id);

            if (array_key_exists("partnumber_id", $match)) {
                // Go get that part question...
                $partquestion = $this->Portalmodel->genericFetch("partquestion", "partquestion_id", $partquestion_id);

                if (array_key_exists("part_id", $partquestion) && $partquestion["part_id"] == $part_id) {
                    // OK, we need to go get that....
                    $this->Portalmodel->removePartQuestionNumber($part_id, $partquestion_id, $match["partnumber_id"]);
                    $this->Statusmodel->setSuccess("Part variation removed.");

                } else {
                    $this->Statusmodel->setError("Sorry, part question not found.");
                }

            } else {
                $this->Statusmodel->setError("Sorry, part number not found.");
            }
        }
        $this->Statusmodel->outputStatus();
    }

    /*
     * And CRUD answers...
     */

    public function inventory($id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $this->_mainData["part_id"] = $id;
        $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/product/inventory', $this->_mainData);
    }

    public function personalization($id) {
        if(!$this->checkValidAccess('products') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $this->_mainData["part_id"] = $id;
        $this->_mainData['product'] = $this->admin_m->getAdminProduct($id);

        if ($this->_mainData['product']['mx'] == 0){
            $this->Portalmodel->cleanPart($id);
            // Now, start stuffing this thing
            $getDataStructure = $this->Portalmodel->getDataStructure($id);
            $this->_mainData["KnownModelCollection"] = $getDataStructure["KnownModelCollection"];
            $this->_mainData["PartQuestionCollection"] = $getDataStructure["PartQuestionCollection"];
            $this->_mainData["PartQuestionAnswerCollection"] = $getDataStructure["PartQuestionAnswerCollection"];
            $this->_mainData["PartQuestionAnswerPartVariationCollection"] = $getDataStructure["PartQuestionAnswerPartVariationCollection"];
            $this->_mainData["PartVariationCollection"] = $getDataStructure["PartVariationCollection"];
            $this->_mainData["PartQuestionAnswerFitmentCollection"] = $getDataStructure["PartQuestionAnswerFitmentCollection"];
            $this->_mainData["DistributorCollection"] = $getDataStructure["DistributorCollection"];
            $this->_mainData["PartNumberCollection"] = $getDataStructure["PartNumberCollection"];
            $this->_mainData["PartPartNumberCollection"] = $getDataStructure["PartPartNumberCollection"];
            $this->_mainData["PartNumberPartQuestionCollection"] = $getDataStructure["PartNumberPartQuestionCollection"];
            $this->_mainData["PartNumberModelCollection"] = $getDataStructure["PartNumberModelCollection"];
            $this->_mainData["ManufacturerCollection"] = $getDataStructure["ManufacturerCollection"];

            $this->setNav('admin/nav_v', 2);
            $this->renderMasterPage('admin/master_v', 'admin/product/personalization_v', $this->_mainData);
        } else {
            print "Sorry, this is not reachable for non-dealer parts.";
            exit();
        }

    }


    protected function _answerCrudSub($revisionset_id, $part_id, $partquestion_id) {
        $part = $this->admin_m->getAdminProduct($part_id);
        if ($part["mx"] == 1) {
            $this->Statusmodel->setError("Sorry, that is not an editable part.");
        } else {
            // does this question belong to this part?
            $question = $this->Portalmodel->genericFetch("partquestion", "partquestion_id", $partquestion_id);
            if ($question['part_id'] != $part_id) {
                $this->Statusmodel->setError('Sorry, that question does not exist for that part.');
            } else {
                // part's good; question's good; revision's good - what more could you want?
                return;
            }
        }
        $this->Statusmodel->outputStatus();
        exit();
    }

    protected function _answerPackSub($revisionset_id) {
        $this->Statusmodel->setData("data", array(
            "PartQuestionAnswerCollection" => $this->Portalmodel->_getPartQuestionAnswerCollection($revisionset_id),
            "PartVariationCollection" => $this->Portalmodel->_getPartVariationCollection($revisionset_id),
            "PartQuestionAnswerPartVariationCollection" => $this->Portalmodel->_getPartQuestionAnswerPartVariationCollection($revisionset_id),
            "PartNumberPartQuestionCollection" => $this->Portalmodel->_getPartNumberPartQuestionCollection($revisionset_id),
            "PartNumberModelCollection" => $this->Portalmodel->_getPartNumberModelCollection($revisionset_id),
            "KnownModelCollection" => $this->Portalmodel->_getKnownModelCollection($revisionset_id)
        ));
    }

    public function getAnswers($revisionset_id, $part_id, $partquestion_id) {
        $this->_answerCrudSub($revisionset_id, $part_id, $partquestion_id);
        $this->Statusmodel->setSuccess("Data retrieved.");
        $this->Statusmodel->setData("data", $this->Portalmodel->fetchAnswers($partquestion_id));
        $this->Statusmodel->outputStatus();
    }

    public function addAnswer($revisionset_id, $part_id, $partquestion_id) {
        $this->_answerCrudSub($revisionset_id, $part_id, $partquestion_id);
        $answer = trim(array_key_exists("answer", $_REQUEST) ? $_REQUEST["answer"] : "");
        $distributor_id = array_key_exists("distributor_id", $_REQUEST) ? $_REQUEST["distributor_id"] : 0;
        $part_number = trim(array_key_exists("part_number", $_REQUEST) ? $_REQUEST["part_number"] : "");
        $fitments = array_key_exists("fitments", $_REQUEST) ? $_REQUEST["fitments"] : array();

//        if ($answer == "") {
//            $this->Statusmodel->setError('Sorry, no answer received. Please provide an answer.');
//        } else
        if ($distributor_id == 0) {
            $this->Statusmodel->setError('Sorry, please specify a distributor.');
        } else if ($part_number == "") {
            $this->Statusmodel->setError('Sorry, please specify a part number.');
        } else {
            $this->Portalmodel->addAnswer($revisionset_id, $partquestion_id, $answer, $distributor_id, $part_number, $fitments);
            $this->Statusmodel->setSuccess("Answer added.");
            $this->_answerPackSub($revisionset_id);
        }

        $this->Statusmodel->outputStatus();
    }

    public function deleteAnswer($revisionset_id, $part_id, $partquestion_id, $partquestionanswer_id) {
        $this->_answerCrudSub($revisionset_id, $part_id, $partquestion_id);
        $partquestionanswer = $this->Portalmodel->genericFetch("partquestionanswer", "partquestionanswer_id",  $partquestionanswer_id);
        if ($partquestionanswer["partquestion_id"] != $partquestion_id) {
            $this->Statusmodel->setError("Sorry that answer does not exist for that question.");
        } else {
            $this->Portalmodel->removePartQuestionAnswer($part_id, $partquestionanswer_id);
            $this->Statusmodel->setSuccess("Answer removed.");
        }
        $this->Statusmodel->outputStatus();
    }

    public function updateAnswer($revisionset_id, $part_id, $partquestion_id, $partquestionanswer_id) {
        $this->_answerCrudSub($revisionset_id, $part_id, $partquestion_id);
        $partquestionanswer = $this->Portalmodel->genericFetch("partquestionanswer", "partquestionanswer_id", $partquestionanswer_id);
        $answer = trim(array_key_exists("answer", $_REQUEST) ? $_REQUEST["answer"] : "");

        if ($partquestionanswer["partquestion_id"] != $partquestion_id) {
            $this->Statusmodel->setError("Sorry that answer does not exist for that question.");
        } else {
            $this->Portalmodel->update("partquestionanswer", "partquestionanswer_id", $partquestionanswer_id, array("answer" => $answer));
            $this->Statusmodel->setSuccess("Answer updated.");
        }
        $this->Statusmodel->outputStatus();
    }

    public function deleteAnswerVariation($revisionset_id, $part_id, $partquestion_id, $partquestionanswer_id, $partvariation_id) {
        $this->_answerCrudSub($revisionset_id, $part_id, $partquestion_id);
        $partquestionanswer = $this->Portalmodel->genericFetch("partquestionanswer", "partquestionanswer_id", $partquestionanswer_id);
        if ($partquestionanswer["partquestion_id"] != $partquestion_id) {
            $this->Statusmodel->setError("Sorry that answer does not exist for that question.");
        } else {

            $this->Portalmodel->removeVariation($partquestionanswer_id, $partvariation_id);
            $this->Statusmodel->setSuccess("Distributor part removed.");
            $this->_answerPackSub($revisionset_id);
        }
        $this->Statusmodel->outputStatus();
    }


    public function addfitment($revisionset_id) {
        $partnumber_id = array_key_exists("partnumber_id", $_REQUEST) ? $_REQUEST["partnumber_id"] : 0;
        $machinetype = array_key_exists("machinetype", $_REQUEST) ? trim($_REQUEST["machinetype"]) : "";
        $make = array_key_exists("make", $_REQUEST) ? trim($_REQUEST["make"]) : "";
        $model = array_key_exists("model", $_REQUEST) ? trim($_REQUEST["model"]) : "";
        $year = array_key_exists("year", $_REQUEST) ? trim($_REQUEST["year"]) : "";

        $this->isValidPartnumber($revisionset_id, $partnumber_id);

        if ($partnumber_id > 0 && $machinetype != "" && $make != "" && $model != "" && $year != "") {
            // OK, make the iggy!
            // Now, we are going to assume that machine type, make, model, year could be messy, just like in the upload process.
            $model_ids = array();

            $machinetypes = $this->Portalmodel->matchByAttributes("machinetype", array(
                "name" => $machinetype
            ));

            if (count($machinetypes) > 0) {
                $machinetype_id = $machinetypes[0]["machinetype_id"];
            } else {
                $machinetype_id = $this->Portalmodel->insert("machinetype", "machinetype_id", array(
                    "name" => $machinetype,
                    "label" => $machinetype
                ));
            }

            $makes = $this->Portalmodel->matchByAttributes("make", array(
                "machinetype_id" => $machinetype_id,
                "name" => $make
            ));

            if (count($makes) > 0) {
                $make_id = $makes[0]["make_id"];
            } else {
                $make_id = $this->Portalmodel->insert("make", array(
                    "name" => $make,
                    "label" => $make,
                    "machinetype_id" => $machinetype_id
                ));
            }

            $model_name = $model;
            if (preg_match("/\//", $model_name)) {
                $model_names = explode("/", $model_name);
            } else {
                $model_names = array($model_name);
            }

            $models = array();
            foreach ($model_names as $model_name) {
                $models = $this->Portalmodel->matchByAttributes("model", array(
                    "name" => $model_name,
                    "make_id" => $make_id
                ));

                if (count($models) > 0) {
                    $models[] = array("name" => $model_name, "model_id" => $models[0]["model_id"]);
                } else {
                    $models[] = array("name" => $model_name, "model_id" => $this->Portalmodel->insert("model", "model_id", array(
                        "name" => $model_name,
                        "label" => $model_name,
                        "make_id" => $make_id
                    )));
                }
            }

            // Machinetype will be a single name, and make will be a single name, but model could have slashes in it to separate these things, and year could be YY, YYYY, or a range.
            // Now, we need to get the years.
            $fitments = array();
            $matches = array();
            $years = $year;
            if (preg_match("/\s*(\d+)\s*-\s*(\d+)\s*/", $years, $matches)) {
                // this is a span...
                $start_year = intVal($matches[1]);
                $end_year = intVal($matches[2]);
            } else {
                $start_year = $end_year = intVal($years);
            }

            $current_year = intVal(date("Y"));
            $yy = $current_year % 100;
            $century_addend = $current_year - $yy;

            // fix it, if required...
            if ($start_year < 100) {
                if ($start_year < $yy + 1) {
                    $start_year += $century_addend;
                } else {
                    $start_year += ($century_addend - 100);
                }
            }

            if ($end_year < 100) {
                if ($end_year < $yy + 1) {
                    $end_year += $century_addend;
                } else {
                    $end_year += ($century_addend - 100);
                }
            }

            // insert them...
            foreach ($models as $rec) {
                for ($i = $start_year; $i <= $end_year; $i++) {
                    $fitments[] = array(
                        "machinetype_id" => $machinetype_id,
                        "make_id" => $make_id,
                        "model_id" => $rec["model_id"],
                        "year" => $i
                    );
                }
            }

            // And, then, insert all of these...
            foreach ($fitments as $f) {
                $this->Portalmodel->insert("partnumbermodel", "partnumbermodel_id", array(
                    "partnumber_id" => $partnumber_id,
                    "model_id" => $f["model_id"],
                    "year" => $f["year"]
                ));
            }

            if (count($fitments) > 0) {
                $this->Portalmodel->update("partnumber", "partnumber_id", $partnumber_id, array("universalfit" => 0));
            } else {
                $this->Portalmodel->update("partnumber", "partnumber_id", $partnumber_id, array("universalfit" => 1));
            }

            $this->Statusmodel->setSuccess("Fitment rule added.");
        }


        $this->Statusmodel->setData("data", array(
            "PartNumberModelCollection" => $this->Portalmodel->_getPartNumberModelCollection($revisionset_id),
            "KnownModelCollection" => $this->Portalmodel->_getKnownModelCollection($revisionset_id)
        ));

        $this->Statusmodel->outputStatus();
    }

    protected function isValidPartnumber($part_id, $partnumber_id) {
        $matches = $this->Portalmodel->matchByAttributes("partpartnumber", array(
            "part_id" => $part_id,
            "partnumber_id" => $partnumber_id
        ));

        if (count($matches) == 0) {
            $this->Statusmodel->setError("Sorry, that is not an editable part number.");
            $this->Statusmodel->outputStatus();
            exit();
        }
    }

    public function removefitment($revisionset_id) {
        $part = $this->admin_m->getAdminProduct($revisionset_id);
        if ($part["mx"] == 1) {
            $this->Statusmodel->setError("Sorry, that is not an editable part.");
            $this->Statusmodel->outputStatus();
            exit();
        }

        $partnumber_id = array_key_exists("partnumber_id", $_REQUEST) ? $_REQUEST["partnumber_id"] : 0;
        $this->isValidPartnumber($revisionset_id, $partnumber_id);

        $machinetype = array_key_exists("machinetype", $_REQUEST) ? trim($_REQUEST["machinetype"]) : "";
        $make = array_key_exists("make", $_REQUEST) ? trim($_REQUEST["make"]) : "";
        $model = array_key_exists("model", $_REQUEST) ? trim($_REQUEST["model"]) : "";
        $year = array_key_exists("year", $_REQUEST) ? trim($_REQUEST["year"]) : "";

        if ($partnumber_id > 0 && $machinetype != "" && $make != "" && $model != "" && $year != "") {

            $machinetypes = $this->Portalmodel->matchByAttributes("machinetype", array(
                "name" => $machinetype
            ));

            if (count($machinetypes) > 0) {
                $machinetype_id = $machinetypes[0]["machinetype_id"];

                $makes = $this->Portalmodel->matchByAttributes("make", array(
                    "machinetype_id" => $machinetype_id,
                    "name" => $make
                ));

                if (count($makes) > 0) {
                    $make_id = $makes[0]["make_id"];

                    $models = $this->Portalmodel->matchByAttributes("model", array(
                        "name" => $model,
                        "make_id" => $make_id
                    ));

                    if (count($models) > 0) {
                        $model_id = $models[0]["model_id"];

                        // fetch them...
                        $matches = $this->Portalmodel->matchByAttributes("partnumbermodel", array(
                            "partnumber_id" => $partnumber_id,
                            "model_id" => $model_id,
                            "year" => $year
                        ));

                        foreach ($matches as $match) {
                            // should be just one, but, whatever...
                            $this->Portalmodel->remove("partnumbermodel", "partnumbermodel_id", $match["partnumbermodel_id"]);
                        }

                        // are there any?
                        $matches = $this->Portalmodel->matchByAttributes("partnumbermodel", array(
                            "partnumber_id" => $partnumber_id
                        ));

                        if (count($matches) == 0) {
                            $this->Portalmodel->update("partnumber", "partnumber_id", $partnumber_id, array("universalfit" => 1));
                        } else {
                            $this->Portalmodel->update("partnumber", "partnumber_id", $partnumber_id, array("universalfit" => 0));
                        }
                        $this->Statusmodel->setSuccess("Fitment rule removed.");

                    } else {
                        $this->Statusmodel->setError("Unrecognized model: $model (make $make_id)");
                    }
                } else {
                    $this->Statusmodel->setError("Unrecognized make: $make (machine type $machinetype_id)");
                }

            } else {
                $this->Statusmodel->setError("Unrecognized machine type: $machinetype");
            }
        } else {
            $this->Statusmodel->setError("Please provide machine, make, model, and year");
        }

        $this->Statusmodel->setData("data", array(
            "PartNumberModelCollection" => $this->Portalmodel->_getPartNumberModelCollection($revisionset_id)
        ));

        $this->Statusmodel->outputStatus();
    }

}