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
if (isset($keywords) && $keywords != "") {
    $meta_keywords = $keywords;
} else if (isset($pageRec) & is_array($pageRec) && array_key_exists("keywords", $pageRec) && $pageRec["keywords"] != "") {
    $meta_keywords = $pageRec["keywords"];
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $page_title; ?></title>
<?php if (SEARCH_NOINDEX): ?>
            <meta name="robots" content="noindex" />
        <?php endif; ?>

        <?php
        $new_assets_url = jsite_url("/qatesting/newassets/");
        $new_assets_url1 = jsite_url("/qatesting/benz_assets/");

        if (@$ssl) {
            $assets = $s_assets;
        }
        ?>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">	
        <link rel="icon" href="http://www.yoursite.com/favicon.ico?v=2" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
        <!--<meta name="viewport" content="user-scalable = yes">-->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <meta name="description" content="<?php print htmlentities($meta_description, ENT_QUOTES | ENT_COMPAT); ?>">
        <meta name="keywords" content="<?php echo htmlentities($meta_keywords, ENT_QUOTES | ENT_COMPAT); ?>">

        <script src="https://code.jquery.com/jquery-2.1.1.js"></script>
        <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
        <script src="https://js.braintreegateway.com/js/braintree-2.30.0.min.js"></script>


        <script src="https://js.braintreegateway.com/web/3.6.3/js/client.min.js"></script>
        <script src="https://js.braintreegateway.com/web/3.6.3/js/paypal.min.js"></script>

        <!-- CSS LINKS -->		
        <link rel="stylesheet" href="<?php echo $assets; ?>/css_front/media.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/nav.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/style.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>/css/responsive.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/benz.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/account_nav.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/jquery.bxslider.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/magnific-popup.css" type="text/css">
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/jquery.selectbox.css" type="text/css">
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/expand.css" type="text/css">
        <link rel="stylesheet" href="<?php echo $assets; ?>/css/modal.css" type="text/css">
        <link rel="stylesheet" href="<?php echo $assets; ?>/font-awesome-4.1.0/css/font-awesome.min.css">
        <meta name="msvalidate.01" content="EBE52F3C372A020CF12DD8D06A48F87C" />
<?php echo @$css; ?>
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/style.css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.carousel.css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.theme.css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.transitions.css" />	
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/font-awesome.css" />

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,500,600,700,900,800,300%22%20/%3E">



        <!-- END CSS LINKS --> 

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                $("li").click(function(){
                    $(this).toggleClass("active");
                    $(this).next(".tlg").stop('true','true').slideToggle("slow");
                });
            });
            function setNamedSearch(event, section, id, name)
            {
                event.preventDefault();
                $.post(base_url + 'ajax/setSearch/',
                {
                    'ajax' : true,
                    'section' : section,
                    'name' : name,
                    'id': id
                },
                function(newURL)
                {
                    window.location.href = base_url + 'shopping/productlist' + newURL;
                });
            }
        </script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script src="<?php echo $new_assets_url1; ?>js/bootstrap.min.js"></script>
        <script src="<?php echo $new_assets_url1; ?>js/owl.carousel.js"></script>
        <!-- jQuery library -->
        <script src="<?php echo $assets; ?>/js/jquery-1.7.2.js"></script>
