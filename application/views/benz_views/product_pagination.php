<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 9/3/18
 * Time: 12:18 PM
 */

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("benz_views/product_pagination.html");

mustache_tmpl_set($template, "MoreThan1", $pages > 1);
mustache_tmpl_set($template, "MoreThan2", $pages > 2);
mustache_tmpl_set($template, "MoreThan3", $pages > 3);

print mustache_tmpl_parse($template);