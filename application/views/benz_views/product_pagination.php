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
echo "<pre>";
echo $pages."###".$cpage;
echo "</pre>";
mustache_tmpl_set($template, "pages", $pages);
mustache_tmpl_set($template, "page", $cpage);
mustache_tmpl_set($template, "MorePagesThan1", $pages > 1);
mustache_tmpl_set($template, "MorePageThan1", $cpage > 1);
mustache_tmpl_set($template, "MorePageThan2", $cpage > 2);
mustache_tmpl_set($template, "MorePageThan3", $cpage > 3);
mustache_tmpl_set($template, "MorePagesThanPage", $cpage < $pages);
mustache_tmpl_set($template, "MorePagesThanPage1", ($page < ($pages-2)));
mustache_tmpl_set($template, "MorePagesThanPage2", ($page < ($pages-3)));
mustache_tmpl_set($template, "minpage1", $cpage-1);
mustache_tmpl_set($template, "minpage2", $cpage-2);
mustache_tmpl_set($template, "plupage1", $cpage+1);
mustache_tmpl_set($template, "plupage2", $cpage+2);

print mustache_tmpl_parse($template);