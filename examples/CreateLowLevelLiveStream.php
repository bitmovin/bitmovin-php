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
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\outputs\GcsOutput;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\model\encodings\LiveDashManifest;
use Bitmovin\api\model\encodings\LiveHlsManifest;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR API KEY');

// CONFIGURATION
$gcs_accessKey = 'INSERT YOUR GCS OUTPUT ACCESS KEY HERE';
$gcs_secretKey = 'INSERT YOUR GCS OUTPUT SECRET KEY HERE';
$gcs_bucketName = 'INSERT YOUR GCS OUTPUT BUCKET NAME HERE';
$gcs_prefix = 'path/to/your/output/destination/';
$stream_key = 'INSERT YOUR STREAM KEY HERE';

// CREATE ENCODING
$encoding = new Encoding('PHP LIVE STREAM');
$encoding->setCloudRegion(CloudRegion::GOOGLE_EUROPE_WEST_1);
$encoding->setDescription('PHP LIVE STREAM');
$encoding = $apiClient->encodings()->create($encoding);

// GET RTMP INPUT
$input = $apiClient->inputs()->rtmp()->listPage()[0];

// CREATE OUTPUT
$output = new GcsOutput($gcs_bucketName, $gcs_accessKey, $gcs_secretKey);
$output = $apiClient->outputs()->create($output);

// CREATE VIDEO STREAM FOR 1080p
$videoConfig1080p = new H264VideoCodecConfiguration('StreamDemo1080p', H264Profile::HIGH, 4800000, 30.0);
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
$encodingOutput1080p->setOutputPath($gcs_prefix . 'video/1080p_dash');
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

// CREATE MUXING FOR 1080p HLS
$encodingOutput1080p = new EncodingOutput($output);
$encodingOutput1080p->setOutputPath($gcs_prefix . 'video/1080p_hls');
$encodingOutput1080p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream1080p = new MuxingStream();
$muxingStream1080p->setStreamId($stream1080p->getId());
$tsMuxing1080p = new TSMuxing();
$tsMuxing1080p->setSegmentLength(4);
$tsMuxing1080p->setSegmentNaming('segment_%number%.ts');
$tsMuxing1080p->setOutputs(array($encodingOutput1080p));
$tsMuxing1080p->setStreams(array($muxingStream1080p));
$tsMuxing1080p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($tsMuxing1080p);

// CREATE VIDEO STREAM FOR 720p
$videoConfig720p = new H264VideoCodecConfiguration('StreamDemo720p', H264Profile::HIGH, 2400000, 30.0);
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
$encodingOutput720p->setOutputPath($gcs_prefix . 'video/720p_dash');
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

// CREATE MUXING FOR 720p HLS
$encodingOutput720p = new EncodingOutput($output);
$encodingOutput720p->setOutputPath($gcs_prefix . 'video/720p_hls');
$encodingOutput720p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream720p = new MuxingStream();
$muxingStream720p->setStreamId($stream720p->getId());
$tsMuxing720p = new TSMuxing();
$tsMuxing720p->setSegmentLength(4);
$tsMuxing720p->setSegmentNaming('segment_%number%.ts');
$tsMuxing720p->setOutputs(array($encodingOutput720p));
$tsMuxing720p->setStreams(array($muxingStream720p));
$tsMuxing720p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($tsMuxing720p);

// CREATE VIDEO STREAM FOR 480p
$videoConfig480p = new H264VideoCodecConfiguration('StreamDemo480p', H264Profile::HIGH, 1200000, 30.0);
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
$encodingOutput480p->setOutputPath($gcs_prefix . 'video/480p_dash');
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

// CREATE MUXING FOR 480p HLS
$encodingOutput480p = new EncodingOutput($output);
$encodingOutput480p->setOutputPath($gcs_prefix . 'video/480p_hls');
$encodingOutput480p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream480p = new MuxingStream();
$muxingStream480p->setStreamId($stream480p->getId());
$tsMuxing480p = new TSMuxing();
$tsMuxing480p->setSegmentLength(4);
$tsMuxing480p->setSegmentNaming('segment_%number%.ts');
$tsMuxing480p->setOutputs(array($encodingOutput480p));
$tsMuxing480p->setStreams(array($muxingStream480p));
$tsMuxing480p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($tsMuxing480p);

