<?php


namespace Bitmovin\output;

class GcsOutput extends AbstractOutput
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
     * @var string GcsRegion enum
     */
    public $cloudRegion;

    /**
     * GcsOutput constructor.
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