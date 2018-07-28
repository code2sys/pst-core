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
	$new_assets_url = jsite_url("/qatesting/newassets/");
	$new_assets_url1 = jsite_url("/qatesting/benz_assets/");
	?>

	<?php
	$CI =& get_instance();
	echo $CI->load->view("master/top_header", array(
		"store_name" => $store_name,
		"meta_description" => $meta_description,
		"meta_keywords" => $meta_keywords
	));

	?>


	<?php echo @$metatag; ?>
	
	<!-- CSS LINKS -->
	<link rel="stylesheet" href="<?php echo $new_assets_url1; ?>/css/responsive.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css_front/media.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/style.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/benz.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/account_nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/jquery.bxslider.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/magnific-popup.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/jquery.selectbox.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/flexisel.css" type="text/css">
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

	<!-- END CSS LINKS --> 
	
	<!-- jQuery library -->
	<script src="<?php echo $new_assets_url1; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo $new_assets_url1; ?>js/owl.carousel.js"></script>
	
	<?php echo $topscript; ?>
	<script type="text/javascript">
	$(document).ready(function() {
		$("li").click(function(){
			$(this).toggleClass("active");
			$(this).next(".tlg").stop('true','true').slideToggle("slow");
		});
	});
	</script>
	

	<script>
	$(document).ready(function(){
		$(".sidebar-menu").click(function(){
			$(".mb-drpdwn").toggle('slow');
		});
	});
	</script>
	
	<script>
		var environment = '<?php echo ENVIRONMENT; ?>';
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
<script src="<?php echo $assets; ?>/js/custom.js"></script>
<!-- END POPUP JS -->

<?php echo @$header; ?>

    <link rel="stylesheet" href="<?php echo jsite_url("qatesting/newassets/stylesheet/style.css"); ?>" />
    <link rel="stylesheet" href="<?php echo jsite_url("qatesting/newassets/stylesheet/custom.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/basebranding.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />
    <?php echo jget_store_block("bottom_header"); ?>

</head>

<body class="body master_v_brand_list" style="width:100%;margin:0 auto;">
<?php echo jget_store_block("top_body"); ?>

<!-- WRAPPER ==============================================================================-->
<div class="wrap">


        <?php
        $CI =& get_instance();

        echo $CI->load->view("master/widgets/mainheader", array(
            "store_name" => $store_name,
            "s_baseURL" => $s_baseURL,
            "invoking_page" => "master_v_brand_list"
        ), true);

        $CI->load->helper("mustache_helper");
        $motorcycle_action_buttons = mustache_tmpl_open("store_header_marquee.html");
        echo mustache_tmpl_parse($motorcycle_action_buttons);
        $motorcycle_action_buttons = mustache_tmpl_open("store_header_banner.html");
        echo mustache_tmpl_parse($motorcycle_action_buttons);
        ?>


        <?php if( !isset($cat_header) ){?>
			<div class="clear"></div>
		<?php }else{?>
			
			<div class="sliderCont">
			<?php 
				$catImage = "dirt_bike.jpg";
				if($top_parent==TOP_LEVEL_CAT_STREET_BIKES){
					$catImage = "street_bike.jpg";
				}else if($top_parent==TOP_LEVEL_CAT_ATV_PARTS){
					$catImage = "atv.jpg";
				}else if($top_parent==TOP_LEVEL_CAT_UTV_PARTS){
					$catImage = "utv.jpg";
				}
			?>
			<img src="<?php echo $new_assets_url;?>images/category_banners/<?php echo $catImage;?>" style="height:300px;" />
			</div>
			<div class="clear"></div>
			<div class="productNav" style="margin-top: -5px;">
			  <div class="productNavCont">
					<ul style="width: 100%; margin: 0 auto; padding: 0px;">
						<?php
						if( !empty($nav_categories) ){				
							$county = 1;
							foreach( $nav_categories as $keyy=>$navRow ){
			
						?>
							<li><a class="topNavAnchors" href="javascript:;" id="<?php echo $county;?>" onClick="showSubNav(<?php echo $county;?>);"><?php echo $navRow['label'];?></a>
								<?php if($county<count($nav_categories)){?>
									<span>|</span>
								<?php }?>
								<?php if( !empty($navRow['subcats']) ){?>
								
								<ul id="nav<?php echo $county;?>" class="active SubNavs" style="display:none;">
									<span class="toolTip"></span>
									<?php foreach($navRow['subcats'] as $subNavRow){?>
									<li><a href="<?php echo base_url()."shopping/productlist".$subNavRow['link'];?>"><?php echo $subNavRow['name'];?></a></li>
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
		
		<?php }?>
		
		<style>
			.button_no{
				background: #EEE !important;
				border: 1px #DDD solid !important;
				color: #BBB !important;
			}
		</style>

	<!-- CONTENT WRAP =========================================================================-->

		<?php  echo @$mainContent; ?>
		
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->


</div>
<!-- END WRAPPER ==========================================================================-->

<?php echo @$footer; ?>	


<script>		
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
    } catch (err) {
        console.log("MagnificPopup error: " + err);
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
echo $CI->load->view("master/tracking",	array( 
	"store_name" =>	$store_name,
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
