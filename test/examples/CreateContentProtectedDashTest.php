<?php


namespace Bitmovin\test\examples;

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\Status;
use Bitmovin\api\model\encodings\drms\cencSystems\CencMarlin;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\drm\cenc\CencPlayReady;
use Bitmovin\configs\drm\cenc\CencWidevine;
use Bitmovin\configs\drm\CencDrm;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;
use Bitmovin\test\AbstractBitmovinApiTest;

class CreateContentProtectedDashTest extends AbstractBitmovinApiTest
{

    public function testJob()
    {
        $config = $this->getConfig();
        $config = $config['examples']['CreateContentProtectedDashTest'];
        $apiKey = $this->getApiKey();
        if ($apiKey == null)
        {
            $apiKey = 'INSERT YOU API KEY HERE';
        }
        $client = new BitmovinClient($apiKey);

        $jobConfig = $this->createJob($config);

        $client->runJobAndWaitForCompletion($jobConfig);
    }

    public function testJobAsync()
    {
        $config = $this->getConfig();
        $config = $config['examples']['CreateContentProtectedDashTest'];
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
            $config['key'] = 'INSERT YOUR CENC DRM KEY HERE';
            $config['kid'] = 'INSERT YOUR CENC DRM KID HERE';
            $config['widevine_pssh'] = 'INSERT YOUR CENC WIDEVINE PSSH HERE';
            $config['playready_laurl'] = 'INSERT YOUR CENC PLAYREADY LAURL HERE';
        }

        $encodingProfile = new EncodingProfileConfig();
        $encodingProfile->name = 'CreateContentProtectedDashTest';
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

        $videoStreamConfig_480 = new H264VideoStreamConfig();
        $videoStreamConfig_480->input = new HttpInput($config['videoInputPath']);
        $videoStreamConfig_480->width = 854;
        $videoStreamConfig_480->height = 480;
        $videoStreamConfig_480->bitrate = 1200000;
        $videoStreamConfig_480->rate = 25.0;
        $encodingProfile->videoStreamConfigs[] = $videoStreamConfig_480;

        $videoStreamConfig_360 = new H264VideoStreamConfig();
        $videoStreamConfig_360->input = new HttpInput($config['videoInputPath']);
        $videoStreamConfig_360->width = 640;
        $videoStreamConfig_360->height = 360;
        $videoStreamConfig_360->bitrate = 800000;
        $videoStreamConfig_360->rate = 25.0;
        $encodingProfile->videoStreamConfigs[] = $videoStreamConfig_360;

        $videoStreamConfig_240 = new H264VideoStreamConfig();
        $videoStreamConfig_240->input = new HttpInput($config['videoInputPath']);
        $videoStreamConfig_240->width = 426;
        $videoStreamConfig_240->height = 240;
        $videoStreamConfig_240->bitrate = 400000;
        $videoStreamConfig_240->rate = 25.0;
        $encodingProfile->videoStreamConfigs[] = $videoStreamConfig_240;

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
        $jobConfig->output->prefix .= time() . '_' . uniqid() . '/';
        $jobConfig->encodingProfile = $encodingProfile;
        $outputFormat = new DashOutputFormat();
        $outputFormat->cenc = new CencDrm($config['key'], $config['kid']);
        $outputFormat->cenc->setWidevine(new CencWidevine($config['widevine_pssh']));
        $outputFormat->cenc->setPlayReady(new CencPlayReady($config['playready_laurl']));
        $outputFormat->cenc->setMarlin(new CencMarlin());
        $jobConfig->outputFormat[] = $outputFormat;
        return $jobConfig;
    }

}