<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\SftpInput;

class SftpInputResource extends InputResource
{


    /**
     * @param SftpInput $input
     *
     * @return SftpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(SftpInput $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param SftpInput $input
     *
     * @return SftpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(SftpInput $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param SftpInput $input
     *
     * @return SftpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(SftpInput $input)
    {
        return parent::getInput($input);
    }

    /**
     * @return SftpInput[]
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
     * @return SftpInput[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $inputId
     *
     * @return SftpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return SftpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}