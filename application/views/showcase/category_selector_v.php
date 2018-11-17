<!-- CONTENT WRAP =========================================================================-->
<div class="content_wrap inventory-content-wrap">



    <!-- MAIN CONTENT -->
    <div class="main_content fl-wdh full_info_content" style="float: none !important; width: 100% !important;">

        <?php echo @$widgetBlock; ?>

        <div class="inventory-selector-block">
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
                    $showcasemachinetypes = $PSTAPI->showcasemachinetypes()->fetch(array(
                        "page_id" => $pageRec["id"],
                        "deleted" => 0
                    ));

                    if (count($showcasemachinetypes) > 0) {
                        $showcasemachinetype = $showcasemachinetypes[0];
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

                        $showcasetrims = $PSTAPI->showcasetrim()->fetch(array(
                            "showcasemodel_id" => $showcasemodel_id,
                            "deleted" => 0
                        ));
                        $display_trims = true;
                    }
                    break;
            }


            $grid_widgets = array();

            if ($display_makes) {
                // single grid, of the makes...
                usort($showcasemakes, function($a, $b) {
                    $a_title = $a->get("title");
                    $b_title = $b->get("title");
                    return strnatcasecmp($a_title, $b_title);
                });

                $grid_widgets[] = array(
                    "title" => "",
                    "tiles" => array_map(function($x) {
                        "title" => $x->get("title"),
                        "url_fragment" => ""
                    }, $showcasemakes)
                );

            } else if ($display_machine_types) {

            } else if ($display_models) {

            } else if ($display_trims) {

            }


            ?>
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
