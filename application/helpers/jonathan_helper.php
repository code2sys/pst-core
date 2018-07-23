<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/16/17
 * Time: 10:01 AM
 */


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
            $active_primary_navigation[$i]["subnavigation"] = $CI->parts_m->getCategories($active_primary_navigation[$i]["category_id"]);
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
            mustache_tmpl_iterate($template, "each_page");
            mustache_tmpl_set($template, "each_page", array(
                "label" => $p['label'],
                "target" => ($p['type'] == 'External Link') ? 'target="_blank"' : '',
                "link" => ($p['type'] == 'External Link') ? $p['external_url'] : site_url('pages/index/' . $p['tag'])
            ));
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

