<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\drm\AesEncryptionMethod;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\HlsVersion;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\drms\AesDrm;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
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
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');


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

// AES CONFIGURATION
$aesKey = "0123456789abcdef0123456789abcdef";
$aesIV = "0123456789abcdef0123456789abcdef";


// CREATE ENCODING
$encoding = new Encoding('hlsv3 with aes encoding');
$encoding->setEncoderVersion("BETA");
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);

// CREATE VIDEO CODEC CONFIGURATIONS
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720p', H264Profile::HIGH, 2400000, null, 720);

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
$videoStream720p = new Stream($codecConfigVideo720p, array($inputStreamVideo));
$videoStream720p = $apiClient->encodings()->streams($encoding)->create($videoStream720p);

// CREATE AUDIO STREAMS
$audioStream128 = new Stream($codecConfigAudio128kbit, array($inputStreamAudio));
$audioStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

// CREATE VIDEO MUXINGS (TS)
$combinedTsMuxing720p = createTsMuxing($apiClient, $encoding, array($videoStream720p, $audioStream128), $s3Output, $outputPath . '720p/hls/', AclPermission::ACL_PUBLIC_READ);

// ADD AES DRM TO HLSv3 MUXINGs
$aesEncodingOutput720p = createEncodingOutput($s3Output, $outputPath . '720p/hls/drm/');
$aesDrm720p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput720p), null);
$combinedTsDrm720p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($combinedTsMuxing720p)->aes()->create($aesDrm720p);

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

$variantStreamUri720p = "video_720p_" . $codecConfigVideo720p->getBitrate() . "_variant.m3u8";
$tsSegmentPath720p = getSegmentOutputPath($outputPath, $combinedTsMuxing720p->getOutputs()[0]->getOutputPath());

//Create a Variant Stream for Video
$videoStreamInfo720p = createHlsVariantStreamInfo($encoding, $videoStream720p, $combinedTsMuxing720p, $tsSegmentPath720p, $variantStreamUri720p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);

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

// CREATE ENCRYPTED HLS PLAYLIST
$manifestName = "stream_aes.m3u8";
$manifest = new HlsManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$masterPlaylist = $apiClient->manifests()->hls()->create($manifest);

$variantStreamUri720p = "video_720p_" . $codecConfigVideo720p->getBitrate() . "_variant_aes.m3u8";
$tsSegmentPath720p = getSegmentOutputPath($outputPath, $combinedTsDrm720p->getOutputs()[0]->getOutputPath());

//Create a Variant Stream for Video
$videoStreamInfo720p = createHlsVariantStreamInfoForDrm($encoding, $videoStream720p, $combinedTsMuxing720p, $combinedTsDrm720p, $tsSegmentPath720p, $variantStreamUri720p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);

//Start Manifest Creation
$response = $apiClient->manifests()->hls()->start($masterPlaylist);

do
{
    $status = $apiClient->manifests()->hls()->status($masterPlaylist);
    var_dump($status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

var_dump("Encrypted Master Playlist finished");

//#####################################################################################################################


function createAes128Drm($key, $iv, $outputs, $keyFileUri)
{
    return new AesDrm(AesEncryptionMethod::AES_128, $key, $iv, $outputs, $keyFileUri);
}

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
 * @param Encoding $encoding
 * @param Stream $stream
 * @param TSMuxing $tsMuxing
 * @param AesDrm $aesDrm
 * @param string $segmentPath
 * @param string $uri
 * @return StreamInfo
 */
function createHlsVariantStreamInfoForDrm(Encoding $encoding, Stream $stream, TSMuxing $tsMuxing, AesDrm $aesDrm, $segmentPath, $uri)
{
    $variantStream = new StreamInfo();
    $variantStream->setEncodingId($encoding->getId());
    $variantStream->setStreamId($stream->getId());
    $variantStream->setMuxingId($tsMuxing->getId());
    $variantStream->setDrmId($aesDrm->getid());
    $variantStream->setAudio(null);
    $variantStream->setSegmentPath($segmentPath);
    $variantStream->setUri($uri);

    return $variantStream;
}

/**
 * @param ApiClient $apiClient
 * @param Encoding  $encoding
 * @param Stream    $stream
 * @param Output    $output
 * @param string    $outputPath
 *
 * @param string    $outputAcl
 * @param string    $initSegmentName
 * @param int       $segmentDuration
 * @param string    $segmentNaming
 * @return FMP4Muxing
 * @throws BitmovinException
 */
function createFmp4Muxing($apiClient, $encoding, $stream, $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ, $segmentDuration = 4, $initSegmentName = 'init.mp4', $segmentNaming = 'segment_%number%.m4s')
{
    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());
    $encodingOutputs = array();

    $fmp4Muxing = new FMP4Muxing();
    $fmp4Muxing->setInitSegmentName($initSegmentName);
    $fmp4Muxing->setSegmentLength($segmentDuration);
    $fmp4Muxing->setSegmentNaming($segmentNaming);
    if (!is_null($output))
    {
        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl(array(new Acl($outputAcl)));
        $encodingOutputs[] = $encodingOutput;
    }
    $fmp4Muxing->setOutputs($encodingOutputs);
    $fmp4Muxing->setStreams(array($muxingStream));

    return $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing);
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
 * @param Encoding   $encoding
 * @param FMP4Muxing $fmp4Muxing
 * @param string     $manifestType
 * @param string     $segmentPath
 * @return DashDrmRepresentation
 */
function createDashRepresentation(Encoding $encoding, FMP4Muxing $fmp4Muxing, $manifestType, $segmentPath)
{
    $representation = new DashDrmRepresentation();
    $representation->setType($manifestType);
    $representation->setSegmentPath($segmentPath);
    $representation->setEncodingId($encoding->getId());
    $representation->setMuxingId($fmp4Muxing->getId());

    return $representation;
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