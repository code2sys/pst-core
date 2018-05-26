<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 5/25/18
 * Time: 7:00 PM
 *
 * There was this logic everywhere - the "hotels and flats" slider.
 * Basically, they would stick these in various places, and they would slowly drift along...but they only did it really for motorcycles.
 * So, instead, I want to make a generic, reusable widget that will just do this, and it will subsume the old one.
 *
 * Why the name? I assume Benz pulled this out of a rental website.
 *
 * Inputs:
 * $rotating_things: An array of things to display, with field "rendered_html"
 * $header_class
 * $header_text
 * $extra_master_classes
 * $wrapper_id
 * $header_link
 * $header_link_text
 * $div_extra_classes
 */


if (count($rotating_things) > 0) {

    ?><!--
    <?php print_r($rotating_things); ?>

    --> <?php

    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $template = mustache_tmpl_open("master/widgets/hotelsflatsslider.html");

    global $hotels_flats_counter;

    if (!isset($hotels_flats_counter)) {
        $hotels_flats_counter = 0;
    }

    $hotels_flats_counter++;
    mustache_tmpl_set($template, "global_counter", $hotels_flats_counter);

    // set the HTML pieces
    foreach ($rotating_things as $r) {
        mustache_tmpl_iterate($template, "rotating_things");
        $r["item_extra_class"] = array_key_exists("item_extra_class", $r) ? r["item_extra_class"] : $item_extra_class;
        $r["box_extra_class"] = array_key_exists("box_extra_class", $r) ? r["box_extra_class"] : $box_extra_class;
        mustache_tmpl_set($template, "rotating_things", $r);
    }

    // just pass all these through...
    foreach (array("header_class", "header_text", "extra_master_classes", "wrapper_id", "header_link", "header_link_text", "div_extra_classes", "item_extra_class", "box_extra_class") as $key) {
        if (isset($$key)) {
            mustache_tmpl_set($template, $key, $$key);
        }
    }

    echo mustache_tmpl_parse($template);
}