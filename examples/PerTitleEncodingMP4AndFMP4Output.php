<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\CodecConfigType;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\encodings\ConditionAttribute;
use Bitmovin\api\enum\encodings\ConditionOperator;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
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
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\condition\Condition;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\Input;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

const BITRATE_ADJUSTMENT_UPPER_BOUNDARY = 1.5;
const BITRATE_ADJUSTMENT_LOWER_BOUNDARY = 0.5;
const COMPLEXITY_MEDIAN_VALUE = 500000;
const ENCODING_STATUS_REFRESH_RATE = 10;

$bitmovinApiKey = 'YOUR_BITMOVIN_API_KEY';
$uniqueId = uniqid();
$encodingName = 'Per-Title-Encoding with MP4/FMP4 Output #' . $uniqueId;
$encodingRegion = CloudRegion::AUTO;

$inputS3AccessKey = 'YOUR_AWS_S3_ACCESS_KEY';
$inputS3SecretKey = 'YOUR_AWS_S3_SECRET_KEY';
$inputS3Bucketname = 'YOUR_AWS_S3_BUCKET_NAME';

$outputS3AccessKey = 'YOUR_AWS_S3_ACCESS_KEY';
$outputS3SecretKey = 'YOUR_AWS_S3_SECRET_KEY';
$outputS3Bucketname = 'YOUR_AWS_S3_BUCKET_NAME';

$baseOutputPath = "your/base/output/path/";
$videoFiles = array(
    array(
        'encodingName' => 'Video-1',
        'inputPath'    => 'path/to/your/input-file-1.mp4',
        'outputPath'   => $baseOutputPath . '/video-1/'
    ),
    array(
        'encodingName' => 'Video-2',
        'inputPath'    => 'path/to/your/input-file-2.mp4',
        'outputPath'   => $baseOutputPath . '/video-2/'
    )
);

$bitrateLadderEntries = array(
    array("codec" => "h264", "height" => 1080, "bitrate" => 4300000, "profile" => H264Profile::HIGH, "highComplexityImpact" => 0.3, "lowComplexityImpact" => 1.5),
    array("codec" => "h264", "height" => 720, "bitrate" => 2500000, "profile" => H264Profile::HIGH, "highComplexityImpact" => 0.4, "lowComplexityImpact" => 1.3),
    array("codec" => "h264", "height" => 720, "bitrate" => 1900000, "profile" => H264Profile::HIGH, "highComplexityImpact" => 0.45, "lowComplexityImpact" => 1.0),
    array("codec" => "h264", "height" => 540, "bitrate" => 1300000, "profile" => H264Profile::HIGH, "highComplexityImpact" => 0.9, "lowComplexityImpact" => 0.9),
    array("codec" => "h264", "height" => 360, "bitrate" => 800000, "profile" => H264Profile::HIGH, "highComplexityImpact" => 1.0, "lowComplexityImpact" => 0.45),
    array("codec" => "h264", "height" => 270, "bitrate" => 450000, "profile" => H264Profile::HIGH, "highComplexityImpact" => 1.3, "lowComplexityImpact" => 0.4),
    array("codec" => "h264", "height" => 180, "bitrate" => 260000, "profile" => H264Profile::HIGH, "highComplexityImpact" => 1.5, "lowComplexityImpact" => 0.3)
);

$audioEncodingProfiles = array(
    array("codec" => "aac", "bitrate" => 128000)
);

// ====================================================================================================================

