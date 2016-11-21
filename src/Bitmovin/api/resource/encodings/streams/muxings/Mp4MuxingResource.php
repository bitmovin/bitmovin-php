<?php

namespace Bitmovin\api\resource\encodings\streams\muxings;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\resource\encodings\streams\muxings\drm\MP4DrmContainer;

class Mp4MuxingResource extends MuxingResource
{
    /** @var Encoding */
    private $encoding;

    /**
     * Fmp4MuxingResource constructor.
     *
     * @param Encoding $encoding
     * @param string   $baseUri
     * @param string   $modelClassName
     * @param string   $apiKey
     */
    public function __construct(Encoding $encoding, $baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, $apiKey);
        $this->encoding = $encoding;
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

    /**
     * @param MP4Muxing $muxing
     * @return MP4DrmContainer
     */
    public function drm(MP4Muxing $muxing)
    {
        return new MP4DrmContainer($this->encoding, $muxing, $this->getApiKey());
    }

}