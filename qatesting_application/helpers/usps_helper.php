<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 function USPSParcelRate($weight,$dest_zip, $country = 'USA') { 
        
         
        // This script was written by Mark Sanborn at http://www.marksanborn.net   
        // If this script benefits you are your business please consider a donation   
        // You can donate at http://www.marksanborn.net/donate.   
        $userName = '7242SIDE7307'; // Your USPS Username 
        $orig_zip = '83228'; // Zipcode you are shipping FROM 
         
        $url = "http://production.shippingapis.com/ShippingAPI.dll"; 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_HEADER, 1); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        $sdate = date("d-m-Y",date("U")); 
        if($country == 'USA')
        {
          $data = 'API=RateV4&XML=<RateV4Request USERID="'.$userName.'"> 
                                      <Revision>2</Revision> ';
          if(($weight * 16) < 14)
          {
             $data .=                '<Package ID="1ST"> 
                                          <Service>FIRST CLASS</Service> 
                                          <FirstClassMailType>PARCEL</FirstClassMailType> 
                                          <ZipOrigination>'.$orig_zip.'</ZipOrigination> 
                                          <ZipDestination>'.$dest_zip.'</ZipDestination> 
                                          <Pounds>0</Pounds> 
                                          <Ounces>'.($weight * 16).'</Ounces> 
                                          <Container></Container> 
                                          <Size>REGULAR</Size> 
                                          <Machinable>true</Machinable> 
                                      </Package>';
          }
          $data .=                   '<Package ID="1ST"> 
                                          <Service>PRIORITY COMMERCIAL</Service> 
                                          <FirstClassMailType>PACKAGE SERVICE</FirstClassMailType> 
                                          <ZipOrigination>'.$orig_zip.'</ZipOrigination> 
                                          <ZipDestination>'.$dest_zip.'</ZipDestination> 
                                          <Pounds>'.$weight.'</Pounds> 
                                          <Ounces>0</Ounces> 
                                          <Container></Container> 
                                          <Size>REGULAR</Size> 
                                          <Width>8</Width>
                                          <Length>8</Length>
                                          <Height>8</Height>
                                          <Machinable>true</Machinable> 
                                      </Package>
                                      <Package ID="1ST"> 
                                          <Service>EXPRESS COMMERCIAL</Service> 
                                          <FirstClassMailType>PACKAGE SERVICE</FirstClassMailType> 
                                          <ZipOrigination>'.$orig_zip.'</ZipOrigination> 
                                          <ZipDestination>'.$dest_zip.'</ZipDestination> 
                                          <Pounds>'.$weight.'</Pounds> 
                                          <Ounces>0</Ounces> 
                                          <Container></Container>  
                                          <Size>REGULAR</Size> 
                                          <Machinable>true</Machinable> 
                                      </Package>  
                                  </RateV4Request>'; 
          } else
        {       
        
          $data = 'API=IntlRateV2&XML=
                  <IntlRateV2Request  USERID="'.$userName.'">
                  <Revision>2</Revision> 
                    <Package ID="1ST">
                       <Pounds>'.$weight.'</Pounds> 
                      <Ounces>0</Ounces>
                      <Machinable>True</Machinable>
                      <MailType>all</MailType>
                      <GXG>
                           <POBoxFlag>N</POBoxFlag>
                           <GiftFlag>N</GiftFlag>
                      </GXG>
                      <ValueOfContents>100.00</ValueOfContents>
                      <Country>'.$country.'</Country>
                      <Container>RECTANGULAR</Container>
                      <Size>Regular</Size>
                      <Width>8</Width>
                      <Length>8</Length>
                      <Height>8</Height>
                      <Girth></Girth>
                      <CommercialFlag>y</CommercialFlag>
                    </Package>
                  </IntlRateV2Request>';
        }
/*
        print_r($data);
        exit();
*/
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data); 
        $result=curl_exec ($ch); 

        $data = strstr($result, '<?'); 
        // echo '<!-- '. $data. ' -->'; // Uncomment to show XML in comments 
        $xml_parser = xml_parser_create(); 
        xml_parse_into_struct($xml_parser, $data, $vals, $index); 
        xml_parser_free($xml_parser); 
        $params = array(); 
        $level = array(); 
        foreach ($vals as $xml_elem) { 
            if ($xml_elem['type'] == 'open') { 
                if (array_key_exists('attributes',$xml_elem)) { 
                    @list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']); 
                } else { 
                $level[$xml_elem['level']] = $xml_elem['tag']; 
                } 
            } 
            if ($xml_elem['type'] == 'complete') { 
            $start_level = 1; 
            $php_stmt = '$params'; 
            while($start_level < $xml_elem['level']) { 
                $php_stmt .= '[$level['.$start_level.']]'; 
                $start_level++; 
            } 
            $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];'; 
            eval(@$php_stmt); 
            } 
        } 
        curl_close($ch); 
/*
         
        echo '<pre>'; print_r($params); echo'</pre>'; // Uncomment to see xml tags 
        exit();
*/
        
        if(($country == 'USA') && (@$params['RATEV4RESPONSE']['1ST']))
          return $params['RATEV4RESPONSE']['1ST']; 
        elseif(@$params['INTLRATEV2RESPONSE']['1ST'])
          return $params['INTLRATEV2RESPONSE']['1ST'];
        else
        {
          echo "<pre>";
          print_r($params);
          echo "</pre>";
          exit();
        }
    } 
