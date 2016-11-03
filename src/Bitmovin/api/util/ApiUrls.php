<?php

namespace Bitmovin\api\util;

final class ApiUrls
{
    const PH_ENCODING_ID = "{encoding_id}";
    const PH_CONFIGURATION_ID = "{configuration_id}";
    const PH_STREAM_ID = "{stream_id}";
    const PH_MUXING_ID = "{muxing_id}";
    const PH_MANIFEST_ID = "{manifest_id}";
    const PH_PERIOD_ID = "{period_id}";
    const PH_ADAPTION_ID = "{adaption_id}";
    const PH_REPRESENTATION_ID = "{representation_id}";

    const ENCODINGS = "encoding/encodings";
    const ENCODING_GET = "encoding/encodings/{encoding_id}";
    const ENCODING_START = "encoding/encodings/{encoding_id}/start";
    const ENCODING_STOP = "encoding/encodings/{encoding_id}/stop";
    const ENCODING_DETAILS_LIVE = "encoding/encodings/{encoding_id}/live";
    const ENCODING_START_LIVE = "encoding/encodings/{encoding_id}/live/start";
    const ENCODING_STOP_LIVE = "encoding/encodings/{encoding_id}/live/stop";
    const ENCODING_STATUS = "encoding/encodings/{encoding_id}/status";

    const ENCODING_STREAMS = "encoding/encodings/{encoding_id}/streams";
    const ENCODING_STREAMS_GET = "encoding/encodings/{encoding_id}/streams/{stream_id}";

