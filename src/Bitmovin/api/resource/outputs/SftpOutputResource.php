<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\SftpOutput;

class SftpOutputResource extends OutputResource
{

    /**
     * @param SftpOutput $output
     *
     * @return SftpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(SftpOutput $output)
    {
        return parent::createOutput($output);
    }

    /**
     * @param SftpOutput $output
     *
     * @return SftpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(SftpOutput $output)
    {
        return parent::deleteOutput($output);
    }

    /**
     * @param SftpOutput $output
     *
     * @return SftpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(SftpOutput $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return SftpOutput[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $outputId
     *
     * @return SftpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }

    /**
     * @param $outputId
     *
     * @return SftpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($outputId)
    {
        return parent::deleteOutputById($outputId);
    }
}