// CREATE VIDEO STREAM FOR 360p
$videoConfig360p = new H264VideoCodecConfiguration('StreamDemo360p', H264Profile::HIGH, 800000, 30.0);
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
$encodingOutput360p->setOutputPath($gcs_prefix . 'video/360p_dash');
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

// CREATE MUXING FOR 360p HLS
$encodingOutput360p = new EncodingOutput($output);
$encodingOutput360p->setOutputPath($gcs_prefix . 'video/360p_hls');
$encodingOutput360p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream360p = new MuxingStream();
$muxingStream360p->setStreamId($stream360p->getId());
$tsMuxing360p = new TSMuxing();
$tsMuxing360p->setSegmentLength(4);
$tsMuxing360p->setSegmentNaming('segment_%number%.ts');
$tsMuxing360p->setOutputs(array($encodingOutput360p));
$tsMuxing360p->setStreams(array($muxingStream360p));
$tsMuxing360p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($tsMuxing360p);

// CREATE VIDEO STREAM FOR 240p
$videoConfig240p = new H264VideoCodecConfiguration('StreamDemo240p', H264Profile::HIGH, 400000, 30.0);
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
$encodingOutput240p->setOutputPath($gcs_prefix . 'video/240p_dash');
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

// CREATE MUXING FOR 240p HLS

$encodingOutput240p = new EncodingOutput($output);
$encodingOutput240p->setOutputPath($gcs_prefix . 'video/240p_hls');
$encodingOutput240p->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStream240p = new MuxingStream();
$muxingStream240p->setStreamId($stream240p->getId());
$tsMuxing240p = new TSMuxing();
$tsMuxing240p->setSegmentLength(4);
$tsMuxing240p->setSegmentNaming('segment_%number%.ts');
$tsMuxing240p->setOutputs(array($encodingOutput240p));
$tsMuxing240p->setStreams(array($muxingStream240p));
$tsMuxing240p = $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($tsMuxing240p);

// CREATE AUDIO STREAM
$audioConfig48000 = new AACAudioCodecConfiguration('StreamDemoAAC48000', 128000, 48000);
$audioConfig48000->setDescription('StreamDemoAAC48000');
$audioConfig48000 = $apiClient->codecConfigurations()->audioAAC()->create($audioConfig48000);
$inputStreamAAC48000 = new InputStream($input, '/', SelectionMode::AUTO);
$inputStreamAAC48000->setPosition(1);
$streamAAC48000 = new Stream($audioConfig48000, array($inputStreamAAC48000));
$streamAAC48000 = $apiClient->encodings()->streams($encoding)->create($streamAAC48000);

// CREATE MUXING FOR AUDIO
$encodingOutputAAC48000 = new EncodingOutput($output);
$encodingOutputAAC48000->setOutputPath($gcs_prefix . 'audio/128kbps_dash');
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

// CREATE MUXING FOR AUDIO HLS

$encodingOutputAAC48000 = new EncodingOutput($output);
$encodingOutputAAC48000->setOutputPath($gcs_prefix . 'audio/128kbps_hls');
$encodingOutputAAC48000->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
$muxingStreamAAC48000 = new MuxingStream();
$muxingStreamAAC48000->setStreamId($streamAAC48000->getId());
$tsMuxingAAC48000 = new TSMuxing();
$tsMuxingAAC48000->setSegmentLength(4);
$tsMuxingAAC48000->setSegmentNaming('segment_%number%.ts');
$tsMuxingAAC48000->setOutputs(array($encodingOutputAAC48000));
$tsMuxingAAC48000->setStreams(array($muxingStreamAAC48000));
$tsMuxingAAC48000 = $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($tsMuxingAAC48000);

