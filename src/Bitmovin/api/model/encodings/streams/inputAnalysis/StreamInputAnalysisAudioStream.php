<?php
/**
 * Created by PhpStorm.
 * User: dmoser
 * Date: 14.03.18
 * Time: 10:58
 */

namespace Bitmovin\api\model\encodings\streams\inputAnalysis;

use JMS\Serializer\Annotation as JMS;

class StreamInputAnalysisAudioStream
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $id;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $position;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $duration;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $codec;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $sampleRate;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $bitrate;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $channelFormat;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $language;

    /**
     * @JMS\Type("boolean")
     * @var boolean
     */
    private $hearingImpaired;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getCodec()
    {
        return $this->codec;
    }

    /**
     * @param string $codec
     */
    public function setCodec($codec)
    {
        $this->codec = $codec;
    }

    /**
     * @return int
     */
    public function getSampleRate()
    {
        return $this->sampleRate;
    }

    /**
     * @param int $sampleRate
     */
    public function setSampleRate($sampleRate)
    {
        $this->sampleRate = $sampleRate;
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
     * @return string
     */
    public function getChannelFormat()
    {
        return $this->channelFormat;
    }

    /**
     * @param string $channelFormat
     */
    public function setChannelFormat($channelFormat)
    {
        $this->channelFormat = $channelFormat;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return bool
     */
    public function isHearingImpaired()
    {
        return $this->hearingImpaired;
    }

    /**
     * @param bool $hearingImpaired
     */
    public function setHearingImpaired($hearingImpaired)
    {
        $this->hearingImpaired = $hearingImpaired;
    }
}