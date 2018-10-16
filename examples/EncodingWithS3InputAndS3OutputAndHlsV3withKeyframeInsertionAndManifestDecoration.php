<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\encodings\PositionMode;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\HlsVersion;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\keyframes\Keyframe;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashDrmRepresentation;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\hls\CustomTag;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');

// CREATE ENCODING
$encoding = new Encoding('A Name for your encoding');
$encoding->setCloudRegion(CloudRegion::AWS_US_EAST_1);
$encoding = $apiClient->encodings()->create($encoding);

// KEYFRAMETIME
$keyframeTime = 17.2;

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
$videoStream1080p = $apiClient->encodings()->streams($encoding)->create($videoStream1080p);
$videoStream720p = $apiClient->encodings()->streams($encoding)->create($videoStream720p);
$videoStream480p = $apiClient->encodings()->streams($encoding)->create($videoStream480p);
$videoStream360p = $apiClient->encodings()->streams($encoding)->create($videoStream360p);
$videoStream240p = $apiClient->encodings()->streams($encoding)->create($videoStream240p);

// CREATE AUDIO STREAMS
$audioStream128 = new Stream($codecConfigAudio128kbit, array($inputStreamAudio));
$audioStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

// CREATE VIDEO MUXINGS (TS)
$tsMuxing1080p = createTsMuxing($apiClient, $encoding, array($videoStream1080p, $audioStream128), $s3Output, $outputPath . 'video/1080p/hls/', AclPermission::ACL_PUBLIC_READ);
$tsMuxing720p = createTsMuxing($apiClient, $encoding, array($videoStream720p, $audioStream128), $s3Output, $outputPath . 'video/720p/hls/', AclPermission::ACL_PUBLIC_READ);
$tsMuxing480p = createTsMuxing($apiClient, $encoding, array($videoStream480p, $audioStream128), $s3Output, $outputPath . 'video/480p/hls/', AclPermission::ACL_PUBLIC_READ);
$tsMuxing360p = createTsMuxing($apiClient, $encoding, array($videoStream360p, $audioStream128), $s3Output, $outputPath . 'video/360p/hls/', AclPermission::ACL_PUBLIC_READ);
$tsMuxing240p = createTsMuxing($apiClient, $encoding, array($videoStream240p, $audioStream128), $s3Output, $outputPath . 'video/240p/hls/', AclPermission::ACL_PUBLIC_READ);

//Create a Keyframe at the specific time and start a new segment there
$keyframe = new Keyframe($keyframeTime, true);
$keyframe = $apiClient->encodings()->keyframes($encoding)->create($keyframe);

$apiClient->encodings()->start($encoding);

do
{
    $status = $apiClient->encodings()->status($encoding);
    var_dump(date_create(null, new DateTimeZone('UTC'))->getTimestamp() . ": " . $status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(5);
} while ($isRunning);

if ($status->getStatus() === Status::ERROR)
{
    var_dump("Encoding failed!");
    exit();
}

//###########################################################################################

//MANIFEST OUTPUT DESTINATION
$manifestOutput = new EncodingOutput($s3Output);
$manifestOutput->setOutputPath($outputPath);
$acl = new Acl(AclPermission::ACL_PUBLIC_READ);
$manifestOutput->setAcl([$acl]);

// CREATE HLS PLAYLIST
$manifestName = "stream.m3u8";
$manifest = new HlsManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$manifest->setHlsMasterPlaylistVersion(HlsVersion::HLS_VERSION_3);
$manifest->setHlsMediaPlaylistVersion(HlsVersion::HLS_VERSION_3);
$masterPlaylist = $apiClient->manifests()->hls()->create($manifest);

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
$videoStreamInfo1080p = createHlsVariantStreamInfo($encoding, $videoStream1080p, $tsMuxing1080p, $tsSegmentPath1080p, $variantStreamUri1080p);
$videoStreamInfo720p = createHlsVariantStreamInfo($encoding, $videoStream720p, $tsMuxing720p, $tsSegmentPath720p, $variantStreamUri720p);
$videoStreamInfo480p = createHlsVariantStreamInfo($encoding, $videoStream480p, $tsMuxing480p, $tsSegmentPath480p, $variantStreamUri480p);
$videoStreamInfo360p = createHlsVariantStreamInfo($encoding, $videoStream360p, $tsMuxing360p, $tsSegmentPath360p, $variantStreamUri360p);
$videoStreamInfo240p = createHlsVariantStreamInfo($encoding, $videoStream240p, $tsMuxing240p, $tsSegmentPath240p, $variantStreamUri240p);

$videoStreamInfo1080p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo1080p);
$videoStreamInfo720p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);
$videoStreamInfo480p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo480p);
$videoStreamInfo360p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo360p);
$videoStreamInfo240p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo240p);

