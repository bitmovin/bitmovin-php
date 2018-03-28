<?php
use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\CodecConfigType;
use Bitmovin\api\enum\codecConfigurations\H265Profile;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H265VideoCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\VP9VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\muxing\WebmMuxing;
use Bitmovin\api\model\encodings\StartEncodingTrimming;
use Bitmovin\api\model\encodings\StartEncodingRequest;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\dash\WebmRepresentation;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

$bitmovinApiKey = 'YOUR_BITMOVIN_API_KEY';
$encodingName = 'Multicodec encoding with time offset';
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
//time offset settings
$offset = 10.0;
// $duration = 10.0;

$h265VideoEncodingProfiles = array(
    array("height" => 1080, "bitrate" => 4800000, "profile" => H265Profile::MAIN),
    array("height" => 720,  "bitrate" => 2400000, "profile" => H265Profile::MAIN),
    array("height" => 480,  "bitrate" => 1200000, "profile" => H265Profile::MAIN),
    array("height" => 360,  "bitrate" => 800000,  "profile" => H265Profile::MAIN),
    array("height" => 240,  "bitrate" => 400000,  "profile" => H265Profile::MAIN)
);

$vp9VideoEncodingProfiles = array(
    array("height" => 1080, "bitrate" => 4800000),
    array("height" => 720,  "bitrate" => 2400000),
    array("height" => 480,  "bitrate" => 1200000),
    array("height" => 360,  "bitrate" => 800000),
    array("height" => 240,  "bitrate" => 400000)
);

