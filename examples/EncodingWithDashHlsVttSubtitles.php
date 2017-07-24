<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\encodings\ThumbnailUnit;
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
use Bitmovin\api\model\encodings\streams\sprites\Sprite;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\encodings\streams\thumbnails\Thumbnail;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashDrmRepresentation;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\SubtitleAdaptationSet;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\dash\VttRepresentation;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\manifests\hls\VttMedia;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$videoInputPath = "path/to/your/input/file.mp4";
$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$input = $apiClient->inputs()->s3()->create($s3Input);

$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$outputPath = "path/to/your/input/file.mp4";
$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$output = $apiClient->outputs()->s3()->create($s3Output);

// CREATE ENCODING
$encoding = new Encoding('HLS / DASH Manifest + Subtitles Example');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);

// CREATE VIDEO CODEC CONFIGURATIONS
$codecConfigVideo1080p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo1080p', H264Profile::HIGH, 4800000, null, 1080);
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720p', H264Profile::HIGH, 2400000, null, 720);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo480p', H264Profile::MAIN, 1200000, null, 480);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo360p', H264Profile::MAIN, 800000, null, 360);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo240p', H264Profile::BASELINE, 400000, null, 240);

// CREATE AUDIO CODEC CONFIGURATIONS
$codecConfigAudio128 = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000);

//CREATE INPUT STREAM FOR VIDEO AND AUDIO
$inputStreamVideo = new InputStream($input, $videoInputPath, SelectionMode::AUTO); //Automatically selects first available video track
$inputStreamAudio = new InputStream($input, $videoInputPath, SelectionMode::AUTO); //Automatically selects first available audio track

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
$audioStream128 = new Stream($codecConfigAudio128, array($inputStreamAudio));
$audioStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

//CREATE THUMBNAILS
$thumbnailOutput = createEncodingOutput($output, $outputPath . "thumbnails/");
$thumbnailHeight = 360;
$thumbnailNamingPattern = "thumb_%number%.png";
$thumbnailUnit = ThumbnailUnit::SECONDS;
$thumbnailPositions = array(5, 10, 15, 20); //You can provide relative positions based on the input file length if unit is set to ThumbnailUnit::PERCENTS

$thumbnail = new Thumbnail($thumbnailHeight, $thumbnailPositions);
$thumbnail->setUnit($thumbnailUnit);
$thumbnail->setPattern($thumbnailNamingPattern);
$thumbnail->setOutputs(array($thumbnailOutput));
$apiClient->encodings()->streams($encoding)->thumbnails($videoStream1080p)->create($thumbnail);

//CREATE THUMBNAILS
$spriteOutput = createEncodingOutput($output, $outputPath . "sprites/");
$spriteWidth = 640;
$spriteHeight = 360;
$spriteDistance = 15; //in seconds
$spriteName = 'fullhd_' . $spriteWidth . 'x' . $spriteHeight . '.jpg';
$spriteVttName = 'fullhd_' . $spriteWidth . 'x' . $spriteHeight . '.vtt';

$sprite = new Sprite($spriteWidth, $spriteHeight, $spriteName, $spriteVttName);
$sprite->setDistance($spriteDistance);
$sprite->setOutputs(array($spriteOutput));
$apiClient->encodings()->streams($encoding)->sprites($videoStream1080p)->create($sprite);

// CREATE VIDEO MUXINGS (FMP4)
$fmp4Muxing1080p = createFmp4Muxing($apiClient, $encoding, $videoStream1080p, $output, $outputPath . 'video/1080p/dash/', AclPermission::ACL_PUBLIC_READ);
$fmp4Muxing720p = createFmp4Muxing($apiClient, $encoding, $videoStream720p, $output, $outputPath . 'video/720p/dash/', AclPermission::ACL_PUBLIC_READ);
$fmp4Muxing480p = createFmp4Muxing($apiClient, $encoding, $videoStream480p, $output, $outputPath . 'video/480p/dash/', AclPermission::ACL_PUBLIC_READ);
$fmp4Muxing360p = createFmp4Muxing($apiClient, $encoding, $videoStream360p, $output, $outputPath . 'video/360p/dash/', AclPermission::ACL_PUBLIC_READ);
$fmp4Muxing240p = createFmp4Muxing($apiClient, $encoding, $videoStream240p, $output, $outputPath . 'video/240p/dash/', AclPermission::ACL_PUBLIC_READ);
// CREATE AUDIO MUXING (FMP4)
$audioFmp4Muxing128 = createFmp4Muxing($apiClient, $encoding, $audioStream128, $output, $outputPath . 'audio/128kbps/dash/', AclPermission::ACL_PUBLIC_READ);

