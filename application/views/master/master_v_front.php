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

<body>
<?php echo jget_store_block("top_body"); ?>

	<div class="topBar_b">
		<div class="container_b">
			<p class="creditCar_b fltL_b">
				<span>Ph : <?php echo $store_name['phone'];?></span>				
				<a href="<?php echo site_url('pages/index/contactus') ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> MAP & HOURS</a>
				<?php if (FALSE !== ($string = joverride_viewpiece("master-master_v_front-1"))) { echo $string; } ?>
			</p>
			<div class="loginSec_b navbar-right">
				<?php if(@$_SESSION['userRecord']): ?>
					<b>Welcome: <?php echo @$_SESSION['userRecord']['first_name']; ?></b> <span class="fltR seperator_b">|</span> <b><a href="<?php echo $s_baseURL.'welcome/logout'; ?>"><u>Logout</u></a></b>
					<?php if($_SESSION['userRecord']['admin'] || $_SESSION['userRecord']['user_type'] == 'employee'): ?>
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
					<li class="icon accountLink"><a href="<?php echo $s_baseURL.'checkout/account'; ?>">Account</a></li>
					<li class="icon wishListLink"><a href="<?php echo base_url('/shopping/wishlist'); ?>">Wish List</a></li>
					<li class="icon shopLink"><a href="<?php echo base_url('shopping/cart'); ?>">Shopping Cart (<span id="shopping_count"><?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0 ; ?></span>)</a></li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
	</div>	

        <div class="header_b">
		<div class="container_b">
			<a href="<?php echo base_url();?>" class="logoCont fltL logo-tp_b">
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
					<img class="cl-img" src="<?php echo $new_assets_url; ?>images/cl.png"><br>Call</a>
				</div>
				<div class="crt">
					<a class="cel" href="<?php echo base_url('shopping/cart'); ?>">
					<img class="cl-img" src="<?php echo $new_assets_url; ?>images/kart.png"><br>Cart</a>
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
            <?php
            $CI =& get_instance();
            echo $CI->load->view("search_placeholder", array(), true);
            ?>
			<div class="clear"></div>						
		</div>
            <div class="container_b">
			<div class="vehicleCategory">
				<?php require(__DIR__ . "/../navigation_fragment.php"); ?>

			</div>	
			<div class="clear"></div>
		</div>
	</div>
        

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
	<div class="sw brd">
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
			</div>
		</div>
	</div>
	
	<div class="sw podct">
		<div class="container_b">						
			<div class="row">			
				<div class="col-md-12 col-sm-12 sldr-section">				
					<h1 class="best mrgnbtm65">Top<span> Rated </span> Products</h1>
					<div id="hotels-flats-1" class="owl-carousel">
						<?php foreach( $topRated as $key => $val ) { ?>
						<div class="item  padg-le">							
							<div class="box">
								<img class="lazyOwl" alt="Product Image" src="<?php echo site_url('productimages/'.$val['images'][0]['path']);?>" style="display: inline;">
								<h2><?php echo $val['label'];?></h2>
								<p>
								<?php for( $i=1; $i<=$val['rating']; $i++ ) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>
								</p>
								<p><?php echo substr($val['review'], 0, 75 ).'...';?></p>
								<a href="<?php echo site_url('shopping/item/'.$val['part_id']);?>" class="btn btn-primary btn-secc">Check Details</a>
							</div>
						</div>
						<?php } ?>						
					</div>	
				</div>					
				<?php if (false): ?>
				<div class="col-md-3 testi pull-right fb-frem">
					<h3> Get Social</h3>					
					<span>&nbsp;</span>
					<div class="social">
						<ul>
							<?php if(FALSE && @$SMSettings['sm_fblink']): ?>
							<li><a class="active scl" data-link="facebook-page" href="javascript:void(0);">Facebook</a></li>
							<?php endif; ?>
							<?php if(FALSE && @$SMSettings['sm_twlink']): ?>
							<li><a class="scl" data-link="twitter-page" href="javascript:void(0);">Twitter</a></li>
							<?php endif; ?>
							<?php if(@$SMSettings['sm_gplink']): ?>
							<li><a class="scl" data-link="google-page" href="javascript:void(0);">Google+</a></li>
							<?php endif; ?>
						</ul>
						<?php if(@$SMSettings['sm_fblink']): ?>
						<div class="facebook-page social-page">
							<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2FPowerSortTechnologies&amp;width&amp;height=590&amp;colorscheme=light&amp;show_faces=true&amp;header=true&amp;stream=true&amp;show_border=true&amp;appId=313201365506980" height="590" frameborder="0" style="border:0" allowfullscreen></iframe>
						</div>
						<?php endif; ?>
						<?php if(@$SMSettings['sm_twlink']): ?>
						<div class="twitter-page social-page" style="display:none;">
							<a class="twitter-timeline" href="<?php echo $SMSettings['sm_twlink']; ?>" data-widget-id="717316381994123265">Tweets by @<?php echo basename($SMSettings['sm_twlink']); ?></a>
								<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id))	{js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</div>
						<?php endif; ?>
						<?php if(@$SMSettings['sm_gplink']): ?>
						<div class="google-page social-page" style="display:none;">						
							<script src="https://apis.google.com/js/platform.js"></script>						
							<div class="g-page" data-href="https://plus.google.com/105422634089178330616/posts/hdbPtrsqMXQ/" data-rel="publisher"></div>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
				
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

<script type="text/javascript">

$(window).load(function() {
    try {

        $("#flexiselDemo1").flexisel();
        $("#flexiselDemo2").flexisel({
            enableResponsiveBreakpoints: true,
            responsiveBreakpoints: {
                portrait: {
                    changePoint: 480,
                    visibleItems: 1
                },
                landscape: {
                    changePoint: 640,
                    visibleItems: 2
                },
                tablet: {
                    changePoint: 768,
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
                    changePoint: 480,
                    visibleItems: 3
                },
                landscape: {
                    changePoint: 640,
                    visibleItems: 4
                },
                tablet: {
                    changePoint: 768,
                    visibleItems: 5
                }
            }
        });

        $("#flexiselDemo4").flexisel({
            clone: false
        });

    } catch(err) {
        console.log("Error in flexiselDemo: " + err);
    }
    
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
