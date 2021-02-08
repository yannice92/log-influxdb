<?php
/**
 *
 * Created by PhpStorm.
 * User: Fernando Yannice ( yannice92@gmail.com )
 * Date: 31/08/20
 * Time: 18.22
 */

namespace Yannice92\LogInfluxDb\Drivers;

use Carbon\Carbon;
use InfluxDB;
use Yannice92\LogInfluxDb\Helpers\LogHelper;

class InfluxDbDriver implements LogDriverInterface
{
    const MEASUREMENT = 'service_monitor';
    const EVENT_TYPE = 'eventType';
    const SERVICE_EXTERNAL = 'serviceExternal';
    const SUCCESS = 'success';
    const SERVICE = 'service';
    /** @var $database InfluxDB\Database */
    private $client, $database;
    private $measurement, $tags, $fields, $timestamp;
    private $responseTime = 0.0;

    /**
     * @param mixed $measurement
     */
    public function setMeasurement(string $measurement)
    {
        $this->measurement = $measurement;
        return $this;
    }

    /**
     * @param mixed $tags
     */
    public function setTags(array $tags)
    {
        if ($this->tags)
            $tags = array_merge($this->tags, $tags);
        $this->tags = $tags;
        return $this;
    }

    /**
     * @param mixed $fields
     */
    public function setFields(array $fields)
    {
        if ($this->fields)
            $fields = array_merge($this->fields, $fields);
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp(int $timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function responseTime(float $responseTime)
    {
        $this->responseTime = $responseTime;
        return $this;
    }

    public function writeLog()
    {
        try {
            $this->init();
            $this->fields = array_merge(['responseTime' => $this->responseTime], $this->fields);
            if (!$this->timestamp) {
                $timestamp = (int)Carbon::now()->getPreciseTimestamp();
            }
            $points = array(
                new InfluxDB\Point(
                    $this->measurement, // name of the measurement
                    null, // the measurement value
                    $this->tags, // optional tags
                    $this->fields, // optional additional fields
                    $timestamp // Time precision has to be set to seconds!
                )
            );
// we are writing unix timestamps, which have a second precision
            $result = $this->database->writePoints($points, InfluxDB\Database::PRECISION_MICROSECONDS, env('INFLUXDB_RETENTION_POLICY', 'default'));
        } catch (\Throwable $e) {
            LogHelper::write('INFLUX_DB', ['error' => $e->getMessage()]);
        }
    }

    public function init()
    {
        $this->client = new InfluxDB\Client(
            env('INFLUXDB_HOST', 'localhost'),
            env('INFLUXDB_PORT', 8086),
            env('INFLUXDB_USER', 'admin'),
            env('INFLUXDB_PASSWORD', ''),
            env('INFLUXDB_SSL', false),
            env('INFLUXDB_VERIFY_SSL', false),
            env('INFLUXDB_TIMEOUT', 0),
            env('INFLUXDB_CONNECT_TIMEOUT', 0));

        $this->database = $this->client->selectDB(env('INFLUXDB_DB'));
    }
}
