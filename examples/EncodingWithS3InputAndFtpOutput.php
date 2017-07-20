<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\output\FtpTransferVersion;
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
use Bitmovin\api\model\manifests\dash\DashDrmRepresentation;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\FtpOutput;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');

// CREATE ENCODING
$encoding = new Encoding('S3 Input FTP Output Example');
$encoding->setCloudRegion(CloudRegion::AWS_EU_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);
$segmentLength = 4;

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$videoInputPath = "path/to/your/input/file.mp4";
$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$input = $apiClient->inputs()->s3()->create($s3Input);

// FTP OUTPUT CONFIGURATION
$ftpHost = 'YOUR-FTP-HOST';
$ftpUserName = 'YOUR-FTP-USERNAME';
$ftpPassword = 'YOUR-FTP-PASSWORD';
$outputPath = 'path/to/your/output/folder/';
$ftpOutput = new FTPOutput($ftpHost, $ftpUserName, $ftpPassword);
$ftpOutput->setPort(21);
$ftpOutput->setTransferVersion(FtpTransferVersion::TRANSFER_VERSION_1_1_0);
$ftpOutput->setMaxConcurrentConnections(20);
$output = $apiClient->outputs()->ftp()->create($ftpOutput);

// CREATE VIDEO CODEC CONFIGURATIONS
$codecConfigVideo720pHigh = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720pHigh', H264Profile::HIGH, 3000000, null, 1080);
$codecConfigVideo720pLow = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720pLow', H264Profile::HIGH, 2400000, null, 720);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo480p', H264Profile::MAIN, 1200000, null, 480);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo360p', H264Profile::MAIN, 800000, null, 360);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo240p', H264Profile::BASELINE, 400000, null, 240);

// CREATE AUDIO CODEC CONFIGURATIONS
$codecConfigAudio128 = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000);