// CREATE VIDEO MUXINGS (TS)
$tsMuxing1080p = createTsMuxing($apiClient, $encoding, $videoStream1080p, $output, $outputPath . 'video/1080p/hls/', AclPermission::ACL_PUBLIC_READ);
$tsMuxing720p = createTsMuxing($apiClient, $encoding, $videoStream720p, $output, $outputPath . 'video/720p/hls/', AclPermission::ACL_PUBLIC_READ);
$tsMuxing480p = createTsMuxing($apiClient, $encoding, $videoStream480p, $output, $outputPath . 'video/480p/hls/', AclPermission::ACL_PUBLIC_READ);
$tsMuxing360p = createTsMuxing($apiClient, $encoding, $videoStream360p, $output, $outputPath . 'video/360p/hls/', AclPermission::ACL_PUBLIC_READ);
$tsMuxing240p = createTsMuxing($apiClient, $encoding, $videoStream240p, $output, $outputPath . 'video/240p/hls/', AclPermission::ACL_PUBLIC_READ);
// CREATE AUDIO MUXING (TS)
$audioTsMuxing128 = createTsMuxing($apiClient, $encoding, $audioStream128, $output, $outputPath . 'audio/128kbps/hls/', AclPermission::ACL_PUBLIC_READ);

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
$manifest = $apiClient->manifests()->dash()->create($manifest);

// ADD PERIOD
$period = new Period();
$period = $apiClient->manifests()->dash()->createPeriod($manifest, $period);

// CREATE SUBTITLE ADAPTATION SET
$subtitleAdaptationSet = new SubtitleAdaptationSet();
$subtitleAdaptationSet->setLang('en');
$subtitleAdaptationSet = $apiClient->manifests()->dash()->addSubtitleAdaptationSetToPeriod($manifest, $period, $subtitleAdaptationSet);

$enVttSubtitleUrl = "https://bitdash-a.akamaihd.net/content/sintel/hls/subtitles_en.vtt";

$vttRepresentation = new VttRepresentation();
$vttRepresentation->setVttUrl($enVttSubtitleUrl);
$apiClient->manifests()->dash()->addVttRepresentationToSubtitleAdaptationSet($manifest, $period, $subtitleAdaptationSet, $vttRepresentation);

// CREATE VIDEO ADPAPTATION SET
$videoAdaptionSet = new VideoAdaptationSet();
$videoAdaptionSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($manifest, $period, $videoAdaptionSet);

