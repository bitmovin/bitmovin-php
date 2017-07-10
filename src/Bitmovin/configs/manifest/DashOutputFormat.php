<?php


namespace Bitmovin\configs\manifest;


use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\drm\CencDrm;

class DashOutputFormat extends AbstractOutputFormat
{

    /**
     * @var CencDrm
     */
    public $cenc = null;

    /**
     * @var AbstractStreamConfig[]
     */
    public $includedStreamConfigs = null;

    /** @var integer */
    public $segmentLength = null;

    /**
     * @var string
     */
    public $name = "stream.mpd";


    /**
     * @var ExternalSubtitleFormat[]
     */
    public $vttSubtitles = array();

}