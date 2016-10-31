<?php


namespace Bitmovin\test\examples;

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\Status;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;
use Bitmovin\test\AbstractBitmovinApiTest;

class CreateSimpleEncoding extends AbstractBitmovinApiTest
{

    public function testJob()
    {
        $config = $this->getConfig();
        $config = $config['examples']['CreateSimpleEncoding'];
        $apiKey = $this->getApiKey();
        if ($apiKey == null)
        {
            $apiKey = 'INSERT YOU API KEY HERE';
        }
        $client = new BitmovinClient($apiKey);

        $jobConfig = $this->createJob($config);

        $client->runJobAndWaitForCompletion($jobConfig);
    }

    /**
     * @param $config
     * @return JobConfig
     */
    private function createJob($config)
    {
        if ($config == null)
        {
            $config['videoInputPath'] = 'INSERT YOUR HTTP VIDEO INPUT PATH HERE';
            $config['accessKey'] = 'INSERT YOUR GCS OUTPUT ACCESS KEY HERE';
            $config['secretKey'] = 'INSERT YOUR GCS OUTPUT SECRET KEY HERE';
            $config['bucketName'] = 'INSERT YOUR GCS OUTPUT BUCKET NAME HERE';
            $config['prefix'] = 'INSERT YOUR GCS OUTPUT PREFIX (FOLDER) HERE';
        }

        $encodingProfile = new EncodingProfileConfig();
        $encodingProfile->name = 'Test Encoding';
        $encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

        $videoStreamConfig_1080 = new H264VideoStreamConfig();
        $videoStreamConfig_1080->input = new HttpInput($config['videoInputPath']);
        $videoStreamConfig_1080->width = 1920;
        $videoStreamConfig_1080->height = 1080;
        $videoStreamConfig_1080->bitrate = 4800000;
        $videoStreamConfig_1080->rate = 25.0;
        $encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

        $videoStreamConfig_720 = new H264VideoStreamConfig();
        $videoStreamConfig_720->input = new HttpInput($config['videoInputPath']);
        $videoStreamConfig_720->width = 1280;
        $videoStreamConfig_720->height = 720;
        $videoStreamConfig_720->bitrate = 2400000;
        $videoStreamConfig_720->rate = 25.0;
        $encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

        $audioConfig = new AudioStreamConfig();
        $audioConfig->input = new HttpInput($config['videoInputPath']);
        $audioConfig->bitrate = 128000;
        $audioConfig->rate = 48000;
        $audioConfig->name = 'English';
        $audioConfig->lang = 'en';
        $audioConfig->position = 1;
        $encodingProfile->audioStreamConfigs[] = $audioConfig;

        $jobConfig = new JobConfig();
        $jobConfig->output = new GcsOutput($config['accessKey'], $config['secretKey'], $config['bucketName'], $config['prefix']);
        $jobConfig->output->prefix .=  time();
        $jobConfig->encodingProfile = $encodingProfile;
        $jobConfig->outputFormat[] = new DashOutputFormat();
        $jobConfig->outputFormat[] = new HlsOutputFormat();

        return $jobConfig;
    }

    public function testJobAsync()
    {
        $config = $this->getConfig()['examples']['MultiAudioExample'];
        $apiKey = $this->getApiKey();
        if ($apiKey == null)
        {
            $apiKey = 'INSERT YOU API KEY HERE';
        }
        $client = new BitmovinClient($apiKey);

        $jobConfig = $this->createJob($config);

        $jobContainer = $client->startJob($jobConfig);
        do
        {
            $allFinished = true;
            $client->updateEncodingJobStatus($jobContainer);
            foreach ($jobContainer->encodingContainers as $encodingContainer)
            {
                self::assertNotEquals(Status::ERROR, $encodingContainer->status);
                if ($encodingContainer->status != Status::FINISHED)
                {
                    $allFinished = false;
                }
            }
            sleep(1);
        } while (!$allFinished);
        self::assertEquals(Status::FINISHED, $client->createDashManifest($jobContainer));
        self::assertEquals(Status::FINISHED, $client->createHlsManifest($jobContainer));
    }

}