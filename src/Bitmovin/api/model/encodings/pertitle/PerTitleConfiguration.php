<?php

namespace Bitmovin\api\model\encodings\pertitle;

use JMS\Serializer\Annotation as JMS;

use Bitmovin\api\model\encodings\AutoRepresentation;

class PerTitleConfiguration
{
    /**
     * @JMS\Type("integer")
     * @var int $minBitrate
     */
    private $minBitrate;

    /**
     * @JMS\Type("integer")
     * @var int $maxBitrate
     */
    private $maxBitrate;

    /**
     * @JMS\Type("float")
     * @var float $minBitrateStepSize
     */
    private $minBitrateStepSize;

    /**
     * @JMS\Type("float")
     * @var float $maxBitrateStepSize
     */
    private $maxBitrateStepSize;

    /**
     * @JMS\SerializedName("autoRepresentations")
     * @JMS\Type("Bitmovin\api\model\encodings\AutoRepresentation")
     * @var AutoRepresentation $autoRepresentation
     */
    private $autoRepresentation;

    /**
     * @return AutoRepresentation
     */
    public function getAutoRepresentation()
    {
        return $this->autoRepresentation;
    }

    /**
     * @param AutoRepresentation $autoRepresentation
     */
    public function setAutoRepresentation($autoRepresentation)
    {
        $this->autoRepresentation = $autoRepresentation;
    }

    /**
     * @return int
     */
    public function getMinBitrate()
    {
        return $this->minBitrate;
    }

    /**
     * @param int $minBitrate
     */
    public function setMinBitrate($minBitrate)
    {
        $this->minBitrate = $minBitrate;
    }

    /**
     * @return int
     */
    public function getMaxBitrate()
    {
        return $this->maxBitrate;
    }

    /**
     * @param int $maxBitrate
     */
    public function setMaxBitrate($maxBitrate)
    {
        $this->maxBitrate = $maxBitrate;
    }

    /**
     * @return float
     */
    public function getMinBitrateStepSize()
    {
        return $this->minBitrateStepSize;
    }

    /**
     * @param float $minBitrateStepSize
     */
    public function setMinBitrateStepSize($minBitrateStepSize)
    {
        $this->minBitrateStepSize = $minBitrateStepSize;
    }

    /**
     * @return float
     */
    public function getMaxBitrateStepSize()
    {
        return $this->maxBitrateStepSize;
    }

    /**
     * @param float $maxBitrateStepSize
     */
    public function setMaxBitrateStepSize($maxBitrateStepSize)
    {
        $this->maxBitrateStepSize = $maxBitrateStepSize;
    }


}