try
{
    // INITIALIZE BITMOVIN API CLIENT
    $apiClient = new ApiClient($bitmovinApiKey);

    //CREATE S3 INPUT
    $input = new S3Input($inputS3Bucketname, $inputS3AccessKey, $inputS3SecretKey);
    $input->setName("AWS S3 Input Bucket");
    $input = $apiClient->inputs()->s3()->create($input);

    //CREATE S3 OUTPUT
    $output = new S3Output($outputS3Bucketname, $outputS3AccessKey, $outputS3SecretKey);
    $output->setName("AWS S3 Output Bucket");
    $output = $apiClient->outputs()->s3()->create($output);

    //Create encoding to calculate the complexity factor
    $activeCrfEncodings = array();
    foreach ($videoFiles as $key => $videoFile)
    {
        $inputPath = $videoFile['inputPath'];
        $outputPath = $videoFile['outputPath'];
        $encodingName = "Complexity Factor | " . $videoFile['encodingName'];
        //Run encoding to calculate the complexity factor
        $videoFiles[$key]['complexityFactorEncoding'] = runFastCrfEncoding($apiClient, $encodingName, $encodingRegion, $input, $inputPath, $output, $outputPath);
    }

    //Wait until all Complexity Factor encodings are finished
    $allCrfFinished = false;
    do
    {
        $states = array();
        foreach ($videoFiles as $key => $videoFile)
        {
            /** @var Encoding $currentCrfEncoding */
            $currentCrfEncoding = $videoFile['complexityFactorEncoding'];
            $status = $apiClient->encodings()->status($currentCrfEncoding);
            $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
            $states[] = $isRunning;
            $currentTimestamp = date_create(null, new DateTimeZone('UTC'))->getTimestamp();
            echo $currentTimestamp . ": " . $currentCrfEncoding->getName() . " => " . $status->getStatus() . "\n";
        }
        $allCrfFinished = !in_array(true, $states);
        sleep(ENCODING_STATUS_REFRESH_RATE);
    } while (!$allCrfFinished);

    //START PER TITLE ENCODINGS
    foreach ($videoFiles as $key => $videoFile)
    {
        $staticVideoEncodingConfigs = array();
        $videoEncodingConfigs = array();

        /** @var Encoding $currentCrfEncoding */
        $currentCrfEncoding = $videoFile['complexityFactorEncoding'];
        $inputPath = $videoFile['inputPath'];
        $outputPath = $videoFile['outputPath'];
        $encodingName = $videoFile['encodingName'];
        $crfMuxing = $apiClient->encodings()->muxings($videoFile['complexityFactorEncoding'])->fmp4Muxing()->listPage()[0];

        // CREATE ENCODING
        $encoding = new Encoding($encodingName);
        $encoding->setCloudRegion($encodingRegion);
        $encoding = $apiClient->encodings()->create($encoding);

        //CREATE VIDEO/AUDIO INPUT STREAMS
        $inputStreamVideo = new InputStream($input, $inputPath, SelectionMode::AUTO);
        $inputStreamAudio = new InputStream($input, $inputPath, SelectionMode::AUTO);

        //CREATE AUDIO CODEC CONFIGURATIONS
        $audioEncodingConfigs = array();
        foreach ($audioEncodingProfiles as $encodingProfile)
        {
            if ($encodingProfile["codec"] !== "aac")
                continue;

            $audioEncodingConfig = array();
            $audioEncodingConfig['profile'] = $encodingProfile;
            $audioCodecConfigName = $encodingProfile["codec"] . "_" . $encodingProfile["bitrate"];

            //DEFINE MUXING OUTPUT PATH
            $muxingOutputPath = $outputPath . 'audio/fmp4/' . $audioCodecConfigName . '/';
            //CREATE AUDIO CODEC CONFIGURATION
            $audioEncodingConfig['codec'] = createAACAudioCodecConfiguration($apiClient, $audioCodecConfigName, $encodingProfile["bitrate"]);
            // CREATE AUDIO STREAM
            $audioStream = new Stream($audioEncodingConfig['codec'], array($inputStreamAudio));
            $audioEncodingConfig['stream'] = $apiClient->encodings()->streams($encoding)->create($audioStream);
            //CREATE FMP4 MUXING
            $audioEncodingConfig['fmp4_muxing'] = createFmp4Muxing($apiClient, $encoding, $audioEncodingConfig['stream'], $output, $muxingOutputPath);
            $audioEncodingConfigs[] = $audioEncodingConfig;
        }
        $videoFiles[$key]['audioEncodingConfigs'] = $audioEncodingConfigs;

        $audioStreams = array();
        foreach ($audioEncodingConfigs as $audioEncodingConfig)
            $audioStreams[] = $audioEncodingConfig['stream'];

        // CREATE VIDEO CODEC CONFIGURATIONS
        foreach ($bitrateLadderEntries as $bitrateLadderEntry)
        {
            if ($bitrateLadderEntry["codec"] !== "h264")
                continue;

            $width = null;
            $height = null;
            $videoEncodingConfig = array();
            $videoEncodingConfig['profile'] = $bitrateLadderEntry;
            $codecConfigName = $bitrateLadderEntry["codec"] . "_" . $bitrateLadderEntry["bitrate"];

            if (key_exists("width", $bitrateLadderEntry))
            {
                $width = $bitrateLadderEntry["width"];
            }
            else if (key_exists("height", $bitrateLadderEntry))
            {
                $height = $bitrateLadderEntry["height"];
            }

            //DEFINE MUXING OUTPUT PATH
            $fmp4MuxingOutputPath = $outputPath . 'video/fmp4/' . $codecConfigName . '/';
            $mp4MuxingOutputPath = $outputPath . 'mp4/';
            $mp4MuxingFilename = $videoEncodingConfig['profile']['bitrate'] . '.mp4';

            $adjustmentFactor = generateBitrateAdjustmentFactorForMuxing($crfMuxing, $bitrateLadderEntry);

            //Created MP4 Muxing with adjusted Bitrate
            $adjustedBitrate = ((int)$bitrateLadderEntry['bitrate'] * $adjustmentFactor);
            //CREATE VIDEO CODEC CONFIGURATION
            $videoEncodingConfig['codec'] = createH264VideoCodecConfiguration($apiClient, $codecConfigName, $bitrateLadderEntry["profile"], $adjustedBitrate, $width, $height);
            //CREATE VIDEO STREAM
            $videoEncodingConfig['stream'] = createStream($apiClient, $encoding, $videoEncodingConfig['codec'], $inputStreamVideo);
            //CREATE MP4 MUXING
            $streams = array_merge(array($videoEncodingConfig['stream']), $audioStreams);
            $videoEncodingConfig['mp4_muxing'] = createMp4Muxing($apiClient, $encoding, $mp4MuxingFilename, $streams, $output, $mp4MuxingOutputPath);
            //CREATE FMP4 MUXING
            $videoEncodingConfig['fmp4_muxing'] = createFmp4Muxing($apiClient, $encoding, $videoEncodingConfig['stream'], $output, $fmp4MuxingOutputPath);
            $videoEncodingConfigs[] = $videoEncodingConfig;
        }

        // START THE ENCODING PROCESS
        $videoFiles[$key]['videoEncodingConfigs'] = $videoEncodingConfigs;

        $apiClient->encodings()->start($encoding);
        $videoFiles[$key]['encoding'] = $encoding;
    }

    //WAIT UNTIL ALL ENCODINGS ARE FINISHED
    $allFinished = false;
    do
    {
        $states = array();
        foreach ($videoFiles as $videoFile)
        {
            /** @var Encoding $currentEncoding */
            $currentEncoding = $videoFile['encoding'];
            $status = $apiClient->encodings()->status($currentEncoding);
            $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
            $states[] = $isRunning;
            $currentTimestamp = date_create(null, new DateTimeZone('UTC'))->getTimestamp();
            echo $currentTimestamp . ": " . $currentEncoding->getName() . " => " . $status->getStatus() . "\n";
        }
        $allFinished = !in_array(true, $states);

        if (!$allFinished)
            sleep(ENCODING_STATUS_REFRESH_RATE);

    } while (!$allFinished);

    //CREATE DASH MANIFEST
    $manifests = array();
    foreach ($videoFiles as $videoFile)
    {
        /** @var Encoding $encoding */
        $encoding = $videoFile['encoding'];
        $encodingId = $encoding->getId();
        $encodingStatus = $apiClient->encodings()->statusById($encodingId)->getStatus();

        $fmp4Muxings = array();
        $encodingConfigs = array_merge($videoFile['videoEncodingConfigs'], $videoFile['audioEncodingConfigs']);
        foreach ($encodingConfigs as $encodingConfig)
            $fmp4Muxings[] = $encodingConfig['fmp4_muxing'];

        $outputPath = $videoFile['outputPath'];
        $manifestName = $videoFile['encodingName'] . ".mpd";
        $dashManifest = createDashManifest($apiClient, $manifestName, $encoding, $fmp4Muxings, $output, $outputPath);

        //Start Manifest Creation
        $apiClient->manifests()->dash()->start($dashManifest);
        $manifests[] = $dashManifest;
    }

    $allFinished = false;
    do
    {
        $states = array();
        foreach ($manifests as $manifest)
        {
            $status = $apiClient->manifests()->dash()->status($manifest);
            $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
            $states[] = $isRunning;
            $currentTimestamp = date_create(null, new DateTimeZone('UTC'))->getTimestamp();
            echo $currentTimestamp . ": " . $manifest->getName() . " => " . $status->getStatus() . "\n";
        }
        $allFinished = !in_array(true, $states);
        if (!$allFinished)
            sleep(4);

    } while (!$allFinished);
    var_dump("Manifest finished");
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
 * @param ApiClient $apiClient
 * @param string    $manifestName
 * @param Encoding  $encoding
 * @param array     $fmp4Muxings
 * @param Output    $output
 * @param string    $outputPath
 * @return DashManifest
 * @throws BitmovinException
 */
function createDashManifest(ApiClient $apiClient, $manifestName, Encoding $encoding, array $fmp4Muxings, Output $output, $outputPath)
{
    $manifestOutput = new EncodingOutput($output);
    $manifestOutput->setOutputPath($outputPath);
    $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
    $manifestOutput->setAcl([$acl]);
    $manifestType = DashMuxingType::TYPE_TEMPLATE;
    $manifest = new DashManifest();
    $manifest->setOutputs(array($manifestOutput));
    $manifest->setManifestName($manifestName);
    $dashManifest = $apiClient->manifests()->dash()->create($manifest);

    //Add Period
    $period = new Period();
    $manifestPeriod = $apiClient->manifests()->dash()->createPeriod($dashManifest, $period);

    $dashAdaptionSet = null;
    $videoAdaptionSet = null;
    $audioAdaptionSet = null;

    foreach ($fmp4Muxings as $fmp4Muxing)
    {
        if (!($fmp4Muxing instanceof FMP4Muxing))
            continue;

        $streamDetails = $apiClient->encodings()->streams($encoding)->getById($fmp4Muxing->getStreams()[0]->getStreamId());
        $codecConfigType = $apiClient->codecConfigurations()->type()->getTypeById($streamDetails->getCodecConfigId());

        $isVideoMuxing = in_array($codecConfigType->getType(), array(CodecConfigType::H264, CodecConfigType::H265));
        $isAudioMuxing = in_array($codecConfigType->getType(), array(CodecConfigType::AAC));

        $segmentPath = $fmp4Muxing->getOutputs()[0]->getOutputPath();
        $substr = substr($segmentPath, 0, strlen($outputPath));
        if ($substr === $outputPath)
        {
            $segmentPath = substr($segmentPath, strlen($outputPath));
        }

        //ADD Representation to Period
        $representation = new DashRepresentation();
        $representation->setType($manifestType);
        $representation->setEncodingId($encoding->getId());
        $representation->setMuxingId($fmp4Muxing->getId());
        $representation->setSegmentPath($segmentPath);

        if ($isVideoMuxing && is_null($videoAdaptionSet))
        {
            $videoAdaptionSet = new VideoAdaptationSet();
            $videoAdaptionSet = $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($dashManifest, $manifestPeriod, $videoAdaptionSet);
        }
        if ($isVideoMuxing)
            $apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $videoAdaptionSet, $representation);

        if ($isAudioMuxing && is_null($audioAdaptionSet))
        {
            $audioAdaptionSet = new AudioAdaptationSet();
            $audioAdaptionSet->setLang("en");
            $audioAdaptionSet = $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($dashManifest, $manifestPeriod, $audioAdaptionSet);
        }
        if ($isAudioMuxing)
            $apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $manifestPeriod, $audioAdaptionSet, $representation);
    }

    return $dashManifest;
}

