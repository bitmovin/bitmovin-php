<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\VP9VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\WebmMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\AsperaInput;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\dash\WebmRepresentation;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\SftpOutput;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY-HERE');

// CREATE ENCODING
$encoding = new Encoding('MultiCodec Example');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);

// CREATE ASPERA INPUT
$videoInputPath = "path/to/your/inputfile.mp4";
$asperaHost = "yourasperahost.com";
$asperaUsername = "username";
$asperaPassword = "password";
$asperaInput = new AsperaInput($asperaHost, $asperaUsername, $asperaPassword);
$input = $apiClient->inputs()->aspera()->create($asperaInput);

//CREATE AN OUTPUT
$sftpHost = 'yourftphost.example.com';
$sftpUsername = 'username';
$sftpPassword = 'password';
$outputPath = 'path/to/output/destination/folder/';
$sftpOutput = new SftpOutput($sftpHost, $sftpUsername, $sftpPassword);
$output = $apiClient->outputs()->create($sftpOutput);

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

// CREATE VP9 VIDEO CODEC CONFIGURATIONS
$vp9CodecConfigVideo1080p = createVP9VideoCodecConfiguration($apiClient, 'VP9StreamDemo1080p', 3360000, null, 1080);
$vp9CodecConfigVideo720p = createVP9VideoCodecConfiguration($apiClient, 'VP9StreamDemo720p', 1680000, null, 720);
$vp9CodecConfigVideo480p = createVP9VideoCodecConfiguration($apiClient, 'VP9StreamDemo480p', 840000, null, 480);
$vp9CodecConfigVideo360p = createVP9VideoCodecConfiguration($apiClient, 'VP9StreamDemo360p', 560000, null, 360);
$vp9CodecConfigVideo240p = createVP9VideoCodecConfiguration($apiClient, 'VP9StreamDemo240p', 280000, null, 240);

// CREATE AAC AUDIO CODEC CONFIGURATIONS
$codecConfigAudio128 = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000);

// CREATE VIDEO STREAMS FMP4
$fmp4VideoStream1080p = new Stream($codecConfigVideo1080p, array($inputStreamVideo));
$fmp4VideoStream720p = new Stream($codecConfigVideo720p, array($inputStreamVideo));
$fmp4VideoStream480p = new Stream($codecConfigVideo480p, array($inputStreamVideo));
$fmp4VideoStream360p = new Stream($codecConfigVideo360p, array($inputStreamVideo));
$fmp4VideoStream240p = new Stream($codecConfigVideo240p, array($inputStreamVideo));
$fmp4VideoEncodingStream1080p = $apiClient->encodings()->streams($encoding)->create($fmp4VideoStream1080p);
$fmp4VideoEncodingStream720p = $apiClient->encodings()->streams($encoding)->create($fmp4VideoStream720p);
$fmp4VideoEncodingStream480p = $apiClient->encodings()->streams($encoding)->create($fmp4VideoStream480p);
$fmp4VideoEncodingStream360p = $apiClient->encodings()->streams($encoding)->create($fmp4VideoStream360p);
$fmp4VideoEncodingStream240p = $apiClient->encodings()->streams($encoding)->create($fmp4VideoStream240p);

// CREATE VIDEO STREAMS VP9
$vp9VideoStream1080p = new Stream($vp9CodecConfigVideo1080p, array($inputStreamVideo));
$vp9VideoStream720p = new Stream($vp9CodecConfigVideo720p, array($inputStreamVideo));
$vp9VideoStream480p = new Stream($vp9CodecConfigVideo480p, array($inputStreamVideo));
$vp9VideoStream360p = new Stream($vp9CodecConfigVideo360p, array($inputStreamVideo));
$vp9VideoStream240p = new Stream($vp9CodecConfigVideo240p, array($inputStreamVideo));
$vp9VideoEncodingStream1080p = $apiClient->encodings()->streams($encoding)->create($vp9VideoStream1080p);
$vp9VideoEncodingStream720p = $apiClient->encodings()->streams($encoding)->create($vp9VideoStream720p);
$vp9VideoEncodingStream480p = $apiClient->encodings()->streams($encoding)->create($vp9VideoStream480p);
$vp9VideoEncodingStream360p = $apiClient->encodings()->streams($encoding)->create($vp9VideoStream360p);
$vp9VideoEncodingStream240p = $apiClient->encodings()->streams($encoding)->create($vp9VideoStream240p);

// CREATE AUDIO STREAMS
$audioStream = new Stream($codecConfigAudio128, array($inputStreamAudio));
$audioEncodingStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream);

