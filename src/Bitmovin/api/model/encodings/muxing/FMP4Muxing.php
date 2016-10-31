<?php

namespace Bitmovin\api\model\encodings\muxing;

use Bitmovin\api\model\encodings\drms\AbstractDrm;
use JMS\Serializer\Annotation as JMS;

class FMP4Muxing extends AbstractMuxing
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
     * @JMS\Exclude()
     * @var AbstractDrm[]
     */
    private $drms = array();

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

    /**
     * @return \Bitmovin\api\model\encodings\drms\AbstractDrm[]
     */
    public function getDrms()
    {
        return $this->drms;
    }

    /**
     * @param \Bitmovin\api\model\encodings\drms\AbstractDrm[] $drms
     */
    public function setDrms($drms)
    {
        $this->drms = $drms;
    }

    /**
     * @param \Bitmovin\api\model\encodings\drms\AbstractDrm $drm
     */
    public function addDrm($drm)
    {
        $this->drms[] = $drm;
    }



}