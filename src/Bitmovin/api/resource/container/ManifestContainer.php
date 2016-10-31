<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\resource\manifest\DashManifestResource;
use Bitmovin\api\resource\manifest\HlsManifestResource;
use Bitmovin\api\util\ApiUrls;

class ManifestContainer
{
    /** @var DashManifestResource */
    private $dash;
    /** @var HlsManifestResource */
    private $hls;

    /**
     * OutputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->dash = new DashManifestResource(ApiUrls::MANIFEST_DASH, DashManifest::class, $apiKey);
        $this->hls = new HlsManifestResource(ApiUrls::MANIFEST_HLS, HlsManifest::class, $apiKey);
    }

    /**
     * @return DashManifestResource
     */
    public function dash()
    {
        return $this->dash;
    }

    /**
     * @return HlsManifestResource
     */
    public function hls()
    {
        return $this->hls;
    }

}