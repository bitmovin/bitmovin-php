<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\encodings\ConditionAttribute;
use Bitmovin\api\enum\encodings\ConditionOperator;
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
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

require_once __DIR__ . '/../vendor/autoload.php';

const BITRATE_ADJUSTMENT_UPPER_BOUNDARY = 1.5;
const BITRATE_ADJUSTMENT_LOWER_BOUNDARY = 0.5;
const COMPLEXITY_MEDIAN_VALUE = 500000;
const ENCODING_STATUS_REFRESH_RATE = 10;

/*$bitmovinApiKey = 'YOUR_BITMOVIN_API_KEY';
$uniqueId = time() . "-" . uniqid();
$encodingName = 'Per-Title-Encoding with MP4 Output #' . $uniqueId;

$inputS3AccessKey = 'YOUR_AWS_S3_ACCESS_KEY';
$inputS3SecretKey = 'YOUR_AWS_S3_SECRET_KEY';
$inputS3Bucketname = 'YOUR_AWS_S3_BUCKET_NAME';

$outputS3AccessKey = 'YOUR_AWS_S3_ACCESS_KEY';
$outputS3SecretKey = 'YOUR_AWS_S3_SECRET_KEY';
$outputS3Bucketname = 'YOUR_AWS_S3_BUCKET_NAME';

$videoFiles = array(
    array(
        'encodingName' => $uniqueId . ' ' . 'Encoding Name #1',
        'inputPath'    => 'path/to/your/input-file-1.mp4',
        'outputPath'   => 'path/to/your/encoding-output-destination/' . $uniqueId . '/'
    ),
    array(
        'encodingName' => $uniqueId . ' ' . 'Encoding Name #2',
        'inputPath'    => 'path/to/your/input-file-2.mp4',
        'outputPath'   => 'path/to/your/encoding-output-destination/' . $uniqueId . '/'
    )
);*/

$bitmovinApiKey = 'd38b9d5f-f370-4f65-b23e-df00bb28e62a';
$uniqueId = uniqid();
$encodingName = 'Per-Title-Encoding with MP4 #' . $uniqueId;

$inputS3AccessKey = 'AKIAJXJDN2Y4DC5V42OQ';
$inputS3SecretKey = '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';
$inputS3Bucketname = 'bitmovin-api-eu-west1-ci-input';

$outputS3AccessKey = 'AKIAIGBZ5CDU2P2UMGMA';
$outputS3SecretKey = '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';
$outputS3Bucketname = 'bitmovin-api-eu-west1-ci';

