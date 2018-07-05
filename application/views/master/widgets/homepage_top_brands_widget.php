<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 6/28/18
 * Time: 9:33 AM
 */

if (isset($featuredBrands) && count($featuredBrands) > 0) {
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $homepage_top_brands_widget_template = mustache_tmpl_open("master/widgets/homepage_top_brands_widget.html");
    mustache_tmpl_set($homepage_top_brands_widget_template, "shop_all_brands_link", site_url('Motorcycle_Gear_Brands'));

    foreach( $featuredBrands as $key => $val ) {
        mustache_tmpl_iterate($homepage_top_brands_widget_template, "featuredBrands");
        $val["slug_url"] = site_url($val['slug']);
        if ($val['image'] != '') {
            $val['image_url'] = site_url('media/'.$val['image']);
        } else {
            $val['image_url'] = false;
        }
        mustache_tmpl_set($homepage_top_brands_widget_template, "featuredBrands", $val);
    }

    echo mustache_tmpl_parse($homepage_top_brands_widget_template);
}

