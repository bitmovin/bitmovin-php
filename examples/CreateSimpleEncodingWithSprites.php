<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\images\SpriteConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CONFIGURATION
$videoInputPath = 'http://eu-storage.bitcodin.com/inputs/Sintel.2010.720p.mkv';
$gcsAccessKey = 'INSERT YOUR GCS OUTPUT ACCESS KEY HERE';
$gcsSecretKey = 'INSERT YOUR GCS OUTPUT SECRET KEY HERE';
$gcsBucketName = 'INSERT YOUR GCS OUTPUT BUCKET NAME HERE';
$gcsPrefix = 'path/to/your/output/destination/';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'Test Encoding';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;


// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig1080 = new H264VideoStreamConfig();
$videoStreamConfig1080->input = new HttpInput($videoInputPath);
$videoStreamConfig1080->width = 1920;
$videoStreamConfig1080->height = 816;
$videoStreamConfig1080->bitrate = 4800000;
$videoStreamConfig1080->rate = 25.0;

$jpgSpriteConfig = new SpriteConfig(640, 360, "fullhd_640x360.jpg", "fullhd_640x360.vtt");
$jpgSpriteConfig->distance = 10;
$pngSpriteConfig = new SpriteConfig(640, 360, "fullhd_640x360.png", "fullhd_640x360.vtt");
$pngSpriteConfig->distance = 10;
$videoStreamConfig1080->spriteConfigs[] = $jpgSpriteConfig;
$videoStreamConfig1080->spriteConfigs[] = $pngSpriteConfig;

$encodingProfile->videoStreamConfigs[] = $videoStreamConfig1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig720 = new H264VideoStreamConfig();
$videoStreamConfig720->input = new HttpInput($videoInputPath);
$videoStreamConfig720->width = 1280;
$videoStreamConfig720->height = 544;
$videoStreamConfig720->bitrate = 2400000;
$videoStreamConfig720->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig720;

// CREATE AUDIO STREAM CONFIG
$audioStreamConfig = new AudioStreamConfig();
$audioStreamConfig->input = new HttpInput($videoInputPath);
$audioStreamConfig->bitrate = 128000;
$audioStreamConfig->rate = 48000;
$audioStreamConfig->name = 'English';
$audioStreamConfig->lang = 'en';
$audioStreamConfig->position = 1;
$encodingProfile->audioStreamConfigs[] = $audioStreamConfig;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = new GcsOutput($gcsAccessKey, $gcsSecretKey, $gcsBucketName, $gcsPrefix);
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE DASH OUTPUT
$jobConfig->outputFormat[] = new DashOutputFormat();
// ENABLE HLS OUTPUT
$jobConfig->outputFormat[] = new HlsOutputFormat();

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);