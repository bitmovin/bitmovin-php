<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\drms\CencDrm;
use Bitmovin\api\model\encodings\drms\cencSystems\CencPlayReady;
use Bitmovin\api\model\encodings\drms\cencSystems\CencWidevine;
use Bitmovin\api\model\encodings\drms\FairPlayDrm;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\LiveDashManifest;
use Bitmovin\api\model\encodings\LiveHlsManifest;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\StartLiveEncodingRequest;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\ContentProtection;
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

//LIVESTREAM CONFIGURATION VALUES
$dashLiveEdgeOffset = 60;
$hlsLiveEdgeOffset = 60;
$hlsTimeShiftWindowSize = 60;
$streamKey = "yourownstreamkey";
$fps = 30;

// CREATE ENCODING
$encoding = new Encoding('DRM Livestream Example');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding->setEncoderVersion("BETA");
$encoding = $apiClient->encodings()->create($encoding);

// GET RTMP INPUT
$rtmpInput = $apiClient->inputs()->rtmp()->listPage()[0];

$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$outputPath = "path/to/your/output/location/";
$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$output = $apiClient->outputs()->s3()->create($s3Output);

//CENC DRM Values
$cencDrmKey = "0123456789abcdef0123456789abcdef";
$cencDrmKid = "0123456789abcdef0123456789abcdef";
$widevinePssh = "widevine-pssh-key-in-base64";
$playreadyLaUrl = "https://example.com/playready-la-url";

//FairPlay DRM Values
$fairPlayKey = "0123456789abcdef0123456789abcdef";
$fairPlayIV = "0123456789abcdef0123456789abcdef";
$fairPlayUri = "skd://userspecifc?custom=information";

//CREATE INPUT STREAMS
//video stream of input file
$inputStreamVideo = new InputStream($rtmpInput, 'live', SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);

//audio stream of input file
$inputStreamAudio = new InputStream($rtmpInput, '/', SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

// CREATE VIDEO CODEC CONFIGURATIONS
$codecConfigVideo1080p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo1080p', H264Profile::HIGH, 4800000, 1920, 1080, $fps);
$codecConfigVideo720p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo720p', H264Profile::HIGH, 2400000, 1280, 720, $fps);
$codecConfigVideo480p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo480p', H264Profile::HIGH, 1200000, 854, 480, $fps);
$codecConfigVideo360p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo360p', H264Profile::HIGH, 800000, 640, 360, $fps);
$codecConfigVideo240p = createH264VideoCodecConfiguration($apiClient, 'StreamDemo240p', H264Profile::HIGH, 400000, 426, 240, $fps);

// CREATE AUDIO CODEC CONFIGURATIONS
$codecConfigAudio128 = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000, 48000);

// CREATE VIDEO STREAMS
$videoStream = new Stream($codecConfigVideo1080p, array($inputStreamVideo));
$videoEncodingStream1080p = $apiClient->encodings()->streams($encoding)->create($videoStream);

$videoStream = new Stream($codecConfigVideo720p, array($inputStreamVideo));
$videoEncodingStream720p = $apiClient->encodings()->streams($encoding)->create($videoStream);

$videoStream = new Stream($codecConfigVideo480p, array($inputStreamVideo));
$videoEncodingStream480p = $apiClient->encodings()->streams($encoding)->create($videoStream);

$videoStream = new Stream($codecConfigVideo360p, array($inputStreamVideo));
$videoEncodingStream360p = $apiClient->encodings()->streams($encoding)->create($videoStream);

$videoStream = new Stream($codecConfigVideo240p, array($inputStreamVideo));
$videoEncodingStream240p = $apiClient->encodings()->streams($encoding)->create($videoStream);

// CREATE AUDIO STREAMS
$audioStream = new Stream($codecConfigAudio128, array($inputStreamAudio));
$audioEncodingStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream);

