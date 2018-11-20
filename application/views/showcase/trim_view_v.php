<?php

// We have to attempt to obtain the images, at least, so that we can display
// Image + description, then we've got to get the specs in there. We don't have
// specs into the
global $PSTAPI;
initializePSTAPI();
$showcasetrims = $PSTAPI->showcasetrim()->fetch(array(
    "page_id" => $pageRec["id"],
    "deleted" => 0
));

$title = $pageRec["title"];
$full_url = "";

$show_spec = false;
$show_info = false;

if (count($showcasetrims) > 0) {
    $showcasetrim = $showcasetrims[0];
    $title = $showcasetrim->get("title");
    $full_url = $showcasetrim->get("full_url");
    $description = $showcasetrim->get("description");
    $show_info = trim(strip_tags($description)) != "";
    $title = $showcasetrim->get("title");
    $images = $PSTAPI->showcasephoto()->fetch(array(
        "showcasetrim_id" => $showcasetrim->id(),
        "deleted" => 0
    ), true);

    // get the spec in there.


}

?>
<style>
    ul.lSPager.lSGallery {
        right: 0px;
    }
</style>
<div class="sw prod-ls">
    <div class="container_b">
        <div class="col-md-12 col-xs-12 pdig">
            <?php
            $CI =& get_instance();
            echo $CI->load->view("showcase/breadcrumbs", array(
                "title" => $title,
                "full_url" => $full_url
            ), true);

            ?>
            <div class="col-md-8 col-xs-12 col-sm-7 pdig sect-sid">
                <div class="clearfix" style="width:100%;">
                    <ul id="image-gallery" class="gallery list-unstyled cS-hidden">
                        <?php foreach( $images as $image ) {

                            $image_url = $image["url"];
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
                <h3><?php $title;?></h3>


            </div>
        </div>
        <div class="col-md-12 col-xs-12 pdig padg-one" style="padding-top:50px;">

            <div class="col-md-12 col-xs-12 col-sm-12 pdig vide-wdt">
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
                        <?php echo $motorcycle['description'];?>
                    </div>
                <?php endif; ?>
                <?php if ($show_spec): ?>
                    <div class="info" id="product-details-spec-body">



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

