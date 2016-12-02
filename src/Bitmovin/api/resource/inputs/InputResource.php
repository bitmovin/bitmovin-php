<?php

namespace Bitmovin\api\resource\inputs;

use Bitmovin\api\model\inputs\Input;
use Bitmovin\api\resource\AbstractResource;

abstract class InputResource extends AbstractResource
{
    const LIST_NAME = 'items';

    /**
     * InputResource constructor.
     *
     * @param string $baseUri
     * @param string $modelClassName
     * @param string $apiKey
     */
    public function __construct($baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, static::LIST_NAME, $apiKey);
    }

    /**
     * @param Input $input
     *
     * @return Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function createInput(Input $input)
    {
        return $this->createResource($input);
    }

    /**
     * @param Input $input
     *
     * @return Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteInput(Input $input)
    {
        return $this->deleteInputById($input->getId());
    }

    /**
     * @param Input $input
     *
     * @return Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getInput(Input $input)
    {
        return $this->getInputById($input->getId());
    }

    /**
     * @param $inputId
     *
     * @return Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getInputById($inputId)
    {
        /** @var Input $input */
        $input = $this->getResource($inputId);

        return $input;
    }

    /**
     * @param $inputId
     *
     * @return Input
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteInputById($inputId)
    {
        /** @var Input $input */
        $input = $this->deleteResource($inputId);

        return $input;
    }
}