// CREATE VIDEO MUXINGS (FMP4)
$fmp4Muxing1080p = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream1080p, null, null);
$fmp4Muxing720p = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream720p, null, null);
$fmp4Muxing480p = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream480p, null, null);
$fmp4Muxing360p = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream360p, null, null);
$fmp4Muxing240p = createFmp4Muxing($apiClient, $encoding, $videoEncodingStream240p, null, null);

// CREATE AUDIO MUXING (FMP4)
$audioFmp4Muxing128 = createFmp4Muxing($apiClient, $encoding, $audioEncodingStream128, null, null);

// ADD DRM TO VIDEO FMP4 MUXINGs
$cencDrmEncodingOutput1080p = createEncodingOutput($output, $outputPath . 'video/1080p/dash/drm/');
$cencDrmEncodingOutput720p = createEncodingOutput($output, $outputPath . 'video/720p/dash/drm/');
$cencDrmEncodingOutput480p = createEncodingOutput($output, $outputPath . 'video/480p/dash/drm/');
$cencDrmEncodingOutput360p = createEncodingOutput($output, $outputPath . 'video/360p/dash/drm/');
$cencDrmEncodingOutput240p = createEncodingOutput($output, $outputPath . 'video/240p/dash/drm/');

$cencDrm1080p = createCencDrm($cencDrmKey, $cencDrmKid, $widevinePssh, $playreadyLaUrl, array($cencDrmEncodingOutput1080p));
$cencDrm720p = createCencDrm($cencDrmKey, $cencDrmKid, $widevinePssh, $playreadyLaUrl, array($cencDrmEncodingOutput720p));
$cencDrm480p = createCencDrm($cencDrmKey, $cencDrmKid, $widevinePssh, $playreadyLaUrl, array($cencDrmEncodingOutput480p));
$cencDrm360p = createCencDrm($cencDrmKey, $cencDrmKid, $widevinePssh, $playreadyLaUrl, array($cencDrmEncodingOutput360p));
$cencDrm240p = createCencDrm($cencDrmKey, $cencDrmKid, $widevinePssh, $playreadyLaUrl, array($cencDrmEncodingOutput240p));

$videoFmp4Drm1080p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing1080p)->cencDrm()->create($cencDrm1080p);
$videoFmp4Drm720p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing720p)->cencDrm()->create($cencDrm720p);
$videoFmp4Drm480p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing480p)->cencDrm()->create($cencDrm480p);
$videoFmp4Drm360p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing360p)->cencDrm()->create($cencDrm360p);
$videoFmp4Drm240p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($fmp4Muxing240p)->cencDrm()->create($cencDrm240p);

// CREATE DRM TO AUDIO FMP4 MUXINGs
$audioCencDrmEncodingOutput128 = createEncodingOutput($output, $outputPath . 'audio/128kbps/dash/drm/');
$audioCencDrm128 = createCencDrm($cencDrmKey, $cencDrmKid, $widevinePssh, $playreadyLaUrl, array($audioCencDrmEncodingOutput128));
$audioFmp4Drm128 = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->drm($audioFmp4Muxing128)->cencDrm()->create($audioCencDrm128);

// CREATE VIDEO MUXINGS (TS)
$tsMuxing1080p = createTsMuxing($apiClient, $encoding, $videoEncodingStream1080p, null, null);
$tsMuxing720p = createTsMuxing($apiClient, $encoding, $videoEncodingStream720p, null, null);
$tsMuxing480p = createTsMuxing($apiClient, $encoding, $videoEncodingStream480p, null, null);
$tsMuxing360p = createTsMuxing($apiClient, $encoding, $videoEncodingStream360p, null, null);
$tsMuxing240p = createTsMuxing($apiClient, $encoding, $videoEncodingStream240p, null, null);

// CREATE AUDIO MUXING (TS)
$audioTsMuxing128 = createTsMuxing($apiClient, $encoding, $audioEncodingStream128, null, null);

