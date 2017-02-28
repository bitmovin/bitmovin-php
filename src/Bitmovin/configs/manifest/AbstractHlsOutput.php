<?php

namespace Bitmovin\configs\manifest;


use Bitmovin\configs\AbstractStreamConfig;

abstract class AbstractHlsOutput extends AbstractOutputFormat
{
    /**
     * @var string
     */
    public $name = "stream.m3u8";

    /**
     * @var AbstractStreamConfig[]
     * @deprecated Please use $audioVideoGroups
     */
    public $includedStreamConfigs = null;

    /**
     * @var HlsConfigurationFileNaming[]
     */
    public $hlsConfigurationFileNaming = array();

    /**
     * @var HlsConfigurationAudioVideoGroup[]
     */
    public $audioVideoGroups = null;

        /**
     * @var ExternalSubtitleFormat[]
     */
    public $vttSubtitles = array();

}