<?php

namespace Bitmovin\api\resource;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class StreamResource extends AbstractResource
{
    const LIST_NAME = "streams";

    /** @var  Encoding */
    private $encoding;

    /**
     * StreamResource constructor.
     *
     * @param Encoding $encoding
     * @param string   $apiKey
     */
    public function __construct(Encoding $encoding, $apiKey)
    {
        $this->encoding = $encoding;

        $baseUri = RouteHelper::buildURI(ApiUrls::ENCODING_STREAMS, array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId()
        ));
        parent::__construct($baseUri, Stream::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param Stream $stream
     *
     * @return Stream
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(Stream $stream)
    {
        return $this->createResource($stream);
    }

    /**
     * @param Stream $stream
     *
     * @return Stream
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(Stream $stream)
    {
        return $this->deleteById($stream->getId());
    }

    /**
     * @param Stream $stream
     *
     * @return Stream
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(Stream $stream)
    {
        return $this->getById($stream->getId());
    }

    /**
     * @return Stream[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @var Stream[] $streams */
        $streams = $this->listResource();

        return $streams;
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return Stream[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $streamId
     *
     * @return Stream
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($streamId)
    {
        /** @var Stream $stream */
        $stream = $this->getResource($streamId);

        return $stream;
    }

    /**
     * @param $streamId
     *
     * @return Stream
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($streamId)
    {
        /** @var Stream $stream */
        $stream = $this->deleteResource($streamId);

        return $stream;
    }

    /**
     * @return Encoding
     */
    protected function getEncoding()
    {
        return $this->encoding;
    }
}