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
     * @JMS\Type("float")
     * @var  float
     */
    private $crf;
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
    private $qp;
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
     * @JMS\Type("string")
     * @var  string H265Level
     */
    private $level;
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
     * @JMS\Type("float")
     * @var  float
     */
    private $minKeyFrameInterval;

    /**
     * @JMS\Type("float")
     * @var  float
     */
    private $maxKeyFrameInterval;

    /**
     * @JMS\Type("string")
     * @var  string  MasterDisplay
     */
    private $masterDisplay;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $maxContentLightLevel;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $maxPictureAverageLightLevel;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $hdr;

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
     * @return float
     */
    public function getCrf()
    {
        return $this->crf;
    }

    /**
     * @param float $rate
     */
    public function setCrf($crf)
    {
        $this->crf = $crf;
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
    public function getQp()
    {
        return $this->qp;
    }

    /**
     * @param int $qp
     */
    public function setQp($qp)
    {
        $this->qp = $qp;
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
     * @return string
     */
    public function getMvPredictionMode()
    {
        return $this->mvPredictionMode;
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

    /**
     * @return float
     */
    public function getMinKeyFrameInterval()
    {
        return $this->minKeyFrameInterval;
    }

    /**
     * @param float $rate
     */
    public function setMinKeyFrameInterval($minKeyFrameInterval)
    {
        $this->minKeyFrameInterval = $minKeyFrameInterval;
    }

    /**
     * @return float
     */
    public function getMaxKeyFrameInterval()
    {
        return $this->maxKeyFrameInterval;
    }

    /**
     * @param float $rate
     */
    public function setMaxKeyFrameInterval($maxKeyFrameInterval)
    {
        $this->maxKeyFrameInterval = $maxKeyFrameInterval;
    }

    /**
     * @return string
     */
    public function getMasterDisplay()
    {
        return $this->masterDisplay;
    }

    /**
     * @param string $masterDisplay
     */
    public function setMasterDisplay($masterDisplay)
    {
        $this->masterDisplay = $masterDisplay;
    }

    /**
     * @return int
     */
    public function getMaxContentLightLevel()
    {
        return $this->maxContentLightLevel;
    }

    /**
     * @param int $maxContentLightLevel
     */
    public function setMaxContentLightLevel($maxContentLightLevel)
    {
        $this->maxContentLightLevel = $maxContentLightLevel;
    }

    /**
     * @return int
     */
    public function getMaxPictureAverageLightLevel()
    {
        return $this->maxPictureAverageLightLevel;
    }

    /**
     * @param int $maxPictureAverageLightLevel
     */
    public function setMaxPictureAverageLightLevel($maxPictureAverageLightLevel)
    {
        $this->maxPictureAverageLightLevel = $maxPictureAverageLightLevel;
    }

    /**
     * @return boolean
     */
    public function isHdr()
    {
        return $this->hdr;
    }

    /**
     * @param boolean $hdr
     */
    public function setHdr($hdr)
    {
        $this->hdr = $hdr;
    }

}
