<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\enum\codecConfigurations\CodecConfigType;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\AutoRepresentation;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\EncodingMode;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\pertitle\H264PerTitleConfiguration;
use Bitmovin\api\model\encodings\pertitle\PerTitle;
use Bitmovin\api\model\encodings\StartEncodingRequest;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\encodings\streams\StreamMode;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;


require_once __DIR__ . '/../vendor/autoload.php';

$bitmovinApiKey = '<INSERT YOUR API KEY>';

$encodingName = 'PHP Example - Per Title';
$encodingRegion = CloudRegion::AUTO;

$inputS3AccessKey = '<INSERT_YOUR_ACCESS_KEY>';
$inputS3SecretKey = '<INSERT_YOUR_SECRET_KEY>';
$inputS3Bucketname = '<INSERT_YOUR_BUCKET_NAME>';

const INPUT_PATH = "/path/to/your/input/file.mp4";

$outputS3AccessKey = '<INSERT_YOUR_ACCESS_KEY>';
$outputS3SecretKey = '<INSERT_YOUR_SECRET_KEY>';
$outputS3Bucketname = '<INSERT_YOUR_BUCKET_NAME>';

const OUTPUT_BASE_PATH = "/your/output/base/path/";


// ====================================================================================================================


try
{
    $apiClient = new ApiClient($bitmovinApiKey);

    //Create the input resource to access the input file
    $s3Input = new S3Input($inputS3Bucketname, $inputS3AccessKey, $inputS3SecretKey);
    $s3Input->setName("Sample S3 Input");
    $s3Input = $apiClient->inputs()->s3()->create($s3Input);

    //Create the output resource to write the output files
    $s3Output = new S3Output($outputS3Bucketname, $outputS3AccessKey, $outputS3SecretKey);
    $s3Output->setName("Sample S3 Output");
    $s3Output = $apiClient->outputs()->s3()->create($s3Output);

    //The encoding is created. The cloud region is set to AUTO to use the best cloud region depending on the input
    $encoding = new Encoding($encodingName);
    $encoding->setCloudRegion($encodingRegion);
    $encoding = $apiClient->encodings()->create($encoding);

    //Select the video and audio input stream that should be encoded
    $audioInputStream = new InputStream($s3Input, INPUT_PATH , SelectionMode::AUTO);
    $videoInputStream = new InputStream($s3Input, INPUT_PATH , SelectionMode::AUTO);

    $audioStream = createAudioStream($apiClient, $encoding, $audioInputStream);
    $videoStream = createH264PerTitleVideoStream($apiClient, $encoding, $videoInputStream);

    $audiofMp4Muxing = createfMp4Muxing($apiClient, $encoding, $s3Output, $audioStream, "audio/dash/128kbps/");
    $videofMp4Muxing = createfMp4Muxing($apiClient, $encoding, $s3Output, $videoStream, "video/dash/{width}_{bitrate}_{uuid}/");

    $audioTSMuxing = createTSMuxing($apiClient, $encoding, $s3Output, $audioStream, "audio/hls/128kbps/");
    $videoTSMuxing = createTSMuxing($apiClient, $encoding, $s3Output, $videoStream, "video/hls/{width}_{bitrate}_{uuid}/");

    startEncoding($apiClient, $encoding);
    generateManifests($apiClient, $encoding, $s3Output, $audioStream, $audiofMp4Muxing, $audioTSMuxing);

}
catch (BitmovinException $e)
{
    var_dump("Bitmovin Exception", $e->getMessage(), $e->getDeveloperMessage());
    exit(1);
}
catch (Exception $e)
{
    var_dump($e->getMessage());
    exit(1);
}

//#####################################################################################################################

/**
 * This will create the audio stream that will be encoded with the given codec configuration.
 *
 * @param ApiClient $apiClient
 * @param Encoding $encoding The reference of the encoding
 * @param InputStream $audioInputStream The input stream that should be encoded
 * @return Stream The created audio stream. This will be used later for the MP4 muxing
 * @throws BitmovinException
 */
