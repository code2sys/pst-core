<?php

if (isset($nav_categories) && is_array($nav_categories) && !empty($nav_categories)) {
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $template = mustache_tmpl_open("master/widgets/nav_categories.html");
    $base_url = base_url();
    $exclude_subnav = isset($exclude_subnav) ? $exclude_subnav : false;

    $county = 1;
    foreach ($nav_categories as $keyy => $navRow) {
        mustache_tmpl_iterate($template, "nav_categories");
        $navRow["base_url"] = $base_url;
        $navRow["county"] = $county;
        if ($exclude_subnav) {
            $navRow["subnav"] = false;
        } else {
            $navRow["subnav"] = array_values($navRow["subnav"]);
        }
        $navRow["separator"] = $county < count($nav_categories);
        mustache_tmpl_set($template, "nav_categories", $navRow);
        $county++;
    }
    echo mustache_tmpl_parse($template);
}

