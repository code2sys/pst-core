<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("abstractcronjob.php");

class CronJobHourly extends AbstractCronJob
{
	public function runJob()
	{
        $this->fixNullManufacturers();
        $this->fixBrandSlugs();
        $this->fixBrandLongNames();
		$this->documentGeneration();
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
