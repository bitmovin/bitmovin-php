<?php


namespace Bitmovin\configs;


use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\video\AbstractVideoStreamConfig;

class EncodingProfileConfig
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $infrastructureId;

    /** @var  string */
    public $encoderVersion;

    /**
     * @var string Enum: \Bitmovin\api\enum\CloudRegion
     */
    public $cloudRegion;

    /**
     * @var AbstractVideoStreamConfig[]
     */
    public $videoStreamConfigs = array();

    /**
     * @var AudioStreamConfig[]
     */
    public $audioStreamConfigs = array();

}