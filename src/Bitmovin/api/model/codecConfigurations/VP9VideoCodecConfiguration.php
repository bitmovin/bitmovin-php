<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

class VP9VideoCodecConfiguration extends VideoConfiguration
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
    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    /**
     * @JMS\Type("array<string>")
     * @var  string[] H264Partition
     */
    /**
     * @JMS\Type("string")
     * @var  string MvPredictionMode
     */

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
    public function __construct($name, $profile, $bitrate, $rate)
    {
        parent::__construct($name, $bitrate, $rate);
        $this->profile = $profile;
    }

}