// CREATE ENCODING OUTPUTS FMP4
$fmp4EncodingOutput1080p = createEncodingOutput($output, $outputPath . 'video/1080p/dash/');
$fmp4EncodingOutput720p = createEncodingOutput($output, $outputPath . 'video/720p/dash/');
$fmp4EncodingOutput480p = createEncodingOutput($output, $outputPath . 'video/480p/dash/');
$fmp4EncodingOutput360p = createEncodingOutput($output, $outputPath . 'video/360p/dash/');
$fmp4EncodingOutput240p = createEncodingOutput($output, $outputPath . 'video/240p/dash/');

// CREATE VIDEO MUXINGS (FMP4)
$fmp4Muxing1080p = createFmp4Muxing($fmp4VideoEncodingStream1080p, $fmp4EncodingOutput1080p);
$fmp4Muxing720p = createFmp4Muxing($fmp4VideoEncodingStream720p, $fmp4EncodingOutput720p);
$fmp4Muxing480p = createFmp4Muxing($fmp4VideoEncodingStream480p, $fmp4EncodingOutput480p);
$fmp4Muxing360p = createFmp4Muxing($fmp4VideoEncodingStream360p, $fmp4EncodingOutput360p);
$fmp4Muxing240p = createFmp4Muxing($fmp4VideoEncodingStream240p, $fmp4EncodingOutput240p);
$fmp4Muxing1080p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing1080p);
$fmp4Muxing720p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing720p);
$fmp4Muxing480p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing480p);
$fmp4Muxing360p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing360p);
$fmp4Muxing240p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing240p);

// CREATE AUDIO ENCODING OUTPUT(FMP4)
$audioEncodingOutput128 = createEncodingOutput($output, $outputPath . 'audio/128kbps/dash/');

// CREATE AUDIO MUXING (FMP4)
$fmp4AudioMuxing128 = createFmp4Muxing($audioEncodingStream128, $audioEncodingOutput128);
$fmp4AudioMuxing128 = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4AudioMuxing128);

// CREATE ENCODING OUTPUTS VP9
$vp9EncodingOutput1080p = createEncodingOutput($output, $outputPath . 'video/1080p/webm/');
$vp9EncodingOutput720p = createEncodingOutput($output, $outputPath . 'video/720p/webm/');
$vp9EncodingOutput480p = createEncodingOutput($output, $outputPath . 'video/480p/webm/');
$vp9EncodingOutput360p = createEncodingOutput($output, $outputPath . 'video/360p/webm/');
$vp9EncodingOutput240p = createEncodingOutput($output, $outputPath . 'video/240p/webm/');

// CREATE VIDEO MUXINGS (VP9)
$webmMuxing1080p = createWebmMuxing($vp9VideoEncodingStream1080p, $vp9EncodingOutput1080p);
$webmMuxing720p = createWebmMuxing($vp9VideoEncodingStream720p, $vp9EncodingOutput720p);
$webmMuxing480p = createWebmMuxing($vp9VideoEncodingStream480p, $vp9EncodingOutput480p);
$webmMuxing360p = createWebmMuxing($vp9VideoEncodingStream360p, $vp9EncodingOutput360p);
$webmMuxing240p = createWebmMuxing($vp9VideoEncodingStream240p, $vp9EncodingOutput240p);
$webmMuxing1080p = $apiClient->encodings()->muxings($encoding)->webmMuxing()->create($webmMuxing1080p);
$webmMuxing720p = $apiClient->encodings()->muxings($encoding)->webmMuxing()->create($webmMuxing720p);
$webmMuxing480p = $apiClient->encodings()->muxings($encoding)->webmMuxing()->create($webmMuxing480p);
$webmMuxing360p = $apiClient->encodings()->muxings($encoding)->webmMuxing()->create($webmMuxing360p);
$webmMuxing240p = $apiClient->encodings()->muxings($encoding)->webmMuxing()->create($webmMuxing240p);

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
$manifestName = "your-multi-codec-manifest.mpd";
$manifestType = DashMuxingType::TYPE_TEMPLATE;

// CREATE DASH MANIFEST
$manifest = new DashManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$dashManifest = $apiClient->manifests()->dash()->create($manifest);

// ADD PERIOD
$period = new Period();
$manifestPeriod = $apiClient->manifests()->dash()->createPeriod($dashManifest, $period);

// CREATE FMP4 VIDEO ADPAPTATION SET
$fmp4VideoAdaptionSet = new VideoAdaptationSet();
$fmp4VideoAdaptionSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($dashManifest, $manifestPeriod, $fmp4VideoAdaptionSet);

