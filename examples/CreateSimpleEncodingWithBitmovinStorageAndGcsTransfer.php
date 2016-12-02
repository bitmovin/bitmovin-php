<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\TransferConfig;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\BitmovinS3Output;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

//TRANSFER OUTPUT CONFIGURATION
$transferGcsAccessKey = 'INSERT YOUR TRANSFER GCS OUTPUT ACCESS KEY HERE';
$transferGcsSecretKey = 'INSERT YOUR TRANSFER GCS OUTPUT SECRET KEY HERE';
$transferGcsBucketName = 'INSERT YOUR TRANSFER GCS OUTPUT BUCKET NAME HERE';
$transferGcsPrefix = 'path/to/your/transfer/output/destination/';

// CONFIGURATION
$videoInputPath = 'http://eu-storage.bitcodin.com/inputs/Sintel.2010.720p.mkv';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'Test Encoding';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new HttpInput($videoInputPath);
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4800000;
$videoStreamConfig_1080->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new HttpInput($videoInputPath);
$videoStreamConfig_720->width = 1280;
$videoStreamConfig_720->height = 720;
$videoStreamConfig_720->bitrate = 2400000;
$videoStreamConfig_720->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

// CREATE AUDIO STREAM CONFIG
$audioConfig = new AudioStreamConfig();
$audioConfig->input = new HttpInput($videoInputPath);
$audioConfig->bitrate = 128000;
$audioConfig->rate = 48000;
$audioConfig->name = 'English';
$audioConfig->lang = 'en';
$audioConfig->position = 1;
$encodingProfile->audioStreamConfigs[] = $audioConfig;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
/*$bitmovinGcsOutput = new BitmovinGcsOutput(CloudRegion::GOOGLE_EUROPE_WEST_1);
$bitmovinGcsOutput ->prefix = "your/preferred/path/";*/
$bitmovinS3Output = new BitmovinS3Output(CloudRegion::AWS_EU_WEST_1);
$bitmovinS3Output->prefix = "your/preferred/path/";
$jobConfig->output = $bitmovinS3Output;
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE DASH OUTPUT
$jobConfig->outputFormat[] = new DashOutputFormat();
// ENABLE HLS OUTPUT
$jobConfig->outputFormat[] = new HlsOutputFormat();

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$jobContainer = $client->runJobAndWaitForCompletion($jobConfig);

//==================================================================================================================

// CREATE TRANSFER CONFIG
$transferConfig = new TransferConfig();
$transferConfig->jobContainer = $jobContainer;
$transferConfig->output = new GcsOutput($transferGcsAccessKey, $transferGcsSecretKey, $transferGcsBucketName, $transferGcsPrefix);

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$transferContainer = $client->runTransferJobAndWaitForCompletion($transferConfig);