// CREATE DASH MANIFEST
$manifestOutput = new EncodingOutput($output);
$manifestOutput->setOutputPath($gcs_prefix);
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
$audioRepresentation->setMuxingId($fmp4MuxingAAC48000->getId());
$audioRepresentation->setSegmentPath('audio/128kbps_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $audioAdaptationSet, $audioRepresentation);

$videoRepresentation_1080p = new DashRepresentation();
$videoRepresentation_1080p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_1080p->setEncodingId($encoding->getId());
$videoRepresentation_1080p->setMuxingId($fmp4Muxing1080p->getId());
$videoRepresentation_1080p->setSegmentPath('video/1080p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_1080p);

$videoRepresentation_720p = new DashRepresentation();
$videoRepresentation_720p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_720p->setEncodingId($encoding->getId());
$videoRepresentation_720p->setMuxingId($fmp4Muxing720p->getId());
$videoRepresentation_720p->setSegmentPath('video/720p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_720p);

$videoRepresentation_480p = new DashRepresentation();
$videoRepresentation_480p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_480p->setEncodingId($encoding->getId());
$videoRepresentation_480p->setMuxingId($fmp4Muxing480p->getId());
$videoRepresentation_480p->setSegmentPath('video/480p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_480p);

$videoRepresentation_360p = new DashRepresentation();
$videoRepresentation_360p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_360p->setEncodingId($encoding->getId());
$videoRepresentation_360p->setMuxingId($fmp4Muxing360p->getId());
$videoRepresentation_360p->setSegmentPath('video/360p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_360p);

$videoRepresentation_240p = new DashRepresentation();
$videoRepresentation_240p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_240p->setEncodingId($encoding->getId());
$videoRepresentation_240p->setMuxingId($fmp4Muxing240p->getId());
$videoRepresentation_240p->setSegmentPath('video/240p_dash');
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_240p);


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
$mediaInfo->setSegmentPath('audio/128kbps_hls/');
$mediaInfo->setMuxingId($tsMuxingAAC48000->getId());
$mediaInfo->setStreamId($streamAAC48000->getId());
$mediaInfo->setEncodingId($encoding->getId());
$mediaInfo->setLanguage('en');
$mediaInfo->setAssocLanguage('en');
$mediaInfo->setAutoselect(false);
$mediaInfo->setDefault(false);
$mediaInfo->setForced(false);
$mediaInfo = $apiClient->manifests()->hls()->createMediaInfo($hlsManifest, $mediaInfo);

$streamInfo_240p = new StreamInfo();
$streamInfo_240p->setUri('video_240p.m3u8');
$streamInfo_240p->setEncodingId($encoding->getId());
$streamInfo_240p->setStreamId($stream240p->getId());
$streamInfo_240p->setMuxingId($tsMuxing240p->getId());
$streamInfo_240p->setAudio('audio');
$streamInfo_240p->setSegmentPath('video/240p_hls');
$streamInfo_240p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_240p);

$streamInfo_360p = new StreamInfo();
$streamInfo_360p->setUri('video_360p.m3u8');
$streamInfo_360p->setEncodingId($encoding->getId());
$streamInfo_360p->setStreamId($stream360p->getId());
$streamInfo_360p->setMuxingId($tsMuxing360p->getId());
$streamInfo_360p->setAudio('audio');
$streamInfo_360p->setSegmentPath('video/360p_hls');
$streamInfo_360p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_360p);

$streamInfo_480p = new StreamInfo();
$streamInfo_480p->setUri('video_480p.m3u8');
$streamInfo_480p->setEncodingId($encoding->getId());
$streamInfo_480p->setStreamId($stream480p->getId());
$streamInfo_480p->setMuxingId($tsMuxing480p->getId());
$streamInfo_480p->setAudio('audio');
$streamInfo_480p->setSegmentPath('video/480p_hls');
$streamInfo_480p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_480p);

$streamInfo_720p = new StreamInfo();
$streamInfo_720p->setUri('video_720p.m3u8');
$streamInfo_720p->setEncodingId($encoding->getId());
$streamInfo_720p->setStreamId($stream720p->getId());
$streamInfo_720p->setMuxingId($tsMuxing720p->getId());
$streamInfo_720p->setAudio('audio');
$streamInfo_720p->setSegmentPath('video/720p_hls');
$streamInfo_720p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_720p);

