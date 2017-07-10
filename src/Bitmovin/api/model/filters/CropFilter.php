<?php

namespace Bitmovin\api\model\filters;

use JMS\Serializer\Annotation as JMS;

class CropFilter extends AbstractFilter
{

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $left;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $right;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $top;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $bottom;

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param int $left
     */
    public function setLeft($left)
    {
        $this->left = $left;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param int $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * @return int
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * @param int $top
     */
    public function setTop($top)
    {
        $this->top = $top;
    }

    /**
     * @return int
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * @param int $bottom
     */
    public function setBottom($bottom)
    {
        $this->bottom = $bottom;
    }

}