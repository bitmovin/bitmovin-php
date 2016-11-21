<?php

namespace Bitmovin\api\model\encodings\muxing;

use Bitmovin\api\model\encodings\drms\AbstractDrm;
use JMS\Serializer\Annotation as JMS;

class MP4Muxing extends AbstractMuxing
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $name;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $fragmentDuration;
    /**
     * @JMS\Exclude()
     * @var AbstractDrm[]
     */
    private $drms = array();

    /**
     * MP4Muxing constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getFragmentDuration()
    {
        return $this->fragmentDuration;
    }

    /**
     * @param int $fragmentDuration
     */
    public function setFragmentDuration($fragmentDuration)
    {
        $this->fragmentDuration = $fragmentDuration;
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