<?php

namespace Bitmovin\api\resource\codecConfigurations;

use Bitmovin\api\model\codecConfigurations\VP9VideoCodecConfiguration;
use Bitmovin\api\util\Defaults;

class VP9VideoCodecConfigurationResource extends CodecConfigurationResource
{


    /**
     * @param VP9VideoCodecConfiguration $codecConfiguration
     *
     * @return VP9VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(VP9VideoCodecConfiguration $codecConfiguration)
    {
        return parent::createCodecConfiguration($codecConfiguration);
    }

    /**
     * @param VP9VideoCodecConfiguration $codecConfiguration
     *
     * @return VP9VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(VP9VideoCodecConfiguration $codecConfiguration)
    {
        return parent::deleteCodecConfiguration($codecConfiguration);
    }

    /**
     * @param VP9VideoCodecConfiguration $codecConfiguration
     *
     * @return VP9VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(VP9VideoCodecConfiguration $codecConfiguration)
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
     * @return VP9VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($codecConfigurationId)
    {
        return parent::getCodecConfigurationById($codecConfigurationId);
    }

    /**
     * @param $codecConfigurationId
     *
     * @return VP9VideoCodecConfiguration
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($codecConfigurationId)
    {
        return parent::deleteCodecConfigurationById($codecConfigurationId);
    }
}