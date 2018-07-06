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
        if (!defined('ENABLE_HLSM_FEED') && ENABLE_HLSM_FEED) {
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
            } else {
                print 0;
            }

        } catch(Exception $e) {
            error_log("Error parsing XML: " . $e->getMessage());
            print (0);
        }
    }

}