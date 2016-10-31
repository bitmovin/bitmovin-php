<?php

namespace Bitmovin\api\model\encodings\muxing;

use JMS\Serializer\Annotation as JMS;

class TSMuxing extends AbstractMuxing
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
     * TSMuxing constructor.
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

}