/**
 * @param FMP4Muxing $muxing
 * @param            $encodingProfile
 * @return float|int
 */
function generateBitrateAdjustmentFactorForMuxing(FMP4Muxing $muxing, $encodingProfile)
{
    $adjustmentFactor = 1.0;
    $avgBitrate = $muxing->getAvgBitrate();
    $complexityImpact = 1;

    if (is_null($avgBitrate))
    {
        var_dump("Adjustment not possible!");
        return $adjustmentFactor;
    }

    $contentComplexity = (float)$avgBitrate / COMPLEXITY_MEDIAN_VALUE;
    if ($contentComplexity < 1.0)
    {
        $complexityImpact = $encodingProfile['lowComplexityImpact'];
        $adjustmentFactor = 1 - (1 - $contentComplexity) * $complexityImpact;
    }
    else
    {
        $complexityImpact = $encodingProfile['highComplexityImpact'];
        $adjustmentFactor = ($contentComplexity - 1) * $complexityImpact + 1;
    }
    $adjustmentFactor = min($adjustmentFactor, BITRATE_ADJUSTMENT_UPPER_BOUNDARY);
    $adjustmentFactor = max($adjustmentFactor, BITRATE_ADJUSTMENT_LOWER_BOUNDARY);

    return $adjustmentFactor;
}

