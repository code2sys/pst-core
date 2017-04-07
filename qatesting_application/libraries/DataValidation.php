<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
/**
 * DataValidation.php
 *
 * This file contains the implementation of the Master_M Class.  The
 * Master_M class should be used to inherit models within a framework-based
 * application.
 *
 * DEPENDENCIES
 *
 *
 * VALIDATION RULE				PARAMETER	DESCRIPTION																																											EXAMPLE
 * required								No				Returns FALSE if the form element is empty.	 
 * matches								Yes				Returns FALSE if the form element does not match the one in the parameter.											matches[form_item]
 * is_unique							Yes				Returns FALSE if the form element is not unique to the table and field name in the parameter.		is_unique[table.field]
 * exists                 Yes				Returns FALSE if the form element is not found in the table (& field name in the parameter).    exists[table.field]
 * is_in                  Yes				Returns FALSE if the form element is not found in array (parameter as string)										is_in[0,1,2,3,4]
 * min_length							Yes				Returns FALSE if the form element is shorter then the parameter value.													min_length[6]
 * max_length							Yes				Returns FALSE if the form element is longer then the parameter value.														max_length[12]
 * exact_length						Yes				Returns FALSE if the form element is not exactly the parameter value.														exact_length[8]
 * greater_than						Yes				Returns FALSE if the form element is less than the parameter value or not numeric.							greater_than[8]
 * less_than							Yes				Returns FALSE if the form element is greater than the parameter value or not numeric.						less_than[8]
 * alpha									No				Returns FALSE if the form element contains anything other than alphabetical characters.	 
 * alpha_numeric					No				Returns FALSE if the form element contains anything other than alpha-numeric characters.	 
 * alpha_dash							No				Returns FALSE if the form element contains anything other than alpha-numeric characters, underscores or dashes.	 
 * numeric								No				Returns FALSE if the form element contains anything other than numeric characters.	 
 * integer								No				Returns FALSE if the form element contains anything other than an integer.	 
 * decimal								NO				Returns FALSE if the form element is not a decimal number.	 
 * is_natural							No				Returns FALSE if the form element contains anything other than a natural number: 0, 1, 2, 3, etc.	 
 * is_natural_no_zero			No				Returns FALSE if the form element contains anything other than a natural number, but not zero: 1, 2, 3, etc.	 
 * valid_email_format			No				Returns FALSE if the form element does not contain a valid email address.	 (Checks format only)
 * valid_email_formats		No				Returns FALSE if any value provided in a comma separated list is not a valid email.	(Checks format only) 
 * valid_email_address		No				Returns FALSE if the form element does not contain a valid email address.	(Checks format AND MX Record)
 * valid_email_addresses	No				Returns FALSE if any value provided in a comma separated list is not a valid email.	(Checks format AND MX Record)
 * valid_ip								No				Returns FALSE if the supplied IP is not valid. Accepts an optional parameter of "IPv4" or "IPv6" to specify an IP format.	 
 * valid_base64						No				Returns FALSE if the supplied string contains anything other than valid Base64 characters.	 
 *
 * PREPPING FUNCTION
 * xss_clean							No				Runs the data through the XSS filtering function, described in the Input Class page.
 * prep_for_form					No				Converts special characters so that HTML data can be shown in a form field without breaking it.
 * prep_url								No				Adds "http://" to URLs if missing.
 * strip_image_tags				No				Strips the HTML from image tags leaving the raw URL.
 * encode_php_tags				No				Converts PHP tags to entities.
 *
 * Note: You can also use any native PHP functions that permit one parameter, like trim, htmlspecialchars, urldecode, etc.
 *
 */
final class DataValidation {

