<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("abstractcronjob.php");

class CronJobHourly extends AbstractCronJob
{
	public function runJob()
	{
        global $PSTAPI;
        initializePSTAPI();

        $vseptprospect_null = $PSTAPI->vseptprospect()->fetch(array("PCHId" => null), true);
        if (count($vseptprospect_null) > 0) {
            print "Error: PCHId missing in these records, indicating they did not go to the server: ";
            foreach ($vseptprospect_null as $r) {
                print "\t" . $r["vseptprospect_id"] . "\n";
            }
        }


	    $this->fixLocalProductCategories();
	    $this->fixLightspeedPartSearch();
        $this->fixVideos();
        $this->fixNullManufacturers();
        $this->fixBrandSlugs();
        $this->fixBrandLongNames();
        $this->fixPendingMDFeed();
        $this->fixPendingLightspeed();
        $this->fixPendingEBay();
		$this->documentGeneration();
	}

	public function fixLocalProductCategories() {
	    $this->db->query("insert into partnumberpartquestion (partnumber_id, partquestion_id, answer, mx) select partpartnumber.partnumber_id, partquestion.partquestion_id, partquestionanswer.answer, null from part join partpartnumber using (part_id)join partquestion using (part_id) join partquestionanswer using (partquestion_id) left join partnumberpartquestion on partpartnumber.partnumber_id = partnumberpartquestion.partnumber_id and partquestion.partquestion_id = partnumberpartquestion.partquestion_id and partquestionanswer.answer = partnumberpartquestion.answer where part.mx = 0 and partquestion.productquestion > 0 and partnumberpartquestion.partnumberpartquestion_id is null");
	    $this->db->query("insert into productquestionpartnumber (partnumber_id, productquestion_id, answer, mx) select partpartnumber.partnumber_id, productquestion.productquestion_id, productquestionanswer.answer, null from part join partpartnumber using (part_id)join productquestion using (part_id) join productquestionanswer using (productquestion_id) left join productquestionpartnumber on partpartnumber.partnumber_id = productquestionpartnumber.partnumber_id and productquestion.productquestion_id = productquestionpartnumber.productquestion_id and productquestionanswer.answer = productquestionpartnumber.answer where part.mx = 0  and productquestionpartnumber.productquestionpartnumber_id is null");
    }

	public function fixLightspeedPartSearch() {
        $this->db->query("update partcategory join partpartnumber using (part_id) join partvariation using (partnumber_id)  set partvariation.from_lightspeed = 0;");
        $this->db->query("update partvariation join lightspeedpart using (partvariation_id) left join partpartnumber using (partnumber_id) left join partcategory using (part_id) set partvariation.from_lightspeed = 1 where partcategory.partcategory_id is null;");
    }

	public function fixVideos() {
        $tables = array("brand_video","category_video", "motorcycle_video", "part_video", "top_videos");
        foreach ($tables as $table) {
            $query = $this->db->query("select * from $table where video_url like '%&%'");
            foreach ($query->result_array() as $row) {
                $url = $row["video_url"];
                $id = $row["id"];
                if (FALSE !== ($pos = strrpos($url, "&"))){
                    $url = substr($url, 0, $pos);
                }
                $this->db->query("Update $table set video_url = ? where id = ? limit 1", array($url, $id));
            }
        }
    }

    public function fixPendingMDFeed() {
        if (defined('ENABLE_MD_FEED') && ENABLE_MD_FEED) {
            $query = $this->db->query("select * from mdfeed_feed_log where run_by = 'admin' and status = 0");
            $results = $query->result_array();
            if (count($results) > 0) {
                // OK, we should attempt to pull the major unit lightspeed parts..
                $this->db->query("Update mdfeed_feed_log set status = 1, processing_start = now() where run_by = 'admin' and status = 0");
                $error_string = "";
                try {
                    $this->load->model("Mdfeed_m");
                    $this->Mdfeed_m->get_major_units(); // that should fetch all those things, great.
                } catch (Exception $e) {
                    $error_string = $e->getMessage();
                    if ($e->getMessage() != "MD Feed credentials not found.") {
                        print "MD Feed error: " . $e->getMessage() . "\n";
                    }
                }

                $this->db->query("Update mdfeed_feed_log set status = 2, processing_end = now(), error_string = ? where run_by = 'admin' and status = 1", array($error_string));
            }
        }
    }

