<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/16/17
 * Time: 10:01 AM
 */

function convert_to_normal_text($text) {

    $normal_characters = "a-zA-Z0-9\s`~!@#$%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]";
    $normal_text = preg_replace("/[^$normal_characters]/", '', $text);

    return $normal_text;
}

function jonathan_saveCategoryToStack($category_id) {
    if (!array_key_exists("categoryStack", $_SESSION)) {
        $_SESSION["categoryStack"] = array();
    }
    if (!array_key_exists("categoryNames", $_SESSION)) {
        $_SESSION["categoryNames"] = array();
    }

    array_unshift($_SESSION["categoryStack"], $category_id);

    $CI =& get_instance();
    $CI->load->model("parts_m");
    $_SESSION["categoryNames"][$category_id] = $CI->parts_m->getCategoryLongName($category_id);

    while (count($_SESSION["categoryStack"]) > 20) {
        array_pop($_SESSION["categoryStack"]);
    }
}

function jonathan_getCategoryStack() {
    return array_key_exists("categoryStack", $_SESSION) ? $_SESSION["categoryStack"] : array();
}

function jonathan_getCategoryNames() {
    return array_key_exists("categoryNames", $_SESSION) ? $_SESSION["categoryNames"] : array();
}

function jonathan_prepareGlobalPrimaryNavigation() {
    $CI =& get_instance();
    $CI->load->model("Primarynavigation_m");
    $active_primary_navigation = $CI->Primarynavigation_m->getPrimaryNavigation(true);

    for ($i = 0; $i < count($active_primary_navigation); $i++) {
        $active_primary_navigation[$i]["external_attr"] = $active_primary_navigation[$i]["external"] > 0 ? " target='_blank' " : "";
        $active_primary_navigation[$i]["mobile_label"] = $active_primary_navigation[$i]["mobile_label"] != "" ? $active_primary_navigation[$i]["mobile_label"] : $active_primary_navigation[$i]["label"];

        if ($active_primary_navigation[$i]["category_id"] > 0 && defined('COMPUTE_EXTENDED_NAVIGATION') && COMPUTE_EXTENDED_NAVIGATION) {
            $CI->load->model("parts_m");
            $active_primary_navigation[$i]["subnavigation"] = array_values($CI->parts_m->getCategories($active_primary_navigation[$i]["category_id"]));
            $active_primary_navigation[$i]["subnav_rendered"] = $CI->load->view("master/widgets/nav_categories", array(
                "nav_categories" => $active_primary_navigation[$i]["subnavigation"]
            ), true);
            if (count($active_primary_navigation[$i]["subnavigation"]) > 0) {
                for ($j = 0; $j < count($active_primary_navigation[$i]["subnavigation"]); $j++) {
                    if (array_key_exists("subcats", $active_primary_navigation[$i]["subnavigation"][$j])) {
                        $active_primary_navigation[$i]["subnavigation"][$j]["subcats"] = array_values($active_primary_navigation[$i]["subnavigation"][$j]["subcats"]);
                    }
                }
            }
        }
    }

    return $active_primary_navigation;
}


if (!function_exists("jonathan_extract_float_value")) {
    function jonathan_extract_float_value($string) {
        $multiplier = 1;
        if ($string[0] == "-" || $string[0] == "(") {
            $multiplier = -1;
        }
        $string = preg_replace("/[^0-9\.]/", "", $string);
        return $multiplier * floatVal($string);
    }
}

/*
 * we need a function to escape things for likes.
 */
if (!function_exists("jonathan_escape_for_likes")) {
    function jonathan_escape_for_likes($s, $e) {
        return str_replace(array($e, '_', '%'), array($e.$e, $e.'_', $e.'%'), $s);
    }
}