function createAudioStream($apiClient, $encoding, $audioInputStream)
{
    $audioCodecConfiguration = new AACAudioCodecConfiguration('audio_codec_configuration', 128000, 48000);
    $audioCodecConfiguration = $apiClient->codecConfigurations()->audioAAC()->create($audioCodecConfiguration);
    $audioStream = new Stream($audioCodecConfiguration, array($audioInputStream));
    $audioStream = $apiClient->encodings()->streams($encoding)->create($audioStream);
    return $audioStream;
}

/**
 * This will create the Per-Title template video stream. This stream will be used as a template for the Per-Title
 * encoding. The Codec Configuration, Muxings, DRMs and Filters applied to the generated Per-Title profile will be
 * based on the same, or closest matching resolutions defined in the template.
 * Please note, that template streams are not necessarily used for the encoding -
 * they are just used as template.
 *
 * @param ApiClient $apiClient The Bitmovin Api-Client
 * @param Encoding $encoding The reference of the encoding
 * @param InputStream $videoInputStream The input stream that should be encoded
 * @return Stream The created Per-Title template video stream. This will be used later for the MP4 muxing
 * @throws BitmovinException
 */
function createH264PerTitleVideoStream($apiClient, $encoding, $videoInputStream)
{
    $videoSCodecConfiguration = new H264VideoCodecConfiguration('H264 Configuration', H264Profile::HIGH, null, null);
    $videoSCodecConfiguration = $apiClient->codecConfigurations()->videoH264()->create($videoSCodecConfiguration);
    $videoStream = new Stream($videoSCodecConfiguration, array($videoInputStream));
    $videoStream->setMode(StreamMode::PER_TITLE_TEMPLATE);
    $videoStream = $apiClient->encodings()->streams($encoding)->create($videoStream);
    return $videoStream;
}

/**
 * An MP4 muxing will be created for with the Per-Title video stream template and the audio stream.
 * This muxing must define either {uuid} or {bitrate} in the output path.  These placeholders will be replaced during
 * the generation of the Per-Title.
 *
 * @param ApiClient $apiClient The Bitmovin Api-Client
 * @param Encoding $encoding The reference of the encoding
 * @param S3Output $s3Output The output the files should be written to
 * @param Stream $stream The Per-Title template video stream
 * @param String $outputPathSuffix The suffix to be concatenated to OUTPUT_BASE_PATH to compose the output path of the muxing.
 * @return FMP4Muxing
 * @throws BitmovinException
 */
function createfMp4Muxing($apiClient, $encoding, $s3Output, $stream, $outputPathSuffix)
{
    $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
    $muxingOutput = new EncodingOutput($s3Output);
    $muxingOutput->setOutputPath(OUTPUT_BASE_PATH . $outputPathSuffix);
    $muxingOutput->setAcl(array($acl));

    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());

    $fmp4Muxing = new FMP4Muxing();
    $fmp4Muxing->setStreams(array($muxingStream));
    $fmp4Muxing->setSegmentLength(4);
    $fmp4Muxing->setSegmentNaming("seg_%number%.m4s");
    $fmp4Muxing->setInitSegmentName("init.mp4");
    $fmp4Muxing->setOutputs(array($muxingOutput));

    return $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing);
}

/**
 * A TS muxing will be created for with the Per-Title video stream template and the audio stream.
 * This muxing must define either {uuid} or {bitrate} in the output path.  These placeholders will be replaced during
 * the generation of the Per-Title.
 *
 * @param ApiClient $apiClient The Bitmovin Api-Client
 * @param Encoding $encoding The reference of the encoding
 * @param S3Output $s3Output The output the files should be written to
 * @param Stream $stream The Per-Title template video stream
 * @param String $outputPathSuffix The suffix to be concatenated to OUTPUT_BASE_PATH to compose the output path of the muxing.
 * @return TSMuxing
 * @throws BitmovinException
 */
