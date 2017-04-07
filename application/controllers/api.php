<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'controllers/Master_Controller.php');


class API extends Master_Controller
{

    function __construct()
    {
      parent::__construct();
      $this->load->model('auth_m');
      $this->load->helper('url');
    	$this->load->config('sitesettings');
    }
    
    
	//----------------------------------------------------------------------
	//
	// Client only fucntions
	//
	//----------------------------------------------------------------------
    function client($function, $dataId)
    {
	    $results = array('command' => $function, 'code' => '0000');
        try 
        {
            $where = array('osr_usa_id_ref' => 'cloverfield');
            $data = $this->auth_m->selectRecord('oauth_server_registry',$where);
            if(!isset($data['osr_id'])) 
            {
                $results['code'] = '1060';
            } 
            else 
            {
                $key    = $data['osr_consumer_key'];
                $secret = $data['osr_consumer_secret'];
                define("CONSUMER_KEY", $key);
                define("CONSUMER_SECRET", $secret);
                
                $url = $data['osr_url'].'/server';
                
                //Get me a token to use
                $data = array('consumerKey' => CONSUMER_KEY, 'consumerSecret' => CONSUMER_SECRET, 'action' =>  'request_token');
                $data['signature'] = $this->generate_signed_request(array('data' => $data), $secret);
                
                //////$results = $this->callCurl($url,$data);
                //////echo 'RESULTS:';
                //////print_r($results);
                //////exit;
               
                $results = json_decode($this->callCurl($url,$data));
                if (isset($results->token)) 
                {
                    $token = $results->token;
                    //With a token I can make my second curl call to process the request
                    $bodyData = $this->auth_m->getPackageData($dataId);
                    $data = array('consumerKey' => CONSUMER_KEY, 'consumerSecret' => CONSUMER_SECRET, 'token' => $token, 'action' =>  'process_request', 'command' => $function, 'body' => $bodyData);
                    $data['signature'] = $this->generate_signed_request(array('data' => $data), $secret);
                    $data['body'] = $bodyData;
                    $result = $this->callCurl($url,$data);                
                    
                    $orderIds = @json_decode($result,true);
                    if (isset($orderIds['butterflyExpressOrderId'])) {
                        $this->load->model('account_m');
                        if ($this->account_m->updateOrderRecord($orderIds)) {
                            //redirect('welcome/account/'.);
                            exit;
                        } else {
//                            echo 'error';
                            echo $result;
                        }
                    }
                    
                    
                    
                    
                    
                    exit;

                } 
                else 
                {
                    $results['code'] = '1050';
                }
            }
        } catch (Exception $e) {
            $results['code'] = '1070';
        }
          
        redirect('alert/failure/'.$results['code']);
  /*         return $results; */
        exit();
    }   
    
    
    public function verify_registration($returnData, $originalData, $polling = FALSE, $exitBlank = FALSE)
    {
      if($polling)
  		  redirect('alert/success/4000'); 
      // Complete oauth_trans
      $this->auth_m->completeOauthTransaction($originalData['oauthTransId']);
      
  		if(isset($returnData->code) && $returnData->code == '0000')
  		{
  			// update database
  			$this->auth_m->completeDriverFacilityRegistration($originalData);
  			if($exitBlank)
  		    return FALSE;
  		  else 
  		    redirect('facilities/settings/'.$originalData['facilityId']);
  		}
  		else
  		{
  		  $this->auth_m->deleteDriverFacilityRegistration($originalData);
  		  if($exitBlank)
  		    return FALSE;
  		  else  
  			  redirect('alert/failure/'.$returnData->code);
  		  
  			exit();
  		}
    }

