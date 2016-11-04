<?php

namespace Bitmovin\api\resource\encodings\streams\muxings;

use Bitmovin\api\model\encodings\muxing\MP4Muxing;

class Mp4MuxingResource extends MuxingResource
{

    /**
     * Fmp4MuxingResource constructor.
     *
     * @param string $baseUri
     * @param string $modelClassName
     * @param string $apiKey
     */
    public function __construct($baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, $apiKey);
    }

    /**
     * @param MP4Muxing $mp4Muxing
     *
     * @return MP4Muxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(MP4Muxing $mp4Muxing)
    {
        return parent::createMuxing($mp4Muxing);
    }

    /**
     * @param MP4Muxing $mp4Muxing
     *
     * @return MP4Muxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(MP4Muxing $mp4Muxing)
    {
        return parent::deleteMuxing($mp4Muxing);
    }

    /**
     * @param MP4Muxing $mp4Muxing
     *
     * @return MP4Muxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(MP4Muxing $mp4Muxing)
    {
        return parent::getMuxing($mp4Muxing);
    }

    /**
     * @return MP4Muxing[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listAllMuxings();
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return MP4Muxing[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $mp4MuxingId
     *
     * @return MP4Muxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($mp4MuxingId)
    {
        return parent::getMuxingById($mp4MuxingId);
    }

    /**
     * @param $mp4MuxingId
     *
     * @return MP4Muxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($mp4MuxingId)
    {
        return parent::deleteMuxingById($mp4MuxingId);
    }
}