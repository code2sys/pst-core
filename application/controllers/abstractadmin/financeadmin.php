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
}