    public function ticket_prefs($returnData, $originalData, $polling = FALSE, $exitBlank = FALSE)
    {
      if($polling)
  		  redirect('alert/success/4000');
      // Complete oauth_trans
      $this->auth_m->completeOauthTransaction($originalData['oauthTransId']);
  		if(isset($returnData->code) && $returnData->code == '0000')
  		{
        // update database
        $this->auth_m->completeTicketPrefs($originalData);

        if($exitBlank)
  		    return FALSE;
  		  else
  		  {
  		    @session_start();
          $_SESSION['settings_success'] = true;
          redirect('facilities/settings/'.$originalData['facilityId']);
        }
  		
  		}
  		else
  		{
        @session_start();
		    $_SESSION['settings_failure'] = false;
		    if($exitBlank)
  		    return FALSE;
  		  else 
  		  {
  		    @session_start();
          $_SESSION['settings_success'] = true;
		      redirect('facilities/settings/'.$originalData['facilityId']);
		    }
  		}
    }      
    
    
	//----------------------------------------------------------------------
	//
	// Server only fucntions
	//
	//----------------------------------------------------------------------
    function server($facilityId, $dataId)
    {
	    $dataResponse = array('command' => 'server', 'code' => '0000', 'matt' => 'testing');
        $action = (isset($_POST['action'])) ? strtolower(trim($_POST['action'])) : '';
        switch ($action) {
            case    'request_token':        $secret = trim($_POST['consumerSecret']);
                                            $env = $this->parse_signed_request($_POST['signature'],$secret);
                                            $isValidResponse = $this->validataSignature($_POST,$env);
                                            $token = '';
                                            if ($isValidResponse) {
                                                $token = $this->generateToken($_POST['consumerKey'], $_POST['consumerSecret']);
                                            }
                                            $dataResponse['token'] = $token;
                                            break;
            case    'process_request':      $secret = trim($_POST['consumerSecret']);
                                            $env = $this->parse_signed_request($_POST['signature'],$secret);
                                            $isValidResponse = $this->validataSignature($_POST,$env);
                                            $token = '';
                                            if ($isValidResponse) {
                                                $token = trim($_POST['token']);
                                                if ($token != '') {
                                                    $dbResults = $this->auth_m->getRegistry($token);
                                                    if (isset($dbResults['osr_id'])) {
                                                        $dataResponse = $this->processServerActions($_POST, $_FILES);
                                                    } else {
											            $dataResponse['code'] = '2050';
                                                    }
                                                } else {
										            $dataResponse['code'] = '2060';
                                                }
                                            } else {
									            $dataResponse['code'] = '2070';
                                            }
                                            break;
            default:                        echo '';
        }
        echo json_encode($dataResponse);
        exit;
    }
	      
    function processServerActions($body, $file = array())
    {
        $results = array();
        try {
        	//Process based on command
        	$data = json_decode($body['body'], TRUE);
        	$facilityId = $data['facilityId'];
        	$data = json_decode($data['data'], TRUE);
					$command = $data['command'];
					$result = array();
					switch ($command) {
						case 'visitDocsUpload':		$results = $this->processDocUpload($data, $file, $facilityId, $command);
																			break;
													
						case 'generateVisitId':		$results = $this->generateVisitId($data, $facilityId);
																			break;
						case 'sendVisitEmail':		$results = $this->sendVisitEmail($data, $facilityId);
																			break;
						case 'completeVisit':		  $results = $this->completeVisit($data, $facilityId);
																			break;
						
						case 'registerBlackBox':	$results = $this->processRegistration($data, $facilityId, $command);
																			break;
																			
						case 'togglePolling':     $results = $this->updatePolling($data, $facilityId);
						                          break;
						                          
						case 'requestPolling':    $results = $this->requestPolling($facilityId);
						                          break;
						                          
						case 'responsePolling':   $results = $this->responsePolling($data, $facilityId);
						                          break;
		
					}
        } catch (Exception $e) {
        }
        return $results;
    }
    
    // Process Server Action Functions
    
    public function responsePolling($data, $facilityId)
    {
      $results = array('command' => 'responsePolling');
      if(!is_array($data))
      {
        $results['code'] = '3050';
        return $results;
      }
        
      foreach($data as $transId => $record)
      {
        
        if(is_numeric($transId))
        {
          $record['command'] = trim($record['command']);
          if(method_exists('api', $record['command']))
          {
            $originalData = $this->auth_m->getPackageRecord($transId);
            $returnData = new stdClass();
            $returnData->code = $record['code'];
            $originalData['oauthTransId'] = $transId;
            $this->$record['command']($returnData, $originalData, FALSE, TRUE);
          }
        }
      }
        $results['code'] = '0000';
        return $results;
        
    }
    
    public function requestPolling($facilityId)
    {
      $results = array();
	    try {
		    
		    $data = $this->auth_m->requestPolling($facilityId);
		    if($data)
		    {
		      $results['polling'] = $data;
          $results['code'] = 0000;
          $results['command'] = 'requestPolling';
		    }
		    else
		      $results['code'] = 3040;
		    return $results;
	    } catch (Exception $e) {
		    $results['code'] = 1070;
	    }
	    return $result;
    }
    
    public function updatePolling($data, $facilityId)
    {
      $results = array();
	    try {
		    $this->load->model('auth_m');
		    $success = $this->auth_m->updatePolling($data, $facilityId);
		    if($success)
		    {
		      $results['polling'] = $data['polling'];
          $results['code'] = 0000;
          $results['command'] = 'togglePolling';
		    }
		    else
		      $results['code'] = 3030;
		    return $results;
	    } catch (Exception $e) {
		    $results['code'] = 1070;
	    }
	    return $result;
    }
    
