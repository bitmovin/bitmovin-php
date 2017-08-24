<?php

namespace Bitmovin\api\model\encodings\drms;

use JMS\Serializer\Annotation as JMS;

class AesDrm extends AbstractDrm
{

    /**
     * @JMS\Type("string")
     * @var string AesEncryptionMethod enum
     */
    private $method;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $key;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $iv;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $keyFileUri;

    /**
     * AbstractDrm constructor.
     * @param string                                                $method
     * @param string (32char hex format)                            $key
     * @param string (32char hex format)                            $iv
     * @param \Bitmovin\api\model\encodings\helper\EncodingOutput[] $outputs
     * @param string                                                $keyFileUri
     */
    public function __construct($method, $key, $iv, array $outputs = [], $keyFileUri = null)
    {
        parent::__construct($outputs);
        $this->method = $method;
        $this->key = $key;
        $this->iv = $iv;
        $this->keyFileUri = $keyFileUri;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * @param string $iv
     */
    public function setIv($iv)
    {
        $this->iv = $iv;
    }

    /**
     * @return string
     */
    public function getKeyFileUri()
    {
        return $this->keyFileUri;
    }

    /**
     * @param string $keyFileUri
     */
    public function setKeyFileUri($keyFileUri)
    {
        $this->keyFileUri = $keyFileUri;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
}