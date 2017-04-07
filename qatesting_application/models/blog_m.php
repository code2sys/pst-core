<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blog_M extends Master_M
{

	public function getBlogs()
	{
	  $records = FALSE;
  	$records = $this->selectRecords('config', array('key' => 'blogImages'));
  	if($records)
  	{
    	foreach($records as &$rec)
    	{
    	  $pieces = explode(".", $rec['value']);
    	  $file = $this->selectRecord('config',  array('key' => $pieces[0].'.text'));
        $rec['text'] = $file['value'];
        $file = $this->selectRecord('config',  array('key' => $pieces[0].'.title'));
        $rec['title'] = $file['value'];
        $rec['comments'] = $this->selectRecords('config',  array('key' => $pieces[0].'.comment'));
      }
  	}
  	return $records;
	}
	
	public function createComment($id, $post)
	{
  	$rec = $this->getBlogById($id);
  	if($rec)
  	{
  	  $pieces = explode(".", $rec['value']);
  	  $this->createRecord('config', array('key' => $pieces[0].'.comment', 'value' => $post['name'] . ' : ' . $post['message']), FALSE);
  	}
	}
	
  public function getBlogById($id)
	{
  	$record = FALSE;
  	$where = array('id' => $id);
  	$record = $this->selectRecord('config', $where);
  	return $record;
	}
	
  public function removeBlog($id)
	{ 
    $record = $this->getBlogById($id);
    $pieces = explode(".", $record['value']);
    $where = array('id' => $id);
    $this->deleteRecord('config', $where);
    $where = array('key' => $pieces[0].'.text');
    $this->deleteRecord('config', $where);
    $where = array('key' => $pieces[0].'.title');
    $this->deleteRecord('config', $where);
    return TRUE;
	}
	
	public function editBlog($newImageName, $key, $existingImageName, $title, $text)
	{     
    $this->updateRecord('config', array('value' => $newImageName), array('value' => $existingImageName), FALSE);
    $this->updateRecord('config', array('value' => $text), array('key' => $key.'.text'), FALSE);
    $this->updateRecord('config', array('value' => $title), array('key' => $key.'.title'), FALSE);
	}
	
	public function editBlogText($key, $title, $text)
	{     
    $this->updateRecord('config', array('value' => $text), array('key' => $key.'.text'), FALSE);
    $this->updateRecord('config', array('value' => $title), array('key' => $key.'.title'), FALSE);
	}
	
	public function createBlog($imageName, $key, $title, $text)
	{ 
    $this->createRecord('config', array('key' => 'blogImages', 'value' => $imageName), FALSE);
    $this->createRecord('config', array('key' => $key.'.title', 'value' => $title), FALSE);
    $this->createRecord('config', array('key' => $key.'.text', 'value' => $text), FALSE);
	}
	
}