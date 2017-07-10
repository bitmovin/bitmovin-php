<?php

namespace Bitmovin\api\util;

final class ApiUrls
{
    const PH_ENCODING_ID = "{encoding_id}";
    const PH_CONFIGURATION_ID = "{configuration_id}";
    const PH_STREAM_ID = "{stream_id}";
    const PH_THUMBNAIL_ID = "{thumbnail_id}";
    const PH_MUXING_ID = "{muxing_id}";
    const PH_MANIFEST_ID = "{manifest_id}";
    const PH_PERIOD_ID = "{period_id}";
    const PH_ADAPTION_ID = "{adaption_id}";
    const PH_REPRESENTATION_ID = "{representation_id}";
    const PH_TRANSFER_ID = "{transfer_id}";

    const ENCODING_TRANSFERS_ENCODING = "encoding/transfers/encoding";
    const ENCODING_TRANSFERS_ENCODING_STATUS = "encoding/transfers/encoding/{transfer_id}/status";
    const ENCODING_TRANSFERS_ENCODING_CUSTOM_DATA = "encoding/transfers/manifest/{transfer_id}/customData";

    const ENCODING_TRANSFERS_MANIFEST = "encoding/transfers/manifest";
    const ENCODING_TRANSFERS_MANIFEST_STATUS = self::ENCODING_TRANSFERS_MANIFEST . "/{transfer_id}/status";
    const ENCODING_TRANSFERS_MANIFEST_CUSTOM_DATA = self::ENCODING_TRANSFERS_MANIFEST . "/{transfer_id}/customData";

    const ENCODINGS = "encoding/encodings";
    const ENCODING_GET = "encoding/encodings/{encoding_id}";
    const ENCODING_START = "encoding/encodings/{encoding_id}/start";
    const ENCODING_STOP = "encoding/encodings/{encoding_id}/stop";
    const ENCODING_DETAILS_LIVE = "encoding/encodings/{encoding_id}/live";
    const ENCODING_START_LIVE = "encoding/encodings/{encoding_id}/live/start";
    const ENCODING_STOP_LIVE = "encoding/encodings/{encoding_id}/live/stop";
    const ENCODING_STATUS = "encoding/encodings/{encoding_id}/status";

    const ENCODING_STREAMS = "encoding/encodings/{encoding_id}/streams";

    const ENCODING_STREAMS_SPRITES = "encoding/encodings/{encoding_id}/streams/{stream_id}/sprites";
    const ENCODING_STREAMS_THUMBNAILS = "encoding/encodings/{encoding_id}/streams/{stream_id}/thumbnails";
    const ENCODING_STREAMS_FILTERS = "encoding/encodings/{encoding_id}/streams/{stream_id}/filters";

    const ENCODING_MUXINGS_MP4 = "encoding/encodings/{encoding_id}/muxings/mp4";
    const ENCODING_MUXINGS_FMP4 = "encoding/encodings/{encoding_id}/muxings/fmp4";
    const ENCODING_MUXINGS_TS = "encoding/encodings/{encoding_id}/muxings/ts";
    const ENCODING_MUXINGS_PROGRESSIVE_TS = "encoding/encodings/{encoding_id}/muxings/progressive-ts";
    const ENCODING_MUXINGS_WEBM = "encoding/encodings/{encoding_id}/muxings/webm";

    const ENCODING_MUXINGS_FMP4_DRM_WIDEVINE = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/widevine";
    const ENCODING_MUXINGS_FMP4_DRM_PLAYREADY = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/playready";
    const ENCODING_MUXINGS_FMP4_DRM_PRIMETIME = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/primetime";
    const ENCODING_MUXINGS_FMP4_DRM_FAIRPLAY = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/fairplay";
    const ENCODING_MUXINGS_FMP4_DRM_MARLIN = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/marlin";
    const ENCODING_MUXINGS_FMP4_DRM_CLEARKEY = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/clearkey";
    const ENCODING_MUXINGS_FMP4_DRM_CENC = "encoding/encodings/{encoding_id}/muxings/fmp4/{muxing_id}/drm/cenc";

    const ENCODING_MUXINGS_MP4_DRM_PLAYREADY = "encoding/encodings/{encoding_id}/muxings/mp4/{muxing_id}/drm/playready";

    const ENCODING_MUXINGS_TS_DRM_FAIRPLAY = "encoding/encodings/{encoding_id}/muxings/ts/{muxing_id}/drm/fairplay";

    const FILTERS = "encoding/filters";
    const FILTERS_WATERMARK = "encoding/filters/watermark";
    const FILTERS_DEINTERLACE = "encoding/filters/deinterlace";
    const FILTERS_CROP = "encoding/filters/crop";
    const FILTERS_ROTATE = "encoding/filters/rotate";

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
    const INPUT_GENERIC_S3 = "encoding/inputs/generic-s3";

