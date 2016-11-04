<?php

namespace Bitmovin\api\resource\codecConfigurations;

use Bitmovin\api\model\codecConfigurations\CodecConfiguration;
use Bitmovin\api\resource\AbstractResource;

abstract class CodecConfigurationResource extends AbstractResource
{
    const LIST_NAME = 'codecConfigurations';

    /**
     * CodecConfigurationResource constructor.
     *
     * @param string $baseUri
     * @param string $modelClassName
     * @param string $apiKey
     */
    public function __construct($baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, static::LIST_NAME, $apiKey);
    }

    /**
     * @param CodecConfiguration $codecConfiguration
     *
     * @return CodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function createCodecConfiguration(CodecConfiguration $codecConfiguration)
    {
        return $this->createResource($codecConfiguration);
    }

    /**
     * @param CodecConfiguration $codecConfiguration
     *
     * @return CodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteCodecConfiguration(CodecConfiguration $codecConfiguration)
    {
        return $this->deleteCodecConfigurationById($codecConfiguration->getId());
    }

    /**
     * @param CodecConfiguration $codecConfiguration
     *
     * @return CodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getCodecConfiguration(CodecConfiguration $codecConfiguration)
    {
        return $this->getCodecConfigurationById($codecConfiguration->getId());
    }

    /**
     * @param $codecConfigurationId
     *
     * @return CodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getCodecConfigurationById($codecConfigurationId)
    {
        /** @var CodecConfiguration $codecConfiguration */
        $codecConfiguration = $this->getResource($codecConfigurationId);

        return $codecConfiguration;
    }

    /**
     * @param $codecConfigurationId
     *
     * @return CodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteCodecConfigurationById($codecConfigurationId)
    {
        /** @var CodecConfiguration $codecConfiguration */
        $codecConfiguration = $this->deleteResource($codecConfigurationId);

        return $codecConfiguration;
    }
}