<?php
/**
 * Created by Sudesh.
 * User: Sudesh
 * Date: 10/16/18
 * Time: 01:06 PM
 *
 *
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* NOTE!!!  Need to make sure to turn on if checkbox is there and off it is not */

class Trafficlogpro_M extends Master_M {

    function __construct() {
        parent::__construct();
        $this->load->model('admin_m');
    }

    public function insertInquiryData($post) {
        
        $apiDetails = $this->admin_m->getAdminShippingProfile();

        $payload = $this->getXml($post, $apiDetails);
                    $trafficLogProRes = $this->sendAsPost(
                    'http://api.trafficlogpro.com/xml/',
                    array('data' => $payload)
                    );

        // convert the XML result into array
        $array_data = json_decode(json_encode(simplexml_load_string($trafficLogProRes)), true);
        //if error code is 1 that's means something went wrong.

                    // print_r($array_data['message']);
        return $array_data;
    }

    public function sendAsPost($url, $vars) {
        $DEBUG = "sendAsPost vars: " . $vars . "  ----  ";
        //echo $DEBUG."<br/>";
    
        $fields_string = '';
        //url-ify the data for the POST
        foreach($vars as $key => $value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_POST, count($vars));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        
        //execute post
        $result = curl_exec($ch);
    
        $error = curl_errno($ch);
    
        //  file_put_contents('temp.tmp', print_r($error, true), FILE_APPEND);
        $DEBUG = "sendAsPost result: " . $result . "  ----  ";
        //echo $DEBUG."<br/>";
        
        //close connection
        curl_close($ch);
        return $result;
    }

    public function getXml($post, $apiDetails)
    {

        $date = date('Y-m-d');
        $timestamp = time();
        $time = date("H:i:s", $timestamp);
        $firstname = $post['firstName'];
        $lastName = $post['lastName'];
        $email = $post['email'];
        $questions_comment = $post['questions'];

        if (array_key_exists("make", $post)) {
            $vehicle_make_tradein = $post["make"];
        }else{
            $vehicle_make_tradein = '';
        }
        if (array_key_exists("model", $post)) {
            $vehicle_model_tradein = $post["model"];
        }else{
            $vehicle_model_tradein = '';
        }
        if (array_key_exists("year", $post)) {
            $vehicle_year_tradein = $post["year"];
        }else{
            $vehicle_year_tradein = '';
        }
        if (array_key_exists("miles", $post)) {
            $vehicle_miles_tradein = $post["miles"];
        }else{
            $vehicle_miles_tradein = '';
        }
        if (array_key_exists("phone", $post)) {
            $phone = $post["phone"];
        }else{
            $phone = '';
        }
        if (array_key_exists("address", $post)) {
            $address = $post['address'];
        }else{
            $address = '';
        }
        if (array_key_exists("city", $post)) {
            $city = $post['city'];
        }else{
            $city = '';
        }
        if (array_key_exists("state", $post)) {
            $state = $post['state'];
        }else{
            $state = '';
        }
        if (array_key_exists("zipcode", $post)) {
            $zipcode = $post['zipcode'];
        }else{
            $zipcode = '';
        }
        if (array_key_exists("date_of_ride", $post)) {
            $date_of_ridedate_of_ride = $post["date_of_ride"];
        }else{
            $date_of_ride = '';
        }
        if (array_key_exists("date_of_ride", $post)) {
            $date_of_ridedate_of_ride = $post["date_of_ride"];
        }else{
            $date_of_ride = '';
        }if (array_key_exists("date_of_ride", $post)) {
            $date_of_ridedate_of_ride = $post["date_of_ride"];
        }else{
            $date_of_ride = '';
        }

        if (array_key_exists("product_id", $post)) {

        $product_id = $post["product_id"];
        $this->load->model('motorcycle_m');
        
        $product_detail = $this->motorcycle_m->getMotorcycle($product_id);
        
        //product details
            
        $prodcuct_year = $product_detail['year'];
        $prodcuct_make = $product_detail['make'];
        $prodcuct_vin_number = $product_detail['vin_number'];
        $prodcuct_mileage = $product_detail['mileage'];
        $prodcuct_color = $product_detail['color'];
        $prodcuct_condition = $product_detail['condition'];
        $prodcuct_model = $product_detail['model'];
        $prodcuct_sale_price = $product_detail['sale_price'];
        $prodcuct_category = $product_detail['category'];

         // traffic log pro API details

        $trafficLogProDealerCode = $apiDetails['trafficLogProDealerCode'];
        $trafficLogProApiKey = $apiDetails['trafficLogProApiKey'];



        }else{

            $prodcuct_year = '';
            $prodcuct_make = '';
            $prodcuct_vin_number = '';
            $prodcuct_mileage = '';
            $prodcuct_color = '';
            $prodcuct_condition = '';
            $prodcuct_model = '';
            $prodcuct_sale_price = '';
            $prodcuct_category = '';
        }
        
        if ($prodcuct_condition == 1){
            $prodcuct_condition = "New";
        }else{
            $prodcuct_condition = "Pre-Owned";
        }

        // SELECT * FROM sudesh_v1.motorcycle WHERE id=862;

        $xml = "<?xml version=\"1.0\"?>" .
        "<feed>" .
            "<apikey>".$trafficLogProApiKey."</apikey>" .
            "<dealer>".$trafficLogProDealerCode
            ."</dealer>" .
            "<date>".$date."</date> " .
            "<time>".$time."</time>" .
            "<lead>" .
                "<date>".$date."</date> " .
                "<time>".$time."</time>" .
                "<contact>" .
                    "<firstname>".$firstname."</firstname>" .
                    "<middlename></middlename>" .
                    "<lastname>".$lastname."</lastname>" .
                    "<address1>".$address."</address1>" .
                    "<address2></address2>" .
                    "<city>".$city."</city>" .
                    "<state>".$state."</state>" .
                    "<zip>".$zipcode."</zip>" .
                    "<county></county>" .
                    "<country></country>" .
                    "<phone1>".$phone."</phone1>" .
                    "<phone2></phone2>" .
                    "<cell></cell>" .
                    "<email>".$email."</email>" .
                    "<dob></dob>" .
                "</contact>" .
                "<product>" .
                    "<year>".$prodcuct_year."</year>" .
                    "<make>".$prodcuct_make."</make>" .
                    "<model>".$prodcuct_model."</model>" .
                    "<color>".$prodcuct_color."</color>" .
                    "<condition>".$prodcuct_condition."</condition>" .
                    "<mileage>".$prodcuct_mileage."</mileage>" .
                    "<category>".$prodcuct_category."</category>" .
                    "<class></class>" .
                    "<vin>".$prodcuct_vin_number."</vin>" .
                    "<price>".$prodcuct_sale_price."</price>" .
                "</product>" .
                "<tradein>" .
                    "<year>".$vehicle_year_tradein."</year>" .
                    "<make>".$vehicle_make_tradein."</make>" .
                    "<model>".$vehicle_model_tradein."</model>" .
                    "<color></color>" .
                    "<mileage>".$vehicle_miles_tradein."</mileage>" .
                    "<vin></vin>" .
                    "<titlenumber></titlenumber>" .
                    "<payoff></payoff>" .
                    "<owedto></owedto>" .
                    "<accountnumber></accountnumber>" .
                "</tradein>" .
                "<comment>".$questions_comment."</comment>" .
            "</lead>" .
        "</feed>";
        return $xml;
    }
}
