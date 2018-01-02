<script>
	$(document).ready(function() {
		$(".tp-cat-head").click(function(){
			$(this).toggleClass("active");
			$(this).next(".tlg").stop('true','true').slideToggle("slow");
		});
	});
</script>
<?php $new_assets_url = jsite_url("/qatesting/newassets/"); ?>

<div class="sw footer clear">
		<div class="container_b">
			<div class="one-fifth">
				<h3>About <span><?php echo $store_name['company'];?></span></h3>
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
				<a href="#"><img src="<?php echo $new_assets_url; ?>images/powered-logo.png"  class="powerlogo-a"/>	
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


<?php foreach($category as $id => $ref){ $catd = $ref['label'];} ?>
<script>
	var ctd = '<?php echo $catd; ?>';

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
</body>
	<link type="text/css" rel="stylesheet" href="<?php echo $s_assets; ?>/css_front/style.css">
<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />

</html>
<script>
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
