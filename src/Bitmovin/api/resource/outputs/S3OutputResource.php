<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\S3Output;

class S3OutputResource extends OutputResource
{

    /**
     * @param S3Output $output
     *
     * @return S3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(S3Output $output)
    {
        return parent::createOutput($output);
    }

    /**
     * @param S3Output $output
     *
     * @return S3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(S3Output $output)
    {
        return parent::deleteOutput($output);
    }

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
     * @return S3Output[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
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

    /**
     * @param $outputId
     *
     * @return S3Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($outputId)
    {
        return parent::deleteOutputById($outputId);
    }
}