if (!function_exists("jonathan_generate_likes")) {
    function jonathan_generate_likes($columns, $filter, $leader = "WHERE ") {
        $query = "";
        if (!is_null($filter) && $filter != "") {
            $tokens = preg_split("/[\s,.;]+/", $filter);

            for ($i = 0; $i < count($tokens); $i++) {
                $t = trim($tokens[$i]);
                if ($t != "") {
                    $t = jonathan_escape_for_likes($t, "=");
                    $token_bits = array();
                    for ($j = 0; $j < count($columns); $j++) {
                        $token_bits[] = $columns[$j] . " LIKE '%" . $t . "%' ESCAPE '='";
                    }
                    $query_bits[] = " ( " . implode( " OR ", $token_bits ) . " ) ";
                }
            }

            if (count($query_bits) > 0) {
                $query = " $leader (" . implode(" AND ", $query_bits) . " ) ";
            }
        }

        return $query;
    }
}

function secure_site_url($url) {
    return str_replace("http:", "https:", site_url($url));
}

if (!function_exists("jsite_url")) {
    function jsite_url($segment, $force_secure = false) {
        if ($segment != "" && $segment[0] != "/") {
            $segment = "/" . $segment;
        }
        if ($force_secure) {
            return "https://" . WEBSITE_HOSTNAME . $segment;
        } else {
            return ( isset($_SERVER['HTTPS']) ) ? ("https://" . WEBSITE_HOSTNAME . $segment) : ("http://" . WEBSITE_HOSTNAME . $segment);
        }
    }
}

if (!function_exists("joverride_viewpiece")) {
    function joverride_viewpiece($viewpiece, $require_instead = false, $params = array()) {
        $filename = STORE_DIRECTORY . "/overrides/" . $viewpiece;
        if (file_exists($filename)) {
            if ($require_instead) {
                require($filename);
                return TRUE;
            } else {
                return file_get_contents($filename);
            }
        } else {
            return FALSE;
        }
    }
}

if (!function_exists("sub_googleSalesXMLNew")) {
    function sub_googleSalesXMLNew() {
        $CI =& get_instance();
        $file = STORE_DIRECTORY . '/googleFeed/csvfile.csv';
        //echo dirname(__DIR__);exit;

        $CI->load->model('reporting_m');
        $csv_handler = fopen($file, 'w');
        $CI->reporting_m->getProductsForGoogle($csv_handler);
        fclose($csv_handler);
        $data = array('run_by' => 'cron', 'status' => '1');
        $CI->load->model('admin_m');
        $CI->admin_m->update_feed_log($data);
    }
}

// We need to print out the footer without always relying on the same code copied-and-pasted everywhere.
function jprint_interactive_footer($pages = null, $output = true) {
    $CI =& get_instance();

    if (!isset($pages) || is_null($pages) || !is_array($pages)) {
        // Well, if there is nothing here, then let's go get the real deal.
        $CI->load->model('pages_m');
        $pages = $CI->pages_m->getPages(1, 'footer');
    }
    $CI->load->helper("mustache_helper");
    $template = mustache_tmpl_open("jprint_interactive_footer.html");

    if (is_array($pages) && count($pages) > 0) {
        mustache_tmpl_set($template, "pages", 1);
        foreach ($pages as $p) {
            if (!in_array($p["page_class"], array("Showroom Model", "Showroom Trim", "Showroom Make", "Showroom Machine Type"))) {
                mustache_tmpl_iterate($template, "each_page");
                mustache_tmpl_set($template, "each_page", array(
                    "label" => $p['label'],
                    "target" => ($p['type'] == 'External Link') ? 'target="_blank"' : '',
                    "link" => ($p['type'] == 'External Link') ? $p['external_url'] : site_url('pages/index/' . $p['tag'])
                ));
            }

        }
    }

    if ($output) {
        echo mustache_tmpl_parse($template);
    } else {
        return mustache_tmpl_parse($template);
    }
}

// for the blocks
function jget_store_block($block_name) {
    $filename = STORE_DIRECTORY . "/store_block_" .     $block_name;
    if (file_exists($filename)) {
        return file_get_contents($filename);
    } else {
        return "";
    }
}


// serves a file
function jserve_file($source_file_path, $filename, $mime_type) {
    header('Content-Description: File Transfer');
    header("Content-Type: " . $mime_type);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($source_file_path));
    header('Content-Disposition: attachment; filename="' . str_replace("'", "",$filename) . '"');
    flush();
    readfile($source_file_path);
}


