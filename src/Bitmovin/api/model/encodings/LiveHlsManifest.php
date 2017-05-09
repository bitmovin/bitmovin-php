<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\Transferable;
use JMS\Serializer\Annotation as JMS;

class LiveHlsManifest
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

}