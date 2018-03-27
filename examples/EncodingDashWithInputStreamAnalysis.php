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
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\inputAnalysis\StreamInputAnalysis;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

$apiKey = "INSERT YOUR API KEY";

$s3InputAccessKey = "YOUR S3 INPUT ACCESS KEY";
$s3InputSecretKey = "YOUR S3 INPUT SECRET KEY";
$s3InputBucketName = "YOUR S3 INPUT BUCKET NAME";
$videoInputPath = "/path/to/your/input.mp4";

$s3OutputAccessKey = 'YOUR S3 OUTPUT ACCESS KEY';
$s3OutputSecretKey = 'YOUR S3 OUTPUT SECRET KEY';
$s3OutputBucketName = "YOUR S3 OUTPUT BUCKET NAME";
$outputPath = "path/to/your/output/" . date('Y-m-d-h-i-s') . "/";


/**
 * @var ApiClient
 */
$apiClient = new ApiClient($apiKey);

$encoding = createEncodingRequest($apiClient, 'PHP Stream Analysis Encoding');

$s3Input = createS3Input($apiClient, $s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);

$s3Output = createS3Output($apiClient, $s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);

$inputStreamVideo = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);

$inputStreamAudio = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

$codecConfigVideo1080p = createH264VideoCodecConfiguration($apiClient, '1080p Video Config', H264Profile::HIGH, 4800000, null, 1080);
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, '720p Video Config', H264Profile::HIGH, 2400000, null, 720);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, '480p Video Config', H264Profile::MAIN, 1200000, null, 480);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, '360p Video Config', H264Profile::MAIN, 800000, null, 360);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, '240p Video Config', H264Profile::BASELINE, 400000, null, 240);

$codecConfigAudio128 = createAACAudioCodecConfiguration($apiClient, '128k Audio Config', 128000);

// CREATE VIDEO STREAMS
$videoStream1080p = new Stream($codecConfigVideo1080p, array($inputStreamVideo));
$videoStream720p = new Stream($codecConfigVideo720p, array($inputStreamVideo));
$videoStream480p = new Stream($codecConfigVideo480p, array($inputStreamVideo));
$videoStream360p = new Stream($codecConfigVideo360p, array($inputStreamVideo));
$videoStream240p = new Stream($codecConfigVideo240p, array($inputStreamVideo));

$videoEncodingStream1080p = $apiClient->encodings()->streams($encoding)->create($videoStream1080p);
$videoEncodingStream720p = $apiClient->encodings()->streams($encoding)->create($videoStream720p);
$videoEncodingStream480p = $apiClient->encodings()->streams($encoding)->create($videoStream480p);
$videoEncodingStream360p = $apiClient->encodings()->streams($encoding)->create($videoStream360p);
$videoEncodingStream240p = $apiClient->encodings()->streams($encoding)->create($videoStream240p);

// CREATE AUDIO STREAMS
$audioStream128 = new Stream($codecConfigAudio128, array($inputStreamAudio));
$audioEncodingStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

// CREATE VIDEO MUXINGS (FMP4)
createFmp4Muxing($apiClient, $encoding, $videoEncodingStream1080p, $s3Output, $outputPath . 'video/1080p/dash/');
createFmp4Muxing($apiClient, $encoding, $videoEncodingStream720p, $s3Output, $outputPath . 'video/720p/dash/');
createFmp4Muxing($apiClient, $encoding, $videoEncodingStream480p, $s3Output, $outputPath . 'video/480p/dash/');
createFmp4Muxing($apiClient, $encoding, $videoEncodingStream360p, $s3Output, $outputPath . 'video/360p/dash/');
createFmp4Muxing($apiClient, $encoding, $videoEncodingStream240p, $s3Output, $outputPath . 'video/240p/dash/');

// CREATE AUDIO MUXING (FMP4)
createFmp4Muxing($apiClient, $encoding, $audioEncodingStream128, $s3Output, $outputPath . 'audio/128kbps/dash/');

