<!DOCTYPE html>
<html lang="en">
<head>
	
	<title><?php echo @$title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo @$descr; ?>">
	<meta name="keywords" content="<?php echo @$keywords; ?>">
	<meta name="author" content="Cloverfield Creations">
	
	
	<!-- CSS LINKS -->
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/style-checkout.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/flexisel.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/font-awesome-4.1.0/css/font-awesome.min.css">
	

	<!-- END CSS LINKS --> 
	
	<!-- jQuery library -->
	<script src="<?php echo $s_assets; ?>/js/jquery.min.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	  ga('create', 'UA-12591015-1', 'auto');
	  ga('send', 'pageview');
	</script>
	<script>
		var s_base_url = '<?php echo $s_baseURL; ?>';
		shopping_cart_count = <?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0; ?>;
	</script>
	
	<script>(function() {
	  var _fbq = window._fbq || (window._fbq = []);
	  if (!_fbq.loaded) {
	    var fbds = document.createElement('script');
	    fbds.async = true;
	    fbds.src = '//connect.facebook.net/en_US/fbds.js';
	    var s = document.getElementsByTagName('script')[0];
	    s.parentNode.insertBefore(fbds, s);
	    _fbq.loaded = true;
	  }
	  _fbq.push(['addPixelId', '761534680600095']);
	})();
	window._fbq = window._fbq || [];
	window._fbq.push(['track', 'PixelInitialized', {}]);
	</script>
	<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=761534680600095&amp;ev=PixelInitialized" /></noscript>

	
	
	<!-- Flexisel JS -->
	<script type="text/javascript" src="<?php echo $s_assets; ?>/js/jquery.flexisel.js"></script>


</head>

<body class="body">

<!-- WRAPPER ==============================================================================-->
<div class="wrap">
	
	
	<!-- HEADER =============================================================================-->
	<div class="header_wrap">
		
		<div class="header_content" style="padding-bottom:0px; margin-bottom:-30px;">
		
			<style>
			
			#desktop_cart{
				float: right;
				padding: 70px 70px 0px 0px;
			}
			#desktop_cart .phonee{
				padding: 10px !important;
				border-radius: 3px !important;
				text-shadow: 1px 1px #D1B9D5 !important;
				color: #663399 !important;
				-webkit-text-fill-color:#663399 !important;
			}
			#desktop_cart .cartt{
				padding: 10px !important;
				border-radius: 3px !important;
				cursor:pointer !important;
			}
			</style>
			<!-- LOGO -->
			<div class="logo">
				<img src="<?php echo $s_logo; ?>">
			</div>
			<div id="desktop_cart">
				<?php if(@$accountAddress['phone']){?>
				<strong class="phonee"><?php echo $accountAddress['phone']; ?></strong>
				<?php }?>
				<strong class="cartt" onClick="window.location='<?php echo base_url();?>shopping/cart'"><i class="fa fa-shopping-cart"></i> Cart (<?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0 ; ?>)</strong>
			</div>
			<!-- END LOGO -->
			
			<div class="clear"></div>
			
			<div id="mobile_cart" style="display:none;">
				<?php if(@$accountAddress['phone']){?>
				<strong class="phonee"><?php echo $accountAddress['phone']; ?></strong>
				<?php }?>
				<strong class="cartt" onClick="window.location='<?php echo base_url();?>shopping/cart'"><i class="fa fa-shopping-cart"></i> Cart (<?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0 ; ?>)</strong>
			</div>
			 
			
		</div>

	</div>
	<!-- END HEADER ===========================================================================-->	

	<?php  echo @$mainContent; ?>
			
	<?php echo @$sidebar; ?>				
		
		<div class="clear"></div>
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->


</div>
<!-- END WRAPPER ==========================================================================-->

<?php echo @$footer; ?>	

<?php // echo @$script; ?>

<!-- BEGIN: Google Trusted Stores -->
<script type="text/javascript">
  var gts = gts || [];

  gts.push(["id", "479602"]);
  gts.push(["badge_position", "BOTTOM_RIGHT"]);
  gts.push(["locale", "en_us"]);
  <?php if(@$product['partnumber']): ?>
  gts.push(["google_base_offer_id", "<?php echo $product['partnumber']; ?>"]);
  <?php endif; ?>
  gts.push(["google_base_subaccount_id", "1108548223"]);
  gts.push(["google_base_country", "US"]);
  gts.push(["google_base_language", "en_us"]);

  (function() {
    var gts = document.createElement("script");
    gts.type = "text/javascript";
    gts.async = true;
    gts.src = "https://www.googlecommerce.com/trustedstores/api/js";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(gts, s);
  })();
</script>
<!-- END: Google Trusted Stores -->

<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1052220103;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1052220103/?value=0&amp;guid=ON&amp;script=0"/>
</div>

</body>
</html>


















