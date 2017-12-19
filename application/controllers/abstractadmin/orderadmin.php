<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:21 AM
 */

require_once(__DIR__ . "/productsbrandsadmin.php");

abstract class Orderadmin extends Productsbrandsadmin {



    protected function validateShipping() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('zip', 'Zip/Postal Code', 'required|xss_clean');
        $this->form_validation->set_rules('weight', 'Weight', 'required|xss_clean');
        $this->form_validation->set_rules('country', 'Country', 'xss_clean');

        return $this->form_validation->run();
    }

    protected function validateEditShippingRules() {
        $this->load->library('form_validation');
        $formFields = $this->input->post();
        if (@$formFields['edit']) {
            $this->form_validation->set_rules('id', 'Id', 'required|xss_clean');
        }

        $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
        $this->form_validation->set_rules('weight_low', 'Weight Low', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('weight_high', 'Weight High', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('price_low', 'Price Low', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('price_high', 'Price High', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('width_low', 'Width Low', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('width_high', 'Width High', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('height_low', 'Height Low', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('height_high', 'Height High', 'is_numeric|xss_clean');
        $this->form_validation->set_rules('country', 'Country', 'required|xss_clean');
        $this->form_validation->set_rules('active', 'Active', 'xss_clean');
        $this->form_validation->set_rules('value', 'Price', 'requiredis_numeric|xss_clean');
        return $this->form_validation->run();
    }

    /*     * ************************* ORDERS ******************************* */

    public function orders($page = 1) {
        unset($_SESSION['admin_cart']);
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->model('account_m');
        $this->_mainData['currentPage'] = $page;
        $arr = $this->input->get();
        if (empty($arr['status'])) {
            $arr['status'][] = 'approved';
            $arr['status'][] = 'processing';
            $arr['days'] = 30;
        }
        if (in_array('approved', $arr['status'])) {
            //$arr['status'][] = 'declined';
            //$arr['status'][] = 'batch order';
            //$arr['status'][] = 'processing';
            //$arr['status'][] = 'back order';
            //$arr['status'][] = 'partially shipped';
            //$arr['status'][] = 'will_call';
            //$arr['status'][] = 'shipped/complete';
            //$arr['status'][] = 'returned';
            //$arr['status'][] = 'refunded';
        }
        //echo '<pre>';
        //print_r($arr);
        //echo '</pre>';
        $this->_mainData['pages'] = $this->adOrderPagination($this->account_m->getOrderCount());
        $offset = ($page - 1) * $this->_adOrderLimit;
        //$this->_mainData['prev_orders'] = $this->account_m->getPrevOrderDates($this->_adOrderLimit, $offset);
        // $this->load->library('pagination');
        // $config['base_url'] = site_url().'admin/orders/';
        // $config['total_rows'] = $this->account_m->getOrderCount();
        // $config['per_page'] = $this->_adOrderLimit;
        // $config['use_page_numbers'] = TRUE;
        // $this->pagination->initialize($config);
        //$arr['limit'] = $this->_adOrderLimit;
        //$arr['offset'] = $offset;

        $this->_mainData['listTable'] = $this->generateListOrderTable($arr);

        $this->loadDateFields(array('datepicker_from', 'datepicker_to'));
        $this->setNav('admin/nav_v', 3);
        $this->_mainData['filter'] = $arr;
        $this->renderMasterPage('admin/master_v', 'admin/order/list_v', $this->_mainData);
    }

    public function order_edit($id = 'new', $newPartNumber = NULL, $qty = 1) {
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->createMonths();
        $this->createYears();
        $this->_mainData['states'] = $this->load_states();
        $this->_mainData['provinces'] = $this->load_provinces();
        $this->loadCountries();
        $this->load->model('order_m');
        $this->_mainData['distributors'] = $this->order_m->getDistributors();

        if (!is_null($newPartNumber) && ($id != 'new')) {

            $this->load->model('parts_m');
            $part = $this->order_m->getPartIdByPartNumber($newPartNumber);

            $questAns = $this->parts_m->getQuestionAnswerByNumber($part['part_id'], $part['partnumber']);
            if(@$questAns) {
                $this->order_m->addProductToOrder($part['partnumber'], $id, $qty, $part['part_id']);
            } else if(!@$questAns && @$part) {
                $this->load->model('account_m');
                $partnumber = $this->account_m->getStockByPartId($part['part_id']);
                $this->order_m->addProductToOrder($part['partnumber'], $id, $qty, $part['part_id']);
            } else {
                if(!$this->order_m->checkPartNumber($newPartNumber, $qty, $id)) {
                    $this->session->set_flashdata('error','Product Not Found.');
                }
            }

            redirect('admin/order_edit/'.$id);
        }

        $store_name = $this->admin_m->getAdminShippingProfile();
        if( $this->input->post()) {
            $clientToken = $this->braintree_lib->create_client_token();

            $post = $this->input->post();
            if(@$post['transaction_id'] && $post['refund_amount'] > 0) {
                $result = Braintree_Transaction::refund($post['transaction_id'], $post['refund_amount']);

                if( @$result->success ) {
                    $transaction = $result->transaction;
                    $arr = array('braintree_transaction_id' => $transaction->id, 'sales_price' => '-'.$this->input->post('refund_amount'));
                    $this->admin_m->updateOrderPaymentByAdmin( $id, $arr );
                    //$this->admin_m->updateOrderStatusByAdmin( $id, 'Approved' );
                    //$this->load->model('order_m');
                    //$this->order_m->updateStatus($id, 'Approved', 'Ajax Update');
                    //redirect('admin/order_edit/'.$id);
                } else {
                    $error = $result->message;
                    $this->session->set_flashdata('error',$error);

                    //$this->load->model('order_m');
                    //$this->order_m->updateStatus($id, 'Declined', 'Ajax Update');

                    //redirect('admin/order_edit/'.$id);
                }
                exit;
            }

            $result = Braintree_Transaction::sale(['amount' => $this->input->post('amount'),
                    'paymentMethodNonce' => $this->input->post("payment_method_nonce"),
                    'options' => ['submitForSettlement' => True  ],
                    'deviceData' => $this->input->post('device_data'),
                    'customer' => [
                        'firstName' => $_POST['first_name'][0],
                        'lastName' => $_POST['last_name'][0],
                        'company' => $_POST['company'][0],
                        'phone' => $_POST['phone'][0],
                        'email' => $_POST['email'][0]
                    ],
                    'billing' => [
                        'firstName' => $_POST['first_name'][0],
                        'lastName' => $_POST['last_name'][0],
                        'company' => $_POST['company'][0],
                        'streetAddress' => $_POST['street_address'][0],
                        'extendedAddress' => $_POST['address_2'][0],
                        'locality' => $_POST['state'][0],
                        'postalCode' => $_POST['zip'][0]
                    ],
                    'shipping' => [
                        'firstName' => $_POST['first_name'][1],
                        'lastName' => $_POST['last_name'][1],
                        'company' => $_POST['company'][1],
                        'streetAddress' => $_POST['street_address'][1],
                        'extendedAddress' => $_POST['address_2'][1],
                        'locality' => $_POST['state'][1],
                        'postalCode' => $_POST['zip'][1]
                    ],
                    'channel' => 'MxConnectionLLC_SP_PayPalEC_BT']
            );

            if( @$result->success ) {
                $transaction = $result->transaction;
                $arr = array('braintree_transaction_id' => $transaction->id, 'sales_price' => $this->input->post('amount'));
                $this->admin_m->updateOrderPaymentByAdmin( $id, $arr );
                //$this->admin_m->updateOrderStatusByAdmin( $id, 'Approved' );
                $this->load->model('order_m');
                $this->order_m->updateStatus($id, 'Approved', 'Ajax Update');
                redirect('admin/order_edit/'.$id);
            } else {
                $error = $result->message;
                $this->session->set_flashdata('error',$error);

                $this->load->model('order_m');
                $this->order_m->updateStatus($id, 'Declined', 'Ajax Update');

                redirect('admin/order_edit/'.$id);
            }
        }

        if ($id != 'new') {
            $this->_mainData['order'] = $this->order_m->getOrder($id);
        }

        //if($this->_mainData['order']['created_by'] == '1') {
        $weight = 0.00;
        foreach( $this->_mainData['order']['products'] as $product ) {
            if(@$product['dealerRecs']) {
                foreach( $product['dealerRecs'] as $dealerRec ) {
                    $weight += $dealerRec['weight'];
                }
            }else if(@$product['distributorRecs']) {
                foreach( $product['distributorRecs'] as $distributorRec ) {
                    $weight += $distributorRec['weight'];
                }
            }
        }

        $zip = $this->_mainData['order']['shipping_zip'];
        $grndShippingValue = $this->admin_m->shippingRules($this->_mainData['order']['sales_price'], 'USA', $zip, $weight);
        $shippingValue = $this->admin_m->calculateParcel($zip, 'USA', $grndShippingValue, $weight);
        $this->_mainData['postalOptDD'] = $this->admin_m->subdividePostalOptions(@$_SESSION['postalOptionsAdmin']);
        //}

        $this->_mainData['store_name'] = $store_name;
        $this->setNav('admin/nav_v', 3);
        $this->renderMasterPage('admin/master_v', 'admin/order/edit_v', $this->_mainData);
    }

    public function orders_pdf($date = NULL) {
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        // set up PDF Helper files
        $this->load->helper('fpdf_view');
        $parameters = array();
        pdf_init('reporting/poreport.php');

        // Send Variables to PDF
        //update process date and process user info
        $parameters['orders'] = $this->account_m->getPDFOrders($_SESSION['userRecord']['id'], $date);
        $fileName = 'OrderReport_' . time() . '.pdf';

        // Create PDF
        $this->PDF->setParametersArray($parameters);
        $this->PDF->runReport();
        $this->PDF->Output($fileName, 'D'); // I
    }

    public function orders_csv($date = NULL) {
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $orders = $this->account_m->getPDFOrders($_SESSION['userRecord']['id'], $date);
        print_r($orders);
        //echo $this->array2csv($orders);
    }

    /*     * *********************************** END ORDERS ************************************ */

    /*     * ******************* SHIPPING *************************** */

    public function shipping_rules() {
        if (!$this->checkValidAccess('shipping') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }

        if ($this->validateEditShippingRules() === TRUE) {
            $this->admin_m->updateShippingRules($this->input->post());
        }

        $this->setNav('admin/nav_v', 2);
        $this->_mainData['shippingRules'] = $this->admin_m->getShippingRules();
        $this->load_countries();

        $this->renderMasterPage('admin/master_v', 'admin/shipping_rules_v', $this->_mainData);
    }

    public function load_shipping_rules($id) {
        if (!$this->checkValidAccess('shipping') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($id)) {
            $record = $this->admin_m->getShippingRule($id);
            echo json_encode($record);
        }
        exit();
    }

    public function shipping_rule_delete($id) {
        if (!$this->checkValidAccess('shipping') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        if (is_numeric($id)) {
            $this->admin_m->deleteShippingRule($id);
        }
        redirect('admin/shipping_rules');
    }

    public function test_shipping() {
        if ($this->validateShipping() !== FALSE) { // Display Form
            $this->_mainData['weight'] = $this->input->post('weight');
            $this->_mainData['zip'] = $this->input->post('zip');
            $this->_mainData['country'] = $this->input->post('country');
            // UPS Rates
            $this->load->library('UpsShippingQuote');

            $objUpsRate = new UpsShippingQuote();

            $strDestinationZip = $this->input->post('zip');
            $strMethodShortName = 'GND';
            $strPackageLength = '8';
            $strPackageWidth = '8';
            $strPackageHeight = '8';
            $strPackageWeight = $this->input->post('weight');
            $strPackageCountry = $this->_mainData['country'];
            $boolReturnPriceOnly = true;

            $this->_mainData['postalOptions']['UPS'] = $objUpsRate->GetShippingRate(
                $strDestinationZip, $strMethodShortName, $strPackageLength, $strPackageWidth, $strPackageHeight, $strPackageWeight, $boolReturnPriceOnly, $strPackageCountry
            );
            /*

              print_r($this->_mainData['postalOptions']['UPS']);
              exit();

             */


            // USPS Rates
            $this->load->helper('usps');
            $this->_mainData['postalOptions']['USPS'] = USPSParcelRate($strPackageWeight, $strDestinationZip, $strPackageCountry);
        }

        $this->setNav('admin/nav_v', 6);
        $this->renderMasterPage('admin/master_v', 'admin/shipping_v', $this->_mainData);
    }

    /*     * *************************** END SHIPPING ****************************** */

    public function create_order($newPartNumber = NULL, $qty = 1) {
        if (!$this->checkValidAccess('orders') && !@$_SESSION['userRecord']['admin']) {
            redirect('');
        }
        $this->load->model('order_m');
        $orderId = $this->order_m->createNewOrderByAdmin();
        redirect('admin/order_edit/'.$orderId);
        exit;
        $this->createMonths();
        $this->createYears();
        $this->_mainData['states'] = $this->load_states();
        $this->_mainData['provinces'] = $this->load_provinces();
        $this->loadCountries();
        $this->_mainData['distributors'] = $this->order_m->getDistributors();

        if (!is_null($newPartNumber)) {
            $this->load->model('parts_m');
            $part = $this->order_m->getPartIdByPartNumber($newPartNumber);
            $questAns = $this->parts_m->getQuestionAnswerByNumber($part['part_id'], $part['partnumber']);
            if( @$questAns ) {
                //$stock_code = $this->order_m->getPartVariationDetails($part['partnumber_id']);
                $post['display_name'] = $part['name'];//.' |||' . $questAns['question'] . ' :: ' . $questAns['answer'] . '||';
                $post['question'] = $questAns['question'];
                $post['answer'] = $questAns['answer'];
                $post['part_id'] = $part['part_id'];
                $post['partnumber_id'] = $part['partnumber_id'];
                $post['qty'] = $qty;
                $post['stock_code'] = $part['stock_code'];
                $post['price'] = $questAns['dealer_sale'] > 0 ? $questAns['dealer_sale'] : $questAns['sale'];
                $post['sale'] = ($questAns['dealer_sale'] > 0 ? $questAns['dealer_sale'] : $questAns['sale'])*$qty;
                $post['partnumber'] = $newPartNumber;

                //$_SESSION['admin_cart'][$newPartNumber] = $post;
            }
            //$this->order_m->addProductToOrderNew($newPartNumber, $id, $qty);
        }

        if ($id != 'new') {
            $this->_mainData['order'] = $this->order_m->getOrder($id);
        }

        $this->order_m->getDealerAndDistributorRec($_SESSION['admin_cart']);

        $this->setNav('admin/nav_v', 3);
        $this->renderMasterPage('admin/master_v', 'admin/order/create_v', $this->_mainData);
    }

}