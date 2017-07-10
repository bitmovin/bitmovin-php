<?php


namespace Bitmovin\configs\filter;


class CropFilterConfig extends AbstractFilterConfig
{

    /**
     * @var int|null
     */
    public $left = 0;
    /**
     * @var int|null
     */
    public $right = 0;
    /**
     * @var int|null
     */
    public $top = 0;
    /**
     * @var int|null
     */
    public $bottom = 0;

    /**
     * CropFilterConfig constructor.
     * @param int|null $left
     * @param int|null $right
     * @param int|null $top
     * @param int|null $bottom
     */
    public function __construct($left = 0, $right = 0, $top = 0, $bottom = 0)
    {
        $this->left = $left;
        $this->right = $right;
        $this->top = $top;
        $this->bottom = $bottom;
    }

}