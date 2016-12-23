<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\ProgressiveMp4OutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CONFIGURATION
$videoInputPath = 'INSERT YOUR INPUT URL HERE';
$gcsAccessKey = 'INSERT YOUR GCS OUTPUT ACCESS KEY HERE';
$gcsSecretKey = 'INSERT YOUR GCS OUTPUT SECRET KEY HERE';
$gcsBucketName = 'INSERT YOUR GCS OUTPUT BUCKET NAME HERE';
$gcsPrefix = 'path/to/your/output/destination/';

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'MP4-Muxing-Example with MultiAudio';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new HttpInput($videoInputPath);
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4800000;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new HttpInput($videoInputPath);
$videoStreamConfig_720->width = 1280;
$videoStreamConfig_720->height = 720;
$videoStreamConfig_720->bitrate = 2400000;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

// CREATE AUDIO STREAM CONFIG
$englishAudioConfig = new AudioStreamConfig();
$englishAudioConfig->input = new HttpInput($videoInputPath);
$englishAudioConfig->bitrate = 128000;
$englishAudioConfig->name = 'English';
$englishAudioConfig->lang = 'en';
$englishAudioConfig->position = 0; //Selects the first available audio track from your input file
$englishAudioConfig->selectionMode = SelectionMode::AUDIO_RELATIVE;
$encodingProfile->audioStreamConfigs[] = $englishAudioConfig;

// CREATE AUDIO STREAM CONFIG
$germanAudioConfig = new AudioStreamConfig();
$germanAudioConfig->input = new HttpInput($videoInputPath);
$germanAudioConfig->bitrate = 128000;
$germanAudioConfig->name = 'German';
$germanAudioConfig->lang = 'de';
$germanAudioConfig->position = 1; //Selects the second available audio track from your input file
$germanAudioConfig->selectionMode = SelectionMode::AUDIO_RELATIVE;
$encodingProfile->audioStreamConfigs[] = $germanAudioConfig;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = new GcsOutput($gcsAccessKey, $gcsSecretKey, $gcsBucketName, $gcsPrefix);
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;

// ADD PROGRESSIVE MP4 OUTPUTS
$mp4Muxing1080English = new ProgressiveMp4OutputFormat();
$mp4Muxing1080English->fileName = "1080p_4800kbps_multiaudio.mp4";
$mp4Muxing1080English->streamConfigs = array($videoStreamConfig_1080, $germanAudioConfig, $englishAudioConfig);
$jobConfig->outputFormat[] = $mp4Muxing1080English;

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);