// ADD DRM TO VIDEO TS MUXINGs
$fairPlayEncodingOutput1080p = createEncodingOutput($output, $outputPath . 'video/1080p/hls/drm/');
$fairPlayEncodingOutput720p = createEncodingOutput($output, $outputPath . 'video/720p/hls/drm/');
$fairPlayEncodingOutput480p = createEncodingOutput($output, $outputPath . 'video/480p/hls/drm/');
$fairPlayEncodingOutput360p = createEncodingOutput($output, $outputPath . 'video/360p/hls/drm/');
$fairPlayEncodingOutput240p = createEncodingOutput($output, $outputPath . 'video/240p/hls/drm/');

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
$audioFairPlayEncodingOutput128 = createEncodingOutput($output, $outputPath . 'audio/128kbps/hls/drm/');
$audioFairPlayDrm128 = createFairPlayDrm($fairPlayKey, $fairPlayIV, $fairPlayUri, array($audioFairPlayEncodingOutput128));
$audioTsDrm128 = $apiClient->encodings()->muxings($encoding)->tsMuxing()->drm($audioTsMuxing128)->fairplay()->create($audioFairPlayDrm128);

//MANIFEST OUTPUT DESTINATION
$manifestOutput = new EncodingOutput($output);
$manifestOutput->setOutputPath($outputPath);
$acl = new Acl(AclPermission::ACL_PUBLIC_READ);
$manifestOutput->setAcl([$acl]);

//DASH MANIFEST
$manifestName = "livestream.mpd";
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

// ADD CONTENT PROTECTION TO VIDEO ADAPTATION SET
$videoContentProtection = createContentProtectionForAdaptationSet($encoding, $videoFmp4Drm1080p, $fmp4Muxing1080p);
$apiClient->manifests()->dash()->addContentProtectionToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $videoContentProtection);

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

$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashDrmRepresentation1080p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashDrmRepresentation720p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashDrmRepresentation480p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashDrmRepresentation360p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashVideoAdaptionSet, $dashDrmRepresentation240p);

// CREATE AUDIO ADAPTATION SET FOR EACH LANGUAGE
$audioAdaptionSet = new AudioAdaptationSet();
$audioAdaptionSet->setLang("en");
$dashAudioAdaptionSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($dashManifest, $manifestPeriod, $audioAdaptionSet);

// ADD CONTENT PROTECTION TO AUDIO ADAPTATION SET
$audioContentProtection = createContentProtectionForAdaptationSet($encoding, $audioFmp4Drm128, $audioFmp4Muxing128);
$apiClient->manifests()->dash()->addContentProtectionToAdaptationSet($dashManifest, $manifestPeriod, $dashAudioAdaptionSet, $audioContentProtection);

// ADD DRM PROTECTED AUDIO REPRESENTATIONS TO ADAPTATION SET
$audioSegmentPath240p = getSegmentOutputPath($outputPath, $audioFmp4Drm128->getOutputs()[0]->getOutputPath());

$audioDashDrmRepresentation128 = createDashDrmRepresentation($encoding, $audioFmp4Drm128, $audioFmp4Muxing128, DashMuxingType::TYPE_TEMPLATE, $audioSegmentPath240p);

$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $dashAudioAdaptionSet, $audioDashDrmRepresentation128);

// CREATE HLS PLAYLIST
$manifestName = "livestream.m3u8";
$manifest = new HlsManifest();
$manifest->setOutputs(array($manifestOutput));
$manifest->setManifestName($manifestName);
$hlsManifest = $apiClient->manifests()->hls()->create($manifest);
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
$videoStreamInfo1080p = createHlsVariantStreamInfo($encoding, $videoEncodingStream1080p, $tsMuxing1080p, $videoTsDrm1080p, $audioGroupId, $tsSegmentPath1080p, $variantStreamUri1080p);
$videoStreamInfo720p = createHlsVariantStreamInfo($encoding, $videoEncodingStream720p, $tsMuxing720p, $videoTsDrm720p, $audioGroupId, $tsSegmentPath720p, $variantStreamUri720p);
$videoStreamInfo480p = createHlsVariantStreamInfo($encoding, $videoEncodingStream480p, $tsMuxing480p, $videoTsDrm480p, $audioGroupId, $tsSegmentPath480p, $variantStreamUri480p);
$videoStreamInfo360p = createHlsVariantStreamInfo($encoding, $videoEncodingStream360p, $tsMuxing360p, $videoTsDrm360p, $audioGroupId, $tsSegmentPath360p, $variantStreamUri360p);
$videoStreamInfo240p = createHlsVariantStreamInfo($encoding, $videoEncodingStream240p, $tsMuxing240p, $videoTsDrm240p, $audioGroupId, $tsSegmentPath240p, $variantStreamUri240p);

