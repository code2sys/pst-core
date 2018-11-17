<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 11/17/18
 * Time: 6:00 AM
 */


class Showcasemodel extends CI_Model {

    public function __construct()
    {
            parent::__construct();
            global $PSTAPI;
            initializePSTAPI();
            $CI =& get_instance();
            $CI->load->model("CRS_m");
    }


    public function _default_main_title() {
        return "Factory Showroom";
    }
    public function _pageType_main() {
        return "Showroom Landing Page";
    }
    public function _pageType_make() {
        return "Showroom Make";
    }
    public function _pageType_machinetype() {
        return "Showroom Machine Type";
    }
    public function _pageType_model() {
        return "Showroom Model";
    }
    public function _pageType_trim() {
        return "Showroom Trim";
    }

    /*
     * Sub-steps...
     */

    protected function _ensureShowcaseLandingPage() {
        global $PSTAPI;
        $pages = $PSTAPI->pages()->fetch(array(
            "page_class" => $this->_pageType_main()
        ));

        if (count($pages) == 0) {
            // we have to add the page...
            $PSTAPI->pages()->add(array(
                "label" => $this->_default_main_title(),
                "title" => $this->_default_main_title(),
                "active" => 1,
                "delete" => 0,
                "page_class" => $this->_pageType_main()
            ));
        } else {
            // we just have to make sure it's active.
            if ($pages[0]->get("active") == 0) {
                $pages[0]->set("active", 1);
                $pages[0]->save();
            }
        }
    }

    // this should also mark it as updated IF it is not deleted.
    protected function _ensureMakePage($crs_make_id) {
        $this->_subSimpleEnsurePage("showcasemake", "crs_make_id", $crs_make_id, $this->_pageType_make());
    }

    protected function _subSimpleEnsurePage($factory, $key_field, $value, $type) {
        global $PSTAPI;
        $make = $PSTAPI->$factory()->fetch(array(
            $key_field => $value
        ));

        if (count($make) == 0) {
            return;
        }
        $make = $make[0];

        if ($make->get("deleted") == 0) {
            $make->set("updated", 1);
            $make->save();

            // Now, find a page...
            if ($make->get("page_id") > 0) {
                $page = $PSTAPI->pages()->get($make->get("page_id"));
                if ($page->get("active") == 0) {
                    $page->set("active", 1);
                    $page->save();
                }
            } else {
                // make the page...
                $page = $PSTAPI->pages()->add(array(
                    "label" => $make->get("title"),
                    "title" => $make->get("title"),
                    "active" => 1,
                    "delete" => 0,
                    "page_class" => $type
                ));
                $make->set("page_id", $page->id());
                $make->save();
            }
        }
    }

    // this should also mark it as updated IF it is not deleted.
    protected function _ensureMachineTypePage($crs_machinetype) {
        $this->_subSimpleEnsurePage("showcasemachinetype", "crs_machinetype", $crs_machinetype, $this->_pageType_machinetype());
    }

    // this should also mark it as updated IF it is not deleted
    protected function _ensureModelPage($crs_model_id) {
        $this->_subSimpleEnsurePage("showcasemodel", "crs_model_id", $crs_model_id, $this->_pageType_model());
    }

    // this should mark it as updated IF it is not deleted.
    protected function _ensureTrimPage($crs_trim_id) {
        $this->_subSimpleEnsurePage("showcasetrim", "crs_trim_id", $crs_trim_id, $this->_pageType_trim());
    }

    // Return true if this is added at the end of it..
    protected function _addUpdateTrim($crs_machinetype, $crs_make_id, $year, $trim_structure) {
        // You have to make sure there's an entry for the make, and an entry for the make, machine type, and model as well...
        print_r($trim_structure);

    }

    protected function _addProductLine($crs_machinetype, $crs_make_id, $year) {
        $CI =& get_instance();

        $matching_motorcycles = $this->CRS_m->getTrims(array(
            "year" => $year,
            "make_id" => $crs_make_id,
            "machine_type" => $crs_machinetype
        ));

        if (count($matching_motorcycles) == 0) {
            return;
        }

        $contains_one_valid_bike = false;

        foreach ($matching_motorcycles as $m) {

            // JLB 11-15-18 - I added a more complicated approval function. This may be redundant.
            $crs_make = $m["make"];
            $crs_display_name = $m["display_name"];
            if (function_exists("CRSApproveFunction") && !CRSApproveFunction($crs_make, $crs_display_name)) {
                continue; // skip it; we are not doing this bike.
            }

            // OK, that should handle updating this record.
            $contains_one_valid_bike = $this->_addUpdateTrim($crs_machinetype, $crs_make_id, $year, $m) || $contains_one_valid_bike;
        }

        if ($contains_one_valid_bike) {
            // Then, we have to ensure there is a MAKE page and a MODEL page.
            $this->_ensureMakePage($crs_make_id);
            $this->_ensureMachineTypePage($crs_machinetype);
        }

        return $contains_one_valid_bike;
    }

    protected function _disablePages() {
        global $PSTAPI;

        // we need to deactivate the pages...
        foreach (array(
                     $this->_pageType_machinetype(),
                     $this->_pageType_main(),
                     $this->_pageType_make(),
                     $this->_pageType_model(),
                     $this->_pageType_trim()
                 ) as $page_class) {

            $pages = $PSTAPI->pages()->fetch(array(
                "page_class" => $page_class
            ));

            foreach ($pages as $p) {
                $p->set("active", 0);
                $p->save();
            }
        }
    }

    public function loadShowcase() {
        global $PSTAPI;


        $crs_structure = getCRSStructure();

        if ($crs_structure !== FALSE && is_array($crs_structure) && count($crs_structure) > 0) {
            $paged_factories = array(
                "showcasemachinetype", "showcasemake", "showcasemodel", "showcasetrim"
            );


            // clear the update flags;
            foreach ($paged_factories as $f) {
                $PSTAPI->$f()->updateWhere(array(
                    "updated" => 0
                ), array());
            }

            // OK now, this should represent a series of product lines to add...which is likely to create
            $contains_one_valid_bike = false;
            foreach ($crs_structure as $c) {
                $contains_one_valid_bike = $this->_addProductLine($c["crs_machinetype"], $c["crs_make_id"], $c["year"]) || $contains_one_valid_bike;
            }

            // Then, you have to disable them..
            foreach ($paged_factories as $f) {
                $unupdated = $PSTAPI->$f()->fetch(array(
                    "updated" => 0
                ));

                foreach ($unupdated as $u) {
                    $u->disable();
                }
            }

            // ensure that there is a landing page...
            if ($contains_one_valid_bike) {
                $this->_ensureShowcaseLandingPage();
            } else {
                $this->_disablePages();
            }

        } else {
            $this->_disablePages();
        }

    }


}
