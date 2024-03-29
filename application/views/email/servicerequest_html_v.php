<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
		<title>PowerSport Technologies</title>
	</head>

	<body style="color: #333;
	font-family: 'Helvetica', sans-serif;
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
								
								<h1 style="margin:5px 0px;color:#006eac;font:24px 'Helvetica', sans-serif;">
									A Service Schedule has been requested.
								</h1>
								<br>
								
								<p style="margin:5px 0px;color:#666;font:16px 'Georgia', serif;">
									Contact Info:
								</p>
								<p>
								  Name: <?php echo $fname.' '.$lname; ?><br />
									Email: <?php echo $email; ?><br />
									Phone: <?php echo $phone; ?><br />
									Address: <?php echo $address; ?><br />
									City: <?php echo $city; ?><br />
									State: <?php echo $state; ?><br />
									ZipCode: <?php echo $zipcode; ?><br />
								</p>
								<br />
								
							</td>
						</tr>
						<tr>
							<td>
								
								<p style="margin:5px 0px;color:#666;font:16px 'Georgia', serif;">
									Vehicle Being Serviced:
								</p>
								<p>
								  Make: <?php echo $make; ?><br />
									Model: <?php echo $model; ?><br />
									Year: <?php echo $_year; ?><br />
									Vin#: <?php echo $vin; ?><br />
									Miles: <?php echo $miles; ?><br />
								</p>
								<br />
								
							</td>
						</tr>
						<tr>
							<td>
								
								<p style="margin:5px 0px;color:#666;font:16px 'Georgia', serif;">
									Service Needs:
								</p>
								<p>
								  Needs: <?php echo $needs; ?><br />
									Appointment: <?php echo $appointment; ?><br />
								</p>
								<br />
								
							</td>
						</tr>
						<tr>
							<td>
								
								<p style="margin:5px 0px;color:#666;font:16px 'Georgia', serif;">
									Prior Service History:
								</p>
								<p>
								  Serviced Before: <?php echo $serviced; ?><br />
									Last In: <?php echo $lastin; ?><br />
									Work Done: <?php echo $workdone; ?><br />
								</p>
								<br />
								
							</td>
						</tr>
						<tr>
							<td>
							</td>
						</tr>
					</table>
					<!-- END CONTENT -->
					<img src="<?php echo @$emailShadowImg; ?>" border="0" width="630" />
					
					
				</td>
			</tr>
			<!-- END CONTENT CELL////////////////////////////////////////////////////////////-->
			
			
			<!-- FOOTER /////////////////////////////////////////////////////////////////////-->
			<tr>
				<td style="background:#F6F6F6;">
					<p style="margin:15px 0px;color:#777;font:11px 'Helvetica', sans-serif;float:left;">
						Copyright &copy; <?php echo date('Y'); ?>, PowerSport Technologies. All Rights Reserved
					</p>
				</td>
			</tr>
			<!-- END FOOTER /////////////////////////////////////////////////////////////////-->
		
		</table>
		
	</body>
</html>
