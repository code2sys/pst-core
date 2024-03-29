<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* NOTE!!!  Need to make sure to turn on if checkbox is there and off it is not */

use \DTS\eBaySDK\Sdk;
use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\FileTransfer;
use \DTS\eBaySDK\BulkDataExchange;
use \DTS\eBaySDK\MerchantData;
use \DTS\eBaySDK\BulkDataExchange\Services;
use \DTS\eBaySDK\BulkDataExchange\Types;

class Ebay_M extends Master_M {

    public $headers = array();
    public $cred = array();
    public $serverUrl = 'https://api.ebay.com/ws/api.dll';
    private $compatibility_level = 849;
    private $call;
    private $siteID = 0;
    private $all = array();
    private $store_url = '';
    private $boundary;
    private $related_images = array();
    private $check_header_type_image = false; //check for header type
    public $call_from_cron = false;
    public $item_id;
    public $current_product_id;
    private $product_data = array();
    protected $_dieSilentlyIfBad;
    protected $store_zip_code;
    protected $store_name;
    public $debug;

    public function getFeedResults() {
        $query = $this->db->query("select sku, title, error, error_string, long_error_string from ebay_feed_item;");
        return $query->result_array();
    }

    public function getFeedCounts() {
        $query = $this->db->query("select count(*) as the_count, error, error_string from ebay_feed_item group by error_string order by error, count(*) desc");
        return $query->result_array();
    }

    public function __construct() {
        parent::__construct();
        $this->_dieSilentlyIfBad = false;
        $debug = false;

	/*
	 * JLB 08-23-17
		We have to go get the environment name
		And we have to set the serveRUrl look right.
	 */
        $CI =& get_instance();
        $CI->load->model("admin_m");
        $store_name = $CI->admin_m->getAdminShippingProfile();
        $this->store_name = $store_name;
        $this->store_zip_code = $store_name["zip"];
        $environment = $store_name["environment"];


        $this->serverUrl = ($environment == "production") ? 'https://api.ebay.com/ws/api.dll' : 'https://api.sandbox.ebay.com/ws/api.dll';
    }

    function pr($d) {
        echo "<pre>";
        print_r($d);
        echo "</pre>";
    }

    /**************************************\
     *
     * These functions have to do with the ebay feed item table. This is a table for keeping track of a run.
     *
     * ************************************/

    protected $current_run_ordinal;

    protected function beginRun() {
        $this->current_run_ordinal = 0;
        $this->db->query("Delete from ebay_feed_item");
    }

    protected function addRow($sku, $title, $fragment) {
        $this->db->query("Insert into ebay_feed_item (sku, title, ordinal, fragment) values (?, ?, ?, ?)", array($sku, $title, $this->current_run_ordinal, $fragment));
        $this->current_run_ordinal++;
    }

    protected function beginResults() {
        $this->current_run_ordinal = 0;
    }

    protected function markDuplicate()
    {
        $this->db->query("Update ebay_feed_item set duplicate = 1 where ordinal = ? limit 1", array($this->current_run_ordinal));
        $this->current_run_ordinal++;
    }

    protected function markOK() {
        $this->current_run_ordinal++;
    }

    protected function markError($error_message) {
        $this->db->query("Update ebay_feed_item set error = 1, error_string = ? where ordinal = ? limit 1", array($error_message, $this->current_run_ordinal));
        $this->current_run_ordinal++;
    }

    protected function recordError($CorrelationID, $ShortMessage, $LongMessage) {
        if ($ShortMessage == "Listing violates the Duplicate Listing policy.") {
            $this->db->query("Update ebay_feed_item set duplicate = 1 where sku = ?", array($CorrelationID));
        } else {
            // it is an error...
            $this->db->query("Update ebay_feed_item set error = 1, error_string = ?, long_error_string = ? where sku = ?", array($ShortMessage, $LongMessage, $CorrelationID));
        }
    }

    /*********END*********/

    public function generateEbayFeed($products_count, $upload_to_ebay = false, $debug = false) {
        //$products = $this->ebaylistings_no_variation(0, $products_count, 1);
        $temp_file = STORE_DIRECTORY . "/ebay_feed.xml"; // tempnam("/tmp", "ebay_");
        $handle = fopen($temp_file, "w");

        if ($debug) {
            print "Output started to ebay_feed.xml\n";
        }

        $this->startXML($handle);

        if ($debug) {
            print "Output finished to ebay_feed.xml\n";
        }

        // now, we must get products...
		$newArray = [];
		$offset = 0;
        $limit = $products_count == 0 ? 1500 : $products_count;

        if ($debug) {
            print "Begin Run \n";
        }
        $this->beginRun();

        do {
            $products = $this->ebayListings($offset, $limit, 1, $upload_to_ebay, $debug);
            if ($debug) {
                print count($products) . " Returned by ebayListings function \n";
            }

            if (count($products) > 0) {

                $ebay_format_data = $this->convertToEbayFormat($products);
                $ebay_format_data_new = $ebay_format_data;
                $this->addIncrementalParts($handle, $ebay_format_data_new);
                $offset += $limit;
                if ($debug) {
                    print "Offset is now: " . $offset . "\n";
                }
            }

        } while(count($products) > 0);

        if ($debug) {
            print "Closing XML \n";
        }
        $this->closeXML($handle);
        if ($debug) {
            print "Cleaning XML \n";
        }
        $this->cleanXML($temp_file, 1, $debug);

        if ($debug) {
            print "All done\n";
        }
    }


    protected function startXML(&$handle) {
        $count = 1;
        $store_url = base_url();
        $this->_setHeader("AddItems", FALSE);
        $uploadXML = '<?xml version="1.0" encoding="utf-8"?>';
//		$uploadXML .= '<BulkDataExchangeRequests xmlns="urn:ebay:apis:eBLBaseComponents">';

        $uploadXML .= '<BulkDataExchangeRequests>';
        $uploadXML .= '<Header>';
        $uploadXML .= '<SiteID>100</SiteID>';
        $uploadXML .= '<Version>987</Version>';
        $uploadXML .= '</Header>';
        fputs($handle, $uploadXML);
    }

    protected function cleanXML($source_filename, $store_feed = false, $debug = false) {
        $uploadXML = file_get_contents($source_filename);
        if ($debug) {
            print "CleanXML cleaning started; upload XML length: " . strlen($uploadXML);
        }
        $unicode = ["\x01", "\x00", "\x02"];
        $uploadXML = str_replace($unicode, "", str_replace("&B", "&amp;B", str_replace("&G", "&amp;G", $uploadXML)));

        if ($debug) {
            print "CleanXML cleaning done; upload XML length: " . strlen($uploadXML);
        }

        if ($store_feed == true) {
            $file_path = STORE_DIRECTORY . '/ebayFeeds/ebayfeed_un.xml';
            if (file_exists($file_path)) {
                unlink($file_path);
            }
//            $myfile = fopen($file_path, "w");
            file_put_contents($file_path, $uploadXML, LOCK_EX);

            if ($debug) {
                print "Put contents in ebayfeed_un.xml\n";
            }

            $xml = file_get_contents($file_path, LOCK_EX);
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = FALSE;
            $doc->loadXML($xml);
            $doc->formatOutput = TRUE;
//Save XML as a file
            $file = STORE_DIRECTORY . '/ebayFeeds/ebayfeed.xml';
            if (file_exists($file)) {
                unlink($file);
            }
            $doc->save($file);
        }

        if ($debug) {
            print "About to call sendBulkXML\n";
        }

        //$this->pr($uploadXML);
        $this->sendBulkXML($uploadXML, "AddFixedPriceItem", $debug);
    }

    protected function closeXML(&$handle) {
        $uploadXML = '</BulkDataExchangeRequests>';
        fputs($handle, $uploadXML);
    }

