<?php

namespace Bitmovin\api\resource\encodings\streams\muxings\drm;

use Bitmovin\api\model\encodings\drms\PlayReadyDrm;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class MP4DrmContainer
{

    /**
     * @var PlayReadyDrmResource
     */
    private $playReadyDrm;

    /**
     * MuxingContainer constructor.
     *
     * @param Encoding   $encoding
     * @param MP4Muxing $muxing
     * @param            $apiKey
     */
    public function __construct(Encoding $encoding, MP4Muxing $muxing, $apiKey)
    {
        $routeReplacementMap = array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId(),
            ApiUrls::PH_MUXING_ID => $muxing->getId()
        );
        $baseUriCenc = RouteHelper::buildURI(ApiUrls::ENCODING_MUXINGS_MP4_DRM_PLAYREADY, $routeReplacementMap);

        $this->playReadyDrm = new PlayReadyDrmResource($encoding, $muxing, $baseUriCenc, PlayReadyDrm::class, $apiKey);
    }

    /**
     * @return PlayReadyDrmResource
     */
    public function playReadyDrm()
    {
        return $this->playReadyDrm;
    }


}