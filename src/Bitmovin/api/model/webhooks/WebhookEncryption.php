<?php


namespace Bitmovin\api\model\webhooks;

use Bitmovin\api\enum\webhooks\EncryptionType;
use JMS\Serializer\Annotation as JMS;

class WebhookEncryption
{
    /**
     * @JMS\Type("string")
     * @var  string EncryptionType enum
     */
    private $type = EncryptionType::RSA;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $key;

    /**
     * WebhookEncryption constructor.
     * @param string $type
     * @param string $key
     */
    public function __construct($type, $key)
    {
        $this->type = $type;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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

}