    protected function addIncrementalParts(&$handle, $products) {
        $store_url = base_url();
        $uploadXML = "";
        foreach ($products as $product) {
            $uploadXML_start_length = strlen($uploadXML);
//            $string = utf8_encode($product['product']['*Description']);

            $string = $this->xmlEscape($product['product']['*Description']);
            $quantity = $this->get_quantity();
            //die();
            $string = substr($string, 0, 500000);
            $UUID = md5(uniqid(rand(), true));

            $uploadXML .= '<AddFixedPriceItemRequest xmlns = "urn:ebay:apis:eBLBaseComponents">';
            $uploadXML .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $uploadXML .= '<WarningLevel>High</WarningLevel>';
            $uploadXML .= '<Version>987</Version>';
            $uploadXML .= '<MessageID>' . $product['product']['C:Manufacturer Part Number'] . '</MessageID>';
            $uploadXML .= '<Item>';

            $uploadXML .= '<SKU>' . $product['product']['C:Manufacturer Part Number'] . '</SKU>';
            $uploadXML .= '<CategoryMappingAllowed>true</CategoryMappingAllowed>';
            $uploadXML .= '<Country>US</Country>';
            $uploadXML .= '<location>US</location>';
            $uploadXML .= '<Currency>USD</Currency>';


            $uploadXML .= '<ConditionID>' . $product['product']['*ConditionID'] . '</ConditionID>';
            if ($string != "") {
                $late_description = false;
                $uploadXML .= '<Description>' . $string . '</Description>';
            } else {
                $late_description = true;
            }
            $uploadXML .= '<DispatchTimeMax>' . $product['product']['*DispatchTimeMax'] . '</DispatchTimeMax>';
            $uploadXML .= '<ListingDuration>' . $product['product']['*Duration'] . '</ListingDuration>';
            $uploadXML .= '<ListingType>FixedPriceItem</ListingType>';
            $uploadXML .= '<PaymentMethods>PayPal</PaymentMethods>';
            $paypal_email = $this->get_paypalemail();
            $uploadXML .= '<PayPalEmailAddress>' . $paypal_email . '</PayPalEmailAddress>';

            $uploadXML .= '<PictureDetails>';


            if(isset($product['product']['item_id'])) {
                // JLB 09-06-17
                // https://forums.developer.ebay.com/questions/8387/why-appears-ebay-error-10115-you-entered-more-pict.html
                // There is a limit of 12 pictures.
                $pic_sql = "SELECT * from partimage where partimage.part_id = " . $product['product']['item_id'] . " LIMIT 12";
                $query = $this->db->query($pic_sql);
                $pics = $query->result_array();
                if (is_array($pics)) {
                    foreach($pics as $pic) {

                        $uploadXML .= '<PictureURL>http://' . WEBSITE_HOSTNAME . '/productimages/' . $this->xmlEscape($pic['path']) . '</PictureURL>';
                    }

                }
            } else if(isset($product['product']['PicURL'])) {
                $uploadXML .= '<PictureURL>' . $this->xmlEscape($product['product']['PicURL']) . '</PictureURL>';
            }

            $uploadXML .= '</PictureDetails>';

            $uploadXML .= '<PostalCode>' . $product['product']['PostalCode'] . '</PostalCode>';
            $uploadXML .= '<PrimaryCategory>';

            $uploadXML .= '<CategoryID>' . $product['product']['EbayCategory'] . '</CategoryID>';
            //$uploadXML .= '<CategoryID>63850</CategoryID>';

            $uploadXML .= '</PrimaryCategory>';
            $uploadXML .= '<ReturnPolicy>';
            $uploadXML .= '<ReturnsAcceptedOption>' . $this->xmlEscape($product['product']['*ReturnsAcceptedOption']) . '</ReturnsAcceptedOption>';
            $uploadXML .= '<RefundOption>MoneyBack</RefundOption>';
            $uploadXML .= '<ReturnsWithinOption>' . $this->xmlEscape($product['product']['ReturnsWithinOption']) . '</ReturnsWithinOption>';
            $uploadXML .= '<Description></Description>';
            $uploadXML .= '<ShippingCostPaidByOption>' . $this->xmlEscape($product['product']['ShippingCostPaidByOption']) . '</ShippingCostPaidByOption>';
            $uploadXML .= '</ReturnPolicy>';
//            $shipping_first_key = $product['product']['shipping_options'];
//            $uploadXML .= '<ShippingDetails>';
//            $uploadXML .= '<ShippingType>';
//            $uploadXML .= 'Flat';
//            $uploadXML .= '</ShippingType>';
//
//            $uploadXML .= '<ShippingServiceOptions>';
//            $uploadXML .= '<ShippingServicePriority>1</ShippingServicePriority>';
//            $uploadXML .= '<ShippingService>UPSGround</ShippingService>';
//            $shipping_cost = $this->get_shipping_cost();
//            $uploadXML .= '<ShippingServiceCost>12.50</ShippingServiceCost>';
//            $uploadXML .= '<ShippingServiceAdditionalCost>0.00</ShippingServiceAdditionalCost>';
//            $uploadXML .= '</ShippingServiceOptions>';
//            $uploadXML .= '</ShippingDetails>';
            $check_compatibility = FALSE;
            $itemspecifics_XML = "<ItemSpecifics>";
            $itemspecifics_XML .= "<NameValueList>";
            $itemspecifics_XML .= "<Name>Brand</Name>";
            $itemspecifics_XML .= "<Value>".$product['product']['C:Brand']."</Value>";
            $itemspecifics_XML .= "</NameValueList>";


            if (array_key_exists("product_variation", $product) && is_array($product["product_variation"]) && count($product['product_variation']) > 0) {
                $product['product_variation'] = array_unique($product['product_variation'], SORT_REGULAR);
//                $this->pr($product['product_variation']);
                $check_combo = FALSE;
                $check_combo_again = FALSE;
                $check_compatibility = FALSE;
                $variation_XML = '';
                $compatibility_XML = '';
                $variation_XML .= '<Variations>';
                $variation_XML .= '<VariationSpecificsSet>';
                $compatibility_XML .= '<ItemCompatibilityList>';

                $done = 0;
                $years = array();
                foreach ($product['product_variation'] as $combination) {
//                    $combination = array_unique($combination);
//                    $this->pr($variation_key);
                    if (trim(strtolower($combination['Relationship'])) == 'variation') {
                        $variation = explode("=", $combination['RelationshipDetails']);
                        // JLB 09-07-17
                        // This code utterly baffles me. Why only one question?
                        if ($done < 1) {
                            $variation_XML .= '<NameValueList>';
                            $variation_XML .= '<Name>';
                            $variation_XML .= $variation['0'];
                            $variation_XML .= '</Name>';
                        }
                        $variation_XML .= '<Value>';
                        $variation_XML .= $variation['1'];
                        $variation_XML .= '</Value>';
                        $done++;
                    } elseif (trim(strtolower($combination['Relationship'])) == 'compatibility') {
                        $check_compatibility = TRUE;
//                        $this->pr($combination['RelationshipDetails']);
//                        die("*");
                        $compatibilities = explode('|', $combination['RelationshipDetails']);
                        $compatibility_XML .= '<Compatibility>';
                        foreach ($compatibilities as $compatibility_key => $compatibility) {
                            $name_values = explode('=', $compatibility);
                            $product['product']['*StartPrice'] = $combination['*StartPrice'];
                            if (trim(strtolower($name_values[0])) == 'year') {
                                $years[] = $name_values[1];
                            }
                            $compatibility_XML .= "<NameValueList>";
                            $compatibility_XML .= "<Name>$name_values[0]</Name>";
                            $compatibility_XML .= "<Value>$name_values[1]</Value>";
                            $compatibility_XML .= "</NameValueList>";
                        }
                        $compatibility_XML .= '</Compatibility>';

                        if(isset($combination['question'])&&isset($combination['answer'])) {

                            $itemspecifics_XML .= "<NameValueList>";
                            $itemspecifics_XML .= "<Name>".$combination['question']."</Name>";
                            $itemspecifics_XML .= "<Value>".$combination['answer']."</Value>";
                            $itemspecifics_XML .= "</NameValueList>";

                        }


                    } else {
//                        $this->pr($product['product_options']);
//                        die("1234");


                        if (!$check_combo) {
                            foreach ($product['product_options'] as $option_type => $option_value_array) {
                                $variation_XML .= '<NameValueList>';
                                $variation_XML .= '<Name>';
                                $variation_XML .= $option_type;
                                $variation_XML .= '</Name>';
                                foreach ($option_value_array as $option_value) {
                                    $variation_XML .= '<Value>';
                                    $variation_XML .= $option_value;
                                    $variation_XML .= '</Value>';
                                }
                                $variation_XML .= '</NameValueList>';
                            }
                            $check_combo = TRUE;
//                        $this->pr($variation_XML);
//                        die("testing xml");
                        }
                    }
                }
                if (!$check_combo) {
                    $variation_XML .= '</NameValueList>';
                }
                $variation_XML .= '</VariationSpecificsSet>';

                foreach ($product['product_variation'] as $combination) {
                    if (trim(strtolower($combination['Relationship'])) == 'variation') {
                        $variations = explode("=", $combination['RelationshipDetails']);
                        $variation_XML .= '<Variation>';
                        $product_price = $combination['*StartPrice'];
                        $variation_XML .='<StartPrice>' . $combination['*StartPrice'] . '</StartPrice>';

                        $variation_XML .='<Quantity>' . min($combination['*Quantity'], $quantity) . '</Quantity>';

                        $variation_XML .= '<VariationSpecifics>';
                        $variation_XML .= '<NameValueList>';

                        // JLB - why does this check the exact same condition? Does it mutate?
//                        if (trim(strtolower($combination['Relationship'])) == 'variation') {
                        $variation_XML .= '<Name>' . $variations['0'] . '</Name>';
                        $variation_XML .= '<Value>' . $variations['1'] . '</Value>';
//                        }

                        $variation_XML .= '</NameValueList>';
                        $variation_XML .= '</VariationSpecifics>';
                        $variation_XML .= '</Variation>';
                    } elseif (trim(strtolower($combination['Relationship'])) == 'combo') {
                        //$this->pr($product['product_options']);
                        //die('test');
                        if (!$check_combo_again) {

                            if($product['product_options']['GLOVE']) {

                                $variation_XML .= '<NameValueList>';
                                $variation_XML .= '<Name>GLOVE</Name>';

                                foreach ($product['product_options']['GLOVE'] as $option_value_glove_array) {
                                    $variation_XML .= '<Value>' . $option_value_glove_array . '</Value>';
                                }

                                $variation_XML .= '</NameValueList>';

                            }

                            if($product['product_options']['JERSEY']) {

                                $variation_XML .= '<NameValueList>';
                                $variation_XML .= '<Name>JERSEY</Name>';
                                foreach ($product['product_options']['JERSEY'] as $option_value_jersey_array) {
                                    $variation_XML .= '<Value>' . $option_value_jersey_array . '</Value>';
                                }
                                $variation_XML .= '</NameValueList>';
                            }

                            if($product['product_options']['PANT']) {

                                $variation_XML .= '<NameValueList>';
                                $variation_XML .= '<Name>PANT</Name>';
                                foreach ($product['product_options']['PANT'] as $option_value_pant_array) {
                                    $variation_XML .= '<Value>' . $option_value_pant_array . '</Value>';
                                }
                                $variation_XML .= '</NameValueList>';
                            }
                            $check_combo_again = TRUE;
                        }
                    } else {
                        // JLB 09-07-17
                        // We're going to stamp things out here to match the other one, or else we might have no price.
                        $variations = explode("=", $combination['RelationshipDetails']);
                        $variation_XML .= '<Variation>';
                        $product_price = $combination['*StartPrice'];
                        $variation_XML .='<StartPrice>' . $combination['*StartPrice'] . '</StartPrice>';

                        $variation_XML .='<Quantity>' . min($combination['*Quantity'], $quantity) . '</Quantity>';

                        $variation_XML .= '<VariationSpecifics>';
                        $variation_XML .= '<NameValueList>';

                        $variation_XML .= '<Name>' . $variations['0'] . '</Name>';
                        $variation_XML .= '<Value>' . $variations['1'] . '</Value>';

                        $variation_XML .= '</NameValueList>';
                        $variation_XML .= '</VariationSpecifics>';
                        $variation_XML .= '</Variation>';
                    }
                }
                $compatibility_XML .= '</ItemCompatibilityList>';
                $variation_XML .= '</Variations>';
                //echo($check_compatibility);
                //die("STUFF");
                if ($check_compatibility) {
                    $uploadXML .= $compatibility_XML;
                    $uploadXML .= '<Quantity>' . min($product['product']['*Quantity'], $quantity) . '</Quantity>';
                    $product_price = $product['product']['*StartPrice'];
                    $uploadXML .= '<StartPrice currencyID="USD" alt="Jon1">' . $product['product']['*StartPrice'] . '</StartPrice>';
                } else {
                    $uploadXML .= $variation_XML;
                }
            } else {
                $uploadXML .= '<Quantity>' . min($product['product']['*Quantity'], $quantity) . '</Quantity>';
                $product_price = $product['product']['*StartPrice'];
                $uploadXML .= '<StartPrice currencyID="USD" alt="Jon2">' . $product['product']['*StartPrice'] . '</StartPrice>';
            }

            $itemspecifics_XML .= "</ItemSpecifics>";
            $uploadXML .= $itemspecifics_XML;

            $uploadXML .= '<ShippingDetails>';
            $uploadXML .= '<ShippingType>';
            $uploadXML .= 'Flat';
            $uploadXML .= '</ShippingType>';

            $uploadXML .= '<ShippingServiceOptions>';
            $uploadXML .= '<ShippingServicePriority>1</ShippingServicePriority>';
            $uploadXML .= '<ShippingService>UPSGround</ShippingService>';
            $shipping_cost = $this->get_shipping_cost($product_price);
            $uploadXML .= '<ShippingServiceCost>' . $shipping_cost . '</ShippingServiceCost>';
            $uploadXML .= '<ShippingServiceAdditionalCost>0.00</ShippingServiceAdditionalCost>';
            $uploadXML .= '</ShippingServiceOptions>';
            $uploadXML .= '</ShippingDetails>';
            $title = $this->xmlEscape($product['product']['*Title']);
            if ($check_compatibility) {
                $start_year = min($years);
                $end_year = max($years);
                if ($start_year != $end_year) {
                    $uploadXML .= '<Title>' . ($part_title = substr(strip_tags($title . ' ' . $start_year . '-' . $end_year), 0, 78)) . '</Title>';
                } else {
                    $uploadXML .= '<Title>' . ($part_title = substr(strip_tags($title . ' ' . $start_year), 0, 78)) . '</Title>';
                }
            } else {
                $uploadXML .= '<Title>' . ($part_title = substr(strip_tags($title), 0, 78)) . '</Title>';
            }

            if ($late_description) {
                $uploadXML .= "<Description>" . $part_title . "</Description>";
            }

            $uploadXML .= '</Item>';
            $uploadXML .= '</AddFixedPriceItemRequest>
';
            $this->addRow($product['product']['C:Manufacturer Part Number'], $title, substr($uploadXML, $uploadXML_start_length));
        }

        fputs($handle, $uploadXML);
    }


    /*
     * This transforms a list of products into an array $final.
     * $final is mapped by part title.
     */
    private function convertToEbayFormat($data) {
        //$this->pr($data);
        //echo("convertToEbayFormat");
        $final = array();
        foreach ($data as $key => $value) {

            if (strpos($value['*Title'], 'COMBO') !== FALSE || isset($value['product_options'])) {
                $product_variation = $value['product_variation'];
                $different_variations = $value['product_options'];
                unset($value['product_variation']);
                unset($value['product_options']);

                $final[$value['*Title']] = array(
                    'product_variation' => $product_variation,
                    'product_options' => $different_variations
                );
                //$this->pr($different_variations);
                //die("aisi hoti hai");
            }

            // JLB 09-07-17 I think they incorrectly did compatibility and variation as the same thing...
            if (trim($value['*Title']) != "") {
                $title = $value['*Title'];
                $final[$title]['product'] = $value;
                if (trim(strtolower($value['Relationship'])) == "compatibility") {
                    if (!array_key_exists("product_variation", $final[$title])) {
                        $final[$title]["product_variation"] = array();
                    }
                    $final[$title]["product_variation"][] = $value;
                }
            }

            // JLB 09-07-17
            // I think this is again done wrong on the variation type.
//            if (trim(strtolower($value['Relationship'])) == "variation" || trim(strtolower($value['Relationship'])) == "compatibility") {
//                $variations[] = $value;
//            }



		 }
        //echo "************************************";
        //$this->pr($final);
        //die("*");
        return $final;
    }

    public function getcategories() {
        $this->db->select("ebay_category_num");
        $this->db->from('category');
        $query = $this->db->get();
        return $query->result_array();
    }

