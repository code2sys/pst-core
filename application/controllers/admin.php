<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

class Admin extends Master_Controller {

    protected $_adpdtLimit = 50;
    protected $_adusrLimit = 49;
    protected $_adOrderLimit = 50;
    protected $_pagination = 6;

    function __construct() {
        parent::__construct();
        if ($_SESSION['userRecord']['user_type'] == 'employee') {
            
        } else if (!@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (@$_SESSION['userRecord']['admin'] == 0 && $_SESSION['userRecord']['user_type'] == 'normal')
            redirect('welcome');
        $this->setFooterView('admin/footer_v.php');
        $this->load->model('admin_m');
        //$this->output->enable_profiler(TRUE);
    }

    /*     * ****************************************** VALIDATION ************************************************************ */

    protected function validateEditCategory() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('category_id', 'Category Id', 'xss_clean');
        $this->form_validation->set_rules('parent_category_id', 'Parent Category', 'required|xss_clean');
        $this->form_validation->set_rules('active', 'Active', 'xss_clean');
        $this->form_validation->set_rules('featured', 'Featured', 'xss_clean');
        $this->form_validation->set_rules('name', 'Name', 'xss_clean');
        $this->form_validation->set_rules('title', 'Title', 'xss_clean');
        $this->form_validation->set_rules('meta_tag', 'Meta Tag', 'xss_clean');
        $this->form_validation->set_rules('keywords', 'Keywords', 'xss_clean');
        $this->form_validation->set_rules('mark-up', 'Mark-up', 'integer|xss_clean');
        $this->form_validation->set_rules('notice', 'Notice', 'xss_clean');
        return $this->form_validation->run();
    }

