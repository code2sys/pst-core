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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>/css/responsive.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/style.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.carousel.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.theme.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/owl.transitions.css" />	
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>css/font-awesome.css" />	

	<script src="<?php echo $new_assets_url1; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo $new_assets_url1; ?>js/owl.carousel.js"></script>	
	
	<link rel="stylesheet" href="<?php echo $assets; ?>/css_front/media.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url;?>stylesheet/style.css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/jquery.bxslider.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/benz.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>stylesheet/custom.css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/magnific-popup.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/jquery.selectbox.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/flexisel.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/expand.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/modal.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/font-awesome-4.1.0/css/font-awesome.min.css">

	<script src="<?php echo $assets; ?>/js/jquery.simplemodal.js"></script>
	<script src="<?php echo $assets; ?>/js/custom.js"></script>	
	<script type="text/javascript" src="<?php echo $assets; ?>/js/rating.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $assets; ?>/css/rating.css" />

	<script>
	$(document).ready(function(){
		$(".sidebar-menu").click(function(){
			$(".mb-drpdwn").toggle('slow');
		});
	});
	</script>	
	<script>
	$(document).ready(function() {
		$("li").click(function(){
			$(this).toggleClass("active");
			$(this).next(".tlg").stop('true','true').slideToggle("slow");
		});
	});
	</script>
	
	<script>
		var base_url = '<?php echo base_url(); ?>';
		var s_base_url = '<?php echo $s_baseURL; ?>';
		shopping_cart_count = <?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0; ?>;
	</script>
	
	<!-- Magnific Popup core JS file -->
	<script src="<?php echo $assets; ?>/js/jquery.magnific-popup.js"></script>
	
	<!-- bxSlider Javascript file -->
	<script src="<?php echo $assets; ?>/js/jquery.bxslider.min.js"></script>

	
	<!-- Flexisel JS -->
	<script type="text/javascript" src="<?php echo $assets; ?>/js/jquery.flexisel.js"></script>
	
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
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
				$("body").css("display","table");
			}
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
<script src="<?php echo $assets; ?>/js/jquery.simplemodal.js"></script>

<style type="text/css">
	body{ margin:0 auto; padding:0;width:100%;}
	.demo-code{ background-color:#ffffff; border:1px solid #333333; display:block; padding:10px;}
	.option-table td{ border-bottom:1px solid #eeeeee;}
	
	#loading-background {
		background-color: #fff;
		display: block;
		height: 100%;
		left: 0;
		opacity: 0.7;
		position: fixed;
		text-align: center;
		top: 0;
		width: 100%;
		z-index: 99;
	}
	#loading-background img {
		margin: 0 auto;
		position: absolute;
		top: 50%;
		z-index: 100;
	}
</style>
    <link rel="stylesheet" href="<?php echo jsite_url("qatesting/newassets/stylesheet/style.css"); ?>" />
    <link rel="stylesheet" href="<?php echo jsite_url("qatesting/newassets/stylesheet/custom.css"); ?>" />

    <link rel="stylesheet" href="<?php echo jsite_url("/basebranding.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />
    <?php echo jget_store_block("bottom_header"); ?>

</head>

<body class="body master_v_new ">
<?php echo jget_store_block("top_body"); ?>
<div id="loading-background" style="display:none;">
  <img src="<?php echo $new_assets_url;?>images/ajax-loader-black.gif" alt="Loading..." />
</div>

<?php
$CI =& get_instance();

echo $CI->load->view("master/widgets/mainheader", array(
    "store_name" => $store_name,
    "s_baseURL" => $s_baseURL,
    "invoking_page" => "master_v_new"
), true);


$CI->load->helper("mustache_helper");
$motorcycle_action_buttons = mustache_tmpl_open("store_header_marquee.html");
echo mustache_tmpl_parse($motorcycle_action_buttons);
$motorcycle_action_buttons = mustache_tmpl_open("store_header_banner.html");
echo mustache_tmpl_parse($motorcycle_action_buttons);
?>



