<?php

namespace Bitmovin\api\resource\inputs;


use Bitmovin\api\model\inputs\GenericS3Input;
use Bitmovin\api\resource\AbstractResource;

class GenericS3InputResource extends InputResource
{
    /**
     * @param GenericS3Input $input
     *
     * @return GenericS3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(GenericS3Input $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param GenericS3Input $input
     *
     * @return GenericS3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(GenericS3Input $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param GenericS3Input $input
     *
     * @return GenericS3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(GenericS3Input $input)
    {
        return parent::getInput($input);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return GenericS3Input[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $inputId
     *
     * @return GenericS3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return GenericS3Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}