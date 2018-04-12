<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

class Admin_Content extends Master_Controller {

    function __construct() {
        parent::__construct();
        //if(!@$_SESSION['userRecord']['admin'])
        //	redirect('welcome');
        if ($_SESSION['userRecord']['user_type'] == 'employee') {
            
        } else if (!@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->model('admin_m');
        $this->setNav('admin/nav_v', 1);
        //$this->output->enable_profiler(TRUE);
    }

    private function validateEmailSettingsForm() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('post', 'POST', 'required|xss_clean');
        $this->form_validation->set_rules('email_logo', 'Email Logo', 'xss_clean');
        $this->form_validation->set_rules('email_order_complete', 'Email Order Complete', 'xss_clean');
        $this->form_validation->set_rules('email_order_complete_attachment', 'Email Order Complete Attachment', 'xss_clean');
        $this->form_validation->set_rules('registration_email', 'Registration Email', 'xss_clean');
        $this->form_validation->set_rules('registration_email_text', 'Registration Email Text', 'xss_clean');
        $this->form_validation->set_rules('forgot_pass_email_text', 'Forgot Password Email Text', 'xss_clean');
        $this->form_validation->set_rules('mass_email_text', 'Mass Email Text', 'xss_clean');
        $this->form_validation->set_rules('mass_email_attachment', 'Mass Email Attachment', 'xss_clean');
        $this->form_validation->set_rules('mass_email_attachment_doc', 'Mass Email Attachment Document', 'xss_clean');
        $this->form_validation->set_rules('mass_email_list', 'Mass Email List', 'xss_clean');
        $this->form_validation->set_rules('mass_email_list_doc', 'Mass Email List Doc', 'xss_clean');
        return $this->form_validation->run();
    }

