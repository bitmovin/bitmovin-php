<?php


namespace Bitmovin\api\model\outputs;

use JMS\Serializer\Annotation as JMS;

class GcsOutput extends Output
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
     * @var string GcsRegion enum
     */
    private $cloudRegion;

    /**
     * GcsOutput constructor.
     * @param string $bucketName
     * @param string $accessKey
     * @param string $secretKey
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