$h264VideoEncodingProfiles = array(
    array("height" => 1080, "bitrate" => 4800000, "profile" => H264Profile::HIGH),
    array("height" => 720,  "bitrate" => 2400000, "profile" => H264Profile::HIGH),
    array("height" => 480,  "bitrate" => 1200000, "profile" => H264Profile::HIGH),
    array("height" => 360,  "bitrate" => 300000,  "profile" => H264Profile::HIGH),
    array("height" => 240,  "bitrate" => 200000,  "profile" => H264Profile::HIGH)
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
$output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$output = $apiClient->outputs()->s3()->create($output);
//CREATE VIDEO/AUDIO INPUT STREAMS
$inputStreamVideo = new InputStream($input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio = new InputStream($input, $videoInputPath, SelectionMode::AUTO);

$videoEncodingConfigs = array();
$videoEncodingConfigs265 = createVideoCodecConfigAndStream($videoEncodingConfigs, 'h265', $h265VideoEncodingProfiles, $apiClient, $inputStreamVideo, $encoding, $outputPath, $output);
$videoEncodingConfigs264 = createVideoCodecConfigAndStream($videoEncodingConfigs, 'h264', $h264VideoEncodingProfiles, $apiClient, $inputStreamVideo, $encoding, $outputPath, $output);
$videoEncodingConfigsVp9 = createVideoCodecConfigAndStream($videoEncodingConfigs, 'vp9', $vp9VideoEncodingProfiles, $apiClient, $inputStreamVideo, $encoding, $outputPath, $output);
$videoEncodingConfigs = array_merge($videoEncodingConfigs265, $videoEncodingConfigsVp9, $videoEncodingConfigs264);

// CREATE VIDEO CODEC CONFIGS   AND STREAMS
function createVideoCodecConfigAndStream ($videoEncodingConfigs, $codec, $profile, $apiClient, $inputStreamVideo, $encoding, $outputPath, $output)
{

// CREATE VIDEO CODEC CONFIGURATIONS
    foreach ($profile as $encodingProfile) {
        $encodingProfileName = $codec . '_' . $encodingProfile["bitrate"];
        $videoEncodingConfig = array();
        $videoEncodingConfig['profile'] = $encodingProfile;
        if ($codec == 'h265') {
            $videoEncodingConfig['codec'] = createH265VideoCodecConfiguration($apiClient, $encodingProfileName, $encodingProfile["profile"], $encodingProfile["bitrate"], null, $encodingProfile["height"]);
        }
        elseif ($codec == 'vp9') {
            $videoEncodingConfig['codec'] = createVP9VideoCodecConfiguration($apiClient, $encodingProfileName, $encodingProfile["bitrate"], null, $encodingProfile["height"]);
        }
        else {
            $videoEncodingConfig['codec'] = createH264VideoCodecConfiguration($apiClient, $encodingProfileName, $encodingProfile["profile"], $encodingProfile["bitrate"], null, $encodingProfile["height"]);
        }
        $videoEncodingConfig['codecName'] = $codec;
        $videoEncodingConfigs[] = $videoEncodingConfig;
    }

// CREATE VIDEO STREAMS AND MUXINGS
    foreach ($videoEncodingConfigs as $key => $videoEncodingConfig) {
        if($codec == 'vp9')
        {
            $encodingProfile = $videoEncodingConfig['profile'];
            // CREATE VIDEO STREAM
            $videoStream = new Stream($videoEncodingConfig['codec'], array($inputStreamVideo));
            $videoEncodingConfigs[$key]['stream'] = $apiClient->encodings()->streams($encoding)->create($videoStream);
            // CREATE WEBM MUXING FOR VIDEO
            $webMuxingOutputPath = $outputPath . 'video/webm/'. $codec .'/' . $encodingProfile['height'] . 'p_' . $encodingProfile['bitrate'] . '/';
            $videoEncodingConfigs[$key]['webm_muxing'] = createWebmMuxing($apiClient, $encoding, $videoEncodingConfigs[$key]['stream'], $output, $webMuxingOutputPath);

        }
        else {
            $encodingProfile = $videoEncodingConfig['profile'];
            // CREATE VIDEO STREAM
            $videoStream = new Stream($videoEncodingConfig['codec'], array($inputStreamVideo));
            $videoEncodingConfigs[$key]['stream'] = $apiClient->encodings()->streams($encoding)->create($videoStream);
            // CREATE FMP4 MUXING FOR VIDEO
            $fmp4MuxingOutputPath = $outputPath . 'video/fmp4/'. $codec .'/' . $encodingProfile['height'] . 'p_' . $encodingProfile['bitrate'] . '/';
            $videoEncodingConfigs[$key]['fmp4_muxing'] = createFmp4Muxing($apiClient, $encoding, $videoEncodingConfigs[$key]['stream'], $output, $fmp4MuxingOutputPath);
            if($codec == 'h264') {
                $tsMuxingOutputPath = $outputPath . 'video/ts/' . $codec . '/' . $encodingProfile['height'] . 'p_' . $encodingProfile['bitrate'] . '/';
                $videoEncodingConfigs[$key]['ts_muxing'] = createTsMuxing($apiClient, $encoding, $videoEncodingConfigs[$key]['stream'], $output, $tsMuxingOutputPath);
            }
        }
    }
    return $videoEncodingConfigs;
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
    // CREATE FMP4 MUXING FOR AUDIO
    $tsMuxingOutputPath = $outputPath . 'audio/ts/aac/' . $encodingProfile['bitrate'] . '/';
    $audioEncodingConfigs[$key]['ts_muxing'] = createTsMuxing($apiClient, $encoding, $audioEncodingConfigs[$key]['stream'], $output, $tsMuxingOutputPath);
}
// START THE ENCODING PROCESS
$startEncodingRequest = new StartEncodingRequest();
$startEncodingTrimming = new StartEncodingTrimming();
$startEncodingTrimming->setOffset($offset);
// $startEncodingTrimming->setDuration($duration);
$startEncodingRequest->setTrimming($startEncodingTrimming);
$apiClient->encodings()->startWithEncodingRequest($encoding, $startEncodingRequest);

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
$videoAdaptionSet = array();
$audioAdaptionSet = null;
$allEncodingConfigs = array_merge($videoEncodingConfigs, $audioEncodingConfigs);
foreach ($allEncodingConfigs as $encodingConfig)
{
    /** @var FMP4Muxing $fmp4Muxing */
    if (array_key_exists('webm_muxing', $encodingConfig)) {
        $webmMuxing = $encodingConfig['webm_muxing'];
        $codecType = CodecConfigType::VP9;
        $segmentPath = getSegmentOutputPath($outputPath, $webmMuxing->getOutputs()[0]->getOutputPath());
        //CREATE REPRESENTATION
        $representation = new WebmRepresentation();
        $representation->setType($manifestType);
        $representation->setEncodingId($encoding->getId());
        $representation->setMuxingId($webmMuxing->getId());
        $representation->setSegmentPath($segmentPath);
        $isAudioMuxing = false;
    }
    else {
        $fmp4Muxing = $encodingConfig['fmp4_muxing'];
        $streamDetails = $apiClient->encodings()->streams($encoding)->getById($fmp4Muxing->getStreams()[0]->getStreamId());
        $codecConfigType = $apiClient->codecConfigurations()->type()->getTypeById($streamDetails->getCodecConfigId());
        $segmentPath = getSegmentOutputPath($outputPath, $fmp4Muxing->getOutputs()[0]->getOutputPath());
        //CREATE REPRESENTATION
        $representation = new DashRepresentation();
        $representation->setType($manifestType);
        $representation->setEncodingId($encoding->getId());
        $representation->setMuxingId($fmp4Muxing->getId());
        $representation->setSegmentPath($segmentPath);
        $isAudioMuxing = in_array($codecConfigType->getType(), array(CodecConfigType::AAC));
        $codecType = $codecConfigType->getType();
    }

    //CREATE VIDEO ADAPTATION SET IF NOT YET AVAILABLE
    if (!$isAudioMuxing && !array_key_exists($codecType, $videoAdaptionSet))
    {
        $videoAdaptionSet[$codecType] = new VideoAdaptationSet();
        $videoAdaptionSet[$codecType] = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($manifest, $period, $videoAdaptionSet[$codecType]);
    }
    //ADD REPRESENTATION TO VIDEO ADAPTATION SET
    if (!$isAudioMuxing)
        $apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet[$codecType], $representation);
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
$tsAudioGroupId = 'tsAudio';
$masterPlaylist = new HlsManifest();
$masterPlaylist->setOutputs(array($manifestOutput));
$masterPlaylist->setManifestName($masterPlaylistName);
$masterPlaylist = $apiClient->manifests()->hls()->create($masterPlaylist);
// Create Variant-Stream-Infos from each FMP4 Muxing for Video
foreach ($videoEncodingConfigs as $videoEncodingConfig)
{
    if (array_key_exists('fmp4_muxing', $videoEncodingConfig) && $videoEncodingConfig['codecName'] == 'h265') {
        /** @var FMP4Muxing $fmp4Muxing */
        $fmp4Muxing = $videoEncodingConfig['fmp4_muxing'];
        /** @var Stream $videoStream */
        $videoStream = $videoEncodingConfig['stream'];
        $encodingProfile = $videoEncodingConfig['profile'];
        $variantStreamUri = $videoEncodingConfig['codecName'] . $encodingProfile['height'] . '_' . $encodingProfile['bitrate'] . '.m3u8';
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
    else if (array_key_exists('ts_muxing', $videoEncodingConfig)) {
        /** @var FMP4Muxing $fmp4Muxing */
        $tsMuxing = $videoEncodingConfig['ts_muxing'];
        /** @var Stream $videoStream */
        $videoStream = $videoEncodingConfig['stream'];
        $encodingProfile = $videoEncodingConfig['profile'];
        $variantStreamUri = $videoEncodingConfig['codecName'] . $encodingProfile['height'] . '_' . $encodingProfile['bitrate'] . '.m3u8';
        $segmentPath = getSegmentOutputPath($outputPath, $tsMuxing->getOutputs()[0]->getOutputPath());
        $variantStream = new StreamInfo();
        $variantStream->setEncodingId($encoding->getId());
        $variantStream->setStreamId($videoStream->getId());
        $variantStream->setMuxingId($tsMuxing->getId());
        $variantStream->setAudio($tsAudioGroupId);
        $variantStream->setSegmentPath($segmentPath);
        $variantStream->setUri($variantStreamUri);
        $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $variantStream);
    }
}

foreach ($audioEncodingConfigs as $key => $audioEncodingConfig)
{
    $audioStream = $audioEncodingConfig['stream'];
    $tsMuxing = $audioEncodingConfig['ts_muxing'];
    $encodingProfile = $audioEncodingConfig['profile'];
    $variantStreamUri = 'audio_aac_ts' . $encodingProfile['bitrate'] . '.m3u8';
    $mediaInfo = new MediaInfo();
    $mediaInfo->setMuxingId($tsMuxing->getId());
    $mediaInfo->setGroupId($tsAudioGroupId);
    $segmentPath = getSegmentOutputPath($outputPath, $tsMuxing->getOutputs()[0]->getOutputPath());
    $mediaInfo->setSegmentPath($segmentPath);
    $mediaInfo->setName("English");
    $mediaInfo->setLanguage("English");
    $mediaInfo->setAssocLanguage("en");
    $mediaInfo->setUri($variantStreamUri);
    $mediaInfo->setType(MediaInfoType::AUDIO);
    $mediaInfo->setEncodingId($encoding->getId());
    $mediaInfo->setStreamId($audioStream->getId());
    $mediaInfo->setAutoselect(true);
    $mediaInfo->setDefault(true);
    $apiClient->manifests()->hls()->createMediaInfo($masterPlaylist, $mediaInfo);

    $audioStream = $audioEncodingConfig['stream'];
    $fmp4Muxing = $audioEncodingConfig['fmp4_muxing'];
    $encodingProfile = $audioEncodingConfig['profile'];
    $fmp4VariantStreamUri = 'audio_aac_fmp4' . $encodingProfile['bitrate'] . '.m3u8';
    $fmp4MediaInfo = new MediaInfo();
    $fmp4MediaInfo->setMuxingId($fmp4Muxing->getId());
    $fmp4MediaInfo->setGroupId($audioGroupId);
    $fmp4SegmentPath = getSegmentOutputPath($outputPath, $fmp4Muxing->getOutputs()[0]->getOutputPath());
    $fmp4MediaInfo->setSegmentPath($fmp4SegmentPath);
    $fmp4MediaInfo->setName("English");
    $fmp4MediaInfo->setLanguage("English");
    $fmp4MediaInfo->setAssocLanguage("en");
    $fmp4MediaInfo->setUri($fmp4VariantStreamUri);
    $fmp4MediaInfo->setType(MediaInfoType::AUDIO);
    $fmp4MediaInfo->setEncodingId($encoding->getId());
    $fmp4MediaInfo->setStreamId($audioStream->getId());
    $fmp4MediaInfo->setAutoselect(false);
    $fmp4MediaInfo->setDefault(false);
    $apiClient->manifests()->hls()->createMediaInfo($masterPlaylist, $fmp4MediaInfo);
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
 * @param string    $initSegmentName
 * @param int       $segmentDuration
 * @param string    $segmentNaming
 * @return FMP4Muxing
 * @throws BitmovinException
 */
function createWebmMuxing($apiClient, $encoding, $stream, $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ, $initSegmentName = 'init.mp4', $segmentDuration = 4, $segmentNaming = 'segment_%number%.webm')
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
    $webmMuxing = new WebmMuxing();
    $webmMuxing->setInitSegmentName($initSegmentName);
    $webmMuxing->setSegmentLength($segmentDuration);
    $webmMuxing->setSegmentNaming($segmentNaming);
    $webmMuxing->setOutputs($encodingOutputs);
    $webmMuxing->setStreams(array($muxingStream));
    return $apiClient->encodings()->muxings($encoding)->webmMuxing()->create($webmMuxing);
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
 * @return H265VideoCodecConfiguration
 * @throws BitmovinException
 */
function createH265VideoCodecConfiguration($apiClient, $name, $profile, $bitrate, $width = null, $height = null, $rate = null)
{
    $codecConfigVideo = new H265VideoCodecConfiguration($name, $profile, $bitrate, $rate);
    $codecConfigVideo->setDescription($bitrate . '_' . $name);
    $codecConfigVideo->setWidth($width);
    $codecConfigVideo->setHeight($height);
    return $apiClient->codecConfigurations()->videoH265()->create($codecConfigVideo);
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
 * @param float     $rate
 * @param integer   $width
 * @param integer   $height
 * @return VP9VideoCodecConfiguration
 * @throws BitmovinException
 */
function createVP9VideoCodecConfiguration($apiClient, $name, $bitrate, $width = null, $height = null, $rate = null)
{
    $codecConfigVideo = new VP9VideoCodecConfiguration($name, $bitrate, $rate);
    $codecConfigVideo->setDescription($bitrate . '_' . $name);
    $codecConfigVideo->setWidth($width);
    $codecConfigVideo->setHeight($height);
    return $apiClient->codecConfigurations()->videoVP9()->create($codecConfigVideo);
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