    public function getDriverId($data, $facilityId)
    {
    	$this->load->model('facility_m');
    
    	$authData = $data['driverAuthentication'];
    	$enrollmentFields = $this->facility_m->getMyFacilityDetails($facilityId);
			$enrollmentFields = json_decode($enrollmentFields['enrollmentFields'], true);
			unset($enrollmentFields['equipment']);
			unset($enrollmentFields['visitDate']);
			$r = array();
			$error = false;
			foreach ($enrollmentFields AS $key => $val) {
				if (isset($data['driverAuthentication'][$key])) {
					$r[$key] = $data['driverAuthentication'][$key];
				} else {
					$error = true;
				}
			}
			$driverId = FALSE;
			if (!$error) {
			  $r = json_encode($r);
				$driverId = $this->auth_m->getDriverId($r);
			}
			
	    return $driverId;
    }
    
    public function generateVisitId($data, $facilityId)
    {
      $results = array();
	    try {
	    
		    $date_regex = '/^((\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2}))$/';
				if (!preg_match($date_regex, @$data['visitDate'])) // Generate visitDate if not Passed
					$data['visitDate'] = date("Y-m-d H:i:s");
				
        if($data['driverAuthentication']) // Generate DriverId from DriverAuthentication Data if not Passed.
				{
				  $data['driverId'] = $this->getDriverId($data, $facilityId);
				}
        if(!@$data['driverId'])
        {
          $results = array('code' => '3010');
					return $results;
        }
				// VisitId does not exist for this facility 
				if(!$this->auth_m->validateVisitId(@$data['visitId'], $facilityId)) 
  	    	$visitId = $this->auth_m->createVisitId($data, $facilityId);
	    	else
	    	{
		    	$results = array('code' => '3060'); // VisitId already exists for this Facility
					return $results;
	    	}
		    
		    if(@$visitId)
		    {
		      $results['visitId'] = $visitId;
          $results['code'] = 0000;
          $results['command'] = 'generateVisitId';
		    }
		    else
		      $results['code'] = 3020;
		    return $results;
	    } catch (Exception $e) {
		    $results['code'] = 1070;
	    }
	    return $result;
    }
    
    public function sendVisitEmail($data, $facilityId)
    {
      $results = array('command' => 'sendVisitEmail');
	    try 
	    {
				$visitId        = $data['visitId'];
		    $emailTickets   = $this->auth_m->getDriverTicketPreferences($visitId); // Returns driver Ticket preferences record if true
		    $ticketProvided = $this->auth_m->verifyTicket($visitId); // returns mediaIds for tickets if true
		    if($emailTickets && $ticketProvided)
		    {
		      $htmlTemplate = $this->getLangView('email/visit_html_v.php');
          $textTemplate = $this->getLangView('email/visit_text_v.php');
          $this->auth_m->markEmailSent($visitId);
          $this->auth_m->generateTicketEmail($emailTickets, $ticketProvided, $htmlTemplate, $textTemplate); 
          $results['code'] = 0000;
		    }
		    return $results;
	    } catch (Exception $e) {
		    $results['code'] = 1070;
	    }
	    return $result;
    }
    
    public function completeVisit($data, $facilityId)
    {
      $results = array('command' => 'completeVisit');
	    try 
	    {
				$visitId        = $this->auth_m->$data['visitId'];
				$completeDate   = $data['closeDate'];
		    $date_regex = '/^((\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2}))$/';
				if ((trim($completeDate) == '') || (!preg_match($date_regex, $completeDate))) {
					$completeDate = date("Y-m-d H:i:s");
				}
				
        if ($this->auth_m->completeVisit($visitId, $completeDate)) {
	        $results['code'] = 0000;
        } else {
			    $results['code'] = 0000;
			  }
		    return $results;
	    } catch (Exception $e) {
		    $results['code'] = 1070;
	    }
	    return $result;
    }
    
    
    public function processDocUpload($data, $file, $facilityId, $command)
    {
    	$this->load->model('fw_local_media_storage_model');
    	
	    try {
	    	$results = array();
	    	$results['command'] = $command;
	    	$visitId = $this->auth_m->validateVisitId(@$data['visitId'], $facilityId);
	    	if(!$visitId) // VisitId does not exist for this facility
	    	{
  	    	$results['code'] = 3060;
  	    	return $results;
	    	}

        if(isset( $data['documentTypeId']) && (is_numeric($data['documentTypeId'])))
          $documentTypeId   = $data['documentTypeId'];
        else
          $documentTypeId   = 1; // Set document type to Other if not provided.
        

	    	$directory = $this->createDirectory($facilityId, $visitId);
	    	if (trim($directory) != '') 
	    	{
		    	$originalFileName = $data['originalFileInfo']['visitDoc']['name'];
		    	$fileLocation	    = $file['file']['tmp_name'];
		    	$fileName		      = $file['file']['name'];
		    	$destination	    = $directory.'/'.$fileName;
					
					$newMediaId = $this->fw_local_media_storage_model->storeMedia($fileLocation, $originalFileName);
					
					//store in database
					$recordId = $this->auth_m->updateVisitDocumentTable($newMediaId, $visitId, $destination, $documentTypeId);
					if ($recordId != 0) {
						
					} else {
						$results['code'] = 5040;
					}

				} else {
					$results['code'] = 5060;
				}
	    } catch (Exception $e) {
			$results['code'] = 5070;
	    }
	    return $results;
	    exit;
    }
    
