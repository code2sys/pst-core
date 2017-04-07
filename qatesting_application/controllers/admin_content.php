<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Admin_Content extends Master_Controller {

	function __construct()
	{
		parent::__construct();
		if(!@$_SESSION['userRecord']['admin'])
			redirect('welcome');
		$this->load->model('admin_m');
		$this->setNav('admin/nav_v', 1);
		//$this->output->enable_profiler(TRUE);
  	}
  	
  	private function validateEmailSettingsForm()
  	{
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
  	
  	private function validatePages()
  	{
		$this->load->library('form_validation');
	    $this->form_validation->set_rules('active', 'Active', 'xss_clean');
		return $this->form_validation->run();
  	}
  	
  	private function validateSliderImageSettingsForm()
  	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('page', 'Page', 'required|numeric|xss_clean');
		return $this->form_validation->run();
  	}
  	
  	private function validateSocialMedia()
  	{
	  	$this->load->library('form_validation');
	  	$this->form_validation->set_rules('sm_fblink', 'Facebook Link', 'trim|max_length[256]|prep_url|xss_clean');
	  	$this->form_validation->set_rules('sm_twlink', 'Twitter Link', 'trim|max_length[256]|prep_url|xss_clean');
	  	$this->form_validation->set_rules('sm_blglink', 'Blog Link', 'trim|max_length[256]|prep_url|xss_clean');
	  	$this->form_validation->set_rules('sm_ytlink', 'YouTube Link', 'trim|max_length[256]|prep_url|xss_clean');
	  	$this->form_validation->set_rules('sm_gplink', 'Google Plus Link', 'trim|max_length[256]|prep_url|xss_clean');
	  	$this->form_validation->set_rules('sm_gpid', 'Google Plus Page Id', 'trim|max_length[256]|xss_clean');
	  	return $this->form_validation->run();
  	}
  	
  	public function images($pageId = 0)
  	{
  		if($this->validateSliderImageSettingsForm() === TRUE)
  		{
		  	if(@$_FILES['image']['name'])
			{
				$config['max_height'] = '400';
				$config['max_width'] = '1024'; 
				$config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
				$this->load->model('file_handling_m');
				$data = $this->file_handling_m->add_new_file('image', $config);
				if(@$data['error'])
					$this->_mainData['errors'][] = $data['the_errors'];
				else
				{
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
		if(is_numeric($pageId))
		{
			$this->_mainData['bannerImages'] = $this->admin_m->getSliderImages($pageId);
			$this->renderMasterPage('admin/master_v', 'admin/upload_slider_images_v', $this->_mainData);
		}
		elseif($pageId = 'brand')
		{
			$this->renderMasterPage('admin/master_v', 'admin/upload_brand_images_v', $this->_mainData);
			
		}
  	}
  	
	public function remove_image($id, $pageId)
	{
		if(is_numeric($id))
		{
			$this->admin_m->removeImage($id, $this->config->item('upload_path'));  
			redirect('admin_content/images/'.$pageId);
		}
	}
  	
  	public function social_media()
  	{
  		if($this->validateSocialMedia() === TRUE)
  		{
	  		$this->_mainData['success'] = $this->admin_m->updateSMSettings($this->input->post());
  		}
  		$this->_mainData['SMSettings'] = $this->admin_m->getSMSettings();
	  	$this->renderMasterPage('admin/master_v', 'admin/sm_settings_v', $this->_mainData);
  	}
  	
  	public function reviews()
  	{
  		$this->_mainData['reviews'] = $this->admin_m->getNewReviews();
	  	$this->renderMasterPage('admin/master_v', 'admin/comments_v', $this->_mainData);
  	}
  	
  	public function review_approval($reviewId)
  	{
	  	if(is_numeric($reviewId))
	  	{
		  	$this->admin_m->approveReview($reviewId, $_SESSION['userRecord']['id']);
	  	}
	  	redirect('admin_content/reviews');
  	}
  	
  	public function review_reject($reviewId)
  	{
  		if(is_numeric($reviewId))
	  	{
	  		$this->admin_m->deleteReview($reviewId);
	  	}
	  	redirect('admin_content/reviews');
  	}
  	
  	public function pages()
  	{
  		$this->load->model('pages_m');
  		if( $this->validatePages() === TRUE)
  		{
	  		$this->pages_m->editPageActive($this->input->post());
	  		$this->_mainData['success'] = 'Your changes have been made.';
  		}
  		
  		$this->_mainData['pages'] = $this->pages_m->getPages();
	  	$this->renderMasterPage('admin/master_v', 'admin/pages/list_v', $this->_mainData);
  	}
  	
  	public function email()
  	{
  		if($this->validateEmailSettingsForm() === TRUE)
  		{
  			$uploadData = $this->input->post();
  			$this->load->model('file_handling_m');
  			if(@$_FILES['email_logo']['name'])
  			{
  				$config['max_height'] = '300';
  				$config['max_width'] = '500'; 
  				$config['allowed_types'] = 'jpg|jpeg|png|gif|tif';
	  			$data = $this->file_handling_m->add_new_file('email_logo', $config);
	  			if(@$data['error'])
	  				$this->_mainData['errors'][] = $data['the_errors'];
	  			else
	  				$uploadData['email_logo'] = $data['file_name'];
	  		}
	  		if(@$_FILES['mass_email_attachment_doc']['name'])
	  		{
		  		$data = $this->file_handling_m->add_new_file('mass_email_attachment_doc');
	  			if(@$data['error'])
	  				$this->_mainData['errors'][] = $data['the_errors'];
	  			else
	  				$uploadData['mass_email_attachment_doc'] = $data['file_name'];
	  		}
	  		if(@$_FILES['mass_email_list_doc']['name'])
	  		{
		  		$data = $this->file_handling_m->add_new_file('mass_email_list_doc');
	  			if(@$data['error'])
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
  	
  	public function download_emails(){	
	
		//$getUserEmails = $this->admin_m->getUserEmails();
		$getContactTable = $this->admin_m->getContactTable();
		$getNewsLetters = $this->admin_m->getNewsLetters();
		//array(0=>array('email'=>"All Users Emails")), $getUserEmails, 
		//0=>array('email'=>"All Contact Emails")),
		$newArray = array_merge(array(0=>array('email'=>"All Users Emails")), $getContactTable, array(0=>array('email'=>"All News Letters Emails")), $getNewsLetters);
		
		$csv = $this->array2csv($newArray);
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=emails.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $csv;
		exit;
		

  	}
  
	function array2csv(array &$array)
	{
		if (count($array) == 0) 
		{
			return null;
		}
		ob_start();
		$df = fopen("php://output", 'w');
		fputcsv($df, array_keys(reset($array)));
		foreach ($array as $row)
		{
			fputcsv($df, $row);
		}
		fclose($df);
		return ob_get_clean();
	}
  	
	public function debug($param){

		echo "<pre>";
		print_r($param);
		echo "</pre>";
		
	}

}