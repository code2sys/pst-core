<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/22/17
 * Time: 12:19 PM
 */

require_once(__DIR__ . "/../master_m.php");

abstract class AbstractCronJob extends Master_M
{
    abstract public function runJob();
}