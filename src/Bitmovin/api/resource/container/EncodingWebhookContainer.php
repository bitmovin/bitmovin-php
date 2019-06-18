<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\webhooks\Webhook;
use Bitmovin\api\resource\webhooks\WebhookResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class EncodingWebhookContainer
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
    public function __construct(Encoding $encoding, $apiKey)
    {
        $routeReplacementMap = array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId()
        );
        
        $baseUriEncodingFinished = RouteHelper::buildURI(ApiUrls::SPECIFIC_WEBHOOK_ENCODING_FINISHED, $routeReplacementMap);
        $baseUriEncodingError = RouteHelper::buildURI(ApiUrls::SPECIFIC_WEBHOOK_ENCODING_ERROR, $routeReplacementMap);
        $baseUriTransferFinished = RouteHelper::buildURI(ApiUrls::SPECIFIC_WEBHOOK_TRANSFER_FINISHED, $routeReplacementMap);
        $baseUriTransferError = RouteHelper::buildURI(ApiUrls::SPECIFIC_WEBHOOK_TRANSFER_ERROR, $routeReplacementMap);
        
        $this->encodingFinished = new WebhookResource($baseUriEncodingFinished, Webhook::class, $apiKey);
        $this->encodingError = new WebhookResource($baseUriEncodingError, Webhook::class, $apiKey);
        $this->transferFinished = new WebhookResource($baseUriTransferFinished, Webhook::class, $apiKey);
        $this->transferError = new WebhookResource($baseUriTransferError, Webhook::class, $apiKey);
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