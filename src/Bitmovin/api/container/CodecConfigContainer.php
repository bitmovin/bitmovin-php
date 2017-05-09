<?php


namespace Bitmovin\api\container;

use Bitmovin\api\model\codecConfigurations\CodecConfiguration;
use Bitmovin\api\model\encodings\muxing\AbstractMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\encodings\streams\sprites\Sprite;
use Bitmovin\api\model\encodings\streams\thumbnails\Thumbnail;
use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\images\ThumbnailConfig;
use Bitmovin\configs\manifest\AbstractOutputFormat;
use Bitmovin\configs\manifest\ProgressiveMp4OutputFormat;
use Bitmovin\configs\video\AbstractVideoStreamConfig;
use Bitmovin\helper\PathHelper;

class CodecConfigContainer
{

    const AUDIO = 'audio/';
    const VIDEO = 'video/';
    const THUMBNAILS = 'thumbnails/';
    const SPRITES = 'sprites/';

    /**
     * @var CodecConfiguration
     */
    public $apiCodecConfiguration;
    /**
     * @var AbstractStreamConfig
     */
    public $codecConfig;
    /**
     * @var Stream
     */
    public $stream;
    /**
     * @var AbstractMuxing[]
     */
    public $hlsMuxings = array();
    /**
     * @var AbstractMuxing[]
     */
    public $muxings = array();
    /**
     * @var Thumbnail[]
     */
    public $thumbnails = array();
    /**
     * @var Sprite[]
     */
    public $sprites = array();

    public function getDashCencVideoOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AbstractVideoStreamConfig $codecConfigVideo */
        $codecConfigVideo = $this->codecConfig;
        $name = $codecConfigVideo->bitrate;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::VIDEO, $name, '/dash_cenc/');
    }

    public function getDashVideoOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AbstractVideoStreamConfig $codecConfigVideo */
        $codecConfigVideo = $this->codecConfig;
        $name = $codecConfigVideo->bitrate;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::VIDEO, $name, '/dash/');
    }

    public function getHlsVideoOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AbstractVideoStreamConfig $codecConfigVideo */
        $codecConfigVideo = $this->codecConfig;
        $name = $codecConfigVideo->bitrate;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::VIDEO, $name, '/hls/');
    }

    public function getThumbnailOutputPath(JobContainer $jobContainer, ThumbnailConfig $thumbnailConfig)
    {
        return PathHelper::combinePath($jobContainer->getOutputPath(), $thumbnailConfig->folder);
    }

    public function getSpriteOutputPath(JobContainer $jobContainer)
    {
        return PathHelper::combinePath($jobContainer->getOutputPath(), static::SPRITES);
    }

    public function getMp4OutputPath(JobContainer $jobContainer, ProgressiveMp4OutputFormat $progressiveMp4OutputFormat)
    {
        return PathHelper::combinePath($jobContainer->getOutputPath(), $progressiveMp4OutputFormat->folder);
    }

    public function getSmoothStreamingVideoOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AbstractVideoStreamConfig $codecConfigVideo */
        $codecConfigVideo = $this->codecConfig;
        $name = $codecConfigVideo->bitrate;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::VIDEO, $name, '/smoothstreaming/');
    }

    public function getSmoothStreamingPlayReadyVideoOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AbstractVideoStreamConfig $codecConfigVideo */
        $codecConfigVideo = $this->codecConfig;
        $name = $codecConfigVideo->bitrate;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::VIDEO, $name, '/smoothstreaming_playready/');
    }

    public function getDashCencAudioOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AudioStreamConfig $codecConfigAudio */
        $codecConfigAudio = $this->codecConfig;
        $name = $codecConfigAudio->bitrate . '_' . $codecConfigAudio->lang;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::AUDIO, $name, '/dash_cenc/');
    }

    public function getDashAudioOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AudioStreamConfig $codecConfigAudio */
        $codecConfigAudio = $this->codecConfig;
        $name = $codecConfigAudio->bitrate . '_' . $codecConfigAudio->lang;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::AUDIO, $name, '/dash/');
    }

    public function getHlsAudioOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AudioStreamConfig $codecConfigAudio */
        $codecConfigAudio = $this->codecConfig;
        $name = $codecConfigAudio->bitrate . '_' . $codecConfigAudio->lang;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::AUDIO, $name, '/hls/');
    }

    public function getSmoothStreamingAudioOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AudioStreamConfig $codecConfigAudio */
        $codecConfigAudio = $this->codecConfig;
        $name = $codecConfigAudio->bitrate . '_' . $codecConfigAudio->lang;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::AUDIO, $name, '/smoothstreaming/');
    }

    public function getSmoothStreamingPlayReadyAudioOutputPath(JobContainer $jobContainer, AbstractOutputFormat $outputFormat)
    {
        /** @var AudioStreamConfig $codecConfigAudio */
        $codecConfigAudio = $this->codecConfig;
        $name = $codecConfigAudio->bitrate . '_' . $codecConfigAudio->lang;
        return PathHelper::combinePath($jobContainer->getOutputPath(), $outputFormat->folder, static::AUDIO, $name, '/smoothstreaming_playready/');
    }

}