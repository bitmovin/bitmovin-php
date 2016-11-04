<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\FtpInput;

class FtpInputResource extends InputResource
{


    /**
     * @param FtpInput $input
     *
     * @return FtpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(FtpInput $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param FtpInput $input
     *
     * @return FtpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(FtpInput $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param FtpInput $input
     *
     * @return FtpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(FtpInput $input)
    {
        return parent::getInput($input);
    }

    /**
     * @return FtpInput[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listAllInputs();
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return FtpInput[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $inputId
     *
     * @return FtpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return FtpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}