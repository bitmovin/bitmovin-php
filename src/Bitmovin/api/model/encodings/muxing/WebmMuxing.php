<?php

namespace Bitmovin\api\model\encodings\muxing;

use JMS\Serializer\Annotation as JMS;

class WebmMuxing extends AbstractMuxing
{
    /**
     * @JMS\Type("double")
     * @var  double
     */
    private $segmentLength;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $segmentNaming;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $initSegmentName;

    /**
     * FMP4Muxing constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return double
     */
    public function getSegmentLength()
    {
        return $this->segmentLength;
    }

    /**
     * @param double $segmentLength
     */
    public function setSegmentLength($segmentLength)
    {
        $this->segmentLength = $segmentLength;
    }

    /**
     * @return string
     */
    public function getSegmentNaming()
    {
        return $this->segmentNaming;
    }

    /**
     * @param string $segmentNaming
     */
    public function setSegmentNaming($segmentNaming)
    {
        $this->segmentNaming = $segmentNaming;
    }

    /**
     * @return string
     */
    public function getInitSegmentName()
    {
        return $this->initSegmentName;
    }

    /**
     * @param string $initSegmentName
     */
    public function setInitSegmentName($initSegmentName)
    {
        $this->initSegmentName = $initSegmentName;
    }
}