<?php

namespace Bitmovin\api\model\encodings\keyframes;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class Keyframe extends AbstractModel
{
    /**
     * @JMS\Type("float")
     * @var  float
     */
    private $time;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $segmentCut;

    /**
     * Keyframe constructor.
     * @param float $time
     * @param boolean $segmentCut
     */
    public function __construct($time, $segmentCut)
    {
        $this->setTime($time);
        $this->setSegmentCut($segmentCut);
    }

    /**
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param float $time
     */
    public function setName($time)
    {
        $this->time = $time;
    }

    /**
     * @return boolean
     */
    public function getSegmentCut()
    {
        return $this->segmentCut;
    }

    /**
     * @param boolean $segmentCut
     */
    public function setSegmentCut($segmentCut)
    {
        $this->segmentCut = $segmentCut;
    }

}
