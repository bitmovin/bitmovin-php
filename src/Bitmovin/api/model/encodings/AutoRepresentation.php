<?php

namespace Bitmovin\api\model\encodings;

use JMS\Serializer\Annotation as JMS;

class AutoRepresentation
{
    /**
     * @JMS\Type("float")
     * @var float $adoptConfigurationThreshold
     */
    private $adoptConfigurationThreshold;

    /**
     * @return float
     */
    public function getAdoptConfigurationThreshold()
    {
        return $this->adoptConfigurationThreshold;
    }

    /**
     * @param float $adoptConfigurationThreshold
     */
    public function setAdoptConfigurationThreshold($adoptConfigurationThreshold)
    {
        $this->adoptConfigurationThreshold = $adoptConfigurationThreshold;
    }
}