/**
 * @param ApiClient $apiClient
 * @param string    $encodingName
 * @param string    $encodingRegion CloudRegion(ENUM)
 * @param Input     $input
 * @param string    $inputPath
 * @param Output    $output
 * @param string    $outputPath
 * @return Encoding
 * @throws BitmovinException
 */
function runFastCrfEncoding(ApiClient $apiClient, $encodingName = "Fast Complexity Factor Encoding", $encodingRegion, Input $input, $inputPath, Output $output, $outputPath)
{
    // CREATE CRF ENCODING
    $crfEncoding = new Encoding($encodingName);
    $crfEncoding->setCloudRegion($encodingRegion);
    $crfEncoding = $apiClient->encodings()->create($crfEncoding);

    //CREATE VIDEO/AUDIO INPUT STREAMS
    $inputStreamVideo = new InputStream($input, $inputPath, SelectionMode::AUTO);

    //CREATE CRF VIDEO CODEC CONFIGURATION
    $codecConfigVideo = new H264VideoCodecConfiguration("CRF 23", H264Profile::MAIN, null, null);
    $codecConfigVideo->setCrf(23);
    $codecConfigVideo->setWidth(640);
    $codecConfigVideo = $apiClient->codecConfigurations()->videoH264()->create($codecConfigVideo);

    $videoStream = new Stream($codecConfigVideo, array($inputStreamVideo));
    $videoStream = $apiClient->encodings()->streams($crfEncoding)->create($videoStream);

    $muxingOutputPath = $outputPath . 'video-crf-fmp4/';
    $muxingStream = new MuxingStream();
    $muxingStream->setStreamId($videoStream->getId());
    $encodingOutputs = null;

    //Output destination of the CRF encoding
    $encodingOutput = new EncodingOutput($output);
    $encodingOutput->setOutputPath($muxingOutputPath);
    $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
    $encodingOutput->setAcl([$acl]);
    $encodingOutputs[] = $encodingOutput;

    //CREATE CRF FMP4 MUXING
    $fmp4Muxing = new FMP4Muxing();
    $fmp4Muxing->setInitSegmentName('init.mp4');
    $fmp4Muxing->setSegmentLength(30);
    $fmp4Muxing->setSegmentNaming('segment_%number%.m4s');
    $fmp4Muxing->setOutputs($encodingOutputs);
    $fmp4Muxing->setStreams(array($muxingStream));
    $apiClient->encodings()->muxings($crfEncoding)->fmp4Muxing()->create($fmp4Muxing);

    //START CRF ENCODING
    $apiClient->encodings()->start($crfEncoding);
    return $crfEncoding;
}

