<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\ApiClient;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\inputs\Input;
use Bitmovin\test\AbstractBitmovinApiTest;
use Bitmovin\test\api\util\RegexpHelper;

abstract class AbstractInputResourceTest extends AbstractBitmovinApiTest
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
     * @return Input
     */
    protected abstract function createInput();

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

        $input = $this->createInput();
        $createdInput = $this->createInputResource($input);

        $this->assertInstanceOf($this->expectedClass(), $createdInput);
        $this->assertTrue(RegexpHelper::isUUID($createdInput->getId()), "Valid UUID expected");
        //$this->assertEquals($bucketName, $createdS3Input->getBucketName());

        $deletedInput = $this->deleteInput($createdInput);

        $this->assertInstanceOf($this->expectedClass(), $deletedInput);
        $this->assertTrue(RegexpHelper::isUUID($deletedInput->getId()), "Valid UUID expected");
    }


    public function testList()
    {
        $input = $this->createInput();
        $createdInput = $this->createInputResource($input);
        $listResults = $this->getResource()->listAll();
        $this->assertTrue(is_array($listResults));
        $this->assertTrue(sizeof($listResults) > 0);

        /** @var Input $result */
        foreach ($listResults as $result)
        {
            $this->assertInstanceOf($this->expectedClass(), $result);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }

        $this->deleteInput($createdInput);
    }


    public function testGet()
    {
        $input = $this->createInput();
        $createdInput = $this->createInputResource($input);
        /** @var Input[] $listResults */
        $listResults = $this->getResource()->listAll();
        $id = $listResults[0]->getId();

        /** @var Input $input */
        $input = $this->getResource()->getById($id);

        $this->assertInstanceOf($this->expectedClass(), $input);
        $this->assertTrue(RegexpHelper::isUUID($input->getId()), "Valid UUID expected");
        $this->deleteInput($createdInput);
    }

    public function testGetInputNotFoundException()
    {
        $inputId = "NON-EXISTING-INPUT-ID";

        $this->setExpectedException(BitmovinException::class, '', 404);
        $this->getResource()->getById($inputId);
    }


    /**
     * @param Input $input
     *
     * @return Input
     * @throws BitmovinException
     */
    private function createInputResource(Input $input)
    {
        try
        {
            return $this->getResource()->create($input);
        }
        catch (BitmovinException $e)
        {
            var_dump(get_class($e), $e->getMessage(), $e->getDeveloperMessage());
            throw $e;
        }
    }

    /**
     * @param Input $input
     *
     * @return Input
     * @throws BitmovinException
     */
    private function deleteInput(Input $input)
    {
        return $this->getResource()->delete($input);
    }
}