// ADD FMP4 VIDEO REPRESENTATIONS TO ADAPTATION SET
$fmp4SegmentPath1080p = getSegmentOutputPath($outputPath, $fmp4Muxing1080p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath720p = getSegmentOutputPath($outputPath, $fmp4Muxing720p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath480p = getSegmentOutputPath($outputPath, $fmp4Muxing480p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath360p = getSegmentOutputPath($outputPath, $fmp4Muxing360p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath240p = getSegmentOutputPath($outputPath, $fmp4Muxing240p->getOutputs()[0]->getOutputPath());

$fmp4Representation1080p = createFmp4Representation($encoding, $fmp4Muxing1080p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath1080p);
$fmp4Representation720p = createFmp4Representation($encoding, $fmp4Muxing720p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath720p);
$fmp4Representation480p = createFmp4Representation($encoding, $fmp4Muxing480p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath480p);
$fmp4Representation360p = createFmp4Representation($encoding, $fmp4Muxing360p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath360p);
$fmp4Representation240p = createFmp4Representation($encoding, $fmp4Muxing240p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath240p);

$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $fmp4VideoAdaptionSet, $fmp4Representation1080p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $fmp4VideoAdaptionSet, $fmp4Representation720p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $fmp4VideoAdaptionSet, $fmp4Representation480p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $fmp4VideoAdaptionSet, $fmp4Representation360p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $fmp4VideoAdaptionSet, $fmp4Representation240p);

// CREATE AUDIO ADAPTATION SET FOR EACH LANGUAGE
$fmp4AudioAdaptionSet = new AudioAdaptationSet();
$fmp4AudioAdaptionSet->setLang("en");
$fmp4AudioAdaptionSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($dashManifest, $manifestPeriod, $fmp4AudioAdaptionSet);

// ADD FMP4 AUDIO REPRESENTATIONS TO ADAPTATION SET
$fmp4AudioSegmentPath240p = getSegmentOutputPath($outputPath, $fmp4AudioMuxing128->getOutputs()[0]->getOutputPath());
$audioFmp4Representation128 = createFmp4Representation($encoding, $fmp4AudioMuxing128, DashMuxingType::TYPE_TEMPLATE, $fmp4AudioSegmentPath240p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $fmp4AudioAdaptionSet, $audioFmp4Representation128);

// #########################################################

// CREATE WEBM VIDEO ADPAPTATION SET
$webmVideoAdaptationSet = new VideoAdaptationSet();
$webmVideoAdaptationSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($dashManifest, $manifestPeriod, $webmVideoAdaptationSet);

// ADD WEBM VIDEO REPRESENTATIONS TO ADAPTATION SET
$webmSegmentPath1080p = getSegmentOutputPath($outputPath, $webmMuxing1080p->getOutputs()[0]->getOutputPath());
$webmSegmentPath720p = getSegmentOutputPath($outputPath, $webmMuxing720p->getOutputs()[0]->getOutputPath());
$webmSegmentPath480p = getSegmentOutputPath($outputPath, $webmMuxing480p->getOutputs()[0]->getOutputPath());
$webmSegmentPath360p = getSegmentOutputPath($outputPath, $webmMuxing360p->getOutputs()[0]->getOutputPath());
$webmSegmentPath240p = getSegmentOutputPath($outputPath, $webmMuxing240p->getOutputs()[0]->getOutputPath());
$webmRepresentation1080p = createWebmRepresentation($encoding, $webmMuxing1080p, DashMuxingType::TYPE_TEMPLATE, $webmSegmentPath1080p);
$webmRepresentation720p = createWebmRepresentation($encoding, $webmMuxing720p, DashMuxingType::TYPE_TEMPLATE, $webmSegmentPath720p);
$webmRepresentation480p = createWebmRepresentation($encoding, $webmMuxing480p, DashMuxingType::TYPE_TEMPLATE, $webmSegmentPath480p);
$webmRepresentation360p = createWebmRepresentation($encoding, $webmMuxing360p, DashMuxingType::TYPE_TEMPLATE, $webmSegmentPath360p);
$webmRepresentation240p = createWebmRepresentation($encoding, $webmMuxing240p, DashMuxingType::TYPE_TEMPLATE, $webmSegmentPath240p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $webmVideoAdaptationSet, $webmRepresentation1080p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $webmVideoAdaptationSet, $webmRepresentation720p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $webmVideoAdaptationSet, $webmRepresentation480p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $webmVideoAdaptationSet, $webmRepresentation360p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $webmVideoAdaptationSet, $webmRepresentation240p);

//Start Manifest Creation
$response = $apiClient->manifests()->dash()->start($dashManifest);

do
{
    $status = $apiClient->manifests()->dash()->status($dashManifest);
    var_dump($status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

//#####################################################################################################################

/**
 * @param Stream              $stream
 * @param EncodingOutput|null $encodingOutput
 * @param string              $initSegmentName
 * @param int                 $segmentDuration
 * @param string              $segmentNaming
 * @return WebmMuxing
 */
function createWebmMuxing(Stream $stream, EncodingOutput $encodingOutput = null, $initSegmentName = 'init.hdr', $segmentDuration = 4, $segmentNaming = 'segment_%number%.chk')
{
    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());

    $encodingOutputs = null;
    if ($encodingOutput instanceof EncodingOutput)
    {
        $encodingOutputs = array($encodingOutput);
    }

    $webmMuxing = new WebmMuxing();
    $webmMuxing->setInitSegmentName($initSegmentName);
    $webmMuxing->setSegmentLength($segmentDuration);
    $webmMuxing->setSegmentNaming($segmentNaming);
    $webmMuxing->setOutputs($encodingOutputs);
    $webmMuxing->setStreams(array($muxingStream));

    return $webmMuxing;
}

/**
 *
 * @param Stream              $stream
 * @param EncodingOutput|null $encodingOutput
 * @param string              $initSegmentName
 * @param int                 $segmentDuration
 * @param string              $segmentNaming
 * @return FMP4Muxing
 */
function createFmp4Muxing(Stream $stream, EncodingOutput $encodingOutput = null, $initSegmentName = 'init.mp4', $segmentDuration = 4, $segmentNaming = 'segment_%number%.m4s')
{
    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());

    $encodingOutputs = null;
    if ($encodingOutput instanceof EncodingOutput)
    {
        $encodingOutputs = array($encodingOutput);
    }

    $fmp4Muxing = new FMP4Muxing();
    $fmp4Muxing->setInitSegmentName($initSegmentName);
    $fmp4Muxing->setSegmentLength($segmentDuration);
    $fmp4Muxing->setSegmentNaming($segmentNaming);
    $fmp4Muxing->setOutputs($encodingOutputs);
    $fmp4Muxing->setStreams(array($muxingStream));

    return $fmp4Muxing;
}

/**
 * @param Encoding   $encoding
 * @param FMP4Muxing $fmp4Muxing
 * @param string     $manifestType
 * @param string     $segmentPath
 * @return DashRepresentation
 */
function createFmp4Representation(Encoding $encoding, FMP4Muxing $fmp4Muxing, $manifestType, $segmentPath)
{
    $representation = new DashRepresentation();
    $representation->setType($manifestType);
    $representation->setSegmentPath($segmentPath);
    $representation->setEncodingId($encoding->getId());
    $representation->setMuxingId($fmp4Muxing->getId());

    return $representation;
}

/**
 * @param Encoding   $encoding
 * @param WebmMuxing $webmMuxing
 * @param string     $manifestType
 * @param string     $segmentPath
 * @return WebmRepresentation
 */
function createWebmRepresentation(Encoding $encoding, WebmMuxing $webmMuxing, $manifestType, $segmentPath)
{
    $representation = new WebmRepresentation();
    $representation->setType($manifestType);
    $representation->setSegmentPath($segmentPath);
    $representation->setEncodingId($encoding->getId());
    $representation->setMuxingId($webmMuxing->getId());

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
 * @param integer   $bitrate
 * @param float     $rate
 * @param integer   $width
 * @param integer   $height
 * @return VP9VideoCodecConfiguration
 * @throws BitmovinException
 */
function createVP9VideoCodecConfiguration(ApiClient $apiClient, $name, $bitrate, $width = null, $height = null, $rate = null)
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
 * @param string    $profile
 * @param integer   $bitrate
 * @param float     $rate
 * @param integer   $width
 * @param integer   $height
 * @return H264VideoCodecConfiguration
 * @throws BitmovinException
 */
function createH264VideoCodecConfiguration(ApiClient $apiClient, $name, $profile, $bitrate, $width = null, $height = null, $rate = null)
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
function createAACAudioCodecConfiguration(ApiClient $apiClient, $name, $bitrate, $rate = null)
{
    $codecConfigAudio = new AACAudioCodecConfiguration($name, $bitrate, $rate);
    return $apiClient->codecConfigurations()->audioAAC()->create($codecConfigAudio);
}