<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\model\encodings\AutoRepresentation;
use Bitmovin\api\model\encodings\pertitle\H264PerTitleConfiguration;
use Bitmovin\api\model\encodings\pertitle\PerTitle;
use Bitmovin\api\model\encodings\StartEncodingRequest;
use Bitmovin\api\model\encodings\EncodingMode;
use Bitmovin\api\model\encodings\streams\StreamMode;


require_once __DIR__ . '/../vendor/autoload.php';

$bitmovinApiKey = '<INSERT YOUR API KEY>';

$encodingName = 'PHP Example - Per Title';
$encodingRegion = CloudRegion::AUTO;
$encoderVersion = '<INSERT ENCODER VERSION>';

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
    $videoStream = createPerTitleVideoStream($apiClient, $encoding, $videoInputStream);

    createMp4Muxing($apiClient, $encoding, $s3Output, $videoStream, $audioStream);
    startEncoding($apiClient, $encoding);

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
function createPerTitleVideoStream($apiClient, $encoding, $videoInputStream)
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
 * @param Stream $videoStream The Per-Title template video stream
 * @param Stream $audioStream The audio stream
 * @throws BitmovinException
 */
function createMp4Muxing($apiClient, $encoding, $s3Output, $videoStream, $audioStream)
{
    $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
    $mp4MuxingOutput = new EncodingOutput($s3Output);
    $mp4MuxingOutput->setOutputPath(OUTPUT_BASE_PATH . "{width}_{bitrate}_{uuid}/");
    $mp4MuxingOutput->setAcl(array($acl));

    $videoMuxingStream = new MuxingStream();
    $videoMuxingStream->setStreamId($videoStream->getId());

    $audioMuxingStream = new MuxingStream();
    $audioMuxingStream->setStreamId($audioStream->getId());

    $mp4Muxing = new MP4Muxing();
    $mp4Muxing->setStreams(array($videoMuxingStream, $audioMuxingStream));
    $mp4Muxing->setFilename("per_title_mp4.mp4");
    $mp4Muxing->setOutputs(array($mp4MuxingOutput));

    $apiClient->encodings()->muxings($encoding)->mp4Muxing()->create($mp4Muxing);
}

/**
 * The encoding will be started with the per title object and the auto representations set. If the auto
 * representation is set, stream configurations will be automatically added to the Per-Title profile. In that case
 * at least one PER_TITLE_TEMPLATE stream configuration must be available. All other configurations will be
 * automatically chosen by the Per-Title algorithm. All relevant settings for streams and muxings will be taken from
 * the closest PER_TITLE_TEMPLATE stream defined. The closest stream will be chosen based on the resolution
 * specified in the codec configuration.
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
}
