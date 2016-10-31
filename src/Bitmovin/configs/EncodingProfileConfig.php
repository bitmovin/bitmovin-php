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