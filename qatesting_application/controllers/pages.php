<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Pages extends Master_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('pages_m');
		//$this->output->enable_profiler(TRUE);
  	}
  	
  	private function validatePage()
	{
		$this->load->library('form_validation');
	  	$this->form_validation->set_rules('label', 'Page Name', 'required|xss_clean');
	  	$this->form_validation->set_rules('title', 'Meta Title', 'required|xss_clean');
	  	$this->form_validation->set_rules('keywords', 'Keywords', 'xss_clean');
	  	$this->form_validation->set_rules('metatags', 'Metatags', 'xss_clean');
	  	$this->form_validation->set_rules('css', 'CSS', 'xss_clean');
	  	$this->form_validation->set_rules('javascript', 'Javascript', 'xss_clean');
	  	$this->form_validation->set_rules('widget', 'Widgets', 'xss_clean');
	  	$this->form_validation->set_rules('icon', 'Icon', 'xss_clean');
	  	$this->form_validation->set_rules('location', 'location', 'xss_clean');
		return $this->form_validation->run();
	}
	
	private function validateTextBox()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('text', 'Text', 'xss_clean');
		$this->form_validation->set_rules('pageId', 'Page', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('order', 'Set', 'required|numeric|xss_clean');
		return $this->form_validation->run();
	} 
	
	 private function validateSliderImageSettingsForm()
  	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('page', 'Page', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('order', 'Set', 'required|numeric|xss_clean');
		return $this->form_validation->run();
  	}
  	
  	public function validateContactForm()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('subject', 'Subject', 'required|xss_clean');
		$this->form_validation->set_rules('message', 'Message', 'xss_clean');
		$this->form_validation->set_rules('user_answer', 'Math Question', 'required|integer|callback__processCaptcha');
		return $this->form_validation->run();
	}
	
	public function _processCaptcha()
	{
		$this->load->helper('easy_captcha_helper');
		if(validateCaptcha($this->input->post('encrypted_answer'), $this->input->post('user_answer')))
			return TRUE;
		else
		{
			$this->form_validation->set_message('_processCaptcha', 'The %s is incorrect.');
			return FALSE;
		}
	}
  	
  	private function validateTag($text)
  	{
		if(!empty($text))
		{
			if (preg_match('/^[\w]+$/', $text) == 1) {
			    return TRUE;
			}
			else 
			{
			    return FALSE;
			}
		}
		return FALSE;
	}
  	
  	public function index($pageTag = NULL)
  	{
  		if($this->validateTag($pageTag))
  		{
	  		$this->_mainData['pageRec'] = $this->pages_m->getPageRecByTag($pageTag);
	  		$this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
	  		$this->setMasterPageVars('metatag', html_entity_decode($this->_mainData['pageRec']['metatags']));
	  		$this->setMasterPageVars('css', html_entity_decode($this->_mainData['pageRec']['css']));
	  		$this->setMasterPageVars('script', html_entity_decode($this->_mainData['pageRec']['javascript']));
	  		$this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
	  		$this->_mainData['new_header']  = 1;
			$this->setFooterView('master/footer_v.php');
	  		
	  		$this->load->model('parts_m');
			$this->loadSidebar('widgets/garage_v');
	    	
			$this->_mainData['machines'] = $this->parts_m->getMachinesDd();
	    	$this->_mainData['rideSelector'] = $this->load->view('widgets/ride_select_v', $this->_mainData, TRUE);
	    	
	    	$this->_mainData['shippingBar'] = $this->load->view('info/shipping_bar_v', $this->_mainData, TRUE);
	    	$this->_mainData['brandSlider'] = $this->load->view('info/brand_slider_v', $this->_mainData, TRUE);
	    	
	    	$this->load->model('pages_m');
			$this->_mainData['pageRec'] = $this->pages_m->getPageRec($this->_mainData['pageRec']['id']);
			$this->_mainData['widgetBlock'] = $this->pages_m->widgetCreator($this->_mainData['pageRec']['id'], $this->_mainData['pageRec']);
			$this->_mainData['pages'] = $this->pages_m->getPages(1);
			$this->loadSidebar('widgets/info_v');
			
			if($pageTag == 'shippingquestions')
			{
				$block = $this->_mainData['widgetBlock'];
				$this->_mainData['widgetBlock'] = '<img src="'.$this->_mainData['assets'].'/images/Truck_with_Logo.jpg"/>';
				$this->_mainData['widgetBlock'] .= $block;
			}
	  		
			if($pageTag == 'contactus')
	  		{
	  			$this->processContactForm();
		  		$block = $this->_mainData['widgetBlock'];
				$this->load->helper('easy_captcha_helper');
				$this->_mainData['captcha'] = getCaptchaDisplayElements();
				$this->_mainData['widgetBlock'] .= $this->loadGoggleMaps();
				$this->_mainData['widgetBlock'] .= $this->load->view('info/contact_v', $this->_mainData, TRUE);
				$this->_mainData['widgetBlock'] .= $block;
	  		}
	  		
	  		$this->setNav('master/navigation_v', 0);
	  		$this->renderMasterPage('master/master_v', 'info/ride_home_v', $this->_mainData);
	  		
	  	}
	  	else
	  	redirect(base_url());
  	}
  	
  	private function loadGoggleMaps()
  	{
  		$str = '<iframe width="600" height="450" frameborder="0" style="border:0" 
  								src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDUJ3ePr2rnfcvky1_M8Vc2pQ7k1JGIKcI&q='.$this->config->item('googleLocation').'">
		</iframe>';
		return $str;
  	}
  	
  	private function processContactForm()
  	{
	  	if ($this->validateContactForm() === TRUE)
		{

			// Send email
			$this->config->load('sitesettings');
			
			$mailData = array('toEmailAddress' => $this->config->item('contactToEmail'),
  	                    'subject' => $this->input->post('subject'),
  	                    'fromEmailAddress' => $this->input->post('email'),
  	                    'fromName' => $this->input->post('name'),
  	                    'replyToEmailAddress' => $this->input->post('email'),
  	                    'replyToName' => $this->config->item('replyToName'));
			$templateData = array(
					'message' => $this->input->post('message'),
					'email' => $this->input->post('email'),
					'name' => $this->input->post('name'),
					'company' => $this->input->post('company'),
					'company_name' => $this->config->item('company_name')
			);

			$textTemplate = 'email/contactus_text_v';
			$htmlTemplate = 'email/contactus_html_v';

  		$templateData['emailBodyImg'] = site_url('assets/email_images/email_body.jpg');
  		$templateData['emailFooterImg'] = site_url('assets/email_images/email_footer.png');
  		$templateData['emailHeadImg'] = site_url('assets/email_images/email_head.jpg');
  		$templateData['emailShadowImg'] = site_url('assets/email_images/email_shadow.png');
  		$this->load->model('mail_gen_m');                                               
  		$this->_mainData['success'] = $this->mail_gen_m->generateFromView($mailData, $templateData, $htmlTemplate, $textTemplate); 
  		
		}
  	}
  	
  	public function edit($pageId = NULL)
  	{
  		$this->_mainData['widgets'] = $this->pages_m->getWidgets();
  		if(!empty($_POST))
  		{
	  		$_POST['css'] = htmlentities(@$_POST['css']);
	  		$_POST['javascript'] = htmlentities(@$_POST['javascript']);
  		}
  		if($this->validatePage() === TRUE)
  		{
  			$post = $this->input->post();
  			if(@$post['location'])
  				$post['location'] = implode(',', $post['location']);
  			else
  				$post['location'] = '';
			$count = count($this->_mainData['widgets']);
			for($i = 0; $i < $count; $i++)
				unset($post['widgets'][$i]);
  			$newId = $this->pages_m->editPage($post);
  			if(is_numeric($pageId) && ($newId > 1))
  				$pageId = $newId;
  			elseif($newId > 1)
  				redirect('pages/edit/'.$newId);
  		}
  		if(is_numeric($pageId))
  		{
	  		$this->_mainData['pageRec'] = $this->pages_m->getPageRec($pageId);
	  		$this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
	  		$this->setMasterPageVars('title', $this->_mainData['pageRec']['title']);
	  		$this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
	  		$this->_mainData['pageRec']['location'] = explode(',', $this->_mainData['pageRec']['location']);
	  		$this->_mainData['pageRec']['widgets'] = json_decode($this->_mainData['pageRec']['widgets'], TRUE);
	  		$this->_mainData['bannerImages'] = $this->admin_m->getSliderImages($pageId);
	  		$this->_mainData['textboxes'] = $this->pages_m->getTextBoxes($pageId);
  		}
  		if(is_array(@$_SESSION['errors']))
  		{
	  		$this->_mainData['errors'] = $_SESSION['errors'];
	  		unset($_SESSION['errors']);
  		}
  			
  		$this->_mainData['location'] = array('footer' => 'Footer', 'comp_info' => 'Company Info');
  		$this->_mainData['widgets'] = $this->pages_m->getWidgets();
  		$js = '<script type="text/javascript" src="' . $this->_mainData['assets'] . '/js/ckeditor/ckeditor.js"></script>';
  		$this->loadJS($js);
  		$this->_mainData['edit_config'] = $this->_mainData['assets'] . '/js/htmleditor.js';
  		
  		$this->setNav('admin/nav_v', 1);
	  	$this->renderMasterPage('admin/master_v', 'admin/pages/edit_v', $this->_mainData);
  	}
  	
  	public function delete($pageId = NULL)
  	{
	 	if(is_numeric($pageId))
	 	{
		 	$this->pages_m->deletePage($pageId);
	 	}
	 	redirect('admin_content/pages');
  	}
  	
  	public function addTextBox()
  	{
	  	if($this->validateTextBox() === TRUE)
	    {
	      $this->pages_m->updateTextBox($this->input->post());
	      redirect('pages/edit/'.$this->input->post('pageId'));
	    }
  	}
  	
  	public function addImages()
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
					$_SESSION['errors'][] = $data['the_errors'];
				else
				{
					$uploadData['image'] = $data['file_name'];
					$uploadData['pageId'] = $this->input->post('page');
					$uploadData['order'] = $this->input->post('order');
					$this->admin_m->updateSlider($uploadData);
					redirect('pages/edit/'.$this->input->post('page'));
				}	
	  		}
		}
		redirect('pages/edit/'.$this->input->post('page'));
  	}
  	
  	public function remove_image($id, $pageId)
	{
		if(is_numeric($id))
		{
			$this->admin_m->removeImage($id, $this->config->item('upload_path'));  
			redirect('pages/edit/'.$pageId);
		}
	}
  	
 }