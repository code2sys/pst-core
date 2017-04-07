<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * mail_queue_model.php
 *
 * DEPENDENCIES
 *
 * - Database Tables: queued_email, queued_email_attach
 *
 * NOTES:
 *
 * This model does NOT queue an email for sending.  To programmatically
 * send an email using queued operations, use the fw_genmail model.
 *
 * This model is used mostly by a CRON job that is setup to periodically
 * monitor the email queue, send email, and do general housekeeping.  In
 * addition, this model can be used by a controllers to provide a 
 * "management" interface for the mail queue.
 * 
 *
 */

final class Mail_queue_m extends Master_M {

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	function __construct()
  {
    parent::__construct();
  }

	/**
	 * updateEmail function.
	 * 
	 * @access public
	 * @param mixed $queuedEmailId
	 * @param mixed $data
	 * @return integer (number of affected rows or ERR_DB)
	 */
	public function updateEmail($queuedEmailId, $data)
	{
		return $this->updateRecords('queued_email', $data, array('queuedEmailId' => $queuedEmailId));
	}	

	/**
	 * deleteEmail function.
	 * 
	 * @access public
	 * @param mixed $queuedEmailId
	 * @return void
	 */
	public function deleteEmail($queuedEmailId)
	{
		$where = array('queuedEmailId' => $queuedEmailId);
		$this->deleteRecords('queued_email_attach', $where);
		$this->deleteRecords('queued_email', $where);
	}	

	/**
	 * markEmailForDelete function.
	 * 
	 * @access public
	 * @param mixed $queuedEmailId
	 * @return void
	 */
	public function markEmailForDelete($queuedEmailId)
	{
		$where = array('queuedEmailId' => $queuedEmailId);
		$this->markRecordsForDelete('queued_email', $where);
		$this->markRecordsForDelete('queued_email_attach', $where);
	}	

	/**
	 * setProcessedStatus function.
	 * 
	 * @access public
	 * @param mixed $queuedEmailId
	 * @param mixed $readyForProcess
	 * @param mixed $processSuccess
	 * @param string $debugString (default: '')
	 * @return integer - number of rows affected or ERR_DB
	 */
	public function setProcessedStatus($queuedEmailId, $readyForProcess, $processSuccess, $debugString='')
	{
		$data = array();
		$data['readyForProcess'] = $readyForProcess;
		$data['processedTime'] = date( "Y-m-d G:i:s", time());
		$data['processSuccess'] = $processSuccess;
		$data['debugString'] = $debugString;
		if ($processSuccess)
			$data['recDeleted'] = date( "Y-m-d G:i:s", time());
		return $this->updateEmail($queuedEmailId, $data);
	}

	/**
	 * setProcessFailed function.
	 * 
	 * @access public
	 * @param mixed $queuedEmailId
	 * @param string $debugString (default: '')
	 * @return integer - number of rows affected or ERR_DB
	 */
	public function setProcessFailed($queuedEmailId, $debugString='')
	{
		$data = array();
		$data['readyForProcess'] = FALSE;
		$data['processedTime'] = date( "Y-m-d G:i:s", time());
		$data['processSuccess'] = FALSE;
		$data['debugString'] = $debugString;
		return $this->updateEmail($queuedEmailId, $data);
	}

	/**
	 * setProcessSucceeded function.
	 * 
	 * @access public
	 * @param mixed $queuedEmailId
	 * @return integer - number of rows affected or ERR_DB
	 */
	public function setProcessSucceeded($queuedEmailId, $debugString='')
	{
		$data = array();
		$data['readyForProcess'] = FALSE;
		$data['processedTime'] = date( "Y-m-d G:i:s", time());
		$data['processSuccess'] = TRUE;
		$data['recMarkedForDelete'] = date( "Y-m-d G:i:s", time());
		$data['debugString'] = $debugString;
		return $this->updateEmail($queuedEmailId, $data);
	}

	/**
	 * getReadyEmails function.
	 * 
	 * @access public
	 * @param mixed $orderBy (default: NULL)
	 * @param mixed $limit (default: NULL)
	 * @param mixed $offset (default: NULL)
	 * @return array (result_array) or NULL
	 */
	public function getReadyEmails($orderBy=NULL, $limit=NULL, $offset=NULL)
	{
		$rowsArray = NULL;
		$this->db->from('queued_email');
		$this->db->where(array('readyForProcess' => TRUE));
		if (!is_null($orderBy))
			$this->db->order_by($orderBy);
		if (!is_null($limit))
			$this->db->limit($limit, $offset);
		$query = $this->db->get();
		if ($query->num_rows() > 0)
			$rowsArray = $query->result_array();
		$query->free_result();	
		return $rowsArray;
	}

