<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 11/17/18
 * Time: 8:30 AM
 */

require_once("pages.php");

class Showroom extends Pages {

    public function __construct()
    {
        parent::__construct();

        // check that the showroom really works...if there is no showroom, bounce them out.
        if (FALSE === getCRSStructure) {
            header("Location: " . base_url(""));
            exit();
        }
        global $PSTAPI;
        initializePSTAPI();
        $this->load->model("Showcasemodel");
    }

    // show the Showroom Landing Page
    public function index() {
        global $PSTAPI;
        $pages = $PSTAPI->pages()->fetch(array(
            "page_class" => $this->Showcasemodel->_default_main_title()
        ));

        if (count($pages) > 0) {
            parent::index($pages[0]->get("tag"));
        } else {
            header("Location: " . base_url(""));
        }
    }

    public function make($make_name) {

    }

    public function machinetype($make_name, $machine_type_name) {

    }

    public function model($make_name, $machine_type_name, $model_name) {

    }

    public function trim($make_name, $machine_type_name, $model_name, $trim_name) {

    }

}