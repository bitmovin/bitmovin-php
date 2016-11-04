<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\FtpOutput;

class FtpOutputResource extends OutputResource
{

    /**
     * @param FtpOutput $output
     *
     * @return FtpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(FtpOutput $output)
    {
        return parent::createOutput($output);
    }

    /**
     * @param FtpOutput $output
     *
     * @return FtpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(FtpOutput $output)
    {
        return parent::deleteOutput($output);
    }

    /**
     * @param FtpOutput $output
     *
     * @return FtpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(FtpOutput $output)
    {
        return parent::getOutput($output);
    }

    /**
     * @return FtpOutput[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listAllOutputs();
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return FtpOutput[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $outputId
     *
     * @return FtpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($outputId)
    {
        return parent::getOutputById($outputId);
    }

    /**
     * @param $outputId
     *
     * @return FtpOutput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($outputId)
    {
        return parent::deleteOutputById($outputId);
    }
}