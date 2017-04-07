
	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- MAIN CONTENT -->
<!-- 		<div class="main_content"> -->
			<?php echo @$widgetBlock; ?>
<!-- 		</div> -->
<?php if(@$SMSettings['sm_fblink']): ?>
<div class="fb-like-box" data-href="<?php echo @$SMSettings['sm_fblink']; ?>" data-width="600" data-colorscheme="light" data-show-faces="false" data-header="false" data-stream="true" data-show-border="true" style="padding-left:25px; float:left;"></div>
<?php endif; ?>
		<?php if(@$SMSettings['sm_gpid']): ?>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
			<div style="margin:25px 0 0 35px; float:left;"><div class="g-page" data-href="https://plus.google.com/<?php echo $SMSettings['sm_gpid']; ?>" ></div></div>
		<?php endif; ?>
<br />
	<div class="clear"></div>
	<div class="divider"></div>
	<br />
	
	<?php echo @$reviewsBox; ?>


	</div>
<?php if(@$SMSettings['sm_fblink']): ?>	
	<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=558355384270745&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<?php endif; ?>