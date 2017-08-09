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
use Bitmovin\api\model\encodings\drms\CencDrm;
use Bitmovin\api\model\encodings\drms\cencSystems\CencPlayReady;
use Bitmovin\api\model\encodings\drms\cencSystems\CencWidevine;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\ContentProtection;
use Bitmovin\api\model\manifests\dash\DashDrmRepresentation;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR-BITMOVIN-API-KEY');

// CREATE ENCODING
$encoding = new Encoding('Widevine DRM Encoding Example');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$videoInputPath = "path/to/your/input/file.mp4";
$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$s3Input = $apiClient->inputs()->s3()->create($s3Input);

$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$outputPath = "path/to/your/output-destination/";
$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$s3Output = $apiClient->outputs()->s3()->create($s3Output);

$cencDrmKey = "0123456789abcdef0123456789abcdef";
$cencDrmKid = "0123456789abcdef0123456789abcdef";
$widevinePssh = "widevine-pssh-key-in-base64";
$playreadyLaUrl = null;

//CREATE AUDIO/VIDEO INPUT STREAMS
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
$audioStream128 = new Stream($codecConfigAudio128, array($inputStreamAudio));
$audioStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

// CREATE VIDEO MUXINGS (FMP4)
$fmp4Muxing1080p = createFmp4Muxing($apiClient, $encoding, $videoStream1080p, null, null);
$fmp4Muxing720p = createFmp4Muxing($apiClient, $encoding, $videoStream720p, null, null);
$fmp4Muxing480p = createFmp4Muxing($apiClient, $encoding, $videoStream480p, null, null);
$fmp4Muxing360p = createFmp4Muxing($apiClient, $encoding, $videoStream360p, null, null);
$fmp4Muxing240p = createFmp4Muxing($apiClient, $encoding, $videoStream240p, null, null);

// CREATE AUDIO MUXING (FMP4)
$audioFmp4Muxing128 = createFmp4Muxing($apiClient, $encoding, $audioStream128, null, null);

// ADD DRM TO VIDEO FMP4 MUXINGs
$cencDrmEncodingOutput1080p = createEncodingOutput($s3Output, $outputPath . 'video/1080p/dash/drm/');
$cencDrmEncodingOutput720p = createEncodingOutput($s3Output, $outputPath . 'video/720p/dash/drm/');
$cencDrmEncodingOutput480p = createEncodingOutput($s3Output, $outputPath . 'video/480p/dash/drm/');
$cencDrmEncodingOutput360p = createEncodingOutput($s3Output, $outputPath . 'video/360p/dash/drm/');
$cencDrmEncodingOutput240p = createEncodingOutput($s3Output, $outputPath . 'video/240p/dash/drm/');

$cencDrm1080p = createCencDrm($cencDrmKey, $cencDrmKid, array($cencDrmEncodingOutput1080p), $widevinePssh);
$cencDrm720p = createCencDrm($cencDrmKey, $cencDrmKid, array($cencDrmEncodingOutput720p), $widevinePssh);
$cencDrm480p = createCencDrm($cencDrmKey, $cencDrmKid, array($cencDrmEncodingOutput480p), $widevinePssh);
$cencDrm360p = createCencDrm($cencDrmKey, $cencDrmKid, array($cencDrmEncodingOutput360p), $widevinePssh);
$cencDrm240p = createCencDrm($cencDrmKey, $cencDrmKid, array($cencDrmEncodingOutput240p), $widevinePssh);

$videoFmp4Drm1080p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing1080p)->cencDrm()->create($cencDrm1080p);
$videoFmp4Drm720p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing720p)->cencDrm()->create($cencDrm720p);
$videoFmp4Drm480p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing480p)->cencDrm()->create($cencDrm480p);
$videoFmp4Drm360p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing360p)->cencDrm()->create($cencDrm360p);
$videoFmp4Drm240p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing240p)->cencDrm()->create($cencDrm240p);

// CREATE DRM TO AUDIO FMP4 MUXINGs
$audioCencDrmEncodingOutput128 = createEncodingOutput($s3Output, $outputPath . 'audio/128kbps/dash/drm/');
$audioCencDrm128 = createCencDrm($cencDrmKey, $cencDrmKid, array($audioCencDrmEncodingOutput128), null, $playreadyLaUrl);
$audioFmp4Drm128 = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($audioFmp4Muxing128)->cencDrm()->create($audioCencDrm128);

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

//DASH MANIFEST
$manifestName = "your-dash-manifest.mpd";
$manifestType = DashMuxingType::TYPE_TEMPLATE;

// CREATE DASH MANIFEST
$manifest = new DashManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$manifest = $apiClient->manifests()->dash()->create($manifest);

// ADD PERIOD
$period = new Period();
$period = $apiClient->manifests()->dash()->createPeriod($manifest, $period);

// CREATE VIDEO ADPAPTATION SET
$videoAdaptionSet = new VideoAdaptationSet();
$videoAdaptionSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($manifest, $period, $videoAdaptionSet);

