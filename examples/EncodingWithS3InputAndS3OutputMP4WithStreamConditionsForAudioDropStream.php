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

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');

// CREATE ENCODING
$encoding = new Encoding('Encoding with stream conditions');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$s3Input = $apiClient->inputs()->s3()->create($s3Input);
$videoInputPath = "path/to/your/input/file.mp4";

//or an use existing S3 Input
//$s3Input = $apiClient->inputs()->s3()->getById("s3-input-id");
//$videoInputPath = "path/to/your/input/file.mp4";

$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$s3Output = $apiClient->outputs()->s3()->create($s3Output);
$outputPath = "path/to/your/output-destination/";

//or use existing S3 Output
//$s3Output = $apiClient->outputs()->s3()->getById("s3-output-id");
//$outputPath = "path/to/your/input/file.mp4";

// CREATE VIDEO CODEC CONFIGURATIONS
$codecConfigVideo1080p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo1080p', H264Profile::HIGH, 4800000, null, 1080);
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720p', H264Profile::HIGH, 2400000, null, 720);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo480p', H264Profile::MAIN, 1200000, null, 480);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo360p', H264Profile::MAIN, 800000, null, 360);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo240p', H264Profile::BASELINE, 400000, null, 240);

// or use an existing codec configuration
//$codecConfigVideo1080p = $apiClient->codecConfigurations()->videoH264()->getById("h264-codec-configuration-id");

// CREATE AUDIO CODEC CONFIGURATIONS
$codecConfigAudio128kbit = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000);

// or use an existing codec configuration
//$codecConfigAudio128kbit = $apiClient->codecConfigurations()->audioAAC()->getById("aac-codec-configuration-id");

//CREATE AUDIO / VIDEO INPUT STREAMS
$inputStreamVideo = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);
$inputStreamAudio = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

// CREATE VIDEO STREAMS
$videoStream1080p = new Stream($codecConfigVideo1080p, array($inputStreamVideo));
$videoStream720p = new Stream($codecConfigVideo720p, array($inputStreamVideo));
$videoStream480p = new Stream($codecConfigVideo480p, array($inputStreamVideo));
$videoStream360p = new Stream($codecConfigVideo360p, array($inputStreamVideo));
$videoStream240p = new Stream($codecConfigVideo240p, array($inputStreamVideo));
$videoStream1080p->setConditions(new Condition(ConditionAttribute::HEIGHT, ConditionOperator::GREATER_THAN_OR_EQUAL_TO, "1080"));
$videoStream720p->setConditions(new Condition(ConditionAttribute::HEIGHT, ConditionOperator::GREATER_THAN_OR_EQUAL_TO, "720"));
$videoStream480p->setConditions(new Condition(ConditionAttribute::HEIGHT, ConditionOperator::GREATER_THAN_OR_EQUAL_TO, "480"));
$videoStream360p->setConditions(new Condition(ConditionAttribute::HEIGHT, ConditionOperator::GREATER_THAN_OR_EQUAL_TO, "360"));
$videoStream240p->setConditions(new Condition(ConditionAttribute::HEIGHT, ConditionOperator::GREATER_THAN_OR_EQUAL_TO, "240"));
$videoStream1080p = $apiClient->encodings()->streams($encoding)->create($videoStream1080p);
$videoStream720p = $apiClient->encodings()->streams($encoding)->create($videoStream720p);
$videoStream480p = $apiClient->encodings()->streams($encoding)->create($videoStream480p);
$videoStream360p = $apiClient->encodings()->streams($encoding)->create($videoStream360p);
$videoStream240p = $apiClient->encodings()->streams($encoding)->create($videoStream240p);

// CREATE AUDIO STREAMS
$audioStream128 = new Stream($codecConfigAudio128kbit, array($inputStreamAudio));
$audioStream128->setConditions(new Condition(ConditionAttribute::INPUTSTREAM, ConditionOperator::EQUAL, "true"));
$audioStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

$combinedStreams1080p = array($videoStream1080p, $audioStream128);
$combinedStreams720p = array($videoStream720p, $audioStream128);
$combinedStreams480p = array($videoStream480p, $audioStream128);
$combinedStreams360p = array($videoStream360p, $audioStream128);
$combinedStreams240p = array($videoStream240p, $audioStream128);

// CREATE MP4 MUXINGS
createMp4Muxing($apiClient, $encoding, $combinedStreams1080p, $s3Output, $outputPath, '1080p.mp4', AclPermission::ACL_PUBLIC_READ, StreamConditionsMode::DROP_STREAM);
createMp4Muxing($apiClient, $encoding, $combinedStreams720p, $s3Output, $outputPath, '720p.mp4', AclPermission::ACL_PUBLIC_READ, StreamConditionsMode::DROP_STREAM);
createMp4Muxing($apiClient, $encoding, $combinedStreams480p, $s3Output, $outputPath, '480p.mp4', AclPermission::ACL_PUBLIC_READ, StreamConditionsMode::DROP_STREAM);
createMp4Muxing($apiClient, $encoding, $combinedStreams360p, $s3Output, $outputPath, '360p.mp4', AclPermission::ACL_PUBLIC_READ, StreamConditionsMode::DROP_STREAM);
createMp4Muxing($apiClient, $encoding, $combinedStreams240p, $s3Output, $outputPath, '240p.mp4', AclPermission::ACL_PUBLIC_READ, StreamConditionsMode::DROP_STREAM);

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