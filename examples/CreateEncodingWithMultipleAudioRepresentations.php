<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\BitmovinClient;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\EncodingProfileConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsConfigurationAudioVideoGroup;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\HttpInput;
use Bitmovin\output\SftpOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new BitmovinClient('INSERT YOUR API KEY HERE');

// CONFIGURATION
$videoInputPath = 'http://eu-storage.bitcodin.com/inputs/Sintel.2010.720p.mkv';
$sftp_host = 'INSERT YOUR SFTP HOST HERE';
$sftp_username = 'INSERT YOUR SFTP USERNAME HERE';
$sftp_password = 'INSERT YOUR SFTP PASSWORD HERE';
$sftp_prefix = 'path/to/your/output/destination/';

$encodingProfile = new EncodingProfileConfig();
$encodingProfile->name = 'Test Encoding';
$encodingProfile->cloudRegion = CloudRegion::GOOGLE_EUROPE_WEST_1;
$encodingProfile->encoderVersion = 'STABLE';

// CREATE VIDEO STREAM CONFIG FOR 1080p
$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new HttpInput($videoInputPath);
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4800000;
$videoStreamConfig_1080->rate = 24.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

// CREATE VIDEO STREAM CONFIG FOR 720p
$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new HttpInput($videoInputPath);
$videoStreamConfig_720->height = 720;
$videoStreamConfig_720->bitrate = 2400000;
$videoStreamConfig_1080->rate = 24.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

// CREATE VIDEO STREAM CONFIG FOR 480p
$videoStreamConfig_480 = new H264VideoStreamConfig();
$videoStreamConfig_480->input = new HttpInput($videoInputPath);
$videoStreamConfig_480->height = 480;
$videoStreamConfig_480->bitrate = 1200000;
$videoStreamConfig_1080->rate = 24.0;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_480;

$audioConfig_128 = new AudioStreamConfig();
$audioConfig_128->input = new HttpInput($videoInputPath);
$audioConfig_128->bitrate = 128000;
$audioConfig_128->rate = 48000;
$audioConfig_128->name = 'English';
$audioConfig_128->lang = 'en';
$encodingProfile->audioStreamConfigs[] = $audioConfig_128;

$audioConfig_64 = new AudioStreamConfig();
$audioConfig_64->input = new HttpInput($videoInputPath);
$audioConfig_64->bitrate = 64000;
$audioConfig_64->rate = 48000;
$audioConfig_64->name = 'English';
$audioConfig_64->lang = 'en';
$encodingProfile->audioStreamConfigs[] = $audioConfig_64;

$jobConfig = new JobConfig();
$jobConfig->output = new SftpOutput($sftp_host, $sftp_username, $sftp_password, $sftp_prefix);
$jobConfig->encodingProfile = $encodingProfile;
$jobConfig->outputFormat[] = new DashOutputFormat();


$hlsConfiguration = new HlsOutputFormat();

$lowQualityAudioVideoGroup = new HlsConfigurationAudioVideoGroup();
$lowQualityAudioVideoGroup->audioStreams = array($audioConfig_64);
$lowQualityAudioVideoGroup->videoStreams = array($videoStreamConfig_480);
$hlsConfiguration->audioVideoGroups[] = $lowQualityAudioVideoGroup;

$highQualityAudioVideoGroup = new HlsConfigurationAudioVideoGroup();
$highQualityAudioVideoGroup->audioStreams = array($audioConfig_128);
$highQualityAudioVideoGroup->videoStreams = array($videoStreamConfig_720, $videoStreamConfig_1080);
$hlsConfiguration->audioVideoGroups[] = $highQualityAudioVideoGroup;

$jobConfig->outputFormat[] = $hlsConfiguration;

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);
