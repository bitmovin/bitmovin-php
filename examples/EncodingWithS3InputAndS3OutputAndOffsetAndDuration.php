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
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\StartEncodingRequest;
use Bitmovin\api\model\encodings\StartEncodingTrimming;
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
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('e238df1f-5b5b-4ec6-b033-2b766990d3bd');

// S3 INPUT CONFIGURATION
$s3InputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3InputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3InputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$videoInputPath = "path/to/your/input/file.mp4";

$s3OutputAccessKey = 'YOUR-AWS-S3-ACCESS-KEY';
$s3OutputSecretKey = 'YOUR-AWS-S3-SECRET-KEY';
$s3OutputBucketName = "YOUR-AWS-S3-BUCKETNAME";
$outputPath = "path/to/your/output-destination/";

$offset = 10.0;
$duration = 20.0;

// CREATE ENCODING
$encoding = new Encoding('A Name for your encoding');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding = $apiClient->encodings()->create($encoding);

$s3Input = new S3Input($s3InputBucketName, $s3InputAccessKey, $s3InputSecretKey);
$s3Input = $apiClient->inputs()->s3()->create($s3Input);

$s3Output = new S3Output($s3OutputBucketName, $s3OutputAccessKey, $s3OutputSecretKey);
$s3Output = $apiClient->outputs()->s3()->create($s3Output);

$configurations = [
    [ 'height' => 1080, 'bitrate' => 4800, 'profile' => H264Profile::HIGH],
    [ 'height' => 720, 'bitrate' => 2400, 'profile' => H264Profile::HIGH],
    [ 'height' => 480, 'bitrate' => 1200, 'profile' => H264Profile::MAIN],
    [ 'height' => 360, 'bitrate' => 800, 'profile' => H264Profile::MAIN],
    [ 'height' => 240, 'bitrate' => 400, 'profile' => H264Profile::BASELINE],
];


// CREATE VIDEO CODEC CONFIGURATIONS
foreach ($configurations as &$configuration)
{
    $configuration['codec'] = createH264VideoCodecConfiguration($apiClient, 'H264 ' . $configuration['height'] . ' ' . $configuration['bitrate'],
        $configuration['profile'], $configuration['bitrate'] * 1000, null, $configuration['height']);
}

// CREATE AUDIO CODEC CONFIGURATIONS
$codecConfigAudio128kbit = createAACAudioCodecConfiguration($apiClient, 'StreamDemoAAC128k', 128000);

//CREATE AUDIO / VIDEO INPUT STREAMS
$inputStreamVideo = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamVideo->setPosition(0);
$inputStreamAudio = new InputStream($s3Input, $videoInputPath, SelectionMode::AUTO);
$inputStreamAudio->setPosition(1);

// CREATE VIDEO STREAMS
foreach ($configurations as &$configuration)
{
    $stream = new Stream($configuration['codec'], array($inputStreamVideo));
    $configuration['stream'] = $apiClient->encodings()->streams($encoding)->create($stream);
}

// CREATE AUDIO STREAMS
$audioStream128 = new Stream($codecConfigAudio128kbit, array($inputStreamAudio));
$audioStream128 = $apiClient->encodings()->streams($encoding)->create($audioStream128);

// CREATE VIDEO MUXINGS
foreach ($configurations as &$configuration)
{
    $height = $configuration['height'];
    $bitrate = $configuration['bitrate'];
    $configuration['fmp4_muxing'] = createFmp4Muxing($apiClient, $encoding, $configuration['stream'], $s3Output,
        $outputPath . 'video/' . $height . '_' . $bitrate . '/dash/', AclPermission::ACL_PUBLIC_READ);
    $configuration['ts_muxing'] = createTsMuxing($apiClient, $encoding, $configuration['stream'], $s3Output,
        $outputPath . 'video/' . $height . '_' . $bitrate . '/dash/', AclPermission::ACL_PUBLIC_READ);
}

// CREATE AUDIO MUXING (FMP4)
$audioFmp4Muxing128 = createFmp4Muxing($apiClient, $encoding, $audioStream128, $s3Output, $outputPath . 'audio/128kbps/dash/', AclPermission::ACL_PUBLIC_READ);
// CREATE AUDIO MUXING (TS)
$audioTsMuxing128 = createTsMuxing($apiClient, $encoding, $audioStream128, $s3Output, $outputPath . 'audio/128kbps/hls/', AclPermission::ACL_PUBLIC_READ);

