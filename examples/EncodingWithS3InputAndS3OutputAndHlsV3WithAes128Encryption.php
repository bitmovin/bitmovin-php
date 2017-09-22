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

$codecConfigVideo1080p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo1080p', H264Profile::HIGH, 4800000, null, 1080);
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720p', H264Profile::HIGH, 2400000, null, 720);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo480p', H264Profile::MAIN, 1200000, null, 480);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo360p', H264Profile::MAIN, 800000, null, 360);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo240p', H264Profile::BASELINE, 400000, null, 240);

// CREATE AUDIO CODEC CONFIGURATIONS
$codecConfigAudio160kbit = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC160k', 160000);

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
$audioStream160 = new Stream($codecConfigAudio160kbit, array($inputStreamAudio));
$audioStream160 = $apiClient->encodings()->streams($encoding)->create($audioStream160);

// CREATE VIDEO MUXINGS (TS)
$combinedtsMuxing1080p = createTsMuxing($apiClient, $encoding, array($videoStream1080p, $audioStream160), $s3Output, $outputPath . '1080p/hls/', AclPermission::ACL_PUBLIC_READ);
$combinedtsMuxing720p = createTsMuxing($apiClient, $encoding, array($videoStream720p, $audioStream160), $s3Output, $outputPath . '720p/hls/', AclPermission::ACL_PUBLIC_READ);
$combinedtsMuxing480p = createTsMuxing($apiClient, $encoding, array($videoStream480p, $audioStream160), $s3Output, $outputPath . '480p/hls/', AclPermission::ACL_PUBLIC_READ);
$combinedtsMuxing360p = createTsMuxing($apiClient, $encoding, array($videoStream360p, $audioStream160), $s3Output, $outputPath . '360p/hls/', AclPermission::ACL_PUBLIC_READ);
$combinedtsMuxing240p = createTsMuxing($apiClient, $encoding, array($videoStream240p, $audioStream160), $s3Output, $outputPath . '240p/hls/', AclPermission::ACL_PUBLIC_READ);


// ADD AES DRM TO HLSv3 MUXINGs
$aesEncodingOutput1080p = createEncodingOutput($s3Output, $outputPath . '1080p/aes/');
$aesEncodingOutput720p = createEncodingOutput($s3Output, $outputPath . '720p/aes/');
$aesEncodingOutput480p = createEncodingOutput($s3Output, $outputPath . '480p/aes/');
$aesEncodingOutput360p = createEncodingOutput($s3Output, $outputPath . '360p/aes/');
$aesEncodingOutput240p = createEncodingOutput($s3Output, $outputPath . '240p/aes/');
$aesDrm1080p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput1080p), null);
$aesDrm720p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput720p), null);
$aesDrm480p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput480p), null);
$aesDrm360p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput360p), null);
$aesDrm240p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput240p), null);
$combinedTsDrm1080p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($combinedtsMuxing1080p)->aes()->create($aesDrm1080p);
$combinedTsDrm720p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($combinedtsMuxing720p)->aes()->create($aesDrm720p);
$combinedTsDrm480p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($combinedtsMuxing480p)->aes()->create($aesDrm480p);
$combinedTsDrm360p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($combinedtsMuxing360p)->aes()->create($aesDrm360p);
$combinedTsDrm240p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($combinedtsMuxing240p)->aes()->create($aesDrm240p);

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

$variantStreamUri1080p = "video_1080p_" . $codecConfigVideo1080p->getBitrate() . "_variant.m3u8";
$variantStreamUri720p = "video_720p_" . $codecConfigVideo720p->getBitrate() . "_variant.m3u8";
$variantStreamUri480p = "video_480p_" . $codecConfigVideo480p->getBitrate() . "_variant.m3u8";
$variantStreamUri360p = "video_360p_" . $codecConfigVideo360p->getBitrate() . "_variant.m3u8";
$variantStreamUri240p = "video_240p_" . $codecConfigVideo240p->getBitrate() . "_variant.m3u8";
$tsSegmentPath1080p = getSegmentOutputPath($outputPath, $combinedtsMuxing1080p->getOutputs()[0]->getOutputPath());
$tsSegmentPath720p = getSegmentOutputPath($outputPath, $combinedtsMuxing720p->getOutputs()[0]->getOutputPath());
$tsSegmentPath480p = getSegmentOutputPath($outputPath, $combinedtsMuxing480p->getOutputs()[0]->getOutputPath());
$tsSegmentPath360p = getSegmentOutputPath($outputPath, $combinedtsMuxing360p->getOutputs()[0]->getOutputPath());
$tsSegmentPath240p = getSegmentOutputPath($outputPath, $combinedtsMuxing240p->getOutputs()[0]->getOutputPath());

