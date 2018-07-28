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
    <?php echo jget_store_block("top_header"); ?>

	<title><?php echo $page_title; ?></title>
	<?php
	$CI =& get_instance();
	echo $CI->load->view("master/top_header", array(
		"store_name" => $store_name,
		"meta_description" => $meta_description,
		"meta_keywords" => $meta_keywords
	));

	?>


    <?php
	$new_assets_url = jsite_url("/qatesting/newassets/");
	$new_assets_url1 = jsite_url("/qatesting/benz_assets/");
	?>


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
	
	<?php echo @$css; ?>
	
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/style.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.carousel.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.theme.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.transitions.css" />	
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/font-awesome.css" />	
	
	<!-- END CSS LINKS -->
	
	<!-- jQuery library -->
	
	<script src="<?php echo $new_assets_url1; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo $new_assets_url1; ?>js/owl.carousel.js"></script>
	
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

    <link rel="stylesheet" href="<?php echo jsite_url("qatesting/newassets/stylesheet/style.css"); ?>" />
    <link rel="stylesheet" href="<?php echo jsite_url("qatesting/newassets/stylesheet/custom.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/basebranding.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />
    <?php echo jget_store_block("bottom_header"); ?>

</head>

<body class="body" style="width:100%;margin:0 auto;">
<?php echo jget_store_block("top_body"); ?>

<!-- WRAPPER ==============================================================================-->
<div class="wrap">
	


    <?php
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $template = mustache_tmpl_open("master/s_nav_master_v.html");

    mustache_tmpl_set($template, "SUPPORT_PHONE_NUMBER", SUPPORT_PHONE_NUMBER);
mustache_tmpl_set($template, "street_address", $store_name['street_address']);
mustache_tmpl_set($template, "address_2", $store_name['address_2']);
mustache_tmpl_set($template, "city", $store_name['city']);
mustache_tmpl_set($template, "state", $store_name['state']);
mustache_tmpl_set($template, "zip", $store_name['zip']);
mustache_tmpl_set($template, "phone", $store_name['phone']);
mustache_tmpl_set($template, "email", $store_name['email']);
mustache_tmpl_set($template, "new_assets_url", $new_assets_url);

    if (array_key_exists("userRecord", $_SESSION) && $_SESSION["userRecord"]) {
        mustache_tmpl_set($template, "userRecord_show", true);
        mustache_tmpl_set($template, "userRecord_firstname", $_SESSION['userRecord']['first_name']);

        mustache_tmpl_set($template, "userRecord_admin", $_SESSION['userRecord']['admin'] || $_SESSION['userRecord']['user_type'] == 'employee');
    } else {
        mustache_tmpl_set($template, "userRecord_show", false);
    }
    mustache_tmpl_set($template, "shopping_count", (array_key_exists("cart", $_SESSION) && array_key_exists("qty", $_SESSION['cart']) && $_SESSION['cart']['qty'] > 0)  ? $_SESSION['cart']['qty'] : 0);

    mustache_tmpl_set($template, "s_baseURL", $s_baseURL);

    $GLOBAL_MOBILE_NAV_FRAG_STRING = true;
    require(__DIR__ . "/../mobile_navigation_fragment.php");
    mustache_tmpl_set($template, "mobile_navigation_menu", $GLOBAL_MOBILE_NAV_FRAG);
    $GLOBAL_MOBILE_NAV_FRAG_STRING = false;

    mustache_tmpl_set($template, "CLEAN_PHONE_NUMBER", CLEAN_PHONE_NUMBER);

    mustache_tmpl_set($template, "search_placeholder", $CI->load->view("search_placeholder", array(), true));

    $GLOBAL_NAV_FRAG_STRING = true;
    require(__DIR__ . "/../navigation_fragment.php");
    mustache_tmpl_set($template, "navigation_fragment", $GLOBAL_NAV_FRAG);
    $GLOBAL_NAV_FRAG_STRING = false;

    mustache_tmpl_set($template, "new_assets_url1", $new_assets_url1);

    jtemplate_add_store_hours($template, $store_name);


    echo mustache_tmpl_parse($template);

        $motorcycle_action_buttons = mustache_tmpl_open("store_header_marquee.html");
        echo mustache_tmpl_parse($motorcycle_action_buttons);
        $motorcycle_action_buttons = mustache_tmpl_open("store_header_banner.html");
        echo mustache_tmpl_parse($motorcycle_action_buttons);
        ?>


        <div class="clear"></div>


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

	try {
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

    } catch(err) {
	    console.log("Error with magnificPopup AGAIN: " + err);
    }
});
</script>
<?php
$CI =& get_instance();
echo $CI->load->view("master/widgets/flexiselect", array(), true);
?>
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





<!-- BEGIN: Google Trusted Stores -->
<?php

$CI =& get_instance();
echo $CI->load->view("master/tracking", array(
	"store_name" => $store_name,
	"product" => @$product,
	"ga_ecommerce" => false,
	"show_ga_conversion" => true

), true);

if (isset($script) && $script != "") {
	echo $script;
}

?>

<?php
$CI =& get_instance();
echo $CI->load->view("widgets/ride_selection_js", array(
    "product" => isset($product) ? $product : null,
    "secure" => true
), true);
?>

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