    private function findTopPrority($all_category) {
//        $this->pr($all_category);
//        die("*");
        $street_find = array();
        $dirt_find = array();
        $utv_find = array();
        $atv_find = array();
        foreach ($all_category as $key => $category) {
            if (strpos($category, "STREET BIKE PARTS") !== false) {
                $street_find[$key] = $category;
            }

            if (strpos($category, "DIRT BIKE PARTS") !== false) {
                $dirt_find[$key] = $category;
            }
            if (strpos($category, "UTV PARTS") !== false) {
                $utv_find[$key] = $category;
            }
            if (strpos($category, "ATV PARTS ") !== false) {
                $atv_find[$key] = $category;
            }
        }

        $order = array("STREET BIKE PARTS" => $street_find, "DIRT BIKE PARTS" => $dirt_find, "UTV PARTS" => $utv_find, "ATV PARTS" => $atv_find);
//        pr($order);
        foreach ($order as $key => $value) {
            if (count($value) > 0) {
                break;
            }
        }
//        $category_long+string
//         if (strlen($cat['long_name']) > $endCategoryName)
//                            $endCategoryName = $cat['long_name'];
        $greaterThenFound = 0;
        $final_catId = 0;
        foreach ($value as $key => $value) {
            if (substr_count($value, '>') > $greaterThenFound) {
                $greaterThenFound = substr_count($value, '>'); //
                $final_catId = $key;
            }
        }
        return $final_catId;
    }

    public function update_ebay_feed_log($data) {
        $this->db->insert('ebay_feed_log', $data);
    }

    public function get_ebay_feed_log() {
		
        $sql = "SELECT * FROM ebay_feed_log order by run_at desc limit 1";
        $query = $this->db->query($sql);
        $results = $query->result_array();
        if ($results) {
            return $results[0];
        } else {
            return array();
        }
    }

    public function ebayListings($offset = 0, $limit = 1000, $return_csv = FALSE, $send_to_ebay = FALSE, $debug = false) {
        // Filter quantity of 0, Price in 1 row only
        $nocat=0;
        $catg=0;
        $finalArray = array();
        if ($limit == 0) {
            $limit_query = '';
        } else {
            $limit_query = "LIMIT " . $offset . ", " . $limit;
        }
        //$where_part_id = " AND part.part_id = " . 6912593;  //  maxxis tire (combo?)
        //$where_part_id = " AND part.part_id = " . 4661025; //  honda shirt (variations, no compatibility)
        //$where_part_id = " AND part.part_id = " . 6131964;  //  acerbis kit (combo and compatibility)
        //$where_part_id = " AND part.part_id = " . 4661029;  //  honda long sleeve shirt, variations, no MAP
        //$where_part_id = " AND part.part_id = " . 4661416;  //  Firstgear jacket, variations, MAP
        //$where_part_id = " AND part.part_id = " . 6506255;

        $sql = "SELECT 
						part.part_id,
						'Add' AS '*Action(SiteID=eBayMotors|Country=US|Currency=USD|Version=745|CC=UTF-8)',	
						part.name AS '*Title',
						part.description AS '*Description',
						1000 AS '*ConditionID',
						CONCAT ('http://" . WEBSITE_HOSTNAME . "/productimages/', partimage.path) AS PicURL,
						'1' AS 'PayPalAccepted',
						'bvojcek@motomonster.com' AS 'PayPalEmailAddress',
						'FixedPrice' AS '*Format',
						'GTC' AS '*Duration',
						2 AS '*DispatchTimeMax', 
						'ReturnsAccepted' AS '*ReturnsAcceptedOption',
						'Days_30' AS 'ReturnsWithinOption',
						'Buyer' AS 'ShippingCostPaidByOption',
						brand.name AS 'C:Brand',
						partnumber.price as customprice,
						partnumber.sale as saleprice,
						IfNull(partvariation.manufacturer_part_number, CONCAT('DLR', partvariation.distributor_id, '_', partvariation.part_number)) AS 'C:Manufacturer Part Number',
						'" . $this->store_zip_code . "' AS 'PostalCode',
						'UPSGround' AS 'ShippingService-1:Option',
						'1' AS 'ShippingService-1:FreeShipping',
						'' as 'CustomLabel',
						'' AS '*Quantity',
						'' AS '*StartPrice',
						'' AS 'Relationship',
						'' AS 'RelationshipDetails'
					FROM part
						JOIN partpartnumber ON partpartnumber.part_id = part.part_id
						JOIN partimage ON partimage.part_id = partpartnumber.part_id
						JOIN partnumber ON partnumber.partnumber_id = partpartnumber.partnumber_id
						JOIN partbrand ON partbrand.part_id = partpartnumber.part_id
						JOIN brand ON brand.brand_id = partbrand.brand_id
						JOIN partvariation ON partvariation.partnumber_id = partnumber.partnumber_id
						WHERE partnumber.exclude_market_place != 1
						AND partnumber.closeout_market_place != 1
						AND brand.closeout_market_place != 1
						AND brand.exclude_market_place != 1
						AND partnumber.sale > 1
						GROUP BY part.part_id $limit_query";
        $query = $this->db->query($sql);
        $parts = $query->result_array();

        if ($debug) {
            print "eBayListing: parts returned: " . count($parts) . "\n";
        }

        $query->free_result();
        $paypal_email = $this->get_paypalemail();
        $quantity = $this->get_quantity();
        if (is_array($parts)) {
            foreach ($parts as &$part) {

                if (strpos($part['*Title'], 'COMBO') !== FALSE) {
                    continue;
                }
                $part["PayPalEmailAddress"] = $paypal_email;
                $part["*Quantity"] = min($part["*Quantity"], $quantity);
                $part_id = $part['part_id'];
                unset($part['part_id']);
                /*                 * ***********************************
                  Get Categories with longest string count
                 * ************************************ */
                /*
                 * JLB 08-23-17
                 * See the note above; I don't get this stupidity, but, let's let it ride. This could be rewritten
                 * as a regular old query. But, I rise today to vindicate the honor of UTV. No longer will UTV be
                 * treated as a stepchild; if it has a number, it goes.
                 */
                $sql = "SELECT category.long_name,category.ebay_category_num FROM category JOIN partcategory ON partcategory.category_id = category.category_id	WHERE partcategory.part_id = " . $part_id ;
                // ' AND long_name NOT LIKE \'%UTV%\'';
                $query = $this->db->query($sql);
                $categories = $query->result_array();
//                $this->pr($categories);
//                die("category");
                $query->free_result();
                // Create Category Name;
                $endCategoryName = '';

                $all_categories = array();
                if ($categories) {
                    foreach ($categories as $cat) {
                        if ($cat['ebay_category_num'] != NULL) {
                            $all_categories[$cat['ebay_category_num']] = $cat['long_name'];
                            if (strlen($cat['long_name']) > $endCategoryName)
                                $endCategoryName = $cat['long_name'];
                        }
                    }
                }

//                $this->pr($endCategoryName);
//                die("category");
                // If no category, don't list the product
                if (empty($endCategoryName)) {
                    $nocat++;
                    continue;
                } else {

                    $catg++;
                }
//                $this->pr($all_categories);
//                die("*");
//                $part['*Category'] = $all_categories;
//                $part['*Category'] = $this->findTopPrority($all_categories);
                $part['EbayCategory'] = $this->findTopPrority($all_categories);

                $part['StoreCategory'] = $this->eBayStoreCategoryName($endCategoryName);

                /*                 * ************************
                  End Category Name.
                 * ************************* */

                // Get rest of records
                $sql = "SELECT
                        '' AS '*Action(SiteID=eBayMotors|Country=US|Currency=USD|Version=745|CC=UTF-8)',
                        '' AS '*Title',
                        '' AS '*Description',
                        '' AS '*ConditionID',
                        '' AS PicURL,
                        '' AS 'PayPalAccepted',
                        '' AS 'PayPalEmailAddress',
                        '' AS '*Format',
                        '' AS '*Duration',
                        '' AS 'DispatchTimeMax*', 
                        '' AS 'ReturnsAcceptedOption*',
                        '' AS 'ReturnsWithinOption',
                        '' AS 'ShippingCostPaidByOption',
                        '' AS 'C:Brand',
                        '' AS 'C:Manufacturer Part Number',
                        '' AS 'PostalCode',
                        '' AS 'ShippingService-1:Option',
                        '' AS 'ShippingService-1:FreeShipping',
                        partnumber.partnumber_id as CustomLabel,
                        partnumber.price as customprice,
                        partnumber.sale as saleprice,
                        partnumberpartquestion.answer AS 'answer',
                        partquestion.question,
                        IfNull(partvariation.quantity_available, 0) + IfNull(partdealervariation.quantity_available, 0) AS '*Quantity',
                        '' AS '*StartPrice',
                        partnumber.sale as price,
                        IfNull(partvariation.stock_code, partdealervariation.stock_code),
                        '' AS 'Relationship',
                        '' AS 'RelationshipDetails',
                        '' AS '*Category',
                        '' AS 'StoreCategory'
                    FROM partnumber
                    JOIN partnumberpartquestion ON partnumberpartquestion.partnumber_id = partnumber.partnumber_id 
                    JOIN partquestion ON partquestion.partquestion_id = partnumberpartquestion.partquestion_id 
                    JOIN partpartnumber ON partpartnumber.partnumber_id = partnumber.partnumber_id
                    JOIN partimage ON partimage.part_id = partpartnumber.part_id
                    JOIN part ON part.part_id = partpartnumber.part_id
                    LEFT JOIN partvariation ON partnumber.partnumber_id = partvariation.partnumber_id 
                    LEFT JOIN partdealervariation on partnumber.partnumber_id = partdealervariation.partnumber_id
                    WHERE part.part_id = " . $part_id . " 
                    AND partnumber.exclude_market_place != 1
                    AND partnumber.closeout_market_place != 1
                    AND partquestion.productquestion = 0
                    AND (partvariation.partvariation_id > 0 OR partdealervariation.partvariation_id > 0)
                    GROUP BY partnumber.partnumber";
                //					AND partvariation.quantity_available > 3
                $query = $this->db->query($sql);
                $partnumbers = $query->result_array();
                $query->free_result();

                // JLB 09-07-17 This used to be the craziest thing.
                if (is_array($partnumbers) && count($partnumbers) > 0) {
                    $categoryRec = array();
                    $fitmentArr = array();
                    $basicPrice = 0;
                    $samePrice = TRUE;
//                    if (count($partnumbers) > 0) {
                        foreach ($partnumbers as $pn) {

                            if ($pn['*Quantity'] > 0) {
                                //Calculate MAP Price
                                $this->db->select('MIN(brand.map_percent) as map_percent');
                                $where = array('partbrand.part_id' => $part_id);
                                $this->db->join('partbrand', 'partbrand.brand_id = brand.brand_id');
                                $brand_map_percent = $this->selectRecord('brand', $where);


                                $brandMAPPercent = is_numeric(@$brand_map_percent['map_percent']) ? $brand_map_percent['map_percent'] : 0;

                                $pn['*StartPrice'] = $pn['price'];
                                if (($brand_map_percent['map_percent'] !== NULL) && ($pn['stock_code'] != 'Closeout')) {

                                    $mapPrice = (((100 - $brandMAPPercent) / 100) * $pn['customprice']);
                                    // JLB 08-29-17 Of all the bassackwardness...is that >=???
                                    if(!($pn['price'] < $pn['customprice']))
                                        $pn['*StartPrice'] = $mapPrice;


                                }
                                // JLB 08-29-17 - What is the significance of this and why isn't it an ELSE?
                                if($brand_map_percent['map_percent'] === NULL || ($pn['stock_code'] == 'Closeout'))	{
                                    $markup = 1 + ($this->get_markup()/100);
                                    $pn['*StartPrice'] = $pn['*StartPrice']*$markup;


                                }


                                if ($basicPrice == 0) {
                                    $basicPrice = $pn['*StartPrice'];

                                }
                                if (($samePrice) && ($pn['*StartPrice'] != $basicPrice)) {
                                    $samePrice = FALSE;
                                }

                                $quantity = $this->get_quantity();

                                // Record Prep
                                $part['Relationship'] = '';
                                $part['RelationshipDetails'] = '';
                                $part['CustomLabel'] = $pn['CustomLabel'];
                                $part["*Quantity"] = min($pn["*Quantity"], $quantity);

                                $part['*Description'] = preg_replace("/\r\n|\r|\n/", '', $part['*Description']);

                                unset($pn['stock_code']);
                                unset($pn['price']);


                                // Fitment compatability
                                $sql = "SELECT CONCAT ('Make=', make.name, '|Model=',  model.name, '|Year=', partnumbermodel.year) AS fitment 
									FROM (`partnumbermodel`) 
									JOIN `model` ON `model`.`model_id` = `partnumbermodel`.`model_id` 
									JOIN `make` ON `make`.`make_id` = `model`.`make_id` 
									WHERE `partnumbermodel`.`partnumber_id` =  '" . $pn['CustomLabel'] . "'
									AND make.machinetype_id != 43954;";
                                $query = $this->db->query($sql);
                                $rides = $query->result_array();


                                $query->free_result();
                                $pn['CustomLabel'] = '';
                                if (!empty($rides)) { // Save Record for Fitment
                                    //unset($pn['answer']);
                                    //unset($pn['question']);
                                    $samePrice = FALSE;
                                    foreach ($rides as $ride) {
                                        $pn['Relationship'] = 'Compatibility';
                                        $pn['RelationshipDetails'] = $ride['fitment'];
                                        $fitmentArr[] = $pn;
                                    }
                                }
                                if (!empty($pn['question'])) { // Save record for Variations
                                    $pn['Relationship'] = 'Variation';
                                    $pn['RelationshipDetails'] = str_replace(' ', '', $pn['question'] . '=' . $pn['answer']);
                                    unset($pn['answer']);
//                                unset($pn['question']);
                                    $categoryRec[] = $pn;
                                }
                            }
                        }
//                    } else {
//                        // JLB 09-07-17 I don't think this else clause is EVER reached...
//                        $part["*Quantity"] = min($partnumbers[0]["*Quantity"], $quantity);
//                        $part['*StartPrice'] = $partnumbers[0]['price'];
//                        $finalArray[] = $part;
//                    }

                    // JLB 09-07-17
                    // First, it's either the same price or it's not.
                    // Second, it's got to have a price. Otherwise, what are we doing? It gets a price by having a quantity.
                    // if (($samePrice) && count($categoryRec) > 0) {
                    // if (($samePrice) && $basicPrice > 0) {
                    if (count($categoryRec) == 1) {
//                        if (count($categoryRec) > 1) {
//                            $part['*Quantity'] = '';
//                            $part['*StartPrice'] = $basicPrice;
//                            $part['item_id'] = $part_id;
//                            $finalArray[] = $part;
//
//                            foreach ($categoryRec as $rb) {
//                                $rb['*StartPrice'] = $basicPrice;
//                                $finalArray[] = $rb;
//                            }
//                        } else {
                            $part['*StartPrice'] = $basicPrice;
                            $part['item_id'] = $part_id;
                            $finalArray[] = $part;
//                        }
                    } elseif (count($categoryRec) > 0) { // JLB 09-07-17: This has to mean that there are at least two.
                        // $variations = array();
//                        $combopartIds = $this->checkForComboReporting($part_id);
//                        if (is_array($combopartIds)) {
//                            $PriceArr = array();
//                            $finalPriceArr = array('retail_min' => 0, 'retail_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
//                            foreach ($combopartIds as $id) {
//                                $PriceArr[] = $this->getPriceRangeReporting($id, FALSE, FALSE);
//                                $where = array('partpartnumber.part_id' => $id);
//                                $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
//                                $this->db->where('partnumber.price > 0');
//                                $this->db->where('partdealervariation.quantity_available > 0');
//                                $this->db->select('partnumber, MIN(partnumber.dealer_sale) AS dealer_sale_min, MAX(partnumber.dealer_sale) AS dealer_sale_max', FALSE);
//                                $this->db->group_by('part_id');
//                                $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id');
//                                $partDealerRec = $this->selectRecord('partnumber', $where);
//
//                                if (empty($partDealerRec)) {
//                                    $PriceArr['dealer_sale_min'] = 0;
//                                    $PriceArr['dealer_sale_max'] = 0;
//                                }
//                            }
//                            foreach ($PriceArr as $pa) {
//                                $finalPriceArr['retail_min'] += $pa['retail_min'];
//                                $finalPriceArr['retail_max'] += $pa['retail_max'];
//                                $finalPriceArr['sale_min'] += $pa['sale_min'];
//                                $finalPriceArr['sale_max'] += $pa['sale_max'];
//                                $finalPriceArr['dealer_sale_min'] += $pa['dealer_sale_min'];
//                                $finalPriceArr['dealer_sale_max'] += $pa['dealer_sale_max'];
//                            }
//                            $combo_price = $this->calculateMarkupReporting($finalPriceArr['retail_min'], $finalPriceArr['retail_max'], $finalPriceArr['sale_min'], $finalPriceArr['sale_max'], @$_SESSION['userRecord']['markup'], $finalPriceArr['dealer_sale_min'], $finalPriceArr['dealer_sale_max'], $finalPriceArr['cnt'])['sale_min'];
//                        }
                        /*
                         * JLB - So, it's declaring all variations to be combo parts, so it never looks at this?
                         */
                        $combo_variations = array();
                        foreach ($categoryRec as $rb) {
                            $newArray = $part;
                            $newArray['*Quantity'] = $rb['*Quantity'];
                            $newArray['*StartPrice'] = $rb['*StartPrice'];
//                            if(isset($combo_price))
//                                $newArray['*StartPrice'] = $combo_price;
                            $newArray['*Description'] = '';
                            $newArray['Relationship'] = $rb['Relationship'];
                            $newArray['RelationshipDetails'] = $rb['RelationshipDetails'];
                            $newArray['*Title'] = '';
                            $combo_variations[] = $newArray;
                        }
                        $product_options = $this->getProductQuestions($part_id);
                        $options_vailable = array();
                        foreach ($product_options as $otions_array) {
                            $options_vailable[$otions_array['question']][] = $otions_array['answer'];
                        }
//                        if(isset($combo_price)) {
//                            $part['*StartPrice'] = $combo_price;
//                        }
                        $part['product_options'] = $options_vailable;
                        $part['product_variation'] = $combo_variations;
                        $finalArray[] = $part;
                    } else {
                            /*
                             * JLB 09-07-17 Intentionally Left Blank.
                             * This is a case where there were no part variations that had a quantity. Nothing to do here.
                             */
                    }

                    if (!empty($fitmentArr)) {
                        $part['*StartPrice'] = $rb['*StartPrice'];

                        $compatibility_array = array();
                        $item = array();
                        $quantity = $this->get_quantity();
                        foreach ($fitmentArr as $key => $single_fitment) {
                            $change = $part;

                            $data_explode = explode('|', $single_fitment['RelationshipDetails']);
                            $make_explode = explode('=', $data_explode[0]);
                            $model_explode = explode('=', $data_explode[1]);
                            $year_explode = explode('=', $data_explode[2]);

                            $title = $part['*Title'] . ' For ' . $make_explode[1] . ' ' . $model_explode[1];
                            if($single_fitment["saleprice"]!=NULL) {
                                $single_fitment['*StartPrice'] = $single_fitment["saleprice"];
                            } else {
                                $single_fitment['*StartPrice'] = $single_fitment["*StartPrice"];
                            }
                            if($brand_map_percent['map_percent'] === NULL)	{
                                $markup = 1 + ($this->get_markup()/100);
                                $single_fitment['*StartPrice'] = $single_fitment['*StartPrice']*$markup;
                            }
                            if (key_exists($title, $item)) {

                                $item[$title][] = $single_fitment;
                            } else {
                                $change['*Title'] = $title;
                                $change['*StartPrice'] = $single_fitment["*StartPrice"];

                                $change['*Quantity'] = min($single_fitment["*Quantity"], $quantity);
                                $item[$title][] = $change;
                                $item[$title][] = $single_fitment;
                            }
                        }
                        foreach ($item as $key => $single_array) {

                            $finalArray = array_merge($finalArray, $single_array);
                        }
                    }
                }
                // JLB 09-07-17 - This appears to go nowhere...why does it exist?
                if (empty($part['saleprice'])&&isset($part['customprice'])) {
                    $part['*StartPrice'] = $part['customprice'];

                } else {
                    $part['*StartPrice'] = $part['saleprice'];
                }
            }
        }

        if ($debug) {
            print "eBayListing: Bottom of the big loop.\n";
        }

        if(!$send_to_ebay) {
        } else {
            if ($debug) {
                print "Returning CSV file \n";
            }

            if ($return_csv) {
                return $finalArray;
            }
            $csv = $this->array2csv($finalArray);
            return $csv;
        }
    }

