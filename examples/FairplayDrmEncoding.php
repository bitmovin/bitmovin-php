<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\drms\FairPlayDrm;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');

// CREATE ENCODING
$encoding = new Encoding('FairPlay DRM Encoding example');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$s3Input = $apiClient->inputs()->s3()->create($s3Input);
$videoInputPath = "path/to/your/input/file.mp4";

$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$s3Output = $apiClient->outputs()->s3()->create($s3Output);
$outputPath = "path/to/your/output-destination/";

$fairPlayKey = "0123456789abcdef0123456789abcdef";
$fairPlayIV = "0123456789abcdef0123456789abcdef";
$fairPlayUri = "skd://userspecifc?custom=information";

//CREATE AUDIO / VIDEO INPUT STREAMS
$inputStreamVideo = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);
$inputStreamAudio = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

// CREATE VIDEO CODEC CONFIGURATIONS
$codecConfigVideo1080p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo1080p', H264Profile::HIGH, 4800000, null, 1080);
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720p', H264Profile::HIGH, 2400000, null, 720);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo480p', H264Profile::MAIN, 1200000, null, 480);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo360p', H264Profile::MAIN, 800000, null, 360);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo240p', H264Profile::BASELINE, 400000, null, 240);

// CREATE AUDIO CODEC CONFIGURATIONS
$codecConfigAudio128 = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000);

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
$audioStream = new Stream($codecConfigAudio128, array($inputStreamAudio));
$audioEncodingStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream);

// CREATE VIDEO MUXINGS (TS)
$tsMuxing1080p = createTsMuxing($apiClient, $encoding, $videoStream1080p, null, null);
$tsMuxing720p = createTsMuxing($apiClient, $encoding, $videoStream720p, null, null);
$tsMuxing480p = createTsMuxing($apiClient, $encoding, $videoStream480p, null, null);
$tsMuxing360p = createTsMuxing($apiClient, $encoding, $videoStream360p, null, null);
$tsMuxing240p = createTsMuxing($apiClient, $encoding, $videoStream240p, null, null);

// CREATE AUDIO MUXING (TS)
$audioTsMuxing128 = createTsMuxing($apiClient, $encoding, $audioEncodingStream128, null, null);

// ADD DRM TO VIDEO TS MUXINGs
$fairPlayEncodingOutput1080p = createEncodingOutput($s3Output, $outputPath . 'video/1080p/hls/drm/');
$fairPlayEncodingOutput720p = createEncodingOutput($s3Output, $outputPath . 'video/720p/hls/drm/');
$fairPlayEncodingOutput480p = createEncodingOutput($s3Output, $outputPath . 'video/480p/hls/drm/');
$fairPlayEncodingOutput360p = createEncodingOutput($s3Output, $outputPath . 'video/360p/hls/drm/');
$fairPlayEncodingOutput240p = createEncodingOutput($s3Output, $outputPath . 'video/240p/hls/drm/');

$fairPlayDrm1080p = createFairPlayDrm($fairPlayKey, $fairPlayIV, $fairPlayUri, array($fairPlayEncodingOutput1080p));
$fairPlayDrm720p = createFairPlayDrm($fairPlayKey, $fairPlayIV, $fairPlayUri, array($fairPlayEncodingOutput720p));
$fairPlayDrm480p = createFairPlayDrm($fairPlayKey, $fairPlayIV, $fairPlayUri, array($fairPlayEncodingOutput480p));
$fairPlayDrm360p = createFairPlayDrm($fairPlayKey, $fairPlayIV, $fairPlayUri, array($fairPlayEncodingOutput360p));
$fairPlayDrm240p = createFairPlayDrm($fairPlayKey, $fairPlayIV, $fairPlayUri, array($fairPlayEncodingOutput240p));

$videoTsDrm1080p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing1080p)->fairplay()->create($fairPlayDrm1080p);
$videoTsDrm720p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing720p)->fairplay()->create($fairPlayDrm720p);
$videoTsDrm480p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing480p)->fairplay()->create($fairPlayDrm480p);
$videoTsDrm360p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing360p)->fairplay()->create($fairPlayDrm360p);
$videoTsDrm240p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing240p)->fairplay()->create($fairPlayDrm240p);

// CREATE DRM TO AUDIO TS MUXINGs
$audioFairPlayEncodingOutput128 = createEncodingOutput($s3Output, $outputPath . 'audio/128kbps/hls/drm/');
$audioFairPlayDrm128 = createFairPlayDrm($fairPlayKey, $fairPlayIV, $fairPlayUri, array($audioFairPlayEncodingOutput128));
$audioTsDrm128 = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($audioTsMuxing128)->fairplay()->create($audioFairPlayDrm128);

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
$masterPlaylist = $apiClient->manifests()->hls()->create($manifest);
$audioGroupId = 'audio';

$variantStreamUri1080p = "video_1080p_" . $codecConfigVideo1080p->getBitrate() . "_variant.m3u8";
$variantStreamUri720p = "video_720p_" . $codecConfigVideo720p->getBitrate() . "_variant.m3u8";
$variantStreamUri480p = "video_480p_" . $codecConfigVideo480p->getBitrate() . "_variant.m3u8";
$variantStreamUri360p = "video_360p_" . $codecConfigVideo360p->getBitrate() . "_variant.m3u8";
$variantStreamUri240p = "video_240p_" . $codecConfigVideo240p->getBitrate() . "_variant.m3u8";

