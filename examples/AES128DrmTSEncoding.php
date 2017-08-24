<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\drm\AesEncryptionMethod;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\drms\AbstractDrm;
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
use Bitmovin\api\model\manifests\dash\DashDrmRepresentation;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

$apiKey = 'INSERT_YOUR_API_KEY';

// CREATE API CLIENT
$apiClient = new ApiClient($apiKey);

// CREATE ENCODING
$encoding = new Encoding('A Name for your aes drm encoding');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);

$encoding = $apiClient->encodings()->create($encoding);
$segmentLength = 4;

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$s3Input = $apiClient->inputs()->s3()->create($s3Input);
$videoInputPath = "path/to/your/input/file.mp4";

// S3 OUTPUT CONFIGURATION
$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$s3Output = $apiClient->outputs()->s3()->create($s3Output);
$outputPath = "path/to/your/output-destination/";

// AES CONFIGURATION
$aesKey = "0123456789abcdef0123456789abcdef";
$aesIV = "0123456789abcdef0123456789abcdef";

// CREATE VIDEO CODEC CONFIGURATIONS
$codecConfigVideo1080p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo1080p', H264Profile::HIGH, 4800000, null, 1080);
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720p', H264Profile::HIGH, 2400000, null, 720);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo480p', H264Profile::MAIN, 1200000, null, 480);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo360p', H264Profile::MAIN, 800000, null, 360);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo240p', H264Profile::BASELINE, 400000, null, 240);

// CREATE AUDIO CODEC CONFIGURATIONS
$codecConfigAudio128 = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000);

//CREATE INPUT STREAMS
//video stream of input file
$inputStreamVideo = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);
//audio stream of input file
$inputStreamAudio = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

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

// CREATE VIDEO MUXINGS (TS)
$tsMuxing1080p = createTsMuxing($apiClient, $encoding, $videoEncodingStream1080p, $segmentLength);
$tsMuxing720p = createTsMuxing($apiClient, $encoding, $videoEncodingStream720p, $segmentLength);
$tsMuxing480p = createTsMuxing($apiClient, $encoding, $videoEncodingStream480p, $segmentLength);
$tsMuxing360p = createTsMuxing($apiClient, $encoding, $videoEncodingStream360p, $segmentLength);
$tsMuxing240p = createTsMuxing($apiClient, $encoding, $videoEncodingStream240p, $segmentLength);

// CREATE AUDIO MUXING (TS)
$audioTsMuxing128 = createTsMuxing($apiClient, $encoding, $audioEncodingStream128, $segmentLength);

// ADD AES DRM TO VIDEO TS MUXINGs
$aesEncodingOutput1080p = createEncodingOutput($s3Output, $outputPath . 'video/1080p/hls/drm/');
$aesEncodingOutput720p = createEncodingOutput($s3Output, $outputPath . 'video/720p/hls/drm/');
$aesEncodingOutput480p = createEncodingOutput($s3Output, $outputPath . 'video/480p/hls/drm/');
$aesEncodingOutput360p = createEncodingOutput($s3Output, $outputPath . 'video/360p/hls/drm/');
$aesEncodingOutput240p = createEncodingOutput($s3Output, $outputPath . 'video/240p/hls/drm/');

$aesDrm1080p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput1080p), 'keyfile.key');
$aesDrm720p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput720p), 'keyfile.key');
$aesDrm480p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput480p), 'keyfile.key');
$aesDrm360p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput360p), 'keyfile.key');
$aesDrm240p = createAes128Drm($aesKey, $aesIV, array($aesEncodingOutput240p), 'keyfile.key');

$videoTsDrm1080p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing1080p)->aes()->create($aesDrm1080p);
$videoTsDrm720p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing720p)->aes()->create($aesDrm720p);
$videoTsDrm480p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing480p)->aes()->create($aesDrm480p);
$videoTsDrm360p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing360p)->aes()->create($aesDrm360p);
$videoTsDrm240p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($tsMuxing240p)->aes()->create($aesDrm240p);

// CREATE DRM TO AUDIO TS MUXINGs
$audioAesEncodingOutput128 = createEncodingOutput($s3Output, $outputPath . 'audio/128kbps/hls/drm/');

$audioAesDrm128 = createAes128Drm($aesKey, $aesIV, array($audioAesEncodingOutput128), 'keyfile.key');
$audioTsDrm128 = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($audioTsMuxing128)->aes()->create($audioAesDrm128);

$apiClient->encodings()->start($encoding);