function createTSMuxing($apiClient, $encoding, $s3Output, $stream, $outputPathSuffix)
{
    $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
    $muxingOutput = new EncodingOutput($s3Output);
    $muxingOutput->setOutputPath(OUTPUT_BASE_PATH . $outputPathSuffix);
    $muxingOutput->setAcl(array($acl));

    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());

    $tsMuxing = new TSMuxing();
    $tsMuxing->setStreams(array($muxingStream));
    $tsMuxing->setSegmentLength(4);
    $tsMuxing->setSegmentNaming("segment_%number%.ts");
    $tsMuxing->setOutputs(array($muxingOutput));

    return $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($tsMuxing);
}



/**
 * The encoding will be started with the per title object. Stream configurations will be automatically added to
 * the Per-Title profile. In that case at least one PER_TITLE_TEMPLATE stream configuration must be available.
 * All other configurations will be automatically chosen by the Per-Title algorithm. All relevant settings for
 * streams and muxings will be taken from the closest PER_TITLE_TEMPLATE stream defined. The closest stream will
 * be chosen based on the resolution specified in the codec configuration.
 *
 * @param ApiClient $apiClient The Bitmovin api-client
 * @param Encoding $encoding The reference of the encoding
 */
function startEncoding($apiClient, $encoding)
{
    $autoRepresentation = new AutoRepresentation();
    $h264PerTitleConfiguration = new H264PerTitleConfiguration();
    $h264PerTitleConfiguration->setAutoRepresentation($autoRepresentation);

    $perTitle = new PerTitle();
    $perTitle->setH264PerTitleConfiguration($h264PerTitleConfiguration);

    $startEncodingRequest = new StartEncodingRequest();
    $startEncodingRequest->setEncodingMode(EncodingMode::THREE_PASS);
    $startEncodingRequest->setPerTitle($perTitle);

    $apiClient->encodings()->startWithEncodingRequest($encoding, $startEncodingRequest);

    // Wait for the encoding to finish
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
}

/**
 * This will create and generate Dash and HLS manifests for the given encoding. It will fetch PER_TITLE_RESULT streams
 * and reference them in the manifests.
 *
 * @param ApiClient $apiClient The Bitmovin api-client
 * @param Encoding $encoding The reference of the encoding
 * @param Stream audioStream The reference of the audio stream to be linked in the manifests.
 * @param FMP4Muxing $audiofMp4Muxing The reference of the audio fmp4 muxing for DASH.
 * @param TSMuxing $audioTSMuxing The reference of the audio TS muxing for HLS.
 * @throws BitmovinException
 */
