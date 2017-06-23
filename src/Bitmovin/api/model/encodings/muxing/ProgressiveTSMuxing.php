<?php

namespace Bitmovin\api\model\encodings\muxing;

use JMS\Serializer\Annotation as JMS;

class ProgressiveTSMuxing extends AbstractMuxing
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
    private $filename;

    /**
     * ProgressiveTSMuxing constructor.
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
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * @param string $segmentNaming
     */
    public function setFileName($fileName)
    {
        $this->filename = $fileName;
    }

}