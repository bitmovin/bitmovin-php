<?php

namespace Bitmovin\api\resource\encodings\streams\muxings;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\muxing\WebmMuxing;

class WebmMuxingResource extends MuxingResource
{
    /** @var Encoding */
    private $encoding;

    /**
     * WebmMuxingResource constructor.
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
     * @param WebmMuxing $webmMuxing
     *
     * @return WebmMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(WebmMuxing $webmMuxing)
    {
        return parent::createMuxing($webmMuxing);
    }

    /**
     * @param WebmMuxing $webmMuxing
     *
     * @return WebmMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(WebmMuxing $webmMuxing)
    {
        return parent::deleteMuxing($webmMuxing);
    }

    /**
     * @param WebmMuxing $webmMuxing
     *
     * @return WebmMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(WebmMuxing $webmMuxing)
    {
        return parent::getMuxing($webmMuxing);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return WebmMuxing[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $webmMuxingId
     *
     * @return WebmMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($webmMuxingId)
    {
        return parent::getMuxingById($webmMuxingId);
    }

    /**
     * @param $webmMuxingId
     *
     * @return WebmMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($webmMuxingId)
    {
        return parent::deleteMuxingById($webmMuxingId);
    }
}