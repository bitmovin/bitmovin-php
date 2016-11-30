<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\outputs\GcsOutput;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\resource\outputs\BitmovinGcsOutputResource;
use Bitmovin\api\resource\outputs\BitmovinS3OutputResource;
use Bitmovin\api\util\ApiUrls;

class BitmovinOutputContainer
{
    /** @var BitmovinS3OutputResource */
    private $s3;
    /** @var  BitmovinGcsOutputResource */
    private $gcs;

    /**
     * OutputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->s3 = new BitmovinS3OutputResource(ApiUrls::OUTPUT_BITMOVIN_S3, S3Output::class, $apiKey);
        $this->gcs = new BitmovinGcsOutputResource(ApiUrls::OUTPUT_BITMOVIN_GCS, GcsOutput::class, $apiKey);
    }

    /**
     * @return BitmovinS3OutputResource
     */
    public function s3()
    {
        return $this->s3;
    }

    /**
     * @return BitmovinGcsOutputResource
     */
    public function gcs()
    {
        return $this->gcs;
    }

}