<?php

namespace Bitmovin\api\model\manifests\hls;


use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class CustomTag extends AbstractModel
{

    /**
     * @JMS\Type("string")
     * @var  string PositionMode
     */
    private $positionMode;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $keyframeId;

    /**
     * @JMS\Type("float")
     * @var  float
     */
    private $time;

    /**
     * @JMS\Type("integer")
     * @var  Integer
     */
    private $segment;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $data;

    /**
     * @return string
     */
    public function getPositionMode()
    {
        return $this->positionMode;
    }

    /**
     * @param string $positionMode
     */
    public function setPositionMode($positionMode)
    {
        $this->positionMode = $positionMode;
    }

    /**
     * @return string
     */
    public function getKeyframeId()
    {
        return $this->keyframeId;
    }

    /**
     * @param string $language
     */
    public function setKeyframeId($keyframeId)
    {
        $this->keyframeId = $keyframeId;
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
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param int $segment
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

}
