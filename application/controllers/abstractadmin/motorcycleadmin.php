<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:18 AM
 *
 * This is the start of my chain of abstraction. Basically, the admin controller was WAY TOO BIG and CI2 does not do delegated controllers.
 *
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(__DIR__ . "/firstadmin.php");

abstract class Motorcycleadmin extends Firstadmin
{

    protected function validateMotorcycleDesc() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('descr', 'Description', 'required|max_length[5000]|xss_clean');
        return $this->form_validation->run();
    }

    protected function validateMotorcycle($suppress = false, $require_title = false) {
        $this->load->library('form_validation');
        if (!$suppress) {
            $this->form_validation->set_rules('vehicle_type', 'Vehicle Type', 'required');
            $this->form_validation->set_rules('make', 'Make', 'required');
            $this->form_validation->set_rules('year', 'Year', 'required');
            $this->form_validation->set_rules('model', 'Model', 'required');
        }
        if ($require_title) {
            $this->form_validation->set_rules('title', 'Title', 'required');
        }
        $this->form_validation->set_rules('category', 'Category', 'required');
        $this->form_validation->set_rules('condition', 'Condition', 'required');
        $this->form_validation->set_message('sku_not_in_use', 'That SKU is already in use.');
        $this->form_validation->set_rules('sku', 'Sku', 'required|sku_not_in_use');
        return $this->form_validation->run();
    }

    public function motorcycle_edit($id = NULL, $updated = null) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            $this->_mainData['new'] = TRUE;
        } else {
            $this->_mainData['product'] = $this->admin_m->getAdminMotorcycle($id);
            //$this->_mainData['categories'] = $this->admin_m->getMotorcycleCategory();
        }
        if ($updated != null) {
            $this->_mainData['success'] = TRUE;
        }
        $this->_mainData['vehicles'] = $this->admin_m->getMotorcycleVehicle();
        $this->_mainData['category'] = $this->admin_m->getMotorcycleCategory();
        $this->_mainData['id'] = $id;
        $this->setNav('admin/nav_v', 2);

        $js = '<script type="text/javascript" src="' . $this->_mainData['assets'] . '/js/ckeditor/ckeditor.js"></script>';
        $this->loadJS($js);
        $this->_mainData['edit_config'] = $this->_mainData['assets'] . '/js/htmleditor.js';

        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/edit_v', $this->_mainData);
    }

    public function update_motorcycle($id) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->helper('async');


        // we need to know if this already has an id...
        $motorcycle = $this->admin_m->getAdminMotorcycle($id);

        if ($this->validateMotorcycle($id > 0 && $motorcycle["crs_trim_id"] > 0, $id > 0) === TRUE) {
            // we need to assemble the title, if appropriate, for $id == 0
            $post = $this->input->post();
            $was_new = false;

            if (is_null($id) || $id == 0) {
                $was_new = true;
                // we need to assemble the title...
                $post["title"] = $post["year"] . " " . $post["make"] . " " . $post["model"] . (array_key_exists("color", $post) ? " " . $post["color"] : "");
            }

            $id = $this->admin_m->updateMotorcycle($id, $post);

            if ($was_new && array_key_exists("crs_trim_id", $_REQUEST) && $_REQUEST["crs_trim_id"] != "") {

                // Do we need to get the thumbnail?
                $this->load->model("CRS_m");
                $trim = $this->CRS_m->getTrim($_REQUEST["crs_trim_id"]);
                if (count($trim) > 0) {
                    $trim = $trim[0];
                    if ($trim["trim_photo"] != "") {
                        $this->db->query("Insert into motorcycleimage (motorcycle_id, image_name, date_added, description, priority_number, external, version_number, source) values (?, ?, now(), ?, 1, 1, ?, 'PST')", array($id, $trim["trim_photo"], 'Trim Photo: ' . $trim['display_name'], $trim["version_number"]));
                    }

                }


                $this->load->model("CRSCron_m");
                $this->CRSCron_m->refreshCRSData($id);
            }

            redirect('admin/motorcycle_edit/' . $id . '/updated');
        } else {
            $this->motorcycle_edit($id);
        }

