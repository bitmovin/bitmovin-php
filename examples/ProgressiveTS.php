<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\ProgressiveTSMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');

// CREATE ENCODING
$encoding = new Encoding('Progressive TS example');
$encoding->setCloudRegion(CloudRegion::AWS_EU_WEST_1);
$encoding->setEncoderVersion("BETA");
$encoding = $apiClient->encodings()->create($encoding);

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = 'YOUR-AWS-S3-BUCKETNAME';
$videoInputPath = 'path/to/your/input/file.mp4';
$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$input = $apiClient->inputs()->s3()->create($s3Input);

$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = 'YOUR-AWS-S3-BUCKETNAME';
$outputPath = "path/to/your/output/";
$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$output = $apiClient->outputs()->s3()->create($s3Output);

$segmentLength = 4.0;

//CREATE INPUT STREAMS
//video stream of input file
$inputStreamVideo = new InputStream($input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);
//audio stream of input file
$inputStreamAudio = new InputStream($input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

// CREATE VIDEO CODEC CONFIGURATIONS
$codecConfigVideo1080p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo1080p', H264Profile::HIGH, 4800000, null, 1080);
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720p', H264Profile::HIGH, 2400000, null, 720);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo480p', H264Profile::MAIN, 1200000, null, 480);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo360p', H264Profile::MAIN, 800000, null, 360);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo240p', H264Profile::BASELINE, 400000, null, 240);

// CREATE AUDIO CODEC CONFIGURATION
$codecConfigAudio128 = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000);

// CREATE VIDEO STREAMS
$videoStream1080p = new Stream($codecConfigVideo1080p, array($inputStreamVideo));
$videoEncodingStream1080p = $apiClient->encodings()->streams($encoding)->create($videoStream1080p);
$videoStream720p = new Stream($codecConfigVideo720p, array($inputStreamVideo));
$videoEncodingStream720p = $apiClient->encodings()->streams($encoding)->create($videoStream720p);
$videoStream480p = new Stream($codecConfigVideo480p, array($inputStreamVideo));
$videoEncodingStream480p = $apiClient->encodings()->streams($encoding)->create($videoStream480p);
$videoStream360p = new Stream($codecConfigVideo360p, array($inputStreamVideo));
$videoEncodingStream360p = $apiClient->encodings()->streams($encoding)->create($videoStream360p);
$videoStream240p = new Stream($codecConfigVideo240p, array($inputStreamVideo));
$videoEncodingStream240p = $apiClient->encodings()->streams($encoding)->create($videoStream240p);

// CREATE AUDIO STREAMS
$audioStream128 = new Stream($codecConfigAudio128, array($inputStreamAudio));
$audioEncodingStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

// CREATE COMBINED VIDEO and AUDIO MUXINGS (TS)
$tsMuxing1080p = createCombinedTsMuxing($apiClient, $encoding, $videoEncodingStream1080p, $audioEncodingStream128, $output, $outputPath . 'video/1080p/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$tsMuxing720p = createCombinedTsMuxing($apiClient, $encoding, $videoEncodingStream720p, $audioEncodingStream128, $output, $outputPath . 'video/720p/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$tsMuxing480p = createCombinedTsMuxing($apiClient, $encoding, $videoEncodingStream480p, $audioEncodingStream128, $output, $outputPath . 'video/480p/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$tsMuxing360p = createCombinedTsMuxing($apiClient, $encoding, $videoEncodingStream360p, $audioEncodingStream128, $output, $outputPath . 'video/360p/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$tsMuxing240p = createCombinedTsMuxing($apiClient, $encoding, $videoEncodingStream240p, $audioEncodingStream128, $output, $outputPath . 'video/240p/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);

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

//###########################################################################################

//MANIFEST OUTPUT DESTINATION
$manifestOutput = new EncodingOutput($output);
$manifestOutput->setOutputPath($outputPath);
$acl = new Acl(AclPermission::ACL_PUBLIC_READ);
$manifestOutput->setAcl([$acl]);

// CREATE HLS PLAYLIST
$manifestName = "stream.m3u8";
$manifest = new HlsManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$masterPlaylist = $apiClient->manifests()->hls()->create($manifest);
// $audioGroupId = 'audio';

$variantStreamUri1080p = "video_1080p_" . $codecConfigVideo1080p->getBitrate() . "_variant.m3u8";
$variantStreamUri720p = "video_720p_" . $codecConfigVideo720p->getBitrate() . "_variant.m3u8";
$variantStreamUri480p = "video_480p_" . $codecConfigVideo480p->getBitrate() . "_variant.m3u8";
$variantStreamUri360p = "video_360p_" . $codecConfigVideo360p->getBitrate() . "_variant.m3u8";
$variantStreamUri240p = "video_240p_" . $codecConfigVideo240p->getBitrate() . "_variant.m3u8";

$tsSegmentPath1080p = getSegmentOutputPath($outputPath, $tsMuxing1080p->getOutputs()[0]->getOutputPath());
$tsSegmentPath720p = getSegmentOutputPath($outputPath, $tsMuxing720p->getOutputs()[0]->getOutputPath());
$tsSegmentPath480p = getSegmentOutputPath($outputPath, $tsMuxing480p->getOutputs()[0]->getOutputPath());
$tsSegmentPath360p = getSegmentOutputPath($outputPath, $tsMuxing360p->getOutputs()[0]->getOutputPath());
$tsSegmentPath240p = getSegmentOutputPath($outputPath, $tsMuxing240p->getOutputs()[0]->getOutputPath());

//Create a Variant Stream for Video
$videoStreamInfo1080p = createHlsVariantStreamInfo($encoding, $videoEncodingStream1080p, $tsMuxing1080p, $tsSegmentPath1080p, $variantStreamUri1080p);
$videoStreamInfo720p = createHlsVariantStreamInfo($encoding, $videoEncodingStream720p, $tsMuxing720p, $tsSegmentPath720p, $variantStreamUri720p);
$videoStreamInfo480p = createHlsVariantStreamInfo($encoding, $videoEncodingStream480p, $tsMuxing480p, $tsSegmentPath480p, $variantStreamUri480p);
$videoStreamInfo360p = createHlsVariantStreamInfo($encoding, $videoEncodingStream360p, $tsMuxing360p, $tsSegmentPath360p, $variantStreamUri360p);
$videoStreamInfo240p = createHlsVariantStreamInfo($encoding, $videoEncodingStream240p, $tsMuxing240p, $tsSegmentPath240p, $variantStreamUri240p);
$variantStream1080p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo1080p);
$variantStream720p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);
$variantStream480p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo480p);
$variantStream360p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo360p);
$variantStream240p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo240p);

