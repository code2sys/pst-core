<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 6/28/18
 * Time: 9:17 AM
 */

if (isset($bannerImages) && count($bannerImages) > 0) {
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $homepage_main_slider_template = mustache_tmpl_open("master/widgets/homepage_main_slider.html");

    foreach ($bannerImages as $image) {
        mustache_tmpl_iterate($homepage_main_slider_template, "bannerimages");
        mustache_tmpl_set($homepage_main_slider_template, "bannerimages", $image);
    }

    echo mustache_tmpl_parse($homepage_main_slider_template);
}