$videoFiles = array(
    array(
        'encodingName' => $uniqueId . ' ' . ' Caminandes',
        'inputPath'    => 'per-title-encoding-test-inputs/1080p_Caminandes_3.mp4',
        'outputPath'   => 'gzw-per-title-encoding-api-client-example/' . $uniqueId . '-caminandes/'
    ),
    array(
        'encodingName' => $uniqueId . ' ' . 'Sintel',
        'inputPath'    => 'per-title-encoding-test-inputs/Sintel.2010.1080p.mkv',
        'outputPath'   => 'gzw-per-title-encoding-api-client-example/' . $uniqueId . '-sintel-40-17/'
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
        $videoFiles[$key]['complexityFactorEncoding'] = runFastCrfEncoding($apiClient, $encodingName, $input, $inputPath, $output, $outputPath);
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
    $staticVideoEncodingConfigs = array();
    $perTitleVideoEncodingConfigs = array();
    $activeEncodings = array();
    foreach ($videoFiles as $videoFile)
    {
        /** @var Encoding $currentCrfEncoding */
        $currentCrfEncoding = $videoFile['complexityFactorEncoding'];
        $inputPath = $videoFile['inputPath'];
        $outputPath = $videoFile['outputPath'];
        $encodingName = "Per Title | " . $videoFile['encodingName'];
        $crfMuxing = $apiClient->encodings()->muxings($videoFile['complexityFactorEncoding'])->fmp4Muxing()->listPage()[0];

        // CREATE ENCODING
        $encoding = new Encoding($encodingName);
        $encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
        $encoding = $apiClient->encodings()->create($encoding);

        //CREATE VIDEO/AUDIO INPUT STREAMS
        $inputStreamVideo = new InputStream($input, $inputPath, SelectionMode::AUTO);

        // CREATE VIDEO CODEC CONFIGURATIONS
        foreach ($bitrateLadderEntries as $bitrateLadderEntry)
        {
            if ($bitrateLadderEntry["codec"] !== "h264")
                continue;

            $mp4MuxingOutputPath = $outputPath;
            $videoEncodingConfig = array();
            $codecConfigurationVideo = null;
            $codecConfigName = $bitrateLadderEntry["codec"] . "_codecconfig_" . $bitrateLadderEntry["bitrate"];
            $videoEncodingConfig['profile'] = $bitrateLadderEntry;
            $width = null;
            $height = null;

            if (key_exists("width", $bitrateLadderEntry))
            {
                $width = $bitrateLadderEntry["width"];
            }
            else if (key_exists("height", $bitrateLadderEntry))
            {
                $height = $bitrateLadderEntry["height"];
            }

            $encodingOutput = new EncodingOutput($output);
            $encodingOutput->setOutputPath($mp4MuxingOutputPath);
            $encodingOutput->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));

            $adjustmentFactor = generateBitrateAdjustmentFactorForMuxing($crfMuxing, $bitrateLadderEntry);

            //Created MP4 Muxing with adjusted Bitrate
            $adjustedBitrate = ((int)$bitrateLadderEntry['bitrate'] * $adjustmentFactor);
            $videoEncodingConfig['origin_brl'] = $bitrateLadderEntry;
            $videoEncodingConfig['codec'] = createH264VideoCodecConfiguration($apiClient, $codecConfigName, $bitrateLadderEntry["profile"], $adjustedBitrate, $width, $height);
            $videoEncodingConfig['stream'] = createStream($apiClient, $encoding, $videoEncodingConfig['codec'], $inputStreamVideo);
            $mp4MuxingFilename = $videoEncodingConfig['origin_brl']['bitrate'] . '.mp4';
            $videoEncodingConfig['mp4_muxing'] = createMp4Muxing($apiClient, $encoding, $mp4MuxingFilename, array($videoEncodingConfig['stream']), $encodingOutput);
            $perTitleVideoEncodingConfigs[$encoding->getId()][] = $videoEncodingConfig;
        }

        // START THE ENCODING PROCESS
        $apiClient->encodings()->start($encoding);
        $activeEncodings[] = $encoding;
    }

    //WAIT UNTIL ALL PER-TITLE ENCODINGS ARE FINISHED
    $allFinished = false;
    do
    {
        $states = array();
        foreach ($activeEncodings as $activeEncoding)
        {
            $status = $apiClient->encodings()->status($activeEncoding);
            $isRunning = !in_array($status->getStatus(), array(Status::ERROR, Status::FINISHED));
            $states[] = $isRunning;
            $currentTimestamp = date_create(null, new DateTimeZone('UTC'))->getTimestamp();
            echo $currentTimestamp . ": " . $activeEncoding->getName() . " => " . $status->getStatus() . "\n";

        }
        $allFinished = !in_array(true, $states);
        sleep(ENCODING_STATUS_REFRESH_RATE);
    } while (!$allFinished);
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
 * @param Input     $input
 * @param           $inputPath
 * @param Output    $output
 * @param           $outputPath
 * @return Encoding
 * @throws BitmovinException
 */
function runFastCrfEncoding(ApiClient $apiClient, $encodingName = "Fast Complexity Factor Encoding", Input $input, $inputPath, Output $output, $outputPath)
{
    // CREATE CRF ENCODING
    $crfEncoding = new Encoding($encodingName);
    $crfEncoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
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
 * @param ApiClient      $apiClient
 * @param Encoding       $encoding
 * @param string         $filename
 * @param Stream[]       $streams
 * @param EncodingOutput $encodingOutput
 * @return MP4Muxing
 * @throws BitmovinException
 */
function createMp4Muxing(ApiClient $apiClient, Encoding $encoding, $filename, array $streams, EncodingOutput $encodingOutput)
{
    $muxingStreams = array();

    foreach ($streams as $stream)
    {
        $muxingStream = new MuxingStream();
        $muxingStream->setStreamId($stream->getId());
        $muxingStreams[] = $muxingStream;
    }

    $muxing = new MP4Muxing();
    $muxing->setFilename($filename);
    $muxing->setOutputs(array($encodingOutput));
    $muxing->setStreams($muxingStreams);

    return $apiClient->encodings()->muxings($encoding)->mp4Muxing()->create($muxing);
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