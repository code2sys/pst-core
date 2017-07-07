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
				$this->db->limit(FOOTER_PAGE_LIMIT);
		}
		$this->db->order_by("delete", "asc"); 
		
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
		if($post['id'] == 12) {
			$post['tag'] = 'Motorcycle_Gear_Brands';
		} else {
			$post['tag'] = $this->tag_creating($post['label']);
		}

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
        // JLB 07-07-17
        // JLB - I am going to short-circuit this into a simpler thing to implement EXACTLY what Brandt said, as I think he said it,
        // because, ultimately, this widgets array, seems pointless.

        //
        $widgetBlock = '';

        // videos
        $topVideo = $this->getTopVideos($pageId);
        if (!is_null($topVideo) && is_array($topVideo) && count($topVideo) > 0) {
            $mainVideo = $mainTitle = '';
            foreach ($topVideo as $key => $val) {
                if ($val['ordering'] == 1) {
                    $mainVideo = $val['video_url'];
                    $mainTitle = $val['title'];
                    unset($topVideo[$key]);
                    break;
                }
            }
            // Note that below there is a category video that is, well, undefined.
            $data1['mainVideo'] = $mainVideo;
            $data1['mainTitle'] = $mainTitle;
            $data1['video'] = $topVideo;
            $widgetBlock .= $this->load->view('widgets/videos_v', $data1, TRUE);
        }

        // slider
        $bannerImages = $this->admin_m->getSliderImages($pageId);
        $data = array();
        if(!is_null($bannerImages) && is_array($bannerImages) && count($bannerImages) > 0)
        {
            // There was a significant problem with the ordinals.
            $correct_ordinal = 0;
            foreach($bannerImages as $img)
            {
                $correct_ordinal++;
                $data['sliderImages'][$correct_ordinal] = $img;
            }
            $widgetBlock .= $this->load->view('widgets/slider_v', $data, TRUE);
            $widgetBlock .='<br />';
        }

        // textblocks
        $textboxes = $this->pages_m->getTextBoxes($pageId);
        if(!is_null($textboxes) && is_array($textboxes) && count($textboxes) > 0)
        {
            usort($textboxes, function($a, $b) {
               return ($a["order"] < $b["order"] ? -1 : ($a["order"] > $b["order"] ? 1 : 0));
            });

            foreach($textboxes as $text)
            {
                $widgetBlock .= '<div class="content_section">';
                $widgetBlock .= '<h3>'.$text['text'].'</h3>';
                $widgetBlock .= '</div>';
            }
        }

        return $widgetBlock;

		$widgets = json_decode($pageRec['widgets'], TRUE);

  		$allWidgets = $this->getWidgets();
		$widgetBlock = '';
		$slider = 0;
		$textbox = 0;

        // JLB 07-07-17
        // I am trying to make sense of this.

        // So, this sorting part - this is sorting by Our Top Videos, Slider, and then textblocks.
                $sortingArr = array(3,1,2);
        
                $result = array(); // result array
                foreach($sortingArr as $val){ // loop
                    if(array_search($val, $widgets)) {
                        $result[array_search($val, $widgets)] = $val; // adding values
                    }
                }
                $widgets = $result;

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
								//if($img['order'] == $slider)
								//{
									$data['sliderImages'][$img['order']] = $img;
								//} 
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
                                                                if ($text['order'] == $textbox && $text['text'] != '') {
                                                                    $widgetBlock .= '<div class="content_section">';
                                                                    $widgetBlock .= '<h3>'.$text['text'].'</h3>';
                                                                    $widgetBlock .= '</div>';
                                                                    //$widgetBlock .= '<br />';
								}
							}
						}

						break;
                                                case '3' :
                                                    $topVideo = $this->getTopVideos($pageId);
                                                    $mainVideo = $mainTitle = '';
                                                    foreach ($topVideo as $key => $val) {
                                                        if ($val['ordering'] == 1) {
                                                            $mainVideo = $val['video_url'];
                                                            $mainTitle = $val['title'];
                                                            unset($topVideo[$key]);
                                                            break;
                                                            }
                                                    }
                                                    if ($mainVideo == '') {
                                                        $mainVideo = $categoryVideo[0];
                                                        unset($topVideo[0]);
                                                    }
                                                    $data1['mainVideo'] = $mainVideo;
                                                    $data1['mainTitle'] = $mainTitle;
                                                    $data1['video'] = $topVideo;
                                                    $widgetBlock .= $this->load->view('widgets/videos_v', $data1, TRUE);
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
	
	
    public function getSliderImagesForFront($pageId) {
        $where = array('pageId' => $pageId);
        $this->db->order_by('order ASC');
        $records = $this->selectRecords('slider', $where);
        return $records;
    }
	
	public function getServiceEmail() {
		$this->db->select('service_email');
		$where = array('id' => '1');
        $record = $this->selectRecord('contact', $where);
		return $record['service_email'];
	}
	public function getFinanceEmail() {
		$this->db->select('finance_email');
		$where = array('id' => '1');
        $record = $this->selectRecord('contact', $where);
		return $record['finance_email'];
	}

        public function getTopVideos($pageId) {
            $this->db->where('page_id', $pageId);
            $records = $this->selectRecords('top_videos');
            return $records;
}

    public function updateTopVideos($id, $arr) {
        $this->db->delete('top_videos', array('page_id' => $id));
        if (!empty($arr)) {
            $this->db->insert_batch('top_videos', $arr);
        }
    }
}