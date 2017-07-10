<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\GcsOutput;

require_once __DIR__ . '/../../vendor/autoload.php';

$client = new BitmovinClient('YOUR BITMOVIN API KEY');

//CREATE AN OUTPUT
$gcsAccessKey = 'YOUR-ACCESS-KEY';
$gcsSecretKey = 'YOUR-SECRET-KEY';
$gcsBucketName = 'YOURBUCKETNAME';
$gcsPrefix = 'PATH/TO/YOUR/OUTPUT-DESTINATION/';
$gcsOutput = new GcsOutput($gcsAccessKey, $gcsSecretKey, $gcsBucketName, $gcsPrefix);

//CREATE AN INPUT
$inputURL = "https://example.com/path/to/your/input-file.mp4";
$httpInput = new HttpInput($inputURL);

// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'HLS compliant content - BlogPost Example';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;
$rate = 29.97; //framerate of your input file
$keyFrameInt = 2; //key frame interval in seconds
$segmentLength = 6; //segment duration in seconds

// CREATE VIDEO STREAM CONFIGS
$encodingProfile->videoStreamConfigs[] = createH264VideoStreamConfig($httpInput, H264Profile::HIGH, 4800000, 1920, null, $rate, $keyFrameInt);
$encodingProfile->videoStreamConfigs[] = createH264VideoStreamConfig($httpInput, H264Profile::HIGH, 3000000, 1280, null, $rate, $keyFrameInt);
$encodingProfile->videoStreamConfigs[] = createH264VideoStreamConfig($httpInput, H264Profile::MAIN, 2000000, 960, null, $rate, $keyFrameInt);
$encodingProfile->videoStreamConfigs[] = createH264VideoStreamConfig($httpInput, H264Profile::MAIN, 1100000, 768, null, $rate, $keyFrameInt);
$encodingProfile->videoStreamConfigs[] = createH264VideoStreamConfig($httpInput, H264Profile::MAIN, 730000, 640, null, $rate, $keyFrameInt);
$encodingProfile->videoStreamConfigs[] = createH264VideoStreamConfig($httpInput, H264Profile::BASELINE, 365000, 480, null, $rate, $keyFrameInt);
$encodingProfile->videoStreamConfigs[] = createH264VideoStreamConfig($httpInput, H264Profile::BASELINE, 145000, 416, null, $rate, $keyFrameInt);

// CREATE AUDIO STREAM CONFIG
$asc160 = new AudioStreamConfig();
$asc160->input = $httpInput;
$asc160->bitrate = 160000;
$asc160->rate = 48000;
$asc160->name = 'English';
$asc160->lang = 'en';
$asc160->position = 1;
$encodingProfile->audioStreamConfigs[] = $asc160;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();
// ASSIGN OUTPUT
$jobConfig->output = $gcsOutput;
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE HLS OUTPUT
$hlsOutput = new HlsOutputFormat();
$hlsOutput->segmentLength = $segmentLength;
$jobConfig->outputFormat[] = $hlsOutput;


// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);

/**
 * @param            $input
 * @param string     $profile
 * @param int        $bitrate
 * @param int        $width
 * @param int|null   $height
 * @param float|null $rate
 * @param int        $keyFrameInterval
 * @return H264VideoStreamConfig
 */
function createH264VideoStreamConfig($input, $profile, $bitrate, $width, $height = null, $rate = null, $keyFrameInterval = 2)
{
    $videoStreamConfig = new H264VideoStreamConfig();
    $videoStreamConfig->input = $input;
    $videoStreamConfig->width = $width;
    $videoStreamConfig->height = $height;
    $videoStreamConfig->bitrate = $bitrate;
    $videoStreamConfig->rate = $rate;
    $videoStreamConfig->profile = $profile;

    if (!is_null($rate))
    {
        $videoStreamConfig->maxGop = intval($rate * $keyFrameInterval);
    }

    return $videoStreamConfig;
}