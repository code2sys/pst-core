<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:22 AM
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(__DIR__  . '/../Master_Controller.php');

abstract class Firstadmin extends Master_Controller
{

    /*
     * This exists entirely to make it so you can't call this directly.
     */
    abstract protected function isConcrete();

    protected function _printAjaxError($error_message) {
        print json_encode(array(
            "success" => false,
            "error_message" => $error_message
        ));
        exit();
    }

    protected function _printAjaxSuccess($data = array()) {
        print json_encode(array(
            "success" => true,
            "data" => $data
        ));
        exit();
    }


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


    /*     * ********************************************** IMAGE ************************************************ */

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


    /*     * ****************************************** VALIDATION ************************************************************ */







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


}

