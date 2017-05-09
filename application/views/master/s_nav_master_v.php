<?php

/*
        JLB 04-06-17
        There was inconsistent usage so	these things stopped working. I	attempt	to standardize the variables for the meta tags and title.
 */
$page_title = "";
if (isset($title) && $title != "") {
        $page_title = $title;
} else if (isset($pageRec) && is_array($pageRec) && array_key_exists("title", $pageRec) && $pageRec["title"] != "") {
        $page_title = $pageRec["title"];
}

$meta_description = "";
if (isset($descr) && $descr != "") {
        $meta_description = $descr;
} else if (isset($pageRec) && is_array($pageRec) && array_key_exists("descr", $pageRec) && $pageRec["descr"] != "") {
        $meta_description = $pageRec["descr"];        
} else if (isset($pageRec) && is_array($pageRec) && array_key_exists("metatags", $pageRec) && $pageRec["metatags"] != "") {
        $meta_description = $pageRec["metatags"];
}

$meta_keywords = "";
if (isset($keywords) &&	$keywords != "") {
        $meta_keywords = $keywords;
} else if (isset($pageRec) & is_array($pageRec)	&& array_key_exists("keywords",	$pageRec) && $pageRec["keywords"] != "") {
        $meta_keywords = $pageRec["keywords"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>	

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php echo $page_title; ?></title>
    <?php
	$new_assets_url = jsite_url("/qatesting/newassets/");
	$new_assets_url1 = jsite_url("/qatesting/benz_assets/");
	?>
	<?php if (SEARCH_NOINDEX): ?>
		<meta name="robots" content="noindex" />
	<?php endif; ?>


	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
	<!--<meta name="viewport" content="user-scalable = yes">-->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="description" content="<?php print htmlentities($meta_description, ENT_QUOTES | ENT_COMPAT); ?>">
	<meta name="keywords" content="<?php echo htmlentities($meta_keywords, ENT_QUOTES | ENT_COMPAT);  ?>">
	<?php echo @$metatag; ?>
	
	
	<!-- CSS LINKS -->
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css_front/media.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css_front/style.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/style.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/benz.css" type="text/css"/>
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/account_nav.css" type="text/css"/>
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/jquery.bxslider.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/magnific-popup.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/jquery.selectbox.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/flexisel.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/expand.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/modal.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/font-awesome-4.1.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>/css/responsive.css" type="text/css" />
	
	<meta name="msvalidate.01" content="EBE52F3C372A020CF12DD8D06A48F87C" />
	<?php echo @$css; ?>
	
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/style.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.carousel.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.theme.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.transitions.css" />	
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/font-awesome.css" />	
	
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lucida+Sans:400,500,600,700,900,800,300" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Helvetica:400,500,600,700,900,800,300%22%20/%3E" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,500,600,700,900,800,300%22%20/%3E">
	
	<!-- END CSS LINKS --> 
	
	<!-- jQuery library -->
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script src="<?php echo $new_assets_url1; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo $new_assets_url1; ?>js/owl.carousel.js"></script>
	
	<script src="<?php echo $s_assets; ?>/js/jquery-1.7.2.js"></script>
<script>
	$(document).ready(function(){
		$(".sidebar-menu").click(function(){
			$(".mb-drpdwn").toggle('slow');
		});
	});
</script>
	
	<script>
		var base_url = '<?php echo base_url(); ?>';
		var s_base_url = '<?php echo $s_baseURL; ?>';
		shopping_cart_count = <?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0; ?>;
	</script>
	
	<!-- Magnific Popup core JS file -->
	<script src="<?php echo $s_assets; ?>/js/jquery.magnific-popup.js"></script>
	
	<!-- bxSlider Javascript file -->
	<script src="<?php echo $s_assets; ?>/js/jquery.bxslider.min.js"></script>
	<script>
		$(document).ready(function(){
			$('.bxslider').bxSlider({
  			auto: true,
  			pause: 5000,
  			randomStart: true
			});
		});
	</script>
	
	
	<!-- Flexisel JS -->
	<script type="text/javascript" src="<?php echo $s_assets; ?>/js/jquery.flexisel.js"></script>
	
	<!-- NAVIGATION JS -->
	<script>
	$( document ).ready( function( $ ) {
		
			$('body').addClass('js');
	
			$('.menu-link').click(function(e) {
				e.preventDefault();
				$('.menu-link').toggleClass('active');
				$('#menu').toggleClass('active');
			});
	
			$('.has-submenu > a').click(function(e) {
				e.preventDefault();
				$(this).toggleClass('active').next('ul').toggleClass('active');
			});
	});
	</script>
	<!-- END NAVIGATION JS -->
	
	
	<!-- ACCOUNT NAVIGATION JS -->
	<script>
	$( document ).ready( function( $ ) {
		
			$('body').addClass('js');
	
			$('.acct_menu-link').click(function(e) {
				e.preventDefault();
				$('.acct_menu-link').toggleClass('active');
				$('#acct_menu').toggleClass('active');
			});
	
			$('.has-submenu > a').click(function(e) {
				e.preventDefault();
				$(this).toggleClass('active').next('ul').toggleClass('active');
			});
	});
	</script>
	<!-- END ACCOUNT NAVIGATION JS -->
	
	
	<!-- SELECT BOX JS -->
	<script type="text/javascript" src="<?php echo $s_assets; ?>/js/jquery.selectbox-0.2.js"></script>

	<!-- END SELECT BOX JS -->

	
	<!-- ACCORDI0N JS -->
	<script type="text/javascript">
		$(document).ready(function()
		{
			$(".toggle_container").hide();

			$("h4.expand_heading").show(function()
				{
					$(this).addClass("active"); 
				}, 
				function () 
				{
					$(this).removeClass("active");
				}
			);

			$("h4.expand_heading").click(function(){
				$(this).next(".toggle_container").slideToggle("fast");
			});
			$(".expand_all").toggle(function(){
				$(this).addClass("expanded"); 
				}, function () {
				$(this).removeClass("expanded");
			});
			$(".expand_all").click(function(){
				$(".toggle_container").slideToggle("fast");
			});

		});
	</script>

<!-- 	POPUP JS -->
<script src="<?php echo $s_assets; ?>/js/jquery.simplemodal.js"></script>
<script src="<?php echo $s_assets; ?>/js/custom.js"></script>
<!-- END POPUP JS -->

<?php echo @$header; ?>
	<link rel="stylesheet" href="<?php echo jsite_url("/basebranding.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />

</head>

<body class="body" <?php if(isset($new_header)){?>style="width:100%;margin:0 auto;"<?php }?>>

<!-- WRAPPER ==============================================================================-->
<div class="wrap">
	
    <?php
	if(!isset($new_header)){?>
	<!-- HEADER =============================================================================-->
	<div class="header_wrap">
		
		<div class="header_content">
		
			
			<!-- LOGO -->
			<div class="logo">
				<a href="<?php echo $s_base_url; ?>"><img src="<?php echo $s_logo; ?>"></a>
			</div>
			<!-- END LOGO -->
			
			<!-- NAVAGATION -->
				<?php echo @$nav ?>
			<!-- END NAVAGATION -->
			<div class="clear"></div> 
		
		</div>
		
		
		<!-- MOTO MENU & SEARCH -->
		<div class="moto_menu_wrap">
			<div class="moto_menu">
				<h4>SHOP BY MACHINE</h4>
				
				<div class="moto_links" style="line-height: 1;">
					<a href="<?php echo base_url('dirtbikeparts'); ?>"><img src="<?php echo $s_assets; ?>/images/icon_dirtbike.png" border="0" width="55"><br>Dirt Bikes</a>
					<a href="<?php echo base_url('atvparts'); ?>"><img src="<?php echo $s_assets; ?>/images/icon_atv.png" border="0" width="55"><br>ATV's</a>
					<a href="<?php echo base_url('streetbikeparts'); ?>"><img src="<?php echo $s_assets; ?>/images/icon_streetbike.png" border="0" width="55"><br>Street Bikes</a>
					<a href="<?php echo base_url('utvparts'); ?>"><img src="<?php echo $s_assets; ?>/images/icon_utv.png" border="0" width="55"><br>UTV's</a>
				</div>
				<div class="moto_search">
					<form action="<?php echo $s_baseURL; ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
						<input id="search" name="search" placeholder="Search <?php echo WEBSITE_NAME; ?>" class="text medium_search" />
						<a href="javascript:void(0);" class="button" style="margin-top:6px;" onClick="setSearch($('#search').val());">Go!</a>
					</form>
				</div>
				
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
		
		<!-- END MOTO MENU & SEARCH -->
		
		
		<?php echo @$rideSelector; ?>
		
		<?php echo @$shippingBar; ?>
		
		<?php //echo @$brandSlider; ?>
		                  
		
	
	</div>
	<!-- END HEADER ===========================================================================-->	
	<?php }else{?>
		
		<link rel="stylesheet" href="<?php echo $new_assets_url;?>stylesheet/style.css" />
		<link rel="stylesheet" href="<?php echo $new_assets_url;?>stylesheet/custom.css" />
		

	<div class="topBar_b">
		<div class="container_b">
			<p class="creditCar_b fltL_b">
				<span>Ph : <?php echo SUPPORT_PHONE_NUMBER; ?></span>
				<a href="<?php echo site_url('pages/index/contactus') ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> MAP & HOURS</a>				
			</p>			
			<div class="loginSec_b navbar-right">
				<?php if(@$_SESSION['userRecord']): ?>
					<b>Welcome: <?php echo @$_SESSION['userRecord']['first_name']; ?></b> <span class="fltR seperator_b">|</span> <b><a href="<?php echo $s_baseURL.'welcome/logout'; ?>"><u>Logout</u></a></b>
					<?php if($_SESSION['userRecord']['admin']): ?> <span class="fltR seperator_b">|</span>
					<a href="<?php echo base_url('admin'); ?>"><b><u>Admin Panel</u></b></a>
					<?php endif; ?>
				<?php else: ?>
					<a class="loginLink_b fltR mr10" href="javascript:void(0);" onclick="openLogin();"><b><u>Login</u></b></a>
					<span class="fltR seperator_b">|</span>
					<a class="creatAcc ml10 fltR" href="javascript:void(0);" onclick="openCreateAccount();"><b><u>Create Account</u></b></a>
				<?php endif; ?>
				<div class="clear"></div>
			</div>
			<div class="topHeaderNav_b pull-right">
				<ul>
					<li class="icon homeLink"><a href="<?php echo base_url(); ?>">Home</a></li>
					<li class="icon accountLink"><a href="<?php echo $s_baseURL.'checkout/account'; ?>">Account</a></li>
					<li class="icon wishListLink"><a href="<?php echo base_url('/shopping/wishlist'); ?>">Wish List</a></li>
					<li class="icon shopLink"><a href="<?php echo base_url('shopping/cart'); ?>">Shopping Cart (<span id="shopping_count"><?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0 ; ?></span>)</a></li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<!--<div class="header_b">
		<div class="container_b">
			<a href="<?php echo base_url();?>" class="logoCont fltL logo-tp_b">
				<img src="/logo.png" width="200" height="50">
			</a>
			<div class="vehicleCategory">
				<a href="<?php echo base_url('streetbikeparts'); ?>" class="streetBike stre-bk_b">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/streetBike.png">
					</div>
					<span>Shop Street Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('vtwin'); ?>" class="vtwin">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/vtwin.png">
					</div>
					<span id="svp">Shop VTwin Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('dirtbikeparts'); ?>" class="bike">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/bike.png">
					</div>
					<span>Shop Dirt Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('atvparts'); ?>" class="atv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/atv.png">
					</div>
					<span>Shop ATV Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('utvparts'); ?>" class="utv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/utv.png">
					</div>
					<span>Shop UTV Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('Motorcycle_Gear_Brands'); ?>" class="last">
					<div class="stre-bk_b" style="height:42px;">
						<img src="<?php echo $new_assets_url1; ?>images/brand-tag.png">
					</div>
					<span>Shop by Brand</span>
				</a>
			</div>			
			<div class="side-hdr">
				<div class="sidebar-menu">
					<span> <i class="fa fa-bars" aria-hidden="true"></i> Menu</span>
					<ul class="mb-drpdwn">
						<li><a href="<?php echo base_url('streetbikeparts'); ?>">Shop Street</a></li>
						<li><a href="<?php echo base_url('vtwin'); ?>">Shop VTwin</a></li>
						<li><a href="<?php echo base_url('dirtbikeparts'); ?>">Shop Dirt</a></li>
						<li><a href="<?php echo base_url('atvparts'); ?>">Shop ATV</a></li>				
						<li><a href="<?php echo base_url('utvparts'); ?>">Shop UTV</a></li>
						<li><a href=<?php echo base_url('Motorcycle_Gear_Brands'); ?>>Shop by Brand</a></li>				
						<li><a href="<?php echo base_url('/shopping/wishlist'); ?>">Wish list</a></li>
						<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>">Account</a></li>
						<li><a href="#">Login/Signup</a></li>
					</ul>
				</div>		
				<div class="cl"><a href="tel:<?php echo CLEAN_PHONE_NUMBER; ?>">
					<img src="<?php echo $new_assets_url1; ?>images/cl.png"><br>Call</a>
				</div>
				<div class="crt">
					<a href="<?php echo base_url('shopping/cart'); ?>">
					<img src="<?php echo $new_assets_url1; ?>images/kart.png"><br>Cart</a>
				</div>
				<div class="shpbrnd-map">
					<p class="creditCar_b loct">				
						<a href="<?php echo site_url('pages/index/contactus') ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> MAP & HOURS</a>				
					</p>
				</div>
			</div>
			<div class="mblacnt-log">
				<a href="#"> <i class="fa fa-user usr" aria-hidden="true"></i> Login/create account</a>
			</div>
			<div class="searchHolder search-one">
				<form action="<?php echo base_url(); ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
					<input id="search" name="search" placeholder="Search Parts and Apparel" class="search-bx" style="float:left;" />
					<a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
				</form>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>						
		</div>
	</div>-->
        
        <div class="header_b">
		<div class="container_b">
			<a href="<?php echo base_url();?>" class="logoCont fltL logo-tp_b">
				<img src="/assets/images/power-sports-logo.png" width="200" height="50">
			</a>
			<!--<div class="vehicleCategory">
				<a href="<?php echo base_url('streetbikeparts'); ?>" class="streetBike stre-bk_b">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/streetBike.png">
					</div>
					<span id="stp">Shop Street Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('vtwin'); ?>" class="vtwin">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/vtwin.png">
					</div>
					<span id="svp">Shop VTwin Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('dirtbikeparts'); ?>" class="bike">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/bike.png">
					</div>
					<span id="sdp">Shop Dirt Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('atvparts'); ?>" class="atv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/atv.png">
					</div>
					<span id="sap">Shop ATV Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('utvparts'); ?>" class="utv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/utv.png">
					</div>
					<span id="sup">Shop UTV Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('Motorcycle_Gear_Brands'); ?>" class="last">
					<div class="stre-bk_b" style="height:42px;">
						<img src="<?php echo $new_assets_url1; ?>images/brand-tag.png">
					</div>
					<span id="sbb">Shop by Brand</span>
				</a>
			</div>-->
			<div class="side-hdr">
				<div class="sidebar-menu">
					<span> <i class="fa fa-bars" aria-hidden="true"></i> Menu</span>
					<ul class="mb-drpdwn">
						<li><a href="<?php echo base_url('streetbikeparts'); ?>">Shop Street</a></li>
						<li><a href="<?php echo base_url('vtwin'); ?>">Shop VTwin</a></li>
						<li><a href="<?php echo base_url('dirtbikeparts'); ?>">Shop Dirt</a></li>
						<li><a href="<?php echo base_url('atvparts'); ?>">Shop ATV</a></li>				
						<li><a href="<?php echo base_url('utvparts'); ?>">Shop UTV</a></li>
						<li><a href=<?php echo base_url('Motorcycle_Gear_Brands'); ?>>Shop by Brand</a></li>				
						<li><a href="<?php echo base_url('/shopping/wishlist'); ?>">Wish list</a></li>
						<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>">Account</a></li>
						<li><a href="javascript:void(0);" onclick="openLogin();">Login/Signup</a></li>
					</ul>
				</div>		
				<div class="cl"><a class="cel" href="tel:<?php echo CLEAN_PHONE_NUMBER; ?>">
					<img class="cl-img" src="<?php echo $new_assets_url1; ?>images/cl.png"><br>Call</a>
				</div>
				<div class="crt">
					<a class="cel" href="<?php echo base_url('shopping/cart'); ?>">
					<img class="cl-img" src="<?php echo $new_assets_url1; ?>images/kart.png"><br>Cart</a>
				</div>
				<div class="shpbrnd-map">
					<p class="creditCar_b loct">				
						<a href="<?php echo site_url('pages/index/contactus') ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> MAP & HOURS</a>				
					</p>
				</div>
			</div>
			<div class="mblacnt-log">
				<a href="javascript:void(0);" onclick="openLogin();"> <i class="fa fa-user usr" aria-hidden="true"></i> Login/create account</a>
			</div>	
			<div class="searchHolder search-one">
				<form action="<?php echo base_url(); ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
					<input id="search" name="search" placeholder="Search Parts and Apparel" class="search-bx" style="float:left;" />
					<a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
				</form>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>						
		</div>
            <div class="container_b">
			<div class="vehicleCategory">
				<a href="<?php echo base_url('streetbikeparts'); ?>" class="streetBike stre-bk_b">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/streetBike.png">
					</div>
					<span id="stp">Shop Street Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('vtwin'); ?>" class="vtwin">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/vtwin.png">
					</div>
					<span id="svp">Shop VTwin Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('dirtbikeparts'); ?>" class="bike">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/bike.png">
					</div>
					<span id="sdp">Shop Dirt Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('atvparts'); ?>" class="atv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/atv.png">
					</div>
					<span id="sap">Shop ATV Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('utvparts'); ?>" class="utv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url1; ?>images/utv.png">
					</div>
					<span id="sup">Shop UTV Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('Motorcycle_Gear_Brands'); ?>" class="last">
					<div class="stre-bk_b" style="height:45px;">
						<img src="<?php echo $new_assets_url1; ?>images/brand-tag.png">
					</div>
					<span id="sbb">Shop by Brand</span>
				</a>
			</div>	
			<div class="clear"></div>
		</div>
	</div>
	
	<!--<div class="searchHolder search-two">
		<form action="<?php //echo base_url(); ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
			<input id="search" name="search" placeholder="Search Parts and Apparel" class="search-bx" style="float:left;" />
			<a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
		</form>
		<div class="clear"></div>
	</div>-->
	
		<!--<div class="imagesCont" style="display:flex;">

			<img src="<?php echo $new_assets_url;?>images/homepage_slider/banner.jpg" width="100%" alt="" usemap="#Map" />
			<map name="Map" id="Map">
				<area alt="UTV'S" title="UTV'S" href="<?php echo base_url('utvparts'); ?>" shape="poly" coords="1,2,421,2,298,299,0,299" style="text-decoration:none;" />
				<area alt="ATV'S" title="ATV'S" href="<?php echo base_url('atvparts'); ?>" shape="poly" coords="422,2,823,2,668,297,301,299" style="text-decoration:none;" />
				<area alt="Dirt Bikes" title="Dirt Bikes" href="<?php echo base_url('dirtbikeparts'); ?>" shape="poly" coords="826,1,1102,1,937,299,670,298" style="text-decoration:none;" />
				<area alt="Street Bikes" title="Street Bikes" href="<?php echo base_url('streetbikeparts'); ?>" shape="poly" coords="1106,1,1365,1,1365,297,940,298" style="text-decoration:none;" />
			</map>
			
			
			
        </div>-->
		<div class="clear"></div>
		
		<!--<div class="productNav">
	        	<div class="productNavCont">
    	<ul>        	
        	<li><a class="topNavAnchors" href="javascript:;" onclick="showSubNav(1);">Dirt Bike Parts</a><span>|</span>
            	<ul id="nav1" class="active SubNavs" style="display:none;">
					<span class="toolTip"></span>
					<li><a href="javascript:;">Sub Category 1</a></li>
					<li><a href="javascript:;">Sub Category 2</a></li>
					<li><a href="javascript:;">Sub Category 3</a></li>
					<li><a href="javascript:;">Sub Category 4</a></li>
            	</ul>
            </li>
            <li><a class="topNavAnchors" href="javascript:;" onclick="showSubNav(2);">Chemicals & Oils</a><span>|</span>
				<ul id="nav2" class="active SubNavs" style="display:none;">
					<span class="toolTip"></span>
					<li><a href="javascript:;">Sub Category 1</a></li>
					<li><a href="javascript:;">Sub Category 2</a></li>
					<li><a href="javascript:;">Sub Category 3</a></li>
					<li><a href="javascript:;">Sub Category 4</a></li>
            	</ul>
            </li>
            <li><a class="topNavAnchors" href="javascript:;" onclick="showSubNav(3);">Casual Apparel</a><span>|</span>
            	<ul id="nav3" class="active SubNavs" style="display:none;">
					<span class="toolTip"></span>
					<li><a href="javascript:;">Sub Category 1</a></li>
					<li><a href="javascript:;">Sub Category 2</a></li>
					<li><a href="javascript:;">Sub Category 3</a></li>
					<li><a href="javascript:;">Sub Category 4</a></li>
            	</ul>
			</li>
            <li><a class="topNavAnchors" href="javascript:;" onclick="showSubNav(4);">Helmets & Accessories</a><span>|</span>
            	<ul id="nav4" class="active SubNavs" style="display:none;">
					<span class="toolTip"></span>
					<li><a href="javascript:;">Sub Category 1</a></li>
					<li><a href="javascript:;">Sub Category 2</a></li>
					<li><a href="javascript:;">Sub Category 3</a></li>
					<li><a href="javascript:;">Sub Category 4</a></li>
            	</ul>
            </li>
            <li style="display:inline-block">
				<a class="topNavAnchors" href="javascript:;" onclick="showSubNav(5);">Riding Gear</a><span>|</span>
            	<ul id="nav5" class="active SubNavs" style="display:none;">
					<span class="toolTip"></span>
					<li><a href="javascript:;">Sub Category 1</a></li>
					<li><a href="javascript:;">Sub Category 2</a></li>
					<li><a href="javascript:;">Sub Category 3</a></li>
					<li><a href="javascript:;">Sub Category 4</a></li>
            	</ul>
			</li>
            <li style="display:inline-block">
				<a class="topNavAnchors" href="javascript:;" onclick="showSubNav(6);">Tools Trailers & Stands</a>
				<ul id="nav6" class="active SubNavs" style="display:none;">
					<span class="toolTip"></span>
					<li><a href="javascript:;">Sub Category 1</a></li>
					<li><a href="javascript:;">Sub Category 2</a></li>
					<li><a href="javascript:;">Sub Category 3</a></li>
					<li><a href="javascript:;">Sub Category 4</a></li>
            	</ul>
			</li>
        </ul>        
	</div>
		</div>-->
		<!--<div class="filterBar shphd">
			<div class="filterBarCont containerOuter">
				<h1>SHOP BY MACHINE</h1>
				<form action="<?php echo $s_baseURL; ?>ajax/update_garage" method="post" id="update_garage_form" class="form_standard">
                    <select class="selectField" name="machine" id="machine" tabindex="1">
                        <option value="">-- Select Machine --</option>
                        <?php if(@$machines): foreach($machines as $id => $label): ?>
                            <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
                        <?php endforeach; endif; ?>
                    <!-- <optgroup label="Motor Cycles"> -->
                    <!--</select>
                    <select name="make" id="make" tabindex="2" class="selectField">
                        <option>-Make-</option>
                    </select>
                    <select name="model" id="model" tabindex="3" class="selectField">
                        <option>-Model-</option>
                    </select>
                    <select name="year" id="year" tabindex="4" class="selectField">
                        <option>-Year-</option>
                    </select>
					<a href="javascript:void(0);" onClick="updateGarage();" id="add" class="addToCat" style="padding:6px 13px;">Add To Garage</a>
                    </form>
				<div class="clear"></div>
			</div>
		</div>
		<div class="freeShippingBanner">
			<div class="containerOuter">
				<h1>FREE SHIPPING !!!</h1>
				<div class="moreInfoArrow">ON ALL ORDERS OVER $65 IN THE U.S.! <a href="<?php echo base_url();?>pages/index/shippingquestions"> CLICK FOR MORE INFO!</a></div>
				<div class="greenMap"></div>
				<div class="clear"></div>
			</div>
		</div>-->
		
		<?php //echo str_replace("qatesting/index.php?/media/", "media/", @$brandSlider); ?>

	<?php }?>
    
	<!-- CONTENT WRAP =========================================================================-->
	
	
		<?php  echo @$mainContent; ?>			
		
	
	
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->


</div>
<!-- END WRAPPER ==========================================================================-->

<?php echo @$footer; ?>	




<script>


function openLogin()
{
	window.location.replace('<?php echo $s_baseURL.'checkout/account'; ?>');
	/*
$.post(s_base_url + 'welcome/load_login/', {}, function(returnData)
	{
		$.modal(returnData);
		$('#simplemodal-container').height('auto').width('auto');
		$(window).resize();
	});
*/
}
	
function openCreateAccount()
{
	window.location.replace('<?php echo $s_baseURL.'checkout/account'; ?>');
	/*
$.post(s_base_url + 'welcome/load_new_user/', {}, function(returnData)
	{
		$.modal(returnData);
		$('#simplemodal-container').height('auto').width('auto');
	  	$('#create_new').show();
	  	$('#login').hide();
	  	$(window).resize();
	});
*/
}
$(document).ready(function() {
	
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
 		$("body").css("display","table");
	}
	
	$('.popup-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: false,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
			titleSrc: function(item) {
				return item.el.attr('title') + '<small><?php echo WEBSITE_NAME; ?>&trade;</small>';
			}
		}
	});
});
</script>

