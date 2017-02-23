<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\GenericS3Input;
use Bitmovin\output\GenericS3Output;

require_once __DIR__ . "/../vendor/autoload.php";

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CONFIGURATION
//INPUT CONFIGURATION
$scalityInputHost = 's3.yourdomain.com'; // This can also be an ip address of your host where the scality server is running
$scalityInputPort = 50123; // The port on which your scality s3 server is listening
$scalityInputAccessKey = 'YOURSCALITYS3ACCESSKEY'; // This is the access key that is configured either via environment variables or in the scality configuration file. Refer to this guide https://github.com/scality/S3/blob/master/DOCKER.md#access_key-and-secret_key
$scalityInputSecretKey = 'YOURSCALITYS3SECRETKEY'; // This is the secret key configured
$scalityInputBucketName = 'YOURSCALITYS3BUCKETNAME'; // The name of the bucket. Make sure you have created this bucket before
$scalityInputPath = "path/to/your/input/file.mkv"; // This is the path where your files will be transferred to. Subdirectories will be generated automatically

//OUTPUT CONFIGURATION
$scalityOutputHost = 's3.yourdomain.com'; // This can also be an ip address of your host where the scality server is running
$scalityOutputPort = 50123; // The port on which your scality s3 server is listening
$scalityOutputAccessKey = 'YOURSCALITYS3ACCESSKEY'; // This is the access key that is configured either via environment variables or in the scality configuration file. Refer to this guide https://github.com/scality/S3/blob/master/DOCKER.md#access_key-and-secret_key
$scalityOutputSecretKey = 'YOURSCALITYS3SECRETKEY'; // This is the secret key configured
$scalityOutputBucketName = 'YOURSCALITYS3BUCKETNAME'; // The name of the bucket. Make sure you have created this bucket before
$scalityOutputPrefix = "path/to/your/destination/"; // This is the path where your files will be transferred to. Subdirectories will be generated automatically


// CREATE ENCODING PROFILE
$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'Test Encoding';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new GenericS3Input($scalityInputBucketName, $scalityInputAccessKey, $scalityInputSecretKey, $scalityInputHost, $scalityInputPort, $scalityInputPath);
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 816;
$videoStreamConfig_1080->bitrate = 4800000;
$videoStreamConfig_1080->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new GenericS3Input($scalityInputBucketName, $scalityInputAccessKey, $scalityInputSecretKey, $scalityInputHost, $scalityInputPort, $scalityInputPath);
$videoStreamConfig_720->width = 1280;
$videoStreamConfig_720->height = 544;
$videoStreamConfig_720->bitrate = 2400000;
$videoStreamConfig_720->rate = 25.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

// CREATE AUDIO STREAM CONFIG
$audioConfig = new AudioStreamConfig();
$audioConfig->input = new GenericS3Input($scalityInputBucketName, $scalityInputAccessKey, $scalityInputSecretKey, $scalityInputHost, $scalityInputPort, $scalityInputPath);
$audioConfig->bitrate = 128000;
$audioConfig->rate = 48000;
$audioConfig->name = 'English';
$audioConfig->lang = 'en';
$audioConfig->position = 1;
$encodingProfile->audioStreamConfigs[] = $audioConfig;

// CREATE JOB CONFIG
$jobConfig = new JobConfig();

// ASSIGN GCS OUTPUT
$jobConfig->output = new GenericS3Output($scalityOutputAccessKey, $scalityOutputSecretKey, $scalityOutputHost, $scalityOutputPort, $scalityOutputBucketName, $scalityOutputPrefix);
$jobConfig->output->prefix = $gcsPrefix;
// ASSIGN ENCODING PROFILES TO JOB
$jobConfig->encodingProfile = $encodingProfile;
// ENABLE DASH OUTPUT
$jobConfig->outputFormat[] = new DashOutputFormat();
// ENABLE HLS OUTPUT
$jobConfig->outputFormat[] = new HlsOutputFormat();

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$jobContainer = $client->runJobAndWaitForCompletion($jobConfig);