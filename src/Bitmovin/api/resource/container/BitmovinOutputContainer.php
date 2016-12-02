<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\outputs\GcsOutput;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\resource\outputs\BitmovinGcpOutputResource;
use Bitmovin\api\resource\outputs\BitmovinAwsOutputResource;
use Bitmovin\api\util\ApiUrls;

class BitmovinOutputContainer
{
    /** @var BitmovinAwsOutputResource */
    private $aws;
    /** @var  BitmovinGcpOutputResource */
    private $gcp;

    /**
     * OutputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->aws = new BitmovinAwsOutputResource(ApiUrls::OUTPUT_BITMOVIN_AWS, S3Output::class, $apiKey);
        $this->gcp = new BitmovinGcpOutputResource(ApiUrls::OUTPUT_BITMOVIN_GCP, GcsOutput::class, $apiKey);
    }

    /**
     * @return BitmovinAwsOutputResource
     */
    public function aws()
    {
        return $this->aws;
    }

    /**
     * @return BitmovinGcpOutputResource
     */
    public function gcp()
    {
        return $this->gcp;
    }

}