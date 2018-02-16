<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\encodings\ConditionAttribute;
use Bitmovin\api\enum\encodings\ConditionOperator;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\condition\Condition;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\enum\muxing\StreamConditionsMode;

require_once __DIR__ . '/../vendor/autoload.php';

$bitmovinApiKey = 'YOUR_BITMOVIN_API_KEY';
$cloudRegion = CloudRegion::AUTO;

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR_ACCESS_KEY';
$s3InputSecretKey = 'YOUR_SECRET_KEY';
$s3InputBucketName = 'YOUR_BUCKETNAME';
$videoInputPath = 'path/to/your/inputfile.mp4';

$s3OutputAccessKey = 'YOUR_ACCESS_KEY';
$s3OutputSecretKey = 'YOUR_SECRET_KEY';
$s3OutputBucketName = 'YOUR_BUCKETNAME';
$outputPath = 'path/to/your/output/destination/';

$videoEncodingProfiles = array(
    array("height" => 1080, "bitrate" => 5800000, "profile" => H264Profile::HIGH),
    array("height" => 1080, "bitrate" => 4300000, "profile" => H264Profile::HIGH),
    array("height" => 720, "bitrate" => 3000000, "profile" => H264Profile::HIGH),
    array("height" => 720, "bitrate" => 2300000, "profile" => H264Profile::HIGH),
    array("height" => 576, "bitrate" => 1500000, "profile" => H264Profile::HIGH),
    array("height" => 432, "bitrate" => 1000000, "profile" => H264Profile::HIGH),
    array("height" => 360, "bitrate" => 750000, "profile" => H264Profile::HIGH),
    array("height" => 288, "bitrate" => 550000, "profile" => H264Profile::HIGH),
    array("height" => 216, "bitrate" => 375000, "profile" => H264Profile::HIGH),
    array("height" => 216, "bitrate" => 240000, "profile" => H264Profile::HIGH)
);

$audioEncodingProfiles = array(
    array("bitrate" => 128000)
);

// ==================================================================================================================

// CREATE API CLIENT
$apiClient = new ApiClient($bitmovinApiKey);

// CREATE ENCODING
$encoding = new Encoding('Encoding with stream conditions - drop stream');
$encoding->setCloudRegion($cloudRegion);
$encoding = $apiClient->encodings()->create($encoding);

// CREATE INPUT
$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$s3Input = $apiClient->inputs()->s3()->create($s3Input);

// CREATE OUTPUT
$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$s3Output = $apiClient->outputs()->s3()->create($s3Output);

// CREATE AUDIO / VIDEO INPUT STREAMS
$inputStreamVideo = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);
$inputStreamAudio = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

// CREATE VIDEO CODEC CONFIGURATIONS
$videoEncodingConfigs = array();
foreach ($videoEncodingProfiles as $videoEncodingProfile)
{
    $encodingProfileName = "h264_" . $videoEncodingProfile["bitrate"];
    $videoEncodingConfig = array();
    $videoEncodingConfig['profile'] = $videoEncodingProfile;
    $videoEncodingConfig['codec'] = createH264VideoCodecConfiguration($apiClient, $encodingProfileName, $videoEncodingProfile["profile"], $videoEncodingProfile["bitrate"], null, $videoEncodingProfile["height"]);
    $videoEncodingConfigs[] = $videoEncodingConfig;
}

// CREATE AUDIO CODEC CONFIGURATIONS
$audioEncodingConfigs = array();
foreach ($audioEncodingProfiles as $videoEncodingProfile)
{
    $encodingProfileName = "aac_" . $videoEncodingProfile["bitrate"];
    $audioEncodingConfig = array();
    $audioEncodingConfig['profile'] = $videoEncodingProfile;
    $audioEncodingConfig['codec'] = createAACAudioCodecConfiguration($apiClient, $encodingProfileName, $videoEncodingProfile["bitrate"]);;
    $audioEncodingConfigs[] = $audioEncodingConfig;
}

// CREATE VIDEO STREAMS
foreach ($videoEncodingConfigs as $key => $videoEncodingConfig)
{
    $videoEncodingProfile = $videoEncodingConfig['profile'];

    // CREATE VIDEO STREAM
    $videoStream = new Stream($videoEncodingConfig['codec'], array($inputStreamVideo));
    $videoStream->setConditions(new Condition(ConditionAttribute::HEIGHT, ConditionOperator::GREATER_THAN_OR_EQUAL_TO, strval($videoEncodingProfile['height'])));
    $videoEncodingConfigs[$key]['stream'] = $apiClient->encodings()->streams($encoding)->create($videoStream);
}

