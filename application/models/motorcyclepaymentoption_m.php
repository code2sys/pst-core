<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Motorcyclepaymentoption_M extends Master_M {
    /*
    Reference: Model Structure
    {
        motorcycle_id: int, // 0 : global
        condition: tinyint(2), // used only for global, 0: both, 1: New, 2: Used
        active: true/false, // whether activate the payment calculator or not
        custom: true/false, // whether use specific or not
        display_base_payment: true/false, // whether show in list/detail or not
        base_down_payment: double, // down_payment used to calculate base payment
        base_payment_text: varchar, // base payment text
        data: json {
            down_payment_options: [
                50,
                100,
                200
            ],
            terms: [
                {
                    rate: double,
                    term: int
                }
            ],
            fine_print: text,
            warranty_options: [
                {
                    title: text,
                    description: text,
                    price: double
                }
            ],
            accessory_options: [
                {
                    product: text, // optional
                    title: text,
                    description: text,
                    price: double,
                    thumbnail: url
                }
            ]
        }
    }
    */

    function __construct() {
        parent::__construct();

        $this->globalOption = $this->getGlobalPaymentOption();
    }

    /**
     * If Mass Major Unit Option is not defined, use this for data consistency
     */
    public function getDefaultPaymentOption() {
        return array(
            'active' => 0,
            'condition' => 0,
            'custom' => 0,
            'display_base_payment' => 0,
            'base_down_payment' => 500,
            'base_payment_text' => 'Payment as Low as',
            'data' => array(
                'down_payment_options' => array(
                    500, 1000, 1500
                ),
                'term' => 60,
                'interest_rate' => 5,
                'selected_term' => 0,
                'terms' => array(
                    array(
                        'interest_rate' => 5,
                        'term' => 60
                    ),
                    array(
                        'interest_rate' => 6.5,
                        'term' => 72
                    )
                ),
                'fine_print' => '',
                'warranty_options' => array(
                    array(
                        'title'=>'GOOD',
                        'description' => 'A good warranty to keep you on the road',
                        'price' => 600
                    ),
                    array(
                        'title'=>'BETTER',
                        'description' => 'A strong warranty to keep you on the road',
                        'price' => 700
                    ),
                    array(
                        'title'=>'BEST',
                        'description' => 'A strong warranty to keep you on the road',
                        'price' => 800
                    )
                ),
                'accessory_options' => array(
                    array(
                        'title'=>'Winch',
                        'description'=>'This price also includes installation',
                        'price'=>208.32
                    )
                ),
                'accessory_product' => null
            )
        );
    }
    
    /**
     * Make modification just after fetch from DB.
     * Some fields are saved JSON encoded format, we need to decode them after fetch from DB
     */
    private function prefillAfterFetch($option) {
        if (!empty($option['data'])) { 
            $option['data'] = json_decode($option['data'], true); 
            if (isset($option['data']['term']) && isset($option['data']['terms'])) {
                $option['data']['selected_term'] = 0;
                for ($i = 0; $i < count($option['data']['terms']); $i++) {
                    if ($option['data']['term'] == $option['data']['terms'][$i]['term']) {
                        $option['data']['selected_term'] = $i;
                        break;
                    }
                }
            } else {
                $option['data']['selected_term'] = 0;
            }
        }
        return $option;
    }

    /**
     * Get Major Unit Payment Option for a motorcycle
     * if unspecified, return Mass Major Unit Payment Option
     */
    function getPaymentOption($motorcycle_id = null) {
        $option = null;
        if (is_null($motorcycle_id)) {

            if (isset($this->globalOption)) return $this->globalOption;

            $query = $this->db->query("Select * from motorcycle_payment_option where motorcycle_id is null");
            $records = $query->result_array();
            $query->free_result();
            if (count($records) > 0) {
                $option = $this->prefillAfterFetch($records[0]);
            } else {
                $option = $this->getDefaultPaymentOption();
                
            }
        } else {
            $query = $this->db->query("Select * from motorcycle_payment_option where motorcycle_id = ?", array($motorcycle_id));
            $records = $query->result_array();
            $query->free_result();
            if (count($records) > 0) {
                $option = $this->prefillAfterFetch($records[0]);
            } else {
                return null;
            }
        }

        return $option;
        
    }

    /**
     * Merge Mass Major Unit Payment Option and motorcycle specific Major Unit Payment Option
     */
    function getActivePaymentOption($motorcycle_id, $default, $condition) {
        $option = $this->getPaymentOption($motorcycle_id);
        if (!isset($option) || ($option['active'] != 0 && $option['custom'] == 0) ) {
            if ($default['condition'] == 0 || $default['condition'] == $condition)
                return $default;
            else
                return $this->getDefaultPaymentOption();
        }
        return $option;
    }

    /**
     * return Mass Major Unit Payment Option
     */
    function getGlobalPaymentOption() {
        return $this->getPaymentOption(null);
    }

    /**
     * Save Major Unit Payment Option for motorcycle.
     * If motorcycle is unspecified, regard it as Mass
     */
    function savePaymentOption($options, $motorcycle_id = null) {
        
        if (is_null($motorcycle_id)) {
            $query = $this->db->query("Select * from motorcycle_payment_option where motorcycle_id is null");
        } else {
            $query = $this->db->query("Select * from motorcycle_payment_option where motorcycle_id = ?", array($motorcycle_id));
        }

        if (!empty($options['data']['terms'])) {
            if (!empty($options['data']['selected_term']) && $options['selected_term'] >= 0 && $options['selected_term'] < count($options['data']['terms'])) {
                $term_index = $options['data']['selected_term'];
                $options['data']['interest_rate'] = $options['data']['terms'][$term_index]['interest_rate'];
                $options['data']['term'] = $options['data']['terms'][$term_index]['term'];
            } else {
                $options['data']['interest_rate'] = $options['data']['terms'][0]['interest_rate'];
                $options['data']['term'] = $options['data']['terms'][0]['term'];
            }
        }
        $encoded_data = json_encode($options['data']);
        $active = isset($options['active']) ? $options['active'] : 0;
        $custom = isset($options['custom']) ? $options['custom'] : 0;
        $condition = isset($options['condition']) ? $options['condition'] : 0;
        $display_base_payment = isset($options['display_base_payment']) ? $options['display_base_payment'] : 0;
        $base_down_payment = isset($options['base_down_payment']) ? $options['base_down_payment'] : 0;
        $base_payment_text = isset($options['base_payment_text']) ? $options['base_payment_text'] : '';

        $values = array(
            $active, 
            $custom, 
            $condition, 
            $display_base_payment, 
            $base_down_payment, 
            $base_payment_text, 
            $encoded_data, 
            $motorcycle_id);
        if ($query->num_rows() > 0) {
            if (is_null($motorcycle_id)) {
                $this->db->query("update motorcycle_payment_option set `active` = ?, `custom` = ?,`condition` = ?, `display_base_payment` = ?, `base_down_payment` = ?, `base_payment_text` = ?, `data` = ? where `motorcycle_id` is null"
                , $values);
            } else {
                $this->db->query("update motorcycle_payment_option set `active` = ?, `custom` = ?,`condition` = ?, `display_base_payment` = ?, `base_down_payment` = ?, `base_payment_text` = ?, `data` = ? where `motorcycle_id` = ?"
                , $values);
            }
        } else {
            $this->db->query("Insert into motorcycle_payment_option (`active`, `custom`, `condition`, `display_base_payment`, `base_down_payment`, `base_payment_text`, `data`, `motorcycle_id`) values (?, ?, ?, ?, ?, ?, ?, ?)"
                , $values);
            $options['id'] = $this->db->insert_id();
        }

        if (is_null($motorcycle_id)) {
            $this->globalOption = $options;
        }
        return $options;
    }

    /**
     * Save Mass Major Unit Payment Option
     */

    function saveGlobalPaymentOption($options) {
        $this->savePaymentOption($options, null);
    }
}
