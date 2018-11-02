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

echo "<pre>";
echo $descr."###";
print_r($pageRec);
echo "</pre>";

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
        <?php echo jget_store_block("top_header"); ?>
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

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald:400,500">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Bungee+Inline">

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
        <?php echo jget_store_block("bottom_header"); ?>
    </head>

    <body class="body master_v" <?php if (isset($new_header)) { ?>style="width:100%;margin:0 auto;"<?php } ?>>
    <?php echo jget_store_block("top_body"); ?>
        <!-- WRAPPER ==============================================================================-->
        <div class="wrap">
            <?php if (!isset($new_header)) { ?>
                <!-- HEADER =============================================================================-->
                <?php echo @$nav ?>
                <!-- END HEADER ===========================================================================-->
            <?php } else { ?>

                <link rel="stylesheet" href="<?php echo $new_assets_url; ?>stylesheet/style.css" />
                <link rel="stylesheet" href="<?php echo $new_assets_url; ?>stylesheet/custom.css" />

                <?php

                $CI =& get_instance();

                echo $CI->load->view("master/widgets/mainheader", array(
                    "store_name" => $store_name,
                    "s_baseURL" => $s_baseURL
                ), true);

                $CI->load->helper("mustache_helper");
                $motorcycle_action_buttons = mustache_tmpl_open("store_header_marquee.html");
                echo mustache_tmpl_parse($motorcycle_action_buttons);
                $motorcycle_action_buttons = mustache_tmpl_open("store_header_banner.html");
                echo mustache_tmpl_parse($motorcycle_action_buttons);
                ?>


                <?php if (!isset($cat_header)) { ?>
                    <div class="clear"></div>
    <?php } else { ?>
                    <div class="clear"></div>
                    <?php
                    echo $CI->load->view("master/widgets/nav_categories", array(
                            "nav_categories" => $nav_categories
                    ), true);
                    ?>

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
echo "<!-- Title $title Dis Title $disTitle -->";
?>
            <div class="cntnr-ttl">
            <?php if ($this->uri->total_segments() !== 0) { ?>
                    <div class="container">
                        <div class="brndimg">
                            <h1 class='mn'>
                <?php if (@$brandMain['image']): ?>
                                    <img src="<?php echo jsite_url('/media/' . $brandMain['image']); ?>"><br/>
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
                $(".SubNavs").hide();
                $("#nav"+from).show();
            }

            //$( ".bnz-nv" ).on("mouseleave", function() {
            $(".SubNavs").hide('slow');
            //});

            function openLogin()
            {
                window.location.replace('<?php echo $s_baseURL . 'checkout/account'; ?>');
            }

            function openCreateAccount()
            {
                window.location.replace('<?php echo $s_baseURL . 'checkout/account'; ?>');
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
            "product" => isset($product) ? $product : null,

        ), true);
        ?>

    <?php
    $CI =& get_instance();
    echo $CI->load->view("showvideo_function", array(), false);
    ?>

        <script>
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
    <?php echo jget_store_block("bottom_body"); ?>
    </body>
</html>
