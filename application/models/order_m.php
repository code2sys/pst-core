<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Order_M extends Master_M {

    function __construct() {
        parent::__construct();
    }

    public function getOrder($id) {
        $this->db->select('order.id AS order_id, ' .
                'order.user_id AS user_id, ' .
                'order.contact_id AS contact_id, ' .
                'order.shipping_id AS shipping_id, ' .
                'order.sales_price AS sales_price, ' .
                'order.shipping AS shipping, ' .
                'order.tax AS tax, ' .
                'order.special_instr AS special_instr, ' .
                'order.Reveived_date AS Reveived_date,' .
                'order.order_date AS order_date, ' .
                'order.process_date AS process_date, ' .
                'order.ship_tracking_code AS ship_tracking_code, ' .
                'order.IP AS customer_ip, ' .
                'contact.first_name AS first_name, ' .
                'contact.last_name AS last_name, ' .
                'contact.street_address AS street_address, ' .
                'contact.address_2 AS address_2, ' .
                'contact.city AS city, ' .
                'contact.state AS state, ' .
                'contact.zip AS zip, ' .
                'contact.country AS country, ' .
                'contact.email AS email, ' .
                'contact.phone AS phone, ' .
                'contact.company AS company,' .
                'shipping.first_name AS shipping_first_name, ' .
                'shipping.last_name AS shipping_last_name, ' .
                'shipping.street_address AS shipping_street_address, ' .
                'shipping.address_2 AS shipping_address_2, ' .
                'shipping.city AS shipping_city, ' .
                'shipping.state AS shipping_state, ' .
                'shipping.zip AS shipping_zip, ' .
                'shipping.country AS shipping_country, ' .
                'shipping.email AS shipping_email, ' .
                'shipping.phone AS shipping_phone, ' .
                'shipping.company AS shipping_company, ' .
                'shipping.phone AS shipping_phone, ' .
                'cf_user_billing_info.ccfname, ' .
                'cf_user_billing_info.cclname, ' .
                'cf_user_billing_info.ccexpmo, ' .
                'cf_user_billing_info.ccexpyr, ' .
                'cf_user_billing_info.ccnumber, ' .
                'cf_user_billing_info.cvc, ' .
                'order.sendPst, ' .
                'order.sales_price, ' .
                'order.created_by, ' .
                'order.ebay_order_id, ' .
                'order.source, ' .
                'order.shipping_type, order.product_cost, order.shipping_cost, ' .
                'order.braintree_transaction_id AS braintree_transaction_id, '
        );
        $records = FALSE;
        $where = array('order.id' => $id);

        $this->db->join('contact', 'contact.id = order.contact_id', 'left');
        $this->db->join('contact shipping', 'shipping.id = order.shipping_id', 'left');
        $this->db->join('(SELECT * FROM cf_user_billing_info ORDER BY id DESC) AS cf_user_billing_info', 'cf_user_billing_info.billingid = order.id', 'LEFT');

        $record = $this->selectRecord('order', $where);
        if ($record) {
            $this->load->library('encrypt');
            $record['ccnmbr'] = $this->encrypt->decode($record['ccnumber']);

            $record['ccnumber'] = $this->creditCardLast4($record['ccnumber']);
            $where = array('order_id' => $record['order_id']);
            $this->db->order_by('datetime DESC');
            $statusRec = $this->selectRecord('order_status', $where);
            $record['status'] = $statusRec['status'];
            $this->db->select('partnumber.partnumber_id, 
                              part.name, 
                              partnumberpartquestion.answer, 
                              part.part_id, 
                              partnumber.partnumber,
                              partquestion.question,
                              order_product.qty,
                              order_product.fitment,
                              order_product.distributor,
                              partnumber.sale,
                              order_product.price as sale,
                              partvariation.stock_code,
                              partvariation.from_hlsm,
                              order_product.product_sku,
                              order_product.dealer_qty,
                              order_product.distributor_qty,
                              order_product.status');
            $where = array('order_id' => $id, 'productquestion' => 0);
            $this->db->join('partnumber', 'partnumber.partnumber = order_product.product_sku', 'LEFT');
            $this->db->join('part', 'part.part_id = order_product.part_id', 'LEFT');
            $this->db->join('partnumberpartquestion', 'partnumberpartquestion.partnumber_id = partnumber.partnumber_id');
            $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
            $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id', 'LEFT');
            $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');
            $this->db->group_by('partnumber.partnumber');

            $record['products'] = $this->selectRecords('order_product', $where);

            $this->db->select('order_product_details.partnumber_id, 
                              order_product_details.name, 
                              order_product_details.answer, 
                              order_product_details.part_id, 
                              order_product_details.partnumber,
                              order_product_details.question,
                              order_product.qty,
                              order_product.fitment,
                              order_product.distributor,
                              order_product_details.sale,
                              order_product_details.stock_code,
                              order_product.product_sku,
                              order_product.status');
            $where = array('order_product_details.order_id' => $id);
            $this->db->join('order_product', 'order_product.product_sku = order_product_details.partnumber');
            $this->db->group_by('order_product_details.partnumber');
            $record['staticProducts'] = $this->selectRecords('order_product_details', $where);
            //echo $this->db->last_query();

            if (count($record['products']) < count($record['staticProducts']) || empty($record['products'])) {
                $record['products'] = $record['staticProducts'];
            }
            // echo '<pre>';
            // echo count($record['products']);
            // echo count($record['staticProducts']);
            // print_r($record);
            // echo '</pre>';
            // echo '<pre>';
            // print_r($record['products']);
            // print_r($record['staticProducts']);
            // echo '</pre>';exit;
            // echo '<pre>';
            // print_r($record);
            // echo '</pre>';
            // if( empty($record['products']) ) {
            // $record['products'] = $record['staticProducts'];
            // unset($record['staticProducts']);
            // }
            // $this->db->select('count(order_product.order_id) as totalProducts');
            // $where = array('order_product.order_id' => $id);
            // $totalProducts = $this->selectRecord('order_product', $where);
            // $products = array();
            // if( count($record['products']) < $totalProducts['totalProducts'] ) {
            // foreach( $record['staticProducts'] as $k => $v ) {
            // $products[$v['partnumber_id']] = $v;
            // }
            // $record['products'] = $products;
            // unset($record['staticProducts']);
            // }
            // check to see if product is a combo.  If so, join the combo and product name.
            if (@$record['products']) {
                foreach ($record['products'] as &$prod) {
                    // Get distributor id and partvariation.quantity_available
//                    $where = array('partnumber_id' => $prod['partnumber_id']);
//                    //$prod['distributorRecs'] = $this->selectRecords('partvariation', $where);
//                    $prod['distributorRecs'] = $this->selectRecords('partvariation', $where);

                    // JLB 01-10-18
                    // I had to rewrite this so that it would get these records without getting the lightspeed feed
                    $query = $this->db->query("Select partvariation.* from partvariation join distributor using (distributor_id) where partvariation.partnumber_id = ? and distributor.name != 'Lightspeed Feed'", array($prod['partnumber_id']));
                    $prod['distributorRecs'] = $query->result_array();

                    //echo $this->db->last_query();
                    $where = array('partnumber_id' => $prod['partnumber_id']);
                    $prod['dealerRecs'] = $this->selectRecords('partdealervariation', $where);
                    $where = array('partpartnumber.partnumber_id' => $prod['partnumber_id']);
                    $this->db->join('part', 'part.part_id = partpartnumber.part_id');
                    $parts = $this->selectRecords('partpartnumber', $where);
                    if (count($parts) > 1) {
                        foreach ($parts as $part) {
                            if (($part['part_id'] != $prod['part_id']) && (strpos($part['name'], 'COMBO') === FALSE)) {
                                $namepieces = explode('-', $part['name']);
                                $prod['name'] .= ' - ' . $namepieces[1];
                            }
                        }
                    }
                }
            }
            $record['products']['coupons'] = $this->checkForCoupons($id);
            if (!is_array($record['products']['coupons']))
                unset($record['products']['coupons']);


            $this->db->select('user_type');
            $this->db->from('user');
            $this->db->where('id', $record['user_id']);
            $user_type_query = $this->db->get();
            $user_type_res = $user_type_query->row();

            $record['user_type'] = "guest";
            if (!empty($user_type_res)) {
                $record['user_type'] = $user_type_res->user_type;
            }

            $where = array('order_id' => $record['order_id']);
            $this->db->select('amount as sales_price, braintree_transaction_id, transaction_date as order_date');
            //$this->db->limit(2);
            $transaction = $this->selectRecords('order_transaction', $where);
            $record['transaction'] = $transaction;
        }
//         echo '<pre>';
//         print_r($record);
//         echo '</pre>';exit;
        return $record;
    }

    public function getDealerAndDistributorRec($adminCart) {
        foreach ($adminCart as $partnumber => $cart) {
            // Get distributor id and partvariation.quantity_available
            $where = array('partnumber_id' => $cart['partnumber_id']);
            $_SESSION['admin_cart'][$partnumber]['distributorRecs'] = $this->selectRecords('partvariation', $where);

            $where = array('partnumber_id' => $cart['partnumber_id']);
            $_SESSION['admin_cart'][$partnumber]['dealerRecs'] = $this->selectRecords('partdealervariation', $where);
        }
    }

    private function creditCardLast4($encryptedNumber) {
        $this->load->library('encrypt');
        $encryptedNumber = 'VzVZNgY0CjYDalI3BGALblBkAW4KZFFnAWBWMgNn';
        $decodedNumber = $this->encrypt->decode($encryptedNumber);
        $last4 = substr($decodedNumber, -4);
        if (is_numeric($last4))
            return $last4;
        else
            return 'XXXX';
    }

    private function checkForCoupons($id) {
        $query = $this->db->query("Select 'COUPON' as partnumber_id, substr(product_sku, 8)  as name, price as sale, qty as qty, 'COUPON' as partnumber, '' as question, '' as answer from order_product where product_sku like 'coupon_%' and order_id = ?", array($id));
        $list = $query->result_array();
        return count($list) > 0 ? $list[0] : null;

        $this->db->select('couponCode');
        $coupons = $this->selectRecords('coupon');
        if ($coupons) {
            $i = 0;
            $ttl = count($coupons);
            $arr = array();
            foreach ($coupons as $coupon) {
                $arr[] = "coupon_" . $coupon['couponCode'];
            }
            $this->db->where_in("product_sku", $arr);
        } else {
            return null;
        }
        $this->db->where('order_id = ' . $id);
        $this->db->select("'COUPON' AS partnumber_id, product_sku AS name, price AS sale, qty AS qty, 'COUPON' AS partnumber, '' AS question, '' AS answer", FALSE);
        $couponProducts = $this->selectRecord('order_product');

        // You have to flag these as shipped. Otherwise, it will get stupid.
        $this->db->query("Update order_product set status = 'Shipped' where product_sku like 'coupon_%' and order_id = ?", array($id));
        return $couponProducts;
    }

    // JLB 01-10-18
    // WHY does this not support adding things again and just incrementing the quantity?
    public function addProductToOrder($partNumber, $orderId, $qty, $part_id, $fitment = null) {
        $where = array('order_id' => $orderId, 'product_sku' => $partNumber);
        if (!$this->recordExists('order_product', $where)) {
            $where = array('partnumber' => $partNumber);
            $partRec = $this->selectRecord('partnumber', $where);
            $data = array('order_id' => $orderId,
                'product_sku' => $partNumber,
                'price' => ($partRec['price'] * $qty),
                'qty' => $qty,
                'part_id' => $part_id,
                'fitment' => $fitment);
            $data['price'] = $partRec['dealer_sale'] > 0 ? ($partRec['dealer_sale'] * $qty) : ($partRec['sale'] * $qty);
            
            $this->db->select('partnumber.partnumber_id');
            $disWhere = array('partnumber' => $partNumber);
            $distributorcs = $this->selectRecord('partnumber', $disWhere);

            $disWhere = array('partnumber_id' => $distributorcs['partnumber_id']);
            $this->db->join('distributor', 'distributor.distributor_id=partvariation.distributor_id');
            $distributorDtl = $this->selectRecord('partvariation', $disWhere);

            // JLB 06-07-17
            // Some real jackass used to have @$product['qty'] in there for qty, which caused it to go in null.
            // Further, he (or she; could have been Jessie) knew it was wrong, so slapped an @ on there to
            // hide the error message that $product exists NOWHERE in this function and was undefined.
            //
            // I changed this to $qty because, well, I can't imagine what else to put here, and null makes things bad.
            $data['distributor'] = array('id' => $distributorDtl['distributor_id'], 'qty' => $qty, 'part_number' => $distributorDtl['part_number'], 'distributor_name' => $distributorDtl['name'], 'dis_cost' => $distributorDtl['cost']);
            $data['distributor'] = json_encode($data['distributor']);
            
            $this->createRecord('order_product', $data, FALSE);

        } else {
            $query = $this->db->query("Select * From order_product where order_id = ? and product_sku = ?", array($orderId, $partNumber));
            $matches = $query->result_array();
            $match = $matches[0];
            // in this instance, we have a match on the product...
            // I am baffled why I can't just incremnet these things??
            $data = json_decode($match[0]["distributor"], true);
            if (array_key_exists("qty", $data)) {
                $data["qty"] += $qty;
            } else if (array_key_exists("qty", $data[0])) {
                $data[0]["qty"] += $qty;
            }
            $this->db->query("Update order_product set qty = qty + ?, distributor = ? where order_id = ? and product_sku = ?", array($qty, json_encode($data), $orderId, $partNumber));
        }

        $order = $this->selectRecord('order', array('id' => $orderId));
        $shippingAdd = $this->getOrder($orderId);

        $where1 = array('order_id' => $orderId);
        $products = $this->selectRecords('order_product', $where1);

        $grandTotal = 0;
        foreach( $products as $productData ) {
            if( $productData['status'] != 'Refunded' ) {
                $grandTotal += $productData['price'];
            }
        }
        if( @$shippingAdd['shipping_state'] && $shippingAdd['shipping_state'] != '' ) {
            $tax = $this->calculateTax($shippingAdd['shipping_state'], ($grandTotal));
        } else {
            $tax = 0;
        }
        //$tax = $this->calculateTax($shippingAdd['shipping_state'], ($order['sales_price'] + ($data['price'])));

        $this->updateRecord('order', array('sales_price' => $grandTotal, 'tax' => $tax), array('id' => $orderId), FALSE);

        return true;
    }

    public function getPartIdByPartNumber($partNumber) {
        $where = array('partvariation.part_number' => $partNumber);
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id=partnumber.partnumber_id');
        $this->db->join('part', 'partpartnumber.part_id=part.part_id');
        $this->db->join('partvariation', 'partvariation.partnumber_id=partpartnumber.partnumber_id');
        $this->db->select('partpartnumber.part_id, part.name, partnumber.partnumber_id, partvariation.stock_code, partnumber.partnumber');
        $part = $this->selectRecord('partnumber', $where);
        return $part;
    }

    public function getPartVariationDetails($partnumber_id) {
        $where = array('partnumber_id' => $partnumber_id);
        $this->db->select('partvariation.stock_code');
        $partvariation = $this->selectRecord('partvariation', $where);
        return $partvariation['stock_code'];
    }

    public function getDistributors() {
        $ddArr = array();
        $this->db->select('distributor_id, name');
        $distributors = $this->selectRecords('distributor');
        if ($distributors) {
            foreach ($distributors as $dist) {
                $ddArr[$dist['distributor_id']] = $dist['name'];
            }
        }
        return $ddArr;
    }

    public function getDistributorsDetails() {
        $ddArr = array();
        $this->db->select('*');
        $distributors = $this->selectRecords('distributor');
        if ($distributors) {
            foreach ($distributors as $dist) {
                $ddArr[$dist['distributor_id']] = $dist;
            }
        }
        return $ddArr;
    }

    public function updateOrderProductsByOrderId($orderId, $products) {
        if (is_array($products)) {
            foreach ($products as $product_sku => $product) {
                $where = array('order_id' => $orderId, 'product_sku' => $product_sku);
                if ($this->recordExists('order_product', $where))
                    $this->updateRecord('order_product', $product, $where, FALSE);
                else
                    $this->createRecord('order_product', $product, FALSE);
            }
        }
    }

    public function getProductsByOrderId($orderId) {
        $where = array('order_id' => $orderId);
        $products = $this->selectRecords('order_product', $where);
        return $products;
    }

    public function updateStatus($orderId, $status, $notes = NULL) {
        $data['order_id'] = $orderId;
        $data['status'] = $status;
        $data['datetime'] = time();
        $data['userId'] = @$_SESSION['userRecord']['id'];
        $data['notes'] = $notes;
        $this->createRecord('order_status', $data, FALSE);
    }

    public function updateDealerInventory($arr) {
        foreach ($arr as $k => $v) {
            $where = array('partvariation_id' => $v['partnumber']);
            $partvariation = $this->selectRecord('partdealervariation', $where);
            $where = array('partvariation_id' => $v['partnumber']);
            $ar = array('quantity_available' => $partvariation['quantity_available'] - $v['quantity']);
            $this->updateRecord('partdealervariation', $ar, $where, FALSE);
        }
    }

    public function updateStockOnOrder($orderId, $data) {
        foreach ($data as $k => $dt) {
            $where = array('order_id' => $orderId, 'product_sku' => $k);
            $this->updateRecord('order_product', $dt, $where, FALSE);
        }
    }

    public function getPaymentInfo($id) {
        $where = array('id' => $id);
        $record = $this->selectRecord('cf_user_billing_info', $where);
        return $record;
    }

    public function updateProductStatus($orderId, $productSKU, $status, $notes = NULL) {
        $where = array('order_id' => $orderId, 'product_sku' => $productSKU);
        $product = array('status' => $status, 'notes' => $notes);
        $this->updateRecord('order_product', $product, $where, FALSE);
    }

    public function createNewOrderByAdmin() {
        $order = array('sales_price' => '0.00', 'shipping' => '0.00', 'tax' => '0.00', 'order_date' => time(), 'user_id' => '0', 'created_by' => '1');
        $orderId = $this->createRecord('order', $order, FALSE);
        //$orderId = $this->db->insert_id();
        $orderStatus = array('order_id' => $orderId, 'status' => 'Pending', 'datetime' => time(), 'userId' => 1, 'notes' => 'Admin Order');
        $this->createRecord('order_status', $orderStatus, FALSE);
        return $orderId;
    }

    public function removeProductFromOrder($orderId, $product, $status) {
        $where = array('order_id' => $orderId, 'product_sku' => $product);
        $data = $this->selectRecord('order_product', $where);
        $order = $this->selectRecord('order', array('id' => $orderId));
        
        $where1 = array('order_id' => $orderId);
        $products = $this->selectRecords('order_product', $where1);
        
        $grandTotal = 0;
        foreach( $products as $productData ) {
            if( $productData['status'] != 'Refunded' ) {
                $grandTotal += $productData['price'];
            }
        }
        
        $shippingAdd = $this->getOrder($orderId);
        if($data['status'] == 'Refunded') {
            $data['price'] = 0;
        }
        if( @$shippingAdd['shipping_state'] && $shippingAdd['shipping_state'] != '' ) {
            $tax = $this->calculateTax($shippingAdd['shipping_state'], ($grandTotal - ($data['price'])));
        } else {
            $tax = 0;
        }
        
        $this->updateRecord('order', array('sales_price' => $grandTotal - $data['price'], 'tax' => $tax), array('id' => $orderId), FALSE);
        $this->deleteRecord('order_product', $where);
        $where1 = array('order_id' => $orderId, 'partnumber' => $product);
        $this->deleteRecord('order_product_details', $where1);
    }
    
    public function refundProductFromOrder($orderId, $product, $status) {
        $where = array('order_id' => $orderId, 'product_sku' => $product);
        $data = $this->selectRecord('order_product', $where);
        $order = $this->selectRecord('order', array('id' => $orderId));
        
        $where1 = array('order_id' => $orderId, 'partnumber' => $product);
        $this->updateRecord('order_product_details', array('stock_code' => 'Refunded'), $where1, FALSE);
        
        $where = array('order_id' => $orderId);
        $products = $this->selectRecords('order_product', $where);
        
        $grandTotal = 0;
        foreach( $products as $productData ) {
            if( $productData['status'] != 'Refunded' ) {
                $grandTotal += $productData['price'];
            }
        }
        
        $shippingAdd = $this->getOrder($orderId);
        if($data['status'] == 'Refunded') {
            $data['price'] = 0;
        }
        if( @$shippingAdd['shipping_state'] && $shippingAdd['shipping_state'] != '' ) {
            $tax = $this->calculateTax($shippingAdd['shipping_state'], ($grandTotal - ($data['price'])));
        } else {
            $tax = 0;
        }
        
        $this->updateRecord('order', array('sales_price' => $grandTotal - $data['price'], 'tax' => $tax), array('id' => $orderId), FALSE);
        //$this->deleteRecord('order_product', $where);
    }
    

    public function checkPartNumber($newPartNumber, $qty, $orderId) {
        $where = array('partnumber.partnumber' => $newPartNumber);
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id=partnumber.partnumber_id');
        $partRec = $this->selectRecord('partnumber', $where);
        if (@$partRec) {
            $this->load->model('parts_m');
            if ($this->parts_m->validMachines($partRec['part_id'])) {
                $ftmnt = $_SESSION['activeMachine']['name'];
            }
            $this->addProductToOrder($newPartNumber, $orderId, $qty, $partRec['part_id'], $ftmnt);
            return true;
        }
        return false;
    }

    public function getOrderTotal($orderId) {
        $where = array('id' => $orderId);
        $this->db->select('sales_price');
        $total = $this->selectRecord('order', $where);
        return $total['sales_price'];
    }

    public function calculateTax($stateId, $productTotal) {
        $taxesArr = $this->account_m->getTaxes();
        if ($taxesArr[$stateId]['percentage']) {
            $tax = array('finalPrice' => (($productTotal * $taxesArr[$stateId]['tax_value']) / 100),
                'price' => (($productTotal * $taxesArr[$stateId]['tax_value']) / 100),
                'display_name' => 'Sales Tax');
        } else {
            $tax = array('finalPrice' => ($productTotal + $taxesArr[$stateId]['tax_value']),
                'price' => ($productTotal + $taxesArr[$stateId]['tax_value']),
                'display_name' => 'Sales Tax');
        }
        return $tax['finalPrice'];
    }
    
    public function getPartDistributorAjax( $part ) {
        $disWhere = array('partnumber.partnumber' => $part);
        $this->db->join('distributor', 'distributor.distributor_id=partvariation.distributor_id');
        $this->db->join('partnumber', 'partnumber.partnumber_id=partvariation.partnumber_id');
        $distributorDtl = $this->selectRecord('partvariation', $disWhere);
        return $distributorDtl;
    }

}
