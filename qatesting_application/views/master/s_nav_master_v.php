 <!DOCTYPE html>
<html lang="en">
<head>
	
	<title><?php echo @$title; ?></title>
    
    <?php
	$new_assets_url = jsite_url("/qatesting/newassets/");
	?>
    
    <link rel="icon" href="<?php echo $new_assets_url;?>favicon-2.ico" type="image/x-icon" sizes="16x16">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
	<meta name="viewport" content="user-scalable = yes">
	<meta name="description" content="<?php echo @$descr; ?>">
	<meta name="keywords" content="<?php echo @$keywords; ?>">
	<?php echo @$metatag; ?>
	
	
	<!-- CSS LINKS -->
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/style.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/account_nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/jquery.bxslider.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/magnific-popup.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/jquery.selectbox.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/flexisel.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/expand.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/modal.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/font-awesome-4.1.0/css/font-awesome.min.css">
	<meta name="msvalidate.01" content="EBE52F3C372A020CF12DD8D06A48F87C" />
	<?php echo @$css; ?>
	
	<!-- END CSS LINKS --> 
	
	<!-- jQuery library -->
	<script src="<?php echo $s_assets; ?>/js/jquery-1.7.2.js"></script>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	  ga('create', 'UA-12591015-1', 'auto');
	  ga('send', 'pageview');
	</script>
	
	<script type="text/javascript">
		adroll_adv_id = "AN6Z3ECBXVEMVLMGMFPUW3";
		adroll_pix_id = "7X6FPO4PO5ABNLM3SZUAVQ";
		(function () {
		var oldonload = window.onload;
		window.onload = function(){
		   __adroll_loaded=true;
		   var scr = document.createElement("script");
		   var host = "https://s.adroll.com";
		   scr.setAttribute('async', 'true');
		   scr.type = "text/javascript";
		   scr.src = host + "/j/roundtrip.js";
		   ((document.getElementsByTagName('head') || [null])[0] ||
		    document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
		   if(oldonload){oldonload()}};
		}());
		
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
		
		<?php echo @$brandSlider; ?>
		                  
		
	
	</div>
	<!-- END HEADER ===========================================================================-->	
	<?php }else{?>
		
		<link rel="stylesheet" href="<?php echo $new_assets_url;?>stylesheet/style.css" />
		<link rel="stylesheet" href="<?php echo $new_assets_url;?>stylesheet/custom.css" />
		
		<?php /*?><script type="text/javascript" src="<?php echo $new_assets_url;?>homepage_slider/js/DiagonalSlider.js"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $new_assets_url;?>homepage_slider/css/diagonalSlider2.css">
		<link rel="stylesheet" type="text/css" href="<?php echo $new_assets_url;?>homepage_slider/css/diagonalSlider1.css">
		<script type="text/javascript" src="<?php echo $new_assets_url;?>homepage_slider/js/script.js"></script><?php */?>
		
		<div class="topBar">
		<div class="containerOuter">
			<p class="creditCar fltL">Major Credit Cards Accepted!
			<?php if(@$accountAddress['phone']): ?>
				<span> Order By Phone! <?php echo $accountAddress['phone']; ?> </span>
			<?php endif; ?>
			Mon - Fri 8 - 5 EST</p>
			<div class="topHeaderNav">
				<ul>
					<li><a href="<?php echo base_url();?>" class="homeLink">Home</a></li>
					<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>" class="accountLink">Account</a></li>
					<li><a href="<?php echo base_url('/shopping/wishlist'); ?>" class="wishListLink">Wish List</a></li>
					<li><a href="<?php echo base_url('shopping/cart'); ?>" class="accountLink">Shopping Cart <span>(<?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0 ; ?>)</span></a></li>
				</ul>
			</div>
			<div class="loginSec">
				
				<?php if(@$_SESSION['userRecord']): ?>
				<b><a href="javascript:;" style="cursor:default; text-decoration:none;">Welcome: <?php echo @$_SESSION['userRecord']['first_name']; ?></a></b> | <b><a href="<?php echo $s_baseURL.'welcome/logout'; ?>"><u>Logout</u></a></b>
					<?php if($_SESSION['userRecord']['admin']): ?> |
						<a href="<?php echo base_url('admin'); ?>"><b><u>Admin Panel</u></b></a>
					<?php endif; ?>
				<?php else: ?>
					<a class="creatAcc ml10 fltR" href="javascript:void(0);" onClick="openCreateAccount();"><b><u>Create Account</u></b></a>
					<span class="fltR seperator">|</span>
					<a class="loginLink fltR mr10" href="javascript:void(0);" onClick="openLogin();"><b><u>Login</u></b></a>
				<?php endif; ?>
				
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
		<div class="header">
			<div class="containerOuter">
				<a href="<?php echo base_url();?>" class="logoCont fltL"><img src="<?php echo $new_assets_url;?>images/logo.png" width="250" height="auto" /></a>
				<div class="vehicleCategory">
					<a href="<?php echo base_url('dirtbikeparts'); ?>" class="bike">Dirt Bikes</a>
					<a href="<?php echo base_url('atvparts'); ?>" class="atv">ATV'S</a>
					<a href="<?php echo base_url('streetbikeparts'); ?>" class="streetBike">Street Bikes</a>
					<a href="<?php echo base_url('utvparts'); ?>" class="utv">UTV'S</a>
				</div>
				<div class="searchHolder">
					<form action="<?php echo $s_baseURL; ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
						<input id="search" name="search" placeholder="Search <?php echo WEBSITE_NAME; ?>" class="text medium_search" style="float:left;" />
						<a href="javascript:void(0);" class="goBtn" onClick="setSearch($('#search').val());">Go!</a>
					</form>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="imagesCont" style="display:flex;">

			<img src="<?php echo $new_assets_url;?>images/homepage_slider/banner.jpg" width="100%" alt="" usemap="#Map" />
			<map name="Map" id="Map">
				<area alt="UTV'S" title="UTV'S" href="<?php echo base_url('utvparts'); ?>" shape="poly" coords="1,2,421,2,298,299,0,299" style="text-decoration:none;" />
				<area alt="ATV'S" title="ATV'S" href="<?php echo base_url('atvparts'); ?>" shape="poly" coords="422,2,823,2,668,297,301,299" style="text-decoration:none;" />
				<area alt="Dirt Bikes" title="Dirt Bikes" href="<?php echo base_url('dirtbikeparts'); ?>" shape="poly" coords="826,1,1102,1,937,299,670,298" style="text-decoration:none;" />
				<area alt="Street Bikes" title="Street Bikes" href="<?php echo base_url('streetbikeparts'); ?>" shape="poly" coords="1106,1,1365,1,1365,297,940,298" style="text-decoration:none;" />
			</map>
			
			
			
        </div>
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
		<div class="filterBar">
			<div class="filterBarCont containerOuter">
				<h1>SHOP BY MACHINE</h1>
				<form action="<?php echo $s_baseURL; ?>ajax/update_garage" method="post" id="update_garage_form" class="form_standard">
                    <select class="selectField" name="machine" id="machine" tabindex="1">
                        <option value="">-- Select Machine --</option>
                        <?php if(@$machines): foreach($machines as $id => $label): ?>
                            <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
                        <?php endforeach; endif; ?>
                    <!-- <optgroup label="Motor Cycles"> -->
                    </select>
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
		</div>
		
		<?php echo str_replace("qatesting/index.php?/media/", "media/", @$brandSlider); ?>

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
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1052220103/?value=0&amp;guid=ON&amp;script=0"/>
</div>

</body>
</html>
