<?php

namespace Bitmovin\api\model\encodings\muxing;

use JMS\Serializer\Annotation as JMS;

class ProgressiveTSMuxing extends AbstractMuxing
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
    private $filename;

    /**
     * ProgressiveTSMuxing constructor.
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
