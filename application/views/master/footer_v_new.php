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
                <?php
                $CI =& get_instance();
                echo getSocialLinkButtons();
                ?>
				<h3 class="nwsltr">newsletter</h3>
                <form action="" class="form_standard" onSubmit="submitNewsletter();">
                    <input type="text" id="newsletter" name="newsletter">
                    <input type="button" value="SUBMIT" >
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

echo $CI->load->view("owlCarousel", array(), true);
?>


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

    <?php
    if (!isset($CI)) {
        $CI =& get_instance();
    }
    echo $CI->load->view("master/widgets/selector2_js");
    ?>

	
</script>
<script>
	var ct = '<?php echo $top_parent; ?>';

    <?php
    if (!isset($CI)) {
        $CI =& get_instance();
    }
    echo $CI->load->view("master/widgets/selector3_js");
    ?>

	
</script>
</body>
</html>


