<?php


namespace Bitmovin\input;

class S3Input extends AbstractInput
{
    /**
     * @var string
     */
    public $accessKey;

    /**
     * @var string
     */
    public $secretKey;

    /**
     * @var string
     */
    public $bucket;

    /**
     * @var string
     */
    public $prefix;

    /**
     * @var string AwsRegion enum
     */
    public $cloudRegion;

    /**
     * S3Input constructor.
     * @param string $accessKey 20 character alphanumeric string
     * @param string $secretKey 40 character Base64-encoded string
     * @param string $bucket
     * @param string $prefix
     */
    public function __construct($accessKey, $secretKey, $bucket, $prefix)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
    }

}