<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

class H265VideoCodecConfiguration extends VideoConfiguration
{

    /**
     * @JMS\Type("string")
     * @var  string  H265Profile
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
     * @var  string MvPredictionMode
     */
    private $mvPredictionMode;
    /**
     * @JMS\Type("integer")
     * @var  integer Range: 16-24
     */
    private $mvSearchRangeMax;
    /**
     * @JMS\Type("string")
     * @var  string H265Level
     */
    private $level;
    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $cabac;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $rcLookahead;

    /**
     * @JMS\Type("string")
     * @var  string  BAdapt
     */
    private $bAdapt;

    /**
     * @JMS\Type("string")
     * @var  string  MaxCTUSize
     */
    private $maxCTUSize;

    /**
     * @JMS\Type("string")
     * @var  string  TuIntraDepth
     */
    private $tuIntraDepth;

    /**
     * @JMS\Type("string")
     * @var  string  TuInterDepth
     */
    private $tuInterDepth;

    /**
     * @JMS\Type("string")
     * @var  string  MotionSearch
     */
    private $motionSearch;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $subMe;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $motionSearchRange;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $weightPredictionOnPSlice;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $weightPredictionOnBSlice;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $sao;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $profile H265Profile ENUM available
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

    /**
     * @return int
     */
    public function getRcLookahead()
    {
        return $this->rcLookahead;
    }

    /**
     * @param int $rcLookahead
     */
    public function setRcLookahead($rcLookahead)
    {
        $this->rcLookahead = $rcLookahead;
    }

    /**
     * @return string
     */
    public function getBAdapt()
    {
        return $this->bAdapt;
    }

    /**
     * @param string $bAdapt
     */
    public function setBAdapt($bAdapt)
    {
        $this->bAdapt = $bAdapt;
    }

    /**
     * @return string
     */
    public function getMaxCTUSize()
    {
        return $this->maxCTUSize;
    }

    /**
     * @param string $maxCTUSize
     */
    public function setMaxCTUSize($maxCTUSize)
    {
        $this->maxCTUSize = $maxCTUSize;
    }

    /**
     * @return string
     */
    public function getTuIntraDepth()
    {
        return $this->tuIntraDepth;
    }

    /**
     * @param string $tuIntraDepth
     */
    public function setTuIntraDepth($tuIntraDepth)
    {
        $this->tuIntraDepth = $tuIntraDepth;
    }

    /**
     * @return string
     */
    public function getTuInterDepth()
    {
        return $this->tuInterDepth;
    }

    /**
     * @param string $tuInterDepth
     */
    public function setTuInterDepth($tuInterDepth)
    {
        $this->tuInterDepth = $tuInterDepth;
    }

    /**
     * @return string
     */
    public function getMotionSearch()
    {
        return $this->motionSearch;
    }

    /**
     * @param string $motionSearch
     */
    public function setMotionSearch($motionSearch)
    {
        $this->motionSearch = $motionSearch;
    }

    /**
     * @return int
     */
    public function getSubMe()
    {
        return $this->subMe;
    }

    /**
     * @param int $subMe
     */
    public function setSubMe($subMe)
    {
        $this->subMe = $subMe;
    }

    /**
     * @return int
     */
    public function getMotionSearchRange()
    {
        return $this->motionSearchRange;
    }

    /**
     * @param int $motionSearchRange
     */
    public function setMotionSearchRange($motionSearchRange)
    {
        $this->motionSearchRange = $motionSearchRange;
    }

    /**
     * @return boolean
     */
    public function isWeightPredictionOnPSlice()
    {
        return $this->weightPredictionOnPSlice;
    }

    /**
     * @param boolean $weightPredictionOnPSlice
     */
    public function setWeightPredictionOnPSlice($weightPredictionOnPSlice)
    {
        $this->weightPredictionOnPSlice = $weightPredictionOnPSlice;
    }

    /**
     * @return boolean
     */
    public function isWeightPredictionOnBSlice()
    {
        return $this->weightPredictionOnBSlice;
    }

    /**
     * @param boolean $weightPredictionOnBSlice
     */
    public function setWeightPredictionOnBSlice($weightPredictionOnBSlice)
    {
        $this->weightPredictionOnBSlice = $weightPredictionOnBSlice;
    }

    /**
     * @return boolean
     */
    public function isSao()
    {
        return $this->sao;
    }

    /**
     * @param boolean $sao
     */
    public function setSao($sao)
    {
        $this->sao = $sao;
    }

}