<?php


namespace Bitmovin\test\api\manifest;


use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\CodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\Input;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashDrmRepresentation;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\resource\manifest\DashManifestResource;
use Bitmovin\test\AbstractBitmovinApiTest;
use Bitmovin\test\api\util\RegexpHelper;
use Ramsey\Uuid\Uuid;

class DashManifestResourceTest extends AbstractBitmovinApiTest
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
     * @return DashManifest
     */
    protected function create()
    {
        $encodingOutput = new EncodingOutput(new Output());
        $encodingOutput->setOutputId(Uuid::uuid4()->toString());
        $encodingOutput->setOutputPath('/test/path/' . Uuid::uuid4()->toString());
        $dashManifest = new DashManifest();
        $dashManifest->setManifestName('PHP_DASH_TEST');
        $dashManifest->setOutputs([$encodingOutput]);
        return $dashManifest;
    }


    /**
     * @return S3Input
     */
    protected function createInput()
    {
        $input = new S3Input("123", "345", "test");
        $input = $this->apiClient->inputs()->s3()->create($input);
        return $input;
    }

    /**
     * @return Period
     */
    protected function getPeriod()
    {
        $period = new Period();
        $period->setStart("0");
        $period->setDuration("263");
        return $period;
    }

    /**
     * @return S3Output
     */
    protected function createOutput()
    {
        $output = new S3Output("test", "S3S3ACCESSKEYTEST123", "secretkeyTest123secretKeyTest123TestTest");
        $output = $this->apiClient->outputs()->s3()->create($output);
        return $output;
    }

    /**
     * @param  string            $name
     * @param string             $description
     * @param CodecConfiguration $codecConfig
     * @return array
     */
    public function createResourcesForAdaptation($name, $description, $codecConfig)
    {
        $encoding = new Encoding($name);
        $encoding->setDescription($description);
        $encoding = $this->apiClient->encodings()->create($encoding);

        $input = $this->createInput();
        $inputStream = new InputStream($input, "test", SelectionMode::POSITION_ABSOLUTE);

        $stream = new Stream($codecConfig, [$inputStream]);
        $output = new EncodingOutput($this->createOutput());
        $output->setOutputPath("");
        $stream->setOutputs([$output]);
        $stream = $this->apiClient->encodings()->streams($encoding)->create($stream);

        $muxing = new FMP4Muxing();
        $muxing->setSegmentNaming("segment_%number%.m4s");
        $muxing->setSegmentLength(4.0);
        $muxing->setInitSegmentName("init.mp4");
        $streamMuxing = new MuxingStream();
        $streamMuxing->setStreamId($stream->getId());
        $muxing->setStreams([$streamMuxing]);
        $muxing = $this->apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($muxing);
        return array($encoding, $stream, $muxing);
    }

    /**
     * @return \Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration
     */
    protected function createVideoCodecConfig()
    {
        $name = "H264 CodecConfig 4Mbit " . uniqid();
        $profile = H264Profile::BASELINE;
        $bitrate = 4000000;
        $rate = 24;

        $h264VideoCodecConfig = new H264VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH264VideoCodecConfig = $this->apiClient->codecConfigurations()->videoH264()->create($h264VideoCodecConfig);
        return $createdH264VideoCodecConfig;
    }

    /**
     * @return \Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration
     */
    protected function createAudioCodecConfig()
    {
        $name = "AAC CodecConfig 4Mbit " . uniqid();
        $profile = H264Profile::BASELINE;
        $bitrate = 400000;
        $rate = 44100;

        $config = new AACAudioCodecConfiguration($name, $profile, $bitrate, $rate);
        $config = $this->apiClient->codecConfigurations()->audioAAC()->create($config);
        return $config;
    }

    /**
     * @return VideoAdaptationSet
     */
    protected function getVideoAdaptationSet()
    {
        $videoAdaptationSet = new VideoAdaptationSet();
        return $videoAdaptationSet;
    }

    /**
     * @return AudioAdaptationSet
     */
    protected function getAudioAdaptationSet()
    {
        $audioAdaptationSet = new AudioAdaptationSet();
        $audioAdaptationSet->setLang("en");
        return $audioAdaptationSet;
    }


    /**
     * @return DashManifestResource
     */
    protected function getResource()
    {
        return $this->apiClient->manifests()->dash();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return DashManifest::class;
    }

    /**
     * @throws BitmovinException
     */
    public function testCreateAndDelete()
    {

        $dash = $this->create();
        $createdDash = $this->getResource()->create($dash);

        $this->assertInstanceOf($this->expectedClass(), $createdDash);
        $this->assertTrue(RegexpHelper::isUUID($createdDash->getId()), "Valid UUID expected");
        //$this->assertEquals($bucketName, $createdS3Input->getBucketName());

        $deletedDash = $this->getResource()->delete($createdDash);

        $this->assertInstanceOf($this->expectedClass(), $deletedDash);
        $this->assertTrue(RegexpHelper::isUUID($deletedDash->getId()), "Valid UUID expected");
    }

    public function testCreateWithStatusAndDelete()
    {

        $dash = $this->create();
        $createdDash = $this->getResource()->create($dash);

        $this->assertInstanceOf($this->expectedClass(), $createdDash);
        $this->assertTrue(RegexpHelper::isUUID($createdDash->getId()), "Valid UUID expected");
        //$this->assertEquals($bucketName, $createdS3Input->getBucketName());

        $status = $this->getResource()->status($createdDash);
        $this->assertEquals(Status::CREATED, $status->getStatus());


        $deletedDash = $this->getResource()->delete($createdDash);

        $this->assertInstanceOf($this->expectedClass(), $deletedDash);
        $this->assertTrue(RegexpHelper::isUUID($deletedDash->getId()), "Valid UUID expected");
    }


    public function testList()
    {
        $dash = $this->create();
        $createdDash = $this->getResource()->create($dash);

        $listResults = $this->getResource()->listPage();
        $this->assertTrue(is_array($listResults));
        $this->assertTrue(sizeof($listResults) > 0);

        /** @var Input $result */
        foreach ($listResults as $result)
        {
            $this->assertInstanceOf($this->expectedClass(), $result);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }

        $this->getResource()->delete($createdDash);
    }


    public function testGet()
    {
        $dash = $this->create();
        $createdDash = $this->getResource()->create($dash);
        /** @var DashManifest[] $listResults */
        $listResults = $this->getResource()->listPage();
        $id = $listResults[0]->getId();

        /** @var DashManifest $input */
        $input = $this->getResource()->getById($id);

        $this->assertInstanceOf($this->expectedClass(), $input);
        $this->getResource()->delete($createdDash);
    }


    public function testAddPeriod()
    {
        $dash = $this->create();
        $createdDash = $this->getResource()->create($dash);

        $period = $this->getPeriod();
        $newPeriod = $this->getResource()->createPeriod($createdDash, $period);
        $this->assertTrue(RegexpHelper::isUUID($newPeriod->getId()), "Valid UUID expected");

        $this->getResource()->delete($createdDash);
    }

    public function testAddVideoAdaptationSet()
    {
        $dash = $this->create();
        $dash = $this->getResource()->create($dash);

        $period = $this->getPeriod();
        $period = $this->getResource()->createPeriod($dash, $period);

        $videoAdaptationSet = $this->getResource()->addVideoAdaptionSetToPeriod($dash, $period, $this->getVideoAdaptationSet());

        $this->assertTrue(RegexpHelper::isUUID($videoAdaptationSet->getId()), "Valid UUID expected");

        $this->getResource()->delete($dash);
    }

    public function testAddAudioAdaptationSet()
    {
        $dash = $this->create();
        $dash = $this->getResource()->create($dash);

        $period = $this->getPeriod();
        $period = $this->getResource()->createPeriod($dash, $period);

        $audioAdaptationSet = $this->getResource()->addAudioAdaptionSetToPeriod($dash, $period, $this->getAudioAdaptationSet());

        $this->assertTrue(RegexpHelper::isUUID($audioAdaptationSet->getId()), "Valid UUID expected");

        $this->getResource()->delete($dash);
    }

    public function testAddAudioAdaptationRepresentationSet()
    {

        $name = "PHPAPICLIENT_TestEncoding_Name";
        $description = "PHPAPICLIENT_TestEncoding_Description";

        /** @var Encoding $encoding */
        /** @var Stream $stream */
        /** @var FMP4Muxing $muxing */
        list($encoding, $stream, $muxing) = $this->createResourcesForAdaptation($name, $description, $this->createAudioCodecConfig());

        $dash = $this->create();
        $dash = $this->getResource()->create($dash);

        $period = $this->getPeriod();
        $period = $this->getResource()->createPeriod($dash, $period);

        $audioAdaptationSet = $this->getResource()->addAudioAdaptionSetToPeriod($dash, $period, $this->getAudioAdaptationSet());

        $representation = new DashDrmRepresentation();
        $representation->setType(DashMuxingType::TYPE_TEMPLATE);
        $representation->setEncodingId($encoding->getId());
        $representation->setStreamId($stream->getId());
        $representation->setMuxingId($muxing->getId());
        $representation->setSegmentPath("/path/to/segments");

        $representation = $this->apiClient->manifests()->dash()->addDrmRepresentationToAdaptationSet($dash, $period, $audioAdaptationSet, $representation);

        $this->assertTrue(RegexpHelper::isUUID($representation->getId()), "Valid UUID expected");

        $this->getResource()->delete($dash);
    }

    public function testAddVideoAdaptationRepresentationSet()
    {

        $name = "PHPAPICLIENT_TestEncoding_Name";
        $description = "PHPAPICLIENT_TestEncoding_Description";

        /** @var Encoding $encoding */
        /** @var Stream $stream */
        /** @var FMP4Muxing $muxing */
        list($encoding, $stream, $muxing) = $this->createResourcesForAdaptation($name, $description, $this->createVideoCodecConfig());

        $dash = $this->create();
        $dash = $this->getResource()->create($dash);

        $period = $this->getPeriod();
        $period = $this->getResource()->createPeriod($dash, $period);

        $videoAdaptationSet = $this->getResource()->addVideoAdaptionSetToPeriod($dash, $period, $this->getVideoAdaptationSet());

        $representation = new DashDrmRepresentation();
        $representation->setType(DashMuxingType::TYPE_TEMPLATE);
        $representation->setEncodingId($encoding->getId());
        $representation->setStreamId($stream->getId());
        $representation->setMuxingId($muxing->getId());
        $representation->setSegmentPath("/path/to/segments");

        $representation = $this->apiClient->manifests()->dash()->addDrmRepresentationToAdaptationSet($dash, $period, $videoAdaptationSet, $representation);

        $this->assertTrue(RegexpHelper::isUUID($representation->getId()), "Valid UUID expected");

        $this->getResource()->delete($dash);
    }

    public function testGetNotFoundException()
    {
        $dashId = "NON-EXISTING-DASH-ID";

        $this->setExpectedException(BitmovinException::class, '', 404);
        $this->getResource()->getById($dashId);
    }
}