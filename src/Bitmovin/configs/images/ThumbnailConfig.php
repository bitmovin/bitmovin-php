<?php


namespace Bitmovin\configs\images;


use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\video\AbstractVideoStreamConfig;

class ThumbnailConfig
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var  string
     */
    public $description;

    /**
     * @var  integer
     */
    public $height;

    /**
     * @var  array
     */
    public $positions = array();

    /**
     * @var  string
     */
    public $pattern = "thumb_%number%.png";

    /**
     * ThumbnailConfig constructor.
     * @param       $height
     * @param array $positions
     */
    public function __construct($height, array $positions)
    {
        $this->height = $height;
        $this->positions = $positions;
    }


}