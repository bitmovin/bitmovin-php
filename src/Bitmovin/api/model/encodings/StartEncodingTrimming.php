<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\ModelInterface;
use JMS\Serializer\Annotation as JMS;

class StartEncodingTrimming implements ModelInterface
{
    /**
     * @JMS\Type("float")
     * @var float
     */
    private $offset;
    /**
     * @JMS\Type("float")
     * @var float
     */
    private $duration;
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $startPicTiming;
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $endPicTiming;

    /**
     * @return float
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param float $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getStartPicTiming()
    {
        return $this->startPicTiming;
    }

    /**
     * @param string $startPicTiming
     */
    public function setStartPicTiming($startPicTiming)
    {
        $this->startPicTiming = $startPicTiming;
    }

    /**
     * @return string
     */
    public function getEndPicTiming()
    {
        return $this->endPicTiming;
    }

    /**
     * @param string $endPicTiming
     */
    public function setEndPicTiming($endPicTiming)
    {
        $this->endPicTiming = $endPicTiming;
    }

    public function getId()
    {
        return null;
    }

}