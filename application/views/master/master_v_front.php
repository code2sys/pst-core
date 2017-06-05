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

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">		
	<title><?php echo $page_title; ?></title>
	<?php if (SEARCH_NOINDEX): ?>
		<meta name="robots" content="noindex" />
	<?php endif; ?>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="<?php print htmlentities($meta_description, ENT_QUOTES | ENT_COMPAT); ?>">
<meta name="keywords" content="<?php echo htmlentities($meta_keywords, ENT_QUOTES | ENT_COMPAT);  ?>">
	<?php echo @$metatag; ?>
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/style.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.carousel.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.theme.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.transitions.css" />	
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/font-awesome.css" />	
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/responsive.css" />	
	
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lucida+Sans:400,500,600,700,900,800,300" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Helvetica:400,500,600,700,900,800,300%22%20/%3E" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,500,600,700,900,800,300%22%20/%3E">
	
	
	<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/owl.carousel.js"></script>		

	<script>
		$(document).ready(function(){
			$(".sidebar-menu").click(function(){
				$(".mb-drpdwn").toggle('slow');
			});
		});
	</script>
	
	<?php echo @$header; ?>
	<link rel="stylesheet" href="<?php echo jsite_url("/basebranding.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />

</head>

<body>
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
	<!--<div class="header_b">
		<div class="container_b">
			<a href="<?php echo base_url();?>" class="logoCont fltL logo-tp_b">
				<img src="/assets/images/power-sports-logo.png" width="200" height="50">
			</a>
			<div class="vehicleCategory">
				<a href="<?php echo base_url('streetbikeparts'); ?>" class="streetBike stre-bk_b">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/streetBike.png">
					</div>
					<span>Shop Street Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('vtwin'); ?>" class="vtwin">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/vtwin.png">
					</div>
					<span id="svp">Shop VTwin Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('dirtbikeparts'); ?>" class="bike">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/bike.png">
					</div>
					<span>Shop Dirt Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('atvparts'); ?>" class="atv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/atv.png">
					</div>
					<span>Shop ATV Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('utvparts'); ?>" class="utv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/utv.png">
					</div>
					<span>Shop UTV Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('Motorcycle_Gear_Brands'); ?>" class="last">
					<div class="stre-bk_b" style="height:42px;">
						<img src="<?php echo $new_assets_url; ?>images/brand-tag.png">
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
						<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>">Account</a></li>
						<li><a href="javascript:void(0);" onclick="openLogin();">Login/Signup</a></li>
					</ul>
				</div>		
				<div class="cl"><a href="tel:<?php echo CLEAN_PHONE_NUMBER; ?>">
					<img src="<?php echo $new_assets_url; ?>images/cl.png"><br>Call</a>
				</div>
				<div class="crt">
					<a href="<?php echo base_url('shopping/cart'); ?>">
					<img src="<?php echo $new_assets_url; ?>images/kart.png"><br>Cart</a>
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
			<a href="<?php echo base_url();?>" class="logoCont fltL logo-tp_b">
				<img src="/logo.png" width="200" height="50">
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
						<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>">Account</a></li>
						<li><a href="javascript:void(0);" onclick="openLogin();">Login/Signup</a></li>
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
						<img src="<?php echo $new_assets_url; ?>images/streetBike.png">
					</div>
					<span id="stp">Shop Street Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('vtwin'); ?>" class="vtwin">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/vtwin.png">
					</div>
					<span id="svp">Shop VTwin Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('dirtbikeparts'); ?>" class="bike">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/bike.png">
					</div>
					<span id="sdp">Shop Dirt Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('atvparts'); ?>" class="atv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/atv.png">
					</div>
					<span id="sap">Shop ATV Parts & Accessories</span>
				</a>
				<a href="<?php echo base_url('utvparts'); ?>" class="utv">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/utv.png">
					</div>
					<span id="sup">Shop UTV Parts & Accessories</span>
				</a>				
				<a href="<?php echo base_url('Motorcycle_Gear_Brands'); ?>" class="last">
					<div class="stre-bk_b" style="height:45px;">
						<img src="<?php echo $new_assets_url; ?>images/brand-tag.png">
					</div>
					<span id="sbb">Shop by Brand</span>
				</a>
			</div>	
			<div class="clear"></div>
		</div>
	</div>
        
	<!--<div class="searchHolder search-two">
		<form action="<?php //echo base_url(); ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
			<input id="search" name="search" placeholder="Search Parts and Apparel" class="search-bx" style="float:left;" />
			<a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
		</form>
		<div class="clear"></div>
	</div>-->
	
	<div class="sw slider">
		<div id="owl-demo" class="owl-carousel owl-theme">
			<?php foreach( $bannerImages as $image ) { ?>
			  <div class="item"><img src="<?php echo base_url('media/'.$image['image']); ?>"></div> 
			<?php } ?>
		  <!--<div class="item"><img src="<?php echo $new_assets_url; ?>images/banner2.png"></div> 
		  <div class="item"><img src="<?php echo $new_assets_url; ?>images/banner3.png"></div>-->
		</div>
		<div class="clear"></div>
	</div>

	<?php if (!defined("MOTORCYCLE_SHOP_DISABLE") || !MOTORCYCLE_SHOP_DISABLE): ?>
	<div class="sw bg">
		<div class="modal-lg">
			<?php if (MOTORCYCLE_SHOP_NEW): ?>
			<div class="col-md-3 wrap-col">
				<a href="<?php echo site_url("Motorcycle_List?fltr=new") ?>"><h2>shop new models</h2></a>				
			</div>
			<?php $moto_width = 3; ?>
			<?php else: ?>
			<?php $moto_width = 4; ?>
			<?php endif; ?>
			<div class="col-md-<?php echo $moto_width; ?> wrap-col">
				<a href="<?php echo site_url("Motorcycle_List?fltr=pre-owned") ?>"><h2>shop pre-owned</h2></a>
			</div>
			<div class="col-md-<?php echo $moto_width; ?> wrap-col">
				<a href="<?php echo site_url("pages/index/financerequest") ?>"><h2>apply for Financing</h2></a>				
			</div>
			<div class="col-md-<?php echo $moto_width; ?> wrap-col">
				<a href="<?php echo site_url("pages/index/servicerequest") ?>"><h2>schedule service</h2></a>			
			</div>
		</div>
	</div>
	
	<div class="sw filterBar">
		<div class="container_b">
			<h1 class="head-txt"> featured models: <span> <a href="<?php echo base_url('Motorcycle_List');?>">shop more</a> </span></h1>
			<div id="hotels-flats" class="owl-carousel">
				<?php foreach($featured as $feature) { ?>
					<?php $title = str_replace(' ', '_', trim($feature['title']));?>
					<a href="<?php echo base_url(strtolower($feature['type']).'/'.$title.'/'.$feature['sku']);?>">
						<div class="item">
							<div class="item-box">
								<img class="lazyOwl" data-src="<?php echo base_url().'media/'.$feature['image_name']; ?>" alt="Motorcycle Image">
								<p> <?php echo $feature['title'];?></p>
								<?php if( $feature['call_on_price'] == '1' ) { ?>
									<h2 class="cfp">Call For Price</h2>
								<?php } else { ?>
									<h2>Sale Price: &nbsp;$<?php echo $feature['sale_price'];?></h2>
								<?php } ?>
								<!--<h2>CALL FOR PRICE</h2>-->
							</div>
						</div>
					</a>
				<?php } ?>
			</div>
		</div>	
	</div>
	<?php endif; ?>
	<div class="sw brd">
		<div class="container_b">
			<div class="featured-listings">
				<h3> OUR TOP BRANDS </h3>
				<a class="rdrct-lnk" href="<?php echo site_url('Motorcycle_Gear_Brands');?>">Shop all brands </a>
				<div class="panel-body">
					<ul class="lstng">
					<?php foreach( $featuredBrands as $key => $val ) { ?>
						<li class="ftrdb">
							<a class="brnd-nm" href="<?php echo site_url($val['slug']);?>">
								<img src="<?php echo site_url('media/'.$val['image']);?>"><span class="bn"><?php echo $val['name'];?></span>
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
</script>
	<script type="application/javascript" src="<?php echo jsite_url('/custom.js'); ?>" ></script>

	
