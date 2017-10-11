<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 10/11/17
 * Time: 10:58 AM
 */

require_once("admin.php");

class Adminnavigation extends Admin
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Primarynavigation_m");
        $this->load->model("Statusmodel");
        if (!$this->checkValidAccess('navigation') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
            exit();
        }
    }

    /*
     * The purpose of this is to generate
     */
    public function index() {

    }

    protected function _isValid($primarynavigation_id) {

    }

    public function ajax_active($primarynavigation_id) {

    }

    public function ajax_inactive($primarynavigation_id) {

    }

    // for now, require this to be the custom type...
    public function ajax_remove($primarynavigation_id) {

    }

    public function ajax_save($primarynavigation_id) {

    }
}