<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/22/17
 * Time: 11:48 AM
 *
 * Extracted from CronJobMinute
 *
 */

require_once("cronjobminute.php");

class CronJobMail extends CronJobMinute
{

    public function runJob()
    {
        $this->procmail();
    }
}