/*
    This initializes the API object...
 */

function initializePSTAPI() {
    global $PSTAPI;

    if (!isset($PSTAPI)) {
        // we have to make a PDO object...
        $dbh = new PDO("mysql:dbname=" . DATABASE_NAME . ";host=" . DATABASE_HOSTNAME, DATABASE_USER, DATABASE_PASS);

        // then make it!
        $PSTAPI = new PST\API($dbh);
    }

}


// http://stackoverflow.com/questions/2916232/call-to-undefined-function-apache-request-headers
if( !function_exists('apache_request_headers') ) {
///
    function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach($_SERVER as $key => $val) {
            if( preg_match($rx_http, $key) ) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', $arh_key);
                if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                    foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                    $arh_key = implode('-', $rx_matches);
                }
                $arh[$arh_key] = $val;
            }
        }
        return( $arh );
    }
///
}
///

if (!function_exists('getallheaders')) {
    function getallheaders() {
        return apache_Request_headers();
    }
}

/*
 * Kyle wanted the store hours several places, so here we go.
 */

function jtemplate_add_store_hours(&$template, $store_name = null) {
    if (is_null($store_name)) {
        $CI =& get_instance();
        $CI->load->model("admin_m");
        $store_name = $CI->admin_m->getAdminShippingProfile();
    }

    $store_name["free_form_hours"] == ($store_name["free_form_hours"] > 0) && $store_name["free_form_hour_blob"] != "";
    $store_name["store_hours_defined"] = !$store_name["free_form_hours"] && ($store_name["monday_hours"] != "" || $store_name["tuesday_hours"] != "" || $store_name["wednesday_hours"] != "" || $store_name["thursday_hours"] != "" || $store_name["friday_hours"] != "" || $store_name["saturday_hours"] != "" || $store_name["sunday_hours"] != "" || trim($store_name["hours_note"]) != "");

    foreach (array("monday_hours", "tuesday_hours", "wednesday_hours", "thursday_hours", "friday_hours", "saturday_hours", "sunday_hours", "hours_note") as $k) {
        $store_name[$k] = trim($store_name[$k]);
        if ($store_name[$k] == "") {
            $store_name[$k] = false;
        }
    }

    foreach (array(
                "free_form_hours" => "store_hours_use_free_form",
                "free_form_hour_blob" => "store_hours_free_form_blob",
                "monday_hours" => "store_hours_monday",
                "tuesday_hours" => "store_hours_tuesday",
                "wednesday_hours" => "store_hours_wednesday",
                "thursday_hours" => "store_hours_thursday",
                "friday_hours" => "store_hours_friday",
                "saturday_hours" => "store_hours_saturday",
                "sunday_hours" => "store_hours_sunday",
                "hours_note" => "store_hours_note",
        "store_hours_defined" => "store_hours_defined",

             ) as $k => $v) {
        mustache_tmpl_set($template, $v, $store_name[$k]);
    }

}


/*
 * There was no enforcement of recaptcha...
 *
 * TODO: We really should do something with the error codes
 * https://developers.google.com/recaptcha/docs/verify#error-code-reference
 * https://github.com/google/recaptcha/blob/master/examples/example-captcha.php
 *
 */

function jverifyRecaptcha($response = null) {
    if (is_null($response)) {
        $response = array_key_exists('g-recaptcha-response', $_REQUEST) ? $_REQUEST['g-recaptcha-response'] : "";
    }


    $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET);
    $resp = $recaptcha->verify($response, $_SERVER['REMOTE_ADDR']);
    if ($resp->isSuccess()) {
        return true;
    } else {
        return false;
    }
}



// JLB: I moved this from product_band_v
if (!function_exists('tag_creating')) {
    function tag_creating($url)
    {
        $url = str_replace(array(' - ', ' '), '-', $url);
        $url = preg_replace('~[^\\pL0-9_-]+~u', '', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_-]+~', '', $url);
        return $url;
    }
}

