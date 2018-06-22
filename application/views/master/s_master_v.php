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

	<!-- CSS LINKS -->
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css_front/media.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/style-checkout.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/css/flexisel.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $s_assets; ?>/font-awesome-4.1.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $new_assets_url; ?>/css/responsive.css" type="text/css" />

	<!-- END CSS LINKS --> 
	

	<!-- jQuery library -->


	<script>
		var s_base_url = '<?php echo $s_baseURL; ?>';
		var base_url = '<?php echo base_url(); ?>';
		shopping_cart_count = <?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0; ?>;
	</script>
	
	<script>
		$(document).ready(function(){
			$(".mob-menu").click(function(){
				$(".mb-drpdwn").toggle('slow');
			});
		});
	</script>
	
	<!-- Flexisel JS -->
	<script type="text/javascript" src="<?php echo $s_assets; ?>/js/jquery.flexisel.js"></script>

	<link rel="stylesheet" href="<?php echo jsite_url("/basebranding.css"); ?>" />
	<link rel="stylesheet" href="<?php echo jsite_url("/custom.css"); ?>" />
    <?php echo jget_store_block("bottom_header"); ?>

</head>

<body class="body">
<?php echo jget_store_block("top_body"); ?>

<!-- WRAPPER ==============================================================================-->
<div class="wrap">
	
	
	<!-- HEADER =============================================================================-->
	<div class="header_wrap">
		
		<div class="header_content" style="padding-bottom:0px;">
		
			<style>
			
			#desktop_cart{
				float: right;
				padding: 70px 70px 0px 0px;
			}
			#desktop_cart .phonee{
				padding: 10px !important;
				border-radius: 3px !important;
				/*text-shadow: 1px 1px #D1B9D5 !important;*/
				color: #000 !important;
				/*-webkit-text-fill-color:#663399 !important;*/
			}
			#desktop_cart .cartt{
				padding: 10px !important;
				border-radius: 3px !important;
				cursor:pointer !important;
			}
			</style>
			<!-- LOGO -->
			<div class="logo">
				<img src="/logo.png">
			</div>
			<div id="desktop_cart">
				<?php if(@$accountAddress['phone']){?>
				<strong class="phonee"><?php echo $accountAddress['phone']; ?></strong>
				<?php } ?>
				<a href="<?php echo base_url();?>shopping/cart">
					<strong class="cartt" onClick="window.location='<?php echo base_url();?>shopping/cart'">
						<i class="fa fa-shopping-cart"></i> Cart (<?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0 ; ?>)
					</strong>
				</a>
			</div>
			<!-- END LOGO -->
			
			<div class="clear"></div>
			
			<div id="mobile_cart" style="display:none;">
				<?php if(@$accountAddress['phone']){?>
				<strong class="phonee"><?php echo $accountAddress['phone']; ?></strong>
				<?php }?>
				<strong class="cartt" onClick="window.location='<?php echo base_url();?>shopping/cart'">
					<a href="<?php echo base_url();?>shopping/cart">
						<i class="fa fa-shopping-cart"></i> Cart (<?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0 ; ?>)
					</a>
				</strong>
			</div>
			 
			
		</div>

	</div>
	<!-- END HEADER ===========================================================================-->	

	<?php  echo @$mainContent; ?>
			
	<?php echo @$sidebar; ?>				
		
		<div class="clear"></div>
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->


</div>
<!-- END WRAPPER ==========================================================================-->

<?php echo @$footer; ?>	

<?php // echo @$script; ?>

<?php

$CI =& get_instance();
echo $CI->load->view("master/tracking", array(
	"store_name" => $store_name,
	"product" => @$product,
	"ga_ecommerce" => false,
	"show_ga_conversion" => true

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
