<?php

namespace Bitmovin\test\api\codecConfigurations;

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\codecConfigurations\CodecConfigType;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\codecConfigurations\H265Profile;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H265VideoCodecConfiguration;
use Bitmovin\test\AbstractBitmovinApiTest;
use Bitmovin\test\api\util\RegexpHelper;

class CodecConfigurationResourceTest extends AbstractBitmovinApiTest
{
    /** @var  ApiClient */
    private static $apiClient;

    private function getApiClient()
    {
        return static::$apiClient;
    }

    public static function setUpBeforeClass()
    {
        static::$apiClient = new ApiClient(self::getApiKey());
    }

    public static function tearDownAfterClass()
    {
        static::$apiClient = NULL;
    }

    /**
     * @throws BitmovinException
     */
    public function testCreateAndDeleteAACAudioCodecConfiguration()
    {
        $name = "AAC CodecConfig 400kbit " . uniqid();
        $bitrate = 400000;
        $rate = 44100;

        $audioAacCodecConfig = new AACAudioCodecConfiguration($name, $bitrate, $rate);
        $createdAACAudioCodecConfig = $this->createAACAudioCodecConfiguration($audioAacCodecConfig);

        $this->assertInstanceOf(AACAudioCodecConfiguration::class, $createdAACAudioCodecConfig);
        $this->assertTrue(RegexpHelper::isUUID($createdAACAudioCodecConfig->getId()), "Valid UUID expected");

        //$this->assertEquals($name, $createdH264VideoCodecConfiguration->getName());
        //$this->assertEquals($profile, $createdH264VideoCodecConfiguration->getProfile());

        $deletedAACAudioCodecConfig = $this->deleteAACAudioCodecConfig($createdAACAudioCodecConfig);

        $this->assertTrue($deletedAACAudioCodecConfig instanceof AACAudioCodecConfiguration);
        $this->assertTrue(RegexpHelper::isUUID($deletedAACAudioCodecConfig->getId()), "Valid UUID expected");
    }

    public function testListAACAudioCodecConfigurations()
    {
        $apiClient = $this->getApiClient();
        $name = "AAC CodecConfig 400kbit " . uniqid();
        $bitrate = 400000;
        $rate = 44100;

        $audioAacCodecConfig = new AACAudioCodecConfiguration($name, $bitrate, $rate);
        $createdAACAudioCodecConfig = $this->createAACAudioCodecConfiguration($audioAacCodecConfig);

        $listResults = $apiClient->codecConfigurations()->audioAAC()->listPage();
        $this->assertTrue(is_array($listResults));
        $this->assertTrue(sizeof($listResults) > 0);

        foreach ($listResults as $result)
        {
            $this->assertTrue($result instanceof AACAudioCodecConfiguration);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }
        $this->deleteAACAudioCodecConfig($createdAACAudioCodecConfig);
    }

    public function testGetAudioAACCodecConfiguration()
    {
        $apiClient = $this->getApiClient();
        $name = "AAC CodecConfig 400kbit " . uniqid();
        $bitrate = 400000;
        $rate = 44100;

        $audioAacCodecConfig = new AACAudioCodecConfiguration($name, $bitrate, $rate);
        $createdAACAudioCodecConfig = $this->createAACAudioCodecConfiguration($audioAacCodecConfig);
        $resourceId = $apiClient->codecConfigurations()->audioAAC()->listPage()[0]->getId();

        $resource = $apiClient->codecConfigurations()->audioAAC()->getById($resourceId);

        $this->assertInstanceOf(AACAudioCodecConfiguration::class, $resource);
        $this->assertTrue(RegexpHelper::isUUID($resource->getId()), "Valid UUID expected");
        $this->deleteAACAudioCodecConfig($createdAACAudioCodecConfig);
    }

    public function testGetAACAudioCodecConfigurationNotFoundException()
    {
        $apiClient = $this->getApiClient();
        $resourceId = "NON-EXISTING-H264VideoCodecConfiguration-ID";

        $this->setExpectedException(BitmovinException::class, '', 404);
        $apiClient->codecConfigurations()->audioAAC()->getById($resourceId);
    }

    /**
     * @throws BitmovinException
     */
    public function testCreateAndDeleteH264VideoCodecConfiguration()
    {
        $name = "H264 CodecConfig 4Mbit " . uniqid();
        $profile = H264Profile::BASELINE;
        $bitrate = 4000000;
        $rate = 24;

        $h264VideoCodecConfig = new H264VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH264VideoCodecConfig = $this->createH264VideoCodecConfiguration($h264VideoCodecConfig);

        $this->assertInstanceOf(H264VideoCodecConfiguration::class, $createdH264VideoCodecConfig);
        $this->assertTrue(RegexpHelper::isUUID($createdH264VideoCodecConfig->getId()), "Valid UUID expected");

        //$this->assertEquals($name, $createdH264VideoCodecConfiguration->getName());
        //$this->assertEquals($profile, $createdH264VideoCodecConfiguration->getProfile());

        $deletedH264VideoCodecConfig = $this->deleteH264VideoCodecConfig($createdH264VideoCodecConfig);

        $this->assertTrue($deletedH264VideoCodecConfig instanceof H264VideoCodecConfiguration);
        $this->assertTrue(RegexpHelper::isUUID($deletedH264VideoCodecConfig->getId()), "Valid UUID expected");
    }

