<?php

namespace Bitmovin\api\factories\manifest;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\enum\manifests\hls\MediaInfoType;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\muxing\AbstractMuxing;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\manifest\HlsConfigurationFileNaming;
use Bitmovin\configs\manifest\HlsFMP4OutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;

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
     * @param JobContainer        $jobContainer
     * @param EncodingContainer   $encodingContainer
     * @param HlsManifest         $manifest
     * @param ApiClient           $apiClient
     * @param HlsFMP4OutputFormat $hlsFMP4Format
     */
    public static function createHlsFMP4ManifestForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, HlsManifest $manifest, ApiClient $apiClient, HlsFMP4OutputFormat $hlsFMP4Format)
    {
        $audioGroupId = null;
        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                $audioGroupId = 'audio';
            }
        }

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
                    $playlistFileName = static::createPlaylistFileName($segmentPath, $hlsFMP4Format->hlsConfigurationFileNaming, $codecConfigContainer->codecConfig);
                    static::addStreamInfoToHlsManifest($playlistFileName, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), null,
                        $audioGroupId, null, $segmentPath, $manifest, $apiClient);
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
                    $playlistFileName = static::createPlaylistFileName($segmentPath, $hlsFMP4Format->hlsConfigurationFileNaming, $codecConfigContainer->codecConfig);
                    $mediaInfo = static::getDefaultAudioMediaInfo($codec, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), null, $segmentPath, $playlistFileName);
                    $apiClient->manifests()->hls()->createMediaInfo($manifest, $mediaInfo);
                }
            }
        }
    }

    /**
     * @param JobContainer      $jobContainer
     * @param EncodingContainer $encodingContainer
     * @param HlsManifest       $manifest
     * @param ApiClient         $apiClient
     * @param HlsOutputFormat   $hlsOutputFormat
     */
    public static function createHlsManifestForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, HlsManifest $manifest, ApiClient $apiClient, HlsOutputFormat $hlsOutputFormat)
    {
        $audioGroupId = null;
        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                $audioGroupId = 'audio';
            }
        }

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
                    $playlistFileName = static::createPlaylistFileName($segmentPath, $hlsOutputFormat->hlsConfigurationFileNaming, $codecConfigContainer->codecConfig);
                    static::addStreamInfoToHlsManifest($playlistFileName, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), null,
                        $audioGroupId, null, $segmentPath, $manifest, $apiClient);
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
                    $playlistFileName = static::createPlaylistFileName($segmentPath, $hlsOutputFormat->hlsConfigurationFileNaming, $codecConfigContainer->codecConfig);
                    $mediaInfo = static::getDefaultAudioMediaInfo($codec, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), null, $segmentPath, $playlistFileName);
                    $apiClient->manifests()->hls()->createMediaInfo($manifest, $mediaInfo);
                }
            }
        }
    }

    /**
     * @param JobContainer   $jobContainer
     * @param AbstractMuxing $muxing
     * @return mixed|string
     */
    private static function createSegmentPath(JobContainer $jobContainer, AbstractMuxing $muxing)
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
     * @param string                       $segmentPath
     * @param HlsConfigurationFileNaming[] $hlsConfigurationFileNaming
     * @param AbstractStreamConfig         $abstractStreamConfig
     * @return mixed|string
     */
    private static function createPlaylistFileName($segmentPath, $hlsConfigurationFileNaming, $abstractStreamConfig)
    {
        if (is_array($hlsConfigurationFileNaming) && $abstractStreamConfig != null)
        {
            foreach ($hlsConfigurationFileNaming as $item)
            {
                if ($item->configuration == $abstractStreamConfig && $item->name != null)
                    return $item->name;
            }
        }
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