<?php

namespace Bitmovin\api\resource\outputs;

use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\resource\AbstractResource;

abstract class OutputResource extends AbstractResource
{
    const LIST_NAME = 'outputs';

    /**
     * OutputResource constructor.
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
     * @param Output $output
     *
     * @return Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function createOutput(Output $output)
    {
        return $this->createResource($output);
    }

    /**
     * @param Output $output
     *
     * @return Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteOutput(Output $output)
    {
        return $this->deleteOutputById($output->getId());
    }

    /**
     * @param Output $output
     *
     * @return Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getOutput(Output $output)
    {
        return $this->getOutputById($output->getId());
    }

    /**
     * @param $outputId
     *
     * @return Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getOutputById($outputId)
    {
        /** @var Output $output */
        $output = $this->getResource($outputId);

        return $output;
    }

    /**
     * @param $outputId
     *
     * @return Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteOutputById($outputId)
    {
        /** @var Output $output */
        $output = $this->deleteResource($outputId);

        return $output;
    }
}