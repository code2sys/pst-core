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

if (count($showcasetrims) > 0) {
    $showcasetrim = $showcasetrims[0];
    $description = $showcasetrim->get("description");
    $show_info = trim(strip_tags($description)) != "";
    $title = $showcasetrim->get("title");
    $images = $PSTAPI->showcasephoto()->fetch(array(
        "showcasetrim_id" => $showcasetrim->id(),
        "deleted" => 0
    ), true);

    usort($images, function($a, $b) {
        $a_o = intVal($a["ordinal"]);
        $b_o = intVal($b["ordinal"]);
        if ($a_o < $b_o) {
            return -1;
        } else if ($a_o > $b_o) {
            return 1;
        }
        return 0;
    });


    // get the spec in there.
    $show_spec = false;

}

?>
<div class="content_wrap inventory-content-wrap">



    <!-- MAIN CONTENT -->
    <div class="main_content fl-wdh full_info_content" style="float: none !important; width: 100% !important;">

        <?php echo @$widgetBlock; ?>


        <div class="sw prod-ls">
            <div class="container_b">
                <div class="col-md-12 col-xs-12 pdig">
                    <nav class="breadcrumb">

                    </nav>
                </div>
                <div class="col-md-12 col-xs-12 pdig sect-sid">
                    <?php if (count($images) > 0): ?>
                        <div class="clearfix" style="width:100%;">
                            <ul id="image-gallery" class="gallery list-unstyled cS-hidden">
                                <?php
                                foreach( $images as $image ) {

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
                    <?php endif; ?>

                </div>
                <div class="col-md-12 col-xs-12 pdig padg-one" style="padding-top:50px;">
                    <div class="col-md-12 col-xs-12 col-sm-8 pdig vide-wdt">
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
                                <?php echo $description; ?>
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



        <?php echo @$featureBand; ?>

        <?php echo @$dealsBand; ?>

        <?php echo @$topSellersBand;  ?>

        <?php echo @$recentlyViewedBand; ?>

        <?php if(@$notice && $showNotice): ?>
            <div class="content_section">
                <h3><?php echo @$notice; ?></h3>
            </div>
        <?php endif; ?>

        <?php if (isset($pageRec) && is_array($pageRec) && array_key_exists("page_custom_js", $pageRec) && $pageRec["page_custom_js"] != ""): ?>
            <script type="application/javascript">
                <?php echo $pageRec["page_custom_js"]; ?>
            </script>
        <?php endif; ?>

    </div>
    <!-- END MAIN CONTENT -->


    <?php echo @$sidebar; ?>

    <div class="clear"></div>

</div>

<script type="application/javascript">
    $(window).on("load", function() {
        $(".showroom-tile-image").css("height", Math.floor(0.5 * $(".showroom-tile-image").width()) + "px");
    })
</script>
