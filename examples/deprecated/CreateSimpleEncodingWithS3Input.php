<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\S3Input;
use Bitmovin\output\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'INSERT YOUR AWS INPUT ACCESS KEY HERE';
$s3InputSecretKey = 'INSERT YOUR AWS INPUT SECRET KEY HERE';
$s3InputBucketName = "INSERT YOUR AWS INPUT BUCKET NAME HERE";
$s3InputPrefix = "prefix/to/your/input/file.mp4";
$s3Input = new S3Input($s3InputAccessKey, $s3InputSecretKey, $s3InputBucketName, $s3InputPrefix);

//S3 OUTPUT CONFIGURATION
$awsAccessKey = 'INSERT YOUR AWS OUTPUT ACCESS KEY HERE';
$awsSecretKey = 'INSERT YOUR AWS OUTPUT SECRET KEY HERE';
$awsBucketName = 'INSERT YOUR AWS OUTPUT BUCKET NAME HERE';
$awsPrefix = 'path/to/your/output/destination/';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'Simple Encoding With direct S3 Input and Output';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig1080 = new H264VideoStreamConfig();
$videoStreamConfig1080->input = $s3Input;
$videoStreamConfig1080->width = 1920;
$videoStreamConfig1080->bitrate = 4800000;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig720 = new H264VideoStreamConfig();
$videoStreamConfig720->input = $s3Input;
$videoStreamConfig720->width = 1280;
$videoStreamConfig720->bitrate = 2400000;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig720;

// CREATE AUDIO STREAM CONFIG
$audioConfig = new AudioStreamConfig();
$audioConfig->input = $s3Input;
$audioConfig->bitrate = 128000;
$audioConfig->rate = 48000;
$audioConfig->name = 'English';
$audioConfig->lang = 'en';
$audioConfig->position = 1;
$encodingProfile->audioStreamConfigs[] = $audioConfig;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = new S3Output($awsAccessKey, $awsSecretKey, $awsBucketName, $awsPrefix);
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE DASH OUTPUT
$jobConfig->outputFormat[] = new DashOutputFormat();
// ENABLE HLS OUTPUT
$jobConfig->outputFormat[] = new HlsOutputFormat();

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);