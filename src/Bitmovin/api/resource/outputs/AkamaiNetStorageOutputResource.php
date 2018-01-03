<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\AkamaiNetStorageOutput;

class AkamaiNetStorageOutputResource extends OutputResource
{

    /**
     * @param AkamaiNetStorageOutput $output
     *
     * @return AkamaiNetStorageOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(AkamaiNetStorageOutput $output)
    {
        return parent::createOutput($output);
    }

    /**
     * @param AkamaiNetStorageOutput $output
     *
     * @return AkamaiNetStorageOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(AkamaiNetStorageOutput $output)
    {
        return parent::deleteOutput($output);
    }

    /**
     * @param AkamaiNetStorageOutput $output
     *
     * @return AkamaiNetStorageOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(AkamaiNetStorageOutput $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return AkamaiNetStorageOutput[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $outputId
     *
     * @return AkamaiNetStorageOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }

    /**
     * @param $outputId
     *
     * @return AkamaiNetStorageOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($outputId)
    {
        return parent::deleteOutputById($outputId);
    }
}