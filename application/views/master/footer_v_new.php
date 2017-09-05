<?php
$new_assets_url = jsite_url(  "/qatesting/newassets/");
$new_assets_url1 = jsite_url( "/qatesting/benz_assets/");
	exit;?>
	<div class="sw footer clear">
		<div class="container_b">
			<div class="one-fifth">
				<h3 class="aut-title">About<span><?php echo $store_name['company'];?></span></h3>
				<ul class="clear">
					<li><a href="<?php echo site_url('pages/index/aboutus');?>">About Us</a></li>
				</ul>				
			</div>
			<?php
			jprint_interactive_footer($pages); ?>


			<div class="one-fifth map">
				<h3>Contact Us</h3>
				<ul class="clear">
                    <li style="line-height: 16px">Address: <?php echo $store_name['street_address'].', ' . ($store_name['address_2'] != "" ? $store_name['address_2'] . ", " : "") . $store_name['city'].', '.$store_name['state'] . ' ' . $store_name['zip'];?></li>
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
				<form action="" method="post" id="form_example" class="form_standard">
					<input type="text" id="newsletter" name="newsletter">
					<input type="button" value="SUBMIT" onclick="submitNewsletter();">
				</form>
			</div>
			<div class="img-footer">
				<a href="http://powersporttechnologies.com"><img src="<?php echo $new_assets_url; ?>images/powered-logo.png"  class="powerlogo-a"/>	
			</div>
			<hr class="ftr-line">
		</div>				
	</div>
</div>

<?php
$CI =& get_instance();
echo $CI->load->view("braintree", array(
	"store_name" => $store_name
), true);
?>
		
	<script>		
		$(document).ready(function() {
 
		$("#owl-demo").owlCarousel({
		 
			  navigation : true,
			  slideSpeed : 300,
			  paginationSpeed : 400,
			  singleItem:true,
			  autoPlay: true,
			  autoPlayTimeout:1000
		 
		 
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
			navigation : true,
		    autoPlay: true,
		    autoPlayTimeout:3000
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
   
  
</script>
<?php foreach($category as $id => $ref){ $catd = $ref['label'];}  ?>
<script>
	var ctd = '<?php echo $catd; ?>';
        
	if(ctd=='UTV PARTS'){
		$("#stp").removeClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').addClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ctd=='DIRT BIKE PARTS'){
		$("#stp").removeClass('actv');
		$('#sdp').addClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ctd=='STREET BIKE PARTS'){
		$("#stp").addClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ctd=='ATV PARTS'){
		$("#stp").removeClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').addClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}
	
</script>
<script>
	var ct = '<?php echo $top_parent; ?>';
	
	if(ct=='<?php echo TOP_LEVEL_CAT_UTV_PARTS; ?>'){
		$("#stp").removeClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').addClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ct=='<?php echo TOP_LEVEL_CAT_DIRT_BIKES; ?>'){
		$("#stp").removeClass('actv');
		$('#sdp').addClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ct=='<?php echo TOP_LEVEL_CAT_STREET_BIKES; ?>'){
		$("#stp").addClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').removeClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}else if(ct=='<?php echo TOP_LEVEL_CAT_ATV_PARTS; ?>'){
		$("#stp").removeClass('actv');
		$('#sdp').removeClass('actv');
		$('#sap').addClass('actv');
		$('#sup').removeClass('actv');
		$('#sbb').removeClass('actv');
	}
	
</script>
</body>
</html>


