<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:24 AM
 */

require_once(__DIR__ . "/financeadmin.php");

abstract class Customeradmin extends Financeadmin {

    /*
        This whole section is for the default pricing rules; we will probably dual-purpose these for customers, too.
     */

    public function ajax_customer_pricing_tier_add($user_id, $pricingtier_id) {
        if (!$this->checkValidAccess('customers') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if (!ENABLE_CUSTOMER_PRICING) {
            $this->redirect("/"); exit();
        }

        $this->load->model("Statusmodel");
        global $PSTAPI;
        initializePSTAPI();
        $model = $PSTAPI->customerpricingtier()->add(array(
            "user_id" => $user_id,
            "pricingtier_id" => $pricingtier_id
        ));
        $this->Statusmodel->setData("model", $model->to_array());
        $this->Statusmodel->setSuccess("Added successfully.");
        $this->Statusmodel->outputStatus();
    }

    public function ajax_customer_pricing_tier_remove($customerpricingtier_id) {
        if (!$this->checkValidAccess('customers') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if (!ENABLE_CUSTOMER_PRICING) {
            $this->redirect("/"); exit();
        }

        $this->load->model("Statusmodel");
        global $PSTAPI;
        initializePSTAPI();
        $model = $PSTAPI->customerpricingtier()->remove($customerpricingtier_id);
        $this->Statusmodel->setSuccess("Removed successfully.");
        $this->Statusmodel->outputStatus();
    }

    public function customer_pricing_defaults() {
        if (!$this->checkValidAccess('customers') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if (!ENABLE_CUSTOMER_PRICING) {
            $this->redirect("/"); exit();
        }

        $this->setNav('admin/nav_v', 2);
        $this->renderMasterPage('admin/master_v', 'admin/customer/customer_pricing_defaults_v', $this->_mainData);
    }

    // This should return an error string if there is one, that is human intelligible, or "" if none.
    protected function _isValidCustomerPricing_settings($pricing_rule, $amount) {
        if (!in_array($pricing_rule, array("Cost+", "Retail-", "PcntMgn"))) {
            return "Unrecognized pricing rule: " . $pricing_rule;
        }

        // Now, we have to figure out the range
        switch($pricing_rule) {
            case "Retail-":
            if ($amount > 1 || $amount < 0) {
                return "Sorry, please specify between 0% and 100%.";
            }
            break;

            case "PcntMgn":
            if ($amount > 1 || $amount < 0) {
                return "Sorry, please specify between 0% and 100%.";
            }
            break;
        }
        

        return "";
    }

    public function ajax_customer_pricing_update($customerpricing_id, $user_id = null) {
        $this->sub_ajax_customer_pricing_save($user_id, $customerpricing_id, true);
    }

    public function ajax_customer_pricing_add($user_id = null) {
        $this->sub_ajax_customer_pricing_save($user_id, null, false);
    }

    public function ajax_customer_pricing_remove($customerpricing_id, $user_id = null) {
        $this->load->model("Statusmodel");
        global $PSTAPI;
        initializePSTAPI();
        $PSTAPI->customerpricing()->remove($customerpricing_id);
        $this->Statusmodel->setSuccess("Pricing rule removed.");
        $this->Statusmodel->outputStatus();
    }

    protected function sub_percentage_to_amount($pricing_rule, $percentage) {
        $percentage = preg_replace("/[^0-9\.]/", "", $percentage);
        $percentage = floatVal($percentage);
        $amount = $percentage / 100.0;
        if ($pricing_rule == "Cost+") {
            $amount = 1 + $amount;
        }
        return $amount;
    }

    protected function sub_ajax_customer_pricing_save($user_id, $customerpricing_id, $update = false) {
        $this->load->model("Statusmodel");
        global $PSTAPI;
        initializePSTAPI();

        $pricing_rule = array_key_exists("pricing_rule", $_REQUEST) ? $_REQUEST["pricing_rule"] : "";
        $percentage = array_key_exists("percentage", $_REQUEST) ? $_REQUEST["percentage"] : 0;
        $distributor_id = array_key_exists("distributor_id", $_REQUEST) ? $_REQUEST["distributor_id"] : null;
        $pricing_tier = array_key_exists("pricing_tier", $_REQUEST) ? $_REQUEST["pricing_tier"] : null;
        $pricingtier_id = null;
        $amount = $this->sub_percentage_to_amount($pricing_rule, $percentage);
        $this->Statusmodel->setData("percentage", $percentage);
        $this->Statusmodel->setData("amount", $amount);

        $error = $this->_isValidCustomerPricing_settings($pricing_rule, $amount);

        if ($error == "") {
            if (is_null($user_id)) {
                // we have to fetch a pricing tier...
                if ($pricing_tier == "") {
                    $error = "Please specify a pricing tier.";
                } else {
                    $pricingtier = $PSTAPI->pricingtier()->fetch(array("name" => $pricing_tier));
                    if (count($pricingtier) > 0) {
                        $pricingtier_id = $pricingtier[0]->id(); // get the ID value.
                        // better fix it in case they changed the spelling somehow
                        $PSTAPI->pricingtier()->update($pricingtier_id, array("name" => $pricing_tier));
                    } else {
                        $pricingtier = $PSTAPI->pricingtier()->add(array("name" => $pricing_tier));
                        $pricingtier_id = $pricingtier->id();
                    }
                }
            }
        }

        if ($error == "") {
            // Is this distributor OK?
            $distributor = $PSTAPI->distributor()->get($distributor_id);

            if ($distributor_id > 0 && is_null($distributor)) {
                $error = "Sorry, that distributor is not recognized.";
            } else {
                if ($distributor_id == 0) {
                    $distributor_id = null;
                }

                // OK, we need to ensure there aren't rules for this user already.
                $matches = $PSTAPI->customerpricing()->fetch(array(
                    "distributor_id" => $distributor_id,
                    "user_id" => $user_id, 
                    "pricingtier_id" => $pricingtier_id
                ));

                if (count($matches) > 0 && $matches[0]->id() != $customerpricing_id) {
                    // this is also an error
                    $error = "Sorry, there is already a rule for this distributor.";
                }
            }
        }

        if ($error != "") {
            $this->Statusmodel->setError($error);
        } else {
            if ($update) {
                // update it
                $model = $PSTAPI->customerpricing()->update($customerpricing_id, array(
                    "amount" => $amount,
                    "pricing_rule" => $pricing_rule,
                    "distributor_id" => $distributor_id,
                    "pricingtier_id" => $pricingtier_id
                ));
                $this->Statusmodel->setData("model", $model->to_array());
            } else{
                $pricing_rule = $PSTAPI->customerpricing()->add(array(
                    "pricing_rule" => $pricing_rule,
                    "amount" => $amount,
                    "user_id" => $user_id,
                    "distributor_id" => $distributor_id,
                    "pricingtier_id" => $pricingtier_id
                ));
                $this->Statusmodel->setData("model", $pricing_rule->to_array());
                $this->Statusmodel->setSuccess("Rule added successfully.");
            }


            // record success
            $this->Statusmodel->setSuccess("Rule updated successfully.");
        }

        $this->Statusmodel->outputStatus();
    }

    public function __construct() {
        parent::__construct();

        if (!defined('ENABLE_CUSTOMER_PRICING')) {
            define('ENABLE_CUSTOMER_PRICING', true);
        }
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

    public function load_customer_rec($page = 0) {
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
        } else if ($this->checkValidAccess('all_user_specific_customers')) {
            $filter['custom'] = 'all_own';
        } else if ($this->checkValidAccess('service_customers')) {
            $filter['custom'] = 'service';
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
            $employee = $v['employee'];
            if ($v['created_by'] == -1) {
                $employee = 'SERVICE';
            }
            $data[] = array($v['first_name'] . ' ' . $v['last_name'], $v['phone'], $v['email'], $v['orders'], $employee, $attention, $str);
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
        $this->_mainData['sales_persons'] = $this->admin_m->getAllCustomers(array(
            'user_type' => 'employee',
            'employee_type' => array('sales_person', 'lead_manager')
        ));
        $this->renderMasterPage('admin/master_v', 'admin/customer/view_v', $this->_mainData);
    }

    public function ajax_save_notes($customer_id, $note_id = NULL) {
        $note = $_POST['note'];
        $this->load->model('user_note_m');
        if (is_null($note_id)) {
            $note_id = $this->user_note_m->addNote($customer_id, $note, $_SESSION['userRecord']['id']);
        } else {
            $note_id = $this->user_note_m->updateNote($note_id, $note);
        }
        if ($note_id == FALSE) {
            print json_encode(array('result'=>FALSE));
        } else {
            $note = $this->user_note_m->getNote($note_id);
            if ($note !== FALSE) {
                print json_encode(array('result'=>TRUE, 'note' => $note));
            } else {
                print json_encode(array('result'=>TRUE));
            }
        }
    }

    public function ajax_delete_note($note_id) {
        $this->load->model('user_note_m');
        $this->user_note_m->deleteNote($note_id);
        print json_encode(array('result'=>TRUE));
    }

    public function ajax_get_customer_notes($customer_id) {
        $this->load->model('user_note_m');
        $notes = $this->user_note_m->getNotes($customer_id);
        print json_encode(array('count' => count($notes), 'notes' => $notes));
    }

    public function ajax_assign_employee_to_customer() {
        $result = array(
            "success" => false,
            "success_message" => "",
            "error_message" => ""
        );
        if (!$this->checkValidAccess('customers') && !@$_SESSION['userRecord']['admin']) {
            $result["error_message"] = "Access Forbidden";
            print json_encode($result);
            return;
        }

        $employee_id = $_POST['employee'];
        $customer_id = $_POST['customer'];

        if ($this->admin_m->assignEmployeeToCustomer($employee_id, $customer_id) == FALSE) {
            $result['error_message'] = "Invalid request";
            print json_encode($result);
        } else {
            $result['success'] = true;
            print json_encode($result);
        }
    }

    public function ajax_get_open_activities($customer_id = NULL) {
        $length = array_key_exists("length", $_REQUEST) ? $_REQUEST["length"] : 10;
        $start = array_key_exists("start", $_REQUEST) ? $_REQUEST["start"] : 0;
        $filter['custom'] = 'all';
        if ($this->checkValidAccess('all_customers')) {
            $filter['custom'] = 'all';
        } else if ($this->checkValidAccess('web_customers')) {
            $filter['custom'] = 'web';
        } else if ($this->checkValidAccess('user_specific_customers')) {
            $filter['custom'] = 'own';
        } else if ($this->checkValidAccess('all_user_specific_customers')) {
            $filter['custom'] = 'all_own';
        } else if ($this->checkValidAccess('service_customers')) {
            $filter['custom'] = 'service';
        }
        list($activities, $total_count, $filtered_count) = $this->admin_m->getOpenReminders($customer_id, $length, $start, $filter);
        $rows = array();
        foreach ($activities as $activity) {
            $owner = $activity['owner_first_name'].' '.$activity['owner_last_name'];
            if ($activity['owner_id'] == -1) {
                $owner = 'SERVICE';
            }
            $row = array(
                '<a class="pointer activity" data-id="'.$activity['id'].'" data-date="'.date('Y-m-d', strtotime($activity['start_datetime'])).'">'.$activity['subject'].'</a>',
                $activity['start_datetime'],
                $activity['end_datetime'],
                $owner,
                $activity['modified_on'],
            );
            if (is_null($customer_id)) {
                $row[] = '<a class="pointer customer" href="'. base_url('admin/customer_detail/' . $activity['customer_id']).'">'.$activity['customer_first_name'].' '.$activity['customer_last_name'].'</a>';
            }
            $rows[] = $row;
        }
        print json_encode(array(
            "data" => $rows,
            "draw" => array_key_exists("draw", $_REQUEST) ? $_REQUEST["draw"] : 0,
            "recordsTotal" => $total_count,
            "recordsFiltered" => $filtered_count,
            "limit" => $length,
            "offset" => $start,
            "order_string" => $order_string,
            "search" => ''
        ));
    }

    public function ajax_get_closed_activities($customer_id) {
        $length = array_key_exists("length", $_REQUEST) ? $_REQUEST["length"] : 10;
        $start = array_key_exists("start", $_REQUEST) ? $_REQUEST["start"] : 0;
        list($activities, $total_count, $filtered_count) = $this->admin_m->getClosedReminders($customer_id, $length, $start);
        $rows = array();
        foreach ($activities as $activity) {
            $row = array(
                '<a class="pointer activity" data-id="'.$activity['id'].'" data-date="'.date('Y-m-d', strtotime($activity['start_datetime'])).'">'.$activity['subject'].'</a>',
                $activity['start_datetime'],
                $activity['end_datetime'],
                $activity['completed_first_name'].' '.$activity['completed_last_name'].'('.$activity['completed_username'].')',
                $activity['completed_on']
            );
            $rows[] = $row;
        }
        print json_encode(array(
            "data" => $rows,
            "draw" => array_key_exists("draw", $_REQUEST) ? $_REQUEST["draw"] : 0,
            "recordsTotal" => $total_count,
            "recordsFiltered" => $filtered_count,
            "limit" => $length,
            "offset" => $start,
            "order_string" => $order_string,
            "search" => ''
        ));
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
        $this->_mainData['dateReminder'] = isset($_POST['dt']) ? $_POST['dt'] : date('Y-m-d');
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
            $arr['modified_on'] = date('Y-m-d H:i:s');

            if ($id != null) {
                $arr['id'] = $id;
            } else {
                $arr['created_on'] = date('Y-m-d H:i:s');
                $arr['created_by'] = $_SESSION['userRecord']['id'];
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

                if( $this->checkValidAccess('service_customers') ) {
                    $post['created_by'] = -1;
                }

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

}
