<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

class VP9VideoCodecConfiguration extends VideoConfiguration
{
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $crf;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $lagInFrames;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $tileColumns;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $tileRows;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $frameParallel;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $maxIntraRate;

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
    private $rateUndershootPct;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $cpuUsed;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $noiseSensitivity;

    /**
     * @JMS\Type("string")
     * @var  string VP9Quality
     */
    private $quality;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $lossless;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $staticThresh;

    /**
     * @JMS\Type("string")
     * @var  string VP9aqMode
     */
    private $aqMode;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $arnrMaxFrames;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $arnrStrength;

    /**
     * @JMS\Type("string")
     * @var  string VP9arnrType
     */
    private $arnrType;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $profile H264Profile ENUM available
     * @param int    $bitrate Target bitrate for the encoded video in bps (bits per second)
     * @param float  $rate    Target frame rate of the encoded video
     */
    public function __construct($name, $bitrate, $rate)
    {
        parent::__construct($name, $bitrate, $rate);
    }

    /**
     * @return int
     */
    public function getCrf()
    {
        return $this->crf;
    }

    /**
     * @param int $crf
     */
    public function setCrf($crf)
    {
        $this->crf = $crf;
    }

    /**
     * @return int
     */
    public function getLagInFrames()
    {
        return $this->lagInFrames;
    }

    /**
     * @param int $lagInFrames
     */
    public function setLagInFrames($lagInFrames)
    {
        $this->lagInFrames = $lagInFrames;
    }

    /**
     * @return int
     */
    public function getTileColumns()
    {
        return $this->tileColumns;
    }

    /**
     * @param int $tileColumns
     */
    public function setTileColumns($tileColumns)
    {
        $this->tileColumns = $tileColumns;
    }

    /**
     * @return int
     */
    public function getTileRows()
    {
        return $this->tileRows;
    }

    /**
     * @param int $tileRows
     */
    public function setTileRows($tileRows)
    {
        $this->tileRows = $tileRows;
    }

    /**
     * @return bool
     */
    public function isFrameParallel()
    {
        return $this->frameParallel;
    }

    /**
     * @param bool $frameParallel
     */
    public function setFrameParallel($frameParallel)
    {
        $this->frameParallel = $frameParallel;
    }

    /**
     * @return int
     */
    public function getMaxIntraRate()
    {
        return $this->maxIntraRate;
    }

    /**
     * @param int $maxIntraRate
     */
    public function setMaxIntraRate($maxIntraRate)
    {
        $this->maxIntraRate = $maxIntraRate;
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
    public function getRateUndershootPct()
    {
        return $this->rateUndershootPct;
    }

    /**
     * @param int $rateUndershootPct
     */
    public function setRateUndershootPct($rateUndershootPct)
    {
        $this->rateUndershootPct = $rateUndershootPct;
    }

    /**
     * @return int
     */
    public function getCpuUsed()
    {
        return $this->cpuUsed;
    }

    /**
     * @param int $cpuUsed
     */
    public function setCpuUsed($cpuUsed)
    {
        $this->cpuUsed = $cpuUsed;
    }

    /**
     * @return bool
     */
    public function isNoiseSensitivity()
    {
        return $this->noiseSensitivity;
    }

    /**
     * @param bool $noiseSensitivity
     */
    public function setNoiseSensitivity($noiseSensitivity)
    {
        $this->noiseSensitivity = $noiseSensitivity;
    }

    /**
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param string $quality
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
    }

    /**
     * @return bool
     */
    public function isLossless()
    {
        return $this->lossless;
    }

    /**
     * @param bool $lossless
     */
    public function setLossless($lossless)
    {
        $this->lossless = $lossless;
    }

    /**
     * @return int
     */
    public function getStaticThresh()
    {
        return $this->staticThresh;
    }

    /**
     * @param int $staticThresh
     */
    public function setStaticThresh($staticThresh)
    {
        $this->staticThresh = $staticThresh;
    }

    /**
     * @return string
     */
    public function getAqMode()
    {
        return $this->aqMode;
    }

    /**
     * @param string $aqMode
     */
    public function setAqMode($aqMode)
    {
        $this->aqMode = $aqMode;
    }

    /**
     * @return int
     */
    public function getArnrMaxFrames()
    {
        return $this->arnrMaxFrames;
    }

    /**
     * @param int $arnrMaxFrames
     */
    public function setArnrMaxFrames($arnrMaxFrames)
    {
        $this->arnrMaxFrames = $arnrMaxFrames;
    }

    /**
     * @return int
     */
    public function getArnrStrength()
    {
        return $this->arnrStrength;
    }

    /**
     * @param int $arnrStrength
     */
    public function setArnrStrength($arnrStrength)
    {
        $this->arnrStrength = $arnrStrength;
    }

    /**
     * @return string
     */
    public function getArnrType()
    {
        return $this->arnrType;
    }

    /**
     * @param string $arnrType
     */
    public function setArnrType($arnrType)
    {
        $this->arnrType = $arnrType;
    }
}