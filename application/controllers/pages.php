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
	
	public function validateServiceForm()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('fname', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('lname', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone No', 'required|numeric|xss_clean');
		$this->form_validation->set_rules('make', 'Make', 'required|xss_clean');
		$this->form_validation->set_rules('model', 'Model', 'required|xss_clean');
		$this->form_validation->set_rules('_year', 'Year', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('needs', 'Needs', 'required|xss_clean');
		$this->form_validation->set_rules('needs', 'Needs', 'required|xss_clean');
		$this->form_validation->set_rules('appointment', 'Appointment', 'xss_clean');
		return $this->form_validation->run();
	}
	
	public function validateFinanceForm()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('fname', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('lname', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('make', 'Make', 'required|xss_clean');
		$this->form_validation->set_rules('model', 'Model', 'required|xss_clean');
		$this->form_validation->set_rules('year', 'Year', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('initial', 'Initial', 'required|xss_clean');
		$this->form_validation->set_rules('type', 'Type', 'required|xss_clean');
		$this->form_validation->set_rules('condition', 'Condition', 'required|xss_clean');
		$this->form_validation->set_rules('down_payment', 'Down Payment', 'required|xss_clean');
		$this->form_validation->set_rules('contact_info[rphone]', 'Residence Phone', 'required|xss_clean');
		$this->form_validation->set_rules('contact_info[ssno]', 'Social Security Number', 'required|xss_clean');
		$this->form_validation->set_rules('contact_info[dob]', 'Date of Birth', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[paddress]', 'Physical Address', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[city]', 'City', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[state]', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[zip]', 'Zip', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[country]', 'Country', 'required|xss_clean');
		$this->form_validation->set_rules('housing_info[owns]', 'Do you rent or own your home, or other ?', 'required|xss_clean');
		$this->form_validation->set_rules('housing_info[rent]', 'Rent / Mortgage Monthly Amount', 'required|xss_clean');
		$this->form_validation->set_rules('housing_info[]', 'Time at Current Residence', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[occupation]', 'Occupation', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_name]', 'Employer Name', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_addr]', 'Employer Address', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_city]', 'Employer City', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[state]', 'Employer State', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_zip]', 'Employer Zip', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_phone]', 'Employer Phone', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[salary]', 'Salary(Annually Gross)', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[]', 'Time at Employer', 'required|xss_clean');
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
		$this->_mainData['showNotice'] = true;
		$this->_mainData['ssl'] = false;
  		if($this->validateTag($pageTag))
  		{
	  		$this->_mainData['pageRec'] = $this->pages_m->getPageRecByTag($pageTag);
			// echo "<pre>";
			// print_r($this->_mainData['pageRec']);exit;
			// echo "</pre>";
	  		$this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
	  		$this->setMasterPageVars('metatags', $this->_mainData['pageRec']['metatags']);
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
			$notice = $this->pages_m->getTextBoxes($this->_mainData['pageRec']['id']);
			$this->_mainData['notice'] = $notice[0]['text'];
			$this->_mainData['widgetBlock'] = $this->pages_m->widgetCreator($this->_mainData['pageRec']['id'], $this->_mainData['pageRec']);
			$this->_mainData['pages'] = $this->pages_m->getPages(1);
			$this->loadSidebar('widgets/info_v');
			
			if($pageTag == 'shippingquestions')
			{
				$block = $this->_mainData['widgetBlock'];
				//$this->_mainData['widgetBlock'] = '<img src="'.$this->_mainData['assets'].'/images/Truck_with_Logo.jpg"/>';
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
			
			if($pageTag == 'servicerequest')
	  		{
				if(( !isset($_SERVER['HTTPS'] ) ) ){
					redirect($this->_mainData['s_baseURL'] . 'pages/index/servicerequest');
				}
	  			$this->processServiceForm();
		  		$block = $this->_mainData['widgetBlock'];
				//$this->load->helper('easy_captcha_helper');
				//$this->_mainData['captcha'] = getCaptchaDisplayElements();
				//$this->_mainData['widgetBlock'] .= $this->loadGoggleMaps();
				$this->_mainData['showNotice'] = false;
				$this->_mainData['widgetBlock'] .= $this->load->view('info/service_request', $this->_mainData, TRUE);
				$this->_mainData['ssl'] = true;
				$this->_mainData['widgetBlock'] .= $block;
	  		}
			
			if($pageTag == 'financerequest')
	  		{
				if(( !isset($_SERVER['HTTPS'] ) ) ){
					redirect($this->_mainData['s_baseURL'] . 'pages/index/financerequest');
				}
	  			$this->processCreditForm();
		  		$block = $this->_mainData['widgetBlock'];
				//$this->load->helper('easy_captcha_helper');
				//$this->_mainData['captcha'] = getCaptchaDisplayElements();
				//$this->_mainData['widgetBlock'] .= $this->loadGoggleMaps();
				$this->_mainData['showNotice'] = false;
				$this->_mainData['states'] = $this->load_states();
				$this->_mainData['widgetBlock'] .= $this->load->view('info/finance_request', $this->_mainData, TRUE);
				$this->_mainData['ssl'] = true;
				$this->_mainData['widgetBlock'] .= $block;
	  		}
	  		
	  		$this->setNav('master/navigation_v', 0);
	  		$this->renderMasterPage('master/master_v', 'info/ride_home_v', $this->_mainData);
	  		
	  	}
	  	else
	  	redirect(base_url());
  	}

	public function load_states($ajax = FALSE)
	{
		$states = $this->account_m->getTerritories('US');
		if($ajax)
			echo json_encode($states);
		else
			return $states;
	}
  	
	private function processCreditForm() {
	  	if ($this->validateFinanceForm() === TRUE) {
			$financeEmail = $this->pages_m->getFinanceEmail();
			
			$this->load->model("account_m");
			$post = $this->input->post();
			$data = array();
			
			$data['initial'] = $post['initial'];
			$data['type'] = $post['type'];
			$data['condition'] = $post['condition'];
			$data['year'] = $post['year'];
			$data['make'] = $post['make'];
			$data['model'] = $post['model'];
			$data['down_payment'] = $post['down_payment'];
			$data['first_name'] = $post['fname'];
			$data['last_name'] = $post['lname'];
			$data['driver_licence'] = $post['dl'];
			$data['email'] = $post['email'];
			$data['contact_info'] = json_encode($post['contact_info']);
			$data['physical_address'] = json_encode($post['physical_address']);
			$data['housing_info'] = json_encode($post['housing_info']);
			$data['banking_info'] = json_encode($post['banking_info']);
			$data['previous_add'] = json_encode($post['previous_add']);
			$data['employer_info'] = json_encode($post['employer_info']);
			$data['reference'] = json_encode($post['reference']);
			$data['application_date'] = date('Y-m-d H:i:s');
			$this->account_m->creditApplication($data);
			//redirect(base_url('pages/index/financerequest'));

			// Send email
			$this->config->load('sitesettings');
			
			$mailData = array('toEmailAddress' => $financeEmail,
  	                    'subject' => 'You Have a new Credit Application',
  	                    'fromEmailAddress' => $this->input->post('email'),
  	                    'fromName' => $this->input->post('name'),
  	                    'replyToEmailAddress' => $this->input->post('email'),
  	                    'replyToName' => $this->config->item('replyToName'));
			$templateData = $post;

			$htmlTemplate = 'email/financerequest_html_v';
			$textTemplate = 'email/financerequest_html_v';

			$templateData['emailBodyImg'] = site_url('assets/email_images/email_body.jpg');
			$templateData['emailFooterImg'] = site_url('assets/email_images/email_footer.png');
			$templateData['emailHeadImg'] = site_url('assets/email_images/email_head.jpg');
			$templateData['emailShadowImg'] = site_url('assets/email_images/email_shadow.png');
			$this->load->model('mail_gen_m');
			$this->_mainData['success'] = $this->mail_gen_m->generateFromView($mailData, $templateData, $htmlTemplate, $textTemplate);
		}
	}
	
	private function processServiceForm()
  	{
	  	if ($this->validateServiceForm() === TRUE)
		{

			// Send email
			$this->config->load('sitesettings');
			$serviceEmail = $this->pages_m->getServiceEmail();
			//$serviceEmail = "bdvojcek@yahoo.com";
			//echo $serviceEmail;exit;
			
			$mailData = array('toEmailAddress' => $serviceEmail,
  	                    'subject' => 'Service Schedule Request',
  	                    'fromEmailAddress' => $this->input->post('email'),
  	                    'fromName' => $this->input->post('name'),
  	                    'replyToEmailAddress' => $this->input->post('email'),
  	                    'replyToName' => $this->config->item('replyToName'));
			$templateData = array(
					'fname' => $this->input->post('fname'),
					'lname' => $this->input->post('lname'),
					'email' => $this->input->post('email'),
					'phone' => $this->input->post('phone'),
					'address' => $this->input->post('address'),
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'),
					'zipcode' => $this->input->post('zipcode'),
					'make' => $this->input->post('make'),
					'model' => $this->input->post('model'),
					'_year' => $this->input->post('_year'),
					'vin' => $this->input->post('vin'),
					'miles' => $this->input->post('miles'),
					'needs' => $this->input->post('needs'),
					'appointment' => $this->input->post('appointment'),
					'serviced' => $this->input->post('serviced'),
					'lastin' => $this->input->post('lastin'),
					'workdone' => $this->input->post('workdone'),
					'company' => $this->input->post('company'),
					'company_name' => $this->config->item('company_name')
			);

			$textTemplate = 'email/servicerequest_html_v';
			$htmlTemplate = 'email/servicerequest_html_v';

  		$templateData['emailBodyImg'] = site_url('assets/email_images/email_body.jpg');
  		$templateData['emailFooterImg'] = site_url('assets/email_images/email_footer.png');
  		$templateData['emailHeadImg'] = site_url('assets/email_images/email_head.jpg');
  		$templateData['emailShadowImg'] = site_url('assets/email_images/email_shadow.png');
  		$this->load->model('mail_gen_m');
  		$this->_mainData['success'] = $this->mail_gen_m->generateFromView($mailData, $templateData, $htmlTemplate, $textTemplate);
		}
  	}
	
  	private function loadGoggleMaps()
  	{
		//echo $this->config->item('googleLocation');
		//$googleLocation = $this->_mainData['store_name']['company'].','.$this->_mainData['store_name']['city'].'+'.$this->_mainData['store_name']['state'];
		$googleLocation = $this->_mainData['store_name']['company'].','.$this->_mainData['store_name']['city'].'+'.$this->_mainData['store_name']['state'];
  		$str = '<iframe width="600" height="450" frameborder="0" style="border:0" 
  								src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDUJ3ePr2rnfcvky1_M8Vc2pQ7k1JGIKcI&q='.$googleLocation.'">
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

			if ($pageId == NULL) {
				$post["active"] = 1;
			}

  			if(@$post['location'])
  				$post['location'] = implode(',', $post['location']);
  			else
  				$post['location'] = '';
			$count = count($this->_mainData['widgets']);
			for($i = 0; $i < $count; $i++)
				unset($post['widgets'][$i]);

			//$post['widgets'] = array_unique($post['widgets']);
			// echo "<pre>";
			// echo $count;
			// print_r($post);exit;
			// echo "</pre>";
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
