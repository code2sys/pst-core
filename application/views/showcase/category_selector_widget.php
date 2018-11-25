<?php

$display_makes = false;
$display_machine_types = false;
$display_models = false;
$display_trims = false;
$showcasemake_id = null;
$showcasemodel_id = null;
$showcasemachinetype_id = null;
$showcasemakes = null;
$showcasemodels = null;
$showcasetrims = null;

global $PSTAPI;
initializePSTAPI();

$title = $pageRec["title"];
$full_url = "";

figureShowcaseFlags($pageRec, $display_makes, $display_machine_types, $display_models, $display_trims, $showcasemake_id, $showcasemodel_id, $showcasemachinetype_id, $full_url, $showcasemakes, $showcasemodels, $showcasetrims);

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$category_selector_v = mustache_tmpl_open("showcase/category_selector_widget.html");


$grid_widgets = array();
function prepare_widget_group($title, $source_array, &$grid_widgets, $use_display_title = false, $sorted_already = false, $subtitle = "") {
    if (!$sorted_already) {
        usort($source_array, function ($a, $b) {
            $a_title = $a->get("short_title");
            $b_title = $b->get("short_title");
            return strnatcasecmp($a_title, $b_title);
        });
    }

    if (count($source_array) > 0) {
        $grid_widgets[] = array(
            "title" => $title,
            "subtitle" => $subtitle,
            "tiles" => array_map(function ($x) use ($use_display_title) {
                return array(
                    "title" => $use_display_title ? $x->get("display_title") : $x->get("short_title"),
                    "url_fragment" => $x->get("full_url"),
                    "thumbnail" => $x->get("thumbnail_photo")
                );
            }, $source_array)
        );
    }

};

function sortByCategoryOrYear($showcasemodels, &$grid_widgets) {

    // First, you have to figure out if they have categories...

    // sort them by year...
    $year_buckets = array();
    $category_buckets = array();

    foreach ($showcasemodels as $m) {
        $year = intVal($m->get("year"));
        if (!array_key_exists($year, $year_buckets)) {
            $year_buckets[$year] = array();
        }
        $year_buckets[$year][] = $m;

        $category = $m->get("category");
        if (!array_key_exists($category, $category_buckets)) {
            $category_buckets[$category] = array();
        }
        $category_buckets[$category][] = $m;
    }


    if (count(array_keys($category_buckets)) > 1) {
        // display it by the category...
        /// sort them...
        $category_keys = array_keys($category_buckets);
        sort($category_keys);

        // now, display them...
        foreach ($category_keys as $c) {
            $cat_bucket = $category_buckets[$c];

            usort($cat_bucket, function($a, $b) {
                if (intVal($a->get("year")) != intVal($b->get("year"))) {
                    // newer first...
                    return (intVal($a->get("year")) > intVal($b->get("year"))) ? -1 : 1;
                } else {
                    return strnatcasecmp($a->get("title"), $b->get("title"));
                }
            });

            // we have to put the year on the short list, too.
            $clean_bucket = array();

            $current_year = 0;

            $title = $c;
            foreach ($cat_bucket as $cb) {
                if ($current_year != $cb->get("year")) {
                    if ($current_year > 0 && count($clean_bucket) > 0) {
                        prepare_widget_group($title, $clean_bucket, $grid_widgets, false, true, $current_year);
                        $title = "";
                    }
                    $clean_bucket = array();
                    $current_year = $cb->get("year");
                }
                $cb->set("short_title", $cb->get("year") . " " . $cb->get("short_title"));
                $clean_bucket[] = $cb;
            }

            if (count($clean_bucket) > 0) {
                prepare_widget_group($title, $clean_bucket, $grid_widgets, false, true, $current_year);
            }
        }

    } else {
        $year_bucket_keys = array_keys($year_buckets);
        sort($year_bucket_keys);
        $year_bucket_keys = array_reverse($year_bucket_keys);

        foreach ($year_bucket_keys as $year) {
            $buckets = $year_buckets[$year];

            prepare_widget_group($year. " Models", $buckets, $grid_widgets);
        }
    }
}


if ($display_makes) {
    prepare_widget_group("", $showcasemakes, $grid_widgets, true);
} else if ($display_machine_types) {
    prepare_widget_group("", $showcasemachinetypes, $grid_widgets);
} else if ($display_models) {
    sortByCategoryOrYear($showcasemodels, $grid_widgets);
} else if ($display_trims) {
    sortByCategoryOrYear($showcasetrims, $grid_widgets);
}

foreach ($grid_widgets as $grid_widget) {
    mustache_tmpl_iterate($category_selector_v, "grid_widgets");

    $transformed_tiles = array();
    for ($i = 0; $i < count($grid_widget["tiles"]); $i++) {
        $tile = $grid_widget["tiles"][$i];
        $new_tile = array(
            "tile_url" => site_url('Factory_Showroom/' . $tile["url_fragment"]),
            "tile_title" => $tile["title"],
            "thumbnail_url" => (is_null($tile['thumbnail']) || $tile['thumbnail'] == '') ? site_url('/assets/showcase_no_picture_available.png') : $tile['thumbnail']
        );
        if ($i > 0 && $i % 6 == 0) {
            $new_tile["next_row"] = true;
        }
        $transformed_tiles[] = $new_tile;
    }

    mustache_tmpl_set($category_selector_v, "grid_widgets", array(
        "subtitleonly" => $grid_widget["title"] == "" && $grid_widget["subtitle"] != "",
        "widget_title" => $grid_widget["title"],
        "widget_subtitle" => $grid_widget["subtitle"],
        "widget_tiles" => $transformed_tiles
    ));
}

print mustache_tmpl_parse($category_selector_v);