// JLB - need to not display the SKU if it's a complex SKU
function clean_complex_sku($motorcycle) {
    $sku = $motorcycle["sku"];
    $real_sku = $motorcycle["real_sku"];
    if ($real_sku != "" && $real_sku == substr($sku, 0, strlen($real_sku))) {
        return $real_sku;
    }
    return $sku;
}

function normalize_incoming_make($make) {
    $normalize_makes = array(
        "can-am™" => "CAN-AM",
        "canam" => "CAN-AM",
        "can am" => "CAN-AM",
        "ski doo" => "Ski-Doo",
        "skidoo" => "Ski-Doo",
        "seadoo" => "Sea-Doo",
        "sea doo" => "Sea-Doo",
        "artic cat" => "ARCTIC CAT"
    );

    if (array_key_exists(trim(strtolower($make)), $normalize_makes)) {
        $make = $normalize_makes[trim(strtolower($make))];
    }

    return $make;
}

// Rob - to check it has unit or not
function isMajorUnitShop() {
    $isMajorUnitShop = false;
    if ( 
            (defined('MOTORCYCLE_SHOP_NEW') && MOTORCYCLE_SHOP_NEW)
        ||  (defined('MOTORCYCLE_SHOP_USED') && MOTORCYCLE_SHOP_USED) 
        ||  (!defined('MOTORCYCLE_SHOP_DISABLE') || !MOTORCYCLE_SHOP_DISABLE) 
    ) {
        $isMajorUnitShop = true;
    }

    return $isMajorUnitShop;
}

// There is a global structure with CRS that, if present, means they also get the showcase.
function getCRSStructure() {
    // is there a CRS configuration file?
    $filename = "/var/www/crs_configs/" . STORE_NAME;

    if (file_exists($filename)) {
        $crs_struct = json_decode(file_get_contents($filename), true);
        return $crs_struct;

    } else {
        return FALSE;
    }
}

// This is used to generate the description uniformly from CRS wherever it might come in.
function generateCRSDescription($title, $description) {
    return "<div class='description_from_crs'>" . $title . "<br/><br/>" . $description . "</div>";
}

// Fix the CRS bike
function fixCRSBike(&$motorcycle, $exclude_title = false, $exclude_description = false) {
    $motorcycle_id = $motorcycle->id();
    $CI =& get_instance();
    $CI->load->model("CRS_m");
    $crs_trim = $CI->CRS_m->getTrim($motorcycle->get("crs_trim_id"));
    $denormalize = false;

    if ($motorcycle->get("customer_set_title") == 0 && !$exclude_title) {
        // OK, go get that trim display name...
        $motorcycle->set("title", $motorcycle->get("year") . " " . $motorcycle->get("make") . " " . convert_to_normal_text($crs_trim[0]["display_name"]));
        $motorcycle->save();
        $denormalize = true;
    }

    // should we attempt to set the description?
    if ($crs_trim[0]["description"] != "" && $motorcycle->get("customer_set_description") == 0 && $motorcycle->get("lightspeed_set_description") == 0 && !$exclude_description) {
        $motorcycle->set("description", generateCRSDescription( $motorcycle->get("title"), $crs_trim[0]["description"] ));
        $motorcycle->save();
        $denormalize = true;
    }

    if ($denormalize) {
        global $PSTAPI;
        initializePSTAPI();
        $PSTAPI->denormalizedmotorcycle()->moveMotorcycle($motorcycle_id);
    }

    // we should run the cleanup queries...
    $CI->load->model("CRSCron_m");
    $CI->CRSCron_m->cleanUpCRS();
}