//        curl_request_async();
    }

    public function motorcycle_description($id = NULL) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if (is_null($id))
            redirect('admin/motorcycle_edit');

        //validateMotorcycleDesc
        if ($this->input->post()) {
            //if ($this->validateMotorcycleDesc() === TRUE) {
            $this->admin_m->updateMotorcycleDesc($id, $this->input->post());
            $this->_mainData['success'] = TRUE;
        }

        if (is_null($id)) {
            $this->_mainData['new'] = TRUE;
        } else {
            $this->_mainData['product'] = $this->admin_m->getAdminMotorcycle($id);
        }
        $this->_mainData['id'] = $id;
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/desc_v', $this->_mainData);
    }

    public function motorcycle_images($id = NULL, $updated = null) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if ($updated != null) {
            $this->_mainData['success'] = TRUE;
        }

        if (is_null($id))
            redirect('admin/motorcycle_edit');


        if ($this->input->post()) {
            if (isset($_POST['update'])) {
                $arr = array();
                $mid = null;
                foreach ($_POST['description'] as $k => $description) {
                    $arr['description'] = $description;
                    $mid = $k;
                }
                $this->admin_m->updateMotorcycleImageDescription($mid, $arr);
            } elseif (isset($_POST['orderSubmit'])) {
                $arr = explode(",", $this->input->post('order'));
                foreach ($arr as $k => $v) {
                    $rr[] = explode("=", $v);
                }
                foreach ($rr as $k => $v) {
                    $img = $v[0];
                    $ord = $v[1];
                    $this->admin_m->updateImageOrder($img, $ord);
                }
                // echo "<pre>";
                // print_r($rr);
                // echo "</pre>";
                // exit;
            } else {
                $res['img'] = $this->admin_m->getMotorcycleImage($id);
                $ord = end($res['img']);
                $prt = $ord['priority_number'];
                // echo "<pre>";
                // print_r($ord['priority_number']);
                // echo "</pre>";exit;
                foreach ($_FILES['file']['name'] as $key => $val) {
                    if ($prt == "") {
                        $prt = 0;
                    } else {
                        $prt = $prt + 1;
                    }
                    $arr = array();
                    $img = time() . '_' . str_replace(' ', '_', $val);
                    $dir = STORE_DIRECTORY . '/html/media/' . $img;
                    move_uploaded_file($_FILES["file"]["tmp_name"][$key], $dir);
                    $arr['description'] = $_POST['description'];
                    $arr['image_name'] = $img;
                    $arr['motorcycle_id'] = $id;
                    $arr['priority_number'] = $prt;
                    $this->admin_m->updateMotorcycleImage($id, $arr);
                    $prt++;
                }
            }
            redirect('admin/motorcycle_images/' . $id . '/updated');
        }

        if (is_null($id)) {
            $this->_mainData['new'] = TRUE;
        } else {
            $this->_mainData['image'] = $this->admin_m->getMotorcycleImage($id);
        }
        $this->_mainData['id'] = $id;
        $this->_mainData['product'] = $this->admin_m->getAdminMotorcycle($id);
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/images_v', $this->_mainData);
    }

    public function motorcycle_specs($id = NULL) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/motorcycle_edit');
        }

        $this->_mainData['id'] = $id;


        $this->_mainData['product'] = $this->admin_m->getAdminMotorcycle($id);
        $this->load->model("motorcycle_m");
        $this->_mainData['specs'] = $this->motorcycle_m->getMotorcycleSpecs($id);
        $this->_mainData['specgroups'] = $this->motorcycle_m->getMotorcyclSpecGroups($id);
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/specs_v', $this->_mainData);
    }

    public function ajax_motorcycle_vin_decoder() {
        $vin = trim(array_key_exists("vin", $_REQUEST) ? $_REQUEST["vin"] : "");
        if ($vin != "") {
            $this->load->model("CRS_m");
            $this->_printAjaxSuccess($this->CRS_m->queryVin($vin));
        } else {
            $this->_printAjaxError("No VIN received.");
        }
    }

    // OK, you can remove a spec
    public function ajax_motorcycle_spec_remove($motorcycle_id, $motorcyclespec_id) {
        $this->db->query("Update motorcyclespec set hidden = 1 where motorcycle_id = ? and motorcyclespec_id = ?", array($motorcycle_id, $motorcyclespec_id));
        $this->_printAjaxSuccess();
    }

    // You can add a new group
    public function ajax_motorcycle_specgroup_add($motorcycle_id) {
        $this->db->query("Insert into motorcyclespecgroup (motorcycle_id, name, source) values (?, '', 'Admin')", array($motorcycle_id));
        $motorcyclespecgroup_id = $this->db->insert_id();

        $cnt_query = $this->db->query("Select max(ordinal) as cnt from motorcyclespecgroup where motorcycle_id = ? and motorcyclespecgroup_id < ?", array($motorcycle_id, $motorcyclespecgroup_id));
        $count = $cnt_query->result_array();
        $count = $count[0]["cnt"];
        if (is_null($count)) {
            $count = 0;
        }
        $count++;

        $this->db->query("Update motorcyclespecgroup set ordinal = ? where motorcyclespecgroup_id = ? limit 1", array($count, $motorcyclespecgroup_id));

        $query = $this->db->query("Select * from motorcyclespecgroup where motorcyclespecgroup_id = ?", array($motorcyclespecgroup_id));
        $results = $query->result_array();
        $this->_printAjaxSuccess(array(
            "model" => $results[0]
        ));
    }

    // you can add a new spec to the group
    public function ajax_motorcycle_specgroup_addspec($motorcycle_id, $motorcyclespecgroup_id) {
        $this->db->query("Insert into motorcyclespec (motorcycle_id, motorcyclespecgroup_id, feature_name, attribute_name, value, final_value, source) values (?, ?, '', '', '', '', 'Admin')", array($motorcycle_id, $motorcyclespecgroup_id));
        $motorcyclespec_id = $this->db->insert_id();

        $cnt_query = $this->db->query("Select max(ordinal) as cnt from motorcyclespec where motorcycle_id = ? and motorcyclespecgroup_id = ? and motorcyclespec_id < ?", array($motorcycle_id, $motorcyclespecgroup_id, $motorcyclespec_id));
        $count = $cnt_query->result_array();
        $count = $count[0]["cnt"];
        if (is_null($count)) {
            $count = 0;
        }
        $count++;

        $this->db->query("Update motorcyclespec set ordinal = ? where motorcyclespec_id = ? limit 1", array($count, $motorcyclespec_id));

        $query = $this->db->query("Select * from motorcyclespec where motorcyclespec_id = ?", array($motorcyclespec_id));
        $results = $query->result_array();
        $this->_printAjaxSuccess(array(
            "model" => $results[0]
        ));
    }

    // you can edit/update the spec
    public function ajax_motorcycle_specgroup_update($motorcycle_id, $motorcyclespecgroup_id) {
        $name = trim(array_key_exists("name", $_REQUEST) ? $_REQUEST["name"] : "");
        $this->db->query("Update motorcyclespecgroup set name = ? where motorcyclespecgroup_id = ? and motorcycle_id = ? limit 1", array($name, $motorcyclespecgroup_id, $motorcycle_id));
        $this->_printAjaxSuccess(array(
            "name" => $name
        ));
    }
    public function ajax_motorcycle_spec_update($motorcycle_id, $motorcyclespecgroup_id, $motorcyclespec_id) {
        $final_value = trim(array_key_exists("final_value", $_REQUEST) ? $_REQUEST["final_value"] : "");
        $feature_name = trim(array_key_exists("feature_name", $_REQUEST) ? $_REQUEST["feature_name"] : "");
        $attribute_name = trim(array_key_exists("attribute_name", $_REQUEST) ? $_REQUEST["attribute_name"] : "");
        $this->db->query("Update motorcyclespec set final_value = ?, feature_name = ?, attribute_name = ? where motorcyclespec_id = ? and motorcyclespecgroup_id = ? and motorcycle_id = ? limit 1", array($final_value, $feature_name, $attribute_name, $motorcyclespec_id, $motorcyclespecgroup_id, $motorcycle_id));
        $this->_printAjaxSuccess(array(
            "final_value" => $final_value,
            "feature_name" => $feature_name,
            "attribute_name" => $attribute_name
        ));
    }

    // you can remove a spec group
    public function ajax_motorcycle_specgroup_remove($motorcycle_id, $motorcyclespecgroup_id) {
        $this->db->query("Update motorcyclespecgroup set hidden = 1 where motorcycle_id = ? and motorcyclespecgroup_id = ?", array($motorcycle_id, $motorcyclespecgroup_id));
        $this->_printAjaxSuccess();
    }

    // you can reorder specs within a group
    public function ajax_motorcycle_specs_reorder($motorcycle_id, $motorcyclespecgroup_id) {
        $new_order = array_key_exists("new_order", $_REQUEST) ? $_REQUEST["new_order"] : array();

        // now, apply it...
        for ($i = 0; $i < count($new_order); $i++) {
            $this->db->query("Update motorcyclespec set ordinal = ? where motorcycle_id = ? and motorcyclespecgroup_id = ? and motorcyclespec_id = ?", array($i, $motorcycle_id, $motorcyclespecgroup_id, $new_order[$i]));
        }
        $this->_printAjaxSuccess();
    }

    // you can reorder a spec group
    public function ajax_motorcycle_specgroups_reorder($motorcycle_id) {
        $new_order = array_key_exists("new_order", $_REQUEST) ? $_REQUEST["new_order"] : array();

        // now, apply it...
        for ($i = 0; $i < count($new_order); $i++) {
            $this->db->query("Update motorcyclespecgroup set ordinal = ? where motorcycle_id = ? and motorcyclespecgroup_id = ?", array($i, $motorcycle_id, $new_order[$i]));
        }
        $this->_printAjaxSuccess();
    }




    public function motorcycle_video($id = NULL, $updated = null) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id))
            redirect('admin/motorcycle_edit');

        $this->_mainData['product_video'] = $this->admin_m->getMotorcycleVideo($id);
        $this->_mainData['id'] = $id;
        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('video_url') as $k => $v) {
                if ($v != '') {
                    $url = $v;
                    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
                    //$my_array_of_vars['v'];
                    $arr[] = array('video_url' => $my_array_of_vars['v'], 'ordering' => $this->input->post('ordering')[$k], 'part_id' => $this->input->post('part_id'), 'title' => $this->input->post('title')[$k]);
                }
            }
            $this->admin_m->updateMotorcycleVideos($this->input->post('part_id'), $arr);
            redirect('admin/motorcycle_video/' . $this->input->post('part_id') . '/updated');
        }

        if ($updated != null) {
            $this->_mainData['success'] = TRUE;
        }


        $this->_mainData['product'] = $this->admin_m->getAdminMotorcycle($id);
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/video_v', $this->_mainData);
    }

    public function deleteMotorcycleImage($id = null, $motorcycle_id = null) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($id != null && $motorcycle_id != null) {
            $this->admin_m->deleteMotorcycleImage($id, $motorcycle_id);
        }
        redirect('admin/motorcycle_images/' . $motorcycle_id);
    }

    public function motorcycle_delete($prod_id = null) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($prod_id != null && is_numeric($prod_id)) {
            $this->admin_m->deleteMotorcycle($prod_id);
        }
        redirect('admin/mInventory');
    }

    public function motorcycle_outofstock_active() {
        $this->_sub_motorcycle_outofstock(1);
    }

    public function motorcycle_outofstock_inactive() {
        $this->_sub_motorcycle_outofstock(0);
    }

    protected function _sub_motorcycle_outofstock($value = 1) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        // OK, we have to switch them and save them...
        $this->db->query("Update contact set out_of_stock_active = ? where id = 1 limit 1", array($value));
        $this->db->query("Update motorcycle set `status` = ? where stock_status = 'Out Of Stock'", array($value));

        redirect('admin/mInventory');
    }

    protected $_stock_status_mode;
    protected function _getStockStatusMode() {
        if ($this->_stock_status_mode === 0 || $this->_stock_status_mode === 1) {
            return $this->stock_status_mode;
        }

        // need to get it..
        $query = $this->db->query("Select stock_status_mode from contact where id = 1");
        foreach ($query->result_array() as $row) {
            $this->_stock_status_mode = intVal($row["stock_status_mode"]);
        }

        return $this->_stock_status_mode;
    }

    protected $_out_of_stock_active;
    protected function _getOutOfStockActive() {
        if ($this->_out_of_stock_active === 0 || $this->_out_of_stock_active === 1) {
            return $this->out_of_stock_active;
        }

        // need to get it..
        $query = $this->db->query("Select out_of_stock_active from contact where id = 1");
        foreach ($query->result_array() as $row) {
            $this->_out_of_stock_active = intVal($row["out_of_stock_active"]);
        }

        return $this->_out_of_stock_active;
    }


    public function mInventory() {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->setNav('admin/nav_v', 2);
        $this->_mainData["stock_status_mode"] = $this->_getStockStatusMode();
        $this->_mainData["out_of_stock_active"] = $this->_getOutOfStockActive();
        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/list_v', $this->_mainData);
    }

    public function ajax_set_stock_status_mode($stock_status_mode) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->db->query("Update contact set stock_status_mode = ? where id = 1 limit 1", array($stock_status_mode));
    }

    public function minventory_ajax() {
        $columns = array(
            "motorcycle.sku",
            "motorcycle_category.name",
            "motorcycle_type.name",
            "motorcycle.title",
            "motorcycle.title",
            "motorcycle.featured",
            "motorcycle.status",
            "motorcycle.retail_price",
            "motorcycle.sale_price",
            "motorcycle.condition",
            "motorcycle.mileage",
            "motorcycle.source",
            "motorcycle.stock_status",
            "motorcycle.title"
        );

        $length = array_key_exists("length", $_REQUEST) ? $_REQUEST["length"] : 500;
        $start = array_key_exists("start", $_REQUEST) ? $_REQUEST["start"] : 0;

        $order_string = "order by motorcycle.title asc ";

        if (array_key_exists("order", $_REQUEST) && is_array($_REQUEST["order"]) && count($_REQUEST["order"]) > 0) {
            // OK, there's a separate order string...
            $order_string = "order by ";
            $orderings = $_REQUEST["order"];
            if (count($orderings) == 0) {
                $order_string .= " motorcycle.title asc";
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

        $this->load->model("Motorcycle_m");

        // How do we shove through the restrictor from the upper right?
        list($products, $total_count, $filtered_count) = $this->Motorcycle_m->enhancedGetMotorcycles($s = (array_key_exists("search", $_REQUEST) && array_key_exists("value", $_REQUEST["search"]) ? $_REQUEST["search"]["value"] : ""), $order_string, $length, $start);

        // Now, order them...
        $rows = array();
        foreach ($products as $p) {
            $rows[] = array(
                $p["sku"],
                $p["category_name"],
                $p["type_name"],
                "<img style='width: 60px; height: auto;' src='" . ( $p["image_name"] != "" ? ($p["external"] > 0 ? $p["image_name"] : ("/media/" . $p["image_name"])) : "data:image/jpeg;base64,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" ) . "' />",
                $p["title"],
                $p["featured"] > 0 ? "Yes" : "No",
                $p["status"] > 0 ? "Yes" : "No",
                $p["retail_price"],
                $p["sale_price"],
                $p["condition"] == 1 ? "New" : "Used",
                $p["mileage"],
                $p["source"],
                $p["stock_status"],
                "<span class='nowrap'><a href='#' class='edit-button' data-motorcycle-id='" . $p["id"] . "'><i class='fa fa-edit'></i>&nbsp;Edit</a></span><br/> " ./* edit */ /* delete */ /* active */ /* inactive */
                "<span class='nowrap'><a href='#' class='remove-button' data-motorcycle-id='" . $p["id"] . "'><i class='fa fa-remove'></i>&nbsp;Remove</a></span><br/> " .
                ($p["status"] > 0 ? "<span class='nowrap'><a href='#' class='inactive-button' data-motorcycle-id='" . $p["id"] . "'><i class='fa fa-play'></i>&nbsp;Active</a></span><br/> " : "<span class='nowrap'><a href='#' class='active-button' data-motorcycle-id='" . $p["id"] . "'><i class='fa fa-pause'></i>&nbsp;Inactive</a></span><br/> ")
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


    /*
     * We added some ajax actions.
     */
    public function motorcycle_ajax_ac_make() {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            $this->_printAjaxError("Sorry, you do not have access to this feature.");
        }

        // OK, we have to come up with suggestions
        $year = array_key_exists("year", $_REQUEST) ? $_REQUEST["year"] : 0;
        $machine_type = array_key_exists("machine_type", $_REQUEST) ? $_REQUEST["machine_type"] : "";
        $offroad = array_key_exists("offroad", $_REQUEST) ? $_REQUEST["offroad"] : null;

        if ($machine_type == "") {
            $this->_printAjaxError("Sorry, you must specify a machine type.");
        }

        $args = array(
            "machine_type" => $machine_type
        );

        if ($year > 0) {
            $args["year"] = $year;
        }

        if (!is_null($offroad)) {
            $args["offroad"] = $offroad;
        }

        $this->load->model("CRS_m");
        $this->_printAjaxSuccess($this->CRS_m->getMakes($args));
    }

    public function motorcycle_ajax_ac_model() {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            $this->_printAjaxError("Sorry, you do not have access to this feature.");
        }

        // OK, we have to come up with suggestions
        $year = array_key_exists("year", $_REQUEST) ? $_REQUEST["year"] : 0;
        $machine_type = array_key_exists("machine_type", $_REQUEST) ? $_REQUEST["machine_type"] : "";
        $make = array_key_exists("make", $_REQUEST) ? $_REQUEST["make"] : "";
        $offroad = array_key_exists("offroad", $_REQUEST) ? $_REQUEST["offroad"] : null;

        if ($machine_type == "") {
            $this->_printAjaxError("Sorry, you must specify a machine type.");
        }

        if ($make == "") {
            $this->_printAjaxError("Sorry, you must specify a make.");
        }

        $args = array(
            "machine_type" => $machine_type,
            "make" => $make
        );

        if ($year > 0) {
            $args["year"] = $year;
        }
        if (!is_null($offroad)) {
            $args["offroad"] = $offroad;
        }


        $this->load->model("CRS_m");
        $this->_printAjaxSuccess($this->CRS_m->getTrims($args));
    }

    public function motorcycle_ajax_active($id) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            $this->_printAjaxError("Sorry, you do not have access to this feature.");
        }

        $this->db->query("Update motorcycle set status = 1 where id = ?", array($id));
        $this->_printAjaxSuccess();
    }

    public function motorcycle_ajax_inactive($id) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            $this->_printAjaxError("Sorry, you do not have access to this feature.");
        }

        $this->db->query("Update motorcycle set status = 0 where id = ?", array($id));
        $this->_printAjaxSuccess();
    }

    public function motorcycle_ajax_remove($id) {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            $this->_printAjaxError("Sorry, you do not have access to this feature.");
        }

        $this->db->query("Delete from motorcycle where id = ?", array($id));
        $this->_printAjaxSuccess();
    }

    /*
     * These are for the Motorcycle Quotes
     */

    public function motorcycle_quotes() {
        if (!$this->checkValidAccess('unitinquiries') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->setNav('admin/nav_v', 5);

        // Let's get those quotes, all of them...
        $query = $this->db->query("Select * from motorcycle_enquiry");
        $this->_mainData["inquiries"] = $query->result_array();

        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/quotes_index', $this->_mainData);
    }

    public function motorcycle_quote_ajax_remove($id) {
        if (!$this->checkValidAccess('unitinquiries') && !@$_SESSION['userRecord']['admin']) {
            $this->_printAjaxError("Sorry, you do not have access to this feature.");
        }
        $this->db->query("Delete from motorcycle_enquiry where id = ?", array($id));
        $this->_printAjaxSuccess();
    }

    public function motorcycle_quote_view($id) {
        if (!$this->checkValidAccess('unitinquiries') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->setNav('admin/nav_v', 5);

        // getthe quote
        $match = false;
        $the_row = array();
        $query = $this->db->query("Select * from motorcycle_enquiry where id = ?", array($id));
        foreach ($query->result_array() as $row) {
            $match = true;
            $the_row = $row;
        }

        if (!$match) {
            // redirect it...
            header("Location: /admin/motorcycle_quotes");
        } else {
            // OK, we have to cram it down...
            $this->_mainData["quote"] = $the_row;
            $this->renderMasterPage('admin/master_v', 'admin/motorcycle/quotes_view', $this->_mainData);
        }
    }

    public function motorcycle_quote_mark_as_sent($id) {
        if (!$this->checkValidAccess('unitinquiries') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->db->query("Update motorcycle_enquiry set status = 'Sent', sent_time = now() where id = ?", array($id));
        header("Location: /admin/motorcycle_quote_view/" . $id);
    }


    public function motorcycle_quote_ajax() {
        if (!$this->checkValidAccess('unitinquiries') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $columns = array(
            "created",
            "status",
            "name",
            "email", "phone", "motorcycle"
        );

        if (!defined("DISABLE_TEST_DRIVE") || !DISABLE_TEST_DRIVE) {
            $columns[] = "date_of_ride";
        }
        $columns[] = "";


        $length = array_key_exists("length", $_REQUEST) ? $_REQUEST["length"] : 500;
        $start = array_key_exists("start", $_REQUEST) ? $_REQUEST["start"] : 0;

        $order_string = "order by created desc ";

        if (array_key_exists("order", $_REQUEST) && is_array($_REQUEST["order"]) && count($_REQUEST["order"]) > 0) {
            // OK, there's a separate order string...
            $order_string = "order by ";
            $orderings = $_REQUEST["order"];
            if (count($orderings) == 0) {
                $order_string .= " created desc";
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


        $this->load->helper("jonathan");

        $where = jonathan_generate_likes(array("status", "firstName", "lastName", "email", "phone", "motorcycle"), $s = (array_key_exists("search", $_REQUEST) && array_key_exists("value", $_REQUEST["search"]) ? $_REQUEST["search"]["value"] : ""), "WHERE");

        // get total count
        $query = $this->db->query("Select count(*) as cnt from motorcycle_enquiry");
        $total_count = 0;
        foreach ($query->result_array() as $row) {
            $total_count = $row['cnt'];
        }

        $query = $this->db->query("Select count(*) as cnt from motorcycle_enquiry $where");
        $filtered_count = 0;
        foreach ($query->result_array() as $row) {
            $filtered_count = $row['cnt'];
        }

        $query = $this->db->query("Select motorcycle_enquiry.*, concat(firstName, ' ', lastName) as name from motorcycle_enquiry $where $order_string limit $length offset $start  ");
        $rows = $query->result_array();

        $output_rows = array();
        foreach ($rows as $row) {
            $clean_row = array(
                date("m/d/Y g:i a T", strtotime($row['created'])),
                $row['status'],
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['motorcycle']
            );

            if (!defined("DISABLE_TEST_DRIVE") || !DISABLE_TEST_DRIVE) {
                $clean_row[] = $row['date_of_ride'];
            }

            $clean_row[] =

            // put some actions on there...
            "<span class='nowrap'><a href='#' class='view-button' data-motorcycle-id='" . $row["id"] . "'><i class='fa fa-search'></i>&nbsp;View</a></span><br/> " ./* edit */ /* delete */ /* active */ /* inactive */
            "<span class='nowrap'><a href='#' class='remove-button' data-motorcycle-id='" . $row["id"] . "'><i class='fa fa-remove'></i>&nbsp;Remove</a></span><br/> ";

            $output_rows[] = $clean_row;
        }

        print json_encode(array(
            "data" => $output_rows,
            "draw" => array_key_exists("draw", $_REQUEST) ? $_REQUEST["draw"] : 0,
            "recordsTotal" => $total_count,
            "recordsFiltered" => $filtered_count,
            "limit" => $length,
            "offset" => $start,
            "order_string" => $order_string,
            "search" => $s
        ));
    }


}