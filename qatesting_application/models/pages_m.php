<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages_M extends Master_M
{
  	function __construct()
	{
		parent::__construct();
	}
	
	public function getPages($active = 0, $location = NULL)
	{
		if($active)
			$where = array('active' => 1);
		if($location)
		{
			$this->db->like('location', $location);
			if($location == 'footer')
				$this->db->limit(5);
		}
		
		$records = $this->selectRecords('pages', @$where);
		return $records;
	}
	
	public function getPagesDD()
	{
		$records = FALSE;
		$where = array('active' => 1);
		$records = $this->selectRecords('pages', @$where);
		if($records)
		{
			$dd = array();
		}
		return $records;
	}
	
	public function getPageRec($pageId)
	{
		$where = array('id' => $pageId);
		$record = $this->selectRecord('pages', $where);
		return $record;
	}
	
	public function getPageRecByTag($tag)
	{
		$where = array('tag' => $tag);
		$record = $this->selectRecord('pages', $where);
		return $record;
	}
	
	public function getWidgets()
	{
		$records = FALSE;
		$records = $this->selectRecords('widgets');
		if($records)
		{
			$loop = $records;
			$records = array();
			foreach($loop as $rec)
				$records[$rec['id']] = $rec;
		}
		return $records;
	}
	
	public function editPageActive($post)
	{
		$where = array('delete' => 1);
		$data = array('active' => 0);
		$this->updateRecord('pages', $data, $where, FALSE);
		if(@is_array($post['active']))
		{
			foreach($post['active'] as $id)
			{
				$where = array('id' => $id);
				$data = array('active' => 1);
				$this->updateRecord('pages', $data, $where, FALSE);
			}
		}
	}
	
	private function tag_creating($url) 
	{
	   $url = preg_replace('~[^\\pL0-9_]+~u', '', $url);
	   $url = str_replace(' ', '', $url);
	   $url = trim($url, "-");
	   $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
	   $url = strtolower($url);
	   $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
	   return $url;
	}
	
	public function editPage($post)
	{
		$post['tag'] = $this->tag_creating($post['label']);

		if(!empty($post['widgets']))
			$post['widgets'] = json_encode($post['widgets'], TRUE);
		else
			$post['widgets'] = '';
		if(is_numeric($post['id']))
		{
			$where = array('id' => $post['id']);
			$success = $this->updateRecord('pages', $post, $where, FALSE);
		}
		else
		{	
			$data['delete'] = 1;
			$data['active'] = 0;		
			$success = $this->createRecord('pages', $post, FALSE);
		}
		return $success;
	}
	
	public function getTextBoxes($pageId)
	{
		$where = array('pageId' => $pageId);
		$this->db->order_by('order ASC');
		$records = $this->selectRecords('textbox', $where);
		return $records;
	}
	
	public function updateTextBox($post)
	{
		if(@$post['id'])
		{
			$where = array('id' => $post['id']);
			$success = $this->updateRecord('textbox', $post, $where, FALSE);
		}
		else
		{
			$success = $this->createRecord('textbox', $post, FALSE);
		}
	}
	
	public function widgetCreator($pageId, $pageRec)
	{
		$widgets = json_decode($pageRec['widgets'], TRUE);
		$widgetBlock = '';
		$slider = 0;
		$textbox = 0;
		if(!empty($widgets))
		{
	
			foreach($widgets as $wid)
			{
				switch($wid)
				{
					case '1' :
						++$slider;
						$bannerImages = $this->admin_m->getSliderImages($pageId);
						if(@$bannerImages)
						{ 
							foreach($bannerImages as $img)
							{
								if($img['order'] == $slider)
								{
									$data['sliderImages'][] = $img;
								} 
							}
							if(@$data)
							{
								$widgetBlock .= $this->load->view('widgets/slider_v', $data, TRUE);
								$widgetBlock .='<br />';
							}
						}
						break;
					case '2' :
						++$textbox;
						$textboxes = $this->pages_m->getTextBoxes($pageId);
						if(@$textboxes)
						{
							foreach($textboxes as $text)
							{
								if($text['order'] == $textbox)
								{
									$widgetBlock .= $text['text'];
									$widgetBlock .='<br />';
								}
							}
						}

						break;
				}
			}
		}
		return $widgetBlock;
	}
	
	public function deletePage($pageId)
	{
		$where = array('id' => $pageId);
		return $this->deleteRecord('pages', $where);
	}
}