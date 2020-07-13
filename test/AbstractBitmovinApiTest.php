<?php

namespace Bitmovin\test;


abstract class AbstractBitmovinApiTest extends \PHPUnit\Framework\TestCase
{

    protected static function getApiKey()
    {
        return $_ENV['BM_APIKEY'];
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
