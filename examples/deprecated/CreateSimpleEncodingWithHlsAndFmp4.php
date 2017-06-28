<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\HlsFMP4OutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\S3Output;

require_once __DIR__ . '/../../vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CREATE INPUT CONFIGURATION
$videoUrl = 'http://example.com/path/to/your/movie.mp4';
$input = new HttpInput($videoUrl);

// CREATE OUTPUT CONFIGURATION
$s3AccessKey = 'INSERT YOUR S3 ACCESS KEY HERE';
$s3SecretKey = 'INSERT YOUR S3 SECRET KEY HERE';
$s3BucketName = 'INSERT YOUR S3 BUCKET NAME HERE';
$s3Prefix = 'path/to/your/output/destination/';
$s3Output = new S3Output($s3AccessKey, $s3SecretKey, $s3BucketName, $s3Prefix);

// CREATE ENCODING PROFILE
$encodingProfileConfig = new EncodingProfileConfig();
$encodingProfileConfig->name = 'Test Encoding FMP4';
$encodingProfileConfig->cloudRegion = CloudRegion::AWS_EU_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig1080 = new H264VideoStreamConfig();
$videoStreamConfig1080->input = $input;
$videoStreamConfig1080->width = 1920;
$videoStreamConfig1080->height = 1080;
$videoStreamConfig1080->bitrate = 4800000;
$videoStreamConfig1080->rate = 25.0;
$encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig720 = new H264VideoStreamConfig();
$videoStreamConfig720->input = $input;
$videoStreamConfig720->width = 1280;
$videoStreamConfig720->height = 720;
$videoStreamConfig720->bitrate = 2400000;
$videoStreamConfig720->rate = 25.0;
$encodingProfileConfig->videoStreamConfigs[] = $videoStreamConfig720;

// CREATE AUDIO STREAM CONFIG
$audioStreamConfig = new AudioStreamConfig();
$audioStreamConfig->input = $input;
$audioStreamConfig->bitrate = 128000;
$audioStreamConfig->rate = 48000;
$audioStreamConfig->name = 'English';
$audioStreamConfig->lang = 'en';
$audioStreamConfig->position = 1;
$encodingProfileConfig->audioStreamConfigs[] = $audioStreamConfig;

// CREATE OUTPUT FORMAT COLLECTION
$outputFormats = array();
$outputFormats[] = new HlsFMP4OutputFormat();

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = $s3Output;
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfileConfig;
// ASSIGN SELECTED OUTPUT FORMATS
$jobConfig->outputFormat = $outputFormats;

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);