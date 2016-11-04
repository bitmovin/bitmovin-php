<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\GcsInput;

class GcsInputResource extends InputResource
{

    /**
     * @param GcsInput $input
     *
     * @return GcsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(GcsInput $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param GcsInput $input
     *
     * @return GcsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(GcsInput $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param GcsInput $input
     *
     * @return GcsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(GcsInput $input)
    {
        return parent::getInput($input);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return GcsInput[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $inputId
     *
     * @return GcsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return GcsInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}