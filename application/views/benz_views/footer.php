<?php

/*
 * JLB 05-14-18
 * Some jackass decided to make three very similar but different footers.
 */

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("benz_views/footer.html");

mustache_tmpl_set($template, "new_assets_url", jsite_url("/qatesting/benz_assets/"));

mustache_tmpl_set($template, "store_name", $store_name['company']);


// we will set pages two ways
mustache_tmpl_set($template, "pages", $pages);
// we also will make that into its own rendered version
mustache_tmpl_set($template, "pages_rendered", jprint_interactive_footer($pages, false));


// the following builds up the address
mustache_tmpl_set($template, "street_address", $store_name['street_address']);
mustache_tmpl_set($template, "address_2", $store_name['address_2']);
mustache_tmpl_set($template, "city", $store_name['city']);
mustache_tmpl_set($template, "state", $store_name['state']);
mustache_tmpl_set($template, "zip", $store_name['zip']);
mustache_tmpl_set($template, "phone", $store_name['phone']);
mustache_tmpl_set($template, "email", $store_name['email']);
mustache_tmpl_set($template, "new_assets_url", $new_assets_url);

// we now render the social
mustache_tmpl_set($template, "social_link_buttons", $CI->load->view("social_link_buttons", array(
    "SMSettings" => $SMSettings
), true));
// and give it the raw, if desired
mustache_tmpl_set($template, "social_settings_raw", $SMSettings);
foreach ($SMSettings as $key => $val) {
    mustache_tmpl_set($template, "social_" . $key, $val);
}

mustache_tmpl_set($template, "braintree", $CI->load->view("braintree", array(
    "store_name" =>	$store_name
), true));

/*
 * These are specific to the home screen
 */
mustache_tmpl_set($template, "HOME_SCREEN_SLIDER_SPEED", defined("HOME_SCREEN_SLIDER_SPEED") ? HOME_SCREEN_SLIDER_SPEED : 300);
mustache_tmpl_set($template, "HOME_SCREEN_PAGINATION_SPEED", defined("HOME_SCREEN_PAGINATION_SPEED") ? HOME_SCREEN_PAGINATION_SPEED : 400);
mustache_tmpl_set($template, "HOME_SCREEN_AUTO_PLAY_TIMEOUT", defined("HOME_SCREEN_AUTO_PLAY_TIMEOUT") ? HOME_SCREEN_AUTO_PLAY_TIMEOUT : 5000);
// JLB: I don't know why there are two or which works but they had different defaults..
mustache_tmpl_set($template, "HOME_SCREEN_AUTO_PLAY_TIMEOUT2", defined("HOME_SCREEN_AUTO_PLAY_TIMEOUT") ? HOME_SCREEN_AUTO_PLAY_TIMEOUT : 1000);

mustache_tmpl_set($template, "s_baseURL", $s_baseURL);
mustache_tmpl_set($template, "WEBSITE_NAME", WEBSITE_NAME);

mustache_tmpl_set($template, "flexiselect", $CI->load->view("master/widgets/flexiselect", array(), true));

if (isset($script) && $script != "") {
    mustache_tmpl_set($template, "script", $script);
} else {
    mustache_tmpl_set($template, "script", "");
}

mustache_tmpl_set($template, "ride_selection_js",  $CI->load->view("widgets/ride_selection_js", array(
    "product" => isset($product) ? $product : null,

), true));
mustache_tmpl_set($template, "showvideo_function", $CI->load->view("showvideo_function", array(), false));


mustache_tmpl_set($template, "ENABLE_INVENTORY_SITEMAP", defined("ENABLE_INVENTORY_SITEMAP") && ENABLE_INVENTORY_SITEMAP);

/*
 * Comment preserved from the "ownCarousel" in the template:
 *             // JLB 01-31-18
            // The BENZ guys just cannot make good names. I don't know which ones of these are live, but they all appear to exist somewhere.
            // Really, a clusterfuck of bad design on this page...and it's duplicated in header.php and in a few other spots.
 */

jtemplate_add_store_hours($template, $store_name);
echo mustache_tmpl_parse($template);
