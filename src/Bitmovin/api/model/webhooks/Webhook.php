<?php


namespace Bitmovin\api\model\webhooks;

use Bitmovin\api\enum\webhooks\WebhookHttpMethod;
use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class Webhook extends AbstractModel
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $url;
    /**
     * @JMS\Type("string")
     * @var  string WebhookHttpMethod enum
     */
    private $method = WebhookHttpMethod::POST;
    /**
     * @JMS\Type("bool")
     * @var  bool
     */
    private $insecureSsl;
    /**
     * @JMS\Type("Bitmovin\api\model\webhooks\WebhookEncryption")
     * @var  WebhookEncryption
     */
    private $encryption;
    /**
     * @JMS\Type("Bitmovin\api\model\webhooks\WebhookSignature")
     * @var  WebhookSignature
     */
    private $signature;

    /**
     * Webhook constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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

    /**
     * @return bool
     */
    public function isInsecureSsl()
    {
        return $this->insecureSsl;
    }

    /**
     * @param bool $insecureSsl
     */
    public function setInsecureSsl($insecureSsl)
    {
        $this->insecureSsl = $insecureSsl;
    }

    /**
     * @return WebhookEncryption
     */
    public function getEncryption()
    {
        return $this->encryption;
    }

    /**
     * @param WebhookEncryption $encryption
     */
    public function setEncryption($encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * @return WebhookSignature
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param WebhookSignature $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

}