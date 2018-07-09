<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/6/18
 * Time: 2:12 PM
 */

class Hlsmfeed extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("admin_m");
        $store_name = $this->admin_m->getAdminShippingProfile();
        $partsfinder_link = $store_name["partsfinder_link"];
        if ($partsfinder_link  == "") {
            redirect("/");
        }
    }

    public function receive() {
        $input = file_get_contents("php://input");

        try {
            global $PSTAPI;
            initializePSTAPI();
            $obj = $PSTAPI->hlsmxmlfeed()->add(array(
                "raw_xml" => $input
            ));

            // now, attempt to convert it...
            if ($obj->convertFromRaw()) {
                print $obj->id();

                // Convert them...
                $rows = $PSTAPI->hlsmxmlfeedrow()->fetch(array("hlsmxmlfeed_id" => $obj->id()));
                foreach ($rows as $row) {
                    $row->convertToPartVariation();
                }
            } else {
                print 0;
            }

        } catch(Exception $e) {
            error_log("Error parsing XML: " . $e->getMessage());
            print (0);
        }
    }

}