$tsSegmentPath1080p = getSegmentOutputPath($outputPath, $fairPlayDrm1080p->getOutputs()[0]->getOutputPath());
$tsSegmentPath720p = getSegmentOutputPath($outputPath, $fairPlayDrm720p->getOutputs()[0]->getOutputPath());
$tsSegmentPath480p = getSegmentOutputPath($outputPath, $fairPlayDrm480p->getOutputs()[0]->getOutputPath());
$tsSegmentPath360p = getSegmentOutputPath($outputPath, $fairPlayDrm360p->getOutputs()[0]->getOutputPath());
$tsSegmentPath240p = getSegmentOutputPath($outputPath, $fairPlayDrm240p->getOutputs()[0]->getOutputPath());

//Create a Variant Stream for Video
$videoStreamInfo1080p = createHlsVariantStreamInfo($encoding, $videoStream1080p, $tsMuxing1080p, $videoTsDrm1080p, $audioGroupId, $tsSegmentPath1080p, $variantStreamUri1080p);
$videoStreamInfo720p = createHlsVariantStreamInfo($encoding, $videoStream720p, $tsMuxing720p, $videoTsDrm720p, $audioGroupId, $tsSegmentPath720p, $variantStreamUri720p);
$videoStreamInfo480p = createHlsVariantStreamInfo($encoding, $videoStream480p, $tsMuxing480p, $videoTsDrm480p, $audioGroupId, $tsSegmentPath480p, $variantStreamUri480p);
$videoStreamInfo360p = createHlsVariantStreamInfo($encoding, $videoStream360p, $tsMuxing360p, $videoTsDrm360p, $audioGroupId, $tsSegmentPath360p, $variantStreamUri360p);
$videoStreamInfo240p = createHlsVariantStreamInfo($encoding, $videoStream240p, $tsMuxing240p, $videoTsDrm240p, $audioGroupId, $tsSegmentPath240p, $variantStreamUri240p);

$variantStream1080p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo1080p);
$variantStream720p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);
$variantStream480p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo480p);
$variantStream360p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo360p);
$variantStream240p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo240p);


$audioSegmentPath128 = getSegmentOutputPath($outputPath, $audioTsDrm128->getOutputs()[0]->getOutputPath());
$audioVariantStreamUri128 = "audio_1_" . $codecConfigAudio128->getBitrate() . "_variant.m3u8";

$audioMediaInfo128 = new MediaInfo();
$audioMediaInfo128->setGroupId($audioGroupId);
$audioMediaInfo128->setName("English");
$audioMediaInfo128->setLanguage("English");
$audioMediaInfo128->setAssocLanguage("en");
$audioMediaInfo128->setUri($audioVariantStreamUri128);
$audioMediaInfo128->setType(MediaInfoType::AUDIO);
$audioMediaInfo128->setEncodingId($encoding->getId());
$audioMediaInfo128->setStreamId($audioEncodingStream128->getId());
$audioMediaInfo128->setMuxingId($audioTsMuxing128->getId());
$audioMediaInfo128->setDrmId($audioTsDrm128->getId());
$audioMediaInfo128->setAutoselect(false);
$audioMediaInfo128->setDefault(false);
$audioMediaInfo128->setForced(false);
$audioMediaInfo128->setSegmentPath($audioSegmentPath128);

$apiClient->manifests()->hls()->createMediaInfo($masterPlaylist, $audioMediaInfo128);

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
 * @param Encoding    $encoding
 * @param Stream      $stream
 * @param TSMuxing    $tsMuxing
 * @param FairPlayDrm $fairPlayDrm
 * @param string      $audioGroupId
 * @param string      $segmentPath
 * @param string      $uri
 * @return StreamInfo
 */
function createHlsVariantStreamInfo(Encoding $encoding, Stream $stream, TSMuxing $tsMuxing, FairPlayDrm $fairPlayDrm, $audioGroupId, $segmentPath, $uri)
{
    $variantStream = new StreamInfo();
    $variantStream->setEncodingId($encoding->getId());
    $variantStream->setStreamId($stream->getId());
    $variantStream->setMuxingId($tsMuxing->getId());
    $variantStream->setDrmId($fairPlayDrm->getId());
    $variantStream->setAudio($audioGroupId);
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
 * @param int       $segmentDuration
 * @param string    $segmentNaming
 * @return TSMuxing
 * @throws BitmovinException
 */
function createTsMuxing($apiClient, $encoding, $stream, $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ, $segmentDuration = 4, $segmentNaming = 'segment_%number%.ts')
{
    $encodingOutputs = array();

    if ($output instanceof Output)
    {
        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl(array(new Acl($outputAcl)));
    }

    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());

    $tsMuxing = new TSMuxing();
    $tsMuxing->setSegmentLength($segmentDuration);
    $tsMuxing->setSegmentNaming($segmentNaming);
    $tsMuxing->setOutputs($encodingOutputs);
    $tsMuxing->setStreams(array($muxingStream));

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

/**
 * @param string (32 char, hex format) $key
 * @param string (32 char, hex format) $iv
 * @param string                       $uri
 * @param EncodingOutput[]             $outputs
 * @return FairPlayDrm
 */
function createFairPlayDrm($key, $iv, $uri, array $outputs)
{
    //CREATE FAIRPLAY DRM CONFIGURATION
    $fairPlayDrm = new FairPlayDrm($key, $iv, $uri, $outputs);
    return $fairPlayDrm;
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