/**
 * @param ApiClient                   $apiClient
 * @param Encoding                    $encoding
 * @param H264VideoCodecConfiguration $codecConfiguration
 * @param InputStream                 $inputStream
 * @return Stream
 * @throws BitmovinException
 */
function createStream(ApiClient $apiClient, Encoding $encoding, H264VideoCodecConfiguration $codecConfiguration, InputStream $inputStream)
{
    $videoStream = new Stream($codecConfiguration, array($inputStream));

    if (is_null($codecConfiguration->getWidth()))
    {
        $videoStream->setConditions(new Condition(ConditionAttribute::HEIGHT, ConditionOperator::GREATER_THAN_OR_EQUAL_TO, $codecConfiguration->getHeight()));
    }
    else
    {
        $videoStream->setConditions(new Condition(ConditionAttribute::WIDTH, ConditionOperator::GREATER_THAN_OR_EQUAL_TO, $codecConfiguration->getWidth()));
    }

    return $apiClient->encodings()->streams($encoding)->create($videoStream);
}

/**
 * @param ApiClient $apiClient
 * @param Encoding  $encoding
 * @param string    $filename
 * @param Stream[]  $streams
 * @param Output    $output
 * @param           $outputPath
 * @param string    $outputAcl
 * @return MP4Muxing
 * @throws BitmovinException
 */
