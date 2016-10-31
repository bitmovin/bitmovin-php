<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\enum\codecConfigurations\CodecConfigType;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H265VideoCodecConfiguration;
use Bitmovin\api\resource\codecConfigurations\AACAudioCodecConfigurationResource;
use Bitmovin\api\resource\codecConfigurations\H264VideoCodecConfigurationResource;
use Bitmovin\api\resource\codecConfigurations\H265VideoCodecConfigurationResource;
use Bitmovin\api\resource\codecConfigurations\TypeConfigurationResource;
use Bitmovin\api\util\ApiUrls;

class CodecConfigurationContainer
{
    /** @var H264VideoCodecConfigurationResource */
    private $videoH264;
    /** @var H265VideoCodecConfigurationResource */
    private $videoH265;
    /** @var AACAudioCodecConfigurationResource */
    private $audioAAC;
    /** @var TypeConfigurationResource */
    private $type;

    /**
     * CodecConfigurationContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->videoH264 = new H264VideoCodecConfigurationResource(
            ApiUrls::CODEC_CONFIGURATION_H264,
            H264VideoCodecConfiguration::class,
            $apiKey
        );

        $this->videoH265 = new H265VideoCodecConfigurationResource(
            ApiUrls::CODEC_CONFIGURATION_H265,
            H265VideoCodecConfiguration::class,
            $apiKey
        );

        $this->audioAAC = new AACAudioCodecConfigurationResource(
            ApiUrls::CODEC_CONFIGURATION_AAC,
            AACAudioCodecConfiguration::class,
            $apiKey
        );

        $this->type = new TypeConfigurationResource(ApiUrls::CODEC_CONFIGURATIONS_TYPE, CodecConfigType::class, $apiKey);
    }

    /**
     * @return H264VideoCodecConfigurationResource
     */
    public function videoH264()
    {
        return $this->videoH264;
    }

    /**
     * @return H265VideoCodecConfigurationResource
     */
    public function videoH265()
    {
        return $this->videoH265;
    }

    /**
     * @return AACAudioCodecConfigurationResource
     */
    public function audioAAC()
    {
        return $this->audioAAC;
    }

    /**
     * @return TypeConfigurationResource
     */
    public function type()
    {
        return $this->type;
    }
}