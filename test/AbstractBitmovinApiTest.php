<?php

namespace Bitmovin\test;

require_once __DIR__ . '/../vendor/autoload.php';

abstract class AbstractBitmovinApiTest extends \PHPUnit_Framework_TestCase
{

    protected static function getApiKey()
    {
        return json_decode(file_get_contents(__DIR__ . '/config.json'), true)['apiKey'];
    }

    protected static function getConfig()
    {
        return json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    }

    protected static function callMethod($obj, $name, array $args)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

}