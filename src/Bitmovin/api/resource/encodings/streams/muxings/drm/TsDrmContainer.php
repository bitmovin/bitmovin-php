<?php

namespace Bitmovin\api\resource\encodings\streams\muxings\drm;

use Bitmovin\api\model\encodings\drms\FairPlayDrm;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class TsDrmContainer
{

    /**
     * @var FairPlayDrmResource
     */
    private $fairplayDrm;

    /**
     * MuxingContainer constructor.
     *
     * @param Encoding   $encoding
     * @param TSMuxing   $muxing
     * @param            $apiKey
     */
    public function __construct(Encoding $encoding, TSMuxing $muxing, $apiKey)
    {
        $routeReplacementMap = array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId(),
            ApiUrls::PH_MUXING_ID   => $muxing->getId()
        );
        $baseUriFairPlayDrm = RouteHelper::buildURI(ApiUrls::ENCODING_MUXINGS_TS_DRM_FAIRPLAY, $routeReplacementMap);

        $this->fairplayDrm = new FairPlayDrmResource($encoding, $muxing, $baseUriFairPlayDrm, FairPlayDrm::class, $apiKey);
    }

    /**
     * @return FairPlayDrmResource
     */
    public function fairplay()
    {
        return $this->fairplayDrm;
    }


}