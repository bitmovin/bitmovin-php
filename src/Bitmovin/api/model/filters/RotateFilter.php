<?php

namespace Bitmovin\api\model\filters;

use JMS\Serializer\Annotation as JMS;

class RotateFilter extends AbstractFilter
{

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $rotation;

    /**
     * @return int
     */
    public function getRotation()
    {
        return $this->rotation;
    }

    /**
     * @param int $rotation
     */
    public function setRotation($rotation)
    {
        $this->rotation = $rotation;
    }

}