//CREATE INPUT STREAMS
//video stream of input file
$inputStreamVideo = new InputStream($input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);
//audio stream of input file
$inputStreamAudio = new InputStream($input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

// CREATE VIDEO STREAMS
$videoStream720pHigh = new Stream($codecConfigVideo720pHigh, array($inputStreamVideo));
$videoStream720pLow = new Stream($codecConfigVideo720pLow, array($inputStreamVideo));
$videoStream480p = new Stream($codecConfigVideo480p, array($inputStreamVideo));
$videoStream360p = new Stream($codecConfigVideo360p, array($inputStreamVideo));
$videoStream240p = new Stream($codecConfigVideo240p, array($inputStreamVideo));
$videoEncodingStream720pHigh = $apiClient->encodings()->streams($encoding)->create($videoStream720pHigh);
$videoEncodingStream720pLow = $apiClient->encodings()->streams($encoding)->create($videoStream720pLow);
$videoEncodingStream480p = $apiClient->encodings()->streams($encoding)->create($videoStream480p);
$videoEncodingStream360p = $apiClient->encodings()->streams($encoding)->create($videoStream360p);
$videoEncodingStream240p = $apiClient->encodings()->streams($encoding)->create($videoStream240p);

// CREATE AUDIO STREAMS
$audioStream128 = new Stream($codecConfigAudio128, array($inputStreamAudio));
$audioEncodingStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

// CREATE VIDEO MUXINGS (FMP4)
$fmp4Muxing720pHigh = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream720pHigh, $output, $outputPath . 'video/720pHigh/dash/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$fmp4Muxing720pLow = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream720pLow, $output, $outputPath . 'video/720pLow/dash/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$fmp4Muxing480p = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream480p, $output, $outputPath . 'video/480p/dash/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$fmp4Muxing360p = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream360p, $output, $outputPath . 'video/360p/dash/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$fmp4Muxing240p = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream240p, $output, $outputPath . 'video/240p/dash/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
// CREATE AUDIO MUXING (FMP4)
$audioFmp4Muxing128 = createFmp4Muxing($apiClient, $encoding, $audioEncodingStream128, $output, $outputPath . 'audio/128kbps/dash/', AclPermission::ACL_PUBLIC_READ, $segmentLength);

// CREATE VIDEO MUXINGS (TS)
$tsMuxing720pHigh = createTsMuxing($apiClient, $encoding, $videoEncodingStream720pHigh, $output, $outputPath . 'video/720pHigh/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$tsMuxing720pLow = createTsMuxing($apiClient, $encoding, $videoEncodingStream720pLow, $output, $outputPath . 'video/720pLow/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$tsMuxing480p = createTsMuxing($apiClient, $encoding, $videoEncodingStream480p, $output, $outputPath . 'video/480p/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$tsMuxing360p = createTsMuxing($apiClient, $encoding, $videoEncodingStream360p, $output, $outputPath . 'video/360p/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
$tsMuxing240p = createTsMuxing($apiClient, $encoding, $videoEncodingStream240p, $output, $outputPath . 'video/240p/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);
// CREATE AUDIO MUXING (TS)
$audioTsMuxing128 = createTsMuxing($apiClient, $encoding, $audioEncodingStream128, $output, $outputPath . 'audio/128kbps/hls/', AclPermission::ACL_PUBLIC_READ, $segmentLength);

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

//DASH MANIFEST
$manifestName = "stream.mpd";
$manifestType = DashMuxingType::TYPE_TEMPLATE;

// CREATE DASH MANIFEST
$manifest = new DashManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$dashManifest = $apiClient->manifests()->dash()->create($manifest);

// ADD PERIOD
$period = new Period();
$manifestPeriod = $apiClient->manifests()->dash()->createPeriod($dashManifest, $period);

// CREATE VIDEO ADPAPTATION SET
$videoAdaptionSet = new VideoAdaptationSet();
$dashVideoAdaptionSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($dashManifest, $manifestPeriod, $videoAdaptionSet);

// ADD VIDEO REPRESENTATIONS TO ADAPTATION SET
$fmp4SegmentPath720pHigh = getSegmentOutputPath($outputPath, $fmp4Muxing720pHigh->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath720pLow = getSegmentOutputPath($outputPath, $fmp4Muxing720pLow->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath480p = getSegmentOutputPath($outputPath, $fmp4Muxing480p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath360p = getSegmentOutputPath($outputPath, $fmp4Muxing360p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath240p = getSegmentOutputPath($outputPath, $fmp4Muxing240p->getOutputs()[0]->getOutputPath());
$dashRepresentation720pHigh = createDashRepresentation($encoding, $fmp4Muxing720pHigh, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath720pHigh);
$dashRepresentation720pLow = createDashRepresentation($encoding, $fmp4Muxing720pLow, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath720pLow);
$dashRepresentation480p = createDashRepresentation($encoding, $fmp4Muxing480p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath480p);
$dashRepresentation360p = createDashRepresentation($encoding, $fmp4Muxing360p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath360p);
$dashRepresentation240p = createDashRepresentation($encoding, $fmp4Muxing240p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath240p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashRepresentation720pHigh);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashRepresentation720pLow);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashRepresentation480p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashRepresentation360p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashRepresentation240p);

// CREATE AUDIO ADAPTATION SET FOR EACH LANGUAGE
$audioAdaptionSet = new AudioAdaptationSet();
$audioAdaptionSet->setLang("en");
$dashAudioAdaptionSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($dashManifest, $manifestPeriod, $audioAdaptionSet);

// ADD AUDIO REPRESENTATIONS TO ADAPTATION SET
$audioSegmentPath240p = getSegmentOutputPath($outputPath, $audioFmp4Muxing128->getOutputs()[0]->getOutputPath());
$audioDashRepresentation128 = createDashRepresentation($encoding, $audioFmp4Muxing128, DashMuxingType::TYPE_TEMPLATE, $audioSegmentPath240p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashAudioAdaptionSet, $audioDashRepresentation128);

//Start Manifest Creation
$response = $apiClient->manifests()->dash()->start($dashManifest);

do
{
    $status = $apiClient->manifests()->dash()->status($dashManifest);
    var_dump($status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

// CREATE HLS PLAYLIST
$manifestName = "stream.m3u8";
$manifest = new HlsManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$masterPlaylist = $apiClient->manifests()->hls()->create($manifest);
$audioGroupId = 'audio';

$variantStreamUri720pHigh = "video_720pHigh_" . $codecConfigVideo720pHigh->getBitrate() . "_variant.m3u8";
$variantStreamUri720pLow = "video_720pLow_" . $codecConfigVideo720pLow->getBitrate() . "_variant.m3u8";
$variantStreamUri480p = "video_480p_" . $codecConfigVideo480p->getBitrate() . "_variant.m3u8";
$variantStreamUri360p = "video_360p_" . $codecConfigVideo360p->getBitrate() . "_variant.m3u8";
$variantStreamUri240p = "video_240p_" . $codecConfigVideo240p->getBitrate() . "_variant.m3u8";

$tsSegmentPath720pHigh = getSegmentOutputPath($outputPath, $tsMuxing720pHigh->getOutputs()[0]->getOutputPath());
$tsSegmentPath720pLow = getSegmentOutputPath($outputPath, $tsMuxing720pLow->getOutputs()[0]->getOutputPath());
$tsSegmentPath480p = getSegmentOutputPath($outputPath, $tsMuxing480p->getOutputs()[0]->getOutputPath());
$tsSegmentPath360p = getSegmentOutputPath($outputPath, $tsMuxing360p->getOutputs()[0]->getOutputPath());
$tsSegmentPath240p = getSegmentOutputPath($outputPath, $tsMuxing240p->getOutputs()[0]->getOutputPath());

//Create a Variant Stream for Video
$videoStreamInfo720pHigh = createHlsVariantStreamInfo($encoding, $videoEncodingStream720pHigh, $tsMuxing720pHigh, $audioGroupId, $tsSegmentPath720pHigh, $variantStreamUri720pHigh);
$videoStreamInfo720pLow = createHlsVariantStreamInfo($encoding, $videoEncodingStream720pLow, $tsMuxing720pLow, $audioGroupId, $tsSegmentPath720pLow, $variantStreamUri720pLow);
$videoStreamInfo480p = createHlsVariantStreamInfo($encoding, $videoEncodingStream480p, $tsMuxing480p, $audioGroupId, $tsSegmentPath480p, $variantStreamUri480p);
$videoStreamInfo360p = createHlsVariantStreamInfo($encoding, $videoEncodingStream360p, $tsMuxing360p, $audioGroupId, $tsSegmentPath360p, $variantStreamUri360p);
$videoStreamInfo240p = createHlsVariantStreamInfo($encoding, $videoEncodingStream240p, $tsMuxing240p, $audioGroupId, $tsSegmentPath240p, $variantStreamUri240p);
$variantStream720pHigh = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720pHigh);
$variantStream720pLow = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720pLow);
$variantStream480p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo480p);
$variantStream360p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo360p);
$variantStream240p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo240p);

$audioSegmentPath128 = getSegmentOutputPath($outputPath, $audioTsMuxing128->getOutputs()[0]->getOutputPath());
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
 * @param Encoding $encoding
 * @param Stream   $stream
 * @param TSMuxing $tsMuxing
 * @param string   $audioGroupId
 * @param string   $segmentPath
 * @param string   $uri
 * @return StreamInfo
 */
function createHlsVariantStreamInfo(Encoding $encoding, Stream $stream, TSMuxing $tsMuxing, $audioGroupId, $segmentPath, $uri)
{
    $variantStream = new StreamInfo();
    $variantStream->setEncodingId($encoding->getId());
    $variantStream->setStreamId($stream->getId());
    $variantStream->setMuxingId($tsMuxing->getId());
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
 * @param string    $initSegmentName
 * @param int       $segmentDuration
 * @param string    $segmentNaming
 * @return FMP4Muxing
 * @throws BitmovinException
 */
function createFmp4Muxing($apiClient, $encoding, $stream, $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ, $initSegmentName = 'init.mp4', $segmentDuration = 4, $segmentNaming = 'segment_%number%.m4s')
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
