<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_m extends Master_M {

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
  
  
  
  public function createPackage($data, $orderId)
  {
        $data = array('data' => $data);
        return $this->createRecord('oauth_trans', $data, false);
  }
  
  public function getPackageData($dataId)
  {
    $where = array('oauthTransId' => $dataId);
    $data = $this->selectRecord('oauth_trans', $where);
    return $data['data'];
  }
  
  public function getPackageRecord($dataId)
  {
    $where = array('oauthTransId' => $dataId);
    $data = $this->selectRecord('oauth_trans', $where);
    return $data;
  }
  
  public function getDriverFacilityRecord($driverId, $facilityId)
  {
    $where = array('driverId' => $driverId, 'facilityId' => $facilityId);
    $record = $this->selectRecord('driver_facility', $where);
    return $record;
  }
  
  public function getDriverTicketPreferences($visitId)
  {
    $record = FALSE;
    $where = array('visitId' => $visitId);
    $this->db->join('driver_facility_ticket', 'driver_facility_ticket.driverFacilityId = driver_facility_visit.driverFacilityId');
    $record = $this->selectRecord('driver_facility_visit', $where);
    return $record;
  }
  
  public function verifyTicket($visitId)
  {
    $records = FALSE;
    $where = array('visit_document.visitId' => $visitId, 'visit_document.documentTypeId' => 2); // DocTypeId 2 is Ticket
    $this->db->select('localMediaId');
    $this->db->join('driver_facility_visit', 'driver_facility_visit.visitId = visit_document.visitId');
    $records = $this->selectRecords('visit_document', $where);
    return $records;
  }

  public function markEmailSent($visitId)
  {
    
    $where =  array('visitId' => $visitId);
    if($this->recordExists('visit_document', $where))
    {
	    $data = array('documentEmailed' => date('Y-m-d H:i:s'));
      $this->updateRecords('visit_document', $data, $where);
      return TRUE;
    }
  }
  
  public function validateVisitId($visitId = FALSE, $facilityId)
  {
  	if(!$visitId) 
  		return FALSE;
  		
  	$record = FALSE;	
    $where = array('driver_facility_visit.facilityRefId' => $visitId, 'driver_facility.facilityId' => $facilityId);
    $this->db->join('driver_facility', 'driver_facility.driverFacilityId = driver_facility_visit.driverFacilityId');
    $record = $this->selectRecord('driver_facility_visit', $where);// Check facilityRefId against visitId passed in.

    if($record) 
      return $record['visitId'];
    else
    {
	    $where = array('driver_facility_visit.visitId' => $visitId, 'driver_facility.facilityId' => $facilityId);
	    $this->db->join('driver_facility', 'driver_facility.driverFacilityId = driver_facility_visit.driverFacilityId');
	    
	    if($this->recordExists('driver_facility_visit', $where)) // Pass readyAGS visitId against visitId passed in.
	      return $visitId;
	    else
	      return FALSE;
    }
  }
  
 /**
	 * emailAttachment function.
	 * 
	 * Creates an originally named file from the mediaId to submit as an email attachment based
	 * on the mediaId and a security key (if required).
	 *  
	 * @access public
	 * @param mixed $mediaId
	 * @return varchar - containing the path to the originally named file for attachment.
	 */
	
	public function emailAttachment($mediaId)
	{
  	$this->load->model('fw_local_media_retrieval_model', 'media');
  	$mediaRow = $this->media->getMediaRow($mediaId);
  	$this->load->config('sitesettings');
  	$destinationDirectory = $this->createDirectory($this->config->item('mediaRoot').'/attachments');
  	$finalFileLoc = $destinationDirectory . '/' . $mediaRow['origFileName'];
  	
  	$transFile = file_get_contents($mediaRow['mediaPath']);
  	file_put_contents($finalFileLoc, $transFile);
    return $finalFileLoc; 	
	} 
	
	private function createDirectory($destinationDirectory)
  {
  	try 
  	{
    	if (!is_dir($destinationDirectory)) 
    	{
	    	if (!mkdir($destinationDirectory)) 
		    	$destinationDirectory = '';
    	}    	 
    	return $destinationDirectory;
  	} 
  	catch (Exception $e) 
  	{
    	
  	}
  }
  
  public function generateTicketEmail($ticketsPrefs, $ticketMediaIds, $htmlTemplate, $textTemplate)
  {
    // All tickets must be moved and renamed before being attached
    $attachments = array();
    foreach($ticketMediaIds as $rec)
    {
      if(@$rec['localMediaId'])
      $attachments[] = $this->emailAttachment($rec['localMediaId']);
    }
    
    // Generate Email
    
    $this->config->load('sitesettings');
    $mailData = array();
		$templateData = array();
		$mailData = array('toEmailAddress' => $ticketsPrefs['toEmailAddress'],
	                    'subject' => 'readyAGS Visit Ticket Email',
	                    'fromEmailAddress' => $this->config->item('fromEmailAddress'),
	                    'fromName' => $this->config->item('fromName'),
	                    'replyToEmailAddress' => $this->config->item('replyToEmailAddress'),
	                    'replyToName' => $this->config->item('replyToName'));
		$templateData['emailBodyImg'] = site_url('assets/images/email_body.jpg');
		$templateData['emailFooterImg'] = site_url('assets/images/email_footer.jpg');
		$templateData['emailHeadImg'] = site_url('assets/images/email_head.jpg');
		$templateData['emailShadowImg'] = site_url('assets/images/email_shadow.jpg');
		$this->load->model('fw_gen_mail_model');                                               
		$retVal = $this->fw_gen_mail_model->generateFromView($mailData, $templateData, $htmlTemplate, $textTemplate, $attachments); 
		return $retVal;
  }
  
  public function completeDriverFacilityRegistration($inputData)
  {
    // Remove the Command from the Driver Registration Data
    $temp = @json_decode($inputData['data'],true);
    unset($temp['command']);
    $inputData['data'] = json_encode($temp);
    
    $where =  array('driverId' => $inputData['driverId'], 'facilityId' => $inputData['facilityId']);
    if($this->recordExists('driver_facility', $where))
    {
      $this->updateRecords('driver_facility', array('enrolled' => 1, 'verificationData' => $inputData['data']), $where);
      return TRUE;
    }
    $inputData['enrolled'] = 1;
    $inputData['enrolledDate'] = date('Y-m-d H:i:s');
    $inputData['verificationData'] = $inputData['data'];
    unset($inputData['data']);
    unset($inputData['oauthTransId']);
    unset($inputData['complete']);
    unset($inputData['response']);
    $this->insertRecord('driver_facility', $inputData);
  }
  
  public function completeVisit($visitId, $completeDate)
  {
    $where = array('visitId' => $visitId);
    $data = array('complete' => $completeDate);
    $this->updateRecords('driver_facility_visit', $data, $where);
    return true;
  }
  
  public function deleteDriverFacilityRegistration($inputData)
  {
    $where =  array('driverId' => $inputData['driverId'], 'facilityId' => $inputData['facilityId']);
    if($this->recordExists('driver_facility', $where))
      $this->deleteRecord('driver_facility', $where);
  }
  
  public function completeOauthTransaction($dataId)
  {
    $where = array('oauthTransId' => $dataId);
    $complete = $this->updateRecords('oauth_trans', array('complete' => date('Y-m-d H:i:s')), $where);
    return $complete;
  }
	
	public function markForDelete($id)
	{
		return $this->markRecordsForDelete('fw_test', array('testId' => $id));		
	}

	public function unmarkForDelete($id)
	{
		return $this->unMarkRecordsForDelete('fw_test', array('testId' => $id));		
	}
	
	public function saveToken($data, $consumerKey, $consumerSecret)
	{
		$where = array('osr_consumer_key' => $consumerKey, 'osr_consumer_secret' => $consumerSecret);
        return $this->updateRecords("oauth_server_registry", $data, $where);
	}
	
	public function getRegistry($token)
	{
		$where = array('osr_token' => $token);
		$record = $this->selectRecord('oauth_server_registry', $where);
		return $record;
	}
	
	public function getFacilityRecord($facilityId)
	{
  	$where = array('facilityId' => $facilityId);
  	$record = $this->selectRecord('facility', $where);
  	return $record;
	}
	
	public function completeTicketPrefs($data)
	{

	  $data = json_decode($data['data'], TRUE);
  	
  	$updateData = array();
  	$updateData['driverFacilityId'] = $data['driverFacilityId'];
  	$updateData['printTicket'] = $data['printTicket'];
  	$updateData['emailTicket'] = $data['emailTicket'];
  	$updateData['toEmailAddress'] = @$data['toEmailAddress'];
  	$updateData['ccEmailAddress'] = @$data['ccEmailAddress'];
  	$where = array('driverFacilityId' => $data['driverFacilityId']);
  	if($this->recordExists('driver_facility_ticket', $where))
  	  $success = $this->updateRecords("driver_facility_ticket", $updateData, $where);
    else
      $success = $this->insertRecord("driver_facility_ticket", $updateData);
  	return $success;
	}
	
	// authData must already be json encoded before being past in.
	
	public function getDriverId($authData)
	{
		$where = array('verificationData' => $authData);
		$record = $this->selectRecord('driver_facility', $where);
		return @$record['driverId'];
	}
	
	public function createVisitId($data, $facilityId)
	{
		$inputData = array();
		if(@$data['visitId']) // Passed in VisitId
			$inputData['facilityRefId'] = $data['visitId'];
	
	  // Get Driver Facility Id
	  $where = array('facilityId' => $facilityId, 'driverId' => $data['driverId']);
	  $record = $this->selectRecord('driver_facility', $where);
	  // Create Visit Record
	  if($record)
	  {
	    $inputData['driverFacilityId'] = $record['driverFacilityId'];
	    $inputData['visitDate'] = $data['visitDate'];
	    $inputData['equipment'] = @$data['equipment'];
      $visitId = $this->insertRecord('driver_facility_visit', $inputData, TRUE);
      $record = $visitId;
			if(@$inputData['facilityRefId']) // Passed in VisitId
				$record = $inputData['facilityRefId'];
	  }
    return $record;
	}
	
	public function updatePolling($data, $facilityId)
	{
  	$where = array('facilityId' => $facilityId);
  	$updateData = array('polling' => $data['polling']);
  	$success = $this->updateRecords("facility", $updateData, $where);
  	return $success;
	}
	
	public function requestPolling($facilityId)
	{
	  $this->db->select('oauthTransId AS transId, data');
  	$where = array('facilityId' => $facilityId, 'complete IS NULL' => NULL);
  	$records = $this->selectRecords("oauth_trans", $where);
  	if(@$records) 
  	{
  	  foreach($records as &$rec)
  	  {
        $rec['data'] = json_decode($rec['data']);
  	  }
    }
  	return @$records;
	}	
	
	public function updateVisitDocumentTable($newMediaId, $visitId, $destination, $documentTypeId)
	{
		try {
			
	    $inputData = array('visitId' => $visitId,
	                       'localMediaId' => $newMediaId,
	                       'documentTypeId' => $documentTypeId);
      $response = $this->insertRecord('visit_document', $inputData, TRUE);
			return $response;
		} catch (Exception $e) {
		}
		return 0;
		exit;
	}
	
	
}

/* End of file fw_test_model.php */
/* Location: FRAMEWORK/models/fw_test_model.php */
