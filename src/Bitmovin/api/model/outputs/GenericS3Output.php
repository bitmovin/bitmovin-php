<?php

namespace Bitmovin\api\model\outputs;

use JMS\Serializer\Annotation as JMS;

class GenericS3Output extends Output
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
     * @var string
     */
    public $host;

    /**
     * @JMS\Type("integer")
     * @var int
     */
    public $port;

    /**
     * GenericS3Output constructor.
     * @param string $bucketName
     * @param string $accessKey
     * @param string $secretKey
     * @param string $host
     * @param int $port
     */
    public function __construct($bucketName, $accessKey, $secretKey, $host, $port)
    {
        $this->bucketName = $bucketName;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }

    /**
     * @param mixed $bucketName
     */
    public function setBucketName($bucketName)
    {
        $this->bucketName = $bucketName;
    }

    /**
     * @return mixed
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * @param mixed $accessKey
     */
    public function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param mixed $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }
}