do {
    $status = $apiClient->encodings()->status($encoding);
    var_dump(date_create(null, new DateTimeZone('UTC'))->getTimestamp() . ": " . $status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

if ($status->getStatus() === Status::ERROR) {
    var_dump("Encoding failed!");
    exit();
}


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

$tsSegmentPath1080p = getSegmentOutputPath($outputPath, $videoTsDrm1080p->getOutputs()[0]->getOutputPath());
$tsSegmentPath720p = getSegmentOutputPath($outputPath, $videoTsDrm720p->getOutputs()[0]->getOutputPath());
$tsSegmentPath480p = getSegmentOutputPath($outputPath, $videoTsDrm480p->getOutputs()[0]->getOutputPath());
$tsSegmentPath360p = getSegmentOutputPath($outputPath, $videoTsDrm360p->getOutputs()[0]->getOutputPath());
$tsSegmentPath240p = getSegmentOutputPath($outputPath, $videoTsDrm240p->getOutputs()[0]->getOutputPath());

//Create a Variant Stream for Video
$videoStreamInfo1080p = createHlsVariantStreamInfo($encoding, $videoEncodingStream1080p, $tsMuxing1080p, $audioGroupId, $tsSegmentPath1080p, $variantStreamUri1080p, $videoTsDrm1080p);
$videoStreamInfo720p = createHlsVariantStreamInfo($encoding, $videoEncodingStream720p, $tsMuxing720p, $audioGroupId, $tsSegmentPath720p, $variantStreamUri720p, $videoTsDrm720p);
$videoStreamInfo480p = createHlsVariantStreamInfo($encoding, $videoEncodingStream480p, $tsMuxing480p, $audioGroupId, $tsSegmentPath480p, $variantStreamUri480p, $videoTsDrm480p);
$videoStreamInfo360p = createHlsVariantStreamInfo($encoding, $videoEncodingStream360p, $tsMuxing360p, $audioGroupId, $tsSegmentPath360p, $variantStreamUri360p, $videoTsDrm360p);
$videoStreamInfo240p = createHlsVariantStreamInfo($encoding, $videoEncodingStream240p, $tsMuxing240p, $audioGroupId, $tsSegmentPath240p, $variantStreamUri240p, $videoTsDrm240p);
$variantStream1080p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo1080p);
$variantStream720p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);
$variantStream480p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo480p);
$variantStream360p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo360p);
$variantStream240p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo240p);

$audioSegmentPath128 = getSegmentOutputPath($outputPath, $audioAesDrm128->getOutputs()[0]->getOutputPath());
$audioVariantStreamUri128 = "audio_1_" . $codecConfigAudio128->getBitrate() . "_variant.m3u8";

$audioMediaInfo128 = new MediaInfo();
$audioMediaInfo128->setGroupId($audioGroupId);
$audioMediaInfo128->setName("English");
$audioMediaInfo128->setLanguage("English");
$audioMediaInfo128->setAssocLanguage("en");
$audioMediaInfo128->setUri($audioVariantStreamUri128);
$audioMediaInfo128->setType(MediaInfoType::AUDIO);
$audioMediaInfo128->setEncodingId($encoding->getId());
$audioMediaInfo128->setDrmId($audioTsDrm128->getId());
$audioMediaInfo128->setStreamId($audioEncodingStream128->getId());
$audioMediaInfo128->setMuxingId($audioTsMuxing128->getId());
$audioMediaInfo128->setAutoselect(false);
$audioMediaInfo128->setDefault(false);
$audioMediaInfo128->setForced(false);
$audioMediaInfo128->setSegmentPath($audioSegmentPath128);

$apiClient->manifests()->hls()->createMediaInfo($masterPlaylist, $audioMediaInfo128);

//Start Manifest Creation
$response = $apiClient->manifests()->hls()->start($masterPlaylist);

do {
    $status = $apiClient->manifests()->hls()->status($masterPlaylist);
    var_dump($status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

var_dump("Master Playlist finished");

//#####################################################################################################################

function createAes128Drm($key, $iv, $outputs, $keyFileUri)
{
    return new AesDrm(AesEncryptionMethod::AES_128, $key, $iv, $outputs, $keyFileUri);
}

/**
 * @param Encoding      $encoding
 * @param Stream        $stream
 * @param TSMuxing      $tsMuxing
 * @param string        $audioGroupId
 * @param string        $segmentPath
 * @param string        $uri
 * @param AbstractDrm   $drm
 * @return StreamInfo
 */
function createHlsVariantStreamInfo(Encoding $encoding, Stream $stream, TSMuxing $tsMuxing, $audioGroupId, $segmentPath, $uri, AbstractDrm $drm)
{
    $variantStream = new StreamInfo();
    $variantStream->setEncodingId($encoding->getId());
    $variantStream->setStreamId($stream->getId());
    $variantStream->setMuxingId($tsMuxing->getId());
    $variantStream->setAudio($audioGroupId);
    $variantStream->setSegmentPath($segmentPath);
    $variantStream->setUri($uri);
    $variantStream->setDrmId($drm->getId());

    return $variantStream;
}

/**
 * @param ApiClient $apiClient
 * @param Encoding  $encoding
 * @param Stream    $stream
 * @param Output    $output
 * @param string    $outputPath
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
    $encodingOutputs = null;

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
 * @param Stream    $stream
 * @param int       $segmentDuration
 * @param string    $segmentNaming
 * @return TSMuxing
 * @throws BitmovinException
 */
function createTsMuxing($apiClient, $encoding, $stream, $segmentDuration = 4, $segmentNaming = 'segment_%number%.ts')
{
    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());

    $tsMuxing = new TSMuxing();
    $tsMuxing->setSegmentLength($segmentDuration);
    $tsMuxing->setSegmentNaming($segmentNaming);
    $tsMuxing->setStreams(array($muxingStream));

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