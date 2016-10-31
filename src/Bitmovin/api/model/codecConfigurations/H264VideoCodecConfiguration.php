<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

class H264VideoCodecConfiguration extends VideoConfiguration
{

    /**
     * @JMS\Type("string")
     * @var  string  H264Profile
     */
    private $profile;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $bFrames;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $refFrames;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $qpMin;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $qpMax;
    /**
     * @JMS\Type("string")
     * @var  string MvPredictionMode
     */
    private $mvPredictionMode;
    /**
     * @JMS\Type("integer")
     * @var  integer Range: 16-24
     */
    private $mvSearchRangeMax;
    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $cabac;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $minBitrate;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $maxBitrate;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $bufsize;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $minGop;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $maxGop;
    /**
     * @JMS\Type("string")
     * @var  string H264Level
     */
    private $level;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $profile H264Profile ENUM available
     * @param int    $bitrate Target bitrate for the encoded video in bps (bits per second)
     * @param float  $rate    Target frame rate of the encoded video
     */
    public function __construct($name, $profile, $bitrate, $rate)
    {
        parent::__construct($name, $bitrate, $rate);
        $this->profile = $profile;
    }

    /**
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param string $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return int
     */
    public function getBFrames()
    {
        return $this->bFrames;
    }

    /**
     * @param int $bFrames
     */
    public function setBFrames($bFrames)
    {
        $this->bFrames = $bFrames;
    }

    /**
     * @return int
     */
    public function getRefFrames()
    {
        return $this->refFrames;
    }

    /**
     * @param int $refFrames
     */
    public function setRefFrames($refFrames)
    {
        $this->refFrames = $refFrames;
    }

    /**
     * @return int
     */
    public function getQpMin()
    {
        return $this->qpMin;
    }

    /**
     * @param int $qpMin
     */
    public function setQpMin($qpMin)
    {
        $this->qpMin = $qpMin;
    }

    /**
     * @return int
     */
    public function getQpMax()
    {
        return $this->qpMax;
    }

    /**
     * @param int $qpMax
     */
    public function setQpMax($qpMax)
    {
        $this->qpMax = $qpMax;
    }

    /**
     * @return string
     */
    public function getMvPredictionMode()
    {
        return $this->mvPredictionMode;
    }

    /**
     * @param string $mvPredictionMode
     */
    public function setMvPredictionMode($mvPredictionMode)
    {
        $this->mvPredictionMode = $mvPredictionMode;
    }

    /**
     * @return int
     */
    public function getMvSearchRangeMax()
    {
        return $this->mvSearchRangeMax;
    }

    /**
     * @param int $mvSearchRangeMax
     */
    public function setMvSearchRangeMax($mvSearchRangeMax)
    {
        $this->mvSearchRangeMax = $mvSearchRangeMax;
    }

    /**
     * @return boolean
     */
    public function isCabac()
    {
        return $this->cabac;
    }

    /**
     * @param boolean $cabac
     */
    public function setCabac($cabac)
    {
        $this->cabac = $cabac;
    }

    /**
     * @return int
     */
    public function getMinBitrate()
    {
        return $this->minBitrate;
    }

    /**
     * @param int $minBitrate
     */
    public function setMinBitrate($minBitrate)
    {
        $this->minBitrate = $minBitrate;
    }

    /**
     * @return int
     */
    public function getMaxBitrate()
    {
        return $this->maxBitrate;
    }

    /**
     * @param int $maxBitrate
     */
    public function setMaxBitrate($maxBitrate)
    {
        $this->maxBitrate = $maxBitrate;
    }

    /**
     * @return int
     */
    public function getBufsize()
    {
        return $this->bufsize;
    }

    /**
     * @param int $bufsize
     */
    public function setBufsize($bufsize)
    {
        $this->bufsize = $bufsize;
    }

    /**
     * @return int
     */
    public function getMinGop()
    {
        return $this->minGop;
    }

    /**
     * @param int $minGop
     */
    public function setMinGop($minGop)
    {
        $this->minGop = $minGop;
    }

    /**
     * @return int
     */
    public function getMaxGop()
    {
        return $this->maxGop;
    }

    /**
     * @param int $maxGop
     */
    public function setMaxGop($maxGop)
    {
        $this->maxGop = $maxGop;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param string $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

}