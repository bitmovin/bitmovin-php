<?php

namespace Bitmovin\api\resource\encodings\streams\filters;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\resource\encodings\streams\muxings\Fmp4MuxingResource;
use Bitmovin\api\resource\encodings\streams\muxings\Mp4MuxingResource;
use Bitmovin\api\resource\encodings\streams\muxings\TsMuxingResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class FilterContainer
{
    /** @var Mp4MuxingResource */
    private $mp4Muxing;
    /** @var Fmp4MuxingResource */
    private $fmp4Muxing;
    /** @var  TsMuxingResource */
    private $tsMuxing;

    /**
     * MuxingContainer constructor.
     *
     * @param Encoding $encoding
     * @param Stream   $stream
     * @param          $apiKey
     */
    public function __construct(Encoding $encoding, Stream $stream, $apiKey)
    {
        $routeReplacementMap = array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId(),
            ApiUrls::PH_STREAM_ID => $stream->getId()
        );
        $baseUriMp4 = RouteHelper::buildURI(ApiUrls::ENCODING_MUXINGS_MP4, $routeReplacementMap);
        $baseUriFmp4 = RouteHelper::buildURI(ApiUrls::ENCODING_MUXINGS_FMP4, $routeReplacementMap);
        $baseUriTs = RouteHelper::buildURI(ApiUrls::ENCODING_MUXINGS_TS, $routeReplacementMap);

        $this->mp4Muxing = new Mp4MuxingResource($baseUriMp4, MP4Muxing::class, $apiKey);
        $this->fmp4Muxing = new Fmp4MuxingResource($encoding, $stream, $baseUriFmp4, FMP4Muxing::class, $apiKey);
        $this->tsMuxing = new TsMuxingResource($baseUriTs, TSMuxing::class, $apiKey);
    }

    /**
     * @return Mp4MuxingResource
     */
    public function mp4Muxing()
    {
        return $this->mp4Muxing;
    }

    /**
     * @return Fmp4MuxingResource
     */
    public function fmp4Muxing()
    {
        return $this->fmp4Muxing;
    }

    /**
     * @return TsMuxingResource
     */
    public function tsMuxing()
    {
        return $this->tsMuxing;
    }
}