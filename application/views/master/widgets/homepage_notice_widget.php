<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 6/28/18
 * Time: 9:31 AM
 */


if (isset($notice) && $notice != "") {
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $homepage_featured_categories_template = mustache_tmpl_open("master/widgets/homepage_notice_widget.html");
    mustache_tmpl_set($homepage_featured_categories_template, "notice", $notice);
    echo mustache_tmpl_parse($homepage_featured_categories_template);
}