function figureShowcaseFlags($pageRec, &$display_makes, &$display_machine_types, &$display_models, &$display_trims, &$showcasemake_id, &$showcasemodel_id, &$showcasemachinetype_id, &$full_url, &$showcasemakes, &$showcasemodels, &$showcasetrims, &$showcasemachinetypes) {


    global $PSTAPI;
    initializePSTAPI();

    $title = $pageRec["title"];

    switch ($pageRec["page_class"]) {
        case "Showroom Landing Page":
            $display_makes = true;
            $showcasemakes = $PSTAPI->showcasemake()->fetch(array(
                "deleted" => 0
            ));
            break;

        case "Showroom Make":
            // are the machine types?
            $showcasemakes = $PSTAPI->showcasemake()->fetch(array(
                "page_id" => $pageRec["id"],
                "deleted" => 0
            ));

            if (count($showcasemakes) > 0) {
                $showcasemake = $showcasemakes[0];
                $full_url = $showcasemake->get("full_url");
                $showcasemake_id = $showcasemake->id();

                // machinetypes;
                $showcasemachinetypes = $PSTAPI->showcasemachinetype()->fetch(array(
                    "showcasemake_id" => $showcasemake->id(),
                    "deleted" => 0
                ));

                if (count($showcasemachinetypes) == 1) {
                    $showcasemodels = $PSTAPI->showcasemodel()->fetch(array(
                        "showcasemachinetype_id" => $showcasemachinetypes[0]->id(),
                        "deleted" => 0
                    ));
                    $display_models = true;
                } else {
                    // display it.
                    $display_machine_types = true;
                }
            }

            break;

        case "Showroom Machine Type":
            // OK, we have a MAKE in hand.
            $showcasemachinetypes = $PSTAPI->showcasemachinetype()->fetch(array(
                "page_id" => $pageRec["id"],
                "deleted" => 0
            ));

            if (count($showcasemachinetypes) > 0) {
                $showcasemachinetype = $showcasemachinetypes[0];
                $full_url = $showcasemachinetype->get("full_url");
                $showcasemachinetype_id = $showcasemachinetype->id();

                $showcasemodels = $PSTAPI->showcasemodel()->fetch(array(
                    "showcasemachinetype_id" => $showcasemachinetype_id,
                    "deleted" => 0
                ));
                $display_models = true;
            }

            break;

        case "Showroom Model":
            $showcasemodels = $PSTAPI->showcasemodel()->fetch(array(
                "page_id" => $pageRec["id"],
                "deleted" => 0
            ));

            if (count($showcasemodels) > 0) {
                $showcasemodel = $showcasemodels[0];
                $showcasemodel_id = $showcasemodel->id();
                $full_url = $showcasemodel->get("full_url");

                $showcasetrims = $PSTAPI->showcasetrim()->fetch(array(
                    "showcasemodel_id" => $showcasemodel_id,
                    "deleted" => 0
                ));
                $display_trims = true;
            }
            break;
    }

    if ($display_models) {
        // we have to convert these to trims...
        $showcasetrims = array();



        foreach ($showcasemodels as $scm) {
            $new_trims = $PSTAPI->showcasetrim()->fetch(array(
                "showcasemodel_id" => $scm->get("showcasemodel_id"),
                "deleted" => 0
            ));

            foreach ($new_trims as &$nt) {
                $nt->set("year", $scm->get("year"));
            }

            $showcasetrims = array_merge($showcasetrims, $new_trims);
        }

        $display_models = false;
        $display_trims = true;
    }
}

// JLB: I put this here as I expect we'll be putting in an LB in the future...which will require looking at headers differently.
function returnClientIP() {
    return $_SERVER["REMOTE_ADDR"];
}

// Sometimes, you just need to gronify a filename
function gronifyForFilename($val) {
    return preg_replace("/[^a-z0-9\.\-]+/i", '_', $val);
}

// I don't want to keep rendering this. I want to save it.
global $social_link_buttons_rendered;
function getSocialLinkButtons() {
    global $social_link_buttons_rendered;

    if (isset($social_link_buttons_rendered) && !is_null($social_link_buttons_rendered)) {
        return $social_link_buttons_rendered;
    }

    $CI =& get_instance();
    $CI->load->model("admin_m");
    $SMSettings = $CI->admin_m->getSMSettings();
    $social_link_buttons_rendered = $CI->load->view("social_link_buttons", array(
        "SMSettings" => $SMSettings
    ), true);

    return $social_link_buttons_rendered;
}
