<?php


if ($retail_price > 0) {
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $showcase_pricing_widget = mustache_tmpl_open("showcase/showcase_pricing_widget.html");
    mustache_tmpl_set($showcase_pricing_widget, "retail_price", number_format(floatVal($retail_price), 2));
    echo mustache_tmpl_parse($showcase_pricing_widget);
}

