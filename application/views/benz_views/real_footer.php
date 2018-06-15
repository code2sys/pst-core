<?php
$new_assets_url = jsite_url( "/qatesting/newassets/" );
$CI =& get_instance();
$CI->load->helper("mustache_helper");
$footer_template = mustache_tmpl_open("master/widgets/real_footer.html");


mustache_tmpl_set($footer_template, "store_name", $store_name['company']);
mustache_tmpl_set($footer_template, "about_us_url", site_url('pages/index/aboutus'));
// we will set pages two ways
mustache_tmpl_set($footer_template, "pages", $pages);
// we also will make that into its own rendered version
mustache_tmpl_set($footer_template, "pages_rendered", jprint_interactive_footer($pages, false));



// the following builds up the address
mustache_tmpl_set($footer_template, "street_address", $store_name['street_address']);
mustache_tmpl_set($footer_template, "address_2", $store_name['address_2']);
mustache_tmpl_set($footer_template, "city", $store_name['city']);
mustache_tmpl_set($footer_template, "state", $store_name['state']);
mustache_tmpl_set($footer_template, "zip", $store_name['zip']);
mustache_tmpl_set($footer_template, "phone", $store_name['phone']);
mustache_tmpl_set($footer_template, "email", $store_name['email']);
mustache_tmpl_set($footer_template, "new_assets_url", $new_assets_url);

// we now render the social
mustache_tmpl_set($footer_template, "social_link_buttons", $CI->load->view("social_link_buttons", array(
    "SMSettings" => $SMSettings
), true));
// and give it the raw, if desired
mustache_tmpl_set($footer_template, "social_settings_raw", $SMSettings);
foreach ($SMSettings as $key => $val) {
    mustache_tmpl_set($footer_template, "social_" . $key, $val);
}

mustache_tmpl_set($footer_template, "braintree", $CI->load->view("braintree", array(
    "store_name" =>	$store_name
), true));


echo mustache_tmpl_parse($footer_template);