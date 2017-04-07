<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
		<title><?php echo WEBSITE_NAME; ?></title>
	</head>

	<body style="color: #333;
	font-family: 'Doppio One', sans-serif;
	font-size: 14px;
	font-style: normal;
	line-height: normal;
	font-variant: normal;
	-webkit-font-smoothing: antialiased;
	-moz-font-smoothing: antialiased;
	font-smoothing: antialiased;
	text-transform: none;
	text-decoration: none;
">

		<table width="630" cellpadding="10" cellspacing="0" align="center" style="background:#FFF;border:1px solid #FFF;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;
		-moz-box-shadow:0px 0px 4px #BBB;-webkit-box-shadow:0px 0px 4px #BBB;box-shadow:0px 0px 4px #BBB;">
			
			<!-- HEADER /////////////////////////////////////////////////////////////////////-->
			<tr>
				<td style="border-bottom:1px #DDD solid;">
				</td>
			</tr>
			<!-- END HEADER /////////////////////////////////////////////////////////////////-->
			
			
			
			<!-- CONTENT CELL////////////////////////////////////////////////////////////////-->
			<tr>
				<td style="background:#EEE;border-bottom:1px #DDD solid;">
					
					<!-- CONTENT -->
					<table width="630" cellpadding="10" align="center" style="background:#FFF;border:1px solid #DDD;">
						<tr>
							<td>

								
								<h1 style="margin:5px 0px;color:#F63;font:24px 'Helvetica', sans-serif;">
									Thank you for your purchase.
								</h1>
								<br>
								
								<p style="margin:5px 0px;color:#666;font:16px 'Verdana', sans-serif;">
									Attached please find a PDF version of your Order Confirmation with all your order details.<br /><br />
									
									<?php if(isset($ship_tracking_code)): ?>
									<?php if(isset($carrier)): ?>
									Your order is being shipped by <?php echo $carrier; ?>.<br />
									<?php endif; ?>
									Your tracking number is : <?php if(isset($trackingURL)): ?><a href="<?php echo $trackingURL; ?>"><?php echo $ship_tracking_code; ?></a>
																				<?php else: echo $ship_tracking_code; endif; ?>.<br /><br />
									<?php endif; ?>
									
									<br /<br /><br /><br /><br />
									Sincerely,<br />
									The <?php echo WEBSITE_NAME; ?> Team
								</p>
								<br>									
							</td>
						</tr>
					  <tr>
							<td>
							</td>
						</tr>
					</table>
					<!-- END CONTENT -->
					<img src="<?php echo $assets; ?>/email_images/shadow.png" border="0" width="630" />

					<!-- END CONTENT -->

				</td>
			</tr>
		<!-- FOOTER /////////////////////////////////////////////////////////////////////-->
			<tr>
				<td style="background:#F6F6F6;">
					<p style="margin:5px 0px;color:#444;font:11px 'Helvetica', sans-serif;float:left;">
						<?php
							$year = date("Y");
							if($year == 2013)
							echo $year;
							else
							echo "2013 - {$year}";
						?>
						Copyright &copy; <?php echo STYLED_HOSTNAME; ?>. All Rights Reserved.
					</p>
					
				</td>
			</tr>
			<!-- END FOOTER /////////////////////////////////////////////////////////////////-->	
		</table>

	</body>
</html>
