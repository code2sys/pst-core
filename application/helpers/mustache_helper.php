<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 8/14/17
 * Time: 12:04 PM
 */

global $PST_MUSTACHE;
$PST_MUSTACHE = new Mustache_Engine(array(
    "cache" => __DIR__ . "/../../mcache/",
    "escape" => function($value) {
        return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
    }
));

function mustache_tmpl_open($filename) {
    if (file_exists(STORE_DIRECTORY . "/override_templates/" . $filename)) {
        $filename = STORE_DIRECTORY . "/override_templates/" . $filename;
    } elseif (file_exists(CORE_DIRECTORY . "/mtemplates/" . $filename)) {
        $filename = CORE_DIRECTORY . "/mtemplates/" . $filename;
    } else {
        throw new \Exception("Template not found: " . $filename);
    }

    return mustache_tmpl_load(file_get_contents($filename));
}
function mustache_tmpl_load($string) {
    $template = array(
        "template" => $string,
        "data" => array()
    );

    /*
     * If there are default variables that all templates could use, please put those here.
     */

    return $template;
}
function mustache_tmpl_set(&$template, $key, $value) {
    if (array_key_exists($key, $template["data"]) && is_array($template["data"][$key])) {
        $template["data"][$key][] = $value;
    } else {
        $template["data"][$key] = $value;
    }
}
function mustache_tmpl_iterate(&$template, $loop) {
    if (!array_key_exists($loop, $template["data"])) {
        $template["data"][$loop] = array();
    }
}

function mustache_tmpl_parse(&$template) {
    global $PST_MUSTACHE;
    if (!array_key_exists("the_current_year", $template["data"])) {
        mustache_tmpl_set($template, "the_current_year", date("Y"));
    }
    return $PST_MUSTACHE->render($template["template"], $template["data"]);
}
