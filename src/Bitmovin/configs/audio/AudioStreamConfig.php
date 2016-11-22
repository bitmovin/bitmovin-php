<?php

namespace Bitmovin\configs\audio;

use Bitmovin\configs\AbstractStreamConfig;

class AudioStreamConfig extends AbstractStreamConfig
{
    private $id;

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

    public function __construct()
    {
        $this->id = uniqid("bitmovin_",true);
    }

    public function getId() {
        return $this->id;
    }
}