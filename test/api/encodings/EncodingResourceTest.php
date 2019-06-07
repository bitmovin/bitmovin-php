<?php

namespace Bitmovin\test\api\encodings;

use Bitmovin\api\ApiClient;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\ModelInterface;
use Bitmovin\test\AbstractBitmovinApiTest;
use Bitmovin\test\api\util\RegexpHelper;

class EncodingResourceTest extends AbstractBitmovinApiTest
{
    /** @var  ApiClient */
    private static $apiClient;

    private function getApiClient()
    {
        return static::$apiClient;
    }

    public static function setUpBeforeClass()
    {
        static::$apiClient = new ApiClient(self::getApiKey());
    }

    public static function tearDownAfterClass()
    {
        static::$apiClient = NULL;
    }

    /**
     * @throws BitmovinException
     */
    public function testCreateAndDeleteEncoding()
    {
        $name = "PHPAPICLIENT_TestEncoding_Name";
        $description = "PHPAPICLIENT_TestEncoding_Description";

        $encoding = new Encoding($name);
        $encoding->setDescription($description);

        $newEncoding = $this->createEncoding($encoding);

        $this->assertInstanceOf(Encoding::class, $newEncoding);
        $this->assertTrue(RegexpHelper::isUUID($newEncoding->getId()), "Valid UUID expected");
        $this->assertEquals($name, $newEncoding->getName());
        $this->assertEquals($description, $newEncoding->getDescription());
        $this->assertEquals(NULL, $newEncoding->getCloudRegion());

        $deletedEncoding = $this->deleteEncoding($newEncoding);

        $this->assertTrue($deletedEncoding instanceof Encoding);
        $this->assertTrue(RegexpHelper::isUUID($deletedEncoding->getId()), "Valid UUID expected");
    }

    public function testListEncodings()
    {
        $apiClient = $this->getApiClient();

        $listResults = $apiClient->encodings()->listPage();
        $this->assertTrue(is_array($listResults));

        foreach ($listResults as $result)
        {
            $this->assertTrue($result instanceof Encoding);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }

        return $listResults;
    }

    /**
     * @param ModelInterface[] $encodings
     *
     * @depends testListEncodings
     * @throws BitmovinException
     */
    public function testGetEncoding(array $encodings)
    {
        $apiClient = $this->getApiClient();
        $encodingId = $encodings[0]->getId();

        $encoding = $apiClient->encodings()->getById($encodingId);

        $this->assertInstanceOf(Encoding::class, $encoding);
        $this->assertTrue(RegexpHelper::isUUID($encoding->getId()), "Valid UUID expected");
    }

    public function testGetEncodingNotFoundException()
    {
        $apiClient = $this->getApiClient();
        $encodingId = "NON-EXISTING-ENCODING-ID";

        $this->expectException(BitmovinException::class, '', 404);
        $apiClient->encodings()->getById($encodingId);
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

    /**
     * @param Encoding $encoding
     *
     * @return Encoding
     * @throws BitmovinException
     */
    private function deleteEncoding(Encoding $encoding)
    {
        $apiClient = $this->getApiClient();

        return $apiClient->encodings()->delete($encoding);
    }
}