//Create a Variant Stream for Video
$videoStreamInfo1080p = createHlsVariantStreamInfo($encoding, $videoStream1080p, $combinedtsMuxing1080p, $tsSegmentPath1080p, $variantStreamUri1080p);
$videoStreamInfo720p = createHlsVariantStreamInfo($encoding, $videoStream720p, $combinedtsMuxing720p, $tsSegmentPath720p, $variantStreamUri720p);
$videoStreamInfo480p = createHlsVariantStreamInfo($encoding, $videoStream480p, $combinedtsMuxing480p, $tsSegmentPath480p, $variantStreamUri480p);
$videoStreamInfo360p = createHlsVariantStreamInfo($encoding, $videoStream360p, $combinedtsMuxing360p, $tsSegmentPath360p, $variantStreamUri360p);
$videoStreamInfo240p = createHlsVariantStreamInfo($encoding, $videoStream240p, $combinedtsMuxing240p, $tsSegmentPath240p, $variantStreamUri240p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo1080p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo480p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo360p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo240p);

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

$variantStreamUri1080p = "video_1080p_" . $codecConfigVideo1080p->getBitrate() . "_variant_aes.m3u8";
$variantStreamUri720p = "video_720p_" . $codecConfigVideo720p->getBitrate() . "_variant_aes.m3u8";
$variantStreamUri480p = "video_480p_" . $codecConfigVideo480p->getBitrate() . "_variant_aes.m3u8";
$variantStreamUri360p = "video_360p_" . $codecConfigVideo360p->getBitrate() . "_variant_aes.m3u8";
$variantStreamUri240p = "video_240p_" . $codecConfigVideo240p->getBitrate() . "_variant_aes.m3u8";
$tsSegmentPath1080p = getSegmentOutputPath($outputPath, $combinedTsDrm1080p->getOutputs()[0]->getOutputPath());
$tsSegmentPath720p = getSegmentOutputPath($outputPath, $combinedTsDrm720p->getOutputs()[0]->getOutputPath());
$tsSegmentPath480p = getSegmentOutputPath($outputPath, $combinedTsDrm480p->getOutputs()[0]->getOutputPath());
$tsSegmentPath360p = getSegmentOutputPath($outputPath, $combinedTsDrm360p->getOutputs()[0]->getOutputPath());
$tsSegmentPath240p = getSegmentOutputPath($outputPath, $combinedTsDrm240p->getOutputs()[0]->getOutputPath());

//Create a Variant Stream for Video
$videoStreamInfo1080p = createHlsVariantStreamInfoForDrm($encoding, $videoStream1080p, $combinedtsMuxing1080p, $combinedTsDrm1080p, $tsSegmentPath1080p, $variantStreamUri1080p);
$videoStreamInfo720p = createHlsVariantStreamInfoForDrm($encoding, $videoStream720p, $combinedtsMuxing720p, $combinedTsDrm720p, $tsSegmentPath720p, $variantStreamUri720p);
$videoStreamInfo480p = createHlsVariantStreamInfoForDrm($encoding, $videoStream480p, $combinedtsMuxing480p, $combinedTsDrm480p, $tsSegmentPath480p, $variantStreamUri480p);
$videoStreamInfo360p = createHlsVariantStreamInfoForDrm($encoding, $videoStream360p, $combinedtsMuxing360p, $combinedTsDrm360p, $tsSegmentPath360p, $variantStreamUri360p);
$videoStreamInfo240p = createHlsVariantStreamInfoForDrm($encoding, $videoStream240p, $combinedtsMuxing240p, $combinedTsDrm240p, $tsSegmentPath240p, $variantStreamUri240p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo1080p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo480p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo360p);
$apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo240p);

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