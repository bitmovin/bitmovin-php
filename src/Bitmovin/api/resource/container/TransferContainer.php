<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\transfers\TransferEncoding;
use Bitmovin\api\resource\transfers\TransferEncodingResource;
use Bitmovin\api\util\ApiUrls;

class TransferContainer
{
    /**
     * @var TransferEncodingResource
     */
    private $encoding;

    /**
     * TransferContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->encoding = new TransferEncodingResource(ApiUrls::ENCODING_TRANSFERS_ENCODING, TransferEncoding::class, $apiKey);
    }

    /**
     * @return TransferEncodingResource
     */
    public function encoding()
    {
        return $this->encoding;
    }

}