    const ENCODING_MUXINGS_MP4 = "encoding/encodings/{encoding_id}/muxings/mp4";
    const ENCODING_MUXINGS_FMP4 = "encoding/encodings/{encoding_id}/muxings/fmp4";
    const ENCODING_MUXINGS_TS = "encoding/encodings/{encoding_id}/muxings/ts";
    const ENCODING_MUXINGS_MP4_GET = "encoding/encodings/{encoding_id}/muxings/mp4/{muxing_id}";
    const ENCODING_MUXINGS_FMP4_GET = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}";
    const ENCODING_MUXINGS_TS_GET = "encoding/encodings/{encoding_id}/muxings/ts/{muxing_id}";

    const ENCODING_MUXINGS_FMP4_DRM_WIDEVINE = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/widevine";
    const ENCODING_MUXINGS_FMP4_DRM_PLAYREADY = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/playready";
    const ENCODING_MUXINGS_FMP4_DRM_PRIMETIME = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/primetime";
    const ENCODING_MUXINGS_FMP4_DRM_FAIRPLAY = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/fairplay";
    const ENCODING_MUXINGS_FMP4_DRM_MARLIN = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/marlin";
    const ENCODING_MUXINGS_FMP4_DRM_CLEARKEY = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/clearkey";
    const ENCODING_MUXINGS_FMP4_DRM_CENC = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/cenc";

    const INPUTS = "encoding/inputs";
    const INPUT_RTMP = "encoding/inputs/rtmp";
    const INPUT_HTTP = "encoding/inputs/http";
    const INPUT_HTTPS = "encoding/inputs/https";
    const INPUT_S3 = "encoding/inputs/s3";
    const INPUT_GCS = "encoding/inputs/gcs";
    const INPUT_FTP = "encoding/inputs/ftp";
    const INPUT_SFTP = "encoding/inputs/sftp";
    const INPUT_AZURE = "encoding/inputs/azure";
    const INPUT_ASPERA = "encoding/inputs/aspera";

    const OUTPUTS = "encoding/outputs";
    const OUTPUT_S3 = "encoding/outputs/s3";
    const OUTPUT_GCS = "encoding/outputs/gcs";
    const OUTPUT_FTP = "encoding/outputs/ftp";
    const OUTPUT_SFTP = "encoding/outputs/sftp";
    const OUTPUT_AZURE = "encoding/outputs/azure";

    const CODEC_CONFIGURATIONS = "encoding/configurations";
    const CODEC_CONFIGURATIONS_TYPE = "encoding/configurations/{configuration_id}/type";
    const CODEC_CONFIGURATION_H264 = "encoding/configurations/video/h264";
    const CODEC_CONFIGURATION_H265 = "encoding/configurations/video/h265";
    const CODEC_CONFIGURATION_AAC = "encoding/configurations/audio/aac";

    const MANIFEST_DASH = "encoding/manifests/dash";
    const MANIFEST_DASH_PERIODS = "encoding/manifests/dash/{manifest_id}/periods";
    const MANIFEST_DASH_PERIODS_VIDEO_ADAPTION_SET = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/video";
    const MANIFEST_DASH_PERIODS_AUDIO_ADAPTION_SET = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/audio";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4 = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/representations/fmp4";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_CONTENT_PROTECTION = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/contentprotection";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4_CONTENT_PROTECTION = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/representations/fmp4/{representation_id}/contentprotection";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4_DRM_CONTENT_PROTECTION = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/representations/fmp4/drm/{representation_id}/contentprotection";
    const MANIFEST_DASH_START = "encoding/manifests/dash/{manifest_id}/start";
    const MANIFEST_DASH_STOP = "encoding/manifests/dash/{manifest_id}/stop";
    const MANIFEST_DASH_RESTART = "encoding/manifests/dash/{manifest_id}/restart";
    const MANIFEST_DASH_STATUS = "encoding/manifests/dash/{manifest_id}/status";

    const MANIFEST_HLS = "encoding/manifests/hls";
    const MANIFEST_HLS_MEDIA = "encoding/manifests/hls/{manifest_id}/media";
    const MANIFEST_HLS_STREAMS = "encoding/manifests/hls/{manifest_id}/streams";
    const MANIFEST_HLS_START = "encoding/manifests/hls/{manifest_id}/start";
    const MANIFEST_HLS_STOP = "encoding/manifests/hls/{manifest_id}/stop";
    const MANIFEST_HLS_RESTART = "encoding/manifests/hls/{manifest_id}/restart";
    const MANIFEST_HLS_STATUS = "encoding/manifests/hls/{manifest_id}/status";

    /*
    const analysisStart = "encoding/inputs/{input_type}/{input_id}/analysis";
    const analysisStatus = "encoding/inputs/{input_type}/{input_id}/analysis/{analysisId}/status";
    const analysisDetails = "encoding/inputs/{input_type}/{input_id}/analysis/{analysisId}";

    const encodingStart = "encoding/encodings/{encoding_id}/start";
    const encodingStartRest = "encoding/encodings/{encoding_id}/startrest";
    const encodingStop = "encoding/encodings/{encoding_id}/stop";
    const encodingRestart = "encoding/encodings/{encoding_id}/restart";
    const encodingStatus = "encoding/encodings/{encoding_id}/status";

    const addFilterToStream = "encoding/encodings/{encoding_id}/streams/{stream_id}/filters";

    const widevineDrms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/widevine";
    const playReadyDrms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/playready";
    const primeTimeDrms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/primetime";
    const fairPlayFmp4Drms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/fairplay";
    const marlinDrms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/marlin";
    const clearKeyDrms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/clearkey";
    const cencDrms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/cenc";

    const fairPlayTsDrms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/ts/{muxing_id}/drm/fairplay";
    const aesEncryptionDrms = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/ts/{muxing_id}/drm/aes";

    const addWidevineDrmToFmp4Muxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/widevine";
    const addPlayReadyDrmToFmp4Muxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/playready";
    const addPrimeTimeDrmToFmp4Muxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/primetime";
    const addFairPlayDrmToFmp4Muxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/fairplay";
    const addMarlinDrmToFmp4Muxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/marlin";
    const addClearKeyDrmToFmp4Muxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/clearkey";
    const addCencDrmToFmp4Muxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/fmp4/{muxing_id}/drm/cenc";

    const addFairPlayDrmToTssMuxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/ts/{muxing_id}/drm/fairplay";
    const addAESEncryptionToTssMuxing = "encoding/encodings/{encoding_id}/streams/{stream_id}/muxings/ts/{muxing_id}/drm/aes";

    const manifestDashAddPeriod = "encoding/manifests/dash/{manifest_id}/periods";
    const manifestDashAddVideoAdaptionSet = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/video";
    const manifestDashAddAudioAdaptionSet = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/audio";
    const manifestDashAddRepresentation = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaptionset_id}/representations/fmp4";
    const manifestDashStart = "encoding/manifests/dash/{manifest_id}/start";
    const manifestDashStatus = "encoding/manifests/dash/{manifest_id}/status";

    const manifestHls = "encoding/manifests/hls";
    const manifestHlsMediaInfo = "encoding/manifests/hls/{manifest_id}/media";
    const manifestHlsStreamInfo = "encoding/manifests/hls/{manifest_id}/streams";
    const manifestHlsStart = "encoding/manifests/hls/{manifest_id}/start";
    const manifestHlsStatus = "encoding/manifests/hls/{manifest_id}/status";

    const filterCrop = "encoding/filters/crop";
    const filterRotate = "encoding/filters/rotate";
    const filterWatermark = "encoding/filters/watermark";

    const customDataSuffix = "/customData";

    const thumbnails = "encoding/encodings/{encoding_id}/streams/{stream_id}/thumbnails";
    const sprites = "encoding/encodings/{encoding_id}/streams/{stream_id}/sprites";

    const transferEncoding = "encoding/transfers/encoding";
    const transferEncodingStatus = "encoding/transfers/encoding/{transfer_id}/status";
    const transferEncodingDetails = "encoding/transfers/encoding/{transfer_id}";

    const storageFolders = "storage/folders";
    const storageFiles = "storage/files";
    const storageStatistics = "/storage/statistics";*/
}