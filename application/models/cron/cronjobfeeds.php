<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/29/17
 * Time: 5:12 PM
 */

require_once("cronjobminute.php");

class Cronjobfeeds extends CronJobMinute
{

    public function runJob()
    {
        $this->feeds();
    }
}