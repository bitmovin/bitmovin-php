<?php

namespace Bitmovin\api\resource\codecConfigurations;

use Bitmovin\api\model\codecConfigurations\AC3AudioCodecConfiguration;

class AC3AudioCodecConfigurationResource extends CodecConfigurationResource
{


    /**
     * @param AC3AudioCodecConfiguration $codecConfiguration
     *
     * @return AC3AudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(AC3AudioCodecConfiguration $codecConfiguration)
    {
        return parent::createCodecConfiguration($codecConfiguration);
    }

    /**
     * @param AC3AudioCodecConfiguration $codecConfiguration
     *
     * @return AC3AudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(AC3AudioCodecConfiguration $codecConfiguration)
    {
        return parent::deleteCodecConfiguration($codecConfiguration);
    }

    /**
     * @param AC3AudioCodecConfiguration $codecConfiguration
     *
     * @return AC3AudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(AC3AudioCodecConfiguration $codecConfiguration)
    {
        return parent::getCodecConfiguration($codecConfiguration);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return AC3AudioCodecConfiguration[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $codecConfigurationId
     *
     * @return AC3AudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($codecConfigurationId)
    {
        return parent::getCodecConfigurationById($codecConfigurationId);
    }

    /**
     * @param $codecConfigurationId
     *
     * @return AC3AudioCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($codecConfigurationId)
    {
        return parent::deleteCodecConfigurationById($codecConfigurationId);
    }
}
