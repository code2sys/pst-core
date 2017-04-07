<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* NOTE!!!  Need to make sure to turn on if checkbox is there and off it is not */

class Ebaysetting extends Master_M {

    function __construct() {
        parent::__construct();
    }

    public function saveSetting($post) {
//        print_r($post['data']);
//        die('--------');
            $this->db->truncate('ebay_shipping_rates');
            foreach ($post['data']as $key => $value) {

                if(($value['max_value']+$value['min_value']) > 0){
                     $this->db->insert('ebay_shipping_rates', $value);
                } 
            }
            return redirect('admin_content/feeds');
   
    }

    function check_settings($minvalue, $maxvalue, $shippingcost) {
        $this->db->select("*");
        $this->db->from("ebay_shipping_rates");
        $where = array('min_value' => $minvalue, 'max_value' => $maxvalue, 'shipping_cost' => $shippingcost);
        $this->db->where($where);
        return $query->result_array();
    }

    function getEbaySettings() {
        $this->db->select("*");
        $this->db->from("ebay_settings");
        $query = $this->db->get();
        return $query->result_array();
    }

    function getEbayShippingSettings() {
        $this->db->select("*");
        $this->db->from("ebay_shipping_rates");
        $query = $this->db->get();
        return $query->result_array();
    }

    function check_paypalemail() {
        $this->db->select("*");
        $this->db->from("ebay_settings");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function add_paypal_email($post) {
//        echo $this->input->post('paypal_email');

        if (!empty($post)) {
            $data = array(
                'key' => 'paypal_email',
                'value' => $post['paypal_email']
            );
            $email = $this->check_paypalemail();
//            echo '<pre>';
//            print_r($email);
//            print_r($email[0]['value']);
//            die;
            if (!empty($email[0]['value'])) {
                $this->db->where('value', $email[0]['value']);
                $this->db->update('ebay_settings', $data);
            } else {
                $this->db->insert('ebay_settings', $data);
            }
            return redirect('admin_content/feeds');
        }
    }

}
