<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\outputs\GcsOutput;

require_once __DIR__ . '/../vendor/autoload.php';

/*** IMPORTANT: first run CreateLowLevelLiveStream.php to get the ids ***/

// INPUT INFORMATION FROM LIVE STREAM
$encoding_id = 'COPY AND PASTE';
$fmp4_muxing_audio_id = 'COPY AND PASTE';
$fmp4_muxing_1080p_id = 'COPY AND PASTE';
$fmp4_muxing_720p_id = 'COPY AND PASTE';
$fmp4_muxing_480p_id = 'COPY AND PASTE';
$fmp4_muxing_360p_id = 'COPY AND PASTE';
$fmp4_muxing_240p_id = 'COPY AND PASTE';
$ts_stream_audio_id = 'COPY AND PASTE';
$ts_muxing_audio_id = 'COPY AND PASTE';
$ts_stream_1080p_id = 'COPY AND PASTE';
$ts_muxing_1080p_id = 'COPY AND PASTE';
$ts_stream_720p_id = 'COPY AND PASTE';
$ts_muxing_720p_id = 'COPY AND PASTE';
$ts_stream_480p_id = 'COPY AND PASTE';
$ts_muxing_480p_id = 'COPY AND PASTE';
$ts_stream_360_id = 'COPY AND PASTE';
$ts_muxing_360_id = 'COPY AND PASTE';
$ts_stream_240_id = 'COPY AND PASTE';
$ts_muxing_240_id = 'COPY AND PASTE';

// Set the start segment number. If it is set to null the first segment is taken as start segment
$startSegment = null;
// Set the end segment number. If it is set to null the last segment is taken as end segment
$endSegment = null;

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR API KEY');

// CONFIGURATION
$gcs_accessKey = 'INSERT YOUR GCS OUTPUT ACCESS KEY HERE';
$gcs_secretKey = 'INSERT YOUR GCS OUTPUT SECRET KEY HERE';
$gcs_bucketName = 'INSERT YOUR GCS OUTPUT BUCKET NAME HERE';
$gcs_prefix = 'path/to/your/output/destination/';

// CREATE OUTPUT
$output = new GcsOutput($gcs_bucketName, $gcs_accessKey, $gcs_secretKey);
$output = $apiClient->outputs()->create($output);

// CREATE DASH MANIFEST
$manifestOutput = new EncodingOutput($output);
$manifestOutput->setOutputPath($gcs_prefix);
$manifestOutput->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));

$dashManifest = new DashManifest();
$dashManifest->setName("stream_vod.mpd");
$dashManifest->setManifestName("stream_vod.mpd");
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
$audioRepresentation->setEncodingId($encoding_id);
$audioRepresentation->setMuxingId($fmp4_muxing_audio_id);
$audioRepresentation->setSegmentPath('audio/128kbps_dash');
$audioRepresentation->setStartSegmentNumber($startSegment);
$audioRepresentation->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $audioAdaptationSet, $audioRepresentation);

$videoRepresentation_1080p = new DashRepresentation();
$videoRepresentation_1080p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_1080p->setEncodingId($encoding_id);
$videoRepresentation_1080p->setMuxingId($fmp4_muxing_1080p_id);
$videoRepresentation_1080p->setSegmentPath('video/1080p_dash');
$videoRepresentation_1080p->setStartSegmentNumber($startSegment);
$videoRepresentation_1080p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_1080p);

$videoRepresentation_720p = new DashRepresentation();
$videoRepresentation_720p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_720p->setEncodingId($encoding_id);
$videoRepresentation_720p->setMuxingId($fmp4_muxing_720p_id);
$videoRepresentation_720p->setSegmentPath('video/720p_dash');
$videoRepresentation_720p->setStartSegmentNumber($startSegment);
$videoRepresentation_720p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_720p);

$videoRepresentation_480p = new DashRepresentation();
$videoRepresentation_480p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_480p->setEncodingId($encoding_id);
$videoRepresentation_480p->setMuxingId($fmp4_muxing_480p_id);
$videoRepresentation_480p->setSegmentPath('video/480p_dash');
$videoRepresentation_480p->setStartSegmentNumber($startSegment);
$videoRepresentation_480p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_480p);

$videoRepresentation_360p = new DashRepresentation();
$videoRepresentation_360p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_360p->setEncodingId($encoding_id);
$videoRepresentation_360p->setMuxingId($fmp4_muxing_360p_id);
$videoRepresentation_360p->setSegmentPath('video/360p_dash');
$videoRepresentation_360p->setStartSegmentNumber($startSegment);
$videoRepresentation_360p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_360p);

$videoRepresentation_240p = new DashRepresentation();
$videoRepresentation_240p->setType(DashMuxingType::TYPE_TEMPLATE);
$videoRepresentation_240p->setEncodingId($encoding_id);
$videoRepresentation_240p->setMuxingId($fmp4_muxing_240p_id);
$videoRepresentation_240p->setSegmentPath('video/240p_dash');
$videoRepresentation_240p->setStartSegmentNumber($startSegment);
$videoRepresentation_240p->setEndSegmentNumber($endSegment);
$apiClient->manifests()->dash()->addRepresentationToAdaptationSet($dashManifest, $period, $videoAdaptationSet, $videoRepresentation_240p);