<div class="productNav">
  <div class="productNavCont">
    	<ul style="width: 100%; margin: 0 auto; padding: 0px;">
			<?php
			if( !empty($nav_categories) ){				
				$county = 1;
				foreach( $nav_categories as $keyy=>$navRow ){

			?>
				<li><a class="topNavAnchors" href="javascript:;" id="<?php echo $county;?>" onclick="showSubNav(<?php echo $county;?>);"><?php echo $navRow['label'];?></a>
					<?php if($county<count($nav_categories)){?>
						<span>|</span>
					<?php }?>
					<?php if( !empty($navRow['subcats']) ){?>
					
					<ul id="nav<?php echo $county;?>" class="active SubNavs" style="display:none;">
						<span class="toolTip"></span>
						<?php foreach($navRow['subcats'] as $subNavRow){?>
						<li><a onclick="removeHeaderSearch($(this));" href="<?php echo base_url()."shopping/productlist".$subNavRow['link'];?>"><?php echo $subNavRow['name'];?></a></li>
						<?php }?>
					</ul>
					<?php
						}?>
				</li>
			<?php	
				$county++;
				}
			}?>
        </ul>        
	</div>
</div>


<style>
/* ======== BRAND SLIDER ======== */
	
.brand_wrap {
	background:url(../images/brand_bg.jpg)top left repeat-x #EEE;
	border-top:2px #999 solid;
	border-bottom:5px #303 solid;
	height:48px;
	width:100%;
}
.brands {
	height:48px;
	margin:0px auto;
	width:100%;
	max-width:1000px;
}	


</style>

    	<?php //echo str_replace("qatesting/index.php?/media/", "media/", @$brandSlider); ?>
		
		<?php  echo @$mainContent; ?>
		
		<div class="clear"></div>
		
		<?php echo @$footer; ?>

<?php
$CI =& get_instance();
echo $CI->load->view("master/widgets/flexiselect", array(), true);
?>

<script type="application/javascript">
function showSubNav( from ){

	/*if( $("#nav"+from).is(":visible") ){
	
		$("#nav"+from).hide();
	
	}else{*/
	
		$(".SubNavs").hide();	
		$("#nav"+from).show();
	
	/*}*/

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

$(document).ready(function() {
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


	
	/* Submit on Enter */
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
   
   function setSearch(search)
   {
	   //search = search.replace(' ', '_');
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

   function removeHeaderSearch( elem ) {
	   $.post(base_url + 'ajax/removeHeaderSearch/',{},
		function(newURL) {
			window.location.href = elem.attr('href');
		});
		return false;
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
   
   function setMainSearchCategory(event, section, id)
   {
       // JLB 10-12-17
       // This is the stupidest thing in the world if it happens to the top-level categories...
       id = parseInt(id, 10);
       if (id == <?php echo TOP_LEVEL_CAT_STREET_BIKES; ?> || id == <?php echo TOP_LEVEL_CAT_ATV_PARTS; ?> || id == <?php echo TOP_LEVEL_CAT_UTV_PARTS; ?> || id == <?php echo TOP_LEVEL_CAT_VTWIN_PARTS; ?> || id == <?php echo TOP_LEVEL_CAT_DIRT_BIKES; ?> || id == <?php echo TOP_LEVEL_CAT_MARINE; ?> || id == <?php echo TOP_LEVEL_CAT_SNOW; ?>) {
           // sometimes, they just do these URLs wrong.
           switch(id) {
               case <?php echo TOP_LEVEL_CAT_STREET_BIKES; ?>:
                   window.location.href = '/streetbikeparts';
                   break;
               case <?php echo TOP_LEVEL_CAT_ATV_PARTS; ?>:
                   window.location.href = '/atvparts';
                   break;
               case <?php echo TOP_LEVEL_CAT_UTV_PARTS; ?>:
                   window.location.href = '/utvparts';
                   break;
               case <?php echo TOP_LEVEL_CAT_VTWIN_PARTS; ?>:
                   window.location.href = '/vtwin';
                   break;
               case <?php echo TOP_LEVEL_CAT_DIRT_BIKES; ?>:
                   window.location.href = '/dirtbikeparts';
                   break;
               case <?php echo TOP_LEVEL_CAT_MARINE; ?>:
                   window.location.href = '/marine';
                   break;
               case <?php echo TOP_LEVEL_CAT_SNOW; ?>:
                   window.location.href = '/snowparts';
                   break;
               default:
                   return true;
           }

           event.preventDefault();
           return false; // just keep on moving. Nothing to see here.
       }

	   event.preventDefault();
	   $.post(base_url + 'ajax/setSearchCategory/',
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

<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=1038872762878770";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
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

