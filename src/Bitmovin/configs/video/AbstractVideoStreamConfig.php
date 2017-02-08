<?php


namespace Bitmovin\configs\video;


use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\filter\AbstractFilterConfig;
use Bitmovin\configs\images\SpriteConfig;
use Bitmovin\configs\images\ThumbnailConfig;

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
     * @var ThumbnailConfig[]
     */
    public $thumbnailConfigs = array();

    /**
     * @var SpriteConfig[]
     */
    public $spriteConfigs = array();

    /**
     * @var AbstractFilterConfig[]
     */
    public $filterConfigs = array();

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