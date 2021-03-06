<?php

namespace Bitmovin\api\resource;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\filters\AbstractFilter;
use Bitmovin\api\resource\encodings\streams\inputAnalysis\InputStreamAnalysisDetailsResource;
use Bitmovin\api\resource\encodings\streams\sprites\SpriteResource;
use Bitmovin\api\resource\encodings\streams\thumbnails\ThumbnailResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class StreamResource extends AbstractResource
{
    const LIST_NAME = "items";

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
     * @return ThumbnailResource
     */
    public function thumbnails(Stream $stream)
    {
        return new ThumbnailResource($this->getEncoding(), $stream, parent::getApiKey());
    }

    /***
     * @param Stream $stream
     * @param AbstractFilter[]       $abstractFilters
     * @internal param string $filterId
     * @internal param int $position
     */
    public function addFilter(Stream $stream, $abstractFilters)
    {
        $routeReplacementMap = array(ApiUrls::PH_ENCODING_ID => $this->encoding->getId(), ApiUrls::PH_STREAM_ID => $stream->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_STREAMS_FILTERS, $routeReplacementMap);

        $array = array();
        $position = 0;
        foreach ($abstractFilters as $abstractFilter)
        {
            $array[] = array(
                'id' => $abstractFilter->getId(),
                'position' => $position
            );
            $position++;
        }

        parent::postRequest($baseUriEncoding, json_encode($array));
    }

    /**
     * @param Stream $stream
     * @return SpriteResource
     */
    public function sprites(Stream $stream)
    {
        return new SpriteResource($this->getEncoding(), $stream, parent::getApiKey());
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
     * @param Stream $stream
     * @return InputStreamAnalysisDetailsResource
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function streamInputAnalysis($stream)
    {
        return new InputStreamAnalysisDetailsResource($this->getEncoding(), $stream, parent::getApiKey());
    }

    /**
     * @return Encoding
     */
    protected function getEncoding()
    {
        return $this->encoding;
    }
}