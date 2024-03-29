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
            $p = $PSTAPI->pages()->add(array(
                "label" => $this->_default_main_title(),
                "title" => $this->_default_main_title(),
                "active" => 1,
                "delete" => 0,
                "page_class" => $this->_pageType_main(),
                "tag" => "factoryshowroom",
                "location" => "footer,comp_info"
            ));
            $p->fixShowcaseSegment();
        } else {
            // we just have to make sure it's active.
            if ($pages[0]->get("active") == 0) {
                $pages[0]->set("active", 1);
                $pages[0]->save();
            }
        }
    }

    // this should also mark it as updated IF it is not deleted.
    protected function _ensureMakePage($make) {
        $this->_subSimpleEnsurePage("showcasemake", "title", $make, $this->_pageType_make());
    }

    protected function _subSimpleEnsurePage($factory, $key_field, $value, $type) {
        global $PSTAPI;
        initializePSTAPI();

        $query_data = array();

        if (is_array($key_field)) {
            for ($i = 0; $i < count($key_field); $i++) {
                $query_data[$key_field[$i]] = $value[$i];
            }
        } else {
            $query_data[$key_field] = $value;
        }

        $make = $PSTAPI->$factory()->fetch($query_data);

        if (count($make) == 0) {
            return;
        }
        $make = $make[0];

        if ($make->get("deleted") == 0) {
            $PSTAPI->$factory()->update($make->id(), array("updated" => 1));
            $make->set("updated", 1);

            // Now, find a page...
            if ($make->get("page_id") > 0) {
                $page = $PSTAPI->pages()->get($make->get("page_id"));
                if ($page->get("active") == 0) {
                    $page->set("active", 1);
                    $page->save();
                }
            } else {
                $tag = preg_replace("/[^a-z0-9\-\_]+/", "_", strtolower($type . " " . $make->get("title")));

                // make the page...
                $page = $PSTAPI->pages()->add(array(
                    "label" => $make->get("title"),
                    "title" => $make->get("title"),
                    "active" => 1,
                    "delete" => 0,
                    "page_class" => $type,
                    "tag" => $tag
                ));
                $page->fixShowcaseSegment();
                $PSTAPI->$factory()->update($make->id(), array("page_id" => $page->id()));
            }
        }
    }

    // this should also mark it as updated IF it is not deleted.
    protected function _ensureMachineTypePage($crs_machinetype, $make_name) {
        global $PSTAPI;
        initializePSTAPI();
        $makes = $PSTAPI->showcasemake()->fetch(array(
            "title" => $make_name
        ));
        $showcasemake_id = count($makes) > 0 ? $makes[0]->id() : null;

        $this->_subSimpleEnsurePage("showcasemachinetype", array("crs_machinetype", "showcasemake_id"), array($crs_machinetype, $showcasemake_id), $this->_pageType_machinetype());
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
    protected function _addUpdateTrim($trim_structure) {
        global $PSTAPI;
        initializePSTAPI();

        // You have to make sure there's an entry for the make, and an entry for the make, machine type, and model as well...
        $showcasemake = $this->_assertMake($trim_structure["make"]);

        if ($showcasemake->get("deleted") > 0) {
            return false; // the make has been deleted!
        }

        // You have to make sure there's a machine type ID
        $showcasemachinetype = $this->_assertMachineType($trim_structure["machine_type"], $showcasemake->id());

        if ($showcasemachinetype->get("deleted") > 0) {
            return false;
        }

        // You have to then ensure there is a model
        $showcasemodel = $this->_assertModel($trim_structure["model_id"], $showcasemachinetype->id(), $showcasemake->id(), $trim_structure);

        if ($showcasemodel->get("deleted") > 0) {
            return false;
        }

        // You have to then make the trim...
        $showcasetrim = $this->_assertTrim($trim_structure, $showcasemodel->id());

        if ($showcasetrim->get("deleted") > 0) {
            return false;
        }

        // If you are still here, then you need to make sure your trim and model have page.s..
        $this->_ensureModelPage($trim_structure["model_id"]);
        $this->_ensureTrimPage($trim_structure["trim_id"]);

        // OK, now, so long as we are still rolling, you have to:
        // fill the specs
        // fill the photos
        // then, you'll have to tack back for thumbnails if they do not exist...

        $photo = $this->_fetchTrimPhotos($showcasetrim, $trim_structure);

        if ($photo !== FALSE) {

            // consider setting these..
            if (is_null($showcasetrim->get("thumbnail_photo")) || $showcasetrim->get("thumbnail_photo") == "") {
                // OK, we have to set it.
                $showcasetrim->set("thumbnail_photo", $photo);
                $PSTAPI->showcasetrim()->update($showcasetrim->id(), array("thumbnail_photo" => $photo));
            }
            // consider setting these..
            if (is_null($showcasemodel->get("thumbnail_photo")) || $showcasemodel->get("thumbnail_photo") == "") {
                // OK, we have to set it.
                $showcasemodel->set("thumbnail_photo", $photo);
                $PSTAPI->showcasemodel()->update($showcasemodel->id(), array("thumbnail_photo" => $photo));
            }
            if (is_null($showcasemachinetype->get("thumbnail_photo")) || $showcasemachinetype->get("thumbnail_photo") == "") {
                // OK, we have to set it.
                $showcasemachinetype->set("thumbnail_photo", $photo);
                $PSTAPI->showcasemachinetype()->update($showcasemachinetype->id(), array("thumbnail_photo" => $photo));
            }           
            if (is_null($showcasemake->get("thumbnail_photo")) || $showcasemake->get("thumbnail_photo") == "") {
                // OK, we have to set it.
                $showcasemake->set("thumbnail_photo", $photo);
                $PSTAPI->showcasemake()->update($showcasemake->id(), array("thumbnail_photo" => $photo));
            }
        }


        // Make sure there is a page - we'll handle the machine type and make higher up.
        $this->_ensureModelPage($trim_structure["model_id"]);
        $this->_ensureTrimPage($trim_structure["trim_id"]);

        // get its specs
        $this->_fetchTrimSpecs($showcasetrim, $trim_structure);

        return true;
    }

    /*
     * What does this need to do?
     * We have to replicate the specs as they would appear on a motorcycle.
     *
     */
    protected function _fetchTrimSpecs($showcasetrim, $trim_structure) {
        global $PSTAPI;
        initializePSTAPI();

        // Get the trim, specifically, which will include the specs..
        $this->load->model("CRS_m");

        // Then, iterate over specs, get the engine_type, transmission, retail_price, and category attributes. Category is just a string. At the same time, I believe we can run the code in parallel as it is on CRSCron_m to insert into spec group and the spec itself. We assume always a deep clean for PST-sourced ones?

        $attributes = $this->CRS_m->getTrimAttributes($trim_structure["trim_id"], 0);

        $existing_attributes = $PSTAPI->showcasespec()->fetch(array(
            "showcasetrim_id" => $showcasetrim->id()
        ));

        // might as well make a LUT for this as well.
        $existing_LUT = array();
        foreach ($existing_attributes as $ea) {
            $existing_LUT[$ea->get("crs_attribute_id")] = $ea;
        }
        $seen = array();

        foreach ($attributes as $a) {
            if (strtolower(trim($a["text_value"])) == "not available") {
                continue;
            }

            // You have to get the attribute group for it...
            $showcasespecgroup_id = $this->_getAttributeGroup($showcasetrim->id(), $a["attributegroup_name"], $a["attributegroup_number"]);

            $seen[$a["attribute_id"]] = true;
            $attribute_id = $a["attribute_id"];



            foreach (array(30003 => "engine_type", 40002 => "transmission", 20002 => "retail_price", 10011 => "category") as $id => $label) {
                if ($attribute_id == $id) {
                    if ($showcasetrim->get("customer_set_" . $label) == 0) {
                        if ($showcasetrim->get($label) != $a["text_value"]) {
                            $showcasetrim->set($label, $a["text_value"]);
                            $PSTAPI->showcasetrim()->update($showcasetrim->id(), array(
                                $label => $a["text_value"]
                            ));
                        }
                    }
                }
            }

            if (array_key_exists($a["attribute_id"], $existing_LUT)) {
                // we have to consider if an update is appropriate
                $m = $existing_LUT[$a["attribute_id"]];
                if ($m->get("override") == 0) {
                    if ($m->get("value") != $a["text_value"] || $m->get("final_value") != $a["text_value"] || $m->get("showcasespecgroup_id") != $showcasespecgroup_id) {
                        $m->set("value", $a["text_value"]);
                        $m->set("final_value", $a["text_value"]);
                        $m->set("showcasespecgroup_id", $showcasespecgroup_id);
                        $m->save();
                    }
                }
            } else {
                // you can just add it...
                $existing_LUT[$a["attribute_id"]] = $PSTAPI->showcasespec()->add(array(
                    "version_number" => $a["version_number"],
                    "value" => $a["text_value"],
                    "feature_name" => $a["feature_name"],
                    "attribute_name" => $a["label"],
                    "type" => $a["type"],
                    "external_package_id" => $a["package_id"],
                    "showcasetrim_id" => $showcasetrim->id(),
                    "final_value" => $a["text_value"],
                    "crs_attribute_id" => $a["attribute_id"],
                    "showcasespecgroup_id" => $showcasespecgroup_id,
                    "ordinal" => $a["attribute_id"] % 10000
                ));
            }

        }

        // now, you have to deep clean it.
        foreach ($existing_attributes as $ea) {
            if (!array_key_exists($ea->get("crs_attribute_id"), $seen)) {
                $ea->set("deleted", 1);
                $ea->save();
            }
        }
    }

    protected $motorcycle_attributegroups;
    protected function _getAttributeGroup($showcasetrim_id, $attributegroup_name, $attributegroup_number) {
        if (!isset($this->motorcycle_attributegroups)) {
            $this->motorcycle_attributegroups = array();
        }

        $key = $showcasetrim_id . "-" . $attributegroup_number;
        if (array_key_exists($key, $this->motorcycle_attributegroups)) {
            return $this->motorcycle_attributegroups[$key];
        }

        global $PSTAPI;
        initializePSTAPI();
        $matches = $PSTAPI->showcasespecgroup()->fetch(array(
            "showcasetrim_id" => $showcasetrim_id,
            "crs_attributegroup_number" => $attributegroup_number
        ));

        if (count($matches) == 0) {
            // you have to add one.
            $id = $PSTAPI->showcasespecgroup()->add(array(
                "showcasetrim_id" => $showcasetrim_id,
                "name" => $attributegroup_name,
                "ordinal" => $attributegroup_number,
                "crs_attributegroup_number" => $attributegroup_number
            ))->id();
        } else {
            $id = $matches[0]->id();
        }
        $this->motorcycle_attributegroups[$key] = $id;
        return $id;
    }

    // return a photo URL, or false if there isn't one. We'll use it to backfill the thumbnail photo.
    protected function _fetchTrimPhotos($showcasetrim, $trim_structure) {
        $one_good_photo_url = FALSE;

        global $PSTAPI;

        // get the current photos...
        $photos = $PSTAPI->showcasephoto()->fetch(array(
            "showcasetrim_id" => $showcasetrim->id()
        ));

        // make an LUT by URL
        $url_lut = array();
        foreach ($photos as $p) {
            $url_lut[$p->get("url")] = $p;
        }

        $seen_urls = array();

        // get the candidate photos
        $candidate_photos = $this->CRS_m->getTrimPhotos($showcasetrim->get("crs_trim_id"), 0);

        // load them
        foreach ($candidate_photos as $p) {
            $seen_urls[] = $p["photo_url"];
            if (!array_key_exists($p["photo_url"], $url_lut)) {
                // you have to add it..
                $PSTAPI->showcasephoto()->add(array(
                    "crs_photomap_id" => $p["photo_id"],
                    "showcasetrim_id" => $showcasetrim->id(),
                    "url" => $p["photo_url"]
                ));
            }
        }

        // Remove the junked ones...
        foreach ($photos as $p) {
            if (!in_array($p->get("url"), $seen_urls)) {
                $p->remove();
            }
        }

        // check them...
        $photos = $PSTAPI->showcasephoto()->fetch(array(
            "showcasetrim_id" => $showcasetrim->id(),
            "deleted" => 0
        ));

        if (count($photos) > 0) {
            $one_good_photo_url = $photos[0]->get("url");
        }

        // return one good photo URL...
        return $one_good_photo_url;
    }

    /*
     * The following make sure that these exist. Don't worry, you're going to ensure there's a page if you get this far.
     */
    protected $_makeMap;
    protected function _assertMake($make_name) {
        global $PSTAPI;
        initializePSTAPI();

        if (!isset($this->_makeMap)) {
            $makes = $PSTAPI->showcasemake()->fetch();
            $this->_makeMap = array();
            foreach ($makes as $m) {
                $this->_makeMap[$m->get("title")] = $m;
            }
        }

//        // OK, we have to make one, which means, we have to get the information about it.
//        if (!isset($this->_makeMap)) {
//            $this->_makeMap = array();
//            $makes = $this->CRS_m->getMakes();
//            foreach ($makes as $m) {
//                $make_id = intVal($m["make_id"]);
//                $this->_makeMap[$m["title"]] = $m;
//            }
//        }
//
//        $makes = $PSTAPI->showcasemake()->fetch(array(
//            "crs_make_id" => $crs_make_id
//        ));
//
//
//        $crs_make_id = intVal($crs_make_id);
//
//        if (!array_key_exists($crs_make_id, $this->_makeMap)) {
//            throw new \Exception("Could not find make in make map: " . $crs_make_id);
//        }

        // OK, now, it's in there, so we should be able to make this thing.
        if (!array_key_exists($make_name, $this->_makeMap)) {

            $this->_makeMap[$make_name] = $PSTAPI->showcasemake()->add(array(
                "make" => $make_name,
                "description" => "",
                "updated" => 1,
                "title" => $make_name,
                "short_title" => $make_name
            ));
        } else {
            $this->_makeMap[$make_name] = $PSTAPI->showcasemake()->get($this->_makeMap[$make_name]->id());
            $this->_makeMap[$make_name]->set("title", $make_name);
            if ($this->_makeMap[$make_name]->get("customer_set_short_title") == 0) {
                $this->_makeMap[$make_name]->set("short_title", $make_name);
            }
            $this->_makeMap[$make_name]->set("updated", 1);
            $this->_makeMap[$make_name]->save();
        }

        return $this->_makeMap[$make_name];
    }

    // The difference here: The machine types, although independent in CRS, are downstream from Make in the showroom.
    //
    protected function _assertMachineType($crs_machinetype, $showcasemake_id) {
        $map = array(
            "MOT" => "Motorcycles",
            "ATV" => "ATV",
            "UTV" => "UTV",
            "WAT" => "Watercraft",
            "SNO" => "Snowmobiles"
        );

        if (!array_key_exists($crs_machinetype, $map)) {
            throw new \Exception("Unrecognized machine type: $crs_machinetype");
        }

        global $PSTAPI;
        $machinetypes = $PSTAPI->showcasemachinetype()->fetch(array(
            "showcasemake_id" => $showcasemake_id,
            "crs_machinetype" => $crs_machinetype
        ));

        $showcasemake = $PSTAPI->showcasemake()->get($showcasemake_id);

        if (count($machinetypes) == 0) {
            // you have to make one.
            return $PSTAPI->showcasemachinetype()->add(array(
                "showcasemake_id" => $showcasemake_id,
                "crs_machinetype" => $crs_machinetype,
                "title" => $showcasemake->get("title") . " " . $map[$crs_machinetype],
                "short_title" => $map[$crs_machinetype]
            ));
        } else {
            $machinetypes[0]->set("title", $showcasemake->get("title") . " " . $map[$crs_machinetype]);
            if ($machinetypes[0]->get("customer_set_short_title") == 0) {
                $machinetypes[0]->set("short_title", $map[$crs_machinetype]);
            }
            $machinetypes[0]->set("updated", 1);
            $machinetypes[0]->save();
            return $machinetypes[0];
        }

    }

    protected function _assertModel($crs_model_id, $showcasemachinetype_id, $showcasemake_id, $trim_structure) {
        global $PSTAPI;
        $models = $PSTAPI->showcasemodel()->fetch(array(
            "crs_model_id" => $crs_model_id,
            "showcasemachinetype_id" => $showcasemachinetype_id
        ));

        $candidate_crs = $this->CRS_m->getModels(array(
            "make_id" => $trim_structure["make_id"],
            "machine_type" => $trim_structure["machine_type"]
        ));


        $candidate = array();
        $found_candidate = false;
        foreach ($candidate_crs as $c) {
            if ($c["model_id"] == $crs_model_id) {
                $candidate = $c;
                $found_candidate = true;
            }
        }

        if (!$found_candidate) {
            print_r($candidate_crs);
            throw new \Exception("Model could not be found: $crs_model_id");
        }

        $showcasemachinetype = $PSTAPI->showcasemachinetype()->get($showcasemachinetype_id);
        $showcasemake = $PSTAPI->showcasemake()->get($showcasemachinetype->get("showcasemake_id"));

        $update_array = array();
        if (count($models) == 0) {

            // we have to add a model
            $model = $PSTAPI->showcasemodel()->add(array(
                "showcasemake_id" =>$showcasemake_id,
                "year" => $candidate["year"],
                "crs_model_id" => $crs_model_id,
                "showcasemachinetype_id" => $showcasemachinetype_id,
                "title" => $candidate["year"] . " " . $showcasemake->get("title") . " " . $candidate["model"],
                "short_title" => $candidate["year"] . " " . $candidate["model"],
                "updated" => 1
            ));
        } else {
            $model = $models[0];
            $model->set("title", $candidate["year"] . " " . $showcasemake->get("title") . " " . $candidate["model"]);
            $update_array = array(
                "title" => $candidate["year"] . " " . $showcasemake->get("title") . " " . $candidate["model"]
            );

            if ($model->get("customer_set_short_title") == 0) {
                $model->set("short_title", $candidate["year"] . " " . $candidate["model"]);
                $update_array["short_title"] = $model->get("short_title");
            }

        }

        $update_array["updated"] = 1;
        $model->set("updated", 1);
        $PSTAPI->showcasemodel()->update($model->id(), $update_array);
        $this->_ensureModelPage($crs_model_id);
        return $model;
    }

    /*
     * Array
(
    [year] => 2019
    [version_number] => 201801003
    [model] => RZR XP® 4 Turbo S
    [model_id] => 86107
    [make] => Polaris
    [make_id] => 23
    [machine_type] => UTV
    [trim] => Base
    [display_name] => RZR XP® 4 Turbo S
    [description] => The Polaris RZR XP 4 Turbo S Base is a sport utility style utility vehicle with an MSRP of $30,499 and is new for 2019. Power is provided by a 4-Stroke, 925cc, Liquid cooled, DOHC, Parallel Twin engine with Electric starter. The engine is paired with transmission and total fuel capacity is 9.5 gallons. The RZR XP 4 Turbo S Base rides on Aluminum wheels with ITP Coyote 32 x 10-15 (8-Ply Rated) front tires and a ITP Coyote 32 x 10-15 (8-Ply Rated) rear tires. The front suspension is an Independent Double A-Arm while the rear suspension is an Independent. Front Hydraulic Disc brakes and rear Hydraulic Disc brakes provide stopping power. The RZR XP 4 Turbo S Base comes standard with a Bucket, 4-passenger seat.
    [trim_id] => 245980
    [msrp] => 30499.00
    [engine_type] => Parallel Twin
    [transmission] => Continuously Variable (CVT)
    [default_category] => Sport Utility
    [offroad] => 0
)
     */
    protected function _assertTrim($trim_structure, $showcasemodel_id) {
        global $PSTAPI;

        // OK, well, is there one for this trim?
        $trims = $PSTAPI->showcasetrim()->fetch(array(
            "crs_trim_id" => $trim_structure["trim_id"]
        ));
        $showcasemodel = $PSTAPI->showcasemodel()->get($showcasemodel_id);
        $showcasemachinetype = $PSTAPI->showcasemachinetype()->get($showcasemodel->get("showcasemachinetype_id"));
        $showcasemake = $PSTAPI->showcasemake()->get($showcasemachinetype->get("showcasemake_id"));

        $update_array = array();
        if (count($trims) == 0) {

            // OK, we have to add it...
            $trim = $PSTAPI->showcasetrim()->add(array(
                "title" => ($t = $showcasemodel->get("year") . " " . $showcasemake->get("title") . " " . $trim_structure["display_name"]),
                "short_title" => $trim_structure["display_name"],
                "description" => generateCRSDescription($t, $trim_structure["description"]),
                "crs_trim_id" => $trim_structure["trim_id"],
                "updated" => 1,
                "showcasemodel_id" => $showcasemodel_id
            ));
        } else {
            $trim = $trims[0];
            $trim->set("title", $showcasemodel->get("year") . " " . $showcasemake->get("title") . " " . $trim_structure["display_name"]);
            $update_array = array(
                "title" => $trim->get("title")
            );

            if ($trim->get("customer_set_short_title") == 0) {
                $trim->set("short_title", $trim_structure["display_name"]);
                $update_array["short_title"] = $trim->get("short_title");
            }

        }

        // We should make this a page!
        $trim->set("updated", 1);
        $update_array["updated"] = 1;
        $PSTAPI->showcasetrim()->update($trim->id(), $update_array);
        $this->_ensureTrimPage($trim_structure["trim_id"]);
        return $trim;
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

        $ensured_make = array();
        $ensured_machine_types = array();


        foreach ($matching_motorcycles as $m) {

            // JLB 11-15-18 - I added a more complicated approval function. This may be redundant.
            $crs_make = $m["make"];
            $crs_display_name = $m["display_name"];
            if (function_exists("CRSApproveFunction") && !CRSApproveFunction($crs_make, $crs_display_name)) {
                continue; // skip it; we are not doing this bike.
            }

            // OK, that should handle updating this record.
            if ($this->_addUpdateTrim($m)) {
                $contains_one_valid_bike = true;
                $key = $crs_machinetype . "-" .  $crs_make;
                if (!array_key_exists($key, $ensured_machine_types)) {
                    $this->_ensureMachineTypePage($crs_machinetype, $crs_make);
                    $ensured_machine_types[$key] = true;
                }
                if (!array_key_exists($crs_make, $ensured_make)) {
                    $ensured_make[$crs_make] = true;
                    $this->_ensureMakePage($crs_make);
                }
            }
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
        // Temporarily prevent the loading of the showcase.
        if (!defined('FACTORY_SHOWROOM') || !FACTORY_SHOWROOM) {
            return;
        }


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

                // fix the URLs
                $matches = $PSTAPI->$f()->fetch();
                foreach ($matches as $m) {
                    $m->fixURLTitle();
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