function createMp4Muxing(ApiClient $apiClient, Encoding $encoding, $filename, array $streams, Output $output, $outputPath, $outputAcl = AclPermission::ACL_PUBLIC_READ)
{
    $muxingStreams = array();
    $encodingOutputs = null;

    foreach ($streams as $stream)
    {
        $muxingStream = new MuxingStream();
        $muxingStream->setStreamId($stream->getId());
        $muxingStreams[] = $muxingStream;
    }

    if (!is_null($output))
    {
        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl(array(new Acl($outputAcl)));
        $encodingOutputs[] = $encodingOutput;
    }

    $muxing = new MP4Muxing();
    $muxing->setFilename($filename);
    $muxing->setOutputs($encodingOutputs);
    $muxing->setStreams($muxingStreams);

    return $apiClient->encodings()->muxings($encoding)->mp4Muxing()->create($muxing);
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

    if (!is_null($output))
    {
        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl(array(new Acl($outputAcl)));
        $encodingOutputs[] = $encodingOutput;
    }

    $fmp4Muxing = new FMP4Muxing();
    $fmp4Muxing->setInitSegmentName($initSegmentName);
    $fmp4Muxing->setSegmentLength($segmentDuration);
    $fmp4Muxing->setSegmentNaming($segmentNaming);
    $fmp4Muxing->setOutputs($encodingOutputs);
    $fmp4Muxing->setStreams(array($muxingStream));

    return $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing);
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
 * @param Output $output
 * @param string $outputPath
 * @param string $acl
 * @return EncodingOutput
 */
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
    try
    {
        $codecConfigVideo = new H264VideoCodecConfiguration($name, $profile, $bitrate, $rate);
        $codecConfigVideo->setDescription($bitrate . '_' . $name);
        $codecConfigVideo->setWidth($width);
        $codecConfigVideo->setHeight($height);
        return $apiClient->codecConfigurations()->videoH264()->create($codecConfigVideo);
    }
    catch (BitmovinException $e)
    {
        var_dump($e->getCode(), $e->getMessage(), $e->getDeveloperMessage());
        throw $e;
    }
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