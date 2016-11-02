<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\LiveStreamJobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CONFIGURATION
$config = array();
$config['videoInputPath'] = 'http://eu-storage.bitcodin.com/inputs/Sintel.2010.720p.mkv';
$config['accessKey'] = 'YOUR GCS ACCESS KEY';
$config['secretKey'] = 'YOUR GCS SECRET KEY';
$config['bucketName'] = 'YOUR GCS BUCKET NAME';
$config['prefix'] = 'path/to/your/output/destination/';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'Test Encoding';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new HttpInput($config['videoInputPath']);
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4800000;
$videoStreamConfig_1080->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new HttpInput($config['videoInputPath']);
$videoStreamConfig_720->width = 1280;
$videoStreamConfig_720->height = 720;
$videoStreamConfig_720->bitrate = 2400000;
$videoStreamConfig_720->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

// CREATE AUDIO STREAM CONFIG
$audioConfig = new AudioStreamConfig();
$audioConfig->input = new HttpInput($config['videoInputPath']);
$audioConfig->bitrate = 128000;
$audioConfig->rate = 48000;
$audioConfig->name = 'English';
$audioConfig->lang = 'en';
$audioConfig->position = 1;
$encodingProfile->audioStreamConfigs[] = $audioConfig;

// CREATE JOB CONFIG
$jobConfig = new LiveStreamJobConfig();
// ASSIGN STREAM KEY
$jobConfig->streamKey = 'YOUR STREAM KEY';
// ASSIGN OUTPUT
$jobConfig->output = new GcsOutput($config['accessKey'], $config['secretKey'], $config['bucketName'], $config['prefix']);
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE DASH OUTPUT
$jobConfig->outputFormat[] = new DashOutputFormat();
// ENABLE HLS OUTPUT
$jobConfig->outputFormat[] = new HlsOutputFormat();

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);