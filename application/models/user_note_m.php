<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_note_m extends Master_M {
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
	 * addNote
	 * 
	 * Creates a note in user_note table
	 *
	 * @access public
	 * @param int $user_id
	 * @param string $message
	 * @param int $created_by
	 * @return int - record's id if SUCCESS or FALSE if FAILURE
	 */
    public function addNote($user_id, $message, $created_by) {
        $data = array(
            'user_id' => $user_id,
            'note' => $message,
            'created_by' => $created_by
        );
        $newId = $this->createRecord('user_note', $data, FALSE);
		return $newId;
    }

    /**
	 * getNotes
	 * 
	 * Get the notes for the customer with user_id
	 *
	 * @access public
	 * @param int $user_id
	 * @param string $message
	 * @param int $created_by
	 * @return int - record's id if SUCCESS or FALSE if FAILURE
	 */
    public function getNotes($user_id) {
        $query_str = 'select user_note.*, UNIX_TIMESTAMP(user_note.created_at) as created_at_timestamp, author_contact.first_name as author_first_name, author_contact.last_name as author_last_name '.
            'from user_note '.
            'left join user as author on author.id = user_note.created_by '.
            'left join contact as author_contact on author_contact.id = author.billing_id '.
            'where user_note.user_id = ? '.
            'order by user_note.created_at desc';
        $query = $this->db->query($query_str, array($user_id));
        return $query->result_array();
    }

    public function getNote($note_id) {
        $query_str = 'select user_note.*, UNIX_TIMESTAMP(user_note.created_at) as created_at_timestamp, author_contact.first_name as author_first_name, author_contact.last_name as author_last_name '.
            'from user_note '.
            'left join user as author on author.id = user_note.created_by '.
            'left join contact as author_contact on author_contact.id = author.billing_id '.
            'where user_note.id = ? '.
            'order by user_note.created_at desc';
        $query = $this->db->query($query_str, array($note_id));
        $results = $query->result_array();
        if (count($results) > 0) {
            return $results[0];
        }
        return FALSE;
    }
}
