<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\webhooks\Webhook;
use Bitmovin\api\resource\webhooks\WebhookResource;
use Bitmovin\api\util\ApiUrls;

class WebhookContainer
{
    /** @var WebhookResource */
    private $encodingFinished;
    /** @var WebhookResource */
    private $encodingError;
    /** @var WebhookResource */
    private $transferFinished;
    /** @var WebhookResource */
    private $transferError;

    /**
     * OutputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->encodingFinished = new WebhookResource(ApiUrls::WEBHOOK_ENCODING_FINISHED, Webhook::class, $apiKey);
        $this->encodingError = new WebhookResource(ApiUrls::WEBHOOK_ENCODING_ERROR, Webhook::class, $apiKey);
        $this->transferFinished = new WebhookResource(ApiUrls::WEBHOOK_TRANSFER_FINISHED, Webhook::class, $apiKey);
        $this->transferError = new WebhookResource(ApiUrls::WEBHOOK_TRANSFER_ERROR, Webhook::class, $apiKey);
    }

    /**
     * @return WebhookResource
     */
    public function encodingFinished()
    {
        return $this->encodingFinished;
    }

    /**
     * @return WebhookResource
     */
    public function encodingError()
    {
        return $this->encodingError;
    }

    /**
     * @return WebhookResource
     */
    public function transferFinished()
    {
        return $this->transferFinished;
    }

    /**
     * @return WebhookResource
     */
    public function transferError()
    {
        return $this->transferError;
    }

}