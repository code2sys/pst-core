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

    $_SESSION["categoryStack"][] = $category_id;

    $CI =& get_instance();
    $CI->load->model("parts_m");
    $_SESSION["categoryNames"][$category_id] = $CI->parts_m->getCategoryLongName($category_id);

    while (count($_SESSION["categoryStack"]) > 20) {
        array_shift($_SESSION["categoryStack"]);
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
function jprint_interactive_footer($pages) {

    if (!isset($pages) || is_null($pages) || !is_array($pages)) {
        // Well, if there is nothing here, then let's go get the real deal.
        $CI =& get_instance();
        $CI->load->model('pages_m');
        $pages = $CI->pages_m->getPages(1, 'footer');
    }

    ?>
<div class="one-fifth">
    <?php
    if (is_array($pages) && count($pages) > 0) {
        ?>
    <h3>quick links</h3>
    <ul class="clear">

        <?php
        foreach ($pages as $p) {
        ?>
        <li><a href="<?php echo site_url('pages/index/' . $p['tag']); ?>"><?php echo $p['label']; ?></a></li>
            <?php
        }
        ?>
    </ul>
        <?php
    }
    ?>
</div>
    <?php
}



