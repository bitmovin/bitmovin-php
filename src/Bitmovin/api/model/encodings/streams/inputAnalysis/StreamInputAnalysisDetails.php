<?php

namespace Bitmovin\api\model\encodings\streams\inputAnalysis;

use JMS\Serializer\Annotation as JMS;

class StreamInputAnalysisDetails
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $formatName;

    /**
     * @JMS\Type("double")
     * @var double
     */
    private $startTime;

    /**
     * @JMS\Type("double")
     * @var double
     */
    private $duration;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $size;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $bitrate;

    /**
     * @JMS\Type("array<string, string>")
     * @var array
     */
    private $tags;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\streams\inputAnalysis\StreamInputAnalysisVideoStream>")
     * @var StreamInputAnalysisVideoStream[]
     */
    private $videoStreams;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\streams\inputAnalysis\StreamInputAnalysisAudioStream>")
     * @var StreamInputAnalysisAudioStream[]
     */
    private $audioStreams;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\streams\inputAnalysis\StreamInputAnalysisMetaStream>")
     * @var StreamInputAnalysisMetaStream[]
     */
    private $metaStreams;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\streams\inputAnalysis\StreamInputAnalysisSubtitleStream>")
     * @var StreamInputAnalysisSubtitleStream[]
     */
    private $subtitleStreams;

    /**
     * @return string
     */
    public function getFormatName()
    {
        return $this->formatName;
    }

    /**
     * @param string $formatName
     */
    public function setFormatName($formatName)
    {
        $this->formatName = $formatName;
    }

    /**
     * @return float
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param float $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getBitrate()
    {
        return $this->bitrate;
    }

    /**
     * @param int $bitrate
     */
    public function setBitrate($bitrate)
    {
        $this->bitrate = $bitrate;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return StreamInputAnalysisVideoStream[]
     */
    public function getVideoStreams()
    {
        return $this->videoStreams;
    }

    /**
     * @param StreamInputAnalysisVideoStream[] $videoStreams
     */
    public function setVideoStreams($videoStreams)
    {
        $this->videoStreams = $videoStreams;
    }

    /**
     * @return StreamInputAnalysisAudioStream[]
     */
    public function getAudioStreams()
    {
        return $this->audioStreams;
    }

    /**
     * @param StreamInputAnalysisAudioStream[] $audioStreams
     */
    public function setAudioStreams($audioStreams)
    {
        $this->audioStreams = $audioStreams;
    }

    /**
     * @return StreamInputAnalysisMetaStream[]
     */
    public function getMetaStreams()
    {
        return $this->metaStreams;
    }

    /**
     * @param StreamInputAnalysisAudioStream[] $metaStreams
     */
    public function setMetaStreams($metaStreams)
    {
        $this->metaStreams = $metaStreams;
    }

    /**
     * @param  StreamInputAnalysisSubtitleStream[] $subtitleStreams
     */
    public function setSubtitleStreams($subtitleStreams)
    {
        $this->subtitleStreams = $subtitleStreams;
    }

    /**
     * @return StreamInputAnalysisSubtitleStream[]
     */
    public function getSubtitleStreams()
    {
        return $this->subtitleStreams;
    }
}