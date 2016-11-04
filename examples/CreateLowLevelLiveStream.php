<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\Status;
use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\RtmpInput;
use Bitmovin\api\model\outputs\GcsOutput;

require_once __DIR__ . '/vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR API KEY');

// CONFIGURATION
$config = array();
$config['accessKey']  = 'YOUR GCS ACCESS KEY';
$config['secretKey']  = 'YOUR GCS SECRET KEY';
$config['bucketName'] = 'YOUR GCS BUCKET NAME';
$config['prefix']     = 'path/to/your/output/destination/';
$config['streamKey']  = 'YOUR STREAM KEY';

// CREATE ENCODING
$encoding = new Encoding('LIVE-ENCODING-DEMO');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding->setDescription('LIVE-ENCODING-DEMO');
$encoding = $apiClient->encodings()->create($encoding);

// CREATE RTMP INPUT
$input = new RtmpInput();
$input = $apiClient->inputs()->rtmp()->create($input);

// CREATE OUTPUT
$output = new GcsOutput($config['bucketName'], $config['accessKey'], $config['secretKey']);
$output = $apiClient->outputs()->create($output);

// CREATE VIDEO STREAM FOR 1080p
$videoConfig1080p = new H264VideoCodecConfiguration('StreamDemo1080p', H264Profile::HIGH, 4800000, 60.0);
$videoConfig1080p->setDescription('StreamDemo1080p');
$videoConfig1080p->setWidth(1920);
$videoConfig1080p->setHeight(1080);
$videoConfig1080p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig1080p);
$inputStream1080p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream1080p->setPosition(0);
$stream1080p = new Stream($videoConfig1080p, array($inputStream1080p));
$stream1080p = $apiClient->encodings()->streams($encoding)->create($stream1080p);

// CREATE MUXING FOR 1080p
$encodingOutput1080p = new EncodingOutput($output);
$encodingOutput1080p->setOutputPath($config['prefix'] . 'video/1080p');
$encodingOutput1080p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream1080p = new MuxingStream();
$muxingStream1080p->setStreamId($stream1080p->getId());
$fmp4Muxing1080p = new FMP4Muxing();
$fmp4Muxing1080p->setInitSegmentName('init.mp4');
$fmp4Muxing1080p->setSegmentLength(4);
$fmp4Muxing1080p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing1080p->setOutputs(array($encodingOutput1080p));
$fmp4Muxing1080p->setStreams(array($muxingStream1080p));
$fmp4Muxing1080p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing1080p);

// CREATE VIDEO STREAM FOR 720p
$videoConfig720p = new H264VideoCodecConfiguration('StreamDemo720p', H264Profile::HIGH, 2400000, 60.0);
$videoConfig720p->setDescription('StreamDemo720p');
$videoConfig720p->setWidth(1280);
$videoConfig720p->setHeight(720);
$videoConfig720p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig720p);
$inputStream720p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream720p->setPosition(0);
$stream720p = new Stream($videoConfig720p, array($inputStream720p));
$stream720p = $apiClient->encodings()->streams($encoding)->create($stream720p);

// CREATE MUXING FOR 720p
$encodingOutput720p = new EncodingOutput($output);
$encodingOutput720p->setOutputPath($config['prefix'] . 'video/720p');
$encodingOutput720p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream720p = new MuxingStream();
$muxingStream720p->setStreamId($stream720p->getId());
$fmp4Muxing720p = new FMP4Muxing();
$fmp4Muxing720p->setInitSegmentName('init.mp4');
$fmp4Muxing720p->setSegmentLength(4);
$fmp4Muxing720p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing720p->setOutputs(array($encodingOutput720p));
$fmp4Muxing720p->setStreams(array($muxingStream720p));
$fmp4Muxing720p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing720p);

// CREATE VIDEO STREAM FOR 480p
$videoConfig480p = new H264VideoCodecConfiguration('StreamDemo480p', H264Profile::HIGH, 1200000, 60.0);
$videoConfig480p->setDescription('StreamDemo480p');
$videoConfig480p->setWidth(858);
$videoConfig480p->setHeight(480);
$videoConfig480p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig480p);
$inputStream480p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream480p->setPosition(0);
$stream480p = new Stream($videoConfig480p, array($inputStream480p));
$stream480p = $apiClient->encodings()->streams($encoding)->create($stream480p);

// CREATE MUXING FOR 480p
$encodingOutput480p = new EncodingOutput($output);
$encodingOutput480p->setOutputPath($config['prefix'] . 'video/480p');
$encodingOutput480p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream480p = new MuxingStream();
$muxingStream480p->setStreamId($stream480p->getId());
$fmp4Muxing480p = new FMP4Muxing();
$fmp4Muxing480p->setInitSegmentName('init.mp4');
$fmp4Muxing480p->setSegmentLength(4);
$fmp4Muxing480p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing480p->setOutputs(array($encodingOutput480p));
$fmp4Muxing480p->setStreams(array($muxingStream480p));
$fmp4Muxing480p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing480p);

