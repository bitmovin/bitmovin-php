<?php

namespace Bitmovin\api\resource\codecConfigurations;

use Bitmovin\api\enum\codecConfigurations\CodecConfigType;
use Bitmovin\api\model\codecConfigurations\CodecConfiguration;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class TypeConfigurationResource extends CodecConfigurationResource
{

    /**
     * @param CodecConfiguration $codecConfiguration
     * @return CodecConfigType
     */
    public function getType(CodecConfiguration $codecConfiguration)
    {
        $routeReplacementMap = array(ApiUrls::PH_CONFIGURATION_ID => $codecConfiguration->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::CODEC_CONFIGURATIONS_TYPE, $routeReplacementMap);

        return parent::getResourceObject($baseUriEncoding, CodecConfigType::class);
    }

    /**
     * @param string $codecConfigurationId
     * @return CodecConfigType
     * @internal param string $codecConfiguration
     */
    public function getTypeById($codecConfigurationId)
    {
        $routeReplacementMap = array(ApiUrls::PH_CONFIGURATION_ID => $codecConfigurationId);
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::CODEC_CONFIGURATIONS_TYPE, $routeReplacementMap);

        return parent::getResourceObject($baseUriEncoding, CodecConfigType::class);
    }

}