<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/6/18
 * Time: 2:54 PM
 */


$xml_data =<<<END
<HLSM>
<hlsmno>287</hlsmno>
<ItemCnt>3</ItemCnt>
<qty_1>1</qty_1>
<partnum_1>14321-KB4-671</partnum_1>
<hlsm_desc_1>SPROCKET, CAM (38T)</hlsm_desc_1>
<make_1>Honda</make_1>
<hlsm_price_1>29.49</hlsm_price_1>
<hlsm_year_1>2008</hlsm_year_1>
<hlsm_make_1>Honda</hlsm_make_1>
<hlsm_model_1>CMX250C Rebel</hlsm_model_1>
<hlsm_cat_1>Streetbike</hlsm_cat_1>
<hlsm_dealer_1>HLSM</hlsm_dealer_1>
<hlsm_showprice_1>No</hlsm_showprice_1>
<hlsm_ip_address_1>192.168.1.140</hlsm_ip_address_1>
<qty_2>1</qty_2>
<partnum_2>14401-KBG-671</partnum_2>
<hlsm_desc_2>CHAIN, CAM (98L)</hlsm_desc_2>
<make_2>Honda</make_2>
<hlsm_price_2>122.49</hlsm_price_2>
<hlsm_year_2>2008</hlsm_year_2>
<hlsm_make_2>Honda</hlsm_make_2>
<hlsm_model_2>CMX250C Rebel</hlsm_model_2>
<hlsm_cat_2>Streetbike</hlsm_cat_2>
<hlsm_dealer_2>HLSM</hlsm_dealer_2>
<hlsm_showprice_2>No</hlsm_showprice_2>
<hlsm_ip_address_2>192.168.1.140</hlsm_ip_address_2>
<qty_3>1</qty_3>
<partnum_3>5WH-15100-00-00</partnum_3>
<hlsm_desc_3>CRANKCASE ASSY</hlsm_desc_3>
<make_3>Yamaha</make_3>
<hlsm_price_3>498.49</hlsm_price_3>
<hlsm_year_3>-</hlsm_year_3>
<hlsm_make_3>Yamaha</hlsm_make_3>
<hlsm_model_3>-</hlsm_model_3>
<hlsm_cat_3>Motorcycle</hlsm_cat_3>
<hlsm_dealer_3>HLSM</hlsm_dealer_3>
<hlsm_showprice_3>Yes</hlsm_showprice_3>
<hlsm_ip_address_3>192.168.1.140</hlsm_ip_address_3>
</HLSM>
END;

$URL = $argv[1];
$ch = curl_init($URL);
curl_setopt($ch, CURLOPT_MUTE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
print_r($output);