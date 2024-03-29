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

	  	$type = array_key_exists("type", $_REQUEST) ? $_REQUEST["type"] : "Managed Page";

	  	switch ($type) {
            case "Managed Page":
                $this->form_validation->set_rules('keywords', 'Keywords', 'xss_clean');
                $this->form_validation->set_rules('metatags', 'Metatags', 'xss_clean');
                $this->form_validation->set_rules('widget', 'Widgets', 'xss_clean');
                $this->form_validation->set_rules('icon', 'Icon', 'xss_clean');
                $this->form_validation->set_rules('title', 'Meta Title', 'required|xss_clean');

                break;

            case "External Link":
                $this->form_validation->set_rules('external_url', 'External Link', 'required|xss_clean');

                break;

            case "File Attachment":
                // check that the file exists...
                if (!array_key_exists("upload", $_FILES) || $_FILES["upload"]["size"] == 0) {
                    $this->form_validation->set_message("upload", "Sorry, no file received.");
                }
                break;
        }

	  	$this->form_validation->set_rules('location', 'location', 'xss_clean');
		if ($this->form_validation->run()) {
            // OK, did they request a tag?
            $this->load->model("pages_m");
            if (!array_key_exists("tag", $_REQUEST) || $_REQUEST["tag"] == "" || !array_key_exists("id", $_REQUEST) || $_REQUEST["id"] == 0) {
                return true;
            } else {
                if ($this->pages_m->tagIsAvailable($_REQUEST["tag"], $_REQUEST["id"])) {
                    $_SESSION["admin_pages_tag_error"] = false;
                    return true;
                } else {
                    $_SESSION["admin_pages_tag_error"] = true;
                    $_SESSION["admin_pages_tag_requested"] = $_REQUEST["tag"];
                    return false;
                }
            }
        } else {
		    return false;
        }
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
		$this->form_validation->set_rules('dl', 'Driver\'s License', 'required|xss_clean');
		$this->form_validation->set_rules('driver_licence_expiration', 'Driver\'s License Expiration', 'required|xss_clean');
		$this->form_validation->set_rules('type', 'Type', 'required|xss_clean');
		$this->form_validation->set_rules('condition', 'Condition', 'required|xss_clean');
		$this->form_validation->set_rules('down_payment', 'Down Payment', 'required|xss_clean');
		$this->form_validation->set_rules('contact_info[rphone]', 'Residence Phone', 'required|xss_clean');

        if (defined('LIFESTYLESHONDA_VIEW') && LIFESTYLESHONDA_VIEW){

        $this->form_validation->set_rules('contact_info[marital_status]', 'Marital Status', 'required|xss_clean');
		$this->form_validation->set_rules('contact_info[us_citizen]', 'US Citizen', 'required|xss_clean');

        }
        $this->form_validation->set_rules('physical_address[state]', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('contact_info[dob]', 'Date of Birth', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[paddress]', 'Physical Address', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[city]', 'City', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[state]', 'State', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[zip]', 'Zip', 'required|xss_clean');
		$this->form_validation->set_rules('physical_address[country]', 'Country', 'required|xss_clean');
		$this->form_validation->set_rules('housing_info[owns]', 'Do you rent or own your home, or other ?', 'required|xss_clean');
		$this->form_validation->set_rules('housing_info[rent]', 'Rent / Mortgage Monthly Amount', 'required|xss_clean');
		$this->form_validation->set_rules('housing_info[months]', 'Time at Current Residence(Month)', 'required|xss_clean');
		$this->form_validation->set_rules('housing_info[years]', 'Time at Current Residence(Year)', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[occupation]', 'Occupation', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_name]', 'Employer Name', 'required|xss_clean');

        if (defined('BLUFFPOWERSPORTS_VIEW') && !BLUFFPOWERSPORTS_VIEW ){
            
            $this->form_validation->set_rules('employer_info[emp_addr]', 'Employer Address', 'required|xss_clean');
        }
		
		$this->form_validation->set_rules('employer_info[emp_city]', 'Employer City', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[state]', 'Employer State', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_zip]', 'Employer Zip', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_phone]', 'Employer Phone', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[salary]', 'Salary(Annually Gross)', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[month]', 'Time at Employer(Month)', 'required|xss_clean');
        $this->form_validation->set_rules('employer_info[year]', 'Time at Employer(Year)', 'required|xss_clean');

        if (defined('LIFESTYLESHONDA_VIEW') && LIFESTYLESHONDA_VIEW){

        $this->form_validation->set_rules('employer_info[relative_name]', 'Relative Name', 'required|xss_clean');
        $this->form_validation->set_rules('employer_info[relative_phone]', 'Relative Phone', 'required|xss_clean');
        $this->form_validation->set_rules('employer_info[relative_city]', 'Relative City', 'required|xss_clean');
        $this->form_validation->set_rules('employer_info[relative_state]', 'Relative State', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[relative_relationship]', 'Relationship with relative ', 'required|xss_clean');

        }

		// If they've been there for less than 2 years, it's required
        if (intVal($_REQUEST['housing_info']['years']) < 2) {
            $this->form_validation->set_rules('previous_add[address]', 'Previous Residence Address (Under 2 years at current address)', 'required|xss_clean');
            $this->form_validation->set_rules('previous_add[city]', 'Previous Residence City (Under 2 years at current address)', 'required|xss_clean');
            $this->form_validation->set_rules('previous_add[state]', 'Previous Residence State (Under 2 years at current address)', 'required|xss_clean');
            $this->form_validation->set_rules('previous_add[zip]', 'Previous Residence Zip  (Under 2 years at current address)', 'required|xss_clean');
        }

        // If they've worked there less than 2 years,it/s required to give an additional record...
        if ($_REQUEST['employer_info']['year'] < 2) {
            $this->form_validation->set_rules('prior_employer_info[occupation]', 'Previous Occupation (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_name]', 'Previous Employer Name (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_addr]', 'Previous Employer Address (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_city]', 'Previous Employer City (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[state]', 'Previous Employer State (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_zip]', 'Previous Employer Zip (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_phone]', 'Previous Employer Phone (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[salary]', 'Previous Salary(Annually Gross) (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[month]', 'Previous Time at Employer(Month) (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[year]', 'Previous Time at Employer(Year) (Under 2 years at current position)', 'required|xss_clean');
        }

        if (array_key_exists("joint", $_REQUEST) && $_REQUEST["joint"] > 0) {
            // We have to add these for the joint application as well..
            $this->form_validation->set_rules('co_fname', 'Co-Applicant First Name', 'required|xss_clean');
            $this->form_validation->set_rules('co_lname', 'Co-Applicant Last Name', 'required|xss_clean');
            $this->form_validation->set_rules('co_email', 'Co-Applicant Email', 'required|valid_email|xss_clean');
            $this->form_validation->set_rules('co_dl', 'Co-Applicant Driver\'s License', 'required|xss_clean');
            $this->form_validation->set_rules('co_driver_licence_expiration', 'Co-Applicant Driver\'s License Expiration', 'required|xss_clean');
            $this->form_validation->set_rules('co_initial', 'Co-Applicant Initial', 'required|xss_clean');
            $this->form_validation->set_rules('co_contact_info[rphone]', 'Co-Applicant Residence Phone', 'required|xss_clean');
            $this->form_validation->set_rules('co_contact_info[ssno]', 'Co-Applicant Social Security Number', 'required|xss_clean');
            $this->form_validation->set_rules('co_contact_info[dob]', 'Co-Applicant Date of Birth', 'required|xss_clean');
            $this->form_validation->set_rules('co_physical_address[paddress]', 'Co-Applicant Physical Address', 'required|xss_clean');
            $this->form_validation->set_rules('co_physical_address[city]', 'Co-Applicant City', 'required|xss_clean');
            $this->form_validation->set_rules('co_physical_address[state]', 'Co-Applicant State', 'required|xss_clean');
            $this->form_validation->set_rules('co_physical_address[zip]', 'Co-Applicant Zip', 'required|xss_clean');
            $this->form_validation->set_rules('co_physical_address[country]', 'Co-Applicant Country', 'required|xss_clean');
            $this->form_validation->set_rules('co_housing_info[owns]', 'Co-Applicant Do you rent or own your home, or other ?', 'required|xss_clean');
            $this->form_validation->set_rules('co_housing_info[rent]', 'Co-Applicant Rent / Mortgage Monthly Amount', 'required|xss_clean');
            $this->form_validation->set_rules('co_housing_info[months]', 'Co-Applicant Time at Current Residence(Month)', 'required|xss_clean');
            $this->form_validation->set_rules('co_housing_info[years]', 'Co-Applicant Time at Current Residence(Year)', 'required|xss_clean');
            // Brandt said yes emlpoyer is required for both.
            $this->form_validation->set_rules('co_employer_info[occupation]', 'Co-Applicant Occupation', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_name]', 'Co-Applicant Employer Name', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_addr]', 'Co-Applicant Employer Address', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_city]', 'Co-Applicant Employer City', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[state]', 'Co-Applicant Employer State', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_zip]', 'Co-Applicant Employer Zip', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_phone]', 'Co-Applicant Employer Phone', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[salary]', 'Co-Applicant Salary(Annually Gross)', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[month]', 'Co-Applicant Time at Employer(Month)', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[year]', 'Co-Applicant Time at Employer(Year)', 'required|xss_clean');

            if ($_REQUEST['co_housing_info']['years'] < 2) {
                $this->form_validation->set_rules('co_previous_add[address]', 'Co-Applicant Previous Residence Address (Under 2 years at current address)', 'required|xss_clean');
                $this->form_validation->set_rules('co_previous_add[city]', 'Co-Applicant Previous Residence City (Under 2 years at current address)', 'required|xss_clean');
                $this->form_validation->set_rules('co_previous_add[state]', 'Co-Applicant Previous Residence State (Under 2 years at current address)', 'required|xss_clean');
                $this->form_validation->set_rules('co_previous_add[zip]', 'Co-Applicant Previous Residence Zip  (Under 2 years at current address)', 'required|xss_clean');
            }

            if ($_REQUEST['co_employer_info']['year'] < 2) {
                $this->form_validation->set_rules('co_prior_employer_info[occupation]', 'Co-Applicant Previous Occupation (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_name]', 'Co-Applicant Previous Employer Name (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_addr]', 'Co-Applicant Previous Employer Address (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_city]', 'Co-Applicant Previous Employer City (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[state]', 'Co-Applicant Previous Employer State (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_zip]', 'Co-Applicant Previous Employer Zip (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_phone]', 'Co-Applicant Previous Employer Phone (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[salary]', 'Co-Applicant Previous Salary(Annually Gross) (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[month]', 'Co-Applicant Previous Time at Employer(Month) (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[year]', 'Co-Applicant Previous Time at Employer(Year) (Under 2 years at current position)', 'required|xss_clean');
            }

        }

        // At least three reference is required
        $this->form_validation->set_rules('reference[name1]', 'Reference Name (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[phone1]', 'Reference Phone (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[city1]', 'Reference City (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[state1]', 'Reference State (At least three reference is required; more are preferred.)', 'required|xss_clean');


        // At least two reference for LIFESTYLE and at least three for Bluffpowersports
        if (defined('BLUFFPOWERSPORTS_VIEW') && BLUFFPOWERSPORTS_VIEW ){

        $this->form_validation->set_rules('reference[name2]', 'Reference Name (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[phone2]', 'Reference Phone (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[city2]', 'Reference City (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[state2]', 'Reference State (At least three reference is required; more are preferred.)', 'required|xss_clean');

        $this->form_validation->set_rules('reference[name3]', 'Reference Name (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[phone3]', 'Reference Phone (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[city3]', 'Reference City (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[state3]', 'Reference State (At least three reference is required; more are preferred.)', 'required|xss_clean');

        }
        
        if (defined('LIFESTYLESHONDA_VIEW') && LIFESTYLESHONDA_VIEW){

        $this->form_validation->set_rules('reference[name2]', 'Reference Name (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[phone2]', 'Reference Phone (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[city2]', 'Reference City (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[state2]', 'Reference State (At least three reference is required; more are preferred.)', 'required|xss_clean');
        }

        // They must specify a bank...
        $this->form_validation->set_rules('banking_info[bank_name]', 'Bank Name', 'required|xss_clean');
        if (defined('BLUFFPOWERSPORTS_VIEW') && !BLUFFPOWERSPORTS_VIEW){

            $this->form_validation->set_rules('banking_info[ac_type]', 'Bank Account Types', 'required|xss_clean');
        }

		return $this->form_validation->run();
    }
    
    public function validateFinanceFormBLUFF()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('initial', 'Initial', 'required|xss_clean');
		$this->form_validation->set_rules('fname', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('lname', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('contact_info[dob]', 'Date of Birth', 'required|xss_clean');
        $this->form_validation->set_rules('contact_info[ssno]', 'Social Security Number', 'required|xss_clean');
		$this->form_validation->set_rules('contact_info[rphone]', 'Residence Phone', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|xss_clean');
        $this->form_validation->set_rules('dl', 'Driver\'s License', 'required|xss_clean');
        $this->form_validation->set_rules('driver_licence_state', 'Driver\'s License State', 'required|xss_clean');
        $this->form_validation->set_rules('driver_licence_expiration', 'Driver\'s License Expiration', 'required|xss_clean');        
        
        $this->form_validation->set_rules('housing_info[home_address]', 'Home address', 'required|xss_clean');
        $this->form_validation->set_rules('housing_info[county]', 'County', 'required|xss_clean');
        $this->form_validation->set_rules('housing_info[owns]', 'Do you rent or own your home, or other ?', 'required|xss_clean');
		$this->form_validation->set_rules('housing_info[rent]', 'Monthly Payment Amount', 'required|xss_clean');
        $this->form_validation->set_rules('housing_info[years]', 'Years at residence', 'required|xss_clean');
        // If they've been there for less than 2 years, it's required
        if (intVal($_REQUEST['housing_info']['years']) < 2) {
            $this->form_validation->set_rules('previous_add[address]', 'Previous Residence Address (Under 2 years at current address)', 'required|xss_clean');
            $this->form_validation->set_rules('previous_add[city]', 'Previous Residence City (Under 2 years at current address)', 'required|xss_clean');
            $this->form_validation->set_rules('previous_add[state]', 'Previous Residence State (Under 2 years at current address)', 'required|xss_clean');
            $this->form_validation->set_rules('previous_add[zip]', 'Previous Residence Zip  (Under 2 years at current address)', 'required|xss_clean');
        }
        // They must specify a bank...
        $this->form_validation->set_rules('banking_info[bank_name]', 'Bank Name', 'required|xss_clean');

        $this->form_validation->set_rules('employer_info[occupation]', 'Occupation', 'required|xss_clean');
        $this->form_validation->set_rules('employer_info[emp_name]', 'Employer Name', 'required|xss_clean');
        $this->form_validation->set_rules('employer_info[emp_city]', 'Employer City', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[state]', 'Employer State', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_zip]', 'Employer Zip', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[emp_phone]', 'Employer Phone', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[salary]', 'Salary(Annually Gross)', 'required|xss_clean');
		$this->form_validation->set_rules('employer_info[month]', 'Time at Employer(Month)', 'required|xss_clean');
        $this->form_validation->set_rules('employer_info[year]', 'Time at Employer(Year)', 'required|xss_clean');
        // If they've worked there less than 2 years,it/s required to give an additional record...
        if ($_REQUEST['employer_info']['year'] < 2) {
            $this->form_validation->set_rules('prior_employer_info[occupation]', 'Previous Occupation (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_name]', 'Previous Employer Name (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_city]', 'Previous Employer City (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[state]', 'Previous Employer State (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_zip]', 'Previous Employer Zip (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[emp_phone]', 'Previous Employer Phone (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[salary]', 'Previous Salary(Annually Gross) (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[month]', 'Previous Time at Employer(Month) (Under 2 years at current position)', 'required|xss_clean');
            $this->form_validation->set_rules('prior_employer_info[year]', 'Previous Time at Employer(Year) (Under 2 years at current position)', 'required|xss_clean');
        }
        // At least three reference is required
        $this->form_validation->set_rules('reference[name1]', 'Reference Name (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[phone1]', 'Reference Phone (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[city1]', 'Reference City (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[state1]', 'Reference State (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[name2]', 'Reference Name (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[phone2]', 'Reference Phone (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[city2]', 'Reference City (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[state2]', 'Reference State (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[name3]', 'Reference Name (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[phone3]', 'Reference Phone (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[city3]', 'Reference City (At least three reference is required; more are preferred.)', 'required|xss_clean');
        $this->form_validation->set_rules('reference[state3]', 'Reference State (At least three reference is required; more are preferred.)', 'required|xss_clean');

		$this->form_validation->set_rules('type', 'Type', 'required|xss_clean');
		$this->form_validation->set_rules('condition', 'Condition', 'required|xss_clean');
		$this->form_validation->set_rules('year', 'Year', 'required|xss_clean');
		$this->form_validation->set_rules('make', 'Make', 'required|xss_clean');
		$this->form_validation->set_rules('model', 'Model', 'required|xss_clean');
        $this->form_validation->set_rules('down_payment', 'Down Payment', 'required|xss_clean');

        if (array_key_exists("joint", $_REQUEST) && $_REQUEST["joint"] > 0) {
            // We have to add these for the joint application as well..            
            $this->form_validation->set_rules('co_initial', 'Co-Applicant Initial', 'required|xss_clean');
            $this->form_validation->set_rules('co_fname', 'Co-Applicant First Name', 'required|xss_clean');
            $this->form_validation->set_rules('co_lname', 'Co-Applicant Last Name', 'required|xss_clean');
            $this->form_validation->set_rules('co_contact_info[dob]', 'Co-Applicant Date of Birth', 'required|xss_clean');
            $this->form_validation->set_rules('co_contact_info[ssno]', 'Co-Applicant Social Security Number', 'required|xss_clean');
            $this->form_validation->set_rules('co_contact_info[rphone]', 'Co-Applicant Residence Phone', 'required|xss_clean');
            $this->form_validation->set_rules('co_email', 'Co-Applicant Email', 'required|valid_email|xss_clean');
            $this->form_validation->set_rules('co_dl', 'Co-Applicant Driver\'s License', 'required|xss_clean');
            $this->form_validation->set_rules('co_driver_licence_state', 'Co-Applicant Driver\'s License State', 'required|xss_clean');
            $this->form_validation->set_rules('co_driver_licence_expiration', 'Co-Applicant Driver\'s License Expiration', 'required|xss_clean');

            $this->form_validation->set_rules('co_housing_info[home_address]', 'Co-Applicant Home address', 'required|xss_clean');
            $this->form_validation->set_rules('co_housing_info[county]', 'Co-Applicant County', 'required|xss_clean');
            $this->form_validation->set_rules('co_housing_info[owns]', 'Co-Applicant Do you rent or own your home, or other ?', 'required|xss_clean');
            $this->form_validation->set_rules('co_housing_info[rent]', 'Co-Applicant Monthly Payment Amount', 'required|xss_clean');
            $this->form_validation->set_rules('co_housing_info[years]', 'Co-Applicant Years at residence', 'required|xss_clean');
            
            if ($_REQUEST['co_housing_info']['years'] < 2) {
                $this->form_validation->set_rules('co_previous_add[address]', 'Co-Applicant Previous Residence Address (Under 2 years at current address)', 'required|xss_clean');
                $this->form_validation->set_rules('co_previous_add[city]', 'Co-Applicant Previous Residence City (Under 2 years at current address)', 'required|xss_clean');
                $this->form_validation->set_rules('co_previous_add[state]', 'Co-Applicant Previous Residence State (Under 2 years at current address)', 'required|xss_clean');
                $this->form_validation->set_rules('co_previous_add[zip]', 'Co-Applicant Previous Residence Zip  (Under 2 years at current address)', 'required|xss_clean');
            }

            $this->form_validation->set_rules('co_banking_info[bank_name]', 'Co-Applicant Bank Name', 'required|xss_clean');
            
            // Brandt said yes emlpoyer is required for both.
            $this->form_validation->set_rules('co_employer_info[occupation]', 'Co-Applicant Occupation', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_name]', 'Co-Applicant Employer Name', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_city]', 'Co-Applicant Employer City', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[state]', 'Co-Applicant Employer State', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_zip]', 'Co-Applicant Employer Zip', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[emp_phone]', 'Co-Applicant Employer Phone', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[pay_frequency]', 'Co-Applicant Pay Frequency', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[salary]', 'Co-Applicant Salary(Annually Gross)', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[month]', 'Co-Applicant Time at Employer(Month)', 'required|xss_clean');
            $this->form_validation->set_rules('co_employer_info[year]', 'Co-Applicant Time at Employer(Year)', 'required|xss_clean');            

            if ($_REQUEST['co_employer_info']['year'] < 2) {
                $this->form_validation->set_rules('co_prior_employer_info[occupation]', 'Co-Applicant Previous Occupation (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_name]', 'Co-Applicant Previous Employer Name (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_city]', 'Co-Applicant Previous Employer City (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[state]', 'Co-Applicant Previous Employer State (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_zip]', 'Co-Applicant Previous Employer Zip (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[emp_phone]', 'Co-Applicant Previous Employer Phone (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[pay_frequency]', 'Co-Applicant Previous Pay Frequency', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[salary]', 'Co-Applicant Previous Salary(Annually Gross) (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[month]', 'Co-Applicant Previous Time at Employer(Month) (Under 2 years at current position)', 'required|xss_clean');
                $this->form_validation->set_rules('co_prior_employer_info[year]', 'Co-Applicant Previous Time at Employer(Year) (Under 2 years at current position)', 'required|xss_clean');
            }

        }

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
			if (preg_match('/^[\w_-]+$/', $text) == 1) {
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
		$this->_mainData['showNotice'] = false;
		$this->_mainData['ssl'] = false;
  		if($this->validateTag($pageTag))
  		{
	  		$this->_mainData['pageRec'] = $this->pages_m->getPageRecByTag($pageTag);
	  		$this->setMasterPageVars("title", $this->_mainData["pageRec"]["title"]);

	  		// Handle links and file download...
            if ($this->_mainData['pageRec']['type'] == 'External Link') {
                // just redirect it.
                header("Location: " . $this->_mainData['pageRec']['external_url']);
                exit();
            } else if ($this->_mainData['pageRec']['type'] == 'File Attachment') {
                // serve the file
                jserve_file(STORE_DIRECTORY . '/attachments/' . $this->_mainData['pageRec']['attachment_filename'], $this->_mainData['pageRec']['original_filename'], $this->_mainData['pageRec']['attachment_mime_type']);
            }


			// echo "<pre>";
			// print_r($this->_mainData['pageRec']);exit;
			// echo "</pre>";
	  		$this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
	  		$this->setMasterPageVars('metatags', $this->_mainData['pageRec']['metatags']);
	  		$this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
	  		$this->setMasterPageVars('metatag', html_entity_decode($this->_mainData['pageRec']['metatags']));
	  		$this->setMasterPageVars('css', html_entity_decode($this->_mainData['pageRec']['css']));
	  		$this->setMasterPageVars('script', html_entity_decode($this->_mainData['pageRec']['javascript']));
	  		$this->_mainData['pages'] = $this->pages_m->getPages(1, 'footer');
	  		$this->_mainData['new_header']  = 1;
			$this->setFooterView('master/footer_v.php');
	  		
	  		$this->load->model('parts_m');
	  		// Turn off for regular pages?
			// $this->loadSidebar('widgets/garage_v');
	    	
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

			// Turn off for regular pages?
			// $this->loadSidebar('widgets/info_v');
			
			if($pageTag == 'shippingquestions')
			{
                // JLB 07-11-17
                // I have no idea why they do this stupid stuff with $block
				// $block = $this->_mainData['widgetBlock'];
				//$this->_mainData['widgetBlock'] = '<img src="'.$this->_mainData['assets'].'/images/Truck_with_Logo.jpg"/>';
				// $this->_mainData['widgetBlock'] .= $block;
			}
	  		
			if($pageTag == 'contactus')
	  		{
	  			$this->processContactForm();
		  		// $block = $this->_mainData['widgetBlock'];
				$this->load->helper('easy_captcha_helper');
				$this->_mainData['captcha'] = getCaptchaDisplayElements();

				// JLB 01-11-18
                // If there are really store hours, we have to show them....
                $CI =& get_instance();
                $CI->load->model("admin_m");
                $store_name = $CI->admin_m->getAdminShippingProfile();
                $this->_mainData['widgetBlock'] .= $this->load->view('info/store_hours', array("store_name" => $store_name), TRUE);


				$this->_mainData['widgetBlock'] .= $this->loadGoogleMaps();
				$this->_mainData['widgetBlock'] .= $this->load->view('info/contact_v', $this->_mainData, TRUE);
				// $this->_mainData['widgetBlock'] .= $block;
              }
              
            if($pageTag == 'sitemap')
            {
                $CI =& get_instance();
                $CI->load->model("admin_m");
                $CI->load->model("motorcycle_m");

                $this->_mainData['pageRec'] = $this->pages_m->getPageRec(0);

                $store_name = $CI->admin_m->getAdminShippingProfile();
                $this->_mainData['storeInfo'] = $store_name;

                $filter = array();
                $filter["status"] = 1;                
                $this->_mainData['motorcycles'] = $CI->motorcycle_m->getMotorcycles($filter, 0, 0);
                $title = "Inventory Site Map | ".$store_name['company']. " | ".$store_name['city']." ".$store_name['state'];
                $this->setMasterPageVars('title', $title);
                $this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
                $this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);

                $this->load->helper("jonathan");
                if(isMajorUnitShop()) {
                    $this->_mainData['widgetBlock'] .= $this->load->view('info/sitemap_v', $this->_mainData, TRUE);
                }
            }
			
			if($pageTag == 'servicerequest')
	  		{
				if(( !isset($_SERVER['HTTPS'] ) ) ){
					redirect($this->_mainData['s_baseURL'] . 'pages/index/servicerequest');
				}
	  			$this->processServiceForm();
		  		// $block = $this->_mainData['widgetBlock'];
				//$this->load->helper('easy_captcha_helper');
				//$this->_mainData['captcha'] = getCaptchaDisplayElements();
				//$this->_mainData['widgetBlock'] .= $this->loadGoogleMaps();
				$this->_mainData['showNotice'] = false;
				$this->_mainData['widgetBlock'] .= $this->load->view('info/service_request', $this->_mainData, TRUE);
				$this->_mainData['ssl'] = true;
				// $this->_mainData['widgetBlock'] .= $block;
	  		}
			
			if($pageTag == 'financerequest')
	  		{
				if(( !isset($_SERVER['HTTPS'] ) ) ){
					redirect($this->_mainData['s_baseURL'] . 'pages/index/financerequest');
                }
                if (defined('BLUFFPOWERSPORTS_VIEW') && BLUFFPOWERSPORTS_VIEW) {
                    $this->processCreditFormBLUFF();
                } else {                    
                    $this->processCreditForm();    
                }
		  		// $block = $this->_mainData['widgetBlock'];
				//$this->load->helper('easy_captcha_helper');
				//$this->_mainData['captcha'] = getCaptchaDisplayElements();
				//$this->_mainData['widgetBlock'] .= $this->loadGoogleMaps();
				$this->_mainData['showNotice'] = false;
				$this->_mainData['states'] = $this->load_states();
                $this->_mainData['widgetBlock'] = '<h1 style="color:#3f51b5">' . $this->_mainData['pageRec']['label'] .'</h1>' . $this->_mainData['widgetBlock'];
                if (defined('BLUFFPOWERSPORTS_VIEW') && BLUFFPOWERSPORTS_VIEW) {
                    $this->_mainData['widgetBlock'] .= $this->load->view('info/finance_request_bluff', $this->_mainData, TRUE);
                } else {
                    $this->_mainData['widgetBlock'] .= $this->load->view('info/finance_request', $this->_mainData, TRUE);
                }
				$this->_mainData['ssl'] = true;
				// $this->_mainData['widgetBlock'] .= $block;
	  		}

	  		$master_view = "master/master_v";
			$page_view = "info/ride_home_v";
			$embed_location_meta = false;
            $this->load->model("admin_m");
            $store_name = $this->admin_m->getAdminShippingProfile();
            $store_trailer = " | " . $store_name["company"] . " " . $store_name["city"] . " " . $store_name["state"];

	  		switch($this->_mainData['pageRec']["page_class"]) {
                case "Showroom Landing Page":
                    $master_view = "benz_views/header";
                    $page_view = "showcase/category_selector_v";
                    $embed_location_meta = true;
                    $this->_mainData["fancy_title"] = "Factory Showroom $store_trailer ";
                    break;

                case "Showroom Model":
                case "Showroom Make":
                case "Showroom Machine Type":
                    $master_view = "benz_views/header";
                    $page_view = "showcase/category_selector_v";
                    $embed_location_meta = true;
                    $this->_mainData["fancy_title"] = "Factory Showroom " . $this->_mainData["pageRec"]["title"] . " $store_trailer ";
                    break;

                case "Showroom Trim":
                    $master_view = "benz_views/header";
                    $page_view = "showcase/trim_view_motorcycle_v";
                    $embed_location_meta = true;
                    $this->_mainData["fancy_title"] = "Factory Showroom " . $this->_mainData["pageRec"]["title"] . " $store_trailer ";
                    break;
            }

            if ($embed_location_meta) {
                // get the store location...
                if (!array_key_exists("extra_meta_tags", $this->_mainData)) {
                    $this->_mainData["extra_meta_tags"] = "";
                }
                $this->_mainData["extra_meta_tags"] .= $this->load->view("showcase/location_metatags", $store_name, true);
            }

	  		$this->setNav('master/navigation_v', 0);
			$this->_mainData["full_info_content"] = 1;
	  		$this->renderMasterPage($master_view, $page_view, $this->_mainData);
	  		
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

			$data['joint'] = $post['joint'];
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
			$data['driver_licence_expiration'] = $post['driver_licence_expiration'];
			$data['email'] = $post['email'];
			$data['contact_info'] = json_encode($post['contact_info']);
			$data['physical_address'] = json_encode($post['physical_address']);
			$data['housing_info'] = json_encode($post['housing_info']);
			$data['banking_info'] = json_encode($post['banking_info']);
			$data['previous_add'] = json_encode($post['previous_add']);
			$data['employer_info'] = json_encode($post['employer_info']);
			$data['reference'] = json_encode($post['reference']);
			$data['application_date'] = date('Y-m-d H:i:s');

			if ($post["employer_info"]["year"] < 2) {
			    $data["prior_employer_info"] = json_encode($post['prior_employer_info']);
            } else {
			    $data["prior_employer_info"] = "{}";
            }

			if ($post['joint'] > 0) {
			    // we need to include co-applicant information as well
                $data['co_initial'] = $post['co_initial'];
                $data['co_first_name'] = $post['co_fname'];
                $data['co_last_name'] = $post['co_lname'];
                $data['co_driver_licence'] = $post['co_dl'];
                $data['co_driver_licence_expiration'] = $post['co_driver_licence_expiration'];
                $data['co_email'] = $post['co_email'];
                $data['co_contact_info'] = json_encode($post['co_contact_info']);
                $data['co_physical_address'] = json_encode($post['co_physical_address']);
                $data['co_housing_info'] = json_encode($post['co_housing_info']);
                $data['co_banking_info'] = json_encode($post['co_banking_info']);
                $data['co_previous_add'] = json_encode($post['co_previous_add']);
                $data['co_employer_info'] = json_encode($post['co_employer_info']);

                if ($post["co_employer_info"]["year"] < 2) {
                    $data["co_prior_employer_info"] = json_encode($post['co_prior_employer_info']);
                } else {
                    $data["co_prior_employer_info"] = "{}";
                }
            }

			$this->account_m->creditApplication($data);
			//redirect(base_url('pages/index/financerequest'));

			// Send email
			$this->config->load('sitesettings');
			
			$mailData = array('toEmailAddress' => $financeEmail,
  	                    'subject' => 'You Have a new Credit Application',
  	                    'fromEmailAddress' => "noreply@powersporttechnologies.com",
  	                    'fromName' => "Credit Apps Form",
  	                    'replyToEmailAddress' => $this->input->post('email'),
  	                    'replyToName' => $this->config->item('replyToName'));
			$templateData = $post;

			$htmlTemplate = 'email/financerequest_html_v';
			$textTemplate = 'email/financerequest_html_v';

			$templateData['emailBodyImg'] = site_url('assets/email_images/email_body.jpg');
			$templateData['emailFooterImg'] = site_url('assets/email_images/email_footer.png');
			$templateData['emailHeadImg'] = site_url('assets/email_images/email_head.jpg');
			$templateData['emailShadowImg'] = site_url('assets/email_images/email_shadow.png');
			$templateData["name"] = $data['first_name'] . " " . $data['last_name'];
			$templateData["email"] = $this->input->post("email");

			$this->load->model('mail_gen_m');
			$this->_mainData['success'] = $this->mail_gen_m->generateFromView($mailData, $templateData, $htmlTemplate, $textTemplate);
		}
    }
    
    private function processCreditFormBLUFF() {
        if ($this->validateFinanceFormBLUFF() === TRUE) {
          $financeEmail = $this->pages_m->getFinanceEmail();
          
          $this->load->model("account_m");
          $post = $this->input->post();
          $data = array();

          $data['joint'] = $post['joint'];
          $data['initial'] = $post['initial'];          
          $data['first_name'] = $post['fname'];
          $data['last_name'] = $post['lname'];
          $data['driver_licence'] = $post['dl'];
          $data['driver_licence_state'] = $post['driver_licence_state'];
          $data['driver_licence_expiration'] = $post['driver_licence_expiration'];
          $data['type'] = $post['type'];
          $data['condition'] = $post['condition'];
          $data['year'] = $post['year'];
          $data['make'] = $post['make'];
          $data['model'] = $post['model'];
          $data['down_payment'] = $post['down_payment'];
          $data['email'] = $post['email'];
          $data['contact_info'] = json_encode($post['contact_info']);
          $data['housing_info'] = json_encode($post['housing_info']);
          $data['banking_info'] = json_encode($post['banking_info']);
          $data['previous_add'] = json_encode($post['previous_add']);
          $data['employer_info'] = json_encode($post['employer_info']);
          $data['reference'] = json_encode($post['reference']);
          $data['application_date'] = date('Y-m-d H:i:s');

          if ($post["employer_info"]["year"] < 2) {
              $data["prior_employer_info"] = json_encode($post['prior_employer_info']);
          } else {
              $data["prior_employer_info"] = "{}";
          }

          if ($post['joint'] > 0) {
              // we need to include co-applicant information as well
              $data['co_initial'] = $post['co_initial'];
              $data['co_first_name'] = $post['co_fname'];
              $data['co_last_name'] = $post['co_lname'];
              $data['co_driver_licence'] = $post['co_dl'];
              $data['co_driver_licence_state'] = $post['co_driver_licence_state'];
              $data['co_driver_licence_expiration'] = $post['co_driver_licence_expiration'];
              $data['co_email'] = $post['co_email'];
              $data['co_contact_info'] = json_encode($post['co_contact_info']);
              $data['co_housing_info'] = json_encode($post['co_housing_info']);
              $data['co_banking_info'] = json_encode($post['co_banking_info']);
              $data['co_previous_add'] = json_encode($post['co_previous_add']);
              $data['co_employer_info'] = json_encode($post['co_employer_info']);

              if ($post["co_employer_info"]["year"] < 2) {
                  $data["co_prior_employer_info"] = json_encode($post['co_prior_employer_info']);
              } else {
                  $data["co_prior_employer_info"] = "{}";
              }
          }

          $this->account_m->creditApplication($data);

          // Send email
          $this->config->load('sitesettings');
          
          $mailData = array('toEmailAddress' => $financeEmail,
                        'subject' => 'You Have a new Credit Application',
                        'fromEmailAddress' => "noreply@powersporttechnologies.com",
                        'fromName' => "Credit Apps Form",
                        'replyToEmailAddress' => $this->input->post('email'),
                        'replyToName' => $this->config->item('replyToName'));
          $templateData = $post;

          $htmlTemplate = 'email/financerequest_html_v';
          $textTemplate = 'email/financerequest_html_v';

          $templateData['emailBodyImg'] = site_url('assets/email_images/email_body.jpg');
          $templateData['emailFooterImg'] = site_url('assets/email_images/email_footer.png');
          $templateData['emailHeadImg'] = site_url('assets/email_images/email_head.jpg');
          $templateData['emailShadowImg'] = site_url('assets/email_images/email_shadow.png');
          $templateData["name"] = $data['first_name'] . " " . $data['last_name'];
          $templateData["email"] = $this->input->post("email");

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
            $toEmail = $serviceEmail;
			//$serviceEmail = "bdvojcek@yahoo.com";
            //echo $serviceEmail;exit;
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
            
            // create customer and generate event
            if (defined('ENABLE_CRM') && ENABLE_CRM)
            {
                $this->load->model("admin_m");
                $customer = $this->admin_m->createCustomerIfNotExist(array(
                    "email" => $this->input->post('email'),
                    "first_name" => $this->input->post('fname'),
                    "last_name" => $this->input->post('lname'),
                    "phone" => $this->input->post('phone'),
                    "street_address" => $this->input->post('address'),
                    "city" => $this->input->post('city'),
                    "state" => $this->input->post('state'),
                    "zip" => $this->input->post('zipcode')
                ), false, 'service');

                $message = '';
                foreach($templateData as $k => $v) {
                    $message .= $k . ':' . $v . PHP_EOL;
                }

                if ($customer !== FALSE) {

                    // create 'New Lead' event for the customer
                    $now = date('Y-m-d H:i:s');
                    $reminder = array(
                        'notes' => $message,
                        'subject' => 'Service Request',
                        'user_id' => $customer['id'],
                        'start_datetime' => date('Y-m-d H:i:s'),
                        'end_datetime' => date('Y-m-d H:i:s'),
                        'data' => json_encode(array(
                            'recur' => false,
                            'recur_per' => false,
                            'recur_every' => ''
                        )),
                        'created_on' => $now,
                        'created_by' => -1,
                        'modified_on' => $now,
                        'id' => ''
                    );
                    $this->admin_m->saveCustomerReminder($reminder);
                }

                $employees = $this->admin_m->getServiceEmployees();
                foreach($employees as $employee) {
                    $toEmail = $toEmail.','.$employee['lost_password_email'];
                }
            }
            
			$mailData = array('toEmailAddress' => $toEmail,
            'subject' => 'Service Schedule Request',
            'fromEmailAddress' => "noreply@powersporttechnologies.com",
            'fromName' => "Service Request",
            'replyToEmailAddress' => $this->input->post('email'),
            'replyToName' => $this->config->item('replyToName'));

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
	
  	private function loadGoogleMaps()
  	{
  	    return $this->load->view("info/pages/loadGoogleMaps", array(
  	        "store_name" => $this->_mainData['store_name']
        ), true);

  	}
  	
  	private function processContactForm()
  	{
	  	if ($this->validateContactForm() === TRUE)
		{

			// Send email
			$this->config->load('sitesettings');


            $this->load->model("admin_m");
            $store_name = $this->admin_m->getAdminShippingProfile();

            $mailData = array('toEmailAddress' => $store_name["email"],
  	                    'subject' => $this->input->post('subject'),
  	                    'fromEmailAddress' => "noreply@powersporttechnologies.com",
  	                    'fromName' => "Contact Form",
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

  	public function admindownload($pageId) {
        $this->enforceAdmin("pages");
        // get the page info...
        $this->_mainData['pageRec'] = $this->pages_m->getPageRec($pageId);
        // now, shove it down...
        jserve_file(STORE_DIRECTORY . '/attachments/' . $this->_mainData['pageRec']['attachment_filename'], $this->_mainData['pageRec']['original_filename'], $this->_mainData['pageRec']['attachment_mime_type']);
    }


  	public function edit($pageId = NULL)
  	{
        $this->enforceAdmin("pages");


        $this->_mainData['widgets'] = $this->pages_m->getWidgets();
  		if(!empty($_POST))
  		{
	  		$_POST['css'] = htmlentities(@$_POST['css']);
	  		$_POST['javascript'] = htmlentities(@$_POST['javascript']);
  		}
  		if($this->validatePage() === TRUE)
  		{
  			$post = $this->input->post();

  			// we have to filter based on type for a few of these...
            switch($post["type"]) {
                case 'Managed Page':
                    $post["external_url"] = "";
                    $post["external_link"] = 0;
                    $post["original_filename"] = "";
                    $post["attachment_filename"] = "";
                    $post["attachment_mime_type"] = "";
                    break;

                case 'External Link':
                    // this only has some of these...
                    $post["original_filename"] = "";
                    $post["attachment_filename"] = "";
                    $post["attachment_mime_type"] = "";

                    if (!preg_match("/^[a-z0-9]+:/", $post["external_url"])) {
                        // put an http:// on the front of it
                        $post["external_url"] = "http://" . $post["external_url"];
                    }

                    break;

                case 'File Attachment':
                    // You have to put the file somewhere...
                    $upload = $_FILES["upload"];
                    $post['original_filename'] = $upload['name'];
                    // create a new filename
                    $tmp_file = tempnam(STORE_DIRECTORY . "/attachments", "file_attachments");
                    move_uploaded_file($upload['tmp_name'], $tmp_file);
                    $post['attachment_filename'] = basename($tmp_file);
                    $post['attachment_mime_type'] = $upload['type'];

                    $post["external_url"] = "";
                    $post["external_link"] = 0;
                    break;
            }

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

			$page_section_ids = $_REQUEST["page_section_ids"];
			if (array_key_exists("page_section_ids", $post)) {
			    unset($post["page_section_ids"]);
            }

			//$post['widgets'] = array_unique($post['widgets']);
			// echo "<pre>";
			// echo $count;
			// print_r($post);exit;
			// echo "</pre>";
  			$newId = $this->pages_m->editPage($post);

  			// There are three things on the front. They usually throw it all off.

			// update page section ordinals
            $this->pages_m->updatePageSectionOrdinals($newId > 1 ? $newId : $pageId, $page_section_ids);

            // JLB 11-20-18:
            global $PSTAPI;
            initializePSTAPI();

            $page = $PSTAPI->pages()->get($pageId);

            if (!is_null($page)) {
                // We have to do some cleanup for the showroom.
                if ($page->hasShowcaseObject()) {
                    $object = $page->getShowcaseObject();

                    if (!is_null($object)) {
                        if ($page->hasThumbnail()) {
                            $thumbnail = $_FILES["thumbnail"];

                            if ($thumbnail["size"] > 0) {
                                // let's just assume it's an image for now.

                                $image_directory = STORE_DIRECTORY . "/html/media/pagethumbnails";

                                if (!file_exists($image_directory)) {
                                    mkdir($image_directory);
                                }

                                // move the image..
                                $image_filename = $image_directory . "/" . time() . "_" . preg_replace("/[^0-9a-z\.\-\_]+/i", "_", $thumbnail["name"]);

                                move_uploaded_file($thumbnail["tmp_name"], $image_filename);
                                $object->set("thumbnail_photo",  "/media/pagethumbnails/" . basename($image_filename));
                                $object->save();
                            }
                        }

                        // If the title changed, you must change with it.
                        if ($object->get("display_title") != $_REQUEST["label"]) {
                            $object->set("display_title", $_REQUEST["label"]);
                            $object->set("customer_set_title", 1);
                            $object->save();
                        }

                        // If the caption changed, you must change with it.
                        if (array_key_exists("short_title", $_REQUEST) && $object->get("short_title") != $_REQUEST["short_title"]) {
                            $object->set("short_title", $_REQUEST["short_title"]);
                            $object->set("customer_set_short_title", 1);
                            $object->save();
                        }
                    }
                }
            }

  			if(is_numeric($pageId) && ($newId > 1))
  				$pageId = $newId;
  			elseif($newId > 1)
  				redirect('pages/edit/'.$newId);
  		}
  		if(is_numeric($pageId))
  		{
  		    global $PSTAPI;
  		    initializePSTAPI();
            $this->_mainData['bannerlibrary'] = 'bannerlibrary';
            $page = $PSTAPI->pages()->get($pageId);
            $page->inheritHomeMeta();
	  		$this->_mainData['pageRec'] = $page->to_array(); // $this->pages_m->getPageRec($pageId);
	  		$this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
	  		$this->setMasterPageVars('title', $this->_mainData['pageRec']['title']);
	  		$this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
	  		$this->_mainData['pageRec']['location'] = explode(',', $this->_mainData['pageRec']['location']);
	  		$this->_mainData['pageRec']['widgets'] = json_decode($this->_mainData['pageRec']['widgets'], TRUE);
	  		$this->_mainData['page_sections'] = $this->pages_m->getPageSections($pageId);
            foreach ($this->_mainData['page_sections'] as &$section) {
                switch($section["type"]) {
                    case "Textbox":
                        $section["textboxes"] = $this->pages_m->getTextBoxes($pageId, $section["page_section_id"]);
                        break;

                    case "Video":
                        $section["videos"] = $this->pages_m->getTopVideos($pageId, $section["page_section_id"]);
                        break;

                    case "Slider":
                        $section["sliders"] = $this->admin_m->getSliderImages($pageId, $section["page_section_id"]);
                        break;

                    case "Gallery":
                        global $PSTAPI;
                        initializePSTAPI();
                        $section["gallery"] = $PSTAPI->pagevaultimage()->fetch(array("page_section_id" => $section["page_section_id"]), true);
                        usort($section["gallery"], function($a, $b) {
                            if ($a["priority_number"] < $b["priority_number"]) {
                                return -1;
                            } else if ($a["priority_number"] > $b["priority_number"]) {
                                return 1;
                            } else {
                                return 0;
                            }
                        });
                        break;

                    case "Events":
                        global $PSTAPI;
                        initializePSTAPI();
                        $section["events"] = $PSTAPI->pagecalendarevent()->fetch(array("page_section_id" => $section["page_section_id"]), true);
                        break;
                }
            }
  		}
  		if(is_array(@$_SESSION['errors']))
  		{
	  		$this->_mainData['errors'] = $_SESSION['errors'];
	  		unset($_SESSION['errors']);
  		}
  			
  		$this->_mainData['location'] = array('footer' => 'Footer', 'comp_info' => 'Company Info');
  		$this->_mainData['widgets'] = $this->pages_m->getWidgets();
  		$js = '<script type="text/javascript" src="' . $this->_mainData['assets'] . '/ckeditor4/ckeditor.js"></script>';
  		$this->loadJS($js);
  		$this->_mainData['edit_config'] = $this->_mainData['assets'] . '/js/page_htmleditor.js';

  		// We have to compute the flags based on the page information
        global $PSTAPI;
        initializePSTAPI();

        $page = $PSTAPI->pages()->get($pageId);

        $this->_mainData["hidden_managed_page"] = false;
        $this->_mainData["upload_thumbnail"] = false;
        $this->_mainData["custom_link"] = false;
        $this->_mainData["current_thumbnail"] = false;

        if (!is_null($page)) {
            // First, what page types do not permit being files or downloads?
            $this->_mainData["hidden_managed_page"] = in_array($page->get("page_class"), array("System Page", "Part Section", "Showroom Model", "Showroom Trim", "Showroom Make", "Showroom Machine Type", "Showroom Landing Page"));
            $this->_mainData["upload_thumbnail"] = $page->hasThumbnail();
            $this->_mainData["has_showcase_segment"] = $page->hasShowcaseSegment();

            switch ($page->get("page_class")) {
                case "Showroom Model":
                case "Showroom Trim":
                case "Showroom Make":
                case "Showroom Machine Type":
                    $model = $page->getShowcaseObject();
                    if ($model) {
                        $this->_mainData["custom_link"] = "Factory_Showroom/" . $model->get("full_url");
                        $this->_mainData["current_thumbnail"] = $model->get("thumbnail_photo");
                        $this->_mainData["short_title"] = $model->get("short_title");

                    }
                    $this->_doPageFlagsShowroom("showcasemodel", $pageId);
                    break;

                case "Showroom Landing Page":
                    $this->_mainData["custom_link"] = "Factory_Showroom";
                    break;
            }
        }


  		$this->setNav('admin/nav_v', 1);
	  	$this->renderMasterPage('admin/master_v', 'admin/pages/edit_v', $this->_mainData);
  	}

  	protected function _doPageFlagsShowroom($factory, $pageId) {
	    global $PSTAPI;
	    initializePSTAPI();

        $models = $PSTAPI->$factory()->fetch(array(
            "page_id" => $pageId
        ));

        if (count($models) > 0) {
            $model = $models[0];

        }
    }

  	public function delete($pageId = NULL)
  	{
        $this->enforceAdmin("pages");

        if(is_numeric($pageId))
	 	{
		 	$this->pages_m->deletePage($pageId);
	 	}
	 	redirect('admin_content/pages');
  	}
  	
  	public function make_inactive($pageId = NULL)
  	{
        $this->enforceAdmin("pages");

        if(is_numeric($pageId))
	 	{
            global $PSTAPI;
            initializePSTAPI();
            $page = $PSTAPI->pages()->get($pageId);
            if (!is_null($page)) {
                $page->set("active", 0);
                $page->save();
            }
	 	}
	 	redirect('admin_content/pages');
  	}

  	public function make_active($pageId = NULL)
  	{
        $this->enforceAdmin("pages");

        if(is_numeric($pageId))
	 	{
	 	    global $PSTAPI;
	 	    initializePSTAPI();
	 	    $page = $PSTAPI->pages()->get($pageId);
	 	    if (!is_null($page)) {
	 	        $page->set("active", 1);
	 	        $page->save();
            }
	 	}
	 	redirect('admin_content/pages');
  	}

  	public function addTextBox()
  	{
        $this->enforceAdmin("pages");

        if($this->validateTextBox() === TRUE)
	    {
	        $post = $this->input->post();
	        $post["text"] = $_REQUEST["text"];
	      $this->pages_m->updateTextBox($post);
	    }
        redirect('pages/edit/'.$this->input->post('pageId'));
  	}

  	protected function fixSliderOrder($id, $page_id, $page_section_id) {
        $query = $this->db->query("select max(`order`) as max_order from slider where pageId = ? and page_section_id = ? and id < ?", array($page_id, $page_section_id, $id));
        $ordinal = 0;
        foreach ($query->result_array() as $row) {
            $ordinal = $row["max_order"];
        }
        $ordinal++;
        // now, update it.
        $this->db->query("update slider set `order` = ? where id = ? limit 1", array($ordinal, $id));
    }
  	
  	public function addImages()
  	{
        $this->enforceAdmin("pages");

        if (array_key_exists("submit", $_POST) && $_POST["submit"] == "updateSliderTime") {
            $value = floatVal($_POST["slider_seconds"]);

            if ($value > 0) {
                $this->db->query("Update page_section set slider_seconds = ? where page_id = ? and page_section_id = ?", array($value, $this->input->post('page'), $this->input->post("page_section_id")));
            } else {
                $_SESSION['errors'][] = "Sorry, the time must be greater than zero.";
            }
        } else if($this->validateSliderImageSettingsForm() === TRUE)
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
					$uploadData['page_section_id'] = $this->input->post("page_section_id");
					$slider_id = $this->admin_m->updateSlider($uploadData);
                    // fix the slider ordinal...
                    $this->fixSliderOrder($slider_id, $uploadData['pageId'], $uploadData['page_section_id']);
					redirect('pages/edit/'.$this->input->post('page'));
				}	
	  		}
            if(@$_POST['banner_link'] && $_POST['submit'] == 'saveLink') {
                foreach( $_POST['banner_link'] as $key => $link ) {
                    $this->admin_m->updateSliderLink($key, $link);
                }
                $arr = explode(",", $this->input->post('ordering'));
                foreach ($arr as $k => $v) {
                    $rr[] = explode("=", $v);
                }
                foreach ($rr as $k => $v) {
                    $img = $v[0];
                    $ord = $v[1];
                    $this->admin_m->updateSliderOrder($img, $ord, $this->input->post("page_section_id"));
                }
                redirect('pages/edit/' . $this->input->post('page'));
            }
            /*
             * JLB 07-07-17
             * I think that this is completely backwards. This was coded and it appears to copy banners INTO the banner library.
             * That's the wrong direction completely. I think that's why the banners didn't show up correctly when you selected them.
             *
             * I am puzzled beyond measure about the order as well. Why don't we just shove the banner at the end of the list?
             * How is it getting an ordinal?
             *
             */
            if(array_key_exists("banner", $_POST) && $_POST['banner'] && $_POST['submit'] == 'addBanner') {
                foreach( $_POST['banner'] as $banner ) {
                    // Pardy's Original Code:
                    //$bnrExt = explode('.', $banner);
                    //$bannerName = time().'.'.end($bnrExt);
                    //copy(STORE_DIRECTORY . '/html/media/'.$banner, STORE_DIRECTORY . '/html/bannerlibrary/'.$bannerName);

                    // Let's just link them
                    $full_filename = STORE_DIRECTORY . '/html/bannerlibrary/' . $banner;
                    if (file_exists($full_filename)) {
                        $bannerName = "bannerlibrary_" . $banner; // just use this name, okay?
                        symlink($full_filename, STORE_DIRECTORY . "/html/media/" . $bannerName);

                        $uploadData = array();
                        $uploadData['image'] = $bannerName;
                        $uploadData['pageId'] = $this->input->post('page');
                        $uploadData['order'] = $this->input->post('order');
                        $uploadData['page_section_id'] = $this->input->post("page_section_id");
                        $slider_id = $this->admin_m->updateSlider($uploadData);
                        // fix the slider ordinal...
                        $this->fixSliderOrder($slider_id, $uploadData['pageId'], $uploadData['page_section_id']);
                    }
                }
                redirect('pages/edit/' . $this->input->post('page'));
            }
        }
        redirect('pages/edit/' . $this->input->post('page'));
    }
  	
  	public function remove_image($id, $pageId)
	{
        $this->enforceAdmin("pages");

        if(is_numeric($id))
		{
			$this->admin_m->removeImage($id, $this->config->item('upload_path'));  
		}
        redirect('pages/edit/'.$pageId);
    }
    
    public function ajax_edit_image($id) {
        $data = array();
        if (isset($_POST['start_date'])) {
            $data['start_date'] = $_POST['start_date'];
        } else if (isset($_POST['end_date'])){
            $data['end_date'] = $_POST['end_date'];
        } else {
            print json_encode(array('success' => FALSE));
            return;
        }

        $this->admin_m->updateSliderData($id, $data);  
        print json_encode(array('success' => TRUE));
    }

	protected function cleanYouTubeURL($url) {
        $piece = "https://www.youtube.com/watch?v=";
        if (FALSE !== ($pos = strrpos($url, $piece))) {
            // well, we need the end of it..
            $url = substr($url, $pos + strlen($piece));

            // If the segment containts an &, that means they've given us something more. You should kill that.
            if (FALSE !== ($pos = strrpos($url, "&"))){
                $url = substr($url, 0, $pos);
            }
        }

        return $url;
    }

    public function addTopVideos() {
	    $this->enforceAdmin("pages");

        $video_url = $_REQUEST["video_url"];
        $title = $_REQUEST["title"];
        $ordering = $_REQUEST["ordering"];
        $page_section_id = $_REQUEST["page_section_id"];

        $arr = array();

        for ($i = 0; $i < min(count($video_url), count($title), count($ordering)); $i++) {
            $url = $this->cleanYouTubeURL($video_url[$i]);
            if (trim($url) != "") {
                $arr[] = array(
                    "video_url" => $url,
                    "ordering" => $ordering[$i],
                    "title" => $title[$i],
                    "page_id" => $_REQUEST["pageId"],
                    "page_section_id" => $page_section_id
                );
            }
        }


        /*
         * JLB 07-07-17
         * I have never seen the need to do anything like this. WTF?
         *
        foreach ($this->input->post('video_url') as $k => $v) {
            if ($v != '') {
                $url = $v;
                parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
                //$my_array_of_vars['v'];
                $arr[] = array('video_url' => $my_array_of_vars['v'], 'ordering' => $this->input->post('ordering')[$k], 'page_id' => $this->input->post('pageId'), 'title' => $this->input->post('title')[$k]);
            }
        }
        */
        $this->pages_m->updateTopVideos($this->input->post('pageId'), $page_section_id, $arr);
        redirect('pages/edit/' . $this->input->post('pageId'));
    }

    /*
     * JLB 05-20-18
     * Gallery moved here from the vault admin
     */

    public function vault_deleteImage($page_id, $page_section_id, $page_vault_image_id) {
        $this->enforceAdmin("pages");

        global $PSTAPI;
        initializePSTAPI();

        $pvi = $PSTAPI->pagevaultimage()->get($page_vault_image_id);

        if (!is_null($pvi)) {
            $img = $pvi->get("image_name");
            $dir = STORE_DIRECTORY.'/html/media/'.$img;
            if (file_exists($dir) && is_file($dir)) {
                unlink($dir);
            }
            $pvi->remove();
        }

        header("Location: /pages/edit/${page_id}");
    }

    public function vault_updateImage($page_id, $page_section_id, $page_vault_image_id) {
        $this->enforceAdmin("pages");
        global $PSTAPI;
        initializePSTAPI();

        $description = array_key_exists("description", $_REQUEST) ? $_REQUEST["description"] : "";
        $PSTAPI->pagevaultimage()->update($page_vault_image_id, array(
            "description" => $description
        ));

        header("Location: /pages/edit/${page_id}");
    }

    public function vault_reorderImages($page_id, $page_section_id) {
        $this->enforceAdmin("pages");
        global $PSTAPI;
        initializePSTAPI();

        $order = array_key_exists("order", $_REQUEST) ? $_REQUEST["order"] : "";
        $order = explode(",", $order);

        foreach ($order as $part) {
            list($label, $ordinal) = explode("=", $part);
            $number = intVal(substr($label, strlen("pageVaultGallery")));
            $PSTAPI->pagevaultimage()->update($number, array(
               "priority_number" => $ordinal
            ));
        }

        header("Location: /pages/edit/${page_id}");
    }

    public function vault_addImage($page_id, $page_section_id) {
        $this->enforceAdmin("pages");
        global $PSTAPI;
        initializePSTAPI();
        $next_ordinal = $PSTAPI->pagevaultimage()->getNextOrdinal($page_section_id);

        foreach ($_FILES['file']['name'] as $key => $val) {
            $arr = array();
            $img = time() . '_' . $next_ordinal . '_' . str_replace(' ','_',$val);
            $dir = STORE_DIRECTORY.'/html/media/'.$img;
            move_uploaded_file($_FILES["file"]["tmp_name"][$key], $dir);

            $arr['description'] = $_POST['description'];
            $arr['image_name'] = $img;
            $arr['priority_number'] = $next_ordinal;
            $arr['page_section_id'] = $page_section_id;

            $PSTAPI->pagevaultimage()->add($arr);

            $next_ordinal++;
        }

        header("Location: /pages/edit/${page_id}");
    }



    /*
     * These are for the calendar...
     */
    protected function _calendarCleanDateTime($input) {
        // Tue May 29 2018 18:25:00 GMT-0400 (EDT)
        if (FALSE !== ($p = strpos($input, "GMT"))) {
            $input =substr($input, 0, $p - 1);
        }
        return $input;
    }

    public function calendar_addEvent($page_id, $page_section_id) {
        $this->enforceAdmin("pages");
        global $PSTAPI;
        initializePSTAPI();

        $PSTAPI->pagecalendarevent()->add(array(
            "title" => array_key_exists("title", $_REQUEST) ? $_REQUEST["title"] : "",
            "description" => array_key_exists("description", $_REQUEST) ? $_REQUEST["description"] : "",
            "start" => array_key_exists("start", $_REQUEST) && $_REQUEST["start"] != "" ? date("Y-m-d H:i:s", strtotime($this->_calendarCleanDateTime($_REQUEST["start"]))) : null,
            "end" => array_key_exists("end", $_REQUEST) && $_REQUEST["end"] != "" ? date("Y-m-d H:i:s", strtotime($this->_calendarCleanDateTime($_REQUEST["end"]))) : null,
            "url" => array_key_exists("url", $_REQUEST) ? $_REQUEST["url"] : "",
            "address1" => array_key_exists("address1", $_REQUEST) ? $_REQUEST["address1"] : "",
            "address2" => array_key_exists("address2", $_REQUEST) ? $_REQUEST["address2"] : "",
            "state" => array_key_exists("state", $_REQUEST) ? $_REQUEST["state"] : "",
            "zip" => array_key_exists("zip", $_REQUEST) ? $_REQUEST["zip"] : "",
            "city" => array_key_exists("city", $_REQUEST) ? $_REQUEST["city"] : "",
            "page_section_id" => $page_section_id
        ));


        header("Location: /pages/edit/${page_id}");

    }

    public function calendar_removeEvent($page_id, $page_calendar_event_id) {
        $this->enforceAdmin("pages");
        global $PSTAPI;
        initializePSTAPI();
        $PSTAPI->pagecalendarevent()->remove($page_calendar_event_id);
        header("Location: /pages/edit/${page_id}");
    }


    public function calendar_editEvent($pageId, $page_calendar_event_id) {
        $this->enforceAdmin("pages");

        // OK, great. The idea here is to just slam it into a form.
        // I don't know how much of this is really needed...I am going based on function edit.

        $this->_mainData['widgets'] = $this->pages_m->getWidgets();
        $this->_mainData['pageId'] = $pageId;
        $this->_mainData['page_calendar_event_id'] = $page_calendar_event_id;

        $this->_mainData['pageRec'] = $this->pages_m->getPageRec($pageId);
        $this->setMasterPageVars('descr', $this->_mainData['pageRec']['metatags']);
        $this->setMasterPageVars('title', $this->_mainData['pageRec']['title']);
        $this->setMasterPageVars('keywords', $this->_mainData['pageRec']['keywords']);
        $this->_mainData['pageRec']['location'] = explode(',', $this->_mainData['pageRec']['location']);
        $this->_mainData['pageRec']['widgets'] = json_decode($this->_mainData['pageRec']['widgets'], TRUE);

        $this->_mainData['location'] = array('footer' => 'Footer', 'comp_info' => 'Company Info');
        $this->_mainData['widgets'] = $this->pages_m->getWidgets();
        $js = '<script type="text/javascript" src="' . $this->_mainData['assets'] . '/ckeditor4/ckeditor.js"></script>';
        $this->loadJS($js);
        $this->_mainData['edit_config'] = $this->_mainData['assets'] . '/js/htmleditor.js';

        global $PSTAPI;
        initializePSTAPI();
        $this->_mainData['page_calendar_event'] = $PSTAPI->pagecalendarevent()->get($page_calendar_event_id);
        $this->_mainData['page_calendar_event'] = $this->_mainData['page_calendar_event']->to_array();
        $this->setNav('admin/nav_v', 1);
        $this->renderMasterPage('admin/master_v', 'admin/pages/calendar_editEvent_v', $this->_mainData);
    }

    public function calendarSaveEvent($pageId, $page_calendar_event_id) {
        $this->enforceAdmin("pages");
        global $PSTAPI;
        initializePSTAPI();

        // So you think JavaScript is going to do it?
        // I'm not entirely proud of this; it just needs to be done.

        $PSTAPI->pagecalendarevent()->update($page_calendar_event_id, array(
            "title" => array_key_exists("title", $_REQUEST) ? $_REQUEST["title"] : "",
            "description" => array_key_exists("description", $_REQUEST) ? $_REQUEST["description"] : "",
            "start" => array_key_exists("start", $_REQUEST) && $_REQUEST["start"] != "" ? date("Y-m-d H:i:s", strtotime($this->_calendarCleanDateTime($_REQUEST["start"]))) : null,
            "end" => array_key_exists("end", $_REQUEST) && $_REQUEST["end"] != "" ? date("Y-m-d H:i:s", strtotime($this->_calendarCleanDateTime($_REQUEST["end"]))) : null,
            "url" => array_key_exists("url", $_REQUEST) ? $_REQUEST["url"] : "",
            "address1" => array_key_exists("address1", $_REQUEST) ? $_REQUEST["address1"] : "",
            "address2" => array_key_exists("address2", $_REQUEST) ? $_REQUEST["address2"] : "",
            "state" => array_key_exists("state", $_REQUEST) ? $_REQUEST["state"] : "",
            "zip" => array_key_exists("zip", $_REQUEST) ? $_REQUEST["zip"] : "",
            "city" => array_key_exists("city", $_REQUEST) ? $_REQUEST["city"] : ""
        ));

        header("Location: /pages/edit/${pageId}");
    }

}