$startEncodingRequest = new StartEncodingRequest();
$startEncodingTrimming = new StartEncodingTrimming();
$startEncodingTrimming->setOffset($offset);
$startEncodingTrimming->setDuration($duration);
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
    exit();
}

//###########################################################################################

//MANIFEST OUTPUT DESTINATION
$manifestOutput = new EncodingOutput($s3Output);
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
$manifestPeriod = $apiClient->manifests()->dash()->createPeriod($manifest, $period);

// CREATE VIDEO ADPAPTATION SET
$videoAdaptionSet = new VideoAdaptationSet();
$videoAdaptionSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($manifest, $manifestPeriod, $videoAdaptionSet);

// CREATE AUDIO ADAPTATION SET FOR EACH LANGUAGE
$audioAdaptionSet = new AudioAdaptationSet();
$audioAdaptionSet->setLang("en");
$audioAdaptionSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($manifest, $manifestPeriod, $audioAdaptionSet);

// ADD VIDEO REPRESENTATIONS TO ADAPTATION SET
foreach ($configurations as &$configuration)
{
    /** @var FMP4Muxing $fmp4Muxing */
    $fmp4Muxing = $configuration['fmp4_muxing'];
    $fmp4SegmentPath = getSegmentOutputPath($outputPath, $fmp4Muxing->getOutputs()[0]->getOutputPath());
    $dashRepresentation = createDashRepresentation($encoding, $fmp4Muxing, DashMuxingType::TYPE_TEMPLATE, $fmp4SegmentPath);
    $apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $manifestPeriod, $videoAdaptionSet, $dashRepresentation);
}

// ADD AUDIO REPRESENTATIONS TO ADAPTATION SET
$audioSegmentPath240p = getSegmentOutputPath($outputPath, $audioFmp4Muxing128->getOutputs()[0]->getOutputPath());
$audioDashRepresentation128 = createDashRepresentation($encoding, $audioFmp4Muxing128, DashMuxingType::TYPE_TEMPLATE, $audioSegmentPath240p);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $manifestPeriod, $audioAdaptionSet, $audioDashRepresentation128);

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

foreach ($configurations as &$configuration)
{
    /** @var TSMuxing $tsMuxing */
    $tsMuxing = $configuration['ts_muxing'];
    $variantStreamUri = "video_" . $configuration['height'] .'_' . $configuration['bitrate'] . "_variant.m3u8";
    $tsSegmentPath = getSegmentOutputPath($outputPath, $tsMuxing->getOutputs()[0]->getOutputPath());
    $videoStreamInfo = createHlsVariantStreamInfo($encoding, $configuration['stream'], $tsMuxing, $audioGroupId, $tsSegmentPath, $variantStreamUri);
    $apiClient->manifests()->hls()->createStreamInfo($masterPlaylist, $videoStreamInfo);
}

$audioVariantStreamUri128 = "audio_1_" . $codecConfigAudio128kbit->getBitrate() . "_variant.m3u8";
$audioSegmentPath128 = getSegmentOutputPath($outputPath, $audioTsMuxing128->getOutputs()[0]->getOutputPath());

$audioMediaInfo128 = new MediaInfo();
$audioMediaInfo128->setGroupId($audioGroupId);
$audioMediaInfo128->setName("English");
$audioMediaInfo128->setLanguage("English");
$audioMediaInfo128->setUri($audioVariantStreamUri128);
$audioMediaInfo128->setType(MediaInfoType::AUDIO);
$audioMediaInfo128->setEncodingId($encoding->getId());
$audioMediaInfo128->setStreamId($audioStream128->getId());
$audioMediaInfo128->setMuxingId($audioTsMuxing128->getId());
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
function createFmp4Muxing($apiClient, $encoding, $stream, $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ, $segmentDuration = 4, $initSegmentName = 'init.mp4', $segmentNaming = 'segment_%number%.m4s')
{
    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($stream->getId());
    $encodingOutputs = array();

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