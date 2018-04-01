<?php
$CI =& get_instance();
$CI->load->helper("mustache_helper");
// If that thing exists, you echo it.
$motorcycle_action_buttons = mustache_tmpl_open("store_header_marquee.html");
echo mustache_tmpl_parse($motorcycle_action_buttons);
$motorcycle_action_buttons = mustache_tmpl_open("store_header_banner.html");
echo mustache_tmpl_parse($motorcycle_action_buttons);