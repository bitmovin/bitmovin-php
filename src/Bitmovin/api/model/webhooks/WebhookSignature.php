<?php


namespace Bitmovin\api\model\webhooks;

use Bitmovin\api\enum\webhooks\SignatureType;
use JMS\Serializer\Annotation as JMS;

class WebhookSignature
{
    /**
     * @JMS\Type("string")
     * @var  string EncryptionType enum
     */
    private $type = SignatureType::HMAC;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $key;

    /**
     * WebhookSignature constructor.
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
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

}