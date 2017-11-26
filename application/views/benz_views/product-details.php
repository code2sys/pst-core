<?php
$mainVideo = $motorcycle['videos'][0];
unset($motorcycle['videos'][0]);
//include('header.php');
	$new_assets_url = jsite_url( "/qatesting/newassets/" );
	$media_url = jsite_url(  "/media/" );
	// echo "<pre>";
	// print_r($media_url.$motorcycle['images'][0]['image_name']);
	// echo "</pre>";
?>
<style>
	ul.lSPager.lSGallery {
		right: 0px;
	}
</style>
<div class="sw prod-ls">
	<div class="container_b">
		<div class="col-md-12 col-xs-12 pdig">
			<nav class="breadcrumb">
				<a href="<?php echo base_url(); ?>">Home</a>
				<span><i class="fa fa-angle-right" aria-hidden="true"></i></span>
				<a href="<?php echo base_url('Motorcycle_List'); ?>">Motorcycle List</a>
				<span><i class="fa fa-angle-right" aria-hidden="true"></i></span>
				<a href="<?php echo base_url('welcome/benzDetails/'.$motorcycle['id']); ?>"><?php echo $motorcycle['title'];?></a>
			</nav>
			<div class="menu-section">				
				<ul class="nav navbar-nav menu-dti">
					<li><a href="#" data-toggle="modal" data-target="#myModal">GET A QUOTE</a></li>
					<li><a href="#" data-toggle="modal" data-target="#myModal">TRADE VALUE</a></li>
					<li style="margin-right:10px;" data-toggle="modal" data-target="#myModal"><a href="#"><?php if (defined('WORDING_SCHEDULE_TEST_DRIVE')) { echo WORDING_SCHEDULE_TEST_DRIVE; } else { ?>SCHEDULE TEST DRIVE<?php } ?></a></li>
					<li><a href="/pages/index/financerequest"><?php
                            if (!defined('GET_FINANCING_WORDING')) {
                                define('GET_FINANCING_WORDING', 'GET FINANCING');
                            }
                            echo GET_FINANCING_WORDING;
                            ?></a></li>
					<!--<li><a href="#" data-toggle="modal" data-target="#myModal">GET FINANCING</a></li>-->
					<li><a href="https://www.progressive.com/motorcycle/" target="_blank" >INSURANCE QUOTE</a></li>
					<!--<li><a href="#">HISTORY REPORT</a></li>
					<li><a href="#">SEND TO A FRIEND</a></li>-->
					<li><a href="<?php echo site_url('pages/index/contactus') ?>" class="last">CONTACT US</a></li>
				</ul>
			</div>

			<div class="col-md-8 col-xs-12 col-sm-7 pdig sect-sid">
				<div class="clearfix" style="width:100%;">
					<ul id="image-gallery" class="gallery list-unstyled cS-hidden">
						<?php foreach( $motorcycle['images'] as $image ) { ?>
							<li data-thumb="<?php echo $media_url.$image['image_name']; ?>">
								<a class="fancybox" href="<?php echo $media_url.$image['image_name']; ?>" data-fancybox-group="gallery">
									<img src="<?php echo $media_url.$image['image_name']; ?>" />
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<div class="col-md-4 col-sm-5 pull-right bx-rit pdig sect-wdt">
				<h3><?php echo $motorcycle['title'];?></h3>
				<?php if( $motorcycle['call_on_price'] == '1' ) { ?>
					<p class="cfp">Call For Price</p>
                <?php } else {
                    if ($motorcycle['sale_price']>0 && $motorcycle['sale_price'] !== "0.00") { ?>
                       <p>Retail Price: &nbsp; <span class="strikethrough">$<?php echo $motorcycle['retail_price'];?></span></p>
                       <p>Sale Price: &nbsp; &nbsp;<span class="redtext">$<?php echo $motorcycle['sale_price'];?></span></p>
                    <?php } else { ?>
                        <p>Retail Price: &nbsp; $<?php echo $motorcycle['retail_price'];?></p>
				    <?php
				    }
                    if ($motorcycle["destination_charge"]) {
                        echo "<sub>* Plus Applicable destination charge</sub>";
                    }
                } ?>
				<h4>Highlights</h4>
				<hr>
				<div class="dtal-txt">
					<label>location :</label>
					<span><?php echo $store_name['city'].', '.$store_name['state'];?> </span>
				</div>				
				<div class="dtal-txt">
					<label>Condition :</label>
					<span><?php echo $motorcycle['condition'] == '1' ? 'New' : 'Pre-Owned';?></span>
				</div>				
				<div class="dtal-txt">
					<label>year :</label>
					<span><?php echo $motorcycle['year'];?></span>
				</div>				
				<div class="dtal-txt">
					<label>make :</label>
					<span><?php echo $motorcycle['make'];?></span>
				</div>				
				<div class="dtal-txt">
					<label>model :</label>
					<span><?php echo $motorcycle['model'];?></span>
				</div>				
				<div class="dtal-txt">
					<label>color :</label>
					<span><?php echo $motorcycle['color'];?></span>
				</div>
				<?php if( $motorcycle['mileage'] > 0 ) { ?>
					<div class="dtal-txt">
						<label>mileage :</label>
						<span><?php echo $motorcycle['mileage'];?> Miles</span>
					</div>
				<?php } else if($motorcycle['engine_hours'] > 0) { ?>
					<div class="dtal-txt">
						<label>Engine Hours :</label>
						<span><?php echo $motorcycle['engine_hours'];?></span>
					</div>
				<?php } ?>
				<div class="dtal-txt">
					<label>Engine Type :</label>
					<span><?php echo $motorcycle['engine_type'];?></span>
				</div>				
				<div class="dtal-txt">
					<label>transmission :</label>
					<span><?php echo $motorcycle['transmission'];?></span>
				</div>				
				<!--<div class="dtal-txt">
					<label>width :</label>
					<span>32.1 In.</span>
				</div>				
				<div class="dtal-txt">
					<label>Height</label>
					<span>44.7 In.</span>
				</div>-->
				<div class="dtal-txt">
					<label>Vin :</label>
					<span><?php echo $motorcycle['vin_number'];?></span>
				</div>
				<div class="dtal-txt">
					<label>Stock Code :</label>
					<span><?php echo $motorcycle['sku'];?></span>
				</div>
				<div class="social-button">
					<p class="scia-share">Share</p>
					<a href="javascript:fbshareCurrentPage()" target="_blank" alt="Share on Facebook" class="face">
						<span class="fa fa-facebook"></span>
					</a>
					<a href="javascript:tweetCurrentPage()" target="_blank" alt="Tweet this page" class="twitter">
						<span class="fa fa-twitter"></span>
					</a>
					<a href="mailto:?subject=Checkout this Part&amp;body=Check out this site <?php echo base_url('welcome/benzDetail/'.$motorcycle['id']);?>." title="Share by Email" class="mail">
						<span class="glyphicon glyphicon-envelope"></span>
					</a>
					<a href="javascript:googleCurrentPage()" target="_blank" class="plus">
						<span class="fa fa-google-plus"></span>
					</a>
				</div>				
			</div>			
		</div>
		<div class="col-md-12 col-xs-12 pdig padg-one" style="padding-top:50px;">
			<div class="col-md-3 col-xs-12 fltrbar pull-right pdig oder col-sm-4">
				<div class="col-md-12 col-xs-12 text-center">
					<h4 class="recnt" style="margin:20px 0 20px">RECENTLY VIEWED</h4>
				</div>
				<div class="fltrbx ">		
					<?php foreach( $recentlyMotorcycle as $recently ) {

                        $motorcycle_image = $recently['image_name'];
                        if ($recently['external'] == 0) {
                            $motorcycle_image = base_url().'media/'. $motorcycle_image;
                        }
					    ?>
						<?php $title = str_replace(' ', '_', trim($recently['title']));?>
						<div class="col-md-12 text-center">
							<a href="<?php echo base_url(strtolower($recently['type']).'/'.$title.'/'.$recently['sku']);?>">
								<img class="rvm" src="<?php echo $motorcycle_image; ?>" />
							</a>
							<a href="<?php echo base_url(strtolower($recently['type']).'/'.$title.'/'.$recently['sku']);?>"><h1 class="head-txt"><?php echo $recently['title'];?></h1></a>
							<!--<p><?php echo $recently['title'];?></p>-->
							<?php if( $recently['call_on_price'] == '1' ) { ?>
								<p class="cfp">Call For Price</p>
                                <?php
                                } else {
                                if ($recently['sale_price'] > 0 && $recently['sale_price'] !== "0.00" && $recently["sale_price"] != $recently['retail_price']) { ?>
                                    <p>Retail Price: &nbsp; <span
                                                class="strikethrough">$<?php echo number_format($recently['retail_price'], 2); ?></span>
                                    </p>
                                    <p>Sale Price: &nbsp; &nbsp;<span
                                                class="redtext">$<?php echo number_format($recently['sale_price'], 2); ?></span></p>
                                <?php } else { ?>
                                    <p>Retail Price: &nbsp; $<?php echo number_format($recently['retail_price'], 2); ?></p>
                                    <?php
                                }
                                if ($recently["destination_charge"]) {
                                    echo "<sub>* Plus Applicable destination charge</sub>";
                                }
                            }
                            ?>
						</div>
					<?php } ?>
				</div>		
			</div>
			<div class="col-md-9 col-xs-12 col-sm-8 pdig vide-wdt">
				<a href="#" class="btn info-btn">
					info
				</a>
				<hr class="hr-lne">
				<div class="info">
                        <?php if (!empty($mainVideo)) { ?>
					<div class="vds rmv">
							<?php
							$CI =& get_instance();
							echo $CI->load->view("master/embedded_videos", array(
								"class_name" => "main-vdo",
								"mainVideo" => $mainVideo['video_url'],
								"mainTitle" => $mainVideo['title'],
								"video" => $motorcycle['videos'],
								"rltdvdo_class" => "rltv-vdo",
								"autoplay" => false
							), true);
							?>

					</div>
							<div class="clear mn-hght"></div>
						<?php } ?>
					<?php echo $motorcycle['description'];?>
					<!--<h3>Integer tellus dui venenatis non:</h3>
					<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here,  content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover </p>
					<h3>Vivamus porta tellus</h3>
					<ul>
						<li>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words,</li>
						<li>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</li>
						<li>discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..",</li>
					</ul>-->
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade pop" id="myModal">
	<div class="modal-dialog area">	  
		<div class="modal-content">
			<div class="modal-header">
				<div class="clo" data-dismiss="modal">get a quote</div>			 
			</div>
			<?php echo form_open('welcome/productEnquiry', array('class' => 'form_standard')); ?>
				<div class="modal-body" id="scol">				
					 <div class="form-group">						
						<input type="text" class="form-control" placeholder="first name" name="firstName" required="">
						<div class="formRequired">*</div>
					</div>
					 <div class="form-group">						
						<input type="text" class="form-control" placeholder="last name" name="lastName" required="">
						<div class="formRequired">*</div>
					</div>
					 <div class="form-group">						
						<input type="email" class="form-control" placeholder="email" name="email" required="">
						<div class="formRequired">*</div>
					</div>
					 <div class="form-group">						
						<input type="text" class="form-control" placeholder="phone" name="phone">
					</div>
					 <div class="form-group">						
						<input type="text" class="form-control" placeholder="address" name="address">
					</div>
					 <div class="form-group">						
						<input type="text" class="form-control" placeholder="city" name="city">
					</div>
					 <div class="form-group">						
						<input type="text" class="form-control" placeholder="state" name="state">
					</div>
					<div class="form-group">						
						<input type="text" class="form-control" placeholder="zip code" name="zipcode">
					</div>				
					<h3 class="txt-title"><?php if (defined('WORDING_WANT_TO_SCHEDULE_A_TEST_DRIVE')) { echo WORDING_WANT_TO_SCHEDULE_A_TEST_DRIVE; } else { ?>Want to Schedule a Test Drive?<?php } ?></h3>
					
					<div class="form-group">						
						<input type="text" class="form-control" placeholder="<?php if (defined('WORDING_PLACEHOLDER_DATE_OF_RIDE')) { echo WORDING_PLACEHOLDER_DATE_OF_RIDE; } else { ?>date of ride<?php } ?>" name="date_of_ride">
					</div>
					<hr class="brdr">
					<h3 class="txt-title">Trade in?</h3>
					
					<div class="form-group">						
						<input type="text" class="form-control" placeholder="make" name="make">
					</div>
					<div class="form-group">						
						<input type="text" class="form-control" placeholder="model" name="model">
					</div>
					<div class="form-group">						
						<input type="text" class="form-control" placeholder="year" name="year">
					</div>
					<div class="form-group">						
						<input type="text" class="form-control" placeholder="miles" name="miles">
					</div>
					<div class="form-group">						
						<textarea type="text" class="form-control" placeholder="added accessories" name="accessories"></textarea>
					</div>
					<div class="form-group">						
						<textarea type="text" class="form-control" placeholder="comments questions" name="questions"></textarea>
					</div>
					
					<h3 class="txt-title">I am Interested in this Vehicle</h3>
					
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Poloris" value="<?php echo $motorcycle['title'];?>" readonly name="motorcycle">
					</div>
						<input type="hidden" name="product_id" value="<?php echo $motorcycle['id'];?>">
					<div class="col-md-12 text-center" style="float:none;">
						<input type="submit" class="btn bttn" value="Submit">
					</div>
				</div>								
			</form>					
		</div>	  
	</div>
</div>

<script language="javascript">
	function fbshareCurrentPage()
	{window.open("http://www.facebook.com/share.php?u="+escape(window.location.href)+"&picture="+"<?php echo $media_url.$motorcycle['images'][0]['image_name']?>", '', 
	'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');
	return false; }
</script>
<script>
	 $(document).ready(function() {
//		$("#content-slider").lightSlider({
//			loop:true,
//			keyPress:true
//		});
		$('#image-gallery').lightSlider({
			gallery:true,
			item:1,
			thumbItem:9,
			slideMargin: 0,
			speed:500,
			auto:true,
			loop:true,
			onSliderLoad: function() {
				$('#image-gallery').removeClass('cS-hidden');
			}
		});
	});
    function showVideo(vidId, vidTit) {
        var mainVideo = $('#mainVideo').data('id');
        //var mainTitle = $('.vdottl').html();
        $('.vdottl').html(vidTit);
        $("#mainVideo")[0].src = "https://www.youtube.com/embed/" + vidId + "?rel=0&autoplay=1";
        $('#mainVideo').data('id', vidId);
        //$('.shwVidHalf').show();
        $('#' + vidId).hide();
        $('#' + mainVideo).show();
        //$("#mainVideo")[0].src = "https://www.youtube.com/embed/"+vidId+"?rel=0&autoplay=1";
    }
</script>

<script>
	$(document).ready(function() {
		$(".fancybox").fancybox({
			prevEffect	: 'none',
			nextEffect	: 'none',
			helpers	: {
				title	: {
					type: 'outside'
				},
				thumbs	: {
					width	: 50,
					height	: 50
				}
			}
		});
	});
</script>

<?php //include('footer.php'); ?>
	