	protected $CI;
	protected $_field_data			= array();
	protected $_error_count			= 0;

/* 

TODO: add a var to hold the reference to the model.  This reference
will optionally be passed into the constructor of this library.

*/


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		// Set the character encoding in MB.
		if (function_exists('mb_internal_encoding'))
		{
			mb_internal_encoding($this->CI->config->item('charset'));
		}
	}

	/**
	 * getErrorCount
	 *
	 * Returns the number of rules that failed as the result of the last
	 * validation operation.
   *
	 * @access	public
	 * @return integer
	 */
	public function getErrorCount()
	{
		return $this->_error_count;
	}

	/**
	 * clearRules
	 *
	 * Clears the internal field_data array.
   *
	 * @access	public
	 * @return void
	 */
	public function clearRules()
	{
		$this->_field_data = array();
	}

	/**
	 * setRules
	 *
	 * This function takes an array of field names and validation
	 * rules as input, validates the info, and stores it
	 *
	 * @access	public
	 * @param	mixed $field - Array of fields & rules or a single field name (string)
	 * @param	string $rules - A string containing the rules for a single field name
	 * @return	void
	 */
	public function setRules($field, $rules = '', $label = '')
	{
		// If an array was passed via the first parameter instead of indidual string
		// values we cycle through it and recursively call this function.
		if (is_array($field))
		{
			foreach ($field as $row)
			{
				// Houston, we have a problem...
				if ( ! isset($row['field']) OR ! isset($row['rules']))
				{
					continue;
				}
				// Here we go!
				$this->setRules($row['field'], $row['rules'],  (isset($row['label']) ? $row['label'] : '')    );
				
				
				
			}
			return $this;
		}
		// No fields? Nothing to do...
		if ( ! is_string($field) OR  ! is_string($rules) OR $field == '')
		{
			return $this;
		}
		
		// If the field label wasn't passed we use the field name
		$label = ($label == '') ? $field : $label;
		
		// Is the field name an array?  We test for the existence of a bracket "[" in
		// the field name to determine this.  If it is an array, we break it apart
		// into its components so that we can fetch the corresponding POST data later
		if (strpos($field, '[') !== FALSE AND preg_match_all('/\[(.*?)\]/', $field, $matches))
		{
			// Note: Due to a bug in current() that affects some versions
			// of PHP we can not pass function call directly into it
			$x = explode('[', $field);
			$indexes[] = current($x);
			for ($i = 0; $i < count($matches['0']); $i++)
			{
				if ($matches['1'][$i] != '')
				{
					$indexes[] = $matches['1'][$i];
				}
			}
			$is_array = TRUE;
		}
		else
		{
			$indexes	= array();
			$is_array	= FALSE;
		}
		// Build our master array
		$this->_field_data[$field] = array(
			'field'					=> $field,
			'rules'					=> $rules,
			'label'					=> $label,
			'is_array'			=> $is_array,
			'keys'					=> $indexes,
			'postdata'			=> NULL,
			'error'					=> ''
		);
		return $this;
	}

	/**
	 * error
	 *
	 * Gets the error array associated with a particular field
	 *
	 * @access	public
	 * @param	string	the field name
	 * @return	array
	 */
	public function error($field = '')
	{
		$retArr = NULL;
		if ( ! isset($this->_field_data[$field]['error']) OR $this->_field_data[$field]['error'] == '')
		{
			return $retArr;
		}
		$row = $this->_field_data[$field];
		$param = FALSE;
		$pieces = explode("|", $row['error']);
		$rule = $pieces[0];
		if (count($pieces) > 1)
			$param = $pieces[1];
		$retArr = array( 'field' => $row['field'],
												'label' => $row['label'],
														'data' => $row['postdata'], 
         			              'rule' => $rule,
         			             'param' => $param );
		return $retArr;
	}

	/**
	 * errors
	 *
	 * Returns an associative array containing field->error
	 *
	 * @access	public
	 * @return	Array
	 */
	public function errors()
	{
		$retArr = NULL;
		foreach($this->_field_data as $row)
		{
			if (isset($row['error']) && ($row['error'] <> ''))
			{
				$param = FALSE;
				$pieces = explode("|", $row['error']);
				$rule = $pieces[0];
				if (count($pieces) > 1)
					$param = $pieces[1];
				$retArr[$row['field']] = array( 'field' => $row['field'],
														'label' => $row['label'],				
														'data' => $row['postdata'], 
         			              'rule' => $rule,
         			             'param' => $param );
      }
		}
		return $retArr;
	}


	/**
	 * scrubData
	 *
	 * Removes all fields in the data that do not have
	 * a corresponding rule.  In addition, this function
	 * copies any values that have been prepped (by one or
	 * more prepping rules into the data array, overwriting
	 * the original values.
	 *
	 * @access	private
	 * @param	$data (BY REFERENCE) : Associative array of data to be scrubbed
	 * @return	void
	 */
	private function scrubData(&$data)
	{
		// Scrub first - this will get rid of all of the values is the data
		// array that do not have a corresponding rule (even a blank rule).
		// This can prevent extraneous data from being written to a table
		// in the database.
		foreach($data as $dataField => $value)
		{
			$field_found = FALSE;
			foreach($this->_field_data as $row)
			{
				if ($dataField == $row['field'])
				{
					$field_found = TRUE;
					break;
				}
			}
			if (!$field_found)
			{
				// Remove the field from the $data array
				unset($data[$dataField]);
			}
		}
		// Now, copy the prepped values from postdata
		// for each element in the array
		foreach ($data as $data_field => $data_value)
		{
			foreach ($this->_field_data as $field => $row)
			{
				if ( $field == $data_field)
				{
					$data[$field] = $row['postdata'];
				}
			}
		}
	}

	/**
	 * validate
	 *
	 * This function does all the work.
	 *
	 * @access	public
	 * @param	$subjectData (BY REFERENCE) : Associative array of data to be validated
	 * @param	$keepOriginalValues : If this is TRUE, original data will not be changed
	 * @return	bool
	 */
	public function validate(&$subjectData=array(), $keepOriginalValues=FALSE)
	{
		$this->_error_count = 0;
		// Do we even have any data to process?  Mm?
		if (count($subjectData) == 0)
		{
			return FALSE;
		}
		// Does the _field_data array containing the validation rules exist?
		if (count($this->_field_data) == 0)
		{
			return FALSE;
		}
		// Cycle through the rules for each field, match the
		// corresponding $subjectData item and test for errors
		foreach ($this->_field_data as $field => $row)
		{
			// Fetch the data from the corresponding $subjectData array and cache it in the _field_data array.
			// Depending on whether the field name is an array or a string will determine where we get it from.
			if ($row['is_array'] == TRUE)
			{
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($subjectData, $row['keys']);
			}
			else
			{
				if (isset($subjectData[$field]) AND $subjectData[$field] !== '')
				{
					$this->_field_data[$field]['postdata'] = $subjectData[$field];
				}
			}
			if (count($subjectData) < 2)
			{
				reset($subjectData);
				if (key($subjectData) == $field)
					$this->_execute($row, explode('|', $row['rules']), $this->_field_data[$field]['postdata']);
			}
			else
				$this->_execute($row, explode('|', $row['rules']), $this->_field_data[$field]['postdata']);
		}

		// Update the subjectData array with prepped values
		if (!$keepOriginalValues)
		{
			// Scrub and Copy the prepped values into the data array
			$this->scrubData($subjectData);
		}
		// No errors, validation passes!
		if ($this->_error_count == 0)
		{
			return TRUE;
		}
		// Validation fails
		return FALSE;
	}

	/**
   * _reduce_array
   *
	 * Traverse a multidimensional array index until the data is found
	 *
	 * @access private
	 * @param	array
	 * @param	array
	 * @param	integer
	 * @return	mixed
	 */
	private function _reduce_array($array, $keys, $i = 0)
	{
		if (is_array($array))
		{
			if (isset($keys[$i]))
			{
				if (isset($array[$keys[$i]]))
				{
					$array = $this->_reduce_array($array[$keys[$i]], $keys, ($i+1));
				}
				else
				{
					return NULL;
				}
			}
			else
			{
				return $array;
			}
		}
		return $array;
	}

	/**
	 * Executes the Validation routines
	 *
	 * @access	private
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @param	integer
	 * @return	mixed
	 */
	private function _execute($row, $rules, $postdata = NULL, $cycles = 0)
	{
		// If the $_POST data is an array we will run a recursive call
		if (is_array($postdata))
		{
			foreach ($postdata as $key => $val)
			{
				$this->_execute($row, $rules, $val, $cycles);
				$cycles++;
			}
			return;
		}
		// If the field is blank, but NOT required, no further tests are necessary
		$callback = FALSE;
		if ( ! in_array('required', $rules) AND is_null($postdata))
		{
			// Before we bail out, does the rule contain a callback?
			if (preg_match("/(callback_\w+(\[.*?\])?)/", implode(' ', $rules), $match))
			{
				$callback = TRUE;
				$rules = (array('1' => $match[1]));
			}
			else
			{
				return;
			}
		}
		// Isset Test. Typically this rule will only apply to checkboxes.
		if (is_null($postdata) AND $callback == FALSE)
		{
			if (in_array('isset', $rules, TRUE) OR in_array('required', $rules))
			{
				// Set the message type
				$type = (in_array('required', $rules)) ? 'required' : 'isset';
				// JRN-ERR
				$this->_field_data[$row['field']]['error'] = $type;
				// Increment error count
				$this->_error_count++;
			}
			return;
		}
		// Cycle through each rule and run it
		foreach ($rules As $rule)
		{
			$_in_array = FALSE;
			// We set the $postdata variable with the current data in our master array so that
			// each cycle of the loop is dealing with the processed data from the last cycle
			if ($row['is_array'] == TRUE AND is_array($this->_field_data[$row['field']]['postdata']))
			{
				// We shouldn't need this safety, but just in case there isn't an array index
				// associated with this cycle we'll bail out
				if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles]))
				{
					continue;
				}
				$postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
				$_in_array = TRUE;
			}
			else
			{
				$postdata = $this->_field_data[$row['field']]['postdata'];
			}
			// Is the rule a callback?
			$callback = FALSE;
			if (substr($rule, 0, 9) == 'callback_')
			{
				$rule = substr($rule, 9);
				$callback = TRUE;
			}
			// Strip the parameter (if exists) from the rule
			// Rules can contain a parameter: max_length[5]
			$param = FALSE;
			$match = NULL;
			if (preg_match("/(.*?)\[(.*)\]/", $rule, $match))
			{
				$rule	= $match[1];
				$param	= $match[2];
			}
			// Call the function that corresponds to the rule
			if ($callback === TRUE)
			{
				if ( ! method_exists($this->CI, $rule))
				{
					continue;
				}
				// Run the function and grab the result
				$result = $this->CI->$rule($postdata, $param);
				// Re-assign the result to the master data array
				if ($_in_array == TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				}
				// If the field isn't required and we just processed a callback we'll move on...
				if ( ! in_array('required', $rules, TRUE) AND $result !== FALSE)
				{
					continue;
				}
			}
			else
			{
				if ( ! method_exists($this, $rule))
				{

/*

TODO: somewhere within this section, we want to implement the following:

- Search for the "native" method.
- If NOT found, search for the method on the model reference (if it
  is NOT NULL
- If still not found, try to execute a native PHP function  

ALSO, clean up the references to "callback".  We are not using this
functionality in this library.

*/

					// If our own wrapper function doesn't exist we see if a native PHP function does.
					// Users can use any native PHP function call that has one param.
					if (function_exists($rule))
					{
						$result = $rule($postdata);
						if ($_in_array == TRUE)
						{
							$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
						}
						else
						{
							$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
						}
					}
					else
					{
						log_message('debug', "Unable to find validation rule: ".$rule);
					}
					continue;
				}
				$result = $this->$rule($postdata, $param);
				if ($_in_array == TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
				}
			}
			// Did the rule test negatively?  If so, grab the error.
			if ($result === FALSE)
			{
				// JRN-ERR
				if ($param)
					$this->_field_data[$row['field']]['error'] = $rule . '|' . $param;
				else
					$this->_field_data[$row['field']]['error'] = $rule;
				// Increment error count
				$this->_error_count++;
				return;
			}
		}
	}

	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Performs a Regular Expression match test.
	 *
	 * @access	public
	 * @param	string
	 * @param	regex
	 * @return	bool
	 */
	public function regex_match($str, $regex)
	{
		if ( ! preg_match($regex, $str))
		{
			return FALSE;
		}

		return  TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	public function matches($str, $field)
	{
		if ( ! isset($this->_field_data[$field]))
		{
			return FALSE;
		}

		$field = $this->_field_data[$field]['postdata'];

		return ($str !== $field) ? FALSE : TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * is_in
	 *
   * Validates that value is within the supplied array (passed as comma-delimted string)
   *
	 * @access	public
	 * @param	string $str
	 * @param	string $array_string
	 * @return	bool
	 */
	public function is_in($str, $array_string)
	{
    $arr = explode(",", $array_string);
    return in_array($str, $arr); 
  }

	/**
	 * is_unique
	 *
   * Validates that value is unique within a specific field of a table
   *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	public function is_unique($str, $field)
	{
		list($table, $field)=explode('.', $field);
		$query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
		return ($query->num_rows() === 0);
  }

	// --------------------------------------------------------------------

	/**
	 * exists
	 *
   * Validates that value exists within a specific field of a table
   *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	public function exists($str, $field)
	{
		list($table, $field)=explode('.', $field);
		$query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
		return ($query->num_rows() === 1);
  }


	// --------------------------------------------------------------------

	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function min_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) < $val) ? FALSE : TRUE;
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function max_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) > $val) ? FALSE : TRUE;
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function exact_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) != $val) ? FALSE : TRUE;
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_email_format($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	public function valid_email_address($str)
	{
		$this->CI->load->helper('fw_email_address_validation');
		return validateEmailAddress($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Emails
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_email_formats($str)
	{
		if (strpos($str, ',') === FALSE)
		{
			return $this->valid_email_format(trim($str));
		}
		foreach (explode(',', $str) as $email)
		{
			if (trim($email) != '' && $this->valid_email_format(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	public function valid_email_addresses($str)
	{
		if (strpos($str, ',') === FALSE)
		{
			return $this->valid_email_address(trim($str));
		}
		foreach (explode(',', $str) as $email)
		{
			if (trim($email) != '' && $this->valid_email_address(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}
		return TRUE;
	}


	// --------------------------------------------------------------------

	/**
	 * Validate IP Address
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function valid_ip($ip)
	{
		$this->CI->load->helper('fw_ipaddress');
		return validateIPAddress($ip);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	// --------------------------------------------------------------------

	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Integer
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Decimal number
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Greather than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function greater_than($str, $min)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str > $min;
	}

	// --------------------------------------------------------------------

	/**
	 * Less than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function less_than($str, $max)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str < $max;
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural($str)
	{
		return (bool) preg_match( '/^[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural_no_zero($str)
	{
		if ( ! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}

		if ($str == 0)
		{
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Base64
	 *
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_base64($str)
	{
		return (bool) ! preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep data for form
	 *
	 * This function allows HTML to be safely shown in a form.
	 * Special characters are converted.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function prep_for_form($data = '')
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				$data[$key] = $this->prep_for_form($val);
			}

			return $data;
		}
		return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
	}

	// --------------------------------------------------------------------

	/**
	 * Prep URL
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function prep_url($str = '')
	{
		if ($str == 'http://' OR $str == '')
		{
			return '';
		}

		if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
		{
			$str = 'http://'.$str;
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Strip Image Tags
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function strip_image_tags($str)
	{
		return $this->CI->input->strip_image_tags($str);
	}

	// --------------------------------------------------------------------

	/**
	 * XSS Clean
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function xss_clean($str)
	{
		return $this->CI->security->xss_clean($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Convert PHP tags to entities
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function encode_php_tags($str)
	{
		return str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	}

}
// END Form Validation Class

/* End of file FW_DataValidation.php */
/* Location: ./FRAMEWORK/libraries/FW_DataValidation.php */
