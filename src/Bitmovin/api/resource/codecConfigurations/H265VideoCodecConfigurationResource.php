<?php

namespace Bitmovin\api\resource\codecConfigurations;

use Bitmovin\api\model\codecConfigurations\H265VideoCodecConfiguration;
use Bitmovin\api\util\Defaults;

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
     * @param int $offset
     * @param int $limit
     * @return \Bitmovin\api\model\AbstractModel[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listPage($offset = Defaults::LIST_OFFSET, $limit = Defaults::LIST_LIMIT)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
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