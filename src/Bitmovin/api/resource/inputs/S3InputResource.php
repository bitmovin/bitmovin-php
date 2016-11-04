<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\S3Input;

class S3InputResource extends InputResource
{

    /**
     * @param S3Input $input
     *
     * @return S3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(S3Input $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param S3Input $input
     *
     * @return S3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(S3Input $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param S3Input $input
     *
     * @return S3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(S3Input $input)
    {
        return parent::getInput($input);
    }

    /**
     * @return S3Input[]
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
     * @return S3Input[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $inputId
     *
     * @return S3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return S3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}