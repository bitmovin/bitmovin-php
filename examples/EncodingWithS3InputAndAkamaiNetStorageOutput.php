<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\CodecConfigType;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
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
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\AkamaiNetStorageOutput;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

$bitmovinApiKey = 'YOUR_BITMOVIN_API_KEY';

$encodingName = 'Encoding Sample S3Input Akamai NetStorage Output';
$cloudRegion = CloudRegion::AUTO;

//S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR_ACCESS_KEY';
$s3InputSecretKey = 'YOUR_SECRET_KEY';
$s3InputBucketName = 'YOUR_BUCKETNAME';
$videoInputPath = 'path/to/your/inputfile.mp4';

//S3 OUTPUT CONFIGURATION
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
$encoding = new Encoding($encodingName);
$encoding->setCloudRegion($cloudRegion);
$encoding = $apiClient->encodings()->create($encoding);

// CREATE INPUT
$input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$input = $apiClient->inputs()->s3()->create($input);

// CREATE OUTPUT
$output = new AkamaiNetStorageOutput($akamaiNetStorageHost, $akamaiNetStorageUsername, $akamaiNetStoragePassword);
$output = $apiClient->outputs()->akamaiNetStorage()->create($output);

//CREATE VIDEO/AUDIO INPUT STREAMS
$inputStreamVideo = new InputStream($input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio = new InputStream($input, $videoInputPath, SelectionMode::AUTO);

// CREATE VIDEO CODEC CONFIGURATIONS
$videoEncodingConfigs = array();
foreach ($videoEncodingProfiles as $encodingProfile)
{
    $encodingProfileName = "h264_" . $encodingProfile["bitrate"];
    $videoEncodingConfig = array();
    $videoEncodingConfig['profile'] = $encodingProfile;
    $videoEncodingConfig['codec'] = createH264VideoCodecConfiguration($apiClient, $encodingProfileName, $encodingProfile["profile"], $encodingProfile["bitrate"], null, $encodingProfile["height"]);
    $videoEncodingConfigs[] = $videoEncodingConfig;
}
// CREATE AUDIO CODEC CONFIGURATIONS
$audioEncodingConfigs = array();
foreach ($audioEncodingProfiles as $encodingProfile)
{
    $encodingProfileName = "aac_" . $encodingProfile["bitrate"];
    $audioEncodingConfig = array();
    $audioEncodingConfig['profile'] = $encodingProfile;
    $audioEncodingConfig['codec'] = createAACAudioCodecConfiguration($apiClient, $encodingProfileName, $encodingProfile["bitrate"]);;
    $audioEncodingConfigs[] = $audioEncodingConfig;
}

// CREATE VIDEO STREAMS AND MUXINGS
foreach ($videoEncodingConfigs as $key => $videoEncodingConfig)
{
    $encodingProfile = $videoEncodingConfig['profile'];
    // CREATE VIDEO STREAM
    $videoStream = new Stream($videoEncodingConfig['codec'], array($inputStreamVideo));
    $videoEncodingConfigs[$key]['stream'] = $apiClient->encodings()->streams($encoding)->create($videoStream);
    // CREATE FMP4 MUXING FOR VIDEO
    $fmp4MuxingOutputPath = $outputPath . 'video/fmp4/h264/' . $encodingProfile['height'] . 'p_' . $encodingProfile['bitrate'] . '/';
    $videoEncodingConfigs[$key]['fmp4_muxing'] = createFmp4Muxing($apiClient, $encoding, $videoEncodingConfigs[$key]['stream'], $output, $fmp4MuxingOutputPath);
    // CREATE TS MUXING FOR VIDEO
    $tsMuxingOutputPath = $outputPath . 'video/ts/h264/' . $encodingProfile['height'] . 'p_' . $encodingProfile['bitrate'] . '/';
    $videoEncodingConfigs[$key]['ts_muxing'] = createTsMuxing($apiClient, $encoding, $videoEncodingConfigs[$key]['stream'], $output, $tsMuxingOutputPath);
}

// CREATE AUDIO STREAMS AND MUXINGS
foreach ($audioEncodingConfigs as $key => $audioEncodingConfig)
{
    $encodingProfile = $audioEncodingConfig['profile'];
    // CREATE AUDIO STREAM
    $audioStream = new Stream($audioEncodingConfig['codec'], array($inputStreamAudio));
    $audioEncodingConfigs[$key]['stream'] = $apiClient->encodings()->streams($encoding)->create($audioStream);
    // CREATE FMP4 MUXING FOR AUDIO
    $fmp4MuxingOutputPath = $outputPath . 'audio/fmp4/aac/' . $encodingProfile['bitrate'] . '/';
    $audioEncodingConfigs[$key]['fmp4_muxing'] = createFmp4Muxing($apiClient, $encoding, $audioEncodingConfigs[$key]['stream'], $output, $fmp4MuxingOutputPath);
    // CREATE TS MUXING FOR AUDIO
    $tsMuxingOutputPath = $outputPath . 'audio/ts/aac/' . $encodingProfile['bitrate'] . '/';
    $audioEncodingConfigs[$key]['ts_muxing'] = createTsMuxing($apiClient, $encoding, $audioEncodingConfigs[$key]['stream'], $output, $tsMuxingOutputPath);
}

// START THE ENCODING PROCESS
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
    exit(1);
}