<?php echo $topscript; ?>

        <script>
            $(document).ready(function(){
                $(".sidebar-menu").click(function(){
                    $(".mb-drpdwn").toggle('slow');
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $(".fiter-menu").click(function(){
                    $(this).toggleClass("active");
                    $(this).next(".section-fiter").stop('true','true').slideToggle("slow");
                });
            });
        </script>


        <script>
            var environment = '<?php echo ENVIRONMENT; ?>';
            var base_url = '<?php echo base_url(); ?>';
            var s_base_url = '<?php echo $s_baseURL; ?>';
            shopping_cart_count = <?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0; ?>;
        </script>

        <!-- Magnific Popup core JS file 
        <script src="<?php echo $assets; ?>/js/jquery.magnific-popup.js"></script>-->

        <!-- bxSlider Javascript file -->
        <script src="<?php echo $assets; ?>/js/jquery.bxslider.min.js"></script>
        <script>
            $(document).ready(function(){
                $('.bxslider').bxSlider({
                    auto: true,
                    pause: 5000,
                    randomStart: true
                });
            });
        </script>


        <!-- Flexisel JS 
        <script type="text/javascript" src="<?php echo $assets; ?>/js/jquery.flexisel.js"></script>-->

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
        <script type="text/javascript" src="<?php echo $assets; ?>/js/jquery.selectbox-0.2.js"></script>
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

        <!-- 	POPUP JS 
        <script src="<?php echo $assets; ?>/js/jquery.simplemodal.js"></script>
        <script src="<?php echo $assets; ?>/js/custom.js"></script>
        <!-- END POPUP JS -->

<?php echo @$header; ?>



        <link rel="stylesheet" href="<?php echo jsite_url("/basebranding.css"); ?>" />
        <link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />
    </head>

    <body class="body" <?php if (isset($new_header)) { ?>style="width:100%;margin:0 auto;"<?php } ?>>

        <!-- WRAPPER ==============================================================================-->
        <div class="wrap">
<?php if (!isset($new_header)) { ?>
                <!-- HEADER =============================================================================-->
                <!--<div class="header_wrap">
                        <div class="header_content">
                <!-- LOGO -->
                <!--<div class="logo">
                        <a href="<?php echo base_url(); ?>"><img src="<?php echo $logo; ?>"></a>
                </div>
                <!-- END LOGO -->

                <!-- NAVAGATION -->
    <?php echo @$nav ?>
                <!-- END NAVAGATION -->
                <!--<div class="clear"></div> 
        
        </div>
                <!-- MOTO MENU & SEARCH -->
                <!--<div class="moto_menu_wrap">
                        <div class="moto_menu">
                                <h4>SHOP BY MACHINE</h4>	
                                <div class="moto_links">
                                        <a href="<?php echo base_url('streetbikeparts'); ?>"><img src="<?php echo $assets; ?>/images/icon_streetbike.png" border="0" width="55"><br>Shop Street</a>
                                        <a href="<?php echo base_url('dirtbikeparts'); ?>"><img src="<?php echo $assets; ?>/images/icon_dirtbike.png" border="0" width="55"><br>Shop Dirt</a>
                                        <a href="<?php echo base_url('atvparts'); ?>"><img src="<?php echo $assets; ?>/images/icon_atv.png" border="0" width="55"><br>Shop ATV</a>
                                        <a href="<?php echo base_url('utvparts'); ?>"><img src="<?php echo $assets; ?>/images/icon_utv.png" border="0" width="55"><br>Shop UTV</a>
                                </div>
                                <div class="moto_search">
                                        <form action="<?php echo base_url(); ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
                                                <input id="search" name="search" placeholder="Search Apparel Parts & Accessories" class="text medium_search" value="<?php echo $_GET['search']; ?>"/>
                                                <a href="javascript:void(0);" class="button" style="margin-top:6px;" onClick="setSearch($('#search').val());">Go!</a>
                                        </form>
                                </div>
                                <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                </div>-->

                <!-- END MOTO MENU & SEARCH -->
    <?php //echo @$rideSelector;  ?>
    <?php //echo @$shippingBar;  ?>
                <?php //echo @$brandSlider;  ?>
                <!--</div>-->
                <!-- END HEADER ===========================================================================-->	
            <?php } else { ?>

                <link rel="stylesheet" href="<?php echo $new_assets_url; ?>stylesheet/style.css" />
                <link rel="stylesheet" href="<?php echo $new_assets_url; ?>stylesheet/custom.css" />

                <div class="topBar_b">
                    <div class="container_b">
                        <p class="creditCar_b fltL_b">
                            <span>Ph : <?php echo $store_name['phone']; ?></span>				
                            <a href="<?php echo site_url('pages/index/contactus') ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> MAP & HOURS</a>				
                        </p>			
                        <div class="loginSec_b navbar-right">
    <?php if (@$_SESSION['userRecord']): ?>
                                <b>Welcome: <?php echo @$_SESSION['userRecord']['first_name']; ?></b> <span class="fltR seperator_b">|</span> <b><a href="<?php echo $s_baseURL . 'welcome/logout'; ?>"><u>Logout</u></a></b>
                                <?php if ($_SESSION['userRecord']['admin'] || $_SESSION['userRecord']['user_type'] == 'employee'): ?>
                                    <span class="fltR seperator_b">|</span>
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
                                <li class="icon accountLink"><a href="<?php echo $s_baseURL . 'checkout/account'; ?>">Account</a></li>
                                <li class="icon wishListLink"><a href="<?php echo base_url('/shopping/wishlist'); ?>">Wish List</a></li>
                                <li class="icon shopLink"><a href="<?php echo base_url('shopping/cart'); ?>">Shopping Cart (<span id="shopping_count"><?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0; ?></span>)</a></li>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <!--<div class="header_b">
                        <div class="container_b">
                                <a href="<?php echo base_url(); ?>" class="logoCont fltL logo-tp_b">
                                        <img src="/assets/images/power-sports-logo.png" width="200" height="50">
                                </a>
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
                                        <a href="<?php echo base_url('Motorcycle_Gear_Brands'); ?>" class="last" style="bottom:11px;">
                                                <div class="stre-bk_b" style="height:50px;padding-top:5px;">
                                                        <img src="<?php echo $new_assets_url1; ?>images/brand-tag.png">
                                                </div>
                                                <span id="sbb">Shop by Brand</span>
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
                                                        <li><a href="<?php echo $s_baseURL . 'checkout/account'; ?>">Account</a></li>
                                                        <li><a href="javascript:void(0);" onclick="openLogin();">Login/Signup</a></li>
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
                </div>-->
                <div class="header_b">
                    <div class="container_b">
                        <a href="<?php echo base_url(); ?>" class="logoCont fltL logo-tp_b">
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
                                    <li><a href="<?php echo $s_baseURL . 'checkout/account'; ?>">Account</a></li>
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
                        <form action="<?php echo base_url(); ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
                                <input id="search" name="search" placeholder="Search Parts and Apparel" class="search-bx" style="float:left;" />
                                <a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
                        </form>
                        <div class="clear"></div>
                </div>-->

    <?php if (!isset($cat_header)) { ?>
                    <!--<div class="imagesCont" style="display:flex;">
                            <img src="<?php echo $new_assets_url; ?>images/homepage_slider/banner.jpg" width="100%" alt="" usemap="#Map" />
                            <map name="Map" id="Map">
                                    <area alt="UTV'S" title="UTV'S" href="<?php echo base_url('utvparts'); ?>" shape="poly" coords="1,2,421,2,298,299,0,299" style="text-decoration:none;" />
                                    <area alt="ATV'S" title="ATV'S" href="<?php echo base_url('atvparts'); ?>" shape="poly" coords="422,2,823,2,668,297,301,299" style="text-decoration:none;" />
                                    <area alt="Dirt Bikes" title="Dirt Bikes" href="<?php echo base_url('dirtbikeparts'); ?>" shape="poly" coords="826,1,1102,1,937,299,670,298" style="text-decoration:none;" />
                                    <area alt="Street Bikes" title="Street Bikes" href="<?php echo base_url('streetbikeparts'); ?>" shape="poly" coords="1106,1,1365,1,1365,297,940,298" style="text-decoration:none;" />
                            </map>
                    </div>-->
                    <div class="clear"></div>
    <?php } else { ?>
                    <!--<div class="sliderCont">
                    <?php
                    $catImage = "dirt_bike.jpg";
                    if ($top_parent == 20409) {
                        $catImage = "street_bike.jpg";
                    } else if ($top_parent == 20419) {
                        $catImage = "atv.jpg";
                    } else if ($top_parent == 20422) {
                        $catImage = "utv.jpg";
                    }
                    ?>
                    <img src="<?php echo $new_assets_url; ?>images/category_banners/<?php echo $catImage; ?>" style="height:300px;" />
                    </div>-->
                    <div class="clear"></div>
                    <div class="productNav" style="margin-top: 2px;">
                        <div class="productNavCont">
                            <ul style="width: 100%; margin: 0 auto; padding: 0px;">
        <?php
        if (!empty($nav_categories)) {
            $county = 1;
            foreach ($nav_categories as $keyy => $navRow) {
                ?>
                                        <li class="bnz-nv"><a class="topNavAnchors" onclick="removeHeaderSearch();" href="javascript:;" id="<?php echo $county; ?>" onClick="showSubNav(<?php echo $county; ?>);"><?php echo $navRow['label']; ?></a>
                                        <?php if ($county < count($nav_categories)) { ?>
                                                <span>|</span>
                                        <?php } ?>
                                            <?php if (!empty($navRow['subcats'])) { ?>

                                                <ul id="nav<?php echo $county; ?>" class="active SubNavs" style="display:none;">
                                                    <span class="toolTip"></span>
                                                <?php foreach ($navRow['subcats'] as $subNavRow) { ?>
                                                        <li><a onclick="removeHeaderSearch();" href="<?php echo base_url() . "shopping/productlist" . $subNavRow['link']; ?>"><?php echo $subNavRow['name']; ?></a></li>
                    <?php } ?>
                                                </ul>
                                                    <?php } ?>
                                        </li>
                                                <?php
                                                $county++;
                                            }
                                        }
                                        ?>

                            </ul>        
                        </div>
                    </div>

    <?php } ?>

                <style>
                    .button_no{
                        background: #EEE !important;
                        border: 1px #DDD solid !important;
                        color: #BBB !important;
                    }
                </style>

<?php } ?>
            <!-- CONTENT WRAP =========================================================================-->
<?php
$title1 = explode('-', $title);
if (count($title1) == 1) {
    $title1 = explode('–', $title1[0]);
}
$disTitle = '';
$ws_name = WEBSITE_NAME;
foreach ($title1 as $k => $v) {
    if (trim(strtolower($v)) != strtolower($ws_name)) {
        $disTitle .= $v . ' ';
    }
}
?>
            <div class="cntnr-ttl">
            <?php if ($this->uri->total_segments() !== 0) { ?>
                    <div class="container">
                        <div class="brndimg">
                            <h1 class='mn'>
                <?php if (@$brandMain['image']): ?>
                                    <img src="<?php echo jsite_url('/media/' . $brandMain['image']); ?>">
                    <?php endif; ?>
    <?php echo trim($disTitle); ?>
                            </h1>
                        </div>
                    </div>
                            <?php } else { ?>
                    <div class="container" style="display:none;">
                        <h1 class='mn'><?php echo trim($disTitle); ?></h1>
                    </div>
<?php } ?>
            </div>

<?php echo @$mainContent; ?>

            <div class="clearfooter"></div>
            <!-- END CONTENT WRAP ===================================================================-->


        </div>
        <!-- END WRAPPER ==========================================================================-->

<?php echo @$footer; ?>	

        <style>
            .bnz-nv{
                transition:0.5s;
            }
        </style>

        <script>		
            function showSubNav( from ){

                /*if( $("#nav"+from).is(":visible") ){
	
                        $("#nav"+from).hide();
	
                }else{*/
	
                $(".SubNavs").hide();	
                $("#nav"+from).show();
	
                /*}*/

            }

            //$( ".bnz-nv" ).on("mouseleave", function() {
            $(".SubNavs").hide('slow');
            //});

            function openLogin()
            {
                window.location.replace('<?php echo $s_baseURL . 'checkout/account'; ?>');
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
                window.location.replace('<?php echo $s_baseURL . 'checkout/account'; ?>');
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
        </script>

        <script>
            $(document).ready(function() {
	
                if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                    $("body").css("display","table");
                }
	
                $( ".topNavAnchors" ).hover(
                function() {
                    showSubNav( $(this).attr("id") );
                }, function() {
                    //showSubNav( $(this).attr("id") );
                }
            );
                $(document).mouseup(function (e){
                    var container = $(".SubNavs");
                    if (!container.is(e.target) // if the target of the click isn't the container...
                        && container.has(e.target).length === 0) // ... nor a descendant of the container
                    {
                        container.hide();
                    }
                });
            });
        </script>

        <script type="text/javascript">

            /*$(window).load(function() {
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
    
        });*/

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
                $.post(base_url + 'ajax/setSearch/',
                {
                    'ajax' : true,
                    'section' : section,
                    'id': id
                },
                function(newURL)
                {
                    //alert( newURL );
                    window.location.href = base_url + 'shopping/productlist' + newURL;
                });
            }
   
   
            function setNamedSearchBrandt(event, section, id, name)
            {
                event.preventDefault();
                $.post(base_url + 'ajax/setSearch/',
                {
                    'ajax' : true,
                    'section' : section,
                    'name' : name,
                    'id': id
                },
                function(newURL)
                {
                    //window.location.href = base_url + 'shopping/productlist' + newURL;
                });
            }
   
            function setSearch(search)
            {
                //search = search.replace(/\W/g, ' ')
                search = search.toLowerCase();
                search = search.replace("oneal", "o'neal");
                search = search.replace("dcor", "d'cor");
                $.post(base_url + 'ajax/setSearch/',
                {
                    'ajax' : true,
                    'section' : 'search',
                    'name' : search,
                    'id': 1
                },
                function(newURL)
                {
                    //window.location.href = base_url + 'shopping/productlist' + newURL;
                    window.location.href = base_url + 'shopping/search_product/?search=' + search;
                });
            }
   
            function removeHeaderSearch() {
                $.post(base_url + 'ajax/removeHeaderSearch/',{},
                function(newURL) {
                });
            }
   
            function removeMainSearch(section, id)
            {
                $.post(base_url + 'ajax/removeSearch/',
                {
                    'ajax' : true,
                    'section' : section,
                    'id': id
                },
                function(newURL)
                {
                    window.location.href = base_url + 'shopping/productlist' + newURL;
                });
		
            }


        </script>



<?php echo @$script; ?>
<?php
$CI = & get_instance();
echo $CI->load->view("master/tracking", array(
    "store_name" => $store_name,
    "product" => @$product,
    "ga_ecommerce" => true,
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
                                url: base_url + 'ajax/getMake/',
                                data : {'machineId' :  val,
<?php if (@$product['part_id']): ?>
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
                                                                                                $('#add').attr('class', 'addToCat button_no' );
				
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
                                                                                                        url: base_url + 'ajax/getmodel/',
                                                                                                        data : {'makeId' :  val,
<?php if (@$product['part_id']): ?>
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
                                                                                                $('#add').attr('class', 'addToCat button_no' );
				
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
                                                                                                        url: base_url + 'ajax/getYear/',
                                                                                                        data : {'modelId' :  val,
<?php if (@$product['part_id']): ?>
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
                                                                                                $('#add').attr('class', 'addToCat button_no' );
				
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
                                                                                            $('#add').attr('class', 'addToCat button' );
                                                                                        else
                                                                                            $('#add').attr('class', 'addToCat button_no' );
                                                                                    }
	
                                                                                    function updateGarage()
                                                                                    {
                                                                                        var pathname = window.location.pathname;
                                                                                        if(pathname == "/qatesting/index.php"){
                                                                                            pathname = window.location.href.replace(window.location.origin+window.location.pathname+"?/", "");
                                                                                        }
                                                                                        $('#update_garage_form').append('<input type="hidden" name="url" value="'+pathname +'" />');
                                                                                        $('#update_garage_form').submit();
		
                                                                                    }
                                                                                    //showVideo
                                                                                    function showVideo(vidId) {
                                                                                        $("#mainVideo")[0].src = "https://www.youtube.com/embed/"+vidId+"?rel=0&autoplay=1";
                                                                                    }
                                                                                    //cntnr-ttl
                                                                                    //if( "<?php echo $this->router->fetch_method(); ?>" == "index" ) {
                                                                                    $('.grghdng').html($('.cntnr-ttl').html());
                                                                                    $('.cntnr-ttl').addClass('ttldspl');
                                                                                    //} else {
                                                                                    //	$('.cntnr-ttl').removeClass('ttldspl');
                                                                                    //}
                                                                                    //if (!$('.grghdng').is(':empty')){
                                                                                    //	$('.grghdng').addClass('mrgn60');
                                                                                    //} else {
                                                                                    //	$('.grghdng').removeClass('mrgn60');
                                                                                    //}
        </script>

        <script>
            var url      = window.location.href;
            if(url=='<?php echo base_url('streetbikeparts') ?>'){
                $("#stp").addClass('actv');
                $('#sdp').removeClass('actv');
                $('#sap').removeClass('actv');
                $('#sup').removeClass('actv');
                $('#sbb').removeClass('actv');
            }else if(url=='<?php echo base_url('dirtbikeparts') ?>'){
                $("#stp").removeClass('actv');
                $('#sdp').addClass('actv');
                $('#sap').removeClass('actv');
                $('#sup').removeClass('actv');
                $('#sbb').removeClass('actv');
            }else if(url=='<?php echo base_url('atvparts') ?>'){
                $("#stp").removeClass('actv');
                $('#sdp').removeClass('actv');
                $('#sap').addClass('actv');
                $('#sup').removeClass('actv');
                $('#sbb').removeClass('actv');
            }else if(url=='<?php echo base_url('utvparts') ?>'){
                $("#stp").removeClass('actv');
                $('#sdp').removeClass('actv');
                $('#sap').removeClass('actv');
                $('#sup').addClass('actv');
                $('#sbb').removeClass('actv');
            }else if(url=='<?php echo base_url('Motorcycle_Gear_Brands') ?>'){
                $("#stp").removeClass('actv');
                $('#sdp').removeClass('actv');
                $('#sap').removeClass('actv');
                $('#sup').removeClass('actv');
                $('#sbb').addClass('actv');
            }else if(url=='<?php echo base_url('vtwin') ?>'){
                $("#stp").removeClass('actv');
                $('#sdp').removeClass('actv');
                $('#sap').removeClass('actv');
                $('#svp').addClass('actv');
                $('#sbb').removeClass('actv');
            }
	
        </script>
        <script type="application/javascript" src="<?php echo jsite_url('/custom.js'); ?>" ></script>

    </body>
</html>
