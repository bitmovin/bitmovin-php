<?php

namespace Bitmovin\configs\audio;

use Bitmovin\configs\AbstractStreamConfig;

class AudioStreamConfig extends AbstractStreamConfig
{

    /**
     * @var int
     */
    public $bitrate;

    /**
     * @var float
     */
    public $rate;

    /**
     * @var string
     */
    public $lang;

    /**
     * @var string
     */
    public $name;

}