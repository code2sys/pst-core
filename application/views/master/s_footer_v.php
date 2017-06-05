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
			<div class="one-fifth">
				<?php print_r($pages); ?>
				<h3>quick links</h3>
				<ul class="clear">
					<li><a href="<?php echo site_url('pages/index/shippingquestions');?>">Shipping  Questions</a></li>
					<li><a href="<?php echo site_url('pages/index/returnpolicy');?>">Return Policy</a></li>
					<li><a href="<?php echo site_url('pages/index/privacypolicy');?>">Privacy Policy</a></li>
					<li><a href="<?php echo site_url('pages/index/termsofservice');?>">Terms of Service</a></li>
					<li><a href="<?php echo site_url('pages/index/paymentoptions');?>">Payment Option</a></li>
				</ul>
			</div>
			<div class="one-fifth map">
				<h3>Contact Us</h3>
				<ul class="clear">
					<li>Address: <?php echo $store_name['street_address'].' '.$store_name['city'].' '.$store_name['state'];?></li>
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
				<?php if(@$SMSettings['sm_fblink']): ?>
				<a class="social" href="<?php echo @$SMSettings['sm_fblink']; ?>" target="_blank">
					<img src="<?php echo $new_assets_url; ?>images/f.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_twlink']): ?>
				<a class="social" href="<?php echo $SMSettings['sm_twlink']; ?>" target="_blank">
					<img src="<?php echo $new_assets_url; ?>images/t.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_ytlink']): ?>
				<a class="social" href="<?php echo $SMSettings['sm_ytlink']; ?>" target="_blank">
					<img src="<?php echo $new_assets_url; ?>images/youtube1.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_gplink']): ?>
				<a class="social" href="<?php echo $SMSettings['sm_gplink']; ?>" target="_blank">
					<img src="<?php echo $new_assets_url; ?>images/g+.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_insta']): ?>
				<a class="social" href="<?php echo $SMSettings['sm_insta']; ?>" target="_blank" style="color:#F00;">
					<img src="<?php echo $new_assets_url; ?>images/instragram.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
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
	
	if(ctd=='UTV PARTS'){
		$("#stp").removeClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').addClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ctd=='DIRT BIKE PARTS'){
		$("#stp").removeClass('actv');
		$('#sdp').addClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ctd=='STREET BIKE PARTS'){
		$("#stp").addClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ctd=='ATV PARTS'){
		$("#stp").removeClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').addClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}
	
</script>
<script>
	var ct = '<?php echo $top_parent; ?>';
	
	if(ct=='<?php echo TOP_LEVEL_CAT_UTV_PARTS; ?>'){
		$("#stp").removeClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').addClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ct== '<?php echo TOP_LEVEL_CAT_DIRT_BIKES; ?>'){
		$("#stp").removeClass('actv');
		$('#sdp').addClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ct=='<?php echo TOP_LEVEL_CAT_STREET_BIKES; ?>'){
		$("#stp").addClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ct=='<?php echo TOP_LEVEL_CAT_ATV_PARTS; ?>'){
		$("#stp").removeClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').addClass('actv');
		$('#sbb').removeClass('actv');
	}
	
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
