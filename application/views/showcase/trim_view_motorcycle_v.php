<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 11/23/18
 * Time: 10:32 AM
 */

$CI =& get_instance();
global $PSTAPI;
initializePSTAPI();
$showcasetrims = $PSTAPI->showcasetrim()->fetch(array(
    "page_id" => $pageRec["id"],
    "deleted" => 0
));

$title = $pageRec["title"];
$full_url = "";
$new_assets_url = jsite_url( "/qatesting/newassets/" );
$media_url = jsite_url(  "/media/" );

$show_spec = false;
$show_info = false;
$images = array();
$specs = array();
$description = "";

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


    global $PSTAPI;
    initializePSTAPI();
    $specs = $PSTAPI->showcasespec()->fetch(array(
        "showcasetrim_id" => $showcasetrim->id(),
        "deleted" => 0
    ), true);

    // sort it..
    usort($specs, function($a, $b) {
        $a_o = intVal($a["ordinal"]);
        $b_o = intVal($b["ordinal"]);

        if ($a_o < $b_o) {
            return -1;
        } else if ($a_o > $b_o) {
            return 1;
        } else {
            return 0;
        }
    });

    // you have to filter..
    $hidden_groups = array_map(function($x) { return $x->id(); }, $PSTAPI->showcasespecgroup()->fetch(array("deleted" => 1, "showcasetrim_id" => $showcasetrim->id() )));

    $specs = array_values(array_filter($specs, function($x) use ($hidden_groups) {
        if ($x["crs_attribute_id"] >= 230000) {
            return false;
        } else if ($x["crs_attribute_id"] < 20000) {
            return false;
        } else if (in_array($x["showcasespecgroup_id"], $hidden_groups)) {
            return false;
        } else {
            return true;
        }
    }));

    $spec_groups =  $PSTAPI->showcasespecgroup()->fetch(array("deleted" => 0, "showcasetrim_id" => $showcasetrim->id()));
    // LUT
    $specgroup_LUT = array();
    foreach ($spec_groups as $sg) {
        $specgroup_LUT[$sg->id()] = $sg->get("name");
    }


    for ($i = 0; $i < count($specs); $i++) {
        $show_spec = true;
        $specs[$i]["spec_group"] = $specgroup_LUT[ $specs[$i]["showcasespecgroup_id"]];
    }

    // "year", "make", "model", "vehicle_type",
    $showcasemodel = $PSTAPI->showcasemodel()->get($showcasetrim->get("showcasemodel_id"));
    $showcasemachinetype = $PSTAPI->showcasemachinetype()->get($showcasemodel->get("showcasemachinetype_id"));
    $showcasemake = $PSTAPI->showcasemake()->get($showcasemachinetype->get("showcasemake_id"));

    $showcasetrim->set("year", $showcasemodel->get("year"));
    $showcasetrim->set("model", $showcasemodel->get("short_title"));
    $showcasetrim->set("make", $showcasemake->get("display_title"));
    $showcasetrim->set("vehicle_type", $showcasemachinetype->get("short_title"));

}

echo $CI->load->view("benz_views/sub_product-details", array(
    "in_showroom" => true,
    "override_breadcrumbs" => true,
    "breadcrumbs" => $CI->load->view("showcase/breadcrumbs", array(
        "title" => $title,
        "full_url" => $full_url
    ), true),
    "filter_motorcycle_images" => false,
    "new_assets_url" => $new_assets_url,
    "media_url" => $media_url,
    "mainVideo" => array(),
    "motorcycle" => array(
        "id" => $showcasetrim->id(),
        "images" => $images,
        "title" => $title,
        "year" => $showcasetrim->get("year"),
        "make" => $showcasetrim->get("make"),
        "model" => $showcasetrim->get("model"),
        "vehicle_type" => $showcasetrim->get("vehicle_type"),
        "category" => $showcasetrim->get("category"),
        "engine_type" => $showcasetrim->get("engine_type"),
        "transmission" => $showcasetrim->get("transmission"),
        "specs" => $specs,
        "description" => $description
    ),
    "override_pricing_widget" => true,
    "pricing_widget" => $CI->load->view("showcase/showcase_pricing_widget", array(
        "retail_price" => $showcasetrim->get("retail_price")
    ), true),
    "hide_stock_information" => true,
    "hide_recently_viewed" => true,
    "description" => $description
), true);