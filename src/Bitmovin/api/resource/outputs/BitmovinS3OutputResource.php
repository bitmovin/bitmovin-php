<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\util\Defaults;

class BitmovinS3OutputResource extends BitmovinOutputResource
{

    /**
     * @param S3Output $output
     *
     * @return S3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(S3Output $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     *
     * @return S3Output[]
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
     * @return S3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }
}