//###########################################################################################

//CREATE MANIFEST OUTPUT CONFIGURATION
$manifestOutput = new EncodingOutput($output);
$manifestOutput->setOutputPath($outputPath);
$acl = new Acl(AclPermission::ACL_PUBLIC_READ);
$manifestOutput->setAcl([$acl]);

// CREATE DASH MANIFEST
$manifestName = "stream.mpd";
$manifestType = DashMuxingType::TYPE_TEMPLATE;

$manifest = new DashManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$manifest = $apiClient->manifests()->dash()->create($manifest);

//Add Period
$period = new Period();
$period = $apiClient->manifests()->dash()->createPeriod($manifest, $period);

$dashAdaptionSet = null;
$videoAdaptionSet = null;
$audioAdaptionSet = null;

$allEncodingConfigs = array_merge($videoEncodingConfigs, $audioEncodingConfigs);
foreach ($allEncodingConfigs as $encodingConfig)
{
    /** @var FMP4Muxing $fmp4Muxing */
    $fmp4Muxing = $encodingConfig['fmp4_muxing'];
    $streamDetails = $apiClient->encodings()->streams($encoding)->getById($fmp4Muxing->getStreams()[0]->getStreamId());
    $codecConfigType = $apiClient->codecConfigurations()->type()->getTypeById($streamDetails->getCodecConfigId());

    $isVideoMuxing = in_array($codecConfigType->getType(), array(CodecConfigType::H264, CodecConfigType::H265, CodecConfigType::VP9));
    $isAudioMuxing = in_array($codecConfigType->getType(), array(CodecConfigType::AAC));

    $segmentPath = getSegmentOutputPath($outputPath, $fmp4Muxing->getOutputs()[0]->getOutputPath());

    //CREATE REPRESENTATION
    $representation = new DashRepresentation();
    $representation->setType($manifestType);
    $representation->setEncodingId($encoding->getId());
    $representation->setMuxingId($fmp4Muxing->getId());
    $representation->setSegmentPath($segmentPath);

    //CREATE VIDEO ADAPTATION SET IF NOT YET AVAILABLE
    if ($isVideoMuxing && is_null($videoAdaptionSet))
    {
        $videoAdaptionSet = new VideoAdaptationSet();
        $videoAdaptionSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($manifest, $period, $videoAdaptionSet);
    }
    //ADD REPRESENTATION TO VIDEO ADAPTATION SET
    if ($isVideoMuxing)
        $apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $representation);

    //CREATE AUDIO ADAPTATION SET IF NOT YET AVAILABLE
    if ($isAudioMuxing && is_null($audioAdaptionSet))
    {
        $audioAdaptionSet = new AudioAdaptationSet();
        $audioAdaptionSet->setLang("en");
        $audioAdaptionSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($manifest, $period, $audioAdaptionSet);
    }
    //ADD REPRESENTATION TO AUDIO ADAPTATION SET
    if ($isAudioMuxing)
        $apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $audioAdaptionSet, $representation);
}

