<?php

namespace Bitmovin\configs\images;


class SpriteConfig
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
     * @var  string
     */
    public $spriteName;

    /**
     * @var string
     */
    public $vttName;

    /**
     * @var  integer
     */
    public $height;

    /**
     * @var  integer
     */
    public $width;

    /**
     * @var  integer
     */
    public $distance;

    /**
     * SpriteConfig constructor.
     * @param integer $width
     * @param integer $height
     * @param string  $spriteName
     * @param string  $vttName
     */
    public function __construct($width, $height, $spriteName, $vttName)
    {
        $this->width = $width;
        $this->height = $height;
        $this->spriteName = $spriteName;
        $this->vttName = $vttName;
    }


}