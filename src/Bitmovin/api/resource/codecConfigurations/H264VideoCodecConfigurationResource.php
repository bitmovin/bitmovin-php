<?php

namespace Bitmovin\api\resource\codecConfigurations;

use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;

class H264VideoCodecConfigurationResource extends CodecConfigurationResource
{


    /**
     * @param H264VideoCodecConfiguration $codecConfiguration
     *
     * @return H264VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(H264VideoCodecConfiguration $codecConfiguration)
    {
        return parent::createCodecConfiguration($codecConfiguration);
    }

    /**
     * @param H264VideoCodecConfiguration $codecConfiguration
     *
     * @return H264VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(H264VideoCodecConfiguration $codecConfiguration)
    {
        return parent::deleteCodecConfiguration($codecConfiguration);
    }

    /**
     * @param H264VideoCodecConfiguration $codecConfiguration
     *
     * @return H264VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(H264VideoCodecConfiguration $codecConfiguration)
    {
        return parent::getCodecConfiguration($codecConfiguration);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return H264VideoCodecConfiguration[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $codecConfigurationId
     *
     * @return H264VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($codecConfigurationId)
    {
        return parent::getCodecConfigurationById($codecConfigurationId);
    }

    /**
     * @param $codecConfigurationId
     *
     * @return H264VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($codecConfigurationId)
    {
        return parent::deleteCodecConfigurationById($codecConfigurationId);
    }
}