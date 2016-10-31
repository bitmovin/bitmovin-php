<?php

namespace Bitmovin\api\resource\encodings\streams\muxings;

use Bitmovin\api\model\encodings\muxing\AbstractMuxing;
use Bitmovin\api\resource\AbstractResource;

abstract class MuxingResource extends AbstractResource
{
    const LIST_NAME = 'muxings';

    /**
     * MuxingResource constructor.
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
     * @param AbstractMuxing $muxing
     *
     * @return AbstractMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function createMuxing(AbstractMuxing $muxing)
    {
        return $this->createResource($muxing);
    }

    /**
     * @param AbstractMuxing $muxing
     *
     * @return AbstractMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteMuxing(AbstractMuxing $muxing)
    {
        return $this->deleteMuxingById($muxing->getId());
    }

    /**
     * @param AbstractMuxing $muxing
     *
     * @return AbstractMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getMuxing(AbstractMuxing $muxing)
    {
        return $this->getMuxingById($muxing->getId());
    }

    /**
     * @return AbstractMuxing[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function listAllMuxings()
    {
        /** @var AbstractMuxing[] $muxings */
        $muxings = $this->listResource();

        return $muxings;
    }

    /**
     * @param $muxingId
     *
     * @return AbstractMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getMuxingById($muxingId)
    {
        /** @var AbstractMuxing $muxing */
        $muxing = $this->getResource($muxingId);

        return $muxing;
    }

    /**
     * @param $muxingId
     *
     * @return AbstractMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteMuxingById($muxingId)
    {
        /** @var AbstractMuxing $muxing */
        $muxing = $this->deleteResource($muxingId);

        return $muxing;
    }
}