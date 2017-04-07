<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');

class Email extends Master_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  
  function index()
  {
    $mailData = array(
                      'toEmailAddress' => 'test@testemail.com',
                      'subject' => 'Test Email');
	  // Create the Mail Template Data
	  $mailTemplateData = array('assets' => $this->_mainData['assets']);
	  // Generate the Password Verification Email to the User
	  $this->load->model('mail_gen_m');                                               
	  $ret = $this->mail_gen_m->generateFromView($mailData, 
	  																						$mailTemplateData, 
	  																						'email/efile_html_v',
	  																						'email/efile_text_v',
	  																						array($this->config->item('attachments').'About_Stacks.pdf'));
	  if(!$ret)
	  {
  	  switch($this->mail_gen_m->getErrorCode())
  	  {
  	    case '-101' :
  	      echo "Invalid Attachment";
  	      break;
  	    case '-100' :
  	      $this->load->library('DataValidation');
          print_r($this->datavalidation->errors());
          break;
        default:
          echo "Unknown Error";
  	  }
	  }

  }
  
  
}

?>