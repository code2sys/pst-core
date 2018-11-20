<?php

$display_makes = false;
$display_machine_types = false;
$display_models = false;
$display_trims = false;
$showcasemake_id = null;
$showcasemodel_id = null;
$showcasemachinetype_id = null;

global $PSTAPI;
initializePSTAPI();

$title = $pageRec["title"];
$full_url = "";

switch ($pageRec["page_class"]) {
    case "Showroom Landing Page":
        $display_makes = true;
        $showcasemakes = $PSTAPI->showcasemake()->fetch(array(
            "deleted" => 0
        ));
        break;

    case "Showroom Make":
        // are the machine types?
        $showcasemakes = $PSTAPI->showcasemake()->fetch(array(
            "page_id" => $pageRec["id"],
            "deleted" => 0
        ));

        if (count($showcasemakes) > 0) {
            $showcasemake = $showcasemakes[0];
            $full_url = $showcasemake->get("full_url");
            $showcasemake_id = $showcasemake->id();

            // machinetypes;
            $showcasemachinetypes = $PSTAPI->showcasemachinetype()->fetch(array(
                "showcasemake_id" => $showcasemake->id(),
                "deleted" => 0
            ));

            if (count($showcasemachinetypes) == 1) {
                $showcasemodels = $PSTAPI->showcasemodel()->fetch(array(
                    "showcasemachinetype_id" => $showcasemachinetypes[0]->id(),
                    "deleted" => 0
                ));
                $display_models = true;
            } else {
                // display it.
                $display_machine_types = true;
            }
        }

        break;

    case "Showroom Machine Type":
        // OK, we have a MAKE in hand.
        $showcasemachinetypes = $PSTAPI->showcasemachinetype()->fetch(array(
            "page_id" => $pageRec["id"],
            "deleted" => 0
        ));

        if (count($showcasemachinetypes) > 0) {
            $showcasemachinetype = $showcasemachinetypes[0];
            $full_url = $showcasemachinetype->get("full_url");
            $showcasemachinetype_id = $showcasemachinetype->id();

            $showcasemodels = $PSTAPI->showcasemodel()->fetch(array(
                "showcasemachinetype_id" => $showcasemachinetype_id,
                "deleted" => 0
            ));
            $display_models = true;
        }

        break;

    case "Showroom Model":
        $showcasemodels = $PSTAPI->showcasemodel()->fetch(array(
            "page_id" => $pageRec["id"],
            "deleted" => 0
        ));

        if (count($showcasemodels) > 0) {
            $showcasemodel = $showcasemodels[0];
            $showcasemodel_id = $showcasemodel->id();
            $full_url = $showcasemodel->get("full_url");

            $showcasetrims = $PSTAPI->showcasetrim()->fetch(array(
                "showcasemodel_id" => $showcasemodel_id,
                "deleted" => 0
            ));
            $display_trims = true;
        }
        break;
}

?>

<!-- CONTENT WRAP =========================================================================-->
<div class="content_wrap inventory-content-wrap">



    <!-- MAIN CONTENT -->
    <div class="main_content fl-wdh full_info_content" style="float: none !important; width: 100% !important;">

        <?php
        $CI =& get_instance();
        echo $CI->load->view("showcase/breadcrumbs", array(
            "title" => $title,
            "full_url" => $full_url
        ), true);

        ?>

        <?php echo @$widgetBlock; ?>

        <div class="inventory-selector-block content_section">
            <?php

            $grid_widgets = array();
            function prepare_widget_group($title, $source_array, &$grid_widgets, $use_display_title = false) {
                usort($source_array, function($a, $b) {
                    $a_title = $a->get("short_title");
                    $b_title = $b->get("short_title");
                    return strnatcasecmp($a_title, $b_title);
                });

                if (count($source_array) > 0) {
                    $grid_widgets[] = array(
                        "title" => $title,
                        "tiles" => array_map(function ($x) {
                            return array(
                                "title" => $use_display_title ? $x->get("display_title") : $x->get("short_title"),
                                "url_fragment" => $x->get("full_url"),
                                "thumbnail" => $x->get("thumbnail_photo")
                            );
                        }, $source_array)
                    );
                }

            };


            if ($display_makes) {
                prepare_widget_group("", $showcasemakes, $grid_widgets, true);
            } else if ($display_machine_types) {
                prepare_widget_group("", $showcasemachinetypes, $grid_widgets);
             } else if ($display_models) {

                // sort them by year...
                $year_buckets = array();

                foreach ($showcasemodels as $m) {
                    $year = intVal($m->get("year"));
                    if (!array_key_exists($year, $year_buckets)) {
                        $year_buckets[$year] = array();
                    }
                    $year_buckets[$year][] = $m;
                }

                $year_bucket_keys = array_keys($year_buckets);
                sort($year_bucket_keys);
                $year_bucket_keys = array_reverse($year_bucket_keys);

                foreach ($year_bucket_keys as $year) {
                    $buckets = $year_buckets[$year];

                    prepare_widget_group($year. " Models", $buckets, $grid_widgets);
                }

            } else if ($display_trims) {
                prepare_widget_group("Trims", $showcasetrims, $grid_widgets);
            }


            ?>

            <?php foreach ($grid_widgets as $grid_widget): ?>
            <div class="showroom-grid-widget">
                <?php if ($grid_widget["title"] != ""): ?>
                <div class="showroom-grid-widget-title"><?php echo $grid_widget["title"]; ?></div>
                <?php endif; ?>

                <div class="showroom-grid-widget-rows">
                    <div class="row">
                    <?php for ($i = 0; $i < count($grid_widget["tiles"]); $i++) {
                        $tile = $grid_widget["tiles"][$i];
                        if ($i > 0 && $i % 4 == 0) {
                            ?></div><div class="row"><?php
                        }
                        ?>
                        <div class="span3 showroom-tile">
                            <a href="<?php echo site_url('Factory_Showroom/' . $tile["url_fragment"]); ?>" class="showroom-tile-link"><div class="showroom-tile-image" style="background-image: url('<?php echo (is_null($tile['thumbnail']) || $tile['thumbnail'] == '') ? site_url('/assets/showcase_no_picture_available.png') : $tile['thumbnail']; ?>'); "></div><span class="showroom-tile-title"><?php echo $tile["title"]; ?></span><div class="clear: both"></div></a>
                        </div>
                        <?php
                    } ?>

                    </div>

                </div>

            </div>

            <?php endforeach; ?>

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
