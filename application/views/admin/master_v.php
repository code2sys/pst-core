<?php
	$CI =& get_instance();
	$assets = $CI->config->item("s_assets");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	
	<title>PowerSport Technologies</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo @$descr; ?>">
	<meta name="keywords" content="<?php echo @$keywords; ?>">
	<meta name="author" content="Cloverfield Creations">
	
	<!-- CSS LINKS -->
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/admin_nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/admin_nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/admin_style.css" type="text/css" />
	<!--<link rel="stylesheet" href="<?php echo $assets; ?>/font-awesome-4.1.0/css/font-awesome.min.css">-->
	<link rel="stylesheet" href="<?php echo $assets; ?>/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $assets; ?>/css_front/jquery.dataTables.min.css" type="text/css" >
	<!--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" type="text/css" >-->
	
	<!-- END CSS LINKS --> 
	
	    <link href="<?php echo $assets; ?>/wdCalendar/css/dp.css" rel="stylesheet" type="text/css" />
  <!-- <script src="<?php echo $assets; ?>/js/jquery-1.7.2.js"></script> -->
    <script
            src="https://code.jquery.com/jquery-1.9.1.min.js"
            integrity="sha256-wS9gmOZBqsqWxgIVgA8Y9WcQOa7PgSIX+rPA0VL2rbQ="
            crossorigin="anonymous"></script>
  <script src="<?php echo $assets; ?>/wdCalendar/src/Plugins/datepicker_lang_US.js" type="text/javascript"></script>
  <script src="<?php echo $assets; ?>/wdCalendar/src/Plugins/jquery.datepicker.js"></script>
  <script src="<?php echo $assets; ?>/js/jquery-ui-1.10.4.js"></script>
  <script src="<?php echo $assets; ?>/js_front/jquery.dataTables.min.js"></script>

	<script src="<?php echo $assets; ?>/js/jquery.flot.min.js"></script>
	<script src="<?php echo $assets; ?>/js/jquery.flot.time.js"></script>
	<script src="<?php echo $assets; ?>/js/jquery.flot.symbol.js"></script>
	<script src="<?php echo $assets; ?>/js/jquery.flot.categories.js"></script>
	<script src="<?php echo $assets; ?>/js/jquery.flot.orderBars.js"></script>


	<?php echo @$script; ?>
   <script>
   	var base_url = '<?php echo base_url(); ?>';
		var s_base_url = '<?php echo $s_baseURL; ?>';
   </script>
</head>
<body class="body">

<style>
/* Background Loading (start)*/
#loading-background {
	width: 100%;
	height: 100%;
	top: 0px;
	left: 0px;
	position: fixed;
	display: block;
	opacity: 0.7;
	background-color: #fff;
	z-index: 99;
	text-align: center;
}
#loading-background img {
	margin: 0 auto;
	position: absolute;
	top: 250px;
	z-index: 100;
}
/* Background Loading (end)*/

</style>

<div id="loading-background" style="display:none;">
  <img src="/qatesting/newassets/images/ajax-loader-black.gif" alt="Loading..." />
</div>
<!-- WRAPPER =============================================================================================-->
<div class="wrap">
	
	
	
	<!-- HEADER ============================================================================================-->
	<div class="head_wrap">
		<div class="head_content">
			<div class="logo">
				<a href=""><img src="<?php echo $assets; ?>/images/admin_logo.png" border="0"></a>
			</div>
			<div class="title">
				<p><b><em>Welcome to your Admin Panel!</em></b></p>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<!-- END HEADER ========================================================================================-->
			
				<!-- NAVAGATION -->
				<?php echo @$nav ?>
			<!-- END NAVAGATION -->
			<div class="clear"></div> 
	
	
	<?php  echo @$mainContent; ?>			
		
		<div class="clear"></div>
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->


</div>
<!-- END WRAPPER ==========================================================================-->

<?php echo @$footer; ?>	



</body>
</html>













