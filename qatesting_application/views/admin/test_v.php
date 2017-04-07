<html lang="en">
<head>
	
	<title><?php echo @$title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo @$descr; ?>">
	<meta name="keywords" content="<?php echo @$keywords; ?>">
	<meta name="author" content="Cloverfield Creations">
	
		<!-- CSS LINKS -->
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/admin_nav.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/css/admin_style.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $assets; ?>/font-awesome-4.1.0/css/font-awesome.min.css">

	
	    <link href="<?php echo $assets; ?>/wdCalendar/css/dp.css" rel="stylesheet" type="text/css" />
  <script src="<?php echo $assets; ?>/js/jquery-1.7.2.js"></script>
  <script src="<?php echo $assets; ?>/wdCalendar/src/Plugins/datepicker_lang_US.js" type="text/javascript"></script>
  <script src="<?php echo $assets; ?>/wdCalendar/src/Plugins/jquery.datepicker.js"></script> 
   <script type="text/javascript">
        $(document).ready(function() {           
            $("#temptime").datepicker({ picker: "<img class='picker' align='middle' alt=''/>" });
                 });
        
    </script>
    <style>
    img.picker
{
	    height:16px;
	    width:16px;
	    margin-left:-19px;
	    vertical-align:middle;
	    padding-bottom: 2px;
	    cursor:pointer;
	    border:none;		  
}
    </style>
</head>
<body>
            
				<div>
            <input type="text"  id='temptime'/>
        </div>
</body>
</html>