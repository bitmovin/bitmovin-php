<?php

use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\SelectionMode;
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

$videoStreamConfig_1080 = new H264VideoStreamConfig();
$videoStreamConfig_1080->input = new HttpInput($videoInputPath);
$videoStreamConfig_1080->width = 1920;
$videoStreamConfig_1080->height = 1080;
$videoStreamConfig_1080->bitrate = 4992000;
$videoStreamConfig_1080->rate = 25.0;
$videoStreamConfig_1080->selectionMode = SelectionMode::VIDEO_RELATIVE;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_1080;

$videoStreamConfig_720_HQ = new H264VideoStreamConfig();
$videoStreamConfig_720_HQ->input = new HttpInput($videoInputPath);
$videoStreamConfig_720_HQ->width = 1280;
$videoStreamConfig_720_HQ->height = 720;
$videoStreamConfig_720_HQ->bitrate = 3072000;
$videoStreamConfig_720_HQ->rate = 25.0;
$videoStreamConfig_720_HQ->selectionMode = SelectionMode::VIDEO_RELATIVE;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720_HQ;

$videoStreamConfig_720 = new H264VideoStreamConfig();
$videoStreamConfig_720->input = new HttpInput($videoInputPath);
$videoStreamConfig_720->width = 1280;
$videoStreamConfig_720->height = 720;
$videoStreamConfig_720->bitrate = 2496000;
$videoStreamConfig_720->rate = 25.0;
$videoStreamConfig_720->selectionMode = SelectionMode::VIDEO_RELATIVE;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_720;

$videoStreamConfig_576 = new H264VideoStreamConfig();
$videoStreamConfig_576->input = new HttpInput($videoInputPath);
$videoStreamConfig_576->width = 1024;
$videoStreamConfig_576->height = 576;
$videoStreamConfig_576->bitrate = 1856000;
$videoStreamConfig_576->rate = 25.0;
$videoStreamConfig_576->selectionMode = SelectionMode::VIDEO_RELATIVE;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_576;

$videoStreamConfig_480 = new H264VideoStreamConfig();
$videoStreamConfig_480->input = new HttpInput($videoInputPath);
$videoStreamConfig_480->width = 848;
$videoStreamConfig_480->height = 480;
$videoStreamConfig_480->bitrate = 1216000;
$videoStreamConfig_480->rate = 25.0;
$videoStreamConfig_480->selectionMode = SelectionMode::VIDEO_RELATIVE;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_480;

$videoStreamConfig_360 = new H264VideoStreamConfig();
$videoStreamConfig_360->input = new HttpInput($videoInputPath);
$videoStreamConfig_360->width = 640;
$videoStreamConfig_360->height = 360;
$videoStreamConfig_360->bitrate = 896000;
$videoStreamConfig_360->rate = 25.0;
$videoStreamConfig_360->profile = H264Profile::BASELINE;
$videoStreamConfig_360->selectionMode = SelectionMode::VIDEO_RELATIVE;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_360;

$videoStreamConfig_240 = new H264VideoStreamConfig();
$videoStreamConfig_240->input = new HttpInput($videoInputPath);
$videoStreamConfig_240->width = 424;
$videoStreamConfig_240->height = 240;
$videoStreamConfig_240->bitrate = 576000;
$videoStreamConfig_240->rate = 25.0;
$videoStreamConfig_240->profile = H264Profile::BASELINE;
$videoStreamConfig_240->selectionMode = SelectionMode::VIDEO_RELATIVE;
$encodingProfile->videoStreamConfigs[] = $videoStreamConfig_240;

$audioConfig_128 = new AudioStreamConfig();
$audioConfig_128->input = new HttpInput($videoInputPath);
$audioConfig_128->bitrate = 128000;
$audioConfig_128->rate = 48000;
$audioConfig_128->name = 'English';
$audioConfig_128->lang = 'en';
$audioConfig_128->selectionMode = SelectionMode::AUDIO_RELATIVE;
$encodingProfile->audioStreamConfigs[] = $audioConfig_128;

$audioConfig_64 = new AudioStreamConfig();
$audioConfig_64->input = new HttpInput($videoInputPath);
$audioConfig_64->bitrate = 64000;
$audioConfig_64->rate = 48000;
$audioConfig_64->name = 'English';
$audioConfig_64->lang = 'en';
$audioConfig_64->selectionMode = SelectionMode::AUDIO_RELATIVE;
$encodingProfile->audioStreamConfigs[] = $audioConfig_64;

$jobConfig = new JobConfig();
$jobConfig->output = new SftpOutput($sftp_host, $sftp_username, $sftp_password, $sftp_prefix);
$jobConfig->encodingProfile = $encodingProfile;
$jobConfig->outputFormat[] = new DashOutputFormat();


$hlsConfiguration = new HlsOutputFormat();

$lowQualityAudioVideoGroup = new HlsConfigurationAudioVideoGroup();
$lowQualityAudioVideoGroup->audioStreams = array($audioConfig_64);
$lowQualityAudioVideoGroup->videoStreams = array($videoStreamConfig_240, $videoStreamConfig_360, $videoStreamConfig_480, $videoStreamConfig_576, $videoStreamConfig_720);
$hlsConfiguration->audioVideoGroups[] = $lowQualityAudioVideoGroup;

$highQualityAudioVideoGroup = new HlsConfigurationAudioVideoGroup();
$highQualityAudioVideoGroup->audioStreams = array($audioConfig_128);
$highQualityAudioVideoGroup->videoStreams = array($videoStreamConfig_720_HQ, $videoStreamConfig_1080);
$hlsConfiguration->audioVideoGroups[] = $highQualityAudioVideoGroup;

$jobConfig->outputFormat[] = $hlsConfiguration;

// RUN JOB AND WAIT UNTIL IT HAS FINISHED
$client->runJobAndWaitForCompletion($jobConfig);
