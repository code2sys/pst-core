<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

class Welcome extends Master_Controller {

    private $_pageId = TOP_LEVEL_PAGE_ID_HOME;

    function pr($d) {
        echo "<pre>";
        print_r($d);
        echo "</pre>";
    }

    function __construct() {
        parent::__construct();
        $this->_mainData['shoppingCart'] = $this->generateShoppingCart();
        unset($_SESSION['search']);
        $this->load->library("session");
        //$this->output->enable_profiler(TRUE);
    }

    function _validUsername($username) {
        $valid = $this->account_m->verifyUsername($username);
        if ($valid && $valid['status'] == 1) {
            $valid['timestamp'] = time();
            $_SESSION['userRecord'] = $valid;
            return TRUE;
        } else if ($valid && $valid['status'] == 0) {
            $this->form_validation->set_message('_validUsername', 'Your account is not active please contact your administrator.');
            return FALSE;
        } else {
            $this->form_validation->set_message('_validUsername', 'You have provided an invalid Username.');
            return FALSE;
        }
    }

    function _uniqueUsername($username) {
        $valid = $this->account_m->verifyUsername($username);
        if ($valid) {
            $this->form_validation->set_message('_uniqueUsername', 'This email address is already associated with an account. Click the forgot password link in the Login screen if you have forgotten your password.');
            return FALSE;
        }
        return TRUE;
    }

    function _validPasswordUsername($username, $tempCode) {
        $valid = $this->account_m->getUserByTempCode($tempCode);

        if (!$valid) {
            $this->form_validation->set_message('_validPasswordUsername', 'You have an invalid URL.');
            return FALSE;
        }
        if ($valid['username'] != $username) {
            $this->form_validation->set_message('_validPasswordUsername', 'You have provided an invalid Username.');
            return FALSE;
        }
        return TRUE;
    }

    function _validEmail($email) {
        $userRecord = $_SESSION['userRecord'];
        if ($email == $_SESSION['lost_password_email'])
            return TRUE;
        else {
            $this->form_validation->set_message('_validEmail', 'You have provided an invalid Email Address.');
            return FALSE;
        }
    }

    function _createPassword($password) {
        $this->load->library('encrypt');
        $password = $this->encrypt->encode($password);
        $data = $_SESSION['userRecord'];
        $data['password'] = $password;
        $this->account_m->createUser($data);
        return TRUE;
    }

    function _updatePassword($password) {
        $this->load->library('encrypt');
        $password = $this->encrypt->encode($password);
        $data['password'] = $password;
        $data['username'] = $_POST['email'];
        $this->form_validation->set_message('_updatePassword', 'There has been an error attempting to update your password.');
        return $this->account_m->updateUserWithEmail($data);
    }

    function _processLogin($password) {
        $this->load->library('encrypt');
        unset($_SESSION['activeMachine']);
        unset($_SESSION['garage']);
        $userRecord = @$_SESSION['userRecord'];
        if (empty($userRecord['password'])) {
            return TRUE;
        }
        $clear_password = $this->encrypt->decode($userRecord['password']);
        $new_password = $this->encrypt->encode($password);
        
        if ($password == $clear_password) {
            $this->account_m->updateLogin($userRecord['id']);
            unset($_SESSION['contactInfo']);
            $this->load->model('parts_m');
            $newCart = $this->parts_m->getCart();
            if (is_array($newCart)) {
                foreach ($newCart as $key => $cart) {
                    $_SESSION['cart'][$key] = $cart;
                }
            }

            return TRUE;
        } else {
            $this->form_validation->set_message('_processLogin', "You have provided an invalid Password. $new_password $clear_password ");
            $_SESSION['userRecord'] = '';
            return FALSE;
        }
    }

    private function _createLogin($post) {
        if ($this->account_m->recordExists('user', array('username' => @$post['username']))) {
            $this->load->library('encrypt');
            $post['password'] = $this->encrypt->encode($post['password']);
            $this->account_m->createUser($post);
        }
        echo "Your password has been created!";
    }

    function _sku_exists($sku) {
        if (strpos($sku, 'coupon') !== FALSE) {
            if ($this->input->post('qty') == 0)
                return TRUE;
            elseif ($this->account_m->recordExists('coupon', array('couponCode' => $this->input->post('qty'))))
                return TRUE;
            else {
                $this->form_validation->set_message('_sku_exists', 'You have provided an invalid Coupon code.');
                return FALSE;
            }
        } elseif ($this->account_m->recordExists('partnumber', array('partnumber' => $sku)))
            return TRUE;
        else {
            $this->form_validation->set_message('_sku_exists', 'You have provided an invalid SKU.');
            return FALSE;
        }
    }

