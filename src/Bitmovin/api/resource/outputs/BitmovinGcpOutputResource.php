<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\GcsOutput;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\Defaults;

class BitmovinGcpOutputResource extends BitmovinOutputResource
{

    /**
     * @param GcsOutput $output
     *
     * @return GcsOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(GcsOutput $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     *
     * @return GcsOutput[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listPage($offset = Defaults::LIST_OFFSET, $limit = Defaults::LIST_LIMIT)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listResourcePage($offset, $limit);
    }

    /**
     * @param $outputId
     *
     * @return GcsOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }
}