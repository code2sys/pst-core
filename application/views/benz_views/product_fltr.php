<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 9/3/18
 * Time: 11:43 AM
 */

$CI =& get_instance();
$CI->load->helper("mustache_helper");
$template = mustache_tmpl_open("benz_views/product_fltr.html");

mustache_tmpl_set($template, "MOTORCYCLE_SHOP_NEW", MOTORCYCLE_SHOP_NEW);
mustache_tmpl_set($template, "fltr_special_active", $_GET['fltr'] == 'special' );
mustache_tmpl_set($template, "fltr_new_active", $_GET['fltr'] == 'New_Inventory' );
mustache_tmpl_set($template, "fltr_preowned_active", $_GET['fltr'] == 'pre-owned' );
mustache_tmpl_set($template, "major_units_featured_only", array_key_exists("major_units_featured_only", $_SESSION) && $_SESSION["major_units_featured_only"] > 0);

mustache_tmpl_set($template, "major_unit_search_keywords", htmlentities(array_key_exists("major_unit_search_keywords", $_SESSION) ? $_SESSION["major_unit_search_keywords"] : ""));

$currentURL     = current_url();
$queryString    = $_SERVER['QUERY_STRING'];
$params         = explode('&', $queryString);
$params         = array_filter($params);

$indexedParams = array();
foreach ($params as $param) {
    $exp = explode('=', $param);
    $indexedParams[$exp[0]] = $param;
}

$fltrUrl = '';
$brandsUrl = '';
$yearsUrl = '';
$vehiclesUrl = '';
$categoriesUrl = '';

if (!array_key_exists("fltr", $_REQUEST) && !array_key_exists("fltr", $_GET)) {
    $fltrUrl = '?fltr=New_Inventory';
} else {
    $fltrUrl = '?'.$indexedParams['fltr'];
}
if (array_key_exists("categories", $_REQUEST) && array_key_exists("categories", $_GET)) {
    $categoriesUrl = '&'.$indexedParams['categories'];
}
if (array_key_exists("brands", $_REQUEST) && array_key_exists("brands", $_GET)) {
    $brandsUrl = '&'.$indexedParams['brands'];
}
if (array_key_exists("vehicles", $_REQUEST) && array_key_exists("vehicles", $_GET)) {
    $vehiclesUrl = '&'.$indexedParams['vehicles'];
}
if (array_key_exists("years", $_REQUEST) && array_key_exists("years", $_GET)) {
    $yearsUrl = '&'.$indexedParams['years'];
}


$ctgrs = explode('$', $_GET['categories']);
$ctgrs = array_filter($ctgrs);
foreach ($categories as $category) {
    $key = array_search($category['name'], $ctgrs);

    $filteredUrl = '&categories=';
    
    if ($ctgrs[$key] == $category['name']) {
        $tempCtgrs = $ctgrs;
        unset($tempCtgrs[$key]);

        if (count($tempCtgrs) > 0) {
            foreach( $tempCtgrs as $temp ) {
                $filteredUrl .= $temp.'$';
            }
            $filteredUrl = substr($filteredUrl, 0, -1);
        } else {
            $filteredUrl = '';
        }
    } else {
        if ( $categoriesUrl != '' ) {
            $filteredUrl = $categoriesUrl.'$'.$category['name'];
        } else {
            $filteredUrl = '&categories='.$category['name'];
        }
    }
    
    mustache_tmpl_iterate($template, "categories");
    mustache_tmpl_set($template, "categories", array(
        "category_id" => $category['id'],
        "filter_link" => $currentURL . $fltrUrl . $brandsUrl . $filteredUrl . $yearsUrl . $vehiclesUrl . '&'.$indexedParams['filterChange'],
        "checked" => $ctgrs[$key] == $category['name'],
        "category_name" => $category['name']
    ));
}

$brnds = explode('$', $_GET['brands']);
$brnds = array_filter($brnds);

foreach ($brands as $k => $brand) {
    $key = array_search($brand['make'], $brnds);

    $filteredUrl = '&brands=';    
    if ($brnds[$key] == $brand['make']) {
        $tempBrnds = $brnds;
        unset($tempBrnds[$key]);

        if (count($tempBrnds) > 0) {
            foreach( $tempBrnds as $temp ) {
                $filteredUrl .= $temp.'$';
            }
            $filteredUrl = substr($filteredUrl, 0, -1);
        } else {
            $filteredUrl = '';
        }
    } else {
        if ( $brandsUrl != '' ) {
            $filteredUrl = $brandsUrl.'$'.$brand['make'];
        } else {
            $filteredUrl = '&brands='.$brand['make'];
        }
    }

    mustache_tmpl_iterate($template, "brands");
    mustache_tmpl_set($template, "brands", array(
        "brand_make" => $brand['make'],
        "k" => $k,
        "filter_link" => $currentURL . $fltrUrl . $filteredUrl . $categoriesUrl . $yearsUrl . $vehiclesUrl . '&'.$indexedParams['filterChange'],
        "checked" => $brnds[$key] == $brand['make']
    ));
}


$vhcls = explode('$', $_GET['vehicles']);
$vhcls = array_filter($vhcls);

foreach ($vehicles as $vehicle) {
    $key = array_search($vehicle['name'], $vhcls);

    $filteredUrl = '&vehicles=';    
    if ($vhcls[$key] == $vehicle['name']) {
        $tempVhcls = $vhcls;
        unset($tempVhcls[$key]);

        if (count($tempVhcls) > 0) {
            foreach( $tempVhcls as $temp ) {
                $filteredUrl .= $temp.'$';
            }
            $filteredUrl = substr($filteredUrl, 0, -1);
        } else {
            $filteredUrl = '';
        }
    } else {
        if ( $vehiclesUrl != '' ) {
            $filteredUrl = $vehiclesUrl.'$'.$vehicle['name'];
        } else {
            $filteredUrl = '&vehicles='.$vehicle['name'];
        }
    }
    mustache_tmpl_iterate($template, "vehicles");
    mustache_tmpl_set($template, "vehicles", array(
        "vehicle_id" => $vehicle['id'],
        "vehicle_name" => $vehicle['name'],
        "filter_link" => $currentURL . $fltrUrl . $brandsUrl . $categoriesUrl . $yearsUrl . $filteredUrl . '&'.$indexedParams['filterChange'],
        "checked" => $vhcls[$key] == $vehicle['name']
    ));
}

$yr = explode('$', $_GET['years']);
$yr = array_filter($yr);

foreach ($years as $k => $year) {
    $key = array_search($year['year'], $yr);

    $filteredUrl = '&years=';    
    if ($yr[$key] == $year['year']) {
        $tempYr = $yr;
        unset($tempYr[$key]);

        if (count($tempYr) > 0) {
            foreach( $tempYr as $temp ) {
                $filteredUrl .= $temp.'$';
            }
            $filteredUrl = substr($filteredUrl, 0, -1);
        } else {
            $filteredUrl = '';
        }
    } else {
        if ( $yearsUrl != '' ) {
            $filteredUrl = $yearsUrl.'$'.$year['year'];
        } else {
            $filteredUrl = '&years='.$year['year'];
        }
    }

    mustache_tmpl_iterate($template, "years");
    mustache_tmpl_set($template, "years", array(
        "k" => $k,
        "year" => $year['year'],
        "filter_link" => $currentURL . $fltrUrl . $brandsUrl . $categoriesUrl . $filteredUrl . $vehiclesUrl . '&'.$indexedParams['filterChange'],
        "checked" => $yr[$key] == $year['year']
    ));
}

// there's a specifically-styled one


print mustache_tmpl_parse($template);