$variantStream1080p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $videoStreamInfo1080p);
$variantStream720p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $videoStreamInfo720p);
$variantStream480p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $videoStreamInfo480p);
$variantStream360p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $videoStreamInfo360p);
$variantStream240p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $videoStreamInfo240p);


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
$audioMediaInfo128->setDrmId($audioFairPlayDrm128->getId());
$audioMediaInfo128->setAutoselect(false);
$audioMediaInfo128->setDefault(false);
$audioMediaInfo128->setForced(false);
$audioMediaInfo128->setSegmentPath($audioSegmentPath128);

$apiClient->manifests()->hls()->createMediaInfo($hlsManifest, $audioMediaInfo128);

//###########################################################################################

$liveDashManifest = new LiveDashManifest();
$liveDashManifest->setManifestId($dashManifest->getId());
$liveDashManifest->setLiveEdgeOffset($dashLiveEdgeOffset);

$liveHlsManifest = new LiveHlsManifest();
$liveHlsManifest->setManifestId($hlsManifest->getId());
$liveHlsManifest->setTimeshift($hlsTimeShiftWindowSize);
$liveHlsManifest->setLiveEdgeOffset($hlsLiveEdgeOffset);

$startLiveEncodingRequest = new StartLiveEncodingRequest();
$startLiveEncodingRequest->setStreamKey($streamKey);
$startLiveEncodingRequest->setDashManifests(array($liveDashManifest));
$startLiveEncodingRequest->setHlsManifests(array($liveHlsManifest));

$apiClient->encodings()->startLivestreamWithManifests($encoding, $startLiveEncodingRequest);

do
{
    $status = $apiClient->encodings()->status($encoding);
    var_dump(date_create(null, new DateTimeZone('UTC'))->getTimestamp() . ": " . $status->getStatus());
    $isRunning = !in_array($status->getStatus(), array(Status::RUNNING, Status::ERROR));
    sleep(1);
} while ($isRunning);

// WAIT UNTIL LIVE STREAM DATA ARE AVAILABLE
$liveEncodingDetails = null;
do
{
    try
    {
        $liveEncodingDetails = $apiClient->encodings()->getLivestreamDetails($encoding);
    }
    catch (BitmovinException $exception)
    {
        if ($exception->getCode() != 400)
        {
            print 'Got unexpected exception with code ' . strval($exception->getCode()) . ': ' . $exception->getMessage();
            throw $exception;
        }
        sleep(1);
    }
} while ($liveEncodingDetails == null);
print 'RTMP Url: rtmp://' . $liveEncodingDetails->getEncoderIp() . '/live' . "\n";
print 'Stream-Key: ' . $liveEncodingDetails->getStreamKey() . "\n";
print 'Encoding-Id: ' . $encoding->getId() . "\n";

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
function createCencDrm($key, $kid, $widevinePssh, $playreadyLaUrl, array $outputs)
{
    //CREATE CENC DRM CONFIGURATION
    $cencDrm = new CencDrm($key, $kid, $outputs);
    $cencDrm->setWidevine(new CencWidevine($widevinePssh));
    $cencDrm->setPlayReady(new CencPlayReady($playreadyLaUrl));

    return $cencDrm;
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
