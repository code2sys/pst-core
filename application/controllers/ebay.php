<?php

//
//// Report all errors
//error_reporting(E_ALL);
//ini_set("error_reporting", E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);

function pr($data) {
    echo "<pre><h4 style='border:1px solid black;'>";
    print_r($data);
    echo "</pre></h4>";
}

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

class Ebay extends Master_Controller {

    public $headers = array();
    public $cred = array();
    public $serverUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
    private $compatibility_level = 849;
    private $call;
    private $siteID = 0;
    private $all = array();
    private $store_url = '';
    private $boundary;
    private $related_images = array();
    private $check_header_type_image = false; //check for header type
    public $call_from_cron = false;
    public $item_id;
    public $current_product_id;
    private $product_data = array();

    function __construct() {
        parent::__construct();
        $this->load->helpers('url');
        $this->load->model('reporting_m');
        $this->load->model('ebay_m');
    }

    public function home($saveXml = false) {
        $data = $this->ebay_m->ebayListings(0, 3, 1);

        $ebay_format_data = $this->convertToEbayFormat($data);

        $categories = $this->ebay_m->getcategories();

        $this->buildXmlAndHitEbay($ebay_format_data, 0, $saveXml);
    }

//    public function ebay_feeds() {
//        ini_set('memory_limit', '999M');
//
//        if (!$this->checkValidAccess('data_feeds') && !@$_SESSION['userRecord']['admin']) {
//            redirect('');
//        }
//
//        $uploadData = $this->input->post();
//        if (!empty($uploadData)) {
//
//            $file = dirname(__DIR__) . '/ebayFeeds/csvfile.xml';
////            echo dirname(__DIR__);exit;
//            $this->load->model('ebay_m');
//            $csv = $this->ebay_m->ebayListings();
//            $csv_handler = fopen($file, 'w');
//            // JLB 10-07-16
//            fwrite($csv_handler, $csv);
//            foreach ($csv as $c) {
//                fputcsv($csv_handler, $c);
//            }
//            fclose($csv_handler);
//            $data = array('run_by' => 'admin', 'status' => '1');
//            $this->admin_m->update_feed_log($data);
//        }
//        $this->_mainData['cycletrader_feeds'] = $this->admin_m->get_cycletrader_feed_log();
//        $this->_mainData['craglist_feeds'] = $this->admin_m->get_craglist_feed_log();
//        $this->_mainData['feed'] = $this->admin_m->get_feed_log();
//        $this->renderMasterPage('admin/master_v', 'admin/feed_v', $this->_mainData);
//    }
}
