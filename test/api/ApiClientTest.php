<?php

namespace Bitmovin\test\api;

use Bitmovin\api\ApiClient;
use Bitmovin\test\AbstractBitmovinApiTest;


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
        static::assertInstanceOf("Bitmovin\api\ApiClient", self::$apiClient);
    }

    public static function tearDownAfterClass()
    {

    }
}
