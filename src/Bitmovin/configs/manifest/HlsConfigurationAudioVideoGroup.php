<?php


namespace Bitmovin\configs\manifest;


use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\video\AbstractVideoStreamConfig;

class HlsConfigurationAudioVideoGroup
{
    /**
     * @var AudioStreamConfig[]
     */
    public $audioStreams = array();
    /**
     * @var AbstractVideoStreamConfig[]
     */
    public $videoStreams = array();
}