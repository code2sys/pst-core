<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/6/18
 * Time: 2:54 PM
 */


$xml_data =<<<END
<HLSM>
<hlsmno>158018</hlsmno>
<ItemCnt>1</ItemCnt>
<DirID>powersporttechnologies</DirID>
<qty_1>1</qty_1>
<partnum_1>1S3-11351-00-00</partnum_1>
<hlsm_desc_1>GASKET CYLINDER</hlsm_desc_1>
<make_1>Yamaha</make_1>
<hlsm_price_1>15.03</hlsm_price_1>
<hlsm_year_1>2016</hlsm_year_1>
<hlsm_make_1>Yamaha</hlsm_make_1>
<hlsm_model_1>RAPTOR 700R</hlsm_model_1>
<hlsm_cat_1>ATV</hlsm_cat_1>
<hlsm_dealer_1>HLSM</hlsm_dealer_1>
<hlsm_showprice_1>Yes</hlsm_showprice_1>
<hlsm_ip_address_1>192.168.10.13</hlsm_ip_address_1>
<DirID>powersporttechnologies</DirID></HLSM> 
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
