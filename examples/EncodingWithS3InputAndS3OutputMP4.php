<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$videoInputPath = "path/to/your/input.mp4";

$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = "YOUR-AWS-S3-BUCKETNAME";

$outputPath = "path/to/your/output-destination/";

$encoding = new Encoding('PHP Encoding');
$encoding->setCloudRegion(CloudRegion::AWS_EU_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);

$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$s3Input = $apiClient->inputs()->s3()->create($s3Input);

$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$s3Output = $apiClient->outputs()->s3()->create($s3Output);

$videoConfigurations = [
    [
        'config' => createH264VideoCodecConfiguration(4800, null, 1080)
    ],
    [
        'config' => createH264VideoCodecConfiguration(2400, null, 720)
    ],
    [
        'config' => createH264VideoCodecConfiguration(1200, null, 480)
    ],
    [
        'config' => createH264VideoCodecConfiguration(800, null, 360)
    ],
    [
        'config' => createH264VideoCodecConfiguration(400, null, 240)
    ],
    [
        'config' => createH264VideoCodecConfiguration(200, null, 240)
    ]
];

$audioConfiguration = new AACAudioCodecConfiguration('Audio Configuration', 128000, 48000);
$audioConfiguration = $apiClient->codecConfigurations()->audioAAC()->create($audioConfiguration);

$inputStreamVideo = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);
$inputStreamAudio = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(0);

$audioStream = new Stream($audioConfiguration, array($inputStreamAudio));
$audioStream = $apiClient->encodings()->streams($encoding)->create($audioStream);

foreach ($videoConfigurations as &$videoConfiguration)
{
    /** @var H264VideoCodecConfiguration $configuration */
    $configuration = $videoConfiguration['config'];
    $stream = new Stream($videoConfiguration['config'], array($inputStreamVideo));
    $stream = $apiClient->encodings()->streams($encoding)->create($stream);
    $videoConfiguration['stream'] = $stream;
    $muxingStreamVideo = new MuxingStream();
    $muxingStreamVideo->setStreamId($stream->getId());
    $muxingStreamAudio = new MuxingStream();
    $muxingStreamAudio->setStreamId($audioStream->getId());

    $encodingOutput = new EncodingOutput($s3Output);
    $encodingOutput->setOutputPath($outputPath);
    $encodingOutput->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));

    $mp4Muxing = new MP4Muxing();
    $mp4Muxing->setFilename('output_' . $configuration->getHeight() . '_' . $configuration->getBitrate() . '.mp4');
    $mp4Muxing->setOutputs(array($encodingOutput));
    $mp4Muxing->setStreams(array($muxingStreamVideo, $muxingStreamAudio));
    $mp4Muxing = $apiClient->encodings()->muxings($encoding)->mp4Muxing()->create($mp4Muxing);
    $videoConfiguration['mp4Muxing'] = $mp4Muxing;
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
    echo "Encoding failed!";
    exit();
}

echo "Encoding has finished successfully";


function createH264VideoCodecConfiguration($bitrate, $width = null, $height = null)
{
    global $apiClient;
    $codecConfigVideo = new H264VideoCodecConfiguration("Codec Config $height - $bitrate", H264Profile::HIGH, $bitrate * 1000, null);
    $codecConfigVideo->setDescription("Codec Config $height - $bitrate");
    $codecConfigVideo->setWidth($width);
    $codecConfigVideo->setHeight($height);
    return $apiClient->codecConfigurations()->videoH264()->create($codecConfigVideo);
}
