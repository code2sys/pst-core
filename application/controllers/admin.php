<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

require_once(__DIR__ . "/abstractadmin/finaladmin.php");

class Admin extends Finaladmin {

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

    protected function isConcrete() {
        return true;
    }


}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
