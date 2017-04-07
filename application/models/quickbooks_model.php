<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quickbooks_model extends Master_M
{

	function __construct()
  {
		parent::__construct();
		$this->cleanLog();
  }
  
  private function cleanLog()
  {
    $where = 'quickbooks_ticket_id IS NULL';
    $this->deleteRecord('quickbooks_log', $where);
  }
  
  private function objectsIntoArray($arrObjData, $arrSkipIndices = array())
	{
		$arrData = array();
		
		if(is_object($arrObjData))
			$arrObjData = get_object_vars($arrObjData);
			
		if(is_array($arrObjData))
		{
			foreach($arrObjData as $index => $value)
			{
				if(is_object($value) || is_array($value))
					$value = $this->objectsIntoArray($value, $arrSkipIndices);
				
				if(in_array($index, $arrSkipIndices))
					continue;
					
				$arrData[$index] = $value;
			}
		}
		return $arrData;
	}
	
	private function createPartArr($invRec)
	{
		$partArr = array();
		// To Do
		//$partArr['assembly'];
		
		$partArr['corpPartNumber'] = @$invRec['FullName'];
		$partArr['shortDescription'] = @$invRec['SalesDesc'];
		$partArr['active'] = (@$invRec['IsActive'] == 'true') ? 1 : 0;
		if(is_array(@$invRec['DataExtRet']))
		{
			foreach($invRec['DataExtRet'] as $customField)
			{
				if($customField['DataExtName'] == 'Sellable Part?')
					$partArr['sellable'] = ($customField['DataExtValue'] == 'Yes') ? 1 : 0;
				if($customField['DataExtName'] == 'Repairable?')
					$partArr['repairable'] = ($customField['DataExtValue'] == 'Yes') ? 1 : 0;
				if($customField['DataExtName'] == 'EOL?')
					$partArr['eol'] = ($customField['DataExtValue'] == 'Yes') ? date('Y-m-d H:i:s') : NULL;

			}
		}
		return $partArr;
	}
	
	public function getItemListIdById($id)
	{
	  $record = FALSE;
  	$where = array('id' => $id);
  	$this->db->select('quickbooks_list_id');
  	$record = $this->selectRecord('product', $where);
  	if($record)
  	  return @$record['quickbooks_list_id'];
    else
      return FALSE;
	}	
	
	public function queryItemUpdate($id, $xml)
	{	
		// Turn XML into Array
		$dataArr = array();
		$inventoryRec = '';
		$xmlObj = new SimpleXMLElement($xml);
		if($xmlObj)
			$dataArr = $this->objectsIntoArray($xmlObj);
		if(@$dataArr['QBXMLMsgsRs']['ItemQueryRs']['ItemInventoryAssemblyRet'])
		  $inventoryRec = $dataArr['QBXMLMsgsRs']['ItemQueryRs']['ItemInventoryAssemblyRet'];
		elseif($dataArr['QBXMLMsgsRs']['ItemQueryRs']['ItemInventoryRet'])
		  $inventoryRec = $dataArr['QBXMLMsgsRs']['ItemQueryRs']['ItemInventoryRet'];
		$dataString = json_encode($inventoryRec);
    $this->createRecord('test', array('xmlValue' => $dataString), FALSE);
		
		// Process
		
		
		if(@$inventoryRec)
		{
  		foreach($inventoryRec as $rec)
  		{
    		if(@$rec['DataExtRet'][2]['DataExtName'] == 'Web SKU')
    		{
      		$where = array('sku' => $rec['DataExtRet'][2]['DataExtValue']);
      		$this->updateRecord('product', array('quickbooks_list_id' => $rec['ListID']), $where, FALSE);
    		}
  		}
		}
		
		/*
if(@$inventoryRec['SalesPrice'])
		  return $this->updateRecord('product', array('retail' => $inventoryRec['SalesPrice']), array('id' => $id), FALSE);
		else
		{
  		$dataString = json_encode($inventoryRec);
      $this->createRecord('test', array('xmlValue' => $dataString), FALSE);
		}
*/
	}
	
	public function queryItemBatchUpdate($xml)
	{
		// Turn XML into Array
		$dataArr = array();
		$xmlObj = new SimpleXMLElement($xml);
		if($xmlObj)
			$dataArr = $this->objectsIntoArray($xmlObj);
		return $xmlObj;
	}
}