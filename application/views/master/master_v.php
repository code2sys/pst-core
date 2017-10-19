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

if (!defined('SEARCH_PLACEHOLDER_WORDING')) {
    define('SEARCH_PLACEHOLDER_WORDING', 'Search Parts and Apparel');
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $page_title; ?></title>

        <?php
        $new_assets_url = jsite_url("/qatesting/newassets/");
        $new_assets_url1 = jsite_url("/qatesting/benz_assets/");

        if (@$ssl) {
            $assets = $s_assets;
        }
        ?>

        <?php
        $CI =& get_instance();
        echo $CI->load->view("master/top_header", array(
            "store_name" => $store_name,
            "meta_description" => $meta_description,
            "meta_keywords" => $meta_keywords
        ));

        ?>

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
<?php echo @$css; ?>
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/style.css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.carousel.css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.theme.css" />
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.transitions.css" />	
        <link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/font-awesome.css" />
        <link rel="stylesheet" href="/qatesting/benz_assets/css/jquery.fancybox.css" />




        <!-- END CSS LINKS --> 
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
        <script src="<?php echo $new_assets_url1; ?>js/bootstrap.min.js"></script>
        <script src="<?php echo $new_assets_url1; ?>js/owl.carousel.js"></script>
        <!-- jQuery library -->

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

        <!-- bxSlider Javascript file -->
        <script src="<?php echo $assets; ?>/js/jquery.bxslider.min.js"></script>
        <script>
            $(document).ready(function(){
                $('.bxslider').bxSlider({
                    auto: true,
                    pause: 5000,
                    randomStart: false
                });
            });
        </script>

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

        <script src="/qatesting/benz_assets/js/jquery.fancybox.pack.js" ></script>

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
                <?php echo @$nav ?>
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

                <div class="header_b">
                    <div class="container_b">
                        <a href="<?php echo base_url(); ?>" class="logoCont fltL logo-tp_b">
                            <img src="/logo.png" width="200" height="50">
                        </a>

                        <div class="side-hdr">
                            <div class="sidebar-menu">
                                <span> <i class="fa fa-bars" aria-hidden="true"></i> Menu</span>
                                <ul class="mb-drpdwn">
                                    <?php require(__DIR__ . "/../mobile_navigation_fragment.php"); ?>

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
                                <input id="search" name="search" placeholder="<?php echo SEARCH_PLACEHOLDER_WORDING; ?>" class="search-bx" style="float:left;" />
                                <a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
                            </form>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>						
                    </div>
                    <div class="container_b">
                        <div class="vehicleCategory">
                            <?php require(__DIR__ . "/../navigation_fragment.php"); ?>
                        </div>	
                        <div class="clear"></div>
                    </div>
                </div>


    <?php if (!isset($cat_header)) { ?>
                    <div class="clear"></div>
    <?php } else { ?>
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


        <?php
        $CI =& get_instance();
        echo $CI->load->view("widgets/ride_selection_js", array(
            "product" => isset($prodct) ? $product : null,

        ), true);
        ?>


        <script>
            //showVideo
            function showVideo(vidId, vidTit) {
                var mainVideo = $('#mainVideo').data('id');
                //var mainTitle = $('.vdottl').html();
                $('.vdottl').html(vidTit);
                $("#mainVideo")[0].src = "https://www.youtube.com/embed/"+vidId+"?rel=0&autoplay=1";
                $('#mainVideo').data('id', vidId);
                //$('.shwVidHalf').show();
                $('#'+vidId).hide();
                $('#'+mainVideo).show();
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
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=1038872762878770";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>

        <script>
            var url      = window.location.href;

            <?php
            if (!isset($CI)) {
                $CI =& get_instance();
            }
            echo $CI->load->view("master/widgets/selector_js");
            ?>

        </script>
        <script type="application/javascript" src="<?php echo jsite_url('/custom.js'); ?>" ></script>

        <?php
        $CI =& get_instance();
        echo $CI->load->view("master/bottom_footer", array(
            "store_name" => $store_name
        ));
        ?>
    </body>
</html>
