<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\Transferable;
use JMS\Serializer\Annotation as JMS;

class LiveDashManifest
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $manifestId;

    /**
     * @JMS\Type("float")
     * @var float
     */
    private $timeshift;

    /**
     * @JMS\Type("float")
     * @var float
     */
    private $liveEdgeOffset;

    /**
     * @return string
     */
    public function getManifestId()
    {
        return $this->manifestId;
    }

    /**
     * @param string $manifestId
     */
    public function setManifestId($manifestId)
    {
        $this->manifestId = $manifestId;
    }

    /**
     * @return float
     */
    public function getTimeshift()
    {
        return $this->timeshift;
    }

    /**
     * @param float $timeshift
     */
    public function setTimeshift($timeshift)
    {
        $this->timeshift = $timeshift;
    }

    /**
     * @return float
     */
    public function getLiveEdgeOffset()
    {
        return $this->liveEdgeOffset;
    }

    /**
     * @param float $liveEdgeOffset
     */
    public function setLiveEdgeOffset($liveEdgeOffset)
    {
        $this->liveEdgeOffset = $liveEdgeOffset;
    }

}