<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');
class Blog extends Master_Controller 
{

  function __construct()
  {
  	parent::__construct();
    $this->setFooterView('master/footer_v.php');
    $this->load->model('blog_m');
  	//$this->output->enable_profiler(TRUE);
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
  
  public function validateBlogComment()
  {
    $this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('message', 'Comment', 'xss_clean');
		$this->form_validation->set_rules('user_answer', 'Math Question', 'required|integer|callback__processCaptcha');
	  return $this->form_validation->run();
  }
  
  
  public function index()
  {
    $this->load->helper('easy_captcha_helper');
    $this->_mainData['captcha'] = getCaptchaDisplayElements();
    $this->_mainData['media'] = $this->config->item('media');
    $this->_mainData['blogs'] = $this->blog_m->getBlogs();
    $this->setNav('master/navigation_v', 5);
		$this->renderMasterPage('master/master_v', 'info/blog_photo_v', $this->_mainData);
  }
  
  public function comment($id)
  {
    if(($this->validateBlogComment() ===TRUE) && is_numeric($id))
    {
      $this->blog_m->createComment($id, $this->input->post());
      redirect('blog');
    }
    $this->loadJS("<script> $('#".$id."').toggle(); </script> ");
    $this->index();
  }
  
}