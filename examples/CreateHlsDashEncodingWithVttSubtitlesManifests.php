<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\ExternalSubtitleFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '../vendor/autoload.php';

$client = new BitmovinClient('INSERT_YOUR_API_KEY');

// CREATE INPUT CONFIGURATION
$videoUrl = 'http://example.com/path/to/your/movie.mp4';
$input = new HttpInput($videoUrl);

// CREATE OUTPUT CONFIGURATION
$gcsAccessKey = 'INSERT_YOUR_ACCESS_KEY';
$gcsSecretKey = 'INSERT_YOUR_SECRET_KEY';
$gcsBucketName = 'INSERT_YOUR_BUCKET_NAME';
$gcsPrefix = 'path/to/your/output/destination/';
$gcsOutput = new GcsOutput($gcsAccessKey, $gcsSecretKey, $gcsBucketName, $gcsPrefix);

// CREATE ENCODING PROFILE
$encodingProfileConfig = new EncodingProfileConfig();
$encodingProfileConfig->name = 'Test Encoding FMP4';
$encodingProfileConfig->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

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

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = $gcsOutput;
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfileConfig;

// CREATING SUBTITLES
$subTitleFormat = new ExternalSubtitleFormat();
$subTitleFormat->subtitleUrls[] = "https://path/to/your/subtitle.vtt";
$subTitleFormat->lang = "english";

// ENABLE DASH OUTPUT
$dashOutputFormat = new DashOutputFormat();
$dashOutputFormat->vttSubtitles[] = $subTitleFormat;
$jobConfig->outputFormat[] = $dashOutputFormat;

// ENABLE HLS OUTPUT
$hlsOutputFormat = new HlsOutputFormat();
$hlsOutputFormat->vttSubtitles[] = $subTitleFormat;
$jobConfig->outputFormat[] = $hlsOutputFormat;

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$jobContainer = $client->runJobAndWaitForCompletion($jobConfig);