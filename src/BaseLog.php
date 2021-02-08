<?php
/**
 *
 * Created by PhpStorm.
 * User: Fernando Yannice ( yannice92@gmail.com )
 * Date: 02/09/20
 * Time: 11.33
 */

namespace Yannice92\LogInfluxDb;

use Yannice92\LogInfluxDb\Drivers\InfluxDbDriver;

class BaseLog
{
    public $influxClient;
    protected $statusCode = 200;

    public function __construct()
    {
        $this->influxClient = new InfluxDbDriver();
        $this->influxClient->setMeasurement(InfluxDbDriver::MEASUREMENT);
        $this->influxClient->setFields(['hostname' => gethostname()]);
        $this->influxClient->setTags(['node' => env('NODE')]);
    }

    /**
     * Use this for calculate response time when log not use with middleware.
     * @param $startTime
     * @param $endTime
     * @return InfluxDbDriver
     */
    public function calculateResponseTime($startTime, $endTime)
    {
        $responseTimeInMs = (float)number_format((float)($endTime - $startTime) * 1000, 2, '.', '');
        $this->influxClient->responseTime($responseTimeInMs);
        return $this->influxClient;
    }

    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        $this->influxClient->setFields(['statusCode' => (int)$this->statusCode]);
        return $this;
    }
}
