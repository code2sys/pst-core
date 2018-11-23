<?php
global $PSTAPI;
initializePSTAPI();

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$breadcrumbs_menu = mustache_tmpl_open("showcase/breadcrumbs.html");


if ($title != "") {
    mustache_tmpl_set($breadcrumbs_menu, "title", $title);
}


if ($full_url != "") {
    mustache_tmpl_set($breadcrumbs_menu, "show_nav", true);
    mustache_tmpl_set($breadcrumbs_menu, "home_nav", site_url(""));
    mustache_tmpl_set($breadcrumbs_menu, "showroom_nav", site_url("Factory_Showroom"));

    $pieces = explode("/", $full_url);

    for ($i = 0; $i < count($pieces); $i++) {
        $piece = $pieces[$i];

        $factory = "showcase";
        switch ($i) {
            case 0:
                $factory .= "make";
                break;

            case 1:
                $factory .= "machinetype";
                break;

            case 2:
                $factory .= "model";
                break;

            case 3:
                $factory .= "trim";
                break;
        }

        $object = $PSTAPI->$factory()->fetch(array(
            "url_title" => $piece
        ));

        if (count($object) > 0) {
            $object = $object[0];
            mustache_tmpl_iterate($breadcrumbs_menu, "nav_loop");
            mustache_tmpl_set($breadcrumbs_menu, "nav_loop", array(
                "full_url" => site_url("Factory_Showroom/" . $object->get("full_url")),
                "link_title" => $object->get("title")
            ));
        }
    }

}

print mustache_tmpl_parse($breadcrumbs_menu);


