<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\BitmovinGcpOutput;
use Bitmovin\api\util\Defaults;

class BitmovinGcpOutputResource extends BitmovinOutputResource
{

    /**
     * @param BitmovinGcpOutput $output
     *
     * @return BitmovinGcpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(BitmovinGcpOutput $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     *
     * @return BitmovinGcpOutput[]
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
     * @return BitmovinGcpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }
}