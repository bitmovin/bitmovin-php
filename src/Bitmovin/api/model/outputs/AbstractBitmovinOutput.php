<?php


namespace Bitmovin\api\model\outputs;

use JMS\Serializer\Annotation as JMS;

abstract class AbstractBitmovinOutput extends Output
{

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $cloudRegion;

    /**
     * @return string
     */
    public function getCloudRegion()
    {
        return $this->cloudRegion;
    }

    /**
     * @param string $cloudRegion
     */
    public function setCloudRegion($cloudRegion)
    {
        $this->cloudRegion = $cloudRegion;
    }

}