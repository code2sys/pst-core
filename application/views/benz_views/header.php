<!doctype html>
<html>
<?php
	$new_assets_url = jsite_url(  "/qatesting/benz_assets/" );
	$media_url = jsite_url("/media/");
	?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<?php echo @$metatag; ?>
	
	<!--Motercycle Content Start-->
	<!--Motercycle Content End-->
	
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/style.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.carousel.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.theme.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/owl.transitions.css" />	
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/font-awesome.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/lightslider.css" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>/css/responsive.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo jsite_url('/basebranding.css'); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url('/custom.css'); ?>" />
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>css/jquery.fancybox.css" />


	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lucida+Sans:400,500,600,700,900,800,300" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Helvetica:400,500,600,700,900,800,300%22%20/%3E" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,500,600,700,900,800,300%22%20/%3E">
	
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/owl.carousel.js"></script>	
	<script src="<?php echo $new_assets_url; ?>js/lightslider.js"></script>
	<script src="<?php echo $new_assets_url; ?>js/jquery.fancybox.pack.js"></script>


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
	
	
</head>

<body>
	<div class="topBar_b">
		<div class="container_b">
			<p class="creditCar_b fltL_b">
				<span>Ph : <?php echo $store_name['phone'];?></span>				
				<a href="<?php echo site_url('pages/index/contactus') ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> MAP & HOURS</a>				
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
				<img src="<?php echo jsite_url("/logo.png"); ?>" width="200" height="50">
			</a>
			<div class="vehicleCategory">
				<a href="<?php echo base_url('streetbikeparts'); ?>" class="streetBike stre-bk_b">
					<div class="stre-bk_b">
						<img src="<?php echo $new_assets_url; ?>images/streetBike.png">
					</div>
					<span>Shop Street Parts & Accessories</span>
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
				<a class="last" href="<?php echo base_url('Motorcycle_Gear_Brands'); ?>">
					<div class="stre-bk_b" style="height:42px;">
						<img src="<?php echo $new_assets_url; ?>images/brand-tag.png">
					</div>
					<span>Shop by Brand</span>
				</a>
			</div>			
			<div class="side-hdr">
				<div class="sidebar-menu">
					<span> <i class="fa fa-bars" aria-hidden="true"></i> Menu</span>
					<ul class="mb-drpdwn">
						<li><a href="<?php echo base_url('streetbikeparts'); ?>">Shop Street</a></li>
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
					<div class="stre-bk_b" style="height:42px;">
						<img src="<?php echo $new_assets_url; ?>images/brand-tag.png">
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
			<input id="search" name="search" placeholder="Search" class="search-bx" style="float:left;" />
			<a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
		</form>
		<div class="clear"></div>
	</div>-->

	
	<?php  echo @$mainContent; ?>
	
	<?php
	$new_assets_url = jsite_url( "/qatesting/newassets/" );
	?>
	<div class="sw footer clear">
		<div class="container_b">			
			<div class="one-fifth">
				<h3 class="aut-title">About <span><?php echo $store_name['company'];?></span></h3>
				<ul class="clear">
					<li><a href="<?php echo site_url('pages/index/aboutus');?>">About Us</a></li>
				</ul>				
			</div>
			<?php
			jprint_interactive_footer($pages); ?>
			?>

			<div class="one-fifth map">
				<h3>Contact Us</h3>
				<ul class="clear">
					<li>Address: <?php echo $store_name['street_address'].' '.$store_name['city'].' '.$store_name['state'];?></li>
					<li><img src="<?php echo $new_assets_url; ?>images/mobile.png"> <?php echo $store_name['phone'];?></li>
					<li><img src="<?php echo $new_assets_url; ?>images/footer-email.png"> <?php echo $store_name['email'];?> </li>
				</ul>
				<h3 class="aut-title">Payment Methods</h3>
				<a href="<?php echo site_url('pages/index/paymentoptions');?>">
					<img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/cc-badges-ppppcmcvdam.png" alt="Pay with PayPal, PayPal Credit or any major credit card" />
					<!--<img class="crdt" src="<?php echo $new_assets_url; ?>images/Credit-Cards.jpg">-->
				</a>
			</div>
			<div class="one-fifth">
				<h3>find us on</h3>
				<?php if(@$SMSettings['sm_fblink']): ?>
				<a class="social" href="<?php echo @$SMSettings['sm_fblink']; ?>" target="_blank">
					<img src="<?php echo $new_assets_url; ?>images/f.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_twlink']): ?>
				<a class="social" href="<?php echo $SMSettings['sm_twlink']; ?>" target="_blank">
					<img src="<?php echo $new_assets_url; ?>images/t.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_ytlink']): ?>
				<a class="social" href="<?php echo $SMSettings['sm_ytlink']; ?>" target="_blank">
					<img src="<?php echo $new_assets_url; ?>images/youtube1.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_gplink']): ?>
				<a class="social" href="<?php echo $SMSettings['sm_gplink']; ?>" target="_blank">
					<img src="<?php echo $new_assets_url; ?>images/g+.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_insta']): ?>
				<a class="social" href="<?php echo $SMSettings['sm_insta']; ?>" target="_blank" style="color:#F00;">
					<img src="<?php echo $new_assets_url; ?>images/instragram.png" alt="Benzaitens">
				</a>
				<?php endif; ?>
				<h3 class="nwsltr">newsletter</h3>
				<form action="" class="form_standard">
					<input type="text" id="newsletter" name="newsletter">
					<input type="button" value="SUBMIT" onclick="submitNewsletter();">	
				</form>
			</div>
			<div class="img-footer">
				<a href="http://powersporttechnologies.com"><img src="<?php echo $new_assets_url; ?>images/powered-logo.png"  class="powerlogo-a"/></a>
			</div>
			<hr class="ftr-line">		
		</div>
	</div>
	
