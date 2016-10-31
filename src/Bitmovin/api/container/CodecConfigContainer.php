<?php


namespace Bitmovin\api\container;


use Bitmovin\api\model\codecConfigurations\CodecConfiguration;
use Bitmovin\api\model\encodings\muxing\AbstractMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\video\AbstractVideoStreamConfig;

class CodecConfigContainer
{

    const AUDIO = 'audio/';
    const VIDEO = 'video/';

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
    public $muxings = array();

    /**
     * @param string[] ...$paths
     * @return string
     */
    private function combinePath(...$paths)
    {
        $path = '';
        foreach ($paths as $item)
        {
            if (substr($item, 0, 1) != '/' && substr($path, -1) != '/')
            {
                $path .= '/';
            }
            $path .= $item;
        }
        return $path;
    }

    public function getDashCencVideoOutputPath(JobContainer $jobContainer)
    {
        /** @var AbstractVideoStreamConfig $codecConfigVideo */
        $codecConfigVideo = $this->codecConfig;
        $name = $codecConfigVideo->bitrate;
        return $this->combinePath($jobContainer->getOutputPath(), static::VIDEO, $name, '/dash_cenc/');
    }

    public function getDashVideoOutputPath(JobContainer $jobContainer)
    {
        /** @var AbstractVideoStreamConfig $codecConfigVideo */
        $codecConfigVideo = $this->codecConfig;
        $name = $codecConfigVideo->bitrate;
        return $this->combinePath($jobContainer->getOutputPath(), static::VIDEO, $name, '/dash/');
    }

    public function getHlsVideoOutputPath(JobContainer $jobContainer)
    {
        /** @var AbstractVideoStreamConfig $codecConfigVideo */
        $codecConfigVideo = $this->codecConfig;
        $name = $codecConfigVideo->bitrate;
        return $this->combinePath($jobContainer->getOutputPath(), static::VIDEO, $name, '/hls/');
    }

    public function getDashCencAudioOutputPath(JobContainer $jobContainer)
    {
        /** @var AudioStreamConfig $codecConfigAudio */
        $codecConfigAudio = $this->codecConfig;
        $name = $codecConfigAudio->bitrate . '_' . $codecConfigAudio->lang;
        return $this->combinePath($jobContainer->getOutputPath(), static::AUDIO, $name, '/dash_cenc/');
    }

    public function getDashAudioOutputPath(JobContainer $jobContainer)
    {
        /** @var AudioStreamConfig $codecConfigAudio */
        $codecConfigAudio = $this->codecConfig;
        $name = $codecConfigAudio->bitrate . '_' . $codecConfigAudio->lang;
        return $this->combinePath($jobContainer->getOutputPath(), static::AUDIO, $name, '/dash/');
    }

    public function getHlsAudioOutputPath(JobContainer $jobContainer)
    {
        /** @var AudioStreamConfig $codecConfigAudio */
        $codecConfigAudio = $this->codecConfig;
        $name = $codecConfigAudio->bitrate . '_' . $codecConfigAudio->lang;
        return $this->combinePath($jobContainer->getOutputPath(), static::AUDIO, $name, '/hls/');
    }

}