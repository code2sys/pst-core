
<?php
/**
 * Created by VS Code.
 * User: Robert
 * Date: 10/31/18
 * Time: 20:00 PM
 */

$media_url = jsite_url("/media/");

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("info/site_map.html");

foreach ($motorcycles as $motorcycle) {

    // What is the default...
    $motorcycle_image = $motorcycle['image_name'];
    if ($motorcycle['external'] == 0) {
        $motorcycle_image = $media_url . $motorcycle_image;
    }

    if ($motorcycle_image == "" || is_null($motorcycle_image) || $motorcycle_image == $media_url) {
        $motorcycle_image = "/assets/image_unavailable.png";
    }

    mustache_tmpl_iterate($template, "motorcycles");


    mustache_tmpl_set($template, "motorcycles", array(
        "url" => base_url(strtolower($motorcycle['type']) . '/' . $motorcycle['url_title'] . '/' . $motorcycle['sku']),
        "motorcycle_url_title" => $motorcycle["url_title"],
        "motorcycle_sku" => $motorcycle["sku"],
        "motorcycle_image" => $motorcycle_image,
        "motorcycle_title" => $motorcycle["title"],
        "motorcycle_description" => $motorcycle["description"],
        "condition" => $motorcycle['condition'] == '1' ? 'New' : 'Pre-Owned'
    ));
}

print mustache_tmpl_parse($template);
