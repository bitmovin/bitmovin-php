<?php

namespace Bitmovin\test\api\encodings;

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\webhooks\EncryptionType;
use Bitmovin\api\enum\webhooks\SignatureType;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\ModelInterface;
use Bitmovin\api\model\webhooks\Webhook;
use Bitmovin\api\model\webhooks\WebhookEncryption;
use Bitmovin\api\model\webhooks\WebhookSignature;
use Bitmovin\test\AbstractBitmovinApiTest;
use Bitmovin\test\api\util\RegexpHelper;

class WebhookResourceTest extends AbstractBitmovinApiTest
{
    /** @var  ApiClient */
    private $apiClient;

    public function setUp()
    {
        $this->apiClient = new ApiClient(self::getApiKey());
    }

    public function testCreateEncodingFinished()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $createdWebhook = $this->apiClient->webhooks()->encodingFinished()->create($encodingWebhook);
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNotNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
    }

    public function testCreateEncodingFinishedFull()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $encodingWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $encodingWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->encodingFinished()->create($encodingWebhook);
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNotNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
        $this->assertNotNull($createdWebhook->getSignature());
        $this->assertEquals(SignatureType::HMAC, $createdWebhook->getSignature()->getType());
        $this->assertNull($createdWebhook->getSignature()->getKey());
        $this->assertNotNull($createdWebhook->getEncryption());
        $this->assertEquals(EncryptionType::RSA, $createdWebhook->getEncryption()->getType());
        $this->assertNull($createdWebhook->getEncryption()->getKey());
    }

    public function testGetEncodingFinishedFull()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $encodingWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $encodingWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->encodingFinished()->create($encodingWebhook);
        $createdWebhook = $this->apiClient->webhooks()->encodingFinished()->getById($createdWebhook->getId());
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNotNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
        $this->assertNotNull($createdWebhook->getSignature());
        $this->assertEquals(SignatureType::HMAC, $createdWebhook->getSignature()->getType());
        $this->assertNull($createdWebhook->getSignature()->getKey());
        $this->assertNotNull($createdWebhook->getEncryption());
        $this->assertEquals(EncryptionType::RSA, $createdWebhook->getEncryption()->getType());
        $this->assertNull($createdWebhook->getEncryption()->getKey());
    }


    /**
     * @expectedException \Bitmovin\api\exceptions\BitmovinException
     */
    public function testDeleteEncodingFinishedFull()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $encodingWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $encodingWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->encodingFinished()->create($encodingWebhook);
        $this->apiClient->webhooks()->encodingFinished()->delete($createdWebhook);
        $this->apiClient->webhooks()->encodingFinished()->getById($createdWebhook->getId());
    }

    public function testListEncodingFinished()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $encodingWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $encodingWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $this->apiClient->webhooks()->encodingFinished()->create($encodingWebhook);
        $createdWebhooks = $this->apiClient->webhooks()->encodingFinished()->listPage(0, 100);
        $this->assertTrue(sizeof($createdWebhooks) > 0);
    }

    public function testCreateEncodingError()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $createdWebhook = $this->apiClient->webhooks()->encodingError()->create($encodingWebhook);
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNotNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
    }

    public function testCreateEncodingErrorFull()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $encodingWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $encodingWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->encodingError()->create($encodingWebhook);
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNotNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
        $this->assertNotNull($createdWebhook->getSignature());
        $this->assertEquals(SignatureType::HMAC, $createdWebhook->getSignature()->getType());
        $this->assertNull($createdWebhook->getSignature()->getKey());
        $this->assertNotNull($createdWebhook->getEncryption());
        $this->assertEquals(EncryptionType::RSA, $createdWebhook->getEncryption()->getType());
        $this->assertNull($createdWebhook->getEncryption()->getKey());
    }


    public function testGetEncodingErrorFull()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $encodingWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $encodingWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->encodingError()->create($encodingWebhook);
        $createdWebhook = $this->apiClient->webhooks()->encodingError()->getById($createdWebhook->getId());
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNotNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
        $this->assertNotNull($createdWebhook->getSignature());
        $this->assertEquals(SignatureType::HMAC, $createdWebhook->getSignature()->getType());
        $this->assertNull($createdWebhook->getSignature()->getKey());
        $this->assertNotNull($createdWebhook->getEncryption());
        $this->assertEquals(EncryptionType::RSA, $createdWebhook->getEncryption()->getType());
        $this->assertNull($createdWebhook->getEncryption()->getKey());
    }


    /**
     * @expectedException \Bitmovin\api\exceptions\BitmovinException
     */
    public function testDeleteEncodingErrorFull()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $encodingWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $encodingWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->encodingError()->create($encodingWebhook);
        $this->apiClient->webhooks()->encodingFinished()->delete($createdWebhook);
        $this->apiClient->webhooks()->encodingFinished()->getById($createdWebhook->getId());
    }

    public function testListEncodingError()
    {
        $encodingWebhook = new Webhook('http://webhookurl.com/path');
        $encodingWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $encodingWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $this->apiClient->webhooks()->encodingError()->create($encodingWebhook);
        $createdWebhooks = $this->apiClient->webhooks()->encodingError()->listPage(0, 100);
        $this->assertTrue(sizeof($createdWebhooks) > 0);
    }

    public function testCreateTransferFinished()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $createdWebhook = $this->apiClient->webhooks()->transferFinished()->create($transferWebhook);
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
    }

    public function testCreateTransferFinishedFull()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $transferWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $transferWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->transferFinished()->create($transferWebhook);
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
        $this->assertNotNull($createdWebhook->getSignature());
        $this->assertEquals(SignatureType::HMAC, $createdWebhook->getSignature()->getType());
        $this->assertNull($createdWebhook->getSignature()->getKey());
        $this->assertNotNull($createdWebhook->getEncryption());
        $this->assertEquals(EncryptionType::RSA, $createdWebhook->getEncryption()->getType());
        $this->assertNull($createdWebhook->getEncryption()->getKey());
    }

    public function testGetTransferFinishedFull()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $transferWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $transferWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->transferFinished()->create($transferWebhook);
        $createdWebhook = $this->apiClient->webhooks()->transferFinished()->getById($createdWebhook->getId());
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
        $this->assertNotNull($createdWebhook->getSignature());
        $this->assertEquals(SignatureType::HMAC, $createdWebhook->getSignature()->getType());
        $this->assertNull($createdWebhook->getSignature()->getKey());
        $this->assertNotNull($createdWebhook->getEncryption());
        $this->assertEquals(EncryptionType::RSA, $createdWebhook->getEncryption()->getType());
        $this->assertNull($createdWebhook->getEncryption()->getKey());
    }


    /**
     * @expectedException \Bitmovin\api\exceptions\BitmovinException
     */
    public function testDeleteTransferFinishedFull()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $transferWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $transferWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->transferFinished()->create($transferWebhook);
        $this->apiClient->webhooks()->transferFinished()->delete($createdWebhook);
        $this->apiClient->webhooks()->transferFinished()->getById($createdWebhook->getId());
    }

    public function testListTransferFinished()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $transferWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $transferWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $this->apiClient->webhooks()->transferFinished()->create($transferWebhook);
        $createdWebhooks = $this->apiClient->webhooks()->transferFinished()->listPage(0, 100);
        $this->assertTrue(sizeof($createdWebhooks) > 0);
    }

    public function testCreateTransferError()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $createdWebhook = $this->apiClient->webhooks()->transferError()->create($transferWebhook);
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
    }

    public function testCreateTransferErrorFull()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $transferWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $transferWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->transferError()->create($transferWebhook);
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
        $this->assertNotNull($createdWebhook->getSignature());
        $this->assertEquals(SignatureType::HMAC, $createdWebhook->getSignature()->getType());
        $this->assertNull($createdWebhook->getSignature()->getKey());
        $this->assertNotNull($createdWebhook->getEncryption());
        $this->assertEquals(EncryptionType::RSA, $createdWebhook->getEncryption()->getType());
        $this->assertNull($createdWebhook->getEncryption()->getKey());
    }


    public function testGetTransferErrorFull()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $transferWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $transferWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->transferError()->create($transferWebhook);
        $createdWebhook = $this->apiClient->webhooks()->transferError()->getById($createdWebhook->getId());
        $this->assertNotNull($createdWebhook);
        $this->assertNotNull($createdWebhook->getId());
        $this->assertNull($createdWebhook->getSchema());
        $this->assertEquals('http://webhookurl.com/path', $createdWebhook->getUrl());
        $this->assertNotNull($createdWebhook->getSignature());
        $this->assertEquals(SignatureType::HMAC, $createdWebhook->getSignature()->getType());
        $this->assertNull($createdWebhook->getSignature()->getKey());
        $this->assertNotNull($createdWebhook->getEncryption());
        $this->assertEquals(EncryptionType::RSA, $createdWebhook->getEncryption()->getType());
        $this->assertNull($createdWebhook->getEncryption()->getKey());
    }


    /**
     * @expectedException \Bitmovin\api\exceptions\BitmovinException
     */
    public function testDeleteTransferErrorFull()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $transferWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $transferWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $createdWebhook = $this->apiClient->webhooks()->transferError()->create($transferWebhook);
        $this->apiClient->webhooks()->transferFinished()->delete($createdWebhook);
        $this->apiClient->webhooks()->transferFinished()->getById($createdWebhook->getId());
    }

    public function testListTransferError()
    {
        $transferWebhook = new Webhook('http://webhookurl.com/path');
        $transferWebhook->setSignature(new WebhookSignature(SignatureType::HMAC, "the_key"));
        $transferWebhook->setEncryption(new WebhookEncryption(EncryptionType::RSA, "rsa_key"));
        $this->apiClient->webhooks()->transferError()->create($transferWebhook);
        $createdWebhooks = $this->apiClient->webhooks()->transferError()->listPage(0, 100);
        $this->assertTrue(sizeof($createdWebhooks) > 0);
    }
    
    /**
     * @param Encoding $encoding
     *
     * @return Encoding
     * @throws BitmovinException
     */
    private function createEncoding(Encoding $encoding)
    {
        $apiClient = $this->getApiClient();
        
        return $apiClient->encodings()->create($encoding);
    }

}
