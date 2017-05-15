<?php


namespace Bitmovin\configs\video;


use Bitmovin\api\enum\codecConfigurations\H264Profile;

class H264VideoStreamConfig extends AbstractVideoStreamConfig
{
    /**
     * @var string Enum: \Bitmovin\api\enum\codecConfigurations\H264Profile
     */
    public $profile = H264Profile::HIGH;

    /**
     * @var  integer
     */
    public $bFrames;
    /**
     * @var  integer
     */
    public $refFrames;
    /**
     * @var  integer
     */
    public $qpMin;
    /**
     * @var  integer
     */
    public $qpMax;
    /**
     * @var  string MvPredictionMode
     */
    public $mvPredictionMode;
    /**
     * @var  integer Range: 16-24
     */
    public $mvSearchRangeMax;
    /**
     * @var  boolean
     */
    public $cabac;
    /**
     * @var  integer
     */
    public $minBitrate;
    /**
     * @var  integer
     */
    public $maxBitrate;
    /**
     * @var  integer
     */
    public $bufsize;
    /**
     * @var  integer
     */
    public $minGop;
    /**
     * @var  integer
     */
    public $maxGop;
    /**
     * @var  string H264Level
     */
    public $level; 
}