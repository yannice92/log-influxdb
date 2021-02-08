<?php
/**
 *
 * Created by PhpStorm.
 * User: Fernando Yannice ( yannice92@gmail.com )
 * Date: 03/08/20
 * Time: 16.14
 */

namespace Yannice92\LogInfluxDb\Helpers;


use Carbon\Carbon;

class LogHelper
{
    /**
     * @param string $eventName
     * @param array $context
     */
    public static function write(string $eventName, array $context)
    {
        $data["eventName"] = $eventName;
        $data['log_date'] = Carbon::now()->setTimezone(new \DateTimeZone('Asia/Jakarta'))->format(\DateTime::RFC3339_EXTENDED);
        $data = array_merge($data, $context);
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln(json_encode($data));
    }
}
