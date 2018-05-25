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
<!doctype html>
<html>
<?php
$new_assets_url = jsite_url("/qatesting/benz_assets/");
?>
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

	<?php echo @$metatag; ?>
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/style.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.carousel.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.theme.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.transitions.css" />	
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/font-awesome.css" />	
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/responsive.css" />	
	

	<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
	<script src="<?php echo $new_assets_url; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/owl.carousel.js"></script>		

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
	
	<?php echo @$header; ?>
	<link rel="stylesheet" href="<?php echo jsite_url("/basebranding.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />
	
	<style>
		#top-cat img{
			min-height:135px;
		}
		#top-cat .ftrdb{
			min-height:240px;
		}
	</style>
    <?php echo jget_store_block("bottom_header"); ?>

</head>

<body class="master_v_front">
<?php echo jget_store_block("top_body"); ?>
<?php

$CI =& get_instance();

echo $CI->load->view("master/widgets/mainheader", array(
    "store_name" => $store_name,
    "s_baseURL" => $s_baseURL,
    "invoking_page" => "master_v_front"
), true);

$CI->load->helper("mustache_helper");
$motorcycle_action_buttons = mustache_tmpl_open("store_header_marquee.html");
echo mustache_tmpl_parse($motorcycle_action_buttons);
$motorcycle_action_buttons = mustache_tmpl_open("store_header_banner.html");
echo mustache_tmpl_parse($motorcycle_action_buttons);
?>


<div class="sw slider">
		<div id="owl-demo" class="owl-carousel owl-theme">
			<?php foreach( $bannerImages as $image ) { ?>
                    <div class="item"><a href="<?php echo $image['banner_link'];?>"><img src="/media/<?php echo $image['image']; ?>"></a></div>
			<?php } ?>
		  <!--<div class="item"><img src="<?php echo $new_assets_url; ?>images/banner2.png"></div> 
		  <div class="item"><img src="<?php echo $new_assets_url; ?>images/banner3.png"></div>-->
		</div>
		<div class="clear"></div>
	</div>

    <?php

    echo $CI->load->view("master/widgets/motorcycles", array(
        "featured" => $featured
    ), true);

    ?>
        
        <?php if (@$topVideo) { ?>
	<div class="sw brd">
		<div class="container_b">
			<div class="featured-listings">
                        <h3> OUR TOP Videos </h3>
                        <div class="panel-body">
                            <ul class="lstng" id="top-video">
                                <?php foreach ($topVideo as $key => $val) { ?>
                                    <li class="ftrdb">
                                        <iframe src="https://www.youtube.com/embed/<?php echo $val['video_url'];?>" frameborder="0" style="width: 90%;height:220px;" allowfullscreen></iframe>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>				
                    </div>
                </div>
            </div>
        <?php } ?>
        
        <?php if (!empty($featuredCategories)) { ?>
            <div class="sw brd">
                <div class="container_b">
                    <div class="featured-listings">
				<h3> OUR TOP Categories </h3>
				<!--<a class="rdrct-lnk" href="<?php echo site_url('Motorcycle_Gear_Brands');?>">Shop all brands </a>-->
				<div class="panel-body">
					<ul class="lstng" id="top-cat">
					<?php foreach( $featuredCategories as $key => $val ) { ?>
						<li class="ftrdb">
							<a class="brnd-nm" href="javascript:void(0)" onclick="setMainSearch(event, 'category', '<?php echo $val['category_id']; ?>');" id="<?php echo $val['category_id'] ?>" >
								<img src="<?php echo site_url('media/'.$val['image']);?>"><span class="bn"><?php echo $val['name'];?></span>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>				
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="sw brd container_for_top_brands">
		<div class="container_b" id="our_top_brands">
			<div class="featured-listings">
				<h3> OUR TOP BRANDS </h3>
				<a class="rdrct-lnk" href="<?php echo site_url('Motorcycle_Gear_Brands');?>">Shop all brands </a>
				<div class="panel-body">
					<ul class="lstng">
					<?php foreach( $featuredBrands as $key => $val ) { ?>
						<li class="ftrdb">
							<a class="brnd-nm" href="<?php echo site_url($val['slug']);?>">
                                <div class="spannerbox">
                                    <?php if ($val['image'] != ''): ?>
                                    <img src="<?php echo site_url('media/'.$val['image']);?>">
                                    <?php endif; ?>
                                </div>
                                <div class="labelbox">
                                    <span class=""><?php echo $val['name'];?></span>
                                </div>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
                <div style="clear: both"></div>
			</div>
		</div>
	</div>

<?php

if (!defined('DISABLE_TOP_RATED')) {
    define('DISABLE_TOP_RATED', false);
}

if (!DISABLE_TOP_RATED) {
    ?>
    <?php if (count(array_keys($topRated)) > 0): ?>
        <?php
        $CI =& get_instance();
        echo $CI->load->view("master/widgets/hotelsflatsslider", array(
            "extra_master_classes" => "podct",
            "wrapper_id" => "home_top_rated_products",
            "header_class" => "best mrgnbtm65",
            "header_text" => "Top <span>Rated</span> Products",
            "item_extra_class" => "padg-le",
            "box_extra_class" => "box",
            "rotating_things" => array_map(function($x) use ($CI) {
                return array(
                        "rendered_html" => $CI->load->view("master/widgets/front_end_rating_box", array(
                            "val" => $x
                        ), true)
                );
            }, $topRated)
        ), true);


        ?>
    <?php endif; ?>
<?php } ?>


	<div class="sw podct">
		<div class="container_b">						
			<div class="row">


				<div class="col-md-12 text-center cnet">
					<!--<hr class="tp">-->
					<div class="pr" style="padding-top:30px;">
						<?php echo $notice;?>
					</div>
				</div>
			</div>			
		</div>
	</div>
	
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
                preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
            },
            image: {
                tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
                titleSrc: function (item) {
                    return item.el.attr('title') + '<small><?php echo WEBSITE_NAME; ?>&trade;</small>';
                }
            }
        });

    } catch(err) {
	    console.log("Error magnificPopup: " + err);
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
	   var base_url = '<?php echo base_url(); ?>';
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
echo $CI->load->view("master/tracking", array( 
	"store_name" => $store_name	,
	"product" => @$product,
	"ga_ecommerce" => true,
	"show_ga_conversion" => false

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
