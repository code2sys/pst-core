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
        header("Location: /Major_Unit_List" . $this->preSwitch($pre));
    }

    protected function preSwitch($pre) {
        switch($pre) {
            case 2:
                return "?fltr=special";
                break;
            case 1:
                return "?fltr=pre-owned";
                break;
            default:
                return "";
        }
    }

    public function benzProductSort($sort_number, $pre = 0) {
        if (!in_array($sort_number, array(1,2,3,4))) {
            $sort_number = 0;
        }
        $_SESSION["bikeControlSort"] = $sort_number;

        $this->load->model('motorcycle_m');

        $unitUrl = $this->motorcycle_m->generateUnitUrlfromFilter($_SESSION["motorcycle_fltr"], $_SESSION["motorcycle_filter"]);
        
        echo "<pre>";
        echo $unitUrl;
        print_r($_SESSION["motorcycle_filter"]); exit;
        header("Location: /For_Sale/Major_Unit_List" . $this->preSwitch($pre));

    }

    public function benzProductShow($show_number, $pre = 0) {
        if (!in_array($show_number, array(5, 10, 25, 50))) {
            $show_number = ITEMS_ON_PAGE;
        }
        $_SESSION["bikeControlShow"] = $show_number;
        header("Location: /For_Sale/Major_Unit_List" . $this->preSwitch($pre));
    }

    /*
     * This is the main Major_Unit_List page.
     */
    public function featuredNewProducts() {
        $_SESSION["major_units_featured_only"] = $_SESSION["bikeControlFeatured"] = 1;
        $_REQUEST["fltr"] = "New_Inventory";
        $_GET["fltr"] = "New_Inventory";
        $this->benzProduct();
    }

    public function featuredSpecialProducts() {
        $_SESSION["major_units_featured_only"] = $_SESSION["bikeControlFeatured"] = 1;
        $_REQUEST["fltr"] = "special";
        $_GET["fltr"] = "special";
        $this->benzProduct();
    }

    public function featuredUsedProducts() {
        $_SESSION["major_units_featured_only"] = $_SESSION["bikeControlFeatured"] = 1;
        $_REQUEST["fltr"] = "pre-owned";
        $_GET["fltr"] = "pre-owned";
        $this->benzProduct();
    }

    /*
     * This is designed to request things to have them sent down to you.
     * fltr
     * major_unit_search_keywords
     * vehicles
     * brands
     * years
     * categories
     * featured_only
     *
     * The idea is to shove down
     *
     */
    public function ajaxFilterQuery() {
        $this->load->model("motorcycle_m");

        $search_keywords = array_key_exists("search_keywords", $_REQUEST) ? $_REQUEST["search_keywords"] : "";
        $major_units_featured_only = array_key_exists("featured_only", $_REQUEST) ? $_REQUEST["featured_only"] : 0;

        $filter_data = array();
        $this->motorcycle_m->sub_assembleFilterInput($filter_data, $_REQUEST);
        $filter = $this->motorcycle_m->sub_assembleFilterFromRequest($filter_data);

        // we need to assemble our own filter...

        $result = array(
            "search_keywords" => $search_keywords,
            "featured_only" => $major_units_featured_only,
            "filter" => $filter,
            "vehicles" => $this->motorcycle_m->sub_getMotorcycleVehicle($filter, $major_units_featured_only, $search_keywords),
            "brands" => $this->motorcycle_m->sub_getMotorcycleMake($filter, $major_units_featured_only, $search_keywords),
            "years" => $this->motorcycle_m->sub_getMotorcycleYear($filter, $major_units_featured_only, $search_keywords),
            "categories" => $this->motorcycle_m->sub_getMotorcycleCategory($filter, $major_units_featured_only, $search_keywords),
            "models" => $this->motorcycle_m->sub_getMotorcycleDistinctModels($filter, $major_units_featured_only, $search_keywords)
        );

        print json_encode($result);
    }


    public function benzProduct() {

        if (array_key_exists("search_action", $_REQUEST) && $_REQUEST["search_action"] == "Clear") {
            $_REQUEST["search_keywords"] = "";
        }

        $squash_filter = false;
        if (array_key_exists("brands", $_REQUEST)){

            $_SESSION["major_unit_search_keywords"] = "";

        }else if (!array_key_exists("search_keywords", $_REQUEST)) {
            if (!array_key_exists("major_unit_search_keywords", $_SESSION)) {
                $_SESSION["major_unit_search_keywords"] = "";
            }
        } else {
            $new_search = trim($_REQUEST["search_keywords"]);
            if (!array_key_exists("major_unit_search_keywords", $_SESSION) || $new_search != $_SESSION["major_unit_search_keywords"] ) {
                $_SESSION["motorcycle_filter"] = array();
                $squash_filter = true;
            }
            $_SESSION["major_unit_search_keywords"] = $new_search;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            header("Location: " . $actual_link);
            exit();
        }

        if (!array_key_exists("bikeControlSort", $_SESSION)) {
            $_SESSION["bikeControlSort"] = 0;
        }
        if (!array_key_exists("bikeControlShow", $_SESSION)) {
            $_SESSION["bikeControlShow"] = ITEMS_ON_PAGE;
        }

        // JLB 11-27-17
        // I think this is a problem with a default.
        if (!array_key_exists("fltr", $_REQUEST) && !array_key_exists("fltr", $_GET)) {
            if (!defined("MOTORCYCLE_SHOP_NEW") || MOTORCYCLE_SHOP_NEW) {
                $_REQUEST["fltr"] = "New_Inventory";
                $_GET["fltr"] = "New_Inventory";
            } else {
                $_GET["fltr"] = "pre-owned";
            }
        }


        $this->load->model('pages_m');
        $this->load->model('admin_m');
        $this->load->model('motorcycle_m');

        // if (!array_key_exists("filterChange", $_REQUEST) && array_key_exists("motorcycle_filter", $_SESSION) && is_array($_SESSION["motorcycle_filter"]) && (!array_key_exists("fltr", $_REQUEST) || (array_key_exists("motorcycle_fltr", $_SESSION) && $_SESSION["motorcycle_fltr"] == $_REQUEST["fltr"]))) {
        //     $filter = $_SESSION["motorcycle_filter"];
        // } else {
        $filter = $this->motorcycle_m->assembleFilterFromRequest();
        
        $_SESSION["motorcycle_filter"] = $filter;
        $_SESSION["motorcycle_fltr"] = $_REQUEST["fltr"];

        // }

        if ($squash_filter) {
            $filter = array();
            $_SESSION["motorcycle_filter"] = array();
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

        $store_name = $this->admin_m->getAdminShippingProfile();
        $page_info = $this->motorcycle_m->getPageInfos();

        $title = $page_info['page_title']." For Sale ".$store_name['company']." ".$store_name['city']." ".$store_name['state'];
        $this->_mainData['forSaleLink'] = 'For_Sale_'.$store_name['city'].'_'.$store_name['state'];

        $this->_mainData['pageRec'] = $this->pages_m->getPageRec(1);

        $this->setMasterPageVars('title', $title);

        $this->_mainData['meta_description'] = "At " . $store_name['company'] . " in ". $store_name['city'] ." ". $store_name['state'] ." ". $page_info['page_meta'];

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

        if ($id == null) {
            redirect('Major_Unit_List?fltr=New_Inventory');
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
            $metaTag = '<meta property="og:image" content="' . ($this->_mainData['motorcycle']['images'][0]["external"] > 0 ? $this->_mainData['motorcycle']['images'][0]["image_name"] : (jsite_url('/media/') . $this->_mainData['motorcycle']['images'][0]['image_name'])) . '"/>';
            $this->setMasterPageVars('metatag', $metaTag);
        }

        $this->load->library('user_agent');
        $this->_mainData['referUrl'] = base_url('Major_Unit_List');
        if ($this->agent->is_referral())
        {
            $this->_mainData['referUrl'] = $this->agent->referrer();
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
            $_SESSION["bikeControlSort"] = 0;
        }
        if (!array_key_exists("bikeControlShow", $_SESSION)) {
            $_SESSION["bikeControlShow"] = ITEMS_ON_PAGE;
        }

        $this->load->model('motorcycle_m');
        $curPage = intVal($this->input->post("page") ? $this->input->post("page") : 0);
        $offset = ($curPage * $_SESSION["bikeControlShow"]);

        $filter = $_SESSION["motorcycle_filter"];
        
        unset($filter['page']);
        // JLB 06-04-17
        // Why was there a separate one for getFilterMotorcycles?? As far as I can tell, it was to separate off the limit vs. offset.
        $motorcycles['motorcycles'] = $this->motorcycle_m->getMotorcycles($filter, $_SESSION["bikeControlShow"], $offset, $_SESSION["bikeControlSort"], $_SESSION["major_units_featured_only"]);


        $total = $this->motorcycle_m->getTotal($filter, $_SESSION["major_units_featured_only"]);
        $motorcycles['pages'] = ceil($total / $_SESSION["bikeControlShow"]);
        $motorcycles['page'] = $curPage + 1;

        $filteredProducts = $this->load->view('benz_views/filter-product.php', $motorcycles, true);
        echo $filteredProducts;
    }


}