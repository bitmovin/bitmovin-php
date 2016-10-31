<?php

namespace Bitmovin\api\resource\codecConfigurations;

use Bitmovin\api\model\codecConfigurations\H265VideoCodecConfiguration;

class H265VideoCodecConfigurationResource extends CodecConfigurationResource
{


    /**
     * @param H265VideoCodecConfiguration $codecConfiguration
     *
     * @return H265VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(H265VideoCodecConfiguration $codecConfiguration)
    {
        return parent::createCodecConfiguration($codecConfiguration);
    }

    /**
     * @param H265VideoCodecConfiguration $codecConfiguration
     *
     * @return H265VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(H265VideoCodecConfiguration $codecConfiguration)
    {
        return parent::deleteCodecConfiguration($codecConfiguration);
    }

    /**
     * @param H265VideoCodecConfiguration $codecConfiguration
     *
     * @return H265VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(H265VideoCodecConfiguration $codecConfiguration)
    {
        return parent::getCodecConfiguration($codecConfiguration);
    }

    /**
     * @return H265VideoCodecConfiguration[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listAllCodecConfigurations();
    }

    /**
     * @param $codecConfigurationId
     *
     * @return H265VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($codecConfigurationId)
    {
        return parent::getCodecConfigurationById($codecConfigurationId);
    }

    /**
     * @param $codecConfigurationId
     *
     * @return H265VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($codecConfigurationId)
    {
        return parent::deleteCodecConfigurationById($codecConfigurationId);
    }
}