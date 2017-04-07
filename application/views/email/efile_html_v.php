<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
		<title>Wholesalers</title>
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
								<?php if(is_array($attachment)): foreach($attachment as $att): ?>
								  <a href="<?php echo $baseURL; ?>media/<?php echo $att; ?>.zip">Click here</a> to download your <?php echo $att; ?> ebook zip file:
								<?php endforeach; endif; ?>
								<br>
								
								<p style="margin:5px 0px;color:#666;font:16px 'Verdana', sans-serif;">
									<b>Your ebook(s) consists of the following files:</b><br />
									.epub - Apple and /or Android<br />
									.mobi - Kindle Version<br />
									.pdf - can be downloaded to your PC, Apple products and Android (not searchable).<br />
									<br />
									<b>Downloading your eBook onto an eReader device:</b><br />
									-- You can open up the epub, mobi and pdf files directly onto your Android tablet or iPad.<br />
									-- To open up your file onto a Kindle Reader you will need to upload the .mobi file onto your personal computer 
									and then transfer it from your computer to your kindle.<br />
									-- To upload the eBook onto your phone or other device you will need to open the file on your computer 
									and then download it onto your device<br />
									<br />
									<b>Reading your eBook with your PC or Mac:</b><br />
									If you want to download the eBook as an eReader onto your personal computer you will need to download the following 
									software onto your computer.<br />
									Amazon makes a program called 'Kindle Previewer' that will open and read the .mobi version.<br />
									To obtain this software <a href="http://www.amazon.com/gp/feature.html?ie=UTF8&docId=1000426311&tag=googhydr-20&hvadid=7893047648&ref=pd_sl_3ies3d4yuc_b">click here</a> and then follow the directions. <br />
									Adobe makes a program called 'Digital Editions' that will open and read the .epub version.  
									To obtain this software <a href="http://www.adobe.com/products/digitaleditions/">click here</a> and then follow the directions.  
									You will be required to set up an adobe account.<br />
									Adobe also makes the software that opens .pdf files.  It is free and can be downloaded by <a href="http://get.adobe.com/reader/">clicking here</a>.  
									This can be done without creating an account but again pdf's are not searchable.<br /><br />
									
									If you have read this documentation and still have questions please feel free to give us a call at 208-747-3021.  
									We would be happy to help you.
									<br /<br /><br /><br /><br />
									Sincerely,<br />
									The ButterflyExpress Team
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
					<img src="<?php echo base_url($assets); ?>/email_images/shadow.png" border="0" width="630" />

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
						Copyright &copy; Wholesaler, LLC. All Rights Reserved.
					</p>
					
				</td>
			</tr>
			<!-- END FOOTER /////////////////////////////////////////////////////////////////-->	
		</table>

	</body>
</html>