// ADD CONTENT PROTECTION TO VIDEO ADAPTATION SET
$videoContentProtection = createContentProtectionForAdaptationSet($encoding, $videoFmp4Drm1080p, $fmp4Muxing1080p);
$apiClient->manifests()->dash()->addContentProtectionToAdaptationSet($manifest, $period, $videoAdaptionSet, $videoContentProtection);

// ADD DRM PROTECTED VIDEO REPRESENTATIONS TO ADAPTATION SET
$fmp4SegmentPath1080p = getSegmentOutputPath($outputPath, $videoFmp4Drm1080p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath720p = getSegmentOutputPath($outputPath, $videoFmp4Drm720p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath480p = getSegmentOutputPath($outputPath, $videoFmp4Drm480p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath360p = getSegmentOutputPath($outputPath, $videoFmp4Drm360p->getOutputs()[0]->getOutputPath());
$fmp4SegmentPath240p = getSegmentOutputPath($outputPath, $videoFmp4Drm240p->getOutputs()[0]->getOutputPath());

$dashDrmRepresentation1080p = createDashDrmRepresentation($encoding, $videoFmp4Drm1080p, $fmp4Muxing1080p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath1080p);
$dashDrmRepresentation720p = createDashDrmRepresentation($encoding, $videoFmp4Drm720p, $fmp4Muxing720p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath720p);
$dashDrmRepresentation480p = createDashDrmRepresentation($encoding, $videoFmp4Drm480p, $fmp4Muxing480p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath480p);
$dashDrmRepresentation360p = createDashDrmRepresentation($encoding, $videoFmp4Drm360p, $fmp4Muxing360p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath360p);
$dashDrmRepresentation240p = createDashDrmRepresentation($encoding, $videoFmp4Drm240p, $fmp4Muxing240p, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath240p);

$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashDrmRepresentation1080p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashDrmRepresentation720p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashDrmRepresentation480p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashDrmRepresentation360p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $videoAdaptionSet, $dashDrmRepresentation240p);

// CREATE AUDIO ADAPTATION SET FOR EACH LANGUAGE
$audioAdaptionSet = new AudioAdaptationSet();
$audioAdaptionSet->setLang("en");
$audioAdaptionSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($manifest, $period, $audioAdaptionSet);

// ADD CONTENT PROTECTION TO AUDIO ADAPTATION SET
$audioContentProtection = createContentProtectionForAdaptationSet($encoding, $audioFmp4Drm128, $audioFmp4Muxing128);
$apiClient->manifests()->dash()->addContentProtectionToAdaptationSet($manifest, $period, $audioAdaptionSet, $audioContentProtection);

// ADD DRM PROTECTED AUDIO REPRESENTATIONS TO ADAPTATION SET
$audioSegmentPath240p = getSegmentOutputPath($outputPath, $audioFmp4Drm128->getOutputs()[0]->getOutputPath());
$audioDashDrmRepresentation128 = createDashDrmRepresentation($encoding, $audioFmp4Drm128, $audioFmp4Muxing128, DashMuxingType::TYPE_TEMPLATE, $audioSegmentPath240p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $audioAdaptionSet, $audioDashDrmRepresentation128);

//Start Manifest Creation
$response = $apiClient->manifests()->dash()->start($manifest);

do
{
    $status = $apiClient->manifests()->dash()->status($manifest);
    var_dump($status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
    sleep(1);
} while ($isRunning);

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
 * @param Encoding   $encoding
 * @param CencDrm    $cencDrm
 * @param FMP4Muxing $fmp4Muxing
 * @return ContentProtection
 */
function createContentProtectionForAdaptationSet(Encoding $encoding, CencDrm $cencDrm, FMP4Muxing $fmp4Muxing)
{
    $contentProtection = new ContentProtection();
    $contentProtection->setDrmId($cencDrm->getId());
    $contentProtection->setMuxingId($fmp4Muxing->getId());
    $contentProtection->setEncodingId($encoding->getId());

    return $contentProtection;
}

/**
 * @param Encoding   $encoding
 * @param CencDrm    $cencDrm
 * @param FMP4Muxing $fmp4Muxing
 * @param string     $manifestType
 * @param string     $segmentPath
 * @return DashDrmRepresentation
 */
function createDashDrmRepresentation(Encoding $encoding, CencDrm $cencDrm, FMP4Muxing $fmp4Muxing, $manifestType, $segmentPath)
{
    $representation = new DashDrmRepresentation();
    $representation->setType($manifestType);
    $representation->setSegmentPath($segmentPath);
    $representation->setDrmId($cencDrm->getId());
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

/**
 * @param                  $key
 * @param                  $kid
 * @param                  $widevinePssh
 * @param                  $playreadyLaUrl
 * @param EncodingOutput[] $outputs
 * @return CencDrm
 */
function createCencDrm($key, $kid, array $outputs, $widevinePssh = null, $playreadyLaUrl = null)
{
    //CREATE CENC DRM CONFIGURATION
    $cencDrm = new CencDrm($key, $kid, $outputs);

    if (!is_null($widevinePssh))
    {
        $cencDrm->setWidevine(new CencWidevine($widevinePssh));
    }
    if (!is_null($playreadyLaUrl))
    {
        $cencDrm->setPlayReady(new CencPlayReady($playreadyLaUrl));
    }

    return $cencDrm;
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