<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 10/23/17
 * Time: 10:50 AM
 */

$has_fitment = array_key_exists("garage", $_SESSION) && count($_SESSION["garage"]) > 0;
$fitment_image = "/assets/perfect_fit.png";
$universal_image = "/assets/universal_fit.png";
$fitment_width = 155;
$universal_width = 177;
$fitment_height =$universal_height = 68;