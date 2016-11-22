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

    /**
     * AbstractVideoStreamConfig constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getId()
    {
        return parent::getId();
    }
}