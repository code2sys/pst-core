<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 6/28/18
 * Time: 9:21 AM
 */

if (isset($topVideo) && count($topVideo) > 0) {
    $CI =& get_instance();
    $CI->load->helper("mustache_helper");
    $homepage_top_videos_template = mustache_tmpl_open("master/widgets/homepage_top_videos.html");

    foreach ($topVideo as $key => $val) {
        mustache_tmpl_iterate($homepage_top_videos_template, "topvideo");
        mustache_tmpl_set($homepage_top_videos_template, "topvideo", $val);
    }

    echo mustache_tmpl_parse($homepage_top_videos_template);
}
