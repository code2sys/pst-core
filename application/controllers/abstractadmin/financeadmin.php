<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:21 AM
 */

require_once(__DIR__ . "/motorcycleadmin.php");

abstract class Financeadmin extends Motorcycleadmin {

    public function credit_applications() {
        if (!$this->checkValidAccess('finance') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
//        $this->_mainData['applications'] = $this->admin_m->getCreditApplications();

        $this->setNav('admin/nav_v', 5);
        $this->renderMasterPage('admin/master_v', 'admin/finance/list_v', $this->_mainData);
    }

    public function credit_applications_ajax() {
        if (!$this->checkValidAccess('finance') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        $columns = array(
            "joint",
            "first_name",
            "last_name",
            "email",
            "phone",
            "co_first_name",
            "co_last_name",
            "co_email",
            "co_phone",
            "year",
            "make",
            "model",
            "application_status",
            "application_date"
        );

        $length = array_key_exists("length", $_REQUEST) ? $_REQUEST["length"] : 500;
        $start = array_key_exists("start", $_REQUEST) ? $_REQUEST["start"] : 0;

        $order_string = "order by application_date desc ";

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
        list($products, $total_count, $filtered_count) = $this->admin_m->enhancedGetCreditApplications($s = (array_key_exists("search", $_REQUEST) && array_key_exists("value", $_REQUEST["search"]) ? $_REQUEST["search"]["value"] : ""), $order_string, $length, $start);

        // Now, order them...
        $rows = array();
        foreach ($products as $p) {
            $contact_info = json_decode($p["contact_info"]);
            if ($p['joint'] > 0) {
                $co_phone = json_decode($p["co_contact_info"])->rphone;
            } else {
                $co_phone = "";
            }

            $rows[] = array(
                $p['joint'] > 0 ? 'Joint' : 'Individual',
                $p['first_name'], $p['last_name'], $p['email'], $contact_info->rphone,
                $p['co_first_name'], $p['co_last_name'], $p['co_email'], $co_phone,
                $p['year'], $p['make'], $p['model'],
                $p['application_status'], $p['application_date'],

                '<a href="/admin/finance_edit/' . $p['id'] . '"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a>' .
                ' | <a href="/admin/finance_delete/' . $p['id'] .'" onclick="return confirm(\'Are you sure you would like to delete this credit application\')"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a>'

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
            $data['joint'] = $post['joint'];
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
            $data['co_first_name'] = $post['co_fname'];
            $data['co_last_name'] = $post['co_lname'];
            $data['co_driver_licence'] = $post['co_dl'];
            $data['co_email'] = $post['co_email'];
            $data['application_status'] = $post['application_status'];
            $data['contact_info'] = json_encode($post['contact_info']);
            $data['physical_address'] = json_encode($post['physical_address']);
            $data['housing_info'] = json_encode($post['housing_info']);
            $data['banking_info'] = json_encode($post['banking_info']);
            $data['previous_add'] = json_encode($post['previous_add']);
            $data['employer_info'] = json_encode($post['employer_info']);
            $data['co_contact_info'] = json_encode($post['co_contact_info']);
            $data['co_physical_address'] = json_encode($post['co_physical_address']);
            $data['co_housing_info'] = json_encode($post['co_housing_info']);
            $data['co_banking_info'] = json_encode($post['co_banking_info']);
            $data['co_previous_add'] = json_encode($post['co_previous_add']);
            $data['co_employer_info'] = json_encode($post['co_employer_info']);
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
}