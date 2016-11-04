<?php


namespace Bitmovin\test\api\manifest;


use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\resource\manifest\HlsManifestResource;
use Bitmovin\test\AbstractBitmovinApiTest;
use Bitmovin\test\api\util\RegexpHelper;
use Ramsey\Uuid\Uuid;

class HlsManifestResourceTest extends AbstractBitmovinApiTest
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
     * @return HlsManifestResource
     */
    protected function getResource()
    {
        return $this->apiClient->manifests()->hls();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return HlsManifest::class;
    }

    /**
     * @return HlsManifest
     */
    protected function create()
    {
        $encodingOutput = new EncodingOutput(new Output());
        $encodingOutput->setOutputId(Uuid::uuid4()->toString());
        $encodingOutput->setOutputPath('/test/path/' . Uuid::uuid4()->toString());
        $manifest = new HlsManifest();
        $manifest->setName('PHP_HLS_TEST');
        $manifest->setOutputs([$encodingOutput]);
        return $manifest;
    }

    /**
     * @throws BitmovinException
     */
    public function testCreateAndDelete()
    {

        $hls = $this->create();
        $createdHls = $this->getResource()->create($hls);

        $this->assertInstanceOf($this->expectedClass(), $createdHls);
        $this->assertTrue(RegexpHelper::isUUID($createdHls->getId()), "Valid UUID expected");
        //$this->assertEquals($bucketName, $createdS3Input->getBucketName());

        $deletedDash = $this->getResource()->delete($createdHls);

        $this->assertInstanceOf($this->expectedClass(), $deletedDash);
        $this->assertTrue(RegexpHelper::isUUID($deletedDash->getId()), "Valid UUID expected");
    }

    public function testCreateWithStatusAndDelete()
    {

        $hls = $this->create();
        $createdHls = $this->getResource()->create($hls);

        $this->assertInstanceOf($this->expectedClass(), $createdHls);
        $this->assertTrue(RegexpHelper::isUUID($createdHls->getId()), "Valid UUID expected");
        //$this->assertEquals($bucketName, $createdS3Input->getBucketName());

        $status = $this->getResource()->status($createdHls);
        $this->assertEquals(Status::CREATED, $status->getStatus());


        $deletedDash = $this->getResource()->delete($createdHls);

        $this->assertInstanceOf($this->expectedClass(), $deletedDash);
        $this->assertTrue(RegexpHelper::isUUID($deletedDash->getId()), "Valid UUID expected");
    }


    public function testList()
    {
        $hls = $this->create();
        $createdHls = $this->getResource()->create($hls);

        $listResults = $this->getResource()->listPage();
        $this->assertTrue(is_array($listResults));
        $this->assertTrue(sizeof($listResults) > 0);

        /** @var HlsManifest $result */
        foreach ($listResults as $result)
        {
            $this->assertInstanceOf($this->expectedClass(), $result);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }

        $this->getResource()->delete($createdHls);
    }


    public function testGet()
    {
        $hls = $this->create();
        $createdHls = $this->getResource()->create($hls);
        /** @var HlsManifest[] $listResults */
        $listResults = $this->getResource()->listPage();
        $id = $listResults[0]->getId();

        /** @var HlsManifest $hls */
        $hls = $this->getResource()->getById($id);

        $this->assertInstanceOf($this->expectedClass(), $hls);
        $this->getResource()->delete($createdHls);
    }

    public function testAddStreamInfo()
    {
        $hlsManifest = $this->getResource()->create($this->create());
        $this->assertTrue(RegexpHelper::isUUID($hlsManifest->getId()), "Valid UUID expected");
        $streamInfo = $this->getResource()->createStreamInfo($hlsManifest, $this->defaultStreamInfo("EncodingId"));
        $this->assertTrue(RegexpHelper::isUUID($streamInfo->getId()), "Valid UUID expected");
    }

    public function testAddMediaInfo()
    {
        $hlsManifest = $this->getResource()->create($this->create());
        $this->assertTrue(RegexpHelper::isUUID($hlsManifest->getId()), "Valid UUID expected");
        $info = $this->getResource()->createMediaInfo($hlsManifest, $this->defaultMediaInfo());
        $this->assertTrue(RegexpHelper::isUUID($info->getId()), "Valid UUID expected");
    }

    /**
     * @param $encodingId
     * @return StreamInfo
     */
    private function defaultStreamInfo($encodingId)
    {
        $info = new StreamInfo();
        $info->setEncodingId($encodingId);
        $info->setStreamId("test");
        $info->setMuxingId("test");
        $info->setUri("name");
        $info->setSegmentPath("test1");
        $info->setAudio('audio_1');
        return $info;
    }

    /**
     * @return MediaInfo
     */
    private function defaultMediaInfo()
    {
        $info = new MediaInfo();
        $info->setGroupId("audio1");
        $info->setUri("audio1");
        $info->setType(MediaInfoType::AUDIO);
        $info->setEncodingId("encoding-id");
        $info->setStreamId("test");
        $info->setMuxingId("test");
        $info->setSegmentPath("test1");
        $info->setLanguage("en");
        $info->setAssocLanguage("en");
        $info->setAutoselect(false);
        $info->setForced(false);
        $info->setForced(false);
        $info->setCharacteristics(["public.accessibility.describes-audio"]);
        return $info;
    }

}