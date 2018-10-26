<?php
$mainVideo = $motorcycle['videos'][0];
unset($motorcycle['videos'][0]);
//include('header.php');
	$new_assets_url = jsite_url( "/qatesting/newassets/" );
	$media_url = jsite_url(  "/media/" );
	// echo "<pre>";
	// print_r($media_url.$motorcycle['images'][0]['image_name']);
	// echo "</pre>";

$CI =& get_instance();
$stock_status_mode = $CI->_getStockStatusMode();

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
				<a href="<?php echo base_url('Major_Unit_List'); ?>?fltr=<?php echo $motorcycle['condition'] == 1 ? 'New_Inventory' : 'pre-owned'; ?>">Motorcycle List</a>
				<span><i class="fa fa-angle-right" aria-hidden="true"></i></span>
				<a href="<?php echo base_url(strtolower($motorcycle['type']) . '/' . $motorcycle['url_title'] . '/' . $motorcycle['sku']); ?>"><?php echo $motorcycle['title'];?></a>
			</nav>
            <?php
            $CI =& get_instance();
            $CI->load->helper("mustache_helper");
            $menu_section_template = mustache_tmpl_open("benz_views/product-details/menu-section.html");
            mustache_tmpl_set($menu_section_template, "motorcycle_id", $motorcycle['id']);
            mustache_tmpl_set($menu_section_template, "base_url", jsite_url("/"));
            mustache_tmpl_set($menu_section_template, "WORDING_SCHEDULE_TEST_DRIVE", defined('WORDING_SCHEDULE_TEST_DRIVE') ? WORDING_SCHEDULE_TEST_DRIVE : "SCHEDULE TEST DRIVE");
            mustache_tmpl_set($menu_section_template, "GET_FINANCING_WORDING", defined('GET_FINANCING_WORDING') ? GET_FINANCING_WORDING : 'GET FINANCING');
            print mustache_tmpl_parse($menu_section_template);


            ?>

			<div class="col-md-8 col-xs-12 col-sm-7 pdig sect-sid">
				<div class="clearfix" style="width:100%;">
					<ul id="image-gallery" class="gallery list-unstyled cS-hidden">
                        <?php
                        // JLB 12-19-17
                        // If we have > 1 image, and we have a CRS thumbnail image in the mix, we don't show that.
                        if (count($motorcycle['images']) > 1) {
                            $clean_images = array();

                            foreach ($motorcycle['images'] as $img) {
                                if (!($img['crs_thumbnail'] > 0)) {
                                    $clean_images[] = $img;
                                }
                            }

                            $motorcycle['images'] = $clean_images;
                        }


                        ?>

						<?php foreach( $motorcycle['images'] as $image ) {

						    $image_url = $image["image_name"];
						    if ($image["external"] == 0) {
						        $image_url = $media_url. $image_url;
                            }

						    ?>
							<li data-thumb="<?php echo $image_url; ?>">
								<a class="fancybox" href="<?php echo $image_url; ?>" data-fancybox-group="gallery">
									<img src="<?php echo $image_url; ?>" />
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<div class="col-md-4 col-sm-5 pull-right bx-rit pdig sect-wdt">
				<h3><?php echo $motorcycle['title'];?></h3>
				<?php
					$CI =& get_instance();
					echo $CI->load->view("benz_views/pricing_widget", array(
						"motorcycle" => $motorcycle
					), true);

                    $info_block_template = mustache_tmpl_open("benz_views/product-details/info_block.html");

                    // For some of these, we just set them...
                    foreach (array(
                            "year", "make", "model", "type", "category", "engine_type", "transmission", "vin_number", "color", "mileage", "engine_hours"
                             ) as $key) {

                        if (array_key_exists($key, $motorcycle) && !is_null($motorcycle[$key]) && $motorcycle[$key] != "") {
                            if (!in_array($key, array("color", "mileage", "engine_hours")) || ($key == "color" && $motorcycle[$key] != 'N/A') || ($key == "mileage" && $motorcycle["mileage"] > 0)|| ($key == "engine_hours" && $motorcycle["engine_hours"] > 0)) {
                                mustache_tmpl_set($info_block_template, $key, $motorcycle[$key]);
                            }
                        }
                    }

                    mustache_tmpl_set($info_block_template, "condition" . $motorcycle['condition'], true);
                    mustache_tmpl_set($info_block_template, "stock_status", $motorcycle["stock_status"]);
                    // but we also have to do the in stock
                    mustache_tmpl_set($info_block_template, "stock_status_in_stock", $motorcycle["stock_status"] == "In Stock");
                    mustache_tmpl_set($info_block_template, "stock_status_big_flag", (($motorcycle['stock_status'] == 'In Stock' && $stock_status_mode >= 2 ) || ($motorcycle['stock_status'] != 'In Stock' && ($stock_status_mode == 1  || $stock_status_mode == 3))));

                    mustache_tmpl_set($info_block_template, "clean_complex_SKU", clean_complex_sku($motorcycle));

                if ($motorcycle["location_description"] != "") {
                    mustache_tmpl_set($info_block_template, "location_description", $motorcycle["location_description"]);

                } else {
                    mustache_tmpl_set($info_block_template, "location_description", $store_name['city'].', '.$store_name['state']);
                }

                    print mustache_tmpl_parse($info_block_template);
				?>

			</div>
		</div>
		<div class="col-md-12 col-xs-12 pdig padg-one" style="padding-top:50px;">
            <?php
            $CI =& get_instance();
            echo $CI->load->view("benz_views/recently_viewed", array(
                "master_class" => "col-md-3 col-xs-12 fltrbar pull-right pdig oder col-sm-4",
                "subclass" => "col-xs-12",
                "innersubclass" => "",
                "recentlyMotorcycle" => $recentlyMotorcycle,
                "no_fify" => true
            ), true);

            $show_info = !empty($mainVideo) || (trim($motorcycle['description']) != "");
            $show_spec = (count($motorcycle['specs']) > 0);
            ?>

			<div class="col-md-9 col-xs-12 col-sm-8 pdig vide-wdt">
                <?php if ($show_info): ?>
				<span href="#" class="btn info-btn" id="product-details-info">
					info
				</span>
                <?php endif; ?>
                <?php if ($show_spec): ?>
				<span href="#" class="btn info-btn" id="product-details-spec">
					specifications
				</span>
                <?php endif; ?>
				<hr class="hr-lne">
                <?php if ($show_info): ?>
                <div class="info" id="product-details-info-body">
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
                </div>
                <?php endif; ?>
                <?php if ($show_spec): ?>
                <div class="info" id="product-details-spec-body">

                    <?php if (count($motorcycle['specs']) > 0): ?>

                        <div>
                        <style scoped>

                            .row1 {
                                background-color: white;
                            }

                            td {
                                padding: 3px;
                                width: 50%;
                            }

                            td.key {
                                font-weight: bold;
                            }

                            td.value {
                                text-align: right;
                            }


                        </style>
                        <h3>Specifications</h3>

                            <?php
                            $feature_name = "";
                            foreach ($motorcycle["specs"] as $s) {
                                if ($feature_name != $s["spec_group"]) {
                                    if ($feature_name != ""):
                                    ?>
                </table>
                                        <?php
                                        endif;
                                    $feature_name = $s["spec_group"];
                                    ?>
                    <p><strong><?php echo $feature_name; ?></strong></p>
                    <table border="0" width="100%" class="stripedtable">
                        <?php
                                    $k = 0;


                                }
                                ?>
                                <tr class="row<?php echo $k; ?>">
                                    <td class="key" valign="top"><?php echo $s["feature_name"] . ($s["attribute_name"] != "" ? " - " . $s["attribute_name"] : ""); ?></td>
                                    <td class="value" valign="top"><?php echo $s["final_value"]; ?></td>
                                </tr>
                                <?php
                                $k = 1 - $k;

                            }

                            ?>


                        <?php if ($feature_name != ""): ?></table><?php endif; ?>


                        <p><em>Certain features may require an additional add-on package that may not be included in the retail or sale price. Please contact the dealership for full details.</em></p>
                        </div>

                    <?php endif; ?>

					<!--<h3>Integer tellus dui venenatis non:</h3>
					<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here,  content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover </p>
					<h3>Vivamus porta tellus</h3>
					<ul>
						<li>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words,</li>
						<li>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.</li>
						<li>discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..",</li>
					</ul>-->
				</div>
                <?php endif; ?>

            </div>
		</div>
	</div>
</div>

<?php if ($show_info && $show_spec): ?>
<script type="application/javascript">
    $(document).ready(function() {
        // Hide the spec stuff
        $("#product-details-spec").css("opacity", 0.5);
        $("#product-details-spec-body").hide();

        $("#product-details-spec").on("click", function(e) {
            $("#product-details-spec").css("opacity", 1.0);
            $("#product-details-info").css("opacity", 0.5);
            e.stopPropagation();
            e.preventDefault();
            $("#product-details-info-body").hide();
            $("#product-details-spec-body").show();
        });

        $("#product-details-info").on("click", function(e) {
            $("#product-details-info").css("opacity", 1.0);
            $("#product-details-spec").css("opacity", 0.5);
            e.stopPropagation();
            e.preventDefault();
            $("#product-details-spec-body").hide();
            $("#product-details-info-body").show();
        });

    });
</script>
<?php endif; ?>

<?php
if ($image_url == "" || is_null($image_url) || $image_url == $media_url) {
	$image_url = "/assets/image_unavailable.png";
}
?>

<script language="javascript">
	function fbshareCurrentPage()
	{window.open("http://www.facebook.com/share.php?u="+escape(window.location.href)+"&picture="+"<?php
            if ($motorcycle['images'][0]["external"] > 0) {
                echo $motorcycle['images'][0]['image_name'];
            } else {
                echo $media_url.$motorcycle['images'][0]['image_name'];
            }
        ?>", '',
	'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');
	return false; }
</script>
<script>
	 $(document).ready(function() {
//		$("#content-slider").lightSlider({
//			loop:true,
//			keyPress:true
//		});

		var thumbItem = 9;
		var screen_width = $(document).width();

		if (screen_width < 480) {
			// mobile screen
			thumbItem = 4;
		} else if (screen_width < 640) {
			// phablet
			thumbItem = 5;
		 } else if (screen_width < 768) {
			 // tablet
			 thumbItem = 6;
		 } else if (screen_width < 1024) {
			 // I don't know why
			 thumbItem = 8;
		 }

		$('#image-gallery').lightSlider({
			gallery:true,
			item:1,
			galleryMargin: 10,
			thumbItem:thumbItem,
			slideMargin: 0,
			speed:500,
			auto:true,
			loop:true,
			onSliderLoad: function() {
				$('#image-gallery').removeClass('cS-hidden');
			},
			pause:<?php echo defined('MAJOR_UNIT_PAUSE_TIME') ? MAJOR_UNIT_PAUSE_TIME : 2000; ?>
		});
	});

</script>

<?php
$CI =& get_instance();
echo $CI->load->view("showvideo_function", array(), false);
?>


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

<style>
    .lSSlideOuter .lSPager.lSGallery img {
        display: block;
        height: auto;
        width: 100%;
        max-height: 60px;
    }

    .lSSlideOuter .lSPager.lSGallery a {
        display: block;
        height: 60px;
    }
    #image-gallery li {
        background-color: white;
    }

</style>

<?php
$this->view('modals/major_unit_detail_modal.php', array(
	'motorcycle'       => $motorcycle,
	'motorcycle_image' => $image_url,
));
$this->view('modals/trade_in_value_modal.php', array('motorcycle' => $motorcycle));
$this->view('modals/customer_exit_modal.php');
?>

<script type="application/javascript">
$(document).ready(function () {
	// Show Major Unit Detail modal
	setTimeout(function () {
		var siteModalsState = JSON.parse(localStorage.getItem('siteModalsState')) || {};

		// If user has already made a form submission on another modal, don't show this modal
		if (siteModalsState['hasContactedSales']) return;

		$('.modal').modal('hide');

		// Fixes Bootstrap bug
		setTimeout(function () {
			$('#major-unit-detail-modal_<?php echo $motorcycle['id']; ?>').modal('show');
		}, 500);
	}, 5000);
});
</script>
