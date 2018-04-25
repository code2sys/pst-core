<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 12/12/17
 * Time: 4:55 PM
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CRSCron_M extends Master_M
{



    protected $motorcycle_attributegroups;
    protected function _getAttributeGroup($motorcycle_id, $attributegroup_name, $attributegroup_number) {
        if (!isset($this->motorcycle_attributegroups)) {
            $this->motorcycle_attributegroups = array();
        }

        $key = $motorcycle_id . "-" . $attributegroup_number;
        if (array_key_exists($key, $this->motorcycle_attributegroups)) {
            return $this->motorcycle_attributegroups[$key];
        }

        // OK, if we're still here, we have to insert it or create it...
        $query = $this->db->query("Select motorcyclespecgroup_id from  motorcyclespecgroup where motorcycle_id = ? and crs_attributegroup_number = ? ", array($motorcycle_id, $attributegroup_number));
        $motorcyclespecgroup_id = 0;

        foreach ($query->result_array() as $row) {
            $motorcyclespecgroup_id = $row["motorcyclespecgroup_id"];
        }

        if ($motorcyclespecgroup_id == 0) {
            $this->db->query("Insert into motorcyclespecgroup (name, ordinal, source, crs_attributegroup_number, motorcycle_id) values (?, ?, 'PST', ?, ?)", array($attributegroup_name, $attributegroup_number, $attributegroup_number, $motorcycle_id));
            $motorcyclespecgroup_id = $this->db->insert_id();
        }

        // OK, set it ..
        $this->motorcycle_attributegroups[$key] = $motorcyclespecgroup_id;
        return $motorcyclespecgroup_id;
    }

    public function refreshCRSData($motorcycle_id = 0, $deep_cleaning = false) {
        global $PSTAPI;
        initializePSTAPI();

        $CI =& get_instance();
        $CI->load->model("CRS_m");
        $where = $motorcycle_id > 0 ? sprintf(" AND motorcycle.id = %d ", $motorcycle_id) : "";

        // OK, this is straightforward, we have to get the motorcycles that have trim IDs, and we have to update the specifications...
        $query = $this->db->query("Select motorcycle.id as motorcycle_id, crs_trim_id, IfNull(max(motorcyclespec.version_number), 0) as version_number from motorcycle left join motorcyclespec on motorcycle.id = motorcyclespec.motorcycle_id where crs_trim_id > 0 $where group by motorcycle.id");

        // we're going to refresh the data for this...
        $matching_motorcycles = $query->result_array();

        foreach ($matching_motorcycles as $m) {
            $motorcycle_id = $m["motorcycle_id"];
            $trim_id = $m["crs_trim_id"];
            $version_number = $deep_cleaning ? 0 : $m["version_number"];

            $existing_attributes = $PSTAPI->motorcyclespec()->getForMotorcycle($motorcycle_id);
            $seen_attributes = array();

            // get the attributes...
            $attributes = $CI->CRS_m->getTrimAttributes($trim_id, $version_number);

            // Now, you have to update them all...
            foreach ($attributes as $a) {
                $motorcyclespecgroup_id = $this->_getAttributeGroup($motorcycle_id, $a["attributegroup_name"], $a["attributegroup_number"]);

                $this->db->query("Insert into motorcyclespec (version_number, value, feature_name, attribute_name, type, external_package_id, motorcycle_id, final_value, source, crs_attribute_id, motorcyclespecgroup_id, ordinal) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) on duplicate key update value = If(source = 'PST', values(value), value), final_value = If(source = 'PST' AND override = 0, values(final_value), final_value), motorcyclespecgroup_id = values(motorcyclespecgroup_id), motorcyclespec_id = last_insert_id(motorcyclespec)", array(
                    $a["version_number"],
                    $a["text_value"],
                    $a["feature_name"],
                    $a["label"],
                    $a["type"],
                    $a["package_id"],
                    $motorcycle_id,
                    $a["text_value"],
                    "PST",
                    $a["attribute_id"],
                    $motorcyclespecgroup_id,
                    $a["attribute_id"] % 10000
                ));

                $seen_attributes[] = $this->db->insert_id();
            }

            if ($deep_cleaning) {
                foreach ($existing_attributes as $ea) {
                    // We only prune out the PST-ones...since they would be from
                    if ($ea->get("source") == "PST" && !in_array($ea->id(), $seen_attributes)) {
                        $ea->remove();
                    }
                }
            }
        }

        // Now, we need to get the photos...
        $query = $this->db->query("Select motorcycle.id as motorcycle_id, crs_trim_id, IfNull(max(motorcycleimage.version_number), 0) as version_number, IfNull(max(motorcycleimage.priority_number), 0) as ordinal from motorcycle left join (select * from motorcycleimage where crs_thumbnail = 0) motorcycleimage on motorcycle.id = motorcycleimage.motorcycle_id where crs_trim_id > 0 $where group by motorcycle.id");

        $matching_motorcycles = $query->result_array();

        foreach ($matching_motorcycles as $m) {
            $motorcycle_id = $m["motorcycle_id"];
            $trim_id = $m["crs_trim_id"];
            $version_number = $deep_cleaning ? 0 : $m["version_number"];
            $ordinal = $m["ordinal"];

            $existing_photos = $PSTAPI->motorcycleimage()->fetch(array("motorcycle_id" => $motorcycle_id, "source" => "PST"));
            $seen_photos = array();
            $known_urls = array();
            if ($deep_cleaning) {
                foreach ($existing_photos as $ep) {
                    $url = $ep->get("image_name");
                    $known_urls[$url] = true;
                }
            }

            // get the photos...
            $photos = $CI->CRS_m->getTrimPhotos($trim_id, $version_number);

            // skip it
            $ordinal++;

            foreach ($photos as $p) {
                $ordinal++;
                $seen_photos[] = $p["photo_url"];
                // this needs to be inserted...
                if ($deep_cleaning) {
                    if (array_key_exists($p["photo_url"], $known_urls)) {
                        continue;
                    }
                }

                $this->db->query("Insert into motorcycleimage (motorcycle_id, image_name, date_added, description, priority_number, external, version_number, source) values (?, ?, now(), ?, ?, 1, ?, 'PST')", array(
                    $motorcycle_id,
                    $p["photo_url"],
                    $p["photo_label"],
                    $ordinal,
                    $p["version_number"]
                ));
            }

            if ($deep_cleaning) {
                foreach ($existing_photos as $ep) {
                    $url = $ep->get("image_name");
                    if (!in_array($url, $seen_photos)) {
                        $ep->remove();
                    }
                }
            }
        }
    }

    /*
     * JLB 12-29-17
     * The idea of this is as follows:
     * If you have a bike A with trim_id T and source PST
     * And you have a bike B with trim_id T and source != PST
     * And both have condition = 1
     * Then remove A because it's extraneous.
     */
    public function removeExtraCRSBikes() {
        $query = $this->db->query("Select A.id from motorcycle A, motorcycle B where A.crs_trim_id > 0 and B.crs_trim_id > 0 and A.crs_trim_id = B.crs_trim_id and A.source = 'PST' and B.source != 'PST' and A.id != B.id and A.condition = 1 and B.condition = 1");
        $ids_to_delete = array();
        foreach ($query->result_array() as $row) {
            $ids_to_delete[] = $row["id"];
        }
 
        if (count($ids_to_delete) > 0) {
            // OK, delete them.
            $this->db->query("Delete from motorcycle where id in (" . implode(",", $ids_to_delete) . ")");
        }
    }

}