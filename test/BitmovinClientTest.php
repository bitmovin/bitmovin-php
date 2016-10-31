<?php


use Bitmovin\api\container\EncodingContainer;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\test\api\util\RegexpHelper;

class BitmovinClientTest extends \Bitmovin\test\AbstractBitmovinApiTest
{

    public function testConvertInputs()
    {
        $client = new \Bitmovin\BitmovinClient($this->getApiKey());

        $job = new \Bitmovin\configs\JobConfig();
        $jobContainer = new \Bitmovin\api\container\JobContainer();
        $encodingProfile = new EncodingProfileConfig();

        $jobContainer->job = $job;
        $jobContainer->job->encodingProfile = $encodingProfile;
        /** @var EncodingContainer[] $encodingContainer */
        self::callMethod($client, 'convertInputsToEncodingContainer', array($jobContainer));
        $encodingContainer = $jobContainer->encodingContainers;
        $this->assertEmpty($encodingContainer);


        $videoStreamConfig_1080 = new H264VideoStreamConfig();
        $videoStreamConfig_1080->input = new HttpInput('http://www.testpath.com/download');
        $videoStreamConfig_1080->width = 1920;
        $videoStreamConfig_1080->height = 1080;
        $videoStreamConfig_1080->bitrate = 4800000;
        $videoStreamConfig_1080->rate = 25.0;
        $encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;
        self::callMethod($client, 'convertInputsToEncodingContainer', array($jobContainer));
        $encodingContainer = $jobContainer->encodingContainers;

        $this->assertTrue(sizeof($encodingContainer) == 1);
        $encodingConfigs = $encodingContainer[0];
        $this->assertTrue(sizeof($encodingConfigs->codecConfigContainer) == 1);
        $this->assertTrue($encodingConfigs->input instanceof HttpInput);
        $this->assertTrue($encodingConfigs->apiInput instanceof \Bitmovin\api\model\inputs\HttpInput);


        $videoStreamConfig_720 = new H264VideoStreamConfig();
        $videoStreamConfig_720->input = new HttpInput('http://www.testpath.com/download');
        $videoStreamConfig_720->width = 1280;
        $videoStreamConfig_720->height = 720;
        $videoStreamConfig_720->bitrate = 2400000;
        $videoStreamConfig_720->rate = 25.0;
        $encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;
        self::callMethod($client, 'convertInputsToEncodingContainer', array($jobContainer));
        $encodingContainer = $jobContainer->encodingContainers;
        $this->assertTrue(sizeof($encodingContainer) == 1);

        $this->assertTrue(sizeof($encodingContainer) == 1);
        $encodingConfigs = $encodingContainer[0];
        $this->assertTrue(sizeof($encodingConfigs->codecConfigContainer) == 2);
        $this->assertTrue($encodingConfigs->input instanceof HttpInput);
        $this->assertTrue($encodingConfigs->apiInput instanceof \Bitmovin\api\model\inputs\HttpInput);


        $audioCatalanConfig = new AudioStreamConfig();
        $audioCatalanConfig->input = new HttpInput('http://www.testpath.com/catalan');
        $audioCatalanConfig->bitrate = 128000;
        $audioCatalanConfig->rate = 48000;
        $audioCatalanConfig->lang = 'ca';
        $encodingProfile->audioStreamConfigs[] = $audioCatalanConfig;
        self::callMethod($client, 'convertInputsToEncodingContainer', array($jobContainer));
        $encodingContainer = $jobContainer->encodingContainers;

        $this->assertTrue(sizeof($encodingContainer) == 2);
        $encodingConfigs = $encodingContainer[0];
        $this->assertTrue(sizeof($encodingConfigs->codecConfigContainer) == 2);
        $this->assertTrue($encodingConfigs->input instanceof HttpInput);
        /** @var HttpInput $httpInput */
        $httpInput = $encodingConfigs->input;
        $this->assertEquals('http://www.testpath.com/download', $httpInput->url);
        $this->assertTrue($encodingConfigs->apiInput instanceof \Bitmovin\api\model\inputs\HttpInput);
        $encodingConfigs = $encodingContainer[1];
        $this->assertTrue(sizeof($encodingConfigs->codecConfigContainer) == 1);
        $this->assertTrue($encodingConfigs->input instanceof HttpInput);
        /** @var HttpInput $httpInput */
        $httpInput = $encodingConfigs->input;
        $this->assertEquals('http://www.testpath.com/catalan', $httpInput->url);
        $this->assertTrue($encodingConfigs->apiInput instanceof \Bitmovin\api\model\inputs\HttpInput);

    }

    public function testCreateInput()
    {
        $apiClient = new \Bitmovin\api\ApiClient($this->getApiKey());
        $client = new \Bitmovin\BitmovinClient($this->getApiKey());

        /** @var EncodingContainer[] $encodingContainer */
        $jobContainer = new \Bitmovin\api\container\JobContainer();
        $jobContainer->encodingContainers = [];
        $jobContainer->encodingContainers[] = new EncodingContainer(new \Bitmovin\api\model\inputs\HttpInput("www.test1.com"), new HttpInput("testurl"));
        self::callMethod($client, 'createInputs', array($jobContainer));
        $this->assertTrue(RegexpHelper::isUUID($jobContainer->encodingContainers[0]->apiInput->getId()), "Valid UUID expected");

        $apiClient->inputs()->http()->deleteById($jobContainer->encodingContainers[0]->apiInput->getId());
    }


}