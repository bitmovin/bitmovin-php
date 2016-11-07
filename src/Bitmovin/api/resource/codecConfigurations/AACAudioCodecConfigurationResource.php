<?php

namespace Bitmovin\api\resource\codecConfigurations;

use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;

class AACAudioCodecConfigurationResource extends CodecConfigurationResource
{


    /**
     * @param AACAudioCodecConfiguration $codecConfiguration
     *
     * @return AACAudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(AACAudioCodecConfiguration $codecConfiguration)
    {
        return parent::createCodecConfiguration($codecConfiguration);
    }

    /**
     * @param AACAudioCodecConfiguration $codecConfiguration
     *
     * @return AACAudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(AACAudioCodecConfiguration $codecConfiguration)
    {
        return parent::deleteCodecConfiguration($codecConfiguration);
    }

    /**
     * @param AACAudioCodecConfiguration $codecConfiguration
     *
     * @return AACAudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(AACAudioCodecConfiguration $codecConfiguration)
    {
        return parent::getCodecConfiguration($codecConfiguration);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return AACAudioCodecConfiguration[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $codecConfigurationId
     *
     * @return AACAudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($codecConfigurationId)
    {
        return parent::getCodecConfigurationById($codecConfigurationId);
    }

    /**
     * @param $codecConfigurationId
     *
     * @return AACAudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($codecConfigurationId)
    {
        return parent::deleteCodecConfigurationById($codecConfigurationId);
    }
}