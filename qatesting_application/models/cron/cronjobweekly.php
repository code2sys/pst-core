<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CronJobWeekly extends Master_M 
{
	
	function __construct()
  {
    parent::__construct();
  }

	public function runJob()
	{
		$this->cleanMailQueue();
		$this->cleanUnprocessedOrders();
	}

	public function cleanMailQueue()
	{
		// Get the attachments for mail records that are marked for deletion
		$rows = FALSE;
		$this->db->select('queued_email_attach.queuedEmailAttachId,queued_email_attach.attachment');
		$this->db->from('queued_email');
		$this->db->join('queued_email_attach', 'queued_email_attach.queuedEmailId=queued_email.queuedEmailId');
		$this->db->where(array('queued_email.recMarkedForDelete IS NOT NULL' => NULL));
		$query = $this->db->get();
		if ($query->num_rows() > 0)
			$rows = $query->result_array();
		$query->free_result();	
		// Loop through an physically delete each attachment (file) and the attachment record
		if ($rows)
		{
			foreach($rows as $row)
			{
				unlink($row['attachment']);
				$this->db->delete('queued_email_attach', array('queuedEmailAttachId' => $row['queuedEmailAttachId'])); 
			}
		}
		// Delete the mail queue records that are marked for deletion
		$this->db->delete('queued_email', array('recMarkedForDelete IS NOT NULL' => NULL)); 
	}
	
	public function cleanUnprocessedOrders()
	{
  	// Get order # of last batched Admin processed so as not to impact any orders currently being placed.
  	$lastProcOrder = FALSE;
		$this->db->select('id');
		$this->db->from('order');
		$this->db->where(array('process_date IS NOT NULL' => NULL));
		$this->db->limit(1);
		$this->db->order_by('process_date', 'DESC');
		$query = $this->db->get();
		if ($query->num_rows() > 0)
			$lastProcOrder = $query->row_array();
		$query->free_result();	
		
		if(!$lastProcOrder)
		  return FALSE;
		
		$orders = FALSE;  
		$this->db->select('id');
		$this->db->from('order');
		$this->db->where(array('order_date IS NULL' => NULL, 'id <' => $lastProcOrder['id']));
		$query = $this->db->get();
		if ($query->num_rows() > 0)
			$orders = $query->result_array();
		$query->free_result();
		
		if(!$orders)
          return FALSE;

        foreach($orders as $order)
        {
          $this->db->delete('order_product', array('order_id' => $order['id']));
          $this->db->delete('order', array('id' => $order['id']));
        }
	}

}

/* End of file cronjobweekly.php */
/* Location: ./Application/models/cronjobweekly.php */