    public function testListH264VideoCodecConfigurations()
    {
        $apiClient = $this->getApiClient();
        $name = "H264 CodecConfig 4Mbit " . uniqid();
        $profile = H264Profile::BASELINE;
        $bitrate = 4000000;
        $rate = 24;

        $h264VideoCodecConfig = new H264VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH264VideoCodecConfig = $this->createH264VideoCodecConfiguration($h264VideoCodecConfig);

        $listResults = $apiClient->codecConfigurations()->videoH264()->listPage();
        $this->assertTrue(is_array($listResults));

        foreach ($listResults as $result)
        {
            $this->assertTrue($result instanceof H264VideoCodecConfiguration);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }
        $this->deleteH264VideoCodecConfig($createdH264VideoCodecConfig);
    }

    public function testGetH264VideoCodecConfiguration()
    {
        $apiClient = $this->getApiClient();
        $name = "H264 CodecConfig 4Mbit " . uniqid();
        $profile = H264Profile::BASELINE;
        $bitrate = 4000000;
        $rate = 24;

        $h264VideoCodecConfig = new H264VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH264VideoCodecConfig = $this->createH264VideoCodecConfiguration($h264VideoCodecConfig);
        $resourceId = $apiClient->codecConfigurations()->videoH264()->listPage()[0]->getId();

        $resource = $apiClient->codecConfigurations()->videoH264()->getById($resourceId);

        $this->assertInstanceOf(H264VideoCodecConfiguration::class, $resource);
        $this->assertTrue(RegexpHelper::isUUID($resource->getId()), "Valid UUID expected");
        $this->deleteH264VideoCodecConfig($createdH264VideoCodecConfig);
    }

    public function testGetH264VideoCodecConfigurationNotFoundException()
    {
        $apiClient = $this->getApiClient();
        $resourceId = "NON-EXISTING-H264VideoCodecConfiguration-ID";

        $this->setExpectedException(BitmovinException::class, '', 404);
        $apiClient->codecConfigurations()->videoH264()->getById($resourceId);
    }

    /**
     * @throws BitmovinException
     */
    public function testCreateAndDeleteH265VideoCodecConfiguration()
    {
        $name = "H265 CodecConfig 4Mbit " . uniqid();
        $profile = H265Profile::MAIN;
        $bitrate = 4000000;
        $rate = 24.0;

        $h265VideoCodecConfig = new H265VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH265VideoCodecConfig = $this->createH265VideoCodecConfiguration($h265VideoCodecConfig);

        $this->assertInstanceOf(H265VideoCodecConfiguration::class, $createdH265VideoCodecConfig);
        $this->assertTrue(RegexpHelper::isUUID($createdH265VideoCodecConfig->getId()), "Valid UUID expected");

        //$this->assertEquals($name, $createdH264VideoCodecConfiguration->getName());
        //$this->assertEquals($profile, $createdH264VideoCodecConfiguration->getProfile());

        $deletedH264VideoCodecConfig = $this->deleteH265VideoCodecConfig($createdH265VideoCodecConfig);

        $this->assertTrue($deletedH264VideoCodecConfig instanceof H265VideoCodecConfiguration);
        $this->assertTrue(RegexpHelper::isUUID($deletedH264VideoCodecConfig->getId()), "Valid UUID expected");
    }


    public function testListH265VideoCodecConfigurations()
    {
        $apiClient = $this->getApiClient();
        $name = "H265 CodecConfig 4Mbit " . uniqid();
        $profile = H265Profile::MAIN;
        $bitrate = 4000000;
        $rate = 24.0;

        $h265VideoCodecConfig = new H265VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH265VideoCodecConfig = $this->createH265VideoCodecConfiguration($h265VideoCodecConfig);

        $listResults = $apiClient->codecConfigurations()->videoH265()->listPage();
        $this->assertTrue(is_array($listResults));

        foreach ($listResults as $result)
        {
            $this->assertTrue($result instanceof H265VideoCodecConfiguration);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }
        $this->deleteH265VideoCodecConfig($createdH265VideoCodecConfig);
    }

