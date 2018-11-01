
<?php
/**
 * Created by VS Code.
 * User: Robert
 * Date: 10/31/18
 * Time: 20:00 PM
 */


$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("info/site_map.html");

echo "<pre>";
print_r($motorcycles[0]);
echo "</pre>";

mustache_tmpl_set($template, "motorcycles", $motorcycles);

print mustache_tmpl_parse($template);
