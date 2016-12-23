<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\model\webhooks\Webhook;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new ApiClient('INSERT YOUR API KEY HERE');

// Create encoding finished Webhook
$webhook = new Webhook('http://webhookurl.com/encoding/finished');
$createdWebhook = $client->webhooks()->encodingFinished()->create($webhook);
$createdWebhookId = $createdWebhook->getId();

echo "Id of the created encoding finished webhook: $createdWebhookId\n";

// Get encoding finished webhook
$detailsWebhook = $client->webhooks()->encodingFinished()->getById($createdWebhookId);

// Delete encoding finished webhook
$client->webhooks()->encodingFinished()->delete($detailsWebhook);

// Create encoding error Webhook
$webhook = new Webhook('http://webhookurl.com/encoding/error');
$createdWebhook = $client->webhooks()->encodingError()->create($webhook);
$createdWebhookId = $createdWebhook->getId();

echo "Id of the created encoding error webhook: $createdWebhookId\n";

// Get encoding error webhook
$detailsWebhook = $client->webhooks()->encodingError()->getById($createdWebhookId);

// Delete encoding error webhook
$client->webhooks()->encodingError()->delete($detailsWebhook);

// Create transfer finished Webhook
$webhook = new Webhook('http://webhookurl.com/transfer/finished');
$createdWebhook = $client->webhooks()->transferFinished()->create($webhook);
$createdWebhookId = $createdWebhook->getId();

echo "Id of the created transfer finished webhook: $createdWebhookId\n";

// Get transfer finished webhook
$detailsWebhook = $client->webhooks()->transferFinished()->getById($createdWebhookId);

// Delete transfer finished webhook
$client->webhooks()->transferFinished()->delete($detailsWebhook);

// Create transfer error Webhook
$webhook = new Webhook('http://webhookurl.com/transfer/error');
$createdWebhook = $client->webhooks()->transferError()->create($webhook);
$createdWebhookId = $createdWebhook->getId();

echo "Id of the created transfer error webhook: $createdWebhookId\n";

// Get transfer error webhook
$detailsWebhook = $client->webhooks()->transferError()->getById($createdWebhookId);

// Delete transfer error webhook
$client->webhooks()->transferError()->delete($detailsWebhook);