<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 8/27/18
 * Time: 4:57 PM
 */

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("my404.html");
mustache_tmpl_set($template, "base_url", site_url("/"));
print mustache_tmpl_parse($template);