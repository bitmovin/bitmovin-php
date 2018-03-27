<?php

namespace Bitmovin\api\resource\encodings\streams\inputAnalysis;


use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\streams\inputAnalysis\StreamInputAnalysis;
use Bitmovin\api\model\encodings\streams\inputAnalysis\StreamInputAnalysisDetails;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class InputStreamAnalysisDetailsResource extends AbstractResource
{

    const LIST_NAME = "items";

    /** @var  Stream */
    private $stream;

    /** @var Encoding */
    private $encoding;

    /**
     * StreamResource constructor.
     *
     * @param Encoding $encoding
     * @param Stream $stream
     * @param string $apiKey
     */
    public function __construct(Encoding $encoding, Stream $stream, $apiKey)
    {
        $this->stream = $stream;
        $this->encoding = $encoding;

        $baseUri = RouteHelper::buildURI(ApiUrls::ENCODING_STREAMS_INPUTS, array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId(),
            ApiUrls::PH_STREAM_ID => $stream->getId()
        ));
        parent::__construct($baseUri, StreamInputAnalysis::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @return StreamInputAnalysis[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get()
    {
        $routeReplacementMap = array(
            ApiUrls::PH_ENCODING_ID => $this->encoding->getId(),
            ApiUrls::PH_STREAM_ID => $this->stream->getId()
        );

        $baseUriEncoding = RouteHelper::buildURI(
            ApiUrls::ENCODING_STREAMS_INPUTS,
            $routeReplacementMap
        );

        $response = $this->getRequest($baseUriEncoding);
        return $this->buildResourcesFromArrayResponse($response);
    }
}