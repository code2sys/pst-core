<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:39 AM
 */


require_once(__DIR__ . "/employeeadmin.php");

abstract class Individualpageadmin extends Employeeadmin
{


    protected function validateEditDistributor() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
        $this->form_validation->set_rules('username', 'Username', 'required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
        return $this->form_validation->run();
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
        $this->form_validation->set_rules('merchant_id', 'Merchant ID', 'null_min_length[16]|max_length[16]');
        $this->form_validation->set_rules('public_key', 'Public Key', 'null_min_length[16]|max_length[16]');
        $this->form_validation->set_rules('private_key', 'Private Key', 'null_min_length[32]|max_length[32]');
        return $this->form_validation->run();
    }


    public function profile() {
        if (!$this->checkValidAccess('profile') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($this->validateProfile() !== FALSE) { // Display Form
            $this->admin_m->updateAdminShippingProfile($this->input->post());
            $this->_mainData['success'] = TRUE;


            // We have to echo the image,if we get it....
            if (array_key_exists("favicon", $_FILES)) {
                // verify the mime type

                // generate a icon using the guidelines from here
                // https://stackoverflow.com/questions/35365867/limit-the-allowed-file-size-for-input-type-file-in-pure-html-no-js
            }

            if (array_key_exists("logo", $_FILES)) {
                // verify the mime type

                // generate a PNG

            }
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


}