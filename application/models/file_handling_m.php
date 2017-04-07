<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class File_Handling_M extends Master_M {

    function __construct() {
        parent::__construct();
    }

    public function add_new_file($fieldName, $config = array()) {
        // Process the uploaded file
        $config['file_name'] = time();
        $config['upload_path'] = $this->config->item('upload_path');
        if (!@$config['allowed_types'])
            $config['allowed_types'] = 'pdf|xls|xlsx|doc|docx|rtf|jpg|jpeg|png|mov|mp4|m4v|zip';
        if (!@$config['max_size'])
            $config['max_size'] = '102400'; // 100MB in KB
            
//$config['file_name'] = $this->_generate_filename();

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($fieldName)) {
            $error['the_errors'] = $this->upload->display_errors();
            // What are we doing to get errors long term?
            $error['processed'] = 0;
            $error['error'] = TRUE;
            return $error;
        } else
            return $this->upload->data();
    }

    public function add_new_file_brand($fieldName, $config = array()) {
        // Process the uploaded file
        //$config['file_name'] = time();
        $config['upload_path'] = $this->config->item('upload_path');
        if (!@$config['allowed_types'])
            $config['allowed_types'] = 'pdf|xls|xlsx|doc|docx|rtf|jpg|jpeg|png|mov|mp4|m4v|zip';
        if (!@$config['max_size'])
            $config['max_size'] = '102400'; // 100MB in KB
            
//$config['file_name'] = $this->_generate_filename();

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($fieldName)) {
            $error['the_errors'] = $this->upload->display_errors();
            // What are we doing to get errors long term?
            $error['processed'] = 0;
            $error['error'] = TRUE;
            return $error;
        } else
            return $this->upload->data();
    }

    public function add_new_file_brandSizeChart($fieldName, $config = array()) {
        // Process the uploaded file
        //$config['file_name'] = time();
        $config['upload_path'] = $this->config->item('upload_path');
        if (!@$config['allowed_types'])
            $config['allowed_types'] = 'pdf|xls|xlsx|doc|docx|rtf|jpg|jpeg|png|mov|mp4|m4v|zip';
        if (!@$config['max_size'])
            $config['max_size'] = '102400'; // 100MB in KB
            
//$config['file_name'] = $this->_generate_filename();

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($fieldName)) {
            $error['the_errors'] = $this->upload->display_errors();
            // What are we doing to get errors long term?
            $error['processed'] = 0;
            $error['error'] = TRUE;
            return $error;
        } else
            return $this->upload->data();
    }

}