    public function fixPendingLightspeed() {
        // JLB 12-18-17
        // Lightspeed, if you have it...
        if (defined('ENABLE_LIGHTSPEED') && ENABLE_LIGHTSPEED) {
            $query = $this->db->query("select * from lightspeed_feed_log where run_by = 'admin' and status = 0");
            $results = $query->result_array();
            if (count($results) > 0) {
                // OK, we should attempt to pull the major unit lightspeed parts..
                $this->db->query("Update lightspeed_feed_log set status = 1, processing_start = now() where run_by = 'admin' and status = 0");
                $error_string = "";
                try {
                    $this->load->model("Lightspeed_m");
                    $this->Lightspeed_m->get_major_units(); // that should fetch all those things, great.
                    // $this->Lightspeed_m->get_parts(); // that should fetch all those things, great. // The parts feed can be huge; let's delay it to once per day.
                } catch (Exception $e) {
                    $error_string = $e->getMessage();
                    if ($e->getMessage() != "Lightspeed credentials not found.") {
                        print "Lightspeed error: " . $e->getMessage() . "\n";
                    }
                }

                $this->db->query("Update lightspeed_feed_log set status = 2, processing_end = now(), error_string = ? where run_by = 'admin' and status = 1", array($error_string));
            }
        }
    }

	public function fixPendingEBay() {
        $this->load->model("ebay_m");

        $error_message = "";
        if ($this->ebay_m->checkForFatalErrors($error_message)) {
            // JLB 09-06-17 Only do this if there aren't any immediate fatal errors to address. Otherwise, this is stupid.

            $query = $this->db->query("select * from ebay_feed_log where run_by = 'admin' and status = 0");
            $results = $query->result_array();

            if (count($results) > 0) {
                // do the eBay thing...
                $this->ebay_m->generateEbayFeed(0, 1);
                foreach ($results as $row) {
                    $this->db->query("Update ebay_feed_log set status = 1 where id = ?", $row["id"]);
                }
            }
        }
    }

	public function fixBrandLongNames() {
        $this->db->query("Update brand set long_name = name where (long_name = '' or long_name is null) and (name != '' and name is not null)");
    }

	public function fixBrandSlugs() {
        $query = $this->db->query("Select * from brand where slug = '' or slug is null");

        foreach ($query->result_array() as $row) {
            print "Brand missing slug: " . $row["brand_id"] . " " . $row["name"] . "\n";
            $this->db->query("Update brand set slug = ? where brand_id = ? limit 1", array($this->Portalmodel->makeBrandSlug($row["name"]), $row["brand_id"]));
        }
    }

	public function fixNullManufacturers() {
        $this->load->model("Portalmodel");

        // This first thing is going to check for manufacturers without brands.
        $query = $this->db->query("Select * from manufacturer where brand_id is null");

        foreach ($query->result_array() as $row) {
            $manufacturer = $row["name"];

            // you have to make a brand...
            $this->db->query("Insert into brand (name, long_name, slug, title, active, mx, meta_tag) values (?, ?, ?, ?, 1, ?, ?)", array($manufacturer, $manufacturer, $this->Portalmodel->makeBrandSlug($manufacturer), $manufacturer, $row["ext_manufacturer_id"] > 0 ? 1 : 0, $manufacturer));
            $brand_id = $this->db->insert_id();

            $this->db->query("Update manufacturer set brand_id = ? where manufacturer_id = ?", array($brand_id, $row["manufacturer_id"]));
        }
    }
	
	private function documentGeneration()
	{
		$this->load->model('reporting_m');
		$dataArr = $this->reporting_m->getAppeagleAmazonXML();
		$filename = $this->config->item('upload_path')."/Appeagle-Export.txt";
		
		$flag = false;
		foreach($dataArr as $row) 
		{
			if(!$flag) 
			{
			  // display field/column names as first row
			  $data = implode("\t", array_keys($row)) . "\r\n";
			  $flag = true;
			}
			array_walk($row, array($this, 'cleanData'));
			$data .= implode("\t", array_values($row)) . "\r\n";
			
		}
		$fp = fopen($filename, 'w');
		fwrite($fp, $data);
		fclose($fp);
	}

}

/* End of file cronjobhourly.php */
/* Location: ./Application/models/cronjobhourly.php */
