<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\AzureOutput;

class AzureOutputResource extends OutputResource
{

    /**
     * @param AzureOutput $output
     *
     * @return AzureOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(AzureOutput $output)
    {
        return parent::createOutput($output);
    }

    /**
     * @param AzureOutput $output
     *
     * @return AzureOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(AzureOutput $output)
    {
        return parent::deleteOutput($output);
    }

    /**
     * @param AzureOutput $output
     *
     * @return AzureOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(AzureOutput $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @return AzureOutput[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listAllOutputs();
    }

    /**
     * @param $outputId
     *
     * @return AzureOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }

    /**
     * @param $outputId
     *
     * @return AzureOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($outputId)
    {
        return parent::deleteOutputById($outputId);
    }
}