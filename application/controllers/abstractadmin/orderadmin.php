<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/7/17
 * Time: 9:21 AM
 */

require_once(__DIR__ . "/productsbrandsadmin.php");

abstract class Orderadmin extends Productsbrandsadmin {

    /*
     * This function is supposed to bootstrap placing this order...
     */
    public function add_lightspeed_part($id, $lightspeedpart_id, $qty = 1) {
        $query = $this->db->query("Select * from lightspeedpart where lightspeedpart_id = ?", array($lightspeedpart_id));
        $lightspeedpart = $query->result_array();
        $lightspeedpart = $lightspeedpart[0];

        // OK, now, we should be holding it...
        if ($lightspeedpart["partvariation_id"] ==  0 || is_null($lightspeedpart["partvariation_id"])) {

            $this->load->model("Distributormodel");

            // We will have to find this. Which means we will have to consider making it...
            if ($lightspeedpart["eternalpartvariation_id"] > 0) {
                // you have to go get it
                $this->load->model("migrateparts_m");
                $eternalpartvariation = $this->migrateparts_m->getEternalPartVariation($lightspeedpart["eternalpartvariation_id"]);
                $d = $this->Distributormodel->fetchByName($eternalpartvariation["name"]); // that should be the distributor name field...
                // OK, we are going to insert that into partvariation...
                $this->db->query("Insert into partvariation (part_number, distributor_id, quantity_available, quantity_ten_plus, stock_code, quantity_last_updated, cost, price, weight, clean_part_number, width, height, length, manufacturer_part_number, protect, from_lightspeed) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)", array(
                    $eternalpartvariation["part_number"],
                    $d["distributor_id"],
                    $eternalpartvariation["quantity_available"],
                    $eternalpartvariation["quantity_ten_plus"],
                    $eternalpartvariation["stock_code"],
                    $eternalpartvariation["quantity_last_updated"],
                    $eternalpartvariation["cost"],
                    $eternalpartvariation["price"],
                    $eternalpartvariation["weight"],
                    $eternalpartvariation["clean_part_number"],
                    $eternalpartvariation["width"],
                    $eternalpartvariation["height"],
                    $eternalpartvariation["length"],
                    $eternalpartvariation["manufacturer_part_number"]
                ));
            } else {
                $d = $this->Distributormodel->fetchByName("Lightspeed Feed");
                if (!array_key_exists("distributor_id", $d)) {
                    $distributor_id = $this->Distributormodel->add(array(
                        "name" => "Lightspeed Feed",
                        "active" => 1,
                        "customer_distributor" => 1
                    ));
                } else {
                    $distributor_id = $d["distributor_id"];
                }

                // We just have to create one...
                $this->db->query("Insert into partvariation (part_number, distributor_id, quantity_available, quantity_ten_plus, stock_code, quantity_last_updated, cost, price, clean_part_number, manufacturer_part_number, protect, from_lightspeed) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)", array(
                    $lightspeedpart["part_number"],
                    $distributor_id,
                    0,
                    0,
                    "Normal",
                    $lightspeedpart["lightspeed_last_seen"],
                    $lightspeedpart["cost"],
                    $lightspeedpart["current_active_price"],
                    preg_replace("/[^a-z0-9]/i", "", $lightspeedpart["part_number"]),
                    $lightspeedpart["upc"]
                ));
            }
            $partvariation_id = $this->db->insert_id();
            $lightspeedpart["partvariation_id"] = $partvariation_id;

            // OK, we have created an entry in partvariation...we need to create an entry in partnumber next
            // We need to add the lightspeed part information into partdealervariation now...
            $this->load->model("Portalmodel");
            $part_id = $this->Portalmodel->makePartByName("Lightspeed Part Feed: " . $lightspeedpart["description"], $lightspeedpart["description"]);
            $this->db->query("Update part set mx = 0, lightspeed = 1 where part_id = ?", array($part_id));

            $this->Portalmodel->queuePart($part_id);

            // we have to glue these things together..with a partnumber entry and a part partnumber.
            $partnumber_id = $this->Portalmodel->insert("partnumber", "partnumber_id", array(
                "universalfit" => 1, "protect" => 1
            ));

            // join them
            $this->db->query("Update partvariation set partnumber_id = ? where partvariation_id = ?", array($partnumber_id, $partvariation_id));
            $this->db->query("Insert into partpartnumber (part_id, partnumber_id) values (?, ?)", array($part_id, $partnumber_id));


            // make sure to save it...
            $this->db->query("Update lightspeedpart set partvariation_id = ? where lightspeedpart_id = ?", array($lightspeedpart["partvariation_id"], $lightspeedpart_id));

            // Update price, cost, and the part number
            // Just mark them.
            $this->Portalmodel->update("partnumber", "partnumber_id", $partnumber_id, array("protect" => 1, "price" => $lightspeedpart["current_active_price"], "cost" => $lightspeedpart["cost"], "sale" => $lightspeedpart["current_active_price"], "dealer_sale" => $lightspeedpart["current_active_price"]));
            $this->db->query("Update partnumber join partvariation using (partnumber_id) join distributor using (distributor_id) set partnumber = concat(distributor.name, '-', partvariation.part_number) where partnumber.partnumber_id = ? and partvariation.partvariation_id = ?", array($partnumber_id, $partvariation_id));


            // And, at long last, insert this into partdealervariation
            $this->db->query("Insert into partdealervariation (part_number, partnumber_id, distributor_id, quantity_available, quantity_ten_plus, stock_code, quantity_last_updated, cost, price, clean_part_number, manufacturer_part_number) select part_number, partnumber_id, distributor_id, quantity_available, quantity_ten_plus, stock_code, quantity_last_updated, cost, price, clean_part_number, manufacturer_part_number from partvariation where partvariation_id = ?", array($partvariation_id));
        }

        // we should be holding a partvariation_id now...
        $partvariation_id = $lightspeedpart["partvariation_id"];

        // now, we should just try to fetch it, like you normally would
        $query = $this->db->query("Select partpartnumber.part_id, part.name, partnumber.partnumber_id, partvariation.stock_code, partnumber.partnumber from partpartnumber join partnumber using (partnumber_id) join partvariation using (partnumber_id) where partvariation.partvariation_id = ?", array($partvariation_id));
        $part = $query->result_array();
        $part = $part[0];

        $this->load->model('parts_m');

        $questAns = $this->parts_m->getQuestionAnswerByNumber($part['part_id'], $part['partnumber']);
        if(@$questAns) {
            $this->order_m->addProductToOrder($part['partnumber'], $id, $qty, $part['part_id']);
        } else if(!@$questAns && @$part) {
            $this->load->model('account_m');
            $this->order_m->addProductToOrder($part['partnumber'], $id, $qty, $part['part_id']);
        } else {
            $this->session->set_flashdata('error', 'Product Not Found.');
        }

        redirect('admin/order_edit/'.$id);
    }


