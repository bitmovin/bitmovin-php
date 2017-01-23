<?php

namespace Bitmovin\api\resource\webhooks;

use Bitmovin\api\model\webhooks\Webhook;
use Bitmovin\api\resource\AbstractResource;

class WebhookResource extends AbstractResource
{

    const LIST_NAME = 'items';

    /**
     * InputResource constructor.
     *
     * @param string $baseUri
     * @param string $modelClassName
     * @param string $apiKey
     */
    public function __construct($baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, static::LIST_NAME, $apiKey);
    }

    /**
     * @param Webhook $webhook
     *
     * @return Webhook
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(Webhook $webhook)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::createResource($webhook);
    }

    /**
     * @param Webhook $webhook
     *
     * @return Webhook
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(Webhook $webhook)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($webhook->getId());
    }

    /**
     * @param Webhook $webhook
     *
     * @return Webhook
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(Webhook $webhook)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResource($webhook->getId());
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return Webhook[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param string $webhookId
     *
     * @return Webhook
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($webhookId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResource($webhookId);
    }

    /**
     * @param string $webhookId
     *
     * @return Webhook
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($webhookId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($webhookId);
    }

}