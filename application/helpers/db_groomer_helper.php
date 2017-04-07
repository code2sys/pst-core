<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('groomTable'))
{
	function groomTable($dataArr, $tableName)
	{
		if(is_array($dataArr))
		{
			
			$query = '';
			for($i = 0; $i < count($dataArr); $i++)
			{
				$strHeader = '';
				$strBody = '';
				$strHeader .= 'INSERT INTO `'.$tableName.'` (';
				$strBody .= 'VALUES (';
				foreach($dataArr[$i] as $colName => $value)
				{
					$strHeader .= ' `'.$colName.'`,';
					$strBody .= ' "'.$value.'",';
				}
				$strHeader = trim($strHeader, ',');
				$strBody = trim($strBody, ',');
				$strHeader .= ' ) ';
				$strBody .= ');  ';
				$query .= $strHeader . $strBody;
			}
			return $query;
		}
		else 
			return FALSE;
  }
}


/* End of file db_groomer_helper.php */
/* Location: APPPATH/helpers/easy_captcha_helper.php */