	/**
	 * groomScheduledEmails function.
	 * 
	 * This function sets the readyForProcess field of each email record to TRUE if
	 * the email is "due" to be sent.  
	 * 
	 * @access private
	 * @return integer - number of rows affected or ERR_DB
	 */
	private function groomScheduledEmails()
	{
		$where = array('readyForProcess' => FALSE,
									 'sendDateTime IS NOT NULL' => NULL,
		               'sendDateTime <' => date( "Y-m-d G:i:s", time()),
		               'processedTime IS NULL' => NULL);
		$data = array('readyForProcess' => TRUE);

		return $this->updateRecords('queued_email', $data, $where);

	}

	/**
	 * getFailedEmails function.
	 * 
	 * @access public
	 * @param mixed $orderBy (default: NULL)
	 * @param mixed $limit (default: NULL)
	 * @param mixed $offset (default: NULL)
	 * @return array (result_array) or NULL
	 */
	public function getFailedEmails($orderBy=NULL, $limit=NULL, $offset=NULL)
	{
		$rowsArray = NULL;
		$where = array('readyForProcess' => FALSE,
		               'processSuccess' => FALSE);
		$this->db->from('queued_email');
		$this->db->where($where);
		if (!is_null($orderBy))
			$this->db->order_by($orderBy);
		if (!is_null($limit))
			$this->db->limit($limit, $offset);
		$query = $this->db->get();
		if ($query->num_rows() > 0)
			$rowsArray = $query->result_array();
		$query->free_result();	
		return $rowsArray;
	}

	/**
	 * getEmailById function.
	 * 
	 * @access public
	 * @param mixed $queuedEmailId
	 * @return array (row_array) or NULL
	 */
	public function getEmailById($queuedEmailId)
	{
	  $result = NULL;
		$this->db->from('queued_email');
		$this->db->where(array('queuedEmailId' => $queuedEmailId));
		$query = $this->db->get();
		if ($query->num_rows() > 0)
			$result = $query->row_array();
		$query->free_result();	
		return $result;
	}	

	/**
	 * getEmailAttachmentsById function.
	 * 
	 * @access public
	 * @param mixed $queuedEmailId
	 * @return array (result_array) or NULL
	 */
	public function getEmailAttachmentsById($queuedEmailId)
	{
		$rowsArray = NULL;
		$this->db->from('queued_email_attach');
		$this->db->where(array('queuedEmailId' => $queuedEmailId));
		$query = $this->db->get();
		if ($query->num_rows() > 0)
			$rowsArray = $query->result_array();
		$query->free_result();	
		return $rowsArray;
	}

	/**
	 * processMailQueue function.
	 * 
	 * USED BY CRON
	 *
	 * @access public
	 * @param mixed $limit
	 * @return void
	 */
	public function processMailQueue($limit=NULL)
	{
		// First, groom scheduled emails
		// this will set readyForProcess to true for every scheduled email
		// record that is "due"
		$this->groomScheduledEmails();
		
		// On with the processing of email records…
		$debugString = '';
		$orderBy = 'queuedTime asc';
		$mailRecords = $this->getReadyEmails($orderBy, $limit);
		if (is_null($mailRecords))
			return;
		$this->load->library('email');
		$this->load->helper('file');
		foreach($mailRecords as $row)
		{
			$this->email->clear(TRUE);					
			$this->email->set_newline("\r\n");
			$this->email->set_mailtype('html');
			$this->email->from($row['fromEmailAddress'], $row['fromName']);
			$this->email->reply_to($row['replyToEmailAddress'], $row['replyToName']);
			$this->email->to($row['toEmailAddress']);
			$this->email->cc($row['ccEmailAddress']);
			$this->email->subject($row['subject']);
			$this->email->message($row['message']);
			$this->email->set_alt_message($row['alt_message']);
			
			// Deal with attachments
			$attchRecords = $this->getEmailAttachmentsById($row['queuedEmailId']);
			if (!is_null($attchRecords))
			{
				foreach($attchRecords as $attach)
				{
					// Check for the attachment (file) - we are still going to send the email
					// event if one or more attachments are missing
					if (file_exists($attach['attachment']))
						$this->email->attach($attach['attachment']);
					else
						$debugString .= 'Attachment File (' . $attach['attachment'] . ') is missing. '; 
				}
			}
			// Send the email and make updates to the database
			if (!$this->email->send())
			{
				$debugString = $this->email->print_debugger();
				$this->setProcessFailed($row['queuedEmailId'], $debugString);
			}
			else
			{
				$this->setProcessSucceeded($row['queuedEmailId'], $debugString);
			}
		}
	}


}


/* End of file mail_queue.php */
