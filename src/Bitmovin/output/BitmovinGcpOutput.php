<?php


namespace Bitmovin\output;

class BitmovinGcpOutput extends AbstractBitmovinOutput
{
    /**
     * BitmovinGcsOutput constructor.
     *
     * @param string $cloudRegion Recommended: Use CloudRegion constants to select a specific region
     */
    public function __construct($cloudRegion)
    {
        parent::__construct($cloudRegion);
    }
}