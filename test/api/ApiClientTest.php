<?php

namespace Bitmovin\test\api;

use Bitmovin\api\ApiClient;
use Bitmovin\test\AbstractBitmovinApiTest;

require_once __DIR__ . '/../vendor/autoload.php';

class ApiClientTest extends AbstractBitmovinApiTest
{
    private static $apiClient = NULL;

    public static function setUpBeforeClass()
    {
        self::$apiClient = new ApiClient(self::getApiKey());
    }

    /**
     * @test
     */
    public function testApiClientInit()
    {
        static::assertInstanceOf('Bitmovin\ApiClient', self::$apiClient);
    }

    public static function tearDownAfterClass()
    {

    }
}
