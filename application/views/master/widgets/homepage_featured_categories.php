<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 6/28/18
 * Time: 9:27 AM
 */

if (isset($featuredCategories) && count($featuredCategories) > 0) {
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $homepage_featured_categories_template = mustache_tmpl_open("master/widgets/homepage_featured_categories.html");

    foreach ( $featuredCategories as $key => $val ) {
        mustache_tmpl_iterate($homepage_featured_categories_template, "featuredCategories");
        $val["url"] = site_url('media/'.$val['image']);
        mustache_tmpl_set($homepage_featured_categories_template, "featuredCategories", $val);
    }

    echo mustache_tmpl_parse($homepage_featured_categories_template);
}
