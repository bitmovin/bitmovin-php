<?php

namespace Bitmovin\api\resource\encodings\streams\muxings\drm;

use Bitmovin\api\model\encodings\drms\CencDrm;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class Fmp4DrmContainer
{

    /**
     * @var CencDrmResource
     */
    private $cencDrm;

    /**
     * MuxingContainer constructor.
     *
     * @param Encoding   $encoding
     * @param FMP4Muxing $muxing
     * @param            $apiKey
     */
    public function __construct(Encoding $encoding, FMP4Muxing $muxing, $apiKey)
    {
        $routeReplacementMap = array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId(),
            ApiUrls::PH_MUXING_ID => $muxing->getId()
        );
        $baseUriCenc = RouteHelper::buildURI(ApiUrls::ENCODING_MUXINGS_FMP4_DRM_CENC, $routeReplacementMap);

        $this->cencDrm = new CencDrmResource($encoding, $muxing, $baseUriCenc, CencDrm::class, $apiKey);
    }

    /**
     * @return CencDrmResource
     */
    public function cencDrm()
    {
        return $this->cencDrm;
    }


}