    private function eBayCategoryName($categoryName) {
        /*
          •	Banners / Flags    # 56420 (leaf)
          •	Boots    # 6751 (leaf)
          •	Eye Wear    # 50424 (leaf)
          •	Gloves    # 50425 (leaf)
          •	Hats & Caps    # 50426 (leaf)
          •	Helmets    # 6749 (leaf)
          •	Jackets & Leathers    # 6750 (leaf)
          •	Off-Road Gear    # 34353 (leaf)
          •	Other Merchandise    # 34355 (leaf)
          •	Pants & Chaps    # 34354 (leaf)
          •	Patches    # 50427 (leaf)
          •	Shirts    # 6752 (leaf)
          •	Sweats & Hoodies    # 177125 (leaf)
         */
        if (strpos($categoryName, 'PANT') !== FALSE)
            return 34354;
        if (strpos($categoryName, 'SPROCKET') !== FALSE)
            return 49831;
        if (strpos($categoryName, 'HAT') !== FALSE)
            return 50426;
        if (strpos($categoryName, 'BOOT') !== FALSE)
            return 6751;
        if (strpos($categoryName, 'GLASSES') !== FALSE)
            return 50424;
        if (strpos($categoryName, 'GOGGLES') !== FALSE)
            return 50424;
        if (strpos($categoryName, 'HELMET') !== FALSE)
            return 6749;
        if (strpos($categoryName, 'JACKET') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'HOODY') !== FALSE)
            return 177125;
        if (strpos($categoryName, 'SWEATSHIRT') !== FALSE)
            return 177125;
        if (strpos($categoryName, 'SHIRT') !== FALSE)
            return 6752;
        if (strpos($categoryName, 'TANK TOP') !== FALSE)
            return 6752;
        if (strpos($categoryName, 'RAIN') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'JERSEYS') !== FALSE)
            return 34353;
        if (strpos($categoryName, 'PROTECTION') !== FALSE)
            return 34353;
        if (strpos($categoryName, 'GLOVES') !== FALSE)
            return 34353;
        if (strpos($categoryName, 'TIRES & WHEELS') !== FALSE)
            return 124313;
        if (strpos($categoryName, 'PACKS & BAGS') !== FALSE)
            return 34355;
        if (strpos($categoryName, 'SWIM TRUNKS') !== FALSE)
            return 34353;
        if (strpos($categoryName, 'SHOES') !== FALSE)
            return 6751;
        if (strpos($categoryName, 'HEATED SOCKS') !== FALSE)
            return 6751;
        if (strpos($categoryName, 'RACESUITS') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'HEATED GLOVES') !== FALSE)
            return 50425;
        if (strpos($categoryName, 'HEATED GEAR ACCESSORIES') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'SUITS') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'BASEGEAR & LINERS') !== FALSE)
            return 6750;
        if (strpos($categoryName, 'GEAR BAGS') !== FALSE)
            return 34355;
        if (strpos($categoryName, 'BACKPACKS') !== FALSE)
            return 34355;
        if (strpos($categoryName, 'CHAINS & MASTER LINKS') !== FALSE)
            return 49831;
        if (strpos($categoryName, 'CHEMICALS & OILS') !== FALSE)
            return 111112;
        if (strpos($categoryName, 'TRAILER ACCESSORIES') !== FALSE)
            return 50069;
        if (strpos($categoryName, 'TRAILER ELECTRICAL') !== FALSE)
            return 50069;
        if (strpos($categoryName, 'TRAILER TIRES & WHEELS') !== FALSE)
            return 50071;
        if (strpos($categoryName, 'TRAILERS') !== FALSE)
            return 50072;
        if (strpos($categoryName, 'TRAILERS') !== FALSE)
            return 50072;
        if (strpos($categoryName, 'TOOLS') !== FALSE)
            return 43990;
        if (strpos($categoryName, 'BARS & CONTROLS') !== FALSE)
            return 35564;
        return $categoryName;
    }

    private function eBayStoreCategoryName($categoryName) {
        if (strpos($categoryName, 'DIRT BIKE PARTS > CASUAL APPAREL') !== FALSE)
            return 8506710012;
        if (strpos($categoryName, 'SPROCKET') !== FALSE)
            return 8494715012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > RIDING GEAR') !== FALSE)
            return 8506717012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > PROTECTION') !== FALSE)
            return 8506711012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > TRAILERS') !== FALSE)
            return 8506712012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > DIRT BIKE PARTS') !== FALSE)
            return 8506716012;
        if (strpos($categoryName, 'STREET BIKE PARTS > PROTECTION') !== FALSE)
            return 8506718012;
        if (strpos($categoryName, 'STREET BIKE PARTS > STREET BIKE PARTS') !== FALSE)
            return 8506719012;
        if (strpos($categoryName, 'STREET BIKE PARTS > RIDING GEAR') !== FALSE)
            return 8506720012;
        if (strpos($categoryName, 'STREET BIKE PARTS > PACKS') !== FALSE)
            return 8506721012;
        if (strpos($categoryName, 'STREET BIKE PARTS > CASUAL APPAREL') !== FALSE)
            return 8506722012;
        if (strpos($categoryName, 'STREET BIKE PARTS > CHEMICALS') !== FALSE)
            return 8506723012;
        if (strpos($categoryName, 'STREET BIKE PARTS > CHEMICALS') !== FALSE)
            return 8506723012;
        if (strpos($categoryName, 'STREET BIKE PARTS > TRAILERS') !== FALSE)
            return 8506724012;
        if (strpos($categoryName, 'STREET BIKE PARTS > TOOLS') !== FALSE)
            return 8506725012;
        if (strpos($categoryName, 'ATV PARTS > TRAILERS') !== FALSE)
            return 8506726012;
        if (strpos($categoryName, 'ATV PARTS > RIDING GEAR') !== FALSE)
            return 8506717012;
        if (strpos($categoryName, 'ATV PARTS > PROTECTION') !== FALSE)
            return 8506711012;
        if (strpos($categoryName, 'ATV PARTS > TRAILERS') !== FALSE)
            return 8506712012;
        if (strpos($categoryName, 'ATV PARTS > HELMETS & ACCESSORIES > HELMETS') !== FALSE)
            return 8514794012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > JACKETS') !== FALSE)
            return 8514793012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > HOODYS & SWEATSHIRTS') !== FALSE)
            return 8514818012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > DUAL SPORT HELMETS') !== FALSE)
            return 8514646012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > OPEN FACE HELMETS') !== FALSE)
            return 8514647012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > FULL FACE HELMETS') !== FALSE)
            return 8514648012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > MODULAR HELMETS') !== FALSE)
            return 8514650012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > HALF SHELL HELMETS') !== FALSE)
            return 8514650012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > COMMUNICATION') !== FALSE)
            return 8514653012;
        if (strpos($categoryName, 'STREET BIKE PARTS > HELMETS & ACCESSORIES > HELMET CASES & BAGS') !== FALSE)
            return 8514654012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > T-SHIRTS') !== FALSE)
            return 8514812012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > SWIM TRUNKS') !== FALSE)
            return 8514816012;
        if (strpos($categoryName, 'ATV PARTS > CASUAL APPAREL > RAIN GEAR') !== FALSE)
            return 8514786012;
        if (strpos($categoryName, 'ATV PARTS > ATV PARTS > DRIVE > CHAINS & MASTER LINKS > CHAIN') !== FALSE)
            return 8514864012;
        if (strpos($categoryName, 'DIRT BIKE PARTS > CHEMICALS & OILS > ENGINE OIL') !== FALSE)
            return 8514866012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > GEAR OIL') !== FALSE)
            return 8514868012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > SUSPENSION FLUID') !== FALSE)
            return 8514870012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > 2-STROKE OIL') !== FALSE)
            return 8514867012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > CLEANING SUPPLIES') !== FALSE)
            return 8514869012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > BRAKE FLUID') !== FALSE)
            return 8514873012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > AIR FILTER OIL') !== FALSE)
            return 8514871012;
        if (strpos($categoryName, 'ATV PARTS > CHEMICALS & OILS > GLUE-SEALANT') !== FALSE)
            return 8514865012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > HAND TOOLS') !== FALSE)
            return 8514743012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > CARB & FUEL TOOLS') !== FALSE)
            return 8514745012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > SUSPENSION TOOLS') !== FALSE)
            return 8514750012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > ELECTRICAL TOOLS') !== FALSE)
            return 8514750012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > ENGINE TOOLS') !== FALSE)
            return 8514744012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > TIRE & WHEEL TOOLS') !== FALSE)
            return 8514753012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > CHAIN TOOLS') !== FALSE)
            return 8514747012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > GRIP TOOLS') !== FALSE)
            return 8514754012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > SECURITY CABLES & LOCKS') !== FALSE)
            return 8514756012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > MOTORCYCLE COVERS') !== FALSE)
            return 8514757012;
        if (strpos($categoryName, 'ATV PARTS > TOOLS > TIE DOWNS & ANCHORS') !== FALSE)
            return 8514755012;
        return $categoryName;
    }

    private function xmlEscape($string) {
        return str_replace(array('&', '<', '>', '\'', '"', "'"), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;', '&rsquo;'), $string);
    }

    /**
     * convertToEbayFormat
     *
     * This function get the data from reporting_m model and converts into the 
     * ebay specific format like,product---> variation inside a single array
     *
     * @param (array) (data) this is the input array which we like to manupulate
     * @return (array) (final) this is the output of the function
     */

	
	
	public function endAll($x, $y) {
        $this->_setHeader("EndItems", FALSE);
        $uploadXML = '<?xml version="1.0" encoding="utf-8"?>';

       $uploadXML .= '<BulkDataExchangeRequests>';
        $uploadXML .= '<Header>';
        $uploadXML .= '<SiteID>0</SiteID>';
        $uploadXML .= '<Version>849</Version>';
        $uploadXML .= '</Header>';
		//110206538682
		for($x=110212343213;$x<=110212366458;$x++) {
				$uploadXML .= '<EndFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <EndingReason>Incorrect</EndingReason>
		  <ItemID>'.$x.'</ItemID>
		<Version>849</Version>
		</EndFixedPriceItemRequest>';
		}

		$this->sendBulkXML($uploadXML, "EndFixedPriceItem");		
		
	}

	
	private function sendBulkXML($xml, $job_type, $debug) {
		
		
		require '../vendor/autoload.php';
		
		/**
		 * Include some utility functions.
		 */
		//require __DIR__.'/utils.php';
		/**
		 * Include the configuration values.
		 *
		 * Ensure that you have edited the configuration.php file
		 * to include your application keys.
		 */
		//$config = require __DIR__.'/../configuration.php';
		/**
		 * The namespaces provided by the SDK.
		 */
		/**
		 * Create the service objects.
		 *
		 * This example uses both the File Transfer and Bulk Data Exchange services.
		 */
		 
		 
		 
		$sdk = new Sdk($this->cred['Setting']);
		$exchangeService = $sdk->createBulkDataExchange();
		$transferService = $sdk->createFileTransfer();
		$merchantData = new MerchantData\MerchantData();

		if ($debug) {
		    print "Created the objects in sendBulkXML \n";
        }

		/**
		 * Before anything can be uploaded a request needs to be made to obtain a job ID and file reference ID.
		 * eBay needs to know the job type and a way to identify it.
		 */
		if($this->clear_jobs($debug)) {

            if ($debug) {
                print "sendBulkXML: Inside clear_jobs conditional  \n";
            }


            $createUploadJobRequest = new BulkDataExchange\Types\CreateUploadJobRequest();
			$createUploadJobRequest->uploadJobType = $job_type;
			$createUploadJobRequest->UUID = uniqid();
			/**
			 * Send the request.
			 */
			if ($this->debug || $debug) {
                print('Requesting job Id from eBay...');
            }
			$createUploadJobResponse = $exchangeService->createUploadJob($createUploadJobRequest);
			if ($this->debug || $debug) {
                print("Done\n");
            }

			/**
			 * Output the result of calling the service operation.
			 */
			if (isset($createUploadJobResponse->errorMessage)) {
				foreach ($createUploadJobResponse->errorMessage->error as $error) {
					printf(
						"2048 %s: %s\n\n",
						$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
						$error->message
					);
				}
			}

			if ($createUploadJobResponse->ack !== 'Failure') {
			    if ($this->debug) {
                    printf(
                        "JobId [%s] FileReferenceId [%s]\n",
                        $createUploadJobResponse->jobId,
                        $createUploadJobResponse->fileReferenceId
                    );

                }
				$job['jobId'] = $createUploadJobResponse->jobId;
				$job['fileReferenceId'] = $createUploadJobResponse->fileReferenceId;
			}
		
			/**
			 * Pass the required values to the File Transfer service.
			 */
			$uploadFileRequest = new FileTransfer\Types\UploadFileRequest();
			$uploadFileRequest->fileReferenceId = $job['fileReferenceId'];
			$uploadFileRequest->taskReferenceId = $job['jobId'];
			$uploadFileRequest->fileFormat = 'gzip';
			//$payload = buildPayload();
			/**
			 * Convert our payload to XML.
			 */
			//$payloadXml = $payload->toRequestXml();
			$payloadXml = $xml;
			/**
			 * GZip and attach the XML payload.
			 */
			$uploadFileRequest->attachment(gzencode($payloadXml, 9));
			/**
			 * Now upload the file.
			 */
			if ($this->debug || $debug) {
                print('Uploading fixed price item requests...');
            }

			$uploadFileResponse = $transferService->uploadFile($uploadFileRequest);
			if ($this->debug || $debug) {
                print("Done\n");
            }

			if (isset($uploadFileResponse->errorMessage)) {
				foreach ($uploadFileResponse->errorMessage->error as $error) {
					printf(
						"2093 %s: %s\n\n",
						$error->severity === FileTransfer\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
						$error->message
					);
				}
			}

            if ($uploadFileResponse->ack !== 'Failure') {
				/**
				 * Once the file has uploaded we can tell eBay to start processing it.
				 */
				$startUploadJobRequest = new BulkDataExchange\Types\StartUploadJobRequest();
				$startUploadJobRequest->jobId = $job['jobId'];
				if ($this->debug || $debug) {
                    print('Request processing of fixed price items...');
                }
				$startUploadJobResponse = $exchangeService->startUploadJob($startUploadJobRequest);
				if ($this->debug || $debug) {
                    print("Done\n");
                }

				if (isset($startUploadJobResponse->errorMessage)) {
					foreach ($startUploadJobResponse->errorMessage->error as $error) {
						printf(
							"2115 %s: %s\n\n",
							$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
							$error->message
						);
					}
				}
				if ($startUploadJobResponse->ack !== 'Failure') {
					/**
					 * Now wait for the job to be processed.
					 */
					$getJobStatusRequest = new BulkDataExchange\Types\GetJobStatusRequest();
					$getJobStatusRequest->jobId = $job['jobId'];
					$done = false;
					while (!$done) {
						$getJobStatusResponse = $exchangeService->getJobStatus($getJobStatusRequest);
						if (isset($getJobStatusResponse->errorMessage)) {
							foreach ($getJobStatusResponse->errorMessage->error as $error) {
								printf(
									"2133 %s: %s\n\n",
									$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
									$error->message
								);
							}
						}
						if ($getJobStatusResponse->ack !== 'Failure') {
						    if ($this->debug || $debug) {
                                printf("Status is %s\n", $getJobStatusResponse->jobProfile[0]->jobStatus);
                            }
							switch ($getJobStatusResponse->jobProfile[0]->jobStatus) {
								case BulkDataExchange\Enums\JobStatus::C_COMPLETED:
									$downloadFileReferenceId = $getJobStatusResponse->jobProfile[0]->fileReferenceId;
									$done = true;
									break;
								case BulkDataExchange\Enums\JobStatus::C_ABORTED:
								case BulkDataExchange\Enums\JobStatus::C_FAILED:
									$done = true;
									break;
								default:
									sleep(5);
									break;
							}
						} else {
							$done = true;
						}
					}
					if (isset($downloadFileReferenceId)) {
						$downloadFileRequest = new FileTransfer\Types\DownloadFileRequest();
						$downloadFileRequest->fileReferenceId = $downloadFileReferenceId;
						$downloadFileRequest->taskReferenceId = $job['jobId'];
						if ($this->debug || $debug) {
                            print('Downloading fixed price item responses...');
                        }
						$downloadFileResponse = $transferService->downloadFile($downloadFileRequest);
						if ($this->debug || $debug) {
                            print("Done\n");
                        }
						if (isset($downloadFileResponse->errorMessage)) {
							foreach ($downloadFileResponse->errorMessage->error as $error) {
								printf(
									"2169 %s: %s\n\n",
									$error->severity === FileTransfer\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
									$error->message
								);
							}
						}

						if ($downloadFileResponse->ack !== 'Failure') {
							/**
							 * Check that the response has an attachment.
							 */
							if ($downloadFileResponse->hasAttachment()) {
								$attachment = $downloadFileResponse->attachment();
								/**
								 * Save the attachment to file system's temporary directory.
								 */
								$filename = $this->saveAttachment($attachment['data']);
								if ($filename !== false && class_exists(ZipArchive)) {
									$xml = $this->unZipArchive($filename);
									if ($xml !== false) {
										$responses = $merchantData->addFixedPriceItem($xml);

                                            foreach ($responses as $response) {
											if (isset($response->Errors)) {
												foreach ($response->Errors as $error) {
												    if ($this->debug || $debug) {
                                                        printf(
                                                            "2195 %s: %s\n%s\n\n",
                                                            $error->SeverityCode === MerchantData\Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
                                                            $error->ShortMessage,
                                                            $error->LongMessage
                                                        );
                                                    }
													if ($error->SeverityCode === MerchantData\Enums\SeverityCodeType::C_ERROR) {
                                                        $this->recordError($response->CorrelationID, $error->ShortMessage, $error->LongMessage);
                                                    }
												}
											}
											if ($response->Ack !== 'Failure') {
											    if ($this->debug || $debug) {
                                                    printf(
                                                        "The item was listed to eBay with the Item number %s\n",
                                                        $response->ItemID
                                                    );
                                                }
											}
										}
									}
								}
							} else {
								print("Unable to locate attachment\n\n");
							}
						}
					}
				}
			}
		} 

		
	}
	
	private function clear_jobs($debug) {
				
		try {
			$service = new Services\BulkDataExchangeService($this->cred['Setting']);
		} catch (\Exception $e) {
			print "Error creating connection with credentials: ";
			print $e->getMessage() . "\n";
			return;
		}

		/**
		 * Create the request object.
		 */
		$request = new Types\GetJobsRequest();

		/**
		 * Send the request.
		 */
		try {
			$response = $service->getJobs($request);
		} catch (\Exception $e) {
			print "Error getting jobs: " . $e->getMessage() . "\n";
			return;
		}

		/**
		 * Output the result of calling the service operation.
		 */
		 //var_dump($response);
		if (isset($response->errorMessage)) {
			foreach ($response->errorMessage->error as $error) {
				printf(
					"2255 %s: %s\n\n",
					$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
					$error->message
				);
			}
		}
		if ($response->ack !== 'Failure') {
			/**
			 * Just display the first 3 jobs from the response.
			 */
			$upTo = min(count($response->jobProfile), 3);
			$upTo = 0;
			if(isset($response->jobProfile)) {
				foreach($response->jobProfile as $job) {
				    if ($this->debug || $debug) {
                        printf(
                            "ID: %s\nType: %s\nStatus: %s\nInput File Reference ID: %s\nFile Reference ID: %s\nPercent Complete: %s\nCreated: %s\nCompleted: %s\n\n",
                            $job->jobId,
                            $job->jobType,
                            $job->jobStatus,
                            $job->inputFileReferenceId,
                            $job->fileReferenceId,
                            $job->percentComplete,
                            $job->creationTime->format('H:i (\G\M\T) \o\n l jS F Y'),
                            isset($job->completionTime) ? $job->completionTime->format('H:i (\G\M\T) \o\n l jS F Y') : ''
                        );
                    }
					if($job->jobStatus!="Aborted"&&$job->jobStatus!="Completed"&&$job->jobStatus!="Failed") {
						
						$request = new Types\AbortJobRequest();
						$request->jobId = $job->jobId;
						/**
						 * Send the request.
						 */
						$response = $service->abortJob($request);
						if (isset($response->errorMessage)) {
							foreach ($response->errorMessage->error as $error) {
								printf(
									"2291 %s: %s\n\n",
									$error->severity === BulkDataExchange\Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
									$error->message
								);
							}
						}
						if ($response->ack !== 'Failure') {
							echo "Job aborted\n";
						}
					}
				}
			}
			return true;
		}
		return true;
	}
	
	
	public function updateEbayTracking($order_id, $ship, $carrier, &$error) {

		$store_url = base_url();
        $this->_setHeader("CompleteSale", FALSE);
		$xml = '<?xml version="1.0" encoding="utf-8"?>
<CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <RequesterCredentials>
			<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>
		  </RequesterCredentials>
  <OrderID>'.$order_id.'</OrderID>
  <Shipment> 
    <ShipmentTrackingDetails>
      <ShipmentTrackingNumber>'.$ship.'</ShipmentTrackingNumber>
      <ShippingCarrierUsed>'.$carrier.'</ShippingCarrierUsed>
    </ShipmentTrackingDetails>
  </Shipment>
  <TransactionID> string </TransactionID>
  <Version>849</Version>
  <WarningLevel>High</WarningLevel>
</CompleteSaleRequest>';
		//echo $xml;
        $response = json_decode(json_encode((array) simplexml_load_string($this->call($xml))), 1);
        // JLB 08-24-17 The Ajax handler is so incredibly primitive. David used to write a note
        // Tracking info successfully sent to eBay.
        // But, it's only looking for the word "success" - literaly...

		if($response['Ack']=="Success") {
		    return true;
        } else {
		    if (array_key_exists("Errors", $response) && array_key_exists("ShortMessage", $response["Errors"])) {
                $error = $response["Errors"]["ShortMessage"];
            } else {
                $error = "An unidentified error occurred. Please confirm your eBay authentication settings are valid in the store profile.";
            }
            return false;
        }
	}
	

	public function getOrders() {

		$store_url = base_url();
        $this->_setHeader("GetOrders", FALSE);
		
			$xml='<?xml version="1.0" encoding="utf-8"?>
				<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				  <RequesterCredentials>
					<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>
				  </RequesterCredentials>
				<NumberOfDays>30</NumberOfDays>
				</GetOrdersRequest>';	
				

        $response = json_decode(json_encode((array) simplexml_load_string($this->call($xml))), 1);

		//echo $xml;
//		print_r($response);
//		echo "<br><br><br>****************<br><br>";
		foreach($response['OrderArray']['Order'] as $ebayOrder) {
						
            $where = array('ebay_order_id' => $ebayOrder['OrderID']);
			$results = $this->selectRecords('order', $where);
			if($results) {
				// to do: update ordersc
				// echo "Order" . $results[0]['id'] . " updated.<br/>";
			} else {

				$name = explode(" ", $ebayOrder['ShippingAddress']['Name']);
				$contact_array = array('first_name' => $name[0], 
				'last_name' => $name[1], 
				'street_address' => $ebayOrder['ShippingAddress']['Street1'], 
				'address_2' => implode(" ", $ebayOrder['ShippingAddress']['Street2']), 
				'city' => $ebayOrder['ShippingAddress']['CityName'], 
				'state' => $ebayOrder['ShippingAddress']['StateOrProvince'], 
				'zip' => $ebayOrder['ShippingAddress']['PostalCode'], 
				'country' => $ebayOrder['ShippingAddress']['Country']);
				$contactId = $this->createRecord('contact', $contact_array, FALSE);
				// echo "<br>Contact " . $contactId . " created.<br/>";

				$order = array('sales_price' => $ebayOrder['Subtotal'], 
							'contact_id' => $contactId.'',
							'shipping_id' => $contactId.'',
							'shipping' => $ebayOrder['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'], 
							'tax' => '0.00', 
							'order_date' => time($ebayOrder['CreatedTime']), 
							'user_id' => '0', 
							'created_by' => '1', 
							'source' => 'eBay', 
							'ebay_order_id' => $ebayOrder['OrderID']);
				$orderId = $this->createRecord('order', $order, FALSE);
				// echo "<br>Order" . $orderId . " created.<br/>";
				$transaction_array = array('order_id' => $orderId, 
							'amount' => $ebayOrder['AmountPaid'],
							'transaction_date' => strtotime($ebayOrder['CreatedTime']));
				$this->createRecord('order_transaction', $transaction_array, FALSE);
				//echo "<br>Order transaction for " . $orderId . " created.<br/>";
				$status_array = array('order_id' => $orderId, 
							'status' => 'Processing',
							'datetime' => time($ebayOrder['CreatedTime']));
				//			'status' => $ebayOrder['OrderStatus'],
				$this->createRecord('order_status', $status_array, FALSE);
				//echo "<br>Order status for " . $orderId . " created.<br/>";
				
				if(isset($ebayOrder['TransactionArray']['Transaction']['Item']))
					$transactions = [0 => $ebayOrder['TransactionArray']['Transaction']];
				else 
					$transactions = $ebayOrder['TransactionArray']['Transaction'];
				
				foreach( $transactions as $product) {
					
					$this->db->select('partpartnumber.part_id, partnumber.partnumber as SKU');
					
		            $where = array('manufacturer_part_number' => $product['Item']['SKU']);
					$this->db->join('partnumber', 'partvariation.partnumber_id = partnumber.partnumber_id');
					$this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
					$SKUresults = $this->selectRecords('partvariation', $where);
					if($SKUresults) {
						$part_id = $SKUresults[0]['part_id'];
						$real_SKU = $SKUresults[0]['SKU'];
						//echo "SKU: ".$real_SKU;
						//die();

						// Check whether this order_product already exists (eBay allows duplicates but our system does not) 
						// If already exists, add to quantity of existing order_product
						$where = array('order_id' => $orderId, 'product_sku' => $real_SKU);
						$order_product_exists = $this->selectRecords('order_product', $where);
						if($order_product_exists) {
							$update_array = ['qty' => $order_product_exists[0]['qty']+$product['QuantityPurchased']];
							$this->updateRecord('order_product', $update_array, $where, FALSE);						
							//echo "<br>Order product updated: $orderId - $real_SKU";
						} else {
							$product_array = array('order_id' => $orderId, 
										'product_sku' => $real_SKU, 
										'price' => $product['TransactionPrice'], 
										'qty' => $product['QuantityPurchased'],
										'part_id' => $part_id);
							$this->createRecord('order_product', $product_array, FALSE);
							//echo "<br>Order product added: $orderId - $real_SKU";
						}
						

						
					} else {
                        echo "<br>Product not found for ".$product['Item']['SKU'];
					}
				}
				
			}

		}
		
	}
	
	public function setNotifications() {

		$store_url = base_url();
        $this->_setHeader("SetNotificationPreferences", FALSE);
		
			$xml='<?xml version="1.0" encoding="utf-8"?>
		<SetNotificationPreferencesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <RequesterCredentials>
			<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>
		  </RequesterCredentials>
		  <ApplicationDeliveryPreferences>
			<AlertEmail>mailto:dbmathewes@gmail.com</AlertEmail>
			<AlertEnable>Enable</AlertEnable>
			<ApplicationEnable>Enable</ApplicationEnable>
			<ApplicationURL>'.site_url('incoming/ebay_notifications').'</ApplicationURL>
			<DeviceType>Platform</DeviceType>
			<DeliveryURLDetails>
			  <DeliveryURL>'.site_url('incoming/ebay_notifications').'</DeliveryURL>
			  <DeliveryURLName>Set Notifications</DeliveryURLName>
			  <Status>Enable</Status>
			</DeliveryURLDetails>
		  </ApplicationDeliveryPreferences>
		  <UserDeliveryPreferenceArray>
			<NotificationEnable>
			  <EventType>FixedPriceTransaction</EventType>
			  <EventEnable>Enable</EventEnable>
			</NotificationEnable>
			<NotificationEnable>
			  <EventType>AskSellerQuestion</EventType>
			  <EventEnable>Enable</EventEnable>
			</NotificationEnable>
		  </UserDeliveryPreferenceArray>
		</SetNotificationPreferencesRequest>';	

        $response = json_decode(json_encode((array) simplexml_load_string($this->call($xml))), 1);

		
	}

	public function getNotifications() {

		$store_url = base_url();
        $this->_setHeader("GetNotificationPreferences", FALSE);
		
			$xml='<?xml version="1.0" encoding="utf-8"?>
			<GetNotificationPreferencesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
		  <RequesterCredentials>
			<eBayAuthToken>' . $this->cred['Setting']['user_token'] . '</eBayAuthToken>
		  </RequesterCredentials>
  <PreferenceLevel>Application</PreferenceLevel>
</GetNotificationPreferencesRequest>';	

        $response = json_decode(json_encode((array) simplexml_load_string($this->call($xml))), 1);
		
		
	}


    /**
     * Function to call ebay API using the xml passed to it.
     * @param type $xml
     * @return type
     * @access private
     * @author Manish
     */
    private function call($xml) {
        $connection = curl_init();
//set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl);

//stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

//set the headers using the array of headers
        curl_setopt($connection, CURLOPT_HTTPHEADER, $this->headers);
//set method as POST
        curl_setopt($connection, CURLOPT_POST, 1);

//set the XML body of the request
        curl_setopt($connection, CURLOPT_POSTFIELDS, $xml);
//set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

//Send the Request
        $response = curl_exec($connection);

//close the connection
        curl_close($connection);
		//var_dump(curl_error($connection));
		//die();
        return $response;
    }

    private function getHeaders() {
        $this->boundary = "MIME_boundary";
        $this->getEbayAuthSettingsFromDb();
        if ($this->check_header_type_image) {
            $data = 'Content-Type: multipart/form-data; boundary=' . $this->boundary;
        } else {
            $data = "";
        }
        $this->headers = array(
            $data,
            //Regulates versioning of the XML interface for the API
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatibility_level,
            //set the keys
            'X-EBAY-API-DEV-NAME:' . $this->cred['Setting']['dev_id'],
            'X-EBAY-API-APP-NAME:' . $this->cred['Setting']['app_id'],
            'X-EBAY-API-CERT-NAME:' . $this->cred['Setting']['cert_id'],
            //the name of the call we are requesting
            'X-EBAY-API-CALL-NAME: ' . $this->call,
            //SiteID must also be set in the Request's XML
//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
//SiteID Indicates the eBay site to associate the call with
            'X-EBAY-API-SITEID: ' . 0
        );
    }

    public function dieSilentlyOnBadCredentials($value = true) {
        $this->_dieSilentlyIfBad = $value;
    }

    /**
     * Function to get all ebay auth setting from db
     * @access private
     * @author Anik Goel
     */
    public function sub_getEbayAuthSettingsFromDb() {
        $sql = "SELECT ebay_app_id, ebay_cert_id, ebay_dev_id, ebay_user_token, ebay_environment  
				FROM contact 
				WHERE id = 1";
        $query = $this->db->query($sql);
        $cred = $query->result_array();
        return $cred;
    }

    private function getEbayAuthSettingsFromDb() {


/*
        $this->cred['Setting'] = array(
            'dev_id' => "1a45cdf5-d592-4f43-96dd-83f5c03c29a6",
            'app_id' => 'pushpend-test-SBX-39f29112f-385610c5',
            'cert_id' => "SBX-9f29112f4a9a-1827-4270-911d-d034",
            'user_token' => "AgAAAA**AQAAAA**aAAAAA**k7CZWA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GiAJeCpwSdj6x9nY+seQ**hPYDAA**AAMAAA**58pYXe/Al5AAklWeiyoZzZ/GdVZz7I4Ze2rSB9OejaKTHGF7tF0+fkDwHvT9r+pGQ6bwDo6qN+lENnn7Z/baOHZIMe+xv0BNCYNBFw1cxGgMbOOAYP4jd6oyxcvUpKVJrUEFJiMqX533V/npXTQ03VRuQXh700oouz30DqV08gMt4QksaJkOcNz1paOmvVfdtes1ZDKnORvpUldYBoDoeJuZZxqG9u14HVnMymwOZByicT+4f3K9Ek55QDdDrLGRBx0Z3WTmWxr0OwCKPrDsBM1SKFUeOVjksG7VtI1BX72PcUeQrjPPBkpVQVDnEwHbqfyYqqfOz4klzjwq+Y/wRHSC3LPNf52G2brXQ9Xs/DXE0z1v466Hk4H8nbpltSdtXPiSo2FUZbp2OkenoyMRXSoYrHjLPgU7fnS6hGfhXXZZ+H+d+RihkrTniQmtHeZn9OZBgErRsE6wMHf307jXhiv+tMiHMcqytlR8/iV/Y6OCuN6m3TuOuaiuC/sSp59SXJWj1Yahk1cRKax2crO3tecLZJbYgSoAEjJtrdpST9KWuMg5jYbvkGxAvQ+3ckoT/bjD1+7+1GuS8eka59v4ee0d3hZJ+5jVDau5nO3u1QjBUiL1IAlyVczXsZxjpYFlgw/YyzdrYMuyzKC9FYZB33odB0ER4kIXsCQO/BvO2uzWCDkL6Tbj/iwyCEb4Rw690k5lUGeL8YJkWFtzViid1Vc3mU0aoZa/frtQu3r4vGQfE/LaNVCpFAurSxshAwqq"
        );
		
		$this->cred['Setting']['credentials']['appId']  = $this->cred['Setting']['app_id'];
		$this->cred['Setting']['credentials']['certId'] = $this->cred['Setting']['cert_id'];
		$this->cred['Setting']['credentials']['devId'] = $this->cred['Setting']['dev_id'];
		$this->cred['Setting']['authToken'] = $this->cred['Setting']['user_token'];


        $this->cred['Setting'] = array(
            'dev_id' => "b75ef97a-a42e-4373-ae99-019f78f9abdb",
            'app_id' => 'DavidMat-PowerSpo-SBX-18e3a12c2-738fe25f',
            'cert_id' => "SBX-8e3a12c29b5e-694a-4fe0-9801-5259",
            'user_token' => "AgAAAA**AQAAAA**aAAAAA**YzBIWQ**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GkC5CEoQ2dj6x9nY+seQ**ejwEAA**AAMAAA**dUAyijkm2T0cZZBxcXNjQKPoIVvWgmzN/cWR6R5BYgHMHd+8sfUtJpgfAbyM3DN4lHAt4314vce8UNPA6ZlDmY3uFWgZDrIkgkof2lGZn1cXMCkGwb/MZhLawxoTgsSRcq0ErYc+GiWzV7H08IqKwJ0QUfO+0ivRbcn1MgXXja1j1t2nUV8PArKAZAGp9t/g8T6w/bBmbPdaCRKh70PJuFmThp6yHLVoAPUNj7lkc9znlvMiHwoMmB0zrSd3HCCVoR2vVFJpVZ0H5YN1Zjed5I0DSe1k1BN7TZlC6GayjEsYhut3Kojn5g2y7oL8OlJ5vcCgUUJYfdLJtnURdKFcJqe2vaqyaCPPVV/MzjxypLbyFfGq/z0sSv1LAQdlWncJH7toNTYwO1gsKV9al8dLdnGOjoSSyWc1LYkllLUC5uaeEA5ewxq7vCNnR2UcdoytOKnR5LV/LEIKHbpBCzZcz0RuGRQbZ9U1xj7rUuOBOe8C89202kL2TUueX5BQkvcBKRTYxJk1r8zjVWPxQWL4hg1HrrTZsjwS69oh3sI1OJSAPxNoq9xr6DZsxioORvytJKtKmq4IMKUHNQsi8ctqAd6kZNM8GKlLT1DgElsKLYtKcrN+rtjm58B23aplULHBLGJOsTxLo3f/JUHXRSmeTz19sfJqZ4CiSgQ/S6KMAvLymiecRGl0EjtW7ZUKCYFmYCubJ3drtNiqHSNwdslprBtUHMhxhlGvrydoZCE+uU6pqEJZCt/AVVmVRXNUwVkX"
        );
		$this->cred['Setting']['credentials']['appId']  = $this->cred['Setting']['app_id'];
		$this->cred['Setting']['credentials']['certId'] = $this->cred['Setting']['cert_id'];
		$this->cred['Setting']['credentials']['devId'] = $this->cred['Setting']['dev_id'];
		$this->cred['Setting']['authToken'] = $this->cred['Setting']['user_token'];
		$this->cred['Setting']['sandbox'] = true;
*/
        $cred = $this->sub_getEbayAuthSettingsFromDb();

		if($cred[0]['ebay_dev_id'] == "" || $cred[0]['ebay_app_id'] == "" || $cred[0]['ebay_cert_id'] == "" || $cred[0]['ebay_user_token'] == "") {
            if ($this->_dieSilentlyIfBad) {
                exit();
            } else {
                die("Missing eBay developer credentials!");
            }
		}
        $this->cred['Setting'] = array(
            'dev_id' => $cred[0]['ebay_dev_id'],
            'app_id' => $cred[0]['ebay_app_id'],
            'cert_id' => $cred[0]['ebay_cert_id'],
            'user_token' => $cred[0]['ebay_user_token']
        );
		$this->cred['Setting']['credentials']['appId']  = $this->cred['Setting']['app_id'];
		$this->cred['Setting']['credentials']['certId'] = $this->cred['Setting']['cert_id'];
		$this->cred['Setting']['credentials']['devId'] = $this->cred['Setting']['dev_id'];
		$this->cred['Setting']['authToken'] = $this->cred['Setting']['user_token'];
		if($cred[0]['ebay_environment']=="Sandbox") {
			$this->cred['Setting']['sandbox'] = true;
		}
	}

    /**
     * Function to set header for calls
     * @param type $call_name
     * @param type $status
     * @author  Anil Gautam
     * @return  name of the image
     */
    private function _setHeader($call_name, $status) {
        $this->call = $call_name;
        $this->check_header_type_image = $status;
        $this->getHeaders();
    }

    public function update_ebay_feeds_log($data) {
        $this->db->insert('ebay_feed_log', $data);
    }

    public function insertEbayIds($data) {
        $this->db->insert('ebay_ids', $data);
    }

    public function checkForComboReporting($partid) {
        $sql = "SELECT partpartnumber.partnumber_id
					FROM (`partnumberpartquestion`)
					JOIN `partnumber` ON `partnumber`.`partnumber_id` = `partnumberpartquestion`.`partnumber_id`
					LEFT JOIN `partnumbermodel` ON `partnumbermodel`.`partnumber_id` = `partnumber`.`partnumber_id`
					JOIN `partpartnumber` ON `partpartnumber`.`partnumber_id` = `partnumber`.`partnumber_id`
					JOIN `partquestion` ON `partquestion`.`partquestion_id` = `partnumberpartquestion`.`partquestion_id`
					WHERE `partquestion`.`part_id` =  '" . $partid . "'
					AND `productquestion` =  0
					AND  (partnumber.universalfit > 0 OR partnumbermodel.partnumbermodel_id is not null) 
					GROUP BY `question`";

        $query = $this->db->query($sql);
        $partnumbers = $query->result_array();
        $query->free_result();
        if (count($partnumbers) > 1) {
            $parts = array();
            foreach ($partnumbers as $rec) {
                $sql = "SELECT part.part_id
							FROM part
							JOIN partpartnumber ON partpartnumber.part_id = part.part_id
							WHERE partpartnumber.partnumber_id = '" . $rec['partnumber_id'] . "'
							AND part.part_id != '" . $partid . "'	";
                $query = $this->db->query($sql);
                $results = $query->result_array();
                $query->free_result();
                $parts[] = @$results[0]['part_id'];
            }
            return $parts;
        } else
            return FALSE;
    }

    public function getPriceRangeReporting($partId, $activeMachine = NULL, $checkCombo = TRUE) {
        $combopartIds = FALSE;
        // $where = array('partpartnumber.part_id' => $partId);
        // $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        // $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        // $this->db->where('partnumber.price > 0');
        // $this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN 0 ELSE 1 END )");
        // $this->db->select('partnumber, MIN(partnumber.sale) AS sale_min, MIN(partnumber.price) AS price_min, MAX(partnumber.price) AS price_max, MAX(partnumber.sale) AS sale_max, count(partnumber) as cnt, MIN(partnumber.dealer_sale) AS dealer_sale_min, MAX(partnumber.dealer_sale) AS dealer_sale_max');
        // $this->db->group_by('part_id');
        // $partNumberRec = $this->selectRecord('partnumber', $where);

        $where = array('partpartnumber.part_id' => $partId);
        $this->db->join('partpartnumber', 'partpartnumber.partnumber_id = partnumber.partnumber_id');
        if (!is_null($activeMachine)) {
            $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partpartnumber.partnumber_id', 'LEFT');
            $where['partnumbermodel.year'] = $activeMachine['year'];
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id', 'left');
        $this->db->where('partnumber.price > 0');
        //$this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN 0 ELSE 1 END )");
        $this->db->where("(CASE WHEN partdealervariation.quantity_available = 0 AND partdealervariation.stock_code = 'Closeout' THEN CASE WHEN partvariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
        // $this->db->select('partnumber, 
        // MIN(partnumber.dealer_sale) AS dealer_sale_min,
        // MAX(partnumber.dealer_sale) AS dealer_sale_max,
        // MIN(partnumber.price) AS price_min, 
        // MAX(partnumber.price) AS price_max, 
        // MIN(partnumber.sale) AS sale_min, 
        // MAX(partnumber.sale) AS sale_max');
        // $this->db->group_by('part_id');
        // $partNumberRec = $this->selectRecord('partnumber', $where);

        $this->db->select('partnumber, partnumber.dealer_sale,partnumber.price, partnumber.sale, partdealervariation.quantity_available as dealer_quantity, partvariation.quantity_available');
        //$this->db->group_by('part_id');
        $partNumberRec1 = $this->selectRecords('partnumber', $where);

        $partNumberRec = array('price_min' => 0, 'price_max' => 0, 'sale_min' => 0, 'sale_max' => 0);
        foreach ($partNumberRec1 as $k => $v) {
            if ($v['dealer_quantity'] > 0) {
                if ($k == '0') {
                    $partNumberRec['sale_min'] = $v['dealer_sale'];
                } else if ($partNumberRec['sale_min'] > 0 && $partNumberRec['sale_min'] > $v['dealer_sale']) {
                    $partNumberRec['sale_min'] = $v['dealer_sale'];
                }
                if ($k == '0') {
                    $partNumberRec['sale_max'] = $v['dealer_sale'];
                } else if ($partNumberRec['sale_max'] > 0 && $partNumberRec['sale_max'] < $v['dealer_sale']) {
                    $partNumberRec['sale_max'] = $v['dealer_sale'];
                }
            } else {
                if ($k == '0') {
                    $partNumberRec['sale_min'] = $v['sale'];
                } else if ($partNumberRec['sale_min'] > 0 && $partNumberRec['sale_min'] > $v['sale']) {
                    $partNumberRec['sale_min'] = $v['sale'];
                }
                if ($k == '0') {
                    $partNumberRec['sale_max'] = $v['sale'];
                } else if ($partNumberRec['sale_max'] > 0 && $partNumberRec['sale_max'] < $v['sale']) {
                    $partNumberRec['sale_max'] = $v['sale'];
                }
            }
            if ($k == '0') {
                $partNumberRec['price_min'] = $v['price'];
            } else if ($partNumberRec['price_min'] > 0 && $partNumberRec['price_min'] > $v['price']) {
                $partNumberRec['price_min'] = $v['price'];
            }
            if ($k == '0') {
                $partNumberRec['price_max'] = $v['price'];
            } else if ($partNumberRec['price_max'] > 0 && $partNumberRec['price_max'] < $v['price']) {
                $partNumberRec['price_max'] = $v['price'];
            }
        }
        return $partNumberRec;
    }

    public function calculateMarkupReporting($retailmin, $retailmax = 0, $min, $max = 0, $userMarkUp = NULL, $dealer_sale_min = 0, $dealer_sale_max = 0, $cnt = 0) {
        $returnArr = array('retail_min' => $retailmin, 'retail_max' => $retailmax);
        if (@$userMarkUp) {
            $userMin = (($retailmin * $userMarkUp) / 100) + $retailmin;
            $userMax = (($retailmax * $userMarkUp) / 100) + $retailmax;
            if ($userMin < $min)
                $min = $userMin;
            if ($userMax < $max)
                $max = $userMax;
        }
        if ($min == $max) {
            $returnArr['sale_min'] = $min;
            $returnArr['sale_max'] = FALSE;
        } else {
            $returnArr['sale_min'] = $min;
            $returnArr['sale_max'] = $max;
        }
        if ($min < $retailmin) {
            $returnArr['percentage'] = 100 - (($min * 100) / $retailmin);
        } else
            $returnArr['percentage'] = FALSE;

        $sale_min = 0;
        if ($returnArr['sale_min'] > $dealer_sale_min && $dealer_sale_min > 0) {
            $sale_min = $returnArr['sale_min'];
            $returnArr['sale_min'] = $dealer_sale_min;
        }
        if ($returnArr['sale_min'] > $dealer_sale_max && $dealer_sale_max > 0) {
            $sale_min = $returnArr['sale_min'];
            $returnArr['sale_min'] = $dealer_sale_max;
        }

        if ($returnArr['sale_max'] < $dealer_sale_min && $dealer_sale_min > 0 && $returnArr['sale_max'] > 0) {
            $returnArr['sale_max'] = $dealer_sale_min;
        }
        if ($returnArr['sale_max'] < $dealer_sale_max && $dealer_sale_max > 0 && $returnArr['sale_max'] > 0) {
            $returnArr['sale_max'] = $dealer_sale_max;
        }

        if ($returnArr['sale_max'] == '' && $returnArr['sale_min'] < $sale_min) {
            $returnArr['sale_max'] = $sale_min;
        }

        if ($cnt == 1 && $returnArr['sale_min'] <= $dealer_sale_max) {
            $returnArr['sale_min'] = $dealer_sale_max;
            $returnArr['sale_max'] = FALSE;
        }

        if ($cnt == 1 && $returnArr['sale_min'] <= $dealer_sale_min) {
            $returnArr['sale_min'] = $dealer_sale_min;
            $returnArr['sale_max'] = FALSE;
        }

        if ($returnArr['sale_min'] > $returnArr['sale_max']) {
            $returnArr['sale_max'] = FALSE;
        }
        return $returnArr;
    }

    public function getProductQuestions($partId, $activeMachine = NULL) {
        $where = array('partquestion.part_id' => $partId, 'productquestion' => 0, "answer != ''" => NULL);
        if (@$activeMachine['model']['model_id']) {
            $where['partnumbermodel.model_id'] = $activeMachine['model']['model_id'];
        }
        if (@$activeMachine['year']) {
            $where['partnumbermodel.year'] = $activeMachine['year'];
        }
        $this->db->where($where);
        $this->db->where(' (partnumber.universalfit > 0 OR partnumbermodel.partnumbermodel_id is not null) ', NULL, FALSE);
        //$this->db->where('partnumber.sale != 0');
        $this->db->where("(CASE WHEN partdealervariation.quantity_available != 0 AND partdealervariation.stock_code = 'Closeout' THEN partnumber.dealer_sale != 0 ELSE partnumber.sale != 0 END )");

        //$this->db->where("(CASE WHEN partvariation.quantity_available = 0 AND partvariation.stock_code = 'Closeout' THEN CASE WHEN partdealervariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
        $this->db->where("(CASE WHEN partdealervariation.quantity_available = 0 AND partdealervariation.stock_code = 'Closeout' THEN CASE WHEN partvariation.quantity_available = 0 THEN 0 ELSE 1 END ELSE 1 END )");
        $this->db->join('partnumber', 'partnumber.partnumber_id = partnumberpartquestion.partnumber_id');
        $this->db->join('partvariation', 'partvariation.partnumber_id = partnumber.partnumber_id');
        $this->db->join('partdealervariation', 'partdealervariation.partnumber_id = partnumber.partnumber_id', 'left');
        $this->db->join('partnumbermodel', 'partnumbermodel.partnumber_id = partnumber.partnumber_id', 'LEFT');
        $this->db->join('partquestion', 'partquestion.partquestion_id = partnumberpartquestion.partquestion_id');

        $this->db->order_by('partquestion.partquestion_id, answer');
        $this->db->group_by('partquestion.partquestion_id, answer');
        $partNumberRecs = $this->selectRecords('partnumberpartquestion');
        //echo $this->db->last_query();
//        echo '<pre>';
//        print_r($partNumberRecs);
//        echo '</pre>';
//        exit;
        return $partNumberRecs;
    }

    public function get_shipping_cost($product_price) {
        $this->db->select("*");
        $this->db->from("ebay_shipping_rates");
        $query = $this->db->get();
        $shipping_cost_range = $query->result_array();
        foreach ($shipping_cost_range as $single_shipping_range) {
            if ($product_price >= $single_shipping_range['min_value'] && $product_price <= $single_shipping_range['max_value']) {
                $shipping_cost = $single_shipping_range['shipping_cost'];
                break;
            } elseif ($product_price >= $single_shipping_range['min_value'] && $single_shipping_range['max_value'] == 0) {
                $shipping_cost = 0;
                break;
            } else {
                $shipping_cost = 0;
            }
        }
        return $shipping_cost;
    }

    public function get_paypalemail()
    {
        $this->db->select("ebay_paypal_email");
        $this->db->from("contact");
        $this->db->where("id", "1");
        $query = $this->db->get();
        $result = $query->result_array();

        return $result[0]['ebay_paypal_email'];
    }

    public function get_markup() {
        $this->db->select("*");
        $this->db->from("ebay_settings");
        $this->db->where("key", "ebay_markup");		
        $query = $this->db->get();
        if (is_array($query->result_array())) {
            foreach ($query->result_array() as $paypal_value_check) {
                if (key_exists('value', $paypal_value_check) && $paypal_value_check['value'] != '') {
                    return floatVal($paypal_value_check['value']);
                }
            }
        }
        // You should never get here now.
        throw new Exception("eBay Request without markup.");
   }

   /*
    * JLB 09-06-17
    * So, we can't run it if there are any fatal errors. We shouldn't allow them to even make a request.
    */

   /*
    * Fatal error conditions: no markup, no quantity, no environment, no user token.
    */
   public function checkForFatalErrors(&$error_message) {
       $success = true;
       $error_message = "";

       // I just want to respect the structure.
       try {
           $markup = $this->get_markup();
       } catch(Exception $e) {
           $success = false;
           $error_message .= "eBay Markup % is not defined. ";
       }

       try {
           $quantity = $this->get_quantity();
       } catch(Exception $e) {
           $success = false;
           $error_message .= "eBay Listing Quantity is not defined. ";
       }

       // now, do you have the token?
       try {
           $quantity = $this->get_user_token();
       } catch(Exception $e) {
           $success = false;
           $error_message .= "eBay User Token is not defined. ";
       }

       return $success;
   }

   /*
    * Warning conditions: No PayPal email address...
    */
   public function checkForWarnings(&$error_message) {
       $success = true;
        $error_message = "";

        $paypal_email = $this->get_paypalemail();
        if (is_null($paypal_email) || trim($paypal_email) === "") {
            $success = false;
            $error_message = "PayPal email address is missing. ";
        }

       return $success;
   }

   public function get_user_token() {
       $cred = $this->sub_getEbayAuthSettingsFromDb();
       if ($cred[0]['ebay_user_token'] != "") {
           return $cred[0]['ebay_user_token'];
       }

       throw new Exception("Missing eBay user token.");
   }

    public function get_quantity() {
        $this->db->select("*");
        $this->db->from("ebay_settings");
        $this->db->where("key", "quantity");		
        $query = $this->db->get();
        if (is_array($query->result_array())) {
            foreach ($query->result_array() as $quantity_value_check) {
                if (key_exists('value', $quantity_value_check) && $quantity_value_check['value'] != '') {
                    return $quantity_value_check['value'];
                }
            }
        }
        // You should never get here.
        throw new Exception("eBay Request without quantity.");
    }
	
	
    public function getPriceByPartNumber($part_number) {
        if ($this->validPartNumber() === TRUE) {
            $partDealerNumberRec = $this->account_m->getDealerPriceByPartNumber($part_number);
            //$price = $this->account_m->getPriceByPartNumber($this->input->post('partnumber'));
            if (!empty($partDealerNumberRec) && $partDealerNumberRec['quantity_available'] > 0) {
                echo json_encode($partDealerNumberRec);
            } else {
                $price = $this->account_m->getPriceByPartNumber($part_number);
                //$partDealerNumberRec = $this->account_m->getDealerPriceByPartNumber($this->input->post('partnumber'));
                echo json_encode($price);
            }
        }
    }
	
	private function saveAttachment($data)
	{
		$tempFilename = tempnam(sys_get_temp_dir(), 'attachment').'.zip';
		$fp = fopen($tempFilename, 'wb');
		if ($fp) {
			fwrite($fp, $data);
			fclose($fp);
			return $tempFilename;
		} else {
			printf("Failed. Cannot open %s to write!\n", $tempFilename);
			return false;
		}
	}
	private function unzipArchive($filename)
	{
	    if ($this->debug) {
            printf("Unzipping %s...", $filename);
        }
		$zip = new ZipArchive();
		if ($zip->open($filename)) {
			/**
			 * Assume there is only one file in archives from eBay.
			 */
			$xml = $zip->getFromIndex(0);
			if ($xml !== false) {
			    if ($this->debug) {
                    print("Done\n");
                }
				return $xml;
			} else {
				printf("Failed. No XML found in %s\n", $filename);
				return false;
			}
		} else {
			printf("Failed. Unable to unzip %s\n", $filename);
			return false;
		}
	}	
	

}
