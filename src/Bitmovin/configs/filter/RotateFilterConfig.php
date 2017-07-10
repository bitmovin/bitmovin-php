<?php


namespace Bitmovin\configs\filter;


class RotateFilterConfig extends AbstractFilterConfig
{

    /**
     * @var int|null
     */
    public $rotation = 0;
    /**
     * @var int|null
     */

    /**
     * RotateFilterConfig constructor.
     * @param int|null $rotation
     * @param int|null $right
     * @param int|null $top
     * @param int|null $bottom
     */
    public function __construct($rotation = 0)
    {
        $this->rotation = $rotation;
    }

}