<script type="text/javascript">

$(window).load(function() {
    $("#flexiselDemo1").flexisel();
    $("#flexiselDemo2").flexisel({
        enableResponsiveBreakpoints: true,
        responsiveBreakpoints: { 
            portrait: { 
                changePoint:480,
                visibleItems: 1
            }, 
            landscape: { 
                changePoint:640,
                visibleItems: 2
            },
            tablet: { 
                changePoint:768,
                visibleItems: 3
            }
        }
    });

    $("#flexiselDemo3").flexisel({
        visibleItems: 5,
        animationSpeed: 1000,
        autoPlay: true,
        autoPlaySpeed: 3000,            
        pauseOnHover: true,
        enableResponsiveBreakpoints: true,
        responsiveBreakpoints: { 
            portrait: { 
                changePoint:480,
                visibleItems: 3
            }, 
            landscape: { 
                changePoint:640,
                visibleItems: 4
            },
            tablet: { 
                changePoint:768,
                visibleItems: 5
            }
        }
    });

    $("#flexiselDemo4").flexisel({
        clone:false
    });
    
});

  /* Submit on Enter */
  $(document).ready(function(){
    $('#search').keydown(function(e){
      if(e.keyCode == 13)
      {
	      e.preventDefault();
		  setSearch($('#search').val());
		  return false;
      }
    });
   });
   
   function setMainSearch(event, section, id)
   {
	   event.preventDefault();
	   $.post(s_base_url + 'ajax/setSearch/',
		{
			'ajax' : true,
			'section' : section,
			'id': id
		},
		function(newURL)
		{
			window.location.href = s_base_url + 'shopping/productlist' + newURL;
		});
   }
   
   function setNamedSearch(event, section, id, name)
   {
	   event.preventDefault();
	   $.post(s_base_url + 'ajax/setSearch/',
		{
			'ajax' : true,
			'section' : section,
			'name' : name,
			'id': id
		},
		function(newURL)
		{
			window.location.href = s_base_url + 'shopping/productlist' + newURL;
		});
   }
   
    function setSearch(search)
   {
	   $.post(s_base_url + 'ajax/setSearch/',
		{
			'ajax' : true,
			'section' : 'search',
			'name' : search,
			'id': 1
		},
		function(newURL)
		{
			window.location.href = s_base_url + 'shopping/productlist' + newURL;
		});
   }
   
   function removeMainSearch(section, id)
   {
	   $.post(s_base_url + 'ajax/removeSearch/',
		{
			'ajax' : true,
			'section' : section,
			'id': id
		},
		function(newURL)
		{
			window.location.href = s_base_url + 'shopping/productlist' + newURL;
		});	
   }