    public function processRegistration($data, $facilityId, $command)
    {
	    $results = array('command' => $command, 'code' => '0000');
        try {
            
            //process the registration request from the black box by doing the folowing items
            //
            //   1.  create a oauth_server_registry record that is inactive
            //   2.  create a facility table record that is inactive
            //   3.  send out a email notice to admin of site to possibly activate a new facility
            //
            //

            
            
            exit;
        } catch(OAuthException2 $e) {
			$results['code'] = 6070;
        }
        return $results;
        exit;
    }
    
    
    public function createDirectory($facilityId, $visitId)
    {
    	try {
	    	$destinationDirectory = $this->config->item('mediaRoot').'/'.$facilityId;
	    	if (is_dir($destinationDirectory)) {
	    		$destinationDirectory .= '/'.$visitId;
		    	if (!is_dir($destinationDirectory)) {
			    	if (!mkdir($destinationDirectory)) {
				    	$destinationDirectory = '';
			    	}
		    	}
	    	} else {
		    	if (mkdir($destinationDirectory)) {
			    	$destinationDirectory .= '/'.$visitId;
			    	if (!mkdir($destinationDirectory)) {
				    	$destinationDirectory = '';
			    	}
		    	}
	    	}
	    	return $destinationDirectory;
    	} catch (Exception $e) {
	    	
    	}
	    exit;
    }

    
	//----------------------------------------------------------------------
	//
	// AUTH only fucntions
	//
	//----------------------------------------------------------------------
    function validataSignature($input,$env) {
        if ($input['consumerKey'] == $env['data']['consumerKey']) {
            if ($input['consumerSecret'] == $env['data']['consumerSecret']) {
                if ($input['action'] == $env['data']['action']) {
                    $difftime = abs(date("U") - $env['issued_at']);   //calc time 2 mins
                    if ($difftime < 120) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    
    function generateToken($consumerKey, $consumerSecret)
    {
        $token = '';
        try {
            $tempString = $consumerKey.'-'.$consumerSecret.'-'.date("U");
            $tempToken = md5($tempString);
            $data = array('osr_token' => $tempToken);
       
            $isUpdated = $this->auth_m->saveToken($data, $consumerKey, $consumerSecret);
            if ($isUpdated) {
                $token = $tempToken;
            }
        } catch (Exception $e) {
            //process error
        }
        return $token;
    }
    
    
    function base64_url_decode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }    
    
    
    function parse_signed_request($input, $secret, $max_age=3600) {
    
    
  //  echo $input.'<br><br>';
    
    
        list($encoded_sig, $encoded_envelope) = explode('.', $input, 2);
        $envelope = json_decode($this->base64_url_decode($encoded_envelope), true);
        $algorithm = $envelope['algorithm'];
        if ($algorithm != 'HMAC-SHA256') {
            throw new Exception('Invalid request. (Unsupported algorithm.)');
        }
        if ($envelope['issued_at'] < time() - $max_age) {
            throw new Exception('Invalid request. (Too old.)');
        }
        if ($this->base64_url_decode($encoded_sig) != hash_hmac('sha256', $encoded_envelope, $secret, $raw=true)) {
            throw new Exception('Invalid request. (Invalid signature.)');
        }
        return $envelope;
    }
    
    
    function base64_url_encode($input) {
        $str = strtr(base64_encode($input), '+/=', '-_.');
        $str = str_replace('.', '', $str); // remove padding
        return $str;
    }
    
    
    function generate_signed_request($data, $secret) {
        // always present, and always at the top level
        $data['algorithm'] = 'HMAC-SHA256';
        $data['issued_at'] = time();
        $payload = $this->base64_url_encode(json_encode($data));
        $sig = $this->base64_url_encode(hash_hmac('sha256', $payload, $secret, $raw=true));
        return $sig.'.'.$payload;
    }
    
    
    function callCurl($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));        
        $results = curl_exec($ch);
        curl_close($ch);
        return $results;
    }
  
  
}