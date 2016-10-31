<?php

namespace Bitmovin\api\model\inputs;

use JMS\Serializer\Annotation as JMS;

class S3Input extends Input
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $bucketName;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $accessKey;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $secretKey;

    /**
     * @JMS\Type("string")
     * @var string S3Region enum
     */
    private $cloudRegion;

    /**
     * S3Input constructor.
     *
     * @param string $bucketName Amazon S3 bucket name
     * @param string $accessKey  Amazon Access Key
     * @param string $secretKey  Amazon Secret Key
     */
    public function __construct($bucketName, $accessKey, $secretKey)
    {
        $this->bucketName = $bucketName;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    /**
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }

    /**
     * @param string $bucketName
     */
    public function setBucketName($bucketName)
    {
        $this->bucketName = $bucketName;
    }

    /**
     * @return string
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * @param string $accessKey
     */
    public function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @return string
     */
    public function getCloudRegion()
    {
        return $this->cloudRegion;
    }

    /**
     * @param string $cloudRegion
     */
    public function setCloudRegion($cloudRegion)
    {
        $this->cloudRegion = $cloudRegion;
    }

}