    /*
     * What happens if it matches the MX part number as well as the regular part number?
     */
    public function ajax_query_part() {
        $partnumber = trim(array_key_exists("partnumber", $_REQUEST) ? $_REQUEST["partnumber"] : "");

        $results = array(
            "partnumber" => $partnumber,
            "success" => false,
            "error_message" => "No part number received.",
            "store_inventory_match" => false
        );

        if ($partnumber != "") {
            // Option 1: Check for an exact match, and, if it exists, we will proceed..
            $this->load->model('parts_m');
            $this->load->model('order_m');
            $part = $this->order_m->getPartIdByPartNumber($partnumber);

            if (isset($part) && !is_null($part) && FALSE !== $part) {
                $results["success"] = true;
                $results["store_inventory_match"] = true;
                $results["part"] = $part;
            } else {
                $results["store_inventory_match"] = false;

                // OK, there was not an exact match...
                // The next possibility is that there could be a match into lightspeed, which could create a just-in-time part if they really wanted to...
                $matches = array();
                $query = $this->db->query("Select * from lightspeedpart where part_number = ? or upc = ?", array($partnumber, $partnumber)); // TODO
                $matches = $query->result_array();

                // Future - should we pull from any inventory that we have? We wouldn't even have a product name, which could be a problem...
                if (count($matches) > 0) {

                    // If there are eternal part variation IDs, we should pull them, too... to see what we can see...
                    $results["lightspeed_match"] = true;
                    $results["success"] = true;

                    $eternalpartvariation_ids = array();
                    for ($i = 0; $i < count($matches); $i++) {
                        $matches[$i]["source"] = "Lightspeed";
                        if ($matches[$i]["eternalpartvariation_id"] > 0) {
                            $eternalpartvariation_ids[] = $matches[$i]["eternalpartvariation_id"];
                        }
                    }

                    if (count($eternalpartvariation_ids) > 0) {
                        // OK, we have to attempt to get the information for these...
                        $this->load->model("migrateparts_m");
                        $epv_matches = $this->migrateparts_m->getEternalPartVariations($eternalpartvariation_ids);
                        // OK, that should give us some options...including the distributor name and the amount available...
                        $epv_match_lut = array();
                        foreach ($epv_matches as $epvrow) {
                            $epv_match_lut[$epvrow["eternalpartvariation_id"]] = $epvrow;
                        }

                        // Now, decorate our matches....
                        for ($i = 0; $i < count($matches); $i++) {
                            if ($matches[$i]["eternalpartvariation_id"] > 0) {
                                if (array_key_exists($matches[$i]["eternalpartvariation_id"], $epv_match_lut)) {
                                    $matches[$i]["inventory"] = $epv_match_lut[$matches[$i]["eternalpartvariation_id"]];
                                }
                            }
                        }
                    }

                    $results["lightspeed"] = $matches;
                } else {
                    // we are currently unable to provide anything...
                    $results["error_message"] = "No match found.";
                }
            }
        }

        if ($results["success"]) {
            $this->_printAjaxSuccess($results);
        } else {
            $this->_printAjaxError($results["error_message"]);
        }

    }



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
                exit; // JLB 01-10-18 WTF is this???
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
                    'channel' => 'MxConnectionLLC_SP_PayPalEC_BT'] // JLB 01-10-18 WTF is this?
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
        $this->_mainData['order_id'] = $id;

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


        // JLB 01-10-18 This is a great cause of problems; this should be farmed out and it should involve some caching.
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