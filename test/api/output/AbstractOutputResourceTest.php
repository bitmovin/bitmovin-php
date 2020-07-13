<?php


namespace Bitmovin\test\api\output;


use Bitmovin\api\ApiClient;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\test\AbstractBitmovinApiTest;
use Bitmovin\test\api\util\RegexpHelper;

abstract class AbstractOutputResourceTest extends AbstractBitmovinApiTest
{
    /** @var  ApiClient */
    protected $apiClient;


    public function setUp()
    {
        $this->apiClient = new ApiClient(self::getApiKey());
    }

    public function tearDown()
    {
        $this->apiClient = null;
    }

    /**
     * @return Output
     */
    protected abstract function createOutput();

    /**
     * @return mixed
     */
    protected abstract function getResource();

    /**
     * @return string
     */
    protected abstract function expectedClass();

    /**
     * @throws BitmovinException
     */
    public function testCreateAndDelete()
    {

        $output = $this->createOutput();
        $createdOutput = $this->createOutputResource($output);

        $this->assertInstanceOf($this->expectedClass(), $createdOutput);
        $this->assertTrue(RegexpHelper::isUUID($createdOutput->getId()), "Valid UUID expected");
        //$this->assertEquals($bucketName, $createdS3Output->getBucketName());

        $deletedOutput = $this->deleteOutput($createdOutput);

        $this->assertInstanceOf($this->expectedClass(), $deletedOutput);
        $this->assertTrue(RegexpHelper::isUUID($deletedOutput->getId()), "Valid UUID expected");
    }


    public function testList()
    {
        $output = $this->createOutput();
        $createdOutput = $this->createOutputResource($output);
        $listResults = $this->getResource()->listAll();
        $this->assertTrue(is_array($listResults));
        $this->assertTrue(sizeof($listResults) > 0);

        /** @var Output $result */
        foreach ($listResults as $result)
        {
            $this->assertInstanceOf($this->expectedClass(), $result);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }

        $this->deleteOutput($createdOutput);
    }


    public function testGet()
    {
        $output = $this->createOutput();
        $createdOutput = $this->createOutputResource($output);
        /** @var Output[] $listResults */
        $listResults = $this->getResource()->listAll();
        $id = $listResults[0]->getId();

        /** @var Output $output */
        $output = $this->getResource()->getById($id);

        $this->assertInstanceOf($this->expectedClass(), $output);
        $this->assertTrue(RegexpHelper::isUUID($output->getId()), "Valid UUID expected");
        $this->deleteOutput($createdOutput);
    }

    public function testGetOutputNotFoundException()
    {
        $this->markTestSkipped('Server responds invalid 404 message.');
        $outputId = "NON-EXISTING-Output-ID";

        $this->expectException(BitmovinException::class, '', 404);
        $this->getResource()->getById($outputId);
    }


    /**
     * @param Output $output
     *
     * @return Output
     * @throws BitmovinException
     */
    private function createOutputResource(Output $output)
    {
        try
        {
            return $this->getResource()->create($output);
        }
        catch (BitmovinException $e)
        {
            var_dump(get_class($e), $e->getMessage(), $e->getDeveloperMessage());
            throw $e;
        }
    }

    /**
     * @param Output $output
     *
     * @return Output
     * @throws BitmovinException
     */
    private function deleteOutput(Output $output)
    {
        return $this->getResource()->delete($output);
    }
}