//Create a Custom Tag to correspond with the Keyframe above.
$customTag = new CustomTag();
$customTag->setTime($keyframeTime);
$customTag->setPositionMode(PositionMode::TIME);
$customTag->setData("#EXT-YOUR-TAG\n#EXT-YOU-CAN-HAVE-MORE-LINES");

$videoStreamInfo1080p = $apiClient->manifests()->hls()->addCustomTagToStreamInfo($masterPlaylist, $videoStreamInfo1080p, $customTag);
$videoStreamInfo720p = $apiClient->manifests()->hls()->addCustomTagToStreamInfo($masterPlaylist, $videoStreamInfo720p, $customTag);
$videoStreamInfo480p = $apiClient->manifests()->hls()->addCustomTagToStreamInfo($masterPlaylist, $videoStreamInfo480p, $customTag);
$videoStreamInfo360p = $apiClient->manifests()->hls()->addCustomTagToStreamInfo($masterPlaylist, $videoStreamInfo360p, $customTag);
$videoStreamInfo240p = $apiClient->manifests()->hls()->addCustomTagToStreamInfo($masterPlaylist, $videoStreamInfo240p, $customTag);

//Start Manifest Creation
$response = $apiClient->manifests()->hls()->start($masterPlaylist);

do
{
    $status = $apiClient->manifests()->hls()->status($masterPlaylist);
    var_dump($status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(5);
} while ($isRunning);

var_dump("Master Playlist finished");

//#####################################################################################################################

/**
 * @param Encoding $encoding
 * @param Stream   $stream
 * @param TSMuxing $tsMuxing
 * @param string   $segmentPath
 * @param string   $uri
 * @return StreamInfo
 */
function createHlsVariantStreamInfo(Encoding $encoding, Stream $stream, TSMuxing $tsMuxing, $segmentPath, $uri)
{
    $variantStream = new StreamInfo();
    $variantStream->setEncodingId($encoding->getId());
    $variantStream->setStreamId($stream->getId());
    $variantStream->setMuxingId($tsMuxing->getId());
    $variantStream->setAudio(null);
    $variantStream->setSegmentPath($segmentPath);
    $variantStream->setUri($uri);

    return $variantStream;
}

/**
 * @param ApiClient $apiClient
 * @param Encoding  $encoding
 * @param Stream[]  $streams
 * @param Output    $output
 * @param string    $outputPath
 *
 * @param string    $outputAcl
 * @param int       $segmentDuration
 * @param string    $segmentNaming
 * @return TSMuxing
 * @throws BitmovinException
 */
function createTsMuxing($apiClient, $encoding, $streams, $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ, $segmentDuration = 4, $segmentNaming = 'segment_%number%.ts')
{
    $encodingOutputs = array();

    if ($output instanceof Output)
    {
        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl(array(new Acl($outputAcl)));
        $encodingOutputs[] = $encodingOutput;
    }

    $muxingStreams = array();
    foreach ($streams as $stream)
    {
        $muxingStream = new MuxingStream();
        $muxingStream->setStreamId($stream->getId());
        $muxingStreams[] = $muxingStream;
    }


    $tsMuxing = new TSMuxing();
    $tsMuxing->setSegmentLength($segmentDuration);
    $tsMuxing->setSegmentNaming($segmentNaming);
    $tsMuxing->setOutputs($encodingOutputs);
    $tsMuxing->setStreams($muxingStreams);

    return $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($tsMuxing);
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
