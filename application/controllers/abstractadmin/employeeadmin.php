<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:24 AM
 */


require_once(__DIR__ . "/orderadmin.php");

abstract class Employeeadmin extends Lightspeedconsole {


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

}