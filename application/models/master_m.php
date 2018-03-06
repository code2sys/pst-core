<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_M extends CI_Model
{
  private $_ruleSetArray = array();

	function __construct()
	{
		parent::__construct();
		//$this->load->database('default');

	}
	
		/**
	 * formSafePrep function.
	 * 
	 * Removes fields from form data that are not setup as rules in
	 * form validaton.
	 *
	 * @access private
	 * @param mixed $inData
	 * @return void
	 */
	public function formSafePrep($inData)
	{
		$outData = array();
		$validFields = array();
		foreach($this->form_validation->_field_data as $obj)
		{
			$validFields[] = $obj['field'];
		}
		foreach($inData as $dataField => $value)
		{
			if (in_array($dataField, $validFields))
				$outData[$dataField] = $value;
		}
		return $outData;
	}
	
		/**
	 * getColumns function.
	 * 
	 * Uses the MySQL "SHOW COLUMNS" command to return the
	 * table schema.
	 *
	 * @access public
	 * @param mixed $table
	 * @return array (result_array)
	 */
	public function getColumns($table)
	{
		$result = FALSE;
		$sql = "SHOW COLUMNS FROM `{$table}`";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		$query->free_result();			
		return $result;		
	}
	
  /**
   * Email validation functions
   */
  protected function setValidationRules($ruleSetName, $rulesArray)
  {
	  $this->_ruleSetArray[$ruleSetName] = $rulesArray;
  }
  
  public function validate(&$valueArray=NULL, $ruleSetName, $keepOriginalValues=FALSE)
  {
	  $this->load->library('DataValidation'); 
	  $this->datavalidation->clearRules();
	  $this->datavalidation->setRules($this->_ruleSetArray[$ruleSetName]);
	  return $this->datavalidation->validate($valueArray, $keepOriginalValues);
  }
  
  protected function setErrorCode($errCode)
  {
	  $this->_errorCode = $errCode;
  }
  
  public function getErrorCode()
  {
	  $retVal = $this->_errorCode;
	  $this->_errorCode = NULL;
	  return $retVal;
  }

	/**
	 * createRecord function.
	 * 
	 * Basic CRUD function for inserting a record into a table.
	 * If the table has an Auto-Increment column defined, the
	 * new ID is returned.  Otherwise, this function returns
	 * FALSE.
	 *
	 * The array of data is checked against the table schema
	 * using only those values that have a corresponding field.
	 *
	 * @access public
	 * @param mixed $table - the table
	 * @param mixed $data - associative array of data to insert
	 * @param boolean $formSafe - update only those fields that have a
	 *                            rule set in form validation
	 * @return boolean
	 */
	/*
	 * JLB 07-07-17
	 * I have no words for how insane this function is.
	 * You mean we are going to dynamically go interrogate the information_schema to decide what to do?
	 * There's no end of describing how insane that is.
	 */
	public function createRecord($table, $data, $formSafe=TRUE)
	{
		$ret = FALSE;
		$auto_increment = FALSE;
		if ($formSafe)
			$data = $this->formSafePrep($data);
		$columns = $this->getColumns($table);
		foreach ($columns as $field)
		{
			// Ignore any column that does not have a corresponding data element
			if (isset($data[$field['Field']]))
			{
				if ( $data[$field['Field']] === "" )
				{
					// If data element is empty, set to null (if allowed in the db)
					if ($field['Null'] == 'YES')
						$this->db->set($field['Field'], null);
				}
				else
				{
					$this->db->set($field['Field'], $data[$field['Field']]);
				}
			}
			// Note if this is an Auto-Increment Field
			if ($field['Extra'] == 'auto_increment')
				$auto_increment = TRUE;
			// If this field is recCreated populate date/time
			if ($field['Field'] == 'createdDate')
				$this->db->set('createdDate', time());
		}
		// Perform the Insert
    if ($this->db->insert($table)) 
    {
			if ($auto_increment)
				$ret = $this->db->insert_id();
			else
				$ret = TRUE;
    }
    else
    {
      $errNo = $this->db->_error_number();
      $errMess = $this->db->_error_message();
      log_message("error", "Inserting ".$table." : ".$errMess." (".$errNo.")"); 
    }
		return $ret;
	}

	/**
	 * updateRecord function.
	 * 
	 * Basic CRUD function for pdating a record in a table.
	 *
	 * The array of data is checked against the table schema
	 * using only those values that have a corresponding field.
	 *	 
	 * @access public
	 * @param mixed $table
	 * @param mixed $data
	 * @param mixed $where
	 * @param boolean $formSafe - update only those fields that have a
	 *                            rule set in form validation
	 */
	public function updateRecord($table, $data, $where, $formSafe=TRUE)
	{
		$ret = FALSE;
		if ($formSafe)
			$data = $this->formSafePrep($data);
		$columns = $this->getColumns($table);
		foreach ($columns as $field)
		{
			if (isset($data[$field['Field']]))
			{
				if ( $data[$field['Field']] === "" )
				{
					// If data element is empty, set to null (if allowed in the db)
					if ($field['Null'] == 'YES')
						$this->db->set($field['Field'], NULL);
          else
                  $this->db->set($field['Field'], '');
          
				}
				else
				{
					$this->db->set($field['Field'], $data[$field['Field']]);
				}
			}
			// If this field is recUpdated populate date/time
			if ($field['Field'] == 'updatedDate')
				$this->db->set('updatedDate', time());
		}
		// Perform the Update
		$this->db->where($where);
    if ($this->db->update($table)) 
    {
			$ret = $this->db->insert_id();
    }
    else
    {
      $errNo = $this->db->_error_number();
      $errMess = $this->db->_error_message();
      log_message("error", "Updating ".$table." : ".$errMess." (".$errNo.")"); 
    }
		return $ret;
	}
	
  protected function updateRecords($table, $data, $where)
	{
		$retVal = FALSE;
		if ($where)
			$this->db->where($where);
    if ($this->db->update($table, $data)) 
    {
			$retVal = $this->db->affected_rows();
    }
    else
    {
	    $this->setDBError();      
      $retVal = FALSE;
    }
		return $retVal;		
	}
	
	public function deleteRecord($table, $where=NULL, $markOnly=FALSE, $userId = NULL)
	{
		$ret = FALSE;
		if ($markOnly)
		{
			$data = array('deleted' => time(), 'updatedBy' => $userId);
			$ret = $this->updateRecord($table, $data, $where, FALSE);
		}
		else
		{
			if ($where)
				$this->db->where($where);
			if ($this->db->delete($table))
				$ret = $this->db->affected_rows();
			else
			{
      	$errNo = $this->db->_error_number();
      	$errMess = $this->db->_error_message();
      	log_message("error", "Deleting ".$table." : ".$errMess." (".$errNo.")"); 
			}
		}
		return $ret;
	}
	
	public function selectRecord($table, $where=NULL)
	{
		$result = FALSE;
		if($where)
			$this->db->where($where);
		$this->db->from($table);
		$query = $this->db->get();
		if($query->num_rows() > 0)
			$result = $query->row_array();
		$query->free_result();
		return $result;
	}
	
	public function recordExists($table, $where=NULL, $backticks=NULL)
	{
		$ret = FALSE;
		$num = 0;
		$this->db->select('1');
		if (!is_null($where))
		{
			$this->db->where($where, NULL, $backticks);
			$this->db->from($table);
			$num = $this->db->count_all_results();
		}
		else
		{
			$num = $this->db->count_all($table);
		}
		$ret = ($num > 0);
		return $ret;
	}
	
	public function selectRecords($table=NULL, $where=NULL, $orderBy=NULL, $limit=NULL, $offset=NULL, $excludeDeleted=FALSE)
	{
		$result = FALSE;
		if (!is_null($where))
			$this->db->where($where);
		if ($excludeDeleted)
			$this->db->where(array($table.'.recDeleted is NULL' => NULL));
		if (!is_null($table))
			$this->db->from($table);
		if (!is_null($limit))
		{
			if (!is_null($offset))
				$this->db->limit($limit, $offset);
			else
				$this->db->limit($limit);			
		}
		if (!is_null($orderBy))
			$this->db->order_by($orderBy); 		
		$query = $this->db->get();
		if ($query->num_rows() > 0)
			$result = $query->result_array();
		$query->free_result();	
		return $result;
	}
	
	public function getValueNameArray($valuefield, $namefield, $table, $order=NULL, $where=NULL)
	{
		$arr = FALSE;
		$this->db->select($valuefield . ', ' . $namefield);
		$this->db->from($table);
		if (!is_null($where)) 
			$this->db->where($where);			
		if (!is_null($order)) 
			$this->db->order_by($order);
		$query = $this->db->get();
		if ($query->num_rows() > 0)
		{
			$arr = array();
			foreach ($query->result_array() as $row)
 				$arr[$row[$valuefield]] = $row[$namefield];
		}
		$query->free_result();
		return $arr;
	}
}
?>
