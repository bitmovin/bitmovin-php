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
    public $filename;

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
     * @param $width
     * @param $height
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }


}