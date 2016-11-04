<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\HttpInput;

class HttpInputResource extends InputResource
{

    /**
     * @param HttpInput $input
     *
     * @return HttpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(HttpInput $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param HttpInput $input
     *
     * @return HttpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(HttpInput $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param HttpInput $input
     *
     * @return HttpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(HttpInput $input)
    {
        return parent::getInput($input);
    }

    /**
     * @return HttpInput[]
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
     * @return HttpInput[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $inputId
     *
     * @return HttpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return HttpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}