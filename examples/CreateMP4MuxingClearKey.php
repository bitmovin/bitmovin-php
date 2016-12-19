<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\drm\ClearKeyDrm;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\ProgressiveMp4OutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CONFIGURATION
$videoInputPath = 'INSERT YOUR HTTP VIDEO INPUT PATH HERE';
$gcs_accessKey = 'INSERT YOUR GCS OUTPUT ACCESS KEY HERE';
$gcs_secretKey = 'INSERT YOUR GCS OUTPUT SECRET KEY HERE';
$gcs_bucketName = 'INSERT YOUR GCS OUTPUT BUCKET NAME HERE';
$gcs_prefix = 'path/to/your/output/destination/';
$clearkey_key = 'CLEARKEY KEY';
$clearkey_kid = 'CLEARKEY KID';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'MP4-Muxing-Example';
$encodingProfile->cloudRegion = CloudRegion::AWS_EU_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new HttpInput($videoInputPath);
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4800000;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

// CREATE AUDIO STREAM CONFIG
$audioConfig = new AudioStreamConfig();
$audioConfig->input = new HttpInput($videoInputPath);
$audioConfig->bitrate = 128000;
$audioConfig->name = 'English';
$audioConfig->lang = 'en';
$audioConfig->position = 1;
$encodingProfile->audioStreamConfigs[] = $audioConfig;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = new GcsOutput($gcs_accessKey, $gcs_secretKey, $gcs_bucketName, $gcs_prefix);
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;

// ADD PROGRESSIVE MP4 OUTPUTS
$mp4Muxing1080 = new ProgressiveMp4OutputFormat();
$mp4Muxing1080->fileName = "1080p_4800kbps.mp4";
$mp4Muxing1080->streamConfigs = array($videoStreamConfig_1080, $audioConfig);
$mp4Muxing1080->clearKey = new ClearKeyDrm($clearkey_key, $clearkey_kid);
$jobConfig->outputFormat[] = $mp4Muxing1080;

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);