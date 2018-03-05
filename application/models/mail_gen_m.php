<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * mail_gen_model.php
 *
 * DEPENDENCIES
 *
 * - Database Tables: queued_email, queued_email_attach
 *
 * NOTES:
 * 
 * When queueing an email, one of the optional fields is "userRefId".  This
 * field should be populated with a unique id representing the user or NULL.
 *
 * Using this model, there are essentially 3 basic methods for generating and queueing
 * an email for delivery:
 * generate() - basic email generation, no templates, email body is passed as a paremeter
 * generateFromView() - generated using a view file as a "template", values are populated
 *                      from an array of data values passed in as a parameter
 * generateFromTemplate() - generated using a database record containing a "template", values
 *                          are populated from an array of data values passed in as a parameter
 *
 * Using either method, you are required to pass in a $mailData array.  The $mailData array
 * may contain the following values:
 *
 *				toEmailAddress 			: comma-delimited string
 *				ccEmailAddress 			: comma-delimited string
 *				bccEmailAddress 		: comma-delimited string
 *				replyToEmailAddress : string
 *				replyToName 				: string
 *        fromEmailAddress 		: string
 *				fromName 						: string
 *				subject 						: string
 *				userRefId						: (integer) an optional user reference id
 *
 *
 */

final class Mail_gen_m extends Master_M
{

	const ERR_DATA_VALIDATION = -100;							// A data validation error
	const ERR_INVALID_ATTACHMENT = -101;					// Invalid attachment file

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct()
  {
    parent::__construct();
		$validationRules = array(
        array('field' => 'toEmailAddress', 'rules' => 'required|xss_clean|strtolower|valid_email_formats'),
        array('field' => 'ccEmailAddress', 'rules' => 'xss_clean|strtolower|valid_email_formats'),
        array('field' => 'bccEmailAddress', 'rules' => 'xss_clean|strtolower|valid_email_formats'),
        array('field' => 'replyToEmailAddress', 'rules' => 'xss_clean|strtolower|valid_email_format'),
        array('field' => 'replyToName', 'rules' => 'xss_clean'),
        array('field' => 'fromEmailAddress', 'rules' => 'xss_clean|strtolower|valid_email_format'),
        array('field' => 'fromName', 'rules' => 'xss_clean'),
        array('field' => 'subject', 'rules' => 'xss_clean'),
        array('field' => 'userRefId', 'rules' => 'xss_clean'),
        array('field' => 'message', 'rules' => ''),
        array('field' => 'alt_message', 'rules' => '')
        );
		// This "Set" of validation rules will be used within the
		// queueEmail() function to validate the email data before
		// storing in the database.
		$this->setValidationRules('EMAIL', $validationRules);
  }

	/**
	 * generate
	 * 
	 * Generic method for queueing an email.  No templates or views are used
	 * in this method.  
   *
	 * @param array $mailData - parameters used for sending email
	 * @param string $htmlBody - the body of the email in HTML
	 * @param string $textBody - the body of the email in plain text 
	 * @param array $attachments optional - array of attachments (full paths)
	 * @param date $sendDateTime - for scheduling email, NULL will send immediately
	 * @return boolean
	 */
	public function generate($mailData=NULL, $htmlBody=NULL, $textBody=NULL, $attachments=NULL, $sendDateTime=NULL)
	{
		$retVal = FALSE;
		if (is_null($mailData) || !is_array($mailData))
			return $retVal;
		if (is_null($htmlBody) && is_null($textBody))
			return $retVal;
		// Add some of the parameters to the mail data array
		$mailData['message'] = $htmlBody;
		$mailData['alt_message'] = $textBody;
		// The following method is on the "parent" class
		$retVal = $this->queueEmail($mailData, $attachments, $sendDateTime);
		return $retVal;
	}

	/**
	 * generateFromView
	 * 
	 * Method to queue an email from a view-based template.  This method creates 
	 * an email message using a pre-defined template (a view file) for formatting.  
	 * After the message body (and alternate message body)
	 * is generated, all of the data needed to send the email is stored in
	 * the fw_queued_email table for later delivery.   
	 *
	 * @param array $mailData - parameters used for sending email
	 * @param array $templateData - data used for filling in templates (values are view-dependent)
	 * @param string $htmlTemplate - name of the HTML view file
	 * @param string $textTemplate - name of the TEXT view file 
	 * @param array $attachments optional - array of attachments (full paths)
	 * @param date $sendDateTime - for scheduling email, NULL will send immediately
	 * @return boolean
	 */
	public function generateFromView($mailData=NULL, $templateData=NULL, $htmlTemplate=NULL, $textTemplate=NULL, $attachments=NULL, $sendDateTime=NULL)
	{
		$retVal = FALSE;
		// Require $mailData
		if (is_null($mailData))
			return $retVal;
		if (!is_null($htmlTemplate) && !is_null($templateData))
			$mailData['message'] = $this->load->view($htmlTemplate, $templateData, TRUE);
		if (!is_null($textTemplate) && !is_null($templateData))
			$mailData['alt_message'] = $this->load->view($textTemplate, $templateData, TRUE);
		// The following method is on the "parent" class
		$retVal = $this->queueEmail($mailData, $attachments, $sendDateTime);
		return $retVal;
	}

