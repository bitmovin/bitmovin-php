<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\transfers\TransferEncoding;
use Bitmovin\api\model\transfers\TransferManifest;
use Bitmovin\api\resource\transfers\TransferEncodingResource;
use Bitmovin\api\resource\transfers\TransferManifestResource;
use Bitmovin\api\util\ApiUrls;

class TransferContainer
{
    /**
     * @var TransferEncodingResource
     */
    private $encoding;

    /**
     * @var TransferManifestResource
     */
    private $manifest;

    /**
     * TransferContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->encoding = new TransferEncodingResource(ApiUrls::ENCODING_TRANSFERS_ENCODING, TransferEncoding::class, $apiKey);
        $this->manifest = new TransferManifestResource(ApiUrls::ENCODING_TRANSFERS_MANIFEST, TransferManifest::class, $apiKey);
    }

    /**
     * @return TransferEncodingResource
     */
    public function encoding()
    {
        return $this->encoding;
    }

    /**
     * @return TransferManifestResource
     */
    public function manifest()
    {
        return $this->manifest;
    }

}