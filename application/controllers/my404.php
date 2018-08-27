<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 8/27/18
 * Time: 4:54 PM
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

class My404 extends Master_Controller
{

    public function index() {
        $this->output->set_status_header("404");
        $this->renderMasterPage("master/master_v_new", 'my404', $this->_mainData);
    }

}