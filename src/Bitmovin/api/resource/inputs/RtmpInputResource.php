<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\RtmpInput;

class RtmpInputResource extends InputResource
{

    /**
     * @param RtmpInput $input
     *
     * @return RtmpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(RtmpInput $input)
    {
        return parent::createInput($input);
    }

    /**
     * @param RtmpInput $input
     *
     * @return RtmpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(RtmpInput $input)
    {
        return parent::deleteInput($input);
    }

    /**
     * @param RtmpInput $input
     *
     * @return RtmpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(RtmpInput $input)
    {
        return parent::getInput($input);
    }

    /**
     * @return RtmpInput[]
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
     * @return RtmpInput[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $inputId
     *
     * @return RtmpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($inputId)
    {
        return parent::getInputById($inputId);
    }

    /**
     * @param $inputId
     *
     * @return RtmpInput
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($inputId)
    {
        return parent::deleteInputById($inputId);
    }
}