//Start Manifest Creation
$response = $apiClient->manifests()->hls()->start($masterPlaylist);

do
{
    $status = $apiClient->manifests()->hls()->status($masterPlaylist);
    var_dump($status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

var_dump("Master Playlist finished");

//#####################################################################################################################

/**
 * @param Encoding            $encoding
 * @param Stream              $stream
 * @param ProgressiveTSMuxing $tsMuxing
 * @param string              $segmentPath
 * @param string              $uri
 * @return StreamInfo
 * @internal param string $audioGroupId
 */
function createHlsVariantStreamInfo(Encoding $encoding, Stream $stream, ProgressiveTSMuxing $tsMuxing, $segmentPath, $uri)
{
    $variantStream = new StreamInfo();
    $variantStream->setEncodingId($encoding->getId());
    $variantStream->setStreamId($stream->getId());
    $variantStream->setMuxingId($tsMuxing->getId());
    $variantStream->setSegmentPath($segmentPath);
    $variantStream->setUri($uri);

    return $variantStream;
}

/**
 * @param ApiClient $apiClient
 * @param Encoding  $encoding
 * @param Stream    $videoStream
 * @param Stream    $audioStream
 * @param Output    $output
 * @param string    $outputPath
 *
 * @param string    $outputAcl
 * @param int       $segmentDuration
 * @param string    $fileName
 * @return ProgressiveTSMuxing
 * @throws BitmovinException
 */
function createCombinedTsMuxing($apiClient, $encoding, $videoStream, $audioStream, $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ, $segmentDuration = 4, $fileName = 'index.ts')
{
    $encodingOutputs = array();

    if ($output instanceof Output)
    {
        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl(array(new Acl($outputAcl)));
        $encodingOutputs[] = $encodingOutput;
    }

    $videoMuxingStream = new MuxingStream();
    $videoMuxingStream->setStreamId($videoStream->getId());

    $audioMuxingStream = new MuxingStream();
    $audioMuxingStream->setStreamId($audioStream->getId());

    $tsMuxing = new ProgressiveTSMuxing();
    $tsMuxing->setSegmentLength($segmentDuration);
    $tsMuxing->setFileName($fileName);
    $tsMuxing->setOutputs($encodingOutputs);
    $tsMuxing->setStreams(array($videoMuxingStream, $audioMuxingStream));

    return $apiClient->encodings()->muxings($encoding)->progressiveTsMuxing()->create($tsMuxing);
}

/**
 * @param $outputPath
 * @param $muxingOutputPath
 * @return string
 */
function getSegmentOutputPath($outputPath, $muxingOutputPath)
{
    $segmentPath = $muxingOutputPath;
    $substr = substr($muxingOutputPath, 0, strlen($outputPath));
    if ($substr === $outputPath)
    {
        $segmentPath = substr($muxingOutputPath, strlen($outputPath));
    }
    return $segmentPath;
}

function createEncodingOutput(Output $output, $outputPath, $acl = AclPermission::ACL_PUBLIC_READ)
{
    $encodingOutput = new EncodingOutput($output);
    $encodingOutput->setOutputPath($outputPath);
    $encodingOutput->setAcl(array(new Acl($acl)));
    return $encodingOutput;
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