    protected function validateEditBrand() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('brand_id', 'Brand Id', 'xss_clean');
        $this->form_validation->set_rules('active', 'Active', 'xss_clean');
        $this->form_validation->set_rules('featured', 'Featured', 'xss_clean');
        $this->form_validation->set_rules('exclude_market_place', 'exclude_market_place', 'xss_clean');
        $this->form_validation->set_rules('closeout_market_place', 'closeout_market_place', 'xss_clean');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('slug', 'Brand Url', 'callback_username_check');
        $this->form_validation->set_rules('meta_tag', 'Meta Tag', 'xss_clean');
        $this->form_validation->set_rules('keywords', 'Keywords', 'xss_clean');
        $this->form_validation->set_rules('mark-up', 'Mark-up', 'is_natural|xss_clean');
        $this->form_validation->set_rules('map_percent', 'MAP Pricing', 'integer|xss_clean');
        return $this->form_validation->run();
    }

    public function username_check($str) {
        if ($this->admin_m->checkBrandSlug($str, $this->input->post('brand_id'))) {
            return TRUE;
        } else {
            $this->form_validation->set_message('username_check', 'Brand Slug should be unique');
            return FALSE;
        }
    }

    protected function validateEditTaxes() {
        return true; // This validator makes no sense anymore.
        
        $this->load->library('form_validation');
        $taxes = $this->input->post('id');
        if (!empty($taxes)) {
            foreach ($taxes as $key => $id) {
                $this->form_validation->set_rules('id[' . $key . ']', 'Id # ' . ($key + 1), 'required|xss_clean');
                $this->form_validation->set_rules('active[' . $key . ']', 'Active # ' . ($key + 1), 'xss_clean');
                $this->form_validation->set_rules('percentage[' . $key . ']', 'Percentage # ' . ($key + 1), 'xss_clean');
                $this->form_validation->set_rules('tax_value[' . $key . ']', 'Value # ' . ($key + 1), 'required|is_numeric|xss_clean');
            }
        }
        return $this->form_validation->run();
    }

    protected function validateEditDistributor() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
        return $this->form_validation->run();
    }

    protected function validateEditShippingRules() {
        $this->load->library('form_validation');
        $formFields = $this->input->post();
        if (@$formFields['edit']) {
            $this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
        }

        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('weight_low', 'Weight Low', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('weight_high', 'Weight High', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('price_low', 'Price Low', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('price_high', 'Price High', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('width_low', 'Width Low', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('width_high', 'Width High', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('height_low', 'Height Low', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('height_high', 'Height High', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
        $this->form_validation->set_rules('active', 'Active', 'xss_clean');
        $this->form_validation->set_rules('value', 'Price', 'requiredis_numeric|xss_clean');
        return $this->form_validation->run();
    }

    protected function validateSearch() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('qty', 'Qty', 'required|xss_clean');
        return $this->form_validation->run();
    }

    protected function validateSku() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('qty', 'Qty', 'required|xss_clean');
        $this->form_validation->set_rules('sku', 'partnumber', 'required|xss_clean');
        return $this->form_validation->run();
    }

    protected function validateImage() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('table', 'Table', 'required|xss_clean');
        $this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
        return $this->form_validation->run();
    }

    protected function validateEditUser() {
        $this->load->library('form_validation');
        $post = $this->input->post();
        $user = @$post['id'];
        if (!empty($user)) {
            foreach ($user as $key => $id) {
                $this->form_validation->set_rules('id[' . $key . ']', 'Id ' . $key, 'xss_clean');
                $this->form_validation->set_rules('wholesaler[' . $key . ']', 'Wholesaler ' . $key, 'xss_clean');
                $this->form_validation->set_rules('no_tax[' . $key . ']', 'No Tax ' . $key, 'xss_clean');
            }
        }
        return $this->form_validation->run();
    }

    function _validateDate($date, $type) {
        if ($date) {
            $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
            if (!preg_match($date_regex, $date)) {
                $this->form_validation->set_message('_validateDate', 'Please provide a valid ' . $type . ' date.');
                return FALSE;
            }
            return $date;
        }
        return TRUE; // Validate Requiment in set_rules line.
    }

    protected function validateCoupon() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('couponCode', 'Coupon Code', 'required|xss_clean');
        $this->form_validation->set_rules('startDate', 'Start Date', 'callback__validateDate[start]|xss_clean');
        $this->form_validation->set_rules('endDate', 'End Date', 'callback__validateDate[end]|xss_clean');
        $this->form_validation->set_rules('totalUses', 'Total Uses', 'integer|xss_clean');
        $this->form_validation->set_rules('type', 'Percentage or Set Value', 'required|xss_clean');
        $this->form_validation->set_rules('amount', 'Amount', 'required|xss_clean');
        $this->form_validation->set_rules('associatedProductSKU', 'Associated Product SKU', 'xss_clean');
        $this->load->model('coupons_m');
        $specialConstraints = $this->coupons_m->getSpecialConstraints();
        if ($specialConstraints) {
            foreach ($specialConstraints as $opt) {
                $this->form_validation->set_rules($opt['ruleName'], $opt['displayName'], 'xss_clean');
            }
        }
        return $this->form_validation->run();
    }

    protected function validateShipping() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('zip', 'Zip/Postal Code', 'required|xss_clean');
        $this->form_validation->set_rules('weight', 'Weight', 'required|xss_clean');
        $this->form_validation->set_rules('country', 'Country', 'xss_clean');

        return $this->form_validation->run();
    }

    protected function validateAdPdtPageBundle() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('order', 'Order', 'xss_clean');
        $this->form_validation->set_rules('page', 'Page', 'numeric|xss_clean');
        $this->form_validation->set_rules('filter', 'Filter', 'xss_clean');
        $this->form_validation->set_rules('dir', 'Direction', 'xss_clean');
        $this->form_validation->set_rules('cat', 'Category', 'xss_clean');
        return $this->form_validation->run();
    }

    protected function validateProduct() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('sku', 'SKU', 'required|is_unique[product.sku]|xss_clean');
        $this->form_validation->set_rules('display_name', 'Product Name', 'required|xss_clean');
        $this->form_validation->set_rules('wholesale', 'Wholesale', 'required|xss_clean');
        $this->form_validation->set_rules('retail', 'Retail', 'required|xss_clean');
        $this->form_validation->set_rules('sale', 'Sales Price', 'xss_clean');
        $this->form_validation->set_rules('saleWs', 'Wholesales Sales Price', 'xss_clean');
        $this->form_validation->set_rules('weight', 'Weight', 'required|xss_clean');
        $this->form_validation->set_rules('description', 'Desc', 'xss_clean');
        $this->form_validation->set_rules('category', 'Category', 'required|xss_clean');
        $this->form_validation->set_rules('code', 'code', 'xss_clean');
        $this->form_validation->set_rules('taxable', 'Taxable', 'xss_clean');
        $this->form_validation->set_rules('onSale', 'onSale', 'xss_clean');
        $this->form_validation->set_rules('active', 'Active', 'xss_clean');
        return $this->form_validation->run();
    }

    protected function validateProfile() {
        $this->load->library('form_validation');
        //$this->form_validation->set_rules('deal', 'Deal Percentage', 'integer|xss_clean');
        //$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        //$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
        $this->form_validation->set_rules('phone', 'Phone', 'required|xss_clean');
        $this->form_validation->set_rules('street_address', 'Street Address', 'required|max_length[40]|xss_clean');
        $this->form_validation->set_rules('address_2', 'Apt/Suite', 'max_length[40]|xss_clean');
        $this->form_validation->set_rules('city', 'City', 'required|xss_clean');
        $this->form_validation->set_rules('state', 'State', 'required|xss_clean');
        $this->form_validation->set_rules('zip', 'Zip', 'required|xss_clean');
        $this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
        $this->form_validation->set_rules('company', 'Company', 'xss_clean');
        return $this->form_validation->run();
    }

    protected function validateOrderSearchFilter() {
        $this->load->library('form_validation');
        //$this->form_validation->set_rules('ajax', 'Approved', 'required|xss_clean');
        $this->form_validation->set_rules('filter', 'Filter', 'xss_clean');
        $this->form_validation->set_rules('days', 'days', 'xss_clean');
        //$this->form_validation->set_rules('date_search_from', 'date_search_from', 'callback__validateDate[from]|xss_clean');
        //$this->form_validation->set_rules('date_search_to', 'date_search_to', 'callback__validateDate[to]|xss_clean');

        return $this->form_validation->run();
    }

    protected function validatePart() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('featured', 'Featured', 'required');
        $this->form_validation->set_rules('status', 'markup', 'required');
        $this->form_validation->set_rules('sku', 'Stock Code', 'required');
        return $this->form_validation->run();
    }

    protected function validateMotorcycle() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('vehicle_type', 'Vehicle Type', 'required');
        $this->form_validation->set_rules('condition', 'Condition', 'required');
        $this->form_validation->set_rules('sku', 'Sku', 'required');
        $this->form_validation->set_rules('category', 'Category', 'required');
        return $this->form_validation->run();
    }

    protected function validateNewSku() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('sku', 'Sku', 'required');
        return $this->form_validation->run();
    }

    protected function validateNewCat() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('category', 'Category', 'required');
        return $this->form_validation->run();
    }

    protected function validateMotorcycleDesc() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('descr', 'Description', 'required|max_length[5000]|xss_clean');
        return $this->form_validation->run();
    }

    /*     * ********************************************** END VALIDATION ********************************************* */

    /*     * ********************************************* PAGINATION ************************************************** */

    protected function adUsrPagination($count) {
        $pages = 0;
        if (is_numeric($count))
            $pages = ceil($count / $this->_adusrLimit); // Number of records per page
        if ($pages < 0)
            $pages = 0;
        return $pages;
    }

    protected function adOrderPagination($count) {
        $pages = 0;
        if (is_numeric($count))
            $pages = ceil($count / $this->_adOrderLimit); // Number of records per page
        if ($pages < 0)
            $pages = 0;
        return $pages;
    }

    public function generatePaginate($direction = NULL) {
        if (is_numeric(@$_POST['page'])) {
            $this->_mainData['pages'] = $this->adpdtPagination($this->admin_m->getProductCount(@$_POST['cat'], @$_POST['filter'], @$_POST['brand']));
            $this->_mainData['currentPage'] = ($direction == 'up') ? (@$_POST['page'] + ($this->_pagination * 2)) : (@$_POST['page'] - ($this->_pagination * 2));
            $this->_mainData['display_pages'] = $this->_pagination;
            $pagination = $this->load->view('pagination_v', $this->_mainData, TRUE);
        }
        if (@$_POST['ajax'])
            echo @$pagination;
        else
            return @$pagination;
    }

    public function generateAdUsrTable($page = 1, $filter = NULL) {
        if (@$_POST['ajax']) {
            $page = $this->input->post('page');
            $filter = $this->input->post('filter');
        }
        $this->_mainData['pages'] = $this->adUsrPagination($this->admin_m->getUserCount($filter));
        $this->_mainData['currentPage'] = $page;
        $this->_mainData['display_pages'] = $this->_pagination;
        $this->_mainData['pagination'] = $this->load->view('admin/pagination_v', $this->_mainData, TRUE);
        $offset = ($page - 1) * $this->_adusrLimit;
        $filter = ($filter == 'NULL') ? NULL : $filter;
        $this->_mainData['users'] = $this->admin_m->getUsers($filter, $this->_adusrLimit, $offset);
        $tableView = $this->load->view('tables/admin_users_v', $this->_mainData, TRUE);
        if (@$_POST['ajax']) {
            echo $tableView;
        } else
            return $tableView;
    }

    public function generateUsrPaginate($direction = NULL) {
        if (is_numeric(@$_POST['page'])) {
            $this->_mainData['pages'] = $this->adUsrPagination($this->admin_m->getUserCount());
            $this->_mainData['currentPage'] = ($direction == 'up') ? (@$_POST['page'] + ($this->_pagination * 2)) : (@$_POST['page'] - ($this->_pagination * 2));
            $this->_mainData['display_pages'] = $this->_pagination;
            $pagination = $this->load->view('admin/pagination_v', $this->_mainData, TRUE);
        }
        if (@$_POST['ajax'])
            echo @$pagination;
        else
            return @$pagination;
    }

    public function generateAdPdtListTable($order = 'name', $dir = 'DESC', $page = 1, $filter = NULL, $cat = NULL, $brand = NULL) {
        if (@$_POST['ajax'] && ($this->validateAdPdtPageBundle() !== FALSE)) {// If form validation passes use passed sorting
            $order = $this->input->post('order');
            $page = $this->input->post('page');
            $filter = $this->input->post('filter');
            $dir = $this->input->post('dir');
            $cat = $this->input->post('cat');
        }

        $order = $order . ' ' . $dir;
        $filter = ($filter == 'NULL') ? NULL : $filter;
        $offset = ($page - 1) * $this->_adpdtLimit;
        $this->_mainData['products'] = $this->admin_m->getProducts($cat, $filter, $order, $this->_adpdtLimit, $offset);
        $this->_mainData['categories'] = $this->admin_m->getCategories();
        $tableView = $this->load->view('admin/product/list_table_v', $this->_mainData, TRUE);
        if (@$_POST['ajax']) {
            echo $tableView;
        } else
            return $tableView;
    }

    public function generateAdPdtListTableMotorcycle($order = 'name', $dir = 'DESC', $page = 1, $filter = NULL, $cat = NULL, $brand = NULL) {
        $this->_adpdtLimit = 100000; // JLB 03-27-17 This is a hack.
        if (@$_POST['ajax'] && ($this->validateAdPdtPageBundle() !== FALSE)) {// If form validation passes use passed sorting
            $order = $this->input->post('order');
            $page = $this->input->post('page');
            $filter = $this->input->post('filter');
            $dir = $this->input->post('dir');
            $cat = $this->input->post('cat');
        }

        $order = $order . ' ' . $dir;
        $filter = ($filter == 'NULL') ? NULL : $filter;
        $offset = ($page - 1) * $this->_adpdtLimit;
        $this->_mainData['products'] = $this->admin_m->getMotorcycleProducts($cat, $filter, $order, $this->_adpdtLimit, $offset);
        $this->_mainData['categories'] = $this->admin_m->getCategories();
        $tableView = $this->load->view('admin/motorcycle/list_table_v', $this->_mainData, TRUE);
        if (@$_POST['ajax']) {
            echo $tableView;
        } else
            return $tableView;
    }

    protected function adpdtPagination($count) {
        $pages = 0;
        if (is_numeric($count))
            $pages = ceil($count / $this->_adpdtLimit); // Number of records per page
        if ($pages < 0)
            $pages = 0;
        return $pages;
    }

    public function generateProductListPaginate($direction = NULL) {
        if (is_numeric(@$_POST['page'])) {
            $this->_mainData['pages'] = $this->adpdtPagination($this->admin_m->getProductCount(@$_POST['cat'], @$_POST['filter'], @$_POST['brand']));
            $this->_mainData['currentPage'] = ($direction == 'up') ? (@$_POST['page'] + ($this->_pagination * 2)) : (@$_POST['page'] - ($this->_pagination * 2));
            $this->_mainData['display_pages'] = $this->_pagination;
            $pagination = $this->load->view('admin/pagination/product_list_v', $this->_mainData, TRUE);
        }
        if (@$_POST['ajax'])
            echo @$pagination;
        else
            return @$pagination;
    }

    public function generateListOrderTable($filter = NULL) {
        if ($this->validateOrderSearchFilter() === TRUE) {
            $this->_mainData['orders'] = $this->admin_m->getOrders($filter);
        } else {
            $this->_mainData['orders'] = $this->admin_m->getOrders($filter);
        }

        $pagination = $this->load->view('admin/order/list_table_v', $this->_mainData, TRUE);
        if (@$_POST['ajax'])
            echo @$pagination;
        else
            return @$pagination;
    }

    /*     * ******************************************************** END VALIDATION *********************************************************** */

    /*     * ************************************************** INDIVIDUAL PAGE FUNCTIONS ******************************************** */

    public function index() {
        $this->load->model('reporting_m');

        $this->load->model('admin_m');
        $this->_mainData['totalOrders'] = $this->reporting_m->getOrdersPerMonthDashboard(date('Y-m-d'));
        $this->_mainData['totalCustomers'] = $this->reporting_m->getCountCustomersforDashboard();
        $arr = array();
        $arr['status'][] = 'approved';
        $arr['status'][] = 'processing';
        $arr['days'] = 30;
        $arr['limit'] = 5;

        $this->_mainData['orders'] = $this->admin_m->getOrders($arr);
        $this->_mainData['chartOrders'] = $this->reporting_m->getOrderForMonthChart();
        $chartOrdersDaily = $this->reporting_m->getOrderForDailyChart();
        $chartOrdersWeekly = $this->reporting_m->getOrderForWeeklyChart();
        $chartOrdersYearly = $this->reporting_m->getOrderForYearlyChart();
        $this->_mainData['totalReviews'] = count($this->admin_m->getNewReviews()); // $this->reporting_m->getTotalReviews();

        $now = time();
        $this->_mainData['ytdRevenueThisYear'] = $this->reporting_m->getRevenueWithinDateRange(date('Y-01-01 00:00:00', $now), date('Y-m-d H:i:s', $now));
        $this->_mainData['ytdOrderCountThisYear'] = $this->reporting_m->getOrdersWithinDateRange(date('Y-01-01 00:00:00', $now), date('Y-m-d H:i:s', $now));
        $last_year = strtotime("-1 year", $now);
        $this->_mainData['ytdRevenueLastYear'] = $this->reporting_m->getRevenueWithinDateRange(date("Y-01-01 00:00:00", $last_year), date("Y-m-d H:i:s", $last_year));
        $this->_mainData['ytdOrderCountLastYear'] = $this->reporting_m->getOrdersWithinDateRange(date("Y-01-01 00:00:00", $last_year), date("Y-m-d H:i:s", $last_year));

        $this->_mainData["todaysData"] = $this->reporting_m->getDashboardStatsByHour(date("Y-m-d 00:00:00"), date("Y-m-d 23:59:59"));
        $this->_mainData["sevenDaysData"] = $this->reporting_m->getDashboardStatsByDay(date("Y-m-d 00:00:00", strtotime("-7 days")), date("Y-m-d 23:59:59"));
        $this->_mainData["thirtyDaysData"] = $this->reporting_m->getDashboardStatsByDay(date("Y-m-d 00:00:00", strtotime("-30 days")), date("Y-m-d 23:59:59"));
        $this->_mainData["oneYearsData"] = $this->reporting_m->getDashboardStatsByMonth(date("Y-m-01 00:00:00", strtotime("-1 year")), date("Y-m-d 23:59:59"));

        $this->_mainData['dashboard'] = $this->checkValidAccess('dashboard');

        $this->setNav('admin/nav_v', 0);
        $this->renderMasterPage('admin/master_v', 'admin/home_v', $this->_mainData);
    }

    public function test() {
        $this->load->view('admin/test_v');
    }

    public function wishlists() {
        $this->setNav('admin/nav_v', 2);
        $this->_mainData['wishlists'] = $this->admin_m->getWishlists();
        $this->renderMasterPage('admin/master_v', 'admin/wishlists_v', $this->_mainData);
    }

    public function taxes() {
        if (!$this->checkValidAccess('taxes') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($this->validateEditTaxes() !== FALSE) { // Display Form
            $this->admin_m->updateTaxes($this->input->post());
        }
        $this->setNav('admin/nav_v', 2);
        $this->_mainData['taxes'] = $this->admin_m->getTaxes();
        $this->load_countries();
        $this->renderMasterPage('admin/master_v', 'admin/taxes_v', $this->_mainData);
    }

    public function distributors() {
        if (!$this->checkValidAccess('distributors') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($this->validateEditDistributor() !== FALSE) { // Display Form
            $this->admin_m->updateDistributors($this->input->post());
        }
        $this->setNav('admin/nav_v', 1);
        $this->_mainData['distributors'] = $this->admin_m->getDistributors();
        $this->renderMasterPage('admin/master_v', 'admin/distributor_v', $this->_mainData);
    }

    public function profile() {
        if (!$this->checkValidAccess('profile') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($this->validateProfile() !== FALSE) { // Display Form
            $this->admin_m->updateAdminShippingProfile($this->input->post());

            // We have to echo the image,if we get it....

            $this->_mainData['success'] = TRUE;
        }
        $this->_mainData['address'] = $this->admin_m->getAdminShippingProfile();
        $this->_mainData['dealPercentage'] = $this->admin_m->getDealPercentage();
        $this->_mainData['states'] = $this->load_states();
        $this->_mainData['provinces'] = $this->load_provinces();
        $this->_mainData['countries'] = array('US' => 'USA', 'CA' => 'Canada');
        $this->setNav('admin/nav_v', 3);
        $this->renderMasterPage('admin/master_v', 'admin/profile_v', $this->_mainData);
    }

    /*     * ********************************** END INDIVIDUAL PAGE FUNCTIONS ********************************** */

    /*     * ******************************PRODUCT SECTION ***************************************** */

    /*     * **********************************************END PRODUCT SECTION ***************************************** */

    /*     * ************************************************ COUPON **************************************** */

    public function coupon() {
        if (!$this->checkValidAccess('coupons') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->model('coupons_m');

        if ($this->validateCoupon() === TRUE) {
            $success = $this->coupons_m->createCoupon($this->input->post());
        }

        $this->_mainData['specialConstraintsDD'] = $this->coupons_m->getSpecialConstraintsDD();
        $this->_mainData['specialConstraints'] = $this->coupons_m->getSpecialConstraints();
        $this->_mainData['brands_list'] = $this->admin_m->getBrands(TRUE);

        $this->_mainData['coupons'] = $this->coupons_m->getCoupons();
        $this->setNav('admin/nav_v', 5);
        $this->renderMasterPage('admin/master_v', 'admin/coupon_v', $this->_mainData);
    }

    public function coupon_delete($id) {
        if (!$this->checkValidAccess('coupons') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($id)) {
            $this->load->model('coupons_m');
            $record = $this->coupons_m->deleteCoupon($id);
        }
        redirect('admin/coupon');
    }

    public function load_coupon($id) {
        if (is_numeric($id)) {
            $this->load->model('coupons_m');
            $record = $this->coupons_m->getCouponById($id);
            echo json_encode($record);
        }
    }

    /*     * *********************************************** END COUPON ******************************************** */

    /*     * ********************************************** IMAGE ************************************************ */

    public function load_image() {
        if ($this->validateImage() !== FALSE) { // Display Form
            $this->_mainData['table'] = $this->input->post('table');
            $this->_mainData['id'] = $this->input->post('id');
            $tableView = $this->load->view('modals/add_image_v', $this->_mainData, TRUE);
            echo $tableView;
        }
    }

    public function add_image() {
        if (@$_FILES['userfile']) {
            $this->load->model('file_handling_m');
            $data = $this->file_handling_m->add_new_file('userfile');
            if (!@$data['error']) {
                $this->setRedirectValues('Error', $data['the_errors']);
                redirect('adminproduct/product');
            } else {
                // Update image name into database
                $fileData = $data;
                $this->admin_m->updateImage($fileData['file_name'], $this->input->post('table'), $this->input->post('id'));
            }
        }
        redirect('admin/' . $this->input->post('table'));
    }

    public function remove_image() {
        $imageName = '';
        $this->admin_m->updateImage($imageName, $this->input->post('table'), $this->input->post('id'));
        return TRUE;
    }

    /*     * ****************************************** END IMAGE ************************************** */

    /*     * ************************** PRODUCT ******************************* */

    public function product() {
        header("Location: " . base_url("adminproduct/product"));
    }

    /*     * ************************** END PRODUCT ******************************* */

    /*     * ************************** CATEGORY *********************************** */

    public function category() {
        if (!$this->checkValidAccess('categories') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $mainCategoryList = $this->admin_m->getCategories(FALSE);
        if ($mainCategoryList) {
            foreach ($mainCategoryList as $cat) {
                $this->_mainData['categories'][$cat['parent_category_id']][] = $cat;
            }
        }

        $this->_mainData['parent_categories'] = $this->admin_m->getCategories(TRUE);



        if ($this->validateEditCategory() !== FALSE && !empty($_POST)) { // Display Form
            $catArr = array();
            $categories = $this->_mainData['categories'];
            $postData = $this->input->post();

            $updateCategories = array();
            $updateCategories[0]['parent_category_id'] = $postData['parent_category_id'];
            $updateCategories[0]['category_id'] = $postData['category_id'];
            $updateCategories[0]['featured'] = $postData['featured'] == 1 ? 1 : 0;
            $updateCategories[0]['active'] = $postData['active'];
            $updateCategories[0]['name'] = $postData['name'];
            $updateCategories[0]['title'] = $postData['title'];
            $updateCategories[0]['meta_tag'] = $postData['meta_tag'];
            $updateCategories[0]['keywords'] = $postData['keywords'];
            $updateCategories[0]['mark-up'] = $postData['mark-up'];
            $updateCategories[0]['google_category_num'] = $postData['google_category_num'];
            $updateCategories[0]['ebay_category_num'] = $postData['ebay_category_num'];
            $updateCategories[0]['notice'] = $postData['notice'];
            $catArr[$postData['category_id']] = $postData['category_id'];

            $counter = 1;
            //!empty($postData['google_category_num']) && 
            if (@$categories[$postData['category_id']]) {
                foreach ($categories[$postData['category_id']] as $subCat) {

                    $updateCategories[$counter]['parent_category_id'] = $subCat['parent_category_id'];
                    $updateCategories[$counter]['category_id'] = $subCat['category_id'];
                    $updateCategories[$counter]['featured'] = $subCat['featured'] == 1 ? 1 : 0;
                    $updateCategories[$counter]['active'] = $subCat['active'];
                    $updateCategories[$counter]['name'] = $subCat['name'];
                    $updateCategories[$counter]['title'] = $subCat['title'];
                    $updateCategories[$counter]['meta_tag'] = $subCat['meta_tag'];
                    $updateCategories[$counter]['keywords'] = $subCat['keywords'];
                    $updateCategories[$counter]['mark-up'] = $subCat['mark_up'];
                    $updateCategories[$counter]['google_category_num'] = $subCat['google_category_num'];
                    $updateCategories[$counter]['ebay_category_num'] = $subCat['ebay_category_num'];
                    $updateCategories[$counter]['notice'] = $subCat['notice'];
                    $catArr[$subCat['category_id']] = $subCat['category_id'];

                    if (@$categories[$subCat['category_id']]) {
                        foreach ($categories[$subCat['category_id']] as $subsubCat) {

                            $secondCounter = count($updateCategories);
                            $updateCategories[$secondCounter]['parent_category_id'] = $subsubCat['parent_category_id'];
                            $updateCategories[$secondCounter]['category_id'] = $subsubCat['category_id'];
                            $updateCategories[$secondCounter]['featured'] = $subsubCat['featured'] == 1 ? 1 : 0;
                            $updateCategories[$secondCounter]['active'] = $subsubCat['active'];
                            $updateCategories[$secondCounter]['name'] = $subsubCat['name'];
                            $updateCategories[$secondCounter]['title'] = $subsubCat['title'];
                            $updateCategories[$secondCounter]['meta_tag'] = $subsubCat['meta_tag'];
                            $updateCategories[$secondCounter]['keywords'] = $subsubCat['keywords'];
                            $updateCategories[$secondCounter]['mark-up'] = $subsubCat['mark_up'];
                            $updateCategories[$secondCounter]['google_category_num'] = $subsubCat['google_category_num'];
                            $updateCategories[$secondCounter]['ebay_category_num'] = $subsubCat['ebay_category_num'];
                            $updateCategories[$secondCounter]['notice'] = $subsubCat['notice'];
                            $catArr[$subsubCat['category_id']] = $subsubCat['category_id'];

                            if (@$categories[$subsubCat['category_id']]) {
                                foreach ($categories[$subsubCat['category_id']] as $subsubsubCat) {

                                    $thirdCounter = count($updateCategories);
                                    $updateCategories[$thirdCounter]['parent_category_id'] = $subsubsubCat['parent_category_id'];
                                    $updateCategories[$thirdCounter]['category_id'] = $subsubsubCat['category_id'];
                                    $updateCategories[$thirdCounter]['featured'] = $subsubsubCat['featured'] == 1 ? 1 : 0;
                                    $updateCategories[$thirdCounter]['active'] = $subsubsubCat['active'];
                                    $updateCategories[$thirdCounter]['name'] = $subsubsubCat['name'];
                                    $updateCategories[$thirdCounter]['title'] = $subsubsubCat['title'];
                                    $updateCategories[$thirdCounter]['meta_tag'] = $subsubsubCat['meta_tag'];
                                    $updateCategories[$thirdCounter]['keywords'] = $subsubsubCat['keywords'];
                                    $updateCategories[$thirdCounter]['mark-up'] = $subsubsubCat['mark_up'];
                                    $updateCategories[$thirdCounter]['google_category_num'] = $subsubsubCat['google_category_num'];
                                    $updateCategories[$thirdCounter]['ebay_category_num'] = $subsubsubCat['ebay_category_num'];
                                    $updateCategories[$thirdCounter]['notice'] = $subsubsubCat['notice'];
                                    $catArr[$subsubsubCat['category_id']] = $subsubsubCat['category_id'];
                                }
                            }
                        }
                    }

                    $counter++;
                }
            }

//             echo "<pre>";
//             print_r($catArr);
//             print_r($updateCategories);
//             echo "</pre>";
//             exit;
            foreach ($updateCategories as $category) {
                $this->admin_m->updateCategory($category);
            }
            redirect('admin/category');
        }

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/category_v', $this->_mainData);
    }

	
    public function category_image($id = NULL) {
		if(!$this->checkValidAccess('categories') && !@$_SESSION['userRecord']['admin']) {
			redirect('');
		}
        if (is_null($id)) {
            redirect('admin/category');
        } else {

            $categoryData = $this->admin_m->getCategory($id);
            $this->_mainData['cate'] = array($categoryData);
            $this->_mainData['id'] = $id;
        }

        if (@$_FILES['image']['name']) {
            $config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
            $config['file_name'] = str_replace("'", '-', str_replace('%', '', str_replace(' ', '_', $categoryData['name'])));
            $this->load->model('file_handling_m');
            $data = $this->file_handling_m->add_new_file_category('image', $config);
            if (@$data['error'])
                $this->_mainData['errors'] = $data['the_errors'];
            else {
                $categoryData['image'] = $data['file_name'];
                $this->admin_m->updateCategoryImage($categoryData);
            }

            // just get it again
            $categoryData = $this->admin_m->getCategory($id);
            $this->_mainData['cate'] = array($categoryData);
            $this->_mainData['id'] = $id;
        }


        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/category_images_v', $this->_mainData);
    }

	
    public function category_delete($id) {
        if (is_numeric($id)) {
            $this->admin_m->deleteCategory($id);
        }
        redirect('admin/category');
    }

    public function load_category_rec($id) {
        if (is_numeric($id)) {
            $record = $this->admin_m->getCategory($id);
            if (is_null($record['title'])) {
                $record['title'] = str_replace(' > ', ', ', $record['long_name']);
                //$record['title'] = $record['long_name'];
            }
            echo json_encode($record);
        }
        exit();
    }

    /*     * ******************************* END CATEGORY ************************************* */

    /*     * ******************************** BRAND ********************************************** */

    public function brand() {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->helper('async');
        if ($this->validateEditBrand() !== FALSE) { // Display Form
            $this->admin_m->updateBrand($this->input->post());
            //redirect('admin/brand?update=1');	
        }
        $this->_mainData['brands'] = $this->admin_m->getBrands(FALSE);
        $this->_mainData['parent_brands'] = $this->admin_m->getBrands(TRUE);
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/brand/brand_v', $this->_mainData);
        curl_request_async();
    }

    public function brand_image($id = NULL) {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/brand');
        } else {

            $brandData = $this->admin_m->getBrand($id);
            $this->_mainData['brands'] = array($brandData);
            $this->_mainData['id'] = $id;
        }

        if (@$_FILES['image']['name']) {
            $config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
            $config['file_name'] = str_replace("'", '-', str_replace('%', '', str_replace(' ', '_', $brandData['name'])));
            $this->load->model('file_handling_m');
            $data = $this->file_handling_m->add_new_file_brand('image', $config);
            if (@$data['error'])
                $this->_mainData['errors'] = $data['the_errors'];
            else {
                $brandData['image'] = $data['file_name'];
                $this->admin_m->updateBrand($brandData);
            }

            // just get it again
            $brandData = $this->admin_m->getBrand($id);
            $this->_mainData['brands'] = array($brandData);
            $this->_mainData['id'] = $id;
        }


        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/brand/brand_images_v', $this->_mainData);
    }

    public function brand_video($id = NULL) {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/brand');
        } else {
            $brandData = $this->admin_m->getBrand($id);
            $brandVideo = $this->admin_m->getBrandVideos($id);
            $this->_mainData['brands'] = array($brandData);
            $this->_mainData['brand_video'] = $brandVideo;
            $this->_mainData['id'] = $id;
        }

        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('video_url') as $k => $v) {
                if ($v != '') {
                    $url = $v;
                    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
                    //$my_array_of_vars['v'];
                    $arr[] = array('video_url' => $my_array_of_vars['v'], 'ordering' => $this->input->post('ordering')[$k], 'brand_id' => $this->input->post('brand_id'), 'title' => $this->input->post('title')[$k]);
                }
            }
            $this->admin_m->updateBrandVideos($this->input->post('brand_id'), $arr);
            redirect('admin/brand_video/' . $this->input->post('brand_id'));
        }


        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/brand/brand_videos_v', $this->_mainData);
    }

    public function brand_sizechart($id = NULL) {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_null($id)) {
            redirect('admin/brand');
        } else {
            $brandData = $this->admin_m->getBrand($id);
            $this->_mainData['brands'] = array($brandData);
            //$this->_mainData['categories'] = $this->admin_m->getCategories();
            $this->load->model('parts_m');
            $this->_mainData['age_gender'] = $this->parts_m->age_gender();
            $listParameters = array('brand' => $brandData['brand_id']);
            $this->_mainData['categories'] = $this->parts_m->getSearchCategoriesBrand($listParameters, 1000);
            $this->_mainData['sizechart'] = $this->admin_m->getSizeChart($id);
            $this->_mainData['id'] = $id;
        }

        if ($this->input->post()) {
            if (@$_FILES['image']['name']) {
                $config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
                $config['file_name'] = str_replace("'", '-', str_replace('%', '', str_replace(' ', '_', $_FILES['image']['name'])));
                $this->load->model('file_handling_m');
                $data = $this->file_handling_m->add_new_file_brandSizeChart('image', $config);
                if (@$data['error'])
                    $this->_mainData['errors'] = $data['the_errors'];
                else {
                    $image = $data['file_name'];
                }
            }

            if ($this->input->post('savebrand')) {
                $brandArr = array('sizechart_url' => $this->input->post('size_url'));
                $brandArr['size_chart_status'] = $this->input->post('active') == 1 ? 1 : 0;
                $this->admin_m->updateBrandSizeChart($id, $brandArr);
            }
            // if ($this->input->post('save')) {
            // $arr = array('brand_id' => $id, 'title' => $this->input->post('title'), 'url' => $this->input->post('url'), 'image' => $image, 'categories' => implode(',', $this->input->post('categories')), 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
            // if( $this->input->post('partquestion_id') != '' ) {
            // $arr['partquestion_id'] = $this->input->post('partquestion_id');
            // }
            // $this->admin_m->insertSizeChart($arr);
            // }
            // if ($this->input->post('update')) {
            // $arr = array('brand_id' => $id, 'title' => $this->input->post('title'), 'url' => $this->input->post('url'), 'image' => $image, 'categories' => implode(',', $this->input->post('categories')), 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
            // if( $this->input->post('partquestion_id') != '' ) {
            // $arr['partquestion_id'] = $this->input->post('partquestion_id');
            // }
            // $this->admin_m->updateSizeChart($this->input->post('id'), $arr);
            // }
            if ($this->input->post('save')) {
                $cat = $this->input->post('categories');
                $ctrgs = $this->admin_m->getAllRelatedCategories($cat);
                $arr = array('brand_id' => $id, 'title' => $this->input->post('title'), 'url' => $this->input->post('url'), 'categories' => implode(',', $ctrgs), 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
                if (@$_FILES['image']['name']) {
                    $arr['image'] = $image;
                }
                if ($this->input->post('partquestion_id') != '') {
                    $arr['partquestion_id'] = implode(',', $this->input->post('partquestion_id'));
                }
                $this->admin_m->insertSizeChart($arr);
            }
            if ($this->input->post('update')) {
                $cat = $this->input->post('categories');
                $ctrgs = $this->admin_m->getAllRelatedCategories($cat);
                $arr = array('brand_id' => $id, 'title' => $this->input->post('title'), 'url' => $this->input->post('url'), 'categories' => implode(',', $ctrgs), 'size_chart' => json_encode($this->input->post('size')), 'content' => $this->input->post('content'));
                if (@$_FILES['image']['name']) {
                    $arr['image'] = $image;
                }
                if ($this->input->post('partquestion_id') != '') {
                    $arr['partquestion_id'] = implode(',', $this->input->post('partquestion_id'));
                }
                $this->admin_m->updateSizeChart($this->input->post('id'), $arr);
            }
            if ($this->input->post('delete')) {
                $this->admin_m->deleteSizeChart($this->input->post('id'));
            }
            //$this->admin_m->updateBrandVideos($this->input->post('brand_id'), $arr);
            redirect('admin/brand_sizechart/' . $id);
        }

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/brand/brand_sizechart_v', $this->_mainData);
    }

    public function brand_delete($id) {
        if (!$this->checkValidAccess('brands') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($id)) {
            $this->admin_m->deleteBrand($id);
        }
        redirect('admin/brand');
    }

    public function load_brand_rec($id) {
        if (is_numeric($id)) {
            $record = $this->admin_m->getBrand($id);
            echo json_encode($record);
        }
        exit();
    }

    /*     * ********************************** END BRAND ******************************** */

    /*     * ************************* ORDERS ******************************* */

    public function orders($page = 1) {
        unset($_SESSION['admin_cart']);
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->model('account_m');
        $this->_mainData['currentPage'] = $page;
        $arr = $this->input->get();
        if (empty($arr['status'])) {
            $arr['status'][] = 'approved';
            $arr['status'][] = 'processing';
            $arr['days'] = 30;
        }
        if (in_array('approved', $arr['status'])) {
            //$arr['status'][] = 'declined';
            //$arr['status'][] = 'batch order';
            //$arr['status'][] = 'processing';
            //$arr['status'][] = 'back order';
            //$arr['status'][] = 'partially shipped';
            //$arr['status'][] = 'will_call';
            //$arr['status'][] = 'shipped/complete';
            //$arr['status'][] = 'returned';
            //$arr['status'][] = 'refunded';
        }
        //echo '<pre>';
        //print_r($arr);
        //echo '</pre>';
        $this->_mainData['pages'] = $this->adOrderPagination($this->account_m->getOrderCount());
        $offset = ($page - 1) * $this->_adOrderLimit;
        //$this->_mainData['prev_orders'] = $this->account_m->getPrevOrderDates($this->_adOrderLimit, $offset);
        // $this->load->library('pagination');
        // $config['base_url'] = site_url().'admin/orders/';
        // $config['total_rows'] = $this->account_m->getOrderCount();
        // $config['per_page'] = $this->_adOrderLimit;
        // $config['use_page_numbers'] = TRUE;
        // $this->pagination->initialize($config);
        //$arr['limit'] = $this->_adOrderLimit;
        //$arr['offset'] = $offset;

        $this->_mainData['listTable'] = $this->generateListOrderTable($arr);

        $this->loadDateFields(array('datepicker_from', 'datepicker_to'));
        $this->setNav('admin/nav_v', 3);
        $this->_mainData['filter'] = $arr;
        $this->renderMasterPage('admin/master_v', 'admin/order/list_v', $this->_mainData);
    }

    public function order_edit($id = 'new', $newPartNumber = NULL, $qty = 1) {
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->createMonths();
        $this->createYears();
        $this->_mainData['states'] = $this->load_states();
        $this->_mainData['provinces'] = $this->load_provinces();
        $this->loadCountries();
        $this->load->model('order_m');
        $this->_mainData['distributors'] = $this->order_m->getDistributors();

        if (!is_null($newPartNumber) && ($id != 'new')) {
            
            $this->load->model('parts_m');
            $part = $this->order_m->getPartIdByPartNumber($newPartNumber);
            
            $questAns = $this->parts_m->getQuestionAnswerByNumber($part['part_id'], $part['partnumber']);
            if(@$questAns) {
                $this->order_m->addProductToOrder($part['partnumber'], $id, $qty, $part['part_id']);
            } else if(!@$questAns && @$part) {
		$this->load->model('account_m');
                $partnumber = $this->account_m->getStockByPartId($part['part_id']);
                $this->order_m->addProductToOrder($part['partnumber'], $id, $qty, $part['part_id']);
            } else {
                if(!$this->order_m->checkPartNumber($newPartNumber, $qty, $id)) {
                    $this->session->set_flashdata('error','Product Not Found.');
                }
            }
            
            redirect('admin/order_edit/'.$id);
        }
        
        $store_name = $this->admin_m->getAdminShippingProfile();
        if( $this->input->post()) {
            require_once(STORE_DIRECTORY.'/lib/Braintree.php');
            Braintree_Configuration::environment($store_name['environment']);
            Braintree_Configuration::merchantId($store_name['merchant_id']);
            Braintree_Configuration::publicKey($store_name['public_key']);
            Braintree_Configuration::privateKey($store_name['private_key']);
            $clientToken = Braintree_ClientToken::generate();
            
            $post = $this->input->post();
            if(@$post['transaction_id'] && $post['refund_amount'] > 0) {
                $result = Braintree_Transaction::refund($post['transaction_id'], $post['refund_amount']);
                
                if( @$result->success ) {
                    $transaction = $result->transaction;
                    $arr = array('braintree_transaction_id' => $transaction->id, 'sales_price' => '-'.$this->input->post('refund_amount'));
                    $this->admin_m->updateOrderPaymentByAdmin( $id, $arr );
                    //$this->admin_m->updateOrderStatusByAdmin( $id, 'Approved' );
                    //$this->load->model('order_m');
                    //$this->order_m->updateStatus($id, 'Approved', 'Ajax Update');
                    //redirect('admin/order_edit/'.$id);
                } else {
                    $error = $result->message;
                    $this->session->set_flashdata('error',$error);

                    //$this->load->model('order_m');
                    //$this->order_m->updateStatus($id, 'Declined', 'Ajax Update');

                    //redirect('admin/order_edit/'.$id);
                }
                exit;
            }

            $result = Braintree_Transaction::sale(['amount' => $this->input->post('amount'),
                   'paymentMethodNonce' => $this->input->post("payment_method_nonce"),
                   'options' => ['submitForSettlement' => True  ],
                   'deviceData' => $this->input->post('device_data'),
                   'customer' => [
                                'firstName' => $_POST['first_name'][0],
                                'lastName' => $_POST['last_name'][0],
                                'company' => $_POST['company'][0],
                                'phone' => $_POST['phone'][0],
                                'email' => $_POST['email'][0]
                          ],
                        'billing' => [
                                'firstName' => $_POST['first_name'][0],
                                'lastName' => $_POST['last_name'][0],
                                'company' => $_POST['company'][0],
                                'streetAddress' => $_POST['street_address'][0],
                                'extendedAddress' => $_POST['address_2'][0],
                                'locality' => $_POST['state'][0],
                                'postalCode' => $_POST['zip'][0]
                        ],
                        'shipping' => [
                                'firstName' => $_POST['first_name'][1],
                                'lastName' => $_POST['last_name'][1],
                                'company' => $_POST['company'][1],
                                'streetAddress' => $_POST['street_address'][1],
                                'extendedAddress' => $_POST['address_2'][1],
                                'locality' => $_POST['state'][1],
                                'postalCode' => $_POST['zip'][1]
                        ],
                   'channel' => 'MxConnectionLLC_SP_PayPalEC_BT']
                  );
            
            if( @$result->success ) {
                $transaction = $result->transaction;
                $arr = array('braintree_transaction_id' => $transaction->id, 'sales_price' => $this->input->post('amount'));
                $this->admin_m->updateOrderPaymentByAdmin( $id, $arr );
                //$this->admin_m->updateOrderStatusByAdmin( $id, 'Approved' );
                $this->load->model('order_m');
                $this->order_m->updateStatus($id, 'Approved', 'Ajax Update');
                redirect('admin/order_edit/'.$id);
            } else {
                $error = $result->message;
                $this->session->set_flashdata('error',$error);
                
                $this->load->model('order_m');
                $this->order_m->updateStatus($id, 'Declined', 'Ajax Update');
                
                redirect('admin/order_edit/'.$id);
            }
        }

        if ($id != 'new') {
            $this->_mainData['order'] = $this->order_m->getOrder($id);
        }
        
        //if($this->_mainData['order']['created_by'] == '1') {
            $weight = 0.00;
            foreach( $this->_mainData['order']['products'] as $product ) {
                if(@$product['dealerRecs']) {
                    foreach( $product['dealerRecs'] as $dealerRec ) {
                        $weight += $dealerRec['weight'];
                    }
                }else if(@$product['distributorRecs']) {
                    foreach( $product['distributorRecs'] as $distributorRec ) {
                        $weight += $distributorRec['weight'];
                    }
                }
            }

            $zip = $this->_mainData['order']['shipping_zip'];
            $grndShippingValue = $this->admin_m->shippingRules($this->_mainData['order']['sales_price'], 'USA', $zip, $weight);
            $shippingValue = $this->admin_m->calculateParcel($zip, 'USA', $grndShippingValue, $weight);
            $this->_mainData['postalOptDD'] = $this->admin_m->subdividePostalOptions(@$_SESSION['postalOptionsAdmin']);
        //}
        
        $this->_mainData['store_name'] = $store_name;
        $this->setNav('admin/nav_v', 3);
        $this->renderMasterPage('admin/master_v', 'admin/order/edit_v', $this->_mainData);
    }

    public function orders_pdf($date = NULL) {
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        // set up PDF Helper files
        $this->load->helper('fpdf_view');
        $parameters = array();
        pdf_init('reporting/poreport.php');

        // Send Variables to PDF
        //update process date and process user info
        $parameters['orders'] = $this->account_m->getPDFOrders($_SESSION['userRecord']['id'], $date);
        $fileName = 'OrderReport_' . time() . '.pdf';

        // Create PDF
        $this->PDF->setParametersArray($parameters);
        $this->PDF->runReport();
        $this->PDF->Output($fileName, 'D'); // I
    }

    public function orders_csv($date = NULL) {
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $orders = $this->account_m->getPDFOrders($_SESSION['userRecord']['id'], $date);
        print_r($orders);
        //echo $this->array2csv($orders);
    }

    /*     * *********************************** END ORDERS ************************************ */

    // Check Admin doc from Brandt for Details.  Add Wishlist to Users View page.

    public function users() {
        if (!$this->checkValidAccess('list') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($this->session->flashdata('errors'))
            $this->_mainData['errors'] = $this->session->flashdata('errors');
        $this->_mainData['userTable'] = $this->generateAdUsrTable(1);
        $this->setNav('admin/nav_v', 4);
        $this->renderMasterPage('master_v', 'admin/users_v', $this->_mainData);
    }

    public function process_edit_users() {
        if ($this->validateEditUser() !== FALSE) { // Display Form
            $this->account_m->updateUserMass($this->input->post());
        }
        $this->generateAdUsrTable();
    }

    /*     * ******************* SHIPPING *************************** */

    public function shipping_rules() {
        if (!$this->checkValidAccess('shipping') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if ($this->validateEditShippingRules() === TRUE) {
            $this->admin_m->updateShippingRules($this->input->post());
        }

        $this->setNav('admin/nav_v', 2);
        $this->_mainData['shippingRules'] = $this->admin_m->getShippingRules();
        $this->load_countries();

        $this->renderMasterPage('admin/master_v', 'admin/shipping_rules_v', $this->_mainData);
    }

    public function load_shipping_rules($id) {
        if (!$this->checkValidAccess('shipping') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($id)) {
            $record = $this->admin_m->getShippingRule($id);
            echo json_encode($record);
        }
        exit();
    }

    public function shipping_rule_delete($id) {
        if (!$this->checkValidAccess('shipping') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($id)) {
            $this->admin_m->deleteShippingRule($id);
        }
        redirect('admin/shipping_rules');
    }

    public function test_shipping() {
        if ($this->validateShipping() !== FALSE) { // Display Form
            $this->_mainData['weight'] = $this->input->post('weight');
            $this->_mainData['zip'] = $this->input->post('zip');
            $this->_mainData['country'] = $this->input->post('country');
            // UPS Rates
            $this->load->library('UpsShippingQuote');

            $objUpsRate = new UpsShippingQuote();

            $strDestinationZip = $this->input->post('zip');
            $strMethodShortName = 'GND';
            $strPackageLength = '8';
            $strPackageWidth = '8';
            $strPackageHeight = '8';
            $strPackageWeight = $this->input->post('weight');
            $strPackageCountry = $this->_mainData['country'];
            $boolReturnPriceOnly = true;

            $this->_mainData['postalOptions']['UPS'] = $objUpsRate->GetShippingRate(
                    $strDestinationZip, $strMethodShortName, $strPackageLength, $strPackageWidth, $strPackageHeight, $strPackageWeight, $boolReturnPriceOnly, $strPackageCountry
            );
            /*

              print_r($this->_mainData['postalOptions']['UPS']);
              exit();

             */


            // USPS Rates
            $this->load->helper('usps');
            $this->_mainData['postalOptions']['USPS'] = USPSParcelRate($strPackageWeight, $strDestinationZip, $strPackageCountry);
        }

        $this->setNav('admin/nav_v', 6);
        $this->renderMasterPage('admin/master_v', 'admin/shipping_v', $this->_mainData);
    }

    /*     * *************************** END SHIPPING ****************************** */

    /*     * ************************************* HELPER FUNCTIONS ************************************* */

    protected function load_countries() {
        $this->_mainData['countries'] = array('US' => 'USA', 'CA' => 'Canada');
    }

    public function load_provinces($ajax = FALSE) {
        $provinces = $this->account_m->getTerritories('CA');
        if ($ajax)
            echo json_encode($provinces);
        else
            return $provinces;
    }

    public function loadCountries() {
        $this->_mainData['countries'] = $this->account_m->getCountries();
    }

    public function change_country() {
        $countryList = array('USA', 'Canada');
        $type = array('billing', 'acct_billing', 'shipping', 'acct_shipping');
        $country = $_POST['country'];
        if (in_array($_POST['country'], $countryList)) {
            $funct = $country . 'fields';
            $labelArr = $this->$funct();
            echo json_encode($labelArr);
        } else
            echo 'Invalid Country';
    }

    protected function USAfields() {
        $labels = array('street_address' => '<b>Address:</b>',
            'address_2' => '<b>Apt/Suite:</b>',
            'city' => '<b>City:</b>',
            'state' => '<b>State:</b>',
            'zip' => '<b>Zipcode:</b>'
        );
        return $labels;
    }

    protected function Canadafields() {
        $labels = array('street_address' => '<b>Civic Address:</b>',
            'address_2' => '<b>Apt/Suite:</b>',
            'city' => '<b>Municipality:</b>',
            'state' => '<b>Province:</b>',
            'zip' => '<b>Postal Code:</b>'
        );
        return $labels;
    }

    public function load_states($ajax = FALSE) {
        $states = $this->account_m->getTerritories('US');
        if ($ajax)
            echo json_encode($states);
        else
            return $states;
    }

    public function createMonths() {

        for ($i = 1; $i < 13; $i++) {
            $monthFormat = sprintf("%02s", $i);
            $dateObj = DateTime::createFromFormat('!m', $i);
            $this->_mainData['months'][$monthFormat] = $dateObj->format('F');
        }
    }

    public function createYears() {
        for ($i = date('y'); $i < (date('y') + 13); $i++) {
            $dt = DateTime::createFromFormat('y', $i);
            $yyyy = $dt->format('Y');
            $this->_mainData['years'][$i] = $yyyy;
        }
    }

    protected function array2csv(array &$array) {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    public function decrypt($id) {
        $password = $this->account_m->getUserPassword($id);
        $this->load->library('encrypt');
        $password = $this->encrypt->decode($password);
        echo $password;
    }

    public function decryptcc($cc, $digits = NULL) {

        $this->load->library('encrypt');
        $cc = $this->encrypt->decode($cc);
        if ($digits)
            return substr($cc, -4);
        else
            return $cc;
    }

    /*
      public function decryptcc($id, $return = FALSE)
      {
      $cc = 'A2dQPVBiCDIBYAZoB2MAYgI+UzAKYwYxUzICYwBvVmM=';
      $this->load->model('order_m');
      $paymentRecord = $this->order_m->getPaymentInfo($id);
      $this->load->library('encrypt');
      $cc = $this->encrypt->decode($paymentRecord['ccnumber']);
      if($return)
      return $cc;
      else
      echo $cc;
      }
     */

    public function new_change_country() {
        $countryList = array('US' => 'USA', 'CA' => 'Canada');
        $type = array('billing', 'acct_billing', 'shipping', 'acct_shipping');
        $country = $_POST['country'];
        if ($countryList[$country]) {
            $funct = $countryList[$country] . 'fields';
            $labelArr = $this->$funct();
            echo json_encode($labelArr);
        } else
            echo 'Invalid Country';
    }

    public function updateStatus() {
        $this->load->model('order_m');
        $this->order_m->updateStatus(1, 'Approved', 'System order');
    }

    /*     * ******************************************** END HELPER FUNCTIONS ******************************************* */

    /*     * ************************************************ PRODUCT RECEIVING **************************************** */

    public function product_receiving() {
        if (!$this->checkValidAccess('product_receiving') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->_mainData['notfound'] = array();
        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('partnumber') as $k => $v) {
                if ($v != '') {
                    $arr1 = array('partnumber' => $v, 'distributor_id' => $this->input->post('distributor_id'), 'quantity' => $this->input->post('quantity')[$k] );
                    if( $this->input->post('cost')[$k] > 0 ) {
                        $arr1['cost'] = $this->input->post('cost')[$k];
                    }
                    $arr[] = $arr1;
                }
            }
            if (!empty($arr)) {
                $err = $this->admin_m->updateDistributorInventory($arr);
                //$err = $this->admin_m->updateDistributorInventory( $arr );
                if (empty($err)) {
                    redirect('admin/product_receiving');
                } else {
                    $this->_mainData['notfound'] = $err['error'];
                    $this->_mainData['found'] = $err['success'];
                }
            }
        }
        if (!empty($this->_mainData['notfound'])) {
            $this->_mainData['errors'] = array('These partnumber not found in the database');
        }

        $this->setNav('admin/nav_v', 2);
        $this->_mainData['distributors'] = $this->admin_m->getDistributorForProductReceiving();
        $this->renderMasterPage('admin/master_v', 'admin/product_receiving', $this->_mainData);
        //echo 'Product Receiving';
    }

    /*     * *********************************************** END PRODUCT RECEIVING ******************************************** */

    /*     * ************************************************ Closeout Repring Rule **************************************** */

    public function closeout_rules() {
        // if( $this->input->post() ) {
        // $arr = array();
        // foreach( $this->input->post('days') as $k => $v ) {
        // $mark_up = $this->input->post('mark_up')[$k] == 1 ? 1 : 0;
        // $arr[] = array('days' => $v, 'percentage' => $this->input->post('percentage')[$k], 'status' => 1, 'mark_up' => $mark_up, 'id' => $k );
        // }
        // if( !empty( $arr ) ) {
        // $this->admin_m->updateCloseoutRules( $arr );
        // redirect('admin/closeout_rules');
        // }
        // }
        // $this->setNav('admin/nav_v', 2);
        // $this->_mainData['closeout_rules'] = $this->admin_m->getAllCloseoutRepringRule();
        // $this->renderMasterPage('admin/master_v', 'admin/closeout_rule', $this->_mainData);
    }

    /*     * *********************************************** END Closeout Repring Rule ******************************************** */

    /*     * ************************************************ Brand Closeout Repring Rule **************************************** */

    public function brand_rule($brand_id = null) {
        if ($brand_id == null) {
            redirect('admin/brand');
        }
        if ($this->input->post()) {
            $arr = array();
            foreach ($this->input->post('days') as $k => $v) {
                $status = 1;
                $mark_up = $this->input->post('mark_up')[$k] == 1 ? 1 : 0;
                $arr[] = array('days' => $v, 'percentage' => $this->input->post('percentage')[$k], 'status' => $status, 'id' => $k, 'brand_id' => $brand_id, 'mark_up' => $mark_up );
            }
            if (!empty($arr)) {
                $this->admin_m->updateCloseoutRules($arr);
                redirect('admin/brand_rule/' . $brand_id);
            }
        }

        $brandData = $this->admin_m->getBrand($brand_id);
        $this->_mainData['brands'] = array($brandData);
        $this->_mainData['id'] = $brand_id;

        $this->setNav('admin/nav_v', 2);
        $this->_mainData['closeout_rules'] = $this->admin_m->getAllCloseoutRepringRule($brand_id);
        $this->renderMasterPage('admin/master_v', 'admin/brand/closeout_rule', $this->_mainData);
    }

    /*     * *********************************************** END Brand Closeout Repring Rule ******************************************** */

    public function deleteRule() {
        if ($this->input->post()) {
            $deletedRule = $this->admin_m->deleteCloseoutRepringRule($this->input->post('id'));
            echo 1;
        } else {
            echo 0;
        }
    }

    public function test_closeout() {
        $this->load->model('parts_m');
        $this->parts_m->closeoutReprisingSchedule();
    }

    //Customer's listing on admin side
    public function customers() {
        if (!$this->checkValidAccess('customers') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $arr = $this->input->post();
        $filter = array();
        if ($arr['search'] != '') {
            $filter = array('search' => $arr['search']);
        }

        $this->setNav('admin/nav_v', 2);
        //$this->_mainData['customers'] = $this->admin_m->getAllCustomers( $filter, 10, 0 );
        //$this->_mainData['cntCustomers'] = $this->admin_m->getAllCustomersCount( $filter );
        $this->renderMasterPage('admin/master_v', 'admin/customer/list_v', $this->_mainData);
    }

    public function load_customer_rec($page) {
        $filter = array();
        $sorting = array('first_name', 3 => 'orders', 5 => 'reminders');
        if ($_POST['order'][0]['column'] != '') {
            $filter['sort'] = $sorting[$_POST['order'][0]['column']];
            $filter['sorter'] = $_POST['order'][0]['dir'];
        }
        if ($_GET['srch'] != '') {
            $filter['search'] = $_GET['srch'];
        }
        $filter['custom'] = 'all';
        if ($this->checkValidAccess('all_customers')) {
            $filter['custom'] = 'all';
        } else if ($this->checkValidAccess('web_customers')) {
            $filter['custom'] = 'web';
        } else if ($this->checkValidAccess('user_specific_customers')) {
            $filter['custom'] = 'own';
        }

        $customers = $this->admin_m->getAllCustomers($filter, $_POST['length'], $_POST['start']);

        $data = array();
        foreach ($customers as $k => $v) {
            $str = '<a style="font-size:17px; margin:-4px 11px 0 0px; color:black; line-height:13px; padding:0px;" data-toggle="tooltip" href="' . base_url('admin/customer_detail/' . $v['id']) . '" title="View" class="glyphicons"><span class="glyphicon">&#xe105;</span></a>';
            if (!empty($v['reminder']) && $v['reminder'] != '') {
                $date = date('Y-m-d', strtotime($v['reminder']['start_datetime']));
                $attention = "<img src='" . site_url('assets/images/attention.png') . "' class='day-rem-evnt' style='height: 30px; width: 30px;' data-id='" . $v['reminder']['id'] . "' data-dt='" . $date . "' data-user='" . $v['id'] . "'>";
            } else {
                $attention = '';
            }
            $data[] = array($v['first_name'] . ' ' . $v['last_name'], $v['phone'], $v['email'], $v['orders'], $v['employee'], $attention, $str);
        }

        $cntCustomers = $this->admin_m->getAllCustomersCount($filter);
        $json_data = array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($cntCustomers),
            "recordsFiltered" => intval($cntCustomers),
            "data" => $data
        );
        echo json_encode($json_data);
        // $offset = $page*10;
        // $filter = array();
        // $customers = $this->admin_m->getAllCustomers( $filter, 10, $offset );
        // $this->load->view('admin/customer/list_table_v', $customers);
    }

    public function customer_detail($user_id = null) {
        if (!$this->checkValidAccess('customers') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if ($this->input->post()) {
            $post = $this->input->post();
            $contactInfo[0]['first_name'] = $post['first_name'][0];
            $contactInfo[0]['last_name'] = $post['last_name'][0];
            $contactInfo[0]['street_address'] = $post['street_address'][0];
            $contactInfo[0]['address_2'] = $post['address_2'][0];
            $contactInfo[0]['city'] = $post['city'][0];
            $contactInfo[0]['state'] = $post['state'][0];
            $contactInfo[0]['zip'] = $post['zip'][0];
            $contactInfo[0]['email'] = $post['email'][0];
            $contactInfo[0]['phone'] = $post['phone'][0];
            $contactInfo[0]['country'] = $post['country'][0];
            $contactInfo[0]['company'] = $post['company'][0];
            //$contactInfo[0]['notes'] = $post['notes'];
            $billing_id = $this->admin_m->updateContact($contactInfo[0], 'billing', $user_id, $post['notes']);
        }

        $this->createMonths();
        $this->createYears();
        $this->_mainData['states'] = $this->load_states();
        $this->_mainData['provinces'] = $this->load_provinces();
        $this->_mainData['user_id'] = $user_id;
        $this->loadCountries();

        $this->setNav('admin/nav_v', 2);
        $this->_mainData['customer'] = $this->admin_m->getCustomerDetail($user_id);
        $this->_mainData['calendar'] = $this->getCalendarCustomer(date('m'), date('Y'), $user_id);
        $this->renderMasterPage('admin/master_v', 'admin/customer/view_v', $this->_mainData);
    }

    //Get Calendar for the customer CRM
    public function getCalendarCustomer($month, $year, $user_id, $ajax = false) {
        $this->_mainData['month'] = $month;
        $this->_mainData['year'] = $year;

        $this->_mainData['reminders'] = $this->admin_m->getMonthReminders($month, $year, $user_id);

        $this->_mainData['eventData'] = $this->admin_m->getReminderRecurrences($month, $year, $user_id);
        $tableView = $this->load->view('admin/customer/calendar_v', $this->_mainData, TRUE);
        if (@$ajax) {
            echo $tableView;
        } else
            return $tableView;
    }

    public function completeEvent($id = null) {
        if ($id != null) {
            $this->admin_m->completeEvent($id);
            echo '1';
        } else {
            redirect('admin/customers/');
        }
        echo '0';
    }

    public function completeRecurEvent($id = null, $rmvd = null) {
        if ($id != null) {
            $this->admin_m->completeRecurEvent($id, $rmvd);
            echo '1';
        } else {
            redirect('admin/customers/');
        }
        echo '0';
    }

    //Get reminder popup for the customer CRM
    public function getReminderPopUpCustomer($id = null) {
        if ($id != null) {
            $this->_mainData['rem'] = $this->admin_m->getReminder($id);
        }
        $this->_mainData['dateReminder'] = $_POST['dt'];
        $this->_mainData['user_id'] = $_POST['user_id'];
        $this->_mainData['tm'] = $this->halfHourTimesPopup();
        $tableView = $this->load->view('admin/customer/reminder_v', $this->_mainData, TRUE);
        echo $tableView;
    }

    public function halfHourTimesPopup() {
        $formatter = function ($time) {
                    if ($time % 3600 == 0) {
                        return date('g:i a', $time);
                    } else {
                        return date('g:i a', $time);
                    }
                };
        $halfHourSteps = range(0, 47 * 1800, 1800);
        return array_map($formatter, $halfHourSteps);
    }

    //Delete customer reminder event
    public function deleteReminderPopUpCustomer($id = null, $user = null) {
        if ($id != null) {
            $this->admin_m->deleteCustomerEvent($id);
        }
        if ($user == null) {
            redirect('admin/customers/');
        } else {
            redirect('admin/customer_detail/' . $user);
        }
    }

    //Get reminder popup for the customer CRM
    public function saveUpdateReminderCustomer($id = null) {
        $arr = array();
        if ($this->input->post()) {
            //$arr['date'] = $this->input->post('date');
            $arr['notes'] = $this->input->post('notes');
            $arr['subject'] = $this->input->post('subject');
            $arr['user_id'] = $this->input->post('user_id');
            $arr['start_datetime'] = date('Y-m-d H:i:s', strtotime($this->input->post('start_date') . ' ' . $this->input->post('start_time')));
            $arr['end_datetime'] = date('Y-m-d H:i:s', strtotime($this->input->post('end_date') . ' ' . $this->input->post('end_time')));
            $arr['data'] = json_encode(array('recur' => $this->input->post('recur'), 'recur_per' => $this->input->post('recur_per'), 'recur_evry' => $this->input->post('rcr_evry')));
            $arr['created_on'] = date('Y-m-d H:i:s');

            if ($id != null) {
                $arr['id'] = $id;
            }
            $parent = $this->admin_m->saveCustomerReminder($arr);
            if ($id != null) {
                $parent = $id;
            }
            $recur = array();
            $arr1 = $arr;
            $arr1['parent'] = $parent;
            unset($arr1['id']);
            $rcr_pr = $this->input->post('recur_per');
            if ($this->input->post('recur') == 'daily') {
                for ($i = 1; $i <= 100; $i++) {
                    $cur_date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                    if ($cur_date > date('Y-m-d', strtotime($arr['end_datetime'])) && $this->input->post('recur_end') != '1') {
                        break;
                    }
                    if (empty($rcr_pr)) {
                        $ndt = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                        $arr1['start_datetime'] = date('Y-m-d H:i:s', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                        $arr1['end_datetime'] = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime']))) . date(' H:i:s', strtotime($arr['end_datetime']));
                        $recur[$ndt] = $arr1;
                    } else {
                        foreach ($rcr_pr as $rcr) {
                            $ndt = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                            $arr1['start_datetime'] = date('Y-m-d H:i:s', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                            $arr1['end_datetime'] = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime']))) . date(' H:i:s', strtotime($arr['end_datetime']));
                            if ($rcr == strtolower(date('l', strtotime($ndt)))) {
                                $recur[$ndt] = $arr1;
                            }
                        }
                    }
                }
            } else if ($this->input->post('recur') == 'weekly') {
                if ($this->input->post('rcr_evry') != '') {
                    $rcr_evry = $this->input->post('rcr_evry');
                    $start_dt = $arr['start_datetime'];
                    for ($i = 1; $i <= 20; $i++) {
                        if ($i % $rcr_evry == '0') {
                            $cur_date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                            if ($cur_date > date('Y-m-d', strtotime($arr['end_datetime'])) && $this->input->post('recur_end') != '1') {
                                break;
                            }
                            $dt = date('Y-m-d', strtotime('+' . $i . ' weeks', strtotime($start_dt)));
                            if (empty($rcr_pr)) {
                                $dy = date('l', strtotime($arr['start_datetime']));
                                $ndt = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                                $arr1['start_datetime'] = date('Y-m-d H:i:s', strtotime('next ' . $dy, strtotime($dt)));
                                $arr1['end_datetime'] = date('Y-m-d', strtotime('next ' . $dy, strtotime($dt))) . date(' H:i:s', strtotime($arr['end_datetime']));
                                $recur[$ndt] = $arr1;
                            } else {
                                foreach ($rcr_pr as $rcr) {
                                    $ndt = date('Y-m-d', strtotime('next ' . $rcr, strtotime($dt)));
                                    $arr1['start_datetime'] = date('Y-m-d H:i:s', strtotime('next ' . $rcr, strtotime($dt)));
                                    $arr1['end_datetime'] = date('Y-m-d', strtotime('next ' . $rcr, strtotime($dt))) . date(' H:i:s', strtotime($arr['end_datetime']));
                                    $recur[$ndt] = $arr1;
                                }
                            }
                        }
                    }
                } else {
                    for ($i = 1; $i <= 100; $i++) {
                        $cur_date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                        if ($cur_date > date('Y-m-d', strtotime($arr['end_datetime'])) && $this->input->post('recur_end') != '1') {
                            break;
                        }
                        if (empty($rcr_pr)) {
                            $dy = date('l', strtotime($arr['start_datetime']));
                            $ndt = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                            $arr1['start_datetime'] = date('Y-m-d H:i:s', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                            $arr1['end_datetime'] = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime']))) . date(' H:i:s', strtotime($arr['end_datetime']));
                            if ($dy == strtolower(date('l', strtotime($ndt)))) {
                                $recur[$ndt] = $arr1;
                            }
                        } else {
                            foreach ($rcr_pr as $rcr) {
                                $ndt = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                                $arr1['start_datetime'] = date('Y-m-d H:i:s', strtotime('+' . $i . ' days', strtotime($arr['start_datetime'])));
                                $arr1['end_datetime'] = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($arr['start_datetime']))) . date(' H:i:s', strtotime($arr['end_datetime']));
                                if ($rcr == strtolower(date('l', strtotime($ndt)))) {
                                    $recur[$ndt] = $arr1;
                                }
                            }
                        }
                    }
                }
            } else if ($this->input->post('recur') == 'monthly') {
                for ($i = 1; $i <= 12; $i++) {
                    $cur_date = date('Y-m-d', strtotime('+' . $i . ' months', strtotime($arr['start_datetime'])));
                    if ($cur_date > date('Y-m-d', strtotime($arr['end_datetime'])) && $this->input->post('recur_end') != '1') {
                        break;
                    }
                    $dt = date('Y-m-', strtotime($arr['start_datetime'])) . '1';
                    $daysInMonth = date('d', strtotime($arr['start_datetime']));
                    $daysInCurrentMonth = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime('+' . $i . ' months', strtotime($dt))), date('Y', strtotime('+' . $i . ' months', strtotime($dt))));
                    if ($daysInMonth > $daysInCurrentMonth) {
                        $dy = date('l', strtotime($arr['start_datetime']));
                        $ndt = date('Y-m-', strtotime('+ ' . $i . ' months', strtotime($dt))) . date('d', strtotime("last " . $dy . " of " . date('F Y', strtotime('+' . $i . ' months', strtotime($dt)))));
                        $arr1['start_datetime'] = date('Y-m-', strtotime('+ ' . $i . ' months', strtotime($dt))) . date('d', strtotime("last " . $dy . " of " . date('F Y', strtotime('+' . $i . ' months', strtotime($dt)))));
                        $arr1['end_datetime'] = date('Y-m-', strtotime('+ ' . $i . ' months', strtotime($dt))) . date('d', strtotime("last " . $dy . " of " . date('F Y', strtotime('+' . $i . ' months', strtotime($dt))))) . date(' H:i:s', strtotime($arr['end_datetime']));
                    } else {
                        $ndt = date('Y-m-', strtotime('+ ' . $i . ' months', strtotime($arr['start_datetime']))) . date('d', strtotime($arr['start_datetime']));
                        $arr1['start_datetime'] = date('Y-m-', strtotime('+ ' . $i . ' months', strtotime($arr['start_datetime']))) . date('d H:i:s', strtotime($arr['start_datetime']));
                        $arr1['end_datetime'] = date('Y-m-', strtotime('+ ' . $i . ' months', strtotime($arr['end_datetime']))) . date('d', strtotime($arr['end_datetime'])) . date(' H:i:s', strtotime($arr['end_datetime']));
                    }
                    $recur[$ndt] = $arr1;
                }
            } else if ($this->input->post('recur') == 'yearly') {
                $ndt = date('Y-m-d', strtotime('+1 year', strtotime($arr1['start_datetime'])));
                $arr1['start_datetime'] = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($arr1['start_datetime'])));
                $arr1['end_datetime'] = date('Y-m-d', strtotime('+1 year', strtotime($arr1['start_datetime']))) . date(' H:i:s', strtotime($arr['end_datetime']));
                $recur[$ndt] = $arr1;
            }

            if (!empty($recur)) {
                $this->admin_m->insertEventRecurrence($recur, $parent);
            }
        }
        redirect('admin/customer_detail/' . $this->input->post('user_id'));
    }

    public function customer_edit($user_id = null) {
        if (!$this->checkValidAccess('customers') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->setNav('admin/nav_v', 2);
        $this->_mainData['customer'] = $this->admin_m->getCustomerDetail($user_id);
        $this->renderMasterPage('admin/master_v', 'admin/customer/edit_v', $this->_mainData);
    }

    public function update_customer($user_id = null) {
        if (!$this->checkValidAccess('customers') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        // if( $user_id == null ) {
        // redirect('admin/customers');
        // }

        if ($this->input->post()) {
            if ($user_id == null) {
                // $sbmtArr = $this->input->post();
                // $billing_id = $this->admin_m->getUserBillingId($user_id);
                // $sbmtArr['id'] = $billing_id;
                // $updated = $this->admin_m->updateCustomerInfo( $sbmtArr );
                $post = $this->input->post();

                //if( $this->checkValidAccess('user_specific_customers') ) {
                $post['created_by'] = $_SESSION['userRecord']['id'];
                //}

                $updated = $this->admin_m->createNewCustomer($post);
                redirect('admin/customers/');
            } else {
                $sbmtArr = $this->input->post();
                $billing_id = $this->admin_m->getUserBillingId($user_id);
                $sbmtArr['id'] = $billing_id;

                $updated = $this->admin_m->updateCustomerInfo($sbmtArr);
                redirect('admin/customers/');
            }
        }
    }

    public function export_customer() {
        //getAllCustomersExcel
        $this->load->model('reporting_m');
        $csv = $this->reporting_m->getAllCustomersExcel();
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=customers.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv;
        exit;
    }

    //Employee's listing on admin side
    public function employees() {
        if (!$this->checkValidAccess('employees') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $arr = $this->input->post();
        $filter = array();
        if ($arr['search'] != '') {
            $filter = array('search' => $arr['search']);
        }

        $this->setNav('admin/nav_v', 3);
        $filter['user_type'] = 'employee';
        $this->_mainData['employees'] = $this->admin_m->getAllCustomers($filter);
        $this->renderMasterPage('admin/master_v', 'admin/employee/list_v', $this->_mainData);
    }

    public function employee_edit($user_id = null) {
        if (!$this->checkValidAccess('employees') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($this->input->post()) {
            $post = $this->input->post();
            if ($user_id == null) {
                $updated = $this->admin_m->createNewEmployee($post);
            } else {
                $post['id'] = $user_id;
                $post['billing_id'] = $this->admin_m->getUserBillingId($user_id);
                $updated = $this->admin_m->updateEmployeeInfo($post);
            }
            redirect('admin/employees');
        }

        $this->setNav('admin/nav_v', 3);
        $this->_mainData['employee'] = $this->admin_m->getCustomerDetail($user_id, true);
        $this->renderMasterPage('admin/master_v', 'admin/employee/edit_v', $this->_mainData);
    }

    public function employee_delete($user_id = null) {
        if (!$this->checkValidAccess('employees') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($user_id == null) {
            redirect('admin/employees');
        }
        $employee = $this->admin_m->getCustomerDetail($user_id, true);
        if ($employee && $employee['user_type'] == 'employee') {
            $this->admin_m->deleteEmployee($user_id);
            redirect('admin/employees');
        }
        redirect('admin/employees');
    }

    public function removeZeroInventory() {
        $this->load->model('parts_m');
        $this->parts_m->removeFinishedInventory();
    }

    public function mInventory() {
        if (!$this->checkValidAccess('mInventory') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        //$this->load->helper('singularize');
        //$this->load->model('parts_m');
        $filter = null;
        if (isset($_POST) && !empty($_POST)) {
            $filter[] = $this->input->post('name');
        }
        //echo '<pre>';
        //print_r($filter);
        //echo '</pre>';
        //$filter[] = rtrim($this->input->post('name'),'s');
        //$arr = explode(' ', $this->input->post('name'));
        //foreach($arr as $k => $v) {
        //	$filter[] = $v;
        //	$filter[] = rtrim($v,'s');
        //}
        $this->_mainData['productListTable'] = $this->generateAdPdtListTableMotorcycle('category.display_page', 'ASC', 1, $filter, $cat);

        // Pagination
        // $this->_mainData['pages'] = $this->generateAdPdtListTableMotorcycle($this->admin_m->getProductCount());
        $this->_mainData['currentPage'] = 1;
        $this->_mainData['display_pages'] = $this->_pagination;
        $this->_mainData['pagination'] = $this->load->view('admin/pagination/product_list_v', $this->_mainData, TRUE);

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/list_v', $this->_mainData);
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


        if ($this->validateMotorcycle() === TRUE) {
            $id = $this->admin_m->updateMotorcycle($id, $this->input->post());
            redirect('admin/motorcycle_edit/' . $id . '/updated');
        } else {
            $this->motorcycle_edit($id);
        }

        curl_request_async();
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
        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/motorcycle/images_v', $this->_mainData);
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

    public function credit_applications() {
        if (!$this->checkValidAccess('finance') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->_mainData['applications'] = $this->admin_m->getCreditApplications();

        $this->setNav('admin/nav_v', 5);
        $this->renderMasterPage('admin/master_v', 'admin/finance/list_v', $this->_mainData);
    }

    public function finance_pdf($id = null) {
        if (!$this->checkValidAccess('finance') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        // set up PDF Helper files
        $this->load->helper('fpdf_view');
        $parameters = array();
        pdf_init('reporting/poreport.php');

        // Send Variables to PDF
        //update process date and process user info
        $parameters['credit'] = $this->admin_m->getCreditApplication($id);
        $fileName = 'CreditApplication_' . time() . '.pdf';

        // echo "<pre>";
        // print_r($parameters);exit;
        // echo "</pre>";
        // Create PDF
        $this->PDF->setParametersArray($parameters);
        $this->PDF->runApplication();
        $this->PDF->Output($fileName, 'D'); // I
    }

    public function finance_edit($id = null) {
        if (!$this->checkValidAccess('finance') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (!empty($_POST) && @$_POST) {
            $post = $_POST;
            $data = array();
            $data['initial'] = $post['initial'];
            $data['type'] = $post['type'];
            $data['condition'] = $post['condition'];
            $data['year'] = $post['year'];
            $data['make'] = $post['make'];
            $data['model'] = $post['model'];
            $data['down_payment'] = $post['down_payment'];
            $data['first_name'] = $post['fname'];
            $data['last_name'] = $post['lname'];
            $data['driver_licence'] = $post['dl'];
            $data['email'] = $post['email'];
            $data['application_status'] = $post['application_status'];
            $data['contact_info'] = json_encode($post['contact_info']);
            $data['physical_address'] = json_encode($post['physical_address']);
            $data['housing_info'] = json_encode($post['housing_info']);
            $data['banking_info'] = json_encode($post['banking_info']);
            $data['previous_add'] = json_encode($post['previous_add']);
            $data['employer_info'] = json_encode($post['employer_info']);
            $data['reference'] = json_encode($post['reference']);
            $this->admin_m->update_finance($id, $data);
            $this->_mainData['success'] = TRUE;
        }

        $this->_mainData['states'] = $this->load_states();
        $this->_mainData['application'] = $this->admin_m->getCreditApplication($id);
        $this->_mainData['id'] = $id;
        $this->setNav('admin/nav_v', 5);
        $this->renderMasterPage('admin/master_v', 'admin/finance/edit_v', $this->_mainData);
    }

    public function finance_delete($id = null) {
        if (!$this->checkValidAccess('finance') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($id != null) {
            $this->admin_m->delete_finance($id);
        }
        redirect('admin/credit_applications');
    }

    public function finance_print($id = null) {
        if (!$this->checkValidAccess('finance') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->_mainData['application'] = $this->admin_m->getCreditApplication($id);
        $this->setNav('admin/nav_v', 5);
        $this->renderMasterPage('admin/master_v_blank', 'admin/finance/print_v', $this->_mainData);
    }

    public function create_order($newPartNumber = NULL, $qty = 1) {
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->model('order_m');
        $orderId = $this->order_m->createNewOrderByAdmin();
        redirect('admin/order_edit/'.$orderId);
        exit;
        $this->createMonths();
        $this->createYears();
        $this->_mainData['states'] = $this->load_states();
        $this->_mainData['provinces'] = $this->load_provinces();
        $this->loadCountries();
        $this->_mainData['distributors'] = $this->order_m->getDistributors();

        if (!is_null($newPartNumber)) {
            $this->load->model('parts_m');
            $part = $this->order_m->getPartIdByPartNumber($newPartNumber);
            $questAns = $this->parts_m->getQuestionAnswerByNumber($part['part_id'], $part['partnumber']);
            if( @$questAns ) {
                //$stock_code = $this->order_m->getPartVariationDetails($part['partnumber_id']);
                $post['display_name'] = $part['name'];//.' |||' . $questAns['question'] . ' :: ' . $questAns['answer'] . '||';
                $post['question'] = $questAns['question'];
                $post['answer'] = $questAns['answer'];
                $post['part_id'] = $part['part_id'];
                $post['partnumber_id'] = $part['partnumber_id'];
                $post['qty'] = $qty;
                $post['stock_code'] = $part['stock_code'];
                $post['price'] = $questAns['dealer_sale'] > 0 ? $questAns['dealer_sale'] : $questAns['sale'];
                $post['sale'] = ($questAns['dealer_sale'] > 0 ? $questAns['dealer_sale'] : $questAns['sale'])*$qty;
                $post['partnumber'] = $newPartNumber;

                //$_SESSION['admin_cart'][$newPartNumber] = $post;
            }
            //$this->order_m->addProductToOrderNew($newPartNumber, $id, $qty);
        }

        if ($id != 'new') {
            $this->_mainData['order'] = $this->order_m->getOrder($id);
        }
        
        $this->order_m->getDealerAndDistributorRec($_SESSION['admin_cart']);

        $this->setNav('admin/nav_v', 3);
        $this->renderMasterPage('admin/master_v', 'admin/order/create_v', $this->_mainData);
    }

}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