//Start Manifest Creation
$response = $apiClient->manifests()->dash()->start($manifest);

do
{
    $status = $apiClient->manifests()->dash()->status($manifest);
    var_dump($status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

var_dump("Master Playlist finished");

// CREATE HLS PLAYLIST
$masterPlaylistName = "stream.m3u8";
$audioGroupId = 'audio';

$masterPlaylist = new HlsManifest();
$masterPlaylist->setOutputs(array($manifestOutput));
$masterPlaylist->setManifestName($masterPlaylistName);
$masterPlaylist = $apiClient->manifests()->hls()->create($masterPlaylist);

// Create Variant-Stream-Infos from each FMP4 Muxing for Video
foreach ($videoEncodingConfigs as $videoEncodingConfig)
{
    /** @var FMP4Muxing $fmp4Muxing */
    $fmp4Muxing = $videoEncodingConfig['ts_muxing'];
    /** @var Stream $videoStream */
    $videoStream = $videoEncodingConfig['stream'];
    $encodingProfile = $videoEncodingConfig['profile'];
    $variantStreamUri = 'video_h264_' . $encodingProfile['height'] . '_' . $encodingProfile['bitrate'] . '.m3u8';
    $segmentPath = getSegmentOutputPath($outputPath, $fmp4Muxing->getOutputs()[0]->getOutputPath());

    $variantStream = new StreamInfo();
    $variantStream->setEncodingId($encoding->getId());
    $variantStream->setStreamId($videoStream->getId());
    $variantStream->setMuxingId($fmp4Muxing->getId());
    $variantStream->setAudio($audioGroupId);
    $variantStream->setSegmentPath($segmentPath);
    $variantStream->setUri($variantStreamUri);
    $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $variantStream);
}

// Create Media-Infos from each FMP4 Muxing for Video
foreach ($audioEncodingConfigs as $key => $audioEncodingConfig)
{
    /** @var FMP4Muxing $fmp4Muxing */
    $fmp4Muxing = $audioEncodingConfig['ts_muxing'];
    /** @var Stream $audioStream */
    $audioStream = $audioEncodingConfig['stream'];
    $encodingProfile = $audioEncodingConfig['profile'];
    $variantStreamUri = 'audio_aac_' . $encodingProfile['bitrate'] . '.m3u8';
    $segmentPath = getSegmentOutputPath($outputPath, $fmp4Muxing->getOutputs()[0]->getOutputPath());

    $mediaInfo = new MediaInfo();
    $mediaInfo->setGroupId($audioGroupId);
    $mediaInfo->setSegmentPath($segmentPath);
    $mediaInfo->setName("English");
    $mediaInfo->setLanguage("English");
    $mediaInfo->setAssocLanguage("en");
    $mediaInfo->setUri($variantStreamUri);
    $mediaInfo->setType(MediaInfoType::AUDIO);
    $mediaInfo->setEncodingId($encoding->getId());
    $mediaInfo->setStreamId($audioStream->getId());
    $mediaInfo->setMuxingId($fmp4Muxing->getId());
    $mediaInfo->setAutoselect(true);
    $mediaInfo->setDefault(true);
    $apiClient->manifests()->hls()->createMediaInfo($masterPlaylist, $mediaInfo);
}

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
function createFmp4Muxing($apiClient, $encoding, $stream, $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ, $initSegmentName = 'init.mp4', $segmentDuration = 4, $segmentNaming = 'segment_%number%.m4s')
{
    $encodingOutputs = null;
    if (!is_null($output))
    {
        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl(array(new Acl($outputAcl)));
        $encodingOutputs[] = $encodingOutput;
    }
    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());

    $fmp4Muxing = new FMP4Muxing();
    $fmp4Muxing->setInitSegmentName($initSegmentName);
    $fmp4Muxing->setSegmentLength($segmentDuration);
    $fmp4Muxing->setSegmentNaming($segmentNaming);
    $fmp4Muxing->setOutputs($encodingOutputs);
    $fmp4Muxing->setStreams(array($muxingStream));

    return $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing);
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
        $encodingOutputs[] = $encodingOutput;
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