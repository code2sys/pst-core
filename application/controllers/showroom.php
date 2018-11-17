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
        if (FALSE === getCRSStructure()) {
            header("Location: " . base_url(""));
            exit();
        }
        global $PSTAPI;
        initializePSTAPI();
        $this->load->model("Showcasemodel");
    }

    // show the Showroom Landing Page
    public function index($pageTag = NULL) {
        global $PSTAPI;
        $pages = $PSTAPI->pages()->fetch(array(
            "page_class" => $this->Showcasemodel->_pageType_main(),
            "active" => 1
        ));

        $this->_sub_sub_findPage($pages, "");
    }

    protected function _sub_sub_findPage($pages, $redirect_url) {
        if (count($pages) > 0) {
            parent::index($pages[0]->get("tag"));
        } else {
            header("Location: " . base_url($redirect_url));
            exit();
        }
    }

    protected function _sub_FetchByURLTitle($factory, $url_title) {
        global $PSTAPI;
        $matches = $PSTAPI->$factory()->fetch(array(
            "url_title" => $url_title,
            "deleted" => 0
        ));

        return count($matches) > 0 ? $matches[0] : false;
    }

    protected function _subFindPage($factory, $url_title, $fallback_url) {
        global $PSTAPI;
        $match = $this->_sub_FetchByURLTitle($factory, $url_title);

        if (FALSE === $match) {
            header("Location: " . base_url($fallback_url));
            exit();
        } else {
            $page_id = $match->get("page_id");

            if ($page_id > 0) {
                $this->_sub_sub_findPage(array($PSTAPI->pages()->get($page_id)), $fallback_url);
            } else {
                header("Location: " . base_url($fallback_url));
                exit();
            }
        }
    }

    public function make($make_name) {
        $this->_subFindPage("showcasemake", $make_name, "Factory_Showroom");
    }

    protected function _sub_RedirectIfOneChild($factory, $url_title, $id_column, $child_factory, $prefix_segment = "") {
        global $PSTAPI;
        $match = $this->_sub_FetchByURLTitle($factory, $url_title);
        if (FALSE !== $match) {
            // OK, so what do we have?
            $child_matches = $PSTAPI->$child_factory()->fetch(array(
                $id_column => $match->id(),
                "deleted" => 0
            ));

            if (count($child_matches) == 1) {
                // redirect it!
                header("Location: " . site_url($prefix_segment . "/" . $child_matches[0]->get("url_title")));
                exit();
            }
        }
    }

    public function machinetype($make_name, $machine_type_name) {
        // Check for children, and intercept if required...
        $base_url = "Factory_Showroom/$make_name";
        $this->_sub_RedirectIfOneChild("showcasemachinetype", $machine_type_name, "showcasemachinetype_id", "showcasemachinetype", $base_url . "/" . $machine_type_name);
        $this->_subFindPage("showcasemachinetype", $machine_type_name, $base_url);
    }

    public function model($make_name, $machine_type_name, $model_name) {
        // Check for children, and intercept if required
        $base_url = "Factory_Showroom/$make_name/$machine_type_name";
        $this->_sub_RedirectIfOneChild("showcasemodel", $machine_type_name, "showcasemodel_id", "showcasetrim", $base_url . "/" . $model_name);
        $this->_subFindPage("showcasemodel", $model_name, $base_url);
    }

    public function trim($make_name, $machine_type_name, $model_name, $trim_name) {
        $this->_subFindPage("showcasetrim", $trim_name, "Factory_Showroom/$make_name/$machine_type_name/$model_name");
    }

}