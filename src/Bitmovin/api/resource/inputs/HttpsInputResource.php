<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\HttpsInput;

class HttpsInputResource extends InputResource
{

    /**
     * @param HttpsInput $input
     *
     * @return HttpsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(HttpsInput $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param HttpsInput $input
     *
     * @return HttpsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(HttpsInput $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param HttpsInput $input
     *
     * @return HttpsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(HttpsInput $input)
    {
        return parent::getInput($input);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return HttpsInput[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $inputId
     *
     * @return HttpsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return HttpsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}