	/**
	 * generateFromTemplate function.
	 * 
	 * @access public
	 * @param array $mailData - parameters used for sending email
	 * @param mixed $templateId (default: NULL)
	 * @param mixed $templateData (default: NULL) - the values to populate the template
	 * @param mixed $attachments (default: NULL) - array of attachment files
	 * @param date $sendDateTime - for scheduling email, NULL will send immediately
	 * @return boolean
	 */
	public function generateFromTemplate($mailData=NULL, $templateId=NULL, $templateData=NULL, $attachments=NULL, $sendDateTime=NULL)
	{
		$retVal = FALSE;
		if (is_null($templateId) || is_null($mailData))
			return $retVal;
		// Lookup mail template by name or id
		if (is_int($templateId))
			$where = array('mailTemplateId' => $templateId);
		else
			$where = array('mailTemplateName' => $templateId);
		// Load the mail template row from the database
		$templateRow = NULL;
		$query = $this->db->get_where('mail_template', $where);
		if ($query->num_rows() > 0)
			$templateRow = $query->row_array();
		$query->free_result();
		if (is_null($templateRow))
			return $retVal;
		// Declare and initialize some local variables
		$subject = $message = $altMessage = NULL; 
		// Merge the data with the template to create email bodies and subject
    if (!is_null($templateRow['subject']))
			$subject = $this->do_replacements($templateRow['subject'], $templateData);
    if (!is_null($templateRow['message']))
    	$message = $this->do_replacements($templateRow['message'], $templateData);
    if (!is_null($templateRow['alt_message']))
    	$altMessage = $this->do_replacements($templateRow['alt_message'], $templateData);
    // Are there any "templated" attachments?
    $attachRows = NULL;
		$where = array('mailTemplateId' => $templateRow['mailTemplateId']);
		$this->db->select('mailTemplateAttachFile');
		$query = $this->db->get_where('mail_template_attach', $where);
		if ($query->num_rows() > 0)
		{
			if (is_null($attachments) || !is_array($attachments))
				$attachments = array();
			$attachRows = $query->result_array();
			foreach($attachRows as $attachRow)
			{
				$attachments[] = $attachRow['mailTemplateAttachFile'];
			}
		}
		$query->free_result();
    // Append to  mailData array
    $mailData['replyToEmailAddress']  = (isset($mailData['replyToEmailAddress']) ? $mailData['replyToEmailAddress'] : $templateRow['replyToEmailAddress']);
    $mailData['replyToName'] 					= (isset($mailData['replyToName']) ? $mailData['replyToName'] : $templateRow['replyToName']);
    $mailData['fromEmailAddress'] 		= (isset($mailData['fromEmailAddress']) ? $mailData['fromEmailAddress'] : $templateRow['fromEmailAddress']);
    $mailData['fromName'] 						= (isset($mailData['fromName']) ? $mailData['fromName'] : $templateRow['fromName']);
    $mailData['subject'] 							= $subject;
    $mailData['message'] 							= $message;
    $mailData['alt_message'] 					= $altMessage;
		// Queue the email - this method is on the parent class
		$retVal = $this->queueEmail($mailData, $attachments, $sendDateTime);
		return $retVal;
	}

	/**
	 * do_replacements function.
	 * 
	 * @access private
	 * @param mixed $templateText
	 * @param mixed $values (default: NULL)
	 * @return string
	 */
	private function do_replacements($templateText, $values=NULL)
	{
		$str = FALSE;
		if (is_null($values))
			$str = $templateText;
		else
		{
			$this->load->library('parser');
			$str = $this->parser->parse_string($templateText, $values, TRUE);		
		}
		return $str;
	}

	/**
	 * queueEmail
	 * 
	 * This method creates an email message and stores the data in
	 * the fw_queued_email table for later delivery.   
	 *
	 * NOTE: A cron job is usually configured to process the fw_queued_email table.
	 *	 
	 * NOTE: See the $mailData array notes at the top of this file.
	 *
	 * @access private
	 * @param array $mailData
	 * @param array $attachments optional
	 * @param datetime $sendDateTime optional - when to send the email (scheduled)
	 * @return boolean - SUCCESS or FAILURE
	 */
	public function queueEmail($mailData=NULL, $attachments=NULL, $sendDateTime=NULL)
	{
		// Validate & prep email data
	  if (!$this->validate($mailData, 'EMAIL'))
	  {
			$this->setErrorCode(self::ERR_DATA_VALIDATION);
			return FALSE;
	  }
	  // Validate attachments
	  if (!is_null($attachments) && is_array($attachments))
	  {
		  foreach($attachments as $file)
		  {
			  if (!file_exists($file))
			  {
					$this->setErrorCode(self::ERR_INVALID_ATTACHMENT);
					return FALSE;
			  }
		  }
	  }
		// Use sendDateTime
		if (!is_null($sendDateTime))
			$mailData['sendDateTime'] = $sendDateTime;
		// Temporarily set readyForProcess to FALSE
		$mailData['readyForProcess'] = FALSE;
		// Create the record in the fw_queued_email table
		$newId = $this->createRecord('queued_email', $mailData, FALSE);
		if ($newId === FALSE)
			return FALSE;
		else
		{
			// Create attachment records when specified
			if (!is_null($attachments) && is_array($attachments))
			{
				$attachArr = array();
				foreach ($attachments as $attach)
				{
					$attachArr['queuedEmailId'] = $newId;
					$attachArr['attachment'] = $attach;
					$this->createRecord('queued_email_attach', $attachArr, FALSE);
				}
			}
			if (!is_null($attachments) && !is_array($attachments)) // Single Attachment File
			{
			  $attachArr = array();
			  $attachArr['queuedEmailId'] = $newId;
				$attachArr['attachment'] = $attachments;
				$this->createRecord('queued_email_attach', $attachArr, FALSE);
			}
			// Make readyForProcess TRUE ONLY IF $sendDateTime IS NULL
			if (is_null($sendDateTime))
				$this->updateRecords('queued_email', array('readyForProcess'=>TRUE), array('queuedEmailId' => $newId));
			return TRUE;
		}
	}

}

/* End of file mail_gen_model.php */
