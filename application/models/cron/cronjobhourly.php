<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("abstractcronjob.php");

class CronJobHourly extends AbstractCronJob
{
	public function runJob()
	{
        $this->fixVideos();
        $this->fixNullManufacturers();
        $this->fixBrandSlugs();
        $this->fixBrandLongNames();
        $this->fixPendingEBay();
		$this->documentGeneration();
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

            print "Manufacturer missing brand: " . $row["manufacturer_id"] . " " . $manufacturer . "\n";

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
