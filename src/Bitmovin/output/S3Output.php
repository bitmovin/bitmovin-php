<?php


namespace Bitmovin\output;

class S3Output extends AbstractOutput
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
    public $prefix = '';

    /**
     * @var string AwsRegion enum
     */
    public $cloudRegion;

    /**
     * S3Output constructor.
     * @param string $accessKey
     * @param string $secretKey
     * @param string $bucket
     * @param string $prefix
     */
    public function __construct($accessKey, $secretKey, $bucket, $prefix = '')
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
    }

}