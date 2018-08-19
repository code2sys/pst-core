<?php

$googleLocation = $store_name['street_address'] . ($store_name['address_2'] != ""? ', ' . $store_name['address_2'] : ""). ',' .$store_name['city'].', '.$store_name['state'] . " " . $store_name['zip'];

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$load_google_maps_template = mustache_tmpl_open("info/pages/loadGoogleMaps.html");
foreach ($store_name as $k => $v) {
    mustache_tmpl_set($load_google_maps_template, $k, $v);
}
mustache_tmpl_set($load_google_maps_template, "googleLocation", $googleLocation);
mustache_tmpl_set($load_google_maps_template, "urlEncodedGoogleLocation", urlencode($googleLocation));
print mustache_tmpl_parse($load_google_maps_template);
