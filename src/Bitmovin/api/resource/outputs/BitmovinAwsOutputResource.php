<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\BitmovinAwsOutput;
use Bitmovin\api\util\Defaults;

class BitmovinAwsOutputResource extends BitmovinOutputResource
{

    /**
     * @param BitmovinAwsOutput $output
     *
     * @return BitmovinAwsOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(BitmovinAwsOutput $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     *
     * @return BitmovinAwsOutput[]
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
     * @return BitmovinAwsOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }
}