<?php


namespace Bitmovin\configs\video;


use Bitmovin\configs\AbstractStreamConfig;

abstract class AbstractVideoStreamConfig extends AbstractStreamConfig
{

    /**
     * @var int
     */
    public $bitrate;

    /**
     * @var int
     */
    public $height;

    /**
     * @var int
     */
    public $width;

    /**
     * @var float
     */
    public $rate;

}