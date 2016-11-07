<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CONFIGURATION
$videoInputPath = 'INSERT YOUR HTTP VIDEO INPUT PATH HERE';
$audioLang1Path = 'INSERT YOUR HTTP AUDIO LANGUAGE 1 INPUT PATH HERE';
$audioLang2Path = 'INSERT YOUR HTTP AUDIO LANGUAGE 2 INPUT PATH HERE';
$audioLang3Path = 'INSERT YOUR HTTP AUDIO LANGUAGE 3 INPUT PATH HERE';
$gcs_accessKey = 'INSERT YOUR GCS OUTPUT ACCESS KEY HERE';
$gcs_secretKey = 'INSERT YOUR GCS OUTPUT SECRET KEY HERE';
$gcs_bucketName = 'INSERT YOUR GCS OUTPUT BUCKET NAME HERE';
$gcs_prefix = 'INSERT YOUR GCS OUTPUT PREFIX (FOLDER) HERE';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'CreateMultiAudioExample';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

// CREATE VIDEO STREAM CONFIGS
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new HttpInput($videoInputPath);
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4800000;
$videoStreamConfig_1080->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new HttpInput($videoInputPath);
$videoStreamConfig_720->width = 1280;
$videoStreamConfig_720->height = 720;
$videoStreamConfig_720->bitrate = 2400000;
$videoStreamConfig_720->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

$videoStreamConfig_480 = new H264VideoStreamConfig();
$videoStreamConfig_480->input = new HttpInput($videoInputPath);
$videoStreamConfig_480->width = 854;
$videoStreamConfig_480->height = 480;
$videoStreamConfig_480->bitrate = 1200000;
$videoStreamConfig_480->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_480;

$videoStreamConfig_360 = new H264VideoStreamConfig();
$videoStreamConfig_360->input = new HttpInput($videoInputPath);
$videoStreamConfig_360->width = 640;
$videoStreamConfig_360->height = 360;
$videoStreamConfig_360->bitrate = 800000;
$videoStreamConfig_360->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_360;

$videoStreamConfig_240 = new H264VideoStreamConfig();
$videoStreamConfig_240->input = new HttpInput($videoInputPath);
$videoStreamConfig_240->width = 426;
$videoStreamConfig_240->height = 240;
$videoStreamConfig_240->bitrate = 400000;
$videoStreamConfig_240->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_240;

// CREATE AUDIO STREAM CONFIGS
$audioCatalanConfig = new AudioStreamConfig();
$audioCatalanConfig->input = new HttpInput($audioLang1Path);
$audioCatalanConfig->bitrate = 128000;
$audioCatalanConfig->rate = 48000;
$audioCatalanConfig->lang = 'es';
$audioCatalanConfig->name = 'Spanish';
$encodingProfile->audioStreamConfigs[] = $audioCatalanConfig;

$audioDescriptionConfig = new AudioStreamConfig();
$audioDescriptionConfig->input = new HttpInput($audioLang2Path);
$audioDescriptionConfig->bitrate = 128000;
$audioDescriptionConfig->rate = 48000;
$audioDescriptionConfig->lang = 'en';
$audioDescriptionConfig->name = 'English';
$encodingProfile->audioStreamConfigs[] = $audioDescriptionConfig;

$audioEffectsConfig = new AudioStreamConfig();
$audioEffectsConfig->input = new HttpInput($audioLang3Path);
$audioEffectsConfig->bitrate = 128000;
$audioEffectsConfig->rate = 48000;
$audioEffectsConfig->lang = 'de';
$audioEffectsConfig->name = 'German';
$encodingProfile->audioStreamConfigs[] = $audioEffectsConfig;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = new GcsOutput($gcs_accessKey, $gcs_secretKey, $gcs_bucketName, $gcs_prefix);
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE HLS OUTPUT
$jobConfig->outputFormat[] = new HlsOutputFormat();
// ENABLE DASH OUTPUT
$jobConfig->outputFormat[] = new DashOutputFormat();

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);