<?php
/**
 *
 * Created by PhpStorm.
 * User: Fernando Yannice ( yannice92@gmail.com )
 * Date: 01/09/20
 * Time: 15.48
 */

namespace Yannice92\LogInfluxDb\Drivers;


interface LogDriverInterface
{
    public function init();

    public function writeLog();

    public function responseTime(float $responseTime);
}