<?php
$CI =& get_instance();
echo $CI->load->view("braintree", array(
        "store_name" =>	$store_name
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
		var pg = $('a', $(this)).html();
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
				//alert(result);
			});
		}
	});
	
		$(document).ready(function() {
 
		$("#owl-demo").owlCarousel({
		 
			  navigation : true,
			  slideSpeed : 300,
			  paginationSpeed : 400,
			  singleItem:true
		 
		 
		  });
		 
		});
		
		$(".brandsearch").keyup(function () {
			_this = this;
			// Show only matching TR, hide rest of them
			$.each($(".brandsfilter").find(".brandfltr"), function () {
				//console.log($(this).text());
				if ($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) == -1)
					$(this).hide();
				else
					$(this).show();
			});
		});
	
		$(document).ready(function() { 
		  $("#homes-for-rent").owlCarousel({
			items : 4,
			lazyLoad : true,
			navigation : true
		  }); 
		  $("#hotels-flats").owlCarousel({
			items : 4,
			lazyLoad : true,
			navigation : true
		  }); 
		 
		});
		
		$(document).ready(function() { 
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
		var condition = "<?php echo $_GET['fltr'];?>";
		
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
		
		//var url1 = cndn+"&brands="+brnd+"&categories="+ctgr+"&years="+yrs+"&vehicles="+vhcl;
		//alert(url1);
		var url = "<?php echo site_url('Motorcycle_List');?>?"+url1;
		window.location.href = url;
		
		// var ajax_url = "<?php echo site_url('welcome/filterMotorcycle');?>";
		// $.post( ajax_url, {'brands':brands,'years':years,'categories':categories,'vehicles':vehicles, 'condition':condition}, function( result ){
			// $('.prdts').html(result);
			// //alert(result);
		// });
   });
  
</script>
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
<!-- BEGIN: Google Trusted Stores -->
<script type="text/javascript">
  var gts = gts || [];

  gts.push(["id", "479602"]);
  gts.push(["badge_position", "BOTTOM_RIGHT"]);
  gts.push(["locale", "en_us"]);
  <?php if(@$product['partnumber']): ?>
  gts.push(["google_base_offer_id", "<?php echo $product['partnumber']; ?>"]);
  <?php endif; ?>
  gts.push(["google_base_subaccount_id", "1108548223"]);
  gts.push(["google_base_country", "US"]);
  gts.push(["google_base_language", "en_us"]);

  (function() {
    var gts = document.createElement("script");
    gts.type = "text/javascript";
    gts.async = true;
    gts.src = "https://www.googlecommerce.com/trustedstores/api/js";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(gts, s);
  })();
</script>
<!-- END: Google Trusted Stores -->

<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1052220103;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>

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
	//function showVideo(vidId) {
	//	$("#mainVideo")[0].src = "https://www.youtube.com/embed/"+vidId+"?rel=0&autoplay=1";
	//}
	
</script>
	<link rel="stylesheet" href="<?php echo jsite_url('/basebranding.css'); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url('/custom.css'); ?>" />

</body>
</html>	
