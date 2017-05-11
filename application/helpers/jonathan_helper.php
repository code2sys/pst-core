<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/16/17
 * Time: 10:01 AM
 */

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
        if (file_exists(filename)) {
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
        $rows = $CI->reporting_m->getProductsForGoogle();
        $csv_handler = fopen($file, 'w');
        // JLB 10-07-16
        foreach ($rows as $row) {
            fputcsv($csv_handler, $row);
        }
        fclose($csv_handler);
        $data = array('run_by' => 'cron', 'status' => '1');
        $CI->load->model('admin_m');
        $CI->admin_m->update_feed_log($data);
    }
}