</script>



<?php echo @$script; ?>

<!-- BEGIN: Google Trusted Stores -->
<?php

$CI =& get_instance();
echo $CI->load->view("master/tracking", array(
	"store_name" => $store_name,
	"product" => @$product,
	"ga_ecommerce" => false,
	"show_ga_conversion" => true

), true);

?>
<script>
	executeMachine();
	executeMake();
	executeModel();
	executeYear();
	
	function executeMachine() {
		$("#machine").selectbox({
			onChange: function (val, inst) 
			{
				if(val != '')
				{
					$.ajax(
					{
				        async: false,
				        type: 'POST',
				        url: s_base_url + 'ajax/getMake/',
				        data : {'machineId' :  val,
									<?php if(@$product['part_id']): ?>
									'partId' : '<?php echo $product['part_id']; ?>',
									<?php endif; ?> 
									 'ajax' : true
									},
						success: function(encodeResponse)
						{
							responseData = JSON.parse(encodeResponse);
							$('#make').selectbox("detach");
							var mySelect = $('#make');
							mySelect.html($('<option></option>').val('').html('-- Select Make --'));
							$.each(responseData, function(val, text) {
							    mySelect.append(
							        $('<option></option>').val(val).html(text)
							    );
							});
							executeMake();
							$('#make').selectbox("attach");
						}
					});
				}
				else
				{
					$('#make').selectbox("detach");
					$('#make').html($('<option></option>').val('').html('-- Make --'));
					executeMake();
					$('#make').selectbox("attach");
				}
				$('#model').selectbox("detach");
				$('#year').selectbox("detach");
				$('#model').html($('<option></option>').val('').html('-- Model --'));
				executeModel();
				$('#model').selectbox("attach");
				$('#year').html($('<option></option>').val('').html('-- Year --'));
				executeYear();
				$('#year').selectbox("attach");
				$('#add').attr('class', 'button_no' );
				
			}
		});
	}
	
		function executeMake() {
		$("#make").selectbox({
			onChange: function (val, inst) 
			{
				if(val != '')
				{
					$.ajax(
					{
				        async: false,
				        type: 'POST',
				        url: s_base_url + 'ajax/getmodel/',
				        data : {'makeId' :  val,
									<?php if(@$product['part_id']): ?>
									'partId' : '<?php echo $product['part_id']; ?>',
									<?php endif; ?> 
									 'ajax' : true
									},
						success: function(encodeResponse)
						{
							responseData = JSON.parse(encodeResponse);
							$('#model').selectbox("detach");
							var mySelect = $('#model');
							mySelect.html($('<option></option>').val('').html('-- Select Model --'));
							$.each(responseData, function(val, text) {
							    mySelect.append(
							        $('<option></option>').val(val).html(text)
							    );
							});
							executeModel();
							$('#model').selectbox("attach");
						}
					});
				}
				else
				{
					$('#model').selectbox("detach");
					$('#model').html($('<option></option>').val('').html('-- Model --'));
					executeModel();
					$('#model').selectbox("attach");
				}
				$('#year').selectbox("detach");
				$('#year').html($('<option></option>').val('').html('-- Year --'));
				executeYear();
				$('#year').selectbox("attach");
				$('#add').attr('class', 'button_no' );
				
			}
		});
	}
	
	function executeModel() {
		$("#model").selectbox({
			onChange: function (val, inst) 
			{
				if(val != '')
				{
					$.ajax(
					{
				        async: false,
				        type: 'POST',
				        url: s_base_url + 'ajax/getYear/',
				        data : {'modelId' :  val,
									<?php if(@$product['part_id']): ?>
									'partId' : '<?php echo $product['part_id']; ?>',
									<?php endif; ?> 
									 'ajax' : true
									},
						success: function(encodeResponse)
						{
							responseData = JSON.parse(encodeResponse);

							var arr = [];
							
							for(var x in responseData){
							  arr.push(responseData[x]);
							}
									
							arr.sort(function(a, b){return b-a});
							$('#year').selectbox("detach");
							var mySelect = $('#year');
							mySelect.html($('<option></option>').val('').html('-- Select Year --'));
							$.each(arr, function(val, text) {
							    mySelect.append(
							        $('<option></option>').val(text).html(text)
							    );



							});
							executeYear();
							$('#year').selectbox("attach");
						}
					});
				}
				else
				{
					$('#year').selectbox("detach");
					$('#year').html($('<option></option>').val('').html('-- Year --'));
					executeYear();
					$('#year').selectbox("attach");
				}
					$('#add').attr('class', 'button_no' );
				
			}
		});
	}
	
	function executeYear()
	{
		$("#year").selectbox({
			onChange: function (val, inst) 
			{
				displayAdd(val);
			}
		});
	}

	
	function displayAdd(val)
	{
		if(val != '')
			$('#add').attr('class', 'button' );
		else
			$('#add').attr('class', 'button_no' );
	}
	
	function updateGarage()
	{
		var pathname = window.location.pathname;
		$('#update_garage_form').append('<input type="hidden" name="url" value="'+pathname +'" />');
		$('#update_garage_form').submit();
		
	}
</script>
<script type="application/javascript" src="<?php echo jsite_url('/custom.js'); ?>" ></script>
</body>
</html>