// CREATE AUDIO STREAMS
foreach ($audioEncodingConfigs as $key => $audioEncodingConfig)
{
    // CREATE AUDIO STREAM
    $audioStream = new Stream($audioEncodingConfig['codec'], array($inputStreamAudio));
    $audioStream->setConditions(new Condition(ConditionAttribute::INPUTSTREAM, ConditionOperator::EQUAL, "true"));
    $audioEncodingConfigs[$key]['stream'] = $apiClient->encodings()->streams($encoding)->create($audioStream);
}

// CREATE MUXINGS
foreach ($audioEncodingConfigs as $audioEncodingConfig)
{
    $audioStream = $audioEncodingConfig['stream'];

    foreach ($videoEncodingConfigs as $videoEncodingConfig)
    {
        $videoStream = $videoEncodingConfig['stream'];
        $videoHeight = $videoEncodingConfig['profile']['height'];

        createMp4Muxing($apiClient, $encoding, array($videoStream, $audioStream), $s3Output, $outputPath, strval($videoHeight) . '.mp4', AclPermission::ACL_PUBLIC_READ, StreamConditionsMode::DROP_STREAM);
    }
}

$apiClient->encodings()->start($encoding);

do
{
    $status = $apiClient->encodings()->status($encoding);
    var_dump(date_create(null, new DateTimeZone('UTC'))->getTimestamp() . ": " . $status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

if ($status->getStatus() === Status::ERROR)
{
    var_dump("Encoding failed!");
    exit();
}

/**
 * @param ApiClient $apiClient
 * @param string    $name
 * @param string    $profile
 * @param integer   $bitrate
 * @param float     $rate
 * @param integer   $width
 * @param integer   $height
 * @return H264VideoCodecConfiguration
 * @throws BitmovinException
 */
function createH264VideoCodecConfiguration($apiClient, $name, $profile, $bitrate, $width = null, $height = null, $rate = null)
{
    $codecConfigVideo = new H264VideoCodecConfiguration($name, $profile, $bitrate, $rate);
    $codecConfigVideo->setDescription($bitrate . '_' . $name);
    $codecConfigVideo->setWidth($width);
    $codecConfigVideo->setHeight($height);
    return $apiClient->codecConfigurations()->videoH264()->create($codecConfigVideo);
}

/**
 * @param ApiClient $apiClient
 * @param string    $name
 * @param integer   $bitrate
 * @param integer   $rate
 * @return AACAudioCodecConfiguration
 * @throws BitmovinException
 */
function createAACAudioCodecConfiguration($apiClient, $name, $bitrate, $rate = null)
{
    $codecConfigAudio = new AACAudioCodecConfiguration($name, $bitrate, $rate);
    return $apiClient->codecConfigurations()->audioAAC()->create($codecConfigAudio);
}

/**
 * @param ApiClient $apiClient
 * @param Encoding $encoding
 * @param array $streams
 * @param Output $output
 * @param string $outputPath
 * @param string $filename
 * @param string $outputAcl
 * @param string $streamConditionMode
 * @return MP4Muxing
 * @throws BitmovinException
 */
function createMp4Muxing($apiClient, $encoding, $streams, $output, $outputPath, $filename, $outputAcl = AclPermission::ACL_PUBLIC_READ, $streamConditionMode = null)
{
    $muxingStreams = array();

    foreach ($streams as $stream)
    {
        $muxingStream = new MuxingStream();
        $muxingStream->setStreamId($stream->getId());

        $muxingStreams[] = $muxingStream;
    }

    $encodingOutputs = null;

    $mp4Muxing = new MP4Muxing();
    $mp4Muxing->setFilename($filename);

    $encodingOutput = new EncodingOutput($output);
    $encodingOutput->setOutputPath($outputPath);
    $encodingOutput->setAcl(array(new Acl($outputAcl)));

    if (!is_null($streamConditionMode))
    {
        $mp4Muxing->setStreamConditionsMode($streamConditionMode);
    }

    $mp4Muxing->setOutputs(array($encodingOutput));
    $mp4Muxing->setStreams($muxingStreams);

    return $apiClient->encodings()->muxings($encoding)->mp4Muxing()->create($mp4Muxing);
}