// ADD VIDEO REPRESENTATIONS TO ADAPTATION SET
$fmp4SegmentPath1080p = getSegmentOutputPath($outputPath, $fmp4Muxing1080p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath720p = getSegmentOutputPath($outputPath, $fmp4Muxing720p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath480p = getSegmentOutputPath($outputPath, $fmp4Muxing480p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath360p = getSegmentOutputPath($outputPath, $fmp4Muxing360p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath240p = getSegmentOutputPath($outputPath, $fmp4Muxing240p->getOutputs()[0]->getOutputPath());
$dashRepresentation1080p = createDashRepresentation($encoding, $fmp4Muxing1080p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath1080p);
$dashRepresentation720p = createDashRepresentation($encoding, $fmp4Muxing720p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath720p);
$dashRepresentation480p = createDashRepresentation($encoding, $fmp4Muxing480p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath480p);
$dashRepresentation360p = createDashRepresentation($encoding, $fmp4Muxing360p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath360p);
$dashRepresentation240p = createDashRepresentation($encoding, $fmp4Muxing240p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath240p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashRepresentation1080p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashRepresentation720p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashRepresentation480p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashRepresentation360p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashRepresentation240p);

// CREATE AUDIO ADAPTATION SET FOR EACH LANGUAGE
$audioAdaptionSet = new AudioAdaptationSet();
$audioAdaptionSet->setLang("en");
$audioAdaptionSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($manifest, $period, $audioAdaptionSet);

// ADD AUDIO REPRESENTATIONS TO ADAPTATION SET
$audioSegmentPath240p = getSegmentOutputPath($outputPath, $audioFmp4Muxing128->getOutputs()[0]->getOutputPath());
$audioDashRepresentation128 = createDashRepresentation($encoding, $audioFmp4Muxing128, DashMuxingType::TYPE_TEMPLATE, $audioSegmentPath240p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $audioAdaptionSet, $audioDashRepresentation128);

//Start Manifest Creation
$response = $apiClient->manifests()->dash()->start($manifest);

do
{
    $status = $apiClient->manifests()->dash()->status($manifest);
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
$subtitleGroupId = 'subtitles';

//Create subtitles
$englishVttMedia = createVttMediaInfo($subtitleGroupId, $enVttSubtitleUrl, "en_subtitles.m3u8", "en", "English");
$apiClient->manifests()->hls()->addVttMedia($masterPlaylist, $englishVttMedia);

//Create a Variant Stream for Video
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

$videoStreamInfo1080p = createHlsVariantStreamInfo($encoding, $videoStream1080p, $tsMuxing1080p, $audioGroupId, $subtitleGroupId, $tsSegmentPath1080p, $variantStreamUri1080p);
$videoStreamInfo720p = createHlsVariantStreamInfo($encoding, $videoStream720p, $tsMuxing720p, $audioGroupId, $subtitleGroupId, $tsSegmentPath720p, $variantStreamUri720p);
$videoStreamInfo480p = createHlsVariantStreamInfo($encoding, $videoStream480p, $tsMuxing480p, $audioGroupId, $subtitleGroupId, $tsSegmentPath480p, $variantStreamUri480p);
$videoStreamInfo360p = createHlsVariantStreamInfo($encoding, $videoStream360p, $tsMuxing360p, $audioGroupId, $subtitleGroupId, $tsSegmentPath360p, $variantStreamUri360p);
$videoStreamInfo240p = createHlsVariantStreamInfo($encoding, $videoStream240p, $tsMuxing240p, $audioGroupId, $subtitleGroupId, $tsSegmentPath240p, $variantStreamUri240p);
$variantStream1080p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo1080p);
$variantStream720p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo720p);
$variantStream480p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo480p);
$variantStream360p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo360p);
$variantStream240p = $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo240p);

$audioVariantStreamUri128 = "audio_1_" . $codecConfigAudio128->getBitrate() . "_variant.m3u8";
$audioSegmentPath128 = getSegmentOutputPath($outputPath, $audioTsMuxing128->getOutputs()[0]->getOutputPath());

$audioMediaInfo128 = new MediaInfo();
$audioMediaInfo128->setGroupId($audioGroupId);
$audioMediaInfo128->setName("English");
$audioMediaInfo128->setLanguage("English");
$audioMediaInfo128->setAssocLanguage("en");
$audioMediaInfo128->setUri($audioVariantStreamUri128);
$audioMediaInfo128->setType(MediaInfoType::AUDIO);
$audioMediaInfo128->setEncodingId($encoding->getId());
$audioMediaInfo128->setStreamId($audioStream128->getId());
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
 * @param string $subtitleGroupId
 * @param string $vttUrl
 * @param string $uri
 * @param string $language
 * @param string $name
 * @param bool   $default
 * @param bool   $autoSelect
 * @param bool   $forced
 * @return VttMedia
 */
function createVttMediaInfo($subtitleGroupId, $vttUrl, $uri, $language, $name, $default = false, $autoSelect = false, $forced = false)
{
    //Add VTT Media
    $vttMedia = new VttMedia();
    $vttMedia->setGroupId($subtitleGroupId);
    $vttMedia->setVttUrl($vttUrl);
    $vttMedia->setUri($uri);
    $vttMedia->setLanguage($language);
    $vttMedia->setForced($forced);
    $vttMedia->setName($name);

    $vttMedia->setIsDefault($default);
    $vttMedia->setAutoSelect($autoSelect);

    return $vttMedia;
}

/**
 * @param Encoding $encoding
 * @param Stream   $stream
 * @param TSMuxing $tsMuxing
 * @param string   $audioGroupId
 * @param string   $subtitleGroupId
 * @param string   $segmentPath
 * @param string   $uri
 * @return StreamInfo
 */
function createHlsVariantStreamInfo(Encoding $encoding, Stream $stream, TSMuxing $tsMuxing, $audioGroupId, $subtitleGroupId, $segmentPath, $uri)
{
    $variantStream = new StreamInfo();
    $variantStream->setEncodingId($encoding->getId());
    $variantStream->setStreamId($stream->getId());
    $variantStream->setMuxingId($tsMuxing->getId());
    $variantStream->setAudio($audioGroupId);
    $variantStream->setSubtitles($subtitleGroupId);
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