// CREATE VIDEO STREAM FOR 360p
$videoConfig360p = new H264VideoCodecConfiguration('StreamDemo360p', H264Profile::HIGH, 800000, 60.0);
$videoConfig360p->setDescription('StreamDemo360p');
$videoConfig360p->setWidth(640);
$videoConfig360p->setHeight(360);
$videoConfig360p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig360p);
$inputStream360p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream360p->setPosition(0);
$stream360p = new Stream($videoConfig360p, array($inputStream360p));
$stream360p = $apiClient->encodings()->streams($encoding)->create($stream360p);

// CREATE MUXING FOR 360p
$encodingOutput360p = new EncodingOutput($output);
$encodingOutput360p->setOutputPath($config['prefix'] . 'video/360p');
$encodingOutput360p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream360p = new MuxingStream();
$muxingStream360p->setStreamId($stream360p->getId());
$fmp4Muxing360p = new FMP4Muxing();
$fmp4Muxing360p->setInitSegmentName('init.mp4');
$fmp4Muxing360p->setSegmentLength(4);
$fmp4Muxing360p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing360p->setOutputs(array($encodingOutput360p));
$fmp4Muxing360p->setStreams(array($muxingStream360p));
$fmp4Muxing360p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing360p);

// CREATE VIDEO STREAM FOR 240p
$videoConfig240p = new H264VideoCodecConfiguration('StreamDemo240p', H264Profile::HIGH, 400000, 60.0);
$videoConfig240p->setDescription('StreamDemo240p');
$videoConfig240p->setWidth(426);
$videoConfig240p->setHeight(240);
$videoConfig240p = $apiClient->codecConfigurations()->videoH264()->create($videoConfig240p);
$inputStream240p = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStream240p->setPosition(0);
$stream240p = new Stream($videoConfig240p, array($inputStream240p));
$stream240p = $apiClient->encodings()->streams($encoding)->create($stream240p);

// CREATE MUXING FOR 240p
$encodingOutput240p = new EncodingOutput($output);
$encodingOutput240p->setOutputPath($config['prefix'] . 'video/240p');
$encodingOutput240p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream240p = new MuxingStream();
$muxingStream240p->setStreamId($stream240p->getId());
$fmp4Muxing240p = new FMP4Muxing();
$fmp4Muxing240p->setInitSegmentName('init.mp4');
$fmp4Muxing240p->setSegmentLength(4);
$fmp4Muxing240p->setSegmentNaming('segment_%number%.m4s');
$fmp4Muxing240p->setOutputs(array($encodingOutput240p));
$fmp4Muxing240p->setStreams(array($muxingStream240p));
$fmp4Muxing240p = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4Muxing240p);

// CREATE AUDIO STREAM
$audioConfig48000 = new AACAudioCodecConfiguration('StreamDemoAAC48000', 128000, 48000);
$audioConfig48000->setDescription('StreamDemoAAC48000');
$audioConfig48000 = $apiClient->codecConfigurations()->audioAAC()->create($audioConfig48000);
$inputStreamAAC48000 = new InputStream($input, 'live', SelectionMode::AUTO);
$inputStreamAAC48000->setPosition(0);
$streamAAC48000 = new Stream($audioConfig48000, array($inputStreamAAC48000));
$streamAAC48000 = $apiClient->encodings()->streams($encoding)->create($streamAAC48000);

// CREATE MUXING FOR AUDIO
$encodingOutputAAC48000 = new EncodingOutput($output);
$encodingOutputAAC48000->setOutputPath($config['prefix'] . 'audio/128kbps');
$encodingOutputAAC48000->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStreamAAC48000 = new MuxingStream();
$muxingStreamAAC48000->setStreamId($streamAAC48000->getId());
$fmp4MuxingAAC48000 = new FMP4Muxing();
$fmp4MuxingAAC48000->setInitSegmentName('init.mp4');
$fmp4MuxingAAC48000->setSegmentLength(4);
$fmp4MuxingAAC48000->setSegmentNaming('segment_%number%.m4s');
$fmp4MuxingAAC48000->setOutputs(array($encodingOutputAAC48000));
$fmp4MuxingAAC48000->setStreams(array($muxingStreamAAC48000));
$fmp4MuxingAAC48000 = $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($fmp4MuxingAAC48000);

// START LIVE STREAM
$apiClient->encodings()->startLivestream($encoding, $config['streamKey']);

// WAIT UNTIL LIVE STREAM IS RUNNING
$status = Status::ERROR;
while (true)
{
    $status = $apiClient->encodings()->status($encoding)->getStatus();
    if ($status == Status::ERROR || $status == Status::RUNNING)
    {
        break;
    }
    sleep(1);
}

// WAIT UNTIL LIVE STREAM DATA ARE AVAILABLE
$liveEncodingDetails = null;
while (true)
{
    try
    {
        $liveEncodingDetails = $apiClient->encodings()->getLivestreamDetails($encoding);
        break;
    }
    catch(BitmovinException $exception)
    {
        if ($exception->getCode() != 400)
        {
            print 'Got unexpected exception with code ' . strval($exception->getCode()) . ': ' . $exception->getMessage();
            throw $exception;
        }
    }
    sleep(1);
}
print 'Live stream ' . $liveEncodingDetails->getStreamKey() . ' is running with IP ' . $liveEncodingDetails->getEncoderIp();

// STOP LIVE STREAM
if ($status == Status::RUNNING)
    $apiClient->encodings()->stopLivestream($encoding);
