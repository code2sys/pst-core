<!doctype html>
<html>
<?php
	$new_assets_url = jsite_url(  "/qatesting/benz_assets/" );
	$media_url = jsite_url("/media/");
$CI =& get_instance();
$CI->load->model("admin_m");
$store_name = $CI->admin_m->getAdminShippingProfile();
$google_conversion_id = $store_name['google_conversion_id'];
$partsfinder_link = $store_name["partsfinder_link"];
$number_across = trim($partsfinder_link) == "" ? "six" : "seven";

if (!defined('SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS')) {
    define('SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS', true);
}

$SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS = SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS;

	?>
<head>
    <?php echo jget_store_block("top_header"); ?>
	<?php if (isset($title)): ?>
    <title><?php echo $title; ?></title>
    <?php endif; ?>
    <?php
    $CI =& get_instance();
    echo $CI->load->view("master/top_header", array(
        "store_name" => $store_name,
        "meta_description" => $meta_description,
        "meta_keywords" => $meta_keywords
    ));

    ?>

	<?php echo @$metatag; ?>

	<!--Motercycle Content Start-->
	<!--Motercycle Content End-->

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald:400,500">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Bungee+Inline">
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/style.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.carousel.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.theme.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.transitions.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/font-awesome.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/lightslider.css" />
	<link rel="stylesheet" href="/qatesting/newassets/stylesheet/custom.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>/css/responsive.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo jsite_url('/basebranding.css'); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url('/custom.css'); ?>" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/jquery.fancybox.css" />

	<script src="<?php echo $CI->config->item("base_scheme"); ?>://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/owl.carousel.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/lightslider.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/jquery.fancybox.pack.js"></script>

    <script>
        var base_url = '<?php echo base_url(); ?>';
    </script>
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
    <style>
        .pager_spacer span {
            background: #eee;
            text-decoration: none;
            color: #000;
            padding: 9px 8px 5px;
            font-size: 15px;
            display: block;
            margin: 0 2px;
            line-height: normal;
        }
    </style>

    <link rel="stylesheet" href="<?php echo jsite_url("qatesting/newassets/stylesheet/style.css"); ?>" />
    <link rel="stylesheet" href="<?php echo jsite_url("qatesting/newassets/stylesheet/custom.css"); ?>" />



    <?php echo jget_store_block("bottom_header"); ?>
    <?php echo jget_store_block("moto_bottom_header"); ?>
</head>

<body <?php if ($SIMPLIFIED_NAV_WITHIN_MAJOR_UNITS): ?>class="benz_views_header simplified_mu_nav"<?php endif; ?>>
<?php echo jget_store_block("top_body"); ?>
<?php echo jget_store_block("moto_top_body"); ?>
<?php

$CI =& get_instance();

echo $CI->load->view("master/widgets/mainheader", array(
    "store_name" => $store_name,
    "s_baseURL" => $s_baseURL,
    "invoking_page" => "benz_views_header"
), true);


$CI->load->helper("mustache_helper");
$motorcycle_action_buttons = mustache_tmpl_open("store_header_marquee.html");
echo mustache_tmpl_parse($motorcycle_action_buttons);
$motorcycle_action_buttons = mustache_tmpl_open("store_header_banner.html");
echo mustache_tmpl_parse($motorcycle_action_buttons);
?>



	<!--<div class="searchHolder search-two">
		<form action="<?php //echo base_url(); ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
			<input id="search" name="search" placeholder="Search" class="search-bx" style="float:left;" />
			<a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
		</form>
		<div class="clear"></div>
	</div>-->


	<?php  echo @$mainContent; ?>

<?php
$CI =& get_instance();
echo $CI->load->view("benz_views/real_footer", array(
    "store_name" => $store_name,
    "pages" => $pages,
    "SMSettings" => $SMSettings
), true);