    private function validateLogin() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email Address', 'required|callback__validUsername|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|callback__processLogin|xss_clean');
        return $this->form_validation->run();
    }

    private function validateForgotPassword() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email Address', 'required|callback__validUsername|xss_clean');
        return $this->form_validation->run();
    }

    private function validateNewPassword($tempCode = NULL) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'Email Address', 'required|callback__validPasswordUsername[' . $tempCode . ']|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[conf_password]|callback__updatePassword|xss_clean');
        $this->form_validation->set_rules('conf_password', 'Confirm Password', 'required|callback__processLogin|xss_clean');
        return $this->form_validation->run();
    }

    private function validateSearch() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('filter', 'filter', 'required|xss_clean');
        return $this->form_validation->run();
    }

    private function contactInfo() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name', 'First Name', 'xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'xss_clean');
        $this->form_validation->set_rules('phone', 'Phone', 'xss_clean');
        $this->form_validation->set_rules('fax', 'Fax', 'xss_clean');
        $this->form_validation->set_rules('street_address', 'Street Address', 'xss_clean');
        $this->form_validation->set_rules('address_2', 'Apt/Suite', 'xss_clean');
        $this->form_validation->set_rules('city', 'City', 'xss_clean');
        $this->form_validation->set_rules('state', 'State', 'xss_clean');
        $this->form_validation->set_rules('zip', 'Zip', 'xss_clean');
        $this->form_validation->set_rules('country', 'Country', 'xss_clean');
        $this->form_validation->set_rules('company', 'Company', 'xss_clean');
        return $this->form_validation->run();
    }

    private function validateNewUser($nonpopup = 1) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[conf_password]|xss_clean');
        $this->form_validation->set_rules('conf_password', 'Confirm Password', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'required|callback__uniqueUsername|xss_clean');
        if ($nonpopup)
            $this->form_validation->set_rules('user_answer', 'Math Question', 'required|integer|callback__processCaptcha');
        return $this->form_validation->run();
    }

    // Select Products
    public function index_old($error = NULL) {
        $googleAdWordsScript = '<script>
		var google_tag_params = {
		ecomm_pagetype: \'home\'
		};
		</script>';
        $this->loadTopJS($googleAdWordsScript);
        if (!is_null($error)) {
            $errorScript = '<script>$(document).ready(function(){
				alert(\'We have detected possible fraudulent behavior. Please call one of our ' . WEBSITE_NAME . ' Associates to place your order at ' . SUPPORT_PHONE_NUMBER . '\');
			});
			</script>';
            $this->loadTopJS($errorScript);
        }

        // Create Customizable elements on the page
        $this->load->model('pages_m');
        $this->_mainData['pageRec'] = $this->pages_m->getPageRec($this->_pageId);
        $this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
        $this->setMasterPageVars('metatag', ''); // JLB - I removed msvalidate.01 from here because I think the new topheader handles it.
        $this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
        $this->setMasterPageVars('title', @$this->_mainData['pageRec']['title']);
        $this->setMasterPageVars('css', html_entity_decode($this->_mainData['pageRec']['css']));
        $this->setMasterPageVars('script', html_entity_decode($this->_mainData['pageRec']['javascript']));
        $this->_mainData['widgetBlock'] = $this->pages_m->widgetCreator($this->_pageId, $this->_mainData['pageRec']);


        $this->load->model('parts_m');
        $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
        $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
        $this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
        $this->_mainData['reviews'] = $this->parts_m->getReviews();
        $this->_mainData['reviewsBox'] = $this->load->view('widgets/reviews_display_v', $this->_mainData, TRUE);

        $this->_mainData['new_header'] = 1;

        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
        $this->setFooterView('master/footer_v.php');
        $this->setNav('master/navigation_v', 0);
        $this->renderMasterPage('master/master_v', 'info/storefront_v', $this->_mainData);
    }

    // Select Products
    public function index($error = NULL) {
        $googleAdWordsScript = '<script>
		var google_tag_params = {
		ecomm_pagetype: \'home\'
		};
		</script>';
        $this->loadTopJS($googleAdWordsScript);
        if (!is_null($error)) {
            $errorScript = '<script>$(document).ready(function(){
				alert(\'We have detected possible fraudulent behavior. Please call one of our ' . WEBSITE_NAME . ' Associates to place your order at ' . SUPPORT_PHONE_NUMBER . '\');
			});
			</script>';
            $this->loadTopJS($errorScript);
        }

        // Create Customizable elements on the page
        $this->load->model('pages_m');
        $this->_mainData['pageRec'] = $this->pages_m->getPageRec($this->_pageId);
            $this->_mainData['bannerImages'] = $this->pages_m->getSliderImagesForFront($this->_pageId);
        $this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
        $this->setMasterPageVars('metatag', ''); // JLB - I removed msvalidate.01 from here because I think the new topheader handles it.
        $this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
        $this->setMasterPageVars('title', @$this->_mainData['pageRec']['title']);
        $this->setMasterPageVars('css', html_entity_decode($this->_mainData['pageRec']['css']));
        $this->setMasterPageVars('script', html_entity_decode($this->_mainData['pageRec']['javascript']));
        $this->_mainData['widgetBlock'] = $this->pages_m->widgetCreator($this->_pageId, $this->_mainData['pageRec']);
	$this->_mainData['topVideo'] = $this->pages_m->getTopVideos($this->_pageId);

        $this->load->model('parts_m');
        $this->_mainData['topRated'] = $this->parts_m->getTopRatedProducts(null, 12);
        $this->_mainData['pageRec'] = $this->pages_m->getPageRec($this->_pageId);
        $notice = $this->pages_m->getTextBoxes($this->_pageId);
        $this->_mainData['notice'] = $notice[0]['text'];
        $this->_mainData['featuredCategories'] = $this->parts_m->getFeaturedCategories();
        $this->_mainData['featuredBrands'] = $this->parts_m->getFeaturedBrands(50);
        $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
        $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
        $this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
        $this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
        $this->_mainData['reviews'] = $this->parts_m->getReviews();
        $this->_mainData['reviewsBox'] = $this->load->view('widgets/reviews_display_v', $this->_mainData, TRUE);

        $this->_mainData['new_header'] = 1;

        $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
        //$this->setFooterView('master/footer_v_new.php');
        $this->setFooterView('benz_views/footer.php');
        $this->setNav('master/navigation_v', 0);
        $this->load->model('motorcycle_m');
        $this->_mainData['featured'] = $this->motorcycle_m->getFeaturedMonster();
        if (!defined('HOMEPAGE_VIEW')) {
            define('HOMEPAGE_VIEW', 'master/master_v_front');
        }
        $this->renderMasterPage(HOMEPAGE_VIEW, 'info/storefront_v', $this->_mainData);
    }

    public function benz() {
        header("Location: " . site_url("motorcycle_ci/benz"));
    }

    public function benzProduct() {
        header("Location: " . site_url("motorcycle_ci/benzProduct"));
    }

    public function benzDetails($title = null) {
        header("Location: " . site_url("motorcycle_ci/benzDetails/$title"));
    }

    public function filterMotorcycle() {
        header("Location: " . site_url("motorcycle_ci/filterMotorcycle"));
    }

    public function product_search() {
        if ($this->validateSearch() !== FALSE) {
            $_SESSION['search'] = $this->input->post('filter');
        }
    }

    public function clear_search() {
        $_SESSION['search'] = NULL;
    }

    // Output F for save to file, output D for on screen
    public function order_pdf($orderNum, $output = 'D') {
        // set up PDF Helper files
        $this->load->helper('fpdf_view');
        $parameters = array();
        pdf_init('reporting/poreport.php');

        // Send Variables to PDF
        //update process date and process user info
        $parameters['orders'] = $this->account_m->getPDFOrder($orderNum);
        $fileName = 'OrderReport_' . $orderNum . '.pdf';
        if ($output == 'F')
            $fileName = $this->config->item('attachments') . 'OrderReport_' . $orderNum . '.pdf';
        // Create PDF
        $this->PDF->setParametersArray($parameters);
        $this->PDF->runReport();
        $this->PDF->Output($fileName, $output);
        return $fileName;
    }

    public function orders() {
        $this->_mainData['orders'] = $this->account_m->getOrders(@$_SESSION['userRecord']['id'], FALSE);
        $this->setNav('master/navigation_v', 7);
        $this->renderMasterPage('master/master_v', 'orders_v', $this->_mainData);
    }

    public function process_billing_info() {
        if ($this->contactInfo() !== FALSE) {
            if (@$_SESSION['userRecord']['id'])
                $this->account_m->updateContact($this->input->post(), 'billing', @$_SESSION['userRecord']['id']);
            redirect('welcome/account');
        }
        else {
            $this->_mainData['billingRecord'] = $_POST;
            $this->_mainData['userRecord'] = @$_SESSION['userRecord'];
            if ($this->_mainData['userRecord']['shipping_id'])
                $this->_mainData['shippingRecord'] = $this->account_m->getShippingInfo($this->_mainData['userRecord']['id']);
            $this->_mainData['orders'] = $this->account_m->getOrders($this->_mainData['userRecord']['id']);
            $this->setNav('master/navigation_v', 7);
            $this->renderMasterPage('master/master_v', 'account_v', $this->_mainData);
        }
    }

    public function process_shipping_info() {
        if ($this->contactInfo() !== FALSE) {
            if (@$_SESSION['userRecord']['id'])
                @$_SESSION['userRecord']['shipping_id'] = $this->account_m->updateContact($this->input->post(), 'shipping', @$_SESSION['userRecord']['id']);
            redirect('welcome/account');
        }
        else {
            $this->_mainData['userRecord'] = @$_SESSION['userRecord'];
            if ($this->_mainData['userRecord']['billing_id'])
                $this->_mainData['billingRecord'] = $this->account_m->getBillingInfo($this->_mainData['userRecord']['id']);
            $this->_mainData['shippingRecord'] = $_POST;
            $this->_mainData['orders'] = $this->account_m->getOrders($this->_mainData['userRecord']['id']);
            $this->setNav('master/navigation_v', 7);
            $this->renderMasterPage('master/master_v', 'account_v', $this->_mainData);
        }
    }

    public function about() {
        $this->setNav('master/navigation_v', 3);
        $this->renderMasterPage('master/master_v', 'info/about_v', $this->_mainData);
    }

    public function validateContactForm() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
        $this->form_validation->set_rules('company', 'Company', 'xss_clean');
        $this->form_validation->set_rules('subject', 'Subject', 'required|xss_clean');
        $this->form_validation->set_rules('message', 'Message', 'xss_clean');
        return $this->form_validation->run();
    }

    public function contact() {

        if ($this->validateContactForm() === TRUE) {

            // Send email
            $this->config->load('sitesettings');

            $this->load->model("admin_m");
            $store_name = $this->admin_m->getAdminShippingProfile();

            $mailData = array('toEmailAddress' => $store_name["email"],

                'subject' => $this->input->post('subject'),
                'fromEmailAddress' => "noreply@powersporttechnologies.com",
                'fromName' => "Contact Form",
                'replyToEmailAddress' => $this->input->post('email'),
                'replyToName' => $this->config->item('replyToName'));
            $templateData = array(
                'message' => $this->input->post('message'),
                'email' => $this->input->post('email'),
                'name' => $this->input->post('name'),
                'company' => $this->input->post('company')
            );

            $textTemplate = 'email/contactus_text_v';
            $htmlTemplate = 'email/contactus_html_v';

            $templateData['emailBodyImg'] = site_url('assets/email_images/email_body.jpg');
            $templateData['emailFooterImg'] = site_url('assets/email_images/email_footer.png');
            $templateData['emailHeadImg'] = site_url('assets/email_images/email_head.jpg');
            $templateData['emailShadowImg'] = site_url('assets/email_images/email_shadow.png');
            $this->load->model('mail_gen_m');
            $this->globalViewData['success'] = $this->mail_gen_m->generateFromView($mailData, $templateData, $htmlTemplate, $textTemplate);
        }

        $this->setNav('master/navigation_v', 4);
        $this->renderMasterPage('master/master_v', 'info/contact_v', $this->_mainData);
    }

    public function tos() {
        $this->setNav('master/navigation_v', 0);
        $this->renderMasterPage('master/master_v', 'info/tos_v', $this->_mainData);
    }

    public function privacy() {
        $this->setNav('master/navigation_v', 0);
        $this->renderMasterPage('master/master_v', 'info/privacy_v', $this->_mainData);
    }

    private function processSKU($post, $plus) {
        $this->load->model('products_m');
        $product = $this->products_m->getProductBySKU($post['sku'], @$_SESSION['userRecord']['wholesaler']);
        $product['qty'] = @$post['qty'];
        $_SESSION['cart'][$post['sku']] = $product;
    }

    private function validateCartUpdate() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('sku', 'SKU', 'required|callback__sku_exists|xss_clean');
        if ($this->input->post('sku') != 'coupon')
            $this->form_validation->set_rules('qty', 'Quantity', 'is_natural|xss_clean');
        return $this->form_validation->run();
    }

    public function update_shopping_cart($plus = NULL) {
        if ($this->validateCartUpdate() !== FALSE) {
            $post = $this->input->post();
            $shoppingCart = @$_SESSION['cart'];
            $error = FALSE;
            if ($plus == 'true')
                ++$post['qty'];
            if (!@$post['qty']) {
                unset($_SESSION['cart'][$post['sku']]);
                $this->load->model('parts_m');
                $this->parts_m->updateCart();
            } else {
                if ($post['sku'] == 'coupon') {
                    $this->load->model('coupons_m');
                    $success = $this->coupons_m->addCoupon($post);
                    if (!$success)
                        $error = 'Your coupon code is invalid or no longer active.<br />';
                }
                elseif (@$_SESSION['cart'][$post['sku']]) {
                    $_SESSION['cart'][$post['sku']]['qty'] = $post['qty'];
                    $_SESSION['cart'][$post['sku']]['finalPrice'] = $post['qty'] * $_SESSION['cart'][$post['sku']]['price'];
                    return TRUE;
                } else
                    $this->processSKU($post, $plus);
            }
            if (!$error)
                $returnCode = $this->_mainData['shoppingCart'];
            else {
                $returnCode = '<div class="sidebar">' . $error . ' Please refresh the page to reload your original shopping cart.</div>';
                $this->load->model('parts_m');
                $this->parts_m->updateCart();
            }
        } else
            $returnCode = '<div class="sidebar">' . validation_errors() . ' Please refresh the page to reload your original shopping cart.</div>';

        echo $returnCode;
    }

    public function process_new_account() {
        $data['error'] = FALSE;
        if ($this->validateNewUser(0) !== FALSE) {
            $this->load->model('account_m');
            $this->account_m->createNewAccount($this->input->post());
            $this->validateLogin();
            $data['success_message'] = 'Your account has been created.';
            $_SESSION['newAccount'] = TRUE;
        } else {
            $data['error'] = TRUE;
            $data['error_message'] = validation_errors();
        }
        echo json_encode($data);
        exit();
    }

    public function login() {
        if ($this->validateLogin() !== FALSE)
            redirect($this->_mainData['baseURL']);
        else {
            $this->_mainData['username'] = $this->input->post('email');
            $this->_mainData['password'] = $this->input->post('password');
        }
        $this->load->model('parts_m');
        $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
        $this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
        $this->setNav('master/navigation_v', 0);
        $this->renderMasterPage('master/s_master_v', 'account/login_v', $this->_mainData);
    }

    public function load_login() {
        $tableView = $this->load->view('modals/login_v', $this->_mainData, TRUE);
        echo $tableView;
    }

    public function load_new_user() {
        $tableView = $this->load->view('modals/new_user_v', $this->_mainData, TRUE);
        echo $tableView;
    }

    public function new_account($form = NULL) {

        if ($form == 'create') {
            if ($this->validateNewUser(1) === TRUE) {
                $this->load->model('account_m');
                $this->account_m->createNewAccount($this->input->post());
                $this->validateLogin();
                if (is_numeric(strpos(@$_SESSION['url'], 'cart')) || is_numeric(strpos(@$_SESSION['url'], 'checkout')))
                    redirect('checkout');
                elseif (@$_SESSION['url'])
                    redirect($_SESSION['url']);
                else
                    redirect('checkout/account');
            }
        }
        elseif ($form == 'login') {
            if ($this->validateLogin() === TRUE) {
                if (is_numeric(strpos(@$_SESSION['url'], 'cart')) || is_numeric(strpos(@$_SESSION['url'], 'checkout')))
                    redirect('checkout');
                else
                    redirect(@$_SESSION['url']);
            }
        }
        elseif ($form == 'forgot') {
            if ($this->validateForgotPassword() !== FALSE) {
                $success = $this->sendForgotPasswordEmail($this->input->post('email'));
                if (!$success) {
                    $this->_mainData['processError'] = TRUE;
                }
            }
        }

        $this->load->helper('easy_captcha_helper');
        $this->_mainData['captcha'] = getCaptchaDisplayElements();
        if (is_numeric(strpos(@$_SESSION['url'], 'cart')) || is_numeric(strpos(@$_SESSION['url'], 'checkout')))
            $master = 's_master_v';
        else {
            $master = 's_nav_master_v';
            $this->setNav('master/navigation_v', 0);
            $this->load->model('parts_m');
            $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
            $this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
            $this->_mainData['rideSelector'] = $this->load->view('widgets/s_ride_select_v', $this->_mainData, TRUE);
            $this->_mainData['brandImages'] = $this->parts_m->getBrandImages();
            $this->_mainData['brandSlider'] = $this->load->view('info/s_brand_slider_v', $this->_mainData, TRUE);
            $this->_mainData['machines'] = $this->parts_m->getMachinesDd();
            $this->_mainData['rideSelector'] = $this->load->view('widgets/s_ride_select_v', $this->_mainData, TRUE);
            $this->_mainData['new_header'] = 1;
            $this->load->model('pages_m');
            $this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
            $this->setFooterView('master/s_footer_v.php');
        }

        $session_url = @$_SESSION['url'];
        $this->_mainData['session_url'] = $session_url;
        $page_view = 'account/signup_v';

        if (is_numeric(strpos(@$_SESSION['url'], 'cart'))) {
            $page_view = 'account/signup_new_v';
        }

        $this->load->model('admin_m');
        $this->_mainData['accountAddress'] = $this->admin_m->getAdminAddress();
        $this->renderMasterPage('master/' . $master, $page_view, $this->_mainData);
    }

    public function modal_login() {
        $data['error'] = FALSE;
        if ($this->validateLogin() !== FALSE)
            $data['success_message'] = "You have successfully logged in!";
        else {
            $data['error'] = TRUE;
            $data['error_message'] = validation_errors();
        }
        echo json_encode($data);
        exit();
    }

    public function modal_forgot_password() {
        $data['error'] = FALSE;
        if ($this->validateForgotPassword() !== FALSE) {
            $success = $this->sendForgotPasswordEmail($this->input->post('email'));
            if (!$success) {
                $data['error'] = TRUE;
                $data['error_message'] = 'The system was unable to process your email at this time.  Please try again in a few minutes.';
            } else
                $data['success_message'] = "Your email has been sent";
        }
        else {
            $data['error'] = TRUE;
            $data['error_message'] = validation_errors();
        }
        echo json_encode($data);
        exit();
    }

    private function sendForgotPasswordEmail($username) {
        $tempCode = uniqid('R');
        $userRecEmail = $this->account_m->updateUserTempCode($username, $tempCode);
        if (@$userRecEmail) {
            $mailData = array(
                'fromEmailAddress' => $this->config->item('fromEmailAddress'),
                'fromName' => $this->config->item('fromEmailName'),
                'replyToEmailAddress' => $this->config->item('replyToEmailAddress'),
                'replyToName' => $this->config->item('replyToName'),
                'toEmailAddress' => $userRecEmail,
                'subject' => 'Forgot Password Email');
            // Create the Mail Template Data
            $mailTemplateData = array('assets' => $this->_mainData['assets'],
                'baseURL' => $this->_mainData['baseURL'],
                'forgotPasswordURL' => $this->_mainData['baseURL'] . 'welcome/reset_password/' . $tempCode);
            // Generate the Password Verification Email to the User
            $this->load->model('mail_gen_m');
            $ret = $this->mail_gen_m->generateFromView($mailData, $mailTemplateData, 'email/forgot_password_html_v', 'email/forgot_password_text_v');
            return $ret;
        } else
            return FALSE;
    }

    public function reset_password($tempCode = NULL) {
        $tempCode = strip_tags($tempCode);
        if (@$tempCode) {
            $record = $this->account_m->getUserByTempCode($tempCode);
            if ($record) {
                $this->_mainData['email'] = $record['username'];
                $this->_mainData['tempCode'] = $tempCode;
                if ($this->validateNewPassword($tempCode) !== FALSE) {
                    $this->_mainData['success'] = TRUE;
                }
            } else
                $this->_mainData['processingError'] = TRUE;
        }
        else {
            $this->_mainData['processingError'] = 'Hello';
        }
        $this->renderMasterPage('master/master_v', 'account/new_password_v', $this->_mainData);
    }

    public function get_product_info($sku) {
        $this->load->model('products_m');
        $record = $this->products_m->getProductBySKU($sku, @$_SESSION['userRecord']['wholesaler']);
        echo json_encode($record);
    }

    public function outsideSalesXML() {
        $this->load->model('reporting_m');
        $csv = $this->reporting_m->getProductsForXML();
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=product.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv;
    }

    public function manual_appeagle_amazon_doc_generation() {
        $this->load->model('reporting_m');
        $dataArr = $this->reporting_m->getAppeagleAmazonXML();

        $filename = $this->config->item('upload_path') . "/Appeagle-Export.txt";
        $fh = fopen($filename, 'w');
        fclose($fh);

        $flag = false;
        foreach ($dataArr as $row) {
            if (!$flag) {
                // display field/column names as first row
                $data = implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, array($this, 'cleanData'));
            $data .= implode("\t", array_values($row)) . "\r\n";
        }
        $fp = fopen($filename, 'w');
        fwrite($fp, $data);
        fclose($fp);
        exit;
    }

    public function SalesBingXML() {
        $this->load->model('reporting_m');
        $dataArr = $this->reporting_m->getProductsForBing();
        $filename = $this->config->item('upload_path') . "/Bing.xml";

        $flag = false;
        foreach ($dataArr as $row) {
            if (!$flag) {
                // display field/column names as first row
                $data = implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, array($this, 'cleanData'));
            $data .= implode("\t", array_values($row)) . "\r\n";
        }
        $fp = fopen($filename, 'w');
        fwrite($fp, $data);
        fclose($fp);
        exit;
    }

    public function cycletraderSalesXML() {
        $this->cycletraderSalesFile();
    }

    public function cycletraderSalesFile() {
        $file_path = STORE_DIRECTORY . '/cycletraderFeed/cycle_trader_feed.txt';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_path));
