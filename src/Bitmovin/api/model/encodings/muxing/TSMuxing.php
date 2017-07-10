<?php

namespace Bitmovin\api\model\encodings\muxing;

use JMS\Serializer\Annotation as JMS;

class TSMuxing extends AbstractMuxing
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
     * TSMuxing constructor.
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

}