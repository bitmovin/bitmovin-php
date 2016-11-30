<?php


namespace Bitmovin\output;

class BitmovinGcsOutput extends AbstractBitmovinOutput
{
    /**
     * @var string GcsRegion enum (e.g. CloudRegion::GOOGLE_EUROPE_WEST_1)
     */
    public $cloudRegion;

    /**
     * BitmovinGcsOutput constructor.
     *
     * @param string $cloudRegion Recommended: Use, CloudRegion::GOOGLE_EUROPE_WEST_1 to select a specific region
     */
    public function __construct($cloudRegion)
    {
        $this->cloudRegion = $cloudRegion;
    }

}