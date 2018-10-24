<?php

// fix the image URLs.

if (!isset($motorcycle_template)) {
    $motorcycle_template = "master/widgets/motorcycles.html";
}
$template = mustache_tmpl_open($motorcycle_template);

mustache_tmpl_set($template, "DISABLE_FRONT_MOTORCYCLE_NAV", defined("DISABLE_FRONT_MOTORCYCLE_NAV") && DISABLE_FRONT_MOTORCYCLE_NAV);
mustache_tmpl_set($template, "MOTORCYCLE_SHOP_DISABLE", defined("MOTORCYCLE_SHOP_DISABLE") && MOTORCYCLE_SHOP_DISABLE);

if (defined("MOTORCYCLE_SHOP_NEW") && MOTORCYCLE_SHOP_NEW && (!defined("MOTORCYCLE_SHOP_USED") || MOTORCYCLE_SHOP_USED)) {
    mustache_tmpl_set($template, "MOTORCYCLE_OPTION_1", true);
    mustache_tmpl_set($template, "moto_width", 3);
} else if (defined("MOTORCYCLE_SHOP_NEW") && MOTORCYCLE_SHOP_NEW) {
    mustache_tmpl_set($template, "MOTORCYCLE_OPTION_2", true);
    mustache_tmpl_set($template, "moto_width", 4);
} elseif (!defined("MOTORCYCLE_SHOP_USED") || MOTORCYCLE_SHOP_USED) {
    mustache_tmpl_set($template, "MOTORCYCLE_OPTION_3", true);
    mustache_tmpl_set($template, "moto_width", 4);
} else {
    mustache_tmpl_set($template, "moto_width", 6);
}

mustache_tmpl_set($template, "MOTORCYCLE_SHOP_DISABLE", defined("MOTORCYCLE_SHOP_DISABLE") && MOTORCYCLE_SHOP_DISABLE);
mustache_tmpl_set($template, "MOTORCYCLE_SHOP_DISABLE", defined("MOTORCYCLE_SHOP_DISABLE") && MOTORCYCLE_SHOP_DISABLE);

// JLB 10-22-18
// If we have this link several spots, and you ever wanted to change it, we should factor it out and just have a default
mustache_tmpl_set($template, "MOTORCYCLE_SERVICE_LINK_URL", defined('MOTORCYCLE_SERVICE_LINK_URL') ? MOTORCYCLE_SERVICE_LINK_URL : "/pages/index/servicerequest");

if (count($featured) > 0) {
    mustache_tmpl_set($template, "ShowFeaturedModels", true);

    foreach ($featured as $feature) {
        if ($feature["call_on_price"] == 1) {
            $price = '<h2 class="cfp">Call For Price</h2>';
        }
        else {
            if ($feature["sale_price"] == 0 || $feature["sale_price"] === "0.00") {
                $price = "<h2>Retail Price: $" . $feature["retail_price"] . "</h2>";
            } else {
                $price = "<h2>Sale Price: $" . $feature["sale_price"] . "</h2>";
            }
        }

        mustache_tmpl_iterate($template, "FeaturedModels");
        mustache_tmpl_set($template, "FeaturedModels", array(
            "link" => strtolower(str_replace(" ", "", $feature['type'])).'/'.$feature['url_title'].'/'.$feature['sku'],
            "image_name" => $feature["external"] > 0 ? $feature["image_name"] : ("/media/" . $feature["image_name"]),
            "original_title" => $feature["title"],
            "price" => $price,
            "destination_charge" => $feature["destination_charge"]
        ));
    }
}

echo mustache_tmpl_parse($template);
