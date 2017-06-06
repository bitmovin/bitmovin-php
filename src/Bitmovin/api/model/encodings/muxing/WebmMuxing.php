<?php

namespace Bitmovin\api\model\encodings\muxing;

use JMS\Serializer\Annotation as JMS;

class WebmMuxing extends AbstractMuxing
{
    /**
     * @JMS\Type("integer")
     * @var  integer
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
     * @return int
     */
    public function getSegmentLength()
    {
        return $this->segmentLength;
    }

    /**
     * @param int $segmentLength
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