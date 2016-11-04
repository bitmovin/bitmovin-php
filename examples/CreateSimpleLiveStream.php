<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\model\encodings\helper\LiveEncodingDetails;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\LiveStreamJobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\RtmpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/vendor/autoload.php';

$client = new BitmovinClient('YOUR API KEY');

// CONFIGURATION
$config = array();
$config['accessKey']  = 'YOUR GCS ACCESS KEY';
$config['secretKey']  = 'YOUR GCS SECRET KEY';
$config['bucketName'] = 'YOUR GCS BUCKET NAME';
$config['prefix']     = 'path/to/your/output/destination/';
$config['streamKey']  = 'YOUR STREAM KEY';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'LIVE-ENCODING-DEMO-SIMPLE';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new RtmpInput();
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4800000;
$videoStreamConfig_1080->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new RtmpInput();
$videoStreamConfig_720->width = 1280;
$videoStreamConfig_720->height = 720;
$videoStreamConfig_720->bitrate = 2400000;
$videoStreamConfig_720->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

// CREATE AUDIO STREAM CONFIG
$audioConfig = new AudioStreamConfig();
$audioConfig->input = new RtmpInput();
$audioConfig->bitrate = 128000;
$audioConfig->rate = 48000;
$audioConfig->name = 'English';
$audioConfig->lang = 'en';
$audioConfig->position = 1;
$encodingProfile->audioStreamConfigs[] = $audioConfig;

// CREATE JOB CONFIG
$jobConfig = new LiveStreamJobConfig();
// ASSIGN STREAM KEY
$jobConfig->streamKey = $config['streamKey'];
// ASSIGN OUTPUT
$jobConfig->output = new GcsOutput($config['accessKey'], $config['secretKey'], $config['bucketName'], $config['prefix']);
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE DASH OUTPUT
$jobConfig->outputFormat[] = new DashOutputFormat();

// START LIVE STREAM
$jobContainer = $client->startJob($jobConfig);

// WAIT UNTIL LIVE STREAM IS RUNNING
$client->waitForJobsToStart($jobContainer);

// RETRIEVE LIVE STREAM DATA WHEN AVAILABLE
$liveEncodingDetailsArray = $client->getLiveStreamDataWhenAvailable($jobContainer);
foreach ($liveEncodingDetailsArray as $liveEncodingDetails)
{
    if ($liveEncodingDetails instanceof LiveEncodingDetails)
        print 'Live stream ' . $liveEncodingDetails->getStreamKey() . ' is running with IP ' . $liveEncodingDetails->getEncoderIp();
}

// STOP LIVE STREAM
$client->stopEncodings($jobContainer);

// WAIT UNTIL THE LIVE STREAM IS FINISHED
$client->waitForJobsToFinish($jobContainer);