$streamInfo_1080p = new StreamInfo();
$streamInfo_1080p->setUri('video_1080p.m3u8');
$streamInfo_1080p->setEncodingId($encoding->getId());
$streamInfo_1080p->setStreamId($stream1080p->getId());
$streamInfo_1080p->setMuxingId($tsMuxing1080p->getId());
$streamInfo_1080p->setAudio('audio');
$streamInfo_1080p->setSegmentPath('video/1080p_hls');
$streamInfo_1080p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_1080p);


// START LIVE STREAM
$liveDashManifest = new LiveDashManifest();
$liveDashManifest->setManifestId($dashManifest->getId());
$liveDashManifest->setLiveEdgeOffset(60);

$liveHlsManifest = new LiveHlsManifest();
$liveHlsManifest->setManifestId($hlsManifest->getId());
$liveHlsManifest->setTimeshift(60);

$startLiveEncodingRequest = new \Bitmovin\api\model\encodings\StartLiveEncodingRequest();
$startLiveEncodingRequest->setStreamKey($stream_key);
$startLiveEncodingRequest->setDashManifests(array($liveDashManifest));
$startLiveEncodingRequest->setHlsManifests(array($liveHlsManifest));

$apiClient->encodings()->startLivestreamWithManifests($encoding, $startLiveEncodingRequest);

// WAIT UNTIL LIVE STREAM IS RUNNING
$status = '';
do
{
    sleep(1);
    $status = $apiClient->encodings()->status($encoding)->getStatus();
}
while ($status != Status::ERROR && $status != Status::RUNNING);

// WAIT UNTIL LIVE STREAM DATA ARE AVAILABLE
$liveEncodingDetails = null;
do
{
    try
    {
        $liveEncodingDetails = $apiClient->encodings()->getLivestreamDetails($encoding);
    }
    catch(BitmovinException $exception)
    {
        if ($exception->getCode() != 400)
        {
            print 'Got unexpected exception with code ' . strval($exception->getCode()) . ': ' . $exception->getMessage();
            throw $exception;
        }
        sleep(1);
    }
}
while ($liveEncodingDetails == null);

print 'RTMP Url: rtmp://' . $liveEncodingDetails->getEncoderIp() . '/live' . "\n";
print 'Stream-Key: ' . $liveEncodingDetails->getStreamKey() . "\n";
print 'Encoding-Id: ' . $encoding->getId() . "\n";
print 'Audio Stream Id: ' . $streamAAC48000->getId() . "\n";
print '1080p Stream Id: ' . $stream1080p->getId() . "\n";
print '720p Stream Id: ' . $stream720p->getId() . "\n";
print '480p Stream Id: ' . $stream480p->getId() . "\n";
print '360p Stream Id: ' . $stream360p->getId() . "\n";
print '240p Stream Id: ' . $stream240p->getId() . "\n";
print 'FMP4 audio Muxing Id: ' . $fmp4MuxingAAC48000->getId() . "\n";
print 'FMP4 1080p Muxing Id: ' . $fmp4Muxing1080p->getId() . "\n";
print 'FMP4 720p Muxing Id: ' . $fmp4Muxing720p->getId() . "\n";
print 'FMP4 480p Muxing Id: ' . $fmp4Muxing480p->getId() . "\n";
print 'FMP4 360p Muxing Id: ' . $fmp4Muxing360p->getId() . "\n";
print 'FMP4 240p Muxing Id: ' . $fmp4Muxing240p->getId() . "\n";
print 'TS audio Muxing Id: ' . $tsMuxingAAC48000->getId() . "\n";
print 'TS 1080p Muxing Id: ' . $tsMuxing1080p->getId() . "\n";
print 'TS 720p Muxing Id: ' . $tsMuxing720p->getId() . "\n";
print 'TS 480p Muxing Id: ' . $tsMuxing480p->getId() . "\n";
print 'TS 360p Muxing Id: ' . $tsMuxing360p->getId() . "\n";
print 'TS 240p Muxing Id: ' . $tsMuxing240p->getId() . "\n";