//        header('Content-Disposition: attachment; filename=' . $file_path);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }

    public function hit_ebay() {
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        error_reporting(-1);
        $this->load->library('ebaylargeapi');
        $this->load->model('admin_m');
        $this->load->model('ebay_m');
        $this->config->load('ebay');
        $ebay_token = $this->config->item('ebay_token');
        $this->ebaylargeapi->create_Upload_Job_Request($ebay_token);
        $this->_mainData['cycletrader_feeds'] = $this->admin_m->get_cycletrader_feed_log();
        $this->_mainData['craglist_feeds'] = $this->admin_m->get_craglist_feed_log();
        $this->_mainData['feed'] = $this->admin_m->get_feed_log();
        $this->_mainData['ebay_feeds'] = $this->ebay_m->get_ebay_feed_log();
        $this->renderMasterPage('admin/master_v', 'admin/feed_v', $this->_mainData);
    }
    public function hit_ebay_update() {
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        error_reporting(-1);
        $this->load->library('ebaylargeapiupdate');
        $this->load->model('admin_m');
        $this->load->model('ebay_m');
        $this->config->load('ebay');
        $ebay_token = $this->config->item('ebay_token');
        $this->ebaylargeapi->create_Upload_Job_Request($ebay_token,'ReviseFixedPriceItem');
        $this->_mainData['cycletrader_feeds'] = $this->admin_m->get_cycletrader_feed_log();
        $this->_mainData['craglist_feeds'] = $this->admin_m->get_craglist_feed_log();
        $this->_mainData['feed'] = $this->admin_m->get_feed_log();
        $this->_mainData['ebay_feeds'] = $this->ebay_m->get_ebay_feed_log();
        $this->renderMasterPage('admin/master_v', 'admin/feed_v', $this->_mainData);
    }

    public function craglistSalesXML() {

        //$this->load->model('reporting_m');
        //$csv = $this->reporting_m->getProductsForGoogle();
        //header("Content-type: text/csv");
        //header("Content-Disposition: attachment; filename=google_product.csv");
        //header("Pragma: no-cache");
        //header("Expires: 0");
        //echo $csv;
        $filename = STORE_DIRECTORY . '/craglistFeed/csvfile.csv';
        header("Content-Type: text/csv");
        $file = $filename;
        header("Content-Disposition: attachment; filename=google_product.csv");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize($file));
        flush(); // this doesn't really matter.
        $fp = fopen($file, "r");
        while (!feof($fp)) {
            echo fread($fp, 65536);
            flush(); // this is essential for large downloads
        }
        fclose($fp);
        exit;
    }

    function createCsv($xml, $f) {
        $headers = array();
        foreach ($xml->ROW->children() as $field) {
            $headers[] = $field->getName();
        }
        $csv_filename = str_replace('xml', 'csv', $filename);
        $file = $this->getCsvDirectory() . '/' . $csv_filename;
        if (file_exists($file)) {
            unlink($file);
        }
        $csv = fopen($file, 'w');
        fputcsv($csv, $headers, ',', '"');
        foreach ($xml as $entry) {
            $data = get_object_vars($entry);
            // Decode HTML entities.
            $sanitized_data = array();
            foreach ($data as $key => $datum) {
                $sanitized_data[$key] = html_entity_decode($datum, ENT_COMPAT, 'UTF-8');
            }
            fputcsv($csv, $sanitized_data, ',', '"');
        }
        fclose($csv);
    }

    public function ebay_status() {
        $job_id = $this->session->userdata('job_id');
        $this->load->library('ebaylargeapi');
        $this->config->load('ebay');
        $ebay_token = $this->config->item('ebay_token');
        $this->ebaylargeapi->getJobStatus($ebay_token, $job_id);
    }

    public function ebaySalesFile() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $file_path = STORE_DIRECTORY . '/ebayFeeds/ebayfeed.xml';
        $csv_file_path = STORE_DIRECTORY . '/ebayFeeds/ebayfeed.csv';
        $xml = file_get_contents($file_path);