    private function validatePages() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('active', 'Active', 'xss_clean');
        return $this->form_validation->run();
    }

    private function validateSliderImageSettingsForm() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('page', 'Page', 'required|numeric|xss_clean');
        return $this->form_validation->run();
    }

    private function validateSocialMedia() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('sm_fblink', 'Facebook Link', 'trim|max_length[256]|prep_url|xss_clean');
        $this->form_validation->set_rules('sm_twlink', 'Twitter Link', 'trim|max_length[256]|prep_url|xss_clean');
        $this->form_validation->set_rules('sm_blglink', 'Blog Link', 'trim|max_length[256]|prep_url|xss_clean');
        $this->form_validation->set_rules('sm_ytlink', 'YouTube Link', 'trim|max_length[256]|prep_url|xss_clean');
        $this->form_validation->set_rules('sm_gplink', 'Google Plus Link', 'trim|max_length[256]|prep_url|xss_clean');
        $this->form_validation->set_rules('sm_gpid', 'Google Plus Page Id', 'trim|max_length[256]|xss_clean');
        return $this->form_validation->run();
    }

    public function images($pageId = 0) {
        if ($this->validateSliderImageSettingsForm() === TRUE) {
            if (@$_FILES['image']['name']) {
                $config['max_height'] = '400';
                $config['max_width'] = '1024';
                $config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
                $this->load->model('file_handling_m');
                $data = $this->file_handling_m->add_new_file('image', $config);
                if (@$data['error'])
                    $this->_mainData['errors'][] = $data['the_errors'];
                else {
                    $uploadData['image'] = $data['file_name'];
                    $uploadData['pageId'] = $this->input->post('page');
                    $this->admin_m->updateSlider($uploadData);
                }
            }
        }
        $this->load->model('pages_m');
        $this->_mainData['activePage'] = $pageId;
        $this->_mainData['pages'] = array('0' => 'Main Home Page',
            '13' => 'Dirt Bike Landing Page',
            '2' => 'ATV Landing Page',
            '3' => 'Street Bike Landing Page',
            '4' => 'UTV Landing Page');
        if (is_numeric($pageId)) {
            $this->_mainData['bannerImages'] = $this->admin_m->getSliderImages($pageId);
            $this->renderMasterPage('admin/master_v', 'admin/upload_slider_images_v', $this->_mainData);
        } elseif ($pageId = 'brand') {
            $this->renderMasterPage('admin/master_v', 'admin/upload_brand_images_v', $this->_mainData);
        }
    }

    public function remove_image($id, $pageId) {
        if (is_numeric($id)) {
            $this->admin_m->removeImage($id, $this->config->item('upload_path'));
            redirect('admin_content/images/' . $pageId);
        }
    }

    public function social_media() {
        if (!$this->checkValidAccess('social_media') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($this->validateSocialMedia() === TRUE) {
            $this->_mainData['success'] = $this->admin_m->updateSMSettings($this->input->post());
        }
        $this->_mainData['SMSettings'] = $this->admin_m->getSMSettings();
        $this->renderMasterPage('admin/master_v', 'admin/sm_settings_v', $this->_mainData);
    }

    public function reviews() {
        if (!$this->checkValidAccess('reviews') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->_mainData['reviews'] = $this->admin_m->getNewReviews();
        $this->renderMasterPage('admin/master_v', 'admin/comments_v', $this->_mainData);
    }

    public function review_approval($reviewId) {
        if (!$this->checkValidAccess('reviews') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($reviewId)) {
            $this->admin_m->approveReview($reviewId, $_SESSION['userRecord']['id']);
        }
        redirect('admin_content/reviews');
    }

    public function review_reject($reviewId) {
        if (!$this->checkValidAccess('reviews') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($reviewId)) {
            $this->admin_m->deleteReview($reviewId);
        }
        redirect('admin_content/reviews');
    }

    public function pages() {
        if (!$this->checkValidAccess('pages') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->model('pages_m');
        if ($this->validatePages() === TRUE) {
            $this->pages_m->editPageActive($this->input->post());
            $this->_mainData['success'] = 'Your changes have been made.';
        }

        $this->_mainData['pages'] = $this->pages_m->getPages();
        $this->renderMasterPage('admin/master_v', 'admin/pages/list_v', $this->_mainData);
    }

    public function email() {
        if (!$this->checkValidAccess('email') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if ($this->validateEmailSettingsForm() === TRUE) {
            $uploadData = $this->input->post();
            $this->load->model('file_handling_m');
            if (@$_FILES['email_logo']['name']) {
                $config['max_height'] = '300';
                $config['max_width'] = '500';
                $config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
                $data = $this->file_handling_m->add_new_file('email_logo', $config);
                if (@$data['error'])
                    $this->_mainData['errors'][] = $data['the_errors'];
                else
                    $uploadData['email_logo'] = $data['file_name'];
            }
            if (@$_FILES['mass_email_attachment_doc']['name']) {
                $data = $this->file_handling_m->add_new_file('mass_email_attachment_doc');
                if (@$data['error'])
                    $this->_mainData['errors'][] = $data['the_errors'];
                else
                    $uploadData['mass_email_attachment_doc'] = $data['file_name'];
            }
            if (@$_FILES['mass_email_list_doc']['name']) {
                $data = $this->file_handling_m->add_new_file('mass_email_list_doc');
                if (@$data['error'])
                    $this->_mainData['errors'][] = $data['the_errors'];
                else
                    $uploadData['mass_email_list_doc'] = $data['file_name'];
            }

            $uploadData['email_order_complete'] = (@$uploadData['email_order_complete']) ? TRUE : FALSE;
            $uploadData['email_order_complete_attachment'] = (@$uploadData['email_order_complete_attachment']) ? TRUE : FALSE;
            $uploadData['registration_email'] = (@$uploadData['registration_email']) ? TRUE : FALSE;
            $this->admin_m->updateSettings($uploadData);
        }

        $this->_mainData['emailSettings'] = $this->admin_m->getEmailSettings();
        $this->renderMasterPage('admin/master_v', 'admin/email_v', $this->_mainData);
    }

    public function download_emails() {
        if (!$this->checkValidAccess('email') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        //$getUserEmails = $this->admin_m->getUserEmails();
        $getContactTable = $this->admin_m->getContactTable();
        $getNewsLetters = $this->admin_m->getNewsLetters();
        //array(0=>array('email'=>"All Users Emails")), $getUserEmails, 
        //0=>array('email'=>"All Contact Emails")),
        $newArray = array_merge(array(0 => array('email' => "All Users Emails")), $getContactTable, array(0 => array('email' => "All News Letters Emails")), $getNewsLetters);

        $csv = $this->array2csv($newArray);
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=emails.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv;
        exit;
    }

    function array2csv(array &$array) {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    public function debug($param) {

        echo "<pre>";
        print_r($param);
        echo "</pre>";
    }

    public function craglist_feeds() {
        if (!$this->checkValidAccess('data_feeds') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $uploadData = $this->input->post();
        if (!empty($uploadData)) {
            $file = STORE_DIRECTORY . '/craglistFeed/delimited.txt';
            //echo dirname(__DIR__);exit;
            $this->load->model('reporting_m');
            $csv = $this->reporting_m->getProductForcraglist();
            $csv_handler = fopen($file, 'w');
            // JLB 10-07-16
            fwrite($csv_handler, $csv);
            // foreach ($csv as $c) {
            // fputcsv($csv_handler, $c);
            // }
            fclose($csv_handler);
            $data = array('run_by' => 'admin', 'status' => '1');
            $this->admin_m->update_craglist_feeds_log($data);
        }
        $this->_mainData['cycletrader_feeds'] = $this->admin_m->get_cycletrader_feed_log();
        $this->_mainData['craglist_feeds'] = $this->admin_m->get_craglist_feed_log();
        $this->_mainData['feed'] = $this->admin_m->get_feed_log();
        $this->renderMasterPage('admin/master_v', 'admin/feed_v', $this->_mainData);
    }

    public function google_feeds() {
        $data = array('run_by' => 'admin', 'status' => '0');
        $this->admin_m->update_feed_log($data);
        header("Location: /admin_content/feeds");
    }

    public function cycletrader_feeds() {
        $data = array('run_by' => 'admin', 'status' => '0');
        $this->admin_m->update_cycletrader_feeds_log($data);
        header("Location: /admin_content/feeds");
    }

    public function ebay_feeds() {
//        if (!$this->checkValidAccess('data_feeds') && !@$_SESSION['userRecord']['admin']) {
//            redirect('');
//        }

        $this->load->model('ebay_m');
        $this->load->model('Ebaysetting');
        $uploadData = $this->input->post();
//        if (!empty($uploadData)) {
        $csv = $this->ebay_m->generateEbayFeed(0, 0);
        $data = array('run_by' => 'admin', 'status' => '1');
        $this->ebay_m->update_ebay_feeds_log($data);
//        }

        $this->_mainData['cycletrader_feeds'] = $this->admin_m->get_cycletrader_feed_log();
//        $this->_mainData['craglist_feeds'] = $this->admin_m->get_craglist_feed_log();
        $this->_mainData['feed'] = $this->admin_m->get_feed_log();
        $this->_mainData['ebay_feeds'] = $this->ebay_m->get_ebay_feed_log();

        $this->_mainData['ebaysettings'] = $this->Ebaysetting->getEbaySettings();
        $this->_mainData['ebayshippingsettings'] = $this->Ebaysetting->getEbayShippingSettings();
        $this->_mainData['paypalemail'] = $this->Ebaysetting->check_paypalemail();
        $this->_mainData['quantity'] = $this->Ebaysetting->check_quantity();
        $this->_mainData['ebaymarkup'] = $this->Ebaysetting->check_markup();
        $this->renderMasterPage('admin/master_v', 'admin/feed_v', $this->_mainData);
    }

    public function feeds() {
        // JLB 2017-12-18 I found this and it scares me; what were they doing?
        ini_set('memory_limit', '999M');

        if (!$this->checkValidAccess('data_feeds') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
//
//        $uploadData = $this->input->post();
//        if (!empty($uploadData)) {
//
//            $file = STORE_DIRECTORY . '/googleFeed/csvfile.csv';
////            echo dirname(__DIR__);exit;
//            $this->load->model('reporting_m');
//            $csv = $this->reporting_m->getProductsForGoogle();
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
        $this->_mainData['cycletrader_feeds'] = $this->admin_m->get_cycletrader_feed_log();
//        $this->_mainData['craglist_feeds'] = $this->admin_m->get_craglist_feed_log();
        $this->_mainData['feed'] = $this->admin_m->get_feed_log();
        $this->load->model('Ebaysetting');
        $this->load->model('ebay_m');
        $this->_mainData["ebay_error"] = !$this->ebay_m->checkForFatalErrors($this->_mainData["ebay_error_string"]);
        $this->_mainData["ebay_warning"] = !$this->ebay_m->checkForWarnings($this->_mainData["ebay_warning_string"]);

        $this->_mainData['ebay_feeds'] = $this->ebay_m->get_ebay_feed_log();
        $this->_mainData['ebaysettings'] = $this->Ebaysetting->getEbaySettings();
        $this->_mainData['ebayshippingsettings'] = $this->Ebaysetting->getEbayShippingSettings();
        $this->_mainData['paypalemail'] = $this->Ebaysetting->check_paypalemail();
        $this->_mainData['ebaymarkup'] = $this->Ebaysetting->check_markup();
        $this->_mainData['quantity'] = $this->Ebaysetting->check_quantity();

        if (defined('ENABLE_MD_FEED') && ENABLE_MD_FEED) {
            initializePSTAPI();
            global $PSTAPI;
            $this->_mainData['mdfeed_enabled'] = true;
            $feeds = $PSTAPI->mdfeed()->fetch();

            if (count($feeds) > 0) {
                // OK, great...
                $query = $this->db->query("Select count(*) as cnt from motorcycle where mdfeed = 1");
                $this->_mainData["mdfeed_major_unit_count"] = $query->result_array();
                $this->_mainData["mdfeed_major_unit_count"] = $this->_mainData["mdfeed_major_unit_count"]['cnt'];

                // When did this run last?
                $query = $this->db->query("Select * From mdfeed_feed_log order by id desc limit 1");
                $this->_mainData["mdfeed_feeds"] = $query->result_array();
                if (count($this->_mainData["mdfeed_feeds"]) > 0){
                    $this->_mainData["mdfeed_feeds"] = $this->_mainData["mdfeed_feeds"][0];
                } else {
                    unset($this->_mainData["mdfeed_feeds"]);
                }
            } else {
                $this->_mainData["mdfeed_error"] = "No feed is defined - please contact PST support and provide a Motorcycle Dealer feed URL.";
            }

        } else {
            $this->_mainData['mdfeed_enabled'] = false;
        }

        if (defined('ENABLE_LIGHTSPEED') && ENABLE_LIGHTSPEED) {
            $this->load->model("Lightspeed_m");

            // OK, we have lightspeed enabled...
            $this->_mainData['lightspeed_enabled'] = true;

            // OK, do we have credentials?
            $lightspeed_error = "";

            $c = $this->Lightspeed_m->getCredentials();

            if ($c["user"] == "" || $c["pass"] == "") {
                $lightspeed_error = "Lightspeed credentials are not configured in Store Profile.";
            }

            $this->_mainData["lightspeed_error"] = $lightspeed_error;

            // If we have credentials, we should display the number of motorcycles, the last time it ran, and a prompt to run it again...which means we'll need a new table...
            $query = $this->db->query("Select count(*) as cnt from motorcycle where lightspeed = 1");
            $this->_mainData["lightspeed_major_unit_count"] = $query->result_array();
            $this->_mainData["lightspeed_major_unit_count"] = $this->_mainData["lightspeed_major_unit_count"]['cnt'];

            // When did this run last?
            $query = $this->db->query("Select * From lightspeed_feed_log order by id desc limit 1");
            $this->_mainData["lightspeed_feeds"] = $query->result_array();
            if (count($this->_mainData["lightspeed_feeds"]) > 0){
                $this->_mainData["lightspeed_feeds"] = $this->_mainData["lightspeed_feeds"][0];
            } else {
                unset($this->_mainData["lightspeed_feeds"]);
            }
        } else {
            $this->_mainData['lightspeed_enabled'] = false;

        }

        // fetch the ebay statistics
        $this->_mainData['ebay_feed_counts'] = $this->ebay_m->getFeedCounts();


        $this->renderMasterPage('admin/master_v', 'admin/feed_v', $this->_mainData);
    }

    public function get_lightspeed_feed() {
        $this->db->query("Insert into lightspeed_feed_log (run_by) values ('admin') ");
        header("Location: /admin_content/feeds");
    }

    public function get_mdfeed_feed() {
        $this->db->query("Insert into mdfeed_feed_log (run_by) values ('admin') ");
        header("Location: /admin_content/feeds");
    }

    /*
     * The purpose of this is to download the ebay feed as a CSV to see all the records...
     */
    public function download_ebay_feed_csv() {
        $this->load->model('ebay_m');

        // Header to send as a file
        // We'll be outputting a PDF
        header('Content-Type: text/csv');

// It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="ebay_run_results' . date('YMDHis') . '.csv"');

        $handle = fopen("php://output", "w");
        fputcsv($handle, array(
            "Manufacturer Part #",
            "Part Name",
            "Error",
            "Error Class",
            "Error Details"
        ));

        // We have to then just fetch these guys and spit them out...
        $results = $this->ebay_m->getFeedResults();

        foreach ($results as $r) {
            fputcsv($handle, array(
                $r["sku"],
                $r["title"],
                $r["error"],
                $r["error"] > 0 ? $r["error_string"] : "",
                $r["error"] > 0 ? $r["long_error_string"] : ""
            ));
        }
    }

    public function ebay_settings() {
        $formData = $this->input->post();
        if (!empty($formData)) {
            echo 'hi';
            $this->load->model('Ebaysetting');
            echo '2';
            $this->Ebaysetting->saveSetting($formData);
            echo '3';
            $_SESSION['userRecord'] = '';
            echo 'hi';
        } else {
            echo 'xpe';
        }
    }

    public function paypal_email() {
        $formData = $this->input->post();
        if (!empty($formData)) {

            $this->load->model('Ebaysetting');
            $this->Ebaysetting->add_paypal_email($formData);
        }
    }

    public function ebay_markup() {
        $formData = $this->input->post();
        if (!empty($formData)) {

            $this->load->model('Ebaysetting');
            $this->Ebaysetting->markup($formData);
        }
    }
	
    public function get_ebay_orders() {
		ini_set('max_execution_time', 300);
        $this->load->model('ebay_m');
        $this->ebay_m->getOrders();
    }
	
    public function ebay_quantity() {
        $formData = $this->input->post();
        if (!empty($formData)) {

            $this->load->model('Ebaysetting');
            $this->Ebaysetting->add_quantity($formData);
        }
    }

    public function send_new_ebay() {
		error_reporting(0);
//		ini_set('max_execution_time', 300);
//		ini_set('set_time_limit', 300);
        $this->load->model('ebay_m');
        $this->load->model('Ebaysetting');
//		$csv = $this->ebay_m->generateEbayFeed(1500, 1);
        $data = array('run_by' => 'admin', 'status' => '0');
        $this->ebay_m->update_ebay_feeds_log($data);

        // redirect it...
        header("Location: /admin_content/feeds");
//
//        $this->_mainData['cycletrader_feeds'] = $this->admin_m->get_cycletrader_feed_log();
////        $this->_mainData['craglist_feeds'] = $this->admin_m->get_craglist_feed_log();
//        $this->_mainData['feed'] = $this->admin_m->get_feed_log();
//        $this->_mainData['ebay_feeds'] = $this->ebay_m->get_ebay_feed_log();
//        $this->_mainData['ebaysettings'] = $this->Ebaysetting->getEbaySettings();
//        $this->_mainData['ebayshippingsettings'] = $this->Ebaysetting->getEbayShippingSettings();
//        $this->_mainData['paypalemail'] = $this->Ebaysetting->check_paypalemail();
//        $this->_mainData['quantity'] = $this->Ebaysetting->check_quantity();
//        $this->_mainData['ebaymarkup'] = $this->Ebaysetting->check_markup();
//        $this->renderMasterPage('admin/master_v', 'admin/feed_v', $this->_mainData);
    }
	
    public function set_ebay_notifications() {
		error_reporting(E_ALL);
        $this->load->model('ebay_m');
        $this->load->model('Ebaysetting');
		$this->ebay_m->setNotifications();	
    }

    public function get_ebay_notifications() {
		error_reporting(E_ALL);
        $this->load->model('ebay_m');
        $this->load->model('Ebaysetting');
		$this->ebay_m->getNotifications();	
    }

    public function ebay_notifications() {
		error_reporting(E_ALL);
        $this->load->model('ebay_m');
        $this->load->model('Ebaysetting');
		echo "test";
		$this->ebay_m->receive_notifications();	
    }
	
	
    public function hit_ebay_end() {
		error_reporting(E_ALL);
		ini_set('max_execution_time', 300);
        $this->load->model('ebay_m');
        $this->load->model('Ebaysetting');
		$csv = $this->ebay_m->endAll(0, 1);	
		echo "this";
    }

    public function download_ebay_xml() {
        $ebay_feed_file = STORE_DIRECTORY . "/ebay_feed.xml";
        if (file_exists($ebay_feed_file) && is_file($ebay_feed_file)) {
            header('Content-Type: text/xml');
            header('Content-Disposition: attachment; filename="ebay_feed.xml"');
            readfile($ebay_feed_file);
        } else {
            print "Sorry, that file has disappeared.\n";
        }
    }
	
}
