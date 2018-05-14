<?php
$new_assets_url = jsite_url( "/qatesting/newassets/");
$new_assets_url1 = jsite_url( "/qatesting/benz_assets/");
	if(isset($_SERVER['HTTPS'])) {
		$assets = $s_assets;
	}

	$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("master/footer_v.html");
mustache_tmpl_set($template, "selector2_js", $CI->load->view("master/widgets/selector2_js"));
mustache_tmpl_set($template, "selector3_js", $CI->load->view("master/widgets/selector3_js"));
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

mustache_tmpl_set($template, "assets", $assets);
mustache_tmpl_set($template, "top_parent", $top_parent);

echo mustache_tmpl_parse($template);


?>
<script>
	$(document).ready(function() {
		$(".tp-cat-head").click(function(){
			$(this).toggleClass("active");
			$(this).next(".tlg").stop('true','true').slideToggle("slow");
		});
	});
</script>
	<div class="sw footer clear">
		<div class="container_b">
			<div class="one-fifth">
				<h3 class="aut-title">About <span><?php echo $store_name['company'];?></span></h3>
				<ul class="clear">
					<li><a href="<?php echo site_url('pages/index/aboutus');?>">About Us</a></li>
				</ul>				
			</div>
			<?php
			jprint_interactive_footer($pages); ?>


			<div class="one-fifth map">
				<h3>Contact Us</h3>
				<ul class="clear">
                    <li style="line-height: 16px">Address: <?php echo $store_name['street_address'].', ' . ($store_name['address_2'] != "" ? $store_name['address_2'] . ", " : "") . $store_name['city'].', '.$store_name['state'] . ' ' . $store_name['zip'];?></li>
					<li><img src="<?php echo $new_assets_url; ?>images/mobile.png"> <?php echo $store_name['phone'];?></li>
					<li><img src="<?php echo $new_assets_url; ?>images/footer-email.png"> <?php echo $store_name['email'];?> </li>
				</ul>
				<h3 class="aut-title">Payment Methods</h3>
				<a href="<?php echo site_url('pages/index/paymentoptions');?>">
					<img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/cc-badges-ppppcmcvdam.png" alt="Pay with PayPal, PayPal Credit or any major credit card" />
					<!--<img class="crdt" src="<?php echo $new_assets_url; ?>images/Credit-Cards.jpg">-->
				</a>
			</div>
			<div class="one-fifth">
				<h3>find us on</h3>
                <?php
                $CI =& get_instance();
                echo $CI->load->view("social_link_buttons", array(
                    "SMSettings" => $SMSettings
                ), true);
                ?>
				<h3 class="nwsltr">newsletter</h3>
				<form action="" method="post" id="form_example" class="form_standard">
					<input type="text" id="newsletter" name="newsletter">
					<input type="button" value="SUBMIT" onclick="submitNewsletter();">
				</form>
			</div>
			<div class="img-footer">
				<a href="http://powersporttechnologies.com"><img src="<?php echo $new_assets_url; ?>images/powered-logo.png"  class="powerlogo-a"/>	
			</div>
			<hr class="ftr-line">
		</div>
	</div>

<?php
$CI =& get_instance();
echo $CI->load->view("braintree", array(
        "store_name" =>	$store_name
), true);
?>

</body>
	<link type="text/css" rel="stylesheet" href="<?php echo $assets; ?>/css_front/style.css">
<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />

</html>
<script>

    //var script = document.createElement('script');
    //script.src = "https://seal.godaddy.com/getSeal?sealID=qc3WD7lClpbpLfFD7HDpLCx8bXBkOWSZP9ImCkgNS7VqSnVbHcLTJJrA6sG";
	//script.type = "text/javascript";
    //setTimeout(function() {
	//	document.getElementById("siteseal").innerHTML = script.outerHTML;
    //}, 2000);
	function submitNewsletter()
	{		
		 $.post(base_url + 'ajax/updateNewsletterList/',
			{ 
			 'email' : $('#newsletter').val(),
			 'ajax' : true
			},
			function()
			{
				$('#newsletter_success').show();
			});
	}
</script>
<script>
	if(window.location.href.indexOf('shopping/cart') != -1)
	{
		var id = new Array();
		var price = jQuery('.cart_total h3')[0].innerHTML.replace("Cart Total: $","");
		var len = jQuery('input[placeholder="Add Quanity"]').length;
		for(i=0;i<len;i++)
		{
			id.push(jQuery('input[placeholder="Add Quanity"]')[i].id);
		}
		var google_tag_params = {
			ecomm_prodid: id,
			ecomm_pagetype: 'cart',
			ecomm_totalvalue: price
		};
	}
</script>
<script>
	if(window.location.pathname == '/')
	{
		var google_tag_params = {
			ecomm_pagetype: 'home'
		};
	}
</script>
<script>
try {
	if(page == "category")
	{
		var google_tag_params = {
			ecomm_pagetype: 'category'
		};
	}

} catch (err) {
	console.log("Error in page category check: " + err);
}
	
</script>
<?php echo @$footerscript; ?>
<?php foreach($category as $id => $ref){
    $catd = $ref['label'];
}
?>
<script>
	var ctd = '<?php echo $catd; ?>';
	
        //  alert(ctd);

    <?php
    if (!isset($CI)) {
        $CI =& get_instance();
    }
    echo $CI->load->view("master/widgets/selector2_js");
    ?>
	
</script>

<script>
	var ct = '<?php echo $top_parent; ?>';

    <?php
    if (!isset($CI)) {
        $CI =& get_instance();
    }
    echo $CI->load->view("master/widgets/selector3_js");
    ?>

	
</script>
</noscript>