?>


	<script language="javascript">
		function tweetCurrentPage()
		{ window.open("https://twitter.com/share?u="+escape(window.location.href), '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false; }
	</script>
	<script language="javascript">
		function googleCurrentPage()
		{
			window.open("https://plus.google.com/share?url="+escape(window.location.href));
			return false;
		}
	</script>
	<script>
	$(document).on('click', '.pgn', function() {
		var pg = $('a', $(this)).attr("data-page-number");
		if(!$(this).hasClass('active')) {
			var brands = $("input[name='brand[]']:checkbox:checked").map(function(){
				return $(this).val();
			}).get();
			var years = $("input[name='year[]']:checkbox:checked").map(function(){
				return $(this).val();
			}).get();
			var categories = $("input[name='category[]']:checkbox:checked").map(function(){
				return $(this).val();
			}).get();
			var vehicles = $("input[name='vehicles[]']:checkbox:checked").map(function(){
				return $(this).val();
			}).get();
			var condition = "<?php echo $_GET['fltr'];?>";
			pg = parseInt(pg)-1;

			var ajax_url = "<?php echo site_url('motorcycle_ci/filterMotorcycle');?>";
			$.post( ajax_url, {'brands':brands,'years':years,'categories':categories,'vehicles':vehicles, 'condition':condition,'page':pg}, function( result ){
				$('.prdts').html(result);
				try {
					$('html,body').animate({scrollTop: $(".mid.prdts").offset().top}, 100);
				} catch(err) {
					window.scrollTo(0, 0);
				}
				//alert(result);
			});
		}
	});

		$(document).ready(function() {

			$("#owl-demo").owlCarousel({
				navigation : true,
			   slideSpeed : <?php echo defined("HOME_SCREEN_SLIDER_SPEED") ? HOME_SCREEN_SLIDER_SPEED : 300; ?>,
			   paginationSpeed : <?php echo defined("HOME_SCREEN_PAGINATION_SPEED") ? HOME_SCREEN_PAGINATION_SPEED : 400; ?>,
			   singleItem:true,
			   autoPlay: <?php echo defined("HOME_SCREEN_AUTO_PLAY_TIMEOUT") ? HOME_SCREEN_AUTO_PLAY_TIMEOUT : 5000; ?>,
			   autoPlayTimeout:<?php echo defined("HOME_SCREEN_AUTO_PLAY_TIMEOUT") ? HOME_SCREEN_AUTO_PLAY_TIMEOUT : 1000; ?>
		   });
            <?php
            // JLB 01-31-18
            // The BENZ guys just cannot make good names. I don't know which ones of these are live, but they all appear to exist somewhere.
            // Really, a clusterfuck of bad design on this page...and it's duplicated in header.php and in a few other spots.
            ?>
            $("#hotels-flats").owlCarousel({
                items : 4,
                lazyLoad : true,
                navigation : true,
                autoPlay: true,
                autoPlayTimeout:3000
            });

            $("#homes-for-rent").owlCarousel({
                items : 4,
                lazyLoad : true,
                navigation : true
            });
            $("#homes-for-rent-1").owlCarousel({
                items : 3,
                lazyLoad : true,
                navigation : true
            });
            $("#hotels-flats-1").owlCarousel({
                items : 3,
                lazyLoad : true,
                navigation : true
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

    $('.scl').click(function(e){
	$('.scl').removeClass('active');
	$(this).addClass('active');
	$('.social-page').hide();
	$('.'+$(this).data('link')).show();
    });

   });

  <?php
          /*
           * JLB 12-26-17
           * This could earn my "dumbest fuck" award. Why did they put this thing over here, in the header file, when it's related to the product search screen?
           */

          ?>
   $(document).on('change', '.styled', function() {
		var brands = $("input[name='brand[]']:checkbox:checked").map(function(){
			return $(this).val();
		}).get();
		var years = $("input[name='year[]']:checkbox:checked").map(function(){
			return $(this).val();
		}).get();
		var categories = $("input[name='category[]']:checkbox:checked").map(function(){
			return $(this).val();
		}).get();
		var vehicles = $("input[name='vehicles[]']:checkbox:checked").map(function(){
			return $(this).val();
		}).get();

		// This is just stupid, too...
		var condition = "<?php echo (array_key_exists('fltr', $_GET) && $_GET['fltr'] != '') ? $_GET['fltr'] : '';?>";

		var cndn = "";
		if( condition != "" ) {
			cndn = "fltr="+condition;
		}

		var brnd = brands.join('$');
		var yrs = years.join('$');
		var ctgrs = categories.join('$');
		var vhcl = vehicles.join('$');

		var url1 = cndn;
		if( brands.length > 0 ) {
			url1 = url1+"&brands="+brnd;
		}
		if( categories.length > 0 ) {
			url1 = url1+"&categories="+ctgrs;
		}
		if( years.length > 0 ) {
			url1 = url1+"&years="+yrs;
		}
		if( vehicles.length > 0 ) {
			url1 = url1+"&vehicles="+vhcl;
		}

		// JLB 12-26-17
       // We need to tell this to change the filter. This helps given that they made the insane choice of POSTING the other things...Why would you ever post this???
       url1 = url1 + (url1 != "" ? '&' : '')  + "filterChange=1";

		//var url1 = cndn+"&brands="+brnd+"&categories="+ctgr+"&years="+yrs+"&vehicles="+vhcl;
		//alert(url1);
		var url = "<?php
            if (array_key_exists("major_units_featured_only", $_SESSION) && $_SESSION["major_units_featured_only"] > 0) {
                if (array_key_exists('fltr', $_GET) && $_GET['fltr'] == "pre-owned") {
                    echo site_url('Motorcycle_List/featured_preowned');
                } else {
                    echo site_url('Motorcycle_List/featured');
                }
            } else {
                echo site_url('Motorcycle_List');
            }
            ?>?"+url1;
		window.location.href = url;

		// var ajax_url = "<?php echo site_url('welcome/filterMotorcycle');?>";
		// $.post( ajax_url, {'brands':brands,'years':years,'categories':categories,'vehicles':vehicles, 'condition':condition}, function( result ){
			// $('.prdts').html(result);
			// //alert(result);
		// });
   });

</script>
<script>
    if (typeof window.showSubNav === 'undefined') {
        window.showSubNav = function(from) {
            $(".SubNavs").hide();
            $("#nav"+from).show();
        }
    }

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
</script>

<script>
$(document).ready(function() {

	$('.panel-title').click(function() {
		var id = $(this).data('id');
		var not = $(this).data('not');
		$('#'+not).slideUp();
		$('#'+id).slideDown();
		//alert(id);
	});
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

    $('.scl').click(function(e){
	$('.scl').removeClass('active');
	$(this).addClass('active');
	$('.social-page').hide();
	$('.'+$(this).data('link')).show();
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
    $CI =& get_instance();
    echo $CI->load->view("widgets/ride_selection_js", array(
        "product" => isset($product) ? $product : null,

    ), true);
?>
<script>
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=1038872762878770";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
	<link rel="stylesheet" href="<?php echo jsite_url('/basebranding.css'); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url('/custom.css'); ?>" />
<style>
.main-vdo {
    width: 69%;
    float: left;
}
.rmv .main-vdo ul {
    width: 100%;
    float: left;
    margin: 0;
    padding: 5px;
    background: rgba(192, 192, 192, 0.48);
}
.rmv .main-vdo ul li {
    float: left;
    margin: 0 15px 0 0px;
    list-style: none;
}
.rmv .main-vdo ul li .ggl {
    max-width: 59px;
    overflow: hidden;
    float: left;
}
.rmv .main-vdo ul li strong {
    float: left;
    margin: 4px 7px 0 0;
}
.rmv .main-vdo ul .subs {
    float: right;
}
.mn-ul .mn-frst {
    width: 48%;
}
.ggl-pls {
    padding-top: 3px;
}
.mn-hght {
    min-height: 10px;
}
</style>
    <?php
    $CI = & get_instance();
    echo $CI->load->view("master/tracking", array(
        "store_name" => $store_name,
        "product" => @$product,
        "ga_ecommerce" => true,
        "show_ga_conversion" => true
    ), true);
    ?>

    <script type="application/javascript" src="<?php echo jsite_url('/custom.js'); ?>" ></script>

<?php echo jget_store_block("bottom_body"); ?>
</body>
</html>
