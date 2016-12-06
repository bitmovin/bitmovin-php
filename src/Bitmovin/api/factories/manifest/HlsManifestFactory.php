<?php

namespace Bitmovin\api\factories\manifest;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\configs\audio\AudioStreamConfig;

class HlsManifestFactory
{

    private static function addStreamInfoToHlsManifest($uri, $encodingId, $streamId, $muxingId, $drmId,
                                                       $audioGroupId, $subtitleGroup, $segmentPath, HlsManifest $manifest, ApiClient $apiClient)
    {
        $s = new StreamInfo();
        $s->setUri($uri);
        $s->setEncodingId($encodingId);
        $s->setStreamId($streamId);
        $s->setMuxingId($muxingId);
        $s->setDrmId($drmId);
        $s->setAudio($audioGroupId);
        $s->setSubtitles($subtitleGroup);
        $s->setSegmentPath($segmentPath);
        return $apiClient->manifests()->hls()->createStreamInfo($manifest, $s);
    }

    /**
     * @param AudioStreamConfig $config
     * @param                   $encodingId
     * @param                   $streamId
     * @param                   $muxingId
     * @param                   $drmId
     * @param                   $segmentPath
     * @param                   $uri
     * @return MediaInfo
     */
    private static function getDefaultAudioMediaInfo(AudioStreamConfig $config, $encodingId, $streamId, $muxingId, $drmId, $segmentPath, $uri)
    {
        $audioMediaInfo = new MediaInfo();
        $audioMediaInfo->setGroupId('audio');
        $audioMediaInfo->setName($config->name);
        $audioMediaInfo->setUri($uri);
        $audioMediaInfo->setType(MediaInfoType::AUDIO);
        $audioMediaInfo->setEncodingId($encodingId);
        $audioMediaInfo->setStreamId($streamId);
        $audioMediaInfo->setMuxingId($muxingId);
        $audioMediaInfo->setDrmId($drmId);
        $audioMediaInfo->setLanguage($config->lang);
        $audioMediaInfo->setAssocLanguage($config->lang);
        $audioMediaInfo->setAutoselect(false);
        $audioMediaInfo->setDefault(false);
        $audioMediaInfo->setForced(false);
        $audioMediaInfo->setCharacteristics(['public.accessibility.describes-audio']);
        $audioMediaInfo->setSegmentPath($segmentPath);
        return $audioMediaInfo;
    }

    /**
     * @param JobContainer                              $jobContainer
     * @param EncodingContainer                         $encodingContainer
     * @param                                           $manifest
     * @param ApiClient                                 $apiClient
     */
    public static function createHlsFmp4ManifestForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, $manifest, ApiClient $apiClient)
    {
        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            if ($codecConfigContainer->apiCodecConfiguration instanceof H264VideoCodecConfiguration)
            {
                foreach ($codecConfigContainer->muxings as $muxing)
                {
                    if (!$muxing instanceof FMP4Muxing)
                    {
                        continue;
                    }
                    $segmentPath = static::createSegmentPath($jobContainer, $muxing);
                    $playlistFileName = static::createPlaylistFileName($segmentPath);
                    static::addStreamInfoToHlsManifest($playlistFileName, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), null,
                        'audio', null, $segmentPath, $manifest, $apiClient);
                }
            }
            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                foreach ($codecConfigContainer->muxings as $muxing)
                {
                    if (!$muxing instanceof FMP4Muxing)
                    {
                        continue;
                    }
                    /** @var AudioStreamConfig $codec */
                    $codec = $codecConfigContainer->codecConfig;
                    $segmentPath = static::createSegmentPath($jobContainer, $muxing);
                    $playlistFileName = static::createPlaylistFileName($segmentPath);
                    $mediaInfo = static::getDefaultAudioMediaInfo($codec, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), null, $segmentPath, $playlistFileName);
                    $apiClient->manifests()->hls()->createMediaInfo($manifest, $mediaInfo);
                }
            }
        }
    }

    /**
     * @param JobContainer                              $jobContainer
     * @param EncodingContainer                         $encodingContainer
     * @param                                           $manifest
     * @param ApiClient                                 $apiClient
     */
    public static function createHlsManifestForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, $manifest, ApiClient $apiClient)
    {
        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            if ($codecConfigContainer->apiCodecConfiguration instanceof H264VideoCodecConfiguration)
            {
                foreach ($codecConfigContainer->muxings as $muxing)
                {
                    if (!$muxing instanceof TSMuxing)
                    {
                        continue;
                    }
                    $segmentPath = static::createSegmentPath($jobContainer, $muxing);
                    $playlistFileName = static::createPlaylistFileName($segmentPath);
                    static::addStreamInfoToHlsManifest($playlistFileName, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), null,
                        'audio', null, $segmentPath, $manifest, $apiClient);
                }
            }
            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                foreach ($codecConfigContainer->muxings as $muxing)
                {
                    if (!$muxing instanceof TSMuxing)
                    {
                        continue;
                    }
                    /** @var AudioStreamConfig $codec */
                    $codec = $codecConfigContainer->codecConfig;
                    $segmentPath = static::createSegmentPath($jobContainer, $muxing);
                    $playlistFileName = static::createPlaylistFileName($segmentPath);
                    $mediaInfo = static::getDefaultAudioMediaInfo($codec, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), null, $segmentPath, $playlistFileName);
                    $apiClient->manifests()->hls()->createMediaInfo($manifest, $mediaInfo);
                }
            }
        }
    }

    /**
     * @param JobContainer $jobContainer
     * @param TSMuxing|FMP4Muxing     $muxing
     * @return mixed|string
     */
    private static function createSegmentPath(JobContainer $jobContainer, $muxing)
    {
        $segmentPath = $muxing->getOutputs()[0]->getOutputPath();
        $segmentPath = str_ireplace($jobContainer->getOutputPath(), "", $segmentPath);
        if (substr($segmentPath, 0, 1) == '/')
        {
            $segmentPath = substr($segmentPath, 1);
        }
        return $segmentPath;
    }

    /**
     * @param $segmentPath
     * @return mixed|string
     */
    private static function createPlaylistFileName($segmentPath)
    {
        $playlistFileName = substr($segmentPath, 0, -1);
        $playlistFileName = str_ireplace("/", "_", $playlistFileName);
        $playlistFileName .= '.m3u8';
        if (substr($playlistFileName, 0, 1) == '_')
        {
            $playlistFileName = substr($playlistFileName, 1);
        }
        return $playlistFileName;
    }

}