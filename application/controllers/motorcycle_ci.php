<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 6/4/17
 * Time: 4:27 PM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(__DIR__ . "/welcome.php");

class Motorcycle_CI extends Welcome {

    protected $_stock_status_mode;
    public function _getStockStatusMode() {
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

    public function __construct()
    {
        parent::__construct();

        if (defined("MOTORCYCLE_SHOP_DISABLE") && MOTORCYCLE_SHOP_DISABLE) {
            // redirect it
            header("Location: /");
            exit();
        }
    }

    /*
     * JLB 06-04-17
     * Migrated from Welcome. Why do we dump EVERYTHING there???
     *
     */

    /*
     * I have no idea what this one does.
     */
    public function benz() {
        // $this->load->view('benz_views/header.php');
        // $this->load->view('benz_views/index.php');
        // $this->load->view('benz_views/footer.php');

        $this->load->model('motorcycle_m');
        $this->_mainData['featured'] = $this->motorcycle_m->getFeaturedMonster();
        $this->renderMasterPage('benz_views/header.php', 'benz_views/index.php', $this->_mainData);
    }

    public function benzProductFeatured($featured, $pre = 0) {
        if ($featured != 1) {
            $featured = 0;
        }
        $_SESSION["major_units_featured_only"] = $_SESSION["bikeControlFeatured"] = $featured;
        header("Location: /Motorcycle_List" . ($pre > 0 ? "?fltr=pre-owned" : ""));
    }

    public function benzProductSort($sort_number, $pre = 0) {
        if (!in_array($sort_number, array(1,2,3,4))) {
            $sort_number = 1;
        }
        $_SESSION["bikeControlSort"] = $sort_number;
        header("Location: /Motorcycle_List" . ($pre > 0 ? "?fltr=pre-owned" : ""));

    }

    public function benzProductShow($show_number, $pre = 0) {
        if (!in_array($show_number, array(5, 10, 25, 50))) {
            $show_number = 5;
        }
        $_SESSION["bikeControlShow"] = $show_number;
        header("Location: /Motorcycle_List" . ($pre > 0 ? "?fltr=pre-owned" : ""));
    }

    /*
     * This is the main Motorcycle_List page.
     */
    public function benzProduct() {
        if (!array_key_exists("bikeControlSort", $_SESSION)) {
            $_SESSION["bikeControlSort"] = 1;
        }
        if (!array_key_exists("bikeControlShow", $_SESSION)) {
            $_SESSION["bikeControlShow"] = 5;
        }

        // JLB 11-27-17
        // I think this is a problem with a default.
        if (!array_key_exists("fltr", $_REQUEST) && !array_key_exists("fltr", $_GET)) {
            if (!defined("MOTORCYCLE_SHOP_NEW") || MOTORCYCLE_SHOP_NEW) {
                $_REQUEST["fltr"] = "new";
                $_GET["fltr"] = "new";
            } else {
                $_GET["fltr"] = "pre-owned";
            }
        }


        $this->load->model('pages_m');
        $this->load->model('motorcycle_m');

        if (!array_key_exists("filterChange", $_REQUEST) && array_key_exists("motorcycle_filter", $_SESSION) && is_array($_SESSION["motorcycle_filter"]) && (!array_key_exists("fltr", $_REQUEST) || (array_key_exists("motorcycle_fltr", $_SESSION) && $_SESSION["motorcycle_fltr"] == $_REQUEST["fltr"]))) {
            $filter = $_SESSION["motorcycle_filter"];
        } else {
            $filter = $this->motorcycle_m->assembleFilterFromRequest();
            $_SESSION["motorcycle_filter"] = $filter;
            $_SESSION["motorcycle_fltr"] = $_REQUEST["fltr"];

        }

        $filter["status"] = 1;
        $this->_mainData['vehicles'] = $this->motorcycle_m->getMotorcycleVehicle($filter, $_SESSION["major_units_featured_only"]);
        $this->_mainData['brands'] = $this->motorcycle_m->getMotorcycleMake($filter, $_SESSION["major_units_featured_only"]);
        $this->_mainData['years'] = $this->motorcycle_m->getMotorcycleYear($filter, $_SESSION["major_units_featured_only"]);
        $this->_mainData['categories'] = $this->motorcycle_m->getMotorcycleCategory($filter, $_SESSION["major_units_featured_only"]);

        if (!array_key_exists("major_units_featured_only", $_SESSION)) {
            $_SESSION["major_units_featured_only"] = 0;
        }

        $this->_mainData['motorcycles'] = $this->motorcycle_m->getMotorcycles($filter, $_SESSION["bikeControlShow"], 0, $_SESSION["bikeControlSort"], $_SESSION["major_units_featured_only"]);

        $total = $this->motorcycle_m->getTotal($filter, $_SESSION["major_units_featured_only"]);
        $this->_mainData['pages'] = ceil($total / $_SESSION["bikeControlShow"]);
        $this->_mainData['fpages'] = $this->pages_m->getPages(1, 'footer');
        $recently = $_SESSION['recentlyMotorcycle'];
        $this->_mainData['recentlyMotorcycle'] = $this->motorcycle_m->getReccentlyMotorcycles($recently);
        $this->_mainData["filter"] = $filter;
        $this->renderMasterPage('benz_views/header.php', 'benz_views/product.php', $this->_mainData);
        // $this->load->view('benz_views/header.php');
        // $this->load->view('benz_views/product.php');
        // $this->load->view('benz_views/footer.php');
    }

    /*
     * This is for the drill down on a motorcycle.
     */
    public function benzDetails($title = null, $stock_code = null) {
        $this->load->model('pages_m');
        $this->load->model('motorcycle_m');

        // JLB 08-23-17
        // This used to just use the title. Let's try the stock code first.
        $id = $this->motorcycle_m->getMotorcycleIdBySKU($stock_code);

        if ($id == null) {
            // This used to be the only thing...
            $title1 = str_replace('_', ' ', urldecode($title));
            $id = $this->motorcycle_m->getMotorcycleIdByTitle($title1);
        }

        // echo urldecode($title);
        // echo $id.'<br>';
        // echo $title;exit;
        if ($id == null) {
            redirect('welcome/benzProduct');
        }

        // $this->load->view('benz_views/header.php');
        // $this->load->view('benz_views/product-details.php');
        // $this->load->view('benz_views/footer.php');

        $this->_mainData['fpages'] = $this->pages_m->getPages(1, 'footer');

        $recently = $_SESSION['recentlyMotorcycle'];
        $this->_mainData['recentlyMotorcycle'] = $this->motorcycle_m->getReccentlyMotorcycles($recently);

        $this->_mainData['motorcycle'] = $this->motorcycle_m->getMotorcycle($id);
        $this->setMasterPageVars('title', @$this->_mainData['motorcycle']['title']);
        if (array_key_exists("recentlyMotorcycle", $_SESSION)) {
            $_SESSION["recentlyMotorcycle"] = array_values($_SESSION["recentlyMotorcycle"]);            
        } else {
            $_SESSION["recentlyMotorcycle"] = array();
        }
        $_SESSION['recentlyMotorcycle'][] = $id;


        if (@$this->_mainData['motorcycle']['images'][0]['image_name']) {
            //$metaTag = '<meta property="og:image" content="'.$this->_mainData['motorcycle']['images'][0]['image_name'].'"/>';
            $metaTag = '<meta property="og:image" content="' . jsite_url('/media/') . $this->_mainData['motorcycle']['images'][0]['image_name'] . '"/>';
            $this->setMasterPageVars('metatag', $metaTag);
        }

        // echo "<pre>";
        // print_r($this->_mainData);exit;
        // echo "</pre>";
        $this->renderMasterPage('benz_views/header.php', 'benz_views/product-details.php', $this->_mainData);
    }

    /*
     * This is for the AJAX controller. It fetches things.
     */
    public function filterMotorcycle() {
        if (!array_key_exists("bikeControlSort", $_SESSION)) {
            $_SESSION["bikeControlSort"] = 1;
        }
        if (!array_key_exists("bikeControlShow", $_SESSION)) {
            $_SESSION["bikeControlShow"] = 5;
        }

        $this->load->model('motorcycle_m');
        $curPage = intVal($this->input->post("page") ? $this->input->post("page") : 0);
        $offset = ($curPage * $_SESSION["bikeControlShow"]);

        $filter = $this->motorcycle_m->assembleFilterFromRequest(true);

        unset($filter['page']);
        // JLB 06-04-17
        // Why was there a separate one for getFilterMotorcycles?? As far as I can tell, it was to separate off the limit vs. offset.
        $motorcycles['motorcycles'] = $this->motorcycle_m->getMotorcycles($filter, $_SESSION["bikeControlShow"], $offset, $_SESSION["bikeControlSort"]);


        $total = $this->motorcycle_m->getTotal($filter);
        $motorcycles['pages'] = ceil($total / $_SESSION["bikeControlShow"]);
        $motorcycles['page'] = $curPage + 1;

        $filteredProducts = $this->load->view('benz_views/filter-product.php', $motorcycles, true);
        echo $filteredProducts;
    }


}