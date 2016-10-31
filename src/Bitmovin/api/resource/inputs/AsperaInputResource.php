<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\AsperaInput;

class AsperaInputResource extends InputResource
{

    /**
     * @param AsperaInput $input
     *
     * @return AsperaInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(AsperaInput $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param AsperaInput $input
     *
     * @return AsperaInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(AsperaInput $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param AsperaInput $input
     *
     * @return AsperaInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(AsperaInput $input)
    {
        return parent::getInput($input);
    }

    /**
     * @return AsperaInput[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listAllInputs();
    }

    /**
     * @param $inputId
     *
     * @return AsperaInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return AsperaInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}