// replace '&' followed by a bunch of letters, numbers
// and underscores and an equal sign with &amp;
        $xml = preg_replace('#&(?=[a-z_0-9]+=)#', '&amp;', $xml);
        $xml = simplexml_load_string($xml);
        $f = fopen($csv_file_path, 'w');
        $this->createCsv($xml, $f);
        fclose($f);
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header('Content-Disposition: attachment; filename=' . basename($csv_file_path));
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header('Content-Length: ' . filesize($csv_file_path));
        readfile($csv_file_path);
        exit;
    }

    public function saveEbayIds() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
//        $file = dirname(__DIR__) . '/ebayFeeds/DownloadFile.zip';
//        $path = dirname(__DIR__) . '/ebayFeeds/';
//        $zip = new ZipArchive;
////        $this->pr(zip_entry_name($file));
////        die("1234");
//        $res = $zip->open($file);
//        if ($res === TRUE) {
//            $filename = $zip->getNameIndex(0);
//            unlink($path.'_unzipped_xml.xml');
//            var_dump($zip->renameName($filename,'_unzipped_xml.xml'));
////            $this->pr($filename);
////            die("***");
//            // extract it to the path we determined above
//            $zip->extractTo($path);
//            $zip->close();
//            echo "WOOT! $file extracted to $path";
//        } else {
//            echo "Doh! I couldn't open $file";
//        }
//        die("happy");

        $xmlString = file_get_contents(STORE_DIRECTORY . "/ebayFeeds/_unzipped.xml"); //The XML file.
        $xml = simplexml_load_string($xmlString, "SimpleXMLElement", LIBXML_NOCDATA); // extension that allows us to easily manipulate and get XML data.
        $json = json_encode($xml); //json encode xml file.
        $response_array = json_decode($json, TRUE); //array in json_decode.
        $this->pr($response_array);die("tesing");
        $this->load->model('ebay_m');
        if(key_exists('AddFixedPriceItemResponse', $response_array) && key_exists('0', $response_array['AddFixedPriceItemResponse'])) {
            foreach ($response_array['AddFixedPriceItemResponse'] as $key => $item) {
                $data['part_number'] = $item['CorrelationID'];
                $data['ebay_id'] = $item['ItemID'];
                $data['status']=FALSE;
                if($item['Ack'] == 'Success')
                $data['status'] = TRUE;
                $this->ebay_m->insertEbayIds($data);
                $this->hit_ebay_update();
            }
        }
    }

    public function ebay_download_xml() {

        $job_id = $this->session->userdata('job_id');
        $file_refrense_id = $this->session->userdata('fileReferenceId');
        $this->load->helper('download');
        $this->load->library('ebaylargeapi');
        $this->config->load('ebay');

        $ebay_token = $this->config->item('ebay_token');
        $this->ebaylargeapi->create_file_to_download($ebay_token, $job_id, $file_refrense_id);
    }

    public function googleSalesXML() {
        //$this->load->model('reporting_m');
        //$csv = $this->reporting_m->getProductsForGoogle();
        //header("Content-type: text/csv");
        //header("Content-Disposition: attachment; filename=google_product.csv");
        //header("Pragma: no-cache");
        //header("Expires: 0");
        //echo $csv;
        $filename = STORE_DIRECTORY . '/googleFeed/csvfile.csv';
        header("Content-Type: text/csv");
        $file = $filename;
        header("Content-Disposition: attachment; filename=google_product.csv");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize($file));
        flush(); // this doesn't really matter.
        $fp = fopen($file, "r");
        while (!feof($fp)) {
            echo fread($fp, 65536);
            flush(); // this is essential for large downloads
        }
        fclose($fp);
        exit;
    }

    public function googleSalesXMLNew() {
        sub_googleSalesXMLNew();
    }

    public function SalesFBXML() {
        $this->load->model('reporting_m');
        $csv = $this->reporting_m->getProductsForFB();
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=fb_product.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv;
    }

    private function cleanData(&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    public function SaleZillaTXTFile() {
        $this->load->model('reporting_m');
        $dataArr = $this->reporting_m->getProductsForSaleZilla();
        $filename = "website_data.txt";
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $flag = false;
        foreach ($dataArr as $row) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, array($this, 'cleanData'));
            echo implode("\t", array_values($row)) . "\r\n";
        }
        exit;
    }

    public function appEagleVariation() {
        $this->load->model('reporting_m');
        $dataArr = $this->reporting_m->getAppEagleVariationOne();
        $filename = $this->config->item('upload_path') . "/Appeagle-Export-Variation.txt";
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        $flag = false;
        foreach ($dataArr as $row) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, array($this, 'cleanData'));
            echo implode("\t", array_values($row)) . "\r\n";
        }
        exit;
    }

    public function ebayListings($page = 1, $limit = 1000) {
        $offset = ($page * 1000) - 1000;
        //$this->output->enable_profiler(TRUE);
        $this->load->model('reporting_m');
        $csv = $this->reporting_m->ebayListings($offset, 1000);
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=product_" . $page . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csv;
    }

    public function productEnquiry() {
        $post = $this->input->post();
        $this->load->model('motorcycle_m');


        $this->motorcycle_m->saveEnquiry($post);

        $toEmail = $this->motorcycle_m->getSalesEmail();
        $message = "";
        $message .= "First Name : " . $post['firstName'] . '<br>';
        $message .= "Last Name : " . $post['lastName'] . '<br>';
        $message .= "Email : " . $post['email'] . '<br>';
        $message .= "Phone : " . $post['phone'] . '<br>';
        $message .= "Address : " . $post['address'] . '<br>';
        $message .= "City : " . $post['city'] . '<br>';
        $message .= "State : " . $post['state'] . '<br>';
        $message .= "Zipcode : " . $post['zipcode'] . '<br>';
        $message .= "Date Of Ride : " . $post['date_of_ride'] . '<br>';
        $message .= "Make : " . $post['make'] . '<br>';
        $message .= "Model : " . $post['model'] . '<br>';
        $message .= "Year : " . $post['year'] . '<br>';
        $message .= "Miles : " . $post['miles'] . '<br>';
        $message .= "Accessories : " . $post['accessories'] . '<br>';
        $message .= "Questions : " . $post['questions'] . '<br>';
        $message .= "Motorcycle : " . $post['motorcycle'] . '<br>';

        $this->load->model("mail_gen_m");


        $this->mail_gen_m->queueEmail(array(
            "toEmailAddress" => $toEmail,
            "replyToEmailAddress" => $post['email'],
            "replyToName" => $post['firstName'] . " " . $post['lastName'],
            "fromEmailAddress" => "noreply@powersporttechnologies.com",
            "fromName" => "Major Unit Inquiry",
            "subject" => "New Motorcycle Inquiry",
            "message" => $message
        ));

//        $header = "From: noreply@powersporttechnologies.com\r\n";
//        $header.= "MIME-Version: 1.0\r\n";
//        $header.= "Content-Type: text/html; charset=utf-8\r\n";
//        $header.= "X-Priority: 1\r\n";
//        mail($toEmail, "New Motorcycle Enquiry", $message, $header);

        // JLB 04-19-18
        // Is the configuration in there for echoing leads to CDK?
        global $PSTAPI;
        initializePSTAPI();

        if ($PSTAPI->config()->getKeyValue("forward_leads_to_cdk") == "Yes") {
            $vehicle_type = $vehicle_make = $vehicle_model = $vehicle_year = "";
            // We should be getting this motorcycle by title?
            $motorcycle = $PSTAPI->motorcycle()->fetch(array("title" => $post['motorcycle']), true);
            $motorcycle = count($motorcycle) > 0 ? $motorcycle[0] : array();

            if (array_key_exists("make", $motorcycle)) {
                $vehicle_make = $motorcycle["make"];
            }
            if (array_key_exists("model", $motorcycle)) {
                $vehicle_model = $motorcycle["model"];
            }
            if (array_key_exists("type", $motorcycle)) {
                $vehicle_type = $motorcycle["type"];
            }
            if (array_key_exists("year", $motorcycle)) {
                $vehicle_year = $motorcycle["year"];
            }

            // OK, we need to save it, and then we need to post it...
            $inquiry = $PSTAPI->vseptprospect()->add(array(
                "Email" => $post['email'],
                "Name" => $post['firstName'] . " " . $post['lastName'],
                "Phone" => $post['phone'],
                "SourceDate" => date("Y-m-d"),
                "Address1" => $post['address'],
                "City" => $post['city'],
                "State" => $post['state'],
                "ZipCode" => $post['zipcode'],
                "Notes" => "", // JLB 04-23-18 They asked me not to echo message here... $message,
                "VehicleType" => $vehicle_type,
                "VehicleMake" => $vehicle_make,
                "VehicleModel" => $vehicle_model,
                "VehicleYear" => $vehicle_year
            ));

            $inquiry->pushToVSept();
        }


        redirect('welcome/benzDetails/' . $post['product_id']);
    }

    public function category() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->db->select('long_name,ebay_category_num,category_id');
        $query = $this->db->get('category');
        echo '<pre>';
        $result = $query->result_array();
        $this->outputCsv('category.csv', $result);
    }

    public function outputCsv($fileName, $assocDataArray) {

        ob_clean();
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $fileName);
        if (isset($assocDataArray['0'])) {
            $fp = fopen('php://output', 'w');
            fputcsv($fp, array_keys($assocDataArray['0']));
            foreach ($assocDataArray AS $values) {
                fputcsv($fp, $values);
            }
            fclose($fp);
        }
        ob_flush();
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