    const OUTPUTS = "encoding/outputs";
    const OUTPUT_S3 = "encoding/outputs/s3";
    const OUTPUT_GCS = "encoding/outputs/gcs";
    const OUTPUT_FTP = "encoding/outputs/ftp";
    const OUTPUT_SFTP = "encoding/outputs/sftp";
    const OUTPUT_AZURE = "encoding/outputs/azure";
    const OUTPUT_GENERIC_S3 = "encoding/outputs/generic-s3";

    const OUTPUT_BITMOVIN_AWS = "encoding/outputs/bitmovin/aws";
    const OUTPUT_BITMOVIN_GCP = "encoding/outputs/bitmovin/gcp";

    const CODEC_CONFIGURATIONS = "encoding/configurations";
    const CODEC_CONFIGURATIONS_TYPE = "encoding/configurations/{configuration_id}/type";
    const CODEC_CONFIGURATION_H264 = "encoding/configurations/video/h264";
    const CODEC_CONFIGURATION_H265 = "encoding/configurations/video/h265";
    const CODEC_CONFIGURATION_VP9 = "encoding/configurations/video/vp9";
    const CODEC_CONFIGURATION_AAC = "encoding/configurations/audio/aac";

    const MANIFEST_DASH = "encoding/manifests/dash";
    const MANIFEST_DASH_PERIODS = "encoding/manifests/dash/{manifest_id}/periods";
    const MANIFEST_DASH_PERIODS_VIDEO_ADAPTION_SET = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/video";
    const MANIFEST_DASH_PERIODS_AUDIO_ADAPTION_SET = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/audio";
    const MANIFEST_DASH_PERIODS_SUBTITLE_ADAPTATION_SET = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/subtitle";
    const MANIFEST_DASH_PERIODS_VTT_REPRESENTATION = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/representations/vtt";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4 = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/representations/fmp4";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_WEBM = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/representations/webm";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_CONTENT_PROTECTION = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/contentprotection";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4_CONTENT_PROTECTION = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/representations/fmp4/{representation_id}/contentprotection";
    const MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4_DRM_CONTENT_PROTECTION = "encoding/manifests/dash/{manifest_id}/periods/{period_id}/adaptationsets/{adaption_id}/representations/fmp4/drm/{representation_id}/contentprotection";
    const MANIFEST_DASH_START = "encoding/manifests/dash/{manifest_id}/start";
    const MANIFEST_DASH_STOP = "encoding/manifests/dash/{manifest_id}/stop";
    const MANIFEST_DASH_RESTART = "encoding/manifests/dash/{manifest_id}/restart";
    const MANIFEST_DASH_STATUS = "encoding/manifests/dash/{manifest_id}/status";

    const MANIFEST_HLS = "encoding/manifests/hls";
    const MANIFEST_HLS_MEDIA = "encoding/manifests/hls/{manifest_id}/media";
    const MANIFEST_HLS_VTT_MEDIA = "encoding/manifests/hls/{manifest_id}/media/vtt";
    const MANIFEST_HLS_STREAMS = "encoding/manifests/hls/{manifest_id}/streams";
    const MANIFEST_HLS_START = "encoding/manifests/hls/{manifest_id}/start";
    const MANIFEST_HLS_STOP = "encoding/manifests/hls/{manifest_id}/stop";
    const MANIFEST_HLS_RESTART = "encoding/manifests/hls/{manifest_id}/restart";
    const MANIFEST_HLS_STATUS = "encoding/manifests/hls/{manifest_id}/status";

    const MANIFEST_SMOOTH = "encoding/manifests/smooth";
    const MANIFEST_SMOOTH_START = "encoding/manifests/smooth/{manifest_id}/start";
    const MANIFEST_SMOOTH_STOP = "encoding/manifests/smooth/{manifest_id}/stop";
    const MANIFEST_SMOOTH_RESTART = "encoding/manifests/smooth/{manifest_id}/restart";
    const MANIFEST_SMOOTH_STATUS = "encoding/manifests/smooth/{manifest_id}/status";
    const MANIFEST_SMOOTH_REPRESENTATION = "encoding/manifests/smooth/{manifest_id}/representations/mp4";
    const MANIFEST_SMOOTH_CONTENT_PROTECTION = "encoding/manifests/smooth/{manifest_id}/contentprotection";

    const WEBHOOK_ENCODING_FINISHED = "notifications/webhooks/encoding/encodings/finished";
    const WEBHOOK_ENCODING_ERROR = "notifications/webhooks/encoding/encodings/error";
    const WEBHOOK_TRANSFER_FINISHED = "notifications/webhooks/encoding/transfers/finished";
    const WEBHOOK_TRANSFER_ERROR = "notifications/webhooks/encoding/transfers/error";

}