function generateManifests($apiClient, $encoding, $s3Output, $audioStream, $audiofMp4Muxing, $audioTSMuxing) {
    // CREATE DASH MANIFEST
    $manifestOutput = new EncodingOutput($s3Output);
    $manifestOutput->setOutputPath(OUTPUT_BASE_PATH);
    $manifestOutput->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));

    $dashManifest = new DashManifest();
    $dashManifest->setName("stream.mpd");
    $dashManifest->setManifestName("stream.mpd");
    $dashManifest->setOutputs(array($manifestOutput));
    $dashManifest = $apiClient->manifests()->dash()->create($dashManifest);

    $period = new Period();
    $period = $apiClient->manifests()->dash()->createPeriod($dashManifest, $period);

    $videoAdaptationSet = new VideoAdaptationSet();
    $videoAdaptationSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($dashManifest, $period, $videoAdaptationSet);

    $audioAdaptationSet = new AudioAdaptationSet();
    $audioAdaptationSet->setLang('en');
    $audioAdaptationSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($dashManifest, $period, $audioAdaptationSet);

    $audioRepresentation = new DashRepresentation();
    $audioRepresentation->setType(DashMuxingType::TYPE_TEMPLATE);
    $audioRepresentation->setEncodingId($encoding->getId());
    $audioRepresentation->setMuxingId($audiofMp4Muxing->getId());
    $audioRepresentation->setSegmentPath('audio/dash/128kbps');
    $apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $audioAdaptationSet, $audioRepresentation);

    $fmp4Muxings = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->listPage();

    foreach ($fmp4Muxings as $fmp4Muxing) {
        $streamDetails = $apiClient->encodings()->streams($encoding)->getById($fmp4Muxing->getStreams()[0]->getStreamId());
        $codecConfigType = $apiClient->codecConfigurations()->type()->getTypeById($streamDetails->getCodecConfigId());

        $streamMode = $streamDetails->getMode();
        $isAudioMuxing = in_array($codecConfigType->getType(), array(CodecConfigType::AAC));

        if ($isAudioMuxing) {
            # we ignore the audio muxing
            continue;
        }

        if ($streamMode != StreamMode::PER_TITLE_RESULT) {
            # we ignore template streams
            continue;
        }

        $segmentPath = $fmp4Muxing->getOutputs()[0]->getOutputPath();
        $segmentPath = getSegmentOutputPath(OUTPUT_BASE_PATH, $segmentPath);

        $videoRepresentation_1080p = new DashRepresentation();
        $videoRepresentation_1080p->setType(DashMuxingType::TYPE_TEMPLATE);
        $videoRepresentation_1080p->setEncodingId($encoding->getId());
        $videoRepresentation_1080p->setMuxingId($fmp4Muxing->getId());
        $videoRepresentation_1080p->setSegmentPath($segmentPath);
        $apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_1080p);
    }

    $apiClient->manifests()->dash()->start($dashManifest);

    // CREATE HLS MANIFEST
    $hlsManifest = new HlsManifest();
    $hlsManifest->setName('stream.m3u8');
    $hlsManifest->setManifestName('stream.m3u8');
    $hlsManifest->setOutputs(array($manifestOutput));
    $hlsManifest = $apiClient->manifests()->hls()->create($hlsManifest);

    $mediaInfo = new MediaInfo();
    $mediaInfo->setGroupId('audio');
    $mediaInfo->setName('English');
    $mediaInfo->setUri('audio.m3u8');
    $mediaInfo->setType(MediaInfoType::AUDIO);
    $mediaInfo->setSegmentPath('audio/hls/128kbps');
    $mediaInfo->setMuxingId($audioTSMuxing->getId());
    $mediaInfo->setStreamId($audioStream->getId());
    $mediaInfo->setEncodingId($encoding->getId());
    $mediaInfo->setLanguage('en');
    $mediaInfo->setAssocLanguage('en');
    $mediaInfo->setAutoselect(false);
    $mediaInfo->setDefault(false);
    $mediaInfo->setForced(false);
    $mediaInfo = $apiClient->manifests()->hls()->createMediaInfo($hlsManifest, $mediaInfo);

    $tsMuxings = $apiClient->encodings()->muxings($encoding)->tsMuxing()->listPage();

    foreach ($tsMuxings as $index=>$tsMuxing) {
        $streamDetails = $apiClient->encodings()->streams($encoding)->getById($tsMuxing->getStreams()[0]->getStreamId());
        $codecConfigType = $apiClient->codecConfigurations()->type()->getTypeById($streamDetails->getCodecConfigId());

        $streamMode = $streamDetails->getMode();
        $isAudioMuxing = in_array($codecConfigType->getType(), array(CodecConfigType::AAC));

        if ($isAudioMuxing) {
            # we ignore the audio muxing
            continue;
        }

        if ($streamMode != StreamMode::PER_TITLE_RESULT) {
            # we ignore template streams
            continue;
        }

        $segmentPath = $tsMuxing->getOutputs()[0]->getOutputPath();
        $segmentPath = getSegmentOutputPath(OUTPUT_BASE_PATH, $segmentPath);

        $streamInfo = new StreamInfo();
        $streamInfo->setUri('video_' . $index . '.m3u8');
        $streamInfo->setEncodingId($encoding->getId());
        $streamInfo->setStreamId($tsMuxing->getStreams()[0]->getStreamId());
        $streamInfo->setMuxingId($tsMuxing->getId());
        $streamInfo->setAudio('audio');
        $streamInfo->setSegmentPath($segmentPath);
        $streamInfo = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo);
    }

    $apiClient->manifests()->hls()->start($hlsManifest);
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
