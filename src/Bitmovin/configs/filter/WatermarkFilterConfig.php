<?php


namespace Bitmovin\configs\filter;


class WatermarkFilterConfig extends AbstractFilterConfig
{

    /**
     * @var string
     */
    public $image = '';
    /**
     * @var int|null
     */
    public $left = 100;
    /**
     * @var int|null
     */
    public $right = null;
    /**
     * @var int|null
     */
    public $top = 10;
    /**
     * @var int|null
     */
    public $bottom = null;

    /**
     * WatermarkFilterConfig constructor.
     * @param string   $image
     * @param int|null $left
     * @param int|null $right
     * @param int|null $top
     * @param int|null $bottom
     */
    public function __construct($image, $left = 100, $right = null, $top = 10, $bottom = null)
    {
        $this->image = $image;
        $this->left = $left;
        $this->right = $right;
        $this->top = $top;
        $this->bottom = $bottom;
    }

}