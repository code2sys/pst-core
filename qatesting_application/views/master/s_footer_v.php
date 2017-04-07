
<!-- FOOTER ===============================================================================-->

<div class="footer_wrap">
	<div class="footer_content">
		
		<div class="footer_sect">
			<h2>Get In Touch!</h2>
			<h2>
				<?php if(@$SMSettings['sm_fblink']): ?>
				<a href="<?php echo @$SMSettings['sm_fblink']; ?>" target="_blank" style="color:#369;"><i class="fa fa-facebook-square"></i></a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_twlink']): ?>
					<a href="<?php echo $SMSettings['sm_twlink']; ?>" style="color:#4099FF" target="_blank"><i class="fa fa-twitter-square"></i></a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_ytlink']): ?>
					<a href="<?php echo $SMSettings['sm_ytlink']; ?>" target="_blank" style="color:#F00;"><i class="fa fa-youtube"></i></a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_gplink']): ?>
					<a href="<?php echo $SMSettings['sm_gplink']; ?>" target="_blank" style="color:#C60;"><i class="fa fa-google-plus-square"></i></a>
				<?php endif; ?>
				<?php if(@$SMSettings['sm_blglink']): ?>
					<div style="float:left; margin:-8px 2px 0 0 ;"><a href="<?php echo $SMSettings['sm_blglink']; ?>" target="_blank"><img src="<?php echo $s_assets; ?>/images/blogger.jpg" width="29px;" style="padding-top: 15px;"></a></div>
				<?php endif; ?>
				<a href="mailto:?subject=Check out this Site&amp;body=Check out this site <?php echo base_url(); ?>." title="Share by Email" style="color:#FFF;"><i class="fa fa-envelope-square"></i></a>
			</h2>
			<p><b>Address:</b> <?php echo $accountAddress['street_address']; ?> - <?php echo $accountAddress['city']; ?>, <?php echo $accountAddress['state']; ?></p>
			<p><b>Phone:</b><a href="<?php echo base_url('pages/index/contactus'); ?>"> <?php echo $accountAddress['phone']; ?></a></p>
			<p><b>Email:</b> <?php echo $accountAddress['email']; ?></p>
			<p style="font-size:11px;">
				Copyright &copy; <?php echo date('Y'); ?>, <?php echo STYLED_HOSTNAME; ?> All Rights Reserved<br>
			</p>
		</div>
		<div class="footer_sect">
			<div class="footer_logo">
				<a href="#"><img src="<?php echo $s_assets; ?>/images/footer_logo2.png" border="0"></a>
				<br><br>
			</div>
		</div>
		
		<div class="footer_sect align-right" style="margin-right:0px;">
			<h2>Join Our Newsletter</h2>
			<p style="margin-top:0;float:right" class="hide" id="newsletter_success">You have been added to the list!</p>
			<form action="" method="post" id="form_example" class="form_standard">
				<input id="newsletter" name="newsletter" placeholder="Enter Email Address" class="text medium" style="background:#333;border:1px #000 solid;color:#999;" />
				<a href="javascript:void(0);" onclick="submitNewsletter();" class="input_button" style="margin-top:0;float:none">Join!</a>
			</form>
			
			<?php if(@$pages): foreach($pages as $page): ?>
			<a href="<?php echo base_url('pages/index/'.$page['tag']); ?>"><?php echo $page['label']; ?> <i class="fa <?php echo @$page['icon']; ?>"></i> &nbsp; </a><br>
			<?php endforeach; endif; ?>
			
		</div>
		<br><br>
		<div class="clear"></div>
		
		
	</div>
	<div class="footer_bar">
		<div class="footer_tag_content">
			<div class="footer_tag" style="padding: 4% 2% 4% 2%;">
				<span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=qc3WD7lClpbpLfFD7HDpLCx8bXBkOWSZP9ImCkgNS7VqSnVbHcLTJJrA6sG"></script></span>
			</div>

			<div class="footer_tag" style="padding:4% 2% 4% 2%;">
				<!-- BEGIN: Bizrate Medal (112x37 pixels) -->
				<script type="text/javascript">var bizrate={small:"true"};</script>
				<script src="//medals.bizrate.com/medals/js/224388_medal.js" type="text/javascript"></script>
				<div style="position:relative;z-index:1;width:112px;height:37px;">
				<a style="bottom:0;right:0;display:block;position:absolute;width:50px;height:16px;z-index:2;text-decoration:none;background:#fff;filter:alpha(opacity=0);opacity: 0;" href="//www.bizrate.com/?rf=sur" title="Bizrate" target="_blank"></a>
				<a class="br-button" href="//www.bizrate.com/ratings_guide/merchant_detail__mid--224388.html?rf=sur" title="See <?php echo STYLED_HOSTNAME; ?> Reviews at Bizrate.com" target="_blank">
				<img src="//medals.bizrate.com/medals/dynamic/small/224388_medal.gif" width="112" height="37" border="0" alt="See <?php echo STYLED_HOSTNAME; ?>  Reviews at Bizrate.com" />
				</a>
				</div>
				<!-- END: Bizrate Medal (112x37 pixels) -->
			</div>
			
			<div style="float:right; padding:3% 2% 4% 2%">
				<a href="<?php echo base_url('pages/index/paymentoptions'); ?>"><img src="<?php echo $s_assets; ?>/images/credit_card_image.png" width="200px"></a>
			</div>
		</div>
	</div>
</div>

<!-- END FOOTER ===========================================================================-->

<script>
	function submitNewsletter()
	{		
		 $.post(base_url + 'ajax/updateNewsletterList/',
			{ 
			 'email' : $('#newsletter').val(),
			 'ajax' : true
			},
			function()
			{
				$('#newsletter_success').show();
			});
	}
</script>

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
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1052220103/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>