$apiClient->encodings()->start($encoding);

do {
    /**
     * @var \Bitmovin\api\model\Status status
     */
    $status = $apiClient->encodings()->status($encoding);
    var_dump(date_create(null, new DateTimeZone('UTC'))->getTimestamp() . ": " . $status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

if ($status->getStatus() === Status::ERROR) {
    var_dump("Encoding failed!");
    exit();
}

// INPUT STREAM ANALYSIS
$streamInputDetails = $apiClient->encodings()->streams($encoding)->streamInputAnalysis($videoEncodingStream1080p)->get();
pretty_print_stream_input_details($streamInputDetails);

/**
 * @param StreamInputAnalysis[] $streamInputDetails
 */
function pretty_print_stream_input_details($streamInputDetails) {
    foreach ($streamInputDetails as $analysisDetails) {
        echo "----------------------------------------------------\n";
        echo "InputId: " . $analysisDetails->getInputId()."\n";
        echo "InputPath: " . $analysisDetails->getInputPath()."\n";

        $details = $analysisDetails->getDetails();
        echo "\n";
        echo "Details:\n";
        echo "FormatName: ".$details->getFormatName()."\n";
        echo "BitRate: ".$details->getBitrate() . "\n";
        echo "Duration: ".$details->getDuration(). "\n";
        echo "Size: ".$details->getSize(). "\n";
        echo "StartTime: ".$details->getStartTime()."\n";

        if (is_array($details->getVideoStreams())) {
            echo "\nVideo Streams:\n";
            foreach ($details->getVideoStreams() as $videoStream) {
                echo "ID: " . $videoStream->getId() . "\n";
                echo "Position: " . $videoStream->getPosition() . "\n";
                echo "Duration: " . $videoStream->getDuration() . "\n";
                echo "Bitrate: " . $videoStream->getBitrate() . "\n";
                echo "Codec: " . $videoStream->getCodec() . "\n";
                echo "FPS: " . $videoStream->getFps() . "\n";
                echo "Height: " . $videoStream->getHeight() . "\n";
                echo "Width: " . $videoStream->getWidth() . "\n";
            }
        }

        if (is_array($details->getAudioStreams())) {
            echo "\nAudio Streams:\n";
            foreach ($details->getAudioStreams() as $audioStream) {
                echo "ID: " . $audioStream->getId()."\n";
                echo "Position: " . $audioStream->getPosition()."\n";
                echo "Duration: ". $audioStream->getDuration()."\n";
                echo "Bitrate: ". $audioStream->getBitrate()."\n";
                echo "Codec: ". $audioStream->getCodec()."\n";
                echo "ChannelFormat: ". $audioStream->getChannelFormat()."\n";
                echo "Language: ". $audioStream->getLanguage()."\n";
                echo "SampleRate: ". $audioStream->getSampleRate()."\n";
            }
        }

        if (is_array($details->getMetaStreams())) {
            echo "\n";
            echo "Meta Streams:\n";
            foreach ($details->getMetaStreams() as $metaStream) {
                echo "ID: ". $metaStream->getId()."\n";
                echo "Position: ". $metaStream->getPosition()."\n";
                echo "Codec: ". $metaStream->getCodec()."\n";
                echo "Duration: ". $metaStream->getDuration()."\n";
            }
        }

        if (is_array($details->getSubtitleStreams())) {
            echo "\n";
            echo "Subtitle Streams:\n";
            foreach ($details->getSubtitleStreams() as $subtitleStream) {
                echo "ID: ". $subtitleStream->getId()."\n";
                echo "Position: ". $subtitleStream->getPosition()."\n";
                echo "Codec: ". $subtitleStream->getCodec()."\n";
                echo "Duration: ". $subtitleStream->getDuration()."\n";
                echo "Language: " . $subtitleStream->getLanguage()."\n";
                echo "Hearing Impaired: " . $subtitleStream->getHearingImpaired()."\n";
            }
        }
        echo "----------------------------------------------------\n";
    }
}

/**
 * @param ApiClient $apiClient
 * @param $encodingName
 * @return Encoding
 */
function createEncodingRequest($apiClient, $encodingName)
{
    $encoding = new Encoding($encodingName);
    $encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);

    return $apiClient->encodings()->create($encoding);
}

/**
 * @param ApiClient $apiClient
 * @param string $s3InputBucketName
 * @param string $s3InputAccessKey
 * @param string $s3InputSecretKey
 * @return S3Input
 */
function createS3Input($apiClient,
                       $s3InputBucketName,
                       $s3InputAccessKey,
                       $s3InputSecretKey)
{
    $s3Input = new S3Input(
        $s3InputBucketName,
        $s3InputAccessKey,
        $s3InputSecretKey
    );
    return $apiClient->inputs()->s3()->create($s3Input);
}

/**
 * @param ApiClient $apiClient
 * @param string $s3OutputBucketName
 * @param string $s3OutputAccessKey
 * @param string $s3OutputSecretKey
 * @return mixed
 */
function createS3Output($apiClient,
                        $s3OutputBucketName,
                        $s3OutputAccessKey,
                        $s3OutputSecretKey)
{
    $s3Output = new S3Output(
        $s3OutputBucketName,
        $s3OutputAccessKey,
        $s3OutputSecretKey
    );
    return $apiClient->outputs()->s3()->create($s3Output);
}

/**
 * @param ApiClient $apiClient
 * @param string $name
 * @param string $profile
 * @param integer $bitrate
 * @param double $rate
 * @param integer $width
 * @return H264VideoCodecConfiguration
 */
function createH264VideoCodecConfiguration($apiClient, $name, $profile, $bitrate, $rate, $width)
{
    $codecConfigVideo = new H264VideoCodecConfiguration($name, $profile, $bitrate, $rate);
    $codecConfigVideo->setDescription($bitrate . '_' . $name);
    $codecConfigVideo->setWidth($width);
    return $apiClient->codecConfigurations()->videoH264()->create($codecConfigVideo);
}

/**
 * @param ApiClient $apiClient
 * @param string $name
 * @param integer $bitrate
 * @return AACAudioCodecConfiguration
 */
function createAACAudioCodecConfiguration($apiClient, $name, $bitrate)
{
    $codecConfigAudio = new AACAudioCodecConfiguration($name, $bitrate, null);
    return $apiClient->codecConfigurations()->audioAAC()->create($codecConfigAudio);
}

/**
 * @param ApiClient $apiClient
 * @param Encoding $encoding
 * @param Stream $stream
 * @param Output $output
 * @param string $outputPath
 * @param string $initSegmentName
 * @param integer $segmentDuration
 * @param string $segmentNaming
 * @param string $aclPermission
 * @return FMP4Muxing
 */
function createFmp4Muxing($apiClient,
                          $encoding,
                          $stream,
                          $output,
                          $outputPath,
                          $initSegmentName = 'init.mp4',
                          $segmentDuration = 4,
                          $segmentNaming = 'segment_%number%.m4s',
                          $aclPermission = AclPermission::ACL_PUBLIC_READ)
{
    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());
    $encodingOutputs = null;

    $fmp4Muxing = new FMP4Muxing();
    $fmp4Muxing->setInitSegmentName($initSegmentName);
    $fmp4Muxing->setSegmentLength($segmentDuration);
    $fmp4Muxing->setSegmentNaming($segmentNaming);
    if (!is_null($output)) {
        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl(array(new Acl($aclPermission)));
        $encodingOutputs[] = $encodingOutput;
    }
    $fmp4Muxing->setOutputs($encodingOutputs);
    $fmp4Muxing->setStreams(array($muxingStream));

    return $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing);
}