    public function testGetH265VideoCodecConfiguration()
    {
        $apiClient = $this->getApiClient();
        $name = "H265 CodecConfig 4Mbit " . uniqid();
        $profile = H265Profile::MAIN;
        $bitrate = 4000000;
        $rate = 24.0;

        $h265VideoCodecConfig = new H265VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH265VideoCodecConfig = $this->createH265VideoCodecConfiguration($h265VideoCodecConfig);
        $resourceId = $apiClient->codecConfigurations()->videoH265()->listPage()[0]->getId();

        $resource = $apiClient->codecConfigurations()->videoH265()->getById($resourceId);

        $this->assertInstanceOf(H265VideoCodecConfiguration::class, $resource);
        $this->assertTrue(RegexpHelper::isUUID($resource->getId()), "Valid UUID expected");
        $this->deleteH265VideoCodecConfig($createdH265VideoCodecConfig);
    }

    public function testGetTypeH265VideoCodecConfiguration()
    {
        $apiClient = $this->getApiClient();
        $name = "H265 CodecConfig 4Mbit " . uniqid();
        $profile = H265Profile::MAIN;
        $bitrate = 4000000;
        $rate = 24.0;

        $h265VideoCodecConfig = new H265VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH265VideoCodecConfig = $this->createH265VideoCodecConfiguration($h265VideoCodecConfig);

        $type = $apiClient->codecConfigurations()->type()->getType($createdH265VideoCodecConfig);
        $this->assertEquals(CodecConfigType::H265, $type->getType());

        $this->deleteH265VideoCodecConfig($createdH265VideoCodecConfig);
    }

    public function testGetH265VideoCodecConfigurationNotFoundException()
    {
        $apiClient = $this->getApiClient();
        $resourceId = "NON-EXISTING-H265VideoCodecConfiguration-ID";

        $this->setExpectedException(BitmovinException::class, '', 404);
        $apiClient->codecConfigurations()->videoH265()->getById($resourceId);
    }

    /**
     * @param AACAudioCodecConfiguration $codecConfiguration
     *
     * @return AACAudioCodecConfiguration
     * @throws BitmovinException
     */
    private function createAACAudioCodecConfiguration(AACAudioCodecConfiguration $codecConfiguration)
    {
        $apiClient = $this->getApiClient();

        try
        {
            return $apiClient->codecConfigurations()->audioAAC()->create($codecConfiguration);
        }
        catch (BitmovinException $e)
        {
            var_dump(get_class($e), $e->getMessage(), $e->getDeveloperMessage());
            throw $e;
        }
    }

    /**
     * @param AACAudioCodecConfiguration $codecConfiguration
     *
     * @return AACAudioCodecConfiguration
     * @throws BitmovinException
     */
    private function deleteAACAudioCodecConfig(AACAudioCodecConfiguration $codecConfiguration)
    {
        $apiClient = $this->getApiClient();

        return $apiClient->codecConfigurations()->audioAAC()->delete($codecConfiguration);
    }

    /**
     * @param H264VideoCodecConfiguration $codecConfiguration
     *
     * @return H264VideoCodecConfiguration
     * @throws BitmovinException
     */
    private function createH264VideoCodecConfiguration(H264VideoCodecConfiguration $codecConfiguration)
    {
        $apiClient = $this->getApiClient();

        try
        {
            return $apiClient->codecConfigurations()->videoH264()->create($codecConfiguration);
        }
        catch (BitmovinException $e)
        {
            var_dump(get_class($e), $e->getMessage(), $e->getDeveloperMessage());
            throw $e;
        }
    }

    /**
     * @param H264VideoCodecConfiguration $codecConfiguration
     *
     * @return H264VideoCodecConfiguration
     * @throws BitmovinException
     */
    private function deleteH264VideoCodecConfig(H264VideoCodecConfiguration $codecConfiguration)
    {
        $apiClient = $this->getApiClient();

        return $apiClient->codecConfigurations()->videoH264()->delete($codecConfiguration);
    }

    /**
     * @param H265VideoCodecConfiguration $codecConfiguration
     *
     * @return H265VideoCodecConfiguration
     * @throws BitmovinException
     */
    private function createH265VideoCodecConfiguration(H265VideoCodecConfiguration $codecConfiguration)
    {
        $apiClient = $this->getApiClient();

        try
        {
            return $apiClient->codecConfigurations()->videoH265()->create($codecConfiguration);
        }
        catch (BitmovinException $e)
        {
            var_dump(get_class($e), $e->getMessage(), $e->getDeveloperMessage());
            throw $e;
        }
    }

    /**
     * @param H265VideoCodecConfiguration $codecConfiguration
     *
     * @return H265VideoCodecConfiguration
     * @throws BitmovinException
     */
    private function deleteH265VideoCodecConfig(H265VideoCodecConfiguration $codecConfiguration)
    {
        $apiClient = $this->getApiClient();

        return $apiClient->codecConfigurations()->videoH265()->delete($codecConfiguration);
    }
}
