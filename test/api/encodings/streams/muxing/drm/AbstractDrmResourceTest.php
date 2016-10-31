<?php


namespace Bitmovin\test\api\encodings\streams\muxing\drm;


use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\drms\AbstractDrm;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\test\AbstractBitmovinApiTest;
use Bitmovin\test\api\util\RegexpHelper;

abstract class AbstractDrmResourceTest extends AbstractBitmovinApiTest
{
    /** @var  ApiClient */
    protected $apiClient;

    /** @var  Encoding */
    protected $encoding;
    /** @var  Stream */
    protected $stream;
    /** @var  FMP4Muxing */
    protected $muxing;
    /** @var  S3Input */
    protected $input;
    /** @var  S3Output */
    protected $output;

    /**
     * @return AbstractDrm
     */
    protected abstract function create();

    /**
     * @return mixed
     */
    protected abstract function getResource();

    /**
     * @return string
     */
    protected abstract function expectedClass();

    public function setUp()
    {
        $this->apiClient = new ApiClient(self::getApiKey());

        $name = "PHPAPICLIENT_TestEncoding_Name";
        $description = "PHPAPICLIENT_TestEncoding_Description";

        $encoding = new Encoding($name);
        $encoding->setDescription($description);
        $encoding = $this->apiClient->encodings()->create($encoding);
        $this->encoding = $encoding;


        $input = $this->createInput();
        $inputStream = new InputStream($input, "test", SelectionMode::POSITION_ABSOLUTE);

        $stream = new Stream($this->createCodecConfig(), [$inputStream]);
        $output = new EncodingOutput($this->createOutput());
        $output->setOutputPath("");
        $stream->setOutputs([$output]);
        $stream = $this->apiClient->encodings()->streams($encoding)->create($stream);
        $this->stream = $stream;

        $muxing = new FMP4Muxing();
        $muxing->setSegmentNaming("segment_%number%.m4s");
        $muxing->setSegmentLength(4.0);
        $muxing->setInitSegmentName("init.mp4");
        $streamMuxing = new MuxingStream();
        $streamMuxing->setStreamId($stream->getId());
        $muxing->setStreams([$streamMuxing]);
        $muxing = $this->apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($muxing);
        $this->muxing = $muxing;
    }

    protected function createInput()
    {
        $input = new S3Input("123", "345", "test");
        $input = $this->apiClient->inputs()->s3()->create($input);
        $this->input = $input;
        return $input;
    }

    protected function createOutput()
    {
        $output = new S3Output("test", "S3S3ACCESSKEYTEST123", "secretkeyTest123secretKeyTest123TestTest");
        $output = $this->apiClient->outputs()->s3()->create($output);
        $this->output = $output;
        return $output;
    }

    protected function createCodecConfig()
    {
        $name = "H264 CodecConfig 4Mbit " . uniqid();
        $profile = H264Profile::BASELINE;
        $bitrate = 4000000;
        $rate = 24;

        $h264VideoCodecConfig = new H264VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $createdH264VideoCodecConfig = $this->apiClient->codecConfigurations()->videoH264()->create($h264VideoCodecConfig);
        return $createdH264VideoCodecConfig;
    }

    public function tearDown()
    {
        if ($this->encoding != null)
        {
            $this->apiClient->encodings()->delete($this->encoding);
        }
        if ($this->output != null)
        {
            $this->apiClient->outputs()->s3()->delete($this->output);
        }
        if ($this->input != null)
        {
            $this->apiClient->inputs()->s3()->delete($this->input);
        }
        $this->apiClient = null;
    }

    /**
     * @throws BitmovinException
     */
    public function testCreateAndDelete()
    {
        $drm = $this->create();
        /** @var AbstractDrm $drm */
        $drm = $this->getResource()->create($drm);

        $this->assertInstanceOf($this->expectedClass(), $drm);
        $this->assertTrue(RegexpHelper::isUUID($drm->getId()), "Valid UUID expected");

        $drm = $this->getResource()->delete($drm);
        $this->assertInstanceOf($this->expectedClass(), $drm);
        $this->assertTrue(RegexpHelper::isUUID($drm->getId()), "Valid UUID expected");
    }

    public function testList()
    {
        $drm = $this->create();
        /** @var AbstractDrm $drm */
        $drm = $this->getResource()->create($drm);
        $listResults = $this->getResource()->listAll();
        $this->assertTrue(is_array($listResults));
        $this->assertTrue(sizeof($listResults) > 0);

        /** @var AbstractDrm $result */
        foreach ($listResults as $result)
        {
            $this->assertInstanceOf($this->expectedClass(), $result);
            $this->assertTrue(RegexpHelper::isUUID($result->getId()), "Valid UUID expected");
        }

        $this->getResource()->delete($drm);
    }

    public function testGet()
    {
        $createDrm = $this->create();
        /** @var AbstractDrm $createDrm */
        $createDrm = $this->getResource()->create($createDrm);
        /** @var AbstractDrm[] $listResults */
        $listResults = $this->getResource()->listAll();
        $id = $listResults[0]->getId();

        /** @var AbstractDrm $drm */
        $drm = $this->getResource()->getById($id);

        $this->assertInstanceOf($this->expectedClass(), $drm);
        $this->assertTrue(RegexpHelper::isUUID($drm->getId()), "Valid UUID expected");
        $this->getResource()->delete($createDrm);
    }

    public function testGetNotFoundException()
    {
        $inputId = "NON-EXISTING-DRM-ID";

        $this->setExpectedException(BitmovinException::class, '', 404);
        $this->getResource()->getById($inputId);
    }

}