// CREATE HLS MANIFEST
$hlsManifest = new HlsManifest();
$hlsManifest->setName('stream_vod.m3u8');
$hlsManifest->setManifestName('stream_vod.m3u8');
$hlsManifest->setOutputs(array($manifestOutput));
$hlsManifest = $apiClient->manifests()->hls()->create($hlsManifest);

$mediaInfo = new MediaInfo();
$mediaInfo->setGroupId('audio');
$mediaInfo->setName('English');
$mediaInfo->setUri('audio_vod.m3u8');
$mediaInfo->setType(MediaInfoType::AUDIO);
$mediaInfo->setSegmentPath('audio/128kbps_hls/');
$mediaInfo->setMuxingId($ts_muxing_audio_id);
$mediaInfo->setStreamId($ts_stream_audio_id);
$mediaInfo->setEncodingId($encoding_id);
$mediaInfo->setLanguage('en');
$mediaInfo->setAssocLanguage('en');
$mediaInfo->setAutoselect(false);
$mediaInfo->setDefault(false);
$mediaInfo->setForced(false);
$mediaInfo->setStartSegmentNumber($startSegment);
$mediaInfo->setEndSegmentNumber($endSegment);
$mediaInfo = $apiClient->manifests()->hls()->createMediaInfo($hlsManifest, $mediaInfo);

$streamInfo_240p = new StreamInfo();
$streamInfo_240p->setUri('video_240p_vod.m3u8');
$streamInfo_240p->setEncodingId($encoding_id);
$streamInfo_240p->setStreamId($ts_stream_240_id);
$streamInfo_240p->setMuxingId($ts_muxing_240_id);
$streamInfo_240p->setAudio('audio');
$streamInfo_240p->setSegmentPath('video/240p_hls');
$streamInfo_240p->setStartSegmentNumber($startSegment);
$streamInfo_240p->setEndSegmentNumber($endSegment);
$streamInfo_240p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_240p);

$streamInfo_360p = new StreamInfo();
$streamInfo_360p->setUri('video_360p_vod.m3u8');
$streamInfo_360p->setEncodingId($encoding_id);
$streamInfo_360p->setStreamId($ts_stream_360_id);
$streamInfo_360p->setMuxingId($ts_muxing_360_id);
$streamInfo_360p->setAudio('audio');
$streamInfo_360p->setSegmentPath('video/360p_hls');
$streamInfo_360p->setStartSegmentNumber($startSegment);
$streamInfo_360p->setEndSegmentNumber($endSegment);
$streamInfo_360p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_360p);

$streamInfo_480p = new StreamInfo();
$streamInfo_480p->setUri('video_480p_vod.m3u8');
$streamInfo_480p->setEncodingId($encoding_id);
$streamInfo_480p->setStreamId($ts_stream_480p_id);
$streamInfo_480p->setMuxingId($ts_muxing_480p_id);
$streamInfo_480p->setAudio('audio');
$streamInfo_480p->setSegmentPath('video/480p_hls');
$streamInfo_480p->setStartSegmentNumber($startSegment);
$streamInfo_480p->setEndSegmentNumber($endSegment);
$streamInfo_480p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_480p);

$streamInfo_720p = new StreamInfo();
$streamInfo_720p->setUri('video_720p_vod.m3u8');
$streamInfo_720p->setEncodingId($encoding_id);
$streamInfo_720p->setStreamId($ts_stream_720p_id);
$streamInfo_720p->setMuxingId($ts_muxing_720p_id);
$streamInfo_720p->setAudio('audio');
$streamInfo_720p->setSegmentPath('video/720p_hls');
$streamInfo_720p->setStartSegmentNumber($startSegment);
$streamInfo_720p->setEndSegmentNumber($endSegment);
$streamInfo_720p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_720p);

$streamInfo_1080p = new StreamInfo();
$streamInfo_1080p->setUri('video_1080p_vod.m3u8');
$streamInfo_1080p->setEncodingId($encoding_id);
$streamInfo_1080p->setStreamId($ts_stream_1080p_id);
$streamInfo_1080p->setMuxingId($ts_muxing_1080p_id);
$streamInfo_1080p->setAudio('audio');
$streamInfo_1080p->setSegmentPath('video/1080p_hls');
$streamInfo_1080p->setStartSegmentNumber($startSegment);
$streamInfo_1080p->setEndSegmentNumber($endSegment);
$streamInfo_1080p = $apiClient->manifests()->hls()->createStreamInfo($hlsManifest, $streamInfo_1080p);

$apiClient->manifests()->dash()->start($dashManifest);
$apiClient->manifests()->hls()->start($hlsManifest);