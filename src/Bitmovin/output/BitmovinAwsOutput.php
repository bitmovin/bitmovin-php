<?php


namespace Bitmovin\output;

class BitmovinAwsOutput extends AbstractBitmovinOutput
{
    /**
     * BitmovinAwsOutput constructor.
     *
     * @param string $cloudRegion Recommended: Use CloudRegion constants to select a specific region
     */
    public function __construct($cloudRegion)
    {
        parent::__construct($cloudRegion);
    }
}