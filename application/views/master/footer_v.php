<?php
$new_assets_url = jsite_url( "/qatesting/newassets/");
$new_assets_url1 = jsite_url( "/qatesting/benz_assets/");
	if(isset($_SERVER['HTTPS'])) {
		$assets = $s_assets;
	}

	$CI =& get_instance();
$CI->load->helper("mustache_helper");
$CI->load->helper("jonathan");

$template = mustache_tmpl_open("master/footer_v.html");
mustache_tmpl_set($template, "selector2_js", $CI->load->view("master/widgets/selector2_js", array(), true));
mustache_tmpl_set($template, "selector3_js", $CI->load->view("master/widgets/selector3_js", array(), true));
$catd = "";
foreach($category as $id => $ref){
    $catd = $ref['label'];
}
mustache_tmpl_set($template, "catd", $catd);
if (isset($footerscript)) {
    mustache_tmpl_set($template, "footerscript", $footerscript);
} else {
    mustache_tmpl_set($template, "footerscript", "");
}

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
mustache_tmpl_set($template, "social_link_buttons", getSocialLinkButtons());
// and give it the raw, if desired
mustache_tmpl_set($template, "social_settings_raw", $SMSettings);
foreach ($SMSettings as $key => $val) {
    mustache_tmpl_set($template, "social_" . $key, $val);
}

mustache_tmpl_set($template, "braintree", $CI->load->view("braintree", array(
    "store_name" =>	$store_name
), true));

mustache_tmpl_set($template, "IS_INVENTORY_SITEMAP", isMajorUnitShop());

mustache_tmpl_set($template, "assets", $assets);
mustache_tmpl_set($template, "top_parent", $top_parent);
jtemplate_